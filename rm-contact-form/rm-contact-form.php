<?php
/*
Plugin Name: RM Contact Form
Author: Joe Flumerfelt
Description: This plugin provides a VERY simple implementation of a contact form.
Version: 0.2
*/

add_action( 'wp_enqueue_scripts', 'rm_enqueue_style', 2 );
function rm_enqueue_style() {
	if ($GLOBALS['use_validator']) wp_enqueue_script('jquery-validate', 
		plugins_url('js/jquery.validate.js', __FILE__), 'jquery');
	wp_enqueue_script('rm-contact-form', plugins_url('js/rm-contact-form.js', __FILE__), 'jquery');
	wp_enqueue_style('rm-contact-form-css', plugins_url('rm-contact-form.css', __FILE__));
}

function rm_form($template, $args=array(), $output=true) {

	$url = plugins_url('rm-contact-form').'/rm-process-form.php';
	$return_url = ($args['return_url']) ? $args['return_url'] : 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"] ;
	
	$GLOBALS['form_args'] = $args;
	
	$html = '';
	$html .='<form id="rm-form" method="POST" '.((!$args['ajax'])?'action="'.$url:'').'">';
	$html .= rm_cf_load_template_part('form', $template); 
	$html .= '<input type="hidden" id="rm-return-url" name="rm-return-url" value="'.$return_url.'" />';
	$html .= '</form>';
	
	if ($output) {
		echo $html;
	} else {
		return $html;
	}
}

function rm_cf_load_template_part($template_name, $part_name=null) {
 	ob_start();
    get_template_part($template_name, $part_name);
    $var = ob_get_contents();
    ob_end_clean();
    return $var;
}


add_action('admin_menu', 'rm_cf_create_menu');
add_action( 'admin_init', 'rm_cf_register_settings' );
function rm_cf_create_menu() {
	add_options_page('RM Contact Form Settings', 'RM Form Settings', 'administrator', __FILE__, 'rm_cf_settings_page');
	
}
function rm_cf_register_settings() {
	register_setting( 'rm-settings-group', 'rm-contact-email' );
	register_setting( 'rm-settings-group', 'rm-contact-from' );
	register_setting( 'rm-settings-group', 'rm-contact-smtp' );
	register_setting( 'rm-settings-group', 'rm-contact-smtp-port' );
}

function rm_cf_settings_page() {
?>
<div class="wrap">
<h2>RM Contact Form Settings</h2>

<form method="post" action="options.php">
    <?php settings_fields( 'rm-settings-group' ); ?>
    <table class="form-table">
    
    	<tr valign="top">
        <th scope="row">Contact Email</th>
        <td><input type="text" name="rm-contact-email" 
        		   value="<?php echo get_option('rm-contact-email'); ?>" style="width:300px;" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">From Email</th>
        <td><input type="text" name="rm-contact-from" 
        		   value="<?php echo get_option('rm-contact-from'); ?>" style="width:300px;" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">SMTP Server</th>
        <td><input type="text" name="rm-contact-smtp" 
        		   value="<?php echo get_option('rm-contact-smtp'); ?>" style="width:300px;" /></td>
        </tr>
        <tr valign="top">
        <th scope="row">SMTP Port</th>
        <td><input type="text" name="rm-contact-smtp-port" 
        		   value="<?php echo get_option('rm-contact-smtp-port'); ?>" style="width:300px;" /></td>
        </tr>
         
    </table>
    
    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>
<?php } 

/* WEB SERVICES ***********************************************/

// Include the web servics only if the Restate Media API plugin is installed
if (function_exists('rm_api_register')) {

	rm_api_register('rm.form.process', 'rm_api_form_process');
	function rm_api_form_process() {
	
		require('PHPMailer/class.phpmailer.php');
           
		$mail = new PHPMailer;
		$mail->PluginDir = dirname(__FILE__).'/PHPMailer/';
		$mail->CharSet = 'utf-8';
		
		// SETUP RECIPIENTS
		
		$contact_email = ($_REQUEST['rm-email-to']) ? $_REQUEST['rm-email-to'] : get_option('rm-contact-email');
		$from = get_option('rm-contact-from');
		
		$mail->From = $from;
		$mail->FromName = get_bloginfo('sitename');
		$mail->AddReplyTo($from, get_bloginfo('sitename'));
		$mail->AddAddress($contact_email);  // Add a recipient
		
		// SETUP BODY 
		
		$mail->IsHTML(true);                                  
		
		$email_template = ($_REQUEST['rm-email-template']) ? $_REQUEST['rm-email-template'] : 'email';
		
		$email_template = file_get_contents(get_template_directory().'/'.$email_template.'.php');
		$email_template = str_replace('%name%', $_REQUEST['rm-name'], $email_template);
		$email_template = str_replace('%email%', $_REQUEST['rm-email'], $email_template);
		$email_template = str_replace('%message%', nl2br($_REQUEST['rm-message']), $email_template);
		
		foreach($_REQUEST['rm-custom'] as $key=>$val) {
			$email_template = str_replace('%'.$key.'%', $val, $email_template);
		}
		
		$mail->Subject = $_REQUEST['rm-subject-prefix'].$_REQUEST['rm-subject'];
		$mail->Body    = $email_template;
		
		// HOW TO SEND
		$smtp = get_option('rm-contact-smtp');
		if ($smtp) {
		
			$host = $smtp;
			$port = get_option('rm-contact-port');
			list($user, $pass) = explode('|', file_get_contents(dirname(__FILE__).'/smtp.txt'));
		
			ini_set('SMTP', 'tls://'.$host);
			ini_set('smtp_port', $post);
			
			$mail->IsSMTP(); 
			$mail->SMTPSecure = 'ssl';          
			$mail->Host = 'smtp.gmail.com'; 
			$mail->Port = 465; 				      
			$mail->SMTPAuth = true;                               
			$mail->Username = $user;                            
			$mail->Password = $pass; 
		
		} 
		
		$ob = array('result'=>$mail->Send());
		rm_api_render($ob);
	
	}	
	
}


?>