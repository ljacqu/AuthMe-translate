<?php
/**
 * Manages IP address access: IP addresses present in the list may update
 * the JSON export files.
 */
error_reporting(E_ALL);
header('Content-Type: application/json');

if (isset($_POST['add'])) {
  $ip = $_POST['add'];
  validate_ip_address($ip);

  // Contains $allowed_ips
  require 'allowed_ips.dat.php';
  $allowed_ips = remove_expired_ips($allowed_ips);

  // Add new ip
  $allowed_ips[$ip] = time() + 3600 * 24 * 30; // valid for 30 days
  save_data($allowed_ips);
  echo json_encode(['error' => '', 'success' => true]);
}

else if (isset($_POST['remove'])) {
  $ip = $_POST['remove'];
  validate_ip_address($ip);

  // Contains $allowed_ips
  require 'allowed_ips.dat.php';
  $allowed_ips = remove_expired_ips($allowed_ips);

  // Remove IP
  unset($allowed_ips[$ip]);
  save_data($allowed_ips);
  echo json_encode(['error' => '', 'success' => true]);
}

// ---------------
// Functions
// ---------------
function remove_expired_ips($data) {
  $now = time();
  return array_filter($data, function ($expiration) use ($now) {
    return $now < $expiration;
  });
}

function validate_ip_address($ip) {
  if (!is_scalar($ip) || !preg_match('~^(\\d{1,3}\\.){3}\\d{1,3}$~', $ip)) {
    exit_with_error('Invalid IP address');
  }
}

function save_data($data) {
  $fh = fopen('allowed_ips.dat.php', 'w');
  fwrite($fh, '<?php $allowed_ips = ' . var_export($data, true) . ';');
  fclose($fh);
}

function exit_with_error($error) {
  die(json_encode(['error' => $error, 'success' => false]));
}
