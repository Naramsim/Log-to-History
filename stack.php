<?php 
	if( isset($_POST['start_date']) && isset($_POST['end_date']) ){
		//print $_POST['start_date']." ".$_POST['end_date'];
		$command = "./main.py ".$_POST['start_date']." ".$_POST['end_date']." 2";
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
Stack chart is a chart which shows among the time the different portions of users on all the folders of a site.
-->
    <head>
        <meta http-equiv="cache-control" content="no-cache"> 
        <meta http-equiv="expires" content="0"> 
        <meta http-equiv="pragma" content="no-cache">
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
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/nvd3/1.7.0/nv.d3.min.js"></script><!-- NVD3 -->
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/nvd3/1.7.0/nv.d3.min.css"><!-- NVD3 css -->
        <script type="text/javascript" src="js/stack_chart.js"></script>
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
    </head>
    <body class='with-3d-shadow with-transitions'>
        <script>
        (function getTemplateAjax(path) {
          var source;
          var template;

          $.ajax({
            url: "templates/head.handlebars", 
              //cache: true,
              success: function(data) {
                source    = data;
                template  = Handlebars.compile(source);
                var context = {title: "Number of users on different folders over time", sub: "Choose the time to analyze", post_page: "stack.php", chart: "prepare_stack();"};
                var header    = template(context);
                $('body').append(header);
            }
          });
        })();
        </script>
        <div style="position:absolute;top:90px;width:97%;height:80%;">
            <svg id="chart" style="overflow:visible"></svg>
        </div>
    </body>
</html>



