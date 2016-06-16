<?php

/**
 *
 * @link       https://kivi.etuovi.com/
 * @since      1.0.0
 *
 * @package    Kivi
 * @subpackage Kivi/public
 * @author     ktalo <antti.keskitalo@almamedia.fi>
 */

 /*
 * A single property item to be displayed on the item page. Supposed to be
 * displayed as a string returned by __toString().
 */

class Kivi_Property {
  private $names;
  private $type;
  private $realtyoptiongroup_id;
  private $realtyoption_id;
  private $prefix="";
  public function __construct( $post_id, $name, $type='simple', $realtyoptiongroup_id='', $realtyoption_id='' ) {
    $this->name = $name;
    $this->type = $type;
    $this->post_id = $post_id;
    $this->realtyoptiongroup_id = $realtyoptiongroup_id;
    $this->realtyoption_id = $realtyoption_id;
  }

  public function setPrefix($prefix){
    $this->prefix = $prefix;
  }

  public static function withPrefix( $prefix, $post_id, $name, $type='simple', $realtyoptiongroup_id='', $realtyoption_id='' ) {
    $prop = new self( $post_id, $name, $type, $realtyoptiongroup_id, $realtyoption_id );
    $prop->setPrefix( $prefix );
    return $prop;
  }

  private function getRealtyOptionValue(){
    $ret = "";
    $opts = get_post_meta($this->post_id, "_realtyrealtyoptions" )[0];
    if( array_key_exists($this->realtyoptiongroup_id, $opts) ){
      if($this->realtyoption_id == ""){
        if( $this->name ){
            $ret = $ret . $this->name . ': ';
        }
        $ret = $ret . implode( ', ', $opts[$this->realtyoptiongroup_id] );
      }else{
        if( in_array( $this->realtyoption_id , $opts[$this->realtyoptiongroup_id] )){
          return $this->name ? $this->name : $this->realtyoption_id;
        }
      }
    }
    return $ret;
  }

  private function getPresentationValue(){
    $opts = get_post_meta($this->post_id, $this->name,true );
    foreach ($opts as $pres) {
      $p = "";
      $d = new DateTime( $pres['presentation_date'] );
      $s = new DateTime( $pres['presentation_start'] );
      $e = new DateTime( $pres['presentation_end'] );

      $p = date_format($d, 'd.m.Y') . " " . date_format($s, 'H:i') . " - " . date_format($e, 'H:i') ;
      return $p;
    }
  }

  function getPrefix(){
    if ($this->getValue() != ""){
      return $this->prefix;
    }
    else{
      return "";
    }
  }

  private function getValue(){
    if( $this->type == 'realtyoption' ){
      return $this->getRealtyOptionValue();
    }elseif( $this->type == 'presentation') {
      return $this->getPresentationValue();
    }else{
      return get_post_meta($this->post_id, $this->name, true);
    }
  }


  public function __toString(){
    return $this->getPrefix() . $this->getValue();

  }
}
