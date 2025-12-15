<?php
session_start();
require_once __DIR__ . '/../Includes/basiccrud.php';
$dbConnCreator = new myConnexion('localhost', 'proyecto', 'root', '', 3306);
$conn = $dbConnCreator->connect();

$login_required = true;
$admin = false;
$user = "";

if (isset($_SESSION["user_id"])) {
    $login_required = false;
    $user = $_SESSION["username"] ?? "Usuario";
    if (isset($_SESSION["is_admin"]) && $_SESSION["is_admin"]) {
        $admin = true;
    }
}

$contacto_status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($conn) {
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $asunto = $_POST['asunto'] ?? '';
        $mensaje = $_POST['mensaje'] ?? '';

        if (!empty($nombre) && !empty($email) && !empty($mensaje)) {
            $helper = new sqlHelper('Mensajes', $conn);
            $data = [
                'nombre' => $nombre,
                'email' => $email,
                'asunto' => $asunto,
                'mensaje' => $mensaje
            ];

            try {
                if ($helper->insert_into($data)) {
                    $contacto_status = "Mensaje enviado con éxito.";
                } else {
                    $contacto_status = "Error al enviar mensaje.";
                }
            } catch (Exception $e) {
                $contacto_status = "Error: " . $e->getMessage();
            }
        } else {
            $contacto_status = "Por favor completa todos los campos requeridos.";
        }
    } else {
        $contacto_status = "Error de conexión.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - Chak</title>
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
                    <?php
                    if ($login_required) {
                        echo
                        "<li class='nav-item'>
                            <a class='nav-link' href='login.php'>Iniciar Sesión</a>
                        </li>
                        ";
                    } else {
                        echo "
                        <li class='nav-item dropdown'>
                        <a class='nav-link dropdown-toggle' id='navbarDropdown' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
                        <i class='fas fa-user' style='color: var(--secondary-color);'></i>
                            $user
                        </a>
                        <ul class='dropdown-menu' aria-labelledby='navbarDropdown'>
                        ";
                        if ($admin) {
                            echo
                            "<li><a class='dropdown-item' href='admin.php'>Admin</a></li>
                            </li>";
                        }
                        echo
                        "<li><a class='dropdown-item' href='logout.php'>Cerrar Sesión</a></li>
                        </ul>
                        </li>";
                    }
                    ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="bg-light py-5">
        <div class="container text-center">
            <h1 class="display-4 fw-bold" style="color: var(--primary-color);">Contáctanos</h1>
            <p class="lead">Envíanos un mensaje con sus dudas, o visítanos.</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row">

            <div class="col-lg-7 mb-4">
                <div class="card p-4">
                    <h3>Envíanos un mensaje</h3>
                    <?php if ($contacto_status): ?>
                        <div class="alert alert-info">
                            <?php echo $contacto_status; ?>
                        </div>
                    <?php endif; ?>
                    <form method="post">
                        <div class="mb-3">
                            <label class="form-label">Nombre Completo</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Tu nombre" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control" placeholder="tucorreo@ejemplo.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Asunto</label>
                            <input type="text" name="asunto" class="form-control" placeholder="Consulta sobre...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mensaje</label>
                            <textarea name="mensaje" class="form-control" rows="5" placeholder="Escribe tu mensaje aquí..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary-custom px-5">Enviar</button>
                    </form>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card bg-light p-4 mb-4">
                    <h4 class="mb-3" style="color: var(--primary-color);">Información de Contacto</h4>
                    <ul class="list-unstyled">
                        <li class="mb-3"><i class="fas fa-map-marker-alt me-3"
                                style="color: var(--secondary-color);"></i>Calle con direcciony numero, Ciudad, México</li>
                        <li class="mb-3"><i class="fas fa-phone me-3" style="color: var(--secondary-color);"></i>+52 123
                            456 7890</li>
                        <li class="mb-3"><i class="fas fa-envelope me-3"
                                style="color: var(--secondary-color);"></i>contacto@rinos_rojos.com</li>
                    </ul>
                </div>

                <div class="card p-4">
                    <h4 class="mb-2" style="color: var(--primary-color);">Acerca de Nosotros</h4>
                    <p>Somos una iniciativa dedicada a promover la seguridad vial.</p>
                    <p>Nuestro objetivo es promover el uso adecuado del casco y fomentar hábitos de conducción responsable para reducir accidentes y proteger la integridad de los motociclistas, y así, reducir el índice de lesiones en accidentes de motocicleta proporcionando equipo de protección accesible y confiable.</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center">
        <div class="container">
            <p>&copy; 2025 Proyecto - Practica 3. CMS: Todos los derechos reservados.</p>
            <a style="color: var(--secondary-color);" href="contacto.php">Contacto</a>
        </div>
    </footer>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>