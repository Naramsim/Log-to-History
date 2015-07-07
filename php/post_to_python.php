<?php 
if( isset($_POST['start_date']) && isset($_POST['end_date']) ){
	//print $_POST['start_date']." ".$_POST['end_date'];
	$graph_type = substr($_POST['graph'], 0, -4);
	$graph_name_unique = $_POST['name'];
	$graph_num = -1;
	if ($graph_type=="tree"){
		$graph_num = 0;
	} else if ($graph_type=="flow"){
		$graph_num = 1;
	} else if ($graph_type=="stack"){
		$graph_num = 2;
	}
	$command = "./main.py ".$_POST['start_date']." ".$_POST['end_date']." ".$graph_num." ".$graph_name_unique;
	print $command;
	ob_start();
	system($command, $status);
	$output = ob_get_clean();
	print $output;
	die();
}
?>


