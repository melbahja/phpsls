<?php
namespace SlsCore\Data;

class Valid
{


	public function email($email) 
	{

		if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) return false;

		$domain = explode('@', $email);

		if(checkdnsrr($domain[1], 'MX') === false) return false;
		 
	  	return true;
	}

	public function alphatic($value) 
	{
	  	return preg_match('/^[\w]+$/', $value);
    }

    public function alpha($data)
    {
    	return ctype_alpha($data);
    }

    public function alphanum($data)
    {
    	return ctype_alnum($data);
    }

	public function url($url)
	{
	   return filter_var($url, FILTER_VALIDATE_URL);
	}    

}