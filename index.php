<?php
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');
require 'constants.php';

require './controller/CreateCopyCtrl.php';
require './controller/EditPageCtrl.php';
require './controller/MainPageCtrl.php';
require './controller/PublicPageCtrl.php';
require './controller/SaveTranslationCtrl.php';
require './controller/Template.php';


$secret_id = false;
if (isset($_POST['create'])) {
try {
    $ctrl = new CreateCopyCtrl();
    $result = $ctrl->run($_POST['create']);
    header('Location:index.php?' . $result);
    exit;
  } catch (Exception $e) {
    echo '<h1>Error</h1><div class="error">' . $e->getMessage() . '</div>';
  }
} else if (isset($_POST['update_file'])) {
  try {
    $secret_id = (new SaveTranslationCtrl())->run();

  } catch (Exception $e) {
    die ('<h1>Error</h1><div class="error">' . $e->getMessage() . '</div>');
  }
}

$action = '';
if (isset($_GET['p'])) {
  $action = 'public';
  if (isset($_GET['json'])) {
    (new PublicPageCtrl())->run($_GET['p'], true);
  }
} else if (isset($_GET['l'])) {
  $action = 'public';
} else if (!empty($_SERVER['QUERY_STRING'])) {
  if (EditPageCtrl::validateSecretCode($_SERVER['QUERY_STRING'])) {
    $action = 'edit';
  } else {
    header('Location:index.php');
    exit;
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>AuthMe Translation</title>
  <link rel="stylesheet" type="text/css" href="style.css" />
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js" type="text/javascript"></script>
  <script src="translation-verification.js" type="text/javascript"></script>
  <script src="language-loader.js" type="text/javascript"></script>
  <script src="color-preview.js" type="text/javascript"></script>
</head>
<body>

<?php
try {
  if ($action === 'public') {
    $ctrl = new PublicPageCtrl();
    if (isset($_GET['l'])) {
      $ctrl->showLanguage($_GET['l']);
    } else {
      $ctrl->run($_GET['p'], false);
    }
  } else if ($action === 'edit') {
    (new EditPageCtrl())->run($secret_id);
  } else {
    (new MainPageCtrl())->run();
  }
} catch (Exception $e) {
  echo '<div class="error">
    <b>Error: </b>' . $e->getMessage() . '
    <br /><a href="index.php">Main page</a></div>';
}
?>

</body>
</html>
