<?php
session_start();
require_once __DIR__ . '/../Includes/basiccrud.php';
$dbConnCreator = new myConnexion('localhost', 'proyecto', 'root', '', 3306);
$conn = $dbConnCreator->connect();

if (isset($_SESSION["user_id"])) {
    header("Location: inicio.php");
    exit();
}

$user_register_error = "";
$username_error = "";
$email_error = "";
$phone_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($conn) {
        $username = $_POST['username'] ?? '';
        $nombres = $_POST['nombres'] ?? '';
        $apellidos = $_POST['apellidos'] ?? '';
        $email = $_POST['email'] ?? '';
        $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $telefono = $_POST['telefono'] ?? '';

        if ($password !== $confirm_password) {
            $user_register_error = "Las contraseñas no coinciden.";
        } else {
            $helper = new sqlHelper('Usuarios', $conn);

            if ($helper->count(['nombre_usuario' => $username]) > 0) {
                $username_error = "Este nombre de usuario ya está registrado!";
            }
            if ($helper->count(['email' => $email]) > 0) {
                $email_error = "Este correo ya está registrado!";
            }
            if ($helper->count(['numero_telefono' => $telefono]) > 0) {
                $phone_error = "Este número de teléfono ya está registrado!";
            }

            if (empty($username_error) && empty($email_error) && empty($phone_error)) {
                $userData = [
                    'nombre_usuario' => $username,
                    'nombres' => $nombres,
                    'apellidos' => $apellidos,
                    'email' => $email,
                    'fecha_nacimiento' => $fecha_nacimiento,
                    'contrasena' => $password,
                    'numero_telefono' => $telefono
                ];

                try {
                    $newId = $helper->insert_into($userData);
                    if ($newId) {
                        header("Location: login.php");
                        exit();
                    } else {
                        $user_register_error = "Error al registrar. Intente de nuevo.";
                    }
                } catch (Exception $e) {
                    $user_register_error = "Error: " . $e->getMessage();
                }
            }
        }
    } else {
        $user_register_error = "Error de conexión.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chak - Seguridad en Cascos</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="inicio.php">
                <i class="fas fa-helmet-safety me-2"></i>Rinos al volante
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="practicas_seguras.php">Practicas Seguras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reglamento_vial.php">Normativas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="lista_cascos.php">Cascos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="certificaciones.php">Certificaciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="noticias_accidentes.php">Ver Accidentes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Iniciar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card p-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-plus fa-3x mb-3" style="color: var(--primary-color);"></i>
                            <h2 class="section-title">Registro de Usuario</h2>
                            <p style="color: var(--soft-text);">
                                Completa el formulario para crear tu cuenta
                            </p>
                        </div>
                        <form method="post">

                            <div class="mb-3">
                                <label class="form-label">Nombre de usuario</label>
                                <input type="text" name="username" class="form-control" placeholder="Ej. usuario" required>
                            </div>
                            <?php
                            if ($username_error) echo "<p class='text-danger'>$username_error</p>";
                            ?>

                            <div class="mb-3">
                                <label class="form-label">Nombre(s)</label>
                                <input type="text" name="nombres" class="form-control" placeholder="Tu nombre" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Apellidos</label>
                                <input type="text" name="apellidos" class="form-control" placeholder="Tus apellidos" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Correo electrónico</label>
                                <input type="email" name="email" class="form-control" placeholder="correo@ejemplo.com" required>
                            </div>
                            <?php
                            if ($email_error) echo "<p class='text-danger'>$email_error</p>";
                            ?>

                            <div class="mb-3">
                                <label class="form-label">Fecha de nacimiento</label>
                                <input type="date" name="fecha_nacimiento" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Repita su Contraseña</label>
                                <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Número de teléfono</label>
                                <input type="tel" name="telefono" class="form-control" placeholder="Ej. +5212345678" required>
                            </div>
                            <?php
                            if ($phone_error) echo "<p class='text-danger'>$phone_error</p>";
                            ?>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-secondary-custom btn-lg">
                                    Registrarse
                                </button>
                                <?php
                                if ($user_register_error) echo "<p class='text-danger text-center'>Error al registrar usuario: $user_register_error</p>";
                                ?>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p style="color: var(--soft-text);">
                                ¿Ya tienes cuenta?
                                <a href="login.php" style="color: var(--primary-color);">
                                    Inicia sesión
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>