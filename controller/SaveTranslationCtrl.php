<?php

class SaveTranslationCtrl {

  function run() {
    $secretId = EditPageCtrl::validateSecretCode($this->getScalarFromPostOrThrow('secret_code'));
    $file = USER_DATA_DIRECTORY . $secretId . '.php';
    // contains $data
    require $file;
    $data = $this->updateDataStructureWithNewMessages($data);
    $this->exportDataToFile($file, $data);
    return $secretId;
  }

  private function updateDataStructureWithNewMessages($data) {
    foreach ($data['messages'] as &$entry) {
      $key = $entry['key'];
      $inputMessage = $this->getScalarFromPostOrThrow($key);
      $entry['translatedMessage'] = $inputMessage;
    }
    $data['meta']['modified'] = time();
    return $data;
  }

  private function exportDataToFile($file, $data) {
    $fh = fopen($file, 'w');
    if ($fh) {
      fwrite($fh, '<?php $data = ' . var_export($data, true) . ';');
      fclose($fh);
    } else {
      throw new Exception('Could not write to file');
    }
  }

  private function getScalarFromPostOrThrow($index) {
    $value = filter_input(INPUT_POST, $index, FILTER_UNSAFE_RAW, FILTER_REQUIRE_SCALAR);
    if ($value === false || $value === null) {
      throw new Exception("Required entry '" . htmlspecialchars($index) . "' not present or invalid");
    }
    return $value;
  }

}