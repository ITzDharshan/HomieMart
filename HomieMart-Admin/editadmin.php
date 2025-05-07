<?php include "db_connection.php"?>
 
<?php
   // checking if the variable is set or not and if set adding the set data value to variable userid
   if(isset($_GET['user_id']))
    {
      $userid = $_GET['user_id']; 
    }
      // SQL query to select all the data from the table where id = $userid
      $query="SELECT * FROM register WHERE id = $userid ";
      $view_users= mysqli_query($conn,$query);
 
      if($row = mysqli_fetch_assoc($view_users))
        {
          $id = $row['id'];
          $user = $row['fname'];
          $email = $row['email'];
          $pass = $row['password'];
        }
  
    if(isset($_POST['update'])) 
    {
        $user = $_POST['user'];
        $email = $_POST['email'];
        $pass = $_POST['pass'];
    
        // Hash the new password before storing it in the database
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);
        
        // SQL query to update the data in user table where the id = $userid 
        $query = "UPDATE register SET fname = '{$user}', email = '{$email}', password = '{$hashed_pass}' WHERE id = $userid";
        $update_user = mysqli_query($conn, $query);
        
        if ($update_user) {
            echo "<script type='text/javascript'>alert('User data updated successfully!'); window.location.href='users.php';</script>";
        } else {
            echo "<script type='text/javascript'>alert('Error updating user data!');</script>";
        }
    }                            
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="img/shopping.png">
    <title>Update User Details</title>
</head>
  <style>
      body {
        font-family: 'Poppins', sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background: url('img/password.jpg') no-repeat center center/cover;
    }

    .container {
        background-color: #fff;
        padding: 40px;
        background: rgba(225, 225, 225, 0.5);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        width: 400px;
    }

    .toast {
        padding: 10px;
        border-radius: 5px;
        color: #fff;
        margin-bottom: 20px;
    }

    .toast.success {
        background-color: rgb(0, 129, 30);
    }

    .toast.danger {
        background-color: rgb(124, 0, 12);
    }

    .toast.warning {
        background-color: rgb(134, 101, 0);
        color: #000;
    }

    form {
        display: flex;
        flex-direction: column;
    }

    form .form-control {
        margin-bottom: 15px;
    }

    label {
        margin-bottom: 5px;
        font-weight: lighter;
    }

    input {
        width: 95%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        background: #c0c0c0;
        margin-top: 10px;
    }

    button {
        background-color: royalblue;
        color: #fff;
        border: none;
        padding: 10px;
        border-radius: 20px;
        cursor: pointer;
        font-weight: lighter;
        width: 80%;
        display: block;
        margin: 0 auto;
        text-align: center;
        margin-top: 10px;
    }

    button:hover {
        background-color: rgb(50, 81, 175);
    }

    h1 {
        font-weight: lighter;
        margin-bottom: 20px;
    }

    .back-btn {
        position: absolute;
        top: 20px;
        left: 20px;
        background-color: rgb(243, 156, 18);
        padding: 10px;
        color: white;
        border-radius: 5px;
        text-decoration: none;
    }

    .back-btn:hover {
        background-color: rgb(230, 119, 0);
    }

  </style>
<body>
  <!-- Back button positioned at the top-left corner -->
  <a href="users.php" class="back-btn">Back</a>
  <div class="container">
      <form action="" method="post">

      <h1 class="text-center">Update User Details</h1>
          <div class="form-group">
              <label for="user">Username</label>
              <input type="text" name="user" class="form-control" value="<?php echo isset($user) ? $user : ''; ?>">
          </div>

          <div class="form-group">
              <label for="email">Email ID</label>
              <input type="text" name="email" class="form-control" value="<?php echo isset($email) ? $email : ''; ?>">
          </div>
          <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone else.</small>

          <div class="form-group">
              <label for="pass">Password</label>
              <input type="password" name="pass" class="form-control" value="<?php echo isset($pass) ? $pass : ''; ?>">
          </div>

          <div class="form-group">
              <button type="submit" name="update" class="btn-update">Update</button>
          </div>
      </form>
  </div>
</body>
</html>