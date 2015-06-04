<?php 
	if( isset($_POST['start_date']) && isset($_POST['end_date']) ){
		//print $_POST['start_date']." ".$_POST['end_date'];
		$command = "./main.py ".$_POST['start_date']." ".$_POST['end_date']." 0";	
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
In this webpage is presented a tree graph which first level nodes represents every IP who has been in a specific website.
we can understand who visited a specific website by parsing the access.log made by the webserver(apache2, nginx, ...) of that specific website
For parsing the log PHP invokes a script made in Python that creates a file that will be interpreted by some Javascript code.
The children of a first-level node are the pages that a user has visited coming directly or from another site(by clicking a link), the children of these nodes are the pages visited coming from parent(by clicking a link), and so on

In this case the script is called "main.py", and the Javascript code is in "tree_graph.js"
-->
	<head>
		<link href="css/header.css" rel="stylesheet" >
	    <link href="https://mtgfiddle.me/tirocinio/pezze/css/bootstrap-datetimepicker.min.css" rel="stylesheet" > <!-- Datetime Picker plugin css (for calendar) -->
	    <link href="https://mtgfiddle.me/tirocinio/pezze/css/bootstrap_.css" rel="stylesheet" > <!-- Bootstrap custom css -->
	    <script src="https://code.jquery.com/jquery-1.11.2.min.js" type="text/javascript"></script><!-- jQuery -->
	    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script><!-- D3 -->
	    <script src="https://netdna.bootstrapcdn.com/twitter-bootstrap/2.2.2/js/bootstrap.min.js" type="text/javascript"></script><!-- Bootstrap -->
	    <script src="https://mtgfiddle.me/tirocinio/pezze/bootstrap-datetimepicker.min.js" type="text/javascript"></script><!-- Datetime Picker plugin (for calendar) -->
	    <script src="js/header.js" type="text/javascript"></script>

		<title>Log to History</title>
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400' rel='stylesheet' type='text/css'> <!-- Font Google Open Sans -->
		<link href='css/tree.css' rel='stylesheet' type='text/css'>
		<script src="https://mtgfiddle.me/tirocinio/pezze/d3tip.js" type="text/javascript"></script> <!-- Bootstrap -->
		<script src="js/tree_graph.js" type="text/javascript"></script>
	</head>
	<body>
		<div id="header">
			<a id="logo" href="index.php">
	            <img src="data:image/svg+xml;base64,CjwhZG9jdHlwZSBodG1sPgo8aHRtbCBsYW5nPSJlbi1VUyI+CiAgICA8aGVhZD4KICAgICAgICA8bWV0YSBjaGFyc2V0PSJ1dGYtOCI+CiAgICAgICAgPHRpdGxlPlJlZ2lzdGVyIHRvIGNvbnRpbnVlIGRvd25sb2FkaW5nIGZyZWUgaWNvbnM8L3RpdGxlPgogICAgICAgIDxtZXRhIG5hbWU9ImtleXdvcmRzIiBjb250ZW50PSIiIC8+CiAgICAgICAgPG1ldGEgbmFtZT0iZGVzY3JpcHRpb24iIGNvbnRlbnQ9IiAiIC8+CiAgICAgICAgPG1ldGEgbmFtZT0idmlld3BvcnQiIGNvbnRlbnQ9IndpZHRoPWRldmljZS13aWR0aCwgaW5pdGlhbC1zY2FsZT0xLjAiIC8+CiAgICAgICAgPG1ldGEgcHJvcGVydHk9Im9nOnNpdGVfbmFtZSIgY29udGVudD0iSWNvbmZpbmRlciIvPgogICAgICAgIDxtZXRhIHByb3BlcnR5PSJvZzp1cmwiIGNvbnRlbnQ9Imh0dHBzOi8vd3d3Lmljb25maW5kZXIuY29tL2ljb25zLzIxNjIxNS9kb3dubG9hZC9zdmcvNDgiLz4KICAgICAgICA8bWV0YSBwcm9wZXJ0eT0iZmI6YWRtaW5zIiBjb250ZW50PSI2NTU2NDcwODEiLz4KICAgICAgICA8bWV0YSBwcm9wZXJ0eT0iZmI6YXBwX2lkIiBjb250ZW50PSIzMTk1MzI2MzM0NDQiLz4KICAgICAgICA8bWV0YSBuYW1lPSJ0d2l0dGVyOnVybCIgY29udGVudD0iaHR0cHM6Ly93d3cuaWNvbmZpbmRlci5jb20vaWNvbnMvMjE2MjE1L2Rvd25sb2FkL3N2Zy80OCI+CiAgICAgICAgPG1ldGEgbmFtZT0idHdpdHRlcjpzaXRlIiBjb250ZW50PSJAaWNvbmZpbmRlciI+PG1ldGEgcHJvcGVydHk9Im9nOnRpdGxlIiBjb250ZW50PSIiLz4KICAgICAgICA8bWV0YSBwcm9wZXJ0eT0ib2c6dHlwZSIgY29udGVudD0id2Vic2l0ZSIgLz4KICAgICAgICA8bWV0YSBwcm9wZXJ0eT0ib2c6aW1hZ2UiIGNvbnRlbnQ9Imh0dHBzOi8vY2RuMS5pY29uZmluZGVyLmNvbS9zdGF0aWMvYTM0NzdjYWMwNTRjZDZhZmI5OTY3YTQzZTVjNWQxMTcvYXNzZXRzL2ltZy9tZXRhZmFjZWJvb2ttYWluLmpwZyIvPgogICAgICAgIDxtZXRhIHByb3BlcnR5PSJvZzpkZXNjcmlwdGlvbiIgY29udGVudD0iIi8+CiAgICAgICAgPG1ldGEgbmFtZT0iYXV0aG9yIiBjb250ZW50PSJJY29uZmluZGVyIj4KICAgICAgICA8bWV0YSBuYW1lPSJ0d2l0dGVyOmNyZWF0b3IiIGNvbnRlbnQ9IkBpY29uZmluZGVyIj4KICAgICAgICA8bWV0YSBuYW1lPSJ0d2l0dGVyOmNhcmQiIGNvbnRlbnQ9InN1bW1hcnlfbGFyZ2VfaW1hZ2UiPgogICAgICAgIDxtZXRhIG5hbWU9InR3aXR0ZXI6dGl0bGUiIGNvbnRlbnQ9IiI+CiAgICAgICAgPG1ldGEgbmFtZT0idHdpdHRlcjpkZXNjcmlwdGlvbiIgY29udGVudD0iIj4KICAgICAgICA8bWV0YSBuYW1lPSJ0d2l0dGVyOmltYWdlOnNyYyIgY29udGVudD0iaHR0cHM6Ly9jZG4xLmljb25maW5kZXIuY29tL3N0YXRpYy9hMzQ3N2NhYzA1NGNkNmFmYjk5NjdhNDNlNWM1ZDExNy9hc3NldHMvaW1nL21ldGFmYWNlYm9va21haW4uanBnIiAvPjxsaW5rIHJlbD0ic2VhcmNoIiB0eXBlPSJhcHBsaWNhdGlvbi9vcGVuc2VhcmNoZGVzY3JpcHRpb24reG1sIiB0aXRsZT0iSWNvbmZpbmRlciIgaHJlZj0iaHR0cHM6Ly9jZG4wLmljb25maW5kZXIuY29tL2ljb25maW5kZXIueG1sIiAvPgoKICAgICAgICA8bGluayByZWw9InNob3J0Y3V0IGljb24iIGhyZWY9Imh0dHBzOi8vY2RuMS5pY29uZmluZGVyLmNvbS9zdGF0aWMvYTk2YzM4Yjg1NDcwNzY4MWY0MjE0OTgzMWYyM2JlY2QvZGVzaWduL2ltYWdlcy9mYXZpY29uLmljbyIgLz4KCiAgICAgICAgPGxpbmsgcmVsPSJzdHlsZXNoZWV0IiBocmVmPSJodHRwczovL2NkbjIuaWNvbmZpbmRlci5jb20vc3RhdGljL2Y2NzU0MGIyYjUwZTNmMGZlYjA2MTdlMjlhNzliNjAxL2ljb25maW5kZXIuY3NzIiAvPgogICAgICAgIDxzY3JpcHQgdHlwZT0idGV4dC9qYXZhc2NyaXB0Ij4KICAgICAgICAgICAgdmFyIF9nYXEgPSBfZ2FxIHx8IFtdOwogICAgICAgICAgICBfZ2FxLnB1c2goWydfc2V0QWNjb3VudCcsICdVQS02MzQ1ODQtMiddKTsKICAgICAgICAgICAgX2dhcS5wdXNoKFsnX3NldERvbWFpbk5hbWUnLCAnd3d3Lmljb25maW5kZXIuY29tJ10pOwogICAgICAgICAgICBfZ2FxLnB1c2goWydfc2V0QWxsb3dMaW5rZXInLCB0cnVlXSk7X2dhcS5wdXNoKFsnX3NldEN1c3RvbVZhcicsCiAgICAgICAgICAgICAgICAxLCAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAnU2lnbmVkIGluJywgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAnRmFsc2UnLAogICAgICAgICAgICAgICAgMiAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICBdKTtfZ2FxLnB1c2goWydfc2V0Q3VzdG9tVmFyJywKICAgICAgICAgICAgICAgIDIsJ1BybyBzdWJzY3JpYmVyJywnRmFsc2UnLAogICAgICAgICAgICAgICAgMl0pO19nYXEucHVzaChbJ190cmFja1BhZ2V2aWV3J10pOwogICAgICAgICAgICAoZnVuY3Rpb24oKSB7CiAgICAgICAgICAgICAgICAgICAgdmFyIGdhID0gZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgnc2NyaXB0Jyk7IGdhLnR5cGUgPSAndGV4dC9qYXZhc2NyaXB0JzsgZ2EuYXN5bmMgPSB0cnVlOwogICAgICAgICAgICAgICAgICAgIGdhLnNyYyA9ICgnaHR0cHM6JyA9PSBkb2N1bWVudC5sb2NhdGlvbi5wcm90b2NvbCA/ICdodHRwczovL3NzbCcgOiAnaHR0cDovL3d3dycpICsgJy5nb29nbGUtYW5hbHl0aWNzLmNvbS9nYS5qcyc7CiAgICAgICAgICAgICAgICAgICAgdmFyIHMgPSBkb2N1bWVudC5nZXRFbGVtZW50c0J5VGFnTmFtZSgnc2NyaXB0JylbMF07IHMucGFyZW50Tm9kZS5pbnNlcnRCZWZvcmUoZ2EsIHMpOwogICAgICAgICAgICB9KSgpOwogICAgICAgIDwvc2NyaXB0PjxzY3JpcHQgdHlwZT0idGV4dC9qYXZhc2NyaXB0IiBzcmM9Imh0dHBzOi8vY2RuMC5pY29uZmluZGVyLmNvbS9zdGF0aWMvOGVmZTlhYzE3YjcwNWY2ODRlYzJlODc3YjM2NjE3ZGMvaWNvbmZpbmRlci5qcyI+PC9zY3JpcHQ+CiAgICAgICAgPCEtLVtpZiBsdGUgSUUgOF0+CiAgICAgICAgPHNjcmlwdCBzcmM9Imh0dHBzOi8vY2RuMi5pY29uZmluZGVyLmNvbS9zdGF0aWMvZmI0ZTNiMTllODk0NjQyN2IxMTQ2MjNhZDk0YjBhOTUvYXNzZXRzL3N0YXRpYy1qcy9odG1sNXNoaXYuanMiPjwvc2NyaXB0PgogICAgICAgIDwhW2VuZGlmXS0tPgogICAgPC9oZWFkPgogICAgPGJvZHkgaWQ9InNpbXBsZWZvcm0iIGRhdGEtYXBwbGljYXRpb249Imljb25zIiBkYXRhLXZpZXc9InJlZ2lzdHJhdGlvbl9yZXF1aXJlZCI+CiAgICAgICAgPGEgaWQ9InRvcCI+PC9hPgogICAgICAgIAogICAgICAgIAogICAgPGRpdiBjbGFzcz0iZml4dHVyZSI+CiAgICAgICAgPGgxPlJlZ2lzdGVyIHRvIGNvbnRpbnVlIGRvd25sb2FkaW5nIGZyZWUgaWNvbnM8L2gxPgogICAgPC9kaXY+CgogICAgICAgIDxzY3JpcHQgdHlwZT0idGV4dC9qYXZhc2NyaXB0Ij4KKGZ1bmN0aW9uKHcpIHsKICAgIHcuRkFDRUJPT0tfQVBQX0lEID0gJzU1MTQ3NDYyNDkwNjMxOSc7CiAgICB3LlNUUklQRV9QVUJMSVNIQUJMRV9LRVkgPSAncGtfbGl2ZV80V3ZSaENOdmhScGxPUVRVdnM2NXV3OXcnOwoKCgoKICAgIHZhciBic2EgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdzY3JpcHQnKTsKICAgIGJzYS50eXBlID0gJ3RleHQvamF2YXNjcmlwdCc7CiAgICBic2EuYXN5bmMgPSB0cnVlOwogICAgYnNhLnNyYyA9ICdodHRwczovL2Nkbi5idXlzZWxsYWRzLmNvbS9hYy9wcm8uanMnOwogICAgZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoJ2hlYWQnKVswXS5hcHBlbmRDaGlsZChic2EpOwoKfSh3aW5kb3cpKTsKICAgICAgICA8L3NjcmlwdD4KICAgICAgICA8c2NyaXB0IGFzeW5jIHR5cGU9InRleHQvamF2YXNjcmlwdCIgc3JjPSJodHRwczovL2pzLnN0cmlwZS5jb20vdjIvIj48L3NjcmlwdD4KICAgICAgICA8ZGl2IGlkPSJmYi1yb290Ij48L2Rpdj4KICAgICAgICA8c2NyaXB0IHR5cGU9InRleHQvamF2YXNjcmlwdCI+YWRyb2xsX2Fkdl9pZD0iV0ZDNVpRN0pGWkhYM0EyWU9TWDdBRSIsYWRyb2xsX3BpeF9pZD0iUFpIVVk1MktORkZGN0haM1FZQU5SNCIsZnVuY3Rpb24oKXt2YXIgYT13aW5kb3cub25sb2FkO3dpbmRvdy5vbmxvYWQ9ZnVuY3Rpb24oKXtfX2Fkcm9sbF9sb2FkZWQ9ITA7dmFyIGI9ZG9jdW1lbnQuY3JlYXRlRWxlbWVudCgic2NyaXB0IiksYz0iaHR0cHM6Ij09ZG9jdW1lbnQubG9jYXRpb24ucHJvdG9jb2w/Imh0dHBzOi8vcy5hZHJvbGwuY29tIjoiaHR0cDovL2EuYWRyb2xsLmNvbSI7Yi5zZXRBdHRyaWJ1dGUoImFzeW5jIiwidHJ1ZSIpLGIudHlwZT0idGV4dC9qYXZhc2NyaXB0IixiLnNyYz1jKyIvai9yb3VuZHRyaXAuanMiLCgoZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoImhlYWQiKXx8W251bGxdKVswXXx8ZG9jdW1lbnQuZ2V0RWxlbWVudHNCeVRhZ05hbWUoInNjcmlwdCIpWzBdLnBhcmVudE5vZGUpLmFwcGVuZENoaWxkKGIpLGEmJmEoKX19KCk7PC9zY3JpcHQ+CiAgICA8L2JvZHk+CjwvaHRtbD4=">
	        </a>
	        <div id="search"></div>
	        <div id="graphic-title-and-subtitle">
	            <div id="graphic-title">Accurated user history on a site</div>
	            <div id="graphic-subtitle">Choose the time to analyze</div>
	            
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
				<div id="explanation">Waiting for user input</div>
	            <div id="loader"></div>
				<script type="text/javascript">
			        $('#submit').on("click",function() { 
			        	var date_regex = /^([123]0|[012][1-9]|31)\/(0[1-9]|1[012])\/(19[0-9]{2}|2[0-9]{3})@([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/ ;
						var to_submit_s = $("#start_date").val();
						var to_submit_e = $("#end_date").val();
			        	if( (date_regex.test(to_submit_s)) && (date_regex.test(to_submit_e)) ){
			        		start_spinner();
			        		$.ajax({
								url: 'tree.php',
								type: 'POST',
								data: { 'start_date': to_submit_s,
										'end_date': to_submit_e}, // An object with the key 'submit' and value 'true;
								success: function (data) {
									  mid_spinner();
									  prepare_graph();
									}
							});
			        	}	
		        	});
		        </script>
	        </div>
	    </div>
	    <div id="graph" style="position:absolute;top:90px;width:100%;height:80%;"></div>
	</body>

<html>