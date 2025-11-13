<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use App\Models\Compra;
use App\Models\Prestamo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CompraController extends Controller
{
    /**
     * Mostrar la página de compra del libro
     */
    public function show(Libro $libro)
    {
        $libro->load(['autor', 'categoria']);
        
        // Verificar si el usuario tiene este libro en favoritos
        $isFavorite = false;
        if (auth()->check()) {
            $isFavorite = auth()->user()->favoritos()->where('libro_id', $libro->id)->exists();
        }

        return view('books.purchase', compact('libro', 'isFavorite'));
    }

    /**
     * Procesar compra física
     */
    public function compraFisica(Request $request, Libro $libro)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email',
            'telefono' => 'required|string|max:20',
            'direccion' => 'required|string|max:500',
            'departamento' => 'required|string|max:100',
            'distrito' => 'required|string|max:100',
            'codigo_postal' => 'nullable|string|max:10',
            'metodo_pago' => 'required|in:tarjeta,paypal,transferencia,yape',
        ]);

        $precio = $libro->precio_compra_fisica ?? 99.90;

        $compra = Compra::create([
            'usuario_id' => Auth::id(),
            'libro_id' => $libro->id,
            'tipo' => 'fisico',
            'precio' => $precio,
            'estado' => 'completada',
            'datos_envio' => json_encode([
                'nombre' => $request->nombre,
                'email' => $request->email,
                'telefono' => $request->telefono,
                'direccion' => $request->direccion,
                'departamento' => $request->departamento,
                'distrito' => $request->distrito,
                'codigo_postal' => $request->codigo_postal,
                'metodo_pago' => $request->metodo_pago,
            ]),
        ]);

        if ($libro->stock > 0) {
            $libro->decrement('stock');
        }

        return redirect()->route('books.purchase.success', $compra)
            ->with('success', '¡Compra realizada con éxito! Tu libro físico será enviado pronto a tu dirección en Perú.');
    }

    /**
     * Procesar compra virtual (PDF)
     */
    public function compraVirtual(Request $request, Libro $libro)
    {
        $request->validate([
            'metodo_pago' => 'required|in:tarjeta,paypal,transferencia,yape',
        ]);

        // Verificar que el libro tiene PDF
        if (!$libro->archivo_pdf) {
            return back()->with('error', 'Este libro no tiene versión digital disponible.');
        }

        $precio = $libro->precio_compra_online ?? 69.90;

        $compra = Compra::create([
            'usuario_id' => Auth::id(),
            'libro_id' => $libro->id,
            'tipo' => 'virtual',
            'precio' => $precio,
            'estado' => 'completada',
            'datos_envio' => json_encode([
                'metodo_pago' => $request->metodo_pago,
            ]),
        ]);

        return redirect()->route('books.purchase.success', $compra)
            ->with('success', '¡Compra realizada con éxito! Ya puedes descargar tu libro digital.');
    }

    /**
     * Procesar préstamo (lectura en línea)
     */
    public function prestamo(Request $request, Libro $libro)
    {
        $usuario = Auth::user();

        // Verificar si ya tiene un préstamo activo de este libro
        $prestamoActivo = Prestamo::where('usuario_id', $usuario->id)
            ->where('libro_id', $libro->id)
            ->where('estado', 'prestado')
            ->first();

        if ($prestamoActivo) {
            return back()->with('error', 'Ya tienes un préstamo activo de este libro.');
        }

        // Verificar límite de préstamos (máximo 3)
        $prestamosActivos = Prestamo::where('usuario_id', $usuario->id)
            ->where('estado', 'prestado')
            ->count();

        if ($prestamosActivos >= 3) {
            return back()->with('error', 'Has alcanzado el límite máximo de préstamos (3 libros).');
        }

        // Crear préstamo
        $prestamo = Prestamo::create([
            'usuario_id' => $usuario->id,
            'libro_id' => $libro->id,
            'fecha_prestamo' => now(),
            'fecha_devolucion_esperada' => now()->addDays(14),
            'estado' => 'prestado',
            'tipo' => 'digital', // Para diferenciar de préstamos físicos
        ]);

        return redirect()->route('books.read', $libro)
            ->with('success', 'Préstamo realizado con éxito. Puedes leer el libro durante 14 días.');
    }

    /**
     * Mostrar página de éxito de compra
     */
    public function success(Compra $compra)
    {
        // Verificar que la compra pertenece al usuario autenticado
        if ($compra->usuario_id !== Auth::id()) {
            return redirect()->route('home')->with('error', 'Acceso denegado.');
        }

        $compra->load(['libro.autor', 'libro.categoria']);
        
        return view('books.purchase-success', compact('compra'));
    }

    /**
     * Descargar PDF del libro comprado
     */
    public function downloadPdf(Compra $compra)
    {
        // Verificar que la compra pertenece al usuario autenticado
        if ($compra->usuario_id !== Auth::id()) {
            return back()->with('error', 'Acceso denegado.');
        }

        // Verificar que es una compra virtual
        if ($compra->tipo !== 'virtual') {
            return back()->with('error', 'Esta compra no incluye descarga digital.');
        }

        $libro = $compra->libro;
        
        if (!$libro->archivo_pdf || !Storage::disk('public')->exists($libro->archivo_pdf)) {
            return back()->with('error', 'El archivo PDF no está disponible.');
        }

        return Storage::disk('public')->download($libro->archivo_pdf, $libro->titulo . '.pdf');
    }

    /**
     * Leer libro prestado (vista en línea)
     */
    public function read(Libro $libro)
    {
        $usuario = Auth::user();

        // Verificar que tiene un préstamo activo
        $prestamo = Prestamo::where('usuario_id', $usuario->id)
            ->where('libro_id', $libro->id)
            ->where('estado', 'prestado')
            ->first();

        if (!$prestamo) {
            return redirect()->route('books.show', $libro)
                ->with('error', 'No tienes un préstamo activo de este libro.');
        }

        $libro->load(['autor', 'categoria']);

        return view('books.read', compact('libro', 'prestamo'));
    }

    /**
     * Historial de compras del usuario
     */
    public function historial()
    {
        $usuario = Auth::user();
        
        $compras = $usuario->compras()
            ->with(['libro.autor', 'libro.categoria'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $prestamos = $usuario->prestamos()
            ->with(['libro.autor', 'libro.categoria'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('user.historial', compact('compras', 'prestamos'));
    }

    public function userPurchases()
    {
        $compras = Compra::where('usuario_id', Auth::id())->with('libro.autor', 'libro.categoria')->latest()->get();
        return view('user.purchases', compact('compras'));
    }

    /**
     * Listar todas las compras (admin)
     */
    public function index(Request $request)
    {
        $query = Compra::with(['usuario', 'libro', 'libro.autor']);

        // Filtros
        if ($request->filled('usuario')) {
            $query->whereHas('usuario', function($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->usuario . '%')
                  ->orWhere('apellido', 'like', '%' . $request->usuario . '%')
                  ->orWhere('email', 'like', '%' . $request->usuario . '%');
            });
        }
        if ($request->filled('libro')) {
            $query->whereHas('libro', function($q) use ($request) {
                $q->where('titulo', 'like', '%' . $request->libro . '%');
            });
        }
        if ($request->filled('modalidad')) {
            $query->where('tipo', $request->modalidad);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('fecha')) {
            $query->whereDate('created_at', $request->fecha);
        }

        $compras = $query->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.compras.index', compact('compras'));
    }
} 