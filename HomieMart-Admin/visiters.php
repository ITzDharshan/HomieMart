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
    $delete_query = "DELETE FROM visiters WHERE id = $id";
    $delete_result = mysqli_query($conn, $delete_query);
    if ($delete_result) {
        echo "<script>alert('Visiter deleted successfully!'); window.location.href='visiters.php';</script>";
    } else {
        echo "<script>alert('Error deleting message!');</script>";
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
	<link rel="stylesheet" href="visiters.css">

	<title>AdminHomieMart</title>
</head>
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
                    <h1>
                        <span class="breadcrumb">
                            <span onclick="window.location.href='home.php';" style="cursor: pointer;">Dashboard</span>  / 
                            <span class="active">Joined Homemakers</span>
                        </span>
                    </h1>
                </div>
            </div>
            
            

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>New joiners Details</h3>
                    </div>
                    <table>
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Mobile Number</th>
                            <th>Address</th>
                            <th>Category</th>
                            <th>Skills</th>
                            <th>Delete / Reply</th>
                        </tr>
                    </thead>
                    <tbody id="visiterTable">
                    <?php
                        // Fetch all messages from the database
                        $query = "SELECT * FROM visiters";
                        $view_messages = mysqli_query($conn, $query);

                        // Check if there are any visitors
                        if (mysqli_num_rows($view_messages) > 0) {
                            while ($row = mysqli_fetch_assoc($view_messages)) {
                                $id = $row['id']; 
                                $username = $row['Username'];
                                $email = $row['Email'];
                                $mobile = $row['Mobile'];
                                $address = $row['Address'];
                                $category = $row['Business']; // Renamed to "Category"
                                $skills = $row['Skills_Products'];

                                echo "<tr>";
                                echo "<td>{$username}</td>";
                                echo "<td>{$email}</td>";
                                echo "<td>{$mobile}</td>";
                                echo "<td>{$address}</td>";
                                echo "<td>{$category}</td>"; 
                                echo "<td>{$skills}</td>";
                                echo "<td>";
                                echo "<a href='?delete={$id}' onclick='return confirm(\"Are you sure you want to delete this entry?\")'>";
                                echo "<i class='bx bx-message-square-x delete'></i></a>";
                                echo "<a href='mailto:{$email}'><i class='bx bx-envelope envelope'></i></a>"; // Mail icon for reply
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            // Display message if no visitors are found
                            echo "<tr><td colspan='7''>No visitors found.</td></tr>";
                        }
                        ?>
                    </tbody>

                    </table>
                </div>
            </div>

            
		</main>
		<!-- MAIN -->
	</section>

	<script src="script.js"></script>
</body>
</html>