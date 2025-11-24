<?php
    session_start();
    if(isset($_SESSION["email"]) && isset($_SESSION["id"]) && isset($_SESSION["username"]) && isset($_SESSION['role'])){
        unset($_SESSION["email"],$_SESSION["cartItems"],$_SESSION["id"],$_SESSION["username"],$_SESSION['role']);
    }
    header("location: ../index.php");
?>