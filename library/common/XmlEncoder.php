<?php
class XmlEncoder
{
    public static function Encode(&$params,$charset='utf-8',$version='1.0')
    {
        $dom = new DOMDocument($version, $charset);
        
        self::EncodeParam($dom,$dom,$params);
        return $dom->saveXML();
    }
    
    public static function EncodeParam(&$dom,&$parent,&$params)
    {
        foreach($params as $name => &$iteminfo)
        {
            if(self::is_vector($iteminfo)){
                for($i=0;$i<count($iteminfo);++$i){
                    self::EncodeElement($dom,$parent,$name,$iteminfo[$i]);
                }
            }else{
                self::EncodeElement($dom,$parent,$name,$iteminfo);
            }
        }        
    }
    
    public static function EncodeElement(&$dom,&$parent,$name,&$iteminfo)
    {
            $subelement = $dom->createElement($name);
            
            if(isset($iteminfo['@attributes'])){
                foreach($iteminfo['@attributes'] as $attrname => $attrvalue){
                    $subelement->setAttribute($attrname,$attrvalue);
                }
            }
            
            if(isset($iteminfo['@nodevalue'])){
                if(is_array($iteminfo['@nodevalue'])){
                    self::EncodeParam($dom,$subelement,$iteminfo['@nodevalue']);
                }else{
                    $subelement->appendChild($dom->createTextNode($iteminfo['@nodevalue']));
                }
            }
            
            $parent->appendChild($subelement);
    
    }
    
    
    public static function is_vector( &$array ) {
       if ( !is_array($array) || empty($array) ) {
          return -1;
       }
       $next = 0;
       foreach ( $array as $k => &$v ) {
          if ( $k !== $next ) return false;
          $next++;
       }
       return true;
    }
}