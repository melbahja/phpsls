<?php
namespace SlsCore\Data;

/**
 * Form CSRF Token Generator
 */
class Form
{
	protected $form_id  = 'Id';
	protected $token_name = 'CSRF_token';


	/**
	 * @param object $session 
	 */
	public function __construct($session)
	{
		$this->session = $session;
	} 

	/**
	 * From CSRF Token Generator
	 * @param strin $name
	 * @return string 
	 */
	public function token($name) 
	{
		$token_name = $name . $this->token_name;
		$form_id  = $name . $this->form_id;
		$fid   = md5(uniqid() . time());
		$token = md5($fid . md5($name . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']));
		$this->session->$form_id   = $fid;
		$this->session->$token_name = $token;
		return '<input name="'.$token_name.'" type="hidden" value="' . $token . '" /><input name="'.$form_id.'" type="hidden" value="'. base64_encode($fid) .'" />';
	}


	/**
	 * CSRF Token Validation
	 * @param string $name 
	 * @param  array $method 
	 * @return boolean
	 */
	public function token_valid($name, $method)
	{

		$token_name = $name . $this->token_name;
		$form_id  = $name . $this->form_id;

		if($_SERVER['REQUEST_METHOD'] !== 'POST' || isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') return false;

		if(!isset($method[$token_name]) || !isset($method[$form_id])) {
			return false; 
		} elseif(empty($method[$token_name]) || empty($method[$form_id])) {
			return false;
		} 

		$_fromid  = $method[$form_id];
		$_tokenid = $method[$token_name];

		if($this->session->valid() === true && base64_decode($_fromid) === $this->session->$form_id && $_tokenid === $this->session->$token_name && $_tokenid === md5(base64_decode($_fromid) . md5($name . $_SERVER['HTTP_USER_AGENT'] . $_SERVER['REMOTE_ADDR']))) return true;

		return false;
	}

	public function close($name) {
		unset($_SESSION[$name . $this->token_name], $_SESSION[$name . $this->form_id]);
	}

	public function valid()
	{
		return new Valid();
	}

}
