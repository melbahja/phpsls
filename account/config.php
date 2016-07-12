<?php defined('INC_FILES') || exit('Direct access not allowed');

/**
 * SLS config
 */
define('SLS_DIR', __DIR__ . '/phpsls');  // phpsls folder dir
define('DOMAIN', 'example.com'); // domain name only 
define('WEB_URL', 'http://' . DOMAIN . '/account'); // sls account url
define('LOGIN_TO', WEB_URL . '/index.php'); // redirect user after login

/**
 * Database Config
 */
define('DB_HOST', 'localhost');  // db hostname
define('DB_USER', 'root'); // db username
define('DB_PASS', ''); // db password
define('DB_NAME', 'Mydb'); // database name

/**
 * Antispam google recaptcha
 * get your site key and secret key here : https://www.google.com/recaptcha/intro/index.html
 */
define('SITE_KEY', '6Le3xCMTAAAAAHuJCb5nMlbAzYyDDDZoTovjRSOG');
define('SECRET_KEY', '6Le3xCMTAAAAAOBSiQ55MwApTDIQJ5raS9JLVO');

$mailer_config = array(
		'setFrom' => 'no-replay@example.com', //  email
		'replayTo' => 'support@example.com', //  support mail 
		'siteName' => 'PHPSlsSite', // your website name
	);
