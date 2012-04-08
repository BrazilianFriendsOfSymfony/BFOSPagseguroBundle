<?php

namespace BFOS\PagseguroBundle\Twig;

use \Symfony\Component\Translation\TranslatorInterface;
use \Symfony\Component\Routing\RouterInterface;
use \Symfony\Component\DependencyInjection\Container;


use \Doctrine\ORM\EntityManager;


class TwigExtension extends \Twig_Extension
{

    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var \Twig_Environment
     */
    protected $env;

    /**
     *
     * @var  \Symfony\Component\DependencyInjection\Container
     */
    protected $container;

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->env = $environment;
    }

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->em = $container->get('doctrine')->getEntityManager();
    }


    public function getFunctions()
    {
        return array(
            'bfos_pagseguro_pagamento_detalhes' => new \Twig_Function_Method($this, 'pagamentoDetalhes', array('is_safe' => array('html'))),
        );
    }


    public function pagamentoDetalhes($reference, $vendedor_email = null){
        $em = $this->em;

        $criteria = array('reference' => $reference);
        $criteria_pagamento = $criteria;
        $criteria_transacao = $criteria;
        if(!is_null($vendedor_email)){
            $criteria_transacao['vendedor_email'] = $vendedor_email;
            $criteria_pagamento['email'] = $vendedor_email;
        }
        $rpagamento   = $this->container->get('doctrine')->getRepository('BFOSPagseguroBundle:Pagamento');
        $pagamento = $rpagamento->findOneBy($criteria_pagamento);

        $rtransacao   = $this->container->get('doctrine')->getRepository('BFOSPagseguroBundle:Transacao');
        $transacao = $rtransacao->findOneBy($criteria_transacao);

        $rnotificacao   = $this->container->get('doctrine')->getRepository('BFOSPagseguroBundle:NotificacaoTransacao');
        $notificacoes = $rnotificacao->getSituacoesPorReference($reference, $vendedor_email);



        return $this->env->render('BFOSPagseguroBundle:Pagseguro:pagamentoDetalhes.html.twig',
            array(
                'pagamento' => $pagamento,
                'notificacoes' => $notificacoes,
                'transacao' => $transacao
            )
        );
    }


    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'bfos_pagseguro';
    }




}
