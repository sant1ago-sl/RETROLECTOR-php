<?php

namespace App\Http\Controllers;

use App\Models\ReadingClub;
use App\Models\ClubMember;
use App\Models\ClubDiscussion;
use App\Models\ClubBook;
use App\Models\Categoria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReadingClubController extends Controller
{
    /**
     * Mostrar página principal de clubes de lectura
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtener estadísticas
        $stats = $this->getClubsStats();
        
        // Obtener categorías para filtros
        $categorias = Categoria::all();
        
        // Obtener clubes destacados
        $featured_clubs = $this->getFeaturedClubs();
        
        // Obtener todos los clubes
        $clubs = $this->getAllClubs();
        
        // Obtener clubes del usuario
        $my_clubs = collect();
        if ($user) {
            $my_clubs = $this->getUserClubs($user);
        }
        
        return view('books.reading-clubs', compact('stats', 'categorias', 'featured_clubs', 'clubs', 'my_clubs'));
    }

    /**
     * Obtener estadísticas de clubes
     */
    private function getClubsStats()
    {
        return [
            'total_clubs' => ReadingClub::count(),
            'total_members' => ClubMember::count(),
            'total_discussions' => ClubDiscussion::count(),
            'books_read' => ClubBook::where('estado', 'completado')->count()
        ];
    }

    /**
     * Obtener clubes destacados
     */
    private function getFeaturedClubs()
    {
        return ReadingClub::with(['categoria', 'members', 'discussions', 'books'])
            ->withCount(['members as miembros_count', 'discussions as discusiones_count', 'books as libros_leidos_count'])
            ->where('es_destacado', true)
            ->where('es_activo', true)
            ->orderBy('miembros_count', 'desc')
            ->limit(6)
            ->get()
            ->map(function ($club) {
                $club->is_member = $this->isUserMember($club);
                $club->is_pending = $this->isUserPending($club);
                return $club;
            });
    }

    /**
     * Obtener todos los clubes
     */
    private function getAllClubs()
    {
        return ReadingClub::with(['categoria', 'members', 'discussions', 'books'])
            ->withCount(['members as miembros_count', 'discussions as discusiones_count', 'books as libros_leidos_count'])
            ->where('es_activo', true)
            ->orderBy('created_at', 'desc')
            ->paginate(12)
            ->through(function ($club) {
                $club->is_member = $this->isUserMember($club);
                $club->is_pending = $this->isUserPending($club);
                return $club;
            });
    }

    /**
     * Obtener clubes del usuario
     */
    private function getUserClubs($user)
    {
        return ReadingClub::with(['categoria', 'members', 'discussions', 'books'])
            ->withCount(['members as miembros_count', 'discussions as discusiones_count', 'books as libros_leidos_count'])
            ->whereHas('members', function ($query) use ($user) {
                $query->where('usuario_id', $user->id);
            })
            ->get()
            ->map(function ($club) use ($user) {
                $club->is_member = true;
                $club->is_admin = $this->isUserAdmin($club, $user);
                return $club;
            });
    }

    /**
     * Verificar si el usuario es miembro
     */
    private function isUserMember($club)
    {
        if (!Auth::check()) return false;
        
        return $club->members()
            ->where('usuario_id', Auth::id())
            ->where('estado', 'activo')
            ->exists();
    }

    /**
     * Verificar si el usuario está pendiente
     */
    private function isUserPending($club)
    {
        if (!Auth::check()) return false;
        
        return $club->members()
            ->where('usuario_id', Auth::id())
            ->where('estado', 'pendiente')
            ->exists();
    }

    /**
     * Verificar si el usuario es administrador
     */
    private function isUserAdmin($club, $user)
    {
        return $club->members()
            ->where('usuario_id', $user->id)
            ->where('rol', 'admin')
            ->exists();
    }

    /**
     * Mostrar detalles de un club
     */
    public function show($id)
    {
        $club = ReadingClub::with([
            'categoria', 
            'members.user', 
            'discussions.user', 
            'books.libro.autor',
            'currentBook.libro.autor'
        ])
        ->withCount(['members as miembros_count', 'discussions as discusiones_count', 'books as libros_leidos_count'])
        ->findOrFail($id);
        
        $user = Auth::user();
        $is_member = $this->isUserMember($club);
        $is_admin = $user ? $this->isUserAdmin($club, $user) : false;
        
        // Obtener discusiones recientes
        $recent_discussions = $club->discussions()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Obtener libros leídos
        $read_books = $club->books()
            ->with('libro.autor')
            ->where('estado', 'completado')
            ->orderBy('fecha_fin', 'desc')
            ->limit(10)
            ->get();
        
        return view('books.club-details', compact('club', 'is_member', 'is_admin', 'recent_discussions', 'read_books'));
    }

    /**
     * Crear nuevo club
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:reading_clubs',
            'descripcion' => 'required|string|max:1000',
            'categoria_id' => 'required|exists:categorias,id',
            'max_miembros' => 'integer|min:5|max:100',
            'es_privado' => 'boolean',
            'reglas' => 'nullable|string|max:1000'
        ]);

        $user = Auth::user();
        
        DB::transaction(function () use ($request, $user) {
            // Crear el club
            $club = ReadingClub::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'categoria_id' => $request->categoria_id,
                'max_miembros' => $request->max_miembros ?? 20,
                'es_privado' => $request->es_privado ?? false,
                'reglas' => $request->reglas,
                'creador_id' => $user->id,
                'es_activo' => true
            ]);
            
            // Agregar al creador como administrador
            ClubMember::create([
                'club_id' => $club->id,
                'usuario_id' => $user->id,
                'rol' => 'admin',
                'estado' => 'activo',
                'fecha_union' => now()
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Club creado exitosamente'
        ]);
    }

    /**
     * Unirse a un club
     */
    public function join($id)
    {
        $club = ReadingClub::findOrFail($id);
        $user = Auth::user();
        
        // Verificar si ya es miembro
        if ($this->isUserMember($club)) {
            return response()->json([
                'success' => false,
                'message' => 'Ya eres miembro de este club'
            ]);
        }
        
        // Verificar si está pendiente
        if ($this->isUserPending($club)) {
            return response()->json([
                'success' => false,
                'message' => 'Ya tienes una solicitud pendiente'
            ]);
        }
        
        // Verificar si el club está lleno
        if ($club->members()->count() >= $club->max_miembros) {
            return response()->json([
                'success' => false,
                'message' => 'El club está completo'
            ]);
        }
        
        // Crear solicitud de membresía
        ClubMember::create([
            'club_id' => $club->id,
            'usuario_id' => $user->id,
            'rol' => 'miembro',
            'estado' => $club->es_privado ? 'pendiente' : 'activo',
            'fecha_union' => now()
        ]);
        
        $message = $club->es_privado 
            ? 'Solicitud enviada. Espera la aprobación del administrador.'
            : 'Te has unido exitosamente al club';
        
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Salir de un club
     */
    public function leave($id)
    {
        $club = ReadingClub::findOrFail($id);
        $user = Auth::user();
        
        $member = $club->members()
            ->where('usuario_id', $user->id)
            ->first();
        
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'No eres miembro de este club'
            ]);
        }
        
        // No permitir que el último administrador salga
        if ($member->rol === 'admin' && $club->members()->where('rol', 'admin')->count() === 1) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes salir siendo el único administrador'
            ]);
        }
        
        $member->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Has salido del club exitosamente'
        ]);
    }

    /**
     * Gestionar club (solo administradores)
     */
    public function manage($id)
    {
        $club = ReadingClub::with(['members.user', 'books.libro', 'discussions.user'])
            ->findOrFail($id);
        
        $user = Auth::user();
        
        if (!$this->isUserAdmin($club, $user)) {
            abort(403, 'No tienes permisos para gestionar este club');
        }
        
        // Obtener solicitudes pendientes
        $pending_requests = $club->members()
            ->with('user')
            ->where('estado', 'pendiente')
            ->get();
        
        // Obtener estadísticas del club
        $stats = [
            'total_members' => $club->members()->count(),
            'active_members' => $club->members()->where('estado', 'activo')->count(),
            'pending_requests' => $pending_requests->count(),
            'total_discussions' => $club->discussions()->count(),
            'books_read' => $club->books()->where('estado', 'completado')->count(),
            'current_book' => $club->currentBook
        ];
        
        return view('books.club-manage', compact('club', 'stats', 'pending_requests'));
    }

    /**
     * Aprobar solicitud de membresía
     */
    public function approveMember(Request $request, $club_id, $member_id)
    {
        $club = ReadingClub::findOrFail($club_id);
        $user = Auth::user();
        
        if (!$this->isUserAdmin($club, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ]);
        }
        
        $member = ClubMember::where('club_id', $club_id)
            ->where('id', $member_id)
            ->where('estado', 'pendiente')
            ->first();
        
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Solicitud no encontrada'
            ]);
        }
        
        $member->update([
            'estado' => 'activo',
            'fecha_aprobacion' => now()
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Solicitud aprobada exitosamente'
        ]);
    }

    /**
     * Rechazar solicitud de membresía
     */
    public function rejectMember(Request $request, $club_id, $member_id)
    {
        $club = ReadingClub::findOrFail($club_id);
        $user = Auth::user();
        
        if (!$this->isUserAdmin($club, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para realizar esta acción'
            ]);
        }
        
        $member = ClubMember::where('club_id', $club_id)
            ->where('id', $member_id)
            ->where('estado', 'pendiente')
            ->first();
        
        if (!$member) {
            return response()->json([
                'success' => false,
                'message' => 'Solicitud no encontrada'
            ]);
        }
        
        $member->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Solicitud rechazada'
        ]);
    }

    /**
     * Crear discusión
     */
    public function createDiscussion(Request $request, $club_id)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'contenido' => 'required|string|max:2000'
        ]);
        
        $club = ReadingClub::findOrFail($club_id);
        $user = Auth::user();
        
        if (!$this->isUserMember($club)) {
            return response()->json([
                'success' => false,
                'message' => 'Debes ser miembro del club para crear discusiones'
            ]);
        }
        
        ClubDiscussion::create([
            'club_id' => $club_id,
            'usuario_id' => $user->id,
            'titulo' => $request->titulo,
            'contenido' => $request->contenido
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Discusión creada exitosamente'
        ]);
    }

    /**
     * Agregar libro al club
     */
    public function addBook(Request $request, $club_id)
    {
        $request->validate([
            'libro_id' => 'required|exists:libros,id',
            'fecha_inicio' => 'required|date|after:today',
            'fecha_fin' => 'required|date|after:fecha_inicio'
        ]);
        
        $club = ReadingClub::findOrFail($club_id);
        $user = Auth::user();
        
        if (!$this->isUserAdmin($club, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'Solo los administradores pueden agregar libros'
            ]);
        }
        
        // Finalizar libro actual si existe
        if ($club->currentBook) {
            $club->currentBook->update([
                'estado' => 'completado',
                'fecha_fin' => now()
            ]);
        }
        
        // Agregar nuevo libro
        ClubBook::create([
            'club_id' => $club_id,
            'libro_id' => $request->libro_id,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin' => $request->fecha_fin,
            'estado' => 'activo'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Libro agregado al club exitosamente'
        ]);
    }

    /**
     * Obtener clubes por categoría
     */
    public function byCategory($categoria_id)
    {
        $categoria = Categoria::findOrFail($categoria_id);
        
        $clubs = ReadingClub::with(['categoria', 'members', 'discussions', 'books'])
            ->withCount(['members as miembros_count', 'discussions as discusiones_count', 'books as libros_leidos_count'])
            ->where('categoria_id', $categoria_id)
            ->where('es_activo', true)
            ->orderBy('miembros_count', 'desc')
            ->paginate(12)
            ->through(function ($club) {
                $club->is_member = $this->isUserMember($club);
                $club->is_pending = $this->isUserPending($club);
                return $club;
            });
        
        return view('books.clubs-by-category', compact('categoria', 'clubs'));
    }

    /**
     * Buscar clubes
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $categoria_id = $request->get('categoria_id');
        $estado = $request->get('estado');
        
        $clubs = ReadingClub::with(['categoria', 'members', 'discussions', 'books'])
            ->withCount(['members as miembros_count', 'discussions as discusiones_count', 'books as libros_leidos_count'])
            ->where('es_activo', true);
        
        if ($query) {
            $clubs->where(function ($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                  ->orWhere('descripcion', 'like', "%{$query}%");
            });
        }
        
        if ($categoria_id) {
            $clubs->where('categoria_id', $categoria_id);
        }
        
        if ($estado) {
            switch ($estado) {
                case 'active':
                    $clubs->where('es_activo', true);
                    break;
                case 'inactive':
                    $clubs->where('es_activo', false);
                    break;
                case 'full':
                    $clubs->whereRaw('(SELECT COUNT(*) FROM club_members WHERE club_members.club_id = reading_clubs.id) >= max_miembros');
                    break;
            }
        }
        
        $clubs = $clubs->orderBy('created_at', 'desc')
            ->paginate(12)
            ->through(function ($club) {
                $club->is_member = $this->isUserMember($club);
                $club->is_pending = $this->isUserPending($club);
                return $club;
            });
        
        return response()->json([
            'success' => true,
            'data' => $clubs
        ]);
    }
} 