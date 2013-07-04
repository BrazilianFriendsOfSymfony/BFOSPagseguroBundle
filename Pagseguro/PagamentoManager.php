<?php
/**
 * Created by JetBrains PhpStorm.
 * User: duo
 * Date: 2/25/12
 * Time: 2:25 PM
 * To change this template use File | Settings | File Templates.
 */
namespace BFOS\PagseguroBundle\Pagseguro;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

use BFOS\PagseguroBundle\Utils\Browser;
use \BFOS\PagseguroBundle\Entity\Pagamento;
use BFOS\PagseguroBundle\Entity\PagamentoItem;
use BFOS\PagseguroBundle\Entity\NotificacaoTransacao;
use BFOS\PagseguroBundle\Entity\Transacao;
use BFOS\PagseguroBundle\Entity\TransacaoItem;
use BFOS\PagseguroBundle\Utils\Pagseguro;

class PagamentoManager
{
    private $container;
    private $em;
    private $rpagamento;
    /**
     * @var \BFOS\PagseguroBundle\Entity\TransacaoRepository $rtransacao
     */
    private $rtransacao;
    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface $logger
     */
    private $logger;

    function __construct(ContainerInterface $container)
    {
        $this->container  = $container;
        $this->em = $container->get('doctrine')->getEntityManager();
        $this->rpagamento = $container->get('doctrine')->getRepository('BFOSPagseguroBundle:Pagamento');
        $this->rtransacao = $container->get('doctrine')->getRepository('BFOSPagseguroBundle:Transacao');
        $this->logger     = $container->get('logger');
    }

    /**
     * Registra o pagamento junto ao Pagseguro
     *
     * @param \BFOS\PagseguroBundle\Entity\Pagamento $pagamento
     *
     * @return boolean|\Symfony\Component\Validator\ConstraintViolationList True se o pagamento eh valido para ser
     *         submetido ao Pagseguro e ConstraintViolationList cajo constrário.
     */
    public function registrarPagamento(Pagamento $pagamento){

        if(($erros = $this->ehValido($pagamento))!==true){
            return $erros;
        }

        $this->rpagamento->persist($pagamento);
        $this->rpagamento->flush();

        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= sprintf('<%s>%s</%s>','checkout', $pagamento->toXML('UTF-8'),'checkout');

        $url = 'https://ws.pagseguro.uol.com.br/v2/checkout?' . sprintf('email=%s&token=%s', $pagamento->getEmail(), $pagamento->getToken());

        $this->logger->debug('url do Pagseguro = ' . $url);
        $this->logger->debug('xml enviado ao Pagseguro = ' . $xml);

        $retorno = Browser::postXml($url, $xml);

        if($retorno['info']['http_code']!=200){
            $erro_msg = 'O Pagseguro nao autorizou o registro da transacao do pagamento com referencia '.$pagamento->getReference().' : ' . $retorno['info']['http_code'] . " | " . $retorno['corpo'];
            $this->logger->err($erro_msg);
            $this->logger->err(print_r($retorno,true));
            $pagamento->setRegistroErrors($erro_msg);
            $this->rpagamento->persist($pagamento);
            $this->rpagamento->flush();
            throw new \Exception($erro_msg);
        }
        /**
         * @var \SimpleXMLElement $retornoXml
         **/
        @$retornoXml = simplexml_load_string($retorno['corpo']);
        if($retornoXml->errors){ // pagseguro retornou xml com errors
            $pagamento->setRegistroErrors($retornoXml->errors->asXML());// TODO: implementar verificacao de encoding
        } else { // deu certo o registro do pagamento
            $retornoToken = (string) $retornoXml->code;
            $retornoEm = (string) $retornoXml->date;
            $retornoEm = new \DateTime($retornoEm);
            $pagamento->setRetornoToken($retornoToken);
            $pagamento->setRetornoEm($retornoEm);
            $this->logger->info(print_r($retornoXml,true));
        }
        // tenta persistir as informacoes no BD, para garantir a persistencia do que estah sendo enviado
        $this->rpagamento->persist($pagamento);
        $this->rpagamento->flush();

        $urlPagseguro = $pagamento->getUrlPagseguro();
        if($urlPagseguro == null){
            throw new \Exception('Não está configurada a url de redirecionamento para o PagSeguro.');
        } else{
            return new RedirectResponse($urlPagseguro);
        }
    }

