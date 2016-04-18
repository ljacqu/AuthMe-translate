<?php
error_reporting(E_ALL);

require '../controller/Template.php';
// Contains $ip_register
require '../userdata/ip_register.dat.php';

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

// Contains $allowed_ips
require 'allowed_ips.dat.php';
$current_time = time();
$codes = array_map(function ($expiration, $ip) use ($current_time) {
  $expiration_in_days = round(($expiration - $current_time) / 3600, 1);
  if ($expiration_in_days <= 0.0) {
    return ['ip' => false];
  }
  return ['ip' => $ip, 'expiration_days' => $expiration_in_days];
}, $allowed_ips, array_keys($allowed_ips));

Template::displayTemplate('main.html', ['register' => $register, 'accesses' => $codes]);