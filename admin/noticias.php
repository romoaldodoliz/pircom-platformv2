<?php
include('header.php');
include('config/conexao.php');

// Adicionar paginação
$limit = 10; // Itens por página
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Contar total de registros
$count_sql = "SELECT COUNT(*) as total FROM noticias";
$count_result = $conn->query($count_sql);
$total_rows = $count_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

// Buscar notícias com paginação
$sql = "SELECT * FROM noticias ORDER BY data DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);
?>
<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <h4 class="fw-bold py-3 mb-0">Notícias</h4>
            <a class='btn btn-dark' href="noticiasform.php">
                <i class="bx bx-plus me-1"></i>Adicionar notícia
            </a>
        </div>
        
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="80">#ID</th>
                            <th width="120">Foto</th>
                            <th>Descrição</th>
                            <th width="120">Data</th>
                            <th width="100" class="text-center">Acção</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?= htmlspecialchars($row["id"]) ?></strong></td>
                                    <td>
                                        <div class="image-preview">
                                            <img 
                                                src='data:image/jpeg;base64,<?= base64_encode($row['foto']) ?>' 
                                                alt='Notícia <?= htmlspecialchars($row["id"]) ?>'
                                                class="img-thumbnail"
                                                loading="lazy"
                                            />
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 300px;" title="<?= htmlspecialchars($row["descricao"]) ?>">
                                            <?= htmlspecialchars($row["descricao"]) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-label-primary">
                                            <?= date('d/m/Y', strtotime($row["data"])) ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <form method='POST' action='remover_noticia.php' 
                                              onsubmit="return confirm('Tem certeza que deseja remover esta notícia?')">
                                            <input type='hidden' name='noticia_id' value='<?= $row['id'] ?>'>
                                            <button type='submit' class='btn btn-sm btn-danger' name='remover'>
                                                <i class="bx bx-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bx bx-news display-4"></i>
                                        <p class="mt-2 mb-0">Nenhuma notícia encontrada.</p>
                                        <small>Clique em "Adicionar notícia" para criar a primeira.</small>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <!-- Paginação -->
                <?php if ($total_pages > 1): ?>
                <div class="card-footer">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
                <?php endif; ?>
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

<style>
    /* Estilos responsivos */
    .image-preview {
        width: 80px;
        height: 80px;
        overflow: hidden;
        border-radius: 8px;
    }
    
    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            border: 0;
        }
        
        .table thead {
            display: none;
        }
        
        .table tr {
            display: block;
            margin-bottom: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
        }
        
        .table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: none;
            padding: 0.75rem;
        }
        
        .table td:before {
            content: attr(data-label);
            font-weight: 600;
            margin-right: 1rem;
        }
        
        .table td[data-label] {
            flex-direction: row;
        }
        
        .image-preview {
            width: 60px;
            height: 60px;
        }
    }
</style>

<script>
    // Adicionar labels para mobile
    document.addEventListener('DOMContentLoaded', function() {
        const labels = ['ID', 'Foto', 'Descrição', 'Data', 'Acção'];
        const tds = document.querySelectorAll('tbody td');
        
        tds.forEach((td, index) => {
            const labelIndex = index % 5;
            td.setAttribute('data-label', labels[labelIndex]);
        });
    });
</script>

<?php
// Fechar a conexão apenas se ainda estiver aberta
if (isset($conn) && $conn->ping()) {
    $conn->close();
}
include('footer.php');
?>