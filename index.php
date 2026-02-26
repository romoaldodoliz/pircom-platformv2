<?php
$page_title = "PIRCOM - Início";
$include_swiper = true;
include 'includes/navbar.php';
include 'includes/swiper-styles.php';
?>

<!-- Hero Slider com Swiper -->
<div class="swiper-container main-slider">
    <div class="swiper-wrapper">
        <?php
        include('config/conexao.php');
        $sql = "SELECT * FROM homepagehero ORDER BY id ASC LIMIT 3";
        $resultado = @$conn->query($sql);

        if (!$resultado) {
            $resultado = new stdClass();
            $resultado->num_rows = 0;
        }

        if ($resultado->num_rows > 0) {
            $i = 0;
            while ($row = $resultado->fetch_assoc()) {
                $imagemBlob = $row['foto'];
                $imagemBase64 = base64_encode($imagemBlob);
                echo '<div class="swiper-slide" style="background:url(data:image/jpeg;base64,' . $imagemBase64 . ') center center; background-size: cover;">';
                if ($i > 0 && !empty($row['descricao'])) {
                    echo '<h2>' . htmlspecialchars($row['descricao']) . '</h2>';
                }
                echo '</div>';
                $i++;
            }
        } else {
            echo '<div class="swiper-slide" style="background:url(\'https://cdn.pixabay.com/photo/2017/08/07/14/02/people-2604149_960_720.jpg\') center center; background-size: cover;">';
            echo '<h2>Pircom</h2>';
            echo '</div>';
        }
        ?>
    </div>
    <div class="swiper-pagination"></div>
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
</div>

<!-- Notícias -->
<section id="noticias" class="py-5">
    <div class="container">
        <div class="section-title">
            <h2>ÚLTIMAS</h2>
            <p>Notícias</p>
        </div>
        <div class="row">
            <?php
            $sql = "SELECT * FROM noticias ORDER BY id DESC LIMIT 3";
            $result = @$conn->query($sql);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imagemBLOB = base64_encode($row["foto"]);
                    echo '<div class="col-lg-4 col-md-6 mb-4">';
                    echo '<div class="card h-100">';
                    echo '<a href="noticias.php?id=' . $row["id"] . '" style="text-decoration:none; color:inherit;">';
                    echo '<img src="data:image/jpeg;base64,' . $imagemBLOB . '" alt="Notícia" class="card-img-top" style="object-fit: cover; height: 250px;">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row["titulo"]) . '</h5>';
                    echo '<p class="card-text">' . htmlspecialchars(substr($row["descricao"], 0, 100)) . '...</p>';
                    echo '</div></a></div></div>';
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- Eventos -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-title">
            <h2>EVENTOS</h2>
            <p>Nossos Eventos</p>
        </div>
        <div class="row">
            <?php
            $sql = "SELECT * FROM eventos ORDER BY id DESC LIMIT 6";
            $result = @$conn->query($sql);
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imagemBLOB = base64_encode($row["foto"]);
                    echo '<div class="col-lg-4 col-md-6 mb-4">';
                    echo '<div class="card">';
                    echo '<img src="data:image/jpeg;base64,' . $imagemBLOB . '" class="card-img-top" style="height:250px; object-fit:cover;">';
                    echo '<div class="card-body">';
                    echo '<h5>' . htmlspecialchars($row["titulo"]) . '</h5>';
                    echo '<p>' . htmlspecialchars(substr($row["descricao"], 0, 100)) . '...</p>';
                    echo '</div></div></div>';
                }
            }
            ?>
        </div>
    </div>
</section>

<!-- CTA -->
<style>
.cta {
    background: #f8f9fa;
    padding: 80px 0;
    text-align: center;
}

.cta h3 {
    color: #000000 !important;  /* Texto preto */
    font-weight: 700;
    margin-bottom: 20px;
    font-size: 2.5rem;
}

.cta p {
    color: #000000 !important;  /* Texto preto */
    font-size: 1.2rem;
    max-width: 800px;
    margin: 0 auto 30px;
    line-height: 1.6;
    opacity: 0.9;
}

.cta-btn {
    display: inline-block;
    padding: 12px 40px;
    background: #FF6F0F;  /* Laranja */
    color: #ffffff !important;
    text-decoration: none;
    font-weight: 600;
    border-radius: 50px;
    transition: all 0.3s ease;
    border: 2px solid #FF6F0F;
}

.cta-btn:hover {
    background: #D95A00;  /* Laranja escuro */
    border-color: #D95A00;
    color: #ffffff !important;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(255,111,15,0.3);
}
</style>

<section class="cta">
    <div class="container text-center">
        <h3>SEJA PARTE DA SOLUÇÃO</h3>
        <p>"Desde 2006, unimos fé e ação para melhorar a saúde materno-infantil, combater a malária e o HIV. Ajude-nos a alcançar mais vidas!"</p>
        <a class="cta-btn" href="doacoes.php">Doar Agora</a>
    </div>
</section>

<?php
$conn->close();
include 'includes/footer.php';
?>

<script>
var swiper = new Swiper('.swiper-container', {
    loop: true,
    autoplay: { delay: 5000, disableOnInteraction: false },
    pagination: { el: '.swiper-pagination', clickable: true },
    navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' }
});
</script>