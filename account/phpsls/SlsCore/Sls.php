<?php
namespace SlsCore;

class Sls 
{


	public $session = array();

	public function __construct()
	{
		
		error_reporting(0); // error reporting

		$this->session  = new Data\Session();
		$this->session->start();
	}

	public function is_login() 
	{
	   return ($this->session->user_id !== false && $this->session->username !== false);
	}

	public function redirect($url, $type = 'header')
	{
		if($type === 'header') {
	        if(!headers_sent()) {
	            header('Location: '. $url);
	            header('Connection: Close');
	        } else {
	            echo '<script type="text/javascript">window.location.href="'.$url.'";</script>';  
	        }
	    } elseif($type === 'js') {
	    	echo '<script type="text/javascript">window.location.href="'.$url.'";</script>'; 
	    }    
	}

}
