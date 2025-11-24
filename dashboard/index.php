<?php
require "../database/dbcon.php";
require "PHP/authprotection.php";

$totalProduct = 0;
$totalOrder = 0;
$topProducts = [];

// total product
$sql = "SELECT COUNT(id) as total FROM products";
$result = $conn->query($sql);
if ($result) {
  $row =  $result->fetch_assoc();
  $totalProduct = $row["total"];
}

// total order
$sql = "SELECT COUNT(id) as total FROM orders";
$result = $conn->query($sql);
if ($result) {
  $row =  $result->fetch_assoc();
  $totalOrder = $row["total"];
}

// top product
$sql = "SELECT COUNT(product_id) as total,name FROM `orderdetails` as o JOIN products as p on p.id =o.id GROUP BY name ORDER BY total DESC LIMIT 5";
$result = $conn->query($sql);
if ($result) {
  $topProducts =  $result->fetch_all(MYSQLI_ASSOC);
};

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard</title>
  <link rel="stylesheet" type="text/css" href="CSS/dashboard.css">
  <link rel="stylesheet" type="text/css" href="CSS/table.css">
    <link rel="stylesheet" type="text/css" href="CSS/modal.css">
</head>

<body>

  <div class="dashboard">
    <!-- Sidebar -->
    <div id="back-blur">
    </div>
    <nav class="sidebar" id="sidebar">
      <!-- header section -->
      <div id="head-section">
        <div id="profile">
          <?php echo strtoupper(substr($_SESSION['backend_email'], 0, 1)) ?>
        </div>
        <h3><?= $_SESSION['backend_email']; ?></h3>
      </div>
      <!-- end head section -->
      <ul class="menu">
        <li><a class="menu-item active" href="index.php">Dashboard</a></li>
        <li><a class="menu-item" href="user.php">Users</a></li>
        <li><a class="menu-item" href="order.php">Orders</a></li>
        <li><a class="menu-item" href="product.php">Products</a></li>
      </ul>
      <a href="./PHP/signout.php" id="sign-out-btn">
        <img style="height: 20px;" src="./IMG/sign_out.svg" alt="">
        <span>Sign out</span>
      </a>

    </nav>

    <!-- Main Content -->
    <div class="main">
      <!-- Top Bar -->
      <header class="topbar" style="justify-content: flex-start">
        <div>
          <label for="chb-hamburger-menu">
            <div class="hamburger-menu" id="hamburger-menu">
              <div></div>
              <div></div>
              <div></div>
            </div>
          </label>
          <input id="chb-hamburger-menu" type="checkbox" name="">
        </div>
        <h2>Welcome back , <?php echo strtoupper(substr($_SESSION["backend_username"],0,strpos($_SESSION["backend_username"]," ")))?></h2>

      </header>
      <!-- Cards -->
      <section class="cards">
        <div class="card">
          <h3>Total Product</h3>
          <p><?= $totalProduct ?></p>
          <a href="product.php" class="card-link">View Product →</a>
        </div>
        <div class="card">
          <h3>Total Orders</h3>
          <p><?= $totalOrder ?></p>
          <a href="order.php" class="card-link">View Order →</a>
        </div>
        <div class="card">
          <h3>Total Users</h3>
          <p><?= $totalOrder ?></p>
          <a href="user.php" class="card-link">View Users →</a>
        </div>
      </section>

      <!-- Chart -->
      <?php if (count($topProducts) >= 5): ?>
        <h4 style="text-align: center;">Top Order Product</h4>
        <section id="chart">
          <div class="simple-bar-chart">
            <?php foreach (array_reverse($topProducts) as $topProduct): ?>
              <div class="item" style="--clr: #069CDB; --val: <?= (int)($topProduct["total"] * 100 / $topProducts[0]["total"]) ?>">
                <div class="label"><?= $topProduct["name"] ?></div>
                <div class="value"><?= (int)($topProduct["total"] * 100 / $topProducts[0]["total"]) ?>%</div>
              </div>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endif; ?>

    </div>
  </div>
  
  <script type="text/javascript" src="JS/dashboard.js"></script>

</body>

</html>