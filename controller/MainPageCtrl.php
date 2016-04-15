
<?php

class MainPageCtrl {

  function run() {
    $codes = array_map(function ($code) {
      return ['code' => $code];
    }, $this->getAvailableLanguages());

    Template::displayTemplate('./controller/tpl/main_page.html', ['codes' => $codes]);
  }

  private function getAvailableLanguages() {
    $dir = IMPORT_DIRECTORY;
    $codes = [];
    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
          if ($this->isMessagesFile($file)) {
            $codes[] = $this->extractLanguageCode($file);
          }
        }
        closedir($dh);
      } else {
        die('Problem reading import directory');
      }
    } else {
      die('Problem getting import directory');
    }
    asort($codes);
    return $codes;
  }

  function isMessagesFile($file) {
    return is_file(IMPORT_DIRECTORY . $file) && preg_match('~^messages_[a-z]{1,4}\\.json~', $file);
  }

  function extractLanguageCode($file) {
    return preg_replace('~^(messages_(.*?)\\.json)$~', '\\2', $file);
  }

}
