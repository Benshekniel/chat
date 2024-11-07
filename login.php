<?php
// login.php
session_start();
require_once("Database.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $username = $_POST['username'];
   $password = $_POST['password'];

   $DB = new Database();
   $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
   $user = $DB->read($query, ['username' => $username]);

   if (count($user) > 0 && ($password === $user[0]['password'])) {
      $_SESSION['userid'] = $user[0]['id'];

      // Update user state to 1 (logged in)
      $updateStateQuery = "UPDATE users SET state = 1 WHERE id = :userid";
      $DB->write($updateStateQuery, ['userid' => $_SESSION['userid']]);

      // Update messages as received
      $updateQuery = "UPDATE message SET received = 1 WHERE receiver = :receiver AND received = 0";
      $DB->write($updateQuery, ['receiver' => $_SESSION['userid']]);

      header("Location: index.php");
      exit();
   } else {
      echo "Invalid username or password.";
   }
}
?>

<form method="POST">
   Username: <input type="text" name="username" required><br>
   Password: <input type="password" name="password" required><br>
   <button type="submit">Login</button>
</form>