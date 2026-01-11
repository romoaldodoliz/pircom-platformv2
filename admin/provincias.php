<?php
include('header.php');
include('../config/conexao.php');
?>
<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4">
            <span class="text-muted fw-light"></span> Províncias - Cobertura Geográfica
        </h4>
        
        <div class="" style="padding-bottom: 10px;">
            <a class='btn btn-primary' href="provinciasform.php">
                <i class='bx bx-plus'></i> Adicionar Província
            </a>
        </div>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Províncias</h5>
                <span class="badge bg-primary">
                    <?php
                    $count_sql = "SELECT COUNT(*) as total FROM provincias";
                    $count_result = $conn->query($count_sql);
                    $count = $count_result->fetch_assoc()['total'];
                    echo $count;
                    ?> províncias
                </span>
            </div>
            
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome da Província</th>
                            <th>Latitude</th>
                            <th>Longitude</th>
                            <th>Data de Criação</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php
                        // Consulta SELECT
                        $sql = "SELECT * FROM provincias ORDER BY nome ASC";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td><strong>" . $row["id"] . "</strong></td>";
                                echo "<td>";
                                echo "<div class='d-flex align-items-center'>";
                                echo "<i class='bx bx-map-pin text-primary me-2' style='font-size: 1.5rem;'></i>";
                                echo "<strong>" . htmlspecialchars($row["nome"]) . "</strong>";
                                echo "</div>";
                                echo "</td>";
                                echo "<td><code>" . $row["latitude"] . "</code></td>";
                                echo "<td><code>" . $row["longitude"] . "</code></td>";
                                echo "<td>" . date('d/m/Y H:i', strtotime($row["created_at"])) . "</td>";
                                echo "<td>";
                                echo "<div class='d-flex gap-2'>";
                                
                                // Botão Editar
                                echo "<a href='provinciasform.php?id=" . $row['id'] . "' class='btn btn-sm btn-warning' title='Editar'>";
                                echo "<i class='bx bx-edit'></i>";
                                echo "</a>";
                                
                                // Formulário Remover
                                echo "<form method='POST' action='remover_provincia.php' style='display:inline;' onsubmit='return confirm(\"Tem certeza que deseja remover a província " . htmlspecialchars($row['nome']) . "?\")'>";
                                echo "<input type='hidden' name='provincia_id' value='" . $row['id'] . "'>";
                                echo "<button type='submit' class='btn btn-sm btn-danger' name='remover' title='Remover'>";
                                echo "<i class='bx bx-trash'></i>";
                                echo "</button>";
                                echo "</form>";
                                
                                echo "</div>";
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center'>Nenhuma província cadastrada.</td></tr>";
                        }

                        // Fechar a conexão
                        $conn->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Info Card -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <i class='bx bx-info-circle text-primary me-3' style='font-size: 2rem;'></i>
                    <div>
                        <h6 class="mb-1">Sobre a Cobertura Geográfica</h6>
                        <p class="mb-0 text-muted">
                            As províncias cadastradas aqui serão exibidas no mapa interativo do site.
                            As coordenadas (latitude e longitude) determinam a localização dos marcadores no mapa.
                        </p>
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

<style>
    .btn-primary {
        background: linear-gradient(135deg, #FF6F0F 0%, #E05A00 100%);
        border: none;
        box-shadow: 0 4px 12px rgba(255, 111, 15, 0.3);
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(255, 111, 15, 0.4);
    }
    
    .badge.bg-primary {
        background: linear-gradient(135deg, #FF6F0F 0%, #E05A00 100%) !important;
    }
    
    .text-primary {
        color: #FF6F0F !important;
    }
</style>

<?php
include('footer.php');
?>
