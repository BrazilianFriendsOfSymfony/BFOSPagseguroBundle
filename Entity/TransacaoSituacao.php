<?php

namespace BFOS\PagseguroBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * BFOS\PagseguroBundle\Entity\TransacaoSituacao
 *
 * @ORM\Table(name="bfos_pagseguro_transacao_situacao")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 */
class TransacaoSituacao
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
     * @var integer $situacao
     *
     * @ORM\Column(name="situacao", type="string", length=50)
     */
    private $situacao;

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


    /**
     * @param int $situacao
     */
    public function setSituacao($situacao)
    {
        $this->situacao = $situacao;
    }

    /**
     * @return int
     */
    public function getSituacao()
    {
        return $this->situacao;
    }

}