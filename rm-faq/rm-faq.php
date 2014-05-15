<?php
/*
Plugin Name: RM FAQ
Description: Basic FAQ implementation
Version: 1.0.0
Author: Restate Media
License: GPLv2 or later
*/

add_action( 'wp_enqueue_scripts', 'rm_faq_enqueue_style', 2 );
function rm_faq_enqueue_style() {
	wp_enqueue_script('rm-jquery-faq', plugins_url('jquery.faq.js', __FILE__), array('jquery'));
	wp_enqueue_style('rm-jquery-faq-css', plugins_url('rm-faq.css', __FILE__));
}

add_action('init', 'rm_faq_init');
function rm_faq_init() {
	

register_post_type('question', array(	'label' => 'FAQ', 'description' => '','public' => true,'show_ui' => true,'show_in_menu' => true,'capability_type' => 'page', 'map_meta_cap' => true, 'hierarchical' => false,'rewrite' => false,'query_var' => false,'has_archive' => false,'menu_position' => 5,'supports' => array('title','editor'),'labels' => array (
  'name' => 'Questions',
  'singular_name' => 'Question',
  'menu_name' => 'Questions',
  'add_new' => 'Add Question',
  'add_new_item' => 'Add New Question',
  'edit' => 'Edit',
  'edit_item' => 'Edit Question',
  'new_item' => 'New Question',
  'view' => 'View Question',
  'view_item' => 'View Question',
  'search_items' => 'Search Questions',
  'not_found' => 'No Questions',
  'not_found_in_trash' => 'No Questions in the Trash',
  'parent' => 'Parent Question',
),) );

register_taxonomy('faq-topic',array('question'),array( 'hierarchical' => true, 'label' => 'Topics','show_ui' => true,'query_var' => true,'rewrite' => true, 'singular_label' => 'Topic') );
 
}

/** API FUNCTIONS ********************************************************/

function rm_faq_get_questions($topic) {

	$args = array('post_type'=>'question',
				  'nopaging'=>true,
				  'order'=>'ASC',
				  'orderby'=>'menu_order title',
				  'tax_query'=>array());
	$args['tax_query'][] = array(
						'taxonomy' => 'faq-topic',
						'field' => 'slug',
						'terms' => $topic
						);	
						
	return new WP_Query($args);

}

function rm_faq_get_topics($args=array()) {

	if (function_exists('rm_setup_lang')) $_GET['taxonomy'] = 'faq-topic';

	$args = array_merge(array('order'=>'ASC',
				  'orderby'=>'custom_sort'), $args);
				  
	return get_terms('faq-topic', $args);
}

/** SHORTCODE **********************************************************/

function rm_faq_shortcode( $atts, $content='' ) {

	$selector = '.listing.faq';

	if ($atts['topic']) {
	
		$html = '<ul id="topic-'.$atts['topic'].'" class="listing faq">';
	
		$q = rm_faq_get_questions($atts['topic']);
			
		foreach((array) $q->posts as $question) {
			$id = $question->ID;
			$html .= '<li class="question" data-question="'.$question->ID.'">';
			$html .= '<div class="faq_title">'.$question->post_title.'</div>';
			$html .= '<div class="answer"><p>'.apply_filters('the_content', $question->post_content).'</p></div>';
			$html .= '</li>';
		}
		
		$html .= '</ul>';
		
		$selector = '#topic-'.$atts['topic'];
	
	} else {
	
		$html = '<div class="section listing faq"><ul>';
	
		$topics = rm_faq_get_topics();
		
		foreach((array) $topics as $topic) {
		
			$html .= '<li class="list_header">'.$topic->name.'</li>';
		
			$q = rm_faq_get_questions($topic->slug);
			
			foreach((array) $q->posts as $question) {
				$id = $question->ID;
				$html .= '<li class="question" data-question="'.$question->ID.'">';
				$html .= '<div class="faq_title">'.$question->post_title.'</div>';
				$html .= '<div class="answer"><p>'.apply_filters('the_content', $question->post_content).'</p></div>';
				$html .= '</li>';
			}
		
		}
		
		$html .= "</ul></div>";
	
	}	
	
	$html .= '<script type="text/javascript">';
	$html .= 'jQuery("'.$selector.'").faq({auto_collapse:"'.$atts['auto_collapse'].'"});';
	$html .= '</script>';
	return $html;
		
	
}
add_shortcode( 'rm-faq', 'rm_faq_shortcode' );

?>