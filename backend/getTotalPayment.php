<?php
    function getTotalPayment($cartItems){
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['qty'];
        }
        return $total;
    }