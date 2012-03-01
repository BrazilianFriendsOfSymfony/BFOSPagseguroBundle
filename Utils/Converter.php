<?php
/**
 * Created by JetBrains PhpStorm.
 * User: duo
 * Date: 2/25/12
 * Time: 3:24 PM
 * To change this template use File | Settings | File Templates.
 */
namespace BFOS\PagseguroBundle\Utils;

class Converter
{

    /*
      * James Earlywine - July 20th 2011
      *
      * Translates a jagged associative array
      * to XML. Works best with
      *
      * @param : $theArray - The jagged Associative Array
      * @param : $tabCount - for persisting tab count across recursive function calls
      */
    public function arrayToXML ($theArray, $tabCount=2) {
        //echo "The Array: ";
        //var_dump($theArray);
        // variables for making the XML output easier to read
        // with human eyes, with tabs delineating nested relationships, etc.

        $theXML = '';
        $tabCount++;
        $tabSpace = "";
        $extraTabSpace = "";
        for ($i = 0; $i<$tabCount; $i++) {
            $tabSpace .= "\t";
        }

        for ($i = 0; $i<$tabCount+1; $i++) {
            $extraTabSpace .= "\t";
        }


        // parse the array for data and output xml
        foreach($theArray as $tag => $val) {
            if (!is_array($val)) {
                $theXML .= PHP_EOL.$tabSpace.'<'.$tag.'>'.htmlentities($val).'</'.$tag.'>';
            } else {
                $tabCount++;
                $theXML .= PHP_EOL.$extraTabSpace.'<'.$tag.'>'.$this->arrayToXML($val, $tabCount);
                $theXML .= PHP_EOL.$extraTabSpace.'</'.$tag.'>';
            }
        }

        return $theXML;
    }
}
