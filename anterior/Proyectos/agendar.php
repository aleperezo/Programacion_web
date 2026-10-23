<?php
require 'conexion.php';

$mensaje_alerta = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agendar_cita'])) {
    $nombre = $conn->real_escape_string(trim($_POST['nombre']));
    $correo = $conn->real_escape_string(trim($_POST['correo']));
    $telefono = $conn->real_escape_string(trim($_POST['telefono']));
    $fecha = $conn->real_escape_string(trim($_POST['fecha']));
    $horario = $conn->real_escape_string(trim($_POST['horario']));

    // Fechas límite para la validación
    $fecha_actual = date('Y-m-d');
    $fecha_maxima = date('Y-m-d', strtotime('+1 year'));

    if (!empty($nombre) && !empty($correo) && !empty($telefono) && !empty($fecha) && !empty($horario)) {
        if ($fecha < $fecha_actual) {
            $mensaje_alerta = "Error: No puedes agendar una visita en una fecha pasada.";
        } elseif ($fecha > $fecha_maxima) {
            $mensaje_alerta = "Error: La fecha no puede ser mayor a un año a partir de hoy.";
        } else {
            $sql = "INSERT INTO visitas (nombre_visitante, correo_visitante, telefono, fecha_visita, horario) 
                    VALUES ('$nombre', '$correo', '$telefono', '$fecha', '$horario')";
            if ($conn->query($sql)) {
                $exito = true;
            } else {
                $mensaje_alerta = "Error al registrar la cita. Inténtalo de nuevo.";
            }
        }
    } else {
        $mensaje_alerta = "Por favor, completa todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda tu Visita | GYMICO</title>
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
</head>
<body class="bg-dark-900 text-gray-200 font-sans antialiased min-h-screen flex flex-col">

    <nav class="w-full bg-dark-900/95 backdrop-blur-sm border-b border-dark-800 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0">
                    <a href="index.html" class="font-heading text-3xl font-bold uppercase tracking-wider">
                        <span class="text-white">GYM</span><span class="text-brand">ICO</span>
                    </a>
                </div>
                <div>
                    <a href="index.html" class="text-gray-400 hover:text-white transition flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Volver al inicio
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-6xl w-full bg-dark-800 rounded-2xl shadow-2xl overflow-hidden flex flex-col lg:flex-row">
            
            <div class="w-full lg:w-1/2 p-8 sm:p-12">
                <div class="mb-8">
                    <h2 class="font-heading text-3xl md:text-4xl font-bold text-white uppercase mb-2">Ven a <span class="text-brand">conocernos</span></h2>
                    <p class="text-gray-400 text-sm">Déjanos tus datos y elige un horario válido. Te esperamos con gusto.</p>
                </div>

                <?php if (!empty($mensaje_alerta)): ?>
                    <div class="mb-6 p-4 bg-red-900/50 border border-brand rounded-lg text-white text-sm">
                        <?php echo $mensaje_alerta; ?>
                    </div>
                <?php endif; ?>

                <form action="agendar.php" method="POST" class="space-y-5 <?php echo isset($exito) ? 'hidden' : ''; ?>">
                    <div>
                        <label class="block text-sm font-medium text-gray-400 mb-1">Nombre Completo</label>
                        <input type="text" name="nombre" required class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-brand">
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Correo Electrónico</label>
                            <input type="email" name="correo" required class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-brand">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Teléfono</label>
                            <input type="tel" name="telefono" required class="w-full bg-dark-900 border border-dark-700 text-white px-4 py-3 rounded-lg focus:outline-none focus:border-brand">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Fecha de Visita</label>
                            <!-- Se agregaron los atributos min y max dinámicos -->
                            <input type="date" name="fecha" required 
                                   min="<?php echo date('Y-m-d'); ?>" 
                                   max="<?php echo date('Y-m-d', strtotime('+1 year')); ?>" 
                                   class="w-full bg-dark-900 border border-dark-700 text-gray-300 px-4 py-3 rounded-lg focus:outline-none focus:border-brand" style="color-scheme: dark;">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-400 mb-1">Horario Sugerido</label>
                            <select name="horario" required class="w-full bg-dark-900 border border-dark-700 text-gray-300 px-4 py-3 rounded-lg focus:outline-none focus:border-brand">
                                <option value="" disabled selected>Selecciona una hora</option>
                                <option value="Mañana (6:00 AM - 12:00 PM)">Mañana (6:00 AM - 12:00 PM)</option>
                                <option value="Tarde (12:00 PM - 6:00 PM)">Tarde (12:00 PM - 6:00 PM)</option>
                                <option value="Noche (6:00 PM - 10:00 PM)">Noche (6:00 PM - 10:00 PM)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" name="agendar_cita" class="w-full bg-brand hover:bg-brand-dark text-white font-bold py-4 rounded-lg uppercase tracking-wide transition transform hover:-translate-y-1 mt-4 shadow-lg shadow-brand/30 cursor-pointer">
                        Confirmar Visita
                    </button>
                </form>

                <div id="mensaje-exito" class="<?php echo isset($exito) ? 'block' : 'hidden'; ?> mt-6 p-6 bg-green-900/50 border border-green-500 rounded-lg text-center">
                    <i class="fas fa-check-circle text-green-500 text-4xl mb-3"></i>
                    <h4 class="text-white font-bold text-xl mb-1">¡Cita Agendada con Éxito!</h4>
                    <p class="text-gray-300 text-sm mb-4">Tus datos se guardaron en el sistema y ya aparecen en el panel del administrador.</p>
                    <a href="index.html" class="inline-block bg-dark-700 hover:bg-dark-600 text-white font-bold px-6 py-2 rounded-lg text-sm transition">Volver al Inicio</a>
                </div>
            </div>

            <div class="w-full lg:w-1/2 bg-dark-900 relative">
                <img src="https://images.unsplash.com/photo-1524661135-423995f22d0b?q=80&w=2074&auto=format&fit=crop" class="absolute inset-0 w-full h-full object-cover opacity-40 grayscale contrast-125">
                <div class="absolute inset-0 bg-gradient-to-t from-dark-900 via-transparent to-transparent"></div>
                <div class="absolute bottom-0 left-0 w-full p-8 sm:p-12">
                    <div class="bg-dark-800/90 backdrop-blur-md p-6 rounded-xl border border-dark-700 shadow-xl">
                        <h3 class="font-heading text-xl font-bold text-white uppercase mb-4 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-brand"></i> Nuestra Ubicación
                        </h3>
                        <p class="text-gray-300 text-sm mb-4 leading-relaxed">
                            <strong>GYMICO Central</strong><br>Av. Central 123, Colonia Centro<br>Estado de México
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </main>

</body>
</html>