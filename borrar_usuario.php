<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'admin') {
    header('Location: login.php');
    exit();
}

if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    
    if ($user_id != $_SESSION['user_id']) {
        $query = "DELETE FROM users WHERE id = $user_id";
        mysqli_query($conn, $query);
    }
}

header('Location: registrar_usuario.php');
exit();
?>