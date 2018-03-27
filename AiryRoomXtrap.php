<?php
require_once "src/AiryRoomXtrap.class.php";
use \dwisiswant0\AiryRoomXtrap As AiryRoomXtrap;

include_once "src/AiryRoomXtrap.config.php";

echo <<<EOF

  AiryRoomXtrap Coupon Codes
  https://github.com/dwisiswant0/AiryRoomXtrap
  --
  (c) made by dw1, 2018


EOF;

$airyrooms = new AiryRoomXtrap\main($verbose);
while (true) {
	$random = $prefix[array_rand($prefix)];
	$coupon = $airyrooms->generateCoupon($random[0], $random[1]);
	$check = $airyrooms->checkCoupon($coupon);
	echo $check;
}