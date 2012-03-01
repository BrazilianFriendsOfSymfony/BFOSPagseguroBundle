<?php
/**
 * Created by JetBrains PhpStorm.
 * User: duo
 * Date: 2/25/12
 * Time: 3:26 PM
 * To change this template use File | Settings | File Templates.
 */

namespace BFOS\PagseguroBundle\Tests\Utils;

class ConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayToXML(){
        $conv = new \BFOS\PagseguroBundle\Utils\Converter();

        // TESTA UMA ARRAY SIMPLES
        $arr1 = array('var1' => 'value1', 'var2'=>'value2');
        $xml = $conv->arrayToXML($arr1);
        echo " --- ANTES DE LIMPAR\n\n";
        echo $xml;
        $xml = str_replace("\t", "", $xml);
        $xml = str_replace(PHP_EOL, "", $xml);
        echo "\n\n --- DEPOIS DE LIMPAR\n\n";
        echo $xml;
        echo "\n\n --- ";
        $this->assertTrue($xml=='<var1>value1</var1><var2>value2</var2>');

        // TESTA UMA COM FILHOS
        $arr2 = array();
        $arr2['id'] = 10;
        $arr2['cliente_id'] = 20;

        $item1 = array('id'=>1010, 'description'=>'Item 1010');
        $item2 = array('id'=>1020, 'description'=>'Item 1020');
        $itens = array();
        $itens[] = $item1;
        $itens[] = $item2;
        $arr2['itens'] = $itens;

        $xml = $conv->arrayToXML($arr2);
        echo " --- ANTES DE LIMPAR\n\n";
        echo $xml;
        $xml = str_replace("\t", "", $xml);
        $xml = str_replace(PHP_EOL, "", $xml);
        echo "\n\n --- DEPOIS DE LIMPAR\n\n";
        echo $xml;
        echo "\n\n --- ";
        $this->assertTrue($xml=='<var1>value1</var1><var2>value2</var2>');


    }
}
