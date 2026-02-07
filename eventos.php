<?php
$page_title = "PIRCOM - Eventos";
include 'includes/navbar.php';
?>

<section class="py-5" style="min-height: 70vh;" aria-labelledby="page-heading">
    <div class="container">
        <div class="section-title mb-5">
            <h1 id="page-heading" class="h2">EVENTOS</h1>
            <p class="lead">Eventos da PIRCOM - Plataforma Inter-Religiosa de Comunicação para a Saúde</p>
        </div>

        <div class="row g-4" role="list" aria-label="Lista de eventos">
            <?php
            include('config/conexao.php');
            
            $sql = "SELECT * FROM eventos ORDER BY id DESC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                $evento_count = 0;
                while ($row = $result->fetch_assoc()) {
                    $evento_count++;
                    $id = (int)$row["id"];
                    $titulo = htmlspecialchars($row["titulo"] ?? '', ENT_QUOTES, 'UTF-8');
                    $descricao = htmlspecialchars($row["descricao"] ?? '', ENT_QUOTES, 'UTF-8');
                    $descricao_curta = mb_strlen($descricao) > 120 ? mb_substr($descricao, 0, 120) . '...' : $descricao;
                    $data = htmlspecialchars($row["data"] ?? '', ENT_QUOTES, 'UTF-8');
                    
                    $data_formatada = $data;
                    $dia = '';
                    $mes = '';
                    $status = '';
                    $status_class = '';
                    
                    if (!empty($data)) {
                        $timestamp = false;
                        
                        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $data)) {
                            $timestamp = strtotime($data);
                        } elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $data)) {
                            $partes = explode('/', substr($data, 0, 10));
                            if (count($partes) == 3) {
                                $timestamp = strtotime($partes[2] . '-' . $partes[1] . '-' . $partes[0]);
                            }
                        }
                        
                        if ($timestamp !== false && $timestamp > 0) {
                            $data_formatada = date('d/m/Y', $timestamp);
                            $dia = date('d', $timestamp);
                            $mes = date('M', $timestamp);
                            
                            $hoje = strtotime(date('Y-m-d'));
                            $data_ev = strtotime(date('Y-m-d', $timestamp));
                            
                            if ($data_ev > $hoje) {
                                $status = 'Próximo';
                                $status_class = 'bg-success';
                            } elseif ($data_ev == $hoje) {
                                $status = 'Hoje';
                                $status_class = 'bg-warning text-dark';
                            } else {
                                $status = 'Realizado';
                                $status_class = 'bg-secondary';
                            }
                        }
                    }
                    
                    $imagem = '';
                    if (!empty($row["foto"])) {
                        $imagemBLOB = base64_encode($row["foto"]);
                        $imagem = 'data:image/jpeg;base64,' . $imagemBLOB;
                    } else {
                        $imagem = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="250" viewBox="0 0 400 250"%3E%3Crect fill="%23e9ecef" width="400" height="250"/%3E%3Ctext fill="%236c757d" font-family="Arial" font-size="18" x="50%25" y="50%25" text-anchor="middle" dominant-baseline="middle"%3EEvento PIRCOM%3C/text%3E%3C/svg%3E';
                    }
            ?>
            
            <div class="col-lg-4 col-md-6" role="listitem">
                <article class="card h-100 shadow-sm border-0 evento-card" 
                         aria-labelledby="evento-titulo-<?php echo $id; ?>"
                         itemscope 
                         itemtype="https://schema.org/Event">
                    
                    <?php if (!empty($status)): ?>
                    <div class="position-absolute top-0 end-0 m-3" style="z-index: 10;">
                        <span class="badge <?php echo $status_class; ?> px-3 py-2 shadow-sm" 
                              role="status"
                              aria-label="Status do evento: <?php echo $status; ?>">
                            <?php echo $status; ?>
                        </span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="position-relative overflow-hidden" style="height: 250px;">
                        <img src="<?php echo $imagem; ?>" 
                             class="card-img-top w-100 h-100" 
                             style="object-fit: cover; transition: transform 0.3s;"
                             alt="Imagem do evento: <?php echo $titulo; ?>"
                             loading="<?php echo $evento_count <= 3 ? 'eager' : 'lazy'; ?>"
                             itemprop="image"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'250\'%3E%3Crect fill=\'%23e9ecef\' width=\'400\' height=\'250\'/%3E%3C/svg%3E'; this.alt='Imagem não disponível';">
                        
                        <?php if (!empty($dia) && !empty($mes)): ?>
                        <div class="position-absolute bottom-0 start-0 m-3 bg-white rounded shadow text-center" 
                             style="width: 60px; padding: 10px;"
                             role="img"
                             aria-label="Data: <?php echo $dia; ?> de <?php echo $mes; ?>">
                            <div class="fw-bold" style="font-size: 24px; line-height: 1; color: var(--primary-color, #2563eb);" aria-hidden="true"><?php echo $dia; ?></div>
                            <div class="text-uppercase" style="font-size: 12px; color: #666;" aria-hidden="true"><?php echo strtoupper($mes); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <h2 id="evento-titulo-<?php echo $id; ?>" 
                            class="card-title fw-bold mb-3 h5" 
                            style="color: #333;"
                            itemprop="name">
                            <?php echo $titulo; ?>
                        </h2>
                        
                        <?php if (!empty($descricao_curta)): ?>
                        <p class="card-text text-muted flex-grow-1" 
                           style="line-height: 1.6;"
                           itemprop="description">
                            <?php echo nl2br($descricao_curta); ?>
                        </p>
                        <?php endif; ?>
                        
                        <?php if (!empty($data_formatada)): ?>
                        <div class="mt-3 pt-3 border-top">
                            <p class="mb-0 small d-flex align-items-center">
                                <i class="bi bi-calendar-event me-2" 
                                   style="color: var(--primary-color, #2563eb); font-size: 1.2rem;"
                                   aria-hidden="true"></i>
                                <span>
                                    <strong>Data:</strong> 
                                    <time datetime="<?php echo date('Y-m-d', $timestamp ?? time()); ?>" 
                                          itemprop="startDate">
                                        <?php echo $data_formatada; ?>
                                    </time>
                                </span>
                            </p>
                        </div>
                        <?php endif; ?>
                        
                        <button class="btn btn-outline-primary mt-3 w-100" 
                                data-bs-toggle="modal" 
                                data-bs-target="#eventoModal<?php echo $id; ?>"
                                style="border-width: 2px; font-weight: 500;"
                                aria-label="Ver mais detalhes sobre <?php echo $titulo; ?>"
                                aria-haspopup="dialog">
                            <i class="bi bi-info-circle me-2" aria-hidden="true"></i>
                            <span>Ler Mais</span>
                        </button>
                    </div>
                </article>
            </div>
            
            <div class="modal fade" 
                 id="eventoModal<?php echo $id; ?>" 
                 tabindex="-1" 
                 role="dialog"
                 aria-labelledby="modalTitulo<?php echo $id; ?>"
                 aria-describedby="modalDescricao<?php echo $id; ?>"
                 aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h3 id="modalTitulo<?php echo $id; ?>" class="modal-title fw-bold h5">
                                <?php echo $titulo; ?>
                            </h3>
                            <button type="button" 
                                    class="btn-close" 
                                    data-bs-dismiss="modal" 
                                    aria-label="Fechar janela de detalhes do evento">
                            </button>
                        </div>
                        <div class="modal-body pt-3" id="modalDescricao<?php echo $id; ?>">
                            <div class="position-relative mb-4">
                                <img src="<?php echo $imagem; ?>" 
                                     class="img-fluid rounded w-100" 
                                     style="max-height: 400px; object-fit: cover;"
                                     alt="Imagem em destaque: <?php echo $titulo; ?>"
                                     loading="lazy"
                                     onerror="this.style.display='none'">
                                
                                <?php if (!empty($status)): ?>
                                <span class="badge <?php echo $status_class; ?> position-absolute top-0 end-0 m-3 px-3 py-2 shadow"
                                      role="status"
                                      aria-label="Status: <?php echo $status; ?>">
                                    <?php echo $status; ?>
                                </span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if (!empty($data_formatada)): ?>
                            <div class="alert alert-light d-flex align-items-center mb-4" role="alert">
                                <i class="bi bi-calendar-event me-3" 
                                   style="font-size: 1.5rem; color: var(--primary-color, #2563eb);"
                                   aria-hidden="true"></i>
                                <div>
                                    <strong>Data do Evento:</strong><br>
                                    <time datetime="<?php echo date('Y-m-d', $timestamp ?? time()); ?>" class="text-muted">
                                        <?php echo $data_formatada; ?>
                                    </time>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($descricao)): ?>
                            <section aria-labelledby="sobre-evento-<?php echo $id; ?>">
                                <h4 id="sobre-evento-<?php echo $id; ?>" 
                                    class="fw-bold mb-3 text-uppercase h6" 
                                    style="color: var(--primary-color, #2563eb); letter-spacing: 1px; font-size: 0.9rem;">
                                    <i class="bi bi-file-text me-2" aria-hidden="true"></i>
                                    Sobre o Evento
                                </h4>
                                <p class="text-muted" style="line-height: 1.8; white-space: pre-line;">
                                    <?php echo $descricao; ?>
                                </p>
                            </section>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" 
                                    class="btn btn-secondary px-4" 
                                    data-bs-dismiss="modal"
                                    aria-label="Fechar e voltar para lista de eventos">
                                <i class="bi bi-x-circle me-2" aria-hidden="true"></i>
                                <span>Fechar</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php
                }
            } else {
            ?>
                <div class="col-12">
                    <div class="text-center py-5" role="status" aria-live="polite">
                        <div class="mb-4" aria-hidden="true">
                            <i class="bi bi-calendar-x" style="font-size: 80px; color: var(--primary-color, #2563eb); opacity: 0.3;"></i>
                        </div>
                        <h3 class="fw-bold mb-3 h4">Nenhum evento disponível no momento</h3>
                        <p class="text-muted mb-0">Volte em breve para ver os próximos eventos da PIRCOM - Plataforma Inter-Religiosa de Comunicação para a Saúde!</p>
                    </div>
                </div>
            <?php
            }
            
            $conn->close();
            ?>
        </div>
    </div>
