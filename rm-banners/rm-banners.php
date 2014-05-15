<?php
/*
Plugin Name: RM Banners
Author: Joe Flumerfelt
Description: This plugins provides a simple interface for using banners.
Version: 0.2
*/

add_action('admin_enqueue_scripts', 'rm_banner_admin_styles', 0);
function rm_banner_admin_styles() {
	
}

add_action('init', 'rm_init_banners');
function rm_init_banners() {	

	register_post_type('banner', array(	'label' => 'Banners','description' => '','public' => true,'show_ui' => true,'show_in_menu' => true,'menu_position' => 8,'capability_type' => 'post','hierarchical' => false,'rewrite' => false,'query_var' => false,'supports' => array('title', 'thumbnail', 'page-attributes'),'labels' => array (
  'name' => 'Banners',
  'singular_name' => 'Banner',
  'menu_name' => 'Banners',
  'add_new' => 'Agregar Banner',
  'add_new_item' => 'Agregar Nuevo Banner',
  'edit' => 'Editar',
  'edit_item' => 'Editar Banner',
  'new_item' => 'Nuevo Banner',
  'view' => 'Ver Banner',
  'view_item' => 'Ver Banner',
  'search_items' => 'Buscar Banners',
  'not_found' => 'No Hay Banners',
  'not_found_in_trash' => 'No hay Banners en la Papelera',
  'parent' => 'Banner Superior',
),) );

	register_taxonomy('banner-category',array ('banner'),array( 'hierarchical' => true, 'label' => 'Categories','show_ui' => true, 'capabilities' => array (
            'manage_terms' => 'manage_options', 
            'edit_terms' => 'manage_options',
            'delete_terms' => 'manage_options',
            'assign_terms' => 'edit_posts' 
            ), 'query_var' => true,'rewrite' => false, 'singular_label' => 'Category') );
	
}

/** ADMIN ************************************************************/

add_filter("manage_edit-banner_columns", "rm_banner_edit_columns"); 
function rm_banner_edit_columns($columns){
  $columns = array(
    "cb" => "<input type=\"checkbox\" />",
    "title" => "Banner",
    "banner-category" => "Category"
  );
 
  return $columns;
}

add_action("manage_banner_posts_custom_column",  "rm_banner_custom_columns");
function rm_banner_custom_columns($column){
  global $post;
 
  switch ($column) {
    case "banner-category":
    	$terms = array_values(wp_get_post_terms($post->ID, 'banner-category'));
    	if ($terms) {
    		$link = add_query_arg('banner-category', $terms[0]->slug);
			echo '<a href="'.$link.'">'.$terms[0]->name.'</a>';
    	}
      break;
  }
}

/** META BOXES ********************************************************/

add_action( 'add_meta_boxes', 'rm_banner_add_meta_boxes' );
function rm_banner_add_meta_boxes($postType) {

	global $post;

	switch($postType) {
		case 'banner':
			add_meta_box( 
		        'rm_banner_link', 'Details', 'rm_banner_link_box',
		        'banner', 'normal'
		    );
			break;
	}
}

add_action( 'save_post', 'rm_banner_save_postdata' );
function rm_banner_save_postdata( $post_id ) {
  
  // Process values
  if (isset($_POST['rm-banner-url'])) update_post_meta($post_id, 'rm-banner-url', $_POST['rm-banner-url']);
  if (isset($_POST['rm-banner-caption'])) update_post_meta($post_id, 'rm-banner-caption', $_POST['rm-banner-caption']);
  if (isset($_POST['rm-banner-link'])) update_post_meta($post_id, 'rm-banner-link', $_POST['rm-banner-link']);
  
}

function rm_banner_link_box($post) {
	$url = get_post_meta($post->ID, 'rm-banner-url', true); 
?>
	 <!--
<br /><a id="content-add_media"><b><?php echo ($url) ? $url : "Upload media files..."; ?></b></a>
	 <input type="hidden" id="rm-banner-url" name="rm-banner-url" value="<?php echo $url;?>" /><br /><br />
	 <script type="text/javascript">
	 	openMediaBrowser('content-add_media', {title:"Add Image", button:"Select", multiple:false},
	 	function(attachment) {
	 		jQuery('#content-add_media b').html(attachment.url);
	 		jQuery('#rm-banner-url').val(attachment.url);
	 	});
	 </script>
-->

	<b>Banner Caption</b><br />
	<textarea type="text" id="rm-banner-caption" name="rm-banner-caption" style="width:100%;height:100px;padding:2px;"><?php echo get_post_meta($post->ID, 'rm-banner-caption', true); ?></textarea><br />
	<small>A short caption describing the banner image.</small><br /><br />
	<b>Link URL</b><br />
	<input type="text" id="rm-banner-link" name="rm-banner-link" style="width:100%;padding:2px;" value="<?php echo get_post_meta($post->ID, 'rm-banner-link', true); ?>" /><br />
	<small>The URL to be loaded when the user clicks on the banner.</small>
<?php
}


/* UTILITIES **********************************************/

function get_banners($type, $args=array(), $return=false) {

	$args['post_type'] = 'banner';
	$args['orderby'] = 'menu_order date';
	$args['order'] = 'ASC';
	if ($args['slug']) {
		$args['tax_query'][] =
							array(
								'taxonomy' => 'banner-category',
								'field' => 'slug',
								'terms' => $args['slug']
							);
	}
	
	$q = new WP_Query($args);
	
	if ($return) {
		return $q->posts;
	} else {
		$GLOBALS['banners'] = $q->posts;
		get_template_part('banner', $type);
	}
	
}

function get_banners_for_category($slug, $args=array()) {
	$args['post_type'] = 'banner';
	$args['orderby'] = 'menu_order';
	$args['order'] = 'ASC';
	$args['tax_query'] = array(
							array(
								'taxonomy' => 'banner-category',
								'field' => 'slug',
								'terms' => $slug
							));
	$q = new WP_Query($args);
	return $q->posts;
}

function get_banner_link($id) {
	$link = get_post_meta($id, 'rm-banner-link', true);
	return (strstr($link, 'http')) ? $link : get_bloginfo('url').$link;
}



?>
