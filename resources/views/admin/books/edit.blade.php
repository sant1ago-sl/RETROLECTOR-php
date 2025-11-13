@extends('layouts.app')

@section('title', 'Editar Libro')

@section('content')
<style>
    .book-form-card {
        background: #fff8f0;
        border-radius: 1rem;
        box-shadow: 0 2px 16px rgba(124,94,60,0.08);
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
    }
    .preview-portada {
        width: 140px;
        height: 200px;
        object-fit: cover;
        border-radius: 0.5rem;
        box-shadow: 0 2px 8px rgba(124,94,60,0.12);
        background: #f7f3ee;
        display: block;
        margin-bottom: 1rem;
    }
    .form-section-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #7c5e3c;
        margin-bottom: 1rem;
    }
    .form-label {
        color: #7c5e3c;
        font-weight: 500;
    }
    .input-group-text {
        background: #ffe9b0;
        color: #7c5e3c;
        font-weight: 600;
    }
    .help-text {
        font-size: 0.95em;
        color: #bfa77a;
    }
    .book-preview-box {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 2px 16px rgba(124,94,60,0.10);
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    .book-preview-title {
        font-size: 1.5rem;
        font-weight: bold;
        color: #4b3a1e;
    }
    .book-preview-meta {
        font-size: 1rem;
        color: #7c5e3c;
        margin-bottom: 0.5rem;
    }
    .book-preview-prices .price-card {
        background: #f7f3ee;
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        margin-bottom: 0.5rem;
        display: inline-block;
        min-width: 120px;
        text-align: center;
    }
    .book-preview-badges .badge {
        margin-right: 0.5rem;
    }
    @media (max-width: 991px) {
        .book-form-card { padding: 1rem; }
    }
</style>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="fas fa-edit me-2"></i>Editar Libro: {{ $libro->titulo }}</h2>
        <a href="{{ route('admin.books.index') }}" class="btn btn-outline-primary btn-lg">
            <i class="fas fa-list me-2"></i>Gestionar libros
        </a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="book-form-card">
                <div class="help-text mb-3">Modifica la información del libro y visualiza cómo se verá en el catálogo.</div>
                @if(session('success'))
                    <div class="toast align-items-center text-bg-success border-0 show position-fixed top-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 9999; min-width: 250px;">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                    </div>
                                </div>
                @endif
                @if($errors->any())
                    <div class="toast align-items-center text-bg-danger border-0 show position-fixed top-0 end-0 m-3" role="alert" aria-live="assertive" aria-atomic="true" style="z-index: 9999; min-width: 250px;">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="fas fa-exclamation-triangle me-2"></i>Ocurrió un error. Por favor, revisa el formulario.
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                                </div>
                                            </div>
                                        @endif
                @include('admin.books.partials.form', [
                    'libro' => $libro,
                    'autores' => $autores,
                    'categorias' => $categorias,
                    'modo' => 'editar',
                    'preview' => true
                ])
                                </div>
                            </div>

        <!-- Vista Previa -->
        <div class="col-lg-5">
            <div class="book-preview-box">
                <h4 class="book-preview-title mb-3">
                    <i class="fas fa-eye me-2"></i>Vista Previa
                </h4>
                <div class="text-center mb-3">
                    <img id="previewPortadaVista" class="preview-portada" 
                         src="{{ $libro->imagen_portada ? asset('storage/' . $libro->imagen_portada) : 'https://via.placeholder.com/140x200?text=Portada' }}" 
                         alt="Portada">
                                    </div>
                <div class="book-preview-meta">
                    <h5 id="previewTitulo">{{ $libro->titulo }}</h5>
                    <p id="previewAutor">Por: {{ $libro->autor->nombre ?? 'Autor' }} {{ $libro->autor->apellido ?? '' }}</p>
                    <p id="previewCategoria">Categoría: {{ $libro->categoria->nombre ?? 'Categoría' }}</p>
                    <p id="previewAnio">Año: {{ $libro->anio_publicacion ?? 'N/A' }}</p>
                    <p id="previewEditorial">Editorial: {{ $libro->editorial ?? 'N/A' }}</p>
                                </div>
                <div class="book-preview-prices">
                    <div class="price-card">
                        <strong>Compra Física:</strong><br>
                        <span id="previewPrecioCompraFisica">S/ {{ number_format($libro->precio_compra_fisica ?? 0, 2) }}</span>
                                    </div>
                    <div class="price-card">
                        <strong>Compra Online:</strong><br>
                        <span id="previewPrecioCompraOnline">S/ {{ number_format($libro->precio_compra_online ?? 0, 2) }}</span>
                                </div>
                    <div class="price-card">
                        <strong>Préstamo Físico:</strong><br>
                        <span id="previewPrecioPrestamoFisico">S/ {{ number_format($libro->precio_prestamo_fisico ?? 0, 2) }}</span>
                            </div>
                    <div class="price-card">
                        <strong>Préstamo Online:</strong><br>
                        <span id="previewPrecioPrestamoOnline">S/ {{ number_format($libro->precio_prestamo_online ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                <div class="book-preview-badges mt-3">
                    <span class="badge bg-primary" id="previewStock">Stock: {{ $libro->stock ?? 0 }}</span>
                    <span class="badge bg-secondary" id="previewEstado">{{ ucfirst($libro->estado ?? 'disponible') }}</span>
                    @if($libro->archivo_pdf)
                        <span class="badge bg-success">PDF Disponible</span>
                    @endif
                </div>
                <div class="mt-3">
                    <p id="previewDescripcion" class="text-muted">{{ $libro->descripcion ?? 'Sin descripción disponible.' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Vista previa de imagen
document.getElementById('imagen_portada').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('previewPortadaVista').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Actualización en tiempo real de la vista previa
document.addEventListener('DOMContentLoaded', function() {
    const inputs = ['titulo', 'autor_id', 'categoria_id', 'anio_publicacion', 'editorial', 'stock', 'estado', 'descripcion'];
    const priceInputs = ['precio_compra_fisica', 'precio_compra_online', 'precio_prestamo_fisico', 'precio_prestamo_online'];
    
    // Actualizar campos de texto
    inputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', updatePreview);
        }
    });
    
    // Actualizar precios
    priceInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', updatePrices);
        }
    });
    
    // Actualizar autor y categoría cuando cambien los selects
    document.getElementById('autor_id').addEventListener('change', updateAuthor);
    document.getElementById('categoria_id').addEventListener('change', updateCategory);
});

