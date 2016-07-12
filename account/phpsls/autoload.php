<?php defined('INC_FILES') || exit('Direct access not allowed');

/**
 * autoloading
 * @param string $class 
 * @return void
 */

function SlsLoader($x)  
{

	$prefix = explode('\\', $x);
	$prefix = $prefix[0];
	// check sls autoloader prefix
	if($prefix != 'SlsCore') { 
		return;
	}
		
    $inclass = SLS_DIR . '/' . str_replace('\\', '/', $x) . '.php';

	if (file_exists($inclass)) {

        if (!class_exists($x)) {
        	
	 	   require_once($inclass);
        }
        
   	 unset($inclass, $x); 
	}

}

spl_autoload_register('SlsLoader');