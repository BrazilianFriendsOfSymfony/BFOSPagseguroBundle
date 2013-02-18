<?php

namespace BFOS\PagseguroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use BFOS\PagseguroBundle\Entity\PagamentoItem;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ExecutionContext;

/**
 * BFOS\PagseguroBundle\Entity\Pagamento
 *
 * @ORM\Table(name="bfos_pagseguro_pagamento")
 * @ORM\Entity(repositoryClass="BFOS\PagseguroBundle\Entity\PagamentoRepository")
 *
 * @Assert\Callback(methods={"checaSeItensSaoValidos"})
 */
class Pagamento
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
     * Codificação de caracteres.
     *
     * Especifica a codificação de caracteres usada nos parâmetros enviados.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Os valores aceitos são ISO-8859-1 e UTF-8.
     *
     * @var string $charset
     *
     * @ORM\Column(name="charset", type="string", length=10, nullable=true)
     *
     * @Assert\Choice({ "ISO-8859-1", "UTF-8" })
     */
    private $charset;

    /**
     * Define um código para fazer referência ao pagamento. Este código fica associado à transação criada pelo pagamento e
     * é útil para vincular as transações do PagSeguro às vendas registradas no seu sistema.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre, com o limite de 200 caracteres.
     *
     * @var string $reference
     *
     * @ORM\Column(name="reference", type="string", length=200, nullable=true)
     *
     * @Assert\MaxLength(200)
     * @Assert\NotBlank()
     */
    private $reference;

    /**
     * Especifica o e-mail associado à conta PagSeguro que está realizando a chamada à API.
     *
     * Presença: Obrigatória.
     * Tipo: Texto.
     * Formato: um e-mail válido (p.e., usuario@site.com.br), com no máximo 60 caracteres.
     *
     * @var string $email
     *
     * @ORM\Column(name="email", type="string", length=60)
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     * @Assert\MaxLength(60)
     */
    private $email;

    /**
     * Informa o token correspondente à conta PagSeguro que está realizando a chamada a API. Para criar um token para sua conta PagSeguro,
     * acesse a página de configurações de pagamentos.
     *
     * Presença: Obrigatória.
     * Tipo: Texto.
     * Formato: uma sequência de 32 caracteres.
     *
     * @var string $token
     *
     * @Assert\MaxLength(32)
     * @Assert\MinLength(limit=32)
     * @Assert\NotBlank()
     */
    private $token;

    /**
     * Código identificador do pagamento criado. Este código deve ser usado para direcionar o comprador para o fluxo de pagamento.
     *
     *  Tipo: Texto.
     *  Formato: Uma sequência de 32 caracteres.
     *
     * @var string $retornoToken
     *
     * @ORM\Column(name="retorno_token", type="string", length=32, nullable=true)
     *
     * @Assert\MaxLength(32)
     * @Assert\MinLength(limit=32)
     */
    private $retornoToken;

    /**
     * Data de criação do código de pagamento.
     *
     * Tipo:Data/hora.
     * Formato: YYYY-MM-DDThh:mm:ss.sTZD, o formato oficial do W3C para datas..
     *
     * @var \DateTime $retornoEm
     *
     * @ORM\Column(name="retorno_em", type="datetime", nullable=true)
     *
     * @Assert\DateTime()
     */
    private $retornoEm;

    /**
     * Indica a moeda na qual o pagamento será feito. No momento, a única opção disponível é BRL (Real).
     *
     * Presença: Obrigatória.
     * Tipo: Texto.
     * Formato: somente o valor BRL é aceito.
     *
     * @var string $currency
     *
     * @ORM\Column(name="currency", type="string", length=3)
     *
     * @Assert\NotBlank()
     * @Assert\Choice({ "BRL" })
     */
    private $currency;

    /**
     * Especifica o e-mail do comprador que está realizando o pagamento. Este campo é opcional e você pode enviá-lo caso já tenha
     * capturado os dados do comprador em seu sistema e queira evitar que ele preencha esses dados novamente no PagSeguro.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: um e-mail válido (p.e., usuario@site.com.br), com no máximo 60 caracteres.
     *
     * @var string $senderEmail
     *
     * @ORM\Column(name="senderEmail", type="string", length=60, nullable=true)
     *
     * @Assert\Email()
     * @Assert\MaxLength(60)
     */
    private $senderEmail;

    /**
     * Especifica o nome completo do comprador que está realizando o pagamento. Este campo é opcional e você pode enviá-lo caso
     * já tenha capturado os dados do comprador em seu sistema e queira evitar que ele preencha esses dados novamente no PagSeguro.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: No mínimo duas sequências de caracteres, com o limite total de 50 caracteres
     *
     * @var string $senderName
     *
     * @ORM\Column(name="senderName", type="string", length=50, nullable=true)
     *
     * @Assert\MaxLength(50)
     * @Assert\Regex(pattern="/^.*(?=.*\s)(?=.*[a-zA-Z]).*$/", message="O nome tem que conter um espaço, caracterizando o nome completo..")
     */
    private $senderName;

    /**
     * Especifica o código de área (DDD) do comprador que está realizando o pagamento. Este campo é opcional e você pode enviá-lo
     * caso já tenha capturado os dados do comprador em seu sistema e queira evitar que ele preencha esses dados novamente no PagSeguro.
     *
     * Presença: Opcional.
     * Tipo: Número.
     * Formato: Um número de 2 dígitos correspondente a um DDD válido.
     *
     * @var string $senderAreaCode
     *
     * @ORM\Column(name="senderAreaCode", type="integer", nullable=true)
     *
     * @Assert\Max(limit=99)
     *
     */
    private $senderAreaCode;

    /**
     * Especifica o número do telefone do comprador que está realizando o pagamento. Este campo é opcional e você pode enviá-lo caso
     * já tenha capturado os dados do comprador em seu sistema e queira evitar que ele preencha esses dados novamente no PagSeguro.
     *
     * Presença: Opcional.
     * Tipo: Número.
     * Formato: Um número de 8 dígitos.
     *
     * @var string $senderPhone
     *
     * @ORM\Column(name="senderPhone", type="string", length=8, nullable=true)
     *
     * @Assert\Max(limit=99999999)
     */
    private $senderPhone;

    /**
     * Valor total do frete.

    Informa o valor total de frete do pedido. Caso este valor seja especificado, o PagSeguro irá assumi-lo como valor do frete e não fará nenhum cálculo referente aos pesos e valores de entrega dos itens.

    Presença: Opcional.
    Tipo: Número.
    Formato: Decimal, com duas casas decimais separadas por ponto (p.e, 1234.56), maior que 0.00 e menor ou igual a 9999999.00.
     *
     * @var float $shippingCost
     *
     * @ORM\Column(name="shippingCost", type="decimal", scale=2, nullable=true)
     */
    private $shippingCost;

    /**
     * Informa o tipo de frete a ser usado para o envio do produto. Esta informação é usada pelo PagSeguro para calcular, junto aos Correios,
     * o valor do frete a partir do peso dos itens. A tabela abaixo descreve os valores aceitos e seus significados:
     *
     * Código	Significado
     *      1	Encomenda normal (PAC).
     *      2	SEDEX
     *      3	Tipo de frete não especificado.
     *
     * Presença: Opcional.
     * Tipo: Número.
     * Formato: Um número inteiro de acordo com a tabela acima.
     *
     * @var string $shippingType
     *
     * @ORM\Column(name="shippingType", type="string", length=255, nullable=true)
     *
     * @Assert\Choice({ "1", "2", "3" })
     */
    private $shippingType;

    /**
     * Informa o país do endereço de envio do produto. Este campo é opcional e você pode enviá-lo caso já tenha capturado os dados do
     * comprador em seu sistema e queira evitar que ele preencha esses dados novamente no PagSeguro.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: No momento, apenas o valor BRA é permitido.
     *
     * @var string $shippingAddressCountry
     *
     * @ORM\Column(name="shippingAddressCountry", type="string", length=3, nullable=true)
     *
     * @Assert\Choice({ "BRA" })
     */
    private $shippingAddressCountry;

    /**
     * Informa o estado do endereço de envio do produto. Este campo é opcional e você pode enviá-lo caso já tenha capturado os dados do
     * comprador em seu sistema e queira evitar que ele preencha esses dados novamente no PagSeguro.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Duas letras, representando a sigla do estado brasileiro correspondente.
     *
     * @var string $shippingAddressState
     *
     * @ORM\Column(name="shippingAddressState", type="string", length=2, nullable=true)
     *
     * @Assert\MaxLength(2)
     * @Assert\MinLength(limit=2)
     */
    private $shippingAddressState;

    /**
     * Informa a cidade do endereço de envio do produto. Este campo é opcional e você pode enviá-lo caso já tenha capturado os dados do
     * comprador em seu sistema e queira evitar que ele preencha esses dados novamente no PagSeguro.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre. Deve ser um nome válido de cidade do Brasil, de acordo com os dados dos Correios
     *
     * @var string $shippingAddressCity
     *
     * @ORM\Column(name="shippingAddressCity", type="string", length=80, nullable=true)
     *
     * @Assert\MaxLength(80)
     */
    private $shippingAddressCity;

    /**
     * Informa o CEP do endereço de envio do produto. Este campo é opcional e você pode enviá-lo caso já tenha capturado os dados do
     * comprador em seu sistema e queira evitar que ele preencha esses dados novamente no PagSeguro.
     *
     * Presença: Opcional.
     * Tipo: Número.
     * Formato: Um número de 8 dígitos.
     *
     * @var string $shippingAddressPostalCode
     *
     * @ORM\Column(name="shippingAddressPostalCode", type="string", length=8, nullable=true)
     *
     * @Assert\Max(limit=99999999)
     */
    private $shippingAddressPostalCode;

    /**
     * Informa o bairro do endereço de envio do produto. Este campo é opcional e você pode enviá-lo caso já tenha capturado os dados do
     * comprador em seu sistema e queira evitar que ele preencha esses dados novamente no PagSeguro.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre.
     *
     * @var string $shippingAddressDistrict
     *
     * @ORM\Column(name="shippingAddressDistrict", type="string", length=80, nullable=true)
     *
     * @Assert\MaxLength(80)
     */
    private $shippingAddressDistrict;

    /**
     * Informa o nome da rua do endereço de envio do produto. Este campo é opcional e você pode enviá-lo caso já tenha capturado os
     * dados do comprador em seu sistema e queira evitar que ele preencha esses dados novamente no PagSeguro.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre.
     *
     * @var string $shippingAddressStreet
     *
     * @ORM\Column(name="shippingAddressStreet", type="string", length=120, nullable=true)
     *
     * @Assert\MaxLength(120)
     */
    private $shippingAddressStreet;

    /**
     * Informa o número do endereço de envio do produto. Este campo é opcional e você pode enviá-lo caso já tenha capturado os dados
     * do comprador em seu sistema e queira evitar que ele preencha esses dados novamente no PagSeguro.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre.
     *
     * @var string $shippingAddressNumber
     *
     * @ORM\Column(name="shippingAddressNumber", type="string", length=10, nullable=true)
     *
     * @Assert\MaxLength(10)
     */
    private $shippingAddressNumber;

    /**
     * Informa o complemento (bloco, apartamento, etc.) do endereço de envio do produto. Este campo é opcional e você pode enviá-lo
     * caso já tenha capturado os dados do comprador em seu sistema e queira evitar que ele preencha esses dados novamente no PagSeguro.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Livre.
     *
     * @var string $shippingAddressComplement
     *
     * @ORM\Column(name="shippingAddressComplement", type="string", length=120, nullable=true)
     *
     * @Assert\MaxLength(120)
     */
    private $shippingAddressComplement;

    /**
     * Especifica um valor extra que deve ser adicionado ou subtraído ao valor total do pagamento. Esse valor pode representar uma taxa
     * extra a ser cobrada no pagamento ou um desconto a ser concedido, caso o valor seja negativo.
     *
     * Presença: Opcional.
     * Tipo: Número.
     * Formato: Decimal (positivo ou negativo), com duas casas decimais separadas por ponto (p.e., 1234.56 ou -1234.56).
     *
     * @var float $extraAmount
     *
     * @ORM\Column(name="extraAmount", type="decimal", scale=2, nullable=true)
     */
    private $extraAmount;

    /**
     * Determina a URL para a qual o comprador será redirecionado após o final do fluxo de pagamento. Este parâmetro permite que seja
     * informado um endereço de específico para cada pagamento realizado. Veja mais em Redirecionando o comprador para um endereço dinâmico.
     *
     * Presença: Opcional.
     * Tipo: Texto.
     * Formato: Uma URL válida.
     *
     * @var string $redirectURL
     *
     * @ORM\Column(name="redirectURL", type="string", length=1000, nullable=true)
     *
     * @Assert\MaxLength(100)
     * @Assert\Url()
     */
    private $redirectURL;

    /**
     * Determina o número máximo de vezes que o código de pagamento criado pela chamada à API de Pagamentos poderá ser usado. Este
     * parâmetro pode ser usado como um controle de segurança.
     *
     * Presença: Opcional.
     * Tipo: Número.
     * Formato: Um número inteiro maior que zero.
     *
     * @var integer $maxUses
     *
     * @ORM\Column(name="maxUses", type="integer", nullable=true)
     *
     * @Assert\Min(limit="1")
     */
    private $maxUses;

    /**
     * Determina o prazo (em segundos) durante o qual o código de pagamento criado pela chamada à API de Pagamentos poderá ser usado.
     * Este parâmetro pode ser usado como um controle de segurança.
     *
     * Presença: Opcional.
     * Tipo: Número.
     * Formato: Um número inteiro maior ou igual a 30.
     *
     * @var integer $maxAge
     *
     * @ORM\Column(name="maxAge", type="integer", nullable=true)
     *
     * @Assert\Min(limit="30")
     */
    private $maxAge;

    /**
     *Código do erro. Identifica a natureza do erro encontrado e permite o tratamento do erro pelo seu sistema.
     *
     * Formato: Veja a tabela de erros no site do PagSeguro.
     *
     * @var string $registroErrors
     *
     * @ORM\Column(name="registroErrors", type="string", nullable=true)
     */
    private $registroErrors;

    /**
     * @var ArrayCollection $itens
     *
     * @ORM\OneToMany(targetEntity="PagamentoItem", mappedBy="pagamento", cascade={"all"})
     */
    private $itens;

    /**
     * @var string transaction_id
     *
     * @ORM\Column(name="transaction_id", type="string", length=50, nullable=true)
     */
    private $transaction_id;


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

        $this->itens = new ArrayCollection();
        $this->currency = 'BRL';
    }

    /**
     * @ORM\PreUpdate
     */
    function preUpdate(){
        $this->updated_at = new \DateTime('now');
        $this->updated_at->setTimezone(new \DateTimeZone('UTC'));
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
    }


    /**
     * @param \DateTime $created_at
     */
    public function setCreatedAt($created_at)
    {
        $created_at->setTimezone(new \DateTimeZone('UTC'));
        $this->created_at = $created_at;
    }

    /**
     * @param null $time_zone
     * @return \DateTime
     */
    public function getCreatedAt($time_zone = null)
    {
        $dateTime = clone $this->created_at;
        if(is_null($time_zone)){
            $time_zone = date_default_timezone_get();
        }
        $dateTime->setTimezone(new \DateTimeZone($time_zone));
        return $dateTime;
    }

    /**
     * @param \DateTime $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $updated_at->setTimezone(new \DateTimeZone('UTC'));
        $this->updated_at = $updated_at;
    }

    /**
     * @param string $time_zone
     *
     * @return \DateTime
     */
    public function getUpdatedAt($time_zone = null)
    {
        $dateTime = clone $this->updated_at;
        if(is_null($time_zone)){
            $time_zone = date_default_timezone_get();
        }
        $dateTime->setTimezone(new \DateTimeZone($time_zone));
        return $dateTime;
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
     * Set charset
     *
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * Get charset
     *
     * @return string 
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Set email
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
     * Set email
     *
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $retornoToken
     */
    public function setRetornoToken($retornoToken)
    {
        $this->retornoToken = $retornoToken;
    }

    /**
     * @return string
     */
    public function getRetornoToken()
    {
        return $this->retornoToken;
    }

    /**
     * @param \DateTime $retornoData
     */
    public function setRetornoEm($retornoData)
    {
        $this->retornoEm = $retornoData;
    }

    /**
     * @return \DateTime
     */
    public function getRetornoEm()
    {
        return $this->retornoEm;
    }

    /**
     * Set currency
     *
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * Get currency
     *
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set senderEmail
     *
     * @param string $senderEmail
     */
    public function setSenderEmail($senderEmail)
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * Get senderEmail
     *
     * @return string 
     */
    public function getSenderEmail()
    {
        return $this->senderEmail;
    }

    /**
     * Set senderName
     *
     * @param string $senderName
     */
    public function setSenderName($senderName)
    {
        $this->senderName = $senderName;
    }

    /**
     * Get senderName
     *
     * @return string 
     */
    public function getSenderName()
    {
        return $this->senderName;
    }

    /**
     * Set senderAreaCode
     *
     * @param string $senderAreaCode
     */
    public function setSenderAreaCode($senderAreaCode)
    {
        $this->senderAreaCode = $senderAreaCode;
    }

    /**
     * Get senderAreaCode
     *
     * @return string 
     */
    public function getSenderAreaCode()
    {
        return $this->senderAreaCode;
    }

    /**
     * Set senderPhone
     *
     * @param string $senderPhone
     */
    public function setSenderPhone($senderPhone)
    {
        $this->senderPhone = $senderPhone;
    }

    /**
     * Get senderPhone
     *
     * @return string 
     */
    public function getSenderPhone()
    {
        return $this->senderPhone;
    }

    /**
     * Set shippingType
     *
     * @param string $shippingType
     */
    public function setShippingType($shippingType)
    {
        $this->shippingType = $shippingType;
    }

    /**
     * Get shippingType
     *
     * @return string 
     */
    public function getShippingType()
    {
        return $this->shippingType;
    }

    /**
     * Set shippingAddressCountry
     *
     * @param string $shippingAddressCountry
     */
    public function setShippingAddressCountry($shippingAddressCountry)
    {
        $this->shippingAddressCountry = $shippingAddressCountry;
    }

    /**
     * Get shippingAddressCountry
     *
     * @return string 
     */
    public function getShippingAddressCountry()
    {
        return $this->shippingAddressCountry;
    }

    /**
     * Set shippingAddressState
     *
     * @param string $shippingAddressState
     */
    public function setShippingAddressState($shippingAddressState)
    {
        $this->shippingAddressState = $shippingAddressState;
    }

    /**
     * Get shippingAddressState
     *
     * @return string 
     */
    public function getShippingAddressState()
    {
        return $this->shippingAddressState;
    }

    /**
     * Set shippingAddressCity
     *
     * @param string $shippingAddressCity
     */
    public function setShippingAddressCity($shippingAddressCity)
    {
        $this->shippingAddressCity = $shippingAddressCity;
    }

    /**
     * Get shippingAddressCity
     *
     * @return string 
     */
    public function getShippingAddressCity()
    {
        return $this->shippingAddressCity;
    }

    /**
     * Set shippingAddressPostalCode
     *
     * @param string $shippingAddressPostalCode
     */
    public function setShippingAddressPostalCode($shippingAddressPostalCode)
    {
        $this->shippingAddressPostalCode = $shippingAddressPostalCode;
    }

    /**
     * Get shippingAddressPostalCode
     *
     * @return string 
     */
    public function getShippingAddressPostalCode()
    {
        return $this->shippingAddressPostalCode;
    }

    /**
     * Set shippingAddressDistrict
     *
     * @param string $shippingAddressDistrict
     */
    public function setShippingAddressDistrict($shippingAddressDistrict)
    {
        $this->shippingAddressDistrict = $shippingAddressDistrict;
    }

    /**
     * Get shippingAddressDistrict
     *
     * @return string 
     */
    public function getShippingAddressDistrict()
    {
        return $this->shippingAddressDistrict;
    }

    /**
     * Set shippingAddressStreet
     *
     * @param string $shippingAddressStreet
     */
    public function setShippingAddressStreet($shippingAddressStreet)
    {
        $this->shippingAddressStreet = $shippingAddressStreet;
    }

    /**
     * Get shippingAddressStreet
     *
     * @return string 
     */
    public function getShippingAddressStreet()
    {
        return $this->shippingAddressStreet;
    }

    /**
     * Set shippingAddressNumber
     *
     * @param string $shippingAddressNumber
     */
    public function setShippingAddressNumber($shippingAddressNumber)
    {
        $this->shippingAddressNumber = $shippingAddressNumber;
    }

    /**
     * Get shippingAddressNumber
     *
     * @return string 
     */
    public function getShippingAddressNumber()
    {
        return $this->shippingAddressNumber;
    }

    /**
     * Set shippingAddressComplement
     *
     * @param string $shippingAddressComplement
     */
    public function setShippingAddressComplement($shippingAddressComplement)
    {
        $this->shippingAddressComplement = $shippingAddressComplement;
    }

    /**
     * Get shippingAddressComplement
     *
     * @return string 
     */
    public function getShippingAddressComplement()
    {
        return $this->shippingAddressComplement;
    }

    /**
     * Set extraAmount
     *
     * @param float $extraAmount
     */
    public function setExtraAmount($extraAmount)
    {
        $this->extraAmount = $extraAmount;
    }

    /**
     * Get extraAmount
     *
     * @return float
     */
    public function getExtraAmount()
    {
        return $this->extraAmount;
    }

    /**
     * Set redirectURL
     *
     * @param string $redirectURL
     */
    public function setRedirectURL($redirectURL)
    {
        $this->redirectURL = $redirectURL;
    }

    /**
     * Get redirectURL
     *
     * @return string 
     */
    public function getRedirectURL()
    {
        return $this->redirectURL;
    }

    /**
     * Set maxUses
     *
     * @param integer $maxUses
     */
    public function setMaxUses($maxUses)
    {
        $this->maxUses = $maxUses;
    }

    /**
     * Get maxUses
     *
     * @return integer 
     */
    public function getMaxUses()
    {
        return $this->maxUses;
    }

    /**
     * Set maxAge
     *
     * @param integer $maxAge
     */
    public function setMaxAge($maxAge)
    {
        $this->maxAge = $maxAge;
    }

    /**
     * Get maxAge
     *
     * @return integer 
     */
    public function getMaxAge()
    {
        return $this->maxAge;
    }

    /**
     * @param string $registroErrors
     */
    public function setRegistroErrors($registroErrors)
    {
        $this->registroErrors = $registroErrors;
    }

    /**
     * @return string
     */
    public function getRegistroErrors()
    {
        return $this->registroErrors;
    }

    /**
     * @param ArrayCollection $itens
     */
    public function setItens($itens)
    {
        $this->itens = new ArrayCollection();
        foreach($itens as $item){
            $this->addItem($item);
        }
    }

    public function addItem(PagamentoItem $item){
        $item->setPagamento($this);
        $this->itens[] = $item;
    }

    /**
     * @return ArrayCollection
     */
    public function getItens()
    {
        return $this->itens;
    }

    // retorna um array com as propriedades desta classe
    public function toArray(){
        $arr = array();
        $arr['id'] = $this->getId();
        $arr['charsert'] = $this->getCharset();
        $arr['currency'] = $this->getCurrency();
        $arr['email'] = $this->getEmail();
        $arr['extraAmount'] = $this->getExtraAmount();
        $arr['maxAge'] = $this->getMaxAge();
        $arr['maxUses'] = $this->getMaxUses();
        $arr['redirectURL'] = $this->getRedirectURL();
        $arr['reference'] = $this->getReference();
        $arr['senderAreaCode'] = $this->getSenderAreaCode();
        $arr['senderEmail'] = $this->getSenderEmail();
        $arr['senderName'] = $this->getSenderName();
        $arr['senderPhone'] = $this->getSenderPhone();
        $arr['shippingAddressCity'] = $this->getShippingAddressCity();
        $arr['shippingAddressStreet'] = $this->getShippingAddressStreet();
        $arr['shippingAddressNumber'] = $this->getShippingAddressNumber();
        $arr['shippingAddressComplement'] = $this->getShippingAddressComplement();
        $arr['shippingAddressDistrict'] = $this->getShippingAddressDistrict();
        $arr['shippingAddressPostalCode'] = $this->getShippingAddressPostalCode();
        $arr['shippingAddressState'] = $this->getShippingAddressState();
        $arr['shippingAddressCountry'] = $this->getShippingAddressCountry();
        $arr['shippingType'] = $this->getShippingType();
        $arr['shippingCost'] = $this->getShippingCost();
        $arr['token'] = $this->getToken();

        $arr_itens = array();
        /**
         * @var PagamentoItem $item
         */
        foreach($this->getItens() as $item){
            $arr_itens[] = $item->toArray();
        }
        $arr['itens'] = $arr_itens;
        return $arr;
    }

    // retorna uma string no formato XML para o envio das informações de pagamento
    public function toXML($encoding = 'UTF-8'){
        if(!in_array($encoding, array('UTF-8','ISO-8859-1'))){
            throw new \Exception('Encoding invalido.');
        }
        $xml = '';

        $tag = 'currency';
        $value = mb_convert_encoding($this->getCurrency(), $encoding, 'UTF-8');
        $xml .= sprintf('<%s>%s</%s>',$tag,$value,$tag);

        $tag = 'reference';
        $value = $this->getReference();
        if($this->getReference()){
            $xml .= sprintf('<%s>%s</%s>',$tag,$value,$tag);
        }

        if($this->getExtraAmount()){
            $tag = 'extraAmount';
            $value = number_format($this->getExtraAmount(), 2, '.', '');
            $xml .= sprintf('<%s>%s</%s>',$tag,$value,$tag);
        }

        $tag = 'redirectURL';
        $value = $this->getRedirectURL();
        if($this->getRedirectURL()){
            $xml .= sprintf('<%s><![CDATA[%s]]></%s>',$tag,$value,$tag);
        }

        $tag = 'sender';
        $value = '';
        if($this->getSenderName()) {
            $value .= sprintf('<%s>%s</%s>','name',$this->getSenderName(),'name');
        }
        if($this->getSenderEmail()) {
            $value .= sprintf('<%s>%s</%s>','email',$this->getSenderEmail(),'email');
        }

        $value2 = '';
        $j = 0;
        if($this->getSenderAreaCode()) { $value2 .= sprintf('<%s>%s</%s>','areaCode',$this->getSenderAreaCode(),'areaCode'); $j++;}
        if($this->getSenderPhone()) { $value2 .= sprintf('<%s>%s</%s>','number',$this->getSenderPhone(),'number');$j++;}
        if($j) {
            $value .= sprintf('<%s>%s</%s>','phone',$value2,'phone');
            $xml .= sprintf('<%s>%s</%s>',$tag,$value,$tag);
        }

        $xml_itens = '';
        /**
         * @var PagamentoItem $item
         */
        foreach($this->getItens() as $item){
            $xml_itens .= sprintf('<%s>%s</%s>','item',$item->toXML(),'item');
        }
        $xml .= sprintf('<%s>%s</%s>','items',$xml_itens,'items');

        $tag = 'shipping';
        $value = '';
        $i = 0;
        if($this->getShippingType()) { $value .= sprintf('<%s>%s</%s>','type', $this->getShippingType(),'type'); $i++;}
        if($this->getShippingCost()) { $value .= sprintf('<%s>%s</%s>','cost', number_format($this->getShippingCost(),2, '.', ''),'cost'); $i++;}
        if($this->getShippingAddressStreet()) { $value2 = sprintf('<%s>%s</%s>','street', $this->getShippingAddressStreet(),'street');$i++;}
        if($this->getShippingAddressNumber()) { $value2 .= sprintf('<%s>%s</%s>','number', $this->getShippingAddressNumber(),'number');$i++;}
        if($this->getShippingAddressComplement()) { $value2 .= sprintf('<%s>%s</%s>','complement', $this->getShippingAddressComplement(),'complement');$i++;}
        if($this->getShippingAddressDistrict()) { $value2 .= sprintf('<%s>%s</%s>','district', $this->getShippingAddressDistrict(),'district');$i++;}
        if($this->getShippingAddressPostalCode()) { $value2 .= sprintf('<%s>%s</%s>','postalCode', $this->getShippingAddressPostalCode(),'postalCode');$i++;}
        if($this->getShippingAddressCity()) { $value2 .= sprintf('<%s>%s</%s>','city', $this->getShippingAddressCity(),'city');$i++;}
        if($this->getShippingAddressState()) { $value2 .= sprintf('<%s>%s</%s>','state', $this->getShippingAddressState(),'state');$i++;}
        if($this->getShippingAddressCountry()) { $value2 .= sprintf('<%s>%s</%s>','country', $this->getShippingAddressCountry(),'country');$i++;}
        if($i) {
            $value .= sprintf('<%s>%s</%s>','address', $value2,'address');
            $xml .= sprintf('<%s>%s</%s>', $tag, $value, $tag);
        }

        return $xml;
    }

    // retorna a URL de redirecionamento para o site do PagSeguro
    public function getUrlPagseguro(){
        if($this->getRegistroErrors()){
            return null;
        } else {
            return 'https://pagseguro.uol.com.br/v2/checkout/payment.html?code=' . $this->getRetornoToken();
        }
    }


    public function checaSeItensSaoValidos(ExecutionContext $context){
        if(count($this->getItens())==0){
            $context->addViolationAtSubPath('itens', 'É preciso pelo menos um item no Pagamento.');
        }
    }

    /**
     * @param string $transaction_id
     */
    public function setTransactionId($transaction_id)
    {
        $this->transaction_id = $transaction_id;
    }

    /**
     * @return string
     */
    public function getTransactionId()
    {
        return $this->transaction_id;
    }

    /**
     * Set shippingCost
     *
     * @param float $shippingCost
     * @return Pagamento
     */
    public function setShippingCost($shippingCost)
    {
        $this->shippingCost = $shippingCost;
    
        return $this;
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

}