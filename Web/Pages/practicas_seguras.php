<?php
session_start();
$login_required = true;
$admin = false;
if(isset($_SESSION["something"]))
{
    //Session started
    $login_required = false;
}
$user = "";
if(!$login_required)
{
    $user = "CapinonM";
}
//TODO
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prácticas Seguras de Conducción</title>
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
                    if($login_required)
                    {
                        echo
                        "<li class='nav-item'>
                            <a class='nav-link' href='login.php'>Iniciar Sesión</a>
                        </li>
                        ";
                    }
                    else
                    {
                        echo "
                        <li class='nav-item dropdown'>
                        <a class='nav-link dropdown-toggle' id='navbarDropdown' role='button' data-bs-toggle='dropdown' aria-expanded='false'>
                        <i class='fas fa-user' style='color: var(--secondary-color);'></i>
                            $user
                        </a>
                        <ul class='dropdown-menu' aria-labelledby='navbarDropdown'>
                        ";
                        if ($admin)
                        {
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
            <h1 class="display-4 fw-bold">Prácticas Seguras</h1>
            <p class="lead"><u>Conduce mejor, conduce seguro</u></p>
        </div>
    </header>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title">Conducción responsable</h2>
            </div>
            <p class="text-center mx-auto" style="max-width: 850px; color: var(--soft-text);">
                Adoptar prácticas seguras al conducir reduce el riesgo de accidentes y mejora
                la convivencia vial. Estas recomendaciones te ayudarán a mantener el control,
                anticiparte a peligros y proteger tu vida en cada trayecto.
            </p>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">

                <div class="col-md-6 col-lg-3">
                    <div class="card card-custom h-100 text-center p-3">
                        <i class="fas fa-eye fa-3x mb-3"></i>
                        <h5>Atención constante</h5>
                        <p>
                            Mantén la vista al frente y evita distracciones como el uso del
                            celular o audífonos al conducir.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card card-custom h-100 text-center p-3">
                        <i class="fas fa-arrows-alt-h fa-3x mb-3"></i>
                        <h5>Distancia segura</h5>
                        <p>
                            Conserva una distancia adecuada con otros vehículos para reaccionar
                            ante frenadas inesperadas.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card card-custom h-100 text-center p-3">
                        <i class="fas fa-hand-paper fa-3x mb-3"></i>
                        <h5>Señaliza tus movimientos</h5>
                        <p>
                            Usa direccionales y señales manuales para indicar giros y cambios
                            de carril.
                        </p>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card card-custom h-100 text-center p-3">
                        <i class="fas fa-helmet-safety fa-3x mb-3"></i>
                        <h5>Equipo de protección</h5>
                        <p>
                            Utiliza casco certificado, guantes y ropa adecuada para minimizar
                            lesiones en caso de caída.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="text-center mb-4">
                <h2 class="section-title">Recomendaciones clave</h2>
            </div>
            <ul class="mx-auto" style="max-width: 800px; color: var(--soft-text);">
                <li>Respeta los límites de velocidad y las señales de tránsito.</li>
                <li>Conduce a la defensiva, anticipando errores de otros conductores.</li>
                <li>Evita conducir cansado o bajo los efectos del alcohol.</li>
                <li>Revisa periódicamente el estado de tu motocicleta.</li>
                <li>Adapta tu conducción a las condiciones del clima.</li>
            </ul>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container text-center">
            <h2 class="section-title"><strong>La seguridad empieza contigo</strong></h2>
            <p class="mx-auto" style="max-width: 800px; color: var(--soft-text);">
                Aplicar prácticas seguras no solo mejora tu experiencia al conducir,
                también protege tu vida y la de quienes te rodean.
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