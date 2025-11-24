<?php

require("./PHP/authprotection.php");
require "../database/dbcon.php";
$sql = "SELECT id,CONCAT(firstname,' ',IFNULL(lastname,'')) AS name,email,role FROM users";
$result = $conn->query($sql);

// search
if (isset($_GET["q"])) {
    $search_query = $_GET['q'];

    if (preg_match("/^[\d\w\s]+$/", $search_query)) {
        $sql = "SELECT id,CONCAT(firstname,' ',IFNULL(lastname,'')) AS name,email,role FROM users WHERE LOWER(CONCAT(firstname,' ',IFNULL(lastname,''))) LIKE LOWER('%$search_query%')";
    } else {
        header("location:  user.php");
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
    $sql = "SELECT id,CONCAT(firstname,' ',IFNULL(lastname,'')) AS name,email,role FROM users  LIMIT " . $page_first_result . ',' . $results_per_page;
$result = $conn->query($sql);
$result->fetch_all(MYSQLI_ASSOC);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - User</title>
    <link rel="stylesheet" type="text/css" href="CSS/dashboard.css">
    <link rel="stylesheet" type="text/css" href="CSS/table.css">
    <link rel="stylesheet" type="text/css" href="CSS/modal.css">
</head>

<body>

    <!-- add modal -->
    <div class="modal" id="modal1">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h1>Add New User</h1>
            <form id="add-user-frm">
                <div class="input-wrapper" style="display: flex;gap: 15px">
                    <div>
                        <label>Firstname</label>
                        <input type="text" name="fname" placeholder="Enter firstname*">
                    </div>
                    <div>
                        <label>Lastname</label>
                        <input type="text" name="lname" placeholder="Enter lastname">
                    </div>
                </div>
                <div class="input-wrapper">
                    <label>Email Address</label>
                    <input type="text" name="email" placeholder="Enter email address*">
                </div>
                <div class="input-wrapper">
                    <label>Role</label>
                    <select>
                        <option value="admin">Admin</option>
                        <option value="user" selected>User</option>
                    </select>
                </div>
                <div class="input-wrapper">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter password*">
                </div>
                <div class="input-wrapper">
                    <label>Confirm Password</label>
                    <input type="password" name="cfpassword" placeholder="Enter confirm password*">
                </div>
                <div style="text-align: right;">
                    <button type="submit" class="button button-primary">Add User</button>
                </div>
            </form>
        </div>
    </div>
    <!-- end addmodal -->

    <!-- edit modal  -->
    <div class="modal" id="modal2">
        <div class="modal-content">
            <span class="modal-close">&times;</span>
            <h1>Edit User</h1>
            <form id="edit-user-frm">
                <input type="hidden" name="user-id">
                <div class="input-wrapper" style="display: flex;gap: 15px">
                    <div>
                        <label>Firstname</label>
                        <input type="text" name="fname" placeholder="Enter firstname*">
                    </div>
                    <div>
                        <label>Lastname</label>
                        <input type="text" name="lname" placeholder="Enter lastname">
                    </div>
                </div>
                <div class="input-wrapper">
                    <label>Email Address</label>
                    <input type="text" name="email" placeholder="Enter email address*">
                </div>
                <div class="input-wrapper">
                    <label>Role</label>
                    <select>
                        <option value="admin">Admin</option>
                        <option value="user" selected>User</option>
                    </select>
                </div>
                <div class="input-wrapper" style="color: black;">
                    Change Password <input type="checkbox" id="chk-change-password">
                </div>
                <div class="input-wrapper">
                    <label>New Password</label>
                    <input type="password" name="new-password" placeholder="Enter password*" disabled>
                </div>
                <div style="text-align: right;">
                    <button type="submit" class="button button-primary">Save User</button>
                </div>
            </form>
        </div>
    </div>
    <!-- end edit modal -->


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
                <li><a class="menu-item active" href="user.php">Users</a></li>
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
                        <caption>User Listing</caption>
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col">Role</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($result as $user): ?>
                                <tr>
                                    <td data-label="#"><?= $user["id"] ?></td>
                                    <td data-label="Name"><?= $user["name"] ?></td>
                                    <td data-label="Email"><?= $user["email"] ?></td>
                                    <td data-label="Role" style="text-transform: capitalize;"><?= $user["role"] ?></td>
                                    <td data-label="Actions" style="min-width: 160px;">
                                        <button <?=$user["email"] == 'dev@gmail.com'? 'disabled':''?>  data-modal-target="modal2" style="user-select: none;background-color: <?=$user["email"] == 'dev@gmail.com'?'#005286':''?>;"  data-user="<?= htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-edit" >Edit</button>
                                        <button class="btn btn-delete" style="user-select: none;pointer-events: <?= $_SESSION['backend_email'] == $user['email'] || $user['email'] == 'dev@gmail.com' ? 'none' : '' ?>;background-color: <?= $_SESSION['backend_email'] == $user['email'] || $user['email'] == 'dev@gmail.com' ? '#a91a1a' : '' ?>;" data-id="<?= $user['id']; ?>">Edit</button>
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
                                <a href="user.php?page=<?= $page - 1 ?>">&laquo;</a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $number_of_page; $i++): ?>
                                <a href="user.php?page=<?= $i ?>" class="<?= $page == $i ? 'active' : '' ?>"><?= $i ?></a>
                            <?php endfor; ?>
                            <?php if ($page < $number_of_page): ?>
                                <a href="user.php?page=<?= $page + 1 ?>">&raquo;</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </section>

        </div>
    </div>
    
    <script type="text/javascript" src="JS/dashboard.js"></script>
    <script type="text/javascript" src="JS/modal.js"></script>
    <script type="text/javascript" src="JS/user.js"></script>
</body>

</html>