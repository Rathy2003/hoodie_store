<?php
session_start();
if(isset($_SESSION["backend_id"]) && isset($_SESSION["backend_email"]) && isset($_SESSION['backend_username']) && isset($_SESSION["backend_role"])){
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
                        
            //  insert to database 
            require_once "../database/dbcon.php";



            if ($conn->connect_errno > 0) {
                echo "Connection failed: " . $conn->connect_error;
                exit();
            }

            try {
                $sql = $conn->prepare("SELECT concat(firstname,' ',IFNULL(lastname,'')) as fullname,id,role,email,password FROM users WHERE email=? AND password = ? AND role = 'admin'");
                $pass = md5($_POST["password"]);
                $sql->bind_param('ss',$email, $pass);
                if ($sql->execute()) {
                    $result = $sql->get_result();
                    if($result->num_rows > 0){ // mean login successfull
                        $row = $result->fetch_assoc();
                        $username = $row["fullname"];
                        // for use backend site
                        session_regenerate_id(true); 

                        $_SESSION["backend_email"] = $email;
                        $_SESSION["backend_username"] = $username;
                        $_SESSION["backend_role"] = $row["role"];
                        $_SESSION["backend_id"] = $row["id"];

                        return header("location: index.php");

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
    <link rel="stylesheet" href="../CSS/signup.css">
</head>

<body>
    <div id="container">

        <form  action="" method="post" style="aspect-ratio: 2/2.5;">
            <h2 style="margin-top: 40px;">login to dashboard</h2>
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
        </form>
    </div>
</body>
</html>