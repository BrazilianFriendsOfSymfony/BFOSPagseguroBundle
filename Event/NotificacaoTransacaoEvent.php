<?php
namespace BFOS\PagseguroBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use BFOS\PagseguroBundle\Entity\NotificacaoTransacao;

class NotificacaoTransacaoEvent extends Event
{
    protected $notificacao_transacao;

    public function __construct(NotificacaoTransacao $notificacao_transacao)
    {
        $this->notificacao_transacao = $notificacao_transacao;
    }

    public function getNotificacaoTransacao()
    {
        return $this->notificacao_transacao;
    }
}
