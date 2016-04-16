<?php

class PublicPageCtrl {

  function run($publicCode, $outputJson) {
    $this->validatePublicCode($publicCode);
    $dataFile = USER_DATA_DIRECTORY . file_get_contents(USER_DATA_DIRECTORY . $publicCode . '.key') . '.php';
    // Contains $data
    require $dataFile;
    if ($outputJson) {
      $this->showJson($data);
    } else {
      $this->showHtmlPage($data, true);
    }
  }

  function showLanguage($langCode) {
    $this->validateLanguageCode($langCode);
    $data = (array) json_decode(file_get_contents(IMPORT_DIRECTORY . 'messages_' . $langCode . '.json'));
    foreach ($data['messages'] as $key => $obj) {
      $data['messages'][$key] = (array) $obj;
    }
    $data['meta'] = ['created' => 0, 'modified' => 0];
    $this->showHtmlPage($data, false);
  }

  private function showHtmlPage($data, $isTranslation) {
    $tags = [
      'language_code' => $data['code'],
      'is_translation' => $isTranslation,
      'is_default' => !$isTranslation,
      'created' => date('Y-m-d H:i', $data['meta']['created']),
      'modified' => date('Y-m-d H:i', $data['meta']['modified']),
      'messages' => array_map(function ($message) {
        return array_map('htmlspecialchars', $message);
      }, $data['messages'])
    ];
    Template::displayTemplate('./controller/tpl/public_page.html', $tags);
  }

  private function showJson($data) {
    header('Content-Type: application/json');
    unset($data['meta']);
    $data['messages'] = array_map(function ($msg) {
      $msg['defaultMessage'] = '';
      return $msg;
    }, $data['messages']);
    echo json_encode($data);
    exit;
  }

  private function validatePublicCode($publicCode) {
    if (!is_scalar($publicCode) || !preg_match('~^\\w{' . PUBLIC_KEY_LENGTH . '}$~', $publicCode)
      || !file_exists(USER_DATA_DIRECTORY . $publicCode . '.key')) {
      throw new Exception('Invalid public code');
    }
  }

  private function validateLanguageCode($languageCode) {
    if (!is_scalar($languageCode) || !preg_match('~^[a-z]{1,4}$~', $languageCode)
      || !file_exists(IMPORT_DIRECTORY . 'messages_' . $languageCode . '.json')) {
      throw new Exception('Invalid language code');
    }
  }

}