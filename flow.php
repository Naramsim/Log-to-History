<?php 
	if( isset($_POST['start_date']) && isset($_POST['end_date']) ){
		//print $_POST['start_date']." ".$_POST['end_date'];
		$command = "./main.py ".$_POST['start_date']." ".$_POST['end_date']." 1";
		print $command;
		ob_start();
		system($command, $status);
		$output1 = json_decode( ob_get_clean() , true);
		$json_string = json_encode($output1, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
		die();
	}
?>
<html>
<!--
In this webpage is presented a flow graph presenting several lines. Each line represent an IP, when a line switch column it means that at a certain time a user stopped to browse a specific folder of a site and started to browse another one.
I.e.: at 10.34 a user is visiting "www.site.com/about/index.html" an then at 10.36 is browsing "www.site.com/recipes/cake.html" here there will be a switch of the user line, from "/about" folder to "/recipes" folder
we can understand who visited a specific website by parsing the access.log made by the webserver(apache2, nginx, ...) of that specific website
For parsing the log PHP invokes a script made in Python that creates a file that will be interpreted by some Javascript code.

In this case the script is called "main.py", and the Javascript code is in "flow_chart.js"
-->
    <head>
    	<link href="css/header.css" rel="stylesheet" >
		<link href="https://mtgfiddle.me/tirocinio/pezze/css/bootstrap-datetimepicker.min.css" rel="stylesheet" > <!-- Datetime Picker plugin css (for calendar) -->
		<link href="https://mtgfiddle.me/tirocinio/pezze/css/bootstrap_.css" rel="stylesheet" > <!-- Bootstrap custom css -->
		<script src="https://code.jquery.com/jquery-1.11.2.min.js" type="text/javascript"></script><!-- jQuery -->
		<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script><!-- D3 -->
		<script src="https://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/js/bootstrap.min.js" type="text/javascript"></script><!-- Bootstrap -->
		<script src="https://mtgfiddle.me/tirocinio/pezze/bootstrap-datetimepicker.min.js" type="text/javascript"></script><!-- Datetime Picker plugin (for calendar) -->

		<title>Log To History</title>
        <link href="css/nfl.css" rel="stylesheet" >
        <link href="https://mtgfiddle.me/tirocinio/pezze/css/chosen.min.css" rel="stylesheet" > <!-- Chosen Pluging css-->
        <script src="https://mtgfiddle.me/tirocinio/pezze/chosen.jquery.min.js" type="text/javascript"></script><!-- Chosen Pluging (for button and search interaction)-->
        <script src="js/flow_chart.js" type="text/javascript"></script>
    </head>
    <body>
        <div id="header">
            <div id="search"></div>
            <div id="graphic-title-and-subtitle">
                <div id="graphic-title">User History on a site</div>
                <div id="graphic-subtitle">IP switching pages are highlighted as:</div>
                
				  <div id="datetimepickerStart" class="input-append date">
				    <input data-format="dd/MM/yyyy@hh:mm:ss" type="text" id="start_date" placeholder="start"></input>
				    <span class="add-on">
				      <i data-time-icon="icon-time" data-date-icon="icon-calendar">
				      </i>
				    </span>
				  </div>
				  <div id="datetimepickerEnd" class="input-append date">
				    <input data-format="dd/MM/yyyy@hh:mm:ss" type="text" id="end_date" placeholder="end"></input>
				    <span class="add-on">
				      <i data-time-icon="icon-time" data-date-icon="icon-calendar">
				      </i>
				    </span>
				  </div>
				
				<script type="text/javascript">
				  $(function() {
				    $('#datetimepickerStart, #datetimepickerEnd').datetimepicker({
				    	format: 'dd/MM/yyyy@hh:mm:ss'
				    	//language: 'pt-BR'
				    });
				  });
				</script>
				<button id="submit" type="button" class="btn btn-default btn-sm">Send</button>
				<script type="text/javascript">
			        $('#submit').on("click",function() { 
			        	var date_regex = /^([123]0|[012][1-9]|31)\/(0[1-9]|1[012])\/(19[0-9]{2}|2[0-9]{3})@([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/ ;
						var to_submit_s = $("#start_date").val();
						var to_submit_e = $("#end_date").val();
			        	if( (date_regex.test(to_submit_s)) && (date_regex.test(to_submit_e)) ){
			        		$.ajax({
								url: 'flow.php',
								type: 'POST',
								data: { 'start_date': to_submit_s,
										'end_date': to_submit_e}, // An object with the key 'submit' and value 'true;
								success: function (data) {
									  prepare_flow_chart();
									}
							});
			        	}	
		        	});
		        </script>
            </div>
        </div>
        <div id="folder-label-container">
            <svg id="folder-label"></svg>
        </div>
        <div id="graphic-and-annotations">
            <!-- <div id="annotations"></div> -->
            <div id="graphic"></div>
            <div id="overlay"></div>
        </div>
    </body>
</html>
