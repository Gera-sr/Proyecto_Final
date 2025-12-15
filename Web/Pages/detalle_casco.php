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

$casco = null;
if ($conn && isset($_GET['id'])) {
    $helper = new sqlHelper('Cascos', $conn);
    $casco = $helper->selectOne([], ['id' => $_GET['id']]);
}

if (!$casco) {
    header("Location: lista_cascos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles del Casco - Chak</title>
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
                        <a class="nav-link" href="inicio.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="practicas_seguras.php">Practicas Seguras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reglamento_vial.php">Normativas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="lista_cascos.php">Cascos</a>
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

    <div class="container my-5">
        <div class="row">
            <div class="col-md-6">
                <img src="<?php echo htmlspecialchars($casco['imagen'] ?? 'https://placehold.co/600x400?text=No+Image'); ?>"
                    class="img-fluid rounded shadow-sm" alt="<?php echo htmlspecialchars($casco['modelo']); ?>">
            </div>
            <div class="col-md-6">
                <h2 class="display-5 fw-bold mb-3" style="color: var(--primary-color);">
                    <?php echo htmlspecialchars($casco['marca'] . ' ' . $casco['modelo']); ?>
                </h2>
                <div class="mb-3">
                    <span class="badge bg-secondary me-2"><?php echo htmlspecialchars($casco['tipo']); ?></span>
                    <span class="badge bg-info text-dark"><?php echo htmlspecialchars($casco['certificacion']); ?></span>
                </div>
                <h3 class="text-danger mb-4">$<?php echo number_format($casco['precio_aprox'], 2); ?></h3>

                <h5 class="custom-underline mb-3">Descripción</h5>
                <p class="lead" style="font-size: 1.1rem;">
                    <?php echo nl2br(htmlspecialchars($casco['descripcion'])); ?>
                </p>

                <div class="mt-5">
                    <button class="btn btn-primary-custom btn-lg w-100 mb-3"><i class="fas fa-shopping-cart me-2"></i>Dónde Comprar</button>
                    <a href="lista_cascos.php" class="btn btn-outline-secondary w-100">Volver al listado</a>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-center mt-auto">
        <div class="container">
            <p>&copy; 2025 Proyecto - Practica 3. CMS: Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>