<?php
namespace BFOS\PagseguroBundle\Utils;

class Browser
{
    static public function postXml($url, $xml){

        $retorno = array();

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/xml; charset=ISO-8859-1'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $retorno['corpo'] = curl_exec($ch);
        $retorno['info'] = curl_getinfo($ch);

        $retorno['erro_num'] = curl_errno($ch);
        $retorno['erro_msg'] = curl_error($ch);
        curl_close($ch);

        return $retorno;
    }

    static public function get($url){
        return file_get_contents($url);
    }
}
