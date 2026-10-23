<?php
require 'conexion.php';
// LÓGICA PARA EXPORTAR A EXCEL
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
// MANTENIMIENTO
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registrar_mantenimiento'])) {
    $nombre_equipo = $conn->real_escape_string(trim($_POST['nombre_equipo']));
    $tipo = $conn->real_escape_string(trim($_POST['tipo']));
    $motivo = $conn->real_escape_string(trim($_POST['motivo']));

    if (!empty($nombre_equipo) && !empty($motivo)) {
        $conn->query("INSERT INTO mantenimiento (nombre_equipo, tipo, motivo) VALUES ('$nombre_equipo', '$tipo', '$motivo')");
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
// VENTAS (CREAR, EDITAR Y ELIMINAR)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_venta'])) {
    $id_venta = isset($_POST['id_venta']) ? intval($_POST['id_venta']) : 0;
    $concepto = $conn->real_escape_string(trim($_POST['concepto']));
    $monto = floatval($_POST['monto']);

    if (!empty($concepto) && $monto > 0) {
        if ($id_venta > 0) {
            $conn->query("UPDATE ventas SET concepto='$concepto', monto=$monto WHERE id_venta=$id_venta");
        } else {
            $conn->query("INSERT INTO ventas (concepto, monto) VALUES ('$concepto', $monto)");
        }
        header("Location: admin.php?exito=venta");
        exit();
    }
}
if (isset($_GET['eliminar_venta'])) {
    $id_v = intval($_GET['eliminar_venta']);
    $conn->query("DELETE FROM ventas WHERE id_venta = $id_v");
    header("Location: admin.php?eliminado=venta");
    exit();
}
// EMPLEADOS
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_empleado'])) {
    $id_emp = isset($_POST['id_empleado']) ? intval($_POST['id_empleado']) : 0;
    $nombre_emp = $conn->real_escape_string(trim($_POST['nombre_empleado']));
    $rol_emp = $conn->real_escape_string(trim($_POST['rol_empleado']));

    if (!empty($nombre_emp) && !empty($rol_emp)) {
        if ($id_emp > 0) {
            $conn->query("UPDATE empleados SET nombre='$nombre_emp', rol='$rol_emp' WHERE id_empleado=$id_emp");
        } else {
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
// REPORTES
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
// VISITAS Y RESERVAS
if (isset($_GET['eliminar_visita'])) {
    $id_visita = intval($_GET['eliminar_visita']);
    $conn->query("DELETE FROM visitas WHERE id_visita = $id_visita");
    header("Location: admin.php?eliminado=visita");
    exit();
}
if (isset($_GET['eliminar_reserva'])) {
    $id_res = intval($_GET['eliminar_reserva']);
    $conn->query("DELETE FROM reservas_salas WHERE id_reserva = $id_res");
    header("Location: admin.php?eliminado=reserva");
    exit();
}

// Consultas a BD
$resultado_mantenimiento = $conn->query("SELECT * FROM mantenimiento ORDER BY id DESC");
$resultado_ventas = $conn->query("SELECT * FROM ventas ORDER BY fecha_venta DESC");
$resultado_empleados = $conn->query("SELECT * FROM empleados ORDER BY id_empleado DESC");
$resultado_reportes = $conn->query("SELECT * FROM reportes ORDER BY fecha_reporte DESC");
$resultado_visitas = $conn->query("SELECT * FROM visitas ORDER BY fecha_visita ASC");
$resultado_reservas = $conn->query("SELECT * FROM reservas_salas ORDER BY fecha_renta ASC");
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
        
        <!--BOTONES ALINEADOS -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <h1 class="font-heading text-4xl font-bold text-white uppercase"><span class="text-brand">Admin</span> GYMICO</h1>
            
            <!-- Botones Superiores -->
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <!-- Botón de Soporte -->
                <a href="https://wa.me/525512345678?text=Hola,%20soy%20el%20administrador%20de%20GYMICO.%20Necesito%20reportar%20un%20error%20en%20el%20sistema." target="_blank" class="bg-dark-800 border border-green-500/50 hover:bg-green-900/40 text-green-400 px-4 py-2.5 rounded-lg font-bold transition flex items-center gap-2 shadow-lg justify-center text-sm">
                    <i class="fab fa-whatsapp text-lg"></i> Soporte (+52 55 1234 5678)
                </a>
                
                <!-- Botón de Recepción -->
                <a href="escaner.html" class="bg-dark-800 border border-dark-700 hover:bg-dark-700 text-white px-4 py-2.5 rounded-lg font-bold transition flex items-center gap-2 shadow-lg justify-center text-sm">
                    <i class="fas fa-id-card text-brand"></i> Ir a Recepción
                </a>
            </div>
        </div>

        <!-- SISTEMA DE PESTAÑAS -->
        <div class="flex gap-6 mb-8 border-b border-dark-700 pb-2 overflow-x-auto">
            <button onclick="mostrarTab('socios')" id="btn-socios" class="text-brand font-bold uppercase pb-2 border-b-2 border-brand transition whitespace-nowrap">Gestión de Socios</button>
            <button onclick="mostrarTab('mantenimiento')" id="btn-mantenimiento" class="text-gray-400 font-bold uppercase pb-2 border-b-2 border-transparent hover:text-white transition whitespace-nowrap">Maquinaria</button>
            <button onclick="mostrarTab('ventas')" id="btn-ventas" class="text-gray-400 font-bold uppercase pb-2 border-b-2 border-transparent hover:text-white transition whitespace-nowrap">Ventas</button>
            <button onclick="mostrarTab('empleados')" id="btn-empleados" class="text-gray-400 font-bold uppercase pb-2 border-b-2 border-transparent hover:text-white transition whitespace-nowrap">Empleados</button>
            <button onclick="mostrarTab('reportes')" id="btn-reportes" class="text-gray-400 font-bold uppercase pb-2 border-b-2 border-transparent hover:text-white transition whitespace-nowrap">Reportes</button>
            <button onclick="mostrarTab('visitas')" id="btn-visitas" class="text-gray-400 font-bold uppercase pb-2 border-b-2 border-transparent hover:text-white transition whitespace-nowrap">Visitas Agendadas</button>
            <button onclick="mostrarTab('reservas')" id="btn-reservas" class="text-gray-400 font-bold uppercase pb-2 border-b-2 border-transparent hover:text-white transition whitespace-nowrap">Salas Reservadas</button>
        </div>

        <!-- SOCIOS -->
        <div id="tab-socios" class="block">
            <div class="flex justify-end mb-4">
                <button onclick="abrirModal()" class="bg-brand hover:bg-red-700 text-white px-6 py-2 rounded-lg font-bold transition shadow-lg">
                    <i class="fas fa-plus mr-2"></i> Nuevo Socio
                </button>
            </div>
            <div class="bg-dark-800 rounded-xl border border-dark-700 overflow-hidden shadow-2xl">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-dark-900 text-gray-400 uppercase text-sm border-b border-dark-700">
                            <th class="p-4">Foto</th><th class="p-4">ID</th><th class="p-4">Nombre / Correo</th><th class="p-4">Edad / Peso</th><th class="p-4">Membresía</th><th class="p-4">Vencimiento</th><th class="p-4 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-usuarios" class="divide-y divide-dark-700"></tbody>
                </table>
            </div>
        </div>

        <!-- MANTENIMIENTO -->
        <div id="tab-mantenimiento" class="hidden">
            <div class="bg-dark-800 rounded-2xl p-8 border border-dark-700 shadow-xl">
                <h2 class="font-heading text-2xl font-bold text-white uppercase mb-6">Gestión de Equipos en Mantenimiento</h2>
                <form action="admin.php" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8 bg-dark-900 p-6 rounded-xl border border-dark-700">
                    <div><label class="block text-sm text-gray-400 mb-1">Nombre Equipo</label><input type="text" name="nombre_equipo" required class="w-full bg-dark-800 border border-dark-700 text-white px-4 py-2.5 rounded-lg"></div>
                    <div><label class="block text-sm text-gray-400 mb-1">Tipo</label><select name="tipo" class="w-full bg-dark-800 border border-dark-700 text-white px-4 py-2.5 rounded-lg"><option value="Aparato">Aparato</option><option value="Sala">Sala</option></select></div>
                    <div><label class="block text-sm text-gray-400 mb-1">Motivo</label><input type="text" name="motivo" required class="w-full bg-dark-800 border border-dark-700 text-white px-4 py-2.5 rounded-lg"></div>
                    <div class="flex items-end"><button type="submit" name="registrar_mantenimiento" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2.5 rounded-lg">Registrar</button></div>
                </form>
                <table class="w-full text-left border-collapse">
                    <thead><tr class="bg-dark-900 text-gray-400 text-xs uppercase border-b border-dark-700"><th class="p-3">ID</th><th class="p-3">Equipo</th><th class="p-3">Tipo</th><th class="p-3">Motivo</th><th class="p-3 text-center">Acciones</th></tr></thead>
                    <tbody class="divide-y divide-dark-700 text-sm">
                        <?php if ($resultado_mantenimiento && $resultado_mantenimiento->num_rows > 0): while($row = $resultado_mantenimiento->fetch_assoc()): ?>
                            <tr class="hover:bg-dark-900/50"><td class="p-3">#<?php echo $row['id']; ?></td><td class="p-3 font-bold"><?php echo htmlspecialchars($row['nombre_equipo']); ?></td><td class="p-3"><?php echo htmlspecialchars($row['tipo']); ?></td><td class="p-3"><?php echo htmlspecialchars($row['motivo']); ?></td><td class="p-3 text-center"><a href="admin.php?resolver=<?php echo $row['id']; ?>" class="bg-emerald-600 text-white px-3 py-1 rounded text-xs font-bold">Reparado</a></td></tr>
                        <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- VENTAS -->
        <div id="tab-ventas" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-dark-800 rounded-2xl p-6 border border-dark-700 shadow-xl">
                    <h3 id="titulo-form-venta" class="font-heading text-xl font-bold text-white uppercase mb-4">Registrar Venta</h3>
                    <form action="admin.php" method="POST" class="space-y-4">
                        <input type="hidden" name="id_venta" id="form-venta-id" value="0">
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Concepto</label>
                            <select name="concepto" id="form-venta-concepto" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg">
                                <option value="Membresia Basica">Membresía Básica</option>
                                <option value="Membresia Premium">Membresía Premium</option>
                                <option value="Membresia VIP">Membresía VIP</option>
                                <option value="Inscripcion">Inscripción</option>
                                <option value="Renta de Sala">Renta de Sala</option>
                                <option value="Suplemento">Suplemento</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Monto ($)</label>
                            <input type="number" step="0.01" name="monto" id="form-venta-monto" required class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg">
                        </div>
                        <button type="submit" name="guardar_venta" id="btn-guardar-venta" class="w-full bg-brand text-white font-bold py-2.5 rounded-lg">Guardar Venta</button>
                        <button type="button" onclick="limpiarFormVenta()" class="w-full bg-dark-700 text-gray-300 font-bold py-2 rounded-lg text-xs mt-2">Limpiar / Cancelar Edición</button>
                    </form>
                </div>

                <div class="bg-dark-800 rounded-2xl p-6 border border-dark-700 shadow-xl md:col-span-2">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-heading text-xl font-bold text-white uppercase">Historial de Ventas</h3>
                        <a href="admin.php?exportar=excel" class="bg-emerald-600 text-white px-4 py-2 rounded-lg font-bold text-xs"><i class="fas fa-file-excel mr-1"></i> Descargar Excel</a>
                    </div>
                    <div class="overflow-x-auto max-h-[400px] overflow-y-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-dark-900 text-gray-400 text-xs uppercase border-b border-dark-700">
                                    <th class="p-3">ID</th>
                                    <th class="p-3">Concepto</th>
                                    <th class="p-3">Monto</th>
                                    <th class="p-3">Fecha</th>
                                    <th class="p-3 text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-700 text-sm">
                                <?php if ($resultado_ventas && $resultado_ventas->num_rows > 0): while($v = $resultado_ventas->fetch_assoc()): ?>
                                    <tr class="hover:bg-dark-900/50">
                                        <td class="p-3 font-mono text-gray-400">#<?php echo $v['id_venta']; ?></td>
                                        <td class="p-3 font-bold text-white"><?php echo htmlspecialchars($v['concepto']); ?></td>
                                        <td class="p-3 text-emerald-400 font-bold">$<?php echo number_format($v['monto'], 2); ?></td>
                                        <td class="p-3 text-xs text-gray-400"><?php echo $v['fecha_venta']; ?></td>
                                        <td class="p-3 text-center">
                                            <button onclick="editarVenta(<?php echo $v['id_venta']; ?>, '<?php echo addslashes($v['concepto']); ?>', <?php echo $v['monto']; ?>)" class="text-blue-400 mx-2" title="Editar"><i class="fas fa-edit"></i></button>
                                            <a href="admin.php?eliminar_venta=<?php echo $v['id_venta']; ?>" onclick="return confirm('¿Estás seguro de eliminar esta venta?');" class="text-red-500 mx-2" title="Eliminar"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endwhile; else: ?>
                                    <tr><td colspan="5" class="p-4 text-center text-gray-500">No hay ventas registradas.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- EMPLEADOS -->
        <div id="tab-empleados" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-dark-800 rounded-2xl p-6 border border-dark-700 shadow-xl">
                    <h3 class="font-heading text-xl font-bold text-white uppercase mb-4">Guardar Empleado</h3>
                    <form action="admin.php" method="POST" class="space-y-4">
                        <input type="hidden" name="id_empleado" id="form-emp-id" value="0">
                        <div><label class="block text-sm text-gray-400 mb-1">Nombre</label><input type="text" name="nombre_empleado" id="form-emp-nombre" required class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg"></div>
                        <div>
                            <label class="block text-sm text-gray-400 mb-1">Rol</label>
                            <select name="rol_empleado" id="form-emp-rol" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg">
                                <option value="Entrenador">Entrenador</option>
                                <option value="Recepcionista">Recepcionista</option>
                                <option value="Nutriologo">Nutriólogo</option>
                                <option value="Gerente">Gerente</option>
                                <option value="Personal de Limpieza">Personal de Limpieza</option>
                                <option value="Mantenimiento">Mantenimiento</option>
                                <option value="Seguridad">Seguridad</option>
                            </select>
                        </div>
                        <button type="submit" name="guardar_empleado" id="btn-guardar-emp" class="w-full bg-blue-600 text-white font-bold py-2.5 rounded-lg">Registrar</button>
                        <button type="button" onclick="limpiarFormEmpleado()" class="w-full bg-dark-700 text-gray-300 font-bold py-2 rounded-lg text-xs mt-2">Limpiar</button>
                    </form>
                </div>
                <div class="bg-dark-800 rounded-2xl p-6 border border-dark-700 shadow-xl md:col-span-2">
                    <h3 class="font-heading text-xl font-bold text-white uppercase mb-4">Plantilla de Staff</h3>
                    <table class="w-full text-left border-collapse">
                        <thead><tr class="bg-dark-900 text-gray-400 text-xs uppercase border-b border-dark-700"><th class="p-3">ID</th><th class="p-3">Nombre</th><th class="p-3">Rol</th><th class="p-3 text-center">Acciones</th></tr></thead>
                        <tbody class="divide-y divide-dark-700 text-sm">
                            <?php if ($resultado_empleados && $resultado_empleados->num_rows > 0): while($emp = $resultado_empleados->fetch_assoc()): ?>
                                <tr class="hover:bg-dark-900/50"><td class="p-3 font-mono text-blue-400">EMP-<?php echo str_pad($emp['id_empleado'], 4, '0', STR_PAD_LEFT); ?></td><td class="p-3 font-bold"><?php echo htmlspecialchars($emp['nombre']); ?></td><td class="p-3"><?php echo htmlspecialchars($emp['rol']); ?></td><td class="p-3 text-center"><button onclick="editarEmpleado(<?php echo $emp['id_empleado']; ?>, '<?php echo addslashes($emp['nombre']); ?>', '<?php echo addslashes($emp['rol']); ?>')" class="text-blue-400 mx-2"><i class="fas fa-edit"></i></button><a href="admin.php?eliminar_empleado=<?php echo $emp['id_empleado']; ?>" onclick="return confirm('¿Baja?');" class="text-red-500"><i class="fas fa-trash-alt"></i></a></td></tr>
                            <?php endwhile; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- REPORTES -->
        <div id="tab-reportes" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-dark-800 rounded-2xl p-6 border border-dark-700 shadow-xl">
                    <h3 class="font-heading text-xl font-bold text-white uppercase mb-4">Levantar Reporte</h3>
                    <form action="admin.php" method="POST" class="space-y-4">
                        <div><label class="block text-sm text-gray-400 mb-1">Tipo</label><select name="tipo_reporte" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg"><option value="Problema en Pagina Web">Error Web</option><option value="Daño en Maquinaria">Daño Maquinaria</option></select></div>
                        <div><label class="block text-sm text-gray-400 mb-1">Descripción</label><textarea name="descripcion_reporte" required rows="3" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg"></textarea></div>
                        <div><label class="block text-sm text-gray-400 mb-1">Reportado por</label><input type="text" name="reportado_por" required class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg"></div>
                        <button type="submit" name="registrar_reporte" class="w-full bg-yellow-600 text-white font-bold py-2.5 rounded-lg">Enviar</button>
                    </form>
                </div>
                <div class="bg-dark-800 rounded-2xl p-6 border border-dark-700 shadow-xl md:col-span-2">
                    <h3 class="font-heading text-xl font-bold text-white uppercase mb-4">Buzón de Reportes</h3>
                    <div class="space-y-4 max-h-[500px] overflow-y-auto">
                        <?php if ($resultado_reportes && $resultado_reportes->num_rows > 0): while($rep = $resultado_reportes->fetch_assoc()): ?>
                            <div class="bg-dark-900 border-l-4 border-yellow-500 rounded-lg p-4 flex justify-between items-center">
                                <div><span class="text-xs font-bold text-yellow-500 uppercase"><?php echo htmlspecialchars($rep['tipo_reporte']); ?></span><p class="text-sm mt-1">"<?php echo htmlspecialchars($rep['descripcion']); ?>"</p><p class="text-xs text-gray-400 mt-1">Por: <?php echo htmlspecialchars($rep['reportado_por']); ?></p></div>
                                <a href="admin.php?resolver_reporte=<?php echo $rep['id_reporte']; ?>" class="bg-emerald-600/20 text-emerald-500 border border-emerald-500 px-3 py-1.5 rounded text-xs font-bold">Resolver</a>
                            </div>
                        <?php endwhile; else: ?><p class="text-gray-500 text-center">Buzón limpio.</p><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- VISITAS AGENDADAS -->
        <div id="tab-visitas" class="hidden">
            <div class="bg-dark-800 rounded-2xl p-8 border border-dark-700 shadow-xl">
                <div class="flex items-center gap-3 mb-6">
                    <h2 class="font-heading text-2xl font-bold text-white uppercase">Visitas y Citas <span class="text-brand">Agendadas</span></h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-dark-900 text-gray-400 text-xs uppercase border-b border-dark-700">
                                <th class="p-3">ID</th>
                                <th class="p-3">Visitante</th>
                                <th class="p-3">Correo / Teléfono</th>
                                <th class="p-3">Fecha de Visita</th>
                                <th class="p-3">Horario Sugerido</th>
                                <th class="p-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-dark-700 text-sm">
                            <?php if ($resultado_visitas && $resultado_visitas->num_rows > 0): ?>
                                <?php while($vis = $resultado_visitas->fetch_assoc()): ?>
                                    <tr class="hover:bg-dark-900/50 transition">
                                        <td class="p-3 font-mono text-gray-400">#<?php echo $vis['id_visita']; ?></td>
                                        <td class="p-3 font-bold text-white"><?php echo htmlspecialchars($vis['nombre_visitante']); ?></td>
                                        <td class="p-3 text-gray-300">
                                            <?php echo htmlspecialchars($vis['correo_visitante']); ?><br>
                                            <span class="text-xs text-gray-500"><?php echo htmlspecialchars($vis['telefono']); ?></span>
                                        </td>
                                        <td class="p-3 text-brand font-bold">
                                            <?php echo date("d M Y", strtotime($vis['fecha_visita'])); ?>
                                        </td>
                                        <td class="p-3 text-gray-300 font-semibold">
                                            <?php echo htmlspecialchars($vis['horario']); ?>
                                        </td>
                                        <td class="p-3 text-center">
                                            <a href="admin.php?eliminar_visita=<?php echo $vis['id_visita']; ?>" onclick="return confirm('¿Marcar esta visita como atendida y eliminarla?');" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-bold uppercase transition inline-flex items-center gap-1 shadow">
                                                <i class="fas fa-check"></i> Atendido
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-gray-400">
                                        No hay visitas o citas agendadas por el momento.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RESERVAS DE SALAS -->
        <div id="tab-reservas" class="hidden">
            <div class="bg-dark-800 rounded-2xl p-8 border border-dark-700 shadow-xl">
                <div class="flex items-center gap-3 mb-6">
                    <i class="fas fa-door-open text-3xl text-brand"></i>
                    <h2 class="font-heading text-2xl font-bold text-white uppercase">Salas <span class="text-brand">Reservadas (VIP)</span></h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-dark-900 text-gray-400 text-xs uppercase border-b border-dark-700">
                                <th class="p-3">ID</th>
                                <th class="p-3">Socio VIP</th>
                                <th class="p-3">Sala Solicitada</th>
                                <th class="p-3">Fecha de Renta</th>
                                <th class="p-3 text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-dark-700 text-sm">
                            <?php if ($resultado_reservas && $resultado_reservas->num_rows > 0): ?>
                                <?php while($res = $resultado_reservas->fetch_assoc()): ?>
                                    <tr class="hover:bg-dark-900/50 transition">
                                        <td class="p-3 font-mono text-gray-400">#<?php echo $res['id_reserva']; ?></td>
                                        <td class="p-3 font-bold text-white"><?php echo htmlspecialchars($res['nombre_usuario']); ?> <br><span class="text-xs text-gray-500">ID: GYM-<?php echo str_pad($res['id_usuario'], 4, '0', STR_PAD_LEFT); ?></span></td>
                                        <td class="p-3 text-gray-300 font-semibold"><?php echo htmlspecialchars($res['sala']); ?></td>
                                        <td class="p-3 text-brand font-bold"><?php echo date("d M Y", strtotime($res['fecha_renta'])); ?></td>
                                        <td class="p-3 text-center">
                                            <a href="admin.php?eliminar_reserva=<?php echo $res['id_reserva']; ?>" onclick="return confirm('¿Marcar la sala como usada/atendida y eliminar de la lista?');" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded text-xs font-bold uppercase transition inline-flex items-center gap-1 shadow">
                                                <i class="fas fa-check"></i> Atendida
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-gray-400">
                                        No hay reservas de salas por el momento.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Formulario Socios -->
    <div id="modal-usuario" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 p-4">
        <div class="bg-dark-800 p-8 rounded-xl w-full max-w-lg border border-dark-700 max-h-[90vh] overflow-y-auto">
            <h2 id="modal-titulo" class="font-heading text-2xl font-bold text-white uppercase mb-6">Registrar Socio</h2>
            <form id="form-usuario" class="space-y-4">
                <input type="hidden" id="user-id">
                <div><label class="block text-sm text-gray-400 mb-1">Nombre</label><input type="text" id="user-nombre" required class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2 rounded"></div>
                <div><label class="block text-sm text-gray-400 mb-1">Correo</label><input type="email" id="user-correo" required class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2 rounded"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm text-gray-400 mb-1">Edad</label><input type="number" id="user-edad" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2 rounded"></div>
                    <div><label class="block text-sm text-gray-400 mb-1">Peso (kg)</label><input type="number" step="0.01" id="user-peso" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2 rounded"></div>
                </div>
                <div id="campo-contrasena"><label class="block text-sm text-gray-400 mb-1">Contraseña</label><input type="text" id="user-pass" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2 rounded"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div><label class="block text-sm text-gray-400 mb-1">Membresía</label><select id="user-membresia" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2 rounded"><option value="Basica">Básica</option><option value="Premium">Premium</option><option value="VIP">VIP</option></select></div>
                    <div><label class="block text-sm text-gray-400 mb-1">Vencimiento</label><input type="date" id="user-vencimiento" required class="w-full bg-dark-900 border border-dark-700 text-gray-300 px-4 py-2 rounded" style="color-scheme: dark;"></div>
                </div>
                <div><label class="block text-sm text-gray-400 mb-1">Foto</label><input type="file" id="user-foto" accept="image/*" class="w-full bg-dark-900 border border-dark-700 text-gray-300 px-4 py-2 rounded text-sm"></div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="cerrarModal()" class="px-4 py-2 text-gray-400">Cancelar</button>
                    <button type="submit" class="bg-brand text-white px-6 py-2 rounded font-bold">Guardar</button>
                </div>
            </form>
        </div>
    </div>


    <script>
        function mostrarTab(tabId) {
            const tabs = ['socios', 'mantenimiento', 'ventas', 'empleados', 'reportes', 'visitas', 'reservas'];
            tabs.forEach(t => {
                document.getElementById('tab-' + t).classList.replace('block', 'hidden');
                document.getElementById('btn-' + t).className = "text-gray-400 font-bold uppercase pb-2 border-b-2 border-transparent hover:text-white transition whitespace-nowrap";
            });
            document.getElementById('tab-' + tabId).classList.replace('hidden', 'block');
            document.getElementById('btn-' + tabId).className = "text-brand font-bold uppercase pb-2 border-b-2 border-brand transition whitespace-nowrap";
        }

        // --- Funciones para Ventas (Editar) ---
        function editarVenta(id, concepto, monto) {
            document.getElementById('form-venta-id').value = id;
            document.getElementById('form-venta-concepto').value = concepto;
            document.getElementById('form-venta-monto').value = monto;
            document.getElementById('titulo-form-venta').innerText = "Editar Venta #" + id;
            document.getElementById('btn-guardar-venta').innerText = "Actualizar Venta";
            mostrarTab('ventas');
        }

        function limpiarFormVenta() {
            document.getElementById('form-venta-id').value = "0";
            document.getElementById('form-venta-concepto').value = "Membresia Basica";
            document.getElementById('form-venta-monto').value = "";
            document.getElementById('titulo-form-venta').innerText = "Registrar Venta";
            document.getElementById('btn-guardar-venta').innerText = "Guardar Venta";
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
            document.getElementById('btn-guardar-emp').innerText = "Registrar";
        }

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
                            <button onclick='editarUsuario(${JSON.stringify(user)})' class="text-blue-400 mx-2" title="Editar"><i class="fas fa-edit"></i></button>
                            <button onclick="eliminarUsuario(${user.id_usuario})" class="text-red-500 mx-2" title="Eliminar"><i class="fas fa-trash"></i></button>
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
            if(confirm('¿Eliminar socio?')) {
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