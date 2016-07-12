<?php
namespace SlsCore\Data;

class Hash
{

	public function hash_pass($data)
	{
		return md5(hash('SHA256', md5($data)));
	}

	public function verify_pass($hash, $data)
	{
		return ($hash === $this->hash_pass($data));
	}
}