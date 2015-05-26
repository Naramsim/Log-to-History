<?php /*
	if( isset($_POST['start_date']) && isset($_POST['end_date']) ){
		//print $_POST['start_date']." ".$_POST['end_date'];
		$command = "./main.py ".$_POST['start_date']." ".$_POST['end_date']." 2".;
		print $command;
		ob_start();
		system($command, $status);
		$output1 = json_decode( ob_get_clean() , true);
		$json_string = json_encode($output1, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		die();
	}*/
  
?>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/nvd3/1.7.0/nv.d3.min.js"></script>
<script type="text/javascript" src="js/stack_chart.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/nvd3/1.7.0/nv.d3.min.css">
<style>
    text {
        font: 12px sans-serif;
    }
    svg {
        display: block;
    }
    html, body, svg {
        margin: 0px;
        padding: 0px;
        height: 100%;
        width: 100%;
    }
</style>
<body class='with-3d-shadow with-transitions'>

<svg id="chart1"></svg>

<script type="text/javascript">
	prepare_stack();
</script>

</body>

