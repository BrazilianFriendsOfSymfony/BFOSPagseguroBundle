<?php
namespace BFOS\PagseguroBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use BFOS\PagseguroBundle\Entity\Transacao;

class RetornoAutomaticoEvent extends Event
{
    protected $transacao;

    public function __construct(Transacao $transacao)
    {
        $this->transacao = $transacao;
    }

    public function getTransacao()
    {
        return $this->transacao;
    }
}
