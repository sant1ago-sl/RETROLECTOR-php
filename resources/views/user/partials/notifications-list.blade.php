@if($notificaciones->count())
    <ul class="list-group list-group-flush">
        @foreach($notificaciones as $notificacion)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge bg-{{ $notificacion->tipo }} me-2">{{ ucfirst($notificacion->tipo) }}</span>
                    <strong>{{ $notificacion->titulo }}</strong>
                    <div class="text-muted small">{{ $notificacion->mensaje }}</div>
                    <div class="text-muted small">{{ $notificacion->created_at->format('d/m/Y H:i') }}</div>
                </div>
                @if(!$notificacion->leida)
                    <form method="POST" action="{{ route('user.notifications.mark-read', $notificacion->id) }}">
                        @csrf
                        <button class="btn btn-sm btn-outline-success">Marcar como leída</button>
                    </form>
                @endif
            </li>
        @endforeach
    </ul>
    <div class="mt-3">
        {{ $notificaciones->links() }}
    </div>
@else
    <div class="alert alert-info text-center mb-0">
        <i class="fas fa-info-circle me-2"></i>No tienes notificaciones aún.
    </div>
@endif 