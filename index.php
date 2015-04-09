<html>
<head>
	<meta charset="utf-8">
	<title>AccessLog</title>
	<!-- Font Google Open Sans -->
	<link  href='https://fonts.googleapis.com/css?family=Open+Sans:400' rel='stylesheet' type='text/css'>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js" type="text/javascript"></script>
	<script src="https://mtgfiddle.me/tirocinio/pezze/d3tip.js" type="text/javascript"></script>
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

		.axis path,
		.axis line {
		  fill: none;
		  stroke: #000;
		  shape-rendering: crispEdges;
		}

		.bar {
		  fill: orange;
		}

		.bar:hover {
		  fill: orangered ;
		}

		.x.axis path {
		  display: none;
		}

		.d3-tip {
		  line-height: 1;
		  font-weight: bold;
		  padding: 12px;
		  background: rgba(0, 0, 0, 0.8);
		  color: #fff;
		  border-radius: 2px;
		  font: 10px "Open Sans";
		  }

		/* Creates a small triangle extender for the tooltip */
		.d3-tip:after {
		  box-sizing: border-box;
		  display: inline;
		  font-size: 10px;
		  width: 100%;
		  line-height: 1;
		  color: rgba(0, 0, 0, 0.8);
		  content: "\25BC";
		  position: absolute;
		  text-align: center;
		}

		/* Style northward tooltips differently */
		.d3-tip.n:after {
		  margin: -1px 0 0 0;
		  top: 100%;
		  left: 0;
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