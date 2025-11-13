<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Mensaje;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MensajeController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        
        // Obtener conversaciones del usuario
        $conversaciones = Mensaje::where('remitente_id', $usuario->id)
            ->orWhere('destinatario_id', $usuario->id)
            ->select('remitente_id', 'destinatario_id', DB::raw('MAX(created_at) as ultimo_mensaje'))
            ->groupBy(DB::raw('CASE WHEN remitente_id = ' . $usuario->id . ' THEN destinatario_id ELSE remitente_id END'))
            ->orderBy('ultimo_mensaje', 'desc')
            ->get();

        $conversacionesConUsuarios = [];
        foreach ($conversaciones as $conversacion) {
            $otroUsuarioId = $conversacion->remitente_id == $usuario->id ? 
                $conversacion->destinatario_id : $conversacion->remitente_id;
            
            $otroUsuario = Usuario::find($otroUsuarioId);
            if ($otroUsuario) {
                $ultimoMensaje = Mensaje::where(function($query) use ($usuario, $otroUsuarioId) {
                    $query->where('remitente_id', $usuario->id)
                          ->where('destinatario_id', $otroUsuarioId)
                          ->orWhere('remitente_id', $otroUsuarioId)
                          ->where('destinatario_id', $usuario->id);
                })->orderBy('created_at', 'desc')->first();

                $conversacionesConUsuarios[] = [
                    'usuario' => $otroUsuario,
                    'ultimo_mensaje' => $ultimoMensaje,
                    'no_leidos' => Mensaje::where('remitente_id', $otroUsuarioId)
                        ->where('destinatario_id', $usuario->id)
                        ->where('leido', false)
                        ->count()
                ];
            }
        }

        return view('user.mensajes.inbox', compact('conversacionesConUsuarios'));
    }

    public function show($usuarioId)
    {
        $usuario = Auth::user();
        $otroUsuario = Usuario::findOrFail($usuarioId);

        // Marcar mensajes como leídos
        Mensaje::where('remitente_id', $usuarioId)
            ->where('destinatario_id', $usuario->id)
            ->where('leido', false)
            ->update(['leido' => true]);

        // Obtener mensajes de la conversación
        $mensajes = Mensaje::where(function($query) use ($usuario, $usuarioId) {
            $query->where('remitente_id', $usuario->id)
                  ->where('destinatario_id', $usuarioId)
                  ->orWhere('remitente_id', $usuarioId)
                  ->where('destinatario_id', $usuario->id);
        })
        ->orderBy('created_at', 'asc')
        ->paginate(50);

        return view('user.mensajes.leer', compact('mensajes', 'otroUsuario'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'destinatario_id' => 'required|exists:usuarios,id',
            'contenido' => 'required|string|max:1000',
            'tipo' => 'nullable|in:texto,imagen,archivo'
        ]);

        $mensaje = Mensaje::create([
            'remitente_id' => Auth::id(),
            'destinatario_id' => $request->destinatario_id,
            'contenido' => $request->contenido,
            'tipo' => $request->tipo ?? 'texto',
            'leido' => false
        ]);

        // Crear notificación para el destinatario
        $destinatario = Usuario::find($request->destinatario_id);
        \App\Http\Controllers\NotificacionController::crearNotificacionMensaje(
            $request->destinatario_id,
            Auth::user(),
            $mensaje
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'mensaje' => $mensaje->load('remitente'),
                'message' => 'Mensaje enviado correctamente'
            ]);
        }

        return redirect()->back()->with('success', 'Mensaje enviado correctamente');
    }

    public function destroy(Mensaje $mensaje)
    {
        // Verificar que el usuario puede eliminar el mensaje
        if ($mensaje->remitente_id !== Auth::id() && $mensaje->destinatario_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $mensaje->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mensaje eliminado correctamente'
        ]);
    }

    public function marcarComoLeido(Mensaje $mensaje)
    {
        // Verificar que el mensaje es para el usuario autenticado
        if ($mensaje->destinatario_id !== Auth::id()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

            $mensaje->update(['leido' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Mensaje marcado como leído'
        ]);
    }

    public function marcarConversacionComoLeida($usuarioId)
    {
        Mensaje::where('remitente_id', $usuarioId)
            ->where('destinatario_id', Auth::id())
            ->where('leido', false)
            ->update(['leido' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Conversación marcada como leída'
        ]);
    }

    public function buscarUsuarios(Request $request)
    {
        $query = $request->get('q');
        
        $usuarios = Usuario::where('tipo', 'cliente')
            ->where('id', '!=', Auth::id())
            ->where(function($q) use ($query) {
                $q->where('nombre', 'LIKE', "%{$query}%")
                  ->orWhere('apellido', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'nombre', 'apellido', 'email', 'avatar']);

        return response()->json($usuarios);
    }

    public function obtenerMensajesNoLeidos()
    {
        $mensajesNoLeidos = Mensaje::where('destinatario_id', Auth::id())
            ->where('leido', false)
            ->count();

        return response()->json([
            'count' => $mensajesNoLeidos
        ]);
    }

    public function obtenerUltimosMensajes()
    {
        $mensajes = Mensaje::where('destinatario_id', Auth::id())
            ->where('leido', false)
            ->with('remitente')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'mensajes' => $mensajes
        ]);
    }

    // Métodos para administradores
    public function adminIndex()
    {
        $mensajes = Mensaje::with(['remitente', 'destinatario'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        $estadisticas = [
            'total' => Mensaje::count(),
            'no_leidos' => Mensaje::where('leido', false)->count(),
            'hoy' => Mensaje::whereDate('created_at', today())->count(),
            'esta_semana' => Mensaje::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count()
        ];

        return view('admin.mensajes.index', compact('mensajes', 'estadisticas'));
    }

    public function adminShow($id)
    {
        $mensaje = Mensaje::with(['remitente', 'destinatario'])->findOrFail($id);
        
        return view('admin.mensajes.show', compact('mensaje'));
    }

    public function adminDestroy(Mensaje $mensaje)
    {
        $mensaje->delete();

        return redirect()->route('admin.mensajes.index')
            ->with('success', 'Mensaje eliminado correctamente');
    }

    public function adminEnviarMensajeMasivo(Request $request)
    {
        $request->validate([
            'destinatarios' => 'required|array',
            'destinatarios.*' => 'exists:usuarios,id',
            'contenido' => 'required|string|max:1000',
            'tipo' => 'nullable|in:texto,imagen,archivo'
        ]);

        $mensajes = [];
        foreach ($request->destinatarios as $destinatarioId) {
            $mensajes[] = [
                'remitente_id' => Auth::id(),
                'destinatario_id' => $destinatarioId,
                'contenido' => $request->contenido,
                'tipo' => $request->tipo ?? 'texto',
                'leido' => false,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        Mensaje::insert($mensajes);

        return redirect()->route('admin.mensajes.index')
            ->with('success', 'Mensajes enviados correctamente');
    }

    // Métodos estáticos para crear mensajes automáticos
    public static function crearMensajeBienvenida($usuarioId)
    {
        $admin = Usuario::where('tipo', 'admin')->first();
        
        if ($admin) {
            return Mensaje::create([
                'remitente_id' => $admin->id,
                'destinatario_id' => $usuarioId,
                'contenido' => '¡Bienvenido a Retrolector! Estamos emocionados de tenerte como parte de nuestra comunidad de lectores. Si tienes alguna pregunta o necesitas ayuda, no dudes en contactarnos.',
                'tipo' => 'texto',
                'leido' => false
            ]);
        }
    }

    public static function crearMensajePrestamoVencido($usuarioId, $libro, $diasVencido)
    {
        $admin = Usuario::where('tipo', 'admin')->first();
        
        if ($admin) {
            return Mensaje::create([
                'remitente_id' => $admin->id,
                'destinatario_id' => $usuarioId,
                'contenido' => "Hola, te recordamos que el libro '{$libro->titulo}' está vencido por {$diasVencido} días. Por favor, devuélvelo lo antes posible para evitar sanciones.",
                'tipo' => 'texto',
                'leido' => false
            ]);
        }
    }

    public static function crearMensajeReservaDisponible($usuarioId, $libro)
    {
        $admin = Usuario::where('tipo', 'admin')->first();
        
        if ($admin) {
            return Mensaje::create([
                'remitente_id' => $admin->id,
                'destinatario_id' => $usuarioId,
                'contenido' => "¡Excelente noticia! El libro '{$libro->titulo}' que reservaste ya está disponible. Tienes 48 horas para recogerlo antes de que se cancele la reserva.",
                'tipo' => 'texto',
                'leido' => false
            ]);
        }
    }

    public static function crearMensajeMantenimiento($usuarioId, $mensaje, $fechaInicio = null, $fechaFin = null)
    {
        $admin = Usuario::where('tipo', 'admin')->first();
        
        if ($admin) {
            $contenido = "Mantenimiento programado: {$mensaje}";
            if ($fechaInicio && $fechaFin) {
                $contenido .= " Período: {$fechaInicio} a {$fechaFin}";
            }

            return Mensaje::create([
                'remitente_id' => $admin->id,
                'destinatario_id' => $usuarioId,
                'contenido' => $contenido,
                'tipo' => 'texto',
                'leido' => false
            ]);
        }
    }

    public static function crearMensajePromocion($usuarioId, $titulo, $mensaje, $codigoDescuento = null)
    {
        $admin = Usuario::where('tipo', 'admin')->first();
        
        if ($admin) {
            $contenido = "🎉 {$titulo}\n\n{$mensaje}";
            if ($codigoDescuento) {
                $contenido .= "\n\nCódigo de descuento: {$codigoDescuento}";
            }

            return Mensaje::create([
                'remitente_id' => $admin->id,
                'destinatario_id' => $usuarioId,
                'contenido' => $contenido,
                'tipo' => 'texto',
                'leido' => false
            ]);
        }
    }

    // Método para limpiar mensajes antiguos
    public static function limpiarMensajesAntiguos($dias = 90)
    {
        $fechaLimite = Carbon::now()->subDays($dias);
        
        return Mensaje::where('created_at', '<', $fechaLimite)
            ->where('leido', true)
            ->delete();
    }

    // Método para obtener estadísticas de mensajes
    public static function obtenerEstadisticas($usuarioId = null)
    {
        $query = Mensaje::query();
        
        if ($usuarioId) {
            $query->where(function($q) use ($usuarioId) {
                $q->where('remitente_id', $usuarioId)
                  ->orWhere('destinatario_id', $usuarioId);
            });
        }

        return [
            'total' => $query->count(),
            'no_leidos' => $query->where('leido', false)->count(),
            'por_tipo' => $query->selectRaw('tipo, COUNT(*) as total')
                ->groupBy('tipo')
                ->pluck('total', 'tipo')
                ->toArray(),
            'ultimas_24h' => $query->where('created_at', '>=', Carbon::now()->subDay())->count()
        ];
    }

    // Método para enviar mensaje masivo
    public static function enviarMensajeMasivo($contenido, $tipo = 'texto', $filtros = [])
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
        $admin = Usuario::where('tipo', 'admin')->first();
        
        if (!$admin) {
            return 0;
        }

        $mensajes = [];
        foreach ($usuarios as $usuario) {
            $mensajes[] = [
                'remitente_id' => $admin->id,
                'destinatario_id' => $usuario->id,
                'contenido' => $contenido,
                'tipo' => $tipo,
                'leido' => false,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Insertar en lotes para mejor rendimiento
        Mensaje::insert($mensajes);
        
        return count($mensajes);
    }
} 