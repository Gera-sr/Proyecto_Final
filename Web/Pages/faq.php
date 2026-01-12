<?php
session_start();
require_once __DIR__ . '/../Includes/config.php';

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

$faqs = [];
if ($conn) {
    $helper = new sqlHelper('FAQ', $conn);
    $faqs = $helper->select([], [], ['orden' => 'ASC']);
    if (!$faqs)
        $faqs = [];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Chak</title>
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
            <h1 class="display-4 fw-bold" style="color: var(--primary-color);">Preguntas Frecuentes</h1>
            <p class="lead">Resuelve tus dudas sobre seguridad y normativas</p>
        </div>
    </div>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <?php if (empty($faqs)): ?>
                    <div class="alert alert-warning text-center">
                        No hay preguntas frecuentes registradas por el momento.
                    </div>
                <?php else: ?>
                    <div class="accordion" id="accordionFAQ">
                        <?php foreach ($faqs as $index => $faq): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?php echo $faq['id']; ?>">
                                    <button class="accordion-button <?php echo $index !== 0 ? 'collapsed' : ''; ?>"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse<?php echo $faq['id']; ?>"
                                        aria-expanded="<?php echo $index === 0 ? 'true' : 'false'; ?>"
                                        aria-controls="collapse<?php echo $faq['id']; ?>">
                                        <?php echo htmlspecialchars($faq['pregunta']); ?>
                                    </button>
                                </h2>
                                <div id="collapse<?php echo $faq['id']; ?>"
                                    class="accordion-collapse collapse <?php echo $index === 0 ? 'show' : ''; ?>"
                                    aria-labelledby="heading<?php echo $faq['id']; ?>" data-bs-parent="#accordionFAQ">
                                    <div class="accordion-body">
                                        <?php echo nl2br(htmlspecialchars($faq['respuesta'])); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="text-center mt-5">
        <div class="container">
            <p>&copy; 2025 Proyecto - Practica 3. CMS: Todos los derechos reservados.</p>
            <a style="color: var(--secondary-color);" href="contacto.php">Contacto</a>
        </div>
    </footer>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>