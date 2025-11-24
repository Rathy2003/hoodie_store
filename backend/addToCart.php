<?php 
    include 'getTotalPayment.php';
    
    session_start();
    $cartItems = [];
    $count = '';
    $total = 0;

    // Get JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    
    if (isset($_SESSION['cartItems'])){
        $cartItems = $_SESSION['cartItems'];
        $count = count($_SESSION['cartItems']);
        $total = getTotalPayment($cartItems);
    }
    

    $checkExistedItem = false;
    
   // Extract data
    $id = $data['id'];
    $name = $data['title'];
    $price = $data['price'];
    $img = $data['img'];
    

    foreach ($cartItems as $key => $item) {
        if ($item['id'] == $id) {
            $cartItems[$key]['qty'] += 1;
            $checkExistedItem = true;
            break; // Optional: stop looping once you've found the item
        }
    }
    
    if (!$checkExistedItem){
        $cartItems[] = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'img' => $img,
            'qty' => 1
        ];
    }

    $count = count($cartItems);
    $_SESSION['cartItems'] = $cartItems;
    $total = getTotalPayment($cartItems);    


    // Send JSON response back to the client
    echo json_encode([
        'id' => $id,
        'title' => $name,
        'price' => $price,
        'img' => $img,
        'qty' => $checkExistedItem ? $cartItems[$key]['qty'] : 1,
        'count' => $count,
        'total' => $total,
        'cartItems' => $cartItems
    ]);

