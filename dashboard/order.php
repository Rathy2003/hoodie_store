<?php

require("./PHP/authprotection.php");
require "../database/dbcon.php";
error_reporting(E_ALL ^ E_DEPRECATED);

function checkQueryParam()
{
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
  $currentUrl = $scheme . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  $query = parse_url($currentUrl, PHP_URL_QUERY);
  parse_str($query, $queryParam);

  foreach ($queryParam as $key => $value) {
    if (trim($value) == "") {
      unset($queryParam[$key]);
    }
  }

  $newQueryString = http_build_query($queryParam);
  $newUrl = strtok($currentUrl, '?');
  if (!empty($newQueryString)) {
    $newUrl .= '?' . $newQueryString;
  }
  if ($currentUrl != $newUrl) {
    return header("Location: $newUrl");
  }
}

checkQueryParam();


$sql = "SELECT o.id,COUNT(product_id) as 'total_product',SUM(qty) as 'total_qty',total FROM `orders` as o JOIN orderdetails as od ON o.id = od.id GROUP BY o.id;";
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

$filterPrice = "";
// sort
if (isset($_GET['price'])) {
  if (strtolower($_GET['price']) === "highest" || strtolower($_GET['price']) === "lowest") {
    $filterPrice = strtolower($_GET['price']);
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
  if ($filterPrice == "lowest") {
    $sql = "SELECT o.id,DATE_FORMAT(date,'%d-%m-%Y %h:%i %p') as date,COUNT(product_id) as 'total_product',SUM(qty) as 'total_qty',total FROM `orders` as o JOIN orderdetails as od ON o.id = od.id GROUP BY o.id ORDER BY total LIMIT " . $page_first_result . ',' . $results_per_page;
  } else if ($filterPrice == "highest") {
    $sql = "SELECT o.id,DATE_FORMAT(date,'%d-%m-%Y %h:%i %p') as date,COUNT(product_id) as 'total_product',SUM(qty) as 'total_qty',total FROM `orders` as o JOIN orderdetails as od ON o.id = od.id GROUP BY o.id ORDER BY total DESC LIMIT " . $page_first_result . ',' . $results_per_page;
  } else {
    $sql = "SELECT o.id,DATE_FORMAT(date,'%d-%m-%Y %h:%i %p') as date,COUNT(product_id) as 'total_product',SUM(qty) as 'total_qty',total FROM `orders` as o JOIN orderdetails as od ON o.id = od.id GROUP BY o.id  LIMIT " . $page_first_result . ',' . $results_per_page;
  }

$startDate = "";
$endDate = "";

// filter by date
if (isset($_GET['start'])) {

  $startDate = formatDate($_GET['start']);
  $endDate = isset($_GET['end']) && $_GET['end'] != "" ? formatDate($_GET['end']) : "";

  // fetch to get pagination results
  $sql = "SELECT o.id,date,COUNT(product_id) as 'total_product',SUM(qty) as 'total_qty',total FROM `orders` as o JOIN orderdetails as od ON o.id = od.id WHERE date BETWEEN '$startDate' AND '$endDate' GROUP BY o.id";
  if ($endDate == "") {
    $sql = "SELECT o.id,date,COUNT(product_id) as 'total_product',SUM(qty) as 'total_qty',total FROM `orders` as o JOIN orderdetails as od ON o.id = od.id WHERE date >= '$startDate' GROUP BY o.id";
  }
  $result = $conn->query($sql);
  $number_of_result = $result->num_rows;
  $number_of_page = ceil($number_of_result / $results_per_page);

  $sql = "SELECT o.id,DATE_FORMAT(date,'%d-%m-%Y %h:%i %p') as date,COUNT(product_id) as 'total_product',SUM(qty) as 'total_qty',total FROM `orders` as o JOIN orderdetails as od ON o.id = od.id WHERE date BETWEEN '$startDate' AND '$endDate' GROUP BY o.id " . ($filterPrice === "" ? "" : ($filterPrice === "lowest" ? "ORDER BY total" : "ORDER BY total DESC")) . " LIMIT " . $page_first_result . ',' . $results_per_page;
  if ($endDate == "") {
    $sql = "SELECT o.id,DATE_FORMAT(date,'%d-%m-%Y %h:%i %p') as date,COUNT(product_id) as 'total_product',SUM(qty) as 'total_qty',total FROM `orders` as o JOIN orderdetails as od ON o.id = od.id WHERE date >= '$startDate' GROUP BY o.id " . ($filterPrice === "" ? "" : ($filterPrice === "lowest" ? "ORDER BY total" : "ORDER BY total DESC")) . " LIMIT " . $page_first_result . ',' . $results_per_page;
  }
}

function formatDate($strDate)
{ // format date  to "yyyy-MM-dd"
  $dateObject = DateTime::createFromFormat('d-m-Y', $strDate);
  if ($dateObject) {
    $outputDate = $dateObject->format('Y-m-d');
    return $outputDate;
  } else {
    return false;
  }
}

function formartDateReverse($strDate)
{ // formart date to "dd - mm - yy"
  $dateObject = DateTime::createFromFormat('Y-m-d', $strDate);
  if ($dateObject) {
    $outputDate = $dateObject->format('d-m-Y');
    return $outputDate;
  } else {
    return false;
  }
}

$result = $conn->query($sql);
$result->fetch_all(MYSQLI_ASSOC);


?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard - Order</title>
  <link rel="stylesheet" type="text/css" href="CSS/dashboard.css">
  <link rel="stylesheet" type="text/css" href="CSS/table.css">
  <link rel="stylesheet" type="text/css" href="CSS/modal.css">
</head>

<body>
  <!-- view modal -->
  <div class="modal" id="viewModal">
    <div class="modal-content" style="width: 585px;">
      <span class="modal-close">&times;</span>
      <br>
      <div id="order-detail-container">
        <div style="display: flex;justify-content: space-between;">
          <span id="order-id-label"><b>OrderID: </b>#001</span>
          <span id="order-date-label"><b>Date: </b>27 May 2025</span>
        </div>
        <div id="customer-name-label"><b>Customer Name: </b>John Doe</div>
        <br>
        <!-- head -->
        <section id="order-detail-header">
          <div>ITEM</div>
          <div>QUANTITY</div>
          <div>PRICE</div>
          <div>AMOUNT</div>
        </section>
        <section id="order-detail-content">
          <div class="order-detail-item">
            <div>IPhone 16 Pro Max</div>
            <div>x2</div>
            <div>1000 $</div>
            <div>2000 $</div>
          </div>
        </section>
        <section id="order-detail-footer">
          <div>TOTAL</div>
          <div id="total-amount-label">15000 $</div>
        </section>
      </div>

      <br>
      <div style="text-align: right">
        <button class="btn" id="btn-close-view-order" style="padding-left: 25px;padding-right: 25px">Close</button>
      </div>

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
        <li><a class="menu-item active" href="order.php">Orders</a></li>
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
      <header class="topbar" style="justify-content: flex-start;">
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

        <!-- filter -->
        <div id="filter-order-wrapper">
          <!-- date filter -->
          <form method="get">
            <input class="input-date-range" name="start" type="text" value="<?php echo formartDateReverse($startDate) ?>" id="" placeholder="start from dd-mm-yyy" <?php if (!isset($_GET["start"])) echo "required" ?> <?php if($result->num_rows == 0 && !isset($_GET['start'])) echo 'disabled' ?>>
            <span>-</span>
            <input class="input-date-range" name="end" type="text" id="" value="<?php echo formartDateReverse($endDate) ?>" placeholder="end at dd-mm-yyy" <?php if($result->num_rows == 0 && !isset($_GET['start'])) echo 'disabled' ?>>
            <input class="btn btn-outline-dark" type="submit" value="Search" <?php if($result->num_rows == 0 && !isset($_GET['start'])) echo 'disabled' ?>>
          </form>
          <!-- end date filter -->

          <!-- price filter -->
          <select id="price-sort" onchange="window.location = this.value" <?php if($result->num_rows == 0) echo 'disabled' ?>>
            <option value="<?php echo "order.php?" . ($page != 1 ? '&page=' . $page : '') . ($startDate != "" ? '&start=' . formartDateReverse($startDate) : '') . ($endDate != "" ? '&end=' . formartDateReverse($endDate) : ''); ?>"
              <?php if ($filterPrice != 'lowest' && $filterPrice != 'highest') {
                echo 'selected="selected"';
              } ?>>None</option>
            <option value="<?php echo "order.php" . '?price=lowest' . ($page != 1 ? '&page=' . $page : '') . ($startDate != "" ? '&start=' . formartDateReverse($startDate) : '') . ($endDate != "" ? '&end=' . formartDateReverse($endDate) : ''); ?>"
              <?php if ($filterPrice == 'lowest') {
                echo 'selected="selected"';
              } ?>>lowest</option>
            <option value="<?php echo "order.php" . '?price=highest' . ($page != 1 ? '&page=' . $page : '') . ($startDate != "" ? '&start=' . formartDateReverse($startDate) : '') . ($endDate != "" ? '&end=' . formartDateReverse($endDate) : ''); ?>"
              <?php if ($filterPrice == 'highest') {
                echo 'selected="selected"';
              } ?>>highest</option>
          </select>
          <!-- end price filter -->
        </div>

      </header>

      <!-- content -->
      <section id="content" style="position: relative;height: 100%;">
        <?php if ($result->num_rows > 0): ?>
          <div style="display: flex;justify-content: flex-end;">
              <!-- price filter -->
            <select id="price-sort-mobile" class="price-sort" onchange="window.location = this.value" <?php if($result->num_rows == 0) echo 'disabled' ?>>
              <option value="<?php echo "order.php?" . ($page != 1 ? '&page=' . $page : '') . ($startDate != "" ? '&start=' . formartDateReverse($startDate) : '') . ($endDate != "" ? '&end=' . formartDateReverse($endDate) : ''); ?>"
                <?php if ($filterPrice != 'lowest' && $filterPrice != 'highest') {
                  echo 'selected="selected"';
                } ?>>None</option>
              <option value="<?php echo "order.php" . '?price=lowest' . ($page != 1 ? '&page=' . $page : '') . ($startDate != "" ? '&start=' . formartDateReverse($startDate) : '') . ($endDate != "" ? '&end=' . formartDateReverse($endDate) : ''); ?>"
                <?php if ($filterPrice == 'lowest') {
                  echo 'selected="selected"';
                } ?>>lowest</option>
              <option value="<?php echo "order.php" . '?price=highest' . ($page != 1 ? '&page=' . $page : '') . ($startDate != "" ? '&start=' . formartDateReverse($startDate) : '') . ($endDate != "" ? '&end=' . formartDateReverse($endDate) : ''); ?>"
                <?php if ($filterPrice == 'highest') {
                  echo 'selected="selected"';
                } ?>>highest</option>
            </select>
            <!-- end price filter -->
          </div>
          <table class="responsive-table">
            <caption>Order Listing</caption>
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col" style="width: 178px;">Date</th>
                <th scope="col">Total Product</th>
                <th scope="col">Total Qty</th>
                <th scope="col">Total</th>
                <th scope="col">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($result as $product): ?>
                <tr>
                  <td data-label="#"><?= $product["id"] ?></td>
                  <td data-label="Date"><?= $product["date"] ?></td>
                  <td data-label="Total Product"><?= $product["total_product"] ?></td>
                  <td data-label="Total Qty"><?= $product["total_qty"] ?></td>
                  <td data-label="Total"><?= $product["total"] . " $" ?></td>
                  <td data-label="Actions">
                    <button data-modal-target="viewModal" class="btn btn-view" data-id="<?= $product["id"] ?>">View</button>
                  </td>
                </tr>
              <?php endforeach;
              $result->free_result();
              $conn->close(); ?>
            </tbody>
          </table>
        <?php else: ?>
          <span style="font-size: 25px;font-weight: bold;color:red;position: absolute;top: 50%;left: 50%;transform: translate(-50%,-50%);">No Record Founded.</span>
        <?php endif; ?>

        <?php if ($number_of_result >= $results_per_page && !isset($_GET["q"])): ?>
          <div style="text-align: center;margin-top: 18px;">
            <div class="pagination">
              <?php if ($page != 1): ?>
                <a href="order.php?page=<?= ($page - 1) . ($filterPrice != 'lowest' ? '&price=' . $filterPrice : '') . ($startDate != "" ? '&start=' . formartDateReverse($startDate) : '') . ($endDate != "" ? '&end=' . formartDateReverse($endDate) : '')  ?>">&laquo;</a>
              <?php endif; ?>

              <?php for ($i = 1; $i <= $number_of_page; $i++): ?>
                <a href="order.php?page=<?= $i . ($filterPrice != 'lowest' ? '&price=' . $filterPrice : '') . ($startDate != "" ? '&start=' . formartDateReverse($startDate) : '') . ($endDate != "" ? '&end=' . formartDateReverse($endDate) : '') ?>" class="<?= $page == $i ? 'active' : '' ?>"><?= $i ?></a>
              <?php endfor; ?>
              <?php if ($page < $number_of_page): ?>
                <a href="order.php?page=<?= ($page + 1) . ($filterPrice != 'lowest' ? '&price=' . $filterPrice : '') . ($startDate != "" ? '&start=' . formartDateReverse($startDate) : '') . ($endDate != "" ? '&end=' . formartDateReverse($endDate) : '')  ?>">&raquo;</a>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

      </section>

    </div>
  </div>
  <script type="text/javascript" src="JS/dashboard.js"></script>
  <script type="text/javascript" src="JS/modal.js"></script>
  <script type="text/javascript" src="JS/order.js"></script>

</body>

</html>