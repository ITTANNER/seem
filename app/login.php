<?php 
session_start();
?>
<!DOCTYPE html> 
<html> 
    <head>
        <title>Inicio de sesión</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="bower_components/bootstrap/dist/css/bootstrap.min.css"  rel="stylesheet" type="text/css">       
        <link href="layout/css/login.css"  rel="stylesheet" type="text/css">
    </head>
    <header id="cabecera">
        <img src= "layout/img/essam.jpg" alt="logo" class="logo">   
        <h3>Control de Acceso</h3>
    </header>    
    <body>               
        <div class="container">
            <div class="imgcontainer"> 
                <img src= "layout/img/profile.png" 
                     alt="Avatar" class="avatar"> 
            </div>                 
            <form method="post"> 
                <div class="form-group">
                    <label><b>Usuario:</b></label> 
                    <input type="text" class="form-control" placeholder="Ingresa tu usuario" name="phpro_username" required> 
                    <label><b>Contraseña:</b></label> 
                    <input class="form-control" type="password" placeholder="Ingresa tu contraseña" name="phpro_password" required> 
                </div>
                <div class="form-group">   
                    <button type="submit" formaction="login_submit.php" class="btn btn-success col-sm-6">Iniciar sesión</button>                     
                    <a class="col-sm-6 forgot" href="forgot_password.php">¿Olvido su contraseña? </a>
                </div>                
                <div class="col-lg-12">   
                    <br>
                    <?php
                    if (isset($_SESSION['error_message'])):
                        $Message = $_SESSION['error_message'];
                        ?>
                        <center><span class="label label-danger"><?= $Message ?></span></center>
                        <?php endif; ?>         
                </div>
            </form> 
        </div> 
        <br>
        <?php require_once './layout/footer.php'; ?>
    </body> 

</html> 
