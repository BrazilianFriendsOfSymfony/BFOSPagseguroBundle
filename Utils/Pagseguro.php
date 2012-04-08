<?php
namespace BFOS\PagseguroBundle\Utils;

class Pagseguro
{
    static $shipping_types = array(
        1 => 'Encomenda normal (PAC)',
        2 => 'SEDEX',
        3 => 'Tipo de frete não especificado'
    );

    static $transaction_type = array(
        1 => 'Pagamento',
        7 => 'Bônus'
    );

    static $transaction_type_description = array(
        1 => 'Pagamento: a transação foi criada por um comprador fazendo um pagamento. Este é o tipo mais comum de transação que você irá receber.',
        7 => 'Bônus: a transação corresponde a um bônus recebido do PagSeguro.'
    );

    static $transaction_status = array(
        1 => 'Aguardando pagamento',
        2 => 'Em análise',
        3 => 'Paga',
        4 => 'Disponível',
        5 => 'Em disputa',
        6 => 'Devolvida',
        7 => 'Cancelada'
    );

    static $transaction_status_description = array(
        1 => 'Aguardando pagamento: o comprador iniciou a transação, mas até o momento o PagSeguro não recebeu nenhuma informação sobre o pagamento.',
        2 => 'Em análise: o comprador optou por pagar com um cartão de crédito e o PagSeguro está analisando o risco da transação.',
        3 => 'Paga: a transação foi paga pelo comprador e o PagSeguro já recebeu uma confirmação da instituição financeira responsável pelo processamento.',
        4 => 'Disponível: a transação foi paga e chegou ao final de seu prazo de liberação sem ter sido retornada e sem que haja nenhuma disputa aberta.',
        5 => 'Em disputa: o comprador, dentro do prazo de liberação da transação, abriu uma disputa.',
        6 => 'Devolvida: o valor da transação foi devolvido para o comprador.',
        7 => 'Cancelada: a transação foi cancelada sem ter sido finalizada.'
    );

    static $payment_method_type = array(
        1 => 'Cartão de crédito',
        2 => 'Boleto',
        3 => 'Débito online (TEF)',
        4 => 'Saldo PagSeguro',
        5 => 'Oi Paggo'
    );

    static $payment_method_code = array(
        101 => 'Cartão de crédito Visa',
        102 => 'Cartão de crédito MasterCard',
        103 => 'Cartão de crédito American Express',
        104 => 'Cartão de crédito Diners',
        105 => 'Cartão de crédito Hipercard',
        106 => 'Cartão de crédito Aura',
        107 => 'Cartão de crédito Elo',
        108 => 'Cartão de crédito PLENOCard',
        109 => 'Cartão de crédito PersonalCard',
        201 => 'Boleto Bradesco',
        202 => 'Boleto Santander',
        301 => 'Débito online Bradesco',
        302 => 'Débito online Itaú',
        303 => 'Débito online Unibanco',
        304 => 'Débito online Banco do Brasil',
        305 => 'Débito online Banco Real',
        306 => 'Débito online Banrisul',
        307 => 'Débito online HSBC',
        401 => 'Saldo PagSeguro',
        501 => 'Oi Paggo'
    );

    /**
     * @static
     * @param $str TipoFrete enviado no retorno automatico do Pagseguro.
     * @return int
     */
    static public function getShippingTypeFromTipoFrete($str){
        if($str=='FR'){
            return 3;
        }
        return null;
    }

    static public function getPaymentMethodTypeFromTipoPagamento($str){
        if($str=='Boleto'){
            return 2;
        }
        return null;
    }

    static public function getStatusFromStatusTransacao($str){
        if($str=='Aguardando Pagto'){
            return 1;
        } else if($str=='Aprovado'){
            return 3;
        }
        return null;
    }
}
