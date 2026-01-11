<?php
$page_title = "PIRCOM - Notícias";
include 'includes/navbar.php';
?>

<section class="py-5" style="min-height: 70vh;">
    <div class="container">
        <div class="section-title mb-5">
            <h2>NOTÍCIAS</h2>
            <p>Últimas Notícias da PIRCOM - Plataforma Inter-Religiosa de Comunicação para a Saúde</p>
        </div>
        <div class="row g-4">
            <?php
            include('config/conexao.php');
            
            // Configuração da paginação
            $registros_por_pagina = 6;
            $pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            $pagina_atual = max(1, $pagina_atual);
            $offset = ($pagina_atual - 1) * $registros_por_pagina;
            
            // Contar total de notícias
            $sql_count = "SELECT COUNT(*) as total FROM noticias";
            $result_count = $conn->query($sql_count);
            $total_registros = $result_count->fetch_assoc()['total'];
            $total_paginas = ceil($total_registros / $registros_por_pagina);
            
            // Buscar notícias com limite e offset
            $sql = "SELECT * FROM noticias ORDER BY id DESC LIMIT ? OFFSET ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $registros_por_pagina, $offset);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Sanitizar dados
                    $id = (int)$row["id"];
                    $titulo = htmlspecialchars($row["titulo"] ?? '', ENT_QUOTES, 'UTF-8');
                    $descricao = htmlspecialchars($row["descricao"] ?? '', ENT_QUOTES, 'UTF-8');
                    $descricao_curta = mb_strlen($descricao) > 150 ? mb_substr($descricao, 0, 150) . '...' : $descricao;
                    
                    // Processar imagem
                    $imagem = '';
                    if (!empty($row["foto"])) {
                        $imagemBLOB = base64_encode($row["foto"]);
                        $imagem = 'data:image/jpeg;base64,' . $imagemBLOB;
                    } else {
                        // Placeholder
                        $imagem = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="250" viewBox="0 0 400 250"%3E%3Crect fill="%23000000" width="400" height="250"/%3E%3Ctext fill="%23ff8c00" font-family="Arial" font-size="16" x="50%25" y="50%25" text-anchor="middle" dominant-baseline="middle"%3ENotícia PIRCOM%3C/text%3E%3C/svg%3E';
                    }
                    
                    // Processar data
                    $data_publicacao = '';
                    if (!empty($row["data_publicacao"])) {
                        $timestamp = strtotime($row["data_publicacao"]);
                        if ($timestamp !== false) {
                            $data_publicacao = date('d/m/Y', $timestamp);
                        }
                    }
                    
                    // Processar autor se existir
                    $autor = !empty($row["autor"]) ? htmlspecialchars($row["autor"]) : 'Equipe PIRCOM';
                    
                    echo '<div class="col-lg-4 col-md-6">';
                    echo '<div class="card h-100 shadow-sm border-0 noticia-card">';
                    
                    // Badge de categoria/tema
                    if (!empty($row["categoria"])) {
                        echo '<div class="position-absolute top-0 start-0 m-3" style="z-index: 10;">';
                        echo '<span class="badge badge-categoria px-3 py-2 shadow-sm">' . htmlspecialchars($row["categoria"]) . '</span>';
                        echo '</div>';
                    }
                    
                    // Imagem
                    echo '<div class="position-relative overflow-hidden" style="height: 250px;">';
                    echo '<img src="' . $imagem . '" class="card-img-top w-100 h-100" style="object-fit: cover; transition: transform 0.3s;" alt="' . $titulo . '">';
                    echo '</div>';
                    
                    // Corpo do card
                    echo '<div class="card-body d-flex flex-column">';
                    
                    // Data de publicação
                    if (!empty($data_publicacao)) {
                        echo '<p class="text-muted small mb-2">';
                        echo '<i class="bi bi-calendar3 me-2 text-orange"></i>';
                        echo $data_publicacao;
                        echo '</p>';
                    }
                    
                    echo '<h5 class="card-title fw-bold mb-3" style="color: #1a1a1a;">' . $titulo . '</h5>';
                    echo '<p class="card-text text-muted flex-grow-1" style="line-height: 1.6;">' . nl2br($descricao_curta) . '</p>';
                    
                    // Botão ver detalhes (abre modal)
                    echo '<div class="mt-3">';
                    echo '<button type="button" class="btn btn-outline-orange w-100 ver-detalhes-btn" 
                            data-noticia-id="' . $id . '"
                            data-noticia-titulo="' . $titulo . '"
                            data-noticia-descricao="' . htmlspecialchars($descricao, ENT_QUOTES, 'UTF-8') . '"
                            data-noticia-imagem="' . $imagem . '"
                            data-noticia-data="' . $data_publicacao . '"
                            data-noticia-categoria="' . htmlspecialchars($row["categoria"] ?? '') . '"
                            data-noticia-autor="' . $autor . '">';
                    echo '<i class="bi bi-arrow-right-circle me-2"></i>Ver Detalhes';
                    echo '</button>';
                    echo '</div>';
                    
                    echo '</div>'; // card-body
                    echo '</div>'; // card
                    echo '</div>'; // col
                }
                
                $stmt->close();
                
                // Paginação
                if ($total_paginas > 1) {
                    echo '<div class="col-12">';
                    echo '<nav aria-label="Navegação de notícias" class="mt-5">';
                    echo '<ul class="pagination justify-content-center">';
                    
                    // Botão Anterior
                    if ($pagina_atual > 1) {
                        echo '<li class="page-item">';
                        echo '<a class="page-link" href="?pagina=' . ($pagina_atual - 1) . '" aria-label="Anterior">';
                        echo '<span aria-hidden="true">&laquo;</span>';
                        echo '</a>';
                        echo '</li>';
                    } else {
                        echo '<li class="page-item disabled">';
                        echo '<span class="page-link">&laquo;</span>';
                        echo '</li>';
                    }
                    
                    // Números das páginas
                    $range = 2;
                    $start = max(1, $pagina_atual - $range);
                    $end = min($total_paginas, $pagina_atual + $range);
                    
                    // Primeira página
                    if ($start > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?pagina=1">1</a></li>';
                        if ($start > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    
                    // Páginas no range
                    for ($i = $start; $i <= $end; $i++) {
                        if ($i == $pagina_atual) {
                            echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                        } else {
                            echo '<li class="page-item"><a class="page-link" href="?pagina=' . $i . '">' . $i . '</a></li>';
                        }
                    }
                    
                    // Última página
                    if ($end < $total_paginas) {
                        if ($end < $total_paginas - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        echo '<li class="page-item"><a class="page-link" href="?pagina=' . $total_paginas . '">' . $total_paginas . '</a></li>';
                    }
                    
                    // Botão Próximo
                    if ($pagina_atual < $total_paginas) {
                        echo '<li class="page-item">';
                        echo '<a class="page-link" href="?pagina=' . ($pagina_atual + 1) . '" aria-label="Próximo">';
                        echo '<span aria-hidden="true">&raquo;</span>';
                        echo '</a>';
                        echo '</li>';
                    } else {
                        echo '<li class="page-item disabled">';
                        echo '<span class="page-link">&raquo;</span>';
                        echo '</li>';
                    }
                    
                    echo '</ul>';
                    echo '</nav>';
                    echo '</div>';
                }
                
            } else {
                // Nenhuma notícia encontrada
                echo '<div class="col-12">';
                echo '<div class="text-center py-5">';
                echo '<div class="mb-4">';
                    echo '<i class="bi bi-newspaper" style="font-size: 80px; color: #ff8c00; opacity: 0.3;"></i>';
                    echo '</div>';
                    echo '<h4 class="fw-bold mb-3">Nenhuma notícia disponível no momento</h4>';
                    echo '<p class="text-muted mb-0">Volte em breve para ler as últimas notícias da PIRCOM!</p>';
                    echo '</div>';
                echo '</div>';
            }
            
            $conn->close();
            ?>
        </div>
    </div>
</section>

<!-- Modal de Detalhes da Notícia -->
<div class="modal fade" id="noticiaModal" tabindex="-1" aria-labelledby="noticiaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-pircom-black text-white border-0">
                <div class="w-100">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div id="modal-categoria" class="badge badge-modal-categoria mb-2"></div>
                            <h5 class="modal-title fw-bold" id="noticiaModalLabel">Detalhes da Notícia</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
            </div>
            
            <div class="modal-body p-0">
                <!-- Imagem Principal -->
                <div id="modal-imagem-container" class="position-relative">
                    <img id="modal-imagem" src="" class="img-fluid w-100" style="max-height: 400px; object-fit: cover;" alt="">
                    <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(transparent, rgba(0,0,0,0.7));">
                        <h3 id="modal-titulo" class="text-white fw-bold mb-2"></h3>
                        <div class="d-flex align-items-center text-light">
                            <i class="bi bi-calendar3 me-2"></i>
                            <span id="modal-data" class="me-3"></span>
                            <i class="bi bi-person-circle me-2"></i>
                            <span id="modal-autor"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Conteúdo -->
                <div class="p-4">
                    <div id="modal-descricao" class="modal-conteudo" style="line-height: 1.8; font-size: 1.1rem;"></div>
                    
                    <div class="mt-4 pt-4 border-top">
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-tag me-1"></i> <span id="modal-categoria-text"></span>
                            </span>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-clock me-1"></i> <span id="modal-data-completa"></span>
                            </span>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-person me-1"></i> <span id="modal-autor-completo"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Fechar
                </button>
                <button type="button" class="btn btn-pircom-orange" onclick="compartilharNoticia()">
                    <i class="bi bi-share me-2"></i>Compartilhar
                </button>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --pircom-orange: #ff8c00;
    --pircom-dark-orange: #e67e00;
    --pircom-black: #1a1a1a;
    --pircom-light-black: #2d2d2d;
    --modal-transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.text-orange {
    color: var(--pircom-orange) !important;
}

.noticia-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid transparent;
}

.noticia-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(255, 140, 0, 0.3) !important;
    border-color: var(--pircom-orange);
}

