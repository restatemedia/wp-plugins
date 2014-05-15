<?php
/*
Plugin Name: Restate Media Language Support
Description: This plugin provides utility functions for working with the Sitepress Multilingual CMS plugin. The actual translations are handled by Sitepress.
Version: 1.0.0
Author: Joe Flumerfelt
License: GPLv2 or later
*/

define('RM_LANG_DEFAULT', 'en');

if (!$_SESSION) session_start();

add_action('wp_enqueue_scripts', 'rm_lang_styles', 0);
function rm_lang_styles() {
	wp_enqueue_style('rm-lang-style', plugins_url('rm-lang.css', __FILE__));
	wp_enqueue_script('rm-lang', plugins_url('rm-lang.js', __FILE__), array('jquery', 'rm-standard-class'));
}

add_action('wp_head', 'rm_lang_head', 0);
function rm_lang_head() {
?>
<script type="text/javascript">
	var lang_config = {lang:'<?php echo rm_get_lang(); ?>',
					   locale:'<?php echo $_SESSION['locale']; ?>',
					   home:'<?php echo remove_query_arg('lang', get_bloginfo('home')); ?>'};
</script>
<?php
}


add_action('plugins_loaded', 'rm_setup_lang', 0);
function rm_setup_lang($wp) {
	if (!is_admin()) {
		if ($_REQUEST['lang']) {
			$_SESSION['lang'] = $_REQUEST['lang'];
		} else if (!$_SESSION['lang']) {
			$_SESSION['lang'] = get_option('rm-lang-default');
		}
	
		$_SESSION['locale'] = rm_lang_get_locales($_SESSION['lang']);
		
	}
}


add_filter( 'locale', 'rm_set_locale' ); 
add_filter( 'plugin_locale', 'rm_set_locale' );
function rm_set_locale($locale) { 
	return ($_SESSION['locale']) ? $_SESSION['locale'] : $locale;
} 

function rm_lang_get_locales($lang) {
	$locales = array('en'=>'en_US',
					 'es'=>'es_ES');
	return $locales[$lang];
}

add_filter( 'icl_set_current_language', 'rm_lang_filter');
function rm_lang_filter($lang) {
	return $_SESSION['lang'];
}

function rm_is_lang($lang) {
	return ($_SESSION['lang'] == $lang);
}

function rm_get_lang() {
	return $_SESSION['lang'];
}

function rm_get_local_url($slug, $type='post') {
	return rm_get_local($slug, $type, 'url');	
}

function rm_get_local_title($slug, $type) {
	return rm_get_local($slug, $type, 'title');
}

function rm_get_local_slug($slug, $type	) {
	return rm_get_local($slug, $type, 'slug');
}

function rm_get_local($slug, $type='post', $return='object') {
	global $sitepress;
	
	if ($type == 'category' || strstr($type, 'tax')) {
	
		if ($type == 'category') {
			$cat = get_category_by_slug($slug);
			if ($return == 'object') return $cat;
			
			$id = $cat->term_id;
			$type = 'tax_category';
		} else {
			$parts = explode('_', $type);
			$taxonomy = $parts[1];
			$cat = get_term_by('slug', $slug, $taxonomy);
			if ($return == 'object') return $cat;
			$id = $cat->term_id;
		}
		
		
		if (!rm_is_lang(RM_LANG_DEFAULT)) {
			$l = rm_get_lang();
			$trid = $sitepress->get_element_trid($id, $type);
			if($trid){
				$translations = $sitepress->get_element_translations($trid, $type, false);
				$cat = get_category($translations[$l]->term_id);
			} 
		} 
		
		if ($return == 'title') {
			return $cat->name;
		} else if ($return == 'url') {
			$cid = (int) (isset($cat->element_id)) ? $cat->element_id : $cat->term_id;
			
			$cat = ($type == 'tax_category') ? get_category_link($cid)
										 	 : get_bloginfo('siteurl').'/'.$taxonomy.'/'.$slug;
			$url = str_replace('&amp;lang='.rm_get_lang(), '', $cat);
			return remove_query_arg('lang', $url);
		} else if ($return == 'slug') {
			return $cat->slug;
		}

	} else {
	
		$args = array('name'=>$slug, 'posts_per_page'=>1);
	
		switch($type) {
			case 'page':
				$type = 'post_page';
				$args['post_type'] = 'page';
				break;
			default:
				$type = 'post_post';
				$args['post_type'] = 'post';
				break;
		}
		
		
		$query = new WP_Query($args);
		if ($return == 'object') return $query->post;
		$id = $query->post->ID;
				
		if (!rm_is_lang(RM_DEFAULT_LANG)) {
			$trid = $sitepress->get_element_trid($id, $type);
			if($trid){
				$l = rm_get_lang();
				$translations = $sitepress->get_element_translations($trid, $type, false);
				$p = get_post($translations[$l]->element_id);
				
			} else {
				$p = $query->post;
			}
		} else {
			$p = $query->post;
		}
		
		if ($return == 'url') {
			$url = str_replace('&amp;lang='.rm_get_lang(), '', get_permalink(($p->element_id) ? $p->element_id : $id));
			return remove_query_arg('lang', $url);
		} else if ($return == 'title') {
			return $p->post_title;
		} else if ($return == 'slug') {
			return $p->post_name;
		}
	
	}	

}

function rm_get_translated_slug_for_page($page, $lang) {
	global $sitepress, $wpdb;
	$trid = $sitepress->get_element_trid($page->ID, 'post_page');
	if ($trid) {
		$translations = $sitepress->get_element_translations($trid, 'post_page', false);
		$res = $wpdb->get_var("SELECT post_name FROM {$wpdb->prefix}posts WHERE ID=".$translations[$lang]->element_id);
		return $res;
	}
	
}

function rm_get_translated_slug($lang, $term=null, $tax='tax_category') {
	global $post, $sitepress, $wpdb;
	if (!$term) {
		$term = get_the_category( $post->ID );
		$term = $term[0];
	}
	$trid = $sitepress->get_element_trid($term->term_id, $tax);
	if ($trid) {
		$translations = $sitepress->get_element_translations($trid, $tax, false);
		$res = $wpdb->get_var("SELECT slug FROM {$wpdb->prefix}terms WHERE term_id=".$translations[$lang]->term_id);
		return $res;
	}
	return $term->slug;
	
}