    /**
         * Verifica se o pagamento eh valido, ou seja, se jah pode ser submetido ao Pagseguro.
         *
         * @param \BFOS\PagseguroBundle\Entity\Pagamento $pagamento
        **/
    public function ehValido(Pagamento $pagamento){

        $validator = $this->container->get('validator');
        $errors = $validator->validate($pagamento);

        if (count($errors) > 0){
            return $errors;
        } else {
            return true;
        }
    }

    /**
     * Consulta a notificação de uma transação pelo notificationCode.
     *
     * @param string $conta_email O e-mail da conta vendedor no Pagseguro
     * @param string $conta_token O token da conta vendedor no Pagseguro
     * @param string $notification_code O notificationCode enviado pelo Pagseguro
     */
    public function consultaNotificacaoDeTransacao($conta_email, $conta_token, $notification_code){
        $url = "https://ws.pagseguro.uol.com.br/v2/transactions/notifications/%s?email=%s&token=%s";
        $url = sprintf($url, $notification_code, $conta_email, $conta_token);

        $this->logger->info("CONSULTA NOTIFICACAO DE TRANSACAO - INICIO");
        $conteudo = Browser::get($url);
        $this->logger->info($conteudo);

        try{

            $nt = $this->salvarNotificacaoTransacaoFromXml($conteudo);

            if(!($t = $this->rtransacao->findOneBy(array('reference'=>$nt->getReference(), 'vendedor_email'=>$conta_email)))){
                $t = new Transacao();
                $t->setVendedorEmail($conta_email);
            }
            $this->atualizarTransacaoAPartirDaNotificacaoTransacao($t, $nt);

            $event = new \BFOS\PagseguroBundle\Event\NotificacaoTransacaoEvent($nt);
            $this->container->get('event_dispatcher')->dispatch(\BFOS\PagseguroBundle\Event\PagseguroEvents::onNotificacaoTransacao, $event);

        } catch(\Exception $e){
            $this->logger->err('Erro processar Notificacao de Transacao: ' . $e->getMessage());
            throw $e;
        }

        $this->logger->info("CONSULTA NOTIFICACAO DE TRANSACAO - FIM");
    }

    public function salvarNotificacaoTransacaoFromXml($xml_str){
        try{


            /**
             * @var \SimpleXMLElement $retornoXml
             **/
            @$xml = simplexml_load_string($xml_str);

            $st = new NotificacaoTransacao();
            $st->setCode((string) $xml->code);
            $st->setDataTransacao((string) $xml->date);
            $st->setReference((string) $xml->reference);
            $st->setLastEventDate((string) $xml->lastEventDate);
            $st->setPaymentMethodCode((string) $xml->paymentMethod->code);
            $st->setPaymentMethodType((string) $xml->paymentMethod->type);
            $st->setStatus((string) $xml->status);
            $xml_utf8 = utf8_encode((string)$xml_str);
            $xml_utf8 = str_replace('encoding="ISO-8859-1"', 'encoding="UTF-8"', $xml_utf8);
            $st->setXml($xml_utf8);
            $st->setType((string) $xml->type);
            $this->em->persist($st);
            $this->em->flush();


        } catch(\Exception $e){
            $this->logger->err('Erro processar Notificacao de Transacao: ' . $e->getMessage());
            throw $e;
        }

        return $st;
    }

