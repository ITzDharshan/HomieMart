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
        echo "Email and password are required.";
        exit();
    }

    // Prepare SQL query to fetch user details
    $stmt = $conn->prepare("SELECT id, email, password FROM adminsign WHERE email = ?");
    if ($stmt === false) {
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
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }
    $stmt->close();
}

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
	<link rel="stylesheet" href="order.css">

	<title>AdminHomieMart</title>
</head>
<style>
.completed, .pending, .processing {
    margin: 0;
    color: #333;
    cursor: pointer;
    text-align: center;
    width: 125%;
    padding: 0 12px;
    border-radius: 50px;
    display: inline-block;
    font-size: 14px; 
}

.completed {
    background: var(--blue);
}

.pending {
    background: var(--orange);
}

.processing {
    background: var(--yellow);
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
			<li>
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
			<li class="active">
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
					<h1>Order History</h1>
				</div>
			</div>


			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Orders</h3>
						<i class='bx bx-search' ></i>
						<i class='bx bx-filter' ></i>
					</div>
					<table>
						<thead>
							<tr>
								<th>Order ID</th>
								<th>Product</th>
								<th>Address</th>
								<th>Email</th>
								<th>Date</th>
								<th>Quantity</th>
								<th>Price</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
						<?php
							// Modify the query to also select the 'status' from the orders table
							$query = "
								SELECT 
									oi.order_id,
									oi.product_name,
									oi.created_at,
									oi.quantity,
									oi.total,
									o.address,
									o.city,
									o.state,
									o.user_email,
									o.payment_status 
								FROM order_items oi
								INNER JOIN orders o ON oi.order_id = o.order_id
								INNER JOIN products p ON oi.product_name = p.product_name
							";

							// Execute the query
							$result = mysqli_query($conn, $query);

							// Check if there are any results
							if ($result && mysqli_num_rows($result) > 0) {
								while ($row = mysqli_fetch_assoc($result)) {
									$orderId = $row['order_id'];
									$product = $row['product_name'];
									$date = $row['created_at'];
									$quantity = $row['quantity'];
									$price = $row['total'];
									$email = $row['user_email'];
									$fullAddress = $row['address'] . ', ' . $row['city'] . ', ' . $row['state'];
									$status = $row['payment_status'];  // Fetching the order status

									// Determine the status class based on the order status
									$statusClass = '';
									if ($status == 'Completed') {
										$statusClass = 'completed';
									} elseif ($status == 'Pending') {
										$statusClass = 'pending';
									} elseif ($status == 'Processing') {
										$statusClass = 'processing';
									}

									echo "<tr>";
									echo "<td>#{$orderId}</td>";
									echo "<td>{$product}</td>";
									echo "<td>{$fullAddress}</td>";
									echo "<td>{$email}</td>";
									echo "<td>{$date}</td>";
									echo "<td>{$quantity}</td>";
									echo "<td>LKR {$price}</td>";
									echo "<td class='{$statusClass}'>{$status}</td>"; 
									echo "</tr>";
								}
							} else {
								echo "<tr><td colspan='8'>No orders found.</td></tr>";
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->
	
	<script src="script.js"></script>
</body>
</html>