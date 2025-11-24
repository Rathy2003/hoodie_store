<?php
	session_start();
	if(isset($_SESSION["id"]) && isset($_SESSION["email"]) && isset($_SESSION['username']) && isset($_SESSION["role"])){
		require "shop.php";
		require "../database/dbcon.php";
		if(count($cartItems) > 0){

			$totalPayment = getTotalPayment($cartItems);
			$sql = "INSERT INTO orders (total,user_id) VALUES(?,?)";
			$stmt = $conn->prepare($sql);
			$user_id = $_SESSION['id'];

			if($stmt){
				$stmt->bind_param("di",$totalPayment,$user_id);
				$stmt->execute();
				$stmt->close();

				$last_insert_id = $conn->insert_id;
				foreach($cartItems as $cart){
					$pId = $cart["id"];
					$qty = $cart["qty"];
					$price = $cart["price"];

					$sql = "INSERT INTO orderdetails (id,product_id,qty,subprice) VALUES(?,?,?,?)";
					$stmt = $conn->prepare($sql);
					if($stmt){
						$stmt->bind_param("iiid",$last_insert_id,$pId,$qty,$price);
						$stmt->execute();
						$stmt->close();
					}
				}

				unset($_SESSION['cartItems']);
				$conn->close();
                $data = ["id" => $last_insert_id,"date" => date("d-M-Y"),"total" => $totalPayment];
                echo json_encode(["status" => 200,"data" => $data,"message" => "checkout successfully."]);
			}


		}else{
            echo json_encode(["status" => 404,"message" => "no cart items."]);
        }
	}else{
//		header("location: ../index.php");
        echo json_encode(["status" => 400,"message" => "please add to cart first."]);
	}
?>