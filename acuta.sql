-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Ноя 17 2021 г., 15:16
-- Версия сервера: 5.5.39
-- Версия PHP: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `acuta`
--

-- --------------------------------------------------------

--
-- Структура таблицы `conn_clients`
--

CREATE TABLE IF NOT EXISTS `conn_clients` (
`id` int(11) unsigned NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `ipn_key` varchar(50) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `conn_clients`
--

INSERT INTO `conn_clients` (`id`, `login`, `password`, `mail`, `ipn_key`) VALUES
(1, 'admin', 'admin', 'admin@admin.com', '123456789');

-- --------------------------------------------------------

--
-- Структура таблицы `conn_contracts`
--

CREATE TABLE IF NOT EXISTS `conn_contracts` (
`id` int(11) unsigned NOT NULL,
  `ipn_key` varchar(50) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=2 ;

--
-- Дамп данных таблицы `conn_contracts`
--

INSERT INTO `conn_contracts` (`id`, `ipn_key`) VALUES
(1, '123456789');

-- --------------------------------------------------------

--
-- Структура таблицы `conn_messages`
--

CREATE TABLE IF NOT EXISTS `conn_messages` (
`id` int(11) unsigned NOT NULL,
  `message_type_id` int(11) unsigned NOT NULL,
  `src_id` int(11) unsigned NOT NULL,
  `order_id` int(11) unsigned DEFAULT NULL,
  `mess_tittle` varchar(150) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `mess_ctx` varchar(300) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=20 ;

--
-- Дамп данных таблицы `conn_messages`
--

INSERT INTO `conn_messages` (`id`, `message_type_id`, `src_id`, `order_id`, `mess_tittle`, `created_at`, `deleted_at`, `mess_ctx`) VALUES
(17, 1, 1, 1, 'asasvasv', '2021-11-17 13:51:33', NULL, 'test1'),
(18, 1, 1, 2, '1sdagasgasg', '2021-11-17 14:14:00', NULL, 'Test2'),
(19, 2, 1, NULL, 'asgsag', '2021-11-17 14:14:24', NULL, 'Test3');

-- --------------------------------------------------------

--
-- Структура таблицы `conn_message_types`
--

CREATE TABLE IF NOT EXISTS `conn_message_types` (
`id` int(11) unsigned NOT NULL,
  `message_type_name` varchar(100) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `conn_message_types`
--

INSERT INTO `conn_message_types` (`id`, `message_type_name`) VALUES
(1, 'Про перевірку наданих документів'),
(2, 'Про хід замовлення');

-- --------------------------------------------------------

--
-- Структура таблицы `conn_orders`
--

CREATE TABLE IF NOT EXISTS `conn_orders` (
`id` int(11) unsigned NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `client_id` int(11) unsigned DEFAULT NULL,
  `contract_id` int(11) unsigned DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=3 ;

--
-- Дамп данных таблицы `conn_orders`
--

INSERT INTO `conn_orders` (`id`, `order_number`, `client_id`, `contract_id`) VALUES
(1, '12356', 1, 1),
(2, '234567', 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `conn_steps`
--

CREATE TABLE IF NOT EXISTS `conn_steps` (
`id` int(11) unsigned NOT NULL,
  `step_type_id` int(11) unsigned NOT NULL DEFAULT '1',
  `order_id` int(11) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `nalog` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) unsigned DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=47 ;

--
-- Дамп данных таблицы `conn_steps`
--

INSERT INTO `conn_steps` (`id`, `step_type_id`, `order_id`, `created_at`, `payed_at`, `completed_at`, `deleted_at`, `price`, `nalog`, `total`) VALUES
(45, 8, 1, '2001-01-20 09:00:00', '2002-01-20 09:00:00', '2012-01-20 09:00:00', NULL, '525.00', '0.18', '619.50'),
(46, 9, 1, '2025-01-20 09:00:00', '2016-02-20 09:00:00', '2012-05-20 08:00:00', NULL, '5115.00', '0.18', '6035.70');

--
-- Триггеры `conn_steps`
--
DELIMITER //
CREATE TRIGGER `tg_step_insert` BEFORE INSERT ON `conn_steps`
 FOR EACH ROW SET NEW.total = NEW.price + (NEW.price*NEW.nalog)
//
DELIMITER ;
DELIMITER //
CREATE TRIGGER `tg_step_update` BEFORE UPDATE ON `conn_steps`
 FOR EACH ROW SET NEW.total = NEW.price + (NEW.price*NEW.nalog)
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `conn_step_types`
--

CREATE TABLE IF NOT EXISTS `conn_step_types` (
`id` int(11) unsigned NOT NULL,
  `name` varchar(150) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=10 ;

--
-- Дамп данных таблицы `conn_step_types`
--

INSERT INTO `conn_step_types` (`id`, `name`) VALUES
(8, 'Видача технічних умов на приєднання'),
(9, 'Приєднання до газорозподільних мереж (стандартне)');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `conn_clients`
--
ALTER TABLE `conn_clients`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `mail` (`mail`), ADD UNIQUE KEY `ipn_key` (`ipn_key`);

--
-- Indexes for table `conn_contracts`
--
ALTER TABLE `conn_contracts`
 ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conn_messages`
--
ALTER TABLE `conn_messages`
 ADD PRIMARY KEY (`id`), ADD KEY `message_type_id` (`message_type_id`), ADD KEY `src_id` (`src_id`);

--
-- Indexes for table `conn_message_types`
--
ALTER TABLE `conn_message_types`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `message_type_name` (`message_type_name`);

--
-- Indexes for table `conn_orders`
--
ALTER TABLE `conn_orders`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `order_number` (`order_number`), ADD KEY `contract_id` (`contract_id`), ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `conn_steps`
--
ALTER TABLE `conn_steps`
 ADD PRIMARY KEY (`id`), ADD KEY `step_type_id` (`step_type_id`), ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `conn_step_types`
--
ALTER TABLE `conn_step_types`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `conn_clients`
--
ALTER TABLE `conn_clients`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `conn_contracts`
--
ALTER TABLE `conn_contracts`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `conn_messages`
--
ALTER TABLE `conn_messages`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `conn_message_types`
--
ALTER TABLE `conn_message_types`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `conn_orders`
--
ALTER TABLE `conn_orders`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `conn_steps`
--
ALTER TABLE `conn_steps`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=47;
--
-- AUTO_INCREMENT for table `conn_step_types`
--
ALTER TABLE `conn_step_types`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `conn_messages`
--
ALTER TABLE `conn_messages`
ADD CONSTRAINT `conn_messages_ibfk_1` FOREIGN KEY (`src_id`) REFERENCES `conn_clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `conn_messages_ibfk_2` FOREIGN KEY (`message_type_id`) REFERENCES `conn_message_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `conn_orders`
--
ALTER TABLE `conn_orders`
ADD CONSTRAINT `conn_orders_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `conn_clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `conn_orders_ibfk_2` FOREIGN KEY (`contract_id`) REFERENCES `conn_contracts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `conn_steps`
--
ALTER TABLE `conn_steps`
ADD CONSTRAINT `conn_steps_ibfk_1` FOREIGN KEY (`step_type_id`) REFERENCES `conn_step_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
