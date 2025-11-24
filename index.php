<?php 

    session_start();
    $isLogin = false;
    $role = "user";
    if(isset($_SESSION["id"]) && isset($_SESSION["email"]) && isset($_SESSION['username']) && isset($_SESSION["role"])){
        $isLogin = true;
        $role = $_SESSION['role'];
    }
    require ('backend/shop.php');
    // unset($_SESSION['cartItems']);
    require './database/dbcon.php';
    $sql = "SELECT * FROM products ORDER BY id DESC";
    if(isset($_GET["q"])){
        $search_query = mysqli_real_escape_string($conn,trim($_GET['q']));
        if(!preg_match("/^[a-zA-Z0-9\s]+$/", $search_query)){
            header("location: index.php");
        }
        if(empty($search_query)){
            header("location: index.php");
        }else{
            $search_query = strtolower($search_query);
            $sql = "SELECT * FROM products WHERE LOWER(name) LIKE LOWER('%$search_query%')  ORDER BY id DESC";
        }
    }
    $data = $conn->query($sql);
    $data->fetch_all(MYSQLI_ASSOC);
    $conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoddie Store</title>
    <link rel="stylesheet" href="CSS/store.css">
</head>
<body>
    <?php if(isset($_SESSION['backend_role']) && $_SESSION['backend_role'] == "admin"):?>
        <div style="background: black;min-height: 65px;padding: 0 30px;display: flex;justify-content: space-between;align-items: center;">
             <h4 style="color: white;">Welcome, <?=$_SESSION['backend_email']?></h4>
            <button class="btn btn-secondary"><a href="dashboard/index.php" target="_blank">Go Dashboard</a></button>
        </div>
    <?php endif; ?>

    <!-- Start Search modal -->
     <dialog id="search-modal" style="user-select: none;">
        <form method="GET" style="display: flex;align-items: center;gap: 10px;">
            <input type="text" name="q" placeholder="Search for products...">
            <button type="submit" class="btn btn-primary" style="color: black;background-color: white;" id="btn-search">Search</button>
        </form>
     </dialog>
     <!-- End Search modal -->


    <!--  Start Order Detail Modal  -->
    <dialog id="order-detail-modal">
        <div id="right-icon"></div>
        <p>Thank you for order.</p>
        <div id="order-detail-container">
            <div class="order-detail-wrapper">
                <span>OrderID</span>
                <span></span>
            </div>
            <div class="order-detail-wrapper">
                <span>Date</span>
                <span></span>
            </div>
            <div class="order-detail-wrapper">
                <span>Discount</span>
                <span>0%</span>
            </div>
            <div class="order-detail-wrapper">
                <span>Total Payment</span>
                <span></span>
            </div>
        </div>

        <button id="btn-close-order-modal">Close</button>
    </dialog>
    <!-- End Order Detail Modal -->

    <!-- Start Content -->
    <div>
        <div class="wrapper">
            <div class="modal-content">
                <!-- <button class="modal-close">×</button> -->
                <div class="header">
                    <h2 class="modal-title">Hoodies</h2>
                    <div class="modal-actions">
                        <button class="btn btn-secondary" id="cart" onclick="openCart()">
                            <span class="item-count" id="itemCount"><?=$count != 0 ? $count : ''?></span>
                            View cart
                        </button>
                        <button class="btn btn-primary" id="show-search-modal-btn">Search</button>
                       <?php if($isLogin) :?>
                            <a href="./backend/logout.php" class="btn btn-outline-primary" style="text-decoration: none;">Logout</a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-outline-primary" style="text-decoration: none;">Login</a>
                            <a href="signup.php" class="btn btn-outline-primary" style="text-decoration: none;">Sign Up</a>
                        <?php endif; ?>
                    </div>
                </div>
    
                <div class="product-grid">

                    <?php 
                        foreach ($data as $key => $item){
                    ?>
                    <div class="product-card">
                        <div>
                            <div class="product-image" id="product-image-<?=$key?>">
                                <img class="product-img" src="IMG/products/<?=$item['image']?>" alt="<?=$item['name']?>">
                            </div>
                            <h3 class="product-title"><?=$item['name']?></h3>
                            <p class="product-price">$ <?=number_format($item['price'], 2, '.', ',')?></p>
                            <button class="btn btn-primary add-to-cart" data-key="<?=$key?>" data-id="<?=$item['id']?>" data-title="<?=$item['name']?>" data-price="<?=$item['price']?>" data-img="IMG/products/<?=$item['image']?>">Add to cart</button>
                        </div>
                    </div>
                    <?php 
                        }
                        $data->free();
                    ?>
                </div>

            </div>
        </div>
    </div>
    <!-- End Content -->

    <!-- Start View Carts Modal  -->
    <div class="bg" id="bg" style="display: none;"></div>
    <div class="container" id="modal">
        <h1 class="page-title">Cart</h1>
        <button class="modal-close" onclick="closeCart()">×</button>
        <div class="cart-layout">
            <div class="cart-items" id="cartItems">
            <!-- One Item  -->
            <?php 
                if (count($cartItems) > 0){
                    foreach ($cartItems as $key => $item){
            ?>
                <div class="cart-item" id="item-<?=$key?>">
                    <!-- <input type="hidden" name="key" value="<?=$key?>"> -->
                    <button class="remove-item" onclick="removeItem(<?=$key?>)">×</button>
                    <div class="item-details">
                        <div class="item-image-side">
                            <img src="<?=$item['img']?>" alt="">
                        </div>
                        <div class="item-info-side">
                            <span class="item-title"><?=$item['name']?></span>
                            <span class="item-price">$ <?=number_format($item['price'], 2, '.', ',')?></span>
                        </div>
                    </div>
                    <div class="item-quantity"><?=$item['qty']?></div>
                </div>
            <?php 
                    }
                }
                else{
                    echo "<span class='empty-cart'>Cart is Empty</span>";
                }
            ?>
            <!-- End of One Item  -->
                
            </div>

            <div class="cart-summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="subtotal">$ <?=number_format($total, 2, '.', ',')?></span>
                </div>
                <!-- <div class="coupon-form">
                    <input type="text" class="coupon-input" placeholder="Coupon code">
                    <button class="btn btn-secondary">Update cart</button>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <button class="btn btn-secondary">Calculate shipping</button>
                </div> -->
                <div class="summary-row total-row">
                    <span>Total</span>
                    <span id="total">$ <?=number_format($total, 2, '.', ',')?></span>
                </div>
                <button id="btn-checkout" class="proceed-btn">
                    Proceed to checkout
                    <span>→</span>
                </button>
            </div>
        </div>
    </div>
    <!-- End View Carts Modal -->

    <script>
        const loginStatus = "<?php echo $isLogin ? 'true' : 'false'; ?>";
    </script>

    <script src="./javascript/store.js"></script>
</body>
</html>
