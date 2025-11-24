<?php
    session_start();
    if(isset($_SESSION["backend_email"]) && isset($_SESSION["backend_id"]) && isset($_SESSION["backend_username"]) && isset($_SESSION['backend_role'])){
        unset($_SESSION["backend_email"],$_SESSION["backend_id"],$_SESSION['backend_username'],$_SESSION['backend_role']);
    }
    header("location: ../login.php");
?>