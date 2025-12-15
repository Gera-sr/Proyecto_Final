<?php
session_start();
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
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Normativas y Reglamento Vial</title>
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

    <header class="hero-section">
        <div class="container">
            <h1 class="display-4 fw-bold">Normativas y Reglamento Vial</h1>
            <p class="lead"><u>Circular con responsabilidad es un deber de todos</u></p>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title">Reglamento Vial en México</h2>
            </div>
            <p class="text-center mx-auto" style="max-width: 850px; color: var(--soft-text);">
                En México, el reglamento vial establece las normas que deben seguir todos los
                conductores para garantizar una circulación segura. Estas leyes buscan prevenir
                accidentes, proteger la vida y fomentar una convivencia vial responsable.
            </p>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">

                <div class="col-md-6 col-lg-3">
                    <div class="card card-custom h-100 text-center p-3">
                        <i class="fas fa-helmet-safety fa-3x mb-3"></i>
                        <h5>Uso obligatorio del casco</h5>
                        <p>
                            El uso de casco certificado es obligatorio para motociclistas y
                            acompañantes. Debe estar correctamente ajustado.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card card-custom h-100 text-center p-3">
                        <i class="fas fa-id-card fa-3x mb-3"></i>
                        <h5>Documentación</h5>
                        <p>
                            Es obligatorio portar licencia vigente, tarjeta de circulación
                            y placas visibles.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card card-custom h-100 text-center p-3">
                        <i class="fas fa-tachometer-alt fa-3x mb-3"></i>
                        <h5>Límites de velocidad</h5>
                        <p>
                            Respetar los límites de velocidad establecidos según la vialidad
                            reduce significativamente el riesgo de accidentes.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card card-custom h-100 text-center p-3">
                        <i class="fas fa-ban fa-3x mb-3"></i>
                        <h5>Alcohol y conducción</h5>
                        <p>
                            Conducir bajo los efectos del alcohol está prohibido y es una de
                            las principales causas de accidentes viales.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title">Lineamientos esenciales</h2>
            </div>
            <ul class="mx-auto" style="max-width: 800px; color: var(--soft-text);">
                <li>Respetar semáforos y señales de tránsito.</li>
                <li>Utilizar direccionales para cambiar de carril.</li>
                <li>No usar el celular mientras se conduce.</li>
                <li>Ceder el paso a peatones y vehículos de emergencia.</li>
                <li>Mantener una distancia segura entre vehículos.</li>
            </ul>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="section-title">Conducir bien también salva vidas</h2>
            <p class="mx-auto" style="max-width: 800px; color: var(--soft-text);">
                Cumplir con el reglamento vial no solo evita multas, también protege tu vida,
                la de tus acompañantes y la de los demás usuarios de la vía.
            </p>
        </div>
    </section>

    <footer class="text-center">
        <div class="container">
            <p>&copy; 2025 Proyecto - Practica 3. CMS: Todos los derechos reservados.</p>
            <a style="color: var(--secondary-color);" href="contacto.php">Contacto</a>
        </div>
    </footer>

    <script src="../js/bootstrap.bundle.min.js"></script>
</body>

</html>