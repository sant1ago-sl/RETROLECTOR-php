@extends('layouts.app')

@section('title', 'Análisis de Lectura - Retrolector')

@section('content')
<div class="analytics-container">
    <!-- Header de Análisis -->
    <div class="analytics-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="analytics-title">
                        <i class="fas fa-chart-line me-3"></i>Análisis de Lectura
                    </h1>
                    <p class="analytics-subtitle">Descubre tus patrones de lectura y mejora tu experiencia</p>
                </div>
                <div class="col-lg-4 text-end">
                    <div class="analytics-period-selector">
                        <select id="periodSelector" class="form-select">
                            <option value="30">Últimos 30 días</option>
                            <option value="90">Últimos 3 meses</option>
                            <option value="180">Últimos 6 meses</option>
                            <option value="365">Último año</option>
                            <option value="all">Todo el tiempo</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Resumen de Estadísticas -->
        <div class="stats-overview">
            <div class="row">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card primary">
                        <div class="stat-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="totalBooks">{{ $stats['total_books'] ?? 0 }}</h3>
                            <p>Libros Leídos</p>
                            <div class="stat-trend">
                                <span class="trend-up">
                                    <i class="fas fa-arrow-up"></i> +{{ $stats['growth_rate'] ?? 0 }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card success">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="readingTime">{{ $stats['total_hours'] ?? 0 }}</h3>
                            <p>Horas de Lectura</p>
                            <div class="stat-trend">
                                <span class="trend-up">
                                    <i class="fas fa-arrow-up"></i> +{{ $stats['time_growth'] ?? 0 }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card warning">
                        <div class="stat-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="avgRating">{{ number_format($stats['avg_rating'] ?? 0, 1) }}</h3>
                            <p>Rating Promedio</p>
                            <div class="stat-trend">
                                <span class="trend-stable">
                                    <i class="fas fa-minus"></i> Estable
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card info">
                        <div class="stat-icon">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="stat-content">
                            <h3 id="readingStreak">{{ $stats['reading_streak'] ?? 0 }}</h3>
                            <p>Días Consecutivos</p>
                            <div class="stat-trend">
                                <span class="trend-up">
                                    <i class="fas fa-fire"></i> ¡En racha!
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos de Análisis -->
        <div class="analytics-charts">
            <div class="row">
                <!-- Gráfico de Lectura por Mes -->
                <div class="col-lg-8">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-area me-2"></i>Actividad de Lectura</h3>
                            <p>Libros leídos por mes</p>
                        </div>
                        <div class="chart-container">
                            <canvas id="readingActivityChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Gráfico de Categorías -->
                <div class="col-lg-4">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-chart-pie me-2"></i>Categorías Favoritas</h3>
                            <p>Distribución por género</p>
                        </div>
                        <div class="chart-container">
                            <canvas id="categoriesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mt-4">
                <!-- Gráfico de Autores -->
                <div class="col-lg-6">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-user-edit me-2"></i>Autores Más Leídos</h3>
                            <p>Top 10 autores</p>
                        </div>
                        <div class="chart-container">
                            <canvas id="authorsChart"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Gráfico de Rating -->
                <div class="col-lg-6">
                    <div class="chart-card">
                        <div class="chart-header">
                            <h3><i class="fas fa-star me-2"></i>Distribución de Ratings</h3>
                            <p>Calificaciones dadas</p>
                        </div>
                        <div class="chart-container">
                            <canvas id="ratingsChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Análisis Detallado -->
        <div class="detailed-analysis">
            <div class="row">
                <!-- Hábitos de Lectura -->
                <div class="col-lg-6">
                    <div class="analysis-card">
                        <div class="analysis-header">
                            <h3><i class="fas fa-calendar-alt me-2"></i>Hábitos de Lectura</h3>
                        </div>
                        <div class="analysis-content">
                            <div class="habit-item">
                                <div class="habit-label">Día favorito para leer</div>
                                <div class="habit-value">{{ $habits['favorite_day'] ?? 'N/A' }}</div>
                            </div>
                            <div class="habit-item">
                                <div class="habit-label">Hora pico de lectura</div>
                                <div class="habit-value">{{ $habits['peak_hour'] ?? 'N/A' }}</div>
                            </div>
                            <div class="habit-item">
                                <div class="habit-label">Promedio de libros por mes</div>
                                <div class="habit-value">{{ $habits['avg_books_per_month'] ?? 0 }}</div>
                            </div>
                            <div class="habit-item">
                                <div class="habit-label">Tiempo promedio por libro</div>
                                <div class="habit-value">{{ $habits['avg_time_per_book'] ?? 0 }} días</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Preferencias -->
                <div class="col-lg-6">
                    <div class="analysis-card">
                        <div class="analysis-header">
                            <h3><i class="fas fa-heart me-2"></i>Preferencias</h3>
                        </div>
                        <div class="analysis-content">
                            <div class="preference-item">
                                <div class="preference-label">Categoría favorita</div>
                                <div class="preference-value">{{ $preferences['favorite_category'] ?? 'N/A' }}</div>
                            </div>
                            <div class="preference-item">
                                <div class="preference-label">Autor favorito</div>
                                <div class="preference-value">{{ $preferences['favorite_author'] ?? 'N/A' }}</div>
                            </div>
                            <div class="preference-item">
                                <div class="preference-label">Año preferido</div>
                                <div class="preference-value">{{ $preferences['favorite_year'] ?? 'N/A' }}</div>
                            </div>
                            <div class="preference-item">
                                <div class="preference-label">Rating promedio dado</div>
                                <div class="preference-value">{{ number_format($preferences['avg_rating_given'] ?? 0, 1) }}/5</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logros y Metas -->
        <div class="achievements-section">
            <div class="section-header">
                <h2><i class="fas fa-medal me-2"></i>Logros y Metas</h2>
                <p>Tu progreso y objetivos de lectura</p>
            </div>
            
            <div class="achievements-grid">
                @foreach($achievements as $achievement)
                    <div class="achievement-card {{ $achievement['completed'] ? 'completed' : 'pending' }}">
                        <div class="achievement-icon">
                            <i class="{{ $achievement['icon'] }}"></i>
                        </div>
                        <div class="achievement-content">
                            <h4>{{ $achievement['title'] }}</h4>
                            <p>{{ $achievement['description'] }}</p>
                            <div class="achievement-progress">
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ $achievement['progress'] }}%"></div>
                                </div>
                                <span class="progress-text">{{ $achievement['current'] }}/{{ $achievement['target'] }}</span>
                            </div>
                        </div>
                        @if($achievement['completed'])
                            <div class="achievement-badge">
                                <i class="fas fa-check"></i>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recomendaciones Personalizadas -->
        <div class="personalized-insights">
            <div class="section-header">
                <h2><i class="fas fa-lightbulb me-2"></i>Insights Personalizados</h2>
                <p>Descubre patrones únicos en tu lectura</p>
            </div>
            
            <div class="insights-grid">
                @foreach($insights as $insight)
                    <div class="insight-card">
                        <div class="insight-icon">
                            <i class="{{ $insight['icon'] }}"></i>
                        </div>
                        <div class="insight-content">
                            <h4>{{ $insight['title'] }}</h4>
                            <p>{{ $insight['description'] }}</p>
                            @if(isset($insight['suggestion']))
                                <div class="insight-suggestion">
                                    <strong>Sugerencia:</strong> {{ $insight['suggestion'] }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Exportar Datos -->
        <div class="export-section">
            <div class="section-header">
                <h2><i class="fas fa-download me-2"></i>Exportar Datos</h2>
                <p>Descarga tu información de lectura</p>
            </div>
            
            <div class="export-options">
                <a href="{{ route('user.export-reading-data') }}" class="btn btn-primary">
                    <i class="fas fa-file-csv me-2"></i>Exportar CSV
                </a>
                <a href="{{ route('user.export-reading-pdf') }}" class="btn btn-secondary">
                    <i class="fas fa-file-pdf me-2"></i>Exportar PDF
                </a>
                <a href="{{ route('user.export-reading-json') }}" class="btn btn-info">
                    <i class="fas fa-file-code me-2"></i>Exportar JSON
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.analytics-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.analytics-header {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 3rem 0;
    margin-bottom: 3rem;
    color: white;
}

.analytics-title {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.analytics-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
}

.analytics-period-selector select {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: white;
    border-radius: 10px;
    padding: 0.75rem 1rem;
}

.analytics-period-selector select option {
    background: #333;
    color: white;
}

.stats-overview {
    margin-bottom: 3rem;
}

.stat-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    text-align: center;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease;
    margin-bottom: 1rem;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-card.primary {
    border-left: 4px solid var(--primary-color);
}

.stat-card.success {
    border-left: 4px solid var(--success-color);
}

.stat-card.warning {
    border-left: 4px solid var(--warning-color);
}

.stat-card.info {
    border-left: 4px solid var(--info-color);
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto 1rem;
}

.stat-card.success .stat-icon {
    background: linear-gradient(135deg, var(--success-color), #28a745);
}

.stat-card.warning .stat-icon {
    background: linear-gradient(135deg, var(--warning-color), #ffc107);
}

.stat-card.info .stat-icon {
    background: linear-gradient(135deg, var(--info-color), #17a2b8);
}

.stat-content h3 {
    font-size: 2.5rem;
    font-weight: 900;
    color: var(--dark-color);
    margin-bottom: 0.5rem;
}

.stat-content p {
    color: #6c757d;
    font-size: 1rem;
    margin-bottom: 1rem;
}

.stat-trend {
    font-size: 0.9rem;
    font-weight: 600;
}

.trend-up {
    color: var(--success-color);
}

.trend-down {
    color: var(--danger-color);
}

.trend-stable {
    color: var(--warning-color);
}

.analytics-charts {
    margin-bottom: 3rem;
}

.chart-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.chart-header {
    margin-bottom: 2rem;
}

.chart-header h3 {
    color: var(--dark-color);
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.chart-header p {
    color: #6c757d;
    margin: 0;
}

.chart-container {
    position: relative;
    height: 300px;
}

.detailed-analysis {
    margin-bottom: 3rem;
}

.analysis-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.analysis-header {
    margin-bottom: 1.5rem;
}

.analysis-header h3 {
    color: var(--dark-color);
    font-weight: 700;
    margin: 0;
}

.habit-item,
.preference-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.habit-item:last-child,
.preference-item:last-child {
    border-bottom: none;
}

.habit-label,
.preference-label {
    font-weight: 600;
    color: var(--dark-color);
}

.habit-value,
.preference-value {
    font-weight: 700;
    color: var(--primary-color);
}

.achievements-section,
.personalized-insights,
.export-section {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.section-header {
    text-align: center;
    margin-bottom: 2rem;
}

.section-header h2 {
    color: var(--dark-color);
    font-weight: 800;
    margin-bottom: 0.5rem;
}

.section-header p {
    color: #6c757d;
    font-size: 1.1rem;
}

.achievements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.achievement-card {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    position: relative;
    transition: all 0.3s ease;
}

.achievement-card.completed {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    border: 2px solid var(--success-color);
}

.achievement-card.pending {
    background: #f8f9fa;
    border: 2px solid #dee2e6;
}

.achievement-icon {
    width: 50px;
    height: 50px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.achievement-card.completed .achievement-icon {
    background: var(--success-color);
}

.achievement-content {
    flex: 1;
}

.achievement-content h4 {
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--dark-color);
}

.achievement-content p {
    color: #6c757d;
    margin-bottom: 1rem;
    font-size: 0.9rem;
}

.achievement-progress {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.progress {
    flex: 1;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
    transition: width 0.3s ease;
}

.achievement-card.completed .progress-bar {
    background: var(--success-color);
}

.progress-text {
    font-size: 0.8rem;
    font-weight: 600;
    color: #6c757d;
}

.achievement-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 30px;
    height: 30px;
    background: var(--success-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
}

.insights-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.insight-card {
    background: #f8f9fa;
    border-radius: 15px;
    padding: 1.5rem;
    border-left: 4px solid var(--primary-color);
}

.insight-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    margin-bottom: 1rem;
}

.insight-content h4 {
    font-weight: 700;
    margin-bottom: 0.5rem;
    color: var(--dark-color);
}

.insight-content p {
    color: #6c757d;
    margin-bottom: 1rem;
}

.insight-suggestion {
    background: rgba(var(--primary-color-rgb), 0.1);
    border: 1px solid rgba(var(--primary-color-rgb), 0.2);
    border-radius: 8px;
    padding: 0.75rem;
    font-size: 0.9rem;
}

.export-options {
    display: flex;
    gap: 1rem;
    justify-content: center;
    flex-wrap: wrap;
}

.export-options .btn {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.export-options .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

/* Responsive */
@media (max-width: 768px) {
    .analytics-title {
        font-size: 2rem;
    }
    
    .chart-container {
        height: 250px;
    }
    
    .achievements-grid,
    .insights-grid {
        grid-template-columns: 1fr;
    }
    
    .export-options {
        flex-direction: column;
        align-items: center;
    }
    
    .export-options .btn {
        width: 100%;
        max-width: 300px;
    }
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Configuración global de Chart.js
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.color = '#6c757d';

// Datos de ejemplo (reemplazar con datos reales del backend)
const chartData = {
    readingActivity: {
        labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        datasets: [{
            label: 'Libros Leídos',
            data: [3, 5, 2, 8, 6, 4, 7, 9, 5, 8, 6, 10],
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    categories: {
        labels: ['Ficción', 'No Ficción', 'Ciencia', 'Historia', 'Biografía'],
        datasets: [{
            data: [35, 25, 20, 15, 5],
            backgroundColor: [
                '#667eea',
                '#764ba2',
                '#f093fb',
                '#f5576c',
                '#4facfe'
            ]
        }]
    },
    authors: {
        labels: ['Autor 1', 'Autor 2', 'Autor 3', 'Autor 4', 'Autor 5'],
        datasets: [{
            label: 'Libros Leídos',
            data: [8, 6, 5, 4, 3],
            backgroundColor: '#667eea',
            borderColor: '#764ba2',
            borderWidth: 2
        }]
    },
    ratings: {
        labels: ['1★', '2★', '3★', '4★', '5★'],
        datasets: [{
            label: 'Cantidad',
            data: [2, 5, 12, 25, 18],
            backgroundColor: [
                '#dc3545',
                '#fd7e14',
                '#ffc107',
                '#28a745',
                '#20c997'
            ]
        }]
    }
};

// Inicializar gráficos
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de actividad de lectura
    const readingActivityCtx = document.getElementById('readingActivityChart').getContext('2d');
    new Chart(readingActivityCtx, {
        type: 'line',
        data: chartData.readingActivity,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Gráfico de categorías
    const categoriesCtx = document.getElementById('categoriesChart').getContext('2d');
    new Chart(categoriesCtx, {
        type: 'doughnut',
        data: chartData.categories,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Gráfico de autores
    const authorsCtx = document.getElementById('authorsChart').getContext('2d');
    new Chart(authorsCtx, {
        type: 'bar',
        data: chartData.authors,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Gráfico de ratings
    const ratingsCtx = document.getElementById('ratingsChart').getContext('2d');
    new Chart(ratingsCtx, {
        type: 'bar',
        data: chartData.ratings,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
});

// Cambiar período de análisis
document.getElementById('periodSelector').addEventListener('change', function() {
    const period = this.value;
    // Aquí se haría una llamada AJAX para actualizar los datos
    console.log('Período seleccionado:', period);
    
    // Simular actualización de datos
    updateAnalyticsData(period);
});

function updateAnalyticsData(period) {
    // Simular carga de datos
    const loadingStates = document.querySelectorAll('.stat-content h3');
    loadingStates.forEach(stat => {
        stat.textContent = '...';
    });
    
    // Simular respuesta del servidor
    setTimeout(() => {
        // Actualizar estadísticas con nuevos datos
        document.getElementById('totalBooks').textContent = Math.floor(Math.random() * 50) + 20;
        document.getElementById('readingTime').textContent = Math.floor(Math.random() * 200) + 100;
        document.getElementById('avgRating').textContent = (Math.random() * 2 + 3).toFixed(1);
        document.getElementById('readingStreak').textContent = Math.floor(Math.random() * 30) + 5;
    }, 1000);
}

// Animaciones de entrada
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observar elementos para animación
document.querySelectorAll('.stat-card, .chart-card, .analysis-card, .achievement-card, .insight-card').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(20px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
});
</script>
@endpush 