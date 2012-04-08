<?php

namespace BFOS\PagseguroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BFOS\PagseguroBundle\Entity\Transacao
 *
 * @ORM\Table(name="bfos_pagseguro_transacao")
 * @ORM\Entity(repositoryClass="BFOS\PagseguroBundle\Entity\TransacaoRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Transacao
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
     * @var \DateTime $data_transacao
     *
     * @ORM\Column(name="data_transacao", type="datetime", nullable=true)
     */
    private $data_transacao;

    /**
     * @var string $transacao_id
     *
     * @ORM\Column(name="transacao_id", type="string", length=40, nullable=true)
     */
    private $transacao_id;

    /**
     * @var string $vendedor_email
     *
     * @ORM\Column(name="vendedor_email", type="string", length=60, nullable=true)
     */
    private $vendedor_email;

    /**
     * @var string $reference
     *
     * @ORM\Column(name="reference", type="string", length=200, nullable=true)
     */
    private $reference;

    /**
     * @var smallint $type
     *
     * @ORM\Column(name="type", type="smallint", nullable=true)
     */
    private $type;

    /**
     * @var smallint $status
     *
     * @ORM\Column(name="status", type="smallint", nullable=true)
     */
    private $status;

    /**
     * @var smallint $payment_method_type
     *
     * @ORM\Column(name="payment_method_type", type="smallint", nullable=true)
     */
    private $payment_method_type;

    /**
     * @var smallint $payment_method_code
     *
     * @ORM\Column(name="payment_method_code", type="smallint", nullable=true)
     */
    private $payment_method_code;

    /**
     * @var string $last_event_date
     *
     * @ORM\Column(name="last_event_date", type="string", length=30, nullable=true)
     */
    private $last_event_date;

    /**
     * @var decimal $gross_amount
     *
     * @ORM\Column(name="gross_amount", type="decimal", scale=2, nullable=true)
     */
    private $gross_amount;

    /**
     * @var decimal $discount_amount
     *
     * @ORM\Column(name="discount_amount", type="decimal", scale=2, nullable=true)
     */
    private $discount_amount;

    /**
     * @var decimal $fee_amount
     *
     * @ORM\Column(name="fee_amount", type="decimal", scale=2, nullable=true)
     */
    private $fee_amount;

    /**
     * @var decimal $net_amount
     *
     * @ORM\Column(name="net_amount", type="decimal", scale=2, nullable=true)
     */
    private $net_amount;

    /**
     * @var decimal $extra_amount
     *
     * @ORM\Column(name="extra_amount", type="decimal", scale=2, nullable=true)
     */
    private $extra_amount;

    /**
     * @var smallint $installment_count
     *
     * @ORM\Column(name="installment_count", type="smallint", nullable=true)
     */
    private $installment_count;

    /**
     * @var smallint $item_count
     *
     * @ORM\Column(name="item_count", type="smallint", nullable=true)
     */
    private $item_count;

    /**
     * @var string $sender_name
     *
     * @ORM\Column(name="sender_name", type="string", length=50, nullable=true)
     */
    private $sender_name;

    /**
     * @var string $sender_email
     *
     * @ORM\Column(name="sender_email", type="string", length=60, nullable=true)
     */
    private $sender_email;

    /**
     * @var string $sender_phone_areacode
     *
     * @ORM\Column(name="sender_phone_areacode", type="string", length=2, nullable=true)
     */
    private $sender_phone_areacode;

    /**
     * @var string $sender_phone_number
     *
     * @ORM\Column(name="sender_phone_number", type="string", length=8, nullable=true)
     */
    private $sender_phone_number;

    /**
     * @var string $shipping_address_street
     *
     * @ORM\Column(name="shipping_address_street", type="string", length=255, nullable=true)
     */
    private $shipping_address_street;

    /**
     * @var string $shipping_address_country
     *
     * @ORM\Column(name="shipping_address_country", type="string", length=3, nullable=true)
     */
    private $shipping_address_country;

    /**
     * @var string $shipping_address_state
     *
     * @ORM\Column(name="shipping_address_state", type="string", length=2, nullable=true)
     */
    private $shipping_address_state;

    /**
     * @var string $shipping_address_city
     *
     * @ORM\Column(name="shipping_address_city", type="string", length=120, nullable=true)
     */
    private $shipping_address_city;

    /**
     * @var string $shipping_address_postalcode
     *
     * @ORM\Column(name="shipping_address_postalcode", type="string", length=8, nullable=true)
     */
    private $shipping_address_postalcode;

    /**
     * @var string $shipping_address_district
     *
     * @ORM\Column(name="shipping_address_district", type="string", length=120, nullable=true)
     */
    private $shipping_address_district;

    /**
     * @var string $shipping_address_number
     *
     * @ORM\Column(name="shipping_address_number", type="string", length=20, nullable=true)
     */
    private $shipping_address_number;

    /**
     * @var string $shipping_address_complement
     *
     * @ORM\Column(name="shipping_address_complement", type="string", length=255, nullable=true)
     */
    private $shipping_address_complement;

    /**
     * @var string $shipping_type
     *
     * @ORM\Column(name="shipping_type", type="smallint", nullable=true)
     */
    private $shipping_type;

    /**
     * @var string $shipping_cost
     *
     * @ORM\Column(name="shipping_cost", type="decimal", scale=2, nullable=true)
     */
    private $shipping_cost;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $itens
     *
     * @ORM\OneToMany(targetEntity="\BFOS\PagseguroBundle\Entity\TransacaoItem", mappedBy="transacao")
     */
    private $itens;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection $situacoes
     *
     * @ORM\OneToMany(targetEntity="\BFOS\PagseguroBundle\Entity\TransacaoSituacao", mappedBy="transacao")
     */
    private $situacoes;

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
        $this->created_at = new \DateTime('now');
        $this->created_at->setTimezone(new \DateTimeZone('UTC'));
        $this->updated_at = new \DateTime('now');
        $this->updated_at->setTimezone(new \DateTimeZone('UTC'));
    }

    /**
     * @ORM\PreUpdate
     */
    function preUpdate(){
        $this->updated_at = new \DateTime('now');
        $this->updated_at->setTimezone(new \DateTimeZone('UTC'));
        if($this->data_transacao){
            $this->data_transacao->setTimezone(new \DateTimeZone('UTC'));
        }
    }

    /**
     * @ORM\PostLoad
     */
    function postLoad(){
        if($this->updated_at){
            $this->updated_at = new \DateTime($this->updated_at->format('Y-m-d H:i:s'), new \DateTimeZone('UTC'));
        }
        if($this->created_at){
            $this->created_at = new \DateTime($this->created_at->format('Y-m-d H:i:s'), new \DateTimeZone('UTC'));
        }
        if($this->data_transacao){
            $this->data_transacao = new \DateTime($this->data_transacao->format('Y-m-d H:i:s'), new \DateTimeZone('UTC'));
        }
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
     * Set data_transacao
     *
     * @param \DateTime $dataTransacao
     */
    public function setDataTransacao($dataTransacao)
    {
        $dataTransacao->setTimezone(new \DateTimeZone('UTC'));
        $this->data_transacao = $dataTransacao;
    }

    /**
     * Get data_transacao
     *
     * @return \DateTime
     */
    public function getDataTransacao($time_zone = null)
    {
        if($this->data_transacao){
            $dateTime = clone $this->data_transacao;
            if(is_null($time_zone)){
                $time_zone = date_default_timezone_get();
            }
            $dateTime->setTimezone(new \DateTimeZone($time_zone));
        } else {
            $dateTime = $this->data_transacao;
        }
        return $dateTime;
    }

    /**
     * Set transacao_id
     *
     * @param string $transacaoId
     */
    public function setTransacaoId($transacaoId)
    {
        $this->transacao_id = $transacaoId;
    }

    /**
     * Get transacao_id
     *
     * @return string 
     */
    public function getTransacaoId()
    {
        return $this->transacao_id;
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
     * Get type label
     *
     * @return string
     */
    public function getTypeLabel()
    {
        return \BFOS\PagseguroBundle\Utils\Pagseguro::$transaction_type[$this->type];
    }

    /**
     * Get type description
     *
     * @return string
     */
    public function getTypeDescription()
    {
        return \BFOS\PagseguroBundle\Utils\Pagseguro::$transaction_type_description[$this->type];
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
     * Set last_event_date
     *
     * @param string $lastEventDate
     */
    public function setLastEventDate($lastEventDate)
    {
        $this->last_event_date = $lastEventDate;
    }

    /**
     * Get last_event_date
     *
     * @return string 
     */
    public function getLastEventDate()
    {
        return $this->last_event_date;
    }

    /**
     * Set gross_amount
     *
     * @param decimal $grossAmount
     */
    public function setGrossAmount($grossAmount)
    {
        $this->gross_amount = $grossAmount;
    }

    /**
     * Get gross_amount
     *
     * @return decimal 
     */
    public function getGrossAmount()
    {
        return $this->gross_amount;
    }

    /**
     * Set discount_amount
     *
     * @param decimal $discountAmount
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->discount_amount = $discountAmount;
    }

    /**
     * Get discount_amount
     *
     * @return decimal 
     */
    public function getDiscountAmount()
    {
        return $this->discount_amount;
    }

    /**
     * Set fee_amount
     *
     * @param decimal $feeAmount
     */
    public function setFeeAmount($feeAmount)
    {
        $this->fee_amount = $feeAmount;
    }

    /**
     * Get fee_amount
     *
     * @return decimal 
     */
    public function getFeeAmount()
    {
        return $this->fee_amount;
    }

    /**
     * Set net_amount
     *
     * @param decimal $netAmount
     */
    public function setNetAmount($netAmount)
    {
        $this->net_amount = $netAmount;
    }

    /**
     * Get net_amount
     *
     * @return decimal 
     */
    public function getNetAmount()
    {
        return $this->net_amount;
    }

    /**
     * Set extra_amount
     *
     * @param decimal $extraAmount
     */
    public function setExtraAmount($extraAmount)
    {
        $this->extra_amount = $extraAmount;
    }

    /**
     * Get extra_amount
     *
     * @return decimal 
     */
    public function getExtraAmount()
    {
        return $this->extra_amount;
    }

    /**
     * Set installment_count
     *
     * @param smallint $installmentCount
     */
    public function setInstallmentCount($installmentCount)
    {
        $this->installment_count = $installmentCount;
    }

    /**
     * Get installment_count
     *
     * @return smallint 
     */
    public function getInstallmentCount()
    {
        return $this->installment_count;
    }

    /**
     * Set item_count
     *
     * @param smallint $itemCount
     */
    public function setItemCount($itemCount)
    {
        $this->item_count = $itemCount;
    }

    /**
     * Get item_count
     *
     * @return smallint 
     */
    public function getItemCount()
    {
        return $this->item_count;
    }

    /**
     * Set sender_name
     *
     * @param string $senderName
     */
    public function setSenderName($senderName)
    {
        $this->sender_name = $senderName;
    }

    /**
     * Get sender_name
     *
     * @return string 
     */
    public function getSenderName()
    {
        return $this->sender_name;
    }

    /**
     * Set sender_email
     *
     * @param string $senderEmail
     */
    public function setSenderEmail($senderEmail)
    {
        $this->sender_email = $senderEmail;
    }

    /**
     * Get sender_email
     *
     * @return string 
     */
    public function getSenderEmail()
    {
        return $this->sender_email;
    }

    /**
     * Set sender_phone_areacode
     *
     * @param string $senderPhoneAreacode
     */
    public function setSenderPhoneAreacode($senderPhoneAreacode)
    {
        $this->sender_phone_areacode = $senderPhoneAreacode;
    }

    /**
     * Get sender_phone_areacode
     *
     * @return string 
     */
    public function getSenderPhoneAreacode()
    {
        return $this->sender_phone_areacode;
    }

    /**
     * Set sender_phone_number
     *
     * @param string $senderPhoneNumber
     */
    public function setSenderPhoneNumber($senderPhoneNumber)
    {
        $this->sender_phone_number = $senderPhoneNumber;
    }

    /**
     * Get sender_phone_number
     *
     * @return string 
     */
    public function getSenderPhoneNumber()
    {
        return $this->sender_phone_number;
    }

    /**
     * Set shipping_address_street
     *
     * @param string $shippingAddressStreet
     */
    public function setShippingAddressStreet($shippingAddressStreet)
    {
        $this->shipping_address_street = $shippingAddressStreet;
    }

    /**
     * Get shipping_address_street
     *
     * @return string 
     */
    public function getShippingAddressStreet()
    {
        return $this->shipping_address_street;
    }

    /**
     * Set shipping_address_country
     *
     * @param string $shippingAddressCountry
     */
    public function setShippingAddressCountry($shippingAddressCountry)
    {
        $this->shipping_address_country = $shippingAddressCountry;
    }

    /**
     * Get shipping_address_country
     *
     * @return string 
     */
    public function getShippingAddressCountry()
    {
        return $this->shipping_address_country;
    }

    /**
     * Set shipping_address_state
     *
     * @param string $shippingAddressState
     */
    public function setShippingAddressState($shippingAddressState)
    {
        $this->shipping_address_state = $shippingAddressState;
    }

    /**
     * Get shipping_address_state
     *
     * @return string 
     */
    public function getShippingAddressState()
    {
        return $this->shipping_address_state;
    }

    /**
     * Set shipping_address_city
     *
     * @param string $shippingAddressCity
     */
    public function setShippingAddressCity($shippingAddressCity)
    {
        $this->shipping_address_city = $shippingAddressCity;
    }

    /**
     * Get shipping_address_city
     *
     * @return string 
     */
    public function getShippingAddressCity()
    {
        return $this->shipping_address_city;
    }

    /**
     * Set shipping_address_postalcode
     *
     * @param string $shippingAddressPostalcode
     */
    public function setShippingAddressPostalcode($shippingAddressPostalcode)
    {
        $this->shipping_address_postalcode = $shippingAddressPostalcode;
    }

    /**
     * Get shipping_address_postalcode
     *
     * @return string 
     */
    public function getShippingAddressPostalcode()
    {
        return $this->shipping_address_postalcode;
    }

    /**
     * Set shipping_address_district
     *
     * @param string $shippingAddressDistrict
     */
    public function setShippingAddressDistrict($shippingAddressDistrict)
    {
        $this->shipping_address_district = $shippingAddressDistrict;
    }

    /**
     * Get shipping_address_district
     *
     * @return string 
     */
    public function getShippingAddressDistrict()
    {
        return $this->shipping_address_district;
    }

    /**
     * Set shipping_address_number
     *
     * @param string $shippingAddressNumber
     */
    public function setShippingAddressNumber($shippingAddressNumber)
    {
        $this->shipping_address_number = $shippingAddressNumber;
    }

    /**
     * Get shipping_address_number
     *
     * @return string 
     */
    public function getShippingAddressNumber()
    {
        return $this->shipping_address_number;
    }

    /**
     * Set shipping_address_complement
     *
     * @param string $shippingAddressComplement
     */
    public function setShippingAddressComplement($shippingAddressComplement)
    {
        $this->shipping_address_complement = $shippingAddressComplement;
    }

    /**
     * Get shipping_address_complement
     *
     * @return string 
     */
    public function getShippingAddressComplement()
    {
        return $this->shipping_address_complement;
    }

    public function addItem(TransacaoItem $item){
        $item->setTransacao($this);
        $this->itens[] = $item;
    }

    /**
     * Set itens
     *
     * @param array $itens
     */
    public function setItens($itens)
    {
        $this->itens = $itens;
    }

    /**
     * Get itens
     *
     * @return array 
     */
    public function getItens()
    {
        return $this->itens;
    }

    /**
     * @param string $vendedor_email
     */
    public function setVendedorEmail($vendedor_email)
    {
        $this->vendedor_email = $vendedor_email;
    }

    /**
     * @return string
     */
    public function getVendedorEmail()
    {
        return $this->vendedor_email;
    }

    /**
     * @param float $shipping_cost
     */
    public function setShippingCost($shipping_cost)
    {
        $this->shipping_cost = $shipping_cost;
    }

    /**
     * @return float
     */
    public function getShippingCost()
    {
        return $this->shipping_cost;
    }

    /**
     * @param string $shipping_type
     */
    public function setShippingType($shipping_type)
    {
        $this->shipping_type = $shipping_type;
    }

    /**
     * @return string
     */
    public function getShippingType()
    {
        return $this->shipping_type;
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

    public function addSituacao(TransacaoSituacao $situacao){
        $situacao->setTransacao($this);
        $this->situacoes[] = $situacao;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $situacoes
     */
    public function setSituacoes($situacoes)
    {
        $this->situacoes = $situacoes;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSituacoes()
    {
        return $this->situacoes;
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