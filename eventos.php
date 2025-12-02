<?php
$page_title = "Strong Woman - Eventos";
include 'includes/navbar.php';
?>

<section class="py-5" style="min-height: 70vh;">
    <div class="container">
        <div class="section-title mb-5">
            <h2>EVENTOS</h2>
            <p>Nossos Eventos Strong Woman</p>
        </div>

        <div class="row g-4">
            <?php
            include('config/conexao.php');
            
            // Query ajustada para sua estrutura
            $sql = "SELECT * FROM eventos ORDER BY id DESC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Sanitizar e formatar dados
                    $id = (int)$row["id"];
                    $titulo = htmlspecialchars($row["titulo"] ?? '', ENT_QUOTES, 'UTF-8');
                    $descricao = htmlspecialchars($row["descricao"] ?? '', ENT_QUOTES, 'UTF-8');
                    $descricao_curta = mb_strlen($descricao) > 120 ? mb_substr($descricao, 0, 120) . '...' : $descricao;
                    $data = htmlspecialchars($row["data"] ?? '', ENT_QUOTES, 'UTF-8');
                    
                    // Tentar formatar a data de forma inteligente
                    $data_formatada = $data;
                    $dia = '';
                    $mes = '';
                    
                    if (!empty($data)) {
                        // Tentar vários formatos de data
                        $timestamp = false;
                        
                        // Formato ISO (YYYY-MM-DD)
                        if (preg_match('/^\d{4}-\d{2}-\d{2}/', $data)) {
                            $timestamp = strtotime($data);
                        }
                        // Formato brasileiro (DD/MM/YYYY)
                        elseif (preg_match('/^\d{2}\/\d{2}\/\d{4}/', $data)) {
                            $partes = explode('/', substr($data, 0, 10));
                            if (count($partes) == 3) {
                                $timestamp = strtotime($partes[2] . '-' . $partes[1] . '-' . $partes[0]);
                            }
                        }
                        
                        if ($timestamp !== false && $timestamp > 0) {
                            $data_formatada = date('d/m/Y', $timestamp);
                            $dia = date('d', $timestamp);
                            $mes = date('M', $timestamp);
                            
                            // Determinar status do evento
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
                        } else {
                            // Se não conseguiu formatar, usa o texto original
                            $status = '';
                            $status_class = '';
                        }
                    } else {
                        $status = '';
                        $status_class = '';
                    }
                    
                    // Processar imagem
                    $imagem = '';
                    if (!empty($row["foto"])) {
                        $imagemBLOB = base64_encode($row["foto"]);
                        $imagem = 'data:image/jpeg;base64,' . $imagemBLOB;
                    } else {
                        // Imagem placeholder caso não tenha foto
                        $imagem = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="400" height="250" viewBox="0 0 400 250"%3E%3Crect fill="%23e9ecef" width="400" height="250"/%3E%3Ctext fill="%236c757d" font-family="Arial" font-size="18" x="50%25" y="50%25" text-anchor="middle" dominant-baseline="middle"%3EEvento Strong Woman%3C/text%3E%3C/svg%3E';
                    }
            ?>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 shadow-sm border-0 evento-card">
                    <!-- Badge de status -->
                    <?php if (!empty($status)): ?>
                    <div class="position-absolute top-0 end-0 m-3" style="z-index: 10;">
                        <span class="badge <?php echo $status_class; ?> px-3 py-2 shadow-sm"><?php echo $status; ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Imagem -->
                    <div class="position-relative overflow-hidden" style="height: 250px;">
                        <img src="<?php echo $imagem; ?>" 
                             class="card-img-top w-100 h-100" 
                             style="object-fit: cover; transition: transform 0.3s;"
                             alt="<?php echo $titulo; ?>"
                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'250\'%3E%3Crect fill=\'%23e9ecef\' width=\'400\' height=\'250\'/%3E%3C/svg%3E'">
                        
                        <!-- Data overlay -->
                        <?php if (!empty($dia) && !empty($mes)): ?>
                        <div class="position-absolute bottom-0 start-0 m-3 bg-white rounded shadow text-center" style="width: 60px; padding: 10px;">
                            <div class="fw-bold" style="font-size: 24px; line-height: 1; color: var(--primary-color, #e91e63);"><?php echo $dia; ?></div>
                            <div class="text-uppercase" style="font-size: 12px; color: #666;"><?php echo strtoupper($mes); ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Conteúdo do card -->
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title fw-bold mb-3" style="color: #333;"><?php echo $titulo; ?></h5>
                        
                        <?php if (!empty($descricao_curta)): ?>
                        <p class="card-text text-muted flex-grow-1" style="line-height: 1.6;">
                            <?php echo nl2br($descricao_curta); ?>
                        </p>
                        <?php endif; ?>
                        
                        <!-- Informação de data -->
                        <?php if (!empty($data_formatada)): ?>
                        <div class="mt-3 pt-3 border-top">
                            <p class="mb-0 small d-flex align-items-center">
                                <i class="bi bi-calendar-event me-2" style="color: var(--primary-color, #e91e63); font-size: 1.2rem;"></i>
                                <span><strong>Data:</strong> <?php echo $data_formatada; ?></span>
                            </p>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Botão ler mais -->
                        <button class="btn btn-outline-primary mt-3 w-100" 
                                data-bs-toggle="modal" 
                                data-bs-target="#eventoModal<?php echo $id; ?>"
                                style="border-width: 2px; font-weight: 500;">
                            <i class="bi bi-info-circle me-2"></i>Ler Mais
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Modal com detalhes completos -->
            <div class="modal fade" id="eventoModal<?php echo $id; ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold"><?php echo $titulo; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                        </div>
                        <div class="modal-body pt-3">
                            <!-- Imagem do evento -->
                            <div class="position-relative mb-4">
                                <img src="<?php echo $imagem; ?>" 
                                     class="img-fluid rounded w-100" 
                                     style="max-height: 400px; object-fit: cover;"
                                     alt="<?php echo $titulo; ?>"
                                     onerror="this.style.display='none'">
                                
                                <?php if (!empty($status)): ?>
                                <span class="badge <?php echo $status_class; ?> position-absolute top-0 end-0 m-3 px-3 py-2 shadow"><?php echo $status; ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Data do evento -->
                            <?php if (!empty($data_formatada)): ?>
                            <div class="alert alert-light d-flex align-items-center mb-4" role="alert">
                                <i class="bi bi-calendar-event me-3" style="font-size: 1.5rem; color: var(--primary-color, #e91e63);"></i>
                                <div>
                                    <strong>Data do Evento:</strong><br>
                                    <span class="text-muted"><?php echo $data_formatada; ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Descrição -->
                            <?php if (!empty($descricao)): ?>
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3 text-uppercase" style="color: var(--primary-color, #e91e63); letter-spacing: 1px; font-size: 0.9rem;">
                                    <i class="bi bi-file-text me-2"></i>Sobre o Evento
                                </h6>
                                <p class="text-muted" style="line-height: 1.8; white-space: pre-line;"><?php echo $descricao; ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                <i class="bi bi-x-circle me-2"></i>Fechar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php
                }
            } else {
                // Nenhum evento encontrado
            ?>
                <div class="col-12">
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="bi bi-calendar-x" style="font-size: 80px; color: var(--primary-color, #e91e63); opacity: 0.3;"></i>
                        </div>
                        <h4 class="fw-bold mb-3">Nenhum evento disponível no momento</h4>
                        <p class="text-muted mb-0">Volte em breve para ver nossos próximos eventos Strong Woman!</p>
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