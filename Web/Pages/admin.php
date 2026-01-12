<?php
session_start();
require_once __DIR__ . '/../Includes/config.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && $conn) {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'create_casco' || $_POST['action'] == 'update_casco') {
            $helper = new sqlHelper('Cascos', $conn);
            $data = [
                'marca' => $_POST['marca'],
                'modelo' => $_POST['modelo'],
                'tipo' => $_POST['tipo'],
                'certificacion' => $_POST['certificacion'],
                'descripcion' => $_POST['descripcion'],
                'precio_aprox' => $_POST['precio']
            ];

            $selected_image = $_POST['imagen_path'] ?? '';
            if (!empty($selected_image)) {
                $data['imagen'] = $selected_image;
            } else {
                if ($_POST['action'] == 'create_casco' && empty($data['imagen']) && empty($selected_image)) {
                }
            }

            if ($_POST['action'] == 'create_casco') {
                try {
                    $helper->insert_into($data);
                    $message = "Casco agregado correctamente.";
                } catch (Exception $e) {
                    $message = "Error al agregar casco: " . $e->getMessage();
                }
            } else {
                $id = $_POST['id'];
                $helper->update($data, ['id' => $id]);
                $message = "Casco actualizado correctamente.";
            }
        } elseif ($_POST['action'] == 'delete_casco') {
            $helper = new sqlHelper('Cascos', $conn);
            $helper->delete(['id' => $_POST['id']]);
            $message = "Casco eliminado.";
        } elseif ($_POST['action'] == 'create_accidente' || $_POST['action'] == 'update_accidente') {
            $helper = new sqlHelper('Accidentes', $conn);
            $data = [
                'fecha' => $_POST['fecha'],
                'lugar' => $_POST['lugar'],
                'descripcion' => $_POST['descripcion'],
                'causa' => $_POST['causa'],
                'lesionados' => $_POST['lesionados'],
                'uso_casco' => isset($_POST['uso_casco']) ? 1 : 0,
                'nivel_gravedad' => $_POST['gravedad']
            ];
            $selected_image = $_POST['imagen_path'] ?? '';
            if (!empty($selected_image)) {
                $data['imagen_evidencia'] = $selected_image;
            }

            try {
                if ($_POST['action'] == 'create_accidente') {
                    $helper->insert_into($data);
                    $message = "Accidente registrado.";
                } else {
                    $id = $_POST['id'];
                    $helper->update($data, ['id' => $id]);
                    $message = "Accidente actualizado.";
                }
            } catch (Exception $e) {
                $message = "Error: " . $e->getMessage();
            }
        } elseif ($_POST['action'] == 'delete_accidente') {
            $helper = new sqlHelper('Accidentes', $conn);
            $helper->delete(['id' => $_POST['id']]);
            $message = "Registro eliminado.";
        } elseif ($_POST['action'] == 'create_faq' || $_POST['action'] == 'update_faq') {
            $helper = new sqlHelper('FAQ', $conn);
            $data = [
                'pregunta' => $_POST['pregunta'],
                'respuesta' => $_POST['respuesta'],
                'orden' => $_POST['orden'] ?? 0
            ];

            if ($_POST['action'] == 'create_faq') {
                $helper->insert_into($data);
                $message = "Pregunta agregada.";
            } else {
                $id = $_POST['id'];
                $helper->update($data, ['id' => $id]);
                $message = "Pregunta actualizada.";
            }
        } elseif ($_POST['action'] == 'delete_faq') {
            $helper = new sqlHelper('FAQ', $conn);
            $helper->delete(['id' => $_POST['id']]);
            $message = "Pregunta eliminada.";
        } elseif ($_POST['action'] == 'delete_user') {
            $helper = new sqlHelper('Usuarios', $conn);
            $helper->delete(['id' => $_POST['id']]);
            $message = "Usuario eliminado.";
        } elseif ($_POST['action'] == 'update_user') {
            $helper = new sqlHelper('Usuarios', $conn);
            $data = [
                'nombre_usuario' => $_POST['nombre_usuario'],
                'nombres' => $_POST['nombres'],
                'apellidos' => $_POST['apellidos'],
                'email' => $_POST['email'],
                'numero_telefono' => $_POST['numero_telefono']
            ];

            if (!empty($_POST['contrasena'])) {
                $data['contrasena'] = $_POST['contrasena'];
            }

            $id = $_POST['id'];
            $helper->update($data, ['id' => $id]);
            $message = "Usuario actualizado.";
        }
    }
}

