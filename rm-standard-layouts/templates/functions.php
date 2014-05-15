<?php

if ( function_exists( 'add_theme_support' ) ) add_theme_support( 'post-thumbnails' );

add_action('after_setup_theme', '%rm-theme-prefix%_theme_setup');
function %rm-theme-prefix%_theme_setup() {}

?>