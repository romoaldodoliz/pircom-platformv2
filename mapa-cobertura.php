<?php
$page_title = "Cobertura Geográfica - PIRCOM";
include 'includes/navbar.php';
?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" 
      crossorigin=""/>

<style>
    /* Hero Section */
    .mapa-hero {
        background: linear-gradient(135deg, #FF6F0F 0%, #E05A00 100%);
        padding: 120px 0 80px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .mapa-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="rgba(255,255,255,0.1)" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,106.7C1248,96,1344,96,1392,96L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>') no-repeat bottom;
        background-size: cover;
        opacity: 0.3;
    }
    
    .mapa-hero h1 {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 15px;
        position: relative;
        z-index: 2;
    }
    
    .mapa-hero p {
        font-size: 1.2rem;
        position: relative;
        z-index: 2;
        margin-bottom: 10px;
    }
    
    /* Map Wrapper */
    .mapa-wrapper {
        background: #f5f5f5;
        padding: 0;
        margin: 0;
    }
    
    /* Map Container */
    .mapa-container {
        background: white;
        border-radius: 0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        overflow: hidden;
        margin: 0;
        width: 100%;
    }
    
    #mapa {
        width: 100%;
        height: 600px;
        display: block;
    }
    
    /* Stats Section */
    .mapa-stats-section {
        padding: 80px 0;
        background: white;
    }
    
    .mapa-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
        margin-bottom: 0;
    }
    
    .mapa-stat-card {
        background: white;
        padding: 40px 30px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .mapa-stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(255, 111, 15, 0.15);
        border-color: #FF6F0F;
    }
    
    .mapa-stat-card i {
        font-size: 3.5rem;
        color: #FF6F0F;
        margin-bottom: 20px;
        display: block;
    }
    
    .mapa-stat-card h3 {
        font-size: 2.5rem;
        font-weight: 800;
        color: #000000;
        margin-bottom: 10px;
    }
    
    .mapa-stat-card p {
        color: #666;
        margin-bottom: 0;
        font-size: 1.1rem;
        line-height: 1.5;
    }
    
    /* Info Section */
    .mapa-info-section {
        padding: 80px 0;
        background: #f8f9fa;
    }
    
    .mapa-info-box {
        background: white;
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        height: 100%;
        transition: all 0.3s ease;
    }
    
    .mapa-info-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.12);
    }
    
    .mapa-info-box i {
        font-size: 2.5rem;
        color: #FF6F0F;
        margin-bottom: 20px;
        display: block;
    }
    
    .mapa-info-box h4 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #000000;
        margin-bottom: 15px;
    }
    
    .mapa-info-box p {
        color: #666;
        line-height: 1.8;
        margin-bottom: 0;
    }
    
    /* Loading Spinner */
    .mapa-loading {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 600px;
        background: #f8f9fa;
    }
    
    .mapa-spinner {
        width: 60px;
        height: 60px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #FF6F0F;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Leaflet Customizations */
    .leaflet-popup-content-wrapper {
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .leaflet-popup-content {
        margin: 15px 20px;
        font-size: 16px;
        font-weight: 600;
        color: #000000;
    }
    
    .leaflet-control-zoom a {
        color: #FF6F0F !important;
    }
    
    .leaflet-control-zoom a:hover {
        background: #FF6F0F !important;
        color: white !important;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .mapa-hero {
            padding: 100px 0 60px;
        }
        
        .mapa-hero h1 {
            font-size: 2rem;
        }
        
        .mapa-hero p {
            font-size: 1rem;
        }
        
        #mapa {
            height: 450px;
        }
        
        .mapa-stats-section {
            padding: 50px 0;
        }
        
        .mapa-stat-card {
            padding: 30px 20px;
        }
        
        .mapa-stat-card h3 {
            font-size: 2rem;
        }
        
        .mapa-info-section {
            padding: 50px 0;
        }
        
        .mapa-info-box {
            margin-bottom: 20px;
        }
    }
</style>

<!-- Hero Section -->
<section class="mapa-hero">
    <div class="container text-center">
        <h1>Cobertura Geográfica da PIRCOM</h1>
        <p style="font-weight: 600;">Plataforma Inter-Religiosa de Comunicação para a Saúde</p>
        <p style="opacity: 0.95; font-size: 1.05rem;">Conheça as províncias onde atuamos em Moçambique</p>
    </div>
</section>

<!-- Map Section -->
<div class="mapa-wrapper">
    <div class="mapa-container">
        <div id="mapa-loading" class="mapa-loading">
            <div class="mapa-spinner"></div>
        </div>
        <div id="mapa" style="display: none;"></div>
    </div>
</div>

<!-- Stats Section -->
<section class="mapa-stats-section">
    <div class="container">
        <div class="mapa-stats">
            <div class="mapa-stat-card">
                <i class='bx bx-map'></i>
                <h3 id="total-provincias">0</h3>
                <p>Províncias com Cobertura</p>
            </div>
            <div class="mapa-stat-card">
                <i class='bx bx-calendar'></i>
                <h3>2006</h3>
                <p>Ano de Fundação</p>
            </div>
            <div class="mapa-stat-card">
                <i class='bx bx-group'></i>
                <h3>Multi-Fé</h3>
                <p>Cristãos, Muçulmanos, Hindus e Bahai</p>
            </div>
        </div>
    </div>
</section>

<!-- Info Section -->
<section class="mapa-info-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 style="color: #000000; font-weight: 800; font-size: 2.5rem;">Sobre a PIRCOM</h2>
            <p style="color: #666; font-size: 1.2rem; max-width: 800px; margin: 15px auto 0;">
                Plataforma Inter-Religiosa de Comunicação para a Saúde
            </p>
        </div>
        
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="mapa-info-box">
                    <i class='bx bx-info-circle'></i>
                    <h4>Nossa História</h4>
                    <p>
                        A PIRCOM foi criada em <strong>19 de abril de 2006</strong> por líderes religiosos no contexto do movimento "Roll Back Malaria", 
                        ecoando mensagens do Ministério da Saúde sobre prevenção e tratamento da malária e outras doenças relacionadas ao saneamento.
                    </p>
                    <p class="mt-3">
                        Somos considerados a <strong>primeira aliança múltipla baseada na fé</strong> através da colaboração entre comunidades 
                        Cristãs, Muçulmanas, Hindus e Bahai em Moçambique.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="mapa-info-box">
                    <i class='bx bx-heart'></i>
                    <h4>Missão & Objetivos</h4>
                    <p>
                        Contribuímos na melhoria do comportamento dos beneficiários prioritários (adolescentes/jovens, mulheres grávidas, 
                        mulheres em lactação, recém-nascidos, crianças menores de 5 anos) em relação à:
                    </p>
                    <ul style="color: #666; line-height: 2;">
                        <li>Prevenção e tratamento da <strong>Malária</strong></li>
                        <li><strong>Nutrição</strong> adequada</li>
                        <li>Saúde <strong>Materno, Neonatal e Infantil</strong></li>
                        <li>Prevenção e tratamento do <strong>HIV</strong></li>
                    </ul>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="mapa-info-box">
                    <i class='bx bx-book-heart'></i>
                    <h4>Modelo Único de Intervenção</h4>
                    <p>
                        O nosso modelo baseia-se na <strong>disseminação de mensagens de saúde através de líderes religiosos</strong>. 
                        As mensagens são padronizadas e alinhadas com as escrituras sagradas: versículos bíblicos, alcorânicos e outros livros sagrados.
                    </p>
                    <p class="mt-3">
                        Este modelo único e bem-sucedido permite que as mensagens de saúde cheguem às comunidades de forma culturalmente 
                        sensível e espiritualmente relevante.
                    </p>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="mapa-info-box">
                    <i class='bx bx-radio'></i>
                    <h4>Canais de Comunicação</h4>
                    <p>
                        Utilizamos abordagens combinadas e complementares de Comunicação para Mudança Social e de Comportamento:
                    </p>
                    <ul style="color: #666; line-height: 2;">
                        <li><strong>Mass Media:</strong> Rádios comunitárias</li>
                        <li><strong>Social Media:</strong> Diferentes plataformas digitais</li>
                        <li><strong>Visitas Domiciliárias:</strong> Através de voluntários capacitados</li>
                        <li><strong>Líderes Religiosos:</strong> Nas igrejas, mesquitas e templos</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Director Info -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="mapa-info-box text-center" style="background: linear-gradient(135deg, #FF6F0F 0%, #E05A00 100%); color: white;">
                    <i class='bx bx-user-circle' style="color: white;"></i>
                    <h4 style="color: white;">Direção Executiva</h4>
                    <p style="color: white; font-size: 1.2rem; margin-bottom: 0;">
                        <strong>Bispo Dinis Matsolo</strong> - Director Executivo da PIRCOM
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" 
        crossorigin=""></script>

<script>
// Variáveis globais
let mapa;
let marcadores = [];

// Inicializar o mapa
function initMapa() {
    // Criar mapa centralizado em Moçambique
    mapa = L.map('mapa', {
        zoomControl: true,
        attributionControl: true
    }).setView([-18.665695, 35.529562], 6);
    
    // Adicionar tiles do OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap | PIRCOM 2006-' + new Date().getFullYear()
    }).addTo(mapa);
    
    // Configurar limites do mapa para Moçambique
    const limites = L.latLngBounds(
        L.latLng(-26.87, 30.22),  // Sudoeste
        L.latLng(-10.47, 40.84)   // Nordeste
    );
    mapa.setMaxBounds(limites);
    mapa.setMinZoom(5);
    
    // Ocultar spinner e mostrar mapa
    document.getElementById('mapa-loading').style.display = 'none';
    document.getElementById('mapa').style.display = 'block';
    
    // Forçar redimensionamento
    setTimeout(() => {
        mapa.invalidateSize();
    }, 100);
}

