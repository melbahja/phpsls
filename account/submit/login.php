<?php
define('INC_FILES', true);

require_once('../config.php');
require_once(SLS_DIR . '/autoload.php');

use SlsCore\Data\Form as form;
use SlsCore\Data\Mysqli as mysqli;
use SlsCore\Data\Hash as hash;
use SlsCore\Sls as sls;

$sls = new sls();

$form = new form($sls->session);

if($sls->is_login() === true) {
  $sls->redirect(LOGIN_TO, 'js');
  exit;
}


if($form->token_valid('login', $_POST) === false) exit('request or token is not valid');

$valid = $form->valid();

if(!isset($_POST['email']) || empty($_POST['email']) || !$valid->email($_POST['email'])) exit('Please add valid email');

if(!isset($_POST['email']) || empty($_POST['email'])) exit('Please add your password');


if($sls->session->_show_recpt === true) {

	if(!isset($_POST['g-recaptcha-response']) || !$_POST['g-recaptcha-response']) exit('Please check the reCaptcha');
	$response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".SECRET_KEY."&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']));
	
    if($response->success === false) exit('reCAPTCHA! is not valid, please try again');

    unset($response, $_POST['g-recaptcha-response']);
}

$db = new mysqli();
$db->conn();

$email 	  = $db->escape($_POST['email']);
$password = $db->escape($_POST['password']);

$user = $db->select_one('user_id, username, password', 'sls_users', "WHERE email='$email'");


if(is_null($user)) {

	exit('email not exists, please try again');

} else {

	if((new hash())->verify_pass($user['password'], $password) === true) {

		$sls->session->user_id 	= $user['user_id'];
		$sls->session->username = $user['username'];
		$form->close('login');
		$sls->session->del('limit_att');
		$sls->session->del('_show_recpt');
		exit('success');
	}

	if($sls->session->limit_att === false) { 
	  $sls->session->limit_att = 1;	
	  exit('Password not exists, please try again');

	} else {

		$sls->session->limit_att = $sls->session->limit_att + 1;
		if($sls->session->limit_att >= 5) {
			$sls->session->_show_recpt = true;
			echo '<script>
			var nscript = document.createElement("script");
			nscript.setAttribute("src", "https://www.google.com/recaptcha/api.js");
			document.head.appendChild(nscript);
			$(\'#recaptcha\').html(\'<div style="padding:5px0px;margin-top:8px;"align="center" class="g-recaptcha" data-sitekey="'.SITE_KEY.'"></div>\');</script>';
			exit('Please check AntiSpam reCaptcha');
        } else {
			exit('Password not exists, please try again');
		}

	} 

}