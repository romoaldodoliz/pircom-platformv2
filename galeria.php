<?php
$page_title = "Strong Woman - Galeria";
include 'includes/navbar.php';
?>

<style>
    .gallery-filter {
        text-align: center;
        margin-bottom: 40px;
    }

    .gallery-filter button {
        background: transparent;
        border: 2px solid var(--secondary-color);
        color: var(--secondary-color);
        padding: 10px 25px;
        margin: 5px;
        border-radius: 30px;
        font-weight: 600;
        transition: all 0.3s;
        cursor: pointer;
    }

    .gallery-filter button:hover,
    .gallery-filter button.active {
        background: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(251, 10, 10, 0.3);
    }

    .gallery-item {
        position: relative;
        overflow: hidden;
        border-radius: 15px;
        margin-bottom: 30px;
        cursor: pointer;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transition: all 0.4s;
    }

    .gallery-item:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(251, 10, 10, 0.2);
    }

    .gallery-item img {
        width: 100%;
        height: 300px;
        object-fit: cover;
        transition: transform 0.4s;
    }

    .gallery-item:hover img {
        transform: scale(1.1);
    }

    /* Estilos específicos para cards de vídeo */
    .video-card {
        border: 2px solid var(--primary-color);
    }

    .video-card .video-thumbnail {
        position: relative;
        width: 100%;
        height: 300px;
        background: #000;
        overflow: hidden;
    }

    .video-card .video-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s;
    }

    .video-card:hover .video-thumbnail img {
        transform: scale(1.1);
    }

    .video-play-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(251, 10, 10, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
    }

    .video-card:hover .video-play-overlay {
        background: rgba(251, 10, 10, 0.2);
    }

    .video-play-icon {
        font-size: 70px;
        color: white;
        text-shadow: 0 2px 10px rgba(0,0,0,0.5);
        transition: all 0.3s;
    }

    .video-card:hover .video-play-icon {
        transform: scale(1.2);
        color: var(--primary-color);
    }

    .video-badge {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--primary-color);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        z-index: 2;
    }

    .gallery-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(21, 21, 21, 0.9), transparent);
        padding: 20px;
        transform: translateY(100%);
        transition: transform 0.3s;
    }

    .gallery-item:hover .gallery-overlay {
        transform: translateY(0);
    }

    .gallery-overlay h5 {
        color: white;
        font-weight: 700;
        margin-bottom: 5px;
    }

    .gallery-overlay p {
        color: rgba(255,255,255,0.9);
        font-size: 14px;
        margin: 0;
    }

    .gallery-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: var(--primary-color);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        z-index: 1;
    }

    .lightbox {
        display: none;
        position: fixed;
        z-index: 9999;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
    }

    .lightbox.active {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .lightbox-content {
        max-width: 90%;
        max-height: 90%;
        position: relative;
    }

    .lightbox-content img {
        max-width: 100%;
        max-height: 85vh;
        border-radius: 10px;
    }

    .video-container {
        position: relative;
        width: 800px;
        max-width: 90vw;
        height: 450px;
        max-height: 80vh;
    }

    .video-container iframe {
        width: 100%;
        height: 100%;
        border-radius: 10px;
        border: none;
    }

    .lightbox-info {
        background: white;
        padding: 20px;
        border-radius: 10px;
        margin-top: 20px;
        text-align: center;
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 30px;
        font-size: 40px;
        color: white;
        cursor: pointer;
        transition: all 0.3s;
        z-index: 10000;
    }

    .lightbox-close:hover {
        color: var(--primary-color);
        transform: rotate(90deg);
    }

    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        font-size: 40px;
        color: white;
        cursor: pointer;
        transition: all 0.3s;
        background: rgba(255,255,255,0.1);
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
    }

    .lightbox-nav:hover {
        background: var(--primary-color);
    }

    .lightbox-prev { left: 30px; }
    .lightbox-next { right: 30px; }

    /* Esconder navegação para vídeos */
    .video-mode .lightbox-nav {
        display: none;
    }
</style>

