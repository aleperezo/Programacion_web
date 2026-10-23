<?php
require 'conexion.php';

// ==========================================
// LÓGICA PARA EXPORTAR A EXCEL
// ==========================================
if (isset($_GET['exportar']) && $_GET['exportar'] === 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=Reporte_Ventas_GYMICO.xls");
    
    echo "<table border='1'>";
    echo "<tr style='background-color: #dc2626; color: white;'>
            <th>ID Venta</th><th>Concepto / Servicio</th><th>Monto Pagado</th><th>Fecha y Hora</th>
          </tr>";
    
    $ventas_excel = $conn->query("SELECT * FROM ventas ORDER BY fecha_venta DESC");
    if($ventas_excel) {
        while ($row = $ventas_excel->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['id_venta'] . "</td>";
            echo "<td>" . utf8_decode($row['concepto']) . "</td>";
            echo "<td>$" . number_format($row['monto'], 2) . "</td>";
            echo "<td>" . $row['fecha_venta'] . "</td>";
            echo "</tr>";
        }
    }
    echo "</table>";
    exit();
}

// ==========================================
// 1. REGISTRAR / ELIMINAR MANTENIMIENTO
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_mantenimiento'])) {
    $nombre_equipo = $conn->real_escape_string(trim($_POST['nombre_equipo']));
    $tipo = $conn->real_escape_string(trim($_POST['tipo']));
    $motivo = $conn->real_escape_string(trim($_POST['motivo']));

    if (!empty($nombre_equipo) && !empty($motivo)) {
        $sql_insert = "INSERT INTO mantenimiento (nombre_equipo, tipo, motivo) VALUES ('$nombre_equipo', '$tipo', '$motivo')";
        $conn->query($sql_insert);
        header("Location: admin.php?exito=mantenimiento");
        exit();
    }
}
if (isset($_GET['resolver'])) {
    $id_mant = intval($_GET['resolver']);
    $conn->query("DELETE FROM mantenimiento WHERE id = $id_mant");
    header("Location: admin.php?resuelto=1");
    exit();
}

// ==========================================
// 2. REGISTRAR NUEVA VENTA
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_venta'])) {
    $concepto = $conn->real_escape_string(trim($_POST['concepto']));
    $monto = floatval($_POST['monto']);

    if (!empty($concepto) && $monto > 0) {
        $sql_venta = "INSERT INTO ventas (concepto, monto) VALUES ('$concepto', '$monto')";
        $conn->query($sql_venta);
        header("Location: admin.php?exito=venta");
        exit();
    }
}

// ==========================================
// 3. GUARDAR (NUEVO/EDITAR) Y ELIMINAR EMPLEADO
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_empleado'])) {
    $id_emp = isset($_POST['id_empleado']) ? intval($_POST['id_empleado']) : 0;
    $nombre_emp = $conn->real_escape_string(trim($_POST['nombre_empleado']));
    $rol_emp = $conn->real_escape_string(trim($_POST['rol_empleado']));

    if (!empty($nombre_emp) && !empty($rol_emp)) {
        if ($id_emp > 0) {
            // Actualizar empleado existente
            $conn->query("UPDATE empleados SET nombre='$nombre_emp', rol='$rol_emp' WHERE id_empleado=$id_emp");
        } else {
            // Crear nuevo empleado
            $conn->query("INSERT INTO empleados (nombre, rol) VALUES ('$nombre_emp', '$rol_emp')");
        }
        header("Location: admin.php?exito=empleado");
        exit();
    }
}
if (isset($_GET['eliminar_empleado'])) {
    $id_emp = intval($_GET['eliminar_empleado']);
    $conn->query("DELETE FROM empleados WHERE id_empleado = $id_emp");
    header("Location: admin.php?eliminado=empleado");
    exit();
}