.noticia-card:hover img {
    transform: scale(1.08);
}

.noticia-card .card-body {
    padding: 1.5rem;
}

.badge-categoria {
    background: var(--pircom-orange) !important;
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.badge-modal-categoria {
    background: var(--pircom-black) !important;
    color: var(--pircom-orange);
    border: 2px solid var(--pircom-orange);
    font-size: 0.8rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    padding: 0.5rem 1rem;
}

.btn-outline-orange {
    border: 2px solid var(--pircom-orange);
    color: var(--pircom-orange);
    background: transparent;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-outline-orange:hover {
    background: var(--pircom-orange);
    border-color: var(--pircom-orange);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 140, 0, 0.4);
}

.btn-pircom-orange {
    background: var(--pircom-orange);
    border: 2px solid var(--pircom-orange);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-pircom-orange:hover {
    background: var(--pircom-dark-orange);
    border-color: var(--pircom-dark-orange);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255, 140, 0, 0.4);
}

.section-title {
    text-align: center;
    margin-bottom: 3rem;
}

.section-title h2 {
    font-weight: 700;
    color: var(--pircom-black);
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
    background: linear-gradient(90deg, var(--pircom-orange) 0%, var(--pircom-black) 100%);
    border-radius: 2px;
}

.section-title p {
    color: #6b7280;
    font-size: 1.1rem;
    margin-top: 1.5rem;
}

/* Estilos da Modal */
#noticiaModal .modal-content {
    border-radius: 16px;
    overflow: hidden;
}

