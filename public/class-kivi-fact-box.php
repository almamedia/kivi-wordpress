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
 * A representation of a fact box inside an item page. Hods a list of instances
 * of Kivi_Viewables. Supposed to be displayed as a strgin returned by
 * __toString().
 */
 Class Kivi_Fact_Box{
   private $rows=[];
   public function __construct(){

   }
   public function add( $item ){
     array_push( $this->rows, $item);
   }
   public function __toString(){
     return implode( "", $this->rows);
   }
 }