// ==========================================
// 4. REGISTRAR Y RESOLVER REPORTES
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_reporte'])) {
    $tipo_rep = $conn->real_escape_string(trim($_POST['tipo_reporte']));
    $desc_rep = $conn->real_escape_string(trim($_POST['descripcion_reporte']));
    $autor_rep = $conn->real_escape_string(trim($_POST['reportado_por']));

    if (!empty($desc_rep) && !empty($autor_rep)) {
        $conn->query("INSERT INTO reportes (tipo_reporte, descripcion, reportado_por) VALUES ('$tipo_rep', '$desc_rep', '$autor_rep')");
        header("Location: admin.php?exito=reporte");
        exit();
    }
}
if (isset($_GET['resolver_reporte'])) {
    $id_rep = intval($_GET['resolver_reporte']);
    $conn->query("DELETE FROM reportes WHERE id_reporte = $id_rep");
    header("Location: admin.php?resuelto=reporte");
    exit();
}

// Consultas a la base de datos para mostrar la información en las tablas
$resultado_mantenimiento = $conn->query("SELECT * FROM mantenimiento ORDER BY id DESC");
$top_ventas = $conn->query("SELECT concepto, COUNT(*) as cantidad, SUM(monto) as total_recaudado FROM ventas GROUP BY concepto ORDER BY cantidad DESC LIMIT 5");
$resultado_empleados = $conn->query("SELECT * FROM empleados ORDER BY id_empleado DESC");
$resultado_reportes = $conn->query("SELECT * FROM reportes ORDER BY fecha_reporte DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | GYMICO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Oswald:wght@500;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: '#dc2626', dark: { 900: '#111827', 800: '#1f2937', 700: '#374151' } }, fontFamily: { sans: ['Inter'], heading: ['Oswald'] } } }
        }
    </script>