// Criar ícone customizado
function criarIconeCustomizado() {
    return L.divIcon({
        className: 'custom-marker-icon',
        html: `<div style="
            background: linear-gradient(135deg, #FF6F0F 0%, #E05A00 100%);
            border: 3px solid white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            box-shadow: 0 4px 15px rgba(255, 111, 15, 0.6);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        ">
            <i class="bx bx-health" style="color: white; font-size: 16px;"></i>
        </div>`,
        iconSize: [30, 30],
        iconAnchor: [15, 15],
        popupAnchor: [0, -20]
    });
}

// Carregar províncias da API
async function carregarProvincias() {
    try {
        const response = await fetch('api/provincias.php');
        const data = await response.json();
        
        if (data.success && data.data.length > 0) {
            // Atualizar contador
            document.getElementById('total-provincias').textContent = data.data.length;
            
            // Adicionar marcadores
            data.data.forEach(provincia => {
                const lat = parseFloat(provincia.latitude);
                const lng = parseFloat(provincia.longitude);
                
                if (!isNaN(lat) && !isNaN(lng)) {
                    const marcador = L.marker([lat, lng], {
                        icon: criarIconeCustomizado()
                    }).addTo(mapa);
                    
                    marcador.bindPopup(`
                        <div style="text-align: center; padding: 8px 5px;">
                            <strong style="font-size: 17px; color: #FF6F0F; display: block; margin-bottom: 5px;">
                                ${provincia.nome}
                            </strong>
                            <small style="color: #666; font-size: 13px;">
                                Província de Moçambique<br>
                                <strong>PIRCOM</strong> em ação
                            </small>
                        </div>
                    `, {
                        maxWidth: 250,
                        className: 'custom-popup'
                    });
                    
                    marcadores.push(marcador);
                }
            });
            
            // Ajustar visualização para mostrar todos os marcadores
            if (marcadores.length > 0) {
                const grupo = new L.featureGroup(marcadores);
                mapa.fitBounds(grupo.getBounds().pad(0.15));
            }
        } else {
            console.warn('Nenhuma província encontrada');
            document.getElementById('total-provincias').textContent = '0';
        }
    } catch (error) {
        console.error('Erro ao carregar províncias:', error);
        document.getElementById('total-provincias').textContent = '0';
    }
}

// Inicializar quando a página carregar
document.addEventListener('DOMContentLoaded', function() {
    initMapa();
    carregarProvincias();
});

// Redimensionar mapa quando a janela mudar de tamanho
window.addEventListener('resize', function() {
    if (mapa) {
        mapa.invalidateSize();
    }
});
</script>