function updatePreview() {
    const titulo = document.getElementById('titulo').value || 'Título del libro';
    const anio = document.getElementById('anio_publicacion').value || 'N/A';
    const editorial = document.getElementById('editorial').value || 'N/A';
    const stock = document.getElementById('stock').value || 0;
    const estado = document.getElementById('estado').value || 'disponible';
    const descripcion = document.getElementById('descripcion').value || 'Sin descripción disponible.';
    
    document.getElementById('previewTitulo').textContent = titulo;
    document.getElementById('previewAnio').textContent = `Año: ${anio}`;
    document.getElementById('previewEditorial').textContent = `Editorial: ${editorial}`;
    document.getElementById('previewStock').textContent = `Stock: ${stock}`;
    document.getElementById('previewEstado').textContent = estado.charAt(0).toUpperCase() + estado.slice(1);
    document.getElementById('previewDescripcion').textContent = descripcion;
}

function updatePrices() {
    const compraFisica = document.getElementById('precio_compra_fisica').value || 0;
    const compraOnline = document.getElementById('precio_compra_online').value || 0;
    const prestamoFisico = document.getElementById('precio_prestamo_fisico').value || 0;
    const prestamoOnline = document.getElementById('precio_prestamo_online').value || 0;
    
    document.getElementById('previewPrecioCompraFisica').textContent = `S/ ${parseFloat(compraFisica).toFixed(2)}`;
    document.getElementById('previewPrecioCompraOnline').textContent = `S/ ${parseFloat(compraOnline).toFixed(2)}`;
    document.getElementById('previewPrecioPrestamoFisico').textContent = `S/ ${parseFloat(prestamoFisico).toFixed(2)}`;
    document.getElementById('previewPrecioPrestamoOnline').textContent = `S/ ${parseFloat(prestamoOnline).toFixed(2)}`;
}

function updateAuthor() {
    const autorSelect = document.getElementById('autor_id');
    const selectedOption = autorSelect.options[autorSelect.selectedIndex];
    const autorText = selectedOption.text || 'Autor';
    document.getElementById('previewAutor').textContent = `Por: ${autorText}`;
}

function updateCategory() {
    const categoriaSelect = document.getElementById('categoria_id');
    const selectedOption = categoriaSelect.options[categoriaSelect.selectedIndex];
    const categoriaText = selectedOption.text || 'Categoría';
    document.getElementById('previewCategoria').textContent = `Categoría: ${categoriaText}`;
}
</script>
@endpush 
@endsection 