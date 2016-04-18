<?php
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8');
require 'constants.php';

require './controller/CreateCopyCtrl.php';
require './controller/DeleteCtrl.php';
require './controller/EditPageCtrl.php';
require './controller/IpRegister.php';
require './controller/MainPageCtrl.php';
require './controller/PublicPageCtrl.php';
require './controller/SaveTranslationCtrl.php';
require './controller/Template.php';


$ip_register = new IpRegister();
$secret_id = false;

if (isset($_POST['create'])) {
try {
    $ctrl = new CreateCopyCtrl($ip_register);
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
} else if (isset($_POST['delete'])) {
  try {
    (new DeleteCtrl($ip_register))->run(
      filter_input(INPUT_POST, 'secret_code', FILTER_UNSAFE_RAW, FILTER_REQUIRE_SCALAR),
      filter_input(INPUT_POST, 'lang_code', FILTER_UNSAFE_RAW, FILTER_REQUIRE_SCALAR));
  } catch (Exception $e) {
    echo '<h1>Error</h1><div class="error">' . $e->getMessage() . '</div>';
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

Template::displayTemplate('./controller/tpl/header.html', []);

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
    (new MainPageCtrl($ip_register))->run();
  }
} catch (Exception $e) {
  echo '<div class="error">
    <b>Error: </b>' . $e->getMessage() . '
    <br /><a href="index.php">Main page</a></div>';
}

Template::displayTemplate('./controller/tpl/footer.html', []);
