<?php
require 'constants.php';

if (isset($_POST['code']) && isset($_POST['file']) && isset($_POST['language'])) {
  // Validate input
  $code = $_POST['code'];
  if (!is_scalar($code) || !preg_match('~^[0-9A-F]{12}$~', $code)) {
    die('Invalid update code');
  }

  $file = $_POST['file'];
  if (!is_scalar($_POST['file'])) {
    die('Invalid language file parameter');
  }

  $language = $_POST['language'];
  if (!is_scalar($language) || !preg_match('~^[a-z]{1,4}$~', $language)) {
    die('Invalid language code');
  }

  // Check update code
  require './admin/update_codes.php'; // Contains $update_codes
  if (!isset($update_codes[$code]) || time() > $update_codes[$code]) {
    die('Invalid or expired update code');
  }

  // Check that $file is valid JSON
  $json_data = json_decode($file);
  if (!$json_data || !is_object($json_data) || !isset($json_data->messages) || !is_array($json_data->messages)
    || !isset($json_data->code)) {
    die('Invalid file data');
  } else if ($json_data->code !== $language) {
    die('Supplied language does not match JSON data');
  }

  // Write file
  $file_to_write = IMPORT_DIRECTORY . 'messages_' . $language . '.json';
  $fh = fopen($file_to_write, 'w');
  if ($fh) {
    fwrite($fh, $file);
    fclose($fh);
  } else {
    die('Could not open file for language "' . $language . '"');
  }

  echo 'Successfully updated ' . $language;
} else {
  die('Incomplete data');
}