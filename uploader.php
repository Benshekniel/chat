<?php
// uploader.php
session_start();

if (!isset($_SESSION['userid'])) {
   echo json_encode(["status" => "error", "message" => "User not logged in."]);
   exit();
}

$uploadDir = 'uploads/';  // Directory to store uploaded files

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
   $file = $_FILES['file'];
   $fileName = basename($file['name']);
   $filePath = $uploadDir . $fileName;
   $fileTmpName = $file['tmp_name'];
   $fileSize = $file['size'];
   $fileType = $file['type'];
   $fileError = $file['error'];

   // Check if there was an error uploading the file
   if ($fileError !== UPLOAD_ERR_OK) {
      echo json_encode(["status" => "error", "message" => "File upload failed."]);
      exit();
   }

   // Check file size (max size 10MB)
   if ($fileSize > 10485760) {
      echo json_encode(["status" => "error", "message" => "File size exceeds the maximum limit of 10MB."]);
      exit();
   }

   // Allowed file types
   $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
   if (!in_array($fileType, $allowedTypes)) {
      echo json_encode(["status" => "error", "message" => "Unsupported file type."]);
      exit();
   }

   // Move the file to the uploads directory
   if (move_uploaded_file($fileTmpName, $filePath)) {
      echo json_encode(["status" => "success", "message" => "File uploaded successfully.", "file_path" => $filePath]);
   } else {
      echo json_encode(["status" => "error", "message" => "Failed to move the uploaded file."]);
   }
} else {
   echo json_encode(["status" => "error", "message" => "No file uploaded."]);
}
