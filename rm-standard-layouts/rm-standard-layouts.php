<?php
/*
Plugin Name: RM Standard Layouts
Description: Provides standard styles and page structures used across many sites.
Author: Joe Flumerfelt
Version: 0.1
*/

session_start();

add_action('admin_enqueue_scripts', 'rm_admin_styles', 0);
function rm_admin_styles() {
	wp_enqueue_script('rm-standard-scripts', plugins_url('rm-standard.js', __FILE__), array('jquery'));
	wp_enqueue_script('rm-standard-class', plugins_url('Class.js', __FILE__), array('jquery'));
	wp_enqueue_style('rm-standard-styles', plugins_url('rm-standard-admin.css', __FILE__));
}

add_action('wp_enqueue_scripts', 'rm_styles', 0);
function rm_styles() {
	wp_enqueue_style('rm-standard-layouts', plugins_url('rm-standard-layouts.css', __FILE__));
	wp_enqueue_script('rm-standard-class', plugins_url('Class.js', __FILE__), array('jquery'));
	wp_enqueue_script('rm-standard-scripts', plugins_url('rm-standard.js', __FILE__), array('jquery'));
	wp_enqueue_script('jquery-wait', plugins_url('jquery.wait.js', __FILE__), array('jquery'));
	wp_enqueue_script('jquery-parse-params', plugins_url('jquery.parseParams.js', __FILE__), array('jquery'));
}

add_action('wp_enqueue_scripts', 'rm_styles_end', 20);
function rm_styles_end() {
	wp_enqueue_style('theme-style', get_bloginfo('stylesheet_url') . '?t=' . 
									filemtime( get_stylesheet_directory() . '/style.css'));
}

/* PLUGIN SETTINGS **************************************************/

function rm_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=rm-standard-layouts/rm-standard-layouts.php">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}

$plugin = plugin_basename(__FILE__); 
add_filter("plugin_action_links_".$plugin, 'rm_settings_link' );

add_action('admin_menu', 'rm_create_menu');
add_action( 'admin_init', 'rm_register_settings' );
function rm_create_menu() {
	add_options_page('Standard Layouts Settings', 'Standard Layouts', 'administrator', __FILE__, 'rm_layout_settings_page');
}
function rm_register_settings() {
	register_setting( 'rm-settings-group', 'rm-theme-folder' );
	register_setting( 'rm-settings-group', 'rm-theme-prefix' );
	register_setting( 'rm-settings-group', 'rm-theme-name' );
	register_setting( 'rm-settings-group', 'rm-theme-author' );
	register_setting( 'rm-settings-group', 'rm-header-height' );
	register_setting( 'rm-settings-group', 'rm-footer-height' );
	register_setting( 'rm-settings-group', 'rm-content-width' );
}

function rm_layout_settings_page() {
?>
<div class="wrap">
<h2>RM Standard Layout Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'rm-settings-group' ); ?>
    <table class="form-table">
    	<tr valign="top">
    		<td colspan="2"><h3>Theme Settings</h3></td>
    	</tr>
    	<tr valign="top">
        <th scope="row">Theme Folder</th>
        <td><input type="text" name="rm-theme-folder" 
        		   value="<?php echo get_option('rm-theme-folder'); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Theme Function Prefix</th>
        <td><input type="text" name="rm-theme-prefix" 
        		   value="<?php echo get_option('rm-theme-prefix'); ?>" /></td>
        </tr>
    	<tr valign="top">
        <th scope="row">Theme Name</th>
        <td><input type="text" name="rm-theme-name" 
        		   value="<?php echo get_option('rm-theme-name'); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">Theme Author</th>
        <td><input type="text" name="rm-theme-author" 
        		   value="<?php echo get_option('rm-theme-author'); ?>" /></td>
        </tr>
         
        <tr valign="top">
    		<td colspan="2"><h3>Layout Settings</h3></td>
    	</tr>
    	<tr valign="top">
        <th scope="row">Header Height</th>
        <td><input type="text" name="rm-header-height" 
        		   value="<?php echo get_option('rm-header-height'); ?>" /></td>
        </tr> 
        <tr valign="top">
        <th scope="row">Footer Height</th>
        <td><input type="text" name="rm-footer-height" 
        		   value="<?php echo get_option('rm-footer-height'); ?>" /></td>
        </tr> 
        <tr valign="top">
        <th scope="row">Content Width</th>
        <td><input type="text" name="rm-content-width" 
        		   value="<?php echo get_option('rm-content-width'); ?>" /></td>
        </tr> 
         
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>    

</form>

	<hr />
    
    <p>
 
    <input type="button" class="button-secondary" onclick="document.location.href = '<?php echo plugins_url('rm-generate.php', __FILE__); ?>';" value="<?php _e('Generate Templates');?>">
    <br />
    <small><i>NOTE: This action will copy files from the plugin to your theme directory. It will skip any files that already exist.</i></small>
    </p>


</div>
<?php } 

/* TEMPLATES *************************************************/

function rm_get_template($template, $ops=array()) {
	$str = file_get_contents(dirname(__FILE__).'/templates/'.$template.'.php');
	if ($str) {
		foreach($ops as $key=>$val) {
			$str = str_replace( '%'.$key.'%', $val, $str );
		}
	}
	return $str;
}

function rm_write_to_theme($base, $name, $content) {
	$path = $base.'/'.$name;
	if (!file_exists($path)) {
		file_put_contents($path, $content);
	}
}

/** REMOVE ADMIN BAR FROM SITE ************************************/

remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
function remove_admin_bar_style_frontend() {   
  echo '<style type="text/css" media="screen"> 
  html { margin-top: 0px !important; } 
  * html body { margin-top: 0px !important; } 
  </style>';  
}  
add_filter('wp_head','remove_admin_bar_style_frontend', 99); 

?>