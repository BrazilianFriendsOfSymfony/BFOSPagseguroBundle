<?php
namespace BFOS\PagseguroBundle\Tests\Pagseguro;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PagamentoManagerTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $_em;

    /**
     * @var \BFOS\PagseguroBundle\Pagseguro\PagamentoManager $manager
     */
    private $manager;

    public function setUp()
    {
        $kernel = static::createKernel();
        $kernel->boot();
        $this->_em = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->manager = $kernel->getContainer()->get('bfos_pagseguro.pagamento_manager');
    }

    public function testSaveNotificacaoTransacaoFromXml()
    {


        $xml = '<?xml version="1.0" encoding="ISO-8859-1" standalone="yes"?><transaction><date>2012-04-05T15:08:32.000-03:00</date><code>C021094F-3758-47F3-9C5F-C748240AC0A9</code><reference>31</reference><type>1</type><status>1</status><lastEventDate>2012-04-05T15:08:36.000-03:00</lastEventDate><paymentMethod><type>2</type><code>202</code></paymentMethod><grossAmount>0.02</grossAmount><discountAmount>0.00</discountAmount><feeAmount>0.02</feeAmount><netAmount>0.00</netAmount><extraAmount>0.00</extraAmount><installmentCount>1</installmentCount><itemCount>1</itemCount><items><item><id>7</id><description>Pagamento do pacote Vozi Recomendado</description><quantity>1</quantity><amount>0.02</amount></item></items><sender><name>Paulo Roberto Ribeiro</name><email>ribeiro.paulor@gmail.com</email><phone><areaCode>19</areaCode><number>32030578</number></phone></sender><shipping><address><street>AVENIDA SANTA ISABEL</street><number>260</number><complement></complement><district>Bar�o Geraldo</district><city>CAMPINAS</city><state>SP</state><country>BRA</country><postalCode>13084012</postalCode></address><type>3</type><cost>0.00</cost></shipping></transaction>';

        $this->manager->saveNotificacaoTransacaoFromXml($xml);
    }
}
