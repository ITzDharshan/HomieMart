<?php
session_start();
include 'db_connection.php'; // Include your database connection

// Redirect if user is not logged in (except for login page)
if (empty($_SESSION['email']) && basename($_SERVER['PHP_SELF']) !== 'HM_index.php') {
    header("Location: HM_index.php");
    exit();
}

if (!isset($_SESSION['business_type']) || $_SESSION['business_type'] != 'Dye Crafts') {
    header("Location: unauthorized.php");
    exit();
}

// Ensure session has email
if (isset($_SESSION['email'])) {
    $userEmail = $_SESSION['email'];

    // Get fname from the register table based on email
    $userQuery = "SELECT fname FROM register WHERE email = '$userEmail'";
    $result = mysqli_query($conn, $userQuery);

    if ($result && mysqli_num_rows($result) > 0) {
        $userRow = mysqli_fetch_assoc($result);
        $userFname = $userRow['fname'];

        // Handle form submission for adding new product
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['productId'])) {
            $productName = mysqli_real_escape_string($conn, $_POST['productName']);
            $productDescription = mysqli_real_escape_string($conn, $_POST['productDescription']);
            $productPrice = $_POST['productPrice'];

            if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
                $imageName = $_FILES['productImage']['name'];
                $imageTmpName = $_FILES['productImage']['tmp_name'];
                $imageFolder = 'uploads/' . basename($imageName);

                if (!is_dir('uploads')) {
                    mkdir('uploads', 0777, true);
                }

                if (move_uploaded_file($imageTmpName, $imageFolder)) {
                    // INSERT with homemaker_email and fname
                    $sql = "INSERT INTO products (product_image, product_name, product_description, product_price, homemaker_email, homemaker_name) 
                            VALUES ('$imageFolder', '$productName', '$productDescription', '$productPrice', '$userEmail', '$userFname')";

                    if ($conn->query($sql) === TRUE) {
                        echo "<script>alert('Product added successfully!'); window.location.href='DC_product.php';</script>";
                        exit();
                    } else {
                        echo "<script>alert('DB Error: " . addslashes($conn->error) . "'); window.location.href='DC_product.php';</script>";
                        exit();
                    }
                } else {
                    echo "<script>alert('Failed to upload image.'); window.location.href='DC_product.php';</script>";
                    exit();
                }
            } else {
                echo "<script>alert('Please upload a valid image.'); window.location.href='DC_product.php';</script>";
                exit();
            }
        }

        // Handle product edit (with optional image change)
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['productId'])) {
            $id = intval($_POST['productId']);
            $productName = mysqli_real_escape_string($conn, $_POST['productName']);
            $productDescription = mysqli_real_escape_string($conn, $_POST['productDescription']);
            $productPrice = floatval($_POST['productPrice']);

            $updateImage = ''; // This will hold the image update SQL part if we update the image
            
            // Optional image upload
            if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
                $imageName = uniqid() . '_' . basename($_FILES['productImage']['name']); // Make filename unique
                $imageTmpName = $_FILES['productImage']['tmp_name'];
                $imageFolder = 'uploads/' . $imageName;

                if (move_uploaded_file($imageTmpName, $imageFolder)) {
                    $updateImage = ", product_image = '$imageFolder'"; // Update image if new image uploaded
                }
            }

            // Update product (with or without new image)
            $update = "UPDATE products SET 
                        product_name = '$productName', 
                        product_description = '$productDescription', 
                        product_price = '$productPrice'
                        $updateImage
                        WHERE id = $id";

            if (mysqli_query($conn, $update)) {
                echo "<script>alert('Product updated successfully!'); window.location.href='DC_product.php';</script>";
            } else {
                echo "<script>alert('Error updating product!');</script>";
            }
        }
    } else {
        echo "<script>alert('User not found. Please log in again.'); window.location.href='HM_index.php';</script>";
        exit();
    }
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete_query = "DELETE FROM products WHERE id = $id";
    $delete_result = mysqli_query($conn, $delete_query);
    if ($delete_result) {
        echo "<script>alert('Product deleted successfully!'); window.location.href='DC_product.php';</script>";
    } else {
        echo "<script>alert('Error deleting product!');</script>";
    }
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT * FROM products WHERE id = $id";
    $result = mysqli_query($conn, $query);
    if ($row = mysqli_fetch_assoc($result)) {
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Product not found']);
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
	<link rel="stylesheet" href="product.css">

	<title>Sellers HomieMart</title>
</head>
<style>
#content main .table-data .order table tr td:nth-child(1), 
#content main .table-data .order table tr td:nth-child(3), 
#content main .table-data .order table tr td:nth-child(4), 
#content main .table-data .order table tr td:nth-child(5) {
	text-align: left;
	vertical-align: top;
}

