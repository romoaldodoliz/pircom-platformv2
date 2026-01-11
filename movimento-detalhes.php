<?php
$page_title = "Causa - PIRCOM";
include 'config/conexao.php';

// Buscar movimento
$movimento_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("SELECT * FROM movimentos WHERE id = ? AND status = 'publicado'");
$stmt->bind_param("i", $movimento_id);
$stmt->execute();
$result = $stmt->get_result();
$movimento = $result->fetch_assoc();
$stmt->close();

if (!$movimento) {
    header('Location: movimentos.php');
    exit;
}

// Buscar fotos do movimento
$stmt = $conn->prepare("SELECT * FROM movimentos_fotos WHERE movimento_id = ? ORDER BY ordem, id");
$stmt->bind_param("i", $movimento_id);
$stmt->execute();
$fotos_result = $stmt->get_result();
$fotos = $fotos_result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Incrementar visualizações
$stmt = $conn->prepare("UPDATE movimentos SET visualizacoes = visualizacoes + 1 WHERE id = ?");
$stmt->bind_param("i", $movimento_id);
$stmt->execute();
$stmt->close();

// Incluir navbar padrão
include 'includes/navbar.php';
?>

<!-- GLightbox CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<style>
    :root {
        --pircom-primary: #2563eb;
        --pircom-secondary: #1e40af;
        --pircom-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .movimento-detalhes {
        padding: 120px 0 50px;
        background: #f7f8fa;
        min-height: 100vh;
    }
    
    .movimento-header {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 40px;
    }
    
    .movimento-hero-image {
        width: 100%;
        height: 500px;
        object-fit: cover;
    }
    
    .movimento-header-content {
        padding: 40px;
    }
    
    .movimento-categoria {
        display: inline-block;
        background: var(--pircom-gradient);
        color: white;
        padding: 8px 20px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .movimento-titulo {
        font-size: 42px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 20px;
        line-height: 1.2;
    }
    
    .movimento-info {
        display: flex;
        gap: 30px;
        flex-wrap: wrap;
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 2px solid #f0f0f0;
    }
    
    .movimento-info-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #666;
        font-size: 16px;
    }
    
    .movimento-info-item i {
        color: var(--pircom-primary);
        font-size: 20px;
    }
    
    .movimento-descricao {
        font-size: 18px;
        line-height: 1.8;
        color: #555;
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    
    .galeria-section {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .galeria-titulo {
        font-size: 32px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 30px;
        text-align: center;
        position: relative;
        padding-bottom: 15px;
    }
    
    .galeria-titulo::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: var(--pircom-gradient);
        border-radius: 2px;
    }
    
    .galeria-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
    }
    
    .galeria-item {
        position: relative;
        overflow: hidden;
        border-radius: 12px;
        cursor: pointer;
        height: 250px;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .galeria-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s;
    }
    
    .galeria-item:hover {
        box-shadow: 0 10px 30px rgba(37, 99, 235, 0.3);
        transform: translateY(-5px);
    }
    
    .galeria-item:hover img {
        transform: scale(1.1);
    }
    
    .galeria-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
        padding: 20px;
        opacity: 0;
        transition: opacity 0.3s;
    }
    
    .galeria-item:hover .galeria-overlay {
        opacity: 1;
    }
    
    .galeria-overlay i {
        color: white;
        font-size: 24px;
    }
    
    .btn-voltar {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: var(--pircom-gradient);
        color: white;
        padding: 12px 30px;
        border-radius: 25px;
        text-decoration: none;
        transition: all 0.3s;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
    }
    
    .btn-voltar:hover {
        background: var(--pircom-secondary);
        color: white;
        transform: translateX(-5px);
        box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
    }
    
    .pircom-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(37, 99, 235, 0.1);
        color: var(--pircom-primary);
        padding: 10px 20px;
        border-radius: 30px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    
    .pircom-badge i {
        font-size: 18px;
    }
    
    @media (max-width: 768px) {
        .movimento-detalhes {
            padding: 100px 0 30px;
        }
        
        .movimento-hero-image {
            height: 300px;
        }
        
        .movimento-header-content {
            padding: 25px;
        }
        
        .movimento-titulo {
            font-size: 28px;
        }
        
        .movimento-descricao {
            font-size: 16px;
        }
        
        .movimento-info {
            gap: 15px;
        }
        
        .galeria-section {
            padding: 25px;
        }
        
        .galeria-grid {
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 15px;
        }
        
        .galeria-item {
            height: 150px;
        }
        
        .galeria-titulo {
            font-size: 24px;
        }
    }