    private function atualizarTransacaoAPartirDaNotificacaoTransacao(Transacao $transacao, NotificacaoTransacao $situacao_transacao){
        /**
         * @var \SimpleXMLElement $retornoXml
         **/
        @$xml = simplexml_load_string($situacao_transacao->getXml());
        if(!$transacao->getDataTransacao()){
            $transacao->setDataTransacao(new \DateTime((string) $xml->date));
        }
        if(!$transacao->getReference()){
            $transacao->setReference((string) $xml->reference);
        }
        if(!$transacao->getType()){
            $transacao->setType((string) $xml->type);
        }
        if(!$transacao->getStatus()){
            $transacao->setStatus((string) $xml->status);
        }
        if(!$transacao->getLastEventDate()){
            $transacao->setLastEventDate((string) $xml->lastEventDate);
        }

        $transacao->setPaymentMethodType((string) $xml->paymentMethod->type);
        $transacao->setPaymentMethodCode((string) $xml->paymentMethod->code);

        if(!$transacao->getGrossAmount()){
            $transacao->setGrossAmount((string) $xml->grossAmount);
        }
        if(!$transacao->getDiscountAmount()){
            $transacao->setDiscountAmount((string) $xml->discountAmount);
        }
        if(!$transacao->getFeeAmount()){
            $transacao->setFeeAmount((string) $xml->feeAmount);
        }
        if(!$transacao->getNetAmount()){
            $transacao->setNetAmount((string) $xml->netAmount);
        }
        if(!$transacao->getExtraAmount()){
            $transacao->setExtraAmount((string) $xml->extraAmount);
        }
        if(!$transacao->getInstallmentCount()){
            $transacao->setInstallmentCount((string) $xml->installmentCount);
        }

        if(!$transacao->getSenderName()){
            $transacao->setSenderName((string) $xml->sender->name);
        }
        if(!$transacao->getSenderEmail()){
            $transacao->setSenderEmail((string) $xml->sender->email);
        }
        if(!$transacao->getSenderPhoneAreacode()){
            $transacao->setSenderPhoneAreacode((string) $xml->sender->phone->areaCode);
        }
        if(!$transacao->getSenderPhoneNumber()){
            $transacao->setSenderPhoneNumber((string) $xml->sender->phone->number);
        }

        if(!$transacao->getShippingType()){
            $transacao->setShippingType((string) $xml->shipping->type);
        }
        if(!$transacao->getShippingCost()){
            $transacao->setShippingCost((string) $xml->shipping->cost);
        }
        if(!$transacao->getShippingAddressStreet()){
            $transacao->setShippingAddressStreet((string) $xml->shipping->address->street);
        }
        if(!$transacao->getShippingAddressNumber()){
            $transacao->setShippingAddressNumber((string) $xml->shipping->address->number);
        }
        if(!$transacao->getShippingAddressComplement()){
            $transacao->setShippingAddressComplement((string) $xml->shipping->address->complement);
        }
        if(!$transacao->getShippingAddressDistrict()){
            $transacao->setShippingAddressDistrict((string) $xml->shipping->address->district);
        }
        if(!$transacao->getShippingAddressCity()){
            $transacao->setShippingAddressCity((string) $xml->shipping->address->city);
        }
        if(!$transacao->getShippingAddressState()){
            $transacao->setShippingAddressState((string) $xml->shipping->address->state);
        }
        if(!$transacao->getShippingAddressCountry()){
            $transacao->setShippingAddressCountry((string) $xml->shipping->address->country);
        }
        if(!$transacao->getShippingAddressPostalcode()){
            $transacao->setShippingAddressPostalcode((string) $xml->shipping->address->postalCode);
        }

        if(!$transacao->getItemCount()){
            $transacao->setItemCount((string) $xml->itemCount);
        }
        $this->em->persist($transacao);
        if(count($transacao->getItens())==0){
            foreach ($xml->items->item as $i) {
                if((string) $i->id){
                    $ti = new TransacaoItem();
                    $ti->setIdentifier((string) $i->id);
                    $ti->setAmount((string) $i->amount);
                    $ti->setDescription((string) $i->description);
                    $ti->setQuantity((string) $i->quantity);
                    $transacao->addItem($ti);
                    $this->em->persist($ti);
                }
            }

        }

//        $this->em->persist($transacao);
        $this->em->flush();
    }


