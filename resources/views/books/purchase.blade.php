@extends('layouts.app')

@section('title', 'Comprar o Prestar ' . $libro->titulo . ' - Retrolector')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Información del libro -->
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center">
                            @if($libro->portada)
                                <img src="{{ asset('storage/' . $libro->portada) }}" alt="{{ $libro->titulo }}" class="img-fluid rounded" style="max-height: 200px;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 200px; width: 150px; margin: 0 auto;">
                                    <i class="fas fa-book fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <h3 class="mb-2">{{ $libro->titulo }}</h3>
                            <p class="text-muted mb-2">
                                <i class="fas fa-user me-2"></i>
                                {{ $libro->autor->nombre ?? 'Autor no especificado' }}
                            </p>
                            <p class="text-muted mb-2">
                                <i class="fas fa-tag me-2"></i>
                                {{ $libro->categoria->nombre ?? 'Sin categoría' }}
                            </p>
                            <p class="text-muted mb-0">
                                <i class="fas fa-eye me-2"></i>
                                {{ $libro->vistas ?? 0 }} vistas
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjetas de precios -->
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-primary text-white text-center">
                            <i class="fas fa-shopping-cart fa-2x mb-2"></i>
                            <h5 class="mb-0">Compra</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="row">
                                <div class="col-6">
                                    <div class="pricing-option mb-3">
                                        <h6 class="text-primary">Físico</h6>
                                        <div class="price-tag">
                                            <span class="currency">S/</span>
                                            <span class="amount">{{ number_format($libro->precio_compra_fisica ?? 0, 2) }}</span>
                                        </div>
                                        <small class="text-muted">Envío incluido</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="pricing-option mb-3">
                                        <h6 class="text-success">Online</h6>
                                        <div class="price-tag">
                                            <span class="currency">S/</span>
                                            <span class="amount">{{ $libro->archivo_pdf ? number_format($libro->precio_compra_online ?? 0, 2) : '--' }}</span>
                                        </div>
                                        @if(!$libro->archivo_pdf)
                                            <small class="text-danger">No disponible</small>
                                        @else
                                            <small class="text-muted">Acceso permanente</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-header bg-success text-white text-center">
                            <i class="fas fa-handshake fa-2x mb-2"></i>
                            <h5 class="mb-0">Préstamo</h5>
                        </div>
                        <div class="card-body text-center">
                            <div class="row">
                                <div class="col-6">
                                    <div class="pricing-option mb-3">
                                        <h6 class="text-primary">Físico</h6>
                                        <div class="price-tag">
                                            <span class="currency">S/</span>
                                            <span class="amount">{{ number_format($libro->precio_prestamo_fisico ?? 0, 2) }}</span>
                                        </div>
                                        <small class="text-muted">14 días</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="pricing-option mb-3">
                                        <h6 class="text-success">Online</h6>
                                        <div class="price-tag">
                                            <span class="currency">S/</span>
                                            <span class="amount">{{ $libro->archivo_pdf ? number_format($libro->precio_prestamo_online ?? 0, 2) : '--' }}</span>
                                        </div>
                                        @if(!$libro->archivo_pdf)
                                            <small class="text-danger">No disponible</small>
                                        @else
                                            <small class="text-muted">7 días</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de solicitud -->
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient-primary text-white text-center">
                    <i class="fas fa-edit fa-2x mb-2"></i>
                    <h4 class="mb-0">Solicitar Libro</h4>
                </div>
                <div class="card-body p-4">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <div class="alert alert-danger text-center mb-4">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Solo los clientes pueden solicitar libros. Inicia sesión como cliente para continuar.
                            </div>
                        @else
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <form id="purchaseForm" action="{{ route('books.process-purchase', $libro) }}" method="POST" autocomplete="off">
                                @csrf
                                
                                <!-- Tipo de transacción -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-tasks me-2"></i>¿Qué deseas hacer?
                                            </label>
                                            <select name="tipo_transaccion" id="tipoTransaccion" class="form-select form-select-lg @error('tipo_transaccion') is-invalid @enderror" required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="comprar" {{ old('tipo_transaccion') == 'comprar' ? 'selected' : '' }}>🛒 Comprar</option>
                                                <option value="prestar" {{ old('tipo_transaccion') == 'prestar' ? 'selected' : '' }}>🤝 Prestar</option>
                                            </select>
                                            @error('tipo_transaccion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-globe me-2"></i>Modalidad
                                            </label>
                                            <select name="modalidad" id="modalidad" class="form-select form-select-lg @error('modalidad') is-invalid @enderror" required>
                                                <option value="">Selecciona una opción</option>
                                                <option value="fisico" {{ old('modalidad') == 'fisico' ? 'selected' : '' }}>📦 Físico</option>
                                                <option value="online" {{ old('modalidad') == 'online' ? 'selected' : '' }}>💻 Online</option>
                                            </select>
                                            @error('modalidad')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Información del cliente -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-user me-2"></i>Nombre Completo
                                            </label>
                                            <input type="text" name="nombre" class="form-control form-control-lg @error('nombre') is-invalid @enderror" value="{{ old('nombre', auth()->user()->nombre ?? '') }}" required>
                                            @error('nombre')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-envelope me-2"></i>Email
                                            </label>
                                            <input type="email" name="email" class="form-control form-control-lg @error('email') is-invalid @enderror" value="{{ old('email', auth()->user()->email ?? '') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Campos condicionales según modalidad -->
                                <div id="camposFisico" class="mb-4" style="display:none;">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <i class="fas fa-map-marker-alt me-2"></i>Información de Entrega
                                        </div>
                                        <div class="card-body">
                                            <label class="form-label fw-bold">Dirección de Entrega</label>
                                            <textarea name="direccion" class="form-control @error('direccion') is-invalid @enderror" rows="3" placeholder="Dirección completa para entrega">{{ old('direccion') }}</textarea>
                                            @error('direccion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div id="camposOnline" class="mb-4" style="display:none;">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <i class="fas fa-laptop me-2"></i>Acceso Online
                                        </div>
                                        <div class="card-body">
                                            <div class="alert alert-info mb-0">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Lectura Online:</strong> Podrás leer el libro directamente en nuestra plataforma web con acceso ilimitado.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Método de Pago -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-credit-card me-2"></i>Método de Pago
                                    </label>
                                    <select name="metodo_pago" id="metodoPago" class="form-select form-select-lg @error('metodo_pago') is-invalid @enderror" required>
                                        <option value="">Selecciona método de pago</option>
                                        <option value="tarjeta" {{ old('metodo_pago') == 'tarjeta' ? 'selected' : '' }}>💳 Tarjeta de Crédito/Débito (S/)</option>
                                        <option value="paypal" {{ old('metodo_pago') == 'paypal' ? 'selected' : '' }}>🔗 PayPal</option>
                                        <option value="transferencia" {{ old('metodo_pago') == 'transferencia' ? 'selected' : '' }}>🏦 Transferencia Bancaria</option>
                                        <option value="yape" {{ old('metodo_pago') == 'yape' ? 'selected' : '' }}>📱 Otros (Yape/Plin)</option>
                                    </select>
                                    @error('metodo_pago')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Campos específicos de cada método de pago -->
                                <div id="pagoTarjeta" class="pago-metodo mb-4" style="display:none;">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <i class="fas fa-credit-card me-2"></i>Información de Tarjeta
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label class="form-label fw-bold">Número de Tarjeta</label>
                                                    <input type="text" name="tarjeta_numero" class="form-control @error('tarjeta_numero') is-invalid @enderror" maxlength="19" placeholder="0000 0000 0000 0000" value="{{ old('tarjeta_numero') }}">
                                                    @error('tarjeta_numero')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Vencimiento</label>
                                                    <input type="text" name="tarjeta_vencimiento" class="form-control @error('tarjeta_vencimiento') is-invalid @enderror" maxlength="5" placeholder="MM/AA" value="{{ old('tarjeta_vencimiento') }}">
                                                    @error('tarjeta_vencimiento')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label fw-bold">CVV</label>
                                                    <input type="text" name="tarjeta_cvv" class="form-control @error('tarjeta_cvv') is-invalid @enderror" maxlength="4" placeholder="123" value="{{ old('tarjeta_cvv') }}">
                                                    @error('tarjeta_cvv')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-3 mb-3">
                                                    <label class="form-label fw-bold">Moneda</label>
                                                    <input type="text" class="form-control" value="S/" readonly>
                                                </div>
                                                <div class="col-12 mb-3">
                                                    <label class="form-label fw-bold">Titular de la Tarjeta</label>
                                                    <input type="text" name="tarjeta_titular" class="form-control @error('tarjeta_titular') is-invalid @enderror" placeholder="Como aparece en la tarjeta" value="{{ old('tarjeta_titular') }}">
                                                    @error('tarjeta_titular')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold">Dirección de Facturación</label>
                                                    <textarea name="tarjeta_direccion" class="form-control @error('tarjeta_direccion') is-invalid @enderror" rows="2" placeholder="Dirección de facturación">{{ old('tarjeta_direccion') }}</textarea>
                                                    @error('tarjeta_direccion')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="pagoPaypal" class="pago-metodo mb-4" style="display:none;">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning text-dark">
                                            <i class="fab fa-paypal me-2"></i>PayPal
                                        </div>
                                        <div class="card-body">
                                            <label class="form-label fw-bold">Email de PayPal</label>
                                            <input type="email" name="paypal_email" class="form-control @error('paypal_email') is-invalid @enderror" placeholder="usuario@ejemplo.com" value="{{ old('paypal_email') }}">
                                            @error('paypal_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div id="pagoTransferencia" class="pago-metodo mb-4" style="display:none;">
                                    <div class="card border-secondary">
                                        <div class="card-header bg-secondary text-white">
                                            <i class="fas fa-university me-2"></i>Transferencia Bancaria
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-12 mb-3">
                                                    <label class="form-label fw-bold">Banco</label>
                                                    <select name="banco" class="form-select @error('banco') is-invalid @enderror">
                                                        <option value="">Selecciona un banco</option>
                                                        <option value="bcp" {{ old('banco') == 'bcp' ? 'selected' : '' }}>🏦 BCP</option>
                                                        <option value="interbank" {{ old('banco') == 'interbank' ? 'selected' : '' }}>🏦 Interbank</option>
                                                        <option value="bbva" {{ old('banco') == 'bbva' ? 'selected' : '' }}>🏦 BBVA</option>
                                                        <option value="scotiabank" {{ old('banco') == 'scotiabank' ? 'selected' : '' }}>🏦 Scotiabank</option>
                                                        <option value="otros" {{ old('banco') == 'otros' ? 'selected' : '' }}>🏦 Otros</option>
                                                    </select>
                                                    @error('banco')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label fw-bold">Número de Operación</label>
                                                    <input type="text" name="numero_operacion" class="form-control @error('numero_operacion') is-invalid @enderror" placeholder="N° de operación o referencia" value="{{ old('numero_operacion') }}">
                                                    @error('numero_operacion')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="pagoYape" class="pago-metodo mb-4" style="display:none;">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <i class="fas fa-mobile-alt me-2"></i>Pago Móvil
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Selecciona App de Pago</label>
                                                    <select name="app_pago" class="form-select @error('app_pago') is-invalid @enderror">
                                                        <option value="">Selecciona una app</option>
                                                        <option value="yape" {{ old('app_pago') == 'yape' ? 'selected' : '' }}>📱 Yape</option>
                                                        <option value="plin" {{ old('app_pago') == 'plin' ? 'selected' : '' }}>📱 Plin</option>
                                                        <option value="otro" {{ old('app_pago') == 'otro' ? 'selected' : '' }}>📱 Otro</option>
                                                    </select>
                                                    @error('app_pago')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label fw-bold">Número de Celular</label>
                                                    <input type="text" name="yape_celular" class="form-control @error('yape_celular') is-invalid @enderror" maxlength="9" placeholder="9XXXXXXXX" value="{{ old('yape_celular') }}">
                                                    @error('yape_celular')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-12 text-center">
                                                    <div style="display: inline-block; background: #fff; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); padding: 20px;">
                                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=000111222" alt="QR Pago" style="width: 180px; height: 180px;">
                                                        <div class="small text-muted mt-3">Escanea este código QR con tu app de pago</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Campo opcional para estante -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="fas fa-map-pin me-2"></i>Estante (opcional)
                                    </label>
                                    <input type="text" name="estante" class="form-control" placeholder="Ej: A-12, B-3, etc." value="{{ old('estante') }}">
                                </div>

                                <!-- Resumen de la transacción -->
                                <div id="resumenTransaccion" class="alert alert-info mb-4" style="display:none;">
                                    <h6 class="alert-heading">
                                        <i class="fas fa-clipboard-list me-2"></i>Resumen de tu solicitud:
                                    </h6>
                                    <div id="resumenContenido"></div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('books.catalog') }}" class="btn btn-outline-secondary btn-lg">
                                        <i class="fas fa-arrow-left me-2"></i>Cancelar
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-check me-2"></i>Confirmar Solicitud
                                    </button>
                                </div>
                            </form>
                        @endif
                    @endauth
                    @guest
                        <div class="alert alert-info text-center mb-4">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            Debes iniciar sesión para solicitar un libro.
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.price-tag {
    font-size: 1.5rem;
    font-weight: bold;
    color: #2c3e50;
}

.price-tag .currency {
    font-size: 1rem;
    color: #7f8c8d;
}

.price-tag .amount {
    color: #27ae60;
}

.pricing-option {
    padding: 15px;
    border-radius: 8px;
    background: #f8f9fa;
    transition: all 0.3s ease;
}

.pricing-option:hover {
    background: #e9ecef;
    transform: translateY(-2px);
}

.card-header {
    border-bottom: none;
}

.form-control-lg, .form-select-lg {
    font-size: 1.1rem;
    padding: 0.75rem 1rem;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
</style>

@push('scripts')
<script>
function mostrarCamposPago() {
    document.querySelectorAll('.pago-metodo').forEach(el => el.style.display = 'none');
    const metodo = document.getElementById('metodoPago').value;
    if (metodo === 'tarjeta') {
        document.getElementById('pagoTarjeta').style.display = 'block';
    } else if (metodo === 'paypal') {
        document.getElementById('pagoPaypal').style.display = 'block';
    } else if (metodo === 'transferencia') {
        document.getElementById('pagoTransferencia').style.display = 'block';
    } else if (metodo === 'yape') {
        document.getElementById('pagoYape').style.display = 'block';
    }
}

function mostrarCamposModalidad() {
    const tipo = document.getElementById('tipoTransaccion').value;
    const modalidad = document.getElementById('modalidad').value;
    
    // Ocultar todos los campos condicionales
    document.getElementById('camposFisico').style.display = 'none';
    document.getElementById('camposOnline').style.display = 'none';
    
    // Mostrar campos según modalidad
    if (modalidad === 'fisico') {
        document.getElementById('camposFisico').style.display = 'block';
    } else if (modalidad === 'online') {
        document.getElementById('camposOnline').style.display = 'block';
    }
    
    actualizarResumen();
}

// Precios desde backend (inyectados en JS)
const precios = {
    compra_fisico: @json($libro->precio_compra_fisica ?? 0),
    compra_online: @json($libro->precio_compra_online ?? 0),
    prestamo_fisico: @json($libro->precio_prestamo_fisico ?? 0),
    prestamo_online: @json($libro->precio_prestamo_online ?? 0)
};

function calcularPrecio(tipo, modalidad) {
    if (tipo === 'comprar' && modalidad === 'fisico') return precios.compra_fisico;
    if (tipo === 'comprar' && modalidad === 'online') return precios.compra_online;
    if (tipo === 'prestar' && modalidad === 'fisico') return precios.prestamo_fisico;
    if (tipo === 'prestar' && modalidad === 'online') return precios.prestamo_online;
    return 0;
}

function actualizarResumen() {
    const tipo = document.getElementById('tipoTransaccion').value;
    const modalidad = document.getElementById('modalidad').value;
    const precio = calcularPrecio(tipo, modalidad);
    let resumen = '';
    if (tipo && modalidad) {
        resumen += `<strong>Tipo:</strong> ${tipo === 'comprar' ? 'Compra' : 'Préstamo'}<br>`;
        resumen += `<strong>Modalidad:</strong> ${modalidad === 'fisico' ? 'Físico' : 'Online'}<br>`;
        resumen += `<strong>Precio:</strong> S/ ${precio.toFixed(2)}<br>`;
        if (tipo === 'prestar') {
            resumen += `<strong>Duración:</strong> ${modalidad === 'fisico' ? '14 días' : '7 días'}<br>`;
        }
        document.getElementById('resumenTransaccion').style.display = '';
    } else {
        document.getElementById('resumenTransaccion').style.display = 'none';
    }
    document.getElementById('resumenContenido').innerHTML = resumen;
}

// Event listeners
document.getElementById('metodoPago').addEventListener('change', mostrarCamposPago);
document.getElementById('tipoTransaccion').addEventListener('change', mostrarCamposModalidad);
document.getElementById('modalidad').addEventListener('change', mostrarCamposModalidad);

document.addEventListener('DOMContentLoaded', function() {
    mostrarCamposPago();
    mostrarCamposModalidad();
    document.getElementById('tipoTransaccion').addEventListener('change', function() {
        actualizarResumen();
    });
    document.getElementById('modalidad').addEventListener('change', function() {
        actualizarResumen();
    });
});
</script>
@endpush
@endsection 