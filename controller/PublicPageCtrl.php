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
      $this->showHtmlPage($data);
    }

  }

  private function showHtmlPage($data) {
    $tags = [
      'language_code' => $data['code'],
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

}