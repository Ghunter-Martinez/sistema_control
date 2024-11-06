<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['rol'] != 'medico') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $patient_name = mysqli_real_escape_string($conn, $_POST['nombre_paciente']);
    $valve_type = mysqli_real_escape_string($conn, $_POST['tipo_valvula']);
    $notes = mysqli_real_escape_string($conn, $_POST['notas']);
    $doctor_id = $_SESSION['user_id'];
    
    $query = "INSERT INTO loan_requests (doctor_id, nombre_paciente, tipo_valvula, notas) 
              VALUES ($doctor_id, '$patient_name', '$valve_type', '$notes')";
    
    if (mysqli_query($conn, $query)) {
        $admin_query = "SELECT id FROM users WHERE role = 'admin'";
        $admin_result = mysqli_query($conn, $admin_query);
        
        while ($admin = mysqli_fetch_assoc($admin_result)) {
            $notif_query = "INSERT INTO notifications (user_id, message) 
                           VALUES ({$admin['id']}, 'Nueva solicitud de préstamo creada')";
            mysqli_query($conn, $notif_query);
        }
        
        header('Location: panel.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Nueva Solicitud de Préstamo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f3f4f6;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            background-color: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }
        h2 {
            color: #4a5568;
            font-weight: 700;
        }
        label {
            font-weight: 600;
            color: #2d3748;
        }
        .form-control {
            border-radius: 5px;
            padding: 0.75rem;
            border: 1px solid #cbd5e0;
        }
        .btn-primary {
            background-color: #667eea;
            border: none;
            border-radius: 5px;
            padding: 0.6rem 1.2rem;
            transition: background 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #5a67d8;
        }
        .btn-secondary {
            background-color: #e2e8f0;
            color: #2d3748;
            border: none;
            border-radius: 5px;
            padding: 0.6rem 1.2rem;
            transition: background 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #cbd5e0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Nueva Solicitud de Préstamo</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Nombre del Paciente:</label>
                <input type="text" name="nombre_paciente" class="form-control" required placeholder="Ingrese el nombre del paciente">
            </div>
            <div class="mb-3">
                <label>Tipo de Válvula:</label>
                <select name="tipo_valvula" class="form-control" required>
                    <option value="tipo1">Tipo 1</option>
                    <option value="tipo2">Tipo 2</option>
                    <option value="tipo3">Tipo 3</option>
                </select>
            </div>
            <div class="mb-3">
                <label>Notas:</label>
                <textarea name="notas" class="form-control" rows="4" placeholder="Ingrese cualquier nota adicional"></textarea>
            </div>
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Enviar Solicitud</button>
                <a href="panel.php" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>
