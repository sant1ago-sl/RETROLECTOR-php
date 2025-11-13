@extends('layouts.app')

@section('title', 'Notificaciones')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white d-flex align-items-center justify-content-between">
                    <h4 class="mb-0"><i class="fas fa-bell me-2"></i>Historial de Notificaciones</h4>
                </div>
                <div class="card-body">
                    @include('user.partials.notifications-list', ['notificaciones' => $notificaciones])
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function refreshNotifications() {
        fetch(window.location.href + '?ajax=1')
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newList = doc.querySelector('.list-group');
                if (newList) {
                    document.querySelector('.list-group').innerHTML = newList.innerHTML;
                }
            });
    }
    setInterval(refreshNotifications, 30000);
</script>
@endsection 