<section class="py-5" style="min-height: 70vh;">
    <div class="container">
        <div class="section-title">
            <h2>GALERIA</h2>
            <p>Momentos Strong Woman</p>
        </div>

        <?php
        include('config/conexao.php');
        
        $tipos_sql = "SELECT DISTINCT tipo FROM galeria WHERE tipo IS NOT NULL AND tipo != '' ORDER BY tipo";
        $tipos_result = @$conn->query($tipos_sql);
        $tipos = [];
        if ($tipos_result && $tipos_result->num_rows > 0) {
            while ($tipo_row = $tipos_result->fetch_assoc()) {
                $tipos[] = $tipo_row['tipo'];
            }
        }
        ?>

        <?php if (count($tipos) > 0): ?>
        <div class="gallery-filter">
            <button class="filter-btn active" data-filter="all">
                <i class="bi bi-grid-3x3"></i> Todos
            </button>
            <button class="filter-btn" data-filter="imagem">
                <i class="bi bi-image"></i> Fotos
            </button>
            <button class="filter-btn" data-filter="video">
                <i class="bi bi-play-btn"></i> Vídeos
            </button>
            <?php foreach ($tipos as $tipo): ?>
                <?php if ($tipo !== 'imagem' && $tipo !== 'video'): ?>
                <button class="filter-btn" data-filter="<?php echo htmlspecialchars($tipo); ?>">
                    <i class="bi bi-tag"></i> <?php echo htmlspecialchars(ucfirst($tipo)); ?>
                </button>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="row" id="gallery-grid">
            <?php
            $sql = "SELECT * FROM galeria ORDER BY created_date DESC";
            $result = @$conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $tipo = !empty($row["tipo"]) ? htmlspecialchars($row["tipo"]) : 'outros';
                    $isVideo = ($tipo === 'video');
                    $isImage = ($tipo === 'imagem');
                    
                    // Classes diferentes para vídeos e imagens
                    $cardClass = $isVideo ? 'video-card' : 'image-card';
                    $contentType = $tipo; // Usa o próprio tipo como content type
                    
                    echo '<div class="col-lg-4 col-md-6 gallery-item-wrapper" data-category="' . $tipo . '" data-content-type="' . $contentType . '">';
                    echo '<div class="gallery-item ' . $cardClass . '" data-id="' . $row["id"] . '" data-titulo="' . htmlspecialchars($row["titulo"]) . '" data-descricao="' . htmlspecialchars($row["descricao"]) . '" data-tipo="' . $tipo . '"';
                    
                    // Adicionar atributo específico para vídeos
                    if ($isVideo) {
                        echo ' data-video-url="' . htmlspecialchars($row["link"]) . '"';
                    }
                    
                    echo '>';
                    
                    if (!empty($row["tipo"]) && $row["tipo"] !== 'imagem' && $row["tipo"] !== 'video') {
                        echo '<span class="gallery-badge">' . htmlspecialchars($row["tipo"]) . '</span>';
                    }
                    
                    // Conteúdo diferente baseado no tipo
                    if ($isVideo) {
                        // Para vídeos - card específico com thumbnail do YouTube
                        $videoId = extractYouTubeId($row["link"]);
                        $thumbnailUrl = $videoId ? "https://img.youtube.com/vi/$videoId/hqdefault.jpg" : '';
                        
                        echo '<div class="video-thumbnail">';
                        echo '<span class="video-badge"><i class="bi bi-play-fill"></i> Vídeo</span>';
                        
                        if ($thumbnailUrl) {
                            echo '<img src="' . $thumbnailUrl . '" alt="' . htmlspecialchars($row["titulo"]) . '" onerror="this.style.display=\'none\'">';
                        } else {
                            // Fallback se não conseguir pegar thumbnail
                            echo '<div style="background: linear-gradient(45deg, var(--primary-color), var(--secondary-color)); width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;">';
                            echo '<i class="bi bi-play-circle-fill" style="font-size: 80px; color: white;"></i>';
                            echo '</div>';
                        }
                        
                        echo '<div class="video-play-overlay">';
                        echo '<div class="video-play-icon"><i class="bi bi-play-circle-fill"></i></div>';
                        echo '</div>';
                        echo '</div>';
                    } else if ($isImage) {
                        // Para imagens - comportamento original
                        if (!empty($row["foto"])) {
                            $imagemBLOB = base64_encode($row["foto"]);
                            echo '<img src="data:image/jpeg;base64,' . $imagemBLOB . '" alt="' . htmlspecialchars($row["titulo"]) . '">';
                        } else {
                            // Fallback para imagem vazia
                            echo '<div style="background: #f8f9fa; width: 100%; height: 300px; display: flex; align-items: center; justify-content: center;">';
                            echo '<i class="bi bi-image" style="font-size: 50px; color: #6c757d;"></i>';
                            echo '</div>';
                        }
                    } else {
                        // Para outros tipos (fallback)
                        if (!empty($row["foto"])) {
                            $imagemBLOB = base64_encode($row["foto"]);
                            echo '<img src="data:image/jpeg;base64,' . $imagemBLOB . '" alt="' . htmlspecialchars($row["titulo"]) . '">';
                        } else {
                            echo '<div style="background: #f8f9fa; width: 100%; height: 300px; display: flex; align-items: center; justify-content: center;">';
                            echo '<i class="bi bi-file-earmark" style="font-size: 50px; color: #6c757d;"></i>';
                            echo '</div>';
                        }
                    }
                    
                    echo '<div class="gallery-overlay">';
                    echo '<h5>' . htmlspecialchars($row["titulo"]) . '</h5>';
                    
                    if (!empty($row["descricao"])) {
                        $descricao = htmlspecialchars($row["descricao"]);
                        echo '<p>' . (strlen($descricao) > 80 ? substr($descricao, 0, 80) . '...' : $descricao) . '</p>';
                    }
                    
                    echo '</div></div></div>';
                }
            } else {
                echo '<div class="col-12 text-center py-5">';
                echo '<i class="bi bi-images" style="font-size: 80px; color: var(--primary-color);"></i>';
                echo '<h4 class="mt-3">Galeria Vazia</h4>';
                echo '<p>Nenhum conteúdo disponível no momento.</p>';
                echo '</div>';
            }

            $conn->close();

            // Função para extrair ID do YouTube
            function extractYouTubeId($url) {
                $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
                preg_match($pattern, $url, $matches);
                return isset($matches[1]) ? $matches[1] : null;
            }
            ?>
        </div>
    </div>
