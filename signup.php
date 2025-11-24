<?php

session_start();
if(isset($_SESSION['email']) && isset($_SESSION['username'])){
    header("location: index.php");
    exit();
}

$errFristname = null;
$errLastname = null;
$errEmail = null;
$errPassword = null;
$err = [];
$fields = array("firstname" => "", "lastname" => "", "email" => "", "password" => "", "cf-password" => "");

if (isset($_POST["signup"])) {
    $firstname = trim($_POST["firstname"]);
    $lastname = trim($_POST["lastname"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["cf-password"]);

    $fields["firstname"] = $firstname;
    $fields["lastname"] = $lastname;
    $fields["email"] = $email;
    $fields["password"] = $password;
    $fields["cf-password"] = $confirm_password;

    if ($firstname == "" || $lastname == "" || $email == "" || $password == "" || $confirm_password == "") {
        $errFristname = "all field are required.";
        $err[] = $errFristname;
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errEmail = "Invalid email format.";
            $err[] = $errEmail;
        }

        if (!preg_match("/^[a-zA-Z]+$/", $firstname)) {
            $err[] = "First name must be string only.";
        }

        if (!preg_match("/^[a-zA-Z]+$/", $lastname)) {
            $err[] = "Lastname must be string only.";
        }

        if (!preg_match("/^.{8,}$/", $password)) {
            $err[] = "Password must be at least 8 length.";
        } else {
            if ($password != $confirm_password) {
                $err[] = "Password and confirm password must match.";
            }
        }
        if (count($err) == 0) {


            //  insert to database 
           require './database/dbcon.php';

            if ($conn->connect_errno > 0) {
                echo "Connection failed: " . $conn->connect_error;
                exit();
            }
            $sql = "SELECT id FROM users WHERE LOWER(firstname) = ? OR LOWER(email) = ? ";
            $stmt = $conn->prepare($sql);

            $newFirstname = strtolower($firstname);
            $newEmail = strtolower($email);

            $stmt->bind_param("ss",$newFirstname ,$newEmail);
            $stmt->execute();

            unset($newEmail,$newFirstname);

            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $err[] = "Firstname or email address is already exists.";
            }else{
                $newPassword = md5($password);
                try {
                    $sql = $conn->prepare("INSERT INTO users (firstname,lastname,email,password) VALUES(?,?,?,?)");
                    $sql->bind_param('ssss', $firstname, $lastname, $email, $newPassword);
                    if ($sql->execute()) {
                        session_regenerate_id(true);

                        // for use client site
                        $_SESSION['email'] = $email;
                        $_SESSION['username'] = $firstname." ".$lastname;
                        $_SESSION['role'] = "user";
                        $_SESSION['id'] = $conn->insert_id;
                        header("location: index.php");
                        exit();
                    }
                } catch (Exception $e) {
                    if ($conn->errno > 0) {
                        if ($conn->errno == 1062) {
                            $err[] = "Email already exists.";
                        }
                    }
                }
            }
            $conn->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="css/signup.css">
</head>

<body>
    <div id="container">

        <form action="" method="post" style="aspect-ratio: 2 / 3.15;">
            <h2>registation from</h2>
            <?php foreach ($err as $errMsg): ?>
                <span>• <?= $errMsg . "<br>" ?></span>
            <?php endforeach; ?>
            <div id="fistandlast-wrapper">

                <div class="input-wrapper">
                    <label for="">First Name</label>
                    <input type="text" name="firstname" value="<?= $fields['firstname'] ?>">
                </div>
                <div class="input-wrapper">
                    <label for="">Last Name</label>
                    <input type="text" name="lastname" value="<?= $fields['lastname'] ?>">
                </div>
            </div>
            <div class="input-wrapper">
                <label for="">Email Address</label>
                <input type="text" name="email" value="<?= $fields['email'] ?>">
            </div>
            <div class="input-wrapper">
                <label for="">Password</label>
                <input type="password" name="password" value="<?= $fields['password'] ?>">
            </div>
            <div class="input-wrapper">
                <label for="">Confirm Password</label>
                <input type="password" name="cf-password" value="<?= $fields['cf-password'] ?>">
            </div>
            <button name="signup" type="submit">
                <span>Sign Up</span>
            </button>
            <div style="margin-top: 20px !important;text-align: center;" class="input-wrapper">
                <span style="color:black">Have account? <a href="login.php">Login</a></span>
            </div>
        </form>
    </div>
</body>

</html>