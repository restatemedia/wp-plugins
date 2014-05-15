<?php
/*
Plugin Name: RM Page Effects
Author: Joe Flumerfelt
Description: This plugins provides various shortcodes for adding javascript layouts to pages.
Version: 0.1
*/

add_action( 'wp_enqueue_scripts', 'rm_fx_enqueue_style', 2 );
function rm_fx_enqueue_style() {
	wp_enqueue_script('rm-fx', plugins_url('js/rm-page-effects.js', __FILE__), array('jquery'));
}

function rm_fx_accordian_shortcode( $atts, $content='' ) {
	$html .= '<div class="rm-accordian">';
	$html .= do_shortcode($content);
	$html .= '</div>';
	return $html;
}
add_shortcode( 'rm-accordian', 'rm_fx_accordian_shortcode' );


function rm_fx_notes_shortcode( $atts, $content='' ) {
	$html .= '<div class="rm-notes" data-start="'.$atts['start'].'">';
	$html .= $content;
	$html .= '</div>';
	return $html;
}
add_shortcode( 'rm-notes', 'rm_fx_notes_shortcode' );

function rm_fx_tabs_shortcode( $atts, $content='' ) {
	$html .= '<div class="rm-tabs" 
				   data-id="'.$atts['id'].'" 
				   data-action="'.$atts['action'].'"
				   data-toggle="'.$atts['toggle'].'">';
	$html .= $content;
	$html .= '</div>';
	return $html;
}
add_shortcode( 'rm-tabs', 'rm_fx_tabs_shortcode' );

function rm_fx_tab_shortcode( $atts, $content='' ) {
	$html .= '<div id="'.$atts['id'].'" class="rm-tab-content">';
	$html .= $content;
	$html .= '</div>';
	return $html;
}
add_shortcode( 'rm-tab', 'rm_fx_tab_shortcode' );

function rm_fx_more_shortcode( $atts, $content='' ) {

	$open_label = (WPLANG == 'es_ES') ? __('Más…') : __('More…');
	$close_label = (WPLANG == 'es_ES') ? __('Menos…') : __('Less…');

	$html .= '<div class="rm-more-toggle"><span class="open ui"><a>'.$open_label.'</a></span>';
	$html .= '<span class="close ui"><a>'.$close_label.'</a></span></div>';
	$html .= '<div class="rm-more"><p>';
	$html .= $content;
	$html .= '</p></div>';
	return $html;
}
add_shortcode( 'rm-more', 'rm_fx_more_shortcode' );

function rm_embed_shortcode( $atts, $content='' ) {
	
	$html = "";
		
	switch($atts['type']) {
		case 'gmap':
			if (!$atts['id']) $atts['id'] = 'map_embed';
			$html .= '<div id="'.$atts['id'].'" class="map"></div>';
			$html .= '<script type="text/javascript">';
			
			$z = ($atts['zoom']) ? $atts['zoom'] : 15;
			$url = sprintf('http://maps.google.com/maps?f=q&source=s_q&hl=es&geocode=&q=%s&z='.$z.'&iwloc=near&output=embed', urlencode($content));
			
			$html .= 'jQuery("#'.$atts['id'].'").embed({type:"'.$atts['type'].'", url:"'.$url.'", w:'.$atts['width'].', h:'.$atts['height'].'});';
			$html .= '</script>';
			break;
		case 'soundcloud':
			
			break;
		case 'vimeo':
		case 'youtube':
			if (!$atts['id']) $atts['id'] = 'video_'+$atts['media'];
			$html .= '<div id="'.$atts['id'].'" class="video"></div>';
			$html .= '<script type="text/javascript">';
			$html .= 'jQuery("#'.$atts['id'].'").embed({type:"'.$atts['type'].'", id:"'.$atts['media'].'", w:'.$atts['width'].', h:'.$atts['height'].'});';
			$html .= '</script>';
			break;
	}
	
	
	
	return $html;
}
add_shortcode( 'rm-embed', 'rm_embed_shortcode' );

?>