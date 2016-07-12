<?php
define('INC_FILES', true);

require_once('./config.php');
require_once(SLS_DIR . '/autoload.php');

use SlsCore\Data\Form as form;
use SlsCore\Data\Mysqli as mysqli;
use SlsCore\Sls as sls;

$sls = new sls();

$token_not_exists = false;
$req = (isset($_GET['uid']) && isset($_GET['token']) && !empty($_GET['uid']) && !empty($_GET['token']));

if($req === true) {

  $db = new mysqli();
  $db->conn();

  $uid    = (int) $db->escape($_GET['uid']);
  $sr_key = $_GET['token'];

  $data = $db->select_one('is_verify, sr_key', 'sls_users', "WHERE user_id='$uid'");

  if($data !== null) {

    if($data['is_verify'] == 1) {

       $sls->session->_login_msg = 'your account already verified';
       $sls->redirect('login.php');
       exit;

    } else {

      if($sr_key === $data['sr_key']) {

         $db->update('sls_users', ['is_verify' => 1], "WHERE user_id='$uid'");
         $sls->session->_login_msg = 'your account has been activated';
         $sls->redirect('login.php');
         exit;

      } else {

        $token_not_exists = true;
        $message = 'token not exists please try again or contact support';

      }

    }



  } else {

    $sls->session->_login_msg = 'verification link not exists';
    $sls->redirect('login.php');
    exit;

  } 

} else {

  $token_not_exists = true;
  $message = 'token not exists please try again or contact support';

}

?>


<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Account verification</title>
    
    <!-- css -->
    <link rel='stylesheet' href='assets/css/bootstrap.min.css'>
    <link rel="stylesheet" href="assets/css/style.css">

  </head>

  <body>

  <div class="wrapper">


  <div class="form-signin">
     <h2>Account verification</h2>
<?php 
$showform = true;
if($sls->session->_verify_msg !== false) {
    echo '<div class="alert btn-info">'.$sls->session->_verify_msg .'</div>'; 
    $sls->session->del('_verify_msg');
    $showform = false;
}

if($showform === true && $token_not_exists === true) { ?>
     <div class="alert btn-info"><?=$message;?></div> 
          <form id="verify" accept-charset="utf-8" method="POST">       
            <div class="form-group">
            <?= (new form($sls->session))->token('verify'); ?>
            </div>
            <div class="form-group">
            <input type="text" class="form-control" name="email" id="email" placeholder="Email Address"  />
            </div>
          <div class="form-group">
            <div class="g-recaptcha" data-sitekey="<?= SITE_KEY; ?>"></div>
          </div>

        <div class="form-group">
        <div id="form-message"></div>
        </div>
        <div class="form-group">
            <button class="btn btn-lg btn-primary btn-block" id="btn-submit" type="submit">Send verification link</button>
         </div>
        </form>
      <?php } ?>  
  </div>
  </div>
    
   <!-- required js -->
   <script type="text/javascript" src="assets/js/jquery.js"></script> 
   <script type="text/javascript" src="assets/js/jquery-validation.js"></script> 
   <script src='https://www.google.com/recaptcha/api.js'></script>
   <script type="text/javascript" src="assets/js/verify.js"></script> 
  </body>
</html>
