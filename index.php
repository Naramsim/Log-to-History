<html>
<head>

  <title>AccessLog</title>

  <!-- Font Google Open Sans-->
  <link href='https://fonts.googleapis.com/css?family=Open+Sans:400' rel='stylesheet' type='text/css'>
  
</head>

<?php 
  ob_start();
  system("./main.py", $status);
  $output = ob_get_clean();
  $output1 = json_decode($output, true);
  $json_string = json_encode($output1, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
  echo "<pre>".$json_string."</pre>";
?>

<html>