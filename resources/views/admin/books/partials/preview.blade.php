<div class="card shadow-sm border-0">
    <div class="row g-0">
        <div class="col-4">
            @if(isset($libro) && $libro->imagen_portada)
                <img src="{{ asset('storage/' . $libro->imagen_portada) }}" alt="Portada" class="img-fluid rounded-start" style="height: 180px; object-fit: cover;">
            @else
                <div class="bg-light d-flex align-items-center justify-content-center rounded-start" style="height: 180px;">
                    <i class="fas fa-book fa-3x text-muted"></i>
                </div>
            @endif
        </div>
        <div class="col-8">
            <div class="card-body p-2">
                <h5 class="card-title mb-1">{{ $libro->titulo ?? 'Título del libro' }}</h5>
                <div class="mb-1 text-muted small">{{ $libro->autor->nombre ?? 'Autor' }} {{ $libro->autor->apellido ?? '' }}</div>
                <div class="mb-2">
                    @php
                        $isNuevo = isset($libro->created_at) && \Carbon\Carbon::parse($libro->created_at)->gt(now()->subDays(7));
                        $soloOnline = ($libro->precio_compra_fisica ?? 0) <= 0 && ($libro->precio_prestamo_fisico ?? 0) <= 0 && (($libro->precio_compra_online ?? 0) > 0 || ($libro->precio_prestamo_online ?? 0) > 0);
                        $soloFisico = ($libro->precio_compra_online ?? 0) <= 0 && ($libro->precio_prestamo_online ?? 0) <= 0 && (($libro->precio_compra_fisica ?? 0) > 0 || ($libro->precio_prestamo_fisico ?? 0) > 0);
                        $conPDF = !empty($libro->archivo_pdf);
                    @endphp
                    @if($isNuevo)
                        <span class="badge bg-success">Nuevo</span>
                    @endif
                    @if(($libro->stock ?? 1) <= 0)
                        <span class="badge bg-danger">Sin stock</span>
                    @endif
                    @if($soloOnline)
                        <span class="badge bg-info">Solo online</span>
                    @endif
                    @if($soloFisico)
                        <span class="badge bg-warning text-dark">Solo físico</span>
                    @endif
                    @if($conPDF)
                        <span class="badge bg-primary">Con PDF</span>
                    @endif
                </div>
                <div class="mb-2 small text-muted">{{ $libro->categoria->nombre ?? 'Categoría' }}</div>
                <div class="mb-2">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="bg-light rounded p-2 text-center">
                                <div>Compra Física:</div>
                                <span id="preview_precio_compra_fisica">S/ {{ isset($libro) && $libro->precio_compra_fisica ? number_format($libro->precio_compra_fisica, 2) : '0.00' }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded p-2 text-center">
                                <div>Compra Online:</div>
                                <span id="preview_precio_compra_online">S/ {{ isset($libro) && $libro->precio_compra_online ? number_format($libro->precio_compra_online, 2) : '0.00' }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded p-2 text-center">
                                <div>Préstamo Físico:</div>
                                <span id="preview_precio_prestamo_fisico">S/ {{ isset($libro) && $libro->precio_prestamo_fisico ? number_format($libro->precio_prestamo_fisico, 2) : '0.00' }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded p-2 text-center">
                                <div>Préstamo Online:</div>
                                <span id="preview_precio_prestamo_online">S/ {{ isset($libro) && $libro->precio_prestamo_online ? number_format($libro->precio_prestamo_online, 2) : '0.00' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <strong>Sinopsis:</strong>
                    <div class="small">{{ $libro->sinopsis ?? 'Sinopsis del libro...' }}</div>
                </div>
                <div class="mb-2">
                    <strong>Vista previa gratuita:</strong>
                    <div class="border rounded p-2 bg-light small" style="max-height: 120px; overflow-y: auto;">
                        @if(isset($libro) && $libro->descripcion_vista_previa)
                            {{ $libro->descripcion_vista_previa }}
                        @elseif(isset($libro) && $libro->contenido)
                            {{ \Illuminate\Support\Str::limit($libro->contenido, ($libro->preview_limit ?? 10) * 300, '...') }}
                        @else
                            <span class="text-muted">Aquí se mostrará la vista previa gratuita personalizada del libro.</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 