<?php
namespace Azamatx\EskizsmsLaravel;

use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use File;

class EskizsmsLaravel
{
	private static $token;
	private static $from;
	private $email;
	private $password;

    public function __construct()
	{
		$this->email = config('eskizsms-laravel.email');
		$this->password = config('eskizsms-laravel.password');
		self::$from = config('eskizsms-laravel.from');
		self::$token = $this->readToken();
	}

	public function readToken()
	{
		$currentTime = Carbon::now();
		$token = '';

		if(File::exists(config_path('eskizsms.json'))) {
			$config_file = File::get(config_path('eskizsms.json'));
			$conf = json_decode($config_file, true);
			// need to refresh token every 30 days
			if ($currentTime->diffInDays($conf['created']) < 27) {
				$token = $conf['token'];
			} else {
				$token = $this->updateToken($conf['token']);
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
		
		if($response->successful()) {
			$data = $response->json();
			if(!empty($data['data']) && !empty($data['data']['token'])) {
				$this->saveToken($data['data']['token']);
				return $data['data']['token'];
			}
		} else {
			throw new \ErrorException($response->body());
		}
	}

	public function updateToken($token)
	{
		$response = Http::withHeaders([
			'Authorization' => 'Bearer '. $token,
		])->patch('https://notify.eskiz.uz/api/auth/refresh');

		if($response->successful()) {
			$this->saveToken($token);
		} else {
			throw new \ErrorException($response->body());
		}

		return $token;
	}

	public function deleteToken()
	{
		// delete eskiz token: DEL request https://notify.eskiz.uz/api/auth/invalidate (with bearer token)
	}

	public function saveToken($token)
	{
		$data = json_encode([
			'created' => date('Y-m-d H:i:s'),
			'token' => $token
		]);

		$res = File::put(config_path('eskizsms.json'), $data);
	}

	public function generatePin()
	{
		return rand(1000,9999);
	}

	public function savePinToSession($pin)
	{
		self::destroyPinSession();
		session(['eskiz_sms_pin' => $pin]);
	}

	public static function destroyPinSession()
	{
		session()->forget('eskiz_sms_pin');
		session()->flush();
	}

	public static function getPinFromSession()
	{
		return session('eskiz_sms_pin');
	}

	public static function sanitizePhoneNumber($mobile_phone)
	{
		return preg_replace('/[^0-9]/', '', $mobile_phone);
	}

	public function sendSms($token, $mobile_phone, $message, $from, $callback_url)
	{
		$response = Http::withHeaders([
			'Authorization' => 'Bearer '. $token,
		])->post('https://notify.eskiz.uz/api/message/sms/send', [
			'mobile_phone' => $mobile_phone,
			'message' => $message,
			'from' => $from,
			'callback_url' => $callback_url,
		]);

		return $response->successful();
	}

	public static function sendPin($phone_number)
	{
		// sanitize phone number
		$mobile_phone = self::sanitizePhoneNumber($phone_number);

		// clear old session pins
		self::destroyPinSession();

		// get new pin (integer, 4 digits)
		$pin = self::generatePin();

		// if you are using nick names, please change this to your nick name
		$from = self::$from;

		// callback url to receive POST sms delivery status report
		$callback_url = config('app.url');

		$message = "Ushbu kodni hech kimga bermang! Sizning " . $callback_url . " uchun SMS pin kodingiz: " . $pin;

		$result = self::sendSms(self::$token, $mobile_phone, $message, $from, $callback_url);

		if(true === $result) {
			self::savePinToSession($pin);
		}

		return $result;
	}

	public static function validatePin($user_input_pin)
	{
		// check user entered pin
		$stored_pin = self::getPinFromSession();
		if(!empty($stored_pin) && intval($stored_pin) === intval($user_input_pin)) {
			return true;
		}

		return false;
	}
}
