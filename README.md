# 🛍️ HomieMart – Empowering Homemakers Through Commerce

**HomieMart** is more than just a marketplace—it is a vibrant ecosystem designed to connect homemakers with customers, offering a seamless interface for selling and purchasing authentic homemade and handcrafted products.

## 🌟 Vision and Purpose

HomieMart aims to empower homemakers by transforming their passions—whether it's cooking, crafting, gardening, or offering digital services—into thriving business ventures. It supports both sellers and buyers by:

- Offering a platform for homemakers to showcase and sell their products
- Providing customers with unique, personalized, and high-quality items
- Encouraging community-driven, sustainable commerce

## ⚙️ Technologies Used

- **Frontend**:  
  `HTML5`, `CSS3`, `JavaScript`  
- **Backend**:  
  `PHP`, `MySQL` (via `phpMyAdmin`)
- **Development Environment**:  
  `WAMP Server` (Windows Apache MySQL PHP)

## 🧩 Features

- Seller registration and login
- Product listing with images, descriptions, and pricing
- Secure customer account creation and shopping cart
- Order placement and management system
- Admin panel to manage users, products, and orders
- Mobile-responsive design
- Database management through PHPMyAdmin

## 🛠️ How to Run This Project Locally

1. **Install WAMP Server**
   - Download and install from [https://www.wampserver.com](https://www.wampserver.com)

2. **Clone or Download this Project**
   - Place the project folder inside the `www` directory in your WAMP installation path.

3. **Start WAMP Server**
   - Launch WAMP and ensure all services (Apache, MySQL) are running.

4. **Create the Database**
   - Open `phpMyAdmin` via `http://localhost/phpmyadmin`
   - Create a new database (e.g., `homiemart_db`)
   - Import the provided `.sql` file (if included) to set up the tables.

5. **Configure Database Connection**
   - Open the `config.php` (or similar) file and update with your database credentials:
     ```php
     $host = "localhost";
     $user = "root";
     $password = "";
     $database = "homiemart_db";
     ```

6. **Run the App**
   - Go to your browser and visit `http://localhost/homiemart`

## 📁 Project Structure

homiemart/
- index.html / index.php # Landing page
- login.php / register.php # User authentication
- seller-dashboard.php # Seller panel
- admin/ # Admin dashboard
- assets/ # Images, CSS, JS files
- includes/ # Reusable components
- database/ # SQL scripts, config files

---

## 🤝 Author

- **Mohana Dharshan**
- GitHub: [github.com/ITzDharshan]([https://github.com/yourusername](https://github.com/ITzDharshan))
- LinkedIn: [linkedin.com/in/MohanaDharshan](www.linkedin.com/in/mdharshan)

---
