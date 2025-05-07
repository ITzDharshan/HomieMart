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

// Check if both order_id and status are sent via POST (for status update)
if (isset($_POST['order_id']) && isset($_POST['status'])) {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];

    // Sanitize inputs
    $orderId = mysqli_real_escape_string($conn, $orderId);
    $status = mysqli_real_escape_string($conn, $status);

    // Update the order status in the database
    $query = "UPDATE orders SET payment_status = '{$status}' WHERE order_id = '{$orderId}'";

    if (mysqli_query($conn, $query)) {
        echo "Status updated successfully";
    } else {
        echo "Error updating status: " . mysqli_error($conn);
    }
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
	<link rel="icon" type="image/png" href="images/shopping.png">
	<link rel="stylesheet" href="order.css">

	<title>Sellers HomieMart</title>
</head>
<body>


	<!-- SIDEBAR -->
	<section id="sidebar">
		<a href="#" class="brand">
			<i class='bx bx-world'></i>
			<span class="text">HomieMart</span>
		</a>
		<ul class="side-menu top">
			<li>
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
			<li class="active">
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
					<h1>Gardener Order History</h1>
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
							$homemakerEmail = $_SESSION['email'];

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
								WHERE p.homemaker_email = '{$homemakerEmail}';
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
									$status = $row['payment_status']; // Fetch current payment status

									echo "<tr>";
									echo "<td>#{$orderId}</td>";
									echo "<td>{$product}</td>";
									echo "<td>{$fullAddress}</td>";
									echo "<td>{$email}</td>";
									echo "<td>{$date}</td>";
									echo "<td>{$quantity}</td>";
									echo "<td>LKR {$price}</td>";
									echo "<td>
											<div class='custom-select' data-order-id='{$orderId}'>
												<div class='select-selected' data-value='{$status}'>{$status} <i class='bx bxs-chevron-down-square'></i></div>
												<div class='select-items'>
													<div data-value='Pending'>Pending</div>
													<div data-value='Processing'>Processing</div>
													<div data-value='Completed'>Completed</div>
												</div>
											</div>
										</td>";
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
	
	<script>
	document.querySelectorAll('.custom-select').forEach(function(select) {
		const selectSelected = select.querySelector('.select-selected');
		const selectItems = select.querySelector('.select-items');
		const options = selectItems.querySelectorAll('div');
		const orderId = select.getAttribute('data-order-id');

		selectSelected.addEventListener('click', function() {
			selectItems.style.display = selectItems.style.display === 'block' ? 'none' : 'block';
		});

		options.forEach(function(option) {
			option.addEventListener('click', function() {
				const newStatus = option.textContent.trim(); // Trim any extra spaces

				selectSelected.textContent = newStatus;
				selectSelected.className = 'select-selected';
				selectSelected.classList.add(newStatus.toLowerCase()); // Update class with status

				selectItems.style.display = 'none';

				// Send the updated status to the server via AJAX
				const xhr = new XMLHttpRequest();
				xhr.open('POST', '', true); // We are posting to the same page
				xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
				xhr.onload = function() {
					if (xhr.status === 200) {
						const response = JSON.parse(xhr.responseText);
						if (response.status === 'success') {
							alert(response.message); // Show success alert
						} else {
							alert(response.message); // Show error alert
						}
					} else {
						console.error('Failed to update status');
					}
				};
				xhr.send('order_id=' + encodeURIComponent(orderId) + '&status=' + encodeURIComponent(newStatus));
			});
		});

		window.addEventListener('click', function(e) {
			if (!select.contains(e.target)) {
				selectItems.style.display = 'none';
			}
		});
	});
	</script>
	<script src="HM_script.js"></script>
</body>
</html>