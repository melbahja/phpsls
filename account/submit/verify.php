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

    if($form->token_valid('verify', $_POST) === false) exit('Request or token not valid');

    $valid = $form->valid();

    if(!isset($_POST['email']) || empty($_POST['email']) || $valid->email($_POST['email']) === false) {
      exit('Please add valid email');
    }

    $db = new mysqli();
    $db->conn();

    $email = $db->escape($_POST['email']);

    $data = $db->select_one('user_id, is_verify, username, sr_key', 'sls_users', "WHERE email='$email'");

    if(is_null($data)) {

      exit('email not exists, please try again');

    } 

    if(!is_null($data) && $data['is_verify'] == 1) {

      $sls->session->_login_msg = 'your account already verified';
      $sls->redirect('login.php', 'js');
      exit;

    } else {

      if(!isset($_POST['g-recaptcha-response']) || !$_POST['g-recaptcha-response']) {
        exit('Please check the AntiSpam reCaptcha.');
      }

      $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".SECRET_KEY."&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']));

      if($response->success === false) {
         exit('reCAPTCHA! is not valid, please try again');
      }

      unset($response, $_POST['g-recaptcha-response']);

      $sr = md5(uniqid(time()) . microtime());

      $db->update('sls_users', ['sr_key' => $sr], "WHERE user_id='".$data['user_id']."'");

      $url_verify = WEB_URL . '/verify.php?uid=' . $data['user_id']  . '&token=' . $sr; 

      $email_verify_msg = "<p dir='ltr'>Hello!
          <br/><br/>
          Thank you for joining <b> ".DOMAIN."</b>.
         <br/><br/>
          Verify your email address and start using ".$mailer_config['siteName']." here:<br/><br/>
          {$url_verify}
          <br/><br/>
          Thanks,<br/>
          The ".$mailer_config['siteName']." Team</p>";

      require_once SLS_DIR . '/libs/mailer/class.phpmailer.php';
      $mail = new PHPMailer();  
      $mail->setFrom($mailer_config['setFrom'], $mailer_config['siteName']);
      $mail->addReplyTo($mailer_config['replyTo'], $mailer_config['siteName']);
      $mail->addAddress($email, $data['username']);
      $mail->Subject = $mailer_config['siteName'] . ' account verification for ' . $data['username'];
      $mail->isHTML(true);
      $mail->msgHTML($email_verify_msg);
      if(!$mail->Send()) exit('Error send mail verification, please Contact Support');
      $form->close('verify');
      unset($_POST, $form);
      exit('success');
    }

    unset($data, $valid);
exit;
