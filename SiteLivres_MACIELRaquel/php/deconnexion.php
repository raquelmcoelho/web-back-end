<?php
session_start();
// Unset all session variables
$_SESSION = array();
// Unset the cookie too
// source: https://stackoverflow.com/questions/2310558/how-to-delete-all-cookies-of-my-website-in-php
$past = time() - 3600;
setcookie( "code_client", 0, $past, '/' );
?>