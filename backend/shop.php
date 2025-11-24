<?php 
    $cartItems = [];
    $count = '';
    $total = 0;
    
    if (isset($_SESSION['cartItems'])){
        $cartItems = $_SESSION['cartItems'];
        $count = count($_SESSION['cartItems']);
        $total = getTotalPayment($cartItems);
    }
    
    if (isset($_POST['submit'])){
        $checkExistedItem = false;
        
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $img = $_POST['img'];
        
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
    }

    if (isset($_POST['deleteItem'])){
        $key = $_POST['key'];

        unset($cartItems[$key]);
        $cartItems = array_values($cartItems);
        $_SESSION['cartItems'] = $cartItems;
        $count = count($cartItems);
        $total = getTotalPayment($cartItems);
    }

    function getTotalPayment($cartItems){
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['qty'];
        }
        return $total;
    }