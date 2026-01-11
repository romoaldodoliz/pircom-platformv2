<?php
$page_title = "Pircom - Doações";
include 'includes/navbar.php';
include('config/conexao.php');

// Buscar configurações de doação
$sql = "SELECT * FROM configuracoes_doacoes LIMIT 1";
$config = @$conn->query($sql);
$doacoes_config = ($config && $config->num_rows > 0) ? $config->fetch_assoc() : null;

// Processar formulário
$mensagem = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $valor = $_POST['valor'] ?? '';
    $metodo = $_POST['metodo_pagamento'] ?? '';
    
    $sql = "INSERT INTO doadores (nome, email, telefone, valor, metodo_pagamento, data_doacao, status) 
            VALUES (?, ?, ?, ?, ?, NOW(), 'pendente')";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssds", $nome, $email, $telefone, $valor, $metodo);
        if ($stmt->execute()) {
            $mensagem = '<div class="alert alert-success">Obrigado pela sua doação! Entraremos em contacto em breve.</div>';
        }
        $stmt->close();
    }
}
?>

<section class="py-5" style="min-height: 70vh;">
    <div class="container">
        <div class="section-title">
            <h2>DOAÇÕES</h2>
            <p>Apoie a Strong Woman</p>
        </div>

        <?php echo $mensagem; ?>

        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <p class="lead">Com sua ajuda, podemos transformar vidas e empoderar mulheres em Moçambique. Toda contribuição faz a diferença!</p>
            </div>
        </div>

        <?php if ($doacoes_config): ?>
        <div class="row mb-5">
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h4><i class="bi bi-bank text-danger"></i> Transferência Bancária</h4>
                        <p><strong>Número de Conta:</strong><br><?php echo htmlspecialchars($doacoes_config['numero_conta'] ?? 'Indisponível'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h4><i class="bi bi-phone text-danger"></i> M-Pesa</h4>
                        <p><strong>Número:</strong><br><?php echo htmlspecialchars($doacoes_config['mpesa_numero'] ?? 'Indisponível'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h4><i class="bi bi-wallet2 text-danger"></i> e-Mola</h4>
                        <p><strong>Número:</strong><br><?php echo htmlspecialchars($doacoes_config['emola_numero'] ?? 'Indisponível'); ?></p>
                    </div>
                </div>
            </div>
            <?php if (!empty($doacoes_config['paypal_button_code'])): ?>
            <div class="col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h4><i class="bi bi-paypal text-danger"></i> PayPal</h4>
                        <?php echo $doacoes_config['paypal_button_code']; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-body p-4">
                        <h4 class="mb-4">Registar Doação</h4>
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>Nome Completo *</label>
                                    <input type="text" name="nome" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Email *</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Telefone *</label>
                                    <input type="tel" name="telefone" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>Valor (MZN) *</label>
                                    <input type="number" name="valor" class="form-control" min="1" step="0.01" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <label>Método de Pagamento *</label>
                                    <select name="metodo_pagamento" class="form-control" required>
                                        <option value="">Selecione...</option>
                                        <option value="transferencia">Transferência Bancária</option>
                                        <option value="mpesa">M-Pesa</option>
                                        <option value="emola">e-Mola</option>
                                        <option value="paypal">PayPal</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary btn-lg w-100">
                                        <i class="bi bi-heart-fill me-2"></i>Registar Doação
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
$conn->close();
include 'includes/footer.php'; 
?>
