<?php
// Begin the session
session_start();

// Unset all of the session variables.
session_unset();

// Destroy the session.
session_destroy();
{header('location:login.php');
				exit();
				}
?>
<html>
<head>
<title>Logged Out</title>
</head>

<body>
<h1>You are now logged out. Please come again</h1>
<br />
<br />
<p><a href="login.php">Go Back to Main</p>
</body>
</html>