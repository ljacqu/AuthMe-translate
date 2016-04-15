<?php

$code = $_SERVER['QUERY_STRING'];
// Although this is validated in index.php already, let's do a superficial check
if (!preg_match('~^\\w+$~', $code)) {
  die('Invalid secret code format');
}

// Contains $data
require USER_DATA_DIRECTORY . $code . '.php';


?>
<h1>Edit Language</h1>
<p>
  <a onclick="hideValidFields()" id="hideValidFieldsLink">Show only fields with errors</a>
  <a onclick="showAllFields()" id="showAllFieldsLink" style="display: none">Show all fields</a>
</p>

<form method="post">
  <table class="edit">
    <tr>
      <th style="width: 200px">Default (en)</th>
      <th>Translated</th>
    </tr>
<?php
foreach ($data['messages'] as $entry) {
  $entry['tags'] = implode(',', $entry['tags']);
  $entry = array_map('htmlspecialchars', $entry);

  echo <<<HTML
  <tr class="key"><td colspan="2">{$entry['key']}</td></tr>
  <tr>
    <td>{$entry['defaultMessage']}</td>
    <td><input type="text"
               value="{$entry['translatedMessage']}"
               name="{$entry['key']}"
               data-tags="{$entry['tags']}"
               onkeyup="checkField(this)" />
        <span class="error"></span>
    </td>
  </tr>
HTML;
}
?>
    <tr>
      <td colspan="2">
        <input type="submit" value=" Save " name="update_file" />
      </td>
    </tr>
  </table>
</form>
