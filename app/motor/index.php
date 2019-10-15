<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Bootstrap Example</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="../bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>   
        <script src="../bower_components/jquery/dist/jquery.min.js"></script>
        <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
        <link href="css/main.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
            <br>
            <div class="panel panel-default">
                <div class="panel-heading"><h3>Motor Actions</h3></div>
                <div class="panel-body ">                         
                    <button type="submit" class="btn btn-primary btn-lg btn-block" id="start" name='"Output_1"' value='1'>Start</button>                    
                    <br>                    
                    <button type="submit" class="btn btn-danger btn-lg btn-block" id="stop" name='"Output_0"' value='0' >Stop</button>
                </div>
                <!--<div class="panel-footer"><strong>Status: </strong><span>(:="Output_0":)</span>-->
                <div class="panel-footer"><strong>Status: </strong><span id="status">0</span></div>
            </div>
        </div>
        <script src="js/socket.io.js" charset="utf-8"></script>
        <script src="js/motor.js" charset="utf-8"></script>
    </body>

</html>