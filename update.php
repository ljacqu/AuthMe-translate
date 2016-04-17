<?php
require 'constants.php';

if (isset($_POST['file']) && isset($_POST['language'])) {
  // Check access
  require './admin/allowed_ips.php'; // Contains $allowed_ips
  $user_ip = $_SERVER['REMOTE_ADDR'];
  if (!isset($allowed_ips[$user_ip]) || time() > $allowed_ips[$user_ip]) {
    $input_code = filter_input(INPUT_POST, 'language', FILTER_UNSAFE_RAW,
      FILTER_REQUIRE_SCALAR | FILTER_FLAG_STRIP_LOW) ?: '';
    error_log('IP address "' . $_SERVER['REMOTE_ADDR'] . '" tried to update language "' . $input_code . '"');
    http_response_code(403);
    die('Unauthorized IP address');
  }

  $file = $_POST['file'];
  if (!is_scalar($_POST['file'])) {
    http_response_code(400);
    die('Invalid language file parameter');
  }

  $language = $_POST['language'];
  if (!is_scalar($language) || !preg_match('~^[a-z]{1,4}$~', $language)) {
    http_response_code(400);
    die('Invalid language code');
  }

  // Check that $file is valid JSON
  $json_data = json_decode($file);
  if (!$json_data || !is_object($json_data) || !isset($json_data->messages) || !is_array($json_data->messages)
    || !isset($json_data->code)) {
    http_response_code(400);
    die('Invalid file data');
  } else if ($json_data->code !== $language) {
    http_response_code(400);
    die('Supplied language does not match JSON data');
  }

  // Write file
  $file_to_write = IMPORT_DIRECTORY . 'messages_' . $language . '.json';
  $fh = fopen($file_to_write, 'w');
  if ($fh) {
    fwrite($fh, $file);
    fclose($fh);
  } else {
    http_response_code(500);
    die('Could not open file for language "' . $language . '"');
  }

  echo 'Successfully updated ' . $language;
} else {
  http_response_code(400);
  die('Incomplete data');
}