<?php defined('INC_FILES') || exit('direct access not allowed');

require_once(SLS_DIR . '/autoload.php');

$sls = new SlsCore\Sls();

spl_autoload_unregister('SlsLoader'); // unregister phpsls autoloader