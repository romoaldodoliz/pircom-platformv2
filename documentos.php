<?php
$page_title = "Documentos - Pircom";
include 'config/conexao.php';

// Incrementar download se houver
if (isset($_GET['download']) && isset($_GET['id'])) {
    $doc_id = intval($_GET['id']);
    $stmt = $conn->prepare("UPDATE documentos SET downloads = downloads + 1 WHERE id = ?");
    $stmt->bind_param("i", $doc_id);
    $stmt->execute();
    $stmt->close();
}

// Buscar documentos publicados
$categoria_filtro = isset($_GET['categoria']) ? $_GET['categoria'] : '';

if ($categoria_filtro) {
    $stmt = $conn->prepare("SELECT * FROM documentos WHERE status = 'publicado' AND categoria = ? ORDER BY created_at DESC");
    $stmt->bind_param("s", $categoria_filtro);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    $result = $conn->query("SELECT * FROM documentos WHERE status = 'publicado' ORDER BY created_at DESC");
}

// Buscar categorias disponíveis
$categorias = $conn->query("SELECT DISTINCT categoria FROM documentos WHERE status = 'publicado' ORDER BY categoria");

include 'includes/navbar.php';
?>

<style>
    .documentos-page {
        padding: 120px 0 60px;
        background: linear-gradient(135deg, #f7f8fa 0%, #fff 100%);
        min-height: 100vh;
    }
    
    .page-header {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .page-title {
        font-size: 48px;
        font-weight: 700;
        color: var(--secondary-color);
        margin-bottom: 15px;
        position: relative;
        display: inline-block;
        padding-bottom: 15px;
    }

    .page-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: var(--primary-color);
        border-radius: 2px;
    }
    
    .page-subtitle {
        font-size: 18px;
        color: #666;
        margin-bottom: 0;
    }

    .intro-banner {
        background: linear-gradient(135deg, var(--primary-color) 0%, #c70808 100%);
        color: white;
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 50px;
        text-align: center;
    }

    .intro-banner h3 {
        font-weight: 700;
        margin-bottom: 15px;
        font-size: 24px;
    }

    .intro-banner p {
        font-size: 16px;
        margin-bottom: 0;
        opacity: 0.95;
        line-height: 1.8;
    }

    .intro-stats {
        display: flex;
        justify-content: center;
        gap: 40px;
        margin-top: 25px;
        flex-wrap: wrap;
    }

    .intro-stat {
        text-align: center;
    }

    .intro-stat-number {
        font-size: 32px;
        font-weight: 700;
        display: block;
        margin-bottom: 5px;
    }

    .intro-stat-label {
        font-size: 14px;
        opacity: 0.9;
    }
    
    .filter-bar {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-bottom: 40px;
        flex-wrap: wrap;
    }
    
    .filter-btn {
        padding: 10px 25px;
        border: 2px solid #e0e0e0;
        background: white;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s;
        font-weight: 600;
        color: #666;
        text-decoration: none;
    }
    
    .filter-btn:hover,
    .filter-btn.active {
        border-color: var(--primary-color);
        background: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(251, 10, 10, 0.3);
    }
    
    .documents-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 30px;
    }
    
    .document-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.3s;
        cursor: pointer;
    }
    
    .document-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(251, 10, 10, 0.15);
    }
    
    .document-icon {
        background: linear-gradient(135deg, var(--primary-color) 0%, #d00909 100%);
        padding: 40px;
        text-align: center;
        position: relative;
    }
    
    .document-icon i {
        font-size: 80px;
        color: white;
        opacity: 0.9;
    }
    
    .document-category {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255,255,255,0.25);
        backdrop-filter: blur(10px);
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        color: white;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .document-body {
        padding: 25px;
    }
    
    .document-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--secondary-color);
        margin-bottom: 10px;
        min-height: 50px;
    }
    
    .document-description {
        color: #666;
        font-size: 14px;
        line-height: 1.6;
        margin-bottom: 20px;
        min-height: 60px;
    }
    
    .document-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 15px;
        border-top: 1px solid #f0f0f0;
    }
    
    .document-meta span {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 13px;
        color: #999;
    }
    
    .document-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }
    
    .btn-view, .btn-download {
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        flex: 1;
        justify-content: center;
    }
    
    .btn-view {
        background: var(--primary-color);
        color: white;
    }
    
    .btn-view:hover {
        background: #d00909;
        color: white;
        transform: scale(1.05);
    }
    
    .btn-download {
        background: var(--secondary-color);
        color: white;
    }
    
    .btn-download:hover {
        background: #000;
        color: white;
        transform: scale(1.05);
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        background: white;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .empty-state i {
        font-size: 80px;
        color: var(--primary-color);
        opacity: 0.5;
    }

    .empty-state h4 {
        font-size: 24px;
        color: var(--secondary-color);
        font-weight: 700;
        margin: 20px 0 10px;
    }

    .empty-state p {
        font-size: 16px;
        color: #999;
        margin-bottom: 0;
    }

    .info-box {
        background: white;
        border-radius: 15px;
        padding: 30px;
        margin-top: 50px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .info-box h4 {
        color: var(--secondary-color);
        font-weight: 700;
        margin-bottom: 15px;
    }

    .info-box ul {
        margin: 0;
        padding-left: 25px;
    }

    .info-box li {
        color: #666;
        margin-bottom: 10px;
        line-height: 1.6;
    }
    
    @media (max-width: 768px) {
        .documentos-page {
            padding: 100px 0 40px;
        }
        
        .page-title {
            font-size: 32px;
        }

        .intro-banner {
            padding: 30px 20px;
        }

        .intro-banner h3 {
            font-size: 20px;
        }

        .intro-stats {
            gap: 20px;
        }
        
        .documents-grid {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        .filter-bar {
            flex-direction: column;
            align-items: stretch;
        }
        
        .filter-btn {
            text-align: center;
        }

        .document-actions {
            flex-direction: column;
        }
    }
</style>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<section class="documentos-page">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">Centro de Documentos</h1>
            <p class="page-subtitle">Acesse relatórios, estudos e recursos sobre saúde comunitária</p>
        </div>

        <!-- Banner Introdutório -->
        <div class="intro-banner">
            <h3><i class="bi bi-file-earmark-text me-2"></i>Transparência e Conhecimento</h3>
            <p>A PIRCOM acredita na importância da transparência e partilha de conhecimento. Aqui você encontra documentos que refletem o nosso trabalho desde 2006 na promoção da saúde através da colaboração inter-religiosa entre comunidades cristãs, muçulmanas, hindus e bahai.</p>
            
            <div class="intro-stats">
                <div class="intro-stat">
                    <span class="intro-stat-number"><i class="bi bi-shield-check"></i></span>
                    <span class="intro-stat-label">Transparência Total</span>
                </div>
                <div class="intro-stat">
                    <span class="intro-stat-number"><i class="bi bi-book"></i></span>
                    <span class="intro-stat-label">Recursos Educativos</span>
                </div>
                <div class="intro-stat">
                    <span class="intro-stat-number"><i class="bi bi-people"></i></span>
                    <span class="intro-stat-label">Acesso Público</span>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="filter-bar">
            <a href="documentos.php" class="filter-btn <?php echo !$categoria_filtro ? 'active' : ''; ?>">
                <i class="bi bi-grid-3x3-gap"></i> Todos
            </a>
            <?php while ($cat = $categorias->fetch_assoc()): ?>
                <a href="?categoria=<?php echo urlencode($cat['categoria']); ?>" 
                   class="filter-btn <?php echo $categoria_filtro === $cat['categoria'] ? 'active' : ''; ?>">
                    <i class="bi bi-folder"></i> <?php echo htmlspecialchars($cat['categoria']); ?>
                </a>
            <?php endwhile; ?>
        </div>

        <!-- Grid de Documentos -->
        <div class="documents-grid">
            <?php while ($doc = $result->fetch_assoc()): ?>
                <div class="document-card">
                    <div class="document-icon">
                        <i class="bi bi-file-earmark-pdf-fill"></i>
                        <span class="document-category"><?php echo htmlspecialchars($doc['categoria']); ?></span>
                    </div>
                    <div class="document-body">
                        <h3 class="document-title"><?php echo htmlspecialchars($doc['titulo']); ?></h3>
                        <p class="document-description">
                            <?php 
                            echo $doc['descricao'] 
                                ? htmlspecialchars(substr($doc['descricao'], 0, 100)) . (strlen($doc['descricao']) > 100 ? '...' : '')
                                : 'Documento disponível para consulta e download.';
                            ?>
                        </p>
                        <div class="document-meta">
                            <span><i class="bi bi-download"></i> <?php echo $doc['downloads']; ?> downloads</span>
                            <span><i class="bi bi-calendar3"></i> <?php echo date('d/m/Y', strtotime($doc['created_at'])); ?></span>
                        </div>
                        <div class="document-actions">
                            <a href="<?php echo $doc['arquivo']; ?>" target="_blank" class="btn-view">
                                <i class="bi bi-eye-fill"></i> Visualizar
                            </a>
                            <a href="<?php echo $doc['arquivo']; ?>?download=1&id=<?php echo $doc['id']; ?>" download class="btn-download">
                                <i class="bi bi-download"></i> Baixar
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>

        <?php if ($result->num_rows === 0): ?>
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <h4>Nenhum Documento Encontrado</h4>
                <p>
                    <?php if ($categoria_filtro): ?>
                        Não há documentos disponíveis na categoria "<?php echo htmlspecialchars($categoria_filtro); ?>".
                    <?php else: ?>
                        Em breve disponibilizaremos documentos sobre o nosso trabalho nas comunidades.
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- Informações Adicionais -->
        <div class="info-box">
            <h4><i class="bi bi-info-circle-fill text-danger me-2"></i>Sobre os Nossos Documentos</h4>
            <div class="row">
                <div class="col-md-6">
                    <h6 style="color: var(--secondary-color); font-weight: 600; margin-top: 15px;">Áreas Documentadas:</h6>
                    <ul>
                        <li><strong>Saúde Materno-Infantil</strong> - Relatórios e estudos sobre cuidados a grávidas e crianças</li>
                        <li><strong>Prevenção da Malária</strong> - Materiais educativos e resultados de campanhas</li>
                        <li><strong>HIV/SIDA</strong> - Estratégias de prevenção e sensibilização comunitária</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 style="color: var(--secondary-color); font-weight: 600; margin-top: 15px;">Também Disponíveis:</h6>
                    <ul>
                        <li><strong>Nutrição</strong> - Guias e recursos sobre alimentação saudável</li>
                        <li><strong>Construção da Paz</strong> - Documentos sobre diálogo inter-religioso</li>
                        <li><strong>Relatórios Anuais</strong> - Prestação de contas e impacto das nossas ações</li>
                    </ul>
                </div>
            </div>
            <p style="color: #999; font-size: 14px; margin-top: 20px; margin-bottom: 0;">
                <i class="bi bi-shield-check me-1"></i>
                Todos os documentos são de acesso público e refletem o nosso compromisso com a transparência e partilha de conhecimento.
            </p>
        </div>
    </div>
</section>

<?php
include 'includes/footer.php';
$conn->close();
?>