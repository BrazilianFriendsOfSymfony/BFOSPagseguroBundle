<?php

namespace BFOS\PagseguroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BFOS\PagseguroBundle\Entity\TransacaoItem
 *
 * @ORM\Table(name="bfos_pagseguro_transacao_item")
 * @ORM\Entity
 */
class TransacaoItem
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
     * @var Transacao $transacao
     *
     * @ORM\ManyToOne(targetEntity="\BFOS\PagseguroBundle\Entity\Transacao", inversedBy="itens")
     * @ORM\JoinColumn(name="transacao_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $transacao;

    /**
     * @var integer $identifier
     *
     * @ORM\Column(name="identifier", type="integer")
     */
    private $identifier;

    /**
     * @var string $description
     *
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @var integer $quantity
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * @var decimal $amount
     *
     * @ORM\Column(name="amount", type="decimal", scale=2)
     */
    private $amount;


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
     * Set identifier
     *
     * @param integer $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * Get identifier
     *
     * @return integer 
     */
    public function getIdentifier()
    {
        return $this->identifier;
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
     * @return integer 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set amount
     *
     * @param decimal $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * Get amount
     *
     * @return decimal 
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param \BFOS\PagseguroBundle\Entity\Transacao $transacao
     */
    public function setTransacao($transacao)
    {
        $this->transacao = $transacao;
    }

    /**
     * @return \BFOS\PagseguroBundle\Entity\Transacao
     */
    public function getTransacao()
    {
        return $this->transacao;
    }
}