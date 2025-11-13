@extends('layouts.app')

@section('title', 'Agregar Nuevo Libro')

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
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('admin.books.index') }}" class="btn btn-outline-primary btn-lg">
            <i class="fas fa-list me-2"></i>Gestionar libros
        </a>
    </div>
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="book-form-card">
                <h2 class="mb-3"><i class="fas fa-plus-circle me-2"></i>Agregar Nuevo Libro</h2>
                <div class="help-text mb-3">Completa la información y visualiza cómo se verá el libro en el catálogo.</div>
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
                <form action="{{ route('admin.books.store') }}" method="POST" enctype="multipart/form-data" id="formLibro">
                    @csrf
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="form-section-title">Datos principales</div>
                        </div>
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="titulo" class="form-label"><i class="fas fa-book me-1"></i> Título *</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" value="{{ old('titulo') }}" required placeholder="Ej: El nombre del viento">
                            </div>
                            <div class="mb-3">
                                <label for="autor_id" class="form-label"><i class="fas fa-user-edit me-1"></i> Autor *</label>
                                <select class="form-select" id="autor_id" name="autor_id" required>
                                    <option value="">Selecciona un autor</option>
                                    @foreach($autores as $autor)
                                        <option value="{{ $autor->id }}" {{ old('autor_id') == $autor->id ? 'selected' : '' }}>{{ $autor->nombre }} {{ $autor->apellido }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="categoria_id" class="form-label"><i class="fas fa-tag me-1"></i> Categoría *</label>
                                <select class="form-select" id="categoria_id" name="categoria_id" required>
                                    <option value="">Selecciona una categoría</option>
                                    @foreach($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" {{ old('categoria_id') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="anio_publicacion" class="form-label"><i class="fas fa-calendar-alt me-1"></i> Año</label>
                                <input type="number" class="form-control" id="anio_publicacion" name="anio_publicacion" value="{{ old('anio_publicacion') }}" min="1800" max="{{ date('Y') + 1 }}" placeholder="Ej: 2020">
                            </div>
                            <div class="mb-3">
                                <label for="editorial" class="form-label"><i class="fas fa-building me-1"></i> Editorial</label>
                                <input type="text" class="form-control" id="editorial" name="editorial" value="{{ old('editorial') }}" placeholder="Ej: Plaza & Janés">
                            </div>
                            <div class="mb-3">
                                <label for="paginas" class="form-label"><i class="fas fa-file-alt me-1"></i> Páginas</label>
                                <input type="number" class="form-control" id="paginas" name="paginas" value="{{ old('paginas') }}" min="1" placeholder="Ej: 500">
                            </div>
                            <div class="mb-3">
                                <label for="idioma" class="form-label"><i class="fas fa-language me-1"></i> Idioma</label>
                                <input type="text" class="form-control" id="idioma" name="idioma" value="{{ old('idioma', 'Español') }}" placeholder="Ej: Español">
                            </div>
                            <div class="mb-3">
                                <label for="isbn" class="form-label"><i class="fas fa-barcode me-1"></i> ISBN</label>
                                <input type="text" class="form-control" id="isbn" name="isbn" value="{{ old('isbn') }}" placeholder="Ej: 978-84-123456-7-8">
                        </div>
                            <div class="mb-3">
                                <label for="ubicacion" class="form-label"><i class="fas fa-map-marker-alt me-1"></i> Ubicación</label>
                                <input type="text" class="form-control" id="ubicacion" name="ubicacion" value="{{ old('ubicacion') }}" placeholder="Ej: Estante A, Nivel 2">
                            </div>
                            <div class="mb-3">
                                <label for="descripcion" class="form-label"><i class="fas fa-align-left me-1"></i> Sinopsis</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Breve descripción del libro">{{ old('descripcion') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="form-section-title">Portada</div>
                            <img id="previewPortada" class="preview-portada" src="{{ asset('images/portada_default.png') }}" alt="Portada libro">
                            <input type="file" class="form-control mt-2" id="imagen_portada" name="imagen_portada" accept="image/*">
                            <div class="help-text">Formatos: JPG, PNG, GIF. Máx 2MB.</div>
                        </div>
                        <div class="col-12 mt-4">
                            <div class="form-section-title">Precios</div>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="precio_compra_fisica" class="form-label"><i class="fas fa-shopping-cart me-1"></i> Compra Física *</label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                        <input type="number" class="form-control" id="precio_compra_fisica" name="precio_compra_fisica" value="{{ old('precio_compra_fisica') }}" step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="precio_compra_online" class="form-label"><i class="fas fa-globe me-1"></i> Compra Online *</label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                        <input type="number" class="form-control" id="precio_compra_online" name="precio_compra_online" value="{{ old('precio_compra_online') }}" step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="precio_prestamo_fisico" class="form-label"><i class="fas fa-handshake me-1"></i> Préstamo Físico *</label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                        <input type="number" class="form-control" id="precio_prestamo_fisico" name="precio_prestamo_fisico" value="{{ old('precio_prestamo_fisico') }}" step="0.01" min="0" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="precio_prestamo_online" class="form-label"><i class="fas fa-laptop me-1"></i> Préstamo Online *</label>
                                <div class="input-group">
                                    <span class="input-group-text">S/</span>
                                        <input type="number" class="form-control" id="precio_prestamo_online" name="precio_prestamo_online" value="{{ old('precio_prestamo_online') }}" step="0.01" min="0" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <div class="form-section-title">Archivos</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="archivo_pdf" class="form-label"><i class="fas fa-file-pdf me-1"></i> PDF (opcional)</label>
                                    <input type="file" class="form-control" id="archivo_pdf" name="archivo_pdf" accept="application/pdf">
                                    <div class="help-text">Solo PDF. Máx 20MB.</div>
                                </div>
                                <div class="col-md-6">
                                    <label for="stock" class="form-label"><i class="fas fa-boxes me-1"></i> Stock *</label>
                                    <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock', 1) }}" min="0" required>
                                </div>
                        </div>
                        </div>
                        <div class="col-12 mt-4">
                            <div class="form-section-title">Contenido y opciones</div>
                        <div class="mb-3">
                                <label for="contenido" class="form-label"><i class="fas fa-file-alt me-1"></i> Contenido *</label>
                                <textarea class="form-control" id="contenido" name="contenido" rows="6" placeholder="Pega o escribe aquí el texto completo del libro..." required>{{ old('contenido') }}</textarea>
                        </div>
                        <div class="mb-3">
                                <label for="preview_limit" class="form-label"><i class="fas fa-eye me-1"></i> Límite de Vista Previa (caracteres) *</label>
                                <input type="number" class="form-control" id="preview_limit" name="preview_limit" value="{{ old('preview_limit', 1000) }}" min="100" max="100000" required>
                                <div class="form-text">Número de caracteres que se mostrarán en la vista previa gratuita</div>
                            </div>
                            <div class="mb-3">
                                <label for="estado" class="form-label"><i class="fas fa-toggle-on me-1"></i> Estado *</label>
                                <select class="form-select" id="estado" name="estado" required>
                                    <option value="disponible" {{ old('estado') == 'disponible' ? 'selected' : '' }}>Disponible</option>
                                    <option value="en_reparacion" {{ old('estado') == 'en_reparacion' ? 'selected' : '' }}>En Reparación</option>
                                    <option value="perdido" {{ old('estado') == 'perdido' ? 'selected' : '' }}>Perdido</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.books.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Guardar Libro
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="book-preview-box mb-4">
                <h4 class="book-preview-title mb-3">
                    <i class="fas fa-eye me-2"></i>Vista Previa Gratuita
                </h4>
                <div class="text-center mb-3">
                    <img id="previewPortadaVista" class="preview-portada" 
                         src="{{ asset('images/portada_default.png') }}" 
                         alt="Portada">
                </div>
                <div class="book-preview-meta">
                    <h5 id="previewTitulo">Vista previa del título</h5>
                    <p id="previewAutor">Por: <span id="previewAutorNombre">Autor</span></p>
                    <p id="previewCategoria">Categoría: <span id="previewCategoriaNombre">Categoría</span></p>
            </div>
                <div class="book-preview-prices mb-2">
                    <div class="price-card">
                        <strong>Compra Física:</strong><br>
                        <span id="previewPrecioCompraFisica">S/ 0.00</span>
                    </div>
                    <div class="price-card">
                        <strong>Compra Online:</strong><br>
                        <span id="previewPrecioCompraOnline">S/ 0.00</span>
                    </div>
                    <div class="price-card">
                        <strong>Préstamo Físico:</strong><br>
                        <span id="previewPrecioPrestamoFisico">S/ 0.00</span>
                    </div>
                    <div class="price-card">
                        <strong>Préstamo Online:</strong><br>
                        <span id="previewPrecioPrestamoOnline">S/ 0.00</span>
                    </div>
                </div>
                <div class="alert alert-warning mb-2">
                    <i class="fas fa-eye me-2"></i>
                    Solo se mostrará el siguiente contenido como vista previa gratuita:
                    </div>
                <div class="border rounded p-3 bg-light mb-2" style="min-height: 120px; max-height: 300px; overflow-y: auto;">
                    <div id="previewContenidoLimitado" class="text-muted" style="white-space: pre-line;"></div>
                        </div>
                <div class="form-group mb-0">
                    <label for="preview_limit" class="form-label">Límite de caracteres para la vista previa gratuita:</label>
                    <input type="number" class="form-control" id="preview_limit" name="preview_limit" value="500" min="100" max="100000">
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    // Previsualización de portada en tiempo real
    document.getElementById('imagen_portada').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(evt) {
                document.getElementById('previewPortada').src = evt.target.result;
                document.getElementById('previewPortadaLive').src = evt.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            document.getElementById('previewPortada').src = "{{ asset('images/portada_default.png') }}";
            document.getElementById('previewPortadaLive').src = "{{ asset('images/portada_default.png') }}";
        }
    });
    // Actualización en tiempo real de la previsualización
    function updatePreview() {
        document.getElementById('previewTitulo').textContent = document.getElementById('titulo').value || 'Título de ejemplo';
        let autor = document.getElementById('autor_id');
        document.getElementById('previewAutor').innerHTML = '<i class="fas fa-user me-1"></i>' + (autor.options[autor.selectedIndex]?.text || 'Autor');
        let categoria = document.getElementById('categoria_id');
        document.getElementById('previewCategoria').innerHTML = '<i class="fas fa-tag me-1"></i>' + (categoria.options[categoria.selectedIndex]?.text || 'Categoría');
        document.getElementById('previewEstado').textContent = document.getElementById('estado').options[document.getElementById('estado').selectedIndex]?.text || 'Disponible';
        document.getElementById('previewAnio').textContent = document.getElementById('anio_publicacion').value || '2024';
        document.getElementById('previewIdioma').textContent = document.getElementById('idioma').value || 'Español';
        document.getElementById('previewISBN').textContent = document.getElementById('isbn').value || 'ISBN';
        document.getElementById('previewPaginas').textContent = (document.getElementById('paginas').value || '0') + ' páginas';
        // Precios
        let pf = parseFloat(document.getElementById('precio_compra_fisica').value);
        document.getElementById('previewPrecioFisico').textContent = pf > 0 ? 'S/ ' + pf.toFixed(2) : 'No disponible';
        let po = parseFloat(document.getElementById('precio_compra_online').value);
        document.getElementById('previewPrecioOnline').textContent = po > 0 ? 'S/ ' + po.toFixed(2) : 'No disponible';
        let ppf = parseFloat(document.getElementById('precio_prestamo_fisico').value);
        document.getElementById('previewPrestamoFisico').textContent = ppf > 0 ? 'S/ ' + ppf.toFixed(2) : 'No disponible';
        let ppo = parseFloat(document.getElementById('precio_prestamo_online').value);
        document.getElementById('previewPrestamoOnline').textContent = ppo > 0 ? 'S/ ' + ppo.toFixed(2) : 'No disponible';
        document.getElementById('previewUbicacion').innerHTML = '<i class="fas fa-map-marker-alt me-1"></i>Ubicación: ' + (document.getElementById('ubicacion').value || '-');
        document.getElementById('previewEditorial').innerHTML = '<i class="fas fa-building me-1"></i>Editorial: ' + (document.getElementById('editorial').value || '-');
        document.getElementById('previewSinopsis').innerHTML = '<i class="fas fa-align-left me-1"></i>Sinopsis: ' + (document.getElementById('descripcion').value || '-');
    }
    [
        'titulo','autor_id','categoria_id','anio_publicacion','editorial','paginas','idioma','isbn','ubicacion','descripcion',
        'precio_compra_fisica','precio_compra_online','precio_prestamo_fisico','precio_prestamo_online','estado'
    ].forEach(function(id){
        document.getElementById(id).addEventListener('input', updatePreview);
        document.getElementById(id).addEventListener('change', updatePreview);
    });
    updatePreview();

    // Vista previa gratuita en tiempo real
    function updatePreviewGratis() {
        document.getElementById('previewTitulo').textContent = document.getElementById('titulo').value || 'Vista previa del título';
        let autorSelect = document.getElementById('autor_id');
        let autorText = autorSelect.options[autorSelect.selectedIndex]?.text || 'Autor';
        document.getElementById('previewAutorNombre').textContent = autorText;
        let categoriaSelect = document.getElementById('categoria_id');
        let categoriaText = categoriaSelect.options[categoriaSelect.selectedIndex]?.text || 'Categoría';
        document.getElementById('previewCategoriaNombre').textContent = categoriaText;
        document.getElementById('previewPrecioCompraFisica').textContent = 'S/ ' + parseFloat(document.getElementById('precio_compra_fisica').value || 0).toFixed(2);
        document.getElementById('previewPrecioCompraOnline').textContent = 'S/ ' + parseFloat(document.getElementById('precio_compra_online').value || 0).toFixed(2);
        document.getElementById('previewPrecioPrestamoFisico').textContent = 'S/ ' + parseFloat(document.getElementById('precio_prestamo_fisico').value || 0).toFixed(2);
        document.getElementById('previewPrecioPrestamoOnline').textContent = 'S/ ' + parseFloat(document.getElementById('precio_prestamo_online').value || 0).toFixed(2);
        let contenido = document.getElementById('contenido').value || '';
        let limit = parseInt(document.getElementById('preview_limit').value || 500);
        document.getElementById('previewContenidoLimitado').textContent = contenido.substring(0, limit);
    }
    document.getElementById('titulo').addEventListener('input', updatePreviewGratis);
    document.getElementById('autor_id').addEventListener('change', updatePreviewGratis);
    document.getElementById('categoria_id').addEventListener('change', updatePreviewGratis);
    document.getElementById('precio_compra_fisica').addEventListener('input', updatePreviewGratis);
    document.getElementById('precio_compra_online').addEventListener('input', updatePreviewGratis);
    document.getElementById('precio_prestamo_fisico').addEventListener('input', updatePreviewGratis);
    document.getElementById('precio_prestamo_online').addEventListener('input', updatePreviewGratis);
    document.getElementById('contenido').addEventListener('input', updatePreviewGratis);
    document.getElementById('preview_limit').addEventListener('input', updatePreviewGratis);
    // Portada en tiempo real
    const portadaInput = document.getElementById('imagen_portada');
    portadaInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('previewPortadaVista').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
    // Inicializar preview
    updatePreviewGratis();
</script>
@endpush
@endsection 