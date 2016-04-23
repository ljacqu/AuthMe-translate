
<?php

class MainPageCtrl {

  private $ipRegister;

  function __construct(IpRegister $ipRegister) {
    $this->ipRegister = $ipRegister;
  }

  function run() {
    $codes = array_map(function ($code) {
      return ['code' => $code];
    }, self::getAvailableLanguages());

    $translations = $this->getTranslationsTag();
    $tags = [
      'codes' => $codes,
      'translations' => $translations
    ];

    Template::displayTemplate('./controller/tpl/main_page.html', $tags);
  }

  static function getAvailableLanguages($folderPrefix = null) {
    $dir = ($folderPrefix ?: '') . IMPORT_DIRECTORY;
    $codes = [];
    if (is_dir($dir)) {
      if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
          if (self::isMessagesFile($dir, $file)) {
            $codes[] = self::extractLanguageCode($file);
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

  private function getTranslationsTag() {
    $translations = $this->ipRegister->getTranslationOfIp($_SERVER['REMOTE_ADDR']);
    return array_map(function ($secretKey, $langCode) {
      return ['secret_key' => $secretKey, 'language_code' => $langCode];
    }, $translations, array_keys($translations));
  }

  private static function isMessagesFile($dir, $file) {
    return is_file($dir . $file) && preg_match('~^messages_[a-z]{1,4}\\.json~', $file);
  }

  private static function extractLanguageCode($file) {
    return preg_replace('~^(messages_(.*?)\\.json)$~', '\\2', $file);
  }

}
