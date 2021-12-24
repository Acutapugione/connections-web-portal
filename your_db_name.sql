-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Дек 24 2021 г., 10:51
-- Версия сервера: 5.5.39
-- Версия PHP: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `{your db name}`
--

-- --------------------------------------------------------

--
-- Структура таблицы `conn_appeals`
--

CREATE TABLE IF NOT EXISTS `conn_appeals` (
`id` int(11) unsigned NOT NULL,
  `order_id` int(11) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `appeal_type_id` int(11) unsigned NOT NULL,
  `text` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(150) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `conn_appeal_types`
--

CREATE TABLE IF NOT EXISTS `conn_appeal_types` (
`id` int(11) unsigned NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Структура таблицы `conn_clients`
--

CREATE TABLE IF NOT EXISTS `conn_clients` (
`id` int(11) unsigned NOT NULL,
  `password` varchar(50) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Структура таблицы `conn_contracts`
--

CREATE TABLE IF NOT EXISTS `conn_contracts` (
`id` int(11) unsigned NOT NULL,
  `client_id` int(11) unsigned DEFAULT NULL,
  `ipn_key` varchar(50) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `conn_orders`
--

CREATE TABLE IF NOT EXISTS `conn_orders` (
`id` int(11) unsigned NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `contract_id` int(11) unsigned NOT NULL,
  `address` varchar(250) NOT NULL,
  `conn_type_name` varchar(250) NOT NULL,
  `project_executor` varchar(250) NOT NULL,
  `planned_capacity` decimal(10,3) NOT NULL,
  `technical_condition` varchar(250) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `conn_org_steps`
--

CREATE TABLE IF NOT EXISTS `conn_org_steps` (
`id` int(11) unsigned NOT NULL,
  `order_id` int(11) unsigned NOT NULL,
  `step_type_id` int(11) unsigned NOT NULL,
  `executor` varchar(250) NOT NULL,
  `start_at` timestamp NULL DEFAULT NULL,
  `deadline_at` timestamp NULL DEFAULT NULL,
  `done_at` timestamp NULL DEFAULT NULL,
  `commentary` varchar(250) NOT NULL,
  `sustain` varchar(250) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Удалено'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `conn_steps`
--

CREATE TABLE IF NOT EXISTS `conn_steps` (
`id` int(11) unsigned NOT NULL,
  `step_type_id` int(11) unsigned NOT NULL DEFAULT '1',
  `order_id` int(11) unsigned NOT NULL,
  `n_dogovor` varchar(250) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `payed_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `nalog` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) unsigned DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

--
-- Триггеры `conn_steps`
--
DELIMITER //
CREATE TRIGGER `tg_step_insert` BEFORE INSERT ON `conn_steps`
 FOR EACH ROW BEGIN
SET NEW.total = NEW.price + (NEW.price*NEW.nalog);
SET NEW.updated_at = CURRENT_TIMESTAMP;
END
//
DELIMITER ;
DELIMITER //
CREATE TRIGGER `tg_step_update` BEFORE UPDATE ON `conn_steps`
 FOR EACH ROW BEGIN
SET NEW.total = NEW.price + (NEW.price*NEW.nalog);
SET NEW.updated_at = CURRENT_TIMESTAMP;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `conn_step_types`
--

CREATE TABLE IF NOT EXISTS `conn_step_types` (
`id` int(11) unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `key_1c` varchar(50) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `conn_appeals`
--
ALTER TABLE `conn_appeals`
 ADD PRIMARY KEY (`id`), ADD KEY `appeal_type_id` (`appeal_type_id`), ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `conn_appeal_types`
--
ALTER TABLE `conn_appeal_types`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `message_type_name` (`name`);

--
-- Indexes for table `conn_clients`
--
ALTER TABLE `conn_clients`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `mail` (`mail`);

--
-- Indexes for table `conn_contracts`
--
ALTER TABLE `conn_contracts`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `uniq_ipn_client` (`client_id`,`ipn_key`), ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `conn_orders`
--
ALTER TABLE `conn_orders`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `order_number` (`order_number`,`contract_id`), ADD KEY `contract_id` (`contract_id`);

--
-- Indexes for table `conn_org_steps`
--
ALTER TABLE `conn_org_steps`
 ADD PRIMARY KEY (`id`), ADD KEY `order_id` (`order_id`), ADD KEY `step_type_id` (`step_type_id`);

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
-- AUTO_INCREMENT for table `conn_appeals`
--
ALTER TABLE `conn_appeals`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `conn_appeal_types`
--
ALTER TABLE `conn_appeal_types`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `conn_clients`
--
ALTER TABLE `conn_clients`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `conn_contracts`
--
ALTER TABLE `conn_contracts`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `conn_orders`
--
ALTER TABLE `conn_orders`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `conn_org_steps`
--
ALTER TABLE `conn_org_steps`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `conn_steps`
--
ALTER TABLE `conn_steps`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `conn_step_types`
--
ALTER TABLE `conn_step_types`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=1;
--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `conn_appeals`
--
ALTER TABLE `conn_appeals`
ADD CONSTRAINT `conn_appeals_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `conn_orders` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `conn_appeals_ibfk_2` FOREIGN KEY (`appeal_type_id`) REFERENCES `conn_appeal_types` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Ограничения внешнего ключа таблицы `conn_contracts`
--
ALTER TABLE `conn_contracts`
ADD CONSTRAINT `conn_contracts_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `conn_clients` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `conn_orders`
--
ALTER TABLE `conn_orders`
ADD CONSTRAINT `conn_orders_ibfk_2` FOREIGN KEY (`contract_id`) REFERENCES `conn_contracts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `conn_org_steps`
--
ALTER TABLE `conn_org_steps`
ADD CONSTRAINT `conn_org_steps_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `conn_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `conn_org_steps_ibfk_2` FOREIGN KEY (`step_type_id`) REFERENCES `conn_step_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `conn_steps`
--
ALTER TABLE `conn_steps`
ADD CONSTRAINT `conn_steps_ibfk_1` FOREIGN KEY (`step_type_id`) REFERENCES `conn_step_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `conn_steps_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `conn_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
