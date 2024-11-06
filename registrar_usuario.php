<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'admin') {
    header('Location: login.php');
    exit();
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $role = mysqli_real_escape_string($conn, $_POST['rol']);
    
    $check_query = "SELECT * FROM users WHERE username = '$username' OR email = '$email'";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "El usuario o email ya existe en el sistema.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO users (username, email, password, rol) 
                 VALUES ('$username', '$email', '$hashed_password', '$rol')";
        
        if (mysqli_query($conn, $query)) {
            $success_message = "Usuario creado exitosamente.";
        } else {
            $error_message = "Error al crear el usuario: " . mysqli_error($conn);
        }
    }
}

$users_query = "SELECT id, username, email, rol FROM users WHERE id != {$_SESSION['user_id']}";
$users_result = mysqli_query($conn, $users_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestión de Usuarios - Sistema de Válvulas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 1rem;
            margin-bottom: 2rem;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        .card-header {
            background: white;
            border-bottom: 1px solid #e9ecef;
            padding: 1.5rem;
            border-radius: 15px 15px 0 0 !important;
        }
        .btn {
            border-radius: 8px;
            padding: 0.5rem 1rem;
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: translateY(-1px);
        }
        .table th {
            font-weight: 600;
            color: #4a5568;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand text-white" href="panel.php">
                <i class="fas fa-hospital me-2"></i>Sistema de Válvulas
            </a>
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link text-white" href="logout.php">
                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <h2>Gestión de Usuarios</h2>
                <a href="panel.php" class="btn btn-secondary mb-3">Volver al Panel</a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Registrar Nuevo Usuario</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($success_message): ?>
                            <div class="alert alert-success"><?php echo $success_message; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger"><?php echo $error_message; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label>Nombre de Usuario:</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label>Email:</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label>Contraseña:</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label>Rol:</label>
                                <select name="role" class="form-control" required>
                                    <option value="medico">Médico</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Registrar Usuario</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Usuarios Existentes</h3>
                    </div>
                    <div class="card-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                                <tr>
                                    <td><?php echo $user['username']; ?></td>
                                    <td><?php echo $user['email']; ?></td>
                                    <td><?php echo $user['rol']; ?></td>
                                    <td>
                                        <a href="editar_usuario.php?id=<?php echo $user['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                                        <a href="borrar_usuario.php?id=<?php echo $user['id']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('¿Está seguro de eliminar este usuario?')">
                                            Eliminar
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>  