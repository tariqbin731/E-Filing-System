<?php
session_start();

if (!isset($_SESSION['efileid'])) {
   
    header("Location: login.php");
    
} 
?>
