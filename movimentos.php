<?php
$page_title = "PIRCOM - Nossas Causas e Áreas de Intervenção";
include 'includes/navbar.php';
?>

<section class="py-5" style="min-height: 70vh;">
    <div class="container">
        <div class="section-title mb-5">
            <h2>NOSSAS CAUSAS E ÁREAS DE INTERVENÇÃO</h2>
            <p>Plataforma Inter-Religiosa de Comunicação para a Saúde</p>
        </div>
        <div class="row g-4">
            <?php
            include('config/conexao.php');
            $sql = "SELECT * FROM movimentos ORDER BY id DESC";
            $result = @$conn->query($sql);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $id = (int)$row["id"];
                    $titulo = htmlspecialchars($row["titulo"] ?? '', ENT_QUOTES, 'UTF-8');
                    $tema = htmlspecialchars($row["tema"] ?? '', ENT_QUOTES, 'UTF-8');
                    $descricao = htmlspecialchars($row["descricao"] ?? '', ENT_QUOTES, 'UTF-8');
                    $descricao_curta = mb_strlen($descricao) > 100 ? mb_substr($descricao, 0, 100) . '...' : $descricao;
                    $imagem = !empty($row["imagem_principal"]) ? htmlspecialchars($row["imagem_principal"]) : '';
                    
                    echo '<div class="col-lg-4 col-md-6">';
                    echo '<div class="card h-100 shadow-sm border-0 movimento-card">';
                    
                    // Imagem
                    if (!empty($imagem)) {
                        echo '<div class="position-relative overflow-hidden" style="height: 250px;">';
                        echo '<img src="' . $imagem . '" class="card-img-top w-100 h-100" style="object-fit: cover; transition: transform 0.3s;" alt="' . $titulo . '">';
                        echo '</div>';
                    } else {
                        echo '<div class="position-relative overflow-hidden d-flex align-items-center justify-content-center" style="height: 250px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">';
                        echo '<i class="bi bi-heart-pulse" style="font-size: 80px; color: rgba(255,255,255,0.3);"></i>';
                        echo '</div>';
                    }
                    
                    // Conteúdo do card
                    echo '<div class="card-body d-flex flex-column">';
                    echo '<h5 class="card-title fw-bold mb-2" style="color: #333;">' . $titulo . '</h5>';
                    
                    if (!empty($tema)) {
                        echo '<p class="text-muted small mb-3"><i class="bi bi-tag-fill me-2" style="color: #2563eb;"></i>' . $tema . '</p>';
                    }
                    
                    echo '<p class="card-text text-muted flex-grow-1" style="line-height: 1.6;">' . $descricao_curta . '</p>';
                    
                    echo '<div class="mt-3">';
                    echo '<a href="movimento-detalhes.php?id=' . $id . '" class="btn btn-primary w-100" style="font-weight: 500;">';
                    echo '<i class="bi bi-arrow-right-circle me-2"></i>Ver Detalhes';
                    echo '</a>';
                    echo '</div>';
                    
                    echo '</div>'; // card-body
                    echo '</div>'; // card
                    echo '</div>'; // col
                }
            } else {
                echo '<div class="col-12">';
                echo '<div class="text-center py-5">';
                echo '<div class="mb-4">';
                echo '<i class="bi bi-folder-x" style="font-size: 80px; color: #2563eb; opacity: 0.3;"></i>';
                echo '</div>';
                echo '<h4 class="fw-bold mb-3">Nenhuma causa disponível no momento</h4>';
                echo '<p class="text-muted mb-0">Volte em breve para conhecer as áreas de intervenção da PIRCOM!</p>';
                echo '</div>';
                echo '</div>';
            }
            $conn->close();
            ?>
        </div>
    </div>
</section>

<style>
.movimento-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
}

.movimento-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2) !important;
}

.movimento-card:hover img {
    transform: scale(1.08);
}

.movimento-card .card-body {
    padding: 1.5rem;
}

.btn-primary {
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(37, 99, 235, 0.4);
    background: linear-gradient(135deg, #1e40af 0%, #1e3a8a 100%);
}

.section-title {
    text-align: center;
    margin-bottom: 3rem;
}

.section-title h2 {
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1rem;
    font-size: 2.5rem;
    position: relative;
    display: inline-block;
}

.section-title h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(135deg, #2563eb 0%, #764ba2 100%);
    border-radius: 2px;
}

.section-title p {
    color: #6b7280;
    font-size: 1.1rem;
    margin-top: 1.5rem;
}

@media (max-width: 768px) {
    .movimento-card {
        margin-bottom: 1.5rem;
    }
    
    .section-title h2 {
        font-size: 2rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>