<?php
namespace Azamatx\EskizsmsLaravel;

use Illuminate\Support\Facades\Http;
use File;

class EskizsmsLaravel
{
	private $token;
	private $email;
	private $password;

    public function __construct()
	{
		// $this->saveToken();
		$this->token = $this->readToken();
		print_r($this->token);
	}

	public function readToken()
	{
		if (File::exists(config_path('eskizsms.json'))) {
			$config_file = File::get(config_path('eskizsms.json'));
			return json_decode($config_file, true);
		} else {

		}
	}

	public function getToken()
	{
		$response = Http::post('https://notify.eskiz.uz/api/auth/login');
		return $response;
	}

	public function updateToken()
	{
		
	}

	public function deleteToken()
	{
		
	}

	public function saveToken()
	{
		$data = json_encode([
			'created' => date('Y-m-d H:i:s'),
			'token' => 'test'
		]);

		$res = File::put(config_path('eskizsms.json'), $data);
	}
}
