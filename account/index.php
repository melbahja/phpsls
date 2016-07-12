<?php
define('INC_FILES', true); // for security

require_once('config.php');
require_once(SLS_DIR . '/global_sls.php');

if($sls->is_login() === true ) {

$sls->redirect(LOGIN_TO);
exit;

} else {

$sls->redirect(WEB_URL . '/login.php');
exit;

}
