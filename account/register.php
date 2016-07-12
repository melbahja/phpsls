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
    <title>Register</title>
    
    <!-- css -->
    <link rel='stylesheet' href='assets/css/bootstrap.min.css'>
    <link rel="stylesheet" href="assets/css/style.css">

  </head>

  <body>

      <div class="wrapper">

    <form class="form-signin" id="register" accept-charset="utf-8" method="POST">       
      <h2 class="form-signin-heading">Register</h2>

      <div class="form-group">
      <?= (new form($sls->session))->token('register'); ?>
      <input type="text" class="form-control" name="fname" id="fname" placeholder="First Name"  />
      <input type="text" class="form-control" name="lname" id="lname" placeholder="Last Name"  />
      </div>
      <div class="form-group">
      <input type="text" class="form-control" name="username" id="username" placeholder="Username"  />
      <input type="text" class="form-control" name="email" id="email" placeholder="Email Address"  />
      </div>

  <div class="form-group">
   <select class="form-control" id="gender" name="gender"> 
     <option value="male">Male</option>
     <option value="female">Female</option> 
    </select>  
  </div>

  <div class="form-group">
    <input type="password" class="form-control" name="password" id="password" placeholder="Password"  />
    <input type="password" class="form-control" name="repassword" id="repassword" placeholder="Confirm password"  />
  </div>

  <div class="form-group">
    <div class="g-recaptcha" data-sitekey="<?= SITE_KEY; ?>"></div>
  </div>

  <div class="form-group">
  <div id="form-message"></div>
  </div>
  <div class="form-group">
      <button class="btn btn-lg btn-primary btn-block" id="btn-submit" type="submit">Register</button>
   </div>

   <div id="form-group">
      <label class="pull-right">
       Already have account ? <a href="login.php"> Login</a>
      </label>
   </div>
        
    </form>
  </div>
    
    
   <!-- required js -->
   <script type="text/javascript" src="assets/js/jquery.js"></script> 
   <script type="text/javascript" src="assets/js/jquery-validation.js"></script> 
   <script src='https://www.google.com/recaptcha/api.js'></script>
   <script type="text/javascript" src="assets/js/register.js"></script> 
  </body>
</html>