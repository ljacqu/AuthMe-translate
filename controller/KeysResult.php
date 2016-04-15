<?php

class KeysResult {

  var $publicKey;
  var $privateKey;

  function __construct($publicKey, $privateKey) {
    $this->publicKey = $publicKey;
    $this->privateKey = $privateKey;
  }

  function __set($name, $value) {
    throw new Exception('Immutable class');
  }

}