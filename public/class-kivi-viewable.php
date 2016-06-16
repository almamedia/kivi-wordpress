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
 * A section inside the fact box of an item to be displayed on the item page.
 * These are stored inside a Kivi_Fact_Box and these have Kivi_Property
 * instances inside.
 * Supposed to be displayed as a string returned by __toString().
 * The result will be a table row with a header and value(s).
 */

class Kivi_Viewable {
  private $properties=[];
  private $label="";
  public function __construct( $label="" ) {
    $this->label = $label;
  }

  public static function asSingle( $post_id, $property ) {
    $view = new self( $label=map_post_meta($property) );
    $view->add( New Kivi_Property( $post_id, $property) );
    return $view;
  }

  public function add( &$property ){
    array_push($this->properties, $property);
  }

  public function getValue(){
    $ret="";
    $empty=True;
    if( count( $this->properties ) >1){
      $ret = $ret . '<ul>';
      foreach($this->properties as $prop){
        if( $prop != "" ){
          $empty = False;
          $ret = $ret . '<li>' . $prop . '</li>';
        }
      }
      $ret = $ret . '</ul>';
      if( $empty ){$ret = "";}
    }elseif(count( $this->properties ) == 1){
      $ret = $ret.$this->properties[0];
    }
    return $ret;
  }

  public function getTableRow(){
    return '<tr><th class="kivi-item-cell kivi-item-cell-header">' . $this->label . '</th>' .
    '<td class="kivi-item-cell kivi-item-cell-value '.'" id="'. preg_replace("/[\s_]/", "-", $this->label ) .'">' . $this->getValue() . '</td></tr>';
  }

  public function __toString(){
    if( $this->getValue() ){
      return $this->getTableRow();
    }else{
      return "";
    }
  }

}
