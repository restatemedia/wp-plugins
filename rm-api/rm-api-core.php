<?php

/** SETUP URL DECODING **************************************/

add_action( 'template_redirect', 'rm_api_rewrite_catch', 0);
function rm_api_rewrite_catch() {
    if( get_query_var( 'api-func' ) ) {
		do_action( 'rm_api_' . get_query_var('api-func') );
        exit();
    } 
}

add_filter( 'query_vars', 'rm_api_custom_vars' );
function rm_api_custom_vars( $public_query_vars ) {
 	$public_query_vars[] = 'api-func';	
	return $public_query_vars;
}

add_filter('generate_rewrite_rules', 'rm_api_gen_rules');
function rm_api_gen_rules($rewrite) {
	
	$rewrite->rules = array_merge(
					array('api/([^/]+)$' =>  'index.php?api-func=$matches[1]'), 
					$rewrite->rules);	
					
	add_rewrite_tag( '%api-func%', '([^/]+)' );
	$rewrite->add_permastruct('api-func', '/api/%api-func%', false);						 
	
	return $rewrite;
}

?>