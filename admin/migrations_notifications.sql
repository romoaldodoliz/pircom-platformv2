-- =====================================================
-- Migration: Sistema de Notificações do Admin
-- Data: 7 de Fevereiro de 2026
-- =====================================================

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `mensagem` TEXT NOT NULL,
  `tipo` ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
  `referencia_tipo` VARCHAR(100), -- ex: 'noticia', 'evento', 'utilizador'
  `referencia_id` INT, -- ID do recurso relacionado
  `lida` BOOLEAN DEFAULT FALSE,
  `criada_em` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `expira_em` TIMESTAMP NULL, -- 30 minutos após criação
  `vista_em` TIMESTAMP NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX (user_id, lida),
  INDEX (expira_em)
);

-- =====================================================
-- Triggers para definir expiração (30 minutos)
-- =====================================================

DELIMITER $$

CREATE TRIGGER notificacao_set_expiracao
BEFORE INSERT ON notifications
FOR EACH ROW
BEGIN
  SET NEW.expira_em = DATE_ADD(NOW(), INTERVAL 30 MINUTE);
END$$

DELIMITER ;
