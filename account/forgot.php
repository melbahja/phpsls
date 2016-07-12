<?php
define('INC_FILES', true);

require_once('./config.php');
require_once(SLS_DIR . '/autoload.php');

use SlsCore\Data\Form as form;
use SlsCore\Sls as sls;

$sls = new sls();

if($sls->is_login() === true) {
  $sls->redirect(LOGIN_TO);
  exit;
}
 
?>
<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    
    <!-- css -->
    <link rel='stylesheet' href='assets/css/bootstrap.min.css'>
    <link rel="stylesheet" href="assets/css/style.css">

     
  </head>

  <body>

  <div class="wrapper">
    <form class="form-signin" id="forgot" accept-charset="utf-8" method="POST">       
      <h2 class="form-signin-heading">Forgot Password</h2>
 <?php 
if($sls->session->_forgot_msg !== false) {
    echo '<div class="alert btn-info">'.$sls->session->_forgot_msg .'</div>'; 
    $sls->session->del('_forgot_msg');
}
?>
<div id="forgotdata">
      <div class="form-group">
      <?= (new form($sls->session))->token('forgot'); ?>
      <input type="text" class="form-control" name="email" id="email" placeholder="Your Email"  />
      </div>

  <div class="form-group">
    <div class="g-recaptcha" data-sitekey="<?= SITE_KEY; ?>"></div>
  </div>
</div>

    <div class="form-group">
    <div id="form-message"></div>
  </div>

  <div class="form-group">
      <button class="btn btn-lg btn-primary btn-block" id="btn-submit" type="submit">Get new password</button>
   </div>

   <div id="form-group"> 
       <a class="pull-right" href="register.php"> Register </a>
       <a class="pull-left" href="login.php"> Login </a>
   </div>
        
    </form>
  </div>
    
    
   <!-- required js -->
   <script type="text/javascript" src="assets/js/jquery.js"></script> 
   <script type="text/javascript" src="assets/js/jquery-validation.js"></script>
   <script src='https://www.google.com/recaptcha/api.js'></script> 
   <script type="text/javascript" src="assets/js/forgot.js"></script> 
  </body>
</html>