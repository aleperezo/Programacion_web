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
$tipo_membresia = trim($user['tipo_membresia']); // "Basica", "Premium", o "VIP"

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
                        <a href="#apartar-salas" class="text-brand hover:text-red-400 transition text-sm font-bold"><i class="fas fa-door-open mr-1"></i> Renta de Salas</a>
                    <?php endif; ?>

                    <a href="index.html" class="border border-brand text-brand hover:bg-brand hover:text-white px-3 py-1.5 rounded-lg font-bold uppercase tracking-wide transition text-xs">
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

        <!-- 2. SECCIÓN MEMBRESÍA -->
        <section id="membresia">
            <div class="flex items-center gap-3 mb-6">
                <i class="fas fa-id-card text-3xl text-brand"></i>
                <h2 class="font-heading text-3xl font-bold text-white uppercase">Membresía</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-dark-800 p-6 rounded-xl border border-dark-700 md:col-span-2 flex flex-col justify-center">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span class="text-xs text-brand font-bold uppercase tracking-wider bg-brand/10 px-2 py-1 rounded">Plan Actual</span>
                            <h3 class="text-2xl font-bold text-white mt-2">Membresía <?php echo htmlspecialchars($tipo_membresia); ?></h3>
                        </div>
                        <span class="bg-green-500/20 text-green-500 px-3 py-1 rounded-full text-sm font-bold border border-green-500/50"><?php echo htmlspecialchars($user['estado'] ?? 'Activo'); ?></span>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div class="bg-dark-900 p-4 rounded-lg">
                            <p class="text-gray-400 text-sm mb-1">Fecha de Registro</p>
                            <p class="text-lg font-bold text-white"><?php echo !empty($user['fecha_registro']) ? date("d M Y", strtotime($user['fecha_registro'])) : 'Reciente'; ?></p>
                        </div>
                        <div class="bg-dark-900 p-4 rounded-lg">
                            <p class="text-gray-400 text-sm mb-1">Fecha de Expiración</p>
                            <p class="text-lg font-bold text-white"><?php echo htmlspecialchars($user['fecha_vencimiento'] ?? 'No definida'); ?></p>
                        </div>
                    </div>
                </div>

                <?php if ($dias_restantes > 30): ?>
                    <div class="bg-emerald-900/20 p-6 rounded-xl border border-emerald-500/50 flex flex-col items-center justify-center text-center">
                        <i class="fas fa-dumbbell text-4xl text-emerald-500 mb-4 animate-bounce"></i>
                        <h4 class="font-bold text-white mb-2 text-lg">¡Disfruta tu entrenamiento!</h4>
                        <p class="text-sm text-gray-300 mb-4">Tu membresía está activa y al corriente. ¡Aprovecha al máximo cada sesión en GYMICO!</p>
                        <button onclick="abrirModalRenovacion()" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-bold text-sm transition shadow-lg shadow-emerald-600/20">Renovar ahora</button>
                    </div>
                <?php else: ?>
                    <div class="bg-red-900/20 p-6 rounded-xl border border-red-500/50 flex flex-col items-center justify-center text-center">
                        <i class="fas fa-exclamation-triangle text-4xl text-brand mb-4 animate-pulse"></i>
                        <h4 class="font-bold text-white mb-2 text-lg">¡Expiración Próxima!</h4>
                        <p class="text-sm text-gray-300 mb-4">Verifica tu fecha de corte. Acércate a recepción para renovar y no perder tu racha.</p>
                        <button onclick="abrirModalRenovacion()" class="bg-brand hover:bg-brand-dark text-white px-6 py-2 rounded-lg font-bold text-sm transition shadow-lg shadow-brand/20">Renovar ahora</button>
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
            
            <div class="bg-dark-800 rounded-xl border border-dark-700 overflow-hidden">
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

        <!-- 4. SECCIÓN VIP: APARTAR SALAS -->
        <?php if ($tipo_membresia === 'VIP'): ?>
            <section id="apartar-salas" class="bg-gradient-to-r from-dark-800 to-dark-900 border border-brand/50 rounded-2xl p-8 shadow-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <i class="fas fa-door-open text-3xl text-brand"></i>
                    <h2 class="font-heading text-3xl font-bold text-white uppercase">Apartado de Salas Exclusivas</h2>
                </div>
                <p class="text-gray-300 text-sm mb-6">Como socio <strong>VIP</strong>, tienes derecho a reservar nuestras salas multiusos para entrenamientos privados, clases o sesiones especiales.</p>
                
                <form onsubmit="event.preventDefault(); alert('¡Sala apartada con éxito! Nos comunicaremos contigo para confirmar el horario.');" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Seleccionar Sala</label>
                        <select class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-2.5 rounded-lg outline-none focus:border-brand text-sm">
                            <option>Sala Multiusos A (Espejos y Audio)</option>
                            <option>Sala B (Piso Amortiguado)</option>
                            <option>Estudio Privado de Cardio</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-400 mb-1">Fecha de Renta</label>
                        <input type="date" required class="w-full bg-dark-900 border border-dark-700 text-gray-300 px-4 py-2.5 rounded-lg text-sm" style="color-scheme: dark;">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-2.5 rounded-lg uppercase tracking-wide transition text-sm shadow-lg shadow-brand/20 cursor-pointer">
                            Confirmar Apartado
                        </button>
                    </div>
                </form>
            </section>
        <?php endif; ?>

        <!-- 5. SECCIONES EXTENSAS DE RUTINA Y DIETA (Solo para Premium y VIP) -->
        <?php if ($tipo_membresia === 'Premium' || $tipo_membresia === 'VIP'): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                
                <!-- RUTINA EXTENSA -->
                <section id="rutina">
                    <div class="flex items-center gap-3 mb-6">
                        <i class="fas fa-dumbbell text-3xl text-brand"></i>
                        <h2 class="font-heading text-3xl font-bold text-white uppercase">Rutina Profesional</h2>
                    </div>
                    <div class="bg-dark-800 rounded-xl border border-dark-700 p-6 space-y-6">
                        <div class="border-b border-dark-700 pb-4">
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-xl font-bold text-white">Día 1: Tren Superior (Pecho y Tríceps)</h3>
                                <span class="bg-brand text-white text-xs px-2.5 py-1 rounded font-bold uppercase">Hipertrofia</span>
                            </div>
                            <ul class="space-y-2 text-sm text-gray-300">
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded"><span>Press de Banca Plano con Barra</span> <strong class="text-brand">4 Series x 10-12 reps</strong></li>
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded"><span>Press Inclinado con Mancuernas</span> <strong class="text-brand">4 Series x 12 reps</strong></li>
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded"><span>Aperturas en Contractora (Pec Deck)</span> <strong class="text-brand">3 Series x 15 reps</strong></li>
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded"><span>Press Francés con Barra EZ</span> <strong class="text-brand">4 Series x 10 reps</strong></li>
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded"><span>Extensiones de Tríceps en Polea</span> <strong class="text-brand">3 Series x 15 reps</strong></li>
                            </ul>
                        </div>

                        <div>
                            <div class="flex justify-between items-center mb-3">
                                <h3 class="text-xl font-bold text-white">Día 2: Tren Inferior (Pierna Completa)</h3>
                                <span class="bg-brand text-white text-xs px-2.5 py-1 rounded font-bold uppercase">Fuerza</span>
                            </div>
                            <ul class="space-y-2 text-sm text-gray-300">
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded"><span>Sentadilla Libre con Barra</span> <strong class="text-brand">4 Series x 8-10 reps</strong></li>
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded"><span>Prensa Inclinada de 45°</span> <strong class="text-brand">4 Series x 12 reps</strong></li>
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded"><span>Extensiones de Cuádriceps Sentado</span> <strong class="text-brand">3 Series x 15 reps</strong></li>
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded"><span>Curl Femoral Acostado</span> <strong class="text-brand">4 Series x 12 reps</strong></li>
                                <li class="flex justify-between bg-dark-900 p-2.5 rounded"><span>Elevación de Talones (Pantorrilla)</span> <strong class="text-brand">4 Series x 20 reps</strong></li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- DIETA EXTENSA (Clasificada por tiempos sin horarios) -->
                <section id="dieta">
                    <div class="flex items-center gap-3 mb-6">
                        <i class="fas fa-apple-alt text-3xl text-brand"></i>
                        <h2 class="font-heading text-3xl font-bold text-white uppercase">Plan Nutricional</h2>
                    </div>
                    <div class="bg-dark-800 rounded-xl border border-dark-700 p-6 flex flex-col justify-between space-y-4">
                        <div class="flex justify-between items-center mb-2 border-b border-dark-700 pb-3">
                            <h3 class="text-xl font-bold text-white">Dieta de Volumen Limpio</h3>
                            <span class="text-brand font-bold bg-brand/10 px-3 py-1 rounded-lg">2,800 kcal</span>
                        </div>
                        
                        <div class="space-y-3 text-sm text-gray-300">
                            <div class="bg-dark-900 p-3 rounded-lg border-l-4 border-brand">
                                <p class="text-brand font-bold uppercase text-xs mb-1"><i class="fas fa-sun mr-1"></i> Desayuno</p>
                                <p class="text-white">100g de avena en hojuelas con leche baja en grasa, 4 huevos enteros revueltos, 1 plátano y un puñado de frutos rojos.</p>
                            </div>

                            <div class="bg-dark-900 p-3 rounded-lg border-l-4 border-brand">
                                <p class="text-brand font-bold uppercase text-xs mb-1"><i class="fas fa-cookie-bite mr-1"></i> Snack AM</p>
                                <p class="text-white">1 taza de yogur griego sin azúcar natural y un puñado de almendras o nueces mixtas (30g).</p>
                            </div>

                            <div class="bg-dark-900 p-3 rounded-lg border-l-4 border-brand">
                                <p class="text-brand font-bold uppercase text-xs mb-1"><i class="fas fa-utensils mr-1"></i> Comida</p>
                                <p class="text-white">200g de pechuga de pollo a la plancha, 150g de arroz blanco o integral, y porción abundante de brócoli al vapor con aceite de oliva.</p>
                            </div>

                            <div class="bg-dark-900 p-3 rounded-lg border-l-4 border-brand">
                                <p class="text-brand font-bold uppercase text-xs mb-1"><i class="fas fa-bolt mr-1"></i> Snack / Post-Entreno</p>
                                <p class="text-white">1 scoop de proteína Whey en agua, acompañado de 2 rebanadas de pan integral tostado con mantequilla de maní natural.</p>
                            </div>

                            <div class="bg-dark-900 p-3 rounded-lg border-l-4 border-brand">
                                <p class="text-brand font-bold uppercase text-xs mb-1"><i class="fas fa-moon mr-1"></i> Cena</p>
                                <p class="text-white">150g de salmón o filete de atún asado, ensalada verde mixta con espinacas, aguacate y aderezo de limón.</p>
                            </div>

                            <div class="bg-dark-900 p-3 rounded-lg border-l-4 border-brand">
                                <p class="text-brand font-bold uppercase text-xs mb-1"><i class="fas fa-bed mr-1"></i> Antes de Dormir</p>
                                <p class="text-white">1 taza de té verde o infusión de manzanilla sin azúcar y opcional un vaso de caseína.</p>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        <?php endif; ?>

    </main>

    <!-- Modal de Renovación -->
    <div id="modal-renovacion" class="fixed inset-0 bg-black/80 hidden items-center justify-center z-50 p-4">
        <div class="bg-dark-800 p-8 rounded-2xl w-full max-w-md border border-dark-700 text-center shadow-2xl">
            <div class="w-16 h-16 bg-brand/20 text-brand rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fas fa-user-tie"></i>
            </div>
            <h3 class="font-heading text-2xl font-bold text-white uppercase mb-2">Renovación de Membresía</h3>
            <p class="text-gray-300 text-sm mb-6">Para renovar o cambiar tu plan, por favor <strong>acércate directamente a la recepción</strong> del gimnasio con el personal encargado. ¡Te atenderemos con gusto!</p>
            <button onclick="cerrarModalRenovacion()" class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-3 rounded-lg transition uppercase tracking-wide cursor-pointer">
                Entendido
            </button>
        </div>
    </div>

    <script>
        function abrirModalRenovacion() {
            document.getElementById('modal-renovacion').classList.replace('hidden', 'flex');
        }
        function cerrarModalRenovacion() {
            document.getElementById('modal-renovacion').classList.replace('flex', 'hidden');
        }
    </script>
</body>
</html>