<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Reserva;
use App\Models\Categoria;
use App\Models\Autor;
use App\Models\Resena;
use App\Models\Notificacion;
use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Estadísticas generales
        $stats = [
            'total_usuarios' => Usuario::where('tipo', 'cliente')->count(),
            'total_libros' => Libro::count(),
            'prestamos_activos' => Prestamo::where('estado', 'prestado')->count(),
            'reservas_pendientes' => Reserva::where('estado', 'pendiente')->count(),
            'libros_disponibles' => Libro::where('estado', 'disponible')->count(),
            'libros_prestados' => Libro::where('estado', 'prestado')->count(),
            'prestamos_vencidos' => Prestamo::where('estado', 'vencido')->count(),
            'resenas_pendientes' => Resena::where('estado', 'pendiente')->count(),
        ];

        // Gráfico de préstamos por mes (últimos 6 meses)
        $prestamosPorMes = Prestamo::selectRaw('MONTH(created_at) as mes, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('mes')
            ->orderBy('mes')
            ->get();

        // Libros más populares
        $librosPopulares = Libro::withCount('prestamos')
            ->orderBy('prestamos_count', 'desc')
            ->limit(5)
            ->get();

        // Usuarios más activos
        $usuariosActivos = Usuario::where('tipo', 'cliente')
            ->withCount('prestamos')
            ->orderBy('prestamos_count', 'desc')
            ->limit(5)
            ->get();

        // Préstamos recientes
        $prestamosRecientes = Prestamo::with(['usuario', 'libro.autor'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Categorías más populares
        $categoriasPopulares = Categoria::withCount('libros')
            ->orderBy('libros_count', 'desc')
            ->limit(5)
            ->get();

        // Alertas del sistema
        $alertas = $this->getAlertasSistema();

        return view('admin.dashboard', compact(
            'stats', 'prestamosPorMes', 'librosPopulares', 'usuariosActivos',
            'prestamosRecientes', 'categoriasPopulares', 'alertas'
        ));
    }

    private function getAlertasSistema()
    {
        $alertas = [];

        // Préstamos vencidos
        $prestamosVencidos = Prestamo::where('estado', 'prestado')
            ->where('fecha_devolucion_esperada', '<', now())
            ->count();
        
        if ($prestamosVencidos > 0) {
            $alertas[] = [
                'tipo' => 'warning',
                'titulo' => 'Préstamos Vencidos',
                'mensaje' => "Hay {$prestamosVencidos} préstamos vencidos que requieren atención.",
                'icono' => 'fas fa-exclamation-triangle',
                'accion' => route('admin.loans.index')
            ];
        }

        // Libros con stock bajo
        $librosStockBajo = Libro::where('stock', '<=', 2)
            ->where('stock', '>', 0)
            ->count();
        
        if ($librosStockBajo > 0) {
            $alertas[] = [
                'tipo' => 'info',
                'titulo' => 'Stock Bajo',
                'mensaje' => "Hay {$librosStockBajo} libros con stock bajo.",
                'icono' => 'fas fa-boxes',
                'accion' => route('admin.books.index')
            ];
        }

        // Reseñas pendientes
        $resenasPendientes = Resena::where('estado', 'pendiente')->count();
        
        if ($resenasPendientes > 0) {
            $alertas[] = [
                'tipo' => 'primary',
                'titulo' => 'Reseñas Pendientes',
                'mensaje' => "Hay {$resenasPendientes} reseñas pendientes de moderación.",
                'icono' => 'fas fa-star',
                'accion' => route('admin.reviews.pending')
            ];
        }

        return $alertas;
    }

    // ==================== GESTIÓN DE LIBROS ====================
    
    public function books(Request $request)
    {
        $query = Libro::with(['autor', 'categoria'])
            ->withCount(['prestamos', 'favoritos', 'resenas']);

        // Filtros básicos
        if ($request->filled('busqueda')) {
            $q = $request->busqueda;
            $query->where(function($sub) use ($q) {
                $sub->where('titulo', 'like', "%$q%")
                    ->orWhere('isbn', 'like', "%$q%")
                    ->orWhereHas('autor', function($a) use ($q) {
                        $a->where('nombre', 'like', "%$q%")
                          ->orWhere('apellido', 'like', "%$q%") ;
                    });
            });
        }
        if ($request->filled('categoria')) {
            $query->where('categoria_id', $request->categoria);
        }
        if ($request->filled('autor')) {
            $query->where('autor_id', $request->autor);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtros avanzados
        if ($request->filled('nuevo')) {
            $query->where('created_at', '>=', now()->subDays(7));
        }
        if ($request->filled('sin_stock')) {
            $query->where('stock', '<=', 0);
        }
        if ($request->filled('solo_online')) {
            $query->where('precio_compra_fisica', '<=', 0)
                  ->where('precio_prestamo_fisico', '<=', 0)
                  ->where(function($q) {
                      $q->where('precio_compra_online', '>', 0)
                        ->orWhere('precio_prestamo_online', '>', 0);
                  });
        }
        if ($request->filled('solo_fisico')) {
            $query->where('precio_compra_online', '<=', 0)
                  ->where('precio_prestamo_online', '<=', 0)
                  ->where(function($q) {
                      $q->where('precio_compra_fisica', '>', 0)
                        ->orWhere('precio_prestamo_fisico', '>', 0);
                  });
        }
        if ($request->filled('con_pdf')) {
            $query->whereNotNull('archivo_pdf');
        }
        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }
        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $libros = $query->orderBy('created_at', 'desc')->paginate(15);

        // Contadores para tarjetas
        $total = Libro::count();
        $nuevos = Libro::where('created_at', '>=', now()->subDays(7))->count();
        $sinStock = Libro::where('stock', '<=', 0)->count();
        $soloOnline = Libro::where('precio_compra_fisica', '<=', 0)
            ->where('precio_prestamo_fisico', '<=', 0)
            ->where(function($q) {
                $q->where('precio_compra_online', '>', 0)
                  ->orWhere('precio_prestamo_online', '>', 0);
            })->count();
        $soloFisico = Libro::where('precio_compra_online', '<=', 0)
            ->where('precio_prestamo_online', '<=', 0)
            ->where(function($q) {
                $q->where('precio_compra_fisica', '>', 0)
                  ->orWhere('precio_prestamo_fisico', '>', 0);
            })->count();
        $conPDF = Libro::whereNotNull('archivo_pdf')->count();

        $categorias = Categoria::all();
        $autores = Autor::all();

        return view('admin.books.index', compact(
            'libros', 'categorias', 'autores',
            'total', 'nuevos', 'sinStock', 'soloOnline', 'soloFisico', 'conPDF'
        ));
    }

    public function createBook()
    {
        $categorias = Categoria::all();
        $autores = Autor::all();
        
        return view('admin.books.create', compact('categorias', 'autores'));
    }

    public function storeBook(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'autor_id' => 'required|exists:autors,id',
            'categoria_id' => 'required|exists:categorias,id',
            'isbn' => 'nullable|string|max:20|unique:libros',
            'anio_publicacion' => 'nullable|integer|min:1800|max:' . date('Y'),
            'editorial' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:2000',
            'paginas' => 'nullable|integer|min:1',
            'idioma' => 'nullable|string|max:50',
            'stock' => 'required|integer|min:0',
            'ubicacion' => 'nullable|string|max:255',
            'imagen_portada' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'archivo_pdf' => 'nullable|file|mimes:pdf|max:20480',
            'contenido' => 'required|string',
            'preview_limit' => 'required|integer|min:100|max:100000',
            'precio_compra_fisica' => 'required|numeric|min:0',
            'precio_compra_online' => 'required|numeric|min:0',
            'precio_prestamo_fisico' => 'required|numeric|min:0',
            'precio_prestamo_online' => 'required|numeric|min:0',
            'estado' => 'required|in:disponible,en_reparacion,perdido',
        ]);

        $data = $request->except(['imagen_portada', 'archivo_pdf', 'precio']);
        $data['creado_por'] = auth()->id();
        $data['descripcion_vista_previa'] = $request->input('descripcion_vista_previa');

        // Procesar imagen de portada
        if ($request->hasFile('imagen_portada')) {
            $imagenPath = $request->file('imagen_portada')->store('libros/portadas', 'public');
            $data['imagen_portada'] = $imagenPath;
        }

        // Procesar archivo PDF
        if ($request->hasFile('archivo_pdf')) {
            $pdfPath = $request->file('archivo_pdf')->store('libros/pdf', 'public');
            $data['archivo_pdf'] = $pdfPath;
        }

        $libro = Libro::create($data);

        return redirect()->route('admin.books.index')
            ->with('success', "Libro '{$libro->titulo}' creado exitosamente.");
    }

    public function editBook(Libro $libro)
    {
        $categorias = Categoria::all();
        $autores = Autor::all();
        
        return view('admin.books.edit', compact('libro', 'categorias', 'autores'));
    }

    public function updateBook(Request $request, Libro $libro)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'autor_id' => 'required|exists:autors,id',
            'categoria_id' => 'required|exists:categorias,id',
            'isbn' => 'nullable|string|max:20|unique:libros,isbn,' . $libro->id,
            'anio_publicacion' => 'nullable|integer|min:1800|max:' . date('Y'),
            'editorial' => 'nullable|string|max:255',
            'descripcion' => 'nullable|string|max:2000',
            'stock' => 'required|integer|min:0',
            'precio_compra_fisica' => 'required|numeric|min:0',
            'precio_compra_online' => 'required|numeric|min:0',
            'precio_prestamo_fisico' => 'required|numeric|min:0',
            'precio_prestamo_online' => 'required|numeric|min:0',
            'imagen_portada' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'archivo_pdf' => 'nullable|file|mimes:pdf|max:10240',
            'estado' => 'required|in:disponible,prestado,en_reparacion,perdido',
            'ubicacion' => 'nullable|string|max:255',
            'contenido' => 'required|string',
            'preview_limit' => 'required|integer|min:100|max:100000',
        ]);

        $data = $request->except(['imagen_portada', 'archivo_pdf']);
        $data['descripcion_vista_previa'] = $request->input('descripcion_vista_previa');
        
        // Procesar nueva imagen si se subió
        if ($request->hasFile('imagen_portada')) {
            // Eliminar imagen anterior si existe
            if ($libro->imagen_portada && !str_starts_with($libro->imagen_portada, 'http')) {
                Storage::disk('public')->delete($libro->imagen_portada);
            }
            
            $imagenPath = $request->file('imagen_portada')->store('libros/portadas', 'public');
            $data['imagen_portada'] = $imagenPath;
        }
        
        // Procesar nuevo archivo PDF si se subió
        if ($request->hasFile('archivo_pdf')) {
            // Eliminar archivo anterior si existe
            if ($libro->archivo_pdf) {
                Storage::disk('public')->delete($libro->archivo_pdf);
            }
            
            $pdfPath = $request->file('archivo_pdf')->store('libros/pdf', 'public');
            $data['archivo_pdf'] = $pdfPath;
        }

        $libro->update($data);

        return redirect()->route('admin.books.index')
            ->with('success', "Libro '{$libro->titulo}' actualizado exitosamente.");
    }

    public function deleteBook(Libro $libro)
    {
        // Verificar que no haya préstamos activos
        $prestamosActivos = $libro->prestamos()->where('estado', 'prestado')->count();
        
        if ($prestamosActivos > 0) {
            return back()->with('error', 'No se puede eliminar un libro con préstamos activos.');
        }

        // Eliminar archivo PDF si existe
        if ($libro->archivo_pdf) {
            Storage::disk('public')->delete($libro->archivo_pdf);
        }

        $titulo = $libro->titulo;
        $libro->delete();

        return redirect()->route('admin.books.index')
            ->with('success', "Libro '{$titulo}' eliminado exitosamente.");
    }

    // ==================== GESTIÓN DE USUARIOS ====================
    
    public function users()
    {
        $usuarios = Usuario::where('tipo', 'cliente')
            ->withCount(['prestamos', 'reservas', 'favoritos'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.users.index', compact('usuarios'));
    }

    public function showUser(Usuario $usuario)
    {
        $usuario->load(['prestamos.libro.autor', 'reservas.libro.autor', 'favoritos.libro.autor']);
        
        $estadisticas = [
            'total_prestamos' => $usuario->prestamos()->count(),
            'prestamos_activos' => $usuario->prestamos()->where('estado', 'prestado')->count(),
            'prestamos_vencidos' => $usuario->prestamos()->where('estado', 'vencido')->count(),
            'total_reservas' => $usuario->reservas()->count(),
            'reservas_pendientes' => $usuario->reservas()->where('estado', 'pendiente')->count(),
            'total_favoritos' => $usuario->favoritos()->count(),
        ];

        return view('admin.users.show', compact('usuario', 'estadisticas'));
    }

    public function editUser(Usuario $usuario)
    {
        return view('admin.users.edit', compact('usuario'));
    }

    public function updateUser(Request $request, Usuario $usuario)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:usuarios,email,' . $usuario->id,
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:500',
            'estado' => 'required|in:activo,inactivo,suspendido',
            'idioma_preferencia' => 'required|in:es,en',
            'tema_preferencia' => 'required|in:claro,oscuro',
        ]);

        $usuario->update($request->all());

        return redirect()->route('admin.users.index')
            ->with('success', "Usuario '{$usuario->nombre_completo}' actualizado exitosamente.");
    }

    public function deleteUser(Usuario $usuario)
    {
        // Verificar que no tenga préstamos activos
        $prestamosActivos = $usuario->prestamos()->where('estado', 'prestado')->count();
        
        if ($prestamosActivos > 0) {
            return back()->with('error', 'No se puede eliminar un usuario con préstamos activos.');
        }

        $nombre = $usuario->nombre_completo;
        $usuario->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Usuario '{$nombre}' eliminado exitosamente.");
    }

    // ==================== GESTIÓN DE PRÉSTAMOS ====================
    
    public function loans()
    {
        $prestamos = Prestamo::with(['usuario', 'libro.autor', 'libro.categoria'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.loans.index', compact('prestamos'));
    }

    public function showLoan(Prestamo $prestamo)
    {
        $prestamo->load(['usuario', 'libro.autor', 'libro.categoria']);
        
        return view('admin.loans.show', compact('prestamo'));
    }

    public function updateLoan(Request $request, Prestamo $prestamo)
    {
        $request->validate([
            'estado' => 'required|in:prestado,devuelto,vencido,perdido',
            'observaciones' => 'nullable|string|max:500',
            'fecha_devolucion_real' => 'nullable|date',
        ]);

        $estadoAnterior = $prestamo->estado;
        $data = $request->all();

        // Si se marca como devuelto, actualizar stock del libro
        if ($request->estado === 'devuelto' && $estadoAnterior !== 'devuelto') {
            $libro = $prestamo->libro;
            $libro->increment('stock');
            
            if ($libro->stock > 0) {
                $libro->update(['estado' => 'disponible']);
            }

            // Notificar al usuario
            Notificacion::create([
                'usuario_id' => $prestamo->usuario_id,
                'titulo' => 'Libro devuelto',
                'mensaje' => "El libro '{$prestamo->libro->titulo}' ha sido marcado como devuelto.",
                'tipo' => 'success',
                'leida' => false
            ]);
        }

        $prestamo->update($data);

        return redirect()->route('admin.loans.index')
            ->with('success', 'Préstamo actualizado exitosamente.');
    }

    // ==================== GESTIÓN DE RESERVAS ====================
    
    public function reservations()
    {
        $reservas = Reserva::with(['usuario', 'libro.autor', 'libro.categoria'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.reservations.index', compact('reservas'));
    }

    public function updateReservation(Request $request, Reserva $reserva)
    {
        $request->validate([
            'estado' => 'required|in:pendiente,completada,cancelada,expirada',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $estadoAnterior = $reserva->estado;
        $reserva->update($request->all());

        // Si se marca como completada, notificar al usuario
        if ($request->estado === 'completada' && $estadoAnterior !== 'completada') {
            Notificacion::create([
                'usuario_id' => $reserva->usuario_id,
                'titulo' => 'Libro disponible',
                'mensaje' => "El libro '{$reserva->libro->titulo}' que reservaste ya está disponible.",
                'tipo' => 'success',
                'leida' => false
            ]);
        }

        return redirect()->route('admin.reservations.index')
            ->with('success', 'Reserva actualizada exitosamente.');
    }

    // ==================== GESTIÓN DE CATEGORÍAS ====================
    
    public function categories()
    {
        $categorias = Categoria::withCount('libros')
            ->orderBy('nombre')
            ->paginate(15);

        return view('admin.categories.index', compact('categorias'));
    }

    public function createCategory()
    {
        return view('admin.categories.create');
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias',
            'descripcion' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
        ]);

        Categoria::create($request->all());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    public function editCategory(Categoria $categoria)
    {
        return view('admin.categories.edit', compact('categoria'));
    }

    public function updateCategory(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string|max:500',
            'color' => 'nullable|string|max:7',
        ]);

        $categoria->update($request->all());

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    public function deleteCategory(Categoria $categoria)
    {
        // Verificar que no tenga libros asociados
        if ($categoria->libros()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una categoría que tiene libros asociados.');
        }

        $nombre = $categoria->nombre;
        $categoria->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', "Categoría '{$nombre}' eliminada exitosamente.");
    }

    // ==================== GESTIÓN DE AUTORES ====================
    
    public function authors()
    {
        $autores = Autor::withCount('libros')
            ->orderBy('nombre')
            ->paginate(15);

        return view('admin.authors.index', compact('autores'));
    }

    public function createAuthor()
    {
        return view('admin.authors.create');
    }

    public function storeAuthor(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'biografia' => 'nullable|string|max:1000',
            'fecha_nacimiento' => 'nullable|date',
            'nacionalidad' => 'nullable|string|max:100',
            'website' => 'nullable|url',
        ]);

        Autor::create($request->all());

        return redirect()->route('admin.authors.index')
            ->with('success', 'Autor creado exitosamente.');
    }

    public function editAuthor(Autor $autor)
    {
        return view('admin.authors.edit', compact('autor'));
    }

    public function updateAuthor(Request $request, Autor $autor)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'biografia' => 'nullable|string|max:1000',
            'fecha_nacimiento' => 'nullable|date',
            'nacionalidad' => 'nullable|string|max:100',
            'website' => 'nullable|url',
        ]);

        $autor->update($request->all());

        return redirect()->route('admin.authors.index')
            ->with('success', 'Autor actualizado exitosamente.');
    }

    public function deleteAuthor(Autor $autor)
    {
        // Verificar que no tenga libros asociados
        if ($autor->libros()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un autor que tiene libros asociados.');
        }

        $nombre = $autor->nombre_completo;
        $autor->delete();

        return redirect()->route('admin.authors.index')
            ->with('success', "Autor '{$nombre}' eliminado exitosamente.");
    }

    // ==================== GESTIÓN DE RESEÑAS ====================
    
    public function reviewsIndex() {
        $resenas = \App\Models\Resena::with(['usuario', 'libro.autor'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('admin.reviews.index', compact('resenas'));
    }

    public function deleteReview($id) {
        $resena = \App\Models\Resena::findOrFail($id);
        $resena->delete();
        return back()->with('success', 'Reseña eliminada exitosamente.');
    }

    // ==================== REPORTES ====================
    
    public function reports()
    {
        return view('admin.reports.index');
    }

    public function loanReport()
    {
        $prestamosPorMes = Prestamo::selectRaw('YEAR(created_at) as año, MONTH(created_at) as mes, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->groupBy('año', 'mes')
            ->orderBy('año')
            ->orderBy('mes')
            ->get();

        $librosMasPrestados = Libro::withCount('prestamos')
            ->orderBy('prestamos_count', 'desc')
            ->limit(10)
            ->get();

        $usuariosMasActivos = Usuario::where('tipo', 'cliente')
            ->withCount('prestamos')
            ->orderBy('prestamos_count', 'desc')
            ->limit(10)
            ->get();

        return view('admin.reports.loans', compact('prestamosPorMes', 'librosMasPrestados', 'usuariosMasActivos'));
    }

    public function bookReport()
    {
        $librosPorCategoria = Categoria::withCount('libros')->get();
        $librosPorAutor = Autor::withCount('libros')->orderBy('libros_count', 'desc')->limit(10)->get();
        $librosPorEstado = Libro::selectRaw('estado, COUNT(*) as total')->groupBy('estado')->get();

        return view('admin.reports.books', compact('librosPorCategoria', 'librosPorAutor', 'librosPorEstado'));
    }

    public function userReport()
    {
        $usuariosPorMes = Usuario::where('tipo', 'cliente')
            ->selectRaw('YEAR(created_at) as año, MONTH(created_at) as mes, COUNT(*) as total')
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->groupBy('año', 'mes')
            ->orderBy('año')
            ->orderBy('mes')
            ->get();

        $usuariosActivos = Usuario::where('tipo', 'cliente')
            ->where('last_login_at', '>=', Carbon::now()->subMonth())
            ->count();

        $usuariosInactivos = Usuario::where('tipo', 'cliente')
            ->where('last_login_at', '<', Carbon::now()->subMonth())
            ->count();

        return view('admin.reports.users', compact('usuariosPorMes', 'usuariosActivos', 'usuariosInactivos'));
    }

    // ==================== CONFIGURACIÓN ====================
    
    public function settings()
    {
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'nombre_biblioteca' => 'required|string|max:255',
            'email_contacto' => 'required|email',
            'telefono_contacto' => 'nullable|string|max:20',
            'direccion_biblioteca' => 'nullable|string|max:500',
            'horario_apertura' => 'nullable|string|max:255',
            'dias_prestamo' => 'required|integer|min:1|max:30',
            'max_prestamos_usuario' => 'required|integer|min:1|max:10',
            'multa_por_dia' => 'nullable|numeric|min:0',
        ]);

        // Aquí se actualizarían las configuraciones del sistema
        // Por ahora solo mostramos un mensaje de éxito

        return back()->with('success', 'Configuraciones actualizadas exitosamente.');
    }

    /**
     * Mostrar historial de notificaciones del sistema
     */
    public function notifications()
    {
        $notificaciones = \App\Models\Notificacion::with('usuario')->orderBy('created_at', 'desc')->paginate(30);
        $usuarios = \App\Models\Usuario::where('tipo', 'cliente')->get();
        return view('admin.notifications', compact('notificaciones', 'usuarios'));
    }

    /**
     * Enviar notificación individual
     */
    public function sendNotification(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'titulo' => 'required|string|max:255',
            'mensaje' => 'required|string|max:1000',
            'tipo' => 'required|in:info,success,warning,error'
        ]);

        \App\Models\Notificacion::create([
            'usuario_id' => $request->usuario_id,
            'titulo' => $request->titulo,
            'mensaje' => $request->mensaje,
            'tipo' => $request->tipo,
            'leida' => false
        ]);

        return back()->with('success', 'Notificación enviada exitosamente.');
    }

    /**
     * Enviar notificación masiva
     */
    public function sendBulkNotification(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'mensaje' => 'required|string|max:1000',
            'tipo' => 'required|in:info,success,warning,error',
            'destinatarios' => 'required|in:todos,activos,inactivos'
        ]);

        $usuarios = \App\Models\Usuario::where('tipo', 'cliente');
        
        switch($request->destinatarios) {
            case 'activos':
                $usuarios = $usuarios->where('estado', 'activo');
                break;
            case 'inactivos':
                $usuarios = $usuarios->where('estado', 'inactivo');
                break;
        }

        $usuarios = $usuarios->get();
        $count = 0;

        foreach($usuarios as $usuario) {
            \App\Models\Notificacion::create([
                'usuario_id' => $usuario->id,
                'titulo' => $request->titulo,
                'mensaje' => $request->mensaje,
                'tipo' => $request->tipo,
                'leida' => false
            ]);
            $count++;
        }

        return back()->with('success', "Notificación enviada a {$count} usuarios.");
    }

    /**
     * Eliminar notificación
     */
    public function deleteNotification($id)
    {
        $notificacion = \App\Models\Notificacion::findOrFail($id);
        $notificacion->delete();
        return back()->with('success', 'Notificación eliminada.');
    }

    /**
     * Limpiar notificaciones antiguas
     */
    public function cleanupNotifications(Request $request)
    {
        $dias = $request->get('dias', 30);
        $count = \App\Models\Notificacion::where('created_at', '<', now()->subDays($dias))->delete();
        return back()->with('success', "Se eliminaron {$count} notificaciones antiguas.");
    }

    /**
     * Obtener notificaciones no leídas (AJAX)
     */
    public function getUnreadNotifications()
    {
        $notificaciones = \App\Models\Notificacion::with('usuario')
            ->where('leida', false)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'notificaciones' => $notificaciones,
            'count' => \App\Models\Notificacion::where('leida', false)->count()
        ]);
    }
}
