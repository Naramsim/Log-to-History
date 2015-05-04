<html>
<!--
In this webpage is presented a flow graph presenting several lines. Each line represent an IP, when a line switch column it means that at a certain time a user stopped to browse a specific folder of a site and started to browse another one.
I.e.: at 10.34 a user is visiting "www.site.com/about/index.html" an then at 10.36 is browsing "www.site.com/recipes/cake.html" here there will be a switch of the user line, from "/about" folder to "/recipes" folder
we can understand who visited a specific website by parsing the access.log made by the webserver(apache2, nginx, ...) of that specific website
For parsing the log PHP invokes a script made in Python that creates a file that will be interpreted by some Javascript code.

In this case the script is called "main.py", and the Javascript code is in "flow_chart.js"
-->
    <head>
        <link href="css/nfl.css" rel="stylesheet" >
        <link href="css/chosen.min.css" rel="stylesheet" >
        <title>AccessLog</title>
        <script src="https://code.jquery.com/jquery-1.11.2.min.js" type="text/javascript"></script><!-- jQuery -->
        <script src="https://mtgfiddle.me/tirocinio/pezze/chosen.jquery.min.js" type="text/javascript"></script><!-- Chosen Pluging (for button and search interaction)-->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js" type="text/javascript"></script><!-- D3 -->
        <script src="js/flow_chart.js" type="text/javascript"></script>
    </head>

    <?php 
      ob_start();
      system("./main.py", $status);
      $output1 = json_decode( ob_get_clean() , true);
      $json_string = json_encode($output1, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    ?>

    <body>
        <div id="header">
            <div id="search"></div>
            <div id="graphic-title-and-subtitle">
                <div id="graphic-title">User History since one hour ago</div>
                <div id="graphic-subtitle">IP switching pages are highlighted as:</div>
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

    <script>
        prepare_flow_chart()
    </script>
</html>
