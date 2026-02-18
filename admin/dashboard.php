<?php
include('header.php');
include('config/conexao.php');

// Obter dados das tabelas principais
$stats = [
    'noticias' => 0,
    'eventos' => 0,
    'areas' => 0,
    'documentos' => 0,
    'usuarios' => 0,
    'galeria' => 0,
    'doadores' => 0
];

$queries = [
    'noticias' => 'SELECT COUNT(*) as total FROM noticias',
    'eventos' => 'SELECT COUNT(*) as total FROM eventos',
    'areas' => 'SELECT COUNT(*) as total FROM areas',
    'documentos' => 'SELECT COUNT(*) as total FROM documentos',
    'usuarios' => 'SELECT COUNT(*) as total FROM users',
    'galeria' => 'SELECT COUNT(*) as total FROM galeria',
    'doadores' => 'SELECT COUNT(*) as total FROM doadores'
];

foreach ($queries as $key => $sql) {
    $result = @$conn->query($sql);
    if ($result) {
        $row = $result->fetch_assoc();
        $stats[$key] = (int)$row['total'];
    }
}

// Dados para gráfico de conteúdo por tipo
$content_types = [
    'Notícias' => $stats['noticias'],
    'Eventos' => $stats['eventos'],
    'Documentos' => $stats['documentos'],
    'Galeria' => $stats['galeria']
];

// Dados para gráfico de crescimento
$noticias_por_mes = [];
$eventos_por_mes = [];

// Noticias usa coluna 'data' que é varchar - tentar fazer parsing
$sql_noticias = "SELECT DATE_FORMAT(STR_TO_DATE(data, '%d/%m/%Y'), '%Y-%m') as mes, COUNT(*) as total 
                 FROM noticias 
                 WHERE STR_TO_DATE(data, '%d/%m/%Y') >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                 GROUP BY DATE_FORMAT(STR_TO_DATE(data, '%d/%m/%Y'), '%Y-%m')
                 ORDER BY mes ASC";

// Eventos usa coluna 'data' que é text
$sql_eventos = "SELECT DATE_FORMAT(STR_TO_DATE(data, '%d/%m/%Y'), '%Y-%m') as mes, COUNT(*) as total 
                FROM eventos 
                WHERE STR_TO_DATE(data, '%d/%m/%Y') >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(STR_TO_DATE(data, '%d/%m/%Y'), '%Y-%m')
                ORDER BY mes ASC";

$result = @$conn->query($sql_noticias);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $noticias_por_mes[$row['mes']] = (int)$row['total'];
    }
}

$result = @$conn->query($sql_eventos);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $eventos_por_mes[$row['mes']] = (int)$row['total'];
    }
}

// Garantir 6 últimos meses
$meses = [];
for ($i = 5; $i >= 0; $i--) {
    $mes = date('Y-m', strtotime("-$i month"));
    $meses[] = $mes;
}

$noticias_valores = array_map(function($m) use ($noticias_por_mes) { 
    return $noticias_por_mes[$m] ?? 0; 
}, $meses);

$eventos_valores = array_map(function($m) use ($eventos_por_mes) { 
    return $eventos_por_mes[$m] ?? 0; 
}, $meses);
?>

<!-- Content wrapper ORIGINAL - com padding do tema antigo -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <h4 class="mb-0">Dashboard PIRCOM</h4>
                <p class="text-muted">Resumo de conteúdo e atividades</p>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100" style="border-left: 4px solid #FF6F0F;">
                    <div class="card-body">
                        <span class="text-muted d-block mb-2" style="font-size: 0.85rem;">Notícias</span>
                        <h3 class="card-title mb-0"><?php echo $stats['noticias']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100" style="border-left: 4px solid #28a745;">
                    <div class="card-body">
                        <span class="text-muted d-block mb-2" style="font-size: 0.85rem;">Eventos</span>
                        <h3 class="card-title mb-0"><?php echo $stats['eventos']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100" style="border-left: 4px solid #007bff;">
                    <div class="card-body">
                        <span class="text-muted d-block mb-2" style="font-size: 0.85rem;">Documentos</span>
                        <h3 class="card-title mb-0"><?php echo $stats['documentos']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card h-100" style="border-left: 4px solid #6c757d;">
                    <div class="card-body">
                        <span class="text-muted d-block mb-2" style="font-size: 0.85rem;">Utilizadores</span>
                        <h3 class="card-title mb-0"><?php echo $stats['usuarios']; ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="row mt-4">
            <!-- Pie Chart - Distribuição de Conteúdo -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Distribuição de Conteúdo</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="contentChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>

            <!-- Bar Chart - Conteúdo por Tipo -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Resumo por Tipo</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="barChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Line Chart - Crescimento -->
        <div class="row">
            <div class="col-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Crescimento (Últimos 6 Meses)</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="growthChart" style="max-height: 300px;"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats -->
        <div class="row mt-4">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="text-muted mb-2">Áreas de Intervenção</h5>
                        <h3 class="mb-0"><?php echo $stats['areas']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="text-muted mb-2">Galeria</h5>
                        <h3 class="mb-0"><?php echo $stats['galeria']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="text-muted mb-2">Doadores</h5>
                        <h3 class="mb-0"><?php echo $stats['doadores']; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="text-muted mb-2">Total de Usuários</h5>
                        <h3 class="mb-0"><?php echo $stats['usuarios']; ?></h3>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <!-- / Content -->

    <!-- Footer -->
    <?php include('footerprincipal.php'); ?>
    <!-- / Footer -->

    <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
// Cores do PIRCOM
const colors = {
    primary: '#FF6F0F',
    success: '#28a745',
    info: '#007bff',
    warning: '#ffc107',
    danger: '#dc3545'
};

// Pie Chart - Distribuição
const ctxPie = document.getElementById('contentChart').getContext('2d');
new Chart(ctxPie, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode(array_keys($content_types)); ?>,
        datasets: [{
            data: <?php echo json_encode(array_values($content_types)); ?>,
            backgroundColor: [
                '#FF6F0F',
                '#28a745',
                '#007bff',
                '#6c757d'
            ],
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Bar Chart
const ctxBar = document.getElementById('barChart').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_keys($content_types)); ?>,
        datasets: [{
            label: 'Quantidade',
            data: <?php echo json_encode(array_values($content_types)); ?>,
            backgroundColor: [
                '#FF6F0F',
                '#28a745',
                '#007bff',
                '#6c757d'
            ],
            borderRadius: 6,
            borderSkipped: false
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        indexAxis: 'y',
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Line Chart - Crescimento
const ctxGrowth = document.getElementById('growthChart').getContext('2d');
new Chart(ctxGrowth, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_map(function($m) { 
            return date('M/Y', strtotime($m . '-01')); 
        }, $meses)); ?>,
        datasets: [
            {
                label: 'Notícias',
                data: <?php echo json_encode($noticias_valores); ?>,
                borderColor: '#FF6F0F',
                backgroundColor: 'rgba(255, 111, 15, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            },
            {
                label: 'Eventos',
                data: <?php echo json_encode($eventos_valores); ?>,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>

<style>
.card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.card-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1rem;
}

.card-header h5 {
    color: #333;
    font-weight: 600;
}
</style>

<?php
include('footer.php');
?>