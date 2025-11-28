-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24/11/2025 às 18:25
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
-- Banco de dados: `produtos_db`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `imagem` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `nome`, `descricao`, `preco`, `categoria`, `quantidade`, `imagem`) VALUES
(7, 'the last of us', 'jogo de zumbi e sobrevivencia com uma historai emocionante', 100.00, 'historia', 100, 'https://www.adrenaline.com.br/wp-content/uploads/2022/11/Capa_do_jogo_The_Last_of_Us_Imagem_Divulgacao_Naughty_Dog.jpg'),
(8, 'FIFA 26 ', '', 250.00, 'esportes', 200, 'https://imgs.search.brave.com/nKSslC2MNQXptyyUPyyLjktaPDkiU8xT7F_IFd_5hGs/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly93YWxs/cGFwZXJjYXZlLmNv/bS93cC93cDE1NTk2/NDAzLmpwZw'),
(9, 'Naruto storm 4', '', 100.00, 'Aventura', 130, 'https://imgs.search.brave.com/UZeCqqMVrhlhsE5Iq-ZCoqaCggW9CCz_Gan0GD_BxlQ/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9tLm1l/ZGlhLWFtYXpvbi5j/b20vaW1hZ2VzL0kv/NTFCZlpsSDhvK0wu/anBn'),
(10, 'Batman arkham night', '', 80.00, 'historia', 270, 'https://imgs.search.brave.com/zfqNQbNcIf8sIEo4HScIiYGSM16czH-fAXFJ30urobs/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9tLm1l/ZGlhLWFtYXpvbi5j/b20vaW1hZ2VzL00v/TVY1Qk1UYzBNVGN4/TXpRME1sNUJNbDVC/YW5CblhrRnRaVGd3/TkRjM016RTBNVEVA/LmpwZw'),
(11, 'uncharted 4 ', '', 83.90, 'historia', 5, 'https://imgs.search.brave.com/JAGkNcsPzv2F_K5yK18D61DthvNJ2Sd-PSPpFp3EJ7U/rs:fit:860:0:0:0/g:ce/aHR0cHM6Ly9jZG4u/YXdzbGkuY29tLmJy/LzYwMHg0NTAvMTM4/LzEzODQzMS9wcm9k/dXRvLzMyNDA4ODMx/LzdkMzFkNjIxZmIu/anBn');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
