<?php

// Taken from https://github.com/ljacqu/connectedshows
class Template {

  private function __construct() {
  }

  static function displayTemplate($file, array $tags) {
    if (!file_exists($file)) {
      throw new Exception('Template file "' . htmlspecialchars($file) . '" does not exist');
    }
    $template = file_get_contents($file);
    echo self::prepareTemplate($template, $tags);
  }

  private static function prepareTemplate($template, array $tags) {
    foreach ($tags as $name => $value) {
      $template = self::handleConditionalTag($template, $name, $value);
      $template = is_array($value)
        ? self::handleRepetitionTag($template, $name, $value)
        : str_replace("{{$name}}", $value, $template);
    }
    return $template;
  }

  private static function handleConditionalTag($template, $tagName, $value) {
    if (strpos($template, "[$tagName]") !== false) {
      $innerReplacement = $value ? '\\2' : '';
      return preg_replace("~(\\[{$tagName}](.*?)\\[/{$tagName}])~s", $innerReplacement, $template);
    }
    return $template;
  }

  private static function handleRepetitionTag($template, $tagName, array $value) {
    if (strpos($template, "[#$tagName]") !== false) {
      preg_match("~\\[#$tagName](.*?)\\[/#$tagName]~s", $template, $matches);
      if (!isset($matches[1])) {
        throw new Exception("Could not get inner text for repetition of $tagName."
          . ' (Is the closing tag missing?)');
      }
      $innerText = trim($matches[1]);
      $replacements = array_map(function ($values) use ($innerText) {
        return self::prepareTemplate($innerText, $values);
      }, $value);
      return preg_replace("~(\\[#$tagName].*?\\[/#$tagName])~s", implode("\n", $replacements), $template);
    }
    return $template;
  }

}