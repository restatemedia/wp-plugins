<?php
/*
Plugin Name: Restate Media Webservice API
Description: This plugin provides a simple way for registering functions to be used as web-services. This interface means that any plugin or theme can define their own services and have them triggered through the URL scheme http://{your-domain-name}/api/{service}.
Version: 1.0.0
Author: Joe Flumerfelt
License: GPLv2 or later
*/

/** 
 *	Array2XML
 * 	For information regarding how to setup your array, go to:
 * 	http://www.lalit.org/lab/convert-php-array-to-xml-with-attributes/
 */
require_once(dirname(__FILE__).'/array2xml.php');

require_once(dirname(__FILE__).'/rm-api-core.php');

/** API INTERFACE ******************************************/

/**
 * Add a hook for a service
 */
function rm_api_register($service, $func) {
	add_action('rm_api_'.$service, $func);
}

/**
 * Format output and print to screen
 */
function rm_api_render($ob, $root='root') {
	$args = $_GET;
	if ($args['format'] == 'debug') {
		print_r($ob);
		exit;
	}
	if ($args['format'] == 'json') {
		print json_encode($ob);
		exit;
	}
	
	header("Content-type: text/xml; charset=utf-8");
	$xml = Array2XML::createXML($root, $ob);
	echo $xml->saveXML();
	exit;

}

/**
 * Get the current format for the output
 */
function rm_api_get_format() {
	if ($_GET['format']) return $_GET['format'];
	return 'xml';
}

?>