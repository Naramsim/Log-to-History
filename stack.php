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
comments
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
        <div id="header">
            <div id="search"></div>
            <div id="graphic-title-and-subtitle">
                <div id="graphic-title">Number of users on different folders over time</div>
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
                <script type="text/javascript">
                    $('#submit').on("click",function() { 
                        var date_regex = /^([123]0|[012][1-9]|31)\/(0[1-9]|1[012])\/(19[0-9]{2}|2[0-9]{3})@([01][0-9]|2[0-3]):([0-5][0-9]):([0-5][0-9])$/ ;
                        var to_submit_s = $("#start_date").val();
                        var to_submit_e = $("#end_date").val();
                        if( (date_regex.test(to_submit_s)) && (date_regex.test(to_submit_e)) ){
                            $.ajax({
                                url: 'stack.php',
                                type: 'POST',
                                data: { 'start_date': to_submit_s,
                                        'end_date': to_submit_e}, // An object with the key 'submit' and value 'true;
                                success: function (data) {
                                      prepare_stack();
                                    }
                            });
                        }   
                    });
                </script>
            </div>
        </div>
        <div style="position:absolute;top:90px;width:100%;height:80%;">
            <svg id="chart1"></svg>
        </div>
    </body>
</html>



