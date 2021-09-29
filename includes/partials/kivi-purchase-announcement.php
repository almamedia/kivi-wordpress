<?php

/**
 * Provide a public-facing view for the plugin
 *
 * Privides a template for a simple single item in listing (shortcode or index).
 * Uses helper functions from kivi-functions.php (view_*_info() et al.)
 *
 * @link       https://kivi.etuovi.com/
 * @since      1.0.0
 *
 * @package    Kivi
 * @subpackage Kivi/public/partials
 */
$view = array();
$view['ITEMGROUP'] = $ota['ITEMGROUP'];
$view['AREAS'] = "";
foreach($ota['AREA'] as $areas_arr){
    $view['AREAS'] .= "{$areas_arr['TOWN']}, {$areas_arr['REGION']}";
    if(!empty($areas_arr['QUARTEROFTOWN'])){
	    $view['AREAS'] .= " ({$areas_arr['QUARTEROFTOWN']})";
    }
}
if($ota['UNENCUMBERED_PRICE_MAX']){
    $view['PRICE'] = "Hinta max {$ota['UNENCUMBERED_PRICE_MAX']} €";
}
if($ota['LIVING_AREA_MIN']){
	$view['MIN_AREA'] = "Koko vähintään {$ota['LIVING_AREA_MIN']} m²";
}
if( ! empty( $ota['FLAT_TYPE'] ) ) {
	$view['FLAT_TYPES'] = implode(', ', $ota['FLAT_TYPE'] );
}
if( ! empty( $ota['INFO'] ) ) {
	$view['INFO'] = $ota['INFO'];
}
?>


<div class="kivi-purchase-announcement" style="margin-bottom: 1em; padding-bottom: 1em; border-bottom: 2px solid #efefef;">
    <h5><?= esc_html($view['ITEMGROUP']) ?></h5>
    <h3><?= esc_html($view['AREAS']) ?></h3>
    <h4><?= esc_html($view['PRICE']) ?></h4>
    <h4><?= esc_html($view['MIN_AREA']) ?></h4>
    <h4><?= esc_html($view['FLAT_TYPES']) ?></h4>
    <p><?= esc_html($view['INFO']) ?></p>
</div>

<?php
//var_dump($ota);