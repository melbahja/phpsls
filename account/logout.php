<?php
define('INC_FILES', true); 

require_once('config.php');
require_once(SLS_DIR . '/global_sls.php');

$sls->session->destroy();
$sls->redirect(WEB_URL);
exit;
