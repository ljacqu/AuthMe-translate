<?php
error_reporting(E_ALL);
define('IMPORT_DIRECTORY', './import/');
define('USER_DATA_DIRECTORY', './userdata/');
define('SECRET_KEY_LENGTH', 20);
define('PUBLIC_KEY_LENGTH',  9);

header('Content-Type: text/html; charset=utf-8');
require './controller/CreateCopyCtrl.php';
require './controller/EditPageCtrl.php';
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
if (!isset($_GET['p']) && !empty($_SERVER['QUERY_STRING'])) {
  if (EditPageCtrl::validateSecretCode($_SERVER['QUERY_STRING'])) {
    $action = 'edit';
  } else {
    header('Location:index.php');
    exit;
  }
} else if (isset($_GET['p'])) {
  $action = 'public';
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
</head>
<body>

<?php
if ($action === 'public') {
  (new PublicPageCtrl())->run($_GET['p']);
} else if ($action === 'edit') {
  (new EditPageCtrl())->run($secret_id);
} else {
  require './controller/main_page_ctrl.php';
}
?>

</body>
</html>
