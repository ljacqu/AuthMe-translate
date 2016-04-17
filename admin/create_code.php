<?php

if (isset($_POST['create'])) {
  // Contains $update_codes
  require 'update_codes.php';

  // Remove all codes that have expired
  $cur_time = time();
  foreach ($update_codes as $code => $expiration) {
    if ($expiration < $cur_time) {
      unset($update_codes[$code]);
    }
  }

  // Add new code + save
  $rand_bytes = openssl_random_pseudo_bytes(6);
  $code = strtoupper(bin2hex($rand_bytes));
  $update_codes[$code] = time() + 15 * 60; // valid for 15 minutes

  $fh = fopen('update_codes.php', 'w');
  fwrite($fh, '<?php $update_codes = ' . var_export($update_codes, true) . ';');
  fclose($fh);
}

header('Location:index.php');
exit;