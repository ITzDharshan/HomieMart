<?php
session_start();
include 'db_connection.php'; // Include your database connection

// Redirect if user is not logged in (except for login page)
if (!isset($_SESSION['admin_email']) && basename($_SERVER['PHP_SELF']) != 'index.php') {
    header("Location: index.php");
    exit();
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    // Retrieve and sanitize input
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email and password are required.";
        header("Location: index.php");
        exit();
    }

    // Prepare SQL query to fetch user details from adminsign table
    $stmt = $conn->prepare("SELECT id, email, password FROM adminsign WHERE email = ?");
    if (!$stmt) {
        die("Error preparing statement: " . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Check if a user exists
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $email, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION['user_id'] = $id;
            $_SESSION['admin_email'] = $email;

            // Redirect to home page
            header("Location: home.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password.";
            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "User not found.";
        header("Location: index.php");
        exit();
    }
    $stmt->close();
}

 // Fetch the total number of messages (visitors)
 $query = "SELECT COUNT(*) AS total_visitors FROM visiters";
 $result = mysqli_query($conn, $query);
 $row = mysqli_fetch_assoc($result);
 $totalVisitors = $row['total_visitors']; 

 $query = "SELECT COUNT(*) AS total_orders FROM order_items";
 $result = mysqli_query($conn, $query);
 $row = mysqli_fetch_assoc($result);
 $totalOrders = $row['total_orders'];
 
 $revenue_query = "SELECT SUM(admin_revenue) AS total_revenue FROM revenue";
 $result = $conn->query($revenue_query);
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
	<link rel="icon" type="image/png" href="img/shopping.png">
	<link rel="stylesheet" href="home.css">

	<title>AdminHomieMart</title>
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
				<a href="home.php">
					<i class='bx bxs-dashboard' ></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="users.php">
					<i class='bx bx-user' ></i>
					<span class="text">Users</span>
				</a>
			</li>
			<li>
				<a href="product.php">
					<i class='bx bx-cart-alt'></i>
					<span class="text">Products</span>
				</a>
			</li>
			<li>
				<a href="message.php">
					<i class='bx bx-chat'></i>
					<span class="text">Message</span>
				</a>
			</li>
			<li>
				<a href="order.php">
					<i class='bx bx-package'></i>
					<span class="text">Orders</span>
				</a>
			</li>
		</ul>
		<ul class="side-menu">
			<li>
				<a href="logoutadmin.php" class="logout">
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
			<p class='profile'> <?php echo htmlspecialchars($_SESSION['admin_email']); ?></p>
			<input type="checkbox" id="switch-mode" hidden>
			<label for="switch-mode" class="switch-mode"></label>
		</nav>
		<!-- NAVBAR -->

		<!-- MAIN -->
		<main>
			<div class="head-title">
				<div class="left">
					<h1>Dashboard</h1>
				</div>
			</div>

			<ul class="box-info">
				<li onclick="window.location.href='order.php';">
					<i class='bx bxs-calendar-check' ></i>
					<span class="text">
						<h3><?php echo $totalOrders; ?></h3> 
						<p>New Order</p>
					</span>
				</li>
				<li onclick="window.location.href='visiters.php';">
					<i class='bx bxs-group'></i>
					<span class="text">
						<h3><?php echo $totalVisitors; ?></h3> 
						<p>Visitors</p>
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
							$query = "
								SELECT 
									o.user_email,
									o.created_at,
									o.payment_status
								FROM orders o
								INNER JOIN order_items oi ON oi.order_id = o.order_id
								INNER JOIN products p ON oi.product_name = p.product_name
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
	

	<script src="script.js"></script>
</body>
</html>