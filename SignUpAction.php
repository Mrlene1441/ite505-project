<?php
//Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "taskmaster";

$conn = new mysqli($servername, $username, $password, $dbname);

//Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

//Get data from the form 
$first_name = trim($_POST['name']);
$last_name = trim($_POST['lastname']);
$email = trim($_POST['email']);
$password = $_POST['psw'];
$confirm_password = $_POST['psw-repeat'];

//Validate inputs
if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($confirm_password)) {
    die("All fields are required.");
}

//Validate passwords
if ($password !== $confirm_password) {
    die("Passwords do not match.");
}

//Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Invalid email format.");
}

//Check if email already exists
$stmt = $conn->prepare("SELECT * FROM user_tb WHERE USER_EMAIL = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    die("Email already exists.");
}

//Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

//Insert data into the database using
$stmt = $conn->prepare("INSERT INTO user_tb (USER_FNAME, USER_LNAME, USER_EMAIL, USER_PASS) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $first_name, $last_name, $email, $hashed_password);

if ($stmt->execute()) {
    echo "Sign up successful!";
} else {
    echo "Error: " . $stmt->error;
}

//Close connections
$stmt->close();
$conn->close();
?>
