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

// Handle user deletion
if (isset($_GET['delete'])) {
    $userid = $_GET['delete'];
    $query = "DELETE FROM register WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userid);
    if ($stmt->execute()) {
        echo "<script>alert('User deleted successfully!'); window.location.href='users.php';</script>";
    } else {
        echo "<script>alert('Error deleting user!'); window.location.href='users.php';</script>";
    }
    $stmt->close();
}

// Handle user addition
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);
    $userType = trim($_POST['userType']);
    $businessType = isset($_POST['businessType']) ? trim($_POST['businessType']) : NULL;

    // Validate inputs
    if (empty($username) || empty($email) || empty($mobile) || empty($password) || empty($confirmPassword) || empty($userType)) {
        echo "<script>alert('All fields are required.'); window.location.href='users.php';</script>";
        exit();
    }

    // Check if passwords match
    if ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.'); window.location.href='users.php';</script>";
        exit();
    }

    // Check if email already exists in the database
    $checkStmt = $conn->prepare("SELECT id FROM register WHERE email = ?");
    $checkStmt->bind_param("s", $email);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        echo "<script>alert('Email already exists. Please use another email.'); window.location.href='users.php';</script>";
        exit();
    }
    $checkStmt->close();

    // Hash the password for security
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO register (fname, email, mobile, password, role, business_type) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $email, $mobile, $hashedPassword, $userType, $businessType);

    if ($stmt->execute()) {
        echo "<script>alert('User added successfully!'); window.location.href='users.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href='users.php';</script>";
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
	<link rel="stylesheet" href="users.css">

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
			<li class="active">
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
					<h1>Users</h1>
				</div>
			</div>
        
            <div class="table-data">
                <div class="todo">
                    <div class="head">
                        <h3>Add Users</h3>
                    </div>
                    <form id="addUserForm" action="users.php" method="POST">
                        <div class="form-group">
                            <label for="username">Username:</label>
                            <input type="text" id="username" name="username" placeholder="Enter username" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" id="email" name="email" placeholder="Enter email" required>
                        </div>
                        <div class="form-group">
                            <label for="mobile">Mobile Number:</label>
                            <input type="tel" id="mobile" name="mobile" placeholder="Enter mobile number" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password:</label>
                            <input type="password" id="password" name="password" placeholder="Enter password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirm Password:</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm password" required>
                        </div>
                        <div class="form-group">
                            <label for="userType">User Type:</label>
                            <select id="userType" name="userType" required onchange="toggleBusinessType()">
                                <option value="">Select User Type</option>
                                <option value="Customer">Customer</option>
                                <option value="Homemaker">Homemaker</option>
                            </select>
                        </div>
                        <div class="form-group hidden" id="businessTypeGroup">
                            <label for="businessType">Select Homemaker's Business Type:</label>
                                <select id="businessType" name="businessType">
                                    <option value="">Select Business Type</option>
                                    <option value="Dye Crafts">Dye Crafts</option>
                                    <option value="Gardening Products">Gardening Products</option>
                                    <option value="Baking">Baking</option>
                                    <option value="Handicrafts">Handicrafts</option>
                                    <option value="Sustainable Products">Sustainable Products</option>
                                    <option value="Cooking">Cooking</option>
                                    <option value="Art and Painting">Art and Painting</option>
                                    <option value="Knitting and Crochet">Knitting and Crochet</option>
                                    <option value="Wellness Products">Wellness Products</option>
                                    <option value="Stationery and Paper Crafts">Stationery and Paper Crafts</option>
                                </select>
                        </div>
                        <div class="form-group">
                            <button type="submit">Add User</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Customer Details</h3>
                        <div class="search-container">
                            <i class='bx bx-search search-icon'></i>
                            <div class="search-box" data-table="cutomerTable">
                                <input type="text" class="search-input" placeholder="Search by username"
                                    onkeyup="searchUsers('cutomerTable', this.value)" />
                            </div>
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>Password</th>
                                <th>Edit / Delete</th>
                            </tr>
                        </thead>
                        <tbody id="cutomerTable">
                        <?php          
                            // Query to fetch customers only
                            $query = "SELECT * FROM register WHERE role = 'Customer'"; 
                            $view_users = mysqli_query($conn, $query); // Execute the query

                            // Check if there are any customers
                            if (mysqli_num_rows($view_users) > 0) {
                                // Loop through the users and display them
                                while ($row = mysqli_fetch_assoc($view_users)) {
                                    $id = $row['id']; 
                                    $user = $row['fname'];
                                    $email = $row['email'];
                                    $mobile = $row['mobile']; // Fetch mobile number
                                    $pass = $row['password'];

                                    echo "<tr>";
                                    echo "<td><p>{$user}</p></td>";
                                    echo "<td>{$email}</td>";
                                    echo "<td>{$mobile}</td>";
                                    echo "<td>********</td>"; // Mask password
                                    echo "<td>";
                                    echo "<a href='editadmin.php?edit&user_id={$id}'><i class='bx bxs-edit edit'></i></a>";
                                    echo "<a href='?delete={$id}' onclick='return confirm(\"Are you sure you want to delete this user?\")'>";
                                    echo "<i class='bx bx-message-square-x delete'></i></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                // If no customers are found, display this message
                                echo "<tr><td colspan='5'>No customers found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>


            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Homemakers Details</h3>
                        <div class="search-container">
                            <i class='bx bx-search search-icon'></i>
                            <div class="search-box" data-table="homemakerTable">
                                <input type="text" class="search-input" placeholder="Search by username"
                                    onkeyup="searchUsers('homemakerTable', this.value)" />
                            </div>
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Mobile Number</th>
                                <th>Password</th>
                                <th>Working Category</th>
                                <th>Edit / Delete</th>
                            </tr>
                        </thead>
                        <tbody id="homemakerTable">
                        <?php          
                            // Query to fetch homemakers only
                            $query = "SELECT * FROM register WHERE role = 'Homemaker'"; 
                            $view_users = mysqli_query($conn, $query); // Execute the query

                            // Check if there are any homemakers
                            if (mysqli_num_rows($view_users) > 0) {
                                // Loop through the homemakers and display them
                                while ($row = mysqli_fetch_assoc($view_users)) {
                                    $id = $row['id']; 
                                    $user = $row['fname'];
                                    $email = $row['email'];
                                    $mobile = $row['mobile']; // Fetch mobile number
                                    $pass = $row['password'];
                                    $business_type = $row['business_type']; // Fetch business type

                                    echo "<tr>";
                                    echo "<td>";
                                    echo "<p>{$user}</p>";
                                    echo "</td>";
                                    echo "<td>{$email}</td>";
                                    echo "<td>{$mobile}</td>";
                                    echo "<td>********</td>"; // Mask password
                                    echo "<td>{$business_type}</td>"; // Display business type
                                    echo "<td>";
                                    echo "<a href='editadmin.php?edit&user_id={$id}'><i class='bx bxs-edit edit'></i></a>";
                                    echo "<a href='?delete={$id}' onclick='return confirm(\"Are you sure you want to delete this homemaker?\")'>";
                                    echo "<i class='bx bx-message-square-x delete'></i></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                // If no homemakers are found, display this message
                                echo "<tr><td colspan='6'>No homemakers found.</td></tr>";
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
        document.getElementById('userType').addEventListener('change', function () {
            const businessTypeGroup = document.getElementById('businessTypeGroup');
            if (this.value === 'Homemaker') {
                businessTypeGroup.classList.remove('hidden');
            } else {
                businessTypeGroup.classList.add('hidden');
            }
        });

       // Function to toggle search box visibility for the clicked section only
       document.addEventListener("DOMContentLoaded", function () {
            const searchIcons = document.querySelectorAll(".search-icon");

            searchIcons.forEach((icon) => {
                icon.addEventListener("click", function () {
                    // Get the parent section
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
            const tbody = document.querySelector(`#${tableId}`); // Get the specific table body

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