</style>

<section class="movimento-detalhes">
    <div class="container">
        <!-- Header do Movimento -->
        <div class="movimento-header">
            <?php if ($movimento['imagem_principal']): ?>
                <img src="<?php echo htmlspecialchars($movimento['imagem_principal']); ?>" 
                     alt="<?php echo htmlspecialchars($movimento['titulo']); ?>" 
                     class="movimento-hero-image">
            <?php endif; ?>
            
            <div class="movimento-header-content">
                <!-- Badge PIRCOM -->
                <div class="pircom-badge">
                    <i class="bi bi-heart-pulse-fill"></i>
                    <span>Área de Intervenção PIRCOM</span>
                </div>
                
                <?php if ($movimento['tema']): ?>
                    <span class="movimento-categoria">
                        <?php echo htmlspecialchars($movimento['tema']); ?>
                    </span>
                <?php endif; ?>
                
                <h1 class="movimento-titulo">
                    <?php echo htmlspecialchars($movimento['titulo']); ?>
                </h1>
                
                <div class="movimento-info">
                    <?php if ($movimento['data_evento']): ?>
                        <div class="movimento-info-item">
                            <i class="bi bi-calendar3"></i>
                            <span><?php echo date('d \d\e F \d\e Y', strtotime($movimento['data_evento'])); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($movimento['local']): ?>
                        <div class="movimento-info-item">
                            <i class="bi bi-geo-alt-fill"></i>
                            <span><?php echo htmlspecialchars($movimento['local']); ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (count($fotos) > 0): ?>
                        <div class="movimento-info-item">
                            <i class="bi bi-images"></i>
                            <span><?php echo count($fotos); ?> foto<?php echo count($fotos) > 1 ? 's' : ''; ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="movimento-info-item">
                        <i class="bi bi-eye-fill"></i>
                        <span><?php echo number_format($movimento['visualizacoes'], 0, ',', '.'); ?> visualizações</span>
                    </div>
                </div>
                
                <div class="movimento-descricao">
                    <?php 
                    // Manter emojis e caracteres especiais
                    echo nl2br(htmlspecialchars($movimento['descricao']));
                    ?>
                </div>
            </div>
        </div>

        <!-- Galeria de Fotos -->
        <?php if (count($fotos) > 0): ?>
            <div class="galeria-section">
                <h2 class="galeria-titulo">Galeria de Imagens</h2>
                <div class="galeria-grid">
                    <?php foreach ($fotos as $foto): ?>
                        <a href="<?php echo htmlspecialchars($foto['foto']); ?>" 
                           class="galeria-item glightbox" 
                           data-gallery="movimento-<?php echo $movimento_id; ?>"
                           <?php if (!empty($foto['legenda'])): ?>
                           data-glightbox="title: <?php echo htmlspecialchars($foto['legenda']); ?>"
                           <?php endif; ?>>
                            <img src="<?php echo htmlspecialchars($foto['foto']); ?>" 
                                 alt="<?php echo htmlspecialchars($foto['legenda'] ?? 'Imagem da causa PIRCOM'); ?>">
                            <div class="galeria-overlay">
                                <i class="bi bi-zoom-in"></i>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Botão Voltar -->
        <div class="text-center mt-5">
            <a href="movimentos.php" class="btn-voltar">
                <i class="bi bi-arrow-left"></i>
                Voltar para Causas
            </a>
        </div>
    </div>
</section>

<!-- GLightbox JS -->
<script src="https://cdn.jsdelivr.net/gh/mcstudios/glightbox/dist/js/glightbox.min.js"></script>

<!-- Initialize Plugins -->
<script>
    // Initialize GLightbox
    const lightbox = GLightbox({
        touchNavigation: true,
        loop: true,
        autoplayVideos: false,
        closeButton: true,
        closeOnOutsideClick: true,
        skin: 'clean'
    });
</script>

<?php 
include 'includes/footer.php';
$conn->close();
?>