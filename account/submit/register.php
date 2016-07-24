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
 
    if($form->token_valid('register', $_POST) === false) exit('Request or token not valid');

    $valid = $form->valid();
    
    if(!isset($_POST['fname']) || empty($_POST['fname']) || $valid->alpha($_POST['fname']) === false){
      exit('Please add valid first name');
    } 

    if(!isset($_POST['lname']) || empty($_POST['lname']) || $valid->alpha($_POST['lname']) === false) {
      exit('Please add valid last name');
    } 

    $valid_gender = false;

    if(!isset($_POST['gender']) || empty($_POST['gender'])) {
      exit('Please select a gander');
    } elseif($_POST['gender'] === 'female' || $_POST['gender'] === 'male') {
      $valid_gender = true;
    }

    if($valid_gender === false) {
      exit('gender type not allowed');
    }

    if(!isset($_POST['username']) || empty($_POST['username']) || strlen($_POST['username'])<4 || $valid->alphatic($_POST['username']) === false) {
      exit('Please add valid username');
    }

    if(!isset($_POST['email']) || empty($_POST['email']) || $valid->email($_POST['email']) === false) {
      exit('Please add valid email');
    }

    if(!isset($_POST['password']) || empty($_POST['password']) || strlen($_POST['password']) < 6) {
      exit('Please add your password');
    } 

    if(!isset($_POST['repassword']) || empty($_POST['repassword'])) {
      exit('Please Confirm your password');
    } 

    if($_POST['repassword'] !== $_POST['password']) {
      exit('The Passwords did not match');
    }

    $db = new mysqli();
    $db->conn();

    $email    = $db->escape($_POST['email']);
    $username = $db->escape($_POST['username']);

    $check = $db->select_one('username, email', 'sls_users', "WHERE username='$username' OR email='$email'");

    if($check !== null) {

      if($username === $check['username']) {
        exit('The username taken, please try again');
      }

      if($email === $check['email']) {
        exit('the email taken, please try again');
      }
    }

    unset($check, $valid);

    if(!isset($_POST['g-recaptcha-response']) || !$_POST['g-recaptcha-response']) {
      exit('Please check the AntiSpam reCaptcha.');
    }

    $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".SECRET_KEY."&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']));

    if($response->success === false) {
       exit('reCAPTCHA! is not valid, please try again');
    }

    unset($response, $_POST['g-recaptcha-response']);

    $password = (new hash())->hash_pass($_POST['password']);

    $sr = md5(uniqid(time()) . microtime());

    $data = array(
        'user_id' => null,
        'is_verify' => 0,
        'username' => $_POST['username'],
        'first_name' => $_POST['fname'],
        'last_name' => $_POST['lname'],
        'gender' => $_POST['gender'],
        'email' => $_POST['email'],
        'password' => $password,
        'sr_key' => $sr
    );

    if($db->insert('sls_users', $data) === true) {

      $url_verify = WEB_URL . '/verify.php?uid=' . $db->insert_id  . '&token=' . $sr; 

      $email_verify_msg = "<p dir='ltr'>Hello!
          <br/><br/>
          Thank you for joining <b> ".DOMAIN."</b>.
         <br/><br/>
          Verify your email address and start using ".$mailer_config['siteName']." here:<br/><br/>
          {$url_verify}
          <br/><br/>
          (If this wasn’t you, don’t worry; we won’t email you again)
          <br/><br/>
          Thanks,<br/>
          The ".$mailer_config['siteName']." Team</p>";

      require_once SLS_DIR . '/libs/mailer/class.phpmailer.php';
      $mail = new PHPMailer();  
      $mail->setFrom($mailer_config['setFrom'], $mailer_config['siteName']);
      $mail->addReplyTo($mailer_config['replyTo'], $mailer_config['siteName']);
      $mail->addAddress($email, $db->escape($_POST['fname']) . ' ' . $db->escape($_POST['lname']));
      $mail->Subject = $mailer_config['siteName'] . ' account verification for ' . $username;
      $mail->isHTML(true);
      $mail->msgHTML($email_verify_msg);
      if(!$mail->Send()) exit('Error send mail verification, please Contact Support');
      $sls->session->_verify_msg = 'Your account created, please check your email';
      $form->close('register');
      unset($_POST, $form);
      exit('success');
    } else {

      exit('Error : Please try again or Contact Support');
    }

exit;
