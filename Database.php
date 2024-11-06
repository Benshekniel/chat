<?php
// Database.php
class Database
{
   private $host = "localhost";
   private $username = "root"; // Update with your MySQL credentials
   private $password = ""; // Update with your MySQL credentials
   private $dbname = "mychat_db"; // Your database name
   private $conn;

   public function __construct()
   {
      try {
         $this->conn = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password);
         $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      } catch (PDOException $e) {
         die("Connection failed: " . $e->getMessage());
      }
   }

   // Method to execute read queries (select)
   public function read($query, $params = [])
   {
      $stmt = $this->conn->prepare($query);
      $stmt->execute($params);
      return $stmt->fetchAll(PDO::FETCH_ASSOC);
   }

   // Method to execute write queries (insert, update, delete)
   public function write($query, $params = [])
   {
      $stmt = $this->conn->prepare($query);
      return $stmt->execute($params);
   }
}
