<?php
define('INC_FILES', true); // for security

require_once('account/config.php');
require_once(SLS_DIR . '/global_sls.php');

if($sls->is_login() === true ) {

echo '<a href="account/logout.php">logout</a> <br/>';

echo '<h2>session:</h2><br/>';

echo 'user id  : ' . $sls->session->user_id  . '<br/>';

echo 'username : ' . $sls->session->username . '<br/>';


// seve session example :
// $sls->session->name = 'value'
// 
// get session
// 
// echo $sls->session->name;
// 
// 
// redirect example :
// 
// $sls->redirect(WEB_URL . '/example.php');
// 
// 
// check user is login 
// 
// if($sls->is_login() === true ) {
//   // user login
// } else {
// 
//  // user not login
// 
// }
// 



} else {

$sls->redirect(WEB_URL . '/login.php');

}
