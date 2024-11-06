<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['rol'];

if ($role == 'admin') {
    $query = "SELECT lr.*, u.username as nombre_doctor 
              FROM loan_requests lr 
              JOIN users u ON lr.doctor_id = u.id 
              ORDER BY fecha_solicitud DESC";
} else {
    $query = "SELECT * FROM loan_requests 
              WHERE doctor_id = $user_id 
              ORDER BY fecha_solicitud DESC";
}

$requests = mysqli_query($conn, $query);

$notif_query = "SELECT * FROM notifications 
                WHERE user_id = $user_id AND status_leido = 0 
                ORDER BY creado_en DESC";
$notifications = mysqli_query($conn, $notif_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel - Sistema de Válvulas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: Arial, sans-serif;
        }
        .navbar {
            background: linear-gradient(135deg, #3b5998 0%, #192f6a 100%);
            padding: 1rem;
            margin-bottom: 2rem;
        }
        .navbar-brand {
            color: white !important;
            font-weight: bold;
        }
        .nav-link {
            color: rgba(255,255,255,0.85) !important;
        }
        .nav-link:hover {
            color: #cdd3e1 !important;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.15);
            margin-bottom: 2rem;
        }
        .card-header {
            background: #192f6a;
            color: white;
            border-bottom: none;
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0;
            font-weight: bold;
        }
        .table {
            margin-bottom: 0;
            font-size: 0.95rem;
        }
        .table th {
            color: #4a5568;
            font-weight: 700;
            background: #e9ecf1;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8fafc;
        }
        .table-striped tbody tr:nth-of-type(even) {
            background-color: #eef1f5;
        }
        .btn-primary {
            background: #3b5998;
            border: none;
            color: white;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #4a69a0;
        }
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            padding: 0.35rem;
            border-radius: 50%;
            background-color: #d9534f;
            color: white;
            font-size: 0.75rem;
        }
        .sidebar {
            background: #3b5998;
            color: white;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
        }
        .list-group-item {
            border: none;
            background: #f8f9fb;
            color: #495057;
        }
        .list-group-item:hover {
            background: #e9ecef;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-hospital me-2"></i>Sistema de Válvulas</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if ($role == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="registrar_usuario.php"><i class="fas fa-users me-2"></i>Gestionar Usuarios</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="#"><i class="fas fa-bell me-2"></i>Notificaciones
                            <?php if (mysqli_num_rows($notifications) > 0): ?>
                                <span class="notification-badge"><?php echo mysqli_num_rows($notifications); ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Solicitudes de Préstamo</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($role == 'medico'): ?>
                            <a href="nueva_solicitud.php" class="btn btn-primary mb-3">Nueva Solicitud</a>
                        <?php endif; ?>
                        <?php if ($role == 'admin'): ?>
                            <a href="registrar_usuario.php" class="btn btn-primary mb-3">Gestionar Usuarios</a>
                        <?php endif; ?>

                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <?php if ($role == 'admin'): ?>
                                        <th>Doctor</th>
                                    <?php endif; ?>
                                    <th>Paciente</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($requests)): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <?php if ($role == 'admin'): ?>
                                            <td><?php echo $row['nombre_doctor']; ?></td>
                                        <?php endif; ?>
                                        <td><?php echo $row['nombre_paciente']; ?></td>
                                        <td><?php echo $row['fecha_solicitud']; ?></td>
                                        <td><?php echo $row['status']; ?></td>
                                        <td>
                                            <a href="view_request.php?id=<?php echo $row['id']; ?>" class="btn btn-info btn-sm">Ver</a>
                                            <?php if ($role == 'admin'): ?>
                                                <a href="update_status.php?id=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm">Actualizar</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="sidebar">
                    <h4>Notificaciones</h4>
                    <div class="list-group">
                        <?php while ($notif = mysqli_fetch_assoc($notifications)): ?>
                            <div class="list-group-item">
                                <p><?php echo $notif['mensaje']; ?></p>
                                <small class="text-muted"><?php echo $notif['creado_en']; ?></small>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