#noticiaModal .modal-header {
    background: linear-gradient(135deg, var(--pircom-black) 0%, var(--pircom-light-black) 100%);
    padding: 1.5rem 2rem;
}

#noticiaModal .modal-body {
    max-height: 70vh;
    overflow-y: auto;
}

#noticiaModal .modal-footer {
    background: #f8f9fa;
    padding: 1.5rem 2rem;
}

.modal-conteudo {
    color: #2d3748;
    font-size: 1.1rem;
    line-height: 1.8;
}

.modal-conteudo p {
    margin-bottom: 1.5rem;
}

.modal-conteudo img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1rem 0;
}

/* Estilos da Paginação */
.pagination {
    gap: 0.5rem;
}

.page-link {
    color: var(--pircom-orange);
    border: 2px solid #dee2e6;
    border-radius: 8px;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.page-link:hover {
    color: white;
    background-color: var(--pircom-orange);
    border-color: var(--pircom-orange);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 140, 0, 0.3);
}

.page-link:focus {
    box-shadow: 0 0 0 0.25rem rgba(255, 140, 0, 0.25);
}

.page-item.active .page-link {
    background-color: var(--pircom-black);
    border-color: var(--pircom-black);
    color: var(--pircom-orange);
    font-weight: 700;
}

.page-item.disabled .page-link {
    color: #6c757d;
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

/* Animações */
.modal.fade .modal-dialog {
    transform: scale(0.9);
    transition: var(--modal-transition);
}

.modal.show .modal-dialog {
    transform: scale(1);
}

#modal-imagem {
    transition: transform 0.5s ease;
}

#modal-imagem-container:hover #modal-imagem {
    transform: scale(1.05);
}

