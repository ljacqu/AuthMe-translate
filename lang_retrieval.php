<?php
header('Content-Type: application/json');
require 'constants.php';

if (isset($_GET['list'])) {
  require './controller/MainPageCtrl.php';
  $codes = MainPageCtrl::getAvailableLanguages();
  echo json_encode(['codes' => $codes]);
}

else if (isset($_GET['lang'])) {
  $lang = filter_input(INPUT_GET, 'lang', FILTER_UNSAFE_RAW, FILTER_REQUIRE_SCALAR);
  if (!$lang || !preg_match('~^[a-z]{1,4}$~', $lang) || !file_exists(IMPORT_DIRECTORY . "messages_$lang.json")) {
    die(json_encode([]));
  }

  $data = json_decode(file_get_contents(IMPORT_DIRECTORY . "messages_$lang.json"));
  $message_array = [];
  foreach ($data->messages as $message) {
    $message_array[$message->key] = $message->translatedMessage;
  }
  echo json_encode($message_array);

}