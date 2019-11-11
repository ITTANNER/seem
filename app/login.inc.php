<?php

require('../vendor/autoload.php');

/* * * begin our session ** */
session_start();
@$_SESSION["welcomeuser"] = $_POST['phpro_username'];
/* * * check if the users is already logged in ** */
if (isset($_SESSION['user_id'])) {
    $message = 'Users is already logged in';
    //$_SESSION['error'] = 2;
    $_SESSION['error_message'] = $message;
    header('Location:login.php');
}
/* * * check that both the username, password have been submitted ** */
if (!isset($_POST['phpro_username'], $_POST['phpro_password'])) {
    $message = 'Please enter a valid username and password';
    //$_SESSION['error'] = 3;
    $_SESSION['error_message'] = $message;
    header('Location:login.php');
}
/* * * check the username is the correct length ** */ elseif (strlen($_POST['phpro_username']) > 20 || strlen($_POST['phpro_username']) < 4) {
    $message = 'Incorrect Length for Username';
    //$_SESSION['error'] = 4;
    $_SESSION['error_message'] = $message;
    header('Location:login.php');    
}
/* * * check the password is the correct length ** */ elseif (strlen($_POST['phpro_password']) > 20 || strlen($_POST['phpro_password']) < 4) {
    $message = 'Incorrect Length for Password';
    //$_SESSION['error'] = 5;
    $_SESSION['error_message'] = $message;
    header('Location:login.php');
}
/* * * check the username has only alpha numeric characters ** */ elseif (ctype_alnum($_POST['phpro_username']) != true) {
    /*     * * if there is no match ** */
    $message = "Username must be alpha numeric";
   // $_SESSION['error'] = 6;
    $_SESSION['error_message'] = $message;
    header('Location:login.php');
}
/* * * check the password has only alpha numeric characters 
  elseif (ctype_alnum($_POST['phpro_password']) != true)
  {

  $message = "Password must be alpha numeric";
  } */ else {
    /*     * * if we are here the data is valid and we can insert it into database ** */
    $phpro_username = filter_var($_POST['phpro_username'], FILTER_SANITIZE_STRING);
    $phpro_password = filter_var($_POST['phpro_password'], FILTER_SANITIZE_STRING);
    /*     * * now we can encrypt the password ** */
    $phpro_password = sha1($phpro_password);


    try {
        $dao = new Core\Database();
        /*         * * $message = a message saying we have connected ** */

        $res = $dao->row("SELECT * FROM users WHERE username = :username AND password = :password", array('username' => $phpro_username,
            'password' => $phpro_password
                )
        );

        /*         * * check for a result ** */
        $user_id = $res['id'];
        /*         * * if we have no result then fail boat ** */
        if ($user_id == false) {
           // $_SESSION['error'] = 1;
            $message = 'Invaild username / password. ';
            $_SESSION['error_message'] = $message;
            header('Location:login.php');
        }
        else {    
            $_SESSION['user_id'] = $user_id;
            $_SESSION['temp_password'] = $res['temp_password'];
            //check if password is temporary
            if (($_SESSION['temp_password'] == 1)) {
                header('location:password_reset_temp.php?username=' . $phpro_username);
                exit();            
            } else {
                header('location:members.php');
            }
        }
    } catch (Exception $e) {
        /*         * * if we are here, something has gone wrong with the database ** */
        $message = 'We are unable to process your request. Please try again later '.$e->getMessage();
    }
}
?>