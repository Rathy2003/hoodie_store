<?php

require("./PHP/authprotection.php");
require "../database/dbcon.php";
$sql = "SELECT * FROM products";
$result = $conn->query($sql);

// search
if (isset($_GET["q"])) {
  $search_query = $_GET['q'];

  if (preg_match("/^[\d\w\s]+$/", $search_query)) {
    $sql = "SELECT * FROM products WHERE LOWER(name) LIKE LOWER('%$search_query%')";
  } else {
    header("location: product.php");
  }
}

// pagination
if (!isset($_GET['page'])) {
  $page = 1;
} else {
  if (is_numeric($_GET['page'])) {
    $page = $_GET['page'];
  } else {
    $page = 1;
  }
}

$results_per_page = 8;
$page_first_result = ($page - 1) * $results_per_page;
$number_of_result = $result->num_rows;
$number_of_page = ceil($number_of_result / $results_per_page);

if (!isset($_GET["q"]))
  $sql = "SELECT * FROM products LIMIT " . $page_first_result . ',' . $results_per_page;
$result = $conn->query($sql);
$result->fetch_all(MYSQLI_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Product</title>
  <link rel="stylesheet" type="text/css" href="CSS/dashboard.css">
  <link rel="stylesheet" type="text/css" href="CSS/table.css">
  <link rel="stylesheet" type="text/css" href="CSS/modal.css">
</head>

<body>

  <!-- add modal -->
  <div class="modal" id="modal1">
    <div class="modal-content">
      <span class="modal-close">&times;</span>
      <h1>Add New Product</h1>
      <form id="add-product-frm">
        <div class="input-wrapper">
          <label>Product Name</label>
          <input type="text" name="name">
        </div>
        <div style="display: flex;gap: 15px;">
          <div class="input-wrapper">
            <label>Price</label>
            <input type="text" name="price">
          </div>
          <div class="input-wrapper">
            <label>Quantity</label>
            <input type="text" name="quantity">
          </div>
        </div>
        <div class="input-wrapper" style="position: relative;">
          <label>Cover</label>
          <img id="delete-choose-img-btn" src="./IMG/xmark-solid.svg">
          <label for="upload-image-file" id="lb-upload-image-file">
          </label>
          <input id="upload-image-file" type="file" accept=".jpg,.jpeg,.png,.webp">
        </div>
        <div style="text-align: right;">
          <button type="submit" class="button button-primary">Add Product</button>
        </div>
      </form>
    </div>
  </div>

  <!-- edit modal -->
  <div class="modal" id="editModal">
    <div class="modal-content">
      <span class="modal-close">&times;</span>
      <h1>Edit Product</h1>
      <form id="edit-product-frm">
        <input type="hidden" name="temp-id">
        <input type="hidden" name="temp-image">
        <div class="input-wrapper">
          <label>Product Name</label>
          <input type="text" name="name">
        </div>
        <div style="display: flex;gap: 15px;">
          <div class="input-wrapper">
            <label>Price</label>
            <input type="text" name="price">
          </div>
          <div class="input-wrapper">
            <label>Quantity</label>
            <input type="text" name="quantity">
          </div>
        </div>
        <div class="input-wrapper" style="position: relative;">
          <label>Cover</label>
          <img id="delete-choose-img-btn" src="./IMG/xmark-solid.svg">
          <label for="upload-image-file" id="lb-upload-image-file">
          </label>
          <input id="upload-image-file" type="file" accept=".jpg,.jpeg,.png,.webp">
        </div>
        <div style="text-align: right;">
          <button type="submit" class="button button-primary">Save Product</button>
        </div>
      </form>
    </div>
  </div>


  <div class="dashboard">
    <!-- Sidebar -->
    <div id="back-blur">
    </div>
    <nav class="sidebar" id="sidebar">
      <!-- header section -->
      <div id="head-section">
        <div id="profile">
            <?php echo strtoupper(substr($_SESSION['backend_email'],0,1)) ?>
        </div>
        <h3><?=$_SESSION['backend_email'];?></h3>
      </div>
      <!-- end head section -->
      <ul class="menu">
        <li><a class="menu-item" href="index.php">Dashboard</a></li>
        <li><a class="menu-item" href="user.php">Users</a></li>
        <li><a class="menu-item" href="order.php">Orders</a></li>
        <li><a class="menu-item active" href="product.php">Products</a></li>
      </ul>
      <a href="./PHP/signout.php" id="sign-out-btn">
          <img style="height: 20px;" src="./IMG/sign_out.svg" alt="">
          <span>Sign out</span>
      </a>

    </nav>

    <!-- Main Content -->
    <div class="main">
      <!-- Top Bar -->
      <header class="topbar">
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

        <div class="search-bar">
          <form id="search-frm" method="GET">
            <input type="text" id="search-bar" name="q" placeholder="Search product here...">
          </form>
        </div>
        <button type="submit" form="search-frm" id="btn-search" style="padding: 0 23px;text-decoration: none;font-size: 15px;color: white;">
          Search
        </button>
        <button data-modal-target="modal1" id="btn-add-product">
          <img src="IMG/plus-solid.svg">
        </button>
      </header>

      <!-- content -->
      <section id="content" style="position: relative;height: 100%;">
        <?php if ($result->num_rows > 0): ?>
          <table>
            <caption>Product Listing</caption>
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Quantity</th>
                <th scope="col">Price</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($result as $product): ?>
                <tr>
                  <td data-label="#"><?= $product["id"] ?></td>
                  <td data-label="Name"><?= $product["name"] ?></td>
                  <td data-label="Quantity"><?= $product["quantity"] ?></td>
                  <td data-label="Price"><?= $product["price"] . " $" ?></td>
                  <td data-label="Actions" style="min-width: 160px;">
                    <button data-modal-target="editModal" data-product="<?= htmlspecialchars(json_encode($product), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-edit">Edit</button>
                    <button class="btn btn-delete" data-id="<?= $product['id']; ?>">Edit</button>
                  </td>
                </tr>
              <?php endforeach;
              $result->free_result();
              $conn->close(); ?>
            </tbody>
          </table>
        <?php else: ?>
          <span style="font-size: 25px;font-weight: bold;color:red;position: absolute;top: 50%;left: 50%;transform: translate(-50%,-50%);">No Record. Add First.</span>
        <?php endif; ?>

        <?php if ($number_of_result >= $results_per_page && !isset($_GET["q"])): ?>
          <div style="text-align: center;margin-top: 18px;">
            <div class="pagination">
              <?php if ($page != 1): ?>
                <a href="product.php?page=<?= $page - 1 ?>">&laquo;</a>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $number_of_page; $i++): ?>
                <a href="product.php?page=<?= $i ?>" class="<?= $page == $i ? 'active' : '' ?>"><?= $i ?></a>
              <?php endfor; ?>
              <?php if ($page < $number_of_page): ?>
                <a href="product.php?page=<?= $page + 1 ?>">&raquo;</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

      </section>

    </div>
  </div>
  
  <script type="text/javascript" src="JS/dashboard.js"></script>
  <script type="text/javascript" src="JS/modal.js"></script>
  <script type="text/javascript" src="JS/product.js"></script>
</body>

</html>