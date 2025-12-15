<?php
session_start();
require_once __DIR__ . '/../Includes/basiccrud.php';
$dbConnCreator = new myConnexion('db', 'proyecto', 'angel', '1234', 3306);
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

$accidentes = [];
if ($conn) {
    $helper = new sqlHelper('Accidentes', $conn);
    // Sort by date descending
    $accidentes = $helper->select([], [], ['fecha' => 'DESC']);
    if (!$accidentes) $accidentes = [];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accidentes - Chak</title>
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
                        <a class="nav-link active" href="noticias_accidentes.php">Ver Accidentes</a>
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
            <h1 class="display-4 fw-bold" style="color: var(--primary-color);">Registro de Accidentes</h1>
            <p class="lead">Conoce los riesgos y concientiza sobre la seguridad vial</p>
        </div>
    </div>

    <div class="container my-5">
        <?php if (empty($accidentes)): ?>
            <div class="alert alert-info text-center">
                No hay registros de accidentes recientes.
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($accidentes as $accidente): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 shadow-sm border-0">
                            <?php if (!empty($accidente['imagen_evidencia'])): ?>
                                <img src="<?php echo htmlspecialchars($accidente['imagen_evidencia']); ?>" class="card-img-top" alt="Evidencia" style="height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="fas fa-camera-retro fa-3x"></i>
                                </div>
                            <?php endif; ?>

                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title text-danger mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Accidente</h5>
                                    <span class="badge <?php echo $accidente['nivel_gravedad'] == 'Alta' ? 'bg-danger' : ($accidente['nivel_gravedad'] == 'Media' ? 'bg-warning text-dark' : 'bg-success'); ?>">
                                        <?php echo htmlspecialchars($accidente['nivel_gravedad'] ?? 'N/A'); ?>
                                    </span>
                                </div>
                                <h6 class="card-subtitle mb-2 text-muted">
                                    <i class="far fa-calendar-alt me-1"></i> <?php echo date('d/m/Y', strtotime($accidente['fecha'])); ?>
                                </h6>
                                <p class="card-text small mb-2">
                                    <strong>Lugar:</strong> <?php echo htmlspecialchars($accidente['lugar']); ?>
                                </p>
                                <p class="card-text">
                                    <?php echo htmlspecialchars(substr($accidente['descripcion'], 0, 100)) . '...'; ?>
                                </p>
                                <ul class="list-unstyled small text-muted mt-3">
                                    <li><strong>Causa:</strong> <?php echo htmlspecialchars($accidente['causa']); ?></li>
                                    <li><strong>Lesionados:</strong> <?php echo $accidente['lesionados']; ?></li>
                                    <li><strong>Uso de Casco:</strong> <?php echo $accidente['uso_casco'] ? '<span class="text-success">Sí</span>' : '<span class="text-danger">No</span>'; ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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