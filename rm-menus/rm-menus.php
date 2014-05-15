<?php
/*
Plugin Name: RM Menus
Description: Utility functions for working with menus
Author: Joe Flumerfelt
Version: 0.1
*/

function rm_nav_menu($id, $menu="main", $target=null) {

	global $post;
	
	$items = wp_get_nav_menu_items( $id );
	if (!$items) $items = wp_get_nav_menu_items( $id );
	
	_wp_menu_item_classes_by_context( $items );
	
	$ob = array();
	foreach((array) $items as $key=>$item) {
		
		$id = $item->ID;
		$parent = $item->menu_item_parent;
		$i = array('label'=>$item->title, 'url'=>$item->url, 'target'=>$item->target,
				   'classes'=>$item->classes);   		   		
				   
		if ($parent > 0) {
			if ($target && strstr($item->url, $target)) {
				$i['classes'][] = 'current-menu-item';
				$ob[$parent]['classes'][] = 'current-menu-item';
			}
			$ob[$parent]['children'][] = $i;
		} else {
		
			
			if ($target && strstr($item->url, $target)) {
				$i['classes'][] = 'current-menu-item';
			}
			$ob[$id] = $i;
		}	
		
	}
	
	$ob = array_values($ob);
	
	$GLOBALS['menu_ob'] = $ob;
	get_template_part('menu', $menu);
	
}

function rm_menu_has_submenu() {
	global $menu_ob, $submenu;
	
	foreach($menu_ob as $item) {
		if ((in_array('current-menu-item', $item['classes']) || 
			in_array('current-menu-parent', $item['classes'])) &&
			$item['children']) {
			$submenu = $item['children'];
		}
	}
	
	return ($submenu) ? true : false;
}



?>