</section>

<div class="lightbox" id="lightbox">
    <span class="lightbox-close" id="lightbox-close">&times;</span>
    <span class="lightbox-nav lightbox-prev" id="lightbox-prev"><i class="bi bi-chevron-left"></i></span>
    <span class="lightbox-nav lightbox-next" id="lightbox-next"><i class="bi bi-chevron-right"></i></span>
    <div class="lightbox-content">
        <img id="lightbox-img" src="" alt="" style="display: none;">
        <div class="video-container" id="video-container" style="display: none;">
            <iframe id="lightbox-video" src="" allow="autoplay; fullscreen" allowfullscreen></iframe>
        </div>
        <div class="lightbox-info">
            <h4 id="lightbox-title"></h4>
            <p id="lightbox-desc"></p>
            <span id="lightbox-type" class="badge" style="background: var(--primary-color);"></span>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
document.querySelectorAll('.filter-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        const filter = this.getAttribute('data-filter');
        document.querySelectorAll('.gallery-item-wrapper').forEach(item => {
            const category = item.getAttribute('data-category');
            const contentType = item.getAttribute('data-content-type');
            
            let shouldShow = false;
            
            if (filter === 'all') {
                shouldShow = true;
            } else if (filter === 'imagem') {
                shouldShow = contentType === 'imagem';
            } else if (filter === 'video') {
                shouldShow = contentType === 'video';
            } else {
                shouldShow = category === filter;
            }
            
            item.style.display = shouldShow ? 'block' : 'none';
        });
        
        // Atualizar itens do lightbox após filtrar
        setTimeout(updateGalleryItems, 100);
    });
});

