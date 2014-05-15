<?php

require_once(dirname(__FILE__).'/../../../wp-load.php');


/* THEME DIRECTORY ************************************/

$base = get_theme_root().'/'.get_option('rm-theme-folder');
if (!is_dir($base)) {
	mkdir($base);
}

/* STANDARD DIRECTORIES *******************************/

if (!is_dir($base.'/img')) mkdir($base.'/img');
if (!is_dir($base.'/css')) mkdir($base.'/css');
if (!is_dir($base.'/js')) mkdir($base.'/js');

/* STYLE SHEET ****************************************/

$ops = array('rm-theme-name'=>get_option('rm-theme-name'), 
			  'rm-theme-author'=>get_option('rm-theme-author'),
			  'rm-header-height'=>get_option('rm-header-height'), 
			  'rm-footer-height'=>get_option('rm-footer-height'), 
			  'rm-content-width'=>get_option('rm-content-width'));
rm_write_to_theme($base, 'style.css', rm_get_template('style', $ops));

/* MAIN TEMPLATES *************************************/

rm_write_to_theme($base, 'header.php', rm_get_template('header'));
rm_write_to_theme($base, 'inc-header.php', rm_get_template('inc-header'));
rm_write_to_theme($base, 'footer.php', rm_get_template('footer'));
rm_write_to_theme($base, 'inc-footer.php', rm_get_template('inc-footer'));
rm_write_to_theme($base, 'index.php', rm_get_template('index'));

$ops = array('rm-theme-prefix'=>get_option('rm-theme-prefix'));
rm_write_to_theme($base, 'functions.php', rm_get_template('functions', $ops));


/* REDIRECT TO SETTINGS ******************************/
$url = add_query_arg('status', $status, 
					 '/wp-admin/options-general.php?page=rm-standard-layouts/rm-standard-layouts.php');
wp_redirect($url);
exit();

?>