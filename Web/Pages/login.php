<?php
session_start();
require_once __DIR__ . '/../Includes/basiccrud.php';
$dbConnCreator = new myConnexion('localhost', 'proyecto', 'root', '', 3306);
$conn = $dbConnCreator->connect();

if (isset($_SESSION["user_id"])) {
    header("Location: inicio.php");
    exit();
}

$user_login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($conn) {
        $userInput = $_POST['usuario'] ?? '';
        $passInput = $_POST['password'] ?? '';

        $helper = new sqlHelper('Usuarios', $conn);

        $userRecord = $helper->selectOne([], ['nombre_usuario' => $userInput]);

        if ($userRecord && $passInput === $userRecord['contrasena']) {
            $_SESSION['user_id'] = $userRecord['id'];
            $_SESSION['username'] = $userRecord['nombre_usuario'];
            $_SESSION['nombres'] = $userRecord['nombres'];

            $adminHelper = new sqlHelper('Admins', $conn);
            $adminRecord = $adminHelper->selectOne([], ['user_id' => $userRecord['id']]);

            if ($adminRecord) {
                $_SESSION['is_admin'] = true;
            } else {
                $_SESSION['is_admin'] = false;
            }

            header("Location: inicio.php");
            exit();
        } else {
            $user_login_error = "Usuario o contraseña incorrectos.";
        }
    } else {
        $user_login_error = "Error de sistema.";
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
                        <a class="nav-link" href="registro.php">Registrarse</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card p-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-lock fa-3x mb-3" style="color: var(--primary-color);"></i>
                            <h2 class="section-title">Iniciar Sesión</h2>
                            <p style="color: var(--soft-text);">
                                Accede a tu cuenta para continuar
                            </p>
                        </div>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Usuario</label>
                                <input type="text" name="usuario" class="form-control" placeholder="usuario" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label">Contraseña</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>

                            <div class="text-end mb-4">
                                <a href="#" style="color: var(--primary-color); font-size: 0.9rem;">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-secondary-custom btn-lg">
                                    Iniciar Sesión
                                </button>
                                <?php
                                echo "<p class='text-danger text-center'>Error al iniciar sesión: $user_login_error</p>";
                                ?>
                            </div>
                        </form>

                        <div class="text-center mt-4">
                            <p style="color: var(--soft-text);">
                                ¿No tienes cuenta?
                                <a href="registro.php" style="color: var(--primary-color);">
                                    Regístrate aquí
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