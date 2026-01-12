<?php
session_start();
require_once __DIR__ . '/../Includes/config.php';

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($conn) {
        $username = $_POST['username'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        if (empty($username) || empty($new_password) || empty($confirm_password)) {
            $error = "Todos los campos son obligatorios.";
        } elseif ($new_password !== $confirm_password) {
            $error = "Las contraseñas no coinciden.";
        } else {
            $helper = new sqlHelper('Usuarios', $conn);
            $user = $helper->selectOne([], ['nombre_usuario' => $username]);

            if ($user) {
                $updated = $helper->update(
                    ['contrasena' => $new_password],
                    ['id' => $user['id']]
                );

                if ($updated !== false) {
                    $_SESSION['flash_message'] = "Contraseña actualizada exitosamente. Inicie sesión.";
                    header("Location: login.php");
                    exit();
                } else {
                    $error = "Error al actualizar la contraseña.";
                }
            } else {
                $error = "Usuario no encontrado.";
            }
        }
    } else {
        $error = "Error de conexión a la base de datos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña - Rinos al volante</title>
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
        </div>
    </nav>

    <section class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card p-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-key fa-3x mb-3" style="color: var(--primary-color);"></i>
                            <h2 class="section-title">Restablecer Contraseña</h2>
                            <p style="color: var(--soft-text);">Ingrese su usuario y nueva contraseña</p>
                        </div>

                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Nombre de Usuario</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nueva Contraseña</label>
                                <input type="password" name="new_password" class="form-control" placeholder="••••••••"
                                    required>
                            </div>

                            <div class="mb-4">
                                <label class="form-label">Confirmar Contraseña</label>
                                <input type="password" name="confirm_password" class="form-control"
                                    placeholder="••••••••" required>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-secondary-custom btn-lg">Actualizar
                                    Contraseña</button>
                                <a href="login.php" class="btn btn-outline-secondary">Cancelar</a>
                            </div>

                            <?php if ($error): ?>
                                <div class="alert alert-danger mt-3" role="alert">
                                    <?php echo $error; ?>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</body>

</html>