</head>
<body class="bg-dark-900 text-gray-200 font-sans antialiased p-8">

    <div class="max-w-7xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="font-heading text-4xl font-bold text-white uppercase"><span class="text-brand">Admin</span> GYMICO</h1>
            <div class="flex gap-4">
                <a href="escaner.html" class="bg-dark-800 border border-dark-700 hover:bg-dark-700 text-white px-4 py-2 rounded-lg font-bold transition flex items-center gap-2">
                    <i class="fas fa-id-card text-brand"></i> Ir a Recepción
                </a>
            </div>
        </div>

        <!-- SISTEMA DE PESTAÑAS -->
        <div class="flex gap-6 mb-8 border-b border-dark-700 pb-2 overflow-x-auto">
            <button onclick="mostrarTab('socios')" id="btn-socios" class="text-brand font-bold uppercase pb-2 border-b-2 border-brand transition whitespace-nowrap">Gestión de Socios</button>
            <button onclick="mostrarTab('mantenimiento')" id="btn-mantenimiento" class="text-gray-400 font-bold uppercase pb-2 border-b-2 border-transparent hover:text-white transition whitespace-nowrap">Maquinaria</button>
            <button onclick="mostrarTab('ventas')" id="btn-ventas" class="text-gray-400 font-bold uppercase pb-2 border-b-2 border-transparent hover:text-white transition whitespace-nowrap">Ventas / Excel</button>
            <button onclick="mostrarTab('empleados')" id="btn-empleados" class="text-gray-400 font-bold uppercase pb-2 border-b-2 border-transparent hover:text-white transition whitespace-nowrap">Staff / Empleados</button>
            <button onclick="mostrarTab('reportes')" id="btn-reportes" class="text-gray-400 font-bold uppercase pb-2 border-b-2 border-transparent hover:text-white transition whitespace-nowrap"><i class="fas fa-exclamation-circle text-yellow-500 mr-1"></i> Reportes de Fallas</button>
        </div>

        <!-- ============================================== -->
        <!-- TAB 1: SOCIOS                                  -->
        <!-- ============================================== -->
        <div id="tab-socios" class="block">
            <div class="flex justify-end mb-4">
                <button onclick="abrirModal()" class="bg-brand hover:bg-red-700 text-white px-6 py-2 rounded-lg font-bold transition">
                    <i class="fas fa-plus mr-2"></i> Nuevo Socio
                </button>
            </div>
            <div class="bg-dark-800 rounded-xl border border-dark-700 overflow-hidden shadow-2xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-dark-900 text-gray-400 uppercase text-sm border-b border-dark-700">
                            <th class="p-4">Foto</th>
                            <th class="p-4">ID</th>
                            <th class="p-4">Nombre / Correo</th>
                            <th class="p-4">Edad / Peso</th>
                            <th class="p-4">Membresía</th>
                            <th class="p-4">Vencimiento</th>
                            <th class="p-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-usuarios" class="divide-y divide-dark-700">
                        <!-- Dinámico por JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 2: MANTENIMIENTO                           -->
        <!-- ============================================== -->
        <div id="tab-mantenimiento" class="hidden">
            <div class="bg-dark-800 rounded-2xl p-8 border border-dark-700 shadow-xl">
                <div class="flex items-center gap-3 mb-6">
                    <i class="fas fa-tools text-3xl text-yellow-500"></i>
                    <h2 class="font-heading text-2xl font-bold text-white uppercase">Gestión de Equipos en <span class="text-yellow-500">Mantenimiento</span></h2>
                </div>
                <form action="admin.php" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 bg-dark-900 p-6 rounded-xl border border-dark-700">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Nombre del Equipo / Sala</label>
                        <input type="text" name="nombre_equipo" required class="w-full bg-dark-800 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-brand text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Tipo</label>
                        <select name="tipo" class="w-full bg-dark-800 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-brand text-sm">
                            <option value="Aparato">Aparato</option>
                            <option value="Sala">Sala</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Motivo de la falla</label>
                        <input type="text" name="motivo" required class="w-full bg-dark-800 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-brand text-sm">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" name="registrar_mantenimiento" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2.5 rounded-lg uppercase tracking-wide transition text-sm shadow-lg cursor-pointer">
                            <i class="fas fa-plus mr-1"></i> Registrar Falla
                        </button>
                    </div>
                </form>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-dark-900 text-gray-400 text-xs uppercase border-b border-dark-700">
                                <th class="p-3">ID</th><th class="p-3">Equipo / Sala</th><th class="p-3">Tipo</th><th class="p-3">Motivo</th><th class="p-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-dark-700 text-sm">
                            <?php if ($resultado_mantenimiento && $resultado_mantenimiento->num_rows > 0): ?>
                                <?php while($row = $resultado_mantenimiento->fetch_assoc()): ?>
                                    <tr class="hover:bg-dark-900/50 transition">
                                        <td class="p-3 font-mono text-gray-400">#<?php echo $row['id']; ?></td>
                                        <td class="p-3 font-bold text-white"><?php echo htmlspecialchars($row['nombre_equipo']); ?></td>
                                        <td class="p-3"><span class="bg-dark-900 border border-dark-700 text-yellow-500 px-2.5 py-1 rounded text-xs font-semibold"><?php echo htmlspecialchars($row['tipo']); ?></span></td>
                                        <td class="p-3 text-gray-300"><?php echo htmlspecialchars($row['motivo']); ?></td>
                                        <td class="p-3 text-center">
                                            <a href="admin.php?resolver=<?php echo $row['id']; ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-bold uppercase transition shadow">
                                                <i class="fas fa-check"></i> Marcar Reparado
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 3: VENTAS Y REPORTES                       -->
        <!-- ============================================== -->
        <div id="tab-ventas" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-dark-800 rounded-2xl p-6 border border-dark-700 shadow-xl">
                    <h3 class="font-heading text-xl font-bold text-white uppercase mb-4"><i class="fas fa-cash-register text-brand mr-2"></i>Registrar Venta</h3>
                    <form action="admin.php" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Concepto / Servicio</label>
                            <select name="concepto" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-brand text-sm">
                                <option value="Membresia Basica">Membresía Básica</option>
                                <option value="Membresia Premium">Membresía Premium</option>
                                <option value="Membresia VIP">Membresía VIP</option>
                                <option value="Inscripcion">Inscripción / Credencial</option>
                                <option value="Renta de Sala">Renta de Sala Exclusiva</option>
                                <option value="Suplemento / Proteina">Suplemento / Proteína</option>
                                <option value="Bebida Energetica">Agua / Bebida Energética</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Monto Cobrado ($)</label>
                            <input type="number" step="0.01" name="monto" required class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-brand text-sm">
                        </div>
                        <button type="submit" name="registrar_venta" class="w-full bg-brand hover:bg-red-700 text-white font-bold py-2.5 rounded-lg uppercase transition text-sm cursor-pointer">
                            Guardar Venta
                        </button>
                    </form>
                </div>
                <div class="bg-dark-800 rounded-2xl p-6 border border-dark-700 shadow-xl md:col-span-2">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-heading text-xl font-bold text-white uppercase"><i class="fas fa-chart-bar text-emerald-500 mr-2"></i>Servicios Más Vendidos</h3>
                        <a href="admin.php?exportar=excel" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-bold uppercase transition text-xs shadow-lg cursor-pointer">
                            <i class="fas fa-file-excel mr-1"></i> Descargar Excel
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-dark-900 text-gray-400 text-xs uppercase border-b border-dark-700">
                                    <th class="p-3">Concepto</th><th class="p-3 text-center">Contrataciones</th><th class="p-3 text-right">Total Generado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-700 text-sm">
                                <?php if ($top_ventas && $top_ventas->num_rows > 0): ?>
                                    <?php while($top = $top_ventas->fetch_assoc()): ?>
                                        <tr class="hover:bg-dark-900/50 transition">
                                            <td class="p-3 font-bold text-white"><?php echo htmlspecialchars($top['concepto']); ?></td>
                                            <td class="p-3 text-center"><span class="bg-brand/20 text-brand px-2 rounded-full font-bold"><?php echo $top['cantidad']; ?></span></td>
                                            <td class="p-3 text-right text-emerald-400 font-bold">$<?php echo number_format($top['total_recaudado'], 2); ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 4: EMPLEADOS (CREAR Y EDITAR)              -->
        <!-- ============================================== -->
        <div id="tab-empleados" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Formulario Empleados -->
                <div class="bg-dark-800 rounded-2xl p-6 border border-dark-700 shadow-xl">
                    <h3 class="font-heading text-xl font-bold text-white uppercase mb-4"><i class="fas fa-user-tie text-blue-500 mr-2"></i>Guardar Empleado</h3>
                    <form action="admin.php" method="POST" class="space-y-4">
                        <!-- ID oculto para saber si editamos o creamos -->
                        <input type="hidden" name="id_empleado" id="form-emp-id" value="0">
                        
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Nombre del Empleado</label>
                            <input type="text" name="nombre_empleado" id="form-emp-nombre" required placeholder="Nombre Completo" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-brand text-sm">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Rol / Puesto</label>
                            <select name="rol_empleado" id="form-emp-rol" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-brand text-sm">
                                <option value="Entrenador">Entrenador(a)</option>
                                <option value="Recepcionista">Recepcionista</option>
                                <option value="Nutriologo">Nutriólogo(a)</option>
                                <option value="Mantenimiento">Mantenimiento</option>
                                <option value="Gerente">Gerente</option>
                            </select>
                        </div>
                        <button type="submit" name="guardar_empleado" id="btn-guardar-emp" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 rounded-lg uppercase transition text-sm cursor-pointer mt-4">
                            Registrar Nuevo Empleado
                        </button>
                        <!-- Botón para cancelar la edición y limpiar el formulario -->
                        <button type="button" onclick="limpiarFormEmpleado()" class="w-full bg-dark-700 hover:bg-dark-600 text-gray-300 font-bold py-2 rounded-lg uppercase transition text-xs mt-2">
                            Limpiar Formulario
                        </button>
                    </form>
                </div>

                <!-- Tabla Empleados -->
                <div class="bg-dark-800 rounded-2xl p-6 border border-dark-700 shadow-xl md:col-span-2">
                    <h3 class="font-heading text-xl font-bold text-white uppercase mb-4"><i class="fas fa-users-cog text-blue-500 mr-2"></i>Plantilla de Staff</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-dark-900 text-gray-400 text-xs uppercase border-b border-dark-700">
                                    <th class="p-3">ID Emp</th><th class="p-3">Nombre</th><th class="p-3">Rol</th><th class="p-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-700 text-sm">
                                <?php if ($resultado_empleados && $resultado_empleados->num_rows > 0): ?>
                                    <?php while($emp = $resultado_empleados->fetch_assoc()): ?>
                                        <tr class="hover:bg-dark-900/50 transition">
                                            <td class="p-3 font-mono text-blue-400 font-bold">EMP-<?php echo str_pad($emp['id_empleado'], 4, '0', STR_PAD_LEFT); ?></td>
                                            <td class="p-3 font-bold text-white"><?php echo htmlspecialchars($emp['nombre']); ?></td>
                                            <td class="p-3"><span class="bg-dark-900 border border-dark-700 text-gray-300 px-2.5 py-1 rounded text-xs font-semibold"><?php echo htmlspecialchars($emp['rol']); ?></span></td>
                                            <td class="p-3 text-center">
                                                <button onclick="editarEmpleado(<?php echo $emp['id_empleado']; ?>, '<?php echo addslashes($emp['nombre']); ?>', '<?php echo addslashes($emp['rol']); ?>')" class="text-blue-400 hover:text-blue-300 transition mx-2" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="admin.php?eliminar_empleado=<?php echo $emp['id_empleado']; ?>" onclick="return confirm('¿Dar de baja a este empleado?');" class="text-red-500 hover:text-red-400 transition mx-2" title="Dar de baja">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="p-4 text-center text-gray-500">No hay empleados registrados.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 5: REPORTES DE FALLAS (SISTEMA Y GYM)      -->
        <!-- ============================================== -->
        <div id="tab-reportes" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Crear Reporte -->
                <div class="bg-dark-800 rounded-2xl p-6 border border-dark-700 shadow-xl">
                    <h3 class="font-heading text-xl font-bold text-white uppercase mb-4"><i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>Levantar Reporte</h3>
                    <form action="admin.php" method="POST" class="space-y-4">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Tipo de Incidencia</label>
                            <select name="tipo_reporte" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-brand text-sm">
                                <option value="Problema en Pagina Web">Error en Página Web</option>
                                <option value="Daño en Maquinaria">Daño en Maquinaria del Gym</option>
                                <option value="Problema en Instalacion">Problema en Instalaciones</option>
                                <option value="Otro">Otro Asunto</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">¿Qué pasó? (Descripción)</label>
                            <textarea name="descripcion_reporte" required rows="3" placeholder="Describe brevemente el problema..." class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-brand text-sm resize-none"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Reportado por</label>
                            <input type="text" name="reportado_por" required placeholder="Nombre de quien reporta" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-brand text-sm">
                        </div>
                        <button type="submit" name="registrar_reporte" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2.5 rounded-lg uppercase transition text-sm cursor-pointer mt-4">
                            Enviar Reporte
                        </button>
                    </form>
                </div>

                <!-- Lista de Reportes -->
                <div class="bg-dark-800 rounded-2xl p-6 border border-dark-700 shadow-xl md:col-span-2">
                    <h3 class="font-heading text-xl font-bold text-white uppercase mb-4"><i class="fas fa-clipboard-list text-yellow-500 mr-2"></i>Buzón de Reportes Activos</h3>
                    <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2">
                        <?php if ($resultado_reportes && $resultado_reportes->num_rows > 0): ?>
                            <?php while($rep = $resultado_reportes->fetch_assoc()): ?>
                                <div class="bg-dark-900 border-l-4 border-yellow-500 rounded-lg p-4 shadow flex flex-col md:flex-row justify-between md:items-center gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2 mb-1">
                                            <span class="text-xs font-bold bg-dark-700 text-yellow-500 px-2 py-0.5 rounded uppercase tracking-wider"><?php echo htmlspecialchars($rep['tipo_reporte']); ?></span>
                                            <span class="text-xs text-gray-500"><i class="far fa-clock"></i> <?php echo date("d M Y H:i", strtotime($rep['fecha_reporte'])); ?></span>
                                        </div>
                                        <p class="text-gray-200 text-sm mt-2">"<?php echo htmlspecialchars($rep['descripcion']); ?>"</p>
                                        <p class="text-xs text-gray-400 mt-2"><strong>Reportado por:</strong> <?php echo htmlspecialchars($rep['reportado_por']); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <a href="admin.php?resolver_reporte=<?php echo $rep['id_reporte']; ?>" onclick="return confirm('¿Marcar este reporte como solucionado y eliminarlo del buzón?');" class="bg-emerald-600/20 text-emerald-500 border border-emerald-500/50 hover:bg-emerald-600 hover:text-white px-4 py-2 rounded text-xs font-bold uppercase transition inline-block">
                                            <i class="fas fa-check"></i> Marcar Resuelto
                                        </a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="text-center p-8 border border-dashed border-dark-700 rounded-xl text-gray-500">
                                <i class="fas fa-check-circle text-4xl mb-3 text-dark-700"></i>
                                <p>Buzón limpio. No hay reportes pendientes.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal Formulario Socios (TU ORIGINAL) -->
    <div id="modal-usuario" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 p-4">
        <div class="bg-dark-800 p-8 rounded-xl w-full max-w-lg border border-dark-700 max-h-[90vh] overflow-y-auto">
            <h2 id="modal-titulo" class="font-heading text-2xl font-bold text-white uppercase mb-6">Registrar Socio</h2>
            <form id="form-usuario" class="space-y-4">
                <input type="hidden" id="user-id">
                
                <div><label class="block text-sm text-gray-400 mb-1">Nombre Completo</label><input type="text" id="user-nombre" required class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2 rounded focus:border-brand outline-none"></div>
                <div><label class="block text-sm text-gray-400 mb-1">Correo Electrónico</label><input type="email" id="user-correo" required class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2 rounded focus:border-brand outline-none"></div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm text-gray-400 mb-1">Edad (años)</label><input type="number" id="user-edad" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2 rounded focus:border-brand outline-none"></div>
                    <div><label class="block text-sm text-gray-400 mb-1">Peso (kg)</label><input type="number" step="0.01" id="user-peso" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2 rounded focus:border-brand outline-none"></div>
                </div>

                <div id="campo-contrasena"><label class="block text-sm text-gray-400 mb-1">Contraseña temporal</label><input type="text" id="user-pass" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2 rounded focus:border-brand outline-none"></div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Membresía</label>
                        <select id="user-membresia" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2 rounded outline-none">
                            <option value="Basica">Básica</option><option value="Premium">Premium</option><option value="VIP">VIP</option>
                        </select>
                    </div>
                    <div><label class="block text-sm text-gray-400 mb-1">Vencimiento</label><input type="date" id="user-vencimiento" required class="w-full bg-dark-900 border border-dark-700 text-gray-300 px-4 py-2 rounded" style="color-scheme: dark;"></div>
                </div>

                <div><label class="block text-sm text-gray-400 mb-1">Subir Fotografía del Socio</label><input type="file" id="user-foto" accept="image/*" class="w-full bg-dark-900 border border-dark-700 text-gray-300 px-4 py-2 rounded focus:border-brand outline-none text-sm"></div>

                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="cerrarModal()" class="px-4 py-2 text-gray-400 hover:text-white">Cancelar</button>
                    <button type="submit" class="bg-brand hover:bg-red-700 text-white px-6 py-2 rounded font-bold">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT JS GENERAL -->
    <script>
        // Pestañas
        function mostrarTab(tabId) {
            const tabs = ['socios', 'mantenimiento', 'ventas', 'empleados', 'reportes'];
            tabs.forEach(t => {
                document.getElementById('tab-' + t).classList.replace('block', 'hidden');
                document.getElementById('btn-' + t).className = "text-gray-400 font-bold uppercase pb-2 border-b-2 border-transparent hover:text-white transition whitespace-nowrap";
            });
            document.getElementById('tab-' + tabId).classList.replace('hidden', 'block');
            document.getElementById('btn-' + tabId).className = "text-brand font-bold uppercase pb-2 border-b-2 border-brand transition whitespace-nowrap";
        }

        // --- Funciones para Empleados (Editar) ---
        function editarEmpleado(id, nombre, rol) {
            document.getElementById('form-emp-id').value = id;
            document.getElementById('form-emp-nombre').value = nombre;
            document.getElementById('form-emp-rol').value = rol;
            document.getElementById('btn-guardar-emp').innerText = "Actualizar Empleado";
            mostrarTab('empleados');
        }

        function limpiarFormEmpleado() {
            document.getElementById('form-emp-id').value = "0";
            document.getElementById('form-emp-nombre').value = "";
            document.getElementById('form-emp-rol').value = "Entrenador";
            document.getElementById('btn-guardar-emp').innerText = "Registrar Nuevo Empleado";
        }

        // --- API Usuarios Original ---
        document.addEventListener('DOMContentLoaded', cargarUsuarios);
        async function cargarUsuarios() {
            try {
                const respuesta = await fetch('api_usuarios.php');
                const usuarios = await respuesta.json();
                const tbody = document.getElementById('tabla-usuarios');
                tbody.innerHTML = '';
                usuarios.forEach(user => {
                    const fotoUrl = user.foto ? user.foto : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=500&auto=format&fit=crop';
                    const tr = document.createElement('tr');
                    tr.className = 'hover:bg-dark-900/50 transition';
                    tr.innerHTML = `
                        <td class="p-4"><img src="${fotoUrl}" class="w-10 h-10 rounded-full object-cover border border-brand"></td>
                        <td class="p-4 font-mono font-bold text-gray-400">#${user.id_usuario}</td>
                        <td class="p-4"><div class="font-bold text-white">${user.nombre_completo}</div><div class="text-xs text-gray-400">${user.correo}</div></td>
                        <td class="p-4 text-gray-300 text-sm">${user.edad ? user.edad + ' años' : 'N/D'}<br><span class="text-xs text-gray-500">${user.peso ? user.peso + ' kg' : ''}</span></td>
                        <td class="p-4"><span class="bg-brand/20 text-brand px-2 py-1 rounded text-xs font-bold">${user.tipo_membresia}</span></td>
                        <td class="p-4 text-gray-400 text-sm">${user.fecha_vencimiento || 'N/D'}</td>
                        <td class="p-4 text-center">
                            <button onclick='editarUsuario(${JSON.stringify(user)})' class="text-blue-400 hover:text-blue-300 mx-2" title="Editar"><i class="fas fa-edit"></i></button>
                            <button onclick="eliminarUsuario(${user.id_usuario})" class="text-red-500 hover:text-red-400 mx-2" title="Eliminar"><i class="fas fa-trash"></i></button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } catch (error) { console.error("Error al cargar usuarios:", error); }
        }

        document.getElementById('form-usuario').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('user-id').value;
            const formData = new FormData();
            if (id) { formData.append('id', id); formData.append('_method', 'PUT'); }
            
            formData.append('nombre', document.getElementById('user-nombre').value);
            formData.append('correo', document.getElementById('user-correo').value);
            formData.append('edad', document.getElementById('user-edad').value);
            formData.append('peso', document.getElementById('user-peso').value);
            formData.append('membresia', document.getElementById('user-membresia').value);
            formData.append('vencimiento', document.getElementById('user-vencimiento').value);
            formData.append('contrasena', document.getElementById('user-pass').value);
            
            const fotoInput = document.getElementById('user-foto').files[0];
            if (fotoInput) { formData.append('foto', fotoInput); }

            try {
                const respuesta = await fetch('api_usuarios.php', { method: 'POST', body: formData });
                const resultado = await respuesta.json();
                if (!resultado.success) { alert("Error: " + (resultado.error || "Fallo la operación")); } 
                else { cerrarModal(); cargarUsuarios(); }
            } catch (err) { alert("Error de conexión."); }
        });

        async function eliminarUsuario(id) {
            if(confirm('¿Estás seguro de eliminar este socio?')) {
                await fetch('api_usuarios.php', { method: 'DELETE', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id }) });
                cargarUsuarios();
            }
        }

        function abrirModal() {
            document.getElementById('form-usuario').reset();
            document.getElementById('user-id').value = '';
            document.getElementById('modal-titulo').innerText = 'Registrar Nuevo Socio';
            document.getElementById('campo-contrasena').style.display = 'block';
            document.getElementById('user-pass').setAttribute('required', 'true');
            document.getElementById('modal-usuario').classList.replace('hidden', 'flex');
        }

        function editarUsuario(user) {
            abrirModal();
            document.getElementById('modal-titulo').innerText = 'Editar Datos del Socio #' + user.id_usuario;
            document.getElementById('user-id').value = user.id_usuario;
            document.getElementById('user-nombre').value = user.nombre_completo;
            document.getElementById('user-correo').value = user.correo;
            document.getElementById('user-edad').value = user.edad || '';
            document.getElementById('user-peso').value = user.peso || '';
            document.getElementById('user-membresia').value = user.tipo_membresia;
            document.getElementById('user-vencimiento').value = user.fecha_vencimiento || '';
            document.getElementById('campo-contrasena').style.display = 'none';
            document.getElementById('user-pass').removeAttribute('required');
        }

        function cerrarModal() {
            document.getElementById('modal-usuario').classList.replace('flex', 'hidden');
        }
    </script>
</body>
</html>