    public function processarRetornoAutomatico($request){



        if(!($t = $this->container->get('doctrine')->getRepository('BFOSPagseguroBundle:Transacao')->findOneBy(array('vendedor_email'=>$request->request->get('VendedorEmail'), 'reference'=>$this->getFromRequest('Referencia', $request))) )){
            $t = new \BFOS\PagseguroBundle\Entity\Transacao();
        }


        if(!$t->getDataTransacao()){
            $data_transacao = $request->request->get('DataTransacao');
            if(preg_match('/(\d{2})[\/](\d{2})[\/](\d{4}) (\d{2}):(\d{2}):(\d{2})/', $data_transacao, $matches)){
                $data_transacao = date('Y-m-d H:i:s', mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[1], $matches[3]));
                $data_transacao = new \DateTime($data_transacao, new \DateTimeZone('America/Sao_Paulo'));
//                $data_transacao->setTimezone(new \DateTimeZone('UTC'));
                $t->setDataTransacao($data_transacao);
            }
        }
        if(!$t->getType()){
            $t->setType(1);
        }
        if(!$t->getVendedorEmail()){
            $t->setVendedorEmail($request->request->get('VendedorEmail'));
        }
        if(!$t->getTransacaoId()){
            $t->setTransacaoId($request->request->get('TransacaoID'));
        }
        if(!$t->getReference()){
            $t->setReference($this->getFromRequest('Referencia', $request));
        }
        if(!$t->getExtraAmount()){
            $t->setExtraAmount($request->request->get('Extras'));
        }
        if(!$t->getShippingType()){
            $t->setShippingType(Pagseguro::getShippingTypeFromTipoFrete($request->request->get('TipoFrete')));
        }
        if(!$t->getShippingCost()){
            $t->setShippingCost(str_replace(',','.', $request->request->get('ValorFrete')));
        }
        if(!$t->getPaymentMethodType()){
            $t->setPaymentMethodType(Pagseguro::getPaymentMethodTypeFromTipoPagamento($request->request->get('TipoPagamento')));
        }
        if(!$t->getStatus()){
            $t->setStatus(Pagseguro::getStatusFromStatusTransacao($request->request->get('StatusTransacao')));
        }
        if(!$t->getSenderName()){
            $t->setSenderName($this->getFromRequest('CliNome', $request));
        }
        if(!$t->getSenderEmail()){
            $t->setSenderEmail($request->request->get('CliEmail'));
        }
        if(!$t->getSenderPhoneAreacode()){
            $t->setSenderPhoneAreacode(substr($request->request->get('CliTelefone'), 0, 2));
        }
        if(!$t->getSenderPhoneNumber()){
            $t->setSenderPhoneNumber(substr($request->request->get('CliTelefone'), 3, 8));
        }
        if(!$t->getShippingAddressStreet()){
            $t->setShippingAddressStreet($this->getFromRequest('CliEndereco', $request));
        }
        if(!$t->getShippingAddressNumber()){
            $t->setShippingAddressNumber($request->request->get('CliNumero'));
        }
        if(!$t->getShippingAddressComplement()){
            $t->setShippingAddressComplement($this->getFromRequest('CliComplemento', $request));
        }
        if(!$t->getShippingAddressDistrict()){
            $t->setShippingAddressDistrict($this->getFromRequest('CliBairro', $request));
        }
        if(!$t->getShippingAddressCity()){
            $t->setShippingAddressCity($this->getFromRequest('CliCidade', $request));
        }
        if(!$t->getShippingAddressState()){
            $t->setShippingAddressState($this->getFromRequest('CliEstado', $request));
        }
        if(!$t->getShippingAddressPostalcode()){
            $t->setShippingAddressPostalcode($request->request->get('CliCEP'));
        }
        if(!$t->getShippingAddressCountry()){
            $t->setShippingAddressCountry('BRA');
        }
        if(!$t->getInstallmentCount()){
            $t->setInstallmentCount($request->request->get('Parcelas'));
        }
        if(!$t->getItemCount()){
            $t->setItemCount($request->request->get('NumItens'));
        }
        $this->container->get('doctrine')->getEntityManager()->persist($t);
        if(count($t->getItens())==0){
            $shipping_cost = 0;
            $extra_amount = 0;
            for($i=1; $i<=$t->getItemCount(); $i++){
                $ti = new TransacaoItem();
                $ti->setIdentifier($this->getFromRequest("ProdID_$i", $request));
                $ti->setDescription($this->getFromRequest("ProdDescricao_$i", $request));
                $ti->setQuantity($this->getFromRequest("ProdQuantidade_$i", $request));
                $ti->setAmount(str_replace(',', '.',$this->getFromRequest("ProdValor_$i", $request)));
                $t->addItem($ti);
                $this->container->get('doctrine')->getEntityManager()->persist($ti);
                $shipping_cost += str_replace(',', '.',$this->getFromRequest("ProdFrete_$i", $request));
                $extra_amount += str_replace(',', '.',$this->getFromRequest("ProdExtras_$i", $request));
            }
            $t->setShippingCost($shipping_cost);
            $t->setExtraAmount($extra_amount);
        }
        $this->container->get('doctrine')->getEntityManager()->persist($t);

        $situacao = new \BFOS\PagseguroBundle\Entity\TransacaoSituacao();
        $situacao->setSituacao($this->getFromRequest('StatusTransacao', $request));
        $t->addSituacao($situacao);
        $this->container->get('doctrine')->getEntityManager()->persist($situacao);

        $this->container->get('doctrine')->getEntityManager()->flush();

        $event = new \BFOS\PagseguroBundle\Event\RetornoAutomaticoEvent($t);
        $this->container->get('event_dispatcher')->dispatch(\BFOS\PagseguroBundle\Event\PagseguroEvents::onRetornoAutomatico, $event);

    }
    private function getFromRequest($var, $request){
        return utf8_encode($request->request->get($var));
    }
}