</section>

<style>
.evento-card {
    transition: all 0.3s ease;
    border-radius: 12px;
    overflow: hidden;
}

.evento-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 40px rgba(0,0,0,0.2) !important;
}

.evento-card:hover img {
    transform: scale(1.08);
}

.evento-card .card-body {
    padding: 1.5rem;
}

.modal-content {
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

.modal-header {
    padding: 1.5rem 1.5rem 0;
}

.modal-body {
    padding: 1rem 1.5rem;
}

.modal-footer {
    padding: 0 1.5rem 1.5rem;
}

.badge {
    font-size: 0.85rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.btn-outline-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.15);
}

/* Melhoria de foco para acessibilidade */
.evento-card:focus-within {
    outline: 3px solid var(--primary-color, #2563eb);
    outline-offset: 2px;
}

.btn:focus-visible,
.btn-close:focus-visible {
    outline: 3px solid var(--primary-color, #2563eb);
    outline-offset: 2px;
}

/* Redução de movimento para usuários com preferências de acessibilidade */
@media (prefers-reduced-motion: reduce) {
    .evento-card,
    .evento-card img,
    .btn-outline-primary {
        transition: none;
    }
    
    .evento-card:hover {
        transform: none;
    }
    
    .evento-card:hover img {
        transform: none;
    }
}

@media (max-width: 768px) {
    .evento-card {
        margin-bottom: 1.5rem;
    }
    
    .modal-dialog {
        margin: 0.5rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>