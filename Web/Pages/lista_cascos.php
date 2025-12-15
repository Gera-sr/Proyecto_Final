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

// Fetch helmets
$cascos = [];
if ($conn) {
    $helper = new sqlHelper('Cascos', $conn);
    // Simple fetch all or add filters later
    // The UI implies pagination, but let's just fetch all/limit for now to make it work
    $cascos = $helper->select();
    if (!$cascos) $cascos = [];
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
                        <a class="nav-link" href="inicio.php">Inicio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="practicas_seguras.php">Practicas Seguras</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reglamento_vial.php">Normativas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="certificaciones.php">Certificaciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="noticias_accidentes.php">Ver Accidentes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="faq.php">FAQ</a>
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

    <div class="bg-light py-5 mb-5">
        <div class="container">
            <h1 class="display-4 fw-bold text-center" style="color: var(--primary-color);">Tipos de cascos</h1>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row">
            <div class="col-lg-3 mb-4">
                <div class="card card-custom p-3">
                    <h5 class="mb-3">Filtrar por</h5>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tipo</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="cat1">
                            <label class="form-check-label" for="cat1">Integral</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="cat2">
                            <label class="form-check-label" for="cat2">Abatible</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="cat3">
                            <label class="form-check-label" for="cat3">Cross</label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Certificación</label>
                        <select class="form-select">
                            <option>Todas</option>
                            <option>DOT</option>
                            <option>ECE</option>
                            <option>SNELL</option>
                        </select>
                    </div>
                    <button class="btn btn-primary-custom w-100">Aplicar Filtros</button>
                </div>
            </div>

            <!--
            Loading
            <div class="card" aria-hidden="true">
            <img src="..." class="card-img-top" alt="...">
            <div class="card-body">
                <h5 class="card-title placeholder-glow">
                <span class="placeholder col-6"></span>
                </h5>
                <p class="card-text placeholder-glow">
                <span class="placeholder col-7"></span>
                <span class="placeholder col-4"></span>
                <span class="placeholder col-4"></span>
                <span class="placeholder col-6"></span>
                <span class="placeholder col-8"></span>
                </p>
                <a class="btn btn-primary disabled placeholder col-6" aria-disabled="true"></a>
            </div>
            </div>

            Active
            <div class="col-md-6 col-xl-4">
            <div class="card card-custom h-100">
                <img loading="lazy" src="https://placehold.co/400x300?text=Casco+2" class="card-img-top" alt="Casco 2">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Raptor 3000</h5>
                    <p class="card-text text-muted mb-1">ECE 22.05</p>
                    <p class="card-text small">Diseño aerodinámico para alta velocidad.</p>
                    <div class="mt-auto">
                        <h4 class="fw-bold mb-3" style="color: var(--primary-color);">$2,500.00</h4>
                        <a href="detalle_casco.php" class="btn btn-outline-danger w-100">Ver Detalles</a>
                    </div>
                </div>
            </div>
        </div>
            -->

            <div class="col-lg-9">
                <div class="row g-4">
                    <?php if (empty($cascos)): ?>
                        <div class="col-12 text-center">
                            <p>No se encontraron cascos.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($cascos as $casco): ?>
                            <div class="col-md-6 col-xl-4">
                                <div class="card card-custom h-100">
                                    <img loading="lazy" src="<?php echo htmlspecialchars($casco['imagen'] ?? 'https://placehold.co/400x300?text=No+Image'); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($casco['modelo']); ?>">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title"><?php echo htmlspecialchars($casco['marca'] . ' ' . $casco['modelo']); ?></h5>
                                        <p class="card-text text-muted mb-1"><?php echo htmlspecialchars($casco['certificacion']); ?></p>
                                        <p class="card-text small"><?php echo htmlspecialchars(substr($casco['descripcion'], 0, 100)) . '...'; ?></p>
                                        <div class="mt-auto">
                                            <h4 class="fw-bold mb-3" style="color: var(--primary-color);">$<?php echo number_format($casco['precio_aprox'], 2); ?></h4>
                                            <!-- Assuming detalle_casco.php takes an ID -->
                                            <a href="detalle_casco.php?id=<?php echo $casco['id']; ?>" class="btn btn-outline-danger w-100">Ver Detalles</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <nav aria-label="Page navigation" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled"><a class="page-link" href="#">Anterior</a></li>
                        <li class="page-item active"
                            style="background-color: var(--primary-color); border-color: var(--primary-color);"><a
                                class="page-link" href="#"
                                style="background-color: var(--primary-color); border-color: var(--primary-color); color: white;">1</a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#" style="color: var(--primary-color);">2</a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#" style="color: var(--primary-color);">3</a>
                        </li>
                        <li class="page-item"><a class="page-link" href="#"
                                style="color: var(--primary-color);">Siguiente</a></li>
                    </ul>
                </nav>

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