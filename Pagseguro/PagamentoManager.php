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
use \BFOS\PagseguroBundle\Entity\Pagamento;
use BFOS\PagseguroBundle\Entity\PagamentoItem;
use Symfony\Component\HttpFoundation\RedirectResponse;

class PagamentoManager
{
    private $container;
    private $repository;
    /**
     * @var \Symfony\Component\HttpKernel\Log\LoggerInterface $logger
     */
    private $logger;

    function __construct(ContainerInterface $container)
    {
        $this->container  = $container;
        $this->repository = $container->get('doctrine')->getRepository('BFOSPagseguroBundle:Pagamento');
        $this->logger     = $container->get('logger');
    }

    /**
     * Registra o pagamento junto ao Pagseguro
     *
     * @param \BFOS\PagseguroBundle\Entity\Pagamento $pagamento
     *
     * @return boolean False se o pagamento nao eh valido para ser submetido ao Pagseguro.
     */
    public function registrarPagamento(Pagamento $pagamento){

        if(!$this->ehValidoPagamento($pagamento)){
            return false;
        }
        $xml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $xml .= sprintf('<%s>%s</%s>','checkout', $pagamento->toXML('UTF-8'),'checkout');

        $url = 'https://ws.pagseguro.uol.com.br/v2/checkout?' . sprintf('email=%s&token=%s', $pagamento->getEmail(), $pagamento->getToken());

        $this->logger->debug(__METHOD__ . ' | url = ' . $url);
        $this->logger->debug(__METHOD__ . ' | xml = ' . $xml);

        $ch = curl_init($url);
//        curl_setopt($ch, CURLOPT_MUTE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml; charset=ISO-8859-1'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $retornoPagseguro = curl_exec($ch);
        $retornoInfo = curl_getinfo($ch);

        $errno_curl = curl_errno($ch);
        $error_curl = curl_error($ch);
        curl_close($ch);

        if($retornoInfo['http_code']!=200){
            $this->logger->err('O Pagseguro nao autorizou o registro da transacao: ' . $retornoInfo['http_code'] . " | " . $retornoPagseguro);
            throw new \Exception('O Pagseguro nao autorizou o registro da transacao');
        }
        /**
                *@var \Symfony\Component\DependencyInjection\SimpleXMLElement $retornoXml
              **/
        @$retornoXml = simplexml_load_string($retornoPagseguro);
        if($retornoXml->errors){ // pagseguro retornou xml com errors
            $pagamento->setRegistroErrors($retornoXml->errors->asXML());// TODO: implementar verificacao de encoding
        } else { // deu certo o registro do pagamento
            $retornoToken = (string) $retornoXml->code;
            $retornoEm = (string) $retornoXml->date;
            $retornoEm = new \DateTime($retornoEm);
            $pagamento->setRetornoToken($retornoToken);
            $pagamento->setRetornoEm($retornoEm);
        }
        // tenta persistir as informacoes no BD, para garantir a persistencia do que estah sendo enviado
        $this->repository->persist($pagamento);
        $this->repository->flush();

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
    protected function ehValidoPagamento(Pagamento $pagamento){

        $validator = $this->container->get('validator');
        $errors = $validator->validate($pagamento);

        if (count($errors) > 0){
            return false;
        } else {
            return true;
        }
    }
}
