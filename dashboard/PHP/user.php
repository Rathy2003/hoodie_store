<?php

if($_SERVER["REQUEST_METHOD"] == "POST"){ // add user
    if(isset($_POST["fname"]) && isset($_POST["lname"]) && isset($_POST["email"]) && isset($_POST["password"]) && isset($_POST["role"])){
        $fname = trim($_POST["fname"]);
        $lname = trim($_POST["lname"]) != "" ? trim($_POST["lname"]) : NULL;
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);
        $role = strtolower(trim($_POST["role"]));

        $fullname = strtolower($fname).strtolower($lname);

        // validate
        if(!filter_var($email,FILTER_VALIDATE_EMAIL)) {
            echo json_encode(["status" => 400 , "message" =>"Please provide valid email address."]);
            exit();
        }

        if(empty($email) || empty($fname) ||  empty($password)) {
            echo json_encode(["status" => 400 , "message" =>"All field are required."]);
            exit();
        }

        if (strlen($password) < 8) {
            echo json_encode(["status" => 400, "message" => "Password must be at least 8 characters long."]);
            exit();
        }

        $allowedRoles = ["admin","user"];

        if(!in_array($role, $allowedRoles)) {
            echo json_encode(["status" => 400 , "message" =>"Invalid Role."]);
            exit();
        }

        require("../../database/dbcon.php");
        $sql = "SELECT CONCAT(firstname,IFNULL(lastname,'')),email FROM users WHERE email = ? OR   LOWER(CONCAT(firstname,IFNULL(lastname,''))) = ?";
        $stmp = $conn->prepare($sql);
        $stmp->bind_param("ss",$email,$fullname);
        $stmp->execute();
        $result = $stmp->get_result();
        if($result->num_rows > 0){
            echo json_encode(["status" => 400 , "message" =>"User already exists."]);
            $stmp->close();
            $conn->close();
            exit();
        }

        // insert to database
        $sql = "INSERT INTO users (firstname,lastname,email,password,role) VALUES (?,?,?,?,?)";
        $stmp = $conn->prepare($sql);
        $hashedPassword = md5($password);
        $stmp->bind_param("sssss", $fname, $lname, $email, $hashedPassword,$role);
        $stmp->execute();
        $stmp->close();
        $conn->close();
        echo json_encode(["status" => 200 , "message" =>"User create successfull."]);
        
    }

    if(isset($_POST["deleteId"])){ // delete user
        $userId = $_POST["deleteId"];

        if(!preg_match("/^[\d]+$/",$userId)){
            echo json_encode(["status" => 400, "message" => "Invalid user id."]);
            exit();
        }
        require("../../database/dbcon.php");

        $sql = "SELECT id FROM orders WHERE user_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i",$userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0){
            echo json_encode(["status" => 400, "message" => "Can't Delete.This User has related ". $result->num_rows." orders."]);
            $stmt->close();
            $conn->close();
            exit();
        }

        $sql = "DELETE FROM users WHERE id =?";
        $stmp = $conn->prepare($sql);
        $stmp->bind_param("i", $userId);
        $stmp->execute();
        $stmp->close();
        $conn->close();
        echo json_encode(["status" => 200, "message" => "User deleted successfully."]);
    }

    if(isset($_POST["userId"])){ // edit user
        if(isset($_POST['firstname']) && isset($_POST['lastname']) && isset($_POST['email']) && isset($_POST['role']) && isset($_POST['isChangePassword'])){
            
            $userId = $_POST["userId"];
            $firstname = trim($_POST['firstname']);
            $lastname = trim($_POST['lastname']) == ""?NULL : trim($_POST['lastname']);
            $email = trim($_POST['email']);
            $role = strtolower(trim($_POST['role']));
            $changePasswordStatus = $_POST["isChangePassword"];

            if(empty($email) || empty($firstname) || empty($role)) {
                echo json_encode(["status" => 400, "message" => "All field are required."]);
                exit();
            }

            if(!filter_var($email,FILTER_VALIDATE_EMAIL)) {
                echo json_encode(["status" => 400, "message" => "Please provide valid email address."]);
                exit();
            }
            $allowedRoles = ["admin","user"];
            if(!in_array($role, $allowedRoles)) {
                echo json_encode(["status" => 400, "message" => "Invalid Role."]);
                exit();
            }

            require("../../database/dbcon.php");

            $sql = "SELECT * FROM users WHERE id<>? AND (LOWER(email) = ? OR LOWER(firstname) = ?)";
            $stmp = $conn->prepare($sql);

            $newEmail = strtolower($email);
            $newFirstName = strtolower($firstname);

            $stmp->bind_param("iss",$userId, $newEmail,$newFirstName);
            $stmp->execute();

            unset($newEmail,$newFirstName);

            $result = $stmp->get_result();
            if($result->num_rows > 0){ // firstname or email  already exist.
                $stmp->close();
                $conn->close();
                echo json_encode(["status" => 400, "message" => "Firstname or email is already exist."]);
                exit();
            }

            if($changePasswordStatus == 'true'){
                $password = trim($_POST['password']);

                if (strlen($password) < 8) {
                    echo json_encode(["status" => 400, "message" => "Password must be at least 8 characters long."]);
                    exit();
                }

                $hashedPassword = md5($password);
                $sql = "UPDATE users SET firstname=?, lastname=?, email=?, role=?, password=? WHERE id=?";
                $stmp = $conn->prepare($sql);
                $stmp->bind_param("sssssi", $firstname, $lastname, $email, $role, $hashedPassword, $userId);
                $stmp->execute();
                $stmp->close();
                $conn->close();
                echo json_encode(["status" => 200, "message" => "User updated successfully with password change."]);
            }else{
                $sql = "UPDATE users SET firstname=?, lastname=?, email=?, role=? WHERE id=?";
                $stmp = $conn->prepare($sql);
                $stmp->bind_param("ssssi", $firstname, $lastname, $email, $role, $userId);
                $stmp->execute();
                $stmp->close();
                $conn->close();
                echo json_encode(["status" => 200, "message" => "User updated successfully."]);
            }
        }
    }
}
?>