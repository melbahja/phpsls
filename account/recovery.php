<?php
define('INC_FILES', true);

require_once('./config.php');
require_once(SLS_DIR . '/autoload.php');

use SlsCore\Data\Form as form;
use SlsCore\Data\Mysqli as mysqli;
use SlsCore\Sls as sls;

$sls = new sls();

if($sls->is_login() === true) {

  $sls->redirect(LOGIN_TO);
  exit;
}

$change_pass = false;
$req = (isset($_GET['token']) && !empty($_GET['token']));

if($req === true) {

  $db = new mysqli();
  $db->conn();

  $token = $db->escape($_GET['token']);

  $data = $db->select_one('user_id, ex_time', 'sls_forgot_password', "WHERE sr_key='$token'");

  if($data !== null) {

    if(time() >= $data['ex_time']) {

       $sls->session->_forgot_msg = 'token expired please try again';
       $db->delete('sls_forgot_password', "WHERE user_id='" . $data['user_id'] . "'");
       $sls->redirect('forgot.php');
       exit;

    } else {

        $change_pass = true;
        $message = 'enter your new password';

    }

  } else {

    $sls->session->_forgot_msg = 'token not exists, please try again';
    $sls->redirect('forgot.php');
    exit;

  } 

} else {

    $sls->session->_forgot_msg = 'token not found, please try again';
    $sls->redirect('forgot.php');
    exit;

}

?>


<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Recovery password</title>
    <!-- css -->
    <link rel='stylesheet' href='assets/css/bootstrap.min.css'>
    <link rel="stylesheet" href="assets/css/style.css">

  </head>

  <body>

  <div class="wrapper">
     
<?php if($change_pass === true) {  ?>
         <div class="form-signin">
        <h2>Account verification</h2>
        <div class="alert btn-info"><?=$message;?></div> 
          <form id="recovery" accept-charset="utf-8" method="POST">       
            <div class="form-group">
            <?= (new form($sls->session))->token('recovery'); ?>
            <input type="hidden" name="pass_token" value="<?= $token ;?>">
            </div>
            <div class="form-group">
            <input type="password" class="form-control" name="password" id="password" placeholder="New password"  />
            <input type="password" class="form-control" name="repassword" id="repassword" placeholder="Confirm your password"  />
            </div>
          <div class="form-group">
            <div class="g-recaptcha" data-sitekey="<?= SITE_KEY; ?>"></div>
          </div>

        <div class="form-group">
        <div id="form-message"></div>
        </div>
        <div class="form-group">
            <button class="btn btn-lg btn-primary btn-block" id="btn-submit" type="submit">Change password</button>
         </div>
        </form>
      </div>
<?php } ?>  
  </div>
    
   <!-- required js -->
   <script type="text/javascript" src="assets/js/jquery.js"></script> 
   <script type="text/javascript" src="assets/js/jquery-validation.js"></script> 
   <script src='https://www.google.com/recaptcha/api.js'></script>
   <script type="text/javascript" src="assets/js/recovery.js"></script> 
  </body>
</html>