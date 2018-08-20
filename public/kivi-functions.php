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

  /**
  * Template tag for displaying the filters form
  * @return html object
  */
  function map_post_meta($meta_field){
      return Kivi_Public::map_post_meta($meta_field);
  }

  function set_kivi_option( $name, $value){
    $old = get_option('kivi-options');
    if( ! is_array($old)){ $old=[];}
    $kivi_options = array_merge( $old, array($name => $value ) );
    update_option( 'kivi-options', $kivi_options );
  }

  function get_kivi_option( $name ){
    $kivi_options = get_option('kivi-options');
    if( is_array($kivi_options) && array_key_exists($name,$kivi_options) ){
      return $kivi_options[$name];
    }else{
      return "";
    }
  }

  /*
  * Used by the index template to populate search criteria for the filtering
  * of the posts based on the (custom) metadata.
  */
  function populate_searchcriteria( &$criteria, &$request, $field, $key, $operator, $intval=false ){
    if ( isset($request[$field]) && $request[$field] != '' ) {
      $value = $request[$field];
      $type='CHAR';
      if( $intval ){
          $value = intval($request[$field]);
          $type = 'NUMERIC';
      }
      $criteria = array(
        'key'     => $key,
        'value'   => $value,
        'compare' => $operator,
        'type'    => $type,
      );
    }
  }

  /*
  * Helper to get a posted value from the POST/GET request.
  */
  function get_posted_value( &$request, $value ){
    $ret="";
    if ( isset($request["submit"]) && isset($request[$value]) ){
      $ret=$request[$value];
    }
    return $ret;
  }

  /* Get the sales person image url related to the post. */
  function get_iv_person_image( $post_id, $size='medium' ){
    $att = get_post_meta( $post_id, '_kivi_iv_person_image',true);
    if( $att ){
      return wp_get_attachment_image_url( $att, $size );
    }else{
      return "";
    }
  }

  /*
  * Define what is shown in the info boxs on the item page.
  * Used by the index template.
  */

  function view_basic_info( $post_id ){
    $box = New Kivi_Fact_Box();
    $box->add( Kivi_Viewable::asSingle( $post_id, '_realty_unique_no' ) );
    $view = New Kivi_Viewable( $label=__("Sijainti","kivi"));
    $view->add( New Kivi_Property( $post_id, '_quarteroftown') );
    $p = Kivi_Property::withPrefix( New Kivi_Property( $post_id, '_street') , $post_id, '_stairway');
    $s = Kivi_Property::withPrefix( $p , $post_id, '_door_number');
    $view->add($s);
    //$view->add( New Kivi_Property( $post_id, '_street') );
    $view->add( New Kivi_Property( $post_id, '_postcode') );
    $view->add( New Kivi_Property( $post_id, '_town') );
    $box->add( $view );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_realtytype_id' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_holdingtype_id' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_owningtype_id' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_flat_structure' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_living_area_m2' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_total_area_m2' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_areabasis_id' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_area_desc' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_floor' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_floors' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_rc_buildyear2' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_rc_usageyear' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_assignmentsale_free_other' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_assignmentsale_free_type_name' ) );
    echo  $box;
  }


  function view_cost_info( $post_id ){
    $box = New Kivi_Fact_Box();
    $box->add( Kivi_Viewable::asSingle( $post_id, '_assignmentrent_rent' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_unencumbered_price' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_price' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_debt' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_chargesmaint2_month' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_charges_finance_base_month' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_charges_maint_base_month' ) );
    $view = New Kivi_Viewable( $label=__("Vesimaksu","kivi"));
    $view->add( New Kivi_Property( $post_id, '_charges_water') );
    $view->add( New Kivi_Property( $post_id, '_watercharge_type_id') );
    $box->add( $view );
	$box->add( Kivi_Viewable::asSingle( $post_id, '_charges_eheating' ) );
	$box->add( Kivi_Viewable::asSingle( $post_id, '_charges_sewage' ) );
	$box->add( Kivi_Viewable::asSingle( $post_id, '_charges_road' ) );
	$box->add( Kivi_Viewable::asSingle( $post_id, '_charges_streetcleansing' ) );
	$box->add( Kivi_Viewable::asSingle( $post_id, '_charges_other' ) );
    echo $box;
  }


  function view_housing_company_info( $post_id){
    $box = New Kivi_Fact_Box();
    $box->add( Kivi_Viewable::asSingle( $post_id, '_realtycompany' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_rc_housemanager' ) );
    $view = New Kivi_Viewable( $label=__("Taloyhtiöön kuuluu","kivi"));
    $view->add( New Kivi_Property( $post_id,  $name='', $type="realtyoption", 'Taloyhtiössä on...', 'urheiluvälinevarasto'  ) );
    $view->add( New Kivi_Property( $post_id,  $name='', $type="realtyoption", 'Taloyhtiössä on...', 'kellarikomero'  ) );
    $view->add( New Kivi_Property( $post_id,  $name='', $type="realtyoption", 'Taloyhtiössä on...', 'väestönsuoja'  ) );
    $box->add($view);
    $box->add( Kivi_Viewable::asSingle( $post_id, '_rc_has_other' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_energyclass_name' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_rc_lot_area_m2' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_rc_lotholding_id' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_rc_carshelter_count' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_rc_garage_count' ) );
	$box->add( Kivi_Viewable::asSingle( $post_id, '_rc_renovation_made' ) );
	$box->add( Kivi_Viewable::asSingle( $post_id, '_rc_renovation_planned' ) );
    echo $box;
  }

  function view_additional_info( $post_id){
    $box = New Kivi_Fact_Box();
    $view = New Kivi_Viewable( $label=__("Sauna","kivi"));
    $view->add( New Kivi_Property( $post_id,  $name='', $type="realtyoption", 'Lisätieto-ominaisuudet', 'oma sauna'  ) );
    $view->add( New Kivi_Property( $post_id,  $name=__('taloyhtiössä sauna',"kivi"), $type="realtyoption", 'Taloyhtiössä on...', 'sauna'  ) );
    $box->add( $view );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_fireplace_other' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_otherspace_desc' ) );
    $view = New Kivi_Viewable( $label=__("Parveke","kivi"));
    $view->add( New Kivi_Property( $post_id,  $name=__('asunnossa on parveke',"kivi"), $type="realtyoption", 'Lisätieto-ominaisuudet', 'parveke'  ) );
    $box->add( $view );
    $view = New Kivi_Viewable( $label=__("Parvekkeen kuvaus","kivi"));
    $view->add( New Kivi_Property( $post_id,  $name=__('Ilmansuunta',"kivi"), $type="realtyoption", 'Parvekkeen ilmansuunta', '' ) );
    $view->add( New Kivi_Property( $post_id,  $name=__('Parveketyyppi',"kivi"), $type="realtyoption", 'Parveketyyppi', '' ) );
    $box->add( $view );
    $view = New Kivi_Viewable( $label=__("Hissi","kivi"));
    $view->add( New Kivi_Property( $post_id,  $name=__('taloyhtiössä on hissi',"kivi"), $type="realtyoption", 'Lisätieto-ominaisuudet', 'hissi' ) );
    $box->add( $view );
    $view = New Kivi_Viewable( $label=__("Asuntoon kuuluu","kivi"));
    $view->add( New Kivi_Property( $post_id,  $name=__('Autopaikka',"kivi"), $type="realtyoption", 'Autopaikka', '' ) );
    $view->add( New Kivi_Property( $post_id,  $name='', $type="realtyoption", '_default', 'kaapeli-tv' ) );
    $box->add( $view );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_condition_id' ) );
    $box->add( Kivi_Viewable::asSingle( $post_id, '_condition' ) );
	$box->add( Kivi_Viewable::asSingle( $post_id, '_renovation_made' ) );
	$box->add( Kivi_Viewable::asSingle( $post_id, '_energyclass_name' ) );
    $view = New Kivi_Viewable( $label=__("Lämmitysjärjestelmän kuvaus","kivi"));
    $view->add( New Kivi_Property( $post_id,  $name='', $type="realtyoption", 'Lämmitysjärjestelmä', '' ) );
    $box->add( $view );
	
	$property = New Kivi_Property( $post_id, '_vi_presentations','vi_presentations' );
	if( ! empty($property) ) {
		$view = New Kivi_Viewable( $label=__("Videoesittelyt","kivi"));
		$view->add($property);
		$box->add($view);
	}
	
	$box->add( Kivi_Viewable::asSingle( $post_id, '_other_important_info' ) );
	echo $box;
  }

  function view_services_info( $post_id){
    $box = New Kivi_Fact_Box();
	$box->add( Kivi_Viewable::asSingle( $post_id, '_services' ) );
	$box->add( Kivi_Viewable::asSingle( $post_id, '_services_other' ) );
	$box->add( Kivi_Viewable::asSingle( $post_id, '_connections' ) );
	$box->add( Kivi_Viewable::asSingle( $post_id, '_daycare' ) );
	$box->add( Kivi_Viewable::asSingle( $post_id, '_school' ) );
	echo $box;
  }


  function view_materials_info( $post_id ){
    $box = New Kivi_Fact_Box();
    $view = New Kivi_Viewable( $label=__("Asunnon tilat ja materiaalit","kivi"));
    $view->add( New Kivi_Property( $post_id,  $name=__('Pääasiallinen rakennusmateriaali','kivi'), $type="realtyoption", 'Rakennusmateriaali', ''  ) );
    $view->add( New Kivi_Property( $post_id, '_floormaterial_info') );
    $view->add( New Kivi_Property( $post_id, '_roofmaterial_info') );
    $box->add( $view );
    $view = New Kivi_Viewable( $label=__("Katto","kivi"));
    $view->add( New Kivi_Property( $post_id, '_rc_roof') );
    $view->add( New Kivi_Property( $post_id, '_rc_roofing') );
    $box->add( $view );
    $view = New Kivi_Viewable( $label=__("Keittiön kuvaus","kivi"));
    $view->add( New Kivi_Property( $post_id, '_kitchen_equipment_desc') );
    $view->add( New Kivi_Property( $post_id,  $name=__('Lattiamateriaalit','kivi'), $type="realtyoption", 'Keittiön lattiamateriaali', ''  ) );
    $view->add( New Kivi_Property( $post_id,  $name=__('Seinämateriaalit','kivi'), $type="realtyoption", 'Keittiön seinien materiaali', ''  ) );
    $view->add( New Kivi_Property( $post_id,  $name=__('Työtaso','kivi'), $type="realtyoption", 'Keittiön työtasojen materiaali', ''  ) );
    $view->add( New Kivi_Property( $post_id,  $name=__('Varusteet','kivi'), $type="realtyoption", 'Keittion muut varusteet', ''  ) );
    $view->add( New Kivi_Property( $post_id,  $name=__('Liesi','kivi'), $type="realtyoption", 'Liesi', ''  ) );
    $view->add( New Kivi_Property( $post_id,  $name=__('Jääkaappi ja pakastin','kivi'), $type="realtyoption", 'Jääkaappi ja pakastin', ''  ) );
    $box->add( $view );
    $view = New Kivi_Viewable( $label=__("Kylpyhuoneen kuvaus","kivi"));
    $view->add( New Kivi_Property( $post_id,  $name=__('Lattiamateriaalit','kivi'), $type="realtyoption", 'Kylpyhuoneen lattiamateriaali', ''  ) );
    $view->add( New Kivi_Property( $post_id,  $name=__('Seinämateriaalit','kivi'), $type="realtyoption", 'Kylpyhuoneen seinämateriaali', ''  ) );
    $view->add( New Kivi_Property( $post_id,  $name=__('Varusteet','kivi'), $type="realtyoption", 'Kylpyhuoneen varustus', ''  ) );
    $view->add( New Kivi_Property( $post_id, '_bathroom_desc') );
    $box->add( $view );
    $view = New Kivi_Viewable( $label=__("WC-tilojen kuvaus","kivi"));
    $prop = Kivi_Property::withPrefix( __('Lukumäärä:','kivi') , $post_id, '_toilet_count');
    $view->add( $prop );
    $view->add( New Kivi_Property( $post_id, '_toilet_info') );
    $view->add( New Kivi_Property( $post_id,  $name=__('Lattiamateriaalit','kivi'), $type="realtyoption", 'WC:n lattiamateriaali', ''  ) );
    $view->add( New Kivi_Property( $post_id,  $name=__('Seinämateriaalit','kivi'), $type="realtyoption", 'WC:n seinämateriaali', ''  ) );
    $view->add( New Kivi_Property( $post_id,  $name=__('Varusteet','kivi'), $type="realtyoption", 'WC:n varustus', ''  ) );
    $box->add( $view );
    $view = New Kivi_Viewable( $label=__("Saunan kuvaus","kivi"));
    $view->add( New Kivi_Property( $post_id, '_sauna_desc') );
    $view->add( New Kivi_Property( $post_id,  $name=__('Lattiamateriaalit','kivi'), $type="realtyoption", 'Saunan lattiamateriaali', ''  ) );
    $view->add( New Kivi_Property( $post_id,  $name=__('Seinämateriaalit','kivi'), $type="realtyoption", 'Saunan seinämateriaali', ''  ) );
    $box->add( $view );
    $view = New Kivi_Viewable( $label=__("Kodinhoitohuoneen kuvaus","kivi"));
    $view->add( New Kivi_Property( $post_id, '_laundry') );
    $view->add( New Kivi_Property( $post_id,  $name=__('Lattiamateriaalit','kivi'), $type="realtyoption", 'Kodinhoitoh. lattiamateriaali', ''  ) );
    $view->add( New Kivi_Property( $post_id,  $name=__('Seinämateriaalit','kivi'), $type="realtyoption", 'Kodinhoitoh. seinämateriaali', ''  ) );
    $view->add( New Kivi_Property( $post_id,  $name=__('Varusteet','kivi'), $type="realtyoption", 'Kodinhoitoh. varustus', ''  ) );
    $box->add( $view );
    $view = New Kivi_Viewable( $label=__("Olohuoneen kuvaus",'kivi'));
    $view->add( New Kivi_Property( $post_id, '_livingroom_info') );
    $view->add( New Kivi_Property( $post_id,  $name=__('Lattiamateriaalit','kivi'), $type="realtyoption", 'Olohuoneen lattiamateriaali', ''  ) );
    $view->add( New Kivi_Property( $post_id,  $name=__('Seinämateriaalit','kivi'), $type="realtyoption", 'Olohuoneen seinämateriaali', ''  ) );
    $box->add( $view );
    $view = New Kivi_Viewable( $label=__("Makuuhuoneiden kuvaus",'kivi'));
    $mh = Kivi_Property::withPrefix( __('Lukumäärä: ','kivi'), $post_id, '_bedroom_count');
    $view->add( $mh );
    $view->add( New Kivi_Property( $post_id, '_bedroom_desc') );
    $view->add( New Kivi_Property( $post_id,  $name=__('Lattiamateriaalit','kivi'), $type="realtyoption", 'Makuuhuoneen lattiamateriaali', ''  ) );
    $view->add( New Kivi_Property( $post_id,  $name=__('Seinämateriaalit','kivi'), $type="realtyoption", 'Makuuhuoneen seinämateriaali', ''  ) );
    $box->add( $view );
	$box->add( Kivi_Viewable::asSingle( $post_id, '_storage_condition' ) );
    echo $box;
  }

  function view_contact_info( $post_id){
    $box = New Kivi_Fact_Box();
    $view = New Kivi_Viewable( $label=__('Yhteystiedot','kivi'));
    $view->add( New Kivi_Property( $post_id, '_iv_person_name') );
    $view->add( New Kivi_Property( $post_id, '_iv_person_suppliername') );
    $view->add( New Kivi_Property( $post_id, '_iv_person_email') );
    $view->add( New Kivi_Property( $post_id, '_iv_person_mobilephone') );
    $box->add($view);
    $s = New Kivi_Viewable( $label=__("Esittelyt","kivi"));
    $s->add( New Kivi_Property( $post_id, '_presentations','presentation' ) );
    $box->add($s);
    echo $box;
  }
  
?>
