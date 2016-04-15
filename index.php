<?php
error_reporting(E_ALL);
define('IMPORT_DIRECTORY', './import/');
define('USER_DATA_DIRECTORY', './userdata/');
define('SECRET_KEY_LENGTH', 20);
define('PUBLIC_KEY_LENGTH',  9);

header('Content-Type: text/html; charset=utf-8');
if (isset($_POST['create'])) {
try {
    require './controller/CreateCopyCtrl.php';
    $ctrl = new CreateCopyCtrl();
    $result = $ctrl->run($_POST['create']);
    header('Location:index.php?' . $result);
    exit;
  } catch (Exception $e) {
    echo '<h1>Error</h1>';
    echo $e->getMessage();
  }
}

$action = '';
if (!isset($_GET['p']) && !empty($_SERVER['QUERY_STRING'])) {
  $potential_code = $_SERVER['QUERY_STRING'];
  if (preg_match('~^\\w{' . SECRET_KEY_LENGTH . '}+$~', $potential_code)
    && file_exists(USER_DATA_DIRECTORY . $potential_code . '.php')) {
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
  require './controller/public_page_ctrl.php';
} else if ($action === 'edit') {
  require './controller/edit_page_ctrl.php';
} else {
  require './controller/main_page_ctrl.php';
}
?>
</body>
</html>
