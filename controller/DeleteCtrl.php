<?php

class DeleteCtrl {

  private $ipRegister;

  function __construct(IpRegister $ipRegister) {
    $this->ipRegister = $ipRegister;
  }

  function run($secretKey, $langCode) {
    $secretKey = EditPageCtrl::validateSecretCode($secretKey);
    if ($secretKey === null) {
      throw new Exception('Unknown secret key');
    }

    // Contains $data
    require USER_DATA_DIRECTORY . $secretKey . '.php';
    if ($data['code'] !== $langCode) {
      throw new Exception('Language code mismatch - cannot delete without correct language code');
    }
    $this->ipRegister->deleteTranslation($_SERVER['REMOTE_ADDR'], $langCode, $secretKey);
    unlink(USER_DATA_DIRECTORY . $secretKey . '.php');
    unlink(USER_DATA_DIRECTORY . $data['meta']['publicKey'] . '.key');
  }

}