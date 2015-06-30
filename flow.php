<?php 
	if( isset($_POST['start_date']) && isset($_POST['end_date']) ){
		//print $_POST['start_date']." ".$_POST['end_date'];
		$command = "./main.py ".$_POST['start_date']." ".$_POST['end_date']." 1";
		//print $command;
		ob_start();
		system($command, $status);
		$output = ob_get_clean();
		print $output;
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
		<script src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/2.0.0/handlebars.js"></script><!-- HandleBar (template engine)-->
		<script src="js/header.js" type="text/javascript"></script>

		<title>Log To History</title>
        <link href="css/nfl.css" rel="stylesheet" >
        <link href="https://mtgfiddle.me/tirocinio/pezze/css/chosen.min.css" rel="stylesheet" > <!-- Chosen Pluging css-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.3/moment.min.js" type="text/javascript"></script> <!-- Moment -->
        <script src="https://mtgfiddle.me/tirocinio/pezze/chosen.jquery.min.js" type="text/javascript"></script><!-- Chosen Pluging (for button and search interaction)-->
        <script src="js/flow_chart.js" type="text/javascript"></script>

        
    </head>
    <body>
    	<script>
		(function getTemplateAjax(path) {
		  var source;
		  var template;

		  $.ajax({
		    url: "templates/head.handlebars", //ex. js/templates/mytemplate.handlebars
		      //cache: true,
		      success: function(data) {
		        source    = data;
		        template  = Handlebars.compile(source);
		        var context = {title: "User History on a site", sub: "IP switching pages are highlighted as:", post_page: "flow.php", chart: "prepare_flow_chart();"};
				var header    = template(context);
		        $('body').append(header);
		    }
		  });
		})();
		</script>
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
