<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Reserva;
use App\Models\Favorito;
use App\Models\Notificacion;
use App\Models\Mensaje;
use App\Models\Compra;
use App\Models\Resena;
use App\Models\Categoria;
use App\Models\Autor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Eliminar o comentar el middleware 'cliente' para permitir acceso a admins
        // $this->middleware('cliente');
    }

    /**
     * Dashboard principal del usuario
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Estadísticas del usuario
        $stats = [
            'total_prestamos' => Prestamo::where('usuario_id', $user->id)->count(),
            'prestamos_activos' => Prestamo::where('usuario_id', $user->id)
                ->where('estado', 'prestado')
                ->count(),
            'total_reservas' => Reserva::where('usuario_id', $user->id)->count(),
            'reservas_activas' => Reserva::where('usuario_id', $user->id)
                ->where('estado', 'pendiente')
                ->count(),
            'total_favoritos' => Favorito::where('usuario_id', $user->id)->count(),
            'total_compras' => Compra::where('usuario_id', $user->id)->count(),
            'libros_leidos' => Prestamo::where('usuario_id', $user->id)
                ->where('estado', 'devuelto')
                ->count(),
            'puntos_lectura' => $user->puntos_lectura ?? 0
        ];
        
        // Préstamos recientes
        $prestamos_recientes = Prestamo::with(['libro', 'libro.autor', 'libro.categoria'])
            ->where('usuario_id', $user->id)
            ->where('estado', 'prestado')
            ->orderBy('fecha_prestamo', 'desc')
            ->limit(5)
            ->get();

        // Reservas activas
        $reservas_activas = Reserva::with(['libro', 'libro.autor'])
            ->where('usuario_id', $user->id)
            ->where('estado', 'pendiente')
            ->orderBy('fecha_reserva', 'desc')
            ->limit(5)
            ->get();

        // Libros favoritos
        $favoritos = Favorito::with(['libro', 'libro.autor'])
            ->where('usuario_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Notificaciones no leídas
        $notificaciones = Notificacion::where('usuario_id', $user->id)
            ->where('leida', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Mensajes no leídos
        $mensajes_no_leidos = Mensaje::where('destinatario_id', $user->id)
            ->where('leido', false)
            ->count();
        
        // Recomendaciones basadas en historial
        $recomendaciones = $this->getRecomendaciones($user);
        
        // Actividad reciente
        $actividad_reciente = $this->getActividadReciente($user);

        // Compras recientes
        $compras_recientes = \App\Models\Compra::with(['libro', 'libro.autor'])
            ->where('usuario_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Si la petición es AJAX, devolver JSON para refresco en tiempo real
        if (request()->ajax()) {
            return response()->json([
                'favoritos' => $favoritos->count(),
                'favoritos_list' => $favoritos->map(function($fav) {
                    return [
                        'id' => $fav->libro->id,
                        'titulo' => $fav->libro->titulo
                    ];
                }),
                'prestamos_recientes' => $prestamos_recientes->count(),
                'prestamos_list' => $prestamos_recientes->map(function($pres) {
                    return [
                        'id' => $pres->libro->id,
                        'titulo' => $pres->libro->titulo,
                        'fecha_prestamo' => $pres->fecha_prestamo->format('d/m/Y'),
                        'estado' => $pres->estado,
                        'ubicacion' => $pres->libro->ubicacion,
                        'archivo_pdf' => $pres->libro->archivo_pdf ? true : false
                    ];
                }),
                'reservas_list' => $reservas_activas->map(function($res) {
                    return [
                        'id' => $res->libro->id,
                        'titulo' => $res->libro->titulo,
                        'fecha_reserva' => $res->fecha_reserva->format('d/m/Y')
                    ];
                }),
                'total_compras' => $stats['total_compras'],
                'notificaciones' => $notificaciones->count(),
                'notificaciones_list' => $notificaciones->map(function($notif) {
                    return [
                        'titulo' => $notif->titulo,
                        'tipo' => $notif->tipo
                    ];
                }),
                // Puedes agregar más datos aquí según lo necesite el frontend
            ]);
        }

        return view('user.dashboard', compact(
            'stats',
            'prestamos_recientes',
            'reservas_activas',
            'favoritos',
            'notificaciones',
            'mensajes_no_leidos',
            'recomendaciones',
            'actividad_reciente',
            'compras_recientes'
        ));
    }

    /**
     * Obtener recomendaciones personalizadas
     */
    private function getRecomendaciones($user)
    {
        // Obtener categorías favoritas del usuario
        $categorias_favoritas = Prestamo::join('libros', 'prestamos.libro_id', '=', 'libros.id')
            ->join('categorias', 'libros.categoria_id', '=', 'categorias.id')
            ->where('prestamos.usuario_id', $user->id)
            ->select('categorias.id', 'categorias.nombre', DB::raw('COUNT(*) as total'))
            ->groupBy('categorias.id', 'categorias.nombre')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->pluck('categorias.id');
        
        // Obtener autores favoritos
        $autores_favoritos = Prestamo::join('libros', 'prestamos.libro_id', '=', 'libros.id')
            ->join('autors', 'libros.autor_id', '=', 'autors.id')
            ->where('prestamos.usuario_id', $user->id)
            ->select('autors.id', 'autors.nombre', DB::raw('COUNT(*) as total'))
            ->groupBy('autors.id', 'autors.nombre')
            ->orderBy('total', 'desc')
            ->limit(3)
            ->pluck('autors.id');
        
        // Libros recomendados
        $recomendaciones = Libro::with(['autor', 'categoria'])
            ->where(function($query) use ($categorias_favoritas, $autores_favoritos) {
                $query->whereIn('categoria_id', $categorias_favoritas)
                      ->orWhereIn('autor_id', $autores_favoritos);
            })
            ->where('estado', 'disponible')
            ->whereNotIn('id', function($query) use ($user) {
                $query->select('libro_id')
                      ->from('prestamos')
                      ->where('usuario_id', $user->id);
            })
            ->limit(6)
            ->get();
        
        return $recomendaciones;
    }

    /**
     * Obtener actividad reciente del usuario
     */
    private function getActividadReciente($user)
    {
        $actividad = collect();
        
        // Préstamos recientes
        $prestamos = Prestamo::with(['libro'])
            ->where('usuario_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($prestamo) {
                return [
                    'tipo' => 'prestamo',
                    'fecha' => $prestamo->created_at,
                    'titulo' => $prestamo->libro->titulo,
                    'descripcion' => 'Prestaste "' . $prestamo->libro->titulo . '"',
                    'icono' => 'fas fa-book',
                    'color' => 'primary'
                ];
            });
        
        // Reservas recientes
        $reservas = Reserva::with(['libro'])
            ->where('usuario_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($reserva) {
                return [
                    'tipo' => 'reserva',
                    'fecha' => $reserva->created_at,
                    'titulo' => $reserva->libro->titulo,
                    'descripcion' => 'Reservaste "' . $reserva->libro->titulo . '"',
                    'icono' => 'fas fa-bookmark',
                    'color' => 'warning'
                ];
            });
        
        // Favoritos recientes
        $favoritos = Favorito::with(['libro'])
            ->where('usuario_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function($favorito) {
                return [
                    'tipo' => 'favorito',
                    'fecha' => $favorito->created_at,
                    'titulo' => $favorito->libro->titulo,
                    'descripcion' => 'Agregaste "' . $favorito->libro->titulo . '" a favoritos',
                    'icono' => 'fas fa-heart',
                    'color' => 'danger'
                ];
            });
        
        // Combinar y ordenar por fecha
        $actividad = $prestamos->concat($reservas)->concat($favoritos)
            ->sortByDesc('fecha')
            ->take(15);
        
        return $actividad;
    }

    /**
     * Vista de préstamos del usuario
     */
    public function loans(Request $request)
    {
        $user = Auth::user();
        $estado = $request->get('estado', 'todos');
        
        $query = Prestamo::with(['libro', 'libro.autor', 'libro.categoria'])
            ->where('usuario_id', $user->id);
        
        if ($estado !== 'todos') {
            $query->where('estado', $estado);
        }
        
        $prestamos = $query->orderBy('fecha_prestamo', 'desc')
            ->paginate(12);
        
        // Estadísticas de préstamos
        $stats = [
            'total' => Prestamo::where('usuario_id', $user->id)->count(),
            'activos' => Prestamo::where('usuario_id', $user->id)->where('estado', 'prestado')->count(),
            'devueltos' => Prestamo::where('usuario_id', $user->id)->where('estado', 'devuelto')->count(),
            'vencidos' => Prestamo::where('usuario_id', $user->id)->where('estado', 'vencido')->count()
        ];
        
        return view('user.loans', compact('prestamos', 'stats', 'estado'));
    }

    /**
     * Renovar préstamo
     */
    public function renewLoan($id)
    {
        $prestamo = Prestamo::where('usuario_id', Auth::id())
            ->where('id', $id)
            ->where('estado', 'prestado')
            ->firstOrFail();
        
        // Verificar si se puede renovar
        $dias_restantes = Carbon::parse($prestamo->fecha_devolucion)->diffInDays(now(), false);
        
        if ($dias_restantes > 2) {
            return back()->with('error', 'Solo puedes renovar préstamos que venzan en menos de 2 días.');
        }
        
        // Verificar si el libro está disponible para renovación
        $libro = $prestamo->libro;
        if ($libro->estado !== 'disponible') {
            return back()->with('error', 'El libro no está disponible para renovación.');
        }
        
        // Renovar por 15 días más
        $prestamo->fecha_devolucion = Carbon::parse($prestamo->fecha_devolucion)->addDays(15);
        $prestamo->renovaciones = $prestamo->renovaciones + 1;
        $prestamo->save();
        
        // Crear notificación
        Notificacion::create([
            'usuario_id' => Auth::id(),
            'titulo' => 'Préstamo Renovado',
            'mensaje' => "Tu préstamo de '{$libro->titulo}' ha sido renovado hasta " . $prestamo->fecha_devolucion->format('d/m/Y'),
            'tipo' => 'renovacion',
            'leida' => false
        ]);
        
        return back()->with('success', 'Préstamo renovado exitosamente.');
    }

    /**
     * Vista de reservas del usuario
     */
    public function reservations(Request $request)
    {
        $user = Auth::user();
        $estado = $request->get('estado', 'todos');
        
        $query = Reserva::with(['libro', 'libro.autor'])
            ->where('usuario_id', $user->id);
        
        if ($estado !== 'todos') {
            $query->where('estado', $estado);
        }
        
        $reservas = $query->orderBy('fecha_reserva', 'desc')
            ->paginate(12);
        
        // Estadísticas de reservas
        $stats = [
            'total' => Reserva::where('usuario_id', $user->id)->count(),
            'pendientes' => Reserva::where('usuario_id', $user->id)->where('estado', 'pendiente')->count(),
            'completadas' => Reserva::where('usuario_id', $user->id)->where('estado', 'completada')->count(),
            'canceladas' => Reserva::where('usuario_id', $user->id)->where('estado', 'cancelada')->count()
        ];
        
        return view('user.reservations', compact('reservas', 'stats', 'estado'));
    }

    /**
     * Cancelar reserva
     */
    public function cancelReservation($id)
    {
        $reserva = Reserva::where('usuario_id', Auth::id())
            ->where('id', $id)
            ->where('estado', 'pendiente')
            ->firstOrFail();
        
        $reserva->estado = 'cancelada';
        $reserva->fecha_cancelacion = now();
        $reserva->save();
        
        // Crear notificación
        Notificacion::create([
            'usuario_id' => Auth::id(),
            'titulo' => 'Reserva Cancelada',
            'mensaje' => "Tu reserva de '{$reserva->libro->titulo}' ha sido cancelada.",
            'tipo' => 'cancelacion',
            'leida' => false
        ]);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Reserva cancelada exitosamente.'
            ]);
        }
        return back()->with('success', 'Reserva cancelada exitosamente.');
    }

    /**
     * Vista de favoritos del usuario
     */
    public function favorites(Request $request)
    {
        $user = Auth::user();
        $categoria_id = $request->get('categoria');
        
        $query = Favorito::with(['libro', 'libro.autor', 'libro.categoria'])
            ->where('usuario_id', $user->id);
        
        if ($categoria_id) {
            $query->whereHas('libro', function($q) use ($categoria_id) {
                $q->where('categoria_id', $categoria_id);
            });
        }
        
        $favoritos = $query->orderBy('created_at', 'desc')
            ->paginate(12);
        
        // Categorías para filtro
        $categorias = Categoria::orderBy('nombre')->get();
        
        return view('user.favorites', compact('favoritos', 'categorias', 'categoria_id'));
    }

    /**
     * Agregar/quitar favorito
     */
    public function toggleFavorite($libro_id)
    {
        $user = Auth::user();
        $favorito = Favorito::where('usuario_id', $user->id)
            ->where('libro_id', $libro_id)
                           ->first();
        $libro = \App\Models\Libro::find($libro_id);

        if ($favorito) {
            $favorito->delete();
            $mensaje = 'Libro removido de favoritos.';
            $accion = 'removido';
            // Notificación de remoción
            \App\Models\Notificacion::create([
                'usuario_id' => $user->id,
                'titulo' => 'Libro removido de favoritos',
                'mensaje' => "Has removido '{$libro->titulo}' de tus favoritos.",
                'tipo' => 'favorito',
                'leida' => false
            ]);
        } else {
            Favorito::create([
                'usuario_id' => $user->id,
                'libro_id' => $libro_id
            ]);
            $mensaje = 'Libro agregado a favoritos.';
            $accion = 'agregado';
            // Notificación de agregado
            \App\Models\Notificacion::create([
                'usuario_id' => $user->id,
                'titulo' => 'Libro agregado a favoritos',
                'mensaje' => "Has agregado '{$libro->titulo}' a tus favoritos.",
                'tipo' => 'favorito',
                'leida' => false
            ]);
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'action' => $accion
            ]);
        }
        
        return back()->with('success', $mensaje);
    }

    /**
     * Vista de historial de préstamos
     */
    public function history(Request $request)
    {
        $user = Auth::user();
        $year = $request->get('year', now()->year);
        $month = $request->get('month');
        
        $query = Prestamo::with(['libro', 'libro.autor', 'libro.categoria'])
            ->where('usuario_id', $user->id)
            ->where('prestamos.estado', 'devuelto');
        
        if ($year) {
            $query->whereYear('fecha_prestamo', $year);
        }
        
        if ($month) {
            $query->whereMonth('fecha_prestamo', $month);
        }
        
        $historial = (clone $query)->orderBy('fecha_prestamo', 'desc')
            ->paginate(20);
        
        // Estadísticas del año
        $stats_anio = [
            'total_libros' => (clone $query)->count(),
            'categorias_leidas' => (clone $query)
                ->join('libros', 'prestamos.libro_id', '=', 'libros.id')
                ->join('categorias', 'libros.categoria_id', '=', 'categorias.id')
                ->where('prestamos.estado', 'devuelto')
                ->distinct('categorias.id')
                ->count('categorias.id'),
            'autores_leidos' => (clone $query)
                ->join('libros', 'prestamos.libro_id', '=', 'libros.id')
                ->join('autors', 'libros.autor_id', '=', 'autors.id')
                ->where('prestamos.estado', 'devuelto')
                ->distinct('autors.id')
                ->count('autors.id'),
            'promedio_lectura' => 0
        ];
        
        // Años disponibles
        $anios = Prestamo::where('usuario_id', $user->id)
            ->distinct()
            ->pluck(DB::raw('YEAR(fecha_prestamo) as year'))
            ->sort()
            ->reverse();
        
        return view('user.historial', compact('historial', 'stats_anio', 'anios', 'year', 'month'));
    }

    /**
     * Vista de compras del usuario
     */
    public function purchases()
    {
        $user = Auth::user();
        
        $compras = Compra::with(['libro', 'libro.autor'])
            ->where('usuario_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        // Estadísticas de compras
        $stats = [
            'total_compras' => $compras->total(),
            'total_gastado' => Compra::where('usuario_id', $user->id)->sum('precio'),
            'promedio_compra' => Compra::where('usuario_id', $user->id)->avg('precio') ?? 0
        ];
        
        return view('user.purchases', compact('compras', 'stats'));
    }

    /**
     * Vista de libros virtuales (compras)
     */
    public function virtualBooks()
    {
        $user = Auth::user();
        
        // Compras físicas: tipo = 'fisico', libro con stock > 0
        $compraFisica = Compra::with(['libro', 'libro.autor'])
            ->where('usuario_id', $user->id)
            ->where('tipo', 'fisico')
            ->whereHas('libro', function($q) {
                $q->where('stock', '>', 0);
            })
            ->get()
            ->pluck('libro');

        // Compras online: tipo = 'virtual', libro con PDF
        $compraOnline = Compra::with(['libro', 'libro.autor'])
            ->where('usuario_id', $user->id)
            ->where('tipo', 'virtual')
            ->whereHas('libro', function($q) {
                $q->whereNotNull('archivo_pdf');
            })
            ->get()
            ->pluck('libro');

        // Préstamos físicos: estado = 'prestado', libro con stock > 0
        $prestamoFisico = Prestamo::with(['libro', 'libro.autor'])
            ->where('usuario_id', $user->id)
            ->where('estado', 'prestado')
            ->whereHas('libro', function($q) {
                $q->where('stock', '>', 0);
            })
            ->get()
            ->pluck('libro');

        // Préstamos online: estado = 'prestado', libro con PDF
        $prestamoOnline = Prestamo::with(['libro', 'libro.autor'])
            ->where('usuario_id', $user->id)
            ->where('estado', 'prestado')
            ->whereHas('libro', function($q) {
                $q->whereNotNull('archivo_pdf');
            })
            ->get()
            ->pluck('libro');
        
        return view('user.virtual-books', compact('compraFisica', 'compraOnline', 'prestamoFisico', 'prestamoOnline'));
    }

    /**
     * Leer libro virtual
     */
    public function readVirtualBook($id)
    {
        $compra = Compra::with(['libro'])
            ->where('usuario_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        return view('books.read', compact('compra'));
    }

    /**
     * Mostrar historial de notificaciones del usuario
     */
    public function notifications()
    {
        $user = auth()->user();
        $notificaciones = $user->notificaciones()->orderBy('created_at', 'desc')->paginate(20);
        if (request()->ajax()) {
            // Devolver solo la lista para AJAX
            return view('user.partials.notifications-list', compact('notificaciones'))->render();
        }
        return view('user.notifications', compact('notificaciones'));
    }

    /**
     * Obtener notificaciones no leídas (AJAX)
     */
    public function getUnreadNotifications()
    {
        $user = auth()->user();
        $notificaciones = $user->notificaciones()
            ->where('leida', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'notificaciones' => $notificaciones,
            'count' => $user->notificaciones()->where('leida', false)->count()
        ]);
    }

    /**
     * Marcar notificación como leída
     */
    public function markNotificationRead($id)
    {
        $user = auth()->user();
        $notificacion = $user->notificaciones()->findOrFail($id);
        $notificacion->leida = true;
        $notificacion->save();
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Notificación marcada como leída.'
            ]);
        }
        return back()->with('success', 'Notificación marcada como leída.');
    }

    /**
     * Marcar todas las notificaciones como leídas
     */
    public function markAllNotificationsRead()
    {
        Notificacion::where('usuario_id', Auth::id())
            ->where('leida', false)
            ->update(['leida' => true]);
        
        return back()->with('success', 'Todas las notificaciones marcadas como leídas.');
    }

    /**
     * Vista de mensajes
     */
    public function messages()
    {
        $user = Auth::user();
        
        $conversaciones = Mensaje::select('remitente_id', 'destinatario_id', DB::raw('MAX(created_at) as ultimo_mensaje'))
            ->where(function($query) use ($user) {
                $query->where('remitente_id', $user->id)
                      ->orWhere('destinatario_id', $user->id);
            })
            ->groupBy('remitente_id', 'destinatario_id')
            ->orderBy('ultimo_mensaje', 'desc')
            ->get()
            ->map(function($conversacion) use ($user) {
                $otro_usuario_id = $conversacion->remitente_id == $user->id ? 
                    $conversacion->destinatario_id : $conversacion->remitente_id;
                
                $otro_usuario = \App\Models\User::find($otro_usuario_id);
                $ultimo_mensaje = Mensaje::where(function($query) use ($user, $otro_usuario_id) {
                    $query->where('remitente_id', $user->id)
                          ->where('destinatario_id', $otro_usuario_id)
                          ->orWhere('remitente_id', $otro_usuario_id)
                          ->where('destinatario_id', $user->id);
                })->orderBy('created_at', 'desc')->first();
                
                return [
                    'usuario' => $otro_usuario,
                    'ultimo_mensaje' => $ultimo_mensaje,
                    'no_leidos' => Mensaje::where('remitente_id', $otro_usuario_id)
                        ->where('destinatario_id', $user->id)
                        ->where('leido', false)
                        ->count()
                ];
            });
        
        return view('user.mensajes.inbox', compact('conversaciones'));
    }

    /**
     * Ver conversación específica
     */
    public function viewConversation($usuario_id)
    {
        $user = Auth::user();
        $otro_usuario = \App\Models\User::findOrFail($usuario_id);
        
        // Marcar mensajes como leídos
        Mensaje::where('remitente_id', $usuario_id)
            ->where('destinatario_id', $user->id)
            ->where('leido', false)
            ->update(['leido' => true]);
        
        // Obtener mensajes de la conversación
        $mensajes = Mensaje::where(function($query) use ($user, $usuario_id) {
            $query->where('remitente_id', $user->id)
                  ->where('destinatario_id', $usuario_id)
                  ->orWhere('remitente_id', $usuario_id)
                  ->where('destinatario_id', $user->id);
        })->orderBy('created_at', 'asc')->get();
        
        return view('user.mensajes.leer', compact('mensajes', 'otro_usuario'));
    }

    /**
     * Enviar mensaje
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'destinatario_id' => 'required|exists:usuarios,id',
            'contenido' => 'required|string|max:1000'
        ]);
        
        $mensaje = Mensaje::create([
            'remitente_id' => Auth::id(),
            'destinatario_id' => $request->destinatario_id,
            'contenido' => $request->contenido,
            'leido' => false
        ]);
        
        // Crear notificación para el receptor
        $receptor = \App\Models\User::find($request->destinatario_id);
        Notificacion::create([
            'usuario_id' => $request->destinatario_id,
            'titulo' => 'Nuevo Mensaje',
            'mensaje' => Auth::user()->nombre . ' te ha enviado un mensaje.',
            'tipo' => 'mensaje',
            'leida' => false
        ]);
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'mensaje' => $mensaje->load('remitente')
            ]);
        }
        
        return back()->with('success', 'Mensaje enviado exitosamente.');
    }

    /**
     * Buscar usuarios para mensajes
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('q');
        
        $usuarios = \App\Models\User::where('nombre', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->where('id', '!=', Auth::id())
            ->limit(10)
            ->get(['id', 'nombre', 'email']);
        
        return response()->json($usuarios);
    }

    /**
     * Perfil del usuario
     */
    public function profile()
    {
        $user = Auth::user();
        
        // Calcular días totales de lectura usando las fechas disponibles
        $dias_lectura = Prestamo::where('usuario_id', $user->id)
            ->where('estado', 'devuelto')
            ->whereNotNull('fecha_devolucion_real')
            ->get()
            ->sum(function($prestamo) {
                return $prestamo->fecha_prestamo->diffInDays($prestamo->fecha_devolucion_real);
            });
        
        // Estadísticas del perfil
        $stats = [
            'libros_leidos' => Prestamo::where('usuario_id', $user->id)
                ->where('estado', 'devuelto')
                ->count(),
            'dias_lectura' => $dias_lectura,
            'categorias_favoritas' => Prestamo::join('libros', 'prestamos.libro_id', '=', 'libros.id')
                ->join('categorias', 'libros.categoria_id', '=', 'categorias.id')
                ->where('prestamos.usuario_id', $user->id)
                ->select('categorias.nombre', DB::raw('COUNT(*) as total'))
                ->groupBy('categorias.id', 'categorias.nombre')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get(),
            'autores_favoritos' => Prestamo::join('libros', 'prestamos.libro_id', '=', 'libros.id')
                ->join('autors', 'libros.autor_id', '=', 'autors.id')
                ->where('prestamos.usuario_id', $user->id)
                ->select('autors.nombre', DB::raw('COUNT(*) as total'))
                ->groupBy('autors.id', 'autors.nombre')
                ->orderBy('total', 'desc')
                ->limit(5)
                ->get()
        ];
        
        return view('user.profile', compact('user', 'stats'));
    }

    /**
     * Actualizar perfil
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,' . $user->id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'fecha_nacimiento' => 'nullable|date',
            'preferencias_lectura' => 'nullable|array'
        ]);
        
        $user->update($request->only([
            'nombre', 'email', 'telefono', 'direccion', 'fecha_nacimiento'
        ]));
        
        if ($request->has('preferencias_lectura')) {
            $user->preferencias_lectura = $request->preferencias_lectura;
            $user->save();
        }
        
        return back()->with('success', 'Perfil actualizado exitosamente.');
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed'
        ]);
        
        $user = Auth::user();
        $user->password = bcrypt($request->password);
        $user->save();
        
        return back()->with('success', 'Contraseña cambiada exitosamente.');
    }

    /**
     * Vista de reseñas del usuario
     */
    public function reviews()
    {
        $user = Auth::user();
        
        $resenas = Resena::with(['libro', 'libro.autor'])
            ->where('usuario_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('user.reviews', compact('resenas'));
    }

    /**
     * Crear reseña
     */
    public function createReview(Request $request, $libro_id)
    {
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comentario' => 'required|string|max:1000'
        ]);
        
        // Verificar si ya existe una reseña
        $resena_existente = Resena::where('usuario_id', Auth::id())
            ->where('libro_id', $libro_id)
            ->first();
        
        if ($resena_existente) {
            return back()->with('error', 'Ya has reseñado este libro.');
        }
        
        // Permitir a cualquier usuario dejar reseña
        $resena = Resena::create([
            'usuario_id' => Auth::id(),
            'libro_id' => $libro_id,
            'calificacion' => $request->rating,
            'comentario' => $request->comentario,
            'estado' => 'aprobada'
        ]);
        
        // Actualizar rating del libro
        $libro = Libro::find($libro_id);
        $nuevo_rating = Resena::where('libro_id', $libro_id)
            ->avg('calificacion');
        
        // Eliminar o comentar esta línea para evitar el error de columna inexistente.
        // $libro->rating = $nuevo_rating ?? 0;
        // $libro->save();
        
        return back()->with('success', '¡Tu reseña se publicó exitosamente!');
    }

    /**
     * Editar reseña
     */
    public function editReview(Request $request, $id)
    {
        $resena = Resena::where('usuario_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comentario' => 'required|string|max:1000'
        ]);
        
        $resena->update([
            'rating' => $request->rating,
            'comentario' => $request->comentario,
            'aprobada' => false // Requiere nueva aprobación
        ]);
        
        return back()->with('success', 'Reseña actualizada exitosamente.');
    }

    /**
     * Eliminar reseña
     */
    public function deleteReview($id)
    {
        $resena = Resena::where('usuario_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        $resena->delete();
        
        return back()->with('success', 'Reseña eliminada exitosamente.');
    }

    /**
     * Vista de configuración
     */
    public function settings()
    {
        $user = Auth::user();
        
        return view('user.settings', compact('user'));
    }

    /**
     * Actualizar configuración
     */
    public function updateSettings(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'notificaciones_email' => 'boolean',
            'notificaciones_push' => 'boolean',
            'tema' => 'in:light,dark,auto',
            'idioma' => 'in:es,en'
        ]);
        
        $user->update($request->only([
            'notificaciones_email',
            'notificaciones_push',
            'tema',
            'idioma'
        ]));
        
        return back()->with('success', 'Configuración actualizada exitosamente.');
    }

    /**
     * Exportar datos del usuario
     */
    public function exportData()
    {
        $user = Auth::user();
        
        $data = [
            'usuario' => $user->toArray(),
            'prestamos' => Prestamo::with(['libro', 'libro.autor'])
                ->where('usuario_id', $user->id)
                ->get()
                ->toArray(),
            'reservas' => Reserva::with(['libro'])
                ->where('usuario_id', $user->id)
                ->get()
                ->toArray(),
            'favoritos' => Favorito::with(['libro'])
                ->where('usuario_id', $user->id)
                ->get()
                ->toArray(),
            'resenas' => Resena::with(['libro'])
                ->where('usuario_id', $user->id)
                ->get()
                ->toArray()
        ];
        
        $filename = 'datos_usuario_' . $user->id . '_' . now()->format('Y-m-d_H-i-s') . '.json';
        
        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Content-Type', 'application/json');
    }

    /**
     * Eliminar cuenta
     */
    public function deleteAccount(Request $request)
    {
        $request->validate([
            'confirmacion' => 'required|accepted'
        ]);
        
        $user = Auth::user();
        
        // Eliminar datos del usuario
        Prestamo::where('usuario_id', $user->id)->delete();
        Reserva::where('usuario_id', $user->id)->delete();
        Favorito::where('usuario_id', $user->id)->delete();
        Notificacion::where('usuario_id', $user->id)->delete();
        Mensaje::where('remitente_id', $user->id)->orWhere('destinatario_id', $user->id)->delete();
        Resena::where('usuario_id', $user->id)->delete();
        Compra::where('usuario_id', $user->id)->delete();
        
        // Eliminar usuario
        $user->delete();
        
        Auth::logout();
        
        return redirect()->route('home')->with('success', 'Tu cuenta ha sido eliminada exitosamente.');
    }
}
