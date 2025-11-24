<?php
	
	if(isset($_POST["id"]) && isset($_POST['name']) && isset($_POST['price']) && isset($_POST['quantity'])){
        require("../../database/dbcon.php");

		$name = $_POST['name'];
		$quantity = $_POST['quantity'];
		$price = $_POST['price'];
		$id = $_POST["id"];
		$old_image = $_POST["old_image"];
		$edit_image_status = $_POST["edit_image_status"];

		if(empty($name) || empty($quantity) || empty($price)){
			echo json_encode(["status" => 400,"message" => "All fields are required."]);
			die();
		}

		// check product exist or not
		$stmp = $conn->prepare("SELECT name FROM products WHERE LOWER(name)=? AND id <> ?");
		$lowerName = strtolower($name);
		$stmp->bind_param("si",$lowerName,$id);
		$stmp->execute();
		$result = $stmp->get_result();
		if($result->num_rows > 0){// mean product name is exist
			$conn->close();
			echo json_encode(["status" => 400,"message" => "Product with this name is already exist."]);
			exit();
		}

		if($edit_image_status == "null"){ // mean image not update
			$sql =  $conn->prepare("UPDATE products SET name=?,price=?,quantity=? WHERE id=?");
			$sql->bind_param("sssi",$name,$price,$quantity,$id);
			if($sql->execute()){
				echo json_encode(["status" => 200,"message" => "Product has been update successfully"]);
			}
		}else{
			// mean it valid
			if(unlink("../../IMG/products/".$old_image)){
				$filename = $_FILES['file']["name"];
				$newName = time().substr($filename,strrpos($filename,"."));
				if(move_uploaded_file($_FILES['file']['tmp_name'], "../../IMG/products/".$newName)){
					// $file = $_FILES['file'];
					$sql =  $conn->prepare("UPDATE products SET name=?,price=?,quantity=?,image=? WHERE id=?");
					$sql->bind_param("ssssi",$name,$price,$quantity,$newName,$id);
					if($sql->execute()){
						echo json_encode(["status" => 200,"message" => "Product has been update successfully"]);
					}
				}
			}
		}
		$conn->close();
	}else if(isset($_POST['name']) && isset($_POST['price']) && isset($_POST['quantity']) && isset($_FILES['file'])){

        require("../../database/dbcon.php");

		$name = $_POST['name'];
		$quantity = $_POST['quantity'];
		$price = $_POST['price'];

		if(empty($name) || empty($quantity) || empty($price)){
			echo json_encode(["status" => 400,"message" => "All fields are required."]);
			die();
		}

		// check product exist or not
		$stmp = $conn->prepare("SELECT name FROM products WHERE LOWER(name)=?");
		$lowerName = strtolower($name);
		$stmp->bind_param("s",$lowerName);
		$stmp->execute();
		$result = $stmp->get_result();
		if($result->num_rows > 0){// mean product name is exist
			$conn->close();
			echo json_encode(["status" => 400,"message" => "Product with this name is already exist."]);
			exit();
		}

		$filename = $_FILES['file']["name"];
		$newName = time().substr($filename,strrpos($filename,"."));

		// mean it valid

		if(move_uploaded_file($_FILES['file']['tmp_name'], "../../IMG/products/".$newName)){
			// $file = $_FILES['file'];
			$sql =  $conn->prepare("INSERT INTO products (name,price,quantity,image) VALUES(?,?,?,?)");
			$sql->bind_param("ssss",$name,$price,$quantity,$newName);
			if($sql->execute()){
				echo json_encode(["status" => 200,"message" => "Product has been added successfully"]);
			}
			$conn->close();
		}
	}else if(isset($_POST['id'])){
		$id = $_POST["id"];
		if(!preg_match("/^[\d]+$/", $id)){
			echo json_encode(["status" => 400,"message" => "Product id must be number."]);
			exit();
		}
        require("../../database/dbcon.php");

		$sql = $conn->prepare("SELECT image FROM products WHERE id=?");
		$sql->bind_param("i",$id);
		$sql->execute();
		$sql->bind_result($image);
		if($sql->fetch()){
			$sql->close();
			if(unlink("../../IMG/products/".$image)){
				$stmp = $conn->prepare("DELETE FROM products WHERE id=?");
				$stmp->bind_param("i",$id);
				$stmp->execute();
				$affectedRows = $stmp->affected_rows;
				if($affectedRows > 0){
					echo json_encode(["status" => 200,"message" => "Product Has been deleted."]);
				}else{
				
				}
				$stmp->close();
				$conn->close();
			}
		}else{
			echo json_encode(["status" => 200,"message" => "No Product has been deleted."]);
		}
		

		
	}else{
		echo json_encode(["status" => 400,"message" => "All fields are required."]);
	}

?>