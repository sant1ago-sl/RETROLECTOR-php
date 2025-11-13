<div class="row g-4">
    <div class="col-md-8">
        <div class="card mb-4 p-4">
            <div class="mb-4 help-text">Modifica la información del libro y visualiza cómo se verá en el catálogo.</div>
            <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" class="form-control" id="titulo" name="titulo" value="{{ old('titulo', $libro->titulo ?? '') }}" required>
            </div>
            <div class="row g-3">
                <div class="col-md-6 mb-3">
                    <label for="autor_id" class="form-label">Autor</label>
                    <div class="mb-3 d-flex align-items-center gap-2">
                        <label for="autor_id" class="form-label mb-0">Autor</label>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="refreshAutoresBtn" title="Actualizar lista de autores">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <select class="form-select" id="autor_id" name="autor_id" required>
                        <option value="">Seleccione un autor</option>
                        @foreach($autores as $autor)
                            <option value="{{ $autor->id }}" {{ old('autor_id', $libro->autor_id ?? '') == $autor->id ? 'selected' : '' }}>{{ $autor->nombre }} {{ $autor->apellido }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="categoria_id" class="form-label">Categoría</label>
                    <select class="form-select" id="categoria_id" name="categoria_id" required>
                        <option value="">Seleccione una categoría</option>
                        @foreach($categorias as $categoria)
                            <option value="{{ $categoria->id }}" {{ old('categoria_id', $libro->categoria_id ?? '') == $categoria->id ? 'selected' : '' }}>{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="sinopsis" class="form-label">Sinopsis</label>
                <textarea class="form-control" id="sinopsis" name="sinopsis" rows="3">{{ old('sinopsis', $libro->sinopsis ?? '') }}</textarea>
            </div>
            <div class="mb-3">
                <label for="contenido" class="form-label">Contenido completo del libro</label>
                <textarea class="form-control" id="contenido" name="contenido" rows="6">{{ old('contenido', $libro->contenido ?? '') }}</textarea>
            </div>
            <div class="row g-3">
                <div class="col-md-4 mb-3">
                    <label for="anio_publicacion" class="form-label">Año</label>
                    <input type="number" class="form-control" id="anio_publicacion" name="anio_publicacion" value="{{ old('anio_publicacion', $libro->anio_publicacion ?? '') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="editorial" class="form-label">Editorial</label>
                    <input type="text" class="form-control" id="editorial" name="editorial" value="{{ old('editorial', $libro->editorial ?? '') }}">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="paginas" class="form-label">Páginas</label>
                    <input type="number" class="form-control" id="paginas" name="paginas" value="{{ old('paginas', $libro->paginas ?? '') }}">
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-6 mb-3">
                    <label for="idioma" class="form-label">Idioma</label>
                    <input type="text" class="form-control" id="idioma" name="idioma" value="{{ old('idioma', $libro->idioma ?? 'Español') }}">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="stock" class="form-label">Stock</label>
                    <input type="number" class="form-control" id="stock" name="stock" value="{{ old('stock', $libro->stock ?? 1) }}" min="0">
                </div>
            </div>
            <div class="row g-3">
                <div class="col-md-3 mb-3">
                    <label for="precio_compra_fisica" class="form-label">Compra Física</label>
                    <input type="number" step="0.01" class="form-control" id="precio_compra_fisica" name="precio_compra_fisica" value="{{ old('precio_compra_fisica', $libro->precio_compra_fisica ?? 0) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="precio_compra_online" class="form-label">Compra Online</label>
                    <input type="number" step="0.01" class="form-control" id="precio_compra_online" name="precio_compra_online" value="{{ old('precio_compra_online', $libro->precio_compra_online ?? 0) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="precio_prestamo_fisico" class="form-label">Préstamo Físico</label>
                    <input type="number" step="0.01" class="form-control" id="precio_prestamo_fisico" name="precio_prestamo_fisico" value="{{ old('precio_prestamo_fisico', $libro->precio_prestamo_fisico ?? 0) }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="precio_prestamo_online" class="form-label">Préstamo Online</label>
                    <input type="number" step="0.01" class="form-control" id="precio_prestamo_online" name="precio_prestamo_online" value="{{ old('precio_prestamo_online', $libro->precio_prestamo_online ?? 0) }}">
                </div>
            </div>
            <div class="row g-3 align-items-end">
                <div class="col-md-6 mb-3">
                    <label for="imagen_portada" class="form-label">Portada</label>
                    <input type="file" class="form-control" id="imagen_portada" name="imagen_portada" accept="image/*">
                    @if(isset($libro) && $libro->imagen_portada)
                        <img src="{{ asset('storage/' . $libro->imagen_portada) }}" alt="Portada actual" class="mt-2 rounded border" style="width: 100px; height: 140px; object-fit: cover;">
                    @endif
                </div>
                <div class="col-md-6 mb-3">
                    <label for="archivo_pdf" class="form-label">Archivo PDF</label>
                    <input type="file" class="form-control" id="archivo_pdf" name="archivo_pdf" accept="application/pdf">
                    @if(isset($libro) && $libro->archivo_pdf)
                        <a href="{{ asset('storage/' . $libro->archivo_pdf) }}" target="_blank" class="d-block mt-2">Ver PDF actual</a>
                    @endif
                </div>
            </div>
            <div class="mb-3">
                <label for="preview_limit" class="form-label">Límite de vista previa (páginas)</label>
                <input type="number" class="form-control" id="preview_limit" name="preview_limit" value="{{ old('preview_limit', $libro->preview_limit ?? 10) }}" min="1">
            </div>
            <div class="mb-3">
                <label for="descripcion_vista_previa" class="form-label">Descripción para vista previa gratuita</label>
                <textarea class="form-control" id="descripcion_vista_previa" name="descripcion_vista_previa" rows="4" placeholder="Escribe aquí una introducción, sinopsis o fragmento que quieras mostrar como vista previa gratuita...">{{ old('descripcion_vista_previa', $libro->descripcion_vista_previa ?? '') }}</textarea>
                <small class="form-text text-muted">Este texto será visible para todos los usuarios como vista previa gratuita y puede ser copiado/pegado.</small>
            </div>
            <div class="mb-3">
                <label class="form-label">Disponibilidad</label>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="disponible_fisico" id="disponible_fisico" value="1" {{ old('disponible_fisico', ($libro->precio_compra_fisica ?? 0) > 0 || ($libro->precio_prestamo_fisico ?? 0) > 0 ? 'checked' : '') }}>
                    <label class="form-check-label" for="disponible_fisico">Físico</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="disponible_online" id="disponible_online" value="1" {{ old('disponible_online', ($libro->precio_compra_online ?? 0) > 0 || ($libro->precio_prestamo_online ?? 0) > 0 ? 'checked' : '') }}>
                    <label class="form-check-label" for="disponible_online">Online (PDF)</label>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2">{{ $modo == 'editar' ? 'Actualizar libro' : 'Agregar libro' }}</button>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Previsualización de portada en tiempo real
    const portadaInput = document.getElementById('imagen_portada');
    const portadaPreview = document.getElementById('preview_portada_form');
    if (portadaInput && portadaPreview) {
        portadaInput.addEventListener('change', function(e) {
            const [file] = e.target.files;
            if (file) {
                portadaPreview.src = URL.createObjectURL(file);
            }
        });
    }
    // Previsualización de precios
    ['precio_compra_fisica', 'precio_compra_online', 'precio_prestamo_fisico', 'precio_prestamo_online'].forEach(function(id) {
        const input = document.getElementById(id);
        const preview = document.getElementById('preview_' + id);
        if (input && preview) {
            input.addEventListener('input', function() {
                preview.textContent = this.value ? 'S/ ' + parseFloat(this.value).toFixed(2) : 'S/ 0.00';
            });
        }
    });

    const refreshBtn = document.getElementById('refreshAutoresBtn');
    const autorSelect = document.getElementById('autor_id');
    if (refreshBtn && autorSelect) {
        refreshBtn.addEventListener('click', function() {
            refreshBtn.disabled = true;
            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            fetch('/admin/authors/json')
                .then(res => res.json())
                .then(data => {
                    autorSelect.innerHTML = '<option value="">Seleccione un autor</option>';
                    data.forEach(function(autor) {
                        const opt = document.createElement('option');
                        opt.value = autor.id;
                        opt.textContent = autor.nombre;
                        autorSelect.appendChild(opt);
                    });
                })
                .finally(() => {
                    refreshBtn.disabled = false;
                    refreshBtn.innerHTML = '<i class="fas fa-sync-alt"></i>';
                });
        });
    }
});
</script> 