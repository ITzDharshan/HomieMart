<?php
session_start();
include 'db_connection.php'; // Include your database connection

// Redirect if user is not logged in (except for login page)
if (empty($_SESSION['email']) && basename($_SERVER['PHP_SELF']) !== 'HM_index.php') {
    header("Location: HM_index.php");
    exit();
}

if (!isset($_SESSION['business_type']) || $_SESSION['business_type'] != 'Cooking') {
    header("Location: unauthorized.php");
    exit();
}

if (isset($_GET['delete_message'])) {
    $id = $_GET['delete_message'];
    $delete_query = "DELETE FROM chat_messages WHERE id = $id";
    $delete_result = mysqli_query($conn, $delete_query);
    if ($delete_result) {
        echo "<script>alert('Message deleted successfully!'); window.location.href='CK_message.php';</script>";
    } else {
        echo "<script>alert('Error deleting message!');</script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reply_message = $_POST['message'];
    $message_id = $_POST['message_id'];

    $update = $conn->prepare("UPDATE chat_messages SET reply_message = ? WHERE id = ?");
    $update->bind_param("si", $reply_message, $message_id);

    if ($update->execute()) {
        echo "<script>alert('Reply message updated successfully!'); window.location.href='CK_message.php';</script>";
    } else {
        echo "<script>alert('Error: " . $update->error . "'); window.location.href='CK_message.php';</script>";
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
	<link rel="stylesheet" href="message.css">

	<title>Sellers HomieMart</title>
</head>
<style>
.table-data .order table tr td .envelope:hover {
	transform: scale(1.2);
}

#content main .table-data .order table tr td:nth-child(5) {
	width: 100px;
	max-width: 100px;
	white-space: normal;
	word-wrap: break-word;
	vertical-align: top;
	text-align: left;
}

.chat-popup {
		font-family: 'Poppins', sans-serif;
        display: none;
        position: fixed;
        bottom: 20px;
        right: 20px;
        width: 400px;
        background-color: white;
        border: 1px solid #ccc;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        padding: 20px;
        z-index: 1000;
        border-radius: 8px;
    }

    .chat-popup-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .close-btn  {
        cursor: pointer;
        border: none;
        background-color: transparent;
        font-size: 18px;
    }

    textarea {
        width: 100%;
        height: 100px;
        margin-top: 10px;
        padding: 5px;
        border: 2px solid darkgoldenrod;
        border-radius: 8px;
    }

    input[type="file"] {
        margin-top: 10px;
    }

    #imagePreview {
        margin-top: 10px;
        max-width: 100%;
        max-height: 150px;
        overflow: hidden;
    }

    .send {
        width: 100%;
        padding: 10px;
        background-color: darkgoldenrod;
        color: black;
        border: none;
        cursor: pointer;
        margin-top: 10px;
        transition: background-color 0.3s, color 0.3s;
        border-radius: 8px;
    }

    .send:hover {
        background-color: black;
        color: white;
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
				<a href="CK_home.php">
					<i class='bx bxs-dashboard' ></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li>
				<a href="CK_product.php">
					<i class='bx bx-cart-alt'></i>
					<span class="text">Products</span>
				</a>
			</li>
			<li class="active">
				<a href="CK_message.php">
					<i class='bx bx-chat'></i>
					<span class="text">Message</span>
				</a>
			</li>
			<li>
				<a href="CK_order.php">
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
					<h1>Cookers Message Section</h1>
				</div>
			</div>


			<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Messgae History</h3>
						<i class='bx bx-search' ></i>
						<i class='bx bx-filter' ></i>
					</div>
					<table>
						<thead>
							<tr>
								<th>Name</th>
								<th>Email</th>
								<th>Image</th>
								<th>Message</th>
								<th>Reply Message</th>
								<th>Reply / Delete</th>
							</tr>
						</thead>
						<tbody>
							<?php
								$homemakerEmail = $_SESSION['email'];

								// Query to fetch messages where the homemaker is the recipient
								$query = "
									SELECT * FROM chat_messages
									WHERE homemaker_email = '$homemakerEmail'
								";

								// Execute the query
								$result = mysqli_query($conn, $query);

								// Check if there are any results
								if ($result && mysqli_num_rows($result) > 0) {
									// Loop through each row and display the data
									while($row = mysqli_fetch_assoc($result)) {
										$id = $row['id'];
										$name = $row['customer_name'];
										$email = $row['customer_email'];
										$image = $row['image'];
										$message = $row['message'];
										$reply = $row['reply_message'];

										echo "<tr>";
										echo "<td><p>{$name}</p></td>";
										echo "<td>{$email}</td>";
										echo "<td><img src='{$image}' alt='Customer Image' style='width: 150px; height: 150px; border-radius: 8px;'></td>";
										echo "<td>{$message}</td>";
										echo "<td>{$reply}</td>";
										echo "<td>";
										echo "<a href='javascript:void(0)' onclick='openChatBox(\"{$email}\", {$id})'>
												<i class='bx bx-envelope envelope' style='color: #00a11b; font-size: 1.5rem; margin-right: 10px;'></i>
											</a>";
										echo "<a href='?delete_message={$id}' onclick='return confirm(\"Are you sure you want to delete this message?\")'><i class='bx bx-message-square-x delete'></i></a>";
										echo "</td>";
										echo "</tr>";
									}
								} else {
									echo "<tr><td colspan='6'>No messages found for you.</td></tr>";
								}
							?>
						</tbody>
					</table>
				</div>
			</div>

			<div id="chatBox" class="chat-popup">
				<div class="chat-popup-header">
					<span id="chatTitle">Reply to Message</span>
					<button onclick="closeChatBox()" class="close-btn"><i class='bx bx-x'></i></button>
				</div>
				<form method="POST" enctype="multipart/form-data">
					<textarea name="message" required placeholder="Enter your message..."></textarea><br>
					
					<input type="hidden" name="message_id" id="messageId" value=""/>
					
					<button type="submit" class="send">Reply Message</button>
				</form>
			</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->
	
    <script>
		function openChatBox(customer_email, messageId) {
			document.getElementById('chatBox').style.display = 'block';
			document.getElementById('chatTitle').innerText = 'Reply to :  ' + customer_email;
			document.getElementById('messageId').value = messageId;
		}
		
		function closeChatBox() {
			document.getElementById('chatBox').style.display = 'none';
		}
	</script>
	<script src="HM_script.js"></script>
</body>
</html>