<?php
namespace Azamatx\EskizsmsLaravel;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use File;

class EskizsmsLaravel
{
	private $token;
	private $email;
	private $password;

    public function __construct()
	{
		// $this->token = $this->readToken();
		// $this->email = config('eskizsms-laravel.email');
		// $this->password = config('eskizsms-laravel.password');
		dd($this->getToken());
	}

	public function readToken()
	{
		$currentTime = Carbon::now();
		$token = '';

		$token = $this->getToken();

		if(File::exists(config_path('eskizsms.json'))) {
			$config_file = File::get(config_path('eskizsms.json'));
			$conf = json_decode($config_file, true);
			// need to refresh token every 30 days
			if ($currentTime->diffInDays($conf['created']) < 27) {
				$token = $conf['token'];
			} else {
				$token = $this->updateToken();
			}
		} else {
			$token = $this->getToken();
		}

		return $token;
	}

	public function getToken()
	{
		$response = Http::post('https://notify.eskiz.uz/api/auth/login', [
			'email' => $this->email,
			'password' => $this->password,
		]);

		//$this->saveToken($response);

		return $response;
	}

	public function updateToken()
	{
		
	}

	public function deleteToken()
	{
		
	}

	public function saveToken($token)
	{
		$data = json_encode([
			'created' => date('Y-m-d H:i:s'),
			'token' => $token
		]);

		$res = File::put(config_path('eskizsms.json'), $data);
	}
}
