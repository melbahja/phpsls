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
  $sls->redirect(WEB_URL, 'js');
  exit;
}

 
    if($form->token_valid('forgot', $_POST) === false) exit('Request or token not valid');

    $valid = $form->valid();

    if(!isset($_POST['email']) || empty($_POST['email']) || $valid->email($_POST['email']) === false) {
      exit('Please add valid email');
    }

    $db = new mysqli();
    $db->conn();

    $email = $db->escape($_POST['email']);
    $check = $db->select_one('user_id, username, email', 'sls_users', "WHERE email='$email'");

    if($check !== null) {

        if(!isset($_POST['g-recaptcha-response']) || !$_POST['g-recaptcha-response']) {
          exit('Please check the AntiSpam reCaptcha.');
        }

        $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".SECRET_KEY."&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']));

        if($response->success === false) {
          exit('reCAPTCHA! is not valid, please try again');
        }

        unset($response, $_POST['g-recaptcha-response']);

        $sr = md5($email.rand()).md5(uniqid(time()).microtime());
        $ex_time = time() + 18000;
        
        if($db->insert('sls_forgot_password', ['sr_key' => $sr, 'ex_time' => $ex_time, 'user_id' => $check['user_id']]) === true) {

         $url_rv = WEB_URL . '/recovery.php?token=' . $sr; 
         
         $email_verify_msg = "<p dir='ltr'>Hello ".$check['username']." !
          <br/><br/>
          You forgot your password? No problem! Just click on the link below to select a new one:.
          <br/><br/>
          {$url_rv}
          <br/><br/>
          (If this wasn’t you, don’t worry; we won’t email you again)
         <br/><br/>
          Thanks,<br/>
          The ".$mailer_config['siteName']." Team</p>";

          require_once SLS_DIR . '/libs/mailer/class.phpmailer.php';
          $mail = new PHPMailer(true);  
          $mail->setFrom($mailer_config['setFrom'], $mailer_config['siteName']);
          $mail->addReplyTo($mailer_config['replayTo'], $mailer_config['siteName']);
          $mail->addAddress($email, $check['username']);
          $mail->Subject = ' New Password for your ' . $mailer_config['siteName'];
          $mail->isHTML(true);
          $mail->msgHTML($email_verify_msg);
          if(!$mail->Send()) exit('Error send mail, please Contact Support');
          $form->close('forgot');
          unset($_POST, $form);
          exit('success');

       } else {

         exit('Error please try again');
       }

        
    } else {

      exit('email not exists, try again or contact support');
    }

    unset($check, $valid);
exit;
