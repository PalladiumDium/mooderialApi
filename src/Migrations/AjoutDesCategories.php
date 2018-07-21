<?php

$servername = "localhost";
$username = "root";
$password = "root";
$db = "Mooderial";

// Create connection
$conn = new mysqli($servername, $username, $password);
// Check connection
if ($conn->connect_error) {
	die("Connection failed: " . mysqli_error($conn));
}
mysqli_select_db($conn, $db);
	
$sql = "INSERT INTO category(label, on_discount) 
		VALUES('Périphériques PC', 0);";
$sql2 = "INSERT INTO category(label, on_discount) 
		VALUES('Cartes Mères', 0);";
$sql3 = "INSERT INTO category(label, on_discount) 
		VALUES('Cartes Graphiques', 1);";
$sql4 = "INSERT INTO category(label, on_discount) 
		VALUES('Ventilateurs', 1);";

for($i = 1; $i < 5; $i++) {
	switch ($i) {
		case 1:
			$result = mysqli_query($conn, $sql);
			break;
		case 2:
			$result = mysqli_query($conn, $sql2);
			break;
		case 3:
			$result = mysqli_query($conn, $sql3);
			break;
		case 4:
			$result = mysqli_query($conn, $sql4);
			break;
	}

	if($result) {
		echo "OK !";
	} else {
		echo "No Results";
	}
}

mysqli_close($conn);
?>