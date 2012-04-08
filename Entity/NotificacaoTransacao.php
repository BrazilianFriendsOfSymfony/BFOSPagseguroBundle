<?php

namespace BFOS\PagseguroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BFOS\PagseguroBundle\Entity\SituacaoTransacao
 *
 * @ORM\Table(name="bfos_pagseguro_situacao_transacao")
 * @ORM\Entity(repositoryClass="BFOS\PagseguroBundle\Entity\SituacaoTransacaoRepository")
 * @ORM\HasLifecycleCallbacks
 */
class SituacaoTransacao
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
     * @var string $reference
     *
     * @ORM\Column(name="reference", type="string", length=200)
     */
    private $reference;

    /**
     * @var smallint $status
     *
     * @ORM\Column(name="status", type="smallint")
     */
    private $status;

    /**
     * @var datetime $last_event_date
     *
     * @ORM\Column(name="last_event_date", type="string", length=30)
     */
    private $last_event_date;

    /**
     * @var smallint $payment_method_type
     *
     * @ORM\Column(name="payment_method_type", type="smallint")
     */
    private $payment_method_type;

    /**
     * @var smallint $payment_method_code
     *
     * @ORM\Column(name="payment_method_code", type="smallint")
     */
    private $payment_method_code;

    /**
     * @var smallint $type
     *
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var string $code
     *
     * @ORM\Column(name="code", type="string", length=40)
     */
    private $code;

    /**
     * @var string $data_transacao
     *
     * @ORM\Column(name="data_transacao", type="string", length=30)
     */
    private $data_transacao;

    /**
     * @var text $xml
     *
     * @ORM\Column(name="xml", type="text")
     */
    private $xml;

    /**
     * @var \DateTime $created_at
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $created_at;

    /**
     * @var \DateTime $updated_at
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updated_at;

    function __construct()
    {
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    function preUpdate(){
        $this->updated_at = new \DateTime();
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
     * Set reference
     *
     * @param string $reference
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
    }

    /**
     * Get reference
     *
     * @return string 
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Set status
     *
     * @param smallint $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Get status
     *
     * @return smallint 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Get label do status
     *
     * @return string
     */
    public function getStatusLabel()
    {
        return \BFOS\PagseguroBundle\Utils\Pagseguro::$transaction_status[$this->status];
    }

    /**
     * Get descricao do status
     *
     * @return string
     */
    public function getStatusDescription()
    {
        return \BFOS\PagseguroBundle\Utils\Pagseguro::$transaction_status_description[$this->status];
    }

    /**
     * Set last_event_date
     *
     * @param datetime $lastEventDate
     */
    public function setLastEventDate($lastEventDate)
    {
        $this->last_event_date = $lastEventDate;
    }

    /**
     * Get last_event_date
     *
     * @return datetime 
     */
    public function getLastEventDate()
    {
        return $this->last_event_date;
    }

    /**
     * Set payment_method_type
     *
     * @param smallint $paymentMethodType
     */
    public function setPaymentMethodType($paymentMethodType)
    {
        $this->payment_method_type = $paymentMethodType;
    }

    /**
     * Get payment_method_type
     *
     * @return smallint 
     */
    public function getPaymentMethodType()
    {
        return $this->payment_method_type;
    }
    /**
     * Get payment_method_type label
     *
     * @return string
     */
    public function getPaymentMethodTypeLabel()
    {
        return \BFOS\PagseguroBundle\Utils\Pagseguro::$payment_method_type[$this->payment_method_type];
    }

    /**
     * Set payment_method_code
     *
     * @param smallint $paymentMethodCode
     */
    public function setPaymentMethodCode($paymentMethodCode)
    {
        $this->payment_method_code = $paymentMethodCode;
    }

    /**
     * Get payment_method_code
     *
     * @return smallint 
     */
    public function getPaymentMethodCode()
    {
        return $this->payment_method_code;
    }

    /**
     * Get payment_method_code label
     *
     * @return string
     */
    public function getPaymentMethodCodeLabel()
    {
        return \BFOS\PagseguroBundle\Utils\Pagseguro::$payment_method_code[$this->payment_method_code];
    }

    /**
     * Set type
     *
     * @param smallint $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * Get type
     *
     * @return smallint 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set code
     *
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set data_transacao
     *
     * @param string $dataTransacao
     */
    public function setDataTransacao($dataTransacao)
    {
        $this->data_transacao = $dataTransacao;
    }

    /**
     * Get data_transacao
     *
     * @return string 
     */
    public function getDataTransacao()
    {
        return $this->data_transacao;
    }

    /**
     * Set xml
     *
     * @param text $xml
     */
    public function setXml($xml)
    {
        $this->xml = $xml;
    }

    /**
     * Get xml
     *
     * @return text 
     */
    public function getXml()
    {
        return $this->xml;
    }

    /**
     * @param \DateTime $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param \DateTime $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
}