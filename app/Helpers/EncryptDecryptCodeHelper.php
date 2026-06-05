<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\User;
use Illuminate\Support\Facades\Crypt;

class EncryptDecryptCodeHelper
{
	public function __construct()
	{
	}
	
	public static  function encryptData($data)
	{
		$encrypted = Crypt::encrypt($data);
		return $encrypted;
	}
	public static  function decryptData($data)
	{
		$decrypted = Crypt::decrypt($data);
		return $decrypted;
	}
}
