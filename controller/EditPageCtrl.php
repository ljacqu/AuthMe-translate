<?php

class EditPageCtrl {

  function run($id = false) {
    $code = $id ? $id : $_SERVER['QUERY_STRING'];
    // Although this is validated in index.php already, let's do a superficial check
    if (!preg_match('~^\\w+$~', $code)) {
      die('Invalid secret code format');
    }

    // Contains $data
    require USER_DATA_DIRECTORY . $code . '.php';

    $tags = [
      'performed_save' => ($id !== false),
      'secret_code' => $code,
      'public_code' => $data['meta']['publicKey'],
      'language_code' => $data['code'],
      'messages' => array_map(function ($message) {
        return array_map('htmlspecialchars', $message);
      }, $data['messages'])
    ];

    Template::displayTemplate('./controller/tpl/edit_page.html', $tags);
  }

  /**
   * Validates safely whether the argument is a valid secret code.
   *
   * @param $code mixed the value to verify
   * @return string valid secret code ID, or null if invalid or non-existent
   */
  static function validateSecretCode($code) {
    if (!empty($code) && is_scalar($code) && preg_match('~^\\w{' . SECRET_KEY_LENGTH . '}+$~', $code)
      && file_exists(USER_DATA_DIRECTORY . $code . '.php')) {
      return $code;
    }
    return null;
  }
}