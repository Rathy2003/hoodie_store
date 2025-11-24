<?php

	if(isset($_POST['id'])){
        require("../../database/dbcon.php");

		$id = $_POST['id'];
		if(!is_numeric($id)){
			echo json_encode(["status" => 400 , "message" => "id must be number."]);
			exit();
		}


		$sql = "SELECT o.id,CONCAT(firstname,' ',IFNULL(lastname,'')) AS fullname,name as item,SUM(qty) as 	quantity,DATE_FORMAT(date,'%d %b %Y') as date,subprice as price,total
				FROM orders as o JOIN orderdetails as od ON o.id = od.id JOIN products as p ON p.id = od.product_id JOIN users as u on u.id = o.user_id WHERE o.id=? GROUP BY od.product_id";
		$stmt = $conn->prepare($sql);
		$stmt->bind_param("i",$id);
		if(!$stmt){
			echo json_encode(["status" => 500 , "message" => "query error."]);
			exit();
		}
		$stmt->execute();
		$result =  $stmt->get_result();
		if($result->num_rows == 0){
			echo json_encode(["status" => 404 , "message" => "order detail with this id not found."]);
		}

		$row = $result->fetch_all(MYSQLI_ASSOC);

		$data = [];

		foreach ($row as $item) {
			$data[] = $item;
		}

		$stmt->close();
		$conn->close();
		$result->free();
		echo json_encode(["status" => 200 ,"data" => $data ,"message" => "successfull."]);

	}

?>