$cascos = [];
$accidentes = [];
$faqs = [];
$usuarios = [];

if ($conn) {
    $cascosHelper = new sqlHelper('Cascos', $conn);
    $cascos = $cascosHelper->select();
    if (!$cascos)
        $cascos = [];

    $accHelper = new sqlHelper('Accidentes', $conn);
    $accidentes = $accHelper->select([], [], ['fecha' => 'DESC']);
    if (!$accidentes)
        $accidentes = [];

    $faqHelper = new sqlHelper('FAQ', $conn);
    $faqs = $faqHelper->select([], [], ['orden' => 'ASC']);
    if (!$faqs)
        $faqs = [];

    $userHelper = new sqlHelper('Usuarios', $conn);
    $usuarios = $userHelper->select();
    if (!$usuarios)
        $usuarios = [];

    $available_images = [];
    $imgDir = __DIR__ . '/../img/';
    if (is_dir($imgDir)) {
        $files = scandir($imgDir);
        foreach ($files as $f) {
            if (in_array($f, ['.', '..']))
                continue;
            if (preg_match('/\.(jpg|jpeg|png|webp)$/i', $f)) {
                $available_images[] = 'img/' . $f;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - Chak</title>
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="inicio.php">
                <i class="fas fa-helmet-safety me-2"></i>Chak Admin
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="inicio.php">Ir al Sitio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="admin.php">Panel</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h2 class="mb-4">Panel de Administración</h2>

        <?php if ($message): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="cascos-tab" data-bs-toggle="tab" data-bs-target="#cascos"
                    type="button" role="tab">Cascos</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="accidentes-tab" data-bs-toggle="tab" data-bs-target="#accidentes"
                    type="button" role="tab">Accidentes</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="faq-tab" data-bs-toggle="tab" data-bs-target="#faq" type="button"
                    role="tab">FAQ</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="usuarios-tab" data-bs-toggle="tab" data-bs-target="#usuarios" type="button"
                    role="tab">Usuarios</button>
            </li>
        </ul>

        <div class="tab-content" id="adminTabsContent">

            <div class="tab-pane fade show active" id="cascos" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Gestión de Cascos</h4>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalCasco"><i
                            class="fas fa-plus me-2"></i>Nuevo Casco</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Tipo</th>
                                <th>Precio</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cascos as $c): ?>
                                <tr>
                                    <td><?php echo $c['id']; ?></td>
                                    <td><?php echo htmlspecialchars($c['marca']); ?></td>
                                    <td><?php echo htmlspecialchars($c['modelo']); ?></td>
                                    <td><?php echo htmlspecialchars($c['tipo']); ?></td>
                                    <td>$<?php echo number_format($c['precio_aprox'], 2); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning me-1 edit-casco"
                                            data-json='<?php echo htmlspecialchars(json_encode($c), ENT_QUOTES, "UTF-8"); ?>'
                                            data-bs-toggle="modal" data-bs-target="#modalCasco">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?');">
                                            <input type="hidden" name="action" value="delete_casco">
                                            <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="accidentes" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Registro de Accidentes</h4>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalAccidente"><i
                            class="fas fa-plus me-2"></i>Nuevo Registro</button>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Lugar</th>
                            <th>Lesionados</th>
                            <th>Gravedad</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($accidentes as $a): ?>
                            <tr>
                                <td><?php echo $a['fecha']; ?></td>
                                <td><?php echo htmlspecialchars($a['lugar']); ?></td>
                                <td><?php echo $a['lesionados']; ?></td>
                                <td>
                                    <span
                                        class="badge bg-warning text-dark"><?php echo htmlspecialchars($a['nivel_gravedad']); ?></span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning me-1 edit-accidente"
                                        data-json='<?php echo htmlspecialchars(json_encode($a), ENT_QUOTES, "UTF-8"); ?>'
                                        data-bs-toggle="modal" data-bs-target="#modalAccidente">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?');">
                                        <input type="hidden" name="action" value="delete_accidente">
                                        <input type="hidden" name="id" value="<?php echo $a['id']; ?>">
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="faq" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Preguntas Frecuentes</h4>
                    <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#modalFAQ"><i
                            class="fas fa-plus me-2"></i>Nueva FAQ</button>
                </div>
                <div class="accordion" id="accordionAdminFAQ">
                    <?php foreach ($faqs as $f): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseA<?php echo $f['id']; ?>">
                                    <?php echo htmlspecialchars($f['pregunta']); ?>
                                </button>
                            </h2>
                            <div id="collapseA<?php echo $f['id']; ?>" class="accordion-collapse collapse"
                                data-bs-parent="#accordionAdminFAQ">
                                <div class="accordion-body d-flex justify-content-between">
                                    <div><?php echo htmlspecialchars($f['respuesta']); ?></div>
                                    <div>
                                        <button class="btn btn-sm btn-warning me-1 edit-faq"
                                            data-json='<?php echo htmlspecialchars(json_encode($f), ENT_QUOTES, "UTF-8"); ?>'
                                            data-bs-toggle="modal" data-bs-target="#modalFAQ">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar?');">
                                            <input type="hidden" name="action" value="delete_faq">
                                            <input type="hidden" name="id" value="<?php echo $f['id']; ?>">
                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="tab-pane fade" id="usuarios" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Gestión de Usuarios</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Nombre Completo</th>
                                <th>Email</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($usuarios as $u): ?>
                                <tr>
                                    <td><?php echo $u['id']; ?></td>
                                    <td><?php echo htmlspecialchars($u['nombre_usuario']); ?></td>
                                    <td><?php echo htmlspecialchars($u['nombres'] . ' ' . $u['apellidos']); ?></td>
                                    <td><?php echo htmlspecialchars($u['email']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning me-1 edit-user"
                                            data-json='<?php echo htmlspecialchars(json_encode($u), ENT_QUOTES, "UTF-8"); ?>'
                                            data-bs-toggle="modal" data-bs-target="#modalUser">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form method="POST" class="d-inline"
                                            onsubmit="return confirm('¿Eliminar usuario?');">
                                            <input type="hidden" name="action" value="delete_user">
                                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                            <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>


    <div class="modal fade" id="modalCasco" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="create_casco">
                    <div class="modal-header">
                        <h5 class="modal-title">Agregar Casco</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Marca</label>
                                <input type="text" name="marca" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Modelo</label>
                                <input type="text" name="modelo" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="tipo" class="form-select">
                                <option value="Integral">Integral</option>
                                <option value="Abatible">Abatible</option>
                                <option value="Cross">Cross</option>
                                <option value="Jet">Jet</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Precio</label>
                                <input type="number" step="0.01" name="precio" class="form-control" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Certificación</label>
                                <select name="certificacion" class="form-select">
                                    <option>DOT</option>
                                    <option>ECE</option>
                                    <option>SNELL</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Imagen</label>
                                <select name="imagen_path" class="form-select">
                                    <option value="">Seleccionar Imagen...</option>
                                    <?php foreach ($available_images as $img): ?>
                                        <option value="<?php echo $img; ?>"><?php echo basename($img); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalAccidente" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="create_accidente">
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Accidente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Fecha</label>
                                <input type="date" name="fecha" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Lugar</label>
                                <input type="text" name="lugar" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Causa</label>
                            <input type="text" name="causa" class="form-control">
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Lesionados</label>
                                <input type="number" name="lesionados" class="form-control" value="0">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Gravedad</label>
                                <select name="gravedad" class="form-select">
                                    <option>Baja</option>
                                    <option>Media</option>
                                    <option>Alta</option>
                                    <option>Fatal</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" name="uso_casco" id="usoCasco">
                                    <label class="form-check-label" for="usoCasco">¿Usaba Casco?</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Imagen Evidencia</label>
                            <select name="imagen_path" class="form-select">
                                <option value="">Seleccionar Imagen...</option>
                                <?php foreach ($available_images as $img): ?>
                                    <option value="<?php echo $img; ?>"><?php echo basename($img); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary-custom">Guardar</button>
                        </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="modalFAQ" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="create_faq">
                    <div class="modal-header">
                        <h5 class="modal-title">Agregar FAQ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Pregunta</label>
                            <input type="text" name="pregunta" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Respuesta</label>
                            <textarea name="respuesta" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Orden</label>
                            <input type="number" name="orden" class="form-control" value="1">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <footer class="text-center mt-auto">
        <div class="container">
            <p>&copy; 2025 Chak - Panel Administrativo</p>
        </div>
    </footer>

    <div class="modal fade" id="modalUser" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <input type="hidden" name="action" value="update_user">
                    <input type="hidden" name="id" id="userId">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Usuario</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Usuario</label>
                            <input type="text" name="nombre_usuario" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nombre(s)</label>
                            <input type="text" name="nombres" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Apellidos</label>
                            <input type="text" name="apellidos" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="numero_telefono" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nueva Contraseña (Dejar en blanco para mantener actual)</label>
                            <input type="password" name="contrasena" class="form-control" placeholder="••••••••">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary-custom">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            function populateForm(form, data, prefix = '') {
                for (const key in data) {
                    const input = form.querySelector(`[name="${key}"]`);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = data[key] == 1 || data[key] == '1';
                        } else {
                            input.value = data[key];
                        }
                    }
                    else if (key === 'nivel_gravedad') {
                        const select = form.querySelector('select[name="gravedad"]');
                        if (select) select.value = data[key];
                    }
                    else if (key === 'precio_aprox') {
                        const input = form.querySelector('input[name="precio"]');
                        if (input) input.value = data[key];
                    }
                }
            }

            const modalCasco = document.getElementById('modalCasco');
            const formCasco = modalCasco.querySelector('form');
            modalCasco.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const json = button.getAttribute('data-json');

                if (!formCasco.querySelector('input[name="id"]')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'id';
                    formCasco.prepend(input);
                }

                if (json) {
                    const data = JSON.parse(json);

                    formCasco.querySelector('input[name="action"]').value = 'update_casco';
                    modalCasco.querySelector('.modal-title').textContent = 'Editar Casco';
                    formCasco.querySelector('input[name="id"]').value = data.id;

                    populateForm(formCasco, data);
                    formCasco.querySelector('input[name="precio"]').value = data.precio_aprox;
                    const imgSelect = formCasco.querySelector('select[name="imagen_path"]');
                    if (imgSelect) imgSelect.value = data.imagen || "";

                } else {
                    formCasco.reset();
                    formCasco.querySelector('input[name="action"]').value = 'create_casco';
                    modalCasco.querySelector('.modal-title').textContent = 'Agregar Casco';
                }
            });

            const modalAccidente = document.getElementById('modalAccidente');
            const formAccidente = modalAccidente.querySelector('form');
            modalAccidente.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const json = button.getAttribute('data-json');

                if (!formAccidente.querySelector('input[name="id"]')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'id';
                    formAccidente.prepend(input);
                }

                if (json) {
                    const data = JSON.parse(json);
                    formAccidente.querySelector('input[name="action"]').value = 'update_accidente';
                    modalAccidente.querySelector('.modal-title').textContent = 'Editar Accidente';
                    formAccidente.querySelector('input[name="id"]').value = data.id;

                    populateForm(formAccidente, data);
                    const gravSelect = formAccidente.querySelector('select[name="gravedad"]');
                    if (gravSelect) gravSelect.value = data.nivel_gravedad;

                    const usoCheck = formAccidente.querySelector('input[name="uso_casco"]');
                    if (usoCheck) usoCheck.checked = (data.uso_casco == 1);

                    const imgSelect = formAccidente.querySelector('select[name="imagen_path"]');
                    if (imgSelect) imgSelect.value = data.imagen_evidencia || "";

                } else {
                    formAccidente.reset();
                    formAccidente.querySelector('input[name="action"]').value = 'create_accidente';
                    modalAccidente.querySelector('.modal-title').textContent = 'Registrar Accidente';
                }
            });

            const modalFAQ = document.getElementById('modalFAQ');
            const formFAQ = modalFAQ.querySelector('form');
            modalFAQ.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const json = button.getAttribute('data-json');

                if (!formFAQ.querySelector('input[name="id"]')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'id';
                    formFAQ.prepend(input);
                }

                if (json) {
                    const data = JSON.parse(json);
                    formFAQ.querySelector('input[name="action"]').value = 'update_faq';
                    modalFAQ.querySelector('.modal-title').textContent = 'Editar FAQ';
                    formFAQ.querySelector('input[name="id"]').value = data.id;
                    populateForm(formFAQ, data);
                } else {
                    formFAQ.reset();
                    formFAQ.querySelector('input[name="action"]').value = 'create_faq';
                    modalFAQ.querySelector('.modal-title').textContent = 'Agregar FAQ';
                }
            });

            const modalUser = document.getElementById('modalUser');
            modalUser.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const json = button.getAttribute('data-json');
                const formUser = modalUser.querySelector('form');

                if (json) {
                    const data = JSON.parse(json);
                    formUser.querySelector('input[name="id"]').value = data.id;
                    populateForm(formUser, data);
                }
            });
        });
    </script>
</body>

</html>