<?php

require_once(dirname(__FILE__) . '/../../../wp-load.php');

require('PHPMailer/class.phpmailer.php');

           
$mail = new PHPMailer;
$mail->PluginDir = dirname(__FILE__).'/PHPMailer/';
$mail->CharSet = 'utf-8';

// SETUP RECIPIENTS

$contact_email = ($_POST['rm-email-to']) ? $_POST['rm-email-to'] : get_option('rm-contact-email');
$from = get_option('rm-contact-from');

$mail->From = $from;
$mail->FromName = get_bloginfo('sitename');
$mail->AddReplyTo($from, get_bloginfo('sitename'));
$mail->AddAddress($contact_email);  // Add a recipient

// SETUP BODY 

$mail->IsHTML(true);                                  

$email_template = ($_POST['rm-email-template']) ? $_POST['rm-email-template'] : 'email';

$email_template = file_get_contents(get_template_directory().'/'.$email_template.'.php');
$email_template = str_replace('%name%', $_POST['rm-name'], $email_template);
$email_template = str_replace('%email%', $_POST['rm-email'], $email_template);
$email_template = str_replace('%message%', nl2br($_POST['rm-message']), $email_template);

foreach($_POST['rm-custom'] as $key=>$val) {
	$email_template = str_replace('%'.$key.'%', $val, $email_template);
}

$mail->Subject = $_POST['rm-subject-prefix'].$_POST['rm-subject'];
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

$res = $mail->Send();
wp_redirect(add_query_arg('rm-result', $res, $_POST['rm-return-url']));
exit();


?>