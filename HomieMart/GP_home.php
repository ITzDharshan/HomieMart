<?php
session_start();
include 'db_connection.php'; // Include your database connection

// Redirect if user is not logged in (except for login page)
if (empty($_SESSION['email']) && basename($_SERVER['PHP_SELF']) !== 'HM_index.php') {
    header("Location: HM_index.php");
    exit();
}

if (!isset($_SESSION['business_type']) || $_SESSION['business_type'] != 'Gardening Products') {
    header("Location: unauthorized.php");
    exit();
}

$homemakerEmail = $_SESSION['email']; // Get homemaker's email from session

$query = "
    SELECT COUNT(*) AS total_orders 
    FROM order_items oi
    INNER JOIN orders o ON oi.order_id = o.order_id
    INNER JOIN products p ON oi.product_name = p.product_name
    WHERE p.homemaker_email = '{$homemakerEmail}';
";
$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);
$totalOrders = $row['total_orders'];

$homemaker_email = $_SESSION['email'];

// Query for homemaker revenue
$revenue_query = "SELECT SUM(homemaker_revenue) AS total_revenue FROM revenue WHERE homemaker_email = ?";
$stmt = $conn->prepare($revenue_query);
$stmt->bind_param("s", $homemaker_email);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalRevenue = $row['total_revenue'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- Boxicons -->
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
	<link rel="icon" type="image/png" href="images/shopping.png">
	<link rel="stylesheet" href="home.css">

	<title>Sellers HomieMart</title>
</head>
<style>
	.status.cancelled {
    background-color:rgb(253, 52, 69);
    color: #721c24;
    padding: 4px 8px;
    border-radius: 4px;
    font-weight: bold;
}
</style>
<body>

	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="#" class="brand">
			<i class='bx bx-world'></i>
			<span class="text">HomieMart</span>
		</a>
		<ul class="side-menu top">
			<li class="active">
				<a href="GP_home.php">
					<i class='bx bxs-dashboard' ></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="GP_product.php">
					<i class='bx bx-cart-alt'></i>
					<span class="text">Products</span>
				</a>
			</li>
			<li>
				<a href="GP_message.php">
					<i class='bx bx-chat'></i>
					<span class="text">Message</span>
				</a>
			</li>
			<li>
				<a href="GP_order.php">
					<i class='bx bx-package'></i>
					<span class="text">Orders</span>
				</a>
			</li>
		</ul>
		<ul class="side-menu">
			<li>
				<a href="logouthomemaker.php" class="logout">
					<i class='bx bx-exit'></i>
					<span class="text">Logout</span>
				</a>
			</li>
		</ul>
	</section>
	<!-- SIDEBAR -->



	<!-- CONTENT -->
	<section id="content">
		<!-- NAVBAR -->
		<nav>
			<i class='bx bx-menu' ></i>
			<a href="#" class="nav-link">Categories</a>
			<form action="#">
				<div class="form-input">
					<input type="search" placeholder="Search...">
					<button type="submit" class="search-btn"><i class='bx bx-search' ></i></button>
				</div>
			</form>
			<p class='profile'> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
			<input type="checkbox" id="switch-mode" hidden>
			<label for="switch-mode" class="switch-mode"></label>
		</nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<div class="head-title">
				<div class="left">
					<h1>Gardener Dashboard</h1>
				</div>
			</div>

			<ul class="box-info">
				<li onclick="window.location.href='GP_order.php';">
					<i class='bx bxs-calendar-check' ></i>
					<span class="text">
						<h3><?php echo $totalOrders; ?></h3>
						<p>New Order</p>
					</span>
				</li>							
				<li>
					<i class='bx bxs-dollar-circle' ></i>
					<span class="text">
					<h3>LKR <?php echo number_format($totalRevenue, 2); ?></h3>
						<p>Total Sales</p>
					</span>
				</li>
			</ul>


			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Recent Orders</h3>
						<i class='bx bx-search' ></i>
						<i class='bx bx-filter' ></i>
					</div>
					<table>
						<thead>
							<tr>
								<th>User</th>
								<th>Date Order</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$homemakerEmail = $_SESSION['email'];

							$query = "
								SELECT 
									o.user_email,
									o.created_at,
									o.payment_status
								FROM orders o
								INNER JOIN order_items oi ON oi.order_id = o.order_id
								INNER JOIN products p ON oi.product_name = p.product_name
								WHERE p.homemaker_email = '{$homemakerEmail}'
								ORDER BY o.created_at DESC
								LIMIT 5;
							";

							// Execute the query
							$result = mysqli_query($conn, $query);

							// Check if there are any results
							if ($result && mysqli_num_rows($result) > 0) {
								while ($row = mysqli_fetch_assoc($result)) {
									$email = $row['user_email'];
									$date = $row['created_at'];
									$status = $row['payment_status'];

									echo "<tr>";
									echo "<td><p>{$email}</p></td>";
									echo "<td>{$date}</td>";

									if ($status == 'Completed') {
										echo "<td><span class='status completed'>Completed</span></td>";
									} elseif ($status == 'Pending') {
										echo "<td><span class='status pending'>Pending</span></td>";
									} elseif ($status == 'Processing') {
										echo "<td><span class='status process'>Processing</span></td>";
									} elseif ($status == 'Cancelled') {
										echo "<td><span class='status cancelled'>Cancelled</span></td>";
									}

									echo "</tr>";
								}
							} else {
								echo "<tr><td colspan='3'>No orders found.</td></tr>";
							}
							?>
						</tbody>
					</table>
				</div>
				<div class="todo">
					<div class="head">
						<h3>Todos</h3>
						<i class='bx bx-plus' ></i>
						<i class='bx bx-filter' ></i>
					</div>
					<ul class="todo-list">
						<li class="completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded' ></i>
						</li>
						<li class="completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded' ></i>
						</li>
						<li class="not-completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded' ></i>
						</li>
						<li class="completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded' ></i>
						</li>
						<li class="not-completed">
							<p>Todo List</p>
							<i class='bx bx-dots-vertical-rounded' ></i>
						</li>
					</ul>
				</div>
			</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->
	

	<script src="HM_script.js"></script>
</body>
</html>