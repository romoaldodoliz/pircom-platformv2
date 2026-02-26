<?php
$page_title = "Pircom - Doações";
include 'includes/navbar.php';
include('config/conexao.php');

$message = '';
$message_type = '';

// Processar formulário
if (isset($_POST['submit_doacao'])) {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $valor = floatval($_POST['valor'] ?? 0);
    $metodo_pagamento = $_POST['metodo_pagamento'] ?? '';
    $mensagem = $_POST['mensagem'] ?? '';
    
    if (empty($nome) || empty($email) || $valor <= 0 || empty($metodo_pagamento)) {
        $message = 'Por favor, preencha todos os campos obrigatórios.';
        $message_type = 'danger';
    } else {
        $comprovativo_path = null;
        if (isset($_FILES['comprovativo']) && $_FILES['comprovativo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/doadores/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = pathinfo($_FILES['comprovativo']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '_' . time() . '.' . $file_extension;
            $file_path = $upload_dir . $file_name;
            
            if (move_uploaded_file($_FILES['comprovativo']['tmp_name'], $file_path)) {
                $comprovativo_path = $file_path;
            }
        }
        
        $stmt = $conn->prepare("INSERT INTO doadores (nome, email, telefone, valor, metodo_pagamento, data_doacao, comprovativo, status) VALUES (?, ?, ?, ?, ?, NOW(), ?, 'pendente')");
        $stmt->bind_param("sssdss", $nome, $email, $telefone, $valor, $metodo_pagamento, $comprovativo_path);
        
        if ($stmt->execute()) {
            $message = 'Obrigado pela sua doação! Entraremos em contacto em breve.';
            $message_type = 'success';
        } else {
            $message = 'Erro ao registar doação. Tente novamente.';
            $message_type = 'danger';
        }
        $stmt->close();
    }
}

// Buscar configurações
$sql = "SELECT * FROM configuracoes_doacoes LIMIT 1";
$result = @$conn->query($sql);
$config = ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;
?>

<style>
    :root {
        --primary-color: #FF6F0F;  /* Laranja PIRCOM */
        --secondary-color: #0D0D0D; /* Preto PIRCOM */
    }

    .payment-method {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 20px;
        transition: all 0.3s;
        height: 100%;
    }
    
    .payment-method:hover {
        border-color: var(--primary-color);
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(255, 111, 15, 0.15); /* Sombra laranja ajustada */
    }
    
    .payment-icon {
        font-size: 48px;
        color: var(--primary-color);
        margin-bottom: 15px;
    }
    
    .donation-form-card {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .impact-card {
        background: linear-gradient(135deg, var(--primary-color) 0%, #D95A00 100%); /* Gradiente laranja */
        color: white;
        border-radius: 15px;
        padding: 50px 40px;
        margin-bottom: 40px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(255, 111, 15, 0.2);
    }
    
    .impact-card h3 {
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 20px;
        color: white; /* Mantém branco para contraste no fundo laranja */
    }

    .impact-card p {
        color: white; /* Mantém branco para contraste no fundo laranja */
        font-size: 1.2rem;
        line-height: 1.6;
        max-width: 900px;
        margin: 0 auto;
        opacity: 0.95;
    }

    .mission-highlight {
        background: #f8f9fa;
        border-left: 4px solid var(--primary-color);
        padding: 30px;
        margin-bottom: 30px;
        border-radius: 10px;
    }

    .mission-highlight h4 {
        color: var(--secondary-color) !important; /* Preto */
        font-weight: 700;
        margin-bottom: 20px;
    }

    .mission-highlight ul {
        margin-bottom: 0;
        padding-left: 20px;
    }

    .mission-highlight li {
        margin-bottom: 10px;
        color: #333; /* Preto suave para melhor legibilidade */
    }

    .mission-highlight li strong {
        color: var(--secondary-color); /* Preto */
    }

    .btn-primary {
        background: var(--primary-color) !important;
        border-color: var(--primary-color) !important;
        color: white !important;
    }

    .btn-primary:hover {
        background: #D95A00 !important; /* Laranja escuro */
        border-color: #D95A00 !important;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(255, 111, 15, 0.3);
    }

    .text-danger {
        color: var(--primary-color) !important; /* Laranja em vez de vermelho */
    }

    .section-title h2 {
        color: var(--secondary-color);
    }

    .section-title p {
        color: var(--primary-color);
    }
</style>

<section class="py-5" style="min-height: 70vh;">
    <div class="container">
        <div class="section-title">
            <h2>APOIE A NOSSA MISSÃO</h2>
            <p>Juntos pela saúde e bem-estar das comunidades moçambicanas</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Impacto -->
        <div class="impact-card">
            <h3><i class="bi bi-heart-pulse me-3"></i>Transforme Vidas Através da Fé e Saúde</h3>
            <p class="lead mb-0">Desde 2006, a PIRCOM une comunidades cristãs, muçulmanas, hindus e bahai na promoção da saúde em Moçambique. A sua contribuição fortalece o nosso trabalho na prevenção da malária, HIV, saúde materno-infantil e construção da paz.</p>
        </div>

        <!-- Áreas de Impacto -->
        <div class="mission-highlight">
            <h4 style="color: var(--secondary-color); font-weight: 700; margin-bottom: 20px;">
                <i class="bi bi-bullseye me-2" style="color: var(--primary-color);"></i>O Seu Apoio Contribui Para:
            </h4>
            <div class="row">
                <div class="col-md-6">
                    <ul>
                        <li><strong>Prevenção e tratamento da malária</strong> nas comunidades vulneráveis</li>
                        <li><strong>Combate ao HIV/SIDA</strong> através de educação e sensibilização</li>
                        <li><strong>Saúde materno-infantil</strong> - cuidados a grávidas, lactantes e crianças menores de 5 anos</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <ul>
                        <li><strong>Nutrição comunitária</strong> para famílias em situação de vulnerabilidade</li>
                        <li><strong>Construção da paz</strong> através do diálogo inter-religioso</li>
                        <li><strong>Comunicação para mudança social</strong> usando rádios comunitárias e visitas domiciliárias</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Métodos de Pagamento -->
        <?php if ($config): ?>
        <div class="row mb-5">
            <div class="col-12 mb-4">
                <h3 class="text-center mb-4" style="color: var(--secondary-color); font-weight: 700;">
                    <i class="bi bi-wallet2 me-2" style="color: var(--primary-color);"></i>Métodos de Doação
                </h3>
            </div>
            
            <?php if (!empty($config['numero_conta'])): ?>
            <div class="col-lg-6 mb-4">
                <div class="payment-method text-center">
                    <i class="bi bi-bank payment-icon"></i>
                    <h5 style="color: var(--secondary-color); font-weight: 700;">Transferência Bancária</h5>
                    <?php if (!empty($config['banco_nome'])): ?>
                        <p class="mb-2"><strong><?php echo htmlspecialchars($config['banco_nome']); ?></strong></p>
                    <?php endif; ?>
                    <p class="mb-0">Conta: <strong style="color: var(--primary-color);"><?php echo htmlspecialchars($config['numero_conta']); ?></strong></p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($config['mpesa_numero'])): ?>
            <div class="col-lg-6 mb-4">
                <div class="payment-method text-center">
                    <i class="bi bi-phone payment-icon"></i>
                    <h5 style="color: var(--secondary-color); font-weight: 700;">M-Pesa</h5>
                    <p class="mb-0">Número: <strong style="color: var(--primary-color);"><?php echo htmlspecialchars($config['mpesa_numero']); ?></strong></p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($config['emola_numero'])): ?>
            <div class="col-lg-6 mb-4">
                <div class="payment-method text-center">
                    <i class="bi bi-wallet2 payment-icon"></i>
                    <h5 style="color: var(--secondary-color); font-weight: 700;">e-Mola</h5>
                    <p class="mb-0">Número: <strong style="color: var(--primary-color);"><?php echo htmlspecialchars($config['emola_numero']); ?></strong></p>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if (!empty($config['paypal_button_code'])): ?>
            <div class="col-lg-6 mb-4">
                <div class="payment-method text-center">
                    <i class="bi bi-paypal payment-icon"></i>
                    <h5 style="color: var(--secondary-color); font-weight: 700;">PayPal</h5>
                    <div class="mt-3"><?php echo $config['paypal_button_code']; ?></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Formulário -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="donation-form-card">
                    <h3 class="mb-4 text-center" style="color: var(--secondary-color);">
                        <i class="bi bi-clipboard-check me-2" style="color: var(--primary-color);"></i>Registar Sua Doação
                    </h3>
                    <p class="text-muted text-center mb-4">Após efetuar a doação por um dos métodos acima, preencha o formulário abaixo para confirmarmos o seu apoio.</p>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nome Completo <span style="color: var(--primary-color);">*</span></label>
                                <input type="text" class="form-control" name="nome" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Email <span style="color: var(--primary-color);">*</span></label>
                                <input type="email" class="form-control" name="email" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Telefone</label>
                                <input type="tel" class="form-control" name="telefone" placeholder="84 123 4567">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Valor (MZN) <span style="color: var(--primary-color);">*</span></label>
                                <input type="number" class="form-control" name="valor" step="0.01" min="1" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Método de Pagamento <span style="color: var(--primary-color);">*</span></label>
                                <select class="form-select" name="metodo_pagamento" required>
                                    <option value="">Selecione...</option>
                                    <option value="transferencia">Transferência Bancária</option>
                                    <option value="mpesa">M-Pesa</option>
                                    <option value="emola">e-Mola</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="outro">Outro</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Comprovativo (Opcional)</label>
                                <input type="file" class="form-control" name="comprovativo" accept="image/*">
                                <small class="text-muted">Anexe o comprovativo de pagamento</small>
                            </div>
                            
                            <div class="col-12 mb-4">
                                <label class="form-label fw-bold">Mensagem (Opcional)</label>
                                <textarea class="form-control" name="mensagem" rows="3" placeholder="Deixe uma mensagem de apoio..."></textarea>
                            </div>
                            
                            <div class="col-12 text-center">
                                <button type="submit" name="submit_doacao" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-heart-fill me-2"></i>Confirmar Doação
                                </button>
                                <p class="text-muted mt-3 mb-0">
                                    <small><i class="bi bi-shield-check me-1"></i>As suas informações estão seguras connosco</small>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mensagem Final -->
        <div class="row mt-5">
            <div class="col-lg-10 mx-auto text-center">
                <div class="alert alert-info" style="background: #e8f4f8; border: none; border-radius: 15px;">
                    <h5 style="color: var(--secondary-color); font-weight: 700;">
                        <i class="bi bi-info-circle me-2" style="color: var(--primary-color);"></i>Cada Contribuição Faz a Diferença
                    </h5>
                    <p class="mb-0" style="color: #333;">Através da união de comunidades de fé e do apoio de pessoas como você, continuamos a salvar vidas, promover a saúde e construir um Moçambique mais saudável e pacífico. Obrigado por fazer parte desta missão!</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
$conn->close();
include 'includes/footer.php'; 
?>