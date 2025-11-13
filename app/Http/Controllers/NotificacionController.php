<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class NotificacionController extends Controller
{
    public function index()
    {
        $notificaciones = Auth::user()->notificaciones()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('user.notifications', compact('notificaciones'));
    }

    public function marcarComoLeida(Notificacion $notificacion)
    {
        // Verificar que la notificación pertenece al usuario autenticado
        if ($notificacion->usuario_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $notificacion->update(['leida' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Notificación marcada como leída',
            'unread_count' => Auth::user()->notificaciones()->where('leida', false)->count()
        ]);
    }

    public function marcarTodasComoLeidas()
    {
        Auth::user()->notificaciones()
            ->where('leida', false)
            ->update(['leida' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Todas las notificaciones marcadas como leídas',
            'unread_count' => 0
        ]);
    }

    public function eliminar(Notificacion $notificacion)
    {
        // Verificar que la notificación pertenece al usuario autenticado
        if ($notificacion->usuario_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $notificacion->delete();

        return response()->json([
            'success' => true,
            'message' => 'Notificación eliminada',
            'unread_count' => Auth::user()->notificaciones()->where('leida', false)->count()
        ]);
    }

    public function obtenerNoLeidas()
    {
        $notificaciones = Auth::user()->notificaciones()
            ->where('leida', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'notificaciones' => $notificaciones,
            'count' => $notificaciones->count()
        ]);
    }

    // Métodos estáticos para crear notificaciones automáticas
    public static function crearNotificacionPrestamo($usuarioId, $libro, $fechaDevolucion)
    {
        return Notificacion::create([
            'usuario_id' => $usuarioId,
            'titulo' => 'Préstamo realizado exitosamente',
            'mensaje' => "Has prestado el libro '{$libro->titulo}'. Fecha de devolución: {$fechaDevolucion}",
            'tipo' => 'success',
            'leida' => false,
            'datos_adicionales' => json_encode([
                'libro_id' => $libro->id,
                'fecha_devolucion' => $fechaDevolucion,
                'accion' => 'ver_prestamo'
            ])
        ]);
    }

    public static function crearNotificacionPrestamoVencido($usuarioId, $libro, $diasVencido)
    {
        return Notificacion::create([
            'usuario_id' => $usuarioId,
            'titulo' => 'Préstamo vencido',
            'mensaje' => "El libro '{$libro->titulo}' está vencido por {$diasVencido} días. Por favor, devuélvelo lo antes posible.",
            'tipo' => 'warning',
            'leida' => false,
            'datos_adicionales' => json_encode([
                'libro_id' => $libro->id,
                'dias_vencido' => $diasVencido,
                'accion' => 'renovar_prestamo'
            ])
        ]);
    }

    public static function crearNotificacionReservaDisponible($usuarioId, $libro)
    {
        return Notificacion::create([
            'usuario_id' => $usuarioId,
            'titulo' => 'Libro disponible',
            'mensaje' => "El libro '{$libro->titulo}' que reservaste ya está disponible. Tienes 48 horas para recogerlo.",
            'tipo' => 'info',
            'leida' => false,
            'datos_adicionales' => json_encode([
                'libro_id' => $libro->id,
                'accion' => 'ver_libro'
            ])
        ]);
    }

    public static function crearNotificacionResenaAprobada($usuarioId, $libro)
    {
        return Notificacion::create([
            'usuario_id' => $usuarioId,
            'titulo' => 'Reseña aprobada',
            'mensaje' => "Tu reseña para '{$libro->titulo}' ha sido aprobada y publicada.",
            'tipo' => 'success',
            'leida' => false,
            'datos_adicionales' => json_encode([
                'libro_id' => $libro->id,
                'accion' => 'ver_libro'
            ])
        ]);
    }

    public static function crearNotificacionResenaRechazada($usuarioId, $libro, $motivo = null)
    {
        $mensaje = "Tu reseña para '{$libro->titulo}' ha sido rechazada.";
        if ($motivo) {
            $mensaje .= " Motivo: {$motivo}";
        }

        return Notificacion::create([
            'usuario_id' => $usuarioId,
            'titulo' => 'Reseña rechazada',
            'mensaje' => $mensaje,
            'tipo' => 'error',
            'leida' => false,
            'datos_adicionales' => json_encode([
                'libro_id' => $libro->id,
                'motivo' => $motivo,
                'accion' => 'editar_resena'
            ])
        ]);
    }

    public static function crearNotificacionNuevoLibro($usuarioId, $libro)
    {
        return Notificacion::create([
            'usuario_id' => $usuarioId,
            'titulo' => 'Nuevo libro disponible',
            'mensaje' => "Se ha agregado '{$libro->titulo}' a nuestro catálogo. ¡Échale un vistazo!",
            'tipo' => 'info',
            'leida' => false,
            'datos_adicionales' => json_encode([
                'libro_id' => $libro->id,
                'accion' => 'ver_libro'
            ])
        ]);
    }

    public static function crearNotificacionMantenimiento($usuarioId, $mensaje, $fechaInicio = null, $fechaFin = null)
    {
        $datosAdicionales = ['accion' => 'ver_mantenimiento'];
        
        if ($fechaInicio && $fechaFin) {
            $datosAdicionales['fecha_inicio'] = $fechaInicio;
            $datosAdicionales['fecha_fin'] = $fechaFin;
        }

        return Notificacion::create([
            'usuario_id' => $usuarioId,
            'titulo' => 'Mantenimiento programado',
            'mensaje' => $mensaje,
            'tipo' => 'warning',
            'leida' => false,
            'datos_adicionales' => json_encode($datosAdicionales)
        ]);
    }

    public static function crearNotificacionPromocion($usuarioId, $titulo, $mensaje, $codigoDescuento = null)
    {
        $datosAdicionales = ['accion' => 'ver_promocion'];
        
        if ($codigoDescuento) {
            $datosAdicionales['codigo_descuento'] = $codigoDescuento;
        }

        return Notificacion::create([
            'usuario_id' => $usuarioId,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'tipo' => 'promocion',
            'leida' => false,
            'datos_adicionales' => json_encode($datosAdicionales)
        ]);
    }

    // Método para limpiar notificaciones antiguas
    public static function limpiarNotificacionesAntiguas($dias = 30)
    {
        $fechaLimite = Carbon::now()->subDays($dias);
        
        return Notificacion::where('created_at', '<', $fechaLimite)
            ->where('leida', true)
            ->delete();
    }

    // Método para obtener estadísticas de notificaciones
    public static function obtenerEstadisticas($usuarioId = null)
    {
        $query = Notificacion::query();
        
        if ($usuarioId) {
            $query->where('usuario_id', $usuarioId);
        }

        return [
            'total' => $query->count(),
            'no_leidas' => $query->where('leida', false)->count(),
            'por_tipo' => $query->selectRaw('tipo, COUNT(*) as total')
                ->groupBy('tipo')
                ->pluck('total', 'tipo')
                ->toArray(),
            'ultimas_24h' => $query->where('created_at', '>=', Carbon::now()->subDay())->count()
        ];
    }

    // Método para enviar notificación masiva
    public static function enviarNotificacionMasiva($titulo, $mensaje, $tipo = 'info', $filtros = [])
    {
        $usuarios = Usuario::where('tipo', 'cliente');
        
        // Aplicar filtros
        if (!empty($filtros['estado'])) {
            $usuarios->where('estado', $filtros['estado']);
        }
        
        if (!empty($filtros['ultima_actividad'])) {
            $usuarios->where('last_login_at', '>=', Carbon::now()->subDays($filtros['ultima_actividad']));
        }

        $usuarios = $usuarios->get();
        
        $notificaciones = [];
        foreach ($usuarios as $usuario) {
            $notificaciones[] = [
                'usuario_id' => $usuario->id,
                'titulo' => $titulo,
                'mensaje' => $mensaje,
                'tipo' => $tipo,
                'leida' => false,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Insertar en lotes para mejor rendimiento
        Notificacion::insert($notificaciones);
        
        return count($notificaciones);
    }
} 