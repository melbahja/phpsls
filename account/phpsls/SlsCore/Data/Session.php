<?php
namespace SlsCore\Data;

class Session
{

	public $https = false;
	protected $sname;

	public function start($name = 'sls_sid', $limit = 2592000, $path = '/', $domain = null)
	{
		$domain = ($domain === null) ? '.' . DOMAIN : $domain;
        $this->sname = $name;

        ini_set('session.cookie_secure', 1); 
        ini_set('session.cookie_httponly', 1);

		session_name($this->sname);

		session_set_cookie_params($limit, $path, $domain, $this->https, true);
		session_start();

		if (!isset($_SESSION['_usrid'])) {

			$this->destroy();
		    $_SESSION['_usrid'] = $this->userid();

		}

		if ($this->valid() === false) $this->destroy();
	}


	public function __destruct()
	{
		session_write_close($this->sname);
	}

	public function valid()
	{
		return (isset($_SESSION['_usrid']) && $_SESSION['_usrid'] === $this->userid());
	}

	public function destroy($all = false)
	{

		if ($all === true) {

           session_unset();
           session_destroy();

		} else {

			session_unset($this->sname);
		}

	}

	public function __set($k, $v)
	{
		$_SESSION[$k] = $v;
	}

	public function __get($k)
	{
		return isset($_SESSION[$k]) ? $_SESSION[$k] : false;
	}

	public function del($name)
	{
		unset($_SESSION[$name]);
	}

	protected function userid()
	{
		return md5($_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . @$_SERVER['HTTP_ACCEPT_LANGUAGE']);
	}

}