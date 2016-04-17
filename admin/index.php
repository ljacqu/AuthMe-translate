<?php
error_reporting(E_ALL);

require '../controller/Template.php';
// Contains $ip_register
require '../userdata/ip_register.php';


$register = [];
foreach ($ip_register as $ip => $entries) {
  $tag_entry = [
    'ip' => $ip,
    'translations' => []
  ];
  foreach ($entries as $lang => $key) {
    $tag_entry['translations'][] = [
      'lang' => $lang,
      'key' => $key
    ];
  }
  $register[] = $tag_entry;
}

// Contains $update_codes
require 'update_codes.php';
$current_time = time();
$codes = array_map(function ($expiration, $code) use ($current_time) {
  $expires_in_minutes = max(round(($expiration - $current_time) / 60, 1), 0.0);
  if ($expires_in_minutes === 0.0) {
    return ['code' => false];
  }
  return ['code' => $code, 'expiration_minutes' => $expires_in_minutes];
}, $update_codes, array_keys($update_codes));

Template::displayTemplate('index.html', ['register' => $register, 'codes' => $codes]);