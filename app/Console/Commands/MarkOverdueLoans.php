<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Prestamo;
use App\Models\Notificacion;
use App\Models\Mensaje;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MarkOverdueLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:process-overdue {--days=1 : Días de gracia antes de marcar como vencido}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procesa préstamos vencidos y envía notificaciones';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Procesando préstamos vencidos...');
        
        $diasGracia = $this->option('days');
        $fechaLimite = Carbon::now()->subDays($diasGracia);
        
        // Obtener préstamos vencidos
        $prestamosVencidos = Prestamo::where('fecha_devolucion', '<', $fechaLimite)
            ->where('estado', 'activo')
            ->with(['usuario', 'libro'])
            ->get();
        
        $this->info("📚 Encontrados {$prestamosVencidos->count()} préstamos vencidos");
        
        $bar = $this->output->createProgressBar($prestamosVencidos->count());
        $bar->start();
        
        $notificacionesCreadas = 0;
        $mensajesCreados = 0;
        $prestamosActualizados = 0;
        
        foreach ($prestamosVencidos as $prestamo) {
            try {
                DB::beginTransaction();
                
                // Calcular días vencido
                $diasVencido = Carbon::now()->diffInDays($prestamo->fecha_devolucion);
                
                // Actualizar estado del préstamo
                $prestamo->update([
                    'estado' => 'vencido',
                    'dias_vencido' => $diasVencido
                ]);
                $prestamosActualizados++;
                
                // Crear notificación
                $notificacion = Notificacion::create([
                    'usuario_id' => $prestamo->usuario_id,
                    'titulo' => 'Préstamo vencido',
                    'mensaje' => "El libro '{$prestamo->libro->titulo}' está vencido por {$diasVencido} días. Por favor, devuélvelo lo antes posible.",
                    'tipo' => 'warning',
                    'leida' => false,
                    'datos_adicionales' => json_encode([
                        'libro_id' => $prestamo->libro_id,
                        'prestamo_id' => $prestamo->id,
                        'dias_vencido' => $diasVencido,
                        'accion' => 'renovar_prestamo'
                    ])
                ]);
                $notificacionesCreadas++;
                
                // Crear mensaje automático si es la primera vez que se vence
                if ($diasVencido <= 7) {
                    $admin = Usuario::where('tipo', 'admin')->first();
                    if ($admin) {
                        Mensaje::create([
                            'remitente_id' => $admin->id,
                            'destinatario_id' => $prestamo->usuario_id,
                            'contenido' => "Hola {$prestamo->usuario->nombre}, te recordamos que el libro '{$prestamo->libro->titulo}' está vencido por {$diasVencido} días. Por favor, devuélvelo lo antes posible para evitar sanciones.",
                            'tipo' => 'texto',
                            'leida' => false
                        ]);
                        $mensajesCreados++;
                    }
                }
                
                DB::commit();
                
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("Error procesando préstamo ID {$prestamo->id}: " . $e->getMessage());
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        
        // Limpiar notificaciones antiguas
        $this->info('🧹 Limpiando notificaciones antiguas...');
        $notificacionesEliminadas = Notificacion::where('created_at', '<', Carbon::now()->subDays(30))
            ->where('leida', true)
            ->delete();
        
        // Limpiar mensajes antiguos
        $this->info('🧹 Limpiando mensajes antiguos...');
        $mensajesEliminados = Mensaje::where('created_at', '<', Carbon::now()->subDays(90))
            ->where('leida', true)
            ->delete();
        
        // Generar reporte
        $this->newLine();
        $this->info('📊 Reporte de procesamiento:');
        $this->table(
            ['Métrica', 'Cantidad'],
            [
                ['Préstamos procesados', $prestamosActualizados],
                ['Notificaciones creadas', $notificacionesCreadas],
                ['Mensajes automáticos', $mensajesCreados],
                ['Notificaciones eliminadas', $notificacionesEliminadas],
                ['Mensajes eliminados', $mensajesEliminados]
            ]
        );
        
        // Verificar reservas disponibles
        $this->info('🔍 Verificando reservas disponibles...');
        $reservasDisponibles = $this->verificarReservasDisponibles();
        
        if ($reservasDisponibles > 0) {
            $this->info("✅ {$reservasDisponibles} reservas marcadas como disponibles");
        }
        
        $this->info('✅ Procesamiento completado exitosamente');
        
        return 0;
    }
    
    /**
     * Verificar reservas que ahora están disponibles
     */
    private function verificarReservasDisponibles()
    {
        $reservasDisponibles = 0;
        
        // Obtener reservas pendientes
        $reservas = \App\Models\Reserva::where('estado', 'pendiente')
            ->with(['usuario', 'libro'])
            ->get();
        
        foreach ($reservas as $reserva) {
            // Verificar si el libro está disponible
            $libroDisponible = \App\Models\Libro::where('id', $reserva->libro_id)
                ->where('estado', 'disponible')
                ->exists();
            
            if ($libroDisponible) {
                // Marcar reserva como disponible
                $reserva->update(['estado' => 'disponible']);
                
                // Crear notificación
                Notificacion::create([
                    'usuario_id' => $reserva->usuario_id,
                    'titulo' => 'Libro disponible',
                    'mensaje' => "El libro '{$reserva->libro->titulo}' que reservaste ya está disponible. Tienes 48 horas para recogerlo.",
                    'tipo' => 'info',
                    'leida' => false,
                    'datos_adicionales' => json_encode([
                        'libro_id' => $reserva->libro_id,
                        'reserva_id' => $reserva->id,
                        'accion' => 'ver_libro'
                    ])
                ]);
                
                // Crear mensaje automático
                $admin = Usuario::where('tipo', 'admin')->first();
                if ($admin) {
                    Mensaje::create([
                        'remitente_id' => $admin->id,
                        'destinatario_id' => $reserva->usuario_id,
                        'contenido' => "¡Excelente noticia! El libro '{$reserva->libro->titulo}' que reservaste ya está disponible. Tienes 48 horas para recogerlo antes de que se cancele la reserva.",
                        'tipo' => 'texto',
                        'leida' => false
                    ]);
                }
                
                $reservasDisponibles++;
            }
        }
        
        return $reservasDisponibles;
    }
} 