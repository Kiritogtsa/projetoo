-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 05/06/2024 às 16:04
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `loja`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(15) NOT NULL,
  `quant` int(11) DEFAULT NULL,
  `preco` double NOT NULL,
  `vendedor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nome` varchar(15) NOT NULL,
  `email` varchar(25) NOT NULL,
  `senha` text NOT NULL,
  `vendedor` BOOLEAN NOT NULL,
  `vendedor_id` int(11) DEFAULT NULL,
  `saldo` double NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendedores`
--

CREATE TABLE `vendedores` (
  `id` int(11) NOT NULL ,
  `usuario_id` int(11) NOT NULL,
  `produtos` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `nome` (`nome`),
  ADD KEY `vendedor_id` (`vendedor_id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usuario_vendedores` (`vendedor_id`);

ALTER TABLE `usuario` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Índices de tabela `vendedores`
--
ALTER TABLE `vendedores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);
  
ALTER TABLE `vendedores` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `vendedores`
--
ALTER TABLE `vendedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `produtos`
--
ALTER TABLE `produtos`
  ADD CONSTRAINT `fk_produtos_vendedores` FOREIGN KEY (`vendedor_id`) REFERENCES `vendedores` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para tabelas `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `fk_usuario_vendedores` FOREIGN KEY (`vendedor_id`) REFERENCES `vendedores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para tabelas `vendedores`
--
ALTER TABLE `vendedores`
  ADD CONSTRAINT `fk_vendedores_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

CREATE TABLE `historico_compras` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data_compra` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_historico_produto` (`produto_id`),
  KEY `fk_historico_usuario` (`usuario_id`),
  CONSTRAINT `fk_historico_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_historico_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

START TRANSACTION;

-- Criar um usuário que não é vendedor
INSERT INTO `usuario` (`nome`, `email`, `senha`, `vendedor`) 
VALUES ('Ana Souza', 'ana@example.com', '$2y$10$5xzAypcdc1dZlxKkpcgQ.uBpkUD2tcnzJkOGV4Au5BrYFSf2dpF/G', FALSE);

-- Criar um novo usuário que será vendedor
INSERT INTO `usuario` (`nome`, `email`, `senha`, `vendedor`) 
VALUES ('Joao Silva', 'joao@example.com', '$2y$10$f.kJfdvRuAY4pG1ohXWOkuB5EO.LqoY.nty.Rp3W.bsR8OufFkrZS', TRUE);

-- Obter o ID do usuário vendedor recém-criado
SELECT @user_vendedor_id := `id` FROM `usuario` WHERE `email` = 'joao@example.com';

-- Inserir o vendedor usando o ID do usuário vendedor
INSERT INTO `vendedores` (`usuario_id`) 
VALUES (@user_vendedor_id);

-- Obter o ID do vendedor recém-criado
SELECT @vendedor_id := `id` FROM `vendedores` WHERE `usuario_id` = @user_vendedor_id;

-- Atualizar o usuário vendedor para associar o vendedor_id
UPDATE `usuario` 
SET `vendedor_id` = @vendedor_id 
WHERE `id` = @user_vendedor_id;

-- Criar um novo produto para o vendedor
INSERT INTO `produtos` (`nome`, `quant`, `preco`, `vendedor_id`) 
VALUES ('Produto A', 100, 29.99, @vendedor_id);

COMMIT;