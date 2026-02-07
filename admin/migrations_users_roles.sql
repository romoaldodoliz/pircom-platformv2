-- =====================================================
-- Migration: Criar Utilizadores Admin e Manager
-- Data: 7 de Fevereiro de 2026
-- =====================================================

-- Primeiro, adicionar coluna role se não existir
ALTER TABLE `users` 
ADD COLUMN `role` enum('admin', 'manager') DEFAULT 'manager' AFTER `senha`;

-- Limpar utilizadores existentes e recriar com dados completos
TRUNCATE TABLE `users`;
ALTER TABLE `users` AUTO_INCREMENT = 1;

-- Inserir usuário ADMIN
INSERT INTO `users` (`id`, `nome`, `email`, `senha`, `role`) VALUES
(1, 'Administrador PIRCOM', 'admin@pircom.org.mz', '$2y$10$sqydC.y6PbYte8uxpwaRB..Aydy/J6K3JbVscyL1cQFSEmpMSuI/O', 'admin');

-- Inserir usuário MANAGER
INSERT INTO `users` (`id`, `nome`, `email`, `senha`, `role`) VALUES
(2, 'Gestor de Conteúdo', 'manager@pircom.org.mz', '$2y$10$sqydC.y6PbYte8uxpwaRB..Aydy/J6K3JbVscyL1cQFSEmpMSuI/O', 'manager');

-- Verificar inserção
SELECT id, nome, email, role FROM users;

-- =====================================================
-- CREDENCIAIS PARA TESTES
-- =====================================================
-- 
-- ADMIN:
-- Email: admin@pircom.org.mz
-- Senha: password (senha hasheada com BCRYPT)
-- Role: admin
-- Acesso: Total ao sistema
--
-- MANAGER:
-- Email: manager@pircom.org.mz
-- Senha: password (mesma senha hasheada)
-- Role: manager
-- Acesso: Cria e edita conteúdos, não pode deletar
--
-- =====================================================