#content main .table-data .order table tr td:nth-child(2) {
	width: 400px;
	max-width: 400px;
	white-space: normal;
	word-wrap: break-word;
	vertical-align: top;
	text-align: left;
	padding-right: 50px;
}
#content main .table-data .order table td img {
	width: 50px;
	height: 50px;
	border-radius: 50%;
	object-fit: cover;
}
.edit-popup {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background: rgba(0, 0, 0, 0.5);
}

.popup-content {
    background: rgba(255, 255, 255, 0.95);
    margin: 10% auto;
    padding: 30px;
    border-radius: 15px;
    backdrop-filter: blur(10px);
    width: 500px;
	height: 115vh;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    position: relative;
}

.close-btn {
    position: absolute;
    right: 20px;
    top: 15px;
    font-size: 24px;
    cursor: pointer;
}

.edit-popup .form-group input,
.edit-popup .form-group textarea {
    width: 100%;
    margin-top: 10px;
}

.form-group button {
	background: darkgoldenrod;
    color: #fff;
}
.form-group button:hover {
	background: black;
    color: darkgoldenrod;
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
				<a href="DC_home.php">
					<i class='bx bxs-dashboard' ></i>
					<span class="text">Dashboard</span>
				</a>
			</li>
			<li class="active">
				<a href="DC_product.php">
					<i class='bx bx-cart-alt'></i>
					<span class="text">Products</span>
				</a>
			</li>
			<li>
				<a href="DC_message.php">
					<i class='bx bx-chat'></i>
					<span class="text">Message</span>
				</a>
			</li>
			<li>
				<a href="DC_order.php">
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
					<h1>Dye Crafters View Products</h1>
				</div>
			</div>
		
		<div class="table-data">
			<div class="todo">
				<div class="head">
					<h3>Add Product</h3>
				</div>
				<form id="addProductForm" method="POST" enctype="multipart/form-data">
					<div class="form-group">
						<label for="productImage">Product Image:</label>
						<input type="file" id="productImage" name="productImage" accept="image/*" required>
					</div>
					<div class="form-group">
						<label for="productName">Product Name:</label>
						<input type="text" id="productName" name="productName" placeholder="Enter product name" required>
					</div>
					<div class="form-group">
						<label for="productDescription">Product Description:</label>
						<textarea id="productDescription" name="productDescription" placeholder="Enter product description" rows="4" required></textarea>
					</div>
					<div class="form-group">
						<label for="productPrice">Product Price:</label>
						<input type="number" id="productPrice" name="productPrice" placeholder="Enter product price" required>
					</div>
					<div class="form-group">
						<button type="submit">Add Product</button>
					</div>
				</form>
			</div>
		</div>	


		<div class="table-data">
				<div class="order">
					<div class="head">
						<h3>Product Details</h3>
						<i class='bx bx-search' ></i>
						<i class='bx bx-filter' ></i>
					</div>
					<table>
						<thead>
							<tr>
								<th>Product Name</th>
                                <th>Description</th>
								<th>Price</th>
                                <th>Edit / Delete</th>
							</tr>
						</thead>
						<tbody>
                        <?php
                            // Fetch products added by this homemaker only
                            $userEmail = $_SESSION['email'];

                            $query = "
                                SELECT * FROM products 
                                WHERE homemaker_email = '$userEmail'
                            ";

                            $view_products = mysqli_query($conn, $query);

                            if ($view_products && mysqli_num_rows($view_products) > 0) {
                                while ($row = mysqli_fetch_assoc($view_products)) {
                                    $id = $row['id']; // product ID
                                    $name = $row['product_name'];
                                    $description = $row['product_description'];
                                    $price = $row['product_price'];
                                    $image = $row['product_image'];
                                    $homemaker_email = $row['homemaker_email'];
                                    $homemaker_name = $row['homemaker_name'];

                                    echo "<tr>";
                                    echo "<td>";
                                    echo "<img src='{$image}' alt='Product Image'>";
                                    echo "<p>{$name}</p>";
                                    echo "<small>By: {$homemaker_name} ({$homemaker_email})</small>"; // Show homemaker info
                                    echo "</td>";
                                    echo "<td>{$description}</td>";
                                    echo "<td>LKR {$price}</td>";
                                    echo "<td>";
                                    echo "<i 
										class='bx bxs-edit edit' 
										data-id='{$id}' 
										data-name='{$name}' 
										data-description='{$description}' 
										data-price='{$price}' 
										data-image='{$image}'
									></i>";
                                    echo "<a href='?delete={$id}' onclick='return confirm(\"Are you sure you want to delete this product?\")'><i class='bx bx-message-square-x delete'></i></a>";
                                    echo "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5'>No products found for you.</td></tr>";
                            }
                            ?>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Popup Modal -->
		<div id="editPopup" class="edit-popup" style="display: none;">
			<div class="popup-content">
				<span class="close-btn" id="closePopup">&times;</span>
				<h1>Edit Product</h1>
				<form id="editProductForm" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="productId" id="productId">
					<div class="form-group" style="display: flex; gap: 40px; align-items: flex-start;">
						<!-- Current Image -->
						<div style="display: flex; flex-direction: column; align-items: center;">
							<label style="margin-bottom: 5px;">Current Image:</label>
							<img id="currentImage" src="" alt="Current Product Image" style="max-width: 100px; border: 1px solid #ccc; padding: 4px;">
						</div>

						<!-- New Selected Image -->
						<div style="display: flex; flex-direction: column; align-items: center;">
							<label style="margin-bottom: 5px;">New Selected Image:</label>
							<img id="newPreview" src="" alt="New Product Preview" style="max-width: 100px; display: none; border: 1px solid #ccc; padding: 4px;">
						</div>
					</div>
					<div class="form-group">
						<label for="editProductImage">Product Image:</label>
						<input type="file" id="editProductImage" name="productImage" accept="image/*">
					</div>
					<div class="form-group">
						<label for="editProductName">Product Name:</label>
						<input type="text" id="editProductName" name="productName" required>
					</div>
					<div class="form-group">
						<label for="editProductDescription">Product Description:</label>
						<textarea id="editProductDescription" name="productDescription" rows="4" required></textarea>
					</div>
					<div class="form-group">
						<label for="editProductPrice">Product Price:</label>
						<input type="number" id="editProductPrice" name="productPrice" required>
					</div>
					<div class="form-group">
						<button type="submit">Update Product</button>
					</div>
				</form>
			</div>
		</div>
		</main>
		<!-- MAIN -->
	</section>
	<!-- CONTENT -->
	
    <script>
		const editButtons = document.querySelectorAll('.edit');
		const popup = document.getElementById('editPopup');
		const closePopup = document.getElementById('closePopup');
		const editImageInput = document.getElementById('editProductImage');
		const newPreview = document.getElementById('newPreview');

		// Open popup when clicking edit button
		editButtons.forEach(btn => {
			btn.addEventListener('click', function () {
				const id = this.dataset.id;
				const name = this.dataset.name;
				const description = this.dataset.description;
				const price = this.dataset.price;
				const image = this.dataset.image;

				// Populate form fields
				document.getElementById('productId').value = id;
				document.getElementById('editProductName').value = name;
				document.getElementById('editProductDescription').value = description;
				document.getElementById('editProductPrice').value = price;
				document.getElementById('currentImage').src = image;

				// Reset new preview
				newPreview.src = '';
				newPreview.style.display = 'none';
				editImageInput.value = '';

				// Open popup
				popup.style.display = 'flex';
			});
		});

		// Close popup only when close button is clicked
		closePopup.addEventListener('click', () => {
			popup.style.display = 'none';
		});

		// New image preview logic
		editImageInput.addEventListener('change', function (e) {
			const file = e.target.files[0];
			if (file) {
				const reader = new FileReader();
				reader.onload = function (event) {
					newPreview.src = event.target.result;
					newPreview.style.display = 'block';
				}
				reader.readAsDataURL(file);
			} else {
				newPreview.src = '';
				newPreview.style.display = 'none';
			}
		});
	</script>
	<script src="HM_script.js"></script>
</body>
</html>