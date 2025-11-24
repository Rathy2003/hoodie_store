<?php

session_start();
if(isset($_SESSION["email"]) && isset($_SESSION['username']) && isset($_SESSION['id']) && isset($_SESSION["role"])){
    return header("location: index.php");
}

$err = [];
$fields = array("email" => "", "password" => "");

if (isset($_POST["login"])) {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    $fields["email"] = $email;
    $fields["password"] = $password;

    if (empty($email) || empty($password)){
        $err[] = "all field are required.";
    } else {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $err[] = "Invalid email format.";
        }

        if (!preg_match("/^.{8,}$/", $password)) {
            $err[] = "Password must be at least 8 length.";
        }

        if (count($err) == 0) {
           require 'database/dbcon.php';

            if ($conn->connect_errno > 0) {
                echo "Connection failed: " . $conn->connect_error;
                exit();
            }

            try {
                $sql = $conn->prepare("SELECT concat(firstname,' ',IFNULL(lastname,'')) as fullname,id,role,email,password FROM users WHERE email=? AND password = ?");
                $pass = md5($password);
                $sql->bind_param('ss',$email, $pass);
                if ($sql->execute()) {
                    $result = $sql->get_result();
                    if($result->num_rows > 0){ // mean login successfull
                        $row = $result->fetch_assoc();
                        $username = $row["fullname"];
                        session_regenerate_id(true);
                        // for use backend site
                        if($row["role"] == "admin"){
                            $_SESSION["backend_email"] = $email;
                            $_SESSION["backend_username"] = $username;
                            $_SESSION["backend_role"] = $row["role"];
                            $_SESSION["backend_id"] = $row["id"];
                            header("location: dashboard/index.php");
                        }else{
                            // for use client site
                            $_SESSION['email'] = $email;
                            $_SESSION['username'] = $username;
                            $_SESSION['role'] = $row["role"];
                            $_SESSION['id'] = $row["id"];
                            header("location: index.php");
                        }

                        
                    }else{
                        $err[] = "Incorrect email or password.";
                    }
                }
            } catch (Exception $e) {
                if ($conn->errno > 0) {
                    echo $sql->errno;
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
    <title>Login</title>
    <link rel="stylesheet" href="/CSS/signup.css">
</head>

<body>
    <div id="container">

        <form  action="" method="post">
            <h2 style="margin-top: 40px;">login to your account</h2>
            <?php foreach ($err as $errMsg): ?>
                <span>• <?= $errMsg . "<br>" ?></span>
            <?php endforeach; ?>
            <div class="input-wrapper" style="margin-top: 40px !important">
                <label for="">Email Address</label>
                <input type="text" name="email" value="<?= $fields['email'] ?>">
            </div>
            <div class="input-wrapper">
                <label for="">Password</label>
                <input type="password" name="password" value="<?= $fields['password'] ?>">
            </div>
            <button name="login" type="submit">
                <span>Login</span>
            </button>
            <div style="margin-top: 30px !important;text-align: center;" class="input-wrapper">
                <span style="color:black">Don't have account? <a href="signup.php">Sign Up</a></span>
            </div>
            
        </form>
    </div>
</body>

</html>