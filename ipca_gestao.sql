-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 20-Mar-2026 às 15:34
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `ipca_gestao`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `cursos`
--

CREATE TABLE `cursos` (
  `Id_cursos` int(11) NOT NULL,
  `Nome` varchar(100) NOT NULL,
  `duracao` int(11) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `cursos`
--

INSERT INTO `cursos` (`Id_cursos`, `Nome`, `duracao`, `descricao`, `data_cadastro`) VALUES
(1, 'Engenharia de Software', 8, 'Curso focado em desenvolvimento e gestão.', '2026-03-06 10:28:10'),
(2, 'Design Gráfico', 6, 'Focado em comunicação visual.', '2026-03-06 10:28:10'),
(3, 'Gestão de Empresas', 6, 'Focado em administração e negócios.', '2026-03-06 10:28:10'),
(5, 'Desenvolvimento Web e Multimédia', 0, NULL, '2026-03-06 11:21:06'),
(7, 'Engenharia Física', 0, NULL, '2026-03-13 21:16:10'),
(8, 'Medicina', 0, NULL, '2026-03-20 11:05:42');

-- --------------------------------------------------------

--
-- Estrutura da tabela `disciplinas`
--

CREATE TABLE `disciplinas` (
  `Id_disciplina` int(11) NOT NULL,
  `nome_disciplina` varchar(100) NOT NULL,
  `carga_horaria` int(11) NOT NULL,
  `curso_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `disciplinas`
--

INSERT INTO `disciplinas` (`Id_disciplina`, `nome_disciplina`, `carga_horaria`, `curso_id`) VALUES
(1, 'Algoritmos e Estruturas de Dados', 60, 1),
(2, 'Sistemas Operacionais', 45, 1),
(4, 'Matemática', 0, NULL),
(6, 'Geometria Descritiva', 0, NULL),
(7, 'Economia', 0, NULL),
(8, 'Biologia Molecular', 0, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `ficha_aluno`
--

CREATE TABLE `ficha_aluno` (
  `id` int(11) NOT NULL,
  `utilizador_id` int(11) NOT NULL,
  `morada` varchar(255) NOT NULL,
  `contacto` varchar(20) NOT NULL,
  `foto_path` varchar(255) NOT NULL,
  `estado` enum('Rascunho','Submetida','Aprovada','Rejeitada') DEFAULT 'Submetida',
  `observacoes_gestor` text DEFAULT NULL,
  `data_submissao` timestamp NOT NULL DEFAULT current_timestamp(),
  `validado_por` int(11) DEFAULT NULL,
  `data_decisao` datetime DEFAULT NULL,
  `observacoes` text DEFAULT NULL,
  `curso_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `ficha_aluno`
--

INSERT INTO `ficha_aluno` (`id`, `utilizador_id`, `morada`, `contacto`, `foto_path`, `estado`, `observacoes_gestor`, `data_submissao`, `validado_por`, `data_decisao`, `observacoes`, `curso_id`) VALUES
(2, 11, 'Rua dos Quatro Caminhos, nº163', '925889427', 'uploads/aluno_11_1773760366.jpg', 'Aprovada', NULL, '2026-03-17 15:12:46', 10, '2026-03-20 11:06:23', 'bom aluno', NULL),
(3, 11, 'Rua dos Quatro Caminhos, nº163', '925889427', 'uploads/foto_11_1773940027.jpg', 'Aprovada', NULL, '2026-03-19 17:07:07', 10, '2026-03-20 11:06:32', 'bom aluno', NULL),
(4, 11, 'Rua dos Quatro Caminhos, nº163', '925889427', 'uploads/aluno_11_1773968613.jpg', 'Aprovada', NULL, '2026-03-20 01:03:33', 10, '2026-03-20 01:04:46', 'muito bonito', NULL),
(5, 1000, 'Rua dos Quatro Caminhos, nº163', '925889427', 'uploads/aluno_1000_1774005430.webp', 'Aprovada', NULL, '2026-03-20 11:17:10', 10, '2026-03-20 11:17:39', 'bonito', 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `matriculas`
--

CREATE TABLE `matriculas` (
  `id` int(11) NOT NULL,
  `utilizador_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `data_pedido` timestamp NOT NULL DEFAULT current_timestamp(),
  `estado` varchar(20) DEFAULT 'Pendente',
  `validado_por` int(11) DEFAULT NULL,
  `data_decisao` datetime DEFAULT NULL,
  `data_submissao` datetime DEFAULT current_timestamp(),
  `observacoes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `matriculas`
--

INSERT INTO `matriculas` (`id`, `utilizador_id`, `curso_id`, `data_pedido`, `estado`, `validado_por`, `data_decisao`, `data_submissao`, `observacoes`) VALUES
(3, 99, 1, '2026-03-18 22:07:08', '', 12, NULL, '2026-03-18 22:07:08', NULL),
(4, 999, 1, '2026-03-19 16:37:32', 'Aprovada', 12, NULL, '2026-03-19 16:37:32', NULL),
(5, 10, 1, '2026-03-19 17:05:12', 'Aprovada', NULL, NULL, '2026-03-19 17:05:12', NULL),
(6, 11, 5, '2026-03-20 01:05:31', 'Aprovada', 12, NULL, '2026-03-20 01:05:31', NULL),
(7, 11, 3, '2026-03-20 11:08:34', 'Aprovada', 12, '2026-03-20 11:09:43', '2026-03-20 11:08:34', 'bom currículo'),
(8, 1000, 2, '2026-03-20 11:19:18', 'Aprovada', 12, '2026-03-20 11:19:43', '2026-03-20 11:19:18', 'tudo certo');

-- --------------------------------------------------------

--
-- Estrutura da tabela `notas`
--

CREATE TABLE `notas` (
  `id` int(11) NOT NULL,
  `utilizador_id` int(11) NOT NULL,
  `curso_id` int(11) NOT NULL,
  `nota` decimal(4,2) NOT NULL,
  `data_lancamento` datetime DEFAULT current_timestamp(),
  `lancado_por` int(11) DEFAULT NULL,
  `ano_letivo` varchar(20) DEFAULT NULL,
  `epoca` varchar(50) DEFAULT NULL,
  `uc_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `notas`
--

INSERT INTO `notas` (`id`, `utilizador_id`, `curso_id`, `nota`, `data_lancamento`, `lancado_por`, `ano_letivo`, `epoca`, `uc_id`) VALUES
(1, 999, 1, 18.50, '2026-03-19 17:06:11', 12, NULL, NULL, NULL),
(2, 999, 1, 18.50, '2026-03-19 17:07:50', 12, NULL, NULL, NULL),
(3, 999, 1, 15.50, '2026-03-19 17:16:46', 12, NULL, NULL, NULL),
(4, 11, 1, 18.00, '2026-03-19 18:43:46', 12, NULL, NULL, NULL),
(5, 1000, 2, 19.00, '2026-03-20 11:20:31', 12, '2025/2026', 'Especial', 6);

-- --------------------------------------------------------

--
-- Estrutura da tabela `plano_estudos`
--

CREATE TABLE `plano_estudos` (
  `id` int(11) NOT NULL,
  `cursos` int(11) NOT NULL,
  `disciplinas` int(11) NOT NULL,
  `semestre` int(11) NOT NULL,
  `obrigatoria` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `plano_estudos`
--

INSERT INTO `plano_estudos` (`id`, `cursos`, `disciplinas`, `semestre`, `obrigatoria`) VALUES
(1, 1, 1, 1, 1),
(2, 5, 2, 2, 1),
(3, 7, 4, 0, 1),
(5, 2, 6, 0, 1),
(6, 3, 7, 0, 1),
(7, 8, 8, 0, 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `unidades_curriculares`
--

CREATE TABLE `unidades_curriculares` (
  `id` int(11) NOT NULL,
  `nome_uc` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `nivel` enum('admin','aluno') NOT NULL DEFAULT 'aluno',
  `perfil` enum('Aluno','Funcionário','Gestor') DEFAULT 'Aluno'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `nivel`, `perfil`) VALUES
(10, 'Administrador', 'admin@ipca.pt', '$2y$10$6lokAE.BI6psD5eoy0vPzuN4BFSDiRraKAQyctit10m24TddLoPBG', 'aluno', 'Gestor'),
(11, 'Aluno Teste', 'aluno@ipca.pt', '$2y$10$6lokAE.BI6psD5eoy0vPzuN4BFSDiRraKAQyctit10m24TddLoPBG', 'aluno', 'Aluno'),
(12, 'Secretaria Académica', 'func@ipca.pt', '$2y$10$6lokAE.BI6psD5eoy0vPzuN4BFSDiRraKAQyctit10m24TddLoPBG', 'aluno', 'Funcionário'),
(99, 'Aluno de Teste', 'aluno_teste@ipca.pt', '$2y$10$6lokAE.BI6psD5eoy0vPzuN4BFSDiRraKAQyctit10m24TddLoPBG', 'aluno', 'Aluno'),
(999, 'Aluno Teste Final', 'teste@ipca.pt', '$2y$10$6lokAE.BI6psD5eoy0vPzuN4BFSDiRraKAQyctit10m24TddLoPBG', 'aluno', 'Aluno'),
(1000, 'Afonso', 'afonso@ipca.pt', '$2y$10$NbJA8G.vl8BddSVB9LbNqui5MeSNexyxMNnirZO.DC./k7VOuJnNC', 'aluno', 'Aluno');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `cursos`
--
ALTER TABLE `cursos`
  ADD PRIMARY KEY (`Id_cursos`);

--
-- Índices para tabela `disciplinas`
--
ALTER TABLE `disciplinas`
  ADD PRIMARY KEY (`Id_disciplina`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Índices para tabela `ficha_aluno`
--
ALTER TABLE `ficha_aluno`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_usuario_ficha` (`utilizador_id`);

--
-- Índices para tabela `matriculas`
--
ALTER TABLE `matriculas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilizador_id` (`utilizador_id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Índices para tabela `notas`
--
ALTER TABLE `notas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilizador_id` (`utilizador_id`),
  ADD KEY `curso_id` (`curso_id`);

--
-- Índices para tabela `plano_estudos`
--
ALTER TABLE `plano_estudos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curso_id` (`cursos`),
  ADD KEY `disciplina_id` (`disciplinas`);

--
-- Índices para tabela `unidades_curriculares`
--
ALTER TABLE `unidades_curriculares`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nome_uc` (`nome_uc`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cursos`
--
ALTER TABLE `cursos`
  MODIFY `Id_cursos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `disciplinas`
--
ALTER TABLE `disciplinas`
  MODIFY `Id_disciplina` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `ficha_aluno`
--
ALTER TABLE `ficha_aluno`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `matriculas`
--
ALTER TABLE `matriculas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `notas`
--
ALTER TABLE `notas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `plano_estudos`
--
ALTER TABLE `plano_estudos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `unidades_curriculares`
--
ALTER TABLE `unidades_curriculares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1001;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `disciplinas`
--
ALTER TABLE `disciplinas`
  ADD CONSTRAINT `disciplinas_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`Id_cursos`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `ficha_aluno`
--
ALTER TABLE `ficha_aluno`
  ADD CONSTRAINT `fk_usuario_ficha` FOREIGN KEY (`utilizador_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `matriculas`
--
ALTER TABLE `matriculas`
  ADD CONSTRAINT `matriculas_ibfk_1` FOREIGN KEY (`utilizador_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `matriculas_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`Id_cursos`);

--
-- Limitadores para a tabela `notas`
--
ALTER TABLE `notas`
  ADD CONSTRAINT `notas_ibfk_1` FOREIGN KEY (`utilizador_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notas_ibfk_2` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`Id_cursos`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `plano_estudos`
--
ALTER TABLE `plano_estudos`
  ADD CONSTRAINT `plano_estudos_ibfk_1` FOREIGN KEY (`cursos`) REFERENCES `cursos` (`Id_cursos`) ON DELETE CASCADE,
  ADD CONSTRAINT `plano_estudos_ibfk_2` FOREIGN KEY (`disciplinas`) REFERENCES `disciplinas` (`Id_disciplina`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
