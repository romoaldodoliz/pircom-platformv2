<?php
include('header.php');
include('config/conexao.php');
?>
<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->

    <div class="container-xxl flex-grow-1 container-p-y">

        <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light"></span> Mass Media</h4>
        <div class="" style="padding-bottom: 10px;">
            <a class="btn btn-dark" href="massmediaform.php">Adicionar</a>
        </div>
        <div class="card">
            <div class="table-responsive text-nowrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Titulo</th>
                            <th>Link</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php
                        // Consulta SELECT
                        $sql = "SELECT * FROM massmedia";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["id"] . "</td>";
                                echo "<td>" . $row["titulo"] . "</td>";
                                echo "<td>" . $row["link"] . "</td>";
                                echo "<td>";
                                if (isAdmin()) {
                                    echo "<form method='POST' action='remover_massmedia.php' class='d-inline'>";
                                    echo csrfField();
                                    echo "<input type='hidden' name='massmedia_id' value='" . $row['id'] . "'>";
                                    echo "<button type='submit' class='btn btn-danger' name='remover'>Remover</button>";
                                    echo "</form>";
                                } else {
                                    $itemName = htmlspecialchars($row['titulo'], ENT_QUOTES);
                                    echo "<button type='button' class='btn btn-danger disabled-delete' data-item-name='" . $itemName . "' aria-disabled='true'>Remover</button>";
                                }
                                echo "</td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Nenhum resultado encontrado.</td></tr>";
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

    <!-- Footer -->
    <?php include('footerprincipal.php'); ?>
    <!-- / Footer -->

    <div class="content-backdrop fade"></div>
</div>
<!-- Content wrapper -->
<?php
include('footer.php');
?>