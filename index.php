<?php
session_start();

if(!empty($_SESSION['oauth_uid'])){
    header("Location: home.php");
}

?>

<a href="login.php">LOGIN WITH TWITTER</a>