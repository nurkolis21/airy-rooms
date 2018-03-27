<?php
namespace dwisiswant0\AiryRoomXtrap;
define("OS", strtolower(PHP_OS));

/**
* AiryRoomXtrap Coupon Codes
* Auto eXtrap & Check AiryRooms Coupon Codes
*
* @category   Library
* @package    dwisiswant0/AiryRoomXtrap
* @author     dw1 <iam@dw1.co>
* @license    https://opensource.org/licenses/MIT  MIT License
* @link       https://github.com/dwisiswant0/AiryRoomXtrap
*/

class main {
	const BASE = "https://api.airyrooms.com";
	const API = "/api/v2";

	public function __construct($verbose = true) {
		$this->VERBOSE = $verbose;
		$this->LOG = "log/coupon_codes-" . date('Ymd') . ".log";
	}

	private function call($data, $endpoint) {
		$ch = curl_init($this::BASE . $this::API . $endpoint);
		curl_setopt_array($ch, array(
			CURLOPT_POST => TRUE,
			CURLOPT_RETURNTRANSFER => TRUE,
			CURLOPT_HTTPHEADER => array(
				"User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10; rv:33.0) Gecko/20100101 Firefox/33.0",
				"Content-Type: application/json",
				"Referer: " . $this::BASE . "/reservation/form",
				"Origin: " . $this::BASE
			),
			CURLOPT_POSTFIELDS => $data
		));
		$response = curl_exec($ch);
		if ($response === false) throw new \Exception(curl_error($ch));
		else return json_decode($response, 1)['data'];
    }

	private function generateIP() {
		return (string) mt_rand(0,255) . "." . mt_rand(0,255) . "." . mt_rand(0,255) . "." . mt_rand(0,255);
	}

	public function generateCoupon($prefix, $digit = 5) {
		$chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charLen = strlen($chars);
		$randStr = '';
		for ($i = 0; $i < $digit; $i++) $randStr .= $chars[rand(0, $charLen - 1)];
		return (string) $prefix . strtoupper($randStr);
	}

    public function checkCoupon($code) {
    	$data = "{\"data\":{\"code\":\"" . $code . "\",\"grossAmount\":{\"currency\":\"IDR\",\"value\":589600},\"customerDetail\":{\"name\":\"Undefined Undefined\",\"email\":\"" . strtolower($code) . "@gmail.com\",\"phone\":\"08" . mt_rand(100000000, 999999999) . "\"},\"orderDetail\":{\"geoId\":103014,\"propertyId\":\"10000041\",\"totalPrice\":{\"currency\":\"IDR\",\"value\":589600},\"checkInMillis\":1522" . mt_rand(100000000, 999999999) . ",\"checkOutMillis\":1522" . mt_rand(100000000, 999999999) . ",\"roomPerNight\":1,\"totalNight\":1,\"roomNight\":1,\"checkIn\":{\"month\":12,\"day\":29,\"year\":2018},\"checkOut\":{\"month\":12,\"day\":30,\"year\":2018}}},\"context\":{\"application\":\"airyrooms\",\"applicationInterface\":\"desktop-web\",\"client\":\"airy\",\"apiKey\":\"airy.airyrooms.ieneeSh4\",\"apiToken\":\"airy.airyrooms.token.tahs1thee1hae2Do\",\"version\":\"v1\",\"headers\":{}},\"trackingContext\":{\"ipAddress\":\"" . $this->generateIP() . "\"}}";

		$check = $this->call($data, "/coupon/check");
		if ($check['message'] == "RULES_SERVICE_REJECTED") {
			$this->saveCoupon($code);
			$check['message'] = (string) "LIVE";
			$check['status'] = (bool) true;
		} elseif ($check['message'] == "COUPON_CODE_NOT_FOUND") {
			$check['message'] = (string) "DIED";
			$check['status'] = (bool) false;
		}

		$output = (OS == "linux" ? "\033[" . ($check['status'] == true ? $this->color()['green'] : $this->color()['red']) . "m" : null) . "[" . date("H:i:s") . "] " . $check['message'] . "! " . $code . (OS == "linux" ? "\033[0m" : null) . "\n";
		if ($this->VERBOSE === true) {
			if ($check['status'] === true) {
				return $output;
			} else return false;
		} else return $output;
	}

	private function color() {
		return array(
			"black" => "30",
			"blue" => "34",
			"green" => "32",
			"cyan" => "36",
			"red" => "31",
			"purple" => "35",
			"brown" => "33",
		);
	}

	private function saveCoupon($data) {
		fwrite(fopen($this->LOG, "a+"), "[" . date("Y/m/d H:i:s") . "] " . $data . PHP_EOL);
		fclose(fopen($this->LOG, "a+"));
	}
}