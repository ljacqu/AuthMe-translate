<h1>AuthMe Translation</h1>
<p>
  This page allows you to edit and export translations of
  <a href="https://github.com/AuthMe-Team/AuthMeReloaded">AuthMe</a> messages.
</p>

<p>Please select the language you want to work on:</p>
<form method="post">
  <select name="create">
<?php
$codes = get_available_languages();
asort($codes);

foreach ($codes as $code) {
  echo '<option value="' . $code . '">Language: ' . $code . '</option>' . "\n";
}
?>
  </select>
  <input type="submit" value="Work on translation" />
</form>

This will create a personal copy of the translation file that you can edit. Once you're satisfied
with the result, you can create an issue on the
<a href="https://github.com/Xephi/AuthMeReloaded/issues">AuthMe issue tracker</a> and we will merge the changes
into the codebase.

<?php
// -------------------
// Functions

function get_available_languages() {
  $dir = IMPORT_DIRECTORY;
  $codes = [];
  if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
      while (($file = readdir($dh)) !== false) {
        if (is_messages_file($file)) {
          $codes[] = get_language_code($file);
        }
      }
      closedir($dh);
    } else {
      die('Problem reading import directory');
    }
  } else {
    die('Problem getting import directory');
  }
  return $codes;
}

function is_messages_file($file) {
  return is_file(IMPORT_DIRECTORY . $file) && preg_match('~^messages_[a-z]{1,4}\\.json~', $file);
}

function get_language_code($file) {
  return preg_replace('~^(messages_(.*?)\\.json)$~', '\\2', $file);
}