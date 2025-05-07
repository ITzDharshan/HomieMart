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

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete_query = "DELETE FROM products WHERE id = $id";
    $delete_result = mysqli_query($conn, $delete_query);
    if ($delete_result) {
        echo "<script>alert('Product deleted successfully!'); window.location.href='product.php';</script>";
    } else {
        echo "<script>alert('Error deleting product!');</script>";
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
	<link rel="icon" type="image/png" href="img/shopping.png">
	<link rel="stylesheet" href="product.css">

	<title>AdminHomieMart</title>
</head>
<style>
	/* Style for the search container inside .table-data */
.search-container {
	display: flex;
	align-items: center;
	position: relative;
}

/* Style for the search icon */
.search-icon {
	cursor: pointer;
	font-size: 24px;
	margin-left: 10px;
}

/* Initially hide the search box */
.search-box {
	display: none;
	width: 200px;
	margin-left: 10px;
	transition: max-width 0.3s ease-in-out, opacity 0.3s ease-in-out;
	overflow: hidden;
	max-width: 0;
	opacity: 0;
}

/* Show the search box when active */
.search-box.active {
	display: block;
	max-width: 200px;
	opacity: 1;
}

/* Input field style */
.search-input {
	width: 100%;
	padding: 8px;
	border: 1px solid #ccc;
	border-radius: 4px;
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
			<li class="active">
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
				<a href="./logoutadmin.php" class="logout">
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
					<h1>View Products</h1>
				</div>
			</div>


			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Product Details</h3>
						<div class="search-container">
							<i class='bx bx-search search-icon'></i>
							<div class="search-box" data-table="productTable">
								<input type="text" class="search-input" placeholder="Search by username"
									onkeyup="searchUsers('productTable', this.value)" />
							</div>
						</div>
					</div>
					<table>
						<thead>
							<tr>
								<th>Product Name</th>
                                <th>Description</th>
								<th>Category</th>
								<th>Price</th>
								<th>Email</th>
                                <th>Edit / Delete</th>
							</tr>
						</thead>
						<tbody id="productTable">
						<?php          
							// Join products with register table to get business_type
							$query = "SELECT p.*, r.business_type 
									FROM products p 
									JOIN register r 
									ON p.homemaker_email = r.email";

							$view_products = mysqli_query($conn, $query);

							// Check if there are any products
							if (mysqli_num_rows($view_products) > 0) {
								// Loop through the products and display them
								while ($row = mysqli_fetch_assoc($view_products)) {
									$id = $row['id']; 
									$image = $row['product_image'];
									$product_name = $row['product_name'];
									$product_description = $row['product_description'];
									$category = $row['business_type']; // from register table
									$price = $row['product_price'];
									$email = $row['homemaker_email'];

									echo "<tr>";
									echo "<td>";
									echo "<img src='{$image}' alt='Product Image'>";
									echo "<p>{$product_name}</p>";
									echo "</td>";
									echo "<td>{$product_description}</td>";
									echo "<td>{$category}</td>";
									echo "<td>LKR " . number_format($price, 2) . "</td>";
									echo "<td>{$email}</td>";
									echo "<td>";
									echo "<a href='edit-product.php?id={$id}'>";
									echo "<i class='bx bxs-edit edit'></i></a> ";
									echo "<a href='?delete={$id}' onclick='return confirm(\"Are you sure you want to delete this product?\")'>";
									echo "<i class='bx bx-message-square-x delete'></i></a>";
									echo "</td>";
									echo "</tr>";
								}
							} else {
								// If no products are found, display this message
								echo "<tr><td colspan='6'>No products found.</td></tr>";
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
		  document.addEventListener("DOMContentLoaded", function () {
			const searchIcons = document.querySelectorAll(".search-icon");

			searchIcons.forEach((icon) => {
				icon.addEventListener("click", function () {
					// Get the parent .order section
					const section = this.closest(".order");
					const searchBox = section.querySelector(".search-box");
					const searchInput = searchBox.querySelector(".search-input");

					// Close all other search boxes
					document.querySelectorAll(".search-box").forEach((box) => {
						if (box !== searchBox) {
							box.classList.remove("active");
							box.querySelector(".search-input").value = "";
							searchUsers(box.getAttribute("data-table"), ""); // Reset search results
						}
					});

					// Toggle current search box
					if (searchBox.classList.contains("active")) {
						searchBox.classList.remove("active");
						searchInput.value = "";
						searchUsers(searchBox.getAttribute("data-table"), ""); // Reset search results
					} else {
						searchBox.classList.add("active");
						searchInput.focus();
					}
				});
			});
		});

		// Function to filter table rows based on input value
		function searchUsers(tableId, query) {
			const filter = query.toLowerCase();
			const tbody = document.querySelector(`#${tableId} tbody`);

			if (!tbody) return; // Ensure the table exists

			const rows = tbody.querySelectorAll("tr");

			rows.forEach((row) => {
				const usernameCell = row.querySelector("td:first-child"); // First <td> is Username
				if (usernameCell) {
					const username = usernameCell.textContent.toLowerCase();
					row.style.display = username.includes(filter) ? "" : "none";
				}
			});
		}
	</script>
    
	<script src="script.js"></script>
</body>
</html>