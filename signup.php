<?php
// signup.php
session_start();
require_once("Database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $username = $_POST['username'];
   $email = $_POST['email'];
   $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
   $gender = $_POST['gender'];

   $DB = new Database();
   $query = "INSERT INTO users (username, email, password, gender, date) VALUES (:username, :email, :password, :gender, NOW())";
   $params = [
      'username' => $username,
      'email' => $email,
      'password' => $password,
      'gender' => $gender
   ];

   if ($DB->write($query, $params)) {
      header("Location: login.php");
      exit();
   } else {
      echo "Error creating account.";
   }
}
?>

<form method="POST">
   Username: <input type="text" name="username" required><br>
   Email: <input type="email" name="email" required><br>
   Password: <input type="password" name="password" required><br>
   Gender: <select name="gender">
      <option value="Male">Male</option>
      <option value="Female">Female</option>
   </select><br>
   <button type="submit">Sign Up</button>
</form>