@media (max-width: 768px) {
    .noticia-card {
        margin-bottom: 1.5rem;
    }
    
    .section-title h2 {
        font-size: 2rem;
    }
    
    .section-title p {
        font-size: 1rem;
    }
    
    .pagination {
        gap: 0.25rem;
    }
    
    .page-link {
        padding: 0.4rem 0.8rem;
        font-size: 0.9rem;
    }
    
    #noticiaModal .modal-dialog {
        margin: 0.5rem;
    }
    
    #noticiaModal .modal-header {
        padding: 1rem;
    }
    
    #modal-imagem-container {
        max-height: 250px;
    }
    
    #modal-titulo {
        font-size: 1.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurar os botões de ver detalhes
    const verDetalhesBtns = document.querySelectorAll('.ver-detalhes-btn');
    const noticiaModal = new bootstrap.Modal(document.getElementById('noticiaModal'));
    
    verDetalhesBtns.forEach(button => {
        button.addEventListener('click', function() {
            const titulo = this.getAttribute('data-noticia-titulo');
            const descricao = this.getAttribute('data-noticia-descricao');
            const imagem = this.getAttribute('data-noticia-imagem');
            const data = this.getAttribute('data-noticia-data');
            const categoria = this.getAttribute('data-noticia-categoria');
            const autor = this.getAttribute('data-noticia-autor');
            
            // Preencher modal com os dados
            document.getElementById('modal-titulo').textContent = titulo;
            document.getElementById('modal-descricao').innerHTML = formatarDescricao(descricao);
            document.getElementById('modal-imagem').src = imagem;
            document.getElementById('modal-imagem').alt = titulo;
            document.getElementById('modal-data').textContent = data;
            document.getElementById('modal-data-completa').textContent = data;
            document.getElementById('modal-autor').textContent = autor;
            document.getElementById('modal-autor-completo').textContent = autor;
            document.getElementById('modal-categoria').textContent = categoria || 'Geral';
            document.getElementById('modal-categoria-text').textContent = categoria || 'Geral';
            document.getElementById('noticiaModalLabel').textContent = titulo;
            
            // Mostrar modal
            noticiaModal.show();
            
            // Adicionar efeito de entrada
            const modalContent = document.querySelector('.modal-content');
            modalContent.style.opacity = '0';
            modalContent.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                modalContent.style.transition = 'all 0.4s ease';
                modalContent.style.opacity = '1';
                modalContent.style.transform = 'translateY(0)';
            }, 100);
        });
    });
    
    // Formatar descrição com quebras de linha
    function formatarDescricao(descricao) {
        return descricao
            .replace(/\n/g, '<br>')
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\*(.*?)\*/g, '<em>$1</em>');
    }
    
    // Função para compartilhar notícia
    window.compartilharNoticia = function() {
        const titulo = document.getElementById('modal-titulo').textContent;
        const url = window.location.href;
        
        if (navigator.share) {
            navigator.share({
                title: titulo,
                text: 'Confira esta notícia da PIRCOM!',
                url: url
            });
        } else {
            // Fallback para copiar link
            navigator.clipboard.writeText(url + ' - ' + titulo)
                .then(() => {
                    alert('Link copiado para a área de transferência!');
                })
                .catch(() => {
                    // Fallback mais antigo
                    const tempInput = document.createElement('input');
                    tempInput.value = url + ' - ' + titulo;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempInput);
                    alert('Link copiado para a área de transferência!');
                });
        }
    };
    
    // Animar elementos do modal quando aberto
    document.getElementById('noticiaModal').addEventListener('shown.bs.modal', function() {
        const elementos = this.querySelectorAll('.modal-header, #modal-imagem-container, .modal-conteudo, .modal-footer');
        elementos.forEach((elemento, index) => {
            elemento.style.opacity = '0';
            elemento.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                elemento.style.transition = 'all 0.5s ease';
                elemento.style.opacity = '1';
                elemento.style.transform = 'translateY(0)';
            }, 100 + (index * 100));
        });
    });
    
    // Efeito de hover nas imagens das notícias
    const noticiaCards = document.querySelectorAll('.noticia-card');
    noticiaCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            const img = this.querySelector('img');
            if (img) {
                img.style.transition = 'transform 0.5s ease';
                img.style.transform = 'scale(1.1)';
            }
        });
        
        card.addEventListener('mouseleave', function() {
            const img = this.querySelector('img');
            if (img) {
                img.style.transform = 'scale(1)';
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>