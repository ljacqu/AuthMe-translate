<?php

class CreateCopyCtrl {

  private $ipRegister;

  function __construct(IpRegister $ipRegister) {
    $this->ipRegister = $ipRegister;
  }

  function run($languageCode) {
    $this->validateLanguageCode($languageCode);
    if ($this->ipRegister->doesIpHaveTranslation($_SERVER['REMOTE_ADDR'], $languageCode)) {
      throw new Exception('You already have a translation for ' . $languageCode
        . '. Delete it first to create a new one (link on main page).');
    }
    $secretKey = $this->randomString(SECRET_KEY_LENGTH);
    $publicKey = $this->randomString(PUBLIC_KEY_LENGTH);

    $this->writeData($languageCode, $publicKey, USER_DATA_DIRECTORY . $secretKey . '.php');
    $fh = fopen(USER_DATA_DIRECTORY . $publicKey . '.key', 'w');
    if ($fh) {
      fwrite($fh, $secretKey);
      fclose($fh);
    } else {
      throw new Exception('Error writing public key file');
    }

    $this->ipRegister->addTranslation($_SERVER['REMOTE_ADDR'], $languageCode, $secretKey);
    return $secretKey;
  }

  private function writeData($langCode, $publicKey, $destinationFile) {
    $data = (array) json_decode(file_get_contents(IMPORT_DIRECTORY . 'messages_' . $langCode . '.json'));
    foreach ($data['messages'] as $key => $obj) {
      $data['messages'][$key] = (array) $obj;
    }
    $data['meta'] = [
      'publicKey' => $publicKey,
      'created' => time(),
      'modified' => time()
    ];

    $fh = fopen($destinationFile, 'w');
    if ($fh) {
      fwrite($fh, '<?php $data = ' . var_export($data, true) . ';');
      fclose($fh);
    } else {
      throw new Exception('Cannot write to data file');
    }
  }

  private function validateLanguageCode($languageCode) {
    if (!is_scalar($languageCode) || !preg_match('~^[a-z]{1,4}$~', $languageCode)) {
      throw new Exception('Invalid language ID');
    }
    if (!file_exists(IMPORT_DIRECTORY . 'messages_' . $languageCode . '.json')) {
      throw new Exception('The language does not exist');
    }
  }

  private function randomString($len) {
    // With bin2hex we will get double as much, so make a bigger half and then strip the result if necessary
    $length = ceil($len / 2);
    $randBytes = openssl_random_pseudo_bytes($length);
    return substr(bin2hex($randBytes), 0, $len);
  }

}