<?php

namespace BFOS\PagseguroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * BFOS\PagseguroBundle\Entity\PagamentoItem
 *
 * @ORM\Table(name="bfos_pagseguro_pagamento_item")
 * @ORM\Entity
 */
class PagamentoItem
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $pagamento
     *
     * @ORM\ManyToOne(targetEntity="Pagamento", inversedBy="itens" )
     * @ORM\JoinColumn(name="pagamento_id", referencedColumnName="id")
     *
     * @Assert\Type(type="BFOS\PagseguroBundle\Entity\Pagamento")
     */
    private $pagamento;

    /**
     * Descrevem os itens sendo pagos. A descrição é o texto que o PagSeguro mostra associado a cada item quando o comprador está
     * finalizando o pagamento, portanto é importante que ela seja clara e explicativa.
     *
     * Presença: Obrigatória.
     * Tipo: Texto.
     * Formato: Livre, com limite de 100 caracteres.
     *
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", length=100)
     *
     * @Assert\NotBlank( )
     * @Assert\MaxLength(100)
     */
    private $description;

    /**
     * Representam os preços unitários de cada item sendo pago. Além de poder conter vários itens, o pagamento também pode conter
     * várias unidades do mesmo item. Este parâmetro representa o valor de uma unidade do item, que será multiplicado pela quantidade
     * para obter o valor total dentro do pagamento.
     *
     * Presença: Obrigatória.
     * Tipo: Número.
     * Formato: Decimal, com duas casas decimais separadas por ponto (p.e., 1234.56).
     *
     * @var float $amount
     *
     * @ORM\Column(name="amount", type="decimal", scale=2)
     *
     * @Assert\NotBlank()
     */
    private $amount;

    /**
     * Representam as quantidades de cada item sendo pago. Além de poder conter vários itens, o pagamento também pode conter várias
     * unidades do mesmo item. Este parâmetro representa a quantidade de um item, que será multiplicado pelo valor unitário para obter o
     * valor total dentro do pagamento.
     *
     * Presença: Obrigatória.
     * Tipo: Número.
     * Formato: Um número inteiro maior ou igual a 1 e menor ou igual a 999.
     *
     * @var integer $quantity
     *
     * @ORM\Column(name="quantity", type="integer")
     *
     * @Assert\NotBlank()
     * @Assert\Max(limit=999)
     * @Assert\Min(limit="1")
     */
    private $quantity;

    /**
     * Representam os custos de frete de cada item sendo pago. Caso este custo seja especificado, o PagSeguro irá assumi-lo como o custo
     * do frete do item e não fará nenhum cálculo usando o peso do item.
     *
     * Presença: Opcional.
     * Tipo: Número.
     * Formato: Decimal, com duas casas decimais.
     *
     * @var float $shippingCost
     *
     * @ORM\Column(name="shippingCost", type="decimal", nullable=true, scale=2)
     */
    private $shippingCost;

    /**
     * Correspondem ao peso (em gramas) de cada item sendo pago. O PagSeguro usa o peso do item para realizar o cálculo do custo de
     * frete nos Correios, exceto se o custo de frete do item já for especificado diretamente. Veja mais sobre as regras de cálculo de frete.
     *
     * Presença: Opcional.
     * Tipo: Número.
     * Formato: Um número inteiro correspondendo ao peso em gramas do item.
     *
     * @var integer $weight
     *
     * @ORM\Column(name="weight", type="integer", nullable=true)
     */
    private $weight;

    function __construct()
    {
        $this->quantity = 1;
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pagamento
     *
     * @param Pagamento $pagamento
     */
    public function setPagamento($pagamento)
    {
        $this->pagamento = $pagamento;
    }

    /**
     * Get pagamento
     *
     * @return Pagamento
     */
    public function getPagemento()
    {
        return $this->pagamento;
    }

    /**
     * Set description
     *
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set amount
     *
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * Get quantity
     *
     * @return float
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set shippingCost
     *
     * @param float $shippingCost
     */
    public function setShippingCost($shippingCost)
    {
        $this->shippingCost = $shippingCost;
    }

    /**
     * Get shippingCost
     *
     * @return float
     */
    public function getShippingCost()
    {
        return $this->shippingCost;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * Get weight
     *
     * @return integer 
     */
    public function getWeight()
    {
        return $this->weight;
    }

    // retorna um array com as propriedades desta classe
    public function toArray() {
        $arr = array();
        $arr['id'] = $this->getId();
        $arr['amount'] = $this->getAmount();
        $arr['descriptiob'] = $this->getDescription();
        $arr['quantity'] = $this->getQuantity();
        $arr['shippingCost'] = $this->getShippingCost();
        $arr['weight'] = $this->getWeight();

        return $arr;
    }

    public function toXML () {

        $xml = '';
        $xml .= sprintf('<%s>%s</%s>', 'id', $this->getId(), 'id');
        $xml .= sprintf('<%s>%s</%s>', 'description', $this->getDescription(), 'description');
        if($this->getAmount()) {
            $xml .= sprintf('<%s>%s</%s>', 'amount', number_format($this->getAmount(),2), 'amount');
        }
        $xml .= sprintf('<%s>%s</%s>', 'quantity', $this->getQuantity(), 'quantity');
        if($this->getWeight()) {
            $xml .= sprintf('<%s>%s</%s>', 'weight', $this->getWeight(), 'weight');
        }

        return $xml;

    }
}