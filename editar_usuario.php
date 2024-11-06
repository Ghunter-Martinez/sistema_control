<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

$success_message = '';
$error_message = '';
$user_data = null;

if (isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    $query = "SELECT * FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $query);
    $user_data = mysqli_fetch_assoc($result);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = (int)$_POST['user_id'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    
    $update_query = "UPDATE users SET email = '$email', role = '$role'";
    
    if (!empty($_POST['password'])) {
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $update_query .= ", password = '$hashed_password'";
    }
    
    $update_query .= " WHERE id = $user_id";
    
    if (mysqli_query($conn, $update_query)) {
        $success_message = "Usuario actualizado exitosamente.";
        $query = "SELECT * FROM users WHERE id = $user_id";
        $result = mysqli_query($conn, $query);
        $user_data = mysqli_fetch_assoc($result);
    } else {
        $error_message = "Error al actualizar el usuario.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="card">
                    <div class="card-header">
                        <h3>Editar Usuario</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($success_message): ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>

                        <?php if ($user_data): ?>
                            <form method="POST">
                                <input type="hidden" name="user_id" value="<?php echo $user_data['id']; ?>">
                                
                                <div class="mb-3">
                                    <label>Nombre de Usuario:</label>
                                    <input type="text" class="form-control" value="<?php echo $user_data['username']; ?>" disabled>
                                </div>
                                
                                <div class="mb-3">
                                    <label>Email:</label>
                                    <input type="email" name="email" class="form-control" value="<?php echo $user_data['email']; ?>" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label>Nueva Contraseña (dejar en blanco para mantener la actual):</label>
                                    <input type="password" name="password" class="form-control">
                                </div>
                                
                                <div class="mb-3">
                                    <label>Rol:</label>
                                    <select name="role" class="form-control" required>
                                        <option value="medico" <?php echo $user_data['role'] == 'medico' ? 'selected' : ''; ?>>Médico</option>
                                        <option value="admin" <?php echo $user_data['role'] == 'admin' ? 'selected' : ''; ?>>Administrador</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
                                <a href="registrar_usuario.php" class="btn btn-secondary">Volver</a>
                            </form>
                        <?php else: ?>
                            <div class="alert alert-danger">Usuario no encontrado.</div>
                            <a href="resgistrar_ususario.php" class="btn btn-secondary">Volver</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>