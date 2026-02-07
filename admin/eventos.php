<?php
include('header.php');
include('config/conexao.php');
?>
<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">
        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Eventos</h4>

        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">Lista de Eventos</h5>
                <a class="btn btn-dark" href="eventosform.php">Adicionar Evento</a>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-striped table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Foto</th>
                            <th>Descrição</th>
                            <th>Data</th>
                            <th>Acção</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php
                        $sql = "SELECT * FROM eventos";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td data-label='#ID'>" . $row["id"] . "</td>";
                                $imgSrc = 'data:image/jpeg;base64,' . base64_encode($row['foto']);
                                echo "<td data-label='Foto'><img src='" . $imgSrc . "' alt='Foto' /></td>";
                                echo "<td data-label='Descrição' class='description-col'>" . htmlspecialchars($row["descricao"]) . "</td>";
                                echo "<td data-label='Data'>" . htmlspecialchars($row["data"]) . "</td>";
                                echo "<td data-label='Acção' class='actions-col'>";
                                echo "<div class='action-buttons'>";
                                echo "<a class='btn btn-sm btn-outline-primary' href='eventosform.php?id=" . $row['id'] . "' title='Editar'><i class='bx bx-edit'></i> <span class='d-none d-sm-inline'>Editar</span></a>";
                                echo "<a class='btn btn-sm btn-outline-secondary' href='../eventos.php?id=" . $row['id'] . "' target='_blank' title='Ver'><i class='bx bx-show'></i> <span class='d-none d-sm-inline'>Ver</span></a>";
                                if (isAdmin()) {
                                    echo "<form method='POST' action='remover_evento.php' class='m-0'>";
                                    echo csrfField();
                                    echo "<input type='hidden' name='evento_id' value='" . $row['id'] . "'>";
                                    echo "<button type='submit' class='btn btn-sm btn-danger'>Remover</button>";
                                    echo "</form>";
                                } else {
                                    echo "<button type='button' class='btn btn-sm btn-outline-danger' disabled title='Remover'>Remover</button>";
                                }
                                echo "</div></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>Nenhum resultado encontrado.</td></tr>";
                        }

                        // Fechar a conexão
                        $conn->close();
                        ?>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
    <!-- / Content -->

            <style>
            /* Admin tabela responsiva tweaks */
            .table img { max-width: 80px; height: auto; object-fit: cover; border-radius: 6px; }
            .table { table-layout: fixed; width: 100%; }
            .table td, .table th { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; vertical-align: middle; }
            .table td.description-col { white-space: normal; max-width: 45ch; }
            .actions-col { min-width: 180px; }
            .action-buttons { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
            .action-buttons .btn { white-space: nowrap; }
            .table-responsive { overflow-x: auto; overflow-y: visible; }
            .dropdown-menu { z-index: 3000; }

            /* Mobile: transform table into stacked blocks for readability and make actions touch-friendly */
            @media (max-width: 767px) {
                .table thead { display: none; }
                .table, .table tbody, .table tr, .table td { display: block; width: 100%; }
                .table td { text-align: right; padding-left: 50%; position: relative; }
                .table td::before { content: attr(data-label); position: absolute; left: 0; width: 45%; padding-left: 12px; text-align: left; font-weight: 600; }
                .actions-col { text-align: left; padding-left: 12px; }
                .action-buttons { flex-direction: column; align-items: stretch; gap: 6px; }
                .action-buttons .btn { width: 100%; text-align: center; }
                .table img { max-width: 64px; }
                .table td { white-space: normal; padding: 8px 12px; }
                .table td::before { top: 8px; }
            }
            </style>

    <!-- Footer -->
    <?php include('footerprincipal.php'); ?>
    <!-- / Footer -->

    <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
<?php
include('footer.php');
?>