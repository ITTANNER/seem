<?php
/* * * begin the session ** */
session_start();
if (!empty($_SESSION)) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Welcome</title>
            <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.css">
            <link rel="stylesheet" href="bower_components/bootstrap/dist/css/bootstrap.min.css">                        
            <link rel="stylesheet" href="layout/css/members.css">
        </head>
        <body>
            <!--================Header Menu Area =================-->
           <?php require_once './layout/navbar.php'; ?>
            <!--================ Hero sm Banner start =================-->
            <div class="container">
                <section class="hero-banner">         
                    <div class="row">
                        <div class="col-lg-7">
                            <img src="layout/img/hero-banner.png" alt="" class="responsive home" >
                        </div>
                        <div class="col-lg-5 pt-5">
                            <div class="hero-banner__content">
                                <h2>El Software Avanzado lo hace más simple </h2>
                                <p>Nuestras Aplicaciones te ayudan a monitorear maquinas en tiempo real.</p>
                                <a class="btn btn-info" href="motor/">Comenzemos</a>
                            </div>
                        </div>
                    </div>
                    <br>
                </section>

            </div>
            <script src="bower_components/jquery/jquery.min.js"></script>
            <script src="bower_components/bootstrap/dist/js/bootstrap.min.js"></script>            
            <!--================ Hero sm Banner end =================-->
            <?php require_once './layout/footer.php'; ?>
            <!-- ================ End footer Area ================= -->                 
        </body>
    </html>
    <?php
} else {
    header("Location: index.php");
}
?>