const lightbox = document.getElementById('lightbox');
const lightboxImg = document.getElementById('lightbox-img');
const lightboxVideo = document.getElementById('lightbox-video');
const videoContainer = document.getElementById('video-container');
const lightboxTitle = document.getElementById('lightbox-title');
const lightboxDesc = document.getElementById('lightbox-desc');
const lightboxType = document.getElementById('lightbox-type');

let currentIndex = 0;
let galleryItems = [];

// Extrair ID do YouTube
function extractYouTubeId(url) {
    const pattern = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
    const matches = url.match(pattern);
    return matches ? matches[1] : null;
}

// Coletar apenas itens visíveis para o lightbox
function updateGalleryItems() {
    galleryItems = [];
    document.querySelectorAll('.gallery-item-wrapper:not([style*="display: none"]) .gallery-item').forEach((item, index) => {
        const videoUrl = item.getAttribute('data-video-url');
        const isVideo = !!videoUrl;
        const tipo = item.getAttribute('data-tipo');
        
        galleryItems.push({
            element: item,
            src: isVideo ? null : item.querySelector('img').src,
            videoUrl: videoUrl,
            isVideo: isVideo,
            tipo: tipo,
            titulo: item.getAttribute('data-titulo'),
            descricao: item.getAttribute('data-descricao')
        });
    });
}

// Inicializar eventos dos itens da galeria
function initializeGalleryItems() {
    document.querySelectorAll('.gallery-item').forEach((item, index) => {
        item.addEventListener('click', () => {
            // Encontrar o índice atual baseado nos itens visíveis
            const visibleItems = Array.from(document.querySelectorAll('.gallery-item-wrapper:not([style*="display: none"]) .gallery-item'));
            currentIndex = visibleItems.indexOf(item);
            updateGalleryItems();
            openLightbox();
        });
    });
}

function openLightbox() {
    if (galleryItems.length === 0) return;
    
    const item = galleryItems[currentIndex];
    
    if (item.isVideo) {
        // Modo vídeo
        lightbox.classList.add('video-mode');
        lightboxImg.style.display = 'none';
        videoContainer.style.display = 'block';
        
        const videoId = extractYouTubeId(item.videoUrl);
        if (videoId) {
            lightboxVideo.src = `https://www.youtube.com/embed/${videoId}?autoplay=1&rel=0`;
        }
    } else {
        // Modo imagem
        lightbox.classList.remove('video-mode');
        videoContainer.style.display = 'none';
        lightboxImg.style.display = 'block';
        lightboxImg.src = item.src;
    }
    
    lightboxTitle.textContent = item.titulo;
    lightboxDesc.textContent = item.descricao;
    lightboxType.textContent = item.tipo;
    lightbox.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeLightbox() {
    // Parar vídeo ao fechar
    if (lightboxVideo.src) {
        lightboxVideo.src = '';
    }
    
    lightbox.classList.remove('active');
    document.body.style.overflow = 'auto';
}

document.getElementById('lightbox-close').addEventListener('click', closeLightbox);

document.getElementById('lightbox-next').addEventListener('click', () => {
    // Parar vídeo atual antes de mudar
    if (lightboxVideo.src) {
        lightboxVideo.src = '';
    }
    currentIndex = (currentIndex + 1) % galleryItems.length;
    openLightbox();
});

document.getElementById('lightbox-prev').addEventListener('click', () => {
    // Parar vídeo atual antes de mudar
    if (lightboxVideo.src) {
        lightboxVideo.src = '';
    }
    currentIndex = (currentIndex - 1 + galleryItems.length) % galleryItems.length;
    openLightbox();
});

lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) closeLightbox();
});

document.addEventListener('keydown', (e) => {
    if (lightbox.classList.contains('active')) {
        if (e.key === 'Escape') closeLightbox();
        
        // Navegação apenas para imagens (não para vídeos)
        if (!lightbox.classList.contains('video-mode')) {
            if (e.key === 'ArrowRight') document.getElementById('lightbox-next').click();
            if (e.key === 'ArrowLeft') document.getElementById('lightbox-prev').click();
        }
    }
});

// Inicializar
updateGalleryItems();
initializeGalleryItems();
</script>