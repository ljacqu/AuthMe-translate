<?php

class IpRegister {

  private $data;

  function __construct() {
    // Contains $ip_register
    require USER_DATA_DIRECTORY . 'ip_register.php';
    $this->data = $ip_register;
  }

  function getTranslationOfIp($ip) {
    return $this->safeIpRetrieve($ip);
  }

  function doesIpHaveTranslation($ip, $language) {
    return isset($this->safeIpRetrieve($ip)[$language]);
  }

  function addTranslation($ip, $language, $key) {
    if (!isset($this->data[$ip])) {
      $this->data[$ip] = [];
    }
    $this->data[$ip][$language] = $key;
    $this->saveIpRegister();
  }

  function deleteTranslation($ip, $language, $secretKey) {
    if (isset($this->data[$ip]) && isset($this->data[$ip][$language]) && $this->data[$ip][$language] === $secretKey) {
      $this->deleteLangForIp($ip, $language);
    } else {
      foreach ($this->data as $curIp => $entries) {
        if (isset($entries[$language]) && $entries[$language] === $secretKey) {
          $this->deleteLangForIp($curIp, $language);
          break;
        }
      }
    }
  }

  private function deleteLangForIp($ip, $language) {
    unset($this->data[$ip][$language]);
    if (empty($this->data[$ip])) {
      unset($this->data[$ip]);
    }
    $this->saveIpRegister();
  }

  private function saveIpRegister() {
    $fh = fopen(USER_DATA_DIRECTORY . 'ip_register.php', 'w');
    if ($fh) {
      fwrite($fh, '<?php $ip_register = ' . var_export($this->data, true) . ';');
      fclose($fh);
    } else {
      throw new Exception('Could not write to IP register');
    }
  }

  private function safeIpRetrieve($ip) {
    return isset($this->data[$ip])
      ? $this->data[$ip]
      : [];
  }

}