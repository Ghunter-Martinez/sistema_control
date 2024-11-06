<?php
$password = "admin123"; 
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO users (username, email, password, role) 
        VALUES ('admin', 'admin@example.com', '$hashed_password', 'admin');";

echo "Tu contraseña de admin será: admin123\n";
echo "SQL para copiar:\n";
echo $sql;
?>