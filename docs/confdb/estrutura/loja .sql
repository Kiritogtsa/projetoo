-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 12/06/2024 às 17:46
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
DROP DATABASE IF EXISTS `loja`;
CREATE DATABASE `loja` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `loja`;

-- --------------------------------------------------------

--
-- Estrutura para tabela `historico_compras`
--
-- Criação: 12/06/2024 às 14:16
-- Última atualização: 12/06/2024 às 14:25
--

DROP TABLE IF EXISTS `historico_compras`;
CREATE TABLE `historico_compras` (
  `id` int(11) NOT NULL,
  `produto_id` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `data_compra` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELACIONAMENTOS PARA TABELAS `historico_compras`:
--   `produto_id`
--       `produtos` -> `id`
--   `usuario_id`
--       `usuario` -> `id`
--

--
-- Despejando dados para a tabela `historico_compras`
--


-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--
-- Criação: 06/06/2024 às 07:33
-- Última atualização: 12/06/2024 às 14:25
--

DROP TABLE IF EXISTS `produtos`;
CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(15) NOT NULL,
  `quant` int(11) DEFAULT NULL,
  `preco` double NOT NULL,
  `vendedor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELACIONAMENTOS PARA TABELAS `produtos`:
--   `vendedor_id`
--       `vendedores` -> `id`
--

--
-- Despejando dados para a tabela `produtos`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuario`3
--
-- Criação: 06/06/2024 às 07:33
-- Última atualização: 12/06/2024 às 15:44
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nome` varchar(15) NOT NULL,
  `email` varchar(25) NOT NULL,
  `senha` text NOT NULL,
  `vendedor` tinyint(1) NOT NULL,
  `vendedor_id` int(11) DEFAULT NULL,
  `saldo` double NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELACIONAMENTOS PARA TABELAS `usuario`:
--   `vendedor_id`
--       `vendedores` -> `id`
--

--
-- Despejando dados para a tabela `usuario`
--


--
-- Estrutura para tabela `vendedores`
--
-- Criação: 06/06/2024 às 07:33
-- Última atualização: 12/06/2024 às 15:44
--

DROP TABLE IF EXISTS `vendedores`;
CREATE TABLE `vendedores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `produtos` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELACIONAMENTOS PARA TABELAS `vendedores`:
--   `usuario_id`
--       `usuario` -> `id`
--

--
-- Despejando dados para a tabela `vendedores`
--


--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `historico_compras`
--
ALTER TABLE `historico_compras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_historico_produto` (`produto_id`),
  ADD KEY `fk_historico_usuario` (`usuario_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD KEY `vendedor_id` (`vendedor_id`);

--
-- Índices de tabela `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome` (`nome`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_usuario_vendedores` (`vendedor_id`);

--
-- Índices de tabela `vendedores`
--
ALTER TABLE `vendedores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `historico_compras`
--
ALTER TABLE `historico_compras`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `vendedores`
--
ALTER TABLE `vendedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `historico_compras`
--
ALTER TABLE `historico_compras`
  ADD CONSTRAINT `fk_historico_produto` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_historico_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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

-- Adicionar mais produtos

-- Adicionar mais usuários vendedores
-- Adicionar mais usuários vendedores
INSERT INTO `usuario` (`nome`, `email`, `senha`, `vendedor`, `saldo`) VALUES
('vendedor2', 'vendedor2@example.com', 'senha123', 1, 0), -- Senha: senha123
('vendedor3', 'vendedor3@example.com', 'outrasenha', 1, 0); -- Senha: outrasenha

-- Atualizar o ID do vendedor nos usuários existentes

-- Inserir registros na tabela vendedores
INSERT INTO `vendedores` (`usuario_id`, `produtos`) VALUES
(LAST_INSERT_ID()-1, 0), -- Vendedor 2
(LAST_INSERT_ID(), 0); -- Vendedor 3
UPDATE `usuario` SET `vendedor_id` = LAST_INSERT_ID() WHERE `nome` = 'vendedor2';
UPDATE `usuario` SET `vendedor_id` = LAST_INSERT_ID() WHERE `nome` = 'vendedor3';
