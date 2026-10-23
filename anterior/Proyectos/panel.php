<?php
session_start();
require 'conexion.php';

$id_usuario = isset($_SESSION['id_usuario']) ? intval($_SESSION['id_usuario']) : 1;

$sql_usuario = "SELECT * FROM usuarios WHERE id_usuario = $id_usuario";
$res_usuario = $conn->query($sql_usuario);

if (!$res_usuario || $res_usuario->num_rows === 0) {
    header("Location: sesiones.html");
    exit();
}

$user = $res_usuario->fetch_assoc();
$tipo_membresia = trim($user['tipo_membresia']); 

// ==================================================
// PROCESAR EL PAGO EN LÍNEA DEL USUARIO
// ==================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['procesar_pago'])) {
    $concepto = "Pago Membresía " . $tipo_membresia . " (" . $user['nombre_completo'] . ")";
    $monto = 450.00;
    
    $conn->query("INSERT INTO ventas (concepto, monto) VALUES ('$concepto', $monto)");
    $conn->query("UPDATE usuarios SET fecha_vencimiento = DATE_ADD(IFNULL(fecha_vencimiento, CURRENT_DATE), INTERVAL 30 DAY), estado = 'Pagado' WHERE id_usuario = $id_usuario");
    
    echo "<script>alert('¡Pago procesado con éxito! Registrado en el sistema del administrador.'); window.location.href='panel.php';</script>";
    exit();
}

// ==================================================
// PROCESAR EL APARTADO DE SALAS (NUEVO)
// ==================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apartar_sala'])) {
    $sala = $conn->real_escape_string(trim($_POST['sala']));
    $fecha_renta = $conn->real_escape_string(trim($_POST['fecha_renta']));
    $nombre_socio = $conn->real_escape_string($user['nombre_completo']);
    
    if (!empty($sala) && !empty($fecha_renta)) {
        $conn->query("INSERT INTO reservas_salas (id_usuario, nombre_usuario, sala, fecha_renta) VALUES ($id_usuario, '$nombre_socio', '$sala', '$fecha_renta')");
        echo "<script>alert('¡Sala apartada con éxito! Nos comunicaremos contigo para confirmar el horario.'); window.location.href='panel.php';</script>";
        exit();
    }
}

// ==================================================
// PROCESAR EL REPORTE ENVIADO POR EL USUARIO
// ==================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enviar_reporte'])) {
    $tipo_rep = $conn->real_escape_string(trim($_POST['tipo_reporte']));
    $desc_rep = $conn->real_escape_string(trim($_POST['descripcion_reporte']));
    $autor_rep = $conn->real_escape_string($user['nombre_completo'] . ' (ID: GYM-' . str_pad($user['id_usuario'], 4, '0', STR_PAD_LEFT) . ')');

    if (!empty($desc_rep)) {
        $conn->query("INSERT INTO reportes (tipo_reporte, descripcion, reportado_por) VALUES ('$tipo_rep', '$desc_rep', '$autor_rep')");
        echo "<script>alert('¡Gracias! Tu reporte ha sido enviado al administrador.'); window.location.href='panel.php';</script>";
        exit();
    }
}

$sql_asistencias = "SELECT fecha_hora FROM asistencias WHERE id_usuario = $id_usuario ORDER BY fecha_hora DESC LIMIT 5";
$res_asistencias = $conn->query($sql_asistencias);

