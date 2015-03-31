<html>
<head>
	<meta charset="utf-8">
	<title>AccessLog</title>
	<!-- Font Google Open Sans -->
	<link  href='https://fonts.googleapis.com/css?family=Open+Sans:400' rel='stylesheet' type='text/css'>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js" type="text/javascript"></script>
	<script src="graph.js" type="text/javascript"></script>
	<style>
		.node {
		  cursor: pointer;
		}

		.node circle {
		  fill: #fff;
		  stroke: steelblue;
		  stroke-width: 1.5px;
		}

		.node text {
		  font: 14px "Open Sans";
		}

		.link {
		  fill: none;
		  stroke: #ccc;
		  stroke-width: 1.5px;
		}
	</style>
</head>

<?php 
  ob_start();
  system("./main.py", $status);
  $output1 = json_decode( ob_get_clean() , true);
  $json_string = json_encode($output1, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>

<body>
	
	<script type="text/javascript">
		prepare_graph()
	</script>

	<?
		echo "<pre>".$json_string."</pre>";
	?>
	
</body>

<html>