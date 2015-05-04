<html>
<!--
In this webpage is presented a tree graph which first level nodes represents every IP who has been in a specific website.
we can understand who visited a specific website by parsing the access.log made by the webserver(apache2, nginx, ...) of that specific website
For parsing the log PHP invokes a script made in Python that creates a file that will be interpreted by some Javascript code.
The children of a first-level node are the pages that a user has visited coming directly or from another site(by clicking a link), the children of these nodes are the pages visited coming from parent(by clicking a link), and so on

In this case the script is called "main.py", and the Javascript code is in "tree_graph.js"
-->
<head>
	<meta charset="utf-8">
	<title>AccessLog</title>
	<!-- Font Google Open Sans -->
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400' rel='stylesheet' type='text/css'>
	<link href='css/index.css' rel='stylesheet' type='text/css'>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js" type="text/javascript"></script>
	<script src="https://mtgfiddle.me/tirocinio/pezze/d3tip.js" type="text/javascript"></script>
	<script src="js/tree_graph.js" type="text/javascript"></script>
</head>

<?php 
  ob_start(); //start an output buffer
  system("./main.py", $status); //executes the script
  $output1 = json_decode( ob_get_clean() , true); //close and store in a $var the output
  $json_string = json_encode($output1, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>

<body>
	
	<script type="text/javascript">
		prepare_graph();
		//prepare_chart();
	</script>

	<?
		//echo "<pre>".$json_string."</pre>";
	?>
	
</body>

<html>