$fecha_vencimiento = !empty($user['fecha_vencimiento']) ? $user['fecha_vencimiento'] : date('Y-m-d');
$dias_restantes = (strtotime($fecha_vencimiento) - time()) / (60 * 60 * 24);
?>
<!DOCTYPE html>
<html lang="es" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Panel | GYMICO</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&family=Oswald:wght@500;700&display=swap" rel="stylesheet">
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: { DEFAULT: '#dc2626', dark: '#b91c1c' }, dark: { 900: '#111827', 800: '#1f2937', 700: '#374151' } },
                    fontFamily: { sans: ['Inter', 'sans-serif'], heading: ['Oswald', 'sans-serif'] }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #111827; }
        ::-webkit-scrollbar-thumb { background: #374151; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #dc2626; }
        section { scroll-margin-top: 5rem; }
    </style>
</head>
<body class="bg-dark-900 text-gray-200 font-sans antialiased">

    <!-- Navegación del Panel -->
    <nav class="fixed w-full z-50 bg-dark-900/95 backdrop-blur-sm border-b border-dark-800 shadow-lg" id="navbar">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0">
                    <a href="#" class="font-heading text-3xl font-bold uppercase tracking-wider">
                        <span class="text-white">GYM</span><span class="text-brand">ICO</span>
                    </a>
                </div>
                
                <div class="hidden md:flex space-x-6 items-center">
                    <a href="index.html" class="text-gray-300 hover:text-white transition text-sm font-medium"><i class="fas fa-home"></i> Inicio</a>
                    <a href="#perfil" class="text-gray-300 hover:text-brand transition text-sm font-semibold">Perfil</a>
                    <a href="#membresia" class="text-gray-300 hover:text-brand transition text-sm font-semibold">Membresía</a>
                    <a href="#asistencia" class="text-gray-300 hover:text-brand transition text-sm font-semibold">Asistencia</a>
                    
                    <?php if ($tipo_membresia === 'Premium' || $tipo_membresia === 'VIP'): ?>
                        <a href="#rutina" class="text-gray-300 hover:text-brand transition text-sm font-semibold">Rutina</a>
                        <a href="#dieta" class="text-gray-300 hover:text-brand transition text-sm font-semibold">Dieta</a>
                    <?php endif; ?>

                    <?php if ($tipo_membresia === 'VIP'): ?>
                        <a href="#apartar-salas" class="text-brand hover:text-red-400 transition text-sm font-bold"><i class="fas fa-door-open mr-1"></i> Renta Salas</a>
                    <?php endif; ?>

                    <button onclick="abrirModalReporte()" class="text-yellow-500 hover:text-yellow-400 transition text-sm font-bold ml-2 cursor-pointer">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Reportar Falla
                    </button>

                    <a href="index.html" class="border border-brand text-brand hover:bg-brand hover:text-white px-3 py-1.5 rounded-lg font-bold uppercase tracking-wide transition text-xs ml-2">
                        Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-16 space-y-16">

        <!-- 1. SECCIÓN PERFIL Y FOTO -->
        <section id="perfil" class="bg-dark-800 rounded-2xl p-8 border border-dark-700 shadow-xl flex flex-col md:flex-row items-center justify-between gap-8">
            <div class="flex items-center gap-6 w-full md:w-auto">
                <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-brand bg-dark-700 flex-shrink-0">
                    <img src="<?php echo htmlspecialchars(!empty($user['foto']) ? $user['foto'] : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=500&auto=format&fit=crop'); ?>" alt="Foto de perfil" class="w-full h-full object-cover">
                </div>
                <div>
                    <h1 class="font-heading text-3xl font-bold text-white uppercase mb-1"><?php echo htmlspecialchars($user['nombre_completo']); ?></h1>
                    <p class="text-gray-400 text-sm mb-3"><i class="fas fa-envelope mr-2 text-brand"></i><?php echo htmlspecialchars($user['correo']); ?></p>
                    <div class="flex gap-4 text-sm">
                        <span class="bg-dark-900 px-3 py-1 rounded-md text-gray-300"><strong>Edad:</strong> <?php echo htmlspecialchars($user['edad'] ?? 'N/D'); ?> años</span>
                        <span class="bg-dark-900 px-3 py-1 rounded-md text-gray-300"><strong>Peso:</strong> <?php echo htmlspecialchars($user['peso'] ?? 'N/D'); ?> kg</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-dark-900 p-4 rounded-xl border border-dark-700 text-center w-full md:w-auto flex flex-col items-center">
                <p class="text-xs text-gray-400 uppercase tracking-widest mb-3">Tu ID de Socio</p>
                <div class="bg-dark-800 border border-dark-700 py-2 px-4 rounded-xl">
                    <span class="text-brand font-mono font-bold text-xl tracking-wider">GYM-<?php echo str_pad($user['id_usuario'], 4, '0', STR_PAD_LEFT); ?></span>
                </div>
            </div>
        </section>

        <!-- 2. SECCIÓN MEMBRESÍA Y AVISO DE ESTADO -->
        <section id="membresia">
            <div class="flex items-center gap-3 mb-6">
                <i class="fas fa-id-card text-3xl text-brand"></i>
                <h2 class="font-heading text-3xl font-bold text-white uppercase">Membresía</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-dark-800 p-6 rounded-xl border border-dark-700 md:col-span-2 flex flex-col justify-between shadow-xl">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="text-xs text-brand font-bold uppercase tracking-wider bg-brand/10 px-2 py-1 rounded">Plan Actual</span>
                                <h3 class="text-2xl font-bold text-white mt-2">Membresía <?php echo htmlspecialchars($tipo_membresia); ?></h3>
                            </div>
                            <span class="bg-green-500/20 text-green-500 px-3 py-1 rounded-full text-sm font-bold border border-green-500/50"><?php echo htmlspecialchars($user['estado'] ?? 'Activo'); ?></span>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mt-4">
                            <div class="bg-dark-900 p-4 rounded-lg border border-dark-700">
                                <p class="text-gray-400 text-sm mb-1">Fecha de Registro</p>
                                <p class="text-lg font-bold text-white"><?php echo !empty($user['fecha_registro']) ? date("d M Y", strtotime($user['fecha_registro'])) : 'Reciente'; ?></p>
                            </div>
                            <div class="bg-dark-900 p-4 rounded-lg border border-dark-700">
                                <p class="text-gray-400 text-sm mb-1">Fecha de Expiración</p>
                                <p class="text-lg font-bold text-white"><?php echo htmlspecialchars($user['fecha_vencimiento'] ?? 'No definida'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t border-dark-700 flex flex-wrap justify-end gap-3">
                        <button onclick="abrirModalFactura()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-lg font-bold text-sm transition shadow-lg flex items-center gap-2 cursor-pointer">
                            <i class="fas fa-file-invoice-dollar"></i> Facturación
                        </button>
                        <button onclick="abrirModalPago()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-lg font-bold text-sm transition shadow-lg shadow-emerald-600/20 cursor-pointer flex items-center gap-2">
                            <i class="fas fa-credit-card"></i> Pagar / Renovar en Línea
                        </button>
                    </div>
                </div>

                <?php if ($dias_restantes > 5): ?>
                    <div class="bg-emerald-900/20 p-6 rounded-xl border border-emerald-500/50 flex flex-col items-center justify-center text-center shadow-xl">
                        <i class="fas fa-dumbbell text-4xl text-emerald-500 mb-4 animate-bounce"></i>
                        <h4 class="font-bold text-white mb-2 text-lg">¡Disfruta tu entrenamiento!</h4>
                        <p class="text-sm text-gray-300 mb-4">Tu membresía está activa y al corriente. ¡Aprovecha al máximo cada sesión en GYMICO!</p>
                        <button onclick="abrirModalRenovacion()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-bold text-sm transition shadow-lg shadow-emerald-600/20 cursor-pointer">Ver Detalles</button>
                    </div>
                <?php else: ?>
                    <div class="bg-red-900/20 p-6 rounded-xl border border-red-500/50 flex flex-col items-center justify-center text-center shadow-xl">
                        <i class="fas fa-exclamation-triangle text-4xl text-brand mb-4 animate-pulse"></i>
                        <h4 class="font-bold text-white mb-2 text-lg">¡Expiración Próxima!</h4>
                        <p class="text-sm text-gray-300 mb-4">Verifica tu fecha de corte. Realiza tu pago en línea para no perder tu racha.</p>
                        <button onclick="abrirModalPago()" class="bg-brand hover:bg-brand-dark text-white px-6 py-2 rounded-lg font-bold text-sm transition shadow-lg shadow-brand/20 cursor-pointer">Pagar Ahora</button>
                    </div>
                <?php endif; ?>
            </div>
        </section>

        <!-- 3. SECCIÓN ASISTENCIA -->
        <section id="asistencia">
            <div class="flex items-center gap-3 mb-6">
                <i class="fas fa-calendar-check text-3xl text-brand"></i>
                <h2 class="font-heading text-3xl font-bold text-white uppercase">Últimas Asistencias</h2>
            </div>
            
            <div class="bg-dark-800 rounded-xl border border-dark-700 overflow-hidden shadow-xl">
                <div class="p-4 grid grid-cols-1 divide-y divide-dark-700">
                    <?php if ($res_asistencias && $res_asistencias->num_rows > 0): ?>
                        <?php while($asistencia = $res_asistencias->fetch_assoc()): ?>
                            <div class="py-4 flex justify-between items-center px-4 hover:bg-dark-700/50 transition">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-green-500/20 text-green-500 flex items-center justify-center"><i class="fas fa-check"></i></div>
                                    <div>
                                        <p class="font-bold text-white"><?php echo date("l, d M Y", strtotime($asistencia['fecha_hora'])); ?></p>
                                        <p class="text-sm text-gray-400">Entrada autorizada en recepción</p>
                                    </div>
                                </div>
                                <span class="text-brand font-bold font-mono text-lg"><?php echo date("H:i", strtotime($asistencia['fecha_hora'])); ?> hrs</span>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="py-8 text-center text-gray-400">
                            <i class="fas fa-info-circle mb-2 text-2xl"></i>
                            <p>Aún no tienes asistencias registradas. ¡Pasa a recepción con tu ID!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <!-- 4. SECCIÓN VIP: APARTAR SALAS (CONECTADA) -->
        <?php if ($tipo_membresia === 'VIP'): ?>
            <section id="apartar-salas" class="bg-gradient-to-r from-dark-800 to-dark-900 border border-brand/50 rounded-2xl p-8 shadow-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <i class="fas fa-door-open text-3xl text-brand"></i>
                    <h2 class="font-heading text-3xl font-bold text-white uppercase">Apartado de Salas Exclusivas</h2>
                </div>
                <p class="text-gray-300 text-sm mb-6">Como socio <strong>VIP</strong>, tienes derecho a reservar nuestras salas multiusos para entrenamientos privados o sesiones especiales.</p>
                
                <form action="panel.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Seleccionar Sala</label>
                        <select name="sala" required class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-brand text-sm">
                            <option value="Sala Multiusos A (Espejos y Audio)">Sala Multiusos A (Espejos y Audio)</option>
                            <option value="Sala B (Piso Amortiguado)">Sala B (Piso Amortiguado)</option>
                            <option value="Estudio Privado de Cardio">Estudio Privado de Cardio</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Fecha de Renta</label>
                        <input type="date" name="fecha_renta" required min="<?php echo date('Y-m-d'); ?>" class="w-full bg-dark-900 border border-dark-700 text-gray-300 px-4 py-2.5 rounded-lg text-sm" style="color-scheme: dark;">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" name="apartar_sala" class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-2.5 rounded-lg uppercase tracking-wide transition text-sm shadow-lg shadow-brand/20 cursor-pointer">
                            Confirmar Apartado
                        </button>
                    </div>
                </form>
            </section>
        <?php endif; ?>

        <!-- 5. SECCIONES EXTENSAS DE RUTINA Y DIETA -->
        <?php if ($tipo_membresia === 'Premium' || $tipo_membresia === 'VIP'): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <section id="rutina">
                    <div class="flex items-center gap-3 mb-6">
                        <i class="fas fa-dumbbell text-3xl text-brand"></i>
                        <h2 class="font-heading text-3xl font-bold text-white uppercase">Rutina Profesional</h2>
                    </div>
                    <div class="bg-dark-800 rounded-xl border border-dark-700 p-6 space-y-6 shadow-xl">
                        <div class="border-b border-dark-700 pb-4">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-xl font-bold text-white">Día 1: Pecho y Tríceps</h3>
                                <span class="bg-brand text-white text-xs px-2.5 py-1 rounded font-bold uppercase">Hipertrofia</span>
                            </div>
                            <ul class="space-y-2 text-sm text-gray-300">
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded border-l-2 border-brand"><span>Press de Banca Plano</span> <strong class="text-brand">4 x 10-12 reps</strong></li>
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded border-l-2 border-brand"><span>Press Inclinado Mancuernas</span> <strong class="text-brand">4 x 12 reps</strong></li>
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded border-l-2 border-brand"><span>Aperturas en Pec Deck</span> <strong class="text-brand">3 x 15 reps</strong></li>
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded border-l-2 border-brand"><span>Press Francés Barra EZ</span> <strong class="text-brand">4 x 10 reps</strong></li>
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded border-l-2 border-brand"><span>Extensiones en Polea</span> <strong class="text-brand">3 x 15 reps</strong></li>
                            </ul>
                        </div>
                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-xl font-bold text-white">Día 2: Pierna Completa</h3>
                                <span class="bg-brand text-white text-xs px-2.5 py-1 rounded font-bold uppercase">Fuerza</span>
                            </div>
                            <ul class="space-y-2 text-sm text-gray-300">
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded border-l-2 border-brand"><span>Sentadilla Libre</span> <strong class="text-brand">4 x 8-10 reps</strong></li>
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded border-l-2 border-brand"><span>Prensa 45°</span> <strong class="text-brand">4 x 12 reps</strong></li>
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded border-l-2 border-brand"><span>Extensiones Cuádriceps</span> <strong class="text-brand">3 x 15 reps</strong></li>
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded border-l-2 border-brand"><span>Curl Femoral Acostado</span> <strong class="text-brand">4 x 12 reps</strong></li>
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded border-l-2 border-brand"><span>Elevación Talones</span> <strong class="text-brand">4 x 20 reps</strong></li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section id="dieta">
                    <div class="flex items-center gap-3 mb-6">
                        <i class="fas fa-apple-alt text-3xl text-brand"></i>
                        <h2 class="font-heading text-3xl font-bold text-white uppercase">Plan Nutricional</h2>
                    </div>
                    <div class="bg-dark-800 rounded-xl border border-dark-700 p-6 flex flex-col justify-between space-y-4 shadow-xl">
                        <div class="flex justify-between items-center mb-2 border-b border-dark-700 pb-3">
                            <h3 class="text-xl font-bold text-white">Volumen Limpio</h3>
                            <span class="text-brand font-bold bg-brand/10 px-3 py-1 rounded-lg">2,800 kcal</span>
                        </div>
                        <div class="space-y-3 text-sm text-gray-300">
                            <div class="bg-dark-900 p-3 rounded-lg border-l-4 border-brand">
                                <p class="text-brand font-bold uppercase text-xs mb-1"><i class="fas fa-sun mr-1"></i> Desayuno</p>
                                <p class="text-white">100g avena con leche light, 4 huevos enteros, 1 plátano.</p>
                            </div>
                            <div class="bg-dark-900 p-3 rounded-lg border-l-4 border-brand">
                                <p class="text-brand font-bold uppercase text-xs mb-1"><i class="fas fa-cookie-bite mr-1"></i> Snack AM</p>
                                <p class="text-white">1 taza yogur griego y 30g de nueces mixtas.</p>
                            </div>
                            <div class="bg-dark-900 p-3 rounded-lg border-l-4 border-brand">
                                <p class="text-brand font-bold uppercase text-xs mb-1"><i class="fas fa-utensils mr-1"></i> Comida</p>
                                <p class="text-white">200g pechuga plancha, 150g arroz blanco y brócoli.</p>
                            </div>
                            <div class="bg-dark-900 p-3 rounded-lg border-l-4 border-brand">
                                <p class="text-brand font-bold uppercase text-xs mb-1"><i class="fas fa-bolt mr-1"></i> Snack PM</p>
                                <p class="text-white">1 scoop proteína Whey y pan integral con mantequilla maní.</p>
                            </div>
                            <div class="bg-dark-900 p-3 rounded-lg border-l-4 border-brand">
                                <p class="text-brand font-bold uppercase text-xs mb-1"><i class="fas fa-moon mr-1"></i> Cena</p>
                                <p class="text-white">150g de salmón asado y ensalada mixta con aguacate.</p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        <?php endif; ?>

        <!-- 6. SECCIÓN DE AVISOS DE MANTENIMIENTO -->
        <section id="mantenimiento-aviso" class="bg-dark-800 border border-yellow-500/40 rounded-2xl p-8 shadow-xl mt-8">
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-tools text-3xl text-yellow-500"></i>
                <h2 class="font-heading text-3xl font-bold text-white uppercase">Avisos de <span class="text-yellow-500">Mantenimiento</span></h2>
            </div>
            <p class="text-gray-300 text-sm mb-6">Los siguientes aparatos o salas se encuentran temporalmente fuera de servicio. Agradecemos su comprensión.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php
                $sql_mant = "SELECT * FROM mantenimiento";
                $res_mant = $conn->query($sql_mant);
                if ($res_mant &&$res_mant->num_rows > 0):
                    while($item =$res_mant->fetch_assoc()):
                ?>
                    <div class="bg-dark-900 border border-red-500/30 rounded-xl p-4 flex flex-col justify-between relative overflow-hidden">
                        <div class="absolute top-0 right-0 bg-red-600 text-white text-[10px] font-bold px-3 py-0.5 uppercase tracking-wider rounded-bl-lg">
                            Fuera de Servicio
                        </div>
                        <div>
                            <span class="text-xs text-yellow-500 font-bold uppercase block mb-1">
                                <i class="fas fa-exclamation-triangle mr-1"></i> <?php echo htmlspecialchars($item['tipo']); ?>
                            </span>
                            <h3 class="font-heading text-xl font-bold text-white uppercase mb-2"><?php echo htmlspecialchars($item['nombre_equipo']); ?></h3>
                            <p class="text-gray-400 text-xs"><strong>Motivo:</strong> <?php echo htmlspecialchars($item['motivo']); ?></p>
                        </div>
                    </div>
                <?php 
                    endwhile; 
                else:
                ?>
                    <div class="col-span-full bg-dark-900 p-6 rounded-xl text-center border border-dark-700 text-gray-400 text-sm">
                        <i class="fas fa-check-circle text-emerald-500 text-2xl mb-2 block"></i>
                        ¡Excelente noticia! Todas nuestras instalaciones y equipos operan al 100%.
                    </div>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <!-- VENTANAS MODALES -->
    
    <!-- Modal: Pago en Línea -->
    <div id="modal-pago" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 p-4">
        <div class="bg-dark-800 p-8 rounded-2xl w-full max-w-md border border-dark-700 shadow-2xl relative">
            <button onclick="cerrarModalPago()" class="absolute top-4 right-4 text-gray-400 hover:text-white text-xl cursor-pointer"><i class="fas fa-times"></i></button>
            <div class="w-16 h-16 bg-emerald-500/20 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-credit-card"></i>
            </div>
            <h3 class="font-heading text-2xl font-bold text-white uppercase mb-2 text-center">Pasarela de Pago GYMICO</h3>
            <p class="text-gray-300 text-sm mb-6 text-center">Renueva tu membresía <strong><?php echo htmlspecialchars($tipo_membresia); ?></strong>. Se registrará en el sistema.</p>
            
            <form action="panel.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Nombre en la Tarjeta</label>
                    <input type="text" required placeholder="Ej. Juan Pérez" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Número de Tarjeta</label>
                    <input type="text" required placeholder="XXXX XXXX XXXX XXXX" maxlength="19" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-emerald-500 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Expiración</label>
                        <input type="text" required placeholder="MM/AA" maxlength="5" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-emerald-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">CVV</label>
                        <input type="password" required placeholder="123" maxlength="4" class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-emerald-500 text-sm">
                    </div>
                </div>
                <button type="submit" name="procesar_pago" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-lg transition uppercase tracking-wide cursor-pointer shadow-lg shadow-emerald-600/20 mt-4">
                    Pagar $450.00 MXN
                </button>
            </form>
        </div>
    </div>

    <!-- Modal: Facturación -->
    <div id="modal-factura" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 p-4">
        <div class="bg-dark-800 p-8 rounded-2xl w-full max-w-lg border border-dark-700 shadow-2xl relative">
            <button onclick="cerrarModalFactura()" class="absolute top-4 right-4 text-gray-400 hover:text-white text-xl cursor-pointer"><i class="fas fa-times"></i></button>
            <div class="flex items-center gap-3 mb-4">
                <i class="fas fa-file-invoice text-3xl text-blue-500"></i>
                <h3 class="font-heading text-2xl font-bold text-white uppercase">Comprobante de Facturación</h3>
            </div>
            <p class="text-gray-400 text-sm mb-6">Detalle de tu comprobante fiscal digital simulado por tu membresía.</p>
            
            <div class="bg-dark-900 p-6 rounded-xl border border-dark-700 space-y-4 mb-6 text-sm">
                <div class="flex justify-between border-b border-dark-700 pb-2">
                    <span class="text-gray-400">Emisor:</span>
                    <span class="font-bold text-white">GYMICO Central S.A. de C.V.</span>
                </div>
                <div class="flex justify-between border-b border-dark-700 pb-2">
                    <span class="text-gray-400">RFC Emisor:</span>
                    <span class="font-mono text-white">GYM261023XYZ</span>
                </div>
                <div class="flex justify-between border-b border-dark-700 pb-2">
                    <span class="text-gray-400">Receptor:</span>
                    <span class="font-bold text-white"><?php echo htmlspecialchars($user['nombre_completo']); ?></span>
                </div>
                <div class="flex justify-between border-b border-dark-700 pb-2">
                    <span class="text-gray-400">Concepto:</span>
                    <span class="text-white">Membresía <?php echo htmlspecialchars($tipo_membresia); ?> (30 Días)</span>
                </div>
                <div class="flex justify-between text-base font-bold pt-2">
                    <span class="text-white">Total Pagado:</span>
                    <span class="text-emerald-400">$450.00 MXN</span>
                </div>
            </div>

            <button onclick="alert('¡Factura descargada en formato PDF con éxito!'); cerrarModalFactura();" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition uppercase tracking-wide cursor-pointer shadow-lg shadow-blue-600/20 flex items-center justify-center gap-2">
                <i class="fas fa-download"></i> Descargar Factura (PDF)
            </button>
        </div>
    </div>

    <!-- Modal: Renovación de Membresía -->
    <div id="modal-renovacion" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 p-4">
        <div class="bg-dark-800 p-8 rounded-2xl w-full max-w-md border border-dark-700 text-center shadow-2xl relative">
            <button onclick="cerrarModalRenovacion()" class="absolute top-4 right-4 text-gray-400 hover:text-white text-xl cursor-pointer"><i class="fas fa-times"></i></button>
            <div class="w-16 h-16 bg-brand/20 text-brand rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-user-tie"></i>
            </div>
            <h3 class="font-heading text-2xl font-bold text-white uppercase mb-2">Información de Membresía</h3>
            <p class="text-gray-300 text-sm mb-6">Tu membresía se encuentra en regla. Puedes realizar pagos en línea cuando gustes o acudir a recepción.</p>
            <button onclick="cerrarModalRenovacion()" class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-3 rounded-lg transition uppercase tracking-wide cursor-pointer">
                Entendido
            </button>
        </div>
    </div>

    <!-- Modal: Reportar Fallas -->
    <div id="modal-reporte" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 p-4">
        <div class="bg-dark-800 p-8 rounded-2xl w-full max-w-md border border-dark-700 shadow-2xl relative">
            <button onclick="cerrarModalReporte()" class="absolute top-4 right-4 text-gray-400 hover:text-white text-xl cursor-pointer">
                <i class="fas fa-times"></i>
            </button>
            <h3 class="font-heading text-2xl font-bold text-white uppercase mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>Enviar Reporte
            </h3>
            <p class="text-gray-400 text-sm mb-6">¿Notaste algún problema? Ayúdanos a mejorar GYMICO reportándolo de inmediato.</p>
            
            <form action="panel.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Tipo de Problema</label>
                    <select name="tipo_reporte" required class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-brand text-sm">
                        <option value="Daño en Maquinaria">Daño en Maquinaria</option>
                        <option value="Problema en Instalacion">Problema en Instalación (Baños, Regaderas, etc.)</option>
                        <option value="Problema en Pagina Web">Falla en la Página Web</option>
                        <option value="Otro Asunto">Sugerencia / Otro Asunto</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Descripción del problema</label>
                    <textarea name="descripcion_reporte" required rows="4" placeholder="Describe detalladamente lo que encontraste..." class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-brand text-sm resize-none"></textarea>
                </div>
                <button type="submit" name="enviar_reporte" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-3 rounded-lg uppercase tracking-wide transition text-sm shadow-lg shadow-yellow-600/20 cursor-pointer">
                    Enviar al Administrador
                </button>
            </form>
        </div>
    </div>

    <script>
        function abrirModalPago() { document.getElementById('modal-pago').classList.replace('hidden', 'flex'); }
        function cerrarModalPago() { document.getElementById('modal-pago').classList.replace('flex', 'hidden'); }
        function abrirModalFactura() { document.getElementById('modal-factura').classList.replace('hidden', 'flex'); }
        function cerrarModalFactura() { document.getElementById('modal-factura').classList.replace('flex', 'hidden'); }
        function abrirModalRenovacion() { document.getElementById('modal-renovacion').classList.replace('hidden', 'flex'); }
        function cerrarModalRenovacion() { document.getElementById('modal-renovacion').classList.replace('flex', 'hidden'); }
        function abrirModalReporte() { document.getElementById('modal-reporte').classList.replace('hidden', 'flex'); }
        function cerrarModalReporte() { document.getElementById('modal-reporte').classList.replace('flex', 'hidden'); }
    </script>
</body>
</html>