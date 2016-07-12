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


if($form->token_valid('recovery', $_POST) === false) exit('request or token is not valid');

$valid = $form->valid();

if(!isset($_POST['pass_token']) || empty($_POST['pass_token'])) {

	$sls->session->_forgot_msg = 'token not exists';
	$sls->redirect('forgot.php', 'js');
	exit;
}

$db = new mysqli();
$db->conn();

$token = $db->escape($_POST['pass_token']);

$data = $db->select_one('user_id, ex_time', 'sls_forgot_password', "WHERE sr_key='$token'");

if(is_null($data)) {

	$sls->session->_forgot_msg = 'token not found ';
	$sls->redirect('forgot.php', 'js');
	exit;

} else {

	if(time() >= $data['ex_time']) {

		$sls->session->_forgot_msg = 'token expired please try again';
		$db->delete('sls_forgot_password', "WHERE user_id='" . $data['user_id'] . "'");
		$sls->redirect('forgot.php', 'js');
		exit;
	}

}

if(!isset($_POST['password']) || empty($_POST['password']) || strlen($_POST['password']) < 6) exit('Please add valid password');

if(!isset($_POST['repassword']) || empty($_POST['repassword'])) exit('Please add a confirm password');

if($_POST['password'] !== $_POST['repassword']) exit('the passwords did not match');


if(!isset($_POST['g-recaptcha-response']) || !$_POST['g-recaptcha-response']) exit('Please check the reCaptcha');
$response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".SECRET_KEY."&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']));

if($response->success === false) exit('reCAPTCHA! is not valid, please try again');

unset($response, $_POST['g-recaptcha-response']);

$password = (new hash())->hash_pass($db->escape($_POST['password']));
$uid = (int) $data['user_id'];

if($db->update('sls_users', ['password' => $password], "WHERE user_id='$uid'")) {
	
	$db->delete('sls_forgot_password', "WHERE user_id='" . $data['user_id'] . "'");
	$sls->session->_login_msg = 'your password changed successfully';
	exit('success');

} else {

	exit('Error please try again or contact support');

}

exit;