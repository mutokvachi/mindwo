-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 21, 2016 at 06:57 AM
-- Server version: 5.6.16
-- PHP Version: 5.5.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `latvenergo11_empty`
--

-- --------------------------------------------------------

--
-- Table structure for table `dx_aggregation_types`
--

CREATE TABLE IF NOT EXISTS `dx_aggregation_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL COMMENT 'The title of aggregation type - visible in CMS interface',
  `sys_name` varchar(200) DEFAULT NULL COMMENT 'The system code of aggregation type - used in CMS PHP code',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `title` (`title`,`sys_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Definējot skata kolonnas var izmantot datus no dažādiem reģistriem. Ir pamatdatu reģistrs, kur katram ierakstam var piekārtot saistītā reģistra ierakstu (no vecāka vai no pakārtotā reģistra). Piekārtojot datus no pakārtotā reģistra (tātad saite 1:n), var norādīt, kā kalkulēt datus, piemēram, summēt, izmantot pēdējo vai pirmo ierakstu un tml' AUTO_INCREMENT=5 ;

--
-- Dumping data for table `dx_aggregation_types`
--

INSERT INTO `dx_aggregation_types` (`id`, `title`, `sys_name`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Pirmais ieraksts', 'MIN', NULL, NULL, NULL, NULL),
(2, 'Pēdējais ieraksts', 'MAX', NULL, NULL, NULL, NULL),
(3, 'Summa', 'SUM', NULL, NULL, NULL, NULL),
(4, 'Skaits', 'COUNT', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_change_log`
--

CREATE TABLE IF NOT EXISTS `dx_change_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) DEFAULT NULL COMMENT 'Reference to register',
  `item_id` int(11) DEFAULT NULL COMMENT 'Reference to register item',
  `field_id` int(11) DEFAULT NULL COMMENT 'Reference to changed field',
  `old_value` text COMMENT 'Old value - before change',
  `new_value` text COMMENT 'New value - after change',
  `log_time` datetime DEFAULT NULL COMMENT 'Change date and time',
  `user_id` int(11) DEFAULT NULL COMMENT 'Reference to user who made change to the item',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `list_id` (`list_id`),
  KEY `item_id` (`item_id`),
  KEY `field_id` (`field_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabulā tiek uzkrātas visas reģistru datu izmaiņas' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dx_config`
--

CREATE TABLE IF NOT EXISTS `dx_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `config_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Iestatījums',
  `config_hint` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Iestatījuma paskaidrojums',
  `field_type_id` int(11) DEFAULT NULL COMMENT 'Lauka tips',
  `val_varchar` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Teksts',
  `val_script` text COLLATE utf8_unicode_ci COMMENT 'Skripts',
  `val_integer` int(11) DEFAULT NULL COMMENT 'Skaitlis',
  `val_date` date DEFAULT NULL COMMENT 'Datums',
  `val_file_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Datne',
  `val_file_guid` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `val_yesno` tinyint(1) DEFAULT '0' COMMENT 'Iestatījums ieslēgts',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dx_config_config_name_index` (`config_name`),
  KEY `field_type_id` (`field_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=18 ;

--
-- Dumping data for table `dx_config`
--

INSERT INTO `dx_config` (`id`, `config_name`, `config_hint`, `field_type_id`, `val_varchar`, `val_script`, `val_integer`, `val_date`, `val_file_name`, `val_file_guid`, `val_yesno`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'PORTAL_LOGO_FILE', 'Portāla logo datne. Logo tiek attēlots lapu augšējā kreisajā stūrī', 12, NULL, NULL, NULL, NULL, 'leport_logo.gif', 'leport_logo.gif', NULL, NULL, NULL, NULL, NULL),
(2, 'PORTAL_NAME', 'Portāla nosaukums. Tiek likts priekšā visiem lapu nosaukumiem, piemēram, Nosaukums :: Lapas nosaukums', 1, 'Leports', NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, 19, '2016-03-20 15:57:01'),
(3, 'GOOGLE_ANALYTIC', 'Google Analytic skripts, kas uzkrāj statistikas informāciju par portāla lapu atvēršanu. Skripts tiek automātiski ievietots visās portāla lapās. Statistika jāaplūko Google Analytic publiskajā rīkā: https://analytics.google.com. \r\n\r\nUZMANĪBU: jāiekopē skripts kopā ar <script> un </script> tagiem!', 15, NULL, '<script>\r\n  (function(i,s,o,g,r,a,m){i[''GoogleAnalyticsObject'']=r;i[r]=i[r]||function(){\r\n  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),\r\n  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)\r\n  })(window,document,''script'',''//www.google-analytics.com/analytics.js'',''ga'');\r\n\r\n  ga(''create'', ''UA-5539495-11'', ''auto'');\r\n  ga(''send'', ''pageview'');\r\n\r\n</script>', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(4, 'EMPLOYEE_AVATAR', 'Attēls, kas tiks lietots pēc noklusēšanas gadījumos, ja darbiniekam nebūs norādīts attēls', 12, NULL, NULL, 0, NULL, 'default_avatar.jpg', '0cb73f84-fe96-4eb8-a78b-d2194072cd71.jpg', 0, NULL, NULL, NULL, NULL),
(5, 'VALID_HTML_ELEMENTS', 'HTML tagi un to atribūti, kas tiks atļauti WYSIWYG redaktorā. Tātad, kopējot Word tekstus, tie tiks automātiski formatēti atbilstoši atļautajiem tagiem. Informācija par iestatījuma formātu šeit: https://www.tinymce.com/docs/configure/content-filtering/#valid_elements', 1, 'p[style|align],em,a[href|target|style],strong/b,div[align|style],br,source[src|type],video[controls|width|height],img[src|alt|width|height],span[style],iframe[width|height|src|frameborder|allowfullscreen]', NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(6, 'VALID_HTML_STYLES', 'HTML atribūta "style" atļautie uzstādījumi. Jānorāda atdalot ar komatu un bez atstarpēm', 1, 'border,font-size,color,background-color', NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(7, 'DATASOURCE_ICON_CLASS', 'Norāda "fa" vai "icon" klases nosaukumu, kas nodrošinās ikonas attēlošanu pie darbinieku informācijas (uzņēmuma nosaukuma). Ikonas pieejamas šeit: http://www.keenthemes.com/preview/metronic/theme/admin_1/ui_icons.html\r\n\r\n"Fa" ikonu gadījumā vienmēr jānorāda arī klase "fa", piemēram, "fa fa-calendar"', 1, 'iconle-le_logo', NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(8, 'SCRIPT_CSS', 'Iestatījumā var norādīt CSS skriptu, kas tiks attiecināts uz visām portāla lapām. Skripts tiks ielādēts kā pēdējais no visiem CSS skriptiem.\r\n\r\nNav jānorāda <style> tagi - tikai saturs.', 15, NULL, '// this is an demo example - delete it and write your script\r\n.demo_test_class_foo {\r\n	color: red!important; // we can use "!important" to 100% overide existing CSSs\r\n}\r\n\r\n.portlet {\r\n    background-color: white;\r\n    padding: 20px;  \r\n}\r\n\r\n.dx-portlet-collapsed .portlet-title {\r\n  border-bottom: none;\r\n  margin-bottom: 0px;\r\n}', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(9, 'SCRIPT_JS', 'Iestatījumā var norādīt JavaScript skriptu, kas tiks izpildīts visās portāla lapās. Skripts tiks ielādēts kā pēdējais no visiem JS skriptiem.\r\n\r\nNav jānorāda <script> tagi - tikai saturs.', 15, NULL, '$(document).ready(function() {\r\n  // do something after page is loaded\r\n  // below are commented examples - you can delete comments and add needed logic\r\n  \r\n  // use jQuery here to affect pages style and behavior\r\n  // for example change elements class\r\n  \r\n  // $(''.ibox-tools'').addClass(''tools'').removeClass(''ibox-tools'');\r\n  \r\n  // you can applay different logic for each page depending on URL\r\n  // for example applay special style for latvenergo page\r\n  \r\n  /*\r\n  if (window.location.pathname == "/latvenergo")\r\n  {\r\n  	// do something on latvenergo page \r\n  }\r\n  */\r\n \r\n  \r\n});', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(10, 'EMTY_VIDEO_AVATAR', 'Attēls, kas tiks parādīts gadījumos, ja video datnei nav pievienots raksturojošais attēls', 12, NULL, NULL, 0, NULL, 'fons_peleks.png', 'abdc5615-fbaf-4a98-b894-a498b8b15ac2.png', 0, NULL, NULL, NULL, NULL),
(11, 'EMTY_PDF_AVATAR', 'Attēls, kas tiks parādīts gadījumos, ja ierakstam ar PDF datni nav pievienots raksturojošais attēls', 12, NULL, NULL, 0, NULL, 'pdflogo.png', '7240be9f-12ae-473d-b7a6-f78df1d0d377.png', 0, NULL, NULL, NULL, NULL),
(12, 'CHART_ELEMENTS_COLORS', 'Grafiku stabiņu krāsas', 1, '''#4b79bc'',''#5c7cab'',''#456086'',''#385e93'',''#265ba5'',''#1859b3'',\r\n                        ''#0b57c0'',\r\n                        ''#889dbb'',\r\n                        ''#739ad0'',\r\n                        ''#5e96e5'',\r\n                        ''#5094f3'',\r\n                        ''#4492ff'',\r\n                        ''#5094f3'',\r\n                        ''#086fff'',\r\n                        ''#2573e2'',\r\n                        ''#3976ce'',', NULL, 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(13, 'CHART_SECTIONS_COLORS', 'Diagrammu elementu krāsas. Ja diagramma būs norādīts vairāk krāsu kā šeit, tad pārējās krāsas tiks ģenerētas automātiski. Krāsas pieraksta formātā "''#ffffff'',''#111111'',..."', 1, '#4b79bc,#5c7cab,#456086,#385e93,#265ba5,#1859b3,#0b57c0,#889dbb,#739ad0,#5e96e5,#5094f3,#4492ff,#5094f3,#086fff,#2573e2,#3976ce', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(14, 'TOP_SLIDE_TRANSITION_TIME', 'TOP ziņu slīdrādes slaidu nomainīšanās intervāls milisekundēs. Lai norādītu, piemēram, 5 sekundes, jāievada 5000.', 5, NULL, NULL, 10000, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(15, 'CLICK2CALL_URL', 'Ceļš uz Click2Call vietni. Tālruņa numura parametrs vērība jānorāda #phone# - tā tiks aizvietota ar reālo tālruņa numuru.', 1, 'http://www.latvenergo.lv?phone=#phone#', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(16, 'CLICK2CALL_INNER_PHONE', 'Organizācijas iekšējā tālruņa numura sākuma daļa. Tiks izmantots, lai pievienotu 4 zīmju numuriem.', 1, '123', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL),
(17, 'USER_MANUAL_INTRO', 'SVS MEDUS lietotāju rokasgrāmatas ievada daļas teksts HTML formātā. Rokasgrāmata tiek ģenerēta no SVS datu bāzes. Rokasgrāmata pieejama vietnē /structure/doc_manual', 15, NULL, '<p>SVS MEDUS visu datu ievade un konfigurācijas tiek veiktas reģistros. Katram reģistram ir definēts vismaz viens datu skatījums (kolonnu kopums) un datu lauku forma.</p>\r\n<p>Autorizētie SVS lietotāji var piekļūt tikai tiem reģistriem, uz kuriem ir norādītas tiesības. Tiesības (skatīt, labot, dzēst) tiek norādītas lomās. Katrs lietotājs var būt piesaistīts vienai vai vairākām lomām.</p>\r\n<p>Reģistriem var piekļūt trijos veidos:</p>\r\n<ul>\r\n  <li><b>No kreisās puses navigācijas</b> - ja reģistrs ir piesaistīts kādai navigācijas izvēlnei;</li>\r\n  <li><b>No cita reģistra formā iekļautās sadaļas</b> - reģistri var būt savā starpā saistīti, tādā gadījumā galvenā reģistrā datu formā var tikt veidotas sadaļas ar saistītajiem reģistriem. Lietotāji var redzēt tikai tās sadaļas, uz kuru reģistriem ir vismaz skatīšanās tiesības;</li>\r\n  <li><b>Ievadot Interneta pārlūkā reģistra skata adresi (URL)</b> - var, piemēram, izveidot portāla lapu, kura pieejama tikai administratoriem, un lapā var ievietot saites uz reģistru skatiem.</li>\r\n</ul>            \r\n<p>Šī rokasgrāmata tiek ģenerēta no SVS MEDUS datu bāzes. Tas nozīmē, ka rokasgrāmata vienmēr ir aktuāla un satur informāciju par pašreizējo SVS konfigurāciju. Rokasgrāmatas saturs arī tiek veidots ar SVS MEDUS atbilstošajiem reģistriem.</p>\r\n<p>Portletu veidošanas instrukcija pieejama PDF formātā <a href="img/portleti_manual.pdf" target="_blank">šeit</a>.</p>\r\n<p>Zemāk ir aprakstīti visi reģistri, norādot to pieejamību no kreisās puses navigācijas un/vai no saistītā reģistra datu formas. Reģistri ir sagrupēti pa funkcionalitāšu grupām.</p>', 0, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_data`
--

CREATE TABLE IF NOT EXISTS `dx_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `multi_list_id` int(11) NOT NULL COMMENT 'Reference to register',
  `varchar1` varchar(4000) DEFAULT NULL COMMENT 'Denormalised table field - can be any varchar as defined in register',
  `varchar2` varchar(4000) DEFAULT NULL COMMENT 'Denormalised table field - can be any varchar as defined in register',
  `varchar3` varchar(4000) DEFAULT NULL COMMENT 'Denormalised table field - can be any varchar as defined in register',
  `datetime1` datetime DEFAULT NULL COMMENT 'Denormalised table field - can be any datetime as defined in register',
  `datetime2` datetime DEFAULT NULL COMMENT 'Denormalised table field - can be any datetime as defined in register',
  `datetime3` datetime DEFAULT NULL COMMENT 'Denormalised table field - can be any datetime as defined in register',
  `rel_id1` int(11) DEFAULT NULL COMMENT 'Denormalised table field - can be any intiger which contains ID to related register (used for dropdowns) as defined in register',
  `rel_id2` int(11) DEFAULT NULL COMMENT 'Denormalised table field - can be any intiger which contains ID to related register (used for dropdowns) as defined in register',
  `rel_id3` int(11) DEFAULT NULL COMMENT 'Denormalised table field - can be any intiger which contains ID to related register (used for dropdowns) as defined in register',
  `num_1` int(11) DEFAULT NULL,
  `int2` int(11) DEFAULT NULL,
  `int3` int(11) DEFAULT NULL,
  `file_name1` varchar(2000) DEFAULT NULL COMMENT 'Denormalised table field - can be any fille name field as defined in register. File''s allways are stored in 2 fields - one used for original file name (uploaded) and 2nd is used for guid (saved in server''s file system)',
  `file_guid1` varchar(100) DEFAULT NULL COMMENT 'Denormalised table field - can be any fille guid field as defined in register. File''s allways are stored in 2 fields - one used for original file name (uploaded) and 2nd is used for guid (saved in server''s file system)',
  `file_name2` varchar(2000) DEFAULT NULL COMMENT 'Denormalised table field - can be any fille name field as defined in register. File''s allways are stored in 2 fields - one used for original file name (uploaded) and 2nd is used for guid (saved in server''s file system)',
  `file_guid2` varchar(100) DEFAULT NULL COMMENT 'Denormalised table field - can be any fille guid field as defined in register. File''s allways are stored in 2 fields - one used for original file name (uploaded) and 2nd is used for guid (saved in server''s file system)',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  `dx_item_status_id` int(11) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `list_id` (`multi_list_id`),
  KEY `rel_id1` (`rel_id1`),
  KEY `rel_id2` (`rel_id2`),
  KEY `rel_id3` (`rel_id3`),
  KEY `multi_list_id` (`multi_list_id`),
  KEY `dx_created_user_id` (`created_user_id`),
  KEY `dx_modified_user_id` (`modified_user_id`),
  KEY `dx_item_status_id` (`dx_item_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tabulā var uzturēt n dažādus reģistrus. SVS var būvēt 2 veidos - visu reģistru datus glabājot vienā tabulā, vai arī katram reģistram veidot atsevišķas tabulas.' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dx_db_events`
--

CREATE TABLE IF NOT EXISTS `dx_db_events` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type_id` int(10) unsigned DEFAULT NULL COMMENT 'Notikuma veids',
  `user_id` int(11) DEFAULT NULL COMMENT 'Lietotājs',
  `event_time` datetime DEFAULT NULL COMMENT 'Notikuma laiks',
  `list_id` int(11) DEFAULT NULL COMMENT 'Reģistrs',
  `item_id` int(10) unsigned DEFAULT NULL COMMENT 'Ieraksta ID',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dx_db_events_type_id_index` (`type_id`),
  KEY `dx_db_events_user_id_index` (`user_id`),
  KEY `dx_db_events_list_id_index` (`list_id`),
  KEY `dx_db_events_item_id_index` (`item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=473 ;

--
-- Dumping data for table `dx_db_events`
--

INSERT INTO `dx_db_events` (`id`, `type_id`, `user_id`, `event_time`, `list_id`, `item_id`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 1, 1, '2016-03-15 17:22:07', 208, 34, NULL, NULL, NULL, NULL),
(2, 2, 1, '2016-03-15 17:29:41', 60, 5, NULL, NULL, NULL, NULL),
(3, 1, 1, '2016-03-15 17:34:29', 208, 35, NULL, NULL, NULL, NULL),
(4, 1, 1, '2016-03-15 17:42:20', 208, 36, NULL, NULL, NULL, NULL),
(5, 1, 1, '2016-03-15 17:44:50', 208, 37, NULL, NULL, NULL, NULL),
(6, 1, 1, '2016-03-15 17:45:05', 189, 38, NULL, NULL, NULL, NULL),
(7, 1, 1, '2016-03-15 17:45:27', 60, 39, NULL, NULL, NULL, NULL),
(8, 2, 1, '2016-03-15 18:15:45', 60, 31, NULL, NULL, NULL, NULL),
(9, 2, 1, '2016-03-15 18:17:55', 60, 31, NULL, NULL, NULL, NULL),
(10, 2, 1, '2016-03-15 18:19:49', 60, 12, NULL, NULL, NULL, NULL),
(11, 2, 1, '2016-03-15 18:20:32', 60, 12, NULL, NULL, NULL, NULL),
(12, 2, 1, '2016-03-15 18:25:17', 60, 39, NULL, NULL, NULL, NULL),
(13, 3, 1, '2016-03-15 18:25:24', 60, 39, NULL, NULL, NULL, NULL),
(14, 3, 1, '2016-03-15 18:25:36', 60, 33, NULL, NULL, NULL, NULL),
(15, 3, 1, '2016-03-15 18:25:41', 60, 32, NULL, NULL, NULL, NULL),
(16, 3, 1, '2016-03-15 18:25:45', 60, 31, NULL, NULL, NULL, NULL),
(17, 2, 1, '2016-03-15 18:26:26', 60, 6, NULL, NULL, NULL, NULL),
(18, 3, 1, '2016-03-15 18:26:43', 232, 1, NULL, NULL, NULL, NULL),
(19, 3, 1, '2016-03-15 18:26:43', 232, 2, NULL, NULL, NULL, NULL),
(20, 3, 1, '2016-03-15 18:26:43', 232, 3, NULL, NULL, NULL, NULL),
(21, 3, 1, '2016-03-15 18:26:43', 232, 4, NULL, NULL, NULL, NULL),
(22, 3, 1, '2016-03-15 18:26:43', 232, 5, NULL, NULL, NULL, NULL),
(23, 3, 1, '2016-03-15 18:27:35', 191, 20, NULL, NULL, NULL, NULL),
(24, 2, 1, '2016-03-15 18:27:41', 189, 19, NULL, NULL, NULL, NULL),
(25, 3, 1, '2016-03-15 18:27:44', 189, 19, NULL, NULL, NULL, NULL),
(26, 3, 1, '2016-03-15 18:27:57', 191, 18, NULL, NULL, NULL, NULL),
(27, 3, 1, '2016-03-15 18:27:57', 191, 19, NULL, NULL, NULL, NULL),
(28, 3, 1, '2016-03-15 18:28:02', 189, 18, NULL, NULL, NULL, NULL),
(29, 3, 1, '2016-03-15 18:28:13', 191, 16, NULL, NULL, NULL, NULL),
(30, 3, 1, '2016-03-15 18:28:13', 191, 17, NULL, NULL, NULL, NULL),
(31, 3, 1, '2016-03-15 18:28:23', 189, 17, NULL, NULL, NULL, NULL),
(32, 3, 1, '2016-03-15 18:28:35', 191, 13, NULL, NULL, NULL, NULL),
(33, 3, 1, '2016-03-15 18:28:35', 191, 14, NULL, NULL, NULL, NULL),
(34, 3, 1, '2016-03-15 18:28:35', 191, 15, NULL, NULL, NULL, NULL),
(35, 3, 1, '2016-03-15 18:28:44', 189, 16, NULL, NULL, NULL, NULL),
(36, 3, 1, '2016-03-15 18:28:54', 189, 15, NULL, NULL, NULL, NULL),
(37, 3, 1, '2016-03-15 18:29:07', 191, 10, NULL, NULL, NULL, NULL),
(38, 3, 1, '2016-03-15 18:29:07', 191, 11, NULL, NULL, NULL, NULL),
(39, 3, 1, '2016-03-15 18:29:07', 191, 12, NULL, NULL, NULL, NULL),
(40, 3, 1, '2016-03-15 18:29:24', 189, 14, NULL, NULL, NULL, NULL),
(41, 3, 1, '2016-03-15 18:29:54', 189, 38, NULL, NULL, NULL, NULL),
(42, 3, 1, '2016-03-15 18:30:06', 191, 74, NULL, NULL, NULL, NULL),
(43, 3, 1, '2016-03-15 18:30:06', 191, 75, NULL, NULL, NULL, NULL),
(44, 3, 1, '2016-03-15 18:30:06', 191, 76, NULL, NULL, NULL, NULL),
(45, 3, 1, '2016-03-15 18:30:06', 191, 77, NULL, NULL, NULL, NULL),
(46, 3, 1, '2016-03-15 18:30:06', 191, 79, NULL, NULL, NULL, NULL),
(47, 3, 1, '2016-03-15 18:30:06', 191, 80, NULL, NULL, NULL, NULL),
(48, 3, 1, '2016-03-15 18:30:06', 191, 81, NULL, NULL, NULL, NULL),
(49, 3, 1, '2016-03-15 18:30:06', 191, 82, NULL, NULL, NULL, NULL),
(50, 3, 1, '2016-03-15 18:30:06', 191, 83, NULL, NULL, NULL, NULL),
(51, 3, 1, '2016-03-15 18:30:06', 191, 84, NULL, NULL, NULL, NULL),
(52, 3, 1, '2016-03-15 18:30:06', 191, 85, NULL, NULL, NULL, NULL),
(53, 3, 1, '2016-03-15 18:30:06', 191, 86, NULL, NULL, NULL, NULL),
(54, 3, 1, '2016-03-15 18:30:06', 191, 87, NULL, NULL, NULL, NULL),
(55, 3, 1, '2016-03-15 18:30:06', 191, 88, NULL, NULL, NULL, NULL),
(56, 3, 1, '2016-03-15 18:30:06', 191, 89, NULL, NULL, NULL, NULL),
(57, 3, 1, '2016-03-15 18:30:06', 191, 90, NULL, NULL, NULL, NULL),
(58, 3, 1, '2016-03-15 18:30:06', 191, 91, NULL, NULL, NULL, NULL),
(59, 3, 1, '2016-03-15 18:30:06', 191, 92, NULL, NULL, NULL, NULL),
(60, 3, 1, '2016-03-15 18:30:06', 191, 93, NULL, NULL, NULL, NULL),
(61, 3, 1, '2016-03-15 18:30:06', 191, 94, NULL, NULL, NULL, NULL),
(62, 3, 1, '2016-03-15 18:30:12', 191, 95, NULL, NULL, NULL, NULL),
(63, 3, 1, '2016-03-15 18:30:12', 191, 96, NULL, NULL, NULL, NULL),
(64, 3, 1, '2016-03-15 18:30:12', 191, 97, NULL, NULL, NULL, NULL),
(65, 3, 1, '2016-03-15 18:30:18', 189, 30, NULL, NULL, NULL, NULL),
(66, 3, 1, '2016-03-15 18:30:53', 208, 37, NULL, NULL, NULL, NULL),
(67, 3, 1, '2016-03-15 18:31:01', 208, 36, NULL, NULL, NULL, NULL),
(68, 3, 1, '2016-03-15 18:31:19', 208, 35, NULL, NULL, NULL, NULL),
(69, 1, 1, '2016-03-15 18:32:49', 16, 71, NULL, NULL, NULL, NULL),
(70, 1, 1, '2016-03-15 18:34:33', 6, 247, NULL, NULL, NULL, NULL),
(71, 1, 1, '2016-03-15 18:34:33', 11, 1247, NULL, NULL, NULL, NULL),
(72, 1, 1, '2016-03-15 18:34:33', 11, 1248, NULL, NULL, NULL, NULL),
(73, 1, 1, '2016-03-15 18:34:33', 11, 1249, NULL, NULL, NULL, NULL),
(74, 1, 1, '2016-03-15 18:34:33', 11, 1250, NULL, NULL, NULL, NULL),
(75, 1, 1, '2016-03-15 18:34:33', 11, 1251, NULL, NULL, NULL, NULL),
(76, 1, 1, '2016-03-15 18:34:33', 11, 1252, NULL, NULL, NULL, NULL),
(77, 1, 1, '2016-03-15 18:34:33', 11, 1253, NULL, NULL, NULL, NULL),
(78, 1, 1, '2016-03-15 18:34:33', 11, 1254, NULL, NULL, NULL, NULL),
(79, 2, 1, '2016-03-15 18:34:51', 6, 247, NULL, NULL, NULL, NULL),
(80, 3, 1, '2016-03-15 18:35:21', 11, 1254, NULL, NULL, NULL, NULL),
(81, 3, 1, '2016-03-15 18:35:21', 11, 1249, NULL, NULL, NULL, NULL),
(82, 3, 1, '2016-03-15 18:35:21', 11, 1252, NULL, NULL, NULL, NULL),
(83, 3, 1, '2016-03-15 18:35:21', 11, 1253, NULL, NULL, NULL, NULL),
(84, 1, 1, '2016-03-15 18:35:43', 11, 1255, NULL, NULL, NULL, NULL),
(85, 1, 1, '2016-03-15 18:36:04', 11, 1256, NULL, NULL, NULL, NULL),
(86, 2, 1, '2016-03-15 18:36:35', 6, 66, NULL, NULL, NULL, NULL),
(87, 1, 1, '2016-03-15 18:38:36', 16, 72, NULL, NULL, NULL, NULL),
(88, 1, 1, '2016-03-15 18:42:25', 11, 1257, NULL, NULL, NULL, NULL),
(89, 2, 1, '2016-03-15 18:43:16', 11, 1257, NULL, NULL, NULL, NULL),
(90, 2, 1, '2016-03-15 18:47:56', 60, 5, NULL, NULL, NULL, NULL),
(91, 3, 1, '2016-03-15 18:48:24', 208, 34, NULL, NULL, NULL, NULL),
(92, 2, 1, '2016-03-15 19:15:22', 7, 1175, NULL, NULL, NULL, NULL),
(93, 2, 1, '2016-03-15 19:16:06', 7, 1289, NULL, NULL, NULL, NULL),
(94, 2, 1, '2016-03-15 20:00:20', 7, 289, NULL, NULL, NULL, NULL),
(95, 2, 1, '2016-03-15 20:04:14', 60, 5, NULL, NULL, NULL, NULL),
(96, 2, 1, '2016-03-15 20:05:28', 60, 5, NULL, NULL, NULL, NULL),
(97, 1, 1, '2016-03-16 13:23:37', 60, 40, NULL, NULL, NULL, NULL),
(98, 1, 1, '2016-03-16 13:25:04', 126, 36, NULL, NULL, NULL, NULL),
(99, 2, 1, '2016-03-16 13:27:57', 60, 40, NULL, NULL, NULL, NULL),
(100, 1, 1, '2016-03-16 13:31:06', 60, 41, NULL, NULL, NULL, NULL),
(101, 2, 1, '2016-03-16 13:32:03', 60, 22, NULL, NULL, NULL, NULL),
(102, 1, 1, '2016-03-16 13:44:22', 189, 42, NULL, NULL, NULL, NULL),
(103, 1, 1, '2016-03-16 13:46:54', 191, 211, NULL, NULL, NULL, NULL),
(104, 1, 1, '2016-03-16 13:46:54', 191, 212, NULL, NULL, NULL, NULL),
(105, 1, 1, '2016-03-16 13:46:54', 191, 213, NULL, NULL, NULL, NULL),
(106, 1, 1, '2016-03-16 13:46:54', 191, 214, NULL, NULL, NULL, NULL),
(107, 1, 1, '2016-03-16 13:46:54', 191, 215, NULL, NULL, NULL, NULL),
(108, 1, 1, '2016-03-16 13:46:54', 191, 216, NULL, NULL, NULL, NULL),
(109, 1, 1, '2016-03-16 13:46:54', 191, 217, NULL, NULL, NULL, NULL),
(110, 1, 1, '2016-03-16 13:46:54', 191, 218, NULL, NULL, NULL, NULL),
(111, 1, 1, '2016-03-16 13:46:54', 191, 219, NULL, NULL, NULL, NULL),
(112, 1, 1, '2016-03-16 13:46:54', 191, 220, NULL, NULL, NULL, NULL),
(113, 1, 1, '2016-03-16 13:46:54', 191, 221, NULL, NULL, NULL, NULL),
(114, 1, 1, '2016-03-16 13:46:54', 191, 222, NULL, NULL, NULL, NULL),
(115, 1, 1, '2016-03-16 13:46:54', 191, 223, NULL, NULL, NULL, NULL),
(116, 1, 1, '2016-03-16 13:46:54', 191, 224, NULL, NULL, NULL, NULL),
(117, 1, 1, '2016-03-16 13:46:54', 191, 225, NULL, NULL, NULL, NULL),
(118, 1, 1, '2016-03-16 13:46:54', 191, 226, NULL, NULL, NULL, NULL),
(119, 1, 1, '2016-03-16 13:46:54', 191, 227, NULL, NULL, NULL, NULL),
(120, 1, 1, '2016-03-16 13:46:54', 191, 228, NULL, NULL, NULL, NULL),
(121, 1, 1, '2016-03-16 13:46:54', 191, 229, NULL, NULL, NULL, NULL),
(122, 1, 1, '2016-03-16 13:46:54', 191, 230, NULL, NULL, NULL, NULL),
(123, 1, 1, '2016-03-16 13:47:31', 191, 231, NULL, NULL, NULL, NULL),
(124, 1, 1, '2016-03-16 13:47:31', 191, 232, NULL, NULL, NULL, NULL),
(125, 1, 1, '2016-03-16 13:47:31', 191, 233, NULL, NULL, NULL, NULL),
(126, 1, 1, '2016-03-16 13:47:31', 191, 234, NULL, NULL, NULL, NULL),
(127, 1, 1, '2016-03-16 13:47:31', 191, 235, NULL, NULL, NULL, NULL),
(128, 1, 1, '2016-03-16 13:47:31', 191, 236, NULL, NULL, NULL, NULL),
(129, 1, 1, '2016-03-16 13:47:31', 191, 237, NULL, NULL, NULL, NULL),
(130, 2, 1, '2016-03-16 13:48:58', 191, 231, NULL, NULL, NULL, NULL),
(131, 1, 1, '2016-03-16 13:50:17', 60, 43, NULL, NULL, NULL, NULL),
(132, 2, 1, '2016-03-16 13:50:36', 60, 43, NULL, NULL, NULL, NULL),
(133, 2, 1, '2016-03-16 13:52:40', 60, 43, NULL, NULL, NULL, NULL),
(134, 1, 1, '2016-03-16 14:12:00', 189, 44, NULL, NULL, NULL, NULL),
(135, 1, 1, '2016-03-16 14:13:21', 191, 238, NULL, NULL, NULL, NULL),
(136, 1, 1, '2016-03-16 14:13:21', 191, 239, NULL, NULL, NULL, NULL),
(137, 1, 1, '2016-03-16 14:13:21', 191, 240, NULL, NULL, NULL, NULL),
(138, 3, 1, '2016-03-16 14:15:18', 60, 43, NULL, NULL, NULL, NULL),
(139, 2, 1, '2016-03-16 14:17:52', 100, 14, NULL, NULL, NULL, NULL),
(140, 1, 1, '2016-03-16 14:19:05', 93, 1, NULL, NULL, NULL, NULL),
(141, 2, 1, '2016-03-16 14:23:17', 23, 159, NULL, NULL, NULL, NULL),
(142, 1, 1, '2016-03-16 18:21:29', 232, 6, NULL, NULL, NULL, NULL),
(143, 1, 1, '2016-03-16 18:21:29', 232, 7, NULL, NULL, NULL, NULL),
(144, 2, 1, '2016-03-16 18:22:11', 60, 5, NULL, NULL, NULL, NULL),
(145, 2, 1, '2016-03-16 18:23:28', 60, 5, NULL, NULL, NULL, NULL),
(146, 1, 1, '2016-03-16 18:48:08', 232, 8, NULL, NULL, NULL, NULL),
(147, 2, 1, '2016-03-16 18:48:23', 60, 5, NULL, NULL, NULL, NULL),
(148, 2, 1, '2016-03-16 19:28:13', 191, 208, NULL, NULL, NULL, NULL),
(149, 2, 1, '2016-03-16 19:32:14', 191, 205, NULL, NULL, NULL, NULL),
(150, 2, 1, '2016-03-17 15:08:59', 60, 5, NULL, NULL, NULL, NULL),
(151, 2, 1, '2016-03-17 15:49:15', 191, 205, NULL, NULL, NULL, NULL),
(152, 2, 1, '2016-03-17 15:49:37', 191, 205, NULL, NULL, NULL, NULL),
(153, 2, 1, '2016-03-17 15:50:12', 191, 208, NULL, NULL, NULL, NULL),
(154, 1, 1, '2016-03-17 15:50:51', 191, 241, NULL, NULL, NULL, NULL),
(155, 1, 1, '2016-03-17 15:53:21', 60, 45, NULL, NULL, NULL, NULL),
(156, 2, 1, '2016-03-17 15:53:32', 60, 45, NULL, NULL, NULL, NULL),
(157, 2, 1, '2016-03-17 15:53:50', 60, 45, NULL, NULL, NULL, NULL),
(158, 1, 1, '2016-03-17 16:14:51', 208, 46, NULL, NULL, NULL, NULL),
(159, 2, 1, '2016-03-17 16:14:58', 60, 5, NULL, NULL, NULL, NULL),
(160, 1, 1, '2016-03-17 17:38:23', 191, 242, NULL, NULL, NULL, NULL),
(161, 3, 1, '2016-03-17 17:39:48', 191, 242, NULL, NULL, NULL, NULL),
(162, 2, 1, '2016-03-17 17:50:02', 189, 3, NULL, NULL, NULL, NULL),
(163, 1, 1, '2016-03-18 09:40:46', 191, 242, NULL, NULL, NULL, NULL),
(164, 1, 1, '2016-03-18 09:41:12', 191, 243, NULL, NULL, NULL, NULL),
(165, 2, 1, '2016-03-18 09:41:34', 191, 243, NULL, NULL, NULL, NULL),
(166, 3, 1, '2016-03-18 09:44:19', 191, 205, NULL, NULL, NULL, NULL),
(167, 3, 1, '2016-03-18 09:44:19', 191, 208, NULL, NULL, NULL, NULL),
(168, 3, 1, '2016-03-18 09:44:19', 191, 209, NULL, NULL, NULL, NULL),
(169, 3, 1, '2016-03-18 09:44:19', 191, 210, NULL, NULL, NULL, NULL),
(170, 3, 1, '2016-03-18 09:44:19', 191, 241, NULL, NULL, NULL, NULL),
(171, 3, 1, '2016-03-18 09:44:19', 191, 242, NULL, NULL, NULL, NULL),
(172, 3, 1, '2016-03-18 09:44:19', 191, 243, NULL, NULL, NULL, NULL),
(173, 1, 1, '2016-03-18 09:44:27', 191, 244, NULL, NULL, NULL, NULL),
(174, 1, 1, '2016-03-18 09:44:27', 191, 245, NULL, NULL, NULL, NULL),
(175, 3, 1, '2016-03-18 09:44:34', 191, 244, NULL, NULL, NULL, NULL),
(176, 3, 1, '2016-03-18 09:44:38', 191, 245, NULL, NULL, NULL, NULL),
(177, 1, 1, '2016-03-18 10:00:11', 191, 246, NULL, NULL, NULL, NULL),
(178, 3, 1, '2016-03-18 10:00:15', 191, 246, NULL, NULL, NULL, NULL),
(179, 2, 1, '2016-03-18 10:05:32', 189, 3, NULL, NULL, NULL, NULL),
(180, 2, 1, '2016-03-18 10:05:38', 189, 3, NULL, NULL, NULL, NULL),
(181, 2, 1, '2016-03-18 10:05:50', 189, 3, NULL, NULL, NULL, NULL),
(182, 1, 1, '2016-03-18 10:06:17', 60, 47, NULL, NULL, NULL, NULL),
(183, 2, 1, '2016-03-18 10:06:28', 60, 47, NULL, NULL, NULL, NULL),
(184, 2, 1, '2016-03-18 17:25:03', 60, 5, NULL, NULL, NULL, NULL),
(185, 2, 1, '2016-03-18 17:27:24', 60, 5, NULL, NULL, NULL, NULL),
(186, 2, 1, '2016-03-18 17:28:04', 60, 5, NULL, NULL, NULL, NULL),
(187, 2, 1, '2016-03-18 17:29:57', 7, 1192, NULL, NULL, NULL, NULL),
(188, 2, 1, '2016-03-18 17:30:18', 7, 1270, NULL, NULL, NULL, NULL),
(189, 2, 1, '2016-03-18 17:30:40', 7, 290, NULL, NULL, NULL, NULL),
(190, 1, 1, '2016-03-18 17:37:14', 7, 1415, NULL, NULL, NULL, NULL),
(191, 2, 1, '2016-03-18 17:37:38', 7, 1415, NULL, NULL, NULL, NULL),
(192, 1, 1, '2016-03-18 17:37:55', 9, 1176, NULL, NULL, NULL, NULL),
(193, 2, 1, '2016-03-18 17:39:00', 189, 44, NULL, NULL, NULL, NULL),
(194, 2, 1, '2016-03-18 17:40:28', 60, 5, NULL, NULL, NULL, NULL),
(195, 3, 1, '2016-03-18 17:46:57', 9, 1176, NULL, NULL, NULL, NULL),
(196, 3, 1, '2016-03-18 17:47:11', 7, 1415, NULL, NULL, NULL, NULL),
(197, 2, 1, '2016-03-18 17:55:17', 11, 295, NULL, NULL, NULL, NULL),
(198, 2, 1, '2016-03-18 17:56:09', 11, 1015, NULL, NULL, NULL, NULL),
(199, 2, 1, '2016-03-18 17:56:36', 11, 1115, NULL, NULL, NULL, NULL),
(200, 2, 1, '2016-03-18 18:27:53', 60, 47, NULL, NULL, NULL, NULL),
(201, 2, 1, '2016-03-18 18:31:43', 60, 47, NULL, NULL, NULL, NULL),
(202, 2, 1, '2016-03-18 18:45:34', 60, 47, NULL, NULL, NULL, NULL),
(203, 2, 1, '2016-03-18 18:49:03', 7, 1364, NULL, NULL, NULL, NULL),
(204, 2, 1, '2016-03-18 19:11:51', 60, 29, NULL, NULL, NULL, NULL),
(205, 2, 1, '2016-03-19 11:53:31', 3, 234, NULL, NULL, NULL, NULL),
(206, 2, 1, '2016-03-19 11:54:15', 6, 250, NULL, NULL, NULL, NULL),
(207, 1, 1, '2016-03-19 11:55:35', 23, 211, NULL, NULL, NULL, NULL),
(208, 2, 1, '2016-03-19 11:56:58', 16, 71, NULL, NULL, NULL, NULL),
(209, 2, 1, '2016-03-19 11:57:28', 16, 72, NULL, NULL, NULL, NULL),
(210, 1, 1, '2016-03-19 12:28:16', 7, 1434, NULL, NULL, NULL, NULL),
(211, 1, 1, '2016-03-19 12:29:33', 9, 1195, NULL, NULL, NULL, NULL),
(212, 1, 1, '2016-03-19 12:34:36', 21, 18, NULL, NULL, NULL, NULL),
(213, 1, 1, '2016-03-19 12:34:44', 22, 41, NULL, NULL, NULL, NULL),
(214, 2, 18, '2016-03-19 14:44:33', 60, 29, NULL, NULL, NULL, NULL),
(215, 1, 1, '2016-03-20 08:23:12', 60, 48, NULL, NULL, NULL, NULL),
(216, 2, 18, '2016-03-20 08:52:20', 60, 41, NULL, NULL, NULL, NULL),
(217, 2, 18, '2016-03-20 08:53:07', 60, 41, NULL, NULL, NULL, NULL),
(218, 2, 18, '2016-03-20 08:56:31', 60, 41, NULL, NULL, NULL, NULL),
(219, 1, 18, '2016-03-20 08:56:45', 126, 37, NULL, NULL, NULL, NULL),
(220, 1, 1, '2016-03-20 09:30:59', 20, 24, NULL, NULL, NULL, NULL),
(221, 1, 1, '2016-03-20 09:31:10', 22, 42, NULL, NULL, NULL, NULL),
(222, 1, 1, '2016-03-20 09:31:29', 23, 212, NULL, NULL, NULL, NULL),
(223, 1, 1, '2016-03-20 09:31:40', 23, 213, NULL, NULL, NULL, NULL),
(224, 1, 1, '2016-03-20 09:31:52', 23, 214, NULL, NULL, NULL, NULL),
(225, 1, 1, '2016-03-20 09:32:05', 23, 215, NULL, NULL, NULL, NULL),
(226, 1, 1, '2016-03-20 09:32:36', 23, 216, NULL, NULL, NULL, NULL),
(227, 1, 1, '2016-03-20 09:33:02', 23, 217, NULL, NULL, NULL, NULL),
(228, 1, 1, '2016-03-20 09:33:20', 23, 218, NULL, NULL, NULL, NULL),
(229, 1, 1, '2016-03-20 09:34:03', 23, 219, NULL, NULL, NULL, NULL),
(230, 1, 1, '2016-03-20 09:34:38', 23, 220, NULL, NULL, NULL, NULL),
(231, 1, 1, '2016-03-20 09:35:20', 23, 221, NULL, NULL, NULL, NULL),
(232, 3, 1, '2016-03-20 09:35:53', 22, 41, NULL, NULL, NULL, NULL),
(233, 1, 18, '2016-03-20 09:39:03', 60, 49, NULL, NULL, NULL, NULL),
(234, 1, 18, '2016-03-20 09:39:24', 232, 9, NULL, NULL, NULL, NULL),
(235, 2, 1, '2016-03-20 09:43:27', 3, 60, NULL, NULL, NULL, NULL),
(236, 2, 1, '2016-03-20 09:43:41', 6, 66, NULL, NULL, NULL, NULL),
(237, 2, 1, '2016-03-20 09:43:53', 3, 189, NULL, NULL, NULL, NULL),
(238, 2, 1, '2016-03-20 09:44:06', 6, 197, NULL, NULL, NULL, NULL),
(239, 2, 1, '2016-03-20 09:44:54', 3, 208, NULL, NULL, NULL, NULL),
(240, 2, 1, '2016-03-20 09:45:30', 3, 234, NULL, NULL, NULL, NULL),
(241, 2, 1, '2016-03-20 09:45:47', 6, 250, NULL, NULL, NULL, NULL),
(242, 2, 1, '2016-03-20 09:46:08', 6, 217, NULL, NULL, NULL, NULL),
(243, 2, 1, '2016-03-20 09:51:23', 3, 194, NULL, NULL, NULL, NULL),
(244, 2, 1, '2016-03-20 09:51:51', 6, 203, NULL, NULL, NULL, NULL),
(245, 2, 1, '2016-03-20 09:53:27', 3, 235, NULL, NULL, NULL, NULL),
(246, 1, 1, '2016-03-20 09:53:47', 23, 222, NULL, NULL, NULL, NULL),
(247, 1, 1, '2016-03-20 09:54:42', 5, 218, NULL, NULL, NULL, NULL),
(248, 3, 1, '2016-03-20 09:55:06', 23, 184, NULL, NULL, NULL, NULL),
(249, 2, 1, '2016-03-20 09:56:57', 5, 126, NULL, NULL, NULL, NULL),
(250, 2, 1, '2016-03-20 09:57:29', 5, 150, NULL, NULL, NULL, NULL),
(251, 2, 1, '2016-03-20 09:58:02', 5, 180, NULL, NULL, NULL, NULL),
(252, 2, 1, '2016-03-20 10:00:56', 3, 100, NULL, NULL, NULL, NULL),
(253, 2, 1, '2016-03-20 10:01:51', 5, 16, NULL, NULL, NULL, NULL),
(254, 2, 1, '2016-03-20 10:02:06', 5, 188, NULL, NULL, NULL, NULL),
(255, 2, 1, '2016-03-20 10:02:26', 5, 126, NULL, NULL, NULL, NULL),
(256, 2, 1, '2016-03-20 10:07:49', 7, 1444, NULL, NULL, NULL, NULL),
(257, 2, 1, '2016-03-20 10:09:42', 9, 1002, NULL, NULL, NULL, NULL),
(258, 2, 1, '2016-03-20 10:09:49', 9, 999, NULL, NULL, NULL, NULL),
(259, 3, 1, '2016-03-20 10:10:04', 9, 1005, NULL, NULL, NULL, NULL),
(260, 2, 1, '2016-03-20 10:10:42', 9, 1001, NULL, NULL, NULL, NULL),
(261, 3, 1, '2016-03-20 10:10:49', 9, 1004, NULL, NULL, NULL, NULL),
(262, 3, 1, '2016-03-20 10:10:53', 9, 1003, NULL, NULL, NULL, NULL),
(263, 1, 1, '2016-03-20 10:12:27', 9, 1205, NULL, NULL, NULL, NULL),
(264, 1, 1, '2016-03-20 10:38:12', 22, 43, NULL, NULL, NULL, NULL),
(265, 1, 18, '2016-03-20 10:47:40', 208, 50, NULL, NULL, NULL, NULL),
(266, 1, 18, '2016-03-20 10:49:05', 60, 51, NULL, NULL, NULL, NULL),
(267, 3, 1, '2016-03-20 14:42:27', 5, 159, NULL, NULL, NULL, NULL),
(268, 3, 1, '2016-03-20 14:42:43', 5, 163, NULL, NULL, NULL, NULL),
(269, 3, 1, '2016-03-20 14:42:50', 5, 167, NULL, NULL, NULL, NULL),
(270, 2, 1, '2016-03-20 15:12:18', 3, 99, NULL, NULL, NULL, NULL),
(271, 2, 1, '2016-03-20 15:12:34', 3, 99, NULL, NULL, NULL, NULL),
(272, 2, 1, '2016-03-20 15:13:37', 11, 67, NULL, NULL, NULL, NULL),
(273, 3, 1, '2016-03-20 15:13:57', 11, 69, NULL, NULL, NULL, NULL),
(274, 1, 1, '2016-03-20 15:14:26', 11, 1287, NULL, NULL, NULL, NULL),
(275, 2, 1, '2016-03-20 15:14:35', 11, 68, NULL, NULL, NULL, NULL),
(276, 2, 1, '2016-03-20 15:27:48', 7, 427, NULL, NULL, NULL, NULL),
(277, 2, 1, '2016-03-20 15:27:57', 7, 428, NULL, NULL, NULL, NULL),
(278, 2, 1, '2016-03-20 15:28:18', 11, 77, NULL, NULL, NULL, NULL),
(279, 2, 1, '2016-03-20 15:28:30', 11, 79, NULL, NULL, NULL, NULL),
(280, 2, 1, '2016-03-20 15:28:46', 11, 80, NULL, NULL, NULL, NULL),
(281, 1, 1, '2016-03-20 15:29:06', 11, 1288, NULL, NULL, NULL, NULL),
(282, 1, 1, '2016-03-20 15:29:24', 11, 1289, NULL, NULL, NULL, NULL),
(283, 2, 1, '2016-03-20 15:32:14', 20, 23, NULL, NULL, NULL, NULL),
(284, 1, 1, '2016-03-20 15:34:04', 20, 25, NULL, NULL, NULL, NULL),
(285, 1, 1, '2016-03-20 15:34:58', 21, 19, NULL, NULL, NULL, NULL),
(286, 1, 1, '2016-03-20 15:35:03', 22, 44, NULL, NULL, NULL, NULL),
(287, 1, 1, '2016-03-20 15:36:10', 23, 223, NULL, NULL, NULL, NULL),
(288, 1, 1, '2016-03-20 15:36:27', 23, 224, NULL, NULL, NULL, NULL),
(289, 1, 1, '2016-03-20 15:36:49', 23, 225, NULL, NULL, NULL, NULL),
(290, 1, 1, '2016-03-20 15:37:05', 23, 226, NULL, NULL, NULL, NULL),
(291, 1, 1, '2016-03-20 15:37:17', 23, 227, NULL, NULL, NULL, NULL),
(292, 1, 1, '2016-03-20 15:37:30', 23, 228, NULL, NULL, NULL, NULL),
(293, 1, 1, '2016-03-20 15:37:43', 23, 229, NULL, NULL, NULL, NULL),
(294, 1, 1, '2016-03-20 15:38:00', 23, 230, NULL, NULL, NULL, NULL),
(295, 1, 1, '2016-03-20 15:38:23', 23, 231, NULL, NULL, NULL, NULL),
(296, 1, 1, '2016-03-20 15:38:45', 23, 232, NULL, NULL, NULL, NULL),
(297, 1, 1, '2016-03-20 15:38:56', 23, 233, NULL, NULL, NULL, NULL),
(298, 2, 1, '2016-03-20 15:39:05', 23, 230, NULL, NULL, NULL, NULL),
(299, 2, 1, '2016-03-20 15:39:14', 23, 225, NULL, NULL, NULL, NULL),
(300, 1, 1, '2016-03-20 15:39:42', 23, 234, NULL, NULL, NULL, NULL),
(301, 1, 1, '2016-03-20 15:40:00', 23, 235, NULL, NULL, NULL, NULL),
(302, 3, 1, '2016-03-20 15:43:12', 4, 91, NULL, NULL, NULL, NULL),
(303, 2, 1, '2016-03-20 15:43:32', 3, 227, NULL, NULL, NULL, NULL),
(304, 1, 1, '2016-03-20 15:44:05', 23, 236, NULL, NULL, NULL, NULL),
(305, 2, 1, '2016-03-20 15:46:45', 23, 227, NULL, NULL, NULL, NULL),
(306, 2, 1, '2016-03-20 15:47:39', 9, 987, NULL, NULL, NULL, NULL),
(307, 2, 1, '2016-03-20 15:48:09', 9, 989, NULL, NULL, NULL, NULL),
(308, 2, 1, '2016-03-20 15:51:27', 11, 1151, NULL, NULL, NULL, NULL),
(309, 2, 1, '2016-03-20 15:52:28', 4, 103, NULL, NULL, NULL, NULL),
(310, 2, 19, '2016-03-20 15:52:49', 193, 2, NULL, NULL, NULL, NULL),
(311, 2, 19, '2016-03-20 15:57:01', 193, 2, NULL, NULL, NULL, NULL),
(312, 1, 1, '2016-03-20 16:01:25', 23, 237, NULL, NULL, NULL, NULL),
(313, 1, 1, '2016-03-20 16:01:48', 23, 238, NULL, NULL, NULL, NULL),
(314, 2, 1, '2016-03-20 16:02:56', 9, 946, NULL, NULL, NULL, NULL),
(315, 2, 1, '2016-03-20 16:07:25', 23, 12, NULL, NULL, NULL, NULL),
(316, 2, 1, '2016-03-20 16:07:58', 23, 185, NULL, NULL, NULL, NULL),
(317, 1, 1, '2016-03-20 16:20:27', 23, 239, NULL, NULL, NULL, NULL),
(318, 1, 1, '2016-03-20 16:20:50', 23, 240, NULL, NULL, NULL, NULL),
(319, 1, 1, '2016-03-20 16:21:27', 23, 241, NULL, NULL, NULL, NULL),
(320, 2, 1, '2016-03-20 16:21:38', 23, 185, NULL, NULL, NULL, NULL),
(321, 2, 1, '2016-03-20 16:22:48', 23, 12, NULL, NULL, NULL, NULL),
(322, 1, 1, '2016-03-20 16:23:17', 22, 45, NULL, NULL, NULL, NULL),
(323, 1, 1, '2016-03-20 16:24:01', 23, 242, NULL, NULL, NULL, NULL),
(324, 2, 1, '2016-03-20 16:30:41', 6, 23, NULL, NULL, NULL, NULL),
(325, 2, 1, '2016-03-20 16:32:52', 20, 2, NULL, NULL, NULL, NULL),
(326, 3, 19, '2016-03-20 16:35:21', 101, 30, NULL, NULL, NULL, NULL),
(327, 3, 19, '2016-03-20 16:35:37', 101, 6, NULL, NULL, NULL, NULL),
(328, 3, 19, '2016-03-20 16:35:38', 101, 14, NULL, NULL, NULL, NULL),
(329, 3, 19, '2016-03-20 16:35:38', 101, 11, NULL, NULL, NULL, NULL),
(330, 3, 19, '2016-03-20 16:35:38', 101, 22, NULL, NULL, NULL, NULL),
(331, 3, 19, '2016-03-20 16:35:38', 101, 24, NULL, NULL, NULL, NULL),
(332, 3, 19, '2016-03-20 16:35:38', 101, 16, NULL, NULL, NULL, NULL),
(333, 3, 19, '2016-03-20 16:35:38', 101, 18, NULL, NULL, NULL, NULL),
(334, 3, 19, '2016-03-20 16:35:38', 101, 26, NULL, NULL, NULL, NULL),
(335, 3, 19, '2016-03-20 16:35:38', 101, 20, NULL, NULL, NULL, NULL),
(336, 3, 19, '2016-03-20 16:35:38', 101, 28, NULL, NULL, NULL, NULL),
(337, 3, 19, '2016-03-20 16:35:38', 101, 32, NULL, NULL, NULL, NULL),
(338, 3, 19, '2016-03-20 16:35:38', 101, 34, NULL, NULL, NULL, NULL),
(339, 3, 19, '2016-03-20 16:35:38', 101, 36, NULL, NULL, NULL, NULL),
(340, 2, 1, '2016-03-20 16:38:43', 6, 23, NULL, NULL, NULL, NULL),
(341, 2, 1, '2016-03-20 16:40:57', 6, 23, NULL, NULL, NULL, NULL),
(342, 2, 1, '2016-03-20 16:43:44', 6, 23, NULL, NULL, NULL, NULL),
(343, 1, 1, '2016-03-20 16:45:33', 22, 46, NULL, NULL, NULL, NULL),
(346, 2, 1, '2016-03-20 16:56:54', 6, 22, NULL, NULL, NULL, NULL),
(347, 2, 1, '2016-03-20 16:57:59', 6, 22, NULL, NULL, NULL, NULL),
(348, 1, 1, '2016-03-20 16:58:57', 11, 1290, NULL, NULL, NULL, NULL),
(349, 1, 1, '2016-03-20 16:59:11', 11, 1291, NULL, NULL, NULL, NULL),
(350, 1, 1, '2016-03-20 16:59:29', 11, 1292, NULL, NULL, NULL, NULL),
(351, 1, 19, '2016-03-20 17:23:01', 22, 48, NULL, NULL, NULL, NULL),
(353, 1, 1, '2016-03-20 17:24:19', 232, 10, NULL, NULL, NULL, NULL),
(354, 3, 1, '2016-03-20 17:24:32', 232, 10, NULL, NULL, NULL, NULL),
(355, 3, 1, '2016-03-20 17:25:25', 195, 6, NULL, NULL, NULL, NULL),
(356, 3, 1, '2016-03-20 17:25:25', 195, 7, NULL, NULL, NULL, NULL),
(357, 3, 1, '2016-03-20 17:25:25', 195, 8, NULL, NULL, NULL, NULL),
(358, 3, 1, '2016-03-20 17:25:25', 195, 9, NULL, NULL, NULL, NULL),
(359, 3, 1, '2016-03-20 17:25:25', 195, 10, NULL, NULL, NULL, NULL),
(360, 3, 1, '2016-03-20 17:25:25', 195, 11, NULL, NULL, NULL, NULL),
(361, 3, 1, '2016-03-20 17:25:25', 195, 12, NULL, NULL, NULL, NULL),
(362, 3, 1, '2016-03-20 17:25:25', 195, 13, NULL, NULL, NULL, NULL),
(363, 3, 1, '2016-03-20 17:25:25', 195, 14, NULL, NULL, NULL, NULL),
(364, 3, 1, '2016-03-20 17:25:25', 195, 15, NULL, NULL, NULL, NULL),
(365, 3, 1, '2016-03-20 17:25:25', 195, 16, NULL, NULL, NULL, NULL),
(366, 3, 1, '2016-03-20 17:25:25', 195, 17, NULL, NULL, NULL, NULL),
(367, 3, 1, '2016-03-20 17:25:25', 195, 18, NULL, NULL, NULL, NULL),
(368, 3, 1, '2016-03-20 17:25:25', 195, 19, NULL, NULL, NULL, NULL),
(369, 3, 1, '2016-03-20 17:25:25', 195, 20, NULL, NULL, NULL, NULL),
(370, 3, 1, '2016-03-20 17:25:25', 195, 21, NULL, NULL, NULL, NULL),
(371, 3, 1, '2016-03-20 17:25:25', 195, 22, NULL, NULL, NULL, NULL),
(372, 3, 1, '2016-03-20 17:25:25', 195, 23, NULL, NULL, NULL, NULL),
(373, 3, 1, '2016-03-20 17:25:25', 195, 24, NULL, NULL, NULL, NULL),
(374, 3, 1, '2016-03-20 17:25:25', 195, 25, NULL, NULL, NULL, NULL),
(375, 3, 1, '2016-03-20 17:25:31', 195, 26, NULL, NULL, NULL, NULL),
(376, 3, 1, '2016-03-20 17:25:31', 195, 27, NULL, NULL, NULL, NULL),
(377, 3, 1, '2016-03-20 17:25:52', 93, 1, NULL, NULL, NULL, NULL),
(378, 3, 1, '2016-03-20 17:27:02', 214, 1, NULL, NULL, NULL, NULL),
(379, 3, 1, '2016-03-20 17:27:02', 214, 2, NULL, NULL, NULL, NULL),
(380, 3, 1, '2016-03-20 17:27:02', 214, 3, NULL, NULL, NULL, NULL),
(381, 3, 1, '2016-03-20 17:27:23', 212, 1, NULL, NULL, NULL, NULL),
(382, 3, 1, '2016-03-20 17:27:23', 212, 2, NULL, NULL, NULL, NULL),
(383, 3, 1, '2016-03-20 17:27:23', 212, 3, NULL, NULL, NULL, NULL),
(384, 3, 1, '2016-03-20 17:27:23', 212, 4, NULL, NULL, NULL, NULL),
(385, 3, 1, '2016-03-20 17:28:54', 227, 1, NULL, NULL, NULL, NULL),
(386, 3, 1, '2016-03-20 17:28:54', 227, 2, NULL, NULL, NULL, NULL),
(387, 1, 1, '2016-03-20 17:29:31', 127, 58, NULL, NULL, NULL, NULL),
(388, 3, 1, '2016-03-20 17:32:08', 21, 9, NULL, NULL, NULL, NULL),
(389, 3, 1, '2016-03-20 17:32:22', 21, 10, NULL, NULL, NULL, NULL),
(390, 3, 1, '2016-03-20 17:32:22', 21, 11, NULL, NULL, NULL, NULL),
(391, 3, 1, '2016-03-20 17:32:22', 21, 12, NULL, NULL, NULL, NULL),
(392, 3, 1, '2016-03-20 17:32:22', 21, 13, NULL, NULL, NULL, NULL),
(393, 3, 1, '2016-03-20 17:32:22', 21, 14, NULL, NULL, NULL, NULL),
(394, 3, 1, '2016-03-20 17:32:22', 21, 15, NULL, NULL, NULL, NULL),
(395, 3, 1, '2016-03-20 17:32:22', 21, 16, NULL, NULL, NULL, NULL),
(396, 3, 1, '2016-03-20 17:32:22', 21, 17, NULL, NULL, NULL, NULL),
(397, 1, 1, '2016-03-20 17:33:10', 21, 20, NULL, NULL, NULL, NULL),
(398, 1, 1, '2016-03-20 17:33:16', 22, 50, NULL, NULL, NULL, NULL),
(399, 1, 1, '2016-03-20 17:33:26', 22, 51, NULL, NULL, NULL, NULL),
(400, 1, 1, '2016-03-20 17:34:43', 11, 1293, NULL, NULL, NULL, NULL),
(402, 3, 1, '2016-03-20 17:35:08', 11, 1293, NULL, NULL, NULL, NULL),
(403, 2, 1, '2016-03-20 17:36:56', 6, 23, NULL, NULL, NULL, NULL),
(404, 2, 1, '2016-03-20 17:38:06', 6, 23, NULL, NULL, NULL, NULL),
(405, 2, 1, '2016-03-20 17:38:54', 6, 23, NULL, NULL, NULL, NULL),
(406, 1, 1, '2016-03-20 17:39:55', 11, 1295, NULL, NULL, NULL, NULL),
(408, 3, 1, '2016-03-20 17:43:33', 11, 1295, NULL, NULL, NULL, NULL),
(409, 1, 1, '2016-03-20 17:45:20', 11, 1297, NULL, NULL, NULL, NULL),
(410, 2, 1, '2016-03-20 17:54:00', 6, 23, NULL, NULL, NULL, NULL),
(411, 2, 1, '2016-03-20 17:55:02', 21, 1, NULL, NULL, NULL, NULL),
(412, 2, 1, '2016-03-20 17:58:52', 20, 1, NULL, NULL, NULL, NULL),
(413, 2, 1, '2016-03-20 17:59:15', 20, 1, NULL, NULL, NULL, NULL),
(414, 2, 1, '2016-03-20 17:59:47', 20, 1, NULL, NULL, NULL, NULL),
(415, 2, 1, '2016-03-20 18:00:52', 20, 23, NULL, NULL, NULL, NULL),
(416, 1, 1, '2016-03-20 18:03:06', 21, 21, NULL, NULL, NULL, NULL),
(417, 2, 1, '2016-03-20 18:03:13', 21, 21, NULL, NULL, NULL, NULL),
(418, 1, 1, '2016-03-20 18:03:18', 22, 52, NULL, NULL, NULL, NULL),
(419, 1, 1, '2016-03-20 18:03:26', 22, 53, NULL, NULL, NULL, NULL),
(420, 2, 1, '2016-03-20 18:03:37', 21, 21, NULL, NULL, NULL, NULL),
(421, 1, 1, '2016-03-20 18:04:15', 21, 22, NULL, NULL, NULL, NULL),
(422, 1, 1, '2016-03-20 18:04:24', 22, 54, NULL, NULL, NULL, NULL),
(423, 1, 1, '2016-03-20 18:04:31', 22, 55, NULL, NULL, NULL, NULL),
(424, 2, 1, '2016-03-20 18:06:43', 21, 18, NULL, NULL, NULL, NULL),
(425, 1, 1, '2016-03-20 18:22:17', 23, 244, NULL, NULL, NULL, NULL),
(426, 1, 1, '2016-03-20 18:22:36', 23, 245, NULL, NULL, NULL, NULL),
(427, 1, 1, '2016-03-20 18:22:54', 23, 246, NULL, NULL, NULL, NULL),
(428, 1, 1, '2016-03-20 18:24:11', 23, 247, NULL, NULL, NULL, NULL),
(429, 2, 1, '2016-03-20 18:24:19', 23, 247, NULL, NULL, NULL, NULL),
(430, 1, 1, '2016-03-20 18:24:26', 23, 248, NULL, NULL, NULL, NULL),
(431, 1, 18, '2016-03-20 18:26:55', 127, 59, NULL, NULL, NULL, NULL),
(432, 1, 1, '2016-03-20 18:30:06', 23, 249, NULL, NULL, NULL, NULL),
(433, 1, 1, '2016-03-20 18:31:12', 23, 250, NULL, NULL, NULL, NULL),
(434, 1, 1, '2016-03-20 18:32:32', 23, 251, NULL, NULL, NULL, NULL),
(435, 1, 1, '2016-03-20 18:35:28', 23, 252, NULL, NULL, NULL, NULL),
(436, 3, 1, '2016-03-20 18:38:07', 22, 3, NULL, NULL, NULL, NULL),
(437, 2, 1, '2016-03-20 18:43:06', 6, 22, NULL, NULL, NULL, NULL),
(438, 2, 1, '2016-03-20 18:44:25', 6, 23, NULL, NULL, NULL, NULL),
(439, 1, 1, '2016-03-21 07:16:25', 60, 52, NULL, NULL, NULL, NULL),
(440, 1, 1, '2016-03-21 07:16:39', 232, 11, NULL, NULL, NULL, NULL),
(441, 1, 1, '2016-03-21 07:16:39', 232, 12, NULL, NULL, NULL, NULL),
(442, 1, 1, '2016-03-21 07:16:54', 126, 38, NULL, NULL, NULL, NULL),
(443, 1, 1, '2016-03-21 07:16:57', 127, 60, NULL, NULL, NULL, NULL),
(444, 1, 1, '2016-03-21 07:21:12', 20, 26, NULL, NULL, NULL, NULL),
(445, 1, 1, '2016-03-21 07:21:20', 22, 56, NULL, NULL, NULL, NULL),
(446, 1, 1, '2016-03-21 07:21:39', 23, 253, NULL, NULL, NULL, NULL),
(447, 1, 1, '2016-03-21 07:21:48', 23, 254, NULL, NULL, NULL, NULL),
(448, 1, 1, '2016-03-21 07:21:58', 23, 255, NULL, NULL, NULL, NULL),
(449, 1, 1, '2016-03-21 07:22:10', 23, 256, NULL, NULL, NULL, NULL),
(450, 1, 1, '2016-03-21 07:22:24', 23, 257, NULL, NULL, NULL, NULL),
(451, 1, 1, '2016-03-21 07:22:45', 23, 258, NULL, NULL, NULL, NULL),
(452, 1, 1, '2016-03-21 07:22:59', 23, 259, NULL, NULL, NULL, NULL),
(453, 1, 1, '2016-03-21 07:23:11', 23, 260, NULL, NULL, NULL, NULL),
(454, 1, 1, '2016-03-21 07:23:26', 23, 261, NULL, NULL, NULL, NULL),
(455, 1, 1, '2016-03-21 07:23:41', 23, 262, NULL, NULL, NULL, NULL),
(456, 1, 1, '2016-03-21 07:23:51', 23, 263, NULL, NULL, NULL, NULL),
(457, 1, 1, '2016-03-21 07:24:13', 23, 264, NULL, NULL, NULL, NULL),
(458, 1, 1, '2016-03-21 07:24:43', 23, 265, NULL, NULL, NULL, NULL),
(459, 1, 1, '2016-03-21 07:24:58', 23, 266, NULL, NULL, NULL, NULL),
(460, 2, 1, '2016-03-21 07:27:30', 23, 254, NULL, NULL, NULL, NULL),
(461, 3, 1, '2016-03-21 07:30:03', 127, 60, NULL, NULL, NULL, NULL),
(462, 1, 21, '2016-03-21 07:38:35', 126, 39, NULL, NULL, NULL, NULL),
(463, 3, 21, '2016-03-21 07:38:40', 126, 39, NULL, NULL, NULL, NULL),
(464, 3, 1, '2016-03-21 07:44:58', 120, 12, NULL, NULL, NULL, NULL),
(465, 3, 1, '2016-03-21 07:45:07', 120, 4, NULL, NULL, NULL, NULL),
(466, 3, 1, '2016-03-21 07:45:15', 120, 3, NULL, NULL, NULL, NULL),
(467, 3, 1, '2016-03-21 07:45:20', 120, 11, NULL, NULL, NULL, NULL),
(468, 3, 1, '2016-03-21 07:46:00', 235, 2, NULL, NULL, NULL, NULL),
(469, 3, 1, '2016-03-21 07:46:16', 235, 1, NULL, NULL, NULL, NULL),
(470, 3, 1, '2016-03-21 07:46:30', 235, 15, NULL, NULL, NULL, NULL),
(471, 3, 1, '2016-03-21 07:49:59', 101, 29, NULL, NULL, NULL, NULL),
(472, 3, 1, '2016-03-21 07:50:02', 100, 12, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_db_event_types`
--

CREATE TABLE IF NOT EXISTS `dx_db_event_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nosaukums',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dx_db_event_types_title_unique` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `dx_db_event_types`
--

INSERT INTO `dx_db_event_types` (`id`, `title`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Jauns', NULL, NULL, NULL, NULL),
(2, 'Labošana', NULL, NULL, NULL, NULL),
(3, 'Dzēšana', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_db_history`
--

CREATE TABLE IF NOT EXISTS `dx_db_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` int(10) unsigned DEFAULT NULL COMMENT 'Notikums',
  `field_id` int(11) DEFAULT NULL COMMENT 'Lauks',
  `old_val_txt` text COLLATE utf8_unicode_ci COMMENT 'Vecā vērtība',
  `new_val_txt` text COLLATE utf8_unicode_ci COMMENT 'Jaunā vērtība',
  `old_val_rel_id` int(11) DEFAULT NULL COMMENT 'Vecais saistītā ieraksta ID',
  `new_val_rel_id` int(11) DEFAULT NULL COMMENT 'Jaunais saistītā ieraksta ID',
  `old_val_file_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Vecais datnes nosaukums',
  `new_val_file_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Jaunais datnes nosaukums',
  `old_val_file_guid` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Vecais datnes GUID',
  `new_val_file_guid` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Jaunais datnes GUID',
  PRIMARY KEY (`id`),
  KEY `dx_db_history_event_id_index` (`event_id`),
  KEY `dx_db_history_field_id_index` (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=846 ;

--
-- Dumping data for table `dx_db_history`
--

INSERT INTO `dx_db_history` (`id`, `event_id`, `field_id`, `old_val_txt`, `new_val_txt`, `old_val_rel_id`, `new_val_rel_id`, `old_val_file_name`, `new_val_file_name`, `old_val_file_guid`, `new_val_file_guid`) VALUES
(1, 2, 547, '27.11.2015 15:00', '27.11.2015 15:00', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 2, 1398, NULL, 'Testa vide galerija', NULL, 34, NULL, NULL, NULL, NULL),
(3, 8, 547, '03.03.2016 17:19', '03.03.2016 17:19', NULL, NULL, NULL, NULL, NULL, NULL),
(4, 8, 289, '', 'Penguins.jpg', NULL, NULL, '', 'Penguins.jpg', '', '53c9f41e-9019-47ca-afd9-c655b77b497e.jpg'),
(5, 9, 547, '03.03.2016 17:19', '03.03.2016 17:19', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 10, 598, '0', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(7, 10, 547, '15.06.2015 15:34', '15.06.2015 15:34', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 11, 598, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 11, 547, '15.06.2015 15:34', '15.06.2015 15:34', NULL, NULL, NULL, NULL, NULL, NULL),
(10, 12, 547, '15.03.2016 17:45', '15.03.2016 17:45', NULL, NULL, NULL, NULL, NULL, NULL),
(11, 13, 1171, 'Ziņas', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(12, 13, 1411, 'Teksts', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(13, 13, 728, 'Latvenergo', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(14, 13, 287, 'TESTS 899', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 13, 1412, 'tests-899', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 13, 598, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 13, 1396, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 13, 547, '15.03.2016 17:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 13, 291, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 13, 290, '127', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 14, 1171, 'Ziņas', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(22, 14, 1411, 'Datne', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(23, 14, 728, 'Latvenergo', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(24, 14, 287, 'Lejuplādējamas datnes demo virsraksts', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 14, 598, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 14, 1396, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 14, 547, '14.03.2016 14:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 14, 546, 'Lejuplādējamas datnes demo virsraksts. Lejuplādējamas datnes demo virsraksts. Lejuplādējamas datnes demo virsraksts', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 14, 289, 'Lighthouse.jpg', NULL, NULL, NULL, 'Lighthouse.jpg', NULL, 'a6a87853-952d-4f78-b165-63bcc1f6722c.jpg', NULL),
(30, 14, 1414, 'LE_Intranet_Protokols_012.doc', NULL, NULL, NULL, 'LE_Intranet_Protokols_012.doc', NULL, '4c2f581e-f4fd-465b-8f3a-5801600501a7.doc', NULL),
(31, 14, 291, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 14, 290, '127', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 15, 1171, 'Ziņas', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(34, 15, 1411, 'Ārējā saite', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(35, 15, 728, 'Latvenergo', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(36, 15, 287, 'Ārējā saite demo nolūkiem', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 15, 598, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 15, 1396, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 15, 547, '14.03.2016 14:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 15, 546, 'Šis ir ārējās saites demo parauga anotācijas teksts. Klikšķinot uz saites, atvērsies cita lapa.', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 15, 1413, 'http://www.latvenergo.lv', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 15, 291, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 15, 290, '127', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 16, 1171, 'Ziņas', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(45, 16, 1411, 'Teksts', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(46, 16, 728, 'Elektrum', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(47, 16, 287, 'TEST22', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 16, 598, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 16, 1396, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 16, 547, '03.03.2016 17:19', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 16, 546, 'tests', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 16, 288, '<p>tests</p>', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 16, 291, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 16, 290, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 17, 547, '04.12.2015 15:08', '04.12.2015 15:08', NULL, NULL, NULL, NULL, NULL, NULL),
(56, 18, 1401, 'AS "Latvenergo" un LOK vienojas par sadarbību', NULL, 6, NULL, NULL, NULL, NULL, NULL),
(57, 18, 1402, 'LE_Intranet_Protokols_011.doc', NULL, NULL, NULL, 'LE_Intranet_Protokols_011.doc', NULL, 'a49b0cc7-5c96-4822-878d-0c95100f2d8a.doc', NULL),
(58, 18, 1404, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 19, 1401, 'AS "Latvenergo" un LOK vienojas par sadarbību', NULL, 6, NULL, NULL, NULL, NULL, NULL),
(60, 19, 1402, '23-02-2016.txt', NULL, NULL, NULL, '23-02-2016.txt', NULL, '360f2e21-0a97-4bd0-b06e-54b2a7648e60.txt', NULL),
(61, 19, 1404, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 20, 1401, 'AS "Latvenergo" un LOK vienojas par sadarbību', NULL, 6, NULL, NULL, NULL, NULL, NULL),
(63, 20, 1402, 'LE_Intranet_Protokols_010.doc', NULL, NULL, NULL, 'LE_Intranet_Protokols_010.doc', NULL, '858f46ac-bf3a-4eff-b86b-4e40214217f4.doc', NULL),
(64, 20, 1404, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 21, 1401, 'AS "Latvenergo" un LOK vienojas par sadarbību', NULL, 6, NULL, NULL, NULL, NULL, NULL),
(66, 21, 1402, 'LE_Intranet_Protokols_012.doc', NULL, NULL, NULL, 'LE_Intranet_Protokols_012.doc', NULL, 'ffe9e91d-0260-4fd4-97e3-dcde55bffbba.doc', NULL),
(67, 21, 1404, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 22, 1401, 'AS "Latvenergo" un LOK vienojas par sadarbību', NULL, 6, NULL, NULL, NULL, NULL, NULL),
(69, 22, 1402, '03-03-2016.txt', NULL, NULL, NULL, '03-03-2016.txt', NULL, '21cda44e-fa4c-46b2-bdd2-f309d2cea3a6.txt', NULL),
(70, 22, 1404, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 23, 1189, 'Tests kas darbojas', NULL, 19, NULL, NULL, NULL, NULL, NULL),
(72, 23, 1190, 'Desert.jpg', NULL, NULL, NULL, 'Desert.jpg', NULL, '3ae495b2-e256-447f-9ed5-b3d432692fd6.jpg', NULL),
(73, 23, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 24, 1179, '11.01.2016 17:53', '11.01.2016 17:53', NULL, NULL, NULL, NULL, NULL, NULL),
(75, 25, 1182, 'Attēli', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(76, 25, 1181, 'Elektrum', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(77, 25, 1173, 'Tests kas darbojas', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(78, 25, 1179, '11.01.2016 17:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(79, 25, 1178, 'TEsta  tests', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 25, 1175, 'Chrysanthemum.jpg', NULL, NULL, NULL, 'Chrysanthemum.jpg', NULL, '5655c068-6ab8-4dac-a99f-9648924a7082.jpg', NULL),
(81, 25, 1177, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(82, 26, 1189, 'Nākošā testa galerija', NULL, 18, NULL, NULL, NULL, NULL, NULL),
(83, 26, 1190, 'Koala.jpg', NULL, NULL, NULL, 'Koala.jpg', NULL, 'e5ecfeb0-c937-45c6-a20b-ca8983e4efe1.jpg', NULL),
(84, 26, 1192, '10', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(85, 27, 1189, 'Nākošā testa galerija', NULL, 18, NULL, NULL, NULL, NULL, NULL),
(86, 27, 1190, 'Penguins.jpg', NULL, NULL, NULL, 'Penguins.jpg', NULL, '2d42e14f-2b52-434a-a146-d24cd0b35d26.jpg', NULL),
(87, 27, 1192, '20', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(88, 28, 1182, 'Attēli', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(89, 28, 1181, 'Personāls', NULL, 4, NULL, NULL, NULL, NULL, NULL),
(90, 28, 1173, 'Nākošā testa galerija', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(91, 28, 1179, '16.01.2016 15:59', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(92, 28, 1178, 'Testa ievads', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(93, 28, 1175, 'Koala.jpg', NULL, NULL, NULL, 'Koala.jpg', NULL, '2a378099-dfa0-4d57-94cd-d0a943635e59.jpg', NULL),
(94, 28, 1177, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(95, 29, 1189, 'Vēl Elektrum testa galerija', NULL, 17, NULL, NULL, NULL, NULL, NULL),
(96, 29, 1190, 'Penguins.jpg', NULL, NULL, NULL, 'Penguins.jpg', NULL, '54bb4c54-87a8-4899-9615-95f2732fbf46.jpg', NULL),
(97, 29, 1192, '10', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 30, 1189, 'Vēl Elektrum testa galerija', NULL, 17, NULL, NULL, NULL, NULL, NULL),
(99, 30, 1190, 'Jellyfish.jpg', NULL, NULL, NULL, 'Jellyfish.jpg', NULL, '29d25166-5e23-46b0-ac97-ddca7d17324c.jpg', NULL),
(100, 30, 1192, '20', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(101, 31, 1182, 'Attēli', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(102, 31, 1181, 'Elektrum', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(103, 31, 1173, 'Vēl Elektrum testa galerija', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(104, 31, 1179, '17.01.2016 15:56', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(105, 31, 1178, 'Testa ievads', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(106, 31, 1175, 'Penguins.jpg', NULL, NULL, NULL, 'Penguins.jpg', NULL, '1f7dc4c8-fbfd-4a30-a145-63d9f8ab4d0f.jpg', NULL),
(107, 31, 1177, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(108, 32, 1189, 'Testa galerija Nr. 3', NULL, 16, NULL, NULL, NULL, NULL, NULL),
(109, 32, 1190, 'Lighthouse.jpg', NULL, NULL, NULL, 'Lighthouse.jpg', NULL, '5b8601e9-cc90-431d-8133-c9f365a78eb0.jpg', NULL),
(110, 32, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(111, 33, 1189, 'Testa galerija Nr. 3', NULL, 16, NULL, NULL, NULL, NULL, NULL),
(112, 33, 1190, 'Jellyfish.jpg', NULL, NULL, NULL, 'Jellyfish.jpg', NULL, '0b7256d6-ef42-4197-aab6-626de96eb745.jpg', NULL),
(113, 33, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(114, 34, 1189, 'Testa galerija Nr. 3', NULL, 16, NULL, NULL, NULL, NULL, NULL),
(115, 34, 1190, 'Desert.jpg', NULL, NULL, NULL, 'Desert.jpg', NULL, '03210e6f-028e-4854-a866-46a28c070cf2.jpg', NULL),
(116, 34, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(117, 35, 1182, 'Attēli', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(118, 35, 1181, 'Elektrum', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(119, 35, 1173, 'Testa galerija Nr. 3', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(120, 35, 1179, '01.01.2016 12:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(121, 35, 1178, 'Vēl viena testa galerija Nr. 3 šoreiz', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(122, 35, 1175, 'Lighthouse.jpg', NULL, NULL, NULL, 'Lighthouse.jpg', NULL, 'f46cd22a-ec31-4fa6-be02-4af35ee755e4.jpg', NULL),
(123, 35, 1177, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(124, 36, 1182, 'Attēli', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(125, 36, 1181, 'Elektrum', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(126, 36, 1173, 'Vēl viena testa galerija', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(127, 36, 1179, '02.01.2016 12:39', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(128, 36, 1178, 'Testa apraksts', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(129, 36, 1175, 'Jellyfish.jpg', NULL, NULL, NULL, 'Jellyfish.jpg', NULL, 'c8f9a5e2-d346-4ad7-801e-62085305d7a4.jpg', NULL),
(130, 36, 1177, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(131, 37, 1189, 'Testa attēlu galerija', NULL, 14, NULL, NULL, NULL, NULL, NULL),
(132, 37, 1190, 'Hydrangeas.jpg', NULL, NULL, NULL, 'Hydrangeas.jpg', NULL, '8c1b5656-4de2-4f38-88cb-0fca7e7e6daa.jpg', NULL),
(133, 37, 1192, '10', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(134, 38, 1189, 'Testa attēlu galerija', NULL, 14, NULL, NULL, NULL, NULL, NULL),
(135, 38, 1190, 'Desert.jpg', NULL, NULL, NULL, 'Desert.jpg', NULL, '423ca43c-55e7-4889-8631-b5df5ee0dcca.jpg', NULL),
(136, 38, 1192, '20', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(137, 39, 1189, 'Testa attēlu galerija', NULL, 14, NULL, NULL, NULL, NULL, NULL),
(138, 39, 1190, 'Penguins.jpg', NULL, NULL, NULL, 'Penguins.jpg', NULL, 'ee5563e6-9d1b-4ae7-89f3-5d8aca943d02.jpg', NULL),
(139, 39, 1192, '30', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(140, 40, 1182, 'Attēli', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(141, 40, 1181, 'Elektrum', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(142, 40, 1173, 'Testa attēlu galerija', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(143, 40, 1179, '03.01.2016 12:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(144, 40, 1178, 'Šī ir testa attēlu galerija, kas izveidota no SVS', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(145, 40, 1175, 'Hydrangeas.jpg', NULL, NULL, NULL, 'Hydrangeas.jpg', NULL, '7622e2e0-6b32-4f0f-a51b-e744d4daf954.jpg', NULL),
(146, 40, 1177, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(147, 41, 1182, 'Attēli', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(148, 41, 1181, 'Latvenergo', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(149, 41, 1173, 'TESTS 666', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(150, 41, 1179, '15.03.2016 17:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(151, 41, 1177, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(152, 42, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(153, 42, 1190, 'olimp.jpg', NULL, NULL, NULL, 'olimp.jpg', NULL, '514208c3-a834-4f42-9d9c-3a8315eeb7b5.jpg', NULL),
(154, 42, 1192, '100', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(155, 43, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(156, 43, 1190, 'turbin.jpg', NULL, NULL, NULL, 'turbin.jpg', NULL, '6bd8424a-cc6a-4a49-867b-f0e3e2174c95.jpg', NULL),
(157, 43, 1192, '100', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(158, 44, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(159, 44, 1190, 'hes.jpg', NULL, NULL, NULL, 'hes.jpg', NULL, 'ca8f867b-a05d-4180-ab30-dc8002740edc.jpg', NULL),
(160, 44, 1192, '100', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(161, 45, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(162, 45, 1190, 'enu_diena.jpg', NULL, NULL, NULL, 'enu_diena.jpg', NULL, '835432bc-8755-46b6-b3f5-6b410ae50143.jpg', NULL),
(163, 45, 1192, '100', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(164, 46, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(165, 46, 1190, 'le_poga.jpg', NULL, NULL, NULL, 'le_poga.jpg', NULL, '6b91e5e1-db42-4913-8194-e7971aaff491.jpg', NULL),
(166, 46, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(167, 47, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(168, 47, 1190, 'metronic_dark.png', NULL, NULL, NULL, 'metronic_dark.png', NULL, '323522e3-7013-4095-8b51-1ed87688833c.png', NULL),
(169, 47, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(170, 48, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(171, 48, 1190, 'olimp.jpg', NULL, NULL, NULL, 'olimp.jpg', NULL, '5670eac7-4329-4af9-9014-9a2b2e2a39ae.jpg', NULL),
(172, 48, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(173, 49, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(174, 49, 1190, 'macibu_kursi.jpg', NULL, NULL, NULL, 'macibu_kursi.jpg', NULL, '086081a4-055f-4069-81de-f0d8bb9c0493.jpg', NULL),
(175, 49, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(176, 50, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(177, 50, 1190, 'd47dca64-e5c0-406a-a085-7be952d81b75.jpg', NULL, NULL, NULL, 'd47dca64-e5c0-406a-a085-7be952d81b75.jpg', NULL, 'b03c9a7f-1c26-4418-9f4f-a09126f09555.jpg', NULL),
(178, 50, 1192, '800', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(179, 51, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(180, 51, 1190, '39aeec08-15d0-48ac-acf1-ad393556480c.jpg', NULL, NULL, NULL, '39aeec08-15d0-48ac-acf1-ad393556480c.jpg', NULL, 'b2f0840b-7d77-4874-87b9-860b2c89982f.jpg', NULL),
(181, 51, 1192, '800', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(182, 52, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(183, 52, 1190, 'd0698c7a-d8f7-4dbc-abd3-ef51b4b2fc7e.jpg', NULL, NULL, NULL, 'd0698c7a-d8f7-4dbc-abd3-ef51b4b2fc7e.jpg', NULL, 'b79d82ad-daa8-45a1-876d-ac8ede42e122.jpg', NULL),
(184, 52, 1192, '800', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(185, 53, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(186, 53, 1190, '98392cd4-3eae-4412-acbb-7d8da887f2e4.jpg', NULL, NULL, NULL, '98392cd4-3eae-4412-acbb-7d8da887f2e4.jpg', NULL, '32270ab6-0226-49f7-b7de-a85b5e3bd40c.jpg', NULL),
(187, 53, 1192, '800', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(188, 54, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(189, 54, 1190, '133fbc51-9eaa-4c81-a8d8-5459608e66c6.jpg', NULL, NULL, NULL, '133fbc51-9eaa-4c81-a8d8-5459608e66c6.jpg', NULL, 'b7df88df-ce3c-43a1-9fac-48a36d4996bf.jpg', NULL),
(190, 54, 1192, '800', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(191, 55, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(192, 55, 1190, 'd8fb29d0-4b94-4d6d-95d0-7e6d92918f04.jpg', NULL, NULL, NULL, 'd8fb29d0-4b94-4d6d-95d0-7e6d92918f04.jpg', NULL, 'db919ba8-0155-4f9c-858c-25ea9168d741.jpg', NULL),
(193, 55, 1192, '800', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(194, 56, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(195, 56, 1190, '972416f4-7716-40f3-80e3-2ad01627173a.jpg', NULL, NULL, NULL, '972416f4-7716-40f3-80e3-2ad01627173a.jpg', NULL, '791b9ab5-9e34-44ac-b044-b0129a1849f1.jpg', NULL),
(196, 56, 1192, '800', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(197, 57, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(198, 57, 1190, 'ilana_paraksts.png', NULL, NULL, NULL, 'ilana_paraksts.png', NULL, '69c7e0a0-a02e-4521-9750-3cf29ad3e18e.png', NULL),
(199, 57, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(200, 58, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(201, 58, 1190, 'aero-energy-background-1920x1080.jpg', NULL, NULL, NULL, 'aero-energy-background-1920x1080.jpg', NULL, '2a580427-a3f8-40a4-8f3f-f769451397f9.jpg', NULL),
(202, 58, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(203, 59, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(204, 59, 1190, 'Winter-magic-daydreaming-18154150-1024-768.jpg', NULL, NULL, NULL, 'Winter-magic-daydreaming-18154150-1024-768.jpg', NULL, '11f7ba32-df40-4073-9d40-7d6f412220c6.jpg', NULL),
(205, 59, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(206, 60, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(207, 60, 1190, 'free_winter_background_pictures-.jpg', NULL, NULL, NULL, 'free_winter_background_pictures-.jpg', NULL, '42c52aed-ed1d-4edb-a4d6-490e6a9a0858.jpg', NULL),
(208, 60, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(209, 61, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(210, 61, 1190, 'Christmas_wallpapers_WideScreen.jpg', NULL, NULL, NULL, 'Christmas_wallpapers_WideScreen.jpg', NULL, '7b115c74-fa69-47db-8074-c50da15b0120.jpg', NULL),
(211, 61, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(212, 62, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(213, 62, 1190, 'zgSKP05.jpg', NULL, NULL, NULL, 'zgSKP05.jpg', NULL, '9c45976e-9250-4373-aebb-37a1d451df66.jpg', NULL),
(214, 62, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(215, 63, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(216, 63, 1190, 'winter background 05.jpg', NULL, NULL, NULL, 'winter background 05.jpg', NULL, 'dbff75e0-4f65-4856-b93a-6c980827ec7d.jpg', NULL),
(217, 63, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(218, 64, 1189, 'Testa galerija multi datnes', NULL, 30, NULL, NULL, NULL, NULL, NULL),
(219, 64, 1190, 'red-and-gold-background.jpg', NULL, NULL, NULL, 'red-and-gold-background.jpg', NULL, 'edffc9a2-0ea5-4c9e-80ed-5ec71d79e69c.jpg', NULL),
(220, 64, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(221, 65, 1182, 'Attēli', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(222, 65, 1181, 'Latvenergo', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(223, 65, 1173, 'Testa galerija multi datnes', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(224, 65, 1179, '02.02.2016 17:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(225, 65, 1178, 'Testējam n datņu pievienošanu', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(226, 65, 1175, 'turbin.jpg', NULL, NULL, NULL, 'turbin.jpg', NULL, 'ce3d7318-83f1-407f-802b-c2a5dccf5072.jpg', NULL),
(227, 65, 1177, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(228, 66, 1294, 'Video', NULL, 4, NULL, NULL, NULL, NULL, NULL),
(229, 66, 1293, 'Latvenergo', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(230, 66, 1288, 'TEST444445', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(231, 66, 1292, '15.03.2016 17:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(232, 66, 1291, 'TESTS2224', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(233, 66, 1290, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(234, 67, 1294, 'Video', NULL, 4, NULL, NULL, NULL, NULL, NULL),
(235, 67, 1293, 'Elektrum', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(236, 67, 1288, 'TEST444', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(237, 67, 1292, '15.03.2016 17:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(238, 67, 1291, 'TEST TEST111444', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(239, 67, 1290, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(240, 68, 1294, 'Video', NULL, 4, NULL, NULL, NULL, NULL, NULL),
(241, 68, 1293, 'Elektrum', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(242, 68, 1288, 'TESTS VIDEO 111', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(243, 68, 1292, '15.03.2016 17:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(244, 68, 1291, 'TESTS', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(245, 68, 1290, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(246, 79, 197, '0', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(247, 80, 37, 'Ziņas galerijām', NULL, 247, NULL, NULL, NULL, NULL, NULL),
(248, 80, 50, 'Visas ziņas', NULL, 60, NULL, NULL, NULL, NULL, NULL),
(249, 80, 38, 'Ziņas veids', NULL, 1171, NULL, NULL, NULL, NULL, NULL),
(250, 80, 39, '50', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(251, 80, 40, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(252, 80, 41, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(253, 80, 195, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(254, 80, 179, 'Vienāds', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(255, 80, 180, '''Ziņas''', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(256, 80, 181, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(257, 81, 37, 'Ziņas galerijām', NULL, 247, NULL, NULL, NULL, NULL, NULL),
(258, 81, 50, 'Visas ziņas', NULL, 60, NULL, NULL, NULL, NULL, NULL),
(259, 81, 38, 'Publicēts', NULL, 291, NULL, NULL, NULL, NULL, NULL),
(260, 81, 39, '50', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(261, 81, 40, '7', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(262, 81, 41, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(263, 81, 195, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(264, 81, 181, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(265, 82, 37, 'Ziņas galerijām', NULL, 247, NULL, NULL, NULL, NULL, NULL),
(266, 82, 50, 'Visas ziņas', NULL, 60, NULL, NULL, NULL, NULL, NULL),
(267, 82, 38, 'Ir aktīvā ziņa', NULL, 598, NULL, NULL, NULL, NULL, NULL),
(268, 82, 39, '50', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(269, 82, 40, '40', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(270, 82, 41, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(271, 82, 169, 'Ir aktīvā', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(272, 82, 195, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(273, 82, 181, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(274, 83, 37, 'Ziņas galerijām', NULL, 247, NULL, NULL, NULL, NULL, NULL),
(275, 83, 50, 'Visas ziņas', NULL, 60, NULL, NULL, NULL, NULL, NULL),
(276, 83, 38, 'Datu avots', NULL, 728, NULL, NULL, NULL, NULL, NULL),
(277, 83, 39, '100', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(278, 83, 40, '80', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(279, 83, 41, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(280, 83, 195, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(281, 83, 181, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(282, 86, 196, '0', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(283, 89, 180, '''Ziņa''', '''Ziņas''', NULL, NULL, NULL, NULL, NULL, NULL),
(284, 90, 547, '27.11.2015 15:00', '27.11.2015 15:00', NULL, NULL, NULL, NULL, NULL, NULL),
(285, 91, 1294, 'Video', NULL, 4, NULL, NULL, NULL, NULL, NULL),
(286, 91, 1293, 'Elektrum', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(287, 91, 1288, 'Testa vide galerija', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(288, 91, 1292, '15.03.2016 17:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(289, 91, 1291, 'Tests tests tests', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(290, 91, 1289, 'Penguins.jpg', NULL, NULL, NULL, 'Penguins.jpg', NULL, 'a6092fb1-ebc6-4b77-94cd-6024c63386b7.jpg', NULL),
(291, 91, 1290, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(292, 92, 1395, NULL, 'Galerijā sīktēla izmērs ir 305px (platums) x 200px (augstums). Sistēma automātiski veido sīktēlu atbilstoši šai proporcijai. Lai nekropļotu attēlu, ieteicams pievienot attēlus tādā pašā izmēru propocijā, piemēram, 915px x 600px.', NULL, NULL, NULL, NULL, NULL, NULL),
(293, 93, 1395, NULL, 'Galerijā sīktēla izmērs ir 305px (platums) x 200px (augstums). Sistēma automātiski veido sīktēlu atbilstoši šai proporcijai. Lai nekropļotu attēlu, ieteicams pievienot attēlus tādā pašā izmēru propocijā, piemēram, 915px x 600px.', NULL, NULL, NULL, NULL, NULL, NULL),
(294, 94, 1395, 'Attēls portālā tiks parādīts ziņu plūsmā un.vai aktīvo ziņu slīdrādē, bet netiks parādīts skatot ziņas detalizācijas lapu.', 'Attēls portālā tiks parādīts ziņu plūsmā un/vai aktīvo ziņu slīdrādē, bet netiks parādīts skatot ziņas detalizācijas lapu. Attēla izmēriem jābūt 1420px (platums) x 840px (augstums) vai lielāki izmēri, bet noteikti saglabājot proporciju.', NULL, NULL, NULL, NULL, NULL, NULL),
(295, 95, 547, '27.11.2015 15:00', '27.11.2015 15:00', NULL, NULL, NULL, NULL, NULL, NULL),
(296, 95, 289, '01_7ac9eb9a-1273-41a7-883a-e670b965fa9b.jpg', 'le_bilde.jpg', NULL, NULL, '01_7ac9eb9a-1273-41a7-883a-e670b965fa9b.jpg', 'le_bilde.jpg', '39aeec08-15d0-48ac-acf1-ad393556480c.jpg', '9cdd7295-e181-4468-b5eb-dbe3be67349f.jpg'),
(297, 96, 598, '0', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(298, 96, 547, '27.11.2015 15:00', '27.11.2015 15:00', NULL, NULL, NULL, NULL, NULL, NULL),
(299, 99, 547, '09.03.2016 13:15', '09.03.2016 13:15', NULL, NULL, NULL, NULL, NULL, NULL),
(300, 99, 290, '127', '100', NULL, NULL, NULL, NULL, NULL, NULL),
(301, 101, 547, '15.01.2016 15:30', '15.01.2016 15:30', NULL, NULL, NULL, NULL, NULL, NULL),
(302, 101, 290, '0', '-500', NULL, NULL, NULL, NULL, NULL, NULL),
(303, 130, 1190, 'd47dca64-e5c0-406a-a085-7be952d81b75.jpg', '39aeec08-15d0-48ac-acf1-ad393556480c.jpg', NULL, NULL, 'd47dca64-e5c0-406a-a085-7be952d81b75.jpg', '39aeec08-15d0-48ac-acf1-ad393556480c.jpg', '93719e33-2f85-4c10-adaf-2c0e50aed6db.jpg', '91502504-e8b0-48b5-9212-af89cee2459e.jpg'),
(304, 130, 1192, '0', '50', NULL, NULL, NULL, NULL, NULL, NULL),
(305, 132, 547, '16.03.2016 13:35', '16.03.2016 13:35', NULL, NULL, NULL, NULL, NULL, NULL),
(306, 132, 290, '127', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(307, 133, 547, '16.03.2016 13:35', '16.03.2016 13:35', NULL, NULL, NULL, NULL, NULL, NULL),
(308, 133, 1398, 'Vai gāzi var liet kā ūdeni - Ventspils svētkos aicina FIZMIX.LV', 'Latvenergo zaļās obligācijas saņem Moody’s kredītreitingu', 3, 4, NULL, NULL, NULL, NULL),
(309, 138, 1171, 'Ziņas', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(310, 138, 1411, 'Teksts', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(311, 138, 728, 'Latvenergo', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(312, 138, 287, 'Testa parastā zīņa', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(313, 138, 1412, 'testa-parasta-zina', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(314, 138, 598, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(315, 138, 1396, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(316, 138, 547, '16.03.2016 13:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(317, 138, 546, 'Anotācija atedss ', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(318, 138, 288, '<p><img src="resources/atteli/Chrysanthemum.jpg" alt="Alternate" width="300" height="225" /></p>\r\n<p></p>\r\n<p>jklsjdk jklsajd</p>\r\n<p>lkasjdk;ajsd;</p>\r\n<p>lkasjdklasdsad</p>', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(319, 138, 1407, 'Kārlis Zariņš', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(320, 138, 289, 'Hydrangeas.jpg', NULL, NULL, NULL, 'Hydrangeas.jpg', NULL, '502ce0e0-e516-4b4c-8f33-6b7a3f81f59b.jpg', NULL),
(321, 138, 1398, 'Latvenergo zaļās obligācijas saņem Moody’s kredītreitingu', NULL, 4, NULL, NULL, NULL, NULL, NULL),
(322, 138, 1399, 'Attēlu galerija', NULL, 42, NULL, NULL, NULL, NULL, NULL),
(323, 138, 291, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(324, 138, 290, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(325, 139, 621, 'faq', 'bui', NULL, NULL, NULL, NULL, NULL, NULL),
(326, 141, 428, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(327, 141, 106, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(328, 141, 427, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(329, 144, 547, '27.11.2015 15:00', '27.11.2015 15:00', NULL, NULL, NULL, NULL, NULL, NULL),
(330, 144, 289, 'le_bilde.jpg', 'olimp - Copy.jpg', NULL, NULL, 'le_bilde.jpg', 'olimp - Copy.jpg', '9cdd7295-e181-4468-b5eb-dbe3be67349f.jpg', '8e2f1562-5a3c-4f93-83fb-d5e894c60d83.jpg'),
(331, 145, 547, '27.11.2015 15:00', '27.11.2015 15:00', NULL, NULL, NULL, NULL, NULL, NULL),
(332, 145, 289, 'olimp - Copy.jpg', 'hes.jpg', NULL, NULL, 'olimp - Copy.jpg', 'hes.jpg', '8e2f1562-5a3c-4f93-83fb-d5e894c60d83.jpg', '2d803ddd-d972-4035-8d28-255934a36e4c.jpg'),
(333, 147, 547, '27.11.2015 15:00', '27.11.2015 15:00', NULL, NULL, NULL, NULL, NULL, NULL),
(334, 147, 289, 'hes.jpg', 'turbin.jpg', NULL, NULL, 'hes.jpg', 'turbin.jpg', '2d803ddd-d972-4035-8d28-255934a36e4c.jpg', '3d900ca8-7424-4094-ab94-e3966a2ffd89.jpg'),
(335, 148, 1192, '50', '501', NULL, NULL, NULL, NULL, NULL, NULL),
(336, 149, 1192, '10', '11', NULL, NULL, NULL, NULL, NULL, NULL),
(337, 150, 547, '27.11.2015 15:00', '27.11.2015 15:00', NULL, NULL, NULL, NULL, NULL, NULL),
(338, 150, 289, 'turbin.jpg', 'Untitled.png', NULL, NULL, 'turbin.jpg', 'Untitled.png', '3d900ca8-7424-4094-ab94-e3966a2ffd89.jpg', 'f1cd9190-87a4-48eb-b3a0-e06aaa0d04c2.png'),
(339, 151, 1190, 'olimp.jpg', 'Penguins.jpg', NULL, NULL, 'olimp.jpg', 'Penguins.jpg', '23a3a996-f363-4a10-8111-6670a1076f60.jpg', '90b2b105-eaab-41d6-bd1b-8aac7125d2a2.jpg'),
(340, 153, 1190, 'olimp.jpg', 'Koala.jpg', NULL, NULL, 'olimp.jpg', 'Koala.jpg', 'b7132d4a-60dc-4be2-98dd-09309c695647.jpg', '655404f4-c39d-4fc8-9c66-108b0729a2ef.jpg'),
(341, 156, 547, '17.03.2016 15:53', '17.03.2016 15:53', NULL, NULL, NULL, NULL, NULL, NULL),
(342, 157, 547, '17.03.2016 15:53', '17.03.2016 15:53', NULL, NULL, NULL, NULL, NULL, NULL),
(343, 157, 1414, NULL, 'portleti_manual.pdf', NULL, NULL, NULL, 'portleti_manual.pdf', NULL, '3b9f8714-a7c0-47c2-aa61-faef8498ca83.pdf'),
(344, 159, 547, '27.11.2015 15:00', '27.11.2015 15:00', NULL, NULL, NULL, NULL, NULL, NULL),
(345, 159, 1398, NULL, 'Testa video galerija', NULL, 46, NULL, NULL, NULL, NULL),
(346, 161, 1189, 'Vai gāzi var liet kā ūdeni - Ventspils svētkos aicina FIZMIX.LV', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(347, 161, 1190, 'big_big.png', NULL, NULL, NULL, 'big_big.png', NULL, '10d2274f-b459-45c6-bc8e-faafa937ae4f.png', NULL),
(348, 161, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(349, 162, 1179, '31.07.2015 12:41', '31.07.2015 12:41', NULL, NULL, NULL, NULL, NULL, NULL),
(350, 165, 1190, '4_vere.png', '5-cup.jpg', NULL, NULL, '4_vere.png', '5-cup.jpg', '15cbc149-1869-4447-b2f9-3d24192c0afa.png', '4b379456-862d-46fb-92cb-fbe8755608f9.jpg'),
(351, 165, 1192, '12', '127', NULL, NULL, NULL, NULL, NULL, NULL),
(352, 166, 1189, 'Vai gāzi var liet kā ūdeni - Ventspils svētkos aicina FIZMIX.LV', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(353, 166, 1190, 'Penguins.jpg', NULL, NULL, NULL, 'Penguins.jpg', NULL, '90b2b105-eaab-41d6-bd1b-8aac7125d2a2.jpg', NULL),
(354, 166, 1192, '11', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(355, 167, 1189, 'Vai gāzi var liet kā ūdeni - Ventspils svētkos aicina FIZMIX.LV', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(356, 167, 1190, 'Koala.jpg', NULL, NULL, NULL, 'Koala.jpg', NULL, '655404f4-c39d-4fc8-9c66-108b0729a2ef.jpg', NULL),
(357, 167, 1192, '501', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(358, 168, 1189, 'Vai gāzi var liet kā ūdeni - Ventspils svētkos aicina FIZMIX.LV', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(359, 168, 1190, 'turbin.jpg', NULL, NULL, NULL, 'turbin.jpg', NULL, '80d9085f-25f2-44f6-b22e-47d5c05264d6.jpg', NULL),
(360, 168, 1192, '50', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(361, 169, 1189, 'Vai gāzi var liet kā ūdeni - Ventspils svētkos aicina FIZMIX.LV', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(362, 169, 1190, 'hes.jpg', NULL, NULL, NULL, 'hes.jpg', NULL, '49be5319-624e-4942-82f6-90dabe109e39.jpg', NULL),
(363, 169, 1192, '50', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(364, 170, 1189, 'Vai gāzi var liet kā ūdeni - Ventspils svētkos aicina FIZMIX.LV', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(365, 170, 1190, 'Desert.jpg', NULL, NULL, NULL, 'Desert.jpg', NULL, 'f6e17445-7107-4991-9f03-2aea6fd8d378.jpg', NULL),
(366, 170, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(367, 171, 1189, 'Vai gāzi var liet kā ūdeni - Ventspils svētkos aicina FIZMIX.LV', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(368, 171, 1190, '4_vere.png', NULL, NULL, NULL, '4_vere.png', NULL, '41e97910-a073-4ed9-b270-9ff05d4c577c.png', NULL),
(369, 171, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(370, 172, 1189, 'Vai gāzi var liet kā ūdeni - Ventspils svētkos aicina FIZMIX.LV', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(371, 172, 1190, '5-cup.jpg', NULL, NULL, NULL, '5-cup.jpg', NULL, '4b379456-862d-46fb-92cb-fbe8755608f9.jpg', NULL),
(372, 172, 1192, '127', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(373, 175, 1189, 'Vai gāzi var liet kā ūdeni - Ventspils svētkos aicina FIZMIX.LV', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(374, 175, 1190, '4_vere.png', NULL, NULL, NULL, '4_vere.png', NULL, 'ac31326f-793f-4ef5-8d59-d6b3b4b08e91.png', NULL),
(375, 175, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(376, 176, 1189, 'Vai gāzi var liet kā ūdeni - Ventspils svētkos aicina FIZMIX.LV', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(377, 176, 1190, '5-cup.jpg', NULL, NULL, NULL, '5-cup.jpg', NULL, '73912b7d-5eb6-4b0d-bbe4-faf83f5534b0.jpg', NULL),
(378, 176, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(379, 178, 1189, 'Vai gāzi var liet kā ūdeni - Ventspils svētkos aicina FIZMIX.LV', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(380, 178, 1190, '4_vere.png', NULL, NULL, NULL, '4_vere.png', NULL, '6618dd5a-4dc2-4945-99ac-c05a945ba523.png', NULL),
(381, 178, 1192, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(382, 179, 1181, 'Sadales tīkls', 'Elektrum', 3, 2, NULL, NULL, NULL, NULL),
(383, 179, 1179, '31.07.2015 12:41', '31.07.2015 12:41', NULL, NULL, NULL, NULL, NULL, NULL),
(384, 180, 1181, 'Elektrum', 'Sadales tīkls', 2, 3, NULL, NULL, NULL, NULL),
(385, 180, 1179, '31.07.2015 12:41', '31.07.2015 12:41', NULL, NULL, NULL, NULL, NULL, NULL),
(386, 181, 1179, '31.07.2015 12:41', '31.07.2015 12:41', NULL, NULL, NULL, NULL, NULL, NULL),
(387, 181, 1175, 'olimp.jpg', '5-cup.jpg', NULL, NULL, 'olimp.jpg', '5-cup.jpg', 'c89eef76-623e-4cfe-bae4-dd2d8ef58804.jpg', '9f9f0da8-6c05-48c7-afb0-4d831f618188.jpg'),
(388, 183, 547, '18.03.2016 10:06', '18.03.2016 10:06', NULL, NULL, NULL, NULL, NULL, NULL),
(389, 183, 1414, NULL, '4_vere.png', NULL, NULL, NULL, '4_vere.png', NULL, '516c169f-e687-4b9f-8b90-fd2cf98c4827.png'),
(390, 184, 547, '27.11.2015 15:00', '27.11.2015 15:00', NULL, NULL, NULL, NULL, NULL, NULL),
(391, 184, 288, '<p>No Ķeguma dzelzceļa stacijas līdz Latvenergo koncerna Enerģētikas muzejam, enerģiski soļojot, var nokļūt apmēram 20 minūtēs. &Scaron;o ceļu, papildinot līdz&scaron;inējo apmeklētāju klāstu no skolām, mērot varēs skolēni no visas Latvijas, jo AS &bdquo;Pasažieru vilciens&rdquo; uzsāks projektu&ldquo;Ar vilcienu uz Enerģētikas muzeju Ķegumā&rdquo;,kura ietvaros izglītības iestāžu audzēkņi, izmantojot ievērojamu atlaidi,&nbsp; uz muzeju varēs doties ar vilcienu. Projekta atklā&scaron;anas pasākums noritēja &scaron;odien, 27. novembrī.</p>\r\n<p>AS &ldquo;Pasažieru vilciens&rdquo; projektu uzsāka, lai popularizētu pasažieru pārvadājumus pa dzelzceļu un piedāvātu alternatīvu ierastajiem autobusa braucieniem, kas parasti tiek izmantoti skolēnu izzino&scaron;o vai izklaidējo&scaron;o ekskursiju organizē&scaron;anai. Pirmais projekta sadarbības partneris ir Latvenergo koncerna Enerģētikas muzejs Ķegumā &ndash; vieta, kas nodro&scaron;ina pla&scaron;as izglītības un elektrodro&scaron;ības programmas bērniem un jaunie&scaron;iem.</p>\r\n<p>Ina Lastovecka, muzeja vadītāja: &ldquo;Domājot par &scaron;o sadarbību, man piemērotākais salīdzinājums &scaron;ķiet tīkls &ndash; gan elektrības, gan dzelzceļa tīkls apvieno Latvijas apdzīvotās vietas, pilsētas. Muzejā mēs esam uzņēmu&scaron;i viesus no daudzām Latvijas vietām un es ticu, ka tagad tām pievienosies jaunas pilsētas, no kurām skolēni uz &scaron;ejieni būs atbrauku&scaron;i ar vilcienu.&rdquo;</p>\r\n<p>&Scaron;is pakalpojums būs pieejams līdz mācību gada beigām &ndash; 2016.gada 17.jūnijam, sazinoties ar AS &ldquo;Pasažieru vilciens&rdquo;:&nbsp; zvanot pa tālruni 67234636 vai 25964716 vai sūtot pieteikumus elektroniski uz e-pastu sabine.ozolina@pv.lv</p>\r\n<p>Braucienam vilcienā skolēniem un pavado&scaron;ajām personām tiek piemērota 50% atlaide un Enerģētikas muzeja apmeklējums gida pavadībā, kā ierasts,&nbsp; bez maksas.</p>', '<p>No Ķeguma dzelzceļa stacijas līdz Latvenergo koncerna Enerģētikas muzejam, enerģiski soļojot, var nokļūt apmēram 20 minūtēs. &Scaron;o ceļu, papildinot līdz&scaron;inējo apmeklētāju klāstu no skolām, mērot varēs<span> skolēni no visas Latvijas, jo AS &bdquo;Pasažieru vilciens&rdquo; uzsāks projektu&ldquo;Ar vilcienu uz Enerģētika</span>s muzeju Ķegumā&rdquo;,kura ietvaros izglītības iestāžu audzēkņi, izmantojot ievērojamu atlaidi,&nbsp; uz muzeju varēs doties ar vilcienu. Projekta atklā&scaron;anas pasākums noritēja &scaron;odien, 27. novembrī.</p>\r\n<p>AS &ldquo;Pasažieru vilciens&rdquo; projektu uzsāka, lai popularizētu pasažieru pārvadājumus pa dzelzceļu un piedāvātu alternatīvu ierastajiem autobusa braucieniem, kas parasti tiek izmantoti skolēnu izzino&scaron;o vai <span style="color: #993300;">izklaidējo&scaron;o ekskursiju organizē&scaron;anai. Pirmais projekta sadarbības partneris ir Latvenergo koncerna Enerģētikas muzejs Ķegumā &ndash; vieta, kas nodro&scaron;ina pla&scaron;as izglītības un elektrodro&scaron;īb</span>as programmas bērniem un jaunie&scaron;iem.</p>\r\n<p>Ina Lastovecka, muzeja vadītāja: &ldquo;Domājot par &scaron;o sadarbību, man piemērotākais salīdzinājums &scaron;ķiet tīkls &ndash; gan elektrības, gan dzelzceļa tīkls apvieno Latvijas apdzīvotās vietas, pilsētas. Muzejā mēs esam uzņēmu&scaron;i viesus no daudzām Latvijas vietām un es ticu, ka tagad tām pievienosies jaunas pilsētas, no kurām skolēni uz &scaron;ejieni būs atbrauku&scaron;i ar vilcienu.&rdquo;</p>\r\n<p>&Scaron;is pakalpojums būs pieejams līdz mācību gada beigām &ndash; 2016.gada 17.jūnijam, sazinoties ar AS &ldquo;Pasažieru vilciens&rdquo;:&nbsp; zvanot pa tālruni 67234636 vai 25964716 vai sūtot pieteikumus elektroniski uz e-pastu sabine.ozolina@pv.lv</p>\r\n<p>Braucienam vilcienā skolēniem un pavado&scaron;ajām personām tiek piemērota 50% atlaide un Enerģētikas muzeja apmeklējums gida pavadībā, kā ierasts,&nbsp; bez maksas.</p>', NULL, NULL, NULL, NULL, NULL, NULL),
(392, 185, 547, '27.11.2015 15:00', '27.11.2015 15:00', NULL, NULL, NULL, NULL, NULL, NULL),
(393, 185, 288, '<p>No Ķeguma dzelzceļa stacijas līdz Latvenergo koncerna Enerģētikas muzejam, enerģiski soļojot, var nokļūt apmēram 20 minūtēs. &Scaron;o ceļu, papildinot līdz&scaron;inējo apmeklētāju klāstu no skolām, mērot varēs<span> skolēni no visas Latvijas, jo AS &bdquo;Pasažieru vilciens&rdquo; uzsāks projektu&ldquo;Ar vilcienu uz Enerģētika</span>s muzeju Ķegumā&rdquo;,kura ietvaros izglītības iestāžu audzēkņi, izmantojot ievērojamu atlaidi,&nbsp; uz muzeju varēs doties ar vilcienu. Projekta atklā&scaron;anas pasākums noritēja &scaron;odien, 27. novembrī.</p>\r\n<p>AS &ldquo;Pasažieru vilciens&rdquo; projektu uzsāka, lai popularizētu pasažieru pārvadājumus pa dzelzceļu un piedāvātu alternatīvu ierastajiem autobusa braucieniem, kas parasti tiek izmantoti skolēnu izzino&scaron;o vai <span style="color: #993300;">izklaidējo&scaron;o ekskursiju organizē&scaron;anai. Pirmais projekta sadarbības partneris ir Latvenergo koncerna Enerģētikas muzejs Ķegumā &ndash; vieta, kas nodro&scaron;ina pla&scaron;as izglītības un elektrodro&scaron;īb</span>as programmas bērniem un jaunie&scaron;iem.</p>\r\n<p>Ina Lastovecka, muzeja vadītāja: &ldquo;Domājot par &scaron;o sadarbību, man piemērotākais salīdzinājums &scaron;ķiet tīkls &ndash; gan elektrības, gan dzelzceļa tīkls apvieno Latvijas apdzīvotās vietas, pilsētas. Muzejā mēs esam uzņēmu&scaron;i viesus no daudzām Latvijas vietām un es ticu, ka tagad tām pievienosies jaunas pilsētas, no kurām skolēni uz &scaron;ejieni būs atbrauku&scaron;i ar vilcienu.&rdquo;</p>\r\n<p>&Scaron;is pakalpojums būs pieejams līdz mācību gada beigām &ndash; 2016.gada 17.jūnijam, sazinoties ar AS &ldquo;Pasažieru vilciens&rdquo;:&nbsp; zvanot pa tālruni 67234636 vai 25964716 vai sūtot pieteikumus elektroniski uz e-pastu sabine.ozolina@pv.lv</p>\r\n<p>Braucienam vilcienā skolēniem un pavado&scaron;ajām personām tiek piemērota 50% atlaide un Enerģētikas muzeja apmeklējums gida pavadībā, kā ierasts,&nbsp; bez maksas.</p>', '<p><span style="background-color: #ff6600;">No Ķeguma</span> dzelzceļa stacijas līdz Latvenergo koncerna Enerģētikas muzejam, enerģiski soļojot, var nokļūt apmēram 20 minūtēs. &Scaron;o ceļu, papildinot līdz&scaron;inējo apmeklētāju klāstu no skolām, mērot varēs<span> skolēni no visas Latvijas, jo AS &bdquo;Pasažieru vilciens&rdquo; uzsāks projektu&ldquo;Ar vilcienu uz Enerģētika</span>s muzeju Ķegumā&rdquo;,kura ietvaros izglītības iestāžu audzēkņi, izmantojot ievērojamu atlaidi,&nbsp; uz muzeju varēs doties ar vilcienu. Projekta atklā&scaron;anas pasākums noritēja &scaron;odien, 27. novembrī.</p>\r\n<p>AS &ldquo;Pasažieru vilciens&rdquo; projektu uzsāka, lai popularizētu pasažieru pārvadājumus pa dzelzceļu un piedāvātu alternatīvu ierastajiem autobusa braucieniem, kas parasti tiek izmantoti skolēnu izzino&scaron;o vai <span style="color: #993300;">izklaidējo&scaron;o ekskursiju organizē&scaron;anai. Pirmais projekta sadarbības partneris ir Latvenergo koncerna Enerģētikas muzejs Ķegumā &ndash; vieta, kas nodro&scaron;ina pla&scaron;as izglītības un elektrodro&scaron;īb</span>as programmas bērniem un jaunie&scaron;iem.</p>\r\n<p>Ina Lastovecka, muzeja vadītāja: &ldquo;Domājot par &scaron;o sadarbību, man piemērotākais salīdzinājums &scaron;ķiet tīkls &ndash; gan elektrības, gan dzelzceļa tīkls apvieno Latvijas apdzīvotās vietas, pilsētas. Muzejā mēs esam uzņēmu&scaron;i viesus no daudzām Latvijas vietām un es ticu, ka tagad tām pievienosies jaunas pilsētas, no kurām skolēni uz &scaron;ejieni būs atbrauku&scaron;i ar vilcienu.&rdquo;</p>\r\n<p>&Scaron;is pakalpojums būs pieejams līdz mācību gada beigām &ndash; 2016.gada 17.jūnijam, sazinoties ar AS &ldquo;Pasažieru vilciens&rdquo;:&nbsp; zvanot pa tālruni 67234636 vai 25964716 vai sūtot pieteikumus elektroniski uz e-pastu sabine.ozolina@pv.lv</p>\r\n<p>Braucienam vilcienā skolēniem un pavado&scaron;ajām personām tiek piemērota 50% atlaide un Enerģētikas muzeja apmeklējums gida pavadībā, kā ierasts,&nbsp; bez maksas.</p>', NULL, NULL, NULL, NULL, NULL, NULL),
(394, 186, 547, '27.11.2015 15:00', '27.11.2015 15:00', NULL, NULL, NULL, NULL, NULL, NULL),
(395, 187, 147, NULL, '100', NULL, NULL, NULL, NULL, NULL, NULL),
(396, 188, 147, NULL, '100', NULL, NULL, NULL, NULL, NULL, NULL),
(397, 189, 147, '1000', '100', NULL, NULL, NULL, NULL, NULL, NULL),
(398, 191, 27, 'Video galerija', 'Attēlu galerija', 1398, 1399, NULL, NULL, NULL, NULL),
(399, 193, 1179, '16.03.2016 14:11', '16.03.2016 14:11', NULL, NULL, NULL, NULL, NULL, NULL),
(400, 193, 1178, 'asjd jkajsd jkasldj klaJDKL Ad ', 'asjd jkajsd jkasldj klaJDKL Ad 1', NULL, NULL, NULL, NULL, NULL, NULL),
(401, 194, 547, '27.11.2015 15:00', '27.11.2015 15:00', NULL, NULL, NULL, NULL, NULL, NULL),
(402, 194, 1399, NULL, 'Pasniegta Gada balva enerģētikā', NULL, 23, NULL, NULL, NULL, NULL),
(403, 195, 43, 'Attēlu galerija', NULL, 150, NULL, NULL, NULL, NULL, NULL),
(404, 195, 44, 'Visas attēlu galerijas', NULL, 189, NULL, NULL, NULL, NULL, NULL),
(405, 195, 45, 'Saistītā ziņa', NULL, 1415, NULL, NULL, NULL, NULL, NULL),
(406, 195, 49, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(407, 195, 48, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(408, 195, 47, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(409, 196, 17, 'Visas attēlu galerijas', NULL, 189, NULL, NULL, NULL, NULL, NULL),
(410, 196, 20, 'id', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(411, 196, 21, 'Saistītais ieraksts', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(412, 196, 22, 'Saistītā ziņa', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(413, 196, 23, 'Saistītā ziņa', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(414, 196, 24, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(415, 196, 25, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(416, 196, 26, 'Visas ziņas', NULL, 60, NULL, NULL, NULL, NULL, NULL),
(417, 196, 27, 'Attēlu galerija', NULL, 1399, NULL, NULL, NULL, NULL, NULL),
(418, 196, 262, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(419, 196, 263, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(420, 196, 724, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(421, 196, 1405, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(422, 196, 1408, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(423, 197, 187, NULL, 'Dilstoši', NULL, 2, NULL, NULL, NULL, NULL),
(424, 198, 187, NULL, 'Dilstoši', NULL, 2, NULL, NULL, NULL, NULL),
(425, 199, 187, NULL, 'Dilstoši', NULL, 2, NULL, NULL, NULL, NULL),
(426, 200, 287, 'Tests 33', 'Tests 337890123456789621', NULL, NULL, NULL, NULL, NULL, NULL),
(427, 200, 1412, 'tests-33', 'tests-337890123456789621', NULL, NULL, NULL, NULL, NULL, NULL),
(428, 200, 547, '18.03.2016 10:06', '18.03.2016 10:06', NULL, NULL, NULL, NULL, NULL, NULL),
(429, 201, 547, '18.03.2016 10:06', '18.03.2016 10:06', NULL, NULL, NULL, NULL, NULL, NULL),
(430, 201, 546, NULL, 'reerer', NULL, NULL, NULL, NULL, NULL, NULL),
(431, 202, 547, '18.03.2016 10:06', '18.03.2016 10:06', NULL, NULL, NULL, NULL, NULL, NULL),
(432, 202, 546, 'reerer', 'reererrer', NULL, NULL, NULL, NULL, NULL, NULL),
(433, 203, 25, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(434, 204, 547, '11.04.2015 10:04', '11.04.2015 10:04', NULL, NULL, NULL, NULL, NULL, NULL),
(435, 204, 1398, NULL, 'Latvenergo zaļās obligācijas saņem Moody’s kredītreitingu', NULL, 4, NULL, NULL, NULL, NULL),
(436, 204, 1399, NULL, 'Uz Enerģētikas muzeju Ķegumā - ar vilcienu! ', NULL, 27, NULL, NULL, NULL, NULL),
(437, 205, 1394, NULL, 'Satura administrēšana', NULL, 4, NULL, NULL, NULL, NULL),
(438, 205, 1397, NULL, 'Šis reģistrs izmantot tos pašus datus ko Ziņu reģistrs. Reģistrs nodrošina galeriju sadaļas "Izmantots ziņās" funkcionalitāti.', NULL, NULL, NULL, NULL, NULL, NULL),
(439, 206, 31, '0', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(440, 208, 68, 'Visas ziņas', 'Visas ziņas - sasaiste', 60, 234, NULL, NULL, NULL, NULL),
(441, 208, 69, 'Attēlu galerija', 'Attēlu galerija', 1399, 1431, NULL, NULL, NULL, NULL),
(442, 209, 68, 'Visas ziņas', 'Visas ziņas - sasaiste', 60, 234, NULL, NULL, NULL, NULL),
(443, 209, 69, 'Video galerija', 'Video galerija', 1398, 1432, NULL, NULL, NULL, NULL),
(444, 214, 547, '11.04.2015 10:04', '11.04.2015 10:04', NULL, NULL, NULL, NULL, NULL, NULL),
(445, 216, 547, '16.03.2016 13:30', '31.03.2016 13:30', NULL, NULL, NULL, NULL, NULL, NULL),
(446, 217, 547, '31.03.2016 13:30', '15.03.2016 13:30', NULL, NULL, NULL, NULL, NULL, NULL),
(447, 218, 547, '15.03.2016 13:30', '31.03.2016 13:30', NULL, NULL, NULL, NULL, NULL, NULL),
(448, 232, 101, 'Renārs Osis', NULL, 18, NULL, NULL, NULL, NULL, NULL),
(449, 232, 102, 'Sistēmas pārvaldība', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(450, 235, 1, 'Visas ziņas', 'Ziņas', NULL, NULL, NULL, NULL, NULL, NULL),
(451, 236, 30, 'Visas ziņas', 'Ziņas', NULL, NULL, NULL, NULL, NULL, NULL),
(452, 237, 1, 'Visas attēlu galerijas', 'Attēlu galerijas', NULL, NULL, NULL, NULL, NULL, NULL),
(453, 238, 30, 'Visas attēlu galerijas', 'Attēlu galerijas', NULL, NULL, NULL, NULL, NULL, NULL),
(454, 239, 1, 'Visas video galerijas', 'Video galerijas', NULL, NULL, NULL, NULL, NULL, NULL),
(455, 239, 1397, NULL, 'Video galeriju reģistrs. Video galerijai var pievienot MP4 video datnes un/vai Youtube saites.', NULL, NULL, NULL, NULL, NULL, NULL),
(456, 240, 1, 'Visas ziņas - sasaiste', 'Ziņas - galeriju sasaiste', NULL, NULL, NULL, NULL, NULL, NULL),
(457, 241, 30, 'Ziņas galerijām', 'Ziņas - galeriju sasaiste', NULL, NULL, NULL, NULL, NULL, NULL),
(458, 242, 30, 'Visas video galerijas', 'Video galerijas', NULL, NULL, NULL, NULL, NULL, NULL),
(459, 243, 1397, NULL, 'Intranet lapās ievietoto satura bloku reģistrs. Reģistrs paredzēts portāla satura administratoriem, tāpēc reģistra formā var mainīt tikai satura lauku.', NULL, NULL, NULL, NULL, NULL, NULL),
(460, 244, 30, 'Satura bloki - visi uzņēmumi', 'Satura bloki', NULL, NULL, NULL, NULL, NULL, NULL),
(461, 245, 1394, NULL, 'SVS administrēšana un konfigurācija', NULL, 2, NULL, NULL, NULL, NULL),
(462, 245, 1397, NULL, 'Reģistrs paredzēts SVS administratoriem, lai pārvaldītu portālā ievietoto satura bloku uzstādījumus.', NULL, NULL, NULL, NULL, NULL, NULL),
(463, 248, 105, 'Satura bloki', NULL, 194, NULL, NULL, NULL, NULL, NULL),
(464, 248, 104, 'Sistēmas pārvaldība', NULL, 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `dx_db_history` (`id`, `event_id`, `field_id`, `old_val_txt`, `new_val_txt`, `old_val_rel_id`, `new_val_rel_id`, `old_val_file_name`, `new_val_file_name`, `old_val_file_guid`, `new_val_file_guid`) VALUES
(465, 248, 428, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(466, 248, 106, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(467, 248, 427, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(468, 249, 1194, '[40] Lapas', '[10] Lapas', NULL, NULL, NULL, NULL, NULL, NULL),
(469, 249, 53, '[210] Visi uzņēmumi', '[270] Portāla iestatījumi', 180, 156, NULL, NULL, NULL, NULL),
(470, 249, 55, '40', '10', NULL, NULL, NULL, NULL, NULL, NULL),
(471, 250, 1194, '[30] HTML bloki', '[650] HTML bloki', NULL, NULL, NULL, NULL, NULL, NULL),
(472, 250, 53, '[210] Visi uzņēmumi', '[270] Portāla iestatījumi', 180, 156, NULL, NULL, NULL, NULL),
(473, 250, 55, '30', '650', NULL, NULL, NULL, NULL, NULL, NULL),
(474, 251, 1194, '[210] Visi uzņēmumi', '[210] Portāla saturs', NULL, NULL, NULL, NULL, NULL, NULL),
(475, 251, 52, 'Visi uzņēmumi', 'Portāla saturs', NULL, NULL, NULL, NULL, NULL, NULL),
(476, 252, 1397, NULL, 'Reģistrs nodrošina portāla lapu pārvaldību. Ir iespējams norādīt lapu HTML un tajā ievietot blokus, izmantojot speciālus kodus.', NULL, NULL, NULL, NULL, NULL, NULL),
(477, 253, 1194, '[100] Izvēlne', '[100] Izvēlnes', NULL, NULL, NULL, NULL, NULL, NULL),
(478, 253, 52, 'Izvēlne', 'Izvēlnes', NULL, NULL, NULL, NULL, NULL, NULL),
(479, 254, 1194, '[600] Konfigurācija', '[1000] Konfigurācija', NULL, NULL, NULL, NULL, NULL, NULL),
(480, 254, 55, '600', '1000', NULL, NULL, NULL, NULL, NULL, NULL),
(481, 255, 1194, '[10] Lapas', '[900] Lapas', NULL, NULL, NULL, NULL, NULL, NULL),
(482, 255, 55, '10', '900', NULL, NULL, NULL, NULL, NULL, NULL),
(483, 256, 1395, NULL, 'Ja datu avots netiek norādīts, tad bloka saturu varēs norādīt tikai lietotāji, kuriem būs piekļuve visiem datu avotiem.', NULL, NULL, NULL, NULL, NULL, NULL),
(484, 257, 49, '0', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(485, 258, 49, '0', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(486, 259, 43, 'Satura bloks', NULL, 155, NULL, NULL, NULL, NULL, NULL),
(487, 259, 44, 'Satura bloki', NULL, 194, NULL, NULL, NULL, NULL, NULL),
(488, 259, 45, 'Ir redaktors', NULL, 1225, NULL, NULL, NULL, NULL, NULL),
(489, 259, 49, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(490, 259, 48, '45', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(491, 259, 47, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(492, 260, 49, '0', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(493, 261, 43, 'Satura bloks', NULL, 155, NULL, NULL, NULL, NULL, NULL),
(494, 261, 44, 'Satura bloki', NULL, 194, NULL, NULL, NULL, NULL, NULL),
(495, 261, 45, 'Ir bez rāmja', NULL, 1224, NULL, NULL, NULL, NULL, NULL),
(496, 261, 49, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(497, 261, 48, '80', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(498, 261, 47, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(499, 262, 43, 'Satura bloks', NULL, 155, NULL, NULL, NULL, NULL, NULL),
(500, 262, 44, 'Satura bloki', NULL, 194, NULL, NULL, NULL, NULL, NULL),
(501, 262, 45, 'Ir aktīvs', NULL, 1223, NULL, NULL, NULL, NULL, NULL),
(502, 262, 49, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(503, 262, 48, '90', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(504, 262, 47, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(505, 267, 1194, '[220] Latvenergo', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(506, 267, 53, '[200] Administrācija', NULL, 155, NULL, NULL, NULL, NULL, NULL),
(507, 267, 52, 'Latvenergo', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(508, 267, 55, '220', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(509, 268, 1194, '[230] Sadales tīkls', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(510, 268, 53, '[200] Administrācija', NULL, 155, NULL, NULL, NULL, NULL, NULL),
(511, 268, 52, 'Sadales tīkls', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(512, 268, 55, '230', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(513, 269, 1194, '[240] Elektrum', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(514, 269, 53, '[200] Administrācija', NULL, 155, NULL, NULL, NULL, NULL, NULL),
(515, 269, 52, 'Elektrum', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(516, 269, 55, '240', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(517, 270, 1, 'Formu JavaScript', 'Formu JavaScript1', NULL, NULL, NULL, NULL, NULL, NULL),
(518, 271, 1, 'Formu JavaScript1', 'Formu JavaScript', NULL, NULL, NULL, NULL, NULL, NULL),
(519, 272, 181, '0', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(520, 273, 37, 'Lomas', NULL, 22, NULL, NULL, NULL, NULL, NULL),
(521, 273, 50, 'Lomas', NULL, 20, NULL, NULL, NULL, NULL, NULL),
(522, 273, 38, 'Ir sistēmas', NULL, 93, NULL, NULL, NULL, NULL, NULL),
(523, 273, 39, '100', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(524, 273, 40, '3', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(525, 273, 41, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(526, 273, 195, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(527, 273, 181, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(528, 275, 187, NULL, 'Augoši', NULL, 1, NULL, NULL, NULL, NULL),
(529, 276, 22, 'Ir dzēšanas tiesības', 'Dzēšana', NULL, NULL, NULL, NULL, NULL, NULL),
(530, 277, 22, 'Var ievadīt jaunu', 'Jauns', NULL, NULL, NULL, NULL, NULL, NULL),
(531, 278, 181, '0', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(532, 279, 41, '0', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(533, 280, 40, '4', '20', NULL, NULL, NULL, NULL, NULL, NULL),
(534, 283, 94, 'Latvenrego intranet lietotāji', 'Intranet portāla lietotāji - pieejamas publiskās lapas.', NULL, NULL, NULL, NULL, NULL, NULL),
(535, 298, 428, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(536, 298, 106, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(537, 298, 427, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(538, 299, 428, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(539, 299, 106, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(540, 299, 427, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(541, 302, 6, 'Sistēmas incidenti', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(542, 302, 5, 'in_incidents', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(543, 302, 137, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(544, 303, 1394, 'Satura administrēšana', 'SVS administrēšana un konfigurācija', 4, 2, NULL, NULL, NULL, NULL),
(545, 305, 428, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(546, 305, 427, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(547, 306, 49, '0', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(548, 307, 49, '0', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(549, 308, 187, NULL, 'Dilstoši', NULL, 2, NULL, NULL, NULL, NULL),
(550, 309, 137, '0', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(551, 310, 1210, 'Leports', 'Leports1', NULL, NULL, NULL, NULL, NULL, NULL),
(552, 311, 1210, 'Leports1', 'Leports', NULL, NULL, NULL, NULL, NULL, NULL),
(553, 314, 49, '0', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(554, 315, 428, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(555, 315, 427, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(556, 316, 428, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(557, 316, 106, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(558, 316, 427, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(559, 320, 428, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(560, 320, 106, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(561, 320, 427, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(562, 321, 428, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(563, 321, 106, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(564, 321, 427, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(565, 324, 629, 'Tabula', 'Tabula ar SQL', 1, 9, NULL, NULL, NULL, NULL),
(566, 324, 653, NULL, 'select id, login_name, password, email, display_name, position_title, ad_login, source_id from dx_users WHERE id not in (SELECT id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)', NULL, NULL, NULL, NULL, NULL, NULL),
(567, 325, 94, 'Tiesības administrēt lietotājus un to tiesības', 'Tiesības administrēt lietotājus - veidot jaunus lietotājus, piesaistīt tos lomām.', NULL, NULL, NULL, NULL, NULL, NULL),
(568, 326, 624, 'TEST TABULA', NULL, 12, NULL, NULL, NULL, NULL, NULL),
(569, 326, 623, 'Sistēmas pārvaldība', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(570, 327, 624, 'Latvenergo', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(571, 327, 623, 'Sistēmas pārvaldība', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(572, 328, 624, 'Ieraksta informācija', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(573, 328, 623, 'Sistēmas pārvaldība', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(574, 329, 624, 'Sākums', NULL, 3, NULL, NULL, NULL, NULL, NULL),
(575, 329, 623, 'Sistēmas pārvaldība', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(576, 330, 624, 'Elektrum', NULL, 5, NULL, NULL, NULL, NULL, NULL),
(577, 330, 623, 'Sistēmas pārvaldība', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(578, 331, 624, 'Sadales tīkli', NULL, 6, NULL, NULL, NULL, NULL, NULL),
(579, 331, 623, 'Sistēmas pārvaldība', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(580, 332, 624, 'Dzimšanas dienas šodien', NULL, 7, NULL, NULL, NULL, NULL, NULL),
(581, 332, 623, 'Sistēmas pārvaldība', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(582, 333, 624, 'Dzimšanas dienas šomēnes', NULL, 8, NULL, NULL, NULL, NULL, NULL),
(583, 333, 623, 'Sistēmas pārvaldība', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(584, 334, 624, 'Personāls', NULL, 9, NULL, NULL, NULL, NULL, NULL),
(585, 334, 623, 'Sistēmas pārvaldība', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(586, 335, 624, 'Darbinieku izmaiņas', NULL, 10, NULL, NULL, NULL, NULL, NULL),
(587, 335, 623, 'Sistēmas pārvaldība', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(588, 336, 624, 'Attēlu galerijas', NULL, 11, NULL, NULL, NULL, NULL, NULL),
(589, 336, 623, 'Sistēmas pārvaldība', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(590, 337, 624, 'Izdevumi', NULL, 13, NULL, NULL, NULL, NULL, NULL),
(591, 337, 623, 'Sistēmas pārvaldība', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(592, 338, 624, 'Biežāk uzdotie jautājumi', NULL, 14, NULL, NULL, NULL, NULL, NULL),
(593, 338, 623, 'Sistēmas pārvaldība', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(594, 339, 624, 'Kalendārs', NULL, 15, NULL, NULL, NULL, NULL, NULL),
(595, 339, 623, 'Sistēmas pārvaldība', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(596, 340, 653, 'select id, login_name, password, email, display_name, position_title, ad_login, source_id from dx_users WHERE id not in (SELECT id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)', 'select id, login_name, password, email, dx_users.display_name as dx_users_display_name, display_name, position_title, ad_login, source_id from dx_users WHERE id not in (SELECT id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)', NULL, NULL, NULL, NULL, NULL, NULL),
(597, 341, 653, 'select id, login_name, password, email, dx_users.display_name as dx_users_display_name, display_name, position_title, ad_login, source_id from dx_users WHERE id not in (SELECT id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)', 'select dx_users.id as id, login_name, password, email, dx_users.display_name as dx_users_display_name, display_name, position_title, ad_login, source_id from dx_users WHERE id not in (SELECT id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)', NULL, NULL, NULL, NULL, NULL, NULL),
(598, 342, 653, 'select dx_users.id as id, login_name, password, email, dx_users.display_name as dx_users_display_name, display_name, position_title, ad_login, source_id from dx_users WHERE id not in (SELECT id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)', 'select * from (select dx_users.id as id, login_name, password, email, dx_users.display_name as dx_users_display_name, display_name, position_title, ad_login, source_id from dx_users WHERE id not in (SELECT id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)) t where 1=1', NULL, NULL, NULL, NULL, NULL, NULL),
(599, 346, 629, 'Tabula', 'Tabula ar SQL', 1, 9, NULL, NULL, NULL, NULL),
(600, 346, 653, NULL, 'select * from (select dx_roles.id as id, dx_roles.title as dx_roles_title, title, is_system, description WHERE id != 1 or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)) t WHERE 1=1', NULL, NULL, NULL, NULL, NULL, NULL),
(601, 347, 653, 'select * from (select dx_roles.id as id, dx_roles.title as dx_roles_title, title, is_system, description WHERE id != 1 or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)) t WHERE 1=1', 'select * from (select dx_roles.id as id, dx_roles.title as dx_roles_title, title, is_system, description FROM dx_roles WHERE id != 1 or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)) t WHERE 1=1', NULL, NULL, NULL, NULL, NULL, NULL),
(602, 354, 1401, 'test4343', NULL, 51, NULL, NULL, NULL, NULL, NULL),
(603, 354, 1402, '4_vere.png', NULL, NULL, NULL, '4_vere.png', NULL, 'bb0a7be6-0d02-4eb0-95f9-1c294ea0c780.png', NULL),
(604, 354, 1404, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(605, 355, 1231, 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(606, 355, 1232, 'test@janis.lv', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(607, 355, 1233, '19.01.2016 13:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(608, 356, 1231, 'tests', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(609, 356, 1232, 'janis@jjj.lv', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(610, 356, 1233, '19.01.2016 14:03', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(611, 357, 1231, 'Te vēl viens tests', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(612, 357, 1232, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(613, 357, 1233, '19.01.2016 14:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(614, 358, 1231, 'Un tagad tests ar e-pastu', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(615, 358, 1232, 'janis@janis.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(616, 358, 1233, '19.01.2016 14:10', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(617, 359, 1231, 'Tests', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(618, 359, 1232, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(619, 359, 1233, '19.01.2016 15:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(620, 360, 1231, 'Tests jajsksa', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(621, 360, 1232, 'Janis.supe@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(622, 360, 1233, '19.01.2016 15:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(623, 361, 1231, 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(624, 361, 1232, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(625, 361, 1233, '24.01.2016 17:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(626, 362, 1231, '123123', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(627, 362, 1232, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(628, 362, 1233, '26.01.2016 23:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(629, 363, 1231, 'xxx', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(630, 363, 1232, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(631, 363, 1233, '26.01.2016 23:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(632, 364, 1231, 'sad', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(633, 364, 1232, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(634, 364, 1233, '26.01.2016 23:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(635, 365, 1231, '232f2', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(636, 365, 1232, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(637, 365, 1233, '26.01.2016 23:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(638, 366, 1231, 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(639, 366, 1232, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(640, 366, 1233, '28.01.2016 00:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(641, 367, 1231, 'test3', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(642, 367, 1232, 'janis.supe@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(643, 367, 1233, '28.01.2016 00:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(644, 368, 1231, 'Mans tests 3', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(645, 368, 1232, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(646, 368, 1233, '28.01.2016 02:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(647, 369, 1231, 'Mans tests 4', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(648, 369, 1232, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(649, 369, 1233, '28.01.2016 02:13', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(650, 370, 1231, 'test', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(651, 370, 1232, 'janis.supe@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(652, 370, 1233, '06.02.2016 09:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(653, 371, 1231, 'Testa jaut', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(654, 371, 1232, 'janis.supe@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(655, 371, 1233, '09.02.2016 11:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(656, 372, 1231, 'Kāds ir tas tests', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(657, 372, 1232, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(658, 372, 1233, '20.02.2016 20:33', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(659, 373, 1231, 'Te ir tas tests', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(660, 373, 1232, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(661, 373, 1233, '20.02.2016 20:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(662, 374, 1231, 'testa tests', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(663, 374, 1232, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(664, 374, 1233, '21.02.2016 10:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(665, 375, 1231, 'uuu', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(666, 375, 1232, '', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(667, 375, 1233, '29.02.2016 18:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(668, 376, 1231, 'asdsd', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(669, 376, 1232, 'janis.supe@inbox.lv', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(670, 376, 1233, '14.03.2016 14:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(671, 377, 567, 'Testa notikums', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(672, 377, 568, 'Testa apraksts', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(673, 377, 569, 'd0698c7a-d8f7-4dbc-abd3-ef51b4b2fc7e.jpg', NULL, NULL, NULL, 'd0698c7a-d8f7-4dbc-abd3-ef51b4b2fc7e.jpg', NULL, '9de0f421-f3bb-4d1b-a6c5-dabf39b6bee0.jpg', NULL),
(674, 377, 570, '15.03.2016 14:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(675, 377, 571, '15.03.2016 14:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(676, 377, 572, 'Testa adrese', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(677, 377, 573, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(678, 378, 1312, 'Latvenergo vēstis', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(679, 378, 1313, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(680, 378, 1314, '05.01.2016', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(681, 378, 1315, '8ca5e5cc-1c77-4a13-9bbe-4d18e069f851.jpg', NULL, NULL, NULL, '8ca5e5cc-1c77-4a13-9bbe-4d18e069f851.jpg', NULL, '8ca5e5cc-1c77-4a13-9bbe-4d18e069f851.jpg', NULL),
(682, 378, 1317, 'NOLIKUMS_Leports_sist_ma_IPR_32.4_final.pdf', NULL, NULL, NULL, 'NOLIKUMS_Leports_sist_ma_IPR_32.4_final.pdf', NULL, 'NOLIKUMS_Leports_sist_ma_IPR_32.4_final.pdf', NULL),
(683, 378, 1319, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(684, 379, 1312, 'Enerģijas ziņas', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(685, 379, 1313, '2', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(686, 379, 1314, '05.01.2016', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(687, 379, 1315, '5b8601e9-cc90-431d-8133-c9f365a78eb0.jpg', NULL, NULL, NULL, '5b8601e9-cc90-431d-8133-c9f365a78eb0.jpg', NULL, '5b8601e9-cc90-431d-8133-c9f365a78eb0.jpg', NULL),
(688, 379, 1317, 'NOLIKUMS_Leports_sist_ma_IPR_32.4_final.pdf', NULL, NULL, NULL, 'NOLIKUMS_Leports_sist_ma_IPR_32.4_final.pdf', NULL, 'NOLIKUMS_Leports_sist_ma_IPR_32.4_final.pdf', NULL),
(689, 379, 1319, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(690, 380, 1312, 'Enerģijas ziņas', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(691, 380, 1313, '3', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(692, 380, 1314, '05.02.2016', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(693, 380, 1315, '4c97fccc-4de6-4b5d-8e23-44e11431d74e.JPG', NULL, NULL, NULL, '4c97fccc-4de6-4b5d-8e23-44e11431d74e.JPG', NULL, '4c97fccc-4de6-4b5d-8e23-44e11431d74e.JPG', NULL),
(694, 380, 1317, 'NOLIKUMS_Leports_sist_ma_IPR_32.4_final.pdf', NULL, NULL, NULL, 'NOLIKUMS_Leports_sist_ma_IPR_32.4_final.pdf', NULL, 'NOLIKUMS_Leports_sist_ma_IPR_32.4_final.pdf', NULL),
(695, 380, 1319, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(696, 381, 1310, 'Latvenergo vēstis', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(697, 382, 1310, 'Enerģijas ziņas', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(698, 383, 1310, 'Elektrodrošība', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(699, 384, 1310, 'Energo efektivitāte', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(700, 385, 1385, 'Oracle HR', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(701, 385, 1382, 'Demo apraksts', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(702, 385, 1384, '25.02.2016 12:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(703, 386, 1385, 'Oracle HR', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(704, 386, 1382, 'Demo ieraksts 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(705, 386, 1384, '23.02.2016 13:47', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(706, 388, 96, 'liene', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(707, 388, 97, '$2y$10$ihKoZkLpQ9ZE4swjK9U7F.Sh3pwgl1tbLxt0ZpPMO7neXr13fCQ3K', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(708, 388, 98, 'liene@inbox.lv', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(709, 388, 99, 'Liene Ozoliņa', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(710, 388, 260, 'Portāla administratore', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(711, 389, 96, 'evita', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(712, 389, 97, '$2y$10$ihKoZkLpQ9ZE4swjK9U7F.Sh3pwgl1tbLxt0ZpPMO7neXr13fCQ3K', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(713, 389, 98, 'evita@inbox.lv', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(714, 389, 99, 'Evita Kārkliņa', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(715, 389, 260, 'Projektu vadītāja', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(716, 390, 96, 'lana', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(717, 390, 97, '$2y$10$ihKoZkLpQ9ZE4swjK9U7F.Sh3pwgl1tbLxt0ZpPMO7neXr13fCQ3K', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(718, 390, 98, 'lana@inbox.lv', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(719, 390, 99, 'Lana Fedoroviča', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(720, 390, 260, 'Projektu vadītāja', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(721, 391, 96, 'oskars', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(722, 391, 97, '$2y$10$ihKoZkLpQ9ZE4swjK9U7F.Sh3pwgl1tbLxt0ZpPMO7neXr13fCQ3K', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(723, 391, 98, 'admin@latvenergo.lv', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(724, 391, 99, 'Oskars Ošenieks', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(725, 391, 260, 'Portāla administrators', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(726, 392, 96, 'maija', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(727, 392, 97, '$2y$10$ihKoZkLpQ9ZE4swjK9U7F.Sh3pwgl1tbLxt0ZpPMO7neXr13fCQ3K', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(728, 392, 98, 'liene@inbox.lv', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(729, 392, 99, 'Maija Bērziņa', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(730, 392, 260, 'Valdes priekšsēdētājs', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(731, 393, 96, 'aija', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(732, 393, 97, '$2y$10$ihKoZkLpQ9ZE4swjK9U7F.Sh3pwgl1tbLxt0ZpPMO7neXr13fCQ3K', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(733, 393, 98, 'liene@inbox.lv', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(734, 393, 99, 'Aija Spanovska', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(735, 393, 260, 'Valdes locekle finanšu jautājumos', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(736, 394, 96, 'svetlana', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(737, 394, 97, '$2y$10$ihKoZkLpQ9ZE4swjK9U7F.Sh3pwgl1tbLxt0ZpPMO7neXr13fCQ3K', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(738, 394, 98, 'liene@inbox.lv', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(739, 394, 99, 'Svetlana Leontjeva', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(740, 394, 260, 'Valdes locekle', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(741, 395, 96, 'energo', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(742, 395, 97, '$2y$10$ihKoZkLpQ9ZE4swjK9U7F.Sh3pwgl1tbLxt0ZpPMO7neXr13fCQ3K', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(743, 395, 98, 'liene@inbox.lv', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(744, 395, 99, 'Olga Dolgova', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(745, 395, 260, 'Biroja administratore', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(746, 396, 96, 'anita', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(747, 396, 97, '$2y$10$ihKoZkLpQ9ZE4swjK9U7F.Sh3pwgl1tbLxt0ZpPMO7neXr13fCQ3K', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(748, 396, 98, 'liene@inbox.lv', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(749, 396, 99, 'Anita Bērziņa', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(750, 396, 260, 'Valdes locekle', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(751, 402, 37, 'Lietotāji', NULL, 23, NULL, NULL, NULL, NULL, NULL),
(752, 402, 50, 'Lietotāji', NULL, 21, NULL, NULL, NULL, NULL, NULL),
(753, 402, 38, 'Datu avots', NULL, 1434, NULL, NULL, NULL, NULL, NULL),
(754, 402, 39, '100', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(755, 402, 40, '10', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(756, 402, 41, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(757, 402, 195, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(758, 402, 181, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(759, 403, 653, 'select * from (select dx_users.id as id, login_name, password, email, dx_users.display_name as dx_users_display_name, display_name, position_title, ad_login, source_id from dx_users WHERE id not in (SELECT id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)) t where 1=1', 'select * from (select dx_users.id as id, login_name, password, email, dx_users.display_name as dx_users_display_name, display_name, position_title, ad_login, in_sources.title as source_id from dx_users left join in_sources where dx_users.source_id = in_sources.id WHERE id not in (SELECT id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)) t where 1=1', NULL, NULL, NULL, NULL, NULL, NULL),
(760, 404, 653, 'select * from (select dx_users.id as id, login_name, password, email, dx_users.display_name as dx_users_display_name, display_name, position_title, ad_login, in_sources.title as source_id from dx_users left join in_sources where dx_users.source_id = in_sources.id WHERE id not in (SELECT id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)) t where 1=1', 'select * from (select dx_users.id as id, login_name, password, email, dx_users.display_name as dx_users_display_name, display_name, position_title, ad_login, in_sources.title as source_id from dx_users left join in_sources on dx_users.source_id = in_sources.id WHERE id not in (SELECT id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)) t where 1=1', NULL, NULL, NULL, NULL, NULL, NULL),
(761, 405, 653, 'select * from (select dx_users.id as id, login_name, password, email, dx_users.display_name as dx_users_display_name, display_name, position_title, ad_login, in_sources.title as source_id from dx_users left join in_sources on dx_users.source_id = in_sources.id WHERE id not in (SELECT id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)) t where 1=1', 'select * from (select dx_users.id as id, login_name, password, email, dx_users.display_name as dx_users_display_name, display_name, position_title, ad_login, in_sources.title as source_id from dx_users left join in_sources on dx_users.source_id = in_sources.id WHERE dx_users.id not in (SELECT id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)) t where 1=1', NULL, NULL, NULL, NULL, NULL, NULL),
(762, 408, 37, 'Lietotāji', NULL, 23, NULL, NULL, NULL, NULL, NULL),
(763, 408, 50, 'Lietotāji', NULL, 21, NULL, NULL, NULL, NULL, NULL),
(764, 408, 38, 'Datu avots', NULL, 1434, NULL, NULL, NULL, NULL, NULL),
(765, 408, 39, '100', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(766, 408, 40, '100', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(767, 408, 41, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(768, 408, 195, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(769, 408, 181, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(770, 410, 653, 'select * from (select dx_users.id as id, login_name, password, email, dx_users.display_name as dx_users_display_name, display_name, position_title, ad_login, in_sources.title as source_id from dx_users left join in_sources on dx_users.source_id = in_sources.id WHERE dx_users.id not in (SELECT id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)) t where 1=1', 'select * from (select dx_users.id as id, login_name, password, email, dx_users.display_name as dx_users_display_name, display_name, position_title, ad_login, in_sources.title as in_sources_1_title from dx_users left join in_sources on dx_users.source_id = in_sources.id WHERE dx_users.id not in (SELECT id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)) t where 1=1', NULL, NULL, NULL, NULL, NULL, NULL),
(771, 411, 97, '$2y$10$kS/UPfXmaguyJB1/q2ET2en.4NA6K/mz7muyFbXkvx0cPqWaAarHe', '$2y$10$S803OIF2qhP8ZliilBZI9eYGCMXhhvAkiWuXXcG3J.Pp2hnImjv2q', NULL, NULL, NULL, NULL, NULL, NULL),
(772, 411, 98, 'janis.supe@es.lv', 'janis.macans@latvenergo.lv', NULL, NULL, NULL, NULL, NULL, NULL),
(773, 411, 99, 'Jānis Supe', 'Jānis Mačāns', NULL, NULL, NULL, NULL, NULL, NULL),
(774, 412, 94, 'Tiesības izveidot sistēmas objektus - reģistrus, formas, skatus, izvēlnes u.c.', 'Tiesības izveidot sistēmas objektus - reģistrus, formas, skatus, izvēlnes u.c. Pilnas tiesības uz saturu. Šo lomu nedrīkst dzēst.', NULL, NULL, NULL, NULL, NULL, NULL),
(775, 413, 94, 'Tiesības izveidot sistēmas objektus - reģistrus, formas, skatus, izvēlnes u.c. Pilnas tiesības uz saturu. Šo lomu nedrīkst dzēst.', 'Tiesības izveidot sistēmas objektus - reģistrus, formas, skatus, izvēlnes u.c. Pilnas tiesības uz saturu. Šo lomu nedrīkst dzēst. Loma pieejama tikai šīs pašas lomas lietotājiem.', NULL, NULL, NULL, NULL, NULL, NULL),
(776, 414, 94, 'Tiesības izveidot sistēmas objektus - reģistrus, formas, skatus, izvēlnes u.c. Pilnas tiesības uz saturu. Šo lomu nedrīkst dzēst. Loma pieejama tikai šīs pašas lomas lietotājiem.', 'Tiesības izveidot sistēmas objektus - reģistrus, formas, skatus, izvēlnes u.c. Pilnas tiesības uz saturu. Tiesības veidot jaunas lomas un piesaistīt reģistrus lomām. Šo lomu nedrīkst dzēst. Loma pieejama tikai šīs pašas lomas lietotājiem.', NULL, NULL, NULL, NULL, NULL, NULL),
(777, 415, 94, 'Intranet portāla lietotāji - pieejamas publiskās lapas.', 'Intranet portāla lietotāji - pieejamas publiskās lapas. Veidojot jaunas publiskās portāla lapas, tām obligāti jāpiesaista šī loma.', NULL, NULL, NULL, NULL, NULL, NULL),
(778, 417, 260, 'Portāla administrators', 'Portāla satura administrators', NULL, NULL, NULL, NULL, NULL, NULL),
(779, 420, 1434, NULL, 'Sadales tīkls', NULL, 3, NULL, NULL, NULL, NULL),
(780, 429, 106, '0', '1', NULL, NULL, NULL, NULL, NULL, NULL),
(781, 436, 101, 'Jānis Mačāns', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(782, 436, 102, 'Lietotāju pārvaldība', NULL, 2, NULL, NULL, NULL, NULL, NULL),
(783, 437, 653, 'select * from (select dx_roles.id as id, dx_roles.title as dx_roles_title, title, is_system, description FROM dx_roles WHERE id != 1 or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)) t WHERE 1=1', 'select * from (select dx_roles.id as id, dx_roles.title as dx_roles_title, title, is_system, description FROM dx_roles WHERE id != 1 or [ME] in (SELECT user_id FROM dx_users_roles WHERE role_id = 1)) t WHERE 1=1', NULL, NULL, NULL, NULL, NULL, NULL),
(784, 438, 653, 'select * from (select dx_users.id as id, login_name, password, email, dx_users.display_name as dx_users_display_name, display_name, position_title, ad_login, in_sources.title as in_sources_1_title from dx_users left join in_sources on dx_users.source_id = in_sources.id WHERE dx_users.id not in (SELECT id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT id FROM dx_users_roles WHERE role_id = 1)) t where 1=1', 'select * from (select dx_users.id as id, login_name, password, email, dx_users.display_name as dx_users_display_name, display_name, position_title, ad_login, in_sources.title as in_sources_1_title from dx_users left join in_sources on dx_users.source_id = in_sources.id WHERE dx_users.id not in (SELECT user_id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT user_id FROM dx_users_roles WHERE role_id = 1)) t where 1=1', NULL, NULL, NULL, NULL, NULL, NULL),
(785, 460, 428, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(786, 460, 106, '1', '0', NULL, NULL, NULL, NULL, NULL, NULL),
(787, 461, 814, '52', NULL, 52, NULL, NULL, NULL, NULL, NULL),
(788, 461, 815, 'TEST2', NULL, 38, NULL, NULL, NULL, NULL, NULL),
(789, 463, 811, 'uuu', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(790, 464, 735, 'BIRTH_HEADER', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(791, 464, 731, 'Dzimšanas dienu lapas augša', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(792, 464, 904, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(793, 464, 733, '<h2><i class="fa fa-birthday-cake" style="color: #FAA42E;"></i> Sveicam dzimšanas dienā!</h2>\r\n<p><a href="/dzimsanas_dienas_sodien">Šodienas jubilāri</a></p>\r\n<p><a href="/dzimsanas_dienas_somenes">Dzimšanas diena šajā mēnesī</a></p>\r\n', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(794, 464, 734, 'Personāls', NULL, 4, NULL, NULL, NULL, NULL, NULL),
(795, 464, 737, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(796, 464, 736, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(797, 465, 735, 'EMPLOYEE', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(798, 465, 731, 'Darbinieku meklēšana - demo', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(799, 465, 904, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(800, 465, 733, '<div class="float-e-margins hblue hpanel">            \r\n	<div class="panel-body">\r\n		<h2><i class="fa fa-users"></i> Darbinieki</h2>\r\n		<div class="input-group" style="margin-top: 20px; margin-bottom: 10px;">\r\n			<input type="text" class="form-control" placeholder="Meklēt darbinieku..." id="doc_criteria">\r\n			<span class="input-group-btn">\r\n			  <button class="btn btn-default" type="button" id="btnSearchDoc"><i class="fa fa-search"></i></button>\r\n			</span>\r\n		</div>\r\n	</div>\r\n</div>', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(801, 465, 737, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(802, 465, 736, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(803, 466, 735, 'LOTUS', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(804, 466, 731, 'Meklēšana Lotus Notes - demo', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(805, 466, 904, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(806, 466, 733, '<div class="float-e-margins hblue hpanel">            \r\n	<div class="panel-body">\r\n		<h2><i class="fa fa-file-text"></i> Dokumenti</h2>\r\n		<div class="input-group" style="margin-top: 20px; margin-bottom: 10px;">\r\n			<input type="text" class="form-control" placeholder="Meklēt dokumentu..." id="doc_criteria">\r\n			<span class="input-group-btn">\r\n			  <button class="btn btn-default" type="button" id="btnSearchDoc"><i class="fa fa-search"></i></button>\r\n			</span>\r\n		</div>\r\n	</div>\r\n</div>', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(807, 466, 737, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(808, 466, 736, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(809, 467, 735, 'MONITOR1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(810, 467, 731, 'Sistēmu monitorings - brīdinājuma variants - demo', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(811, 467, 730, 'Sistēmu pieejamība', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(812, 467, 904, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(813, 467, 733, '<div style="margin-bottom: 10px">Sistēmas:</div>\r\n<p><a class="badge badge-danger" href="http://www.latvenergo.lv" target="_blank">Oracle HR</a></p>\r\n<p><a class="badge badge-warning" href="http://www.latvenergo.lv" target="_blank">Mega sistēma</a></p>\r\n<p><a class="badge badge-success" href="http://www.latvenergo.lv" target="_blank">Centrālā sistēma</a></p>\r\n<hr>\r\n<div><small>Kontroles laiks: 02.12.2015 09:45</small></div>', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(814, 467, 737, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(815, 467, 736, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(816, 468, 1440, 'BANER', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(817, 468, 1437, 'Baneris', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(818, 468, 1443, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(819, 468, 1439, '<p>BANERA INFORMĀCIJA</p>', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(820, 468, 1442, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(821, 468, 1441, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(822, 469, 1440, 'NODER', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(823, 469, 1437, 'Noderīga informācija - demo', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(824, 469, 1436, 'Noderīga informācija', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(825, 469, 1443, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(826, 469, 1439, '<p>Šis ir parauga teksta bloks</p>', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(827, 469, 1444, 'Latvenergo', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(828, 469, 1442, '0', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(829, 469, 1441, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(830, 470, 1440, 'PIEMERS', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(831, 470, 1437, 'Piemēra nosaukums', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(832, 470, 1436, 'Piemērs 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(833, 470, 1443, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(834, 470, 1439, '<p>Cits teksts lai ir!</p>', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(835, 470, 1444, 'Latvenergo', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(836, 470, 1442, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(837, 470, 1441, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(838, 471, 624, 'TEST TABULA', NULL, 12, NULL, NULL, NULL, NULL, NULL),
(839, 471, 623, 'Intranet lietotājs', NULL, 23, NULL, NULL, NULL, NULL, NULL),
(840, 472, 753, 'Latvenergo', NULL, 1, NULL, NULL, NULL, NULL, NULL),
(841, 472, 618, 'TEST TABULA', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(842, 472, 621, 'test_tabula', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(843, 472, 619, '[[OBJ=VIEW|VIEW_ID=139]]\r\n[[OBJ=VIEW|VIEW_ID=23]]', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(844, 472, 887, 'rgba(233,237,239,0.9)', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(845, 472, 620, '1', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_field_operations`
--

CREATE TABLE IF NOT EXISTS `dx_field_operations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL COMMENT 'Name of field operation - used in CMS interface',
  `sys_name` varchar(50) NOT NULL COMMENT 'System code of field operation - used in CMS PHP code',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Sistēmisks klasifikators ar iespējamām lauku operācijām, kas tiek izmantotas veidojot datu atlases nosacījumus.' AUTO_INCREMENT=8 ;

--
-- Dumping data for table `dx_field_operations`
--

INSERT INTO `dx_field_operations` (`id`, `title`, `sys_name`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Vienāds', '=', NULL, NULL, NULL, NULL),
(2, 'Nav vienāds', '!=', NULL, NULL, NULL, NULL),
(3, 'Lielāks par', '>', NULL, NULL, NULL, NULL),
(4, 'Mazāks par', '<', NULL, NULL, NULL, NULL),
(5, 'Ir viens no', ' IN', NULL, NULL, NULL, NULL),
(6, 'Ir nekas', ' IS NULL', NULL, NULL, NULL, NULL),
(7, 'Nav nekas', ' IS NOT NULL', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_field_represent`
--

CREATE TABLE IF NOT EXISTS `dx_field_represent` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `dx_field_represent`
--

INSERT INTO `dx_field_represent` (`id`, `title`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Virsraksts', NULL, NULL, NULL, NULL),
(2, 'Attēls', NULL, NULL, NULL, NULL),
(3, 'Publicēšanas datums/laiks', NULL, NULL, NULL, NULL),
(4, 'Anotācija', NULL, NULL, NULL, NULL),
(5, 'Pilns raksta teksts', NULL, NULL, NULL, NULL),
(6, 'Darbplūsma: reģistrācijas numurs', NULL, NULL, NULL, NULL),
(7, 'Darbplūsma: dokumenta saturs', NULL, NULL, NULL, NULL),
(8, 'Darbplūsma: dokumenta sagatavotājs', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_field_types`
--

CREATE TABLE IF NOT EXISTS `dx_field_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL COMMENT 'Field type name - used in CMS interface',
  `is_max_lenght` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Indicates if field with this type have string lenght limitation (0 - yes, 1 - no)',
  `is_integer` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Indicates if field with this type is integer (0 - yes, 1 - no)',
  `is_decimal` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Indicates if field with this type is decimal with 2 decimal places (0 - yes, 1 - no)',
  `is_date` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Indicates if fields with this type is date (0 - yes, 1 - no)',
  `sys_name` varchar(100) DEFAULT NULL COMMENT 'Field type system code - used in CMS PHP code',
  `is_readonly` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Indicates if field with this type is read only (0 - yes, 1 - no)',
  `height_px` int(11) NOT NULL DEFAULT '21' COMMENT 'Height in pixels - used to calculate generated forms total height',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `title` (`title`),
  KEY `sys_name` (`sys_name`),
  KEY `is_readonly` (`is_readonly`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Sistēmisks klasifikators ar reģistru lauku tipiem.' AUTO_INCREMENT=18 ;

--
-- Dumping data for table `dx_field_types`
--

INSERT INTO `dx_field_types` (`id`, `title`, `is_max_lenght`, `is_integer`, `is_decimal`, `is_date`, `sys_name`, `is_readonly`, `height_px`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Teksts', 1, 0, 0, 0, 'varchar', 0, 34, NULL, NULL, NULL, NULL),
(2, 'DatumsLaiks', 0, 0, 0, 1, 'datetime', 0, 34, NULL, NULL, NULL, NULL),
(3, 'Saistītais ieraksts', 0, 0, 0, 0, 'rel_id', 0, 34, NULL, NULL, NULL, NULL),
(4, 'Garš teksts', 0, 0, 0, 0, 'text', 0, 94, NULL, NULL, NULL, NULL),
(5, 'Skaitlis', 0, 1, 0, 0, 'int', 0, 34, NULL, NULL, NULL, NULL),
(6, 'ID', 0, 0, 0, 0, 'int', 1, 34, NULL, NULL, NULL, NULL),
(7, 'Jā/Nē', 0, 1, 0, 0, 'bool', 0, 21, NULL, NULL, NULL, NULL),
(8, 'Uzmeklēšanas ieraksts', 0, 0, 0, 0, 'autocompleate', 0, 34, NULL, NULL, NULL, NULL),
(9, 'Datums', 0, 0, 0, 0, 'date', 0, 34, NULL, NULL, NULL, NULL),
(10, 'HTML teksts', 0, 0, 0, 0, 'html_text', 0, 340, NULL, NULL, NULL, NULL),
(11, 'E-pasts', 1, 0, 0, 0, 'email', 0, 34, NULL, NULL, NULL, NULL),
(12, 'Datne', 0, 0, 0, 0, 'file', 0, 34, NULL, NULL, NULL, NULL),
(13, 'Reģistrācijas numurs', 0, 0, 0, 0, 'reg_nr', 0, 21, NULL, NULL, NULL, NULL),
(14, 'Daudzlīmeņu', 0, 0, 0, 0, 'tree', 0, 21, NULL, NULL, NULL, NULL),
(15, 'Programmēšanas kods', 0, 0, 0, 0, 'soft_code', 0, 21, NULL, NULL, NULL, NULL),
(16, 'Parole', 0, 0, 0, 0, 'password', 0, 21, NULL, NULL, NULL, NULL),
(17, 'Krāsa', 0, 0, 0, 0, 'color', 0, 21, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_files_headers`
--

CREATE TABLE IF NOT EXISTS `dx_files_headers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `extention` varchar(5) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Paplašinājums',
  `content_type` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Satura tips',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dx_files_headers_extention_unique` (`extention`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

--
-- Dumping data for table `dx_files_headers`
--

INSERT INTO `dx_files_headers` (`id`, `extention`, `content_type`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'xls', 'application/vnd.ms-excel', NULL, NULL, NULL, NULL),
(2, 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', NULL, NULL, NULL, NULL),
(3, 'pdf', 'application/pdf', NULL, NULL, NULL, NULL),
(4, 'docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', NULL, NULL, NULL, NULL),
(5, 'doc', 'application/msword', NULL, NULL, NULL, NULL),
(6, 'odt', 'application/vnd.oasis.opendocument.text', NULL, NULL, NULL, NULL),
(7, 'jpg', 'image/jpeg', NULL, NULL, NULL, NULL),
(8, 'png', 'image/png', NULL, NULL, NULL, NULL),
(9, 'gif', 'image/gif', NULL, NULL, NULL, NULL),
(10, 'txt', 'text/plain', NULL, NULL, NULL, NULL),
(11, 'mp4', 'video/mp4', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_forms`
--

CREATE TABLE IF NOT EXISTS `dx_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL COMMENT 'Reference to register',
  `title` varchar(100) DEFAULT NULL COMMENT 'Title of form - visible in CMS',
  `zones_count` int(11) NOT NULL DEFAULT '0' COMMENT 'Zones count of the form - fields can be grouped and displayed in zones',
  `width` int(11) DEFAULT '0' COMMENT 'Forms width  in pixels',
  `form_type_id` int(11) DEFAULT NULL,
  `custom_url` varchar(1000) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `list_id` (`list_id`),
  KEY `form_type_id` (`form_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Reģistru datu ievades formas. Vienam reģistram var būt vairākas datu ievades formas ar dažādiem reģistra laukiem un dažādu funkcionalitāti.' AUTO_INCREMENT=195 ;

--
-- Dumping data for table `dx_forms`
--

INSERT INTO `dx_forms` (`id`, `list_id`, `title`, `zones_count`, `width`, `form_type_id`, `custom_url`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(3, 4, 'Objekts', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(4, 3, 'Reģistrs', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(5, 7, 'Reģistra lauks', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(6, 10, 'Forma', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(7, 9, 'Formas lauks', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(8, 6, 'Skats', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(9, 11, 'Skata lauks', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(10, 5, 'Izvēlne', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(11, 8, 'Lauka tips', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(15, 16, 'Sadaļa', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(18, 20, 'Loma', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(19, 21, 'Lietotājs', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(20, 22, 'Lietotāja loma', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(21, 23, 'Lomas reģistrs', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(30, 41, 'Datu atbilstība', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(32, 43, 'Lauku operācijas', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(33, 44, 'Secības veids', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(37, 48, 'Agregācijas veids', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(49, 60, 'Ziņa', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(71, 82, 'Vārda diena', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(82, 93, 'Notikums', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(83, 94, 'Darbplūsmas lietotājs', 0, 0, 1, NULL, NULL, NULL, 1, '2016-03-08 09:29:42'),
(84, 95, 'Darbinieka vēsture', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(86, 97, 'Portāla statistika', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(87, 98, 'Numerators', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(88, 99, 'JavaScript', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(89, 100, 'Lapa', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(90, 101, 'Lapas loma', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(91, 102, 'Skata veids', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(92, 103, 'Lauka attēlošanas veids', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(95, 106, 'Uzdevuma statuss', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(96, 107, 'Uzdevuma veids', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(97, 109, 'Uzdevumu izpildītāja veids', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(98, 110, 'Darbplūsmas solis', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(101, 113, 'Formu veidi', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(103, 108, 'Uzdevums', 0, 0, 2, 'form_task', NULL, NULL, NULL, NULL),
(104, 114, 'Ieraksta statuss', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(105, 115, 'Darbplūsmas lauks', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(109, 119, 'Datu avots', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(110, 120, 'HTML bloks', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(111, 121, 'Darbinieks', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(116, 127, 'Raksta iezīme', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(117, 126, 'Iezīme', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(126, 136, 'Laikapstākļu veids', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(127, 137, 'Laika ziņa', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(149, 188, 'Ziņas veids', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(150, 189, 'Attēlu galerija', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(152, 191, 'Attēls', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(154, 193, 'Konfigurācija', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(155, 194, 'Satura bloks', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(156, 195, 'Jautājums', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(159, 198, 'Dienas jautājums', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(161, 200, 'Atbildes variants', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(163, 202, 'Atbildes uz jautājumiem', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(166, 205, 'Video', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(167, 208, 'Video galerija', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(168, 209, 'Jautājuma nodaļa', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(170, 211, 'Biežāk uzdotais jautājums', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(171, 212, 'Izdevuma tips', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(173, 214, 'Izdevums', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(174, 215, 'Darbības veids', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(175, 216, 'Lietotāja darbība', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(181, 222, 'Izmaiņa', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(182, 223, 'Struktūrvienība', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(183, 224, 'Process', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(184, 225, 'Procesa vēsture', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(185, 226, 'Sistēma', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(186, 227, 'Sistēmas incidents', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(188, 229, 'Lotus Notes sistēma', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(190, 231, 'Reģistru grupa', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(191, 232, 'Ziņas datne', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(192, 233, 'Ziņu papildus veids', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(193, 234, 'Ziņa', 0, 0, 1, NULL, NULL, NULL, NULL, NULL),
(194, 235, 'Satura bloks', 0, 0, 1, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_forms_fields`
--

CREATE TABLE IF NOT EXISTS `dx_forms_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL COMMENT 'Reference to register',
  `form_id` int(11) NOT NULL COMMENT 'Reference to form',
  `field_id` int(11) NOT NULL COMMENT 'Reference to register field',
  `zone_nr` tinyint(11) NOT NULL DEFAULT '0' COMMENT 'Zone number - fields can be grouped, each group can be outputed in different places. Default value is 0',
  `is_hidden` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Indicates if field in te form is hidden (0 - no, 1 - yes)',
  `tab_id` int(11) DEFAULT NULL COMMENT 'Reference to tab section - fields can be gruped not only by zones but by tab sections as well',
  `order_index` int(11) NOT NULL DEFAULT '0' COMMENT 'Order of the field in the form',
  `is_readonly` tinyint(11) NOT NULL DEFAULT '0' COMMENT 'Indicates if field is read only (0 - no, 1 - yes)',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`,`field_id`,`tab_id`),
  KEY `list_id` (`list_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Datu ievades formas lauki - var norādīt lauku ievades secību, redzamību un rediģējamību.' AUTO_INCREMENT=1206 ;

--
-- Dumping data for table `dx_forms_fields`
--

INSERT INTO `dx_forms_fields` (`id`, `list_id`, `form_id`, `field_id`, `zone_nr`, `is_hidden`, `tab_id`, `order_index`, `is_readonly`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(8, 4, 3, 8, 0, 0, NULL, 1, 1, NULL, NULL, NULL, NULL),
(9, 4, 3, 6, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(10, 4, 3, 5, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(11, 3, 4, 7, 0, 0, NULL, 1, 1, NULL, NULL, NULL, NULL),
(12, 3, 4, 4, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(15, 3, 4, 1, 0, 0, NULL, 5, 0, NULL, NULL, NULL, NULL),
(16, 7, 5, 16, 0, 0, NULL, 1, 1, NULL, NULL, NULL, NULL),
(17, 7, 5, 17, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(18, 7, 5, 20, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(19, 7, 5, 21, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(20, 7, 5, 22, 0, 0, NULL, 5, 0, NULL, NULL, NULL, NULL),
(21, 7, 5, 23, 0, 0, NULL, 6, 0, NULL, NULL, NULL, NULL),
(22, 7, 5, 24, 0, 0, NULL, 7, 0, NULL, NULL, NULL, NULL),
(23, 7, 5, 25, 0, 0, NULL, 8, 0, NULL, NULL, NULL, NULL),
(24, 7, 5, 26, 0, 0, NULL, 10, 0, NULL, NULL, NULL, NULL),
(25, 7, 5, 27, 0, 0, NULL, 11, 0, NULL, NULL, NULL, NULL),
(26, 10, 6, 35, 0, 0, NULL, 1, 1, NULL, NULL, NULL, NULL),
(27, 10, 6, 32, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(28, 10, 6, 33, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(29, 9, 7, 42, 0, 0, NULL, -10, 1, NULL, NULL, NULL, NULL),
(30, 9, 7, 44, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(31, 9, 7, 43, 0, 0, NULL, -5, 0, NULL, NULL, NULL, NULL),
(32, 9, 7, 45, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(33, 9, 7, 49, 0, 0, NULL, 6, 0, NULL, NULL, NULL, NULL),
(34, 9, 7, 48, 0, 0, NULL, 7, 0, NULL, NULL, NULL, NULL),
(35, 9, 7, 47, 0, 0, NULL, 8, 0, NULL, NULL, NULL, NULL),
(37, 6, 8, 28, 0, 0, NULL, 40, 1, NULL, NULL, NULL, NULL),
(38, 6, 8, 30, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(39, 6, 8, 29, 0, 0, NULL, 45, 0, NULL, NULL, NULL, NULL),
(40, 6, 8, 31, 0, 0, NULL, 70, 0, NULL, NULL, NULL, NULL),
(41, 11, 9, 36, 0, 0, NULL, -10, 1, NULL, NULL, NULL, NULL),
(42, 11, 9, 50, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(43, 11, 9, 37, 0, 0, NULL, -5, 0, NULL, NULL, NULL, NULL),
(44, 11, 9, 38, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(45, 11, 9, 39, 0, 0, NULL, 5, 0, NULL, NULL, NULL, NULL),
(46, 11, 9, 40, 0, 0, NULL, 6, 0, NULL, NULL, NULL, NULL),
(47, 11, 9, 41, 0, 0, NULL, 7, 0, NULL, NULL, NULL, NULL),
(48, 5, 10, 51, 0, 0, NULL, 1, 1, NULL, NULL, NULL, NULL),
(49, 5, 10, 53, 0, 0, NULL, 1, 0, NULL, NULL, NULL, NULL),
(50, 5, 10, 52, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(51, 5, 10, 54, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(52, 5, 10, 55, 0, 0, NULL, 5, 0, NULL, NULL, NULL, NULL),
(53, 8, 11, 18, 0, 0, NULL, 1, 1, NULL, NULL, NULL, NULL),
(54, 8, 11, 19, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(64, 16, 15, 65, 0, 0, NULL, 1, 1, NULL, NULL, NULL, NULL),
(65, 16, 15, 66, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(66, 16, 15, 67, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(67, 16, 15, 68, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(68, 16, 15, 69, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(69, 16, 15, 72, 0, 0, NULL, 5, 0, NULL, NULL, NULL, NULL),
(70, 16, 15, 73, 0, 0, NULL, 7, 0, NULL, NULL, NULL, NULL),
(87, 20, 18, 91, 0, 0, NULL, 1, 1, NULL, NULL, NULL, NULL),
(88, 20, 18, 92, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(89, 20, 18, 93, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(90, 20, 18, 94, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(91, 21, 19, 95, 0, 0, NULL, 1, 1, NULL, NULL, NULL, NULL),
(92, 21, 19, 96, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(93, 21, 19, 97, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(94, 21, 19, 98, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(95, 21, 19, 99, 0, 0, NULL, 5, 0, NULL, NULL, NULL, NULL),
(96, 22, 20, 100, 0, 0, NULL, 1, 1, NULL, NULL, NULL, NULL),
(97, 22, 20, 101, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(98, 22, 20, 102, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(99, 23, 21, 103, 0, 0, NULL, -10, 1, NULL, NULL, NULL, NULL),
(100, 23, 21, 104, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(101, 23, 21, 105, 0, 0, NULL, -5, 0, NULL, NULL, NULL, NULL),
(102, 23, 21, 106, 0, 0, NULL, 5, 0, NULL, NULL, NULL, NULL),
(122, 8, 11, 135, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(123, 8, 11, 136, 0, 0, NULL, 5, 0, NULL, NULL, NULL, NULL),
(124, 4, 3, 137, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(134, 7, 5, 147, 0, 0, NULL, 90, 0, NULL, NULL, NULL, NULL),
(148, 23, 21, 161, 0, 0, NULL, 7, 0, NULL, NULL, NULL, NULL),
(149, 7, 5, 163, 0, 0, NULL, 100, 0, NULL, NULL, NULL, NULL),
(150, 41, 30, 165, 0, 0, NULL, 1, 1, NULL, NULL, NULL, NULL),
(151, 41, 30, 166, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(152, 41, 30, 167, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(153, 41, 30, 168, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(154, 11, 9, 169, 0, 0, NULL, 8, 0, NULL, NULL, NULL, NULL),
(160, 43, 32, 176, 0, 0, NULL, 1, 1, NULL, NULL, NULL, NULL),
(161, 43, 32, 177, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(162, 43, 32, 178, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(163, 11, 9, 179, 0, 0, NULL, 11, 0, NULL, NULL, NULL, NULL),
(164, 11, 9, 180, 0, 0, NULL, 12, 0, NULL, NULL, NULL, NULL),
(165, 11, 9, 181, 0, 0, NULL, 13, 0, NULL, NULL, NULL, NULL),
(166, 44, 33, 184, 0, 0, NULL, 1, 1, NULL, NULL, NULL, NULL),
(167, 44, 33, 185, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(168, 44, 33, 186, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(169, 11, 9, 187, 0, 0, NULL, 14, 0, NULL, NULL, NULL, NULL),
(173, 11, 9, 195, 0, 0, NULL, 9, 0, NULL, NULL, NULL, NULL),
(174, 6, 8, 197, 0, 0, NULL, 80, 0, NULL, NULL, NULL, NULL),
(175, 6, 8, 196, 0, 0, NULL, 90, 0, NULL, NULL, NULL, NULL),
(188, 48, 37, 206, 0, 0, NULL, 1, 1, NULL, NULL, NULL, NULL),
(189, 48, 37, 207, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(190, 48, 37, 208, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(191, 11, 9, 209, 0, 0, NULL, 15, 0, NULL, NULL, NULL, NULL),
(237, 5, 10, 259, 0, 0, NULL, 6, 0, NULL, NULL, NULL, NULL),
(238, 21, 19, 260, 0, 0, NULL, 6, 0, NULL, NULL, NULL, NULL),
(240, 7, 5, 262, 0, 0, NULL, 110, 0, NULL, NULL, NULL, NULL),
(241, 7, 5, 263, 0, 0, NULL, 115, 0, NULL, NULL, NULL, NULL),
(263, 60, 49, 286, 0, 0, NULL, -100, 1, NULL, NULL, NULL, NULL),
(264, 60, 49, 287, 0, 0, NULL, -50, 0, NULL, NULL, NULL, NULL),
(265, 60, 49, 288, 0, 0, NULL, 5, 0, NULL, NULL, NULL, NULL),
(266, 60, 49, 289, 0, 0, NULL, 9, 0, NULL, NULL, 1, '2016-03-11 18:11:44'),
(267, 60, 49, 290, 0, 0, NULL, 20, 0, NULL, NULL, 1, '2016-03-11 08:31:48'),
(268, 60, 49, 291, 0, 0, NULL, 15, 0, NULL, NULL, 1, '2016-03-11 08:31:59'),
(403, 23, 21, 427, 0, 0, NULL, 6, 0, NULL, NULL, NULL, NULL),
(404, 23, 21, 428, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(405, 82, 71, 429, 0, 0, NULL, 0, 1, NULL, NULL, NULL, NULL),
(406, 82, 71, 430, 0, 0, NULL, 1, 1, NULL, NULL, NULL, NULL),
(407, 82, 71, 431, 0, 0, NULL, 2, 1, NULL, NULL, NULL, NULL),
(408, 82, 71, 432, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(522, 60, 49, 546, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(523, 60, 49, 547, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(542, 93, 82, 566, 0, 0, NULL, 0, 1, NULL, NULL, NULL, NULL),
(543, 93, 82, 567, 0, 0, NULL, 1, 0, NULL, NULL, NULL, NULL),
(544, 93, 82, 568, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(545, 93, 82, 569, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(546, 93, 82, 570, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(547, 93, 82, 571, 0, 0, NULL, 5, 0, NULL, NULL, NULL, NULL),
(548, 93, 82, 572, 0, 0, NULL, 6, 0, NULL, NULL, NULL, NULL),
(549, 93, 82, 573, 0, 0, NULL, 7, 0, NULL, NULL, NULL, NULL),
(550, 94, 83, 574, 0, 0, NULL, 0, 1, NULL, NULL, NULL, NULL),
(551, 94, 83, 575, 0, 0, NULL, 1, 0, NULL, NULL, NULL, NULL),
(552, 94, 83, 576, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(553, 94, 83, 577, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(555, 94, 83, 579, 0, 0, NULL, 5, 0, NULL, NULL, NULL, NULL),
(556, 95, 84, 580, 0, 0, NULL, 0, 1, NULL, NULL, NULL, NULL),
(557, 95, 84, 581, 0, 0, NULL, 1, 0, NULL, NULL, NULL, NULL),
(558, 95, 84, 582, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(559, 95, 84, 583, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(560, 95, 84, 584, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(561, 95, 84, 585, 0, 0, NULL, 5, 0, NULL, NULL, NULL, NULL),
(562, 95, 84, 586, 0, 0, NULL, 6, 0, NULL, NULL, NULL, NULL),
(563, 95, 84, 587, 0, 0, NULL, 7, 0, NULL, NULL, NULL, NULL),
(568, 94, 83, 594, 0, 0, NULL, 6, 0, NULL, NULL, NULL, NULL),
(569, 94, 83, 595, 0, 0, NULL, 7, 0, NULL, NULL, NULL, NULL),
(570, 94, 83, 596, 0, 0, NULL, 8, 0, NULL, NULL, NULL, NULL),
(571, 94, 83, 597, 0, 0, NULL, 9, 0, NULL, NULL, NULL, NULL),
(572, 94, 83, 591, 0, 0, NULL, 10, 0, NULL, NULL, NULL, NULL),
(573, 94, 83, 592, 0, 0, NULL, 11, 0, NULL, NULL, NULL, NULL),
(574, 60, 49, 598, 0, 0, NULL, 1, 0, NULL, NULL, NULL, NULL),
(575, 97, 86, 599, 0, 0, NULL, 1, 1, NULL, NULL, NULL, NULL),
(576, 97, 86, 600, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(577, 97, 86, 601, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(578, 97, 86, 602, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(579, 98, 87, 603, 0, 0, NULL, 0, 1, NULL, NULL, NULL, NULL),
(580, 98, 87, 604, 0, 0, NULL, 10, 0, NULL, NULL, NULL, NULL),
(581, 98, 87, 605, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(582, 98, 87, 606, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(583, 98, 87, 607, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(584, 7, 5, 611, 0, 0, NULL, 150, 0, NULL, NULL, NULL, NULL),
(585, 98, 87, 612, 0, 0, NULL, 35, 0, NULL, NULL, NULL, NULL),
(586, 99, 88, 613, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(587, 99, 88, 614, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(588, 99, 88, 615, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(589, 99, 88, 616, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(590, 100, 89, 617, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(591, 100, 89, 618, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(592, 100, 89, 621, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(593, 100, 89, 619, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(594, 100, 89, 620, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(595, 101, 90, 622, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(596, 101, 90, 624, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(597, 101, 90, 623, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(598, 102, 91, 625, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(599, 102, 91, 626, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(600, 103, 92, 627, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(601, 103, 92, 628, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(602, 6, 8, 629, 0, 0, NULL, 60, 0, NULL, NULL, NULL, NULL),
(603, 11, 9, 630, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(615, 106, 95, 642, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(616, 106, 95, 643, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(617, 107, 96, 644, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(618, 107, 96, 645, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(619, 6, 8, 653, 0, 0, NULL, 100, 0, NULL, NULL, NULL, NULL),
(620, 109, 97, 654, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(621, 109, 97, 655, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(622, 110, 98, 657, 0, 0, NULL, 10, 0, NULL, NULL, NULL, NULL),
(623, 110, 98, 656, 0, 0, NULL, 5, 1, NULL, NULL, NULL, NULL),
(624, 110, 98, 658, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(625, 110, 98, 665, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(626, 110, 98, 661, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(627, 110, 98, 659, 0, 0, NULL, 60, 0, NULL, NULL, NULL, NULL),
(628, 110, 98, 660, 0, 0, NULL, 80, 0, NULL, NULL, NULL, NULL),
(629, 110, 98, 667, 0, 0, NULL, 90, 0, NULL, NULL, NULL, NULL),
(630, 110, 98, 662, 0, 0, NULL, 100, 0, NULL, NULL, NULL, NULL),
(631, 110, 98, 664, 0, 0, NULL, 110, 0, NULL, NULL, NULL, NULL),
(632, 110, 98, 666, 0, 0, NULL, 120, 0, NULL, NULL, NULL, NULL),
(633, 110, 98, 668, 0, 0, NULL, 65, 0, NULL, NULL, NULL, NULL),
(634, 110, 98, 669, 0, 0, NULL, 70, 0, NULL, NULL, NULL, NULL),
(635, 110, 98, 670, 0, 0, NULL, 67, 0, NULL, NULL, NULL, NULL),
(644, 113, 101, 679, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(645, 113, 101, 680, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(646, 10, 6, 681, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(647, 10, 6, 682, 0, 0, NULL, 60, 0, NULL, NULL, NULL, NULL),
(648, 6, 8, 683, 0, 0, NULL, 55, 0, NULL, NULL, NULL, NULL),
(649, 6, 8, 684, 0, 0, NULL, 95, 0, NULL, NULL, NULL, NULL),
(650, 114, 104, 685, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(651, 114, 104, 686, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(652, 114, 104, 687, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(654, 115, 105, 692, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(655, 115, 105, 693, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(656, 115, 105, 694, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(657, 115, 105, 695, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(673, 6, 8, 713, 0, 0, NULL, 96, 0, NULL, NULL, NULL, NULL),
(680, 6, 8, 722, 0, 0, NULL, 98, 0, NULL, NULL, NULL, NULL),
(681, 3, 4, 723, 0, 0, NULL, 10, 0, NULL, NULL, NULL, NULL),
(682, 7, 5, 724, 0, 0, NULL, 117, 0, NULL, NULL, NULL, NULL),
(683, 119, 109, 725, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(684, 119, 109, 726, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(685, 119, 109, 727, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(686, 60, 49, 728, 0, 0, NULL, -80, 0, NULL, NULL, NULL, NULL),
(687, 120, 110, 729, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(688, 120, 110, 735, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(689, 120, 110, 731, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(690, 120, 110, 730, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(691, 120, 110, 733, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(692, 120, 110, 734, 0, 0, NULL, 60, 0, NULL, NULL, NULL, NULL),
(693, 120, 110, 737, 0, 0, NULL, 80, 0, NULL, NULL, NULL, NULL),
(694, 120, 110, 736, 0, 0, NULL, 90, 0, NULL, NULL, NULL, NULL),
(695, 121, 111, 738, 0, 0, NULL, 0, 1, NULL, NULL, NULL, NULL),
(696, 121, 111, 739, 0, 0, NULL, 1, 0, NULL, NULL, NULL, NULL),
(697, 121, 111, 740, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(698, 121, 111, 741, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(699, 121, 111, 742, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(700, 121, 111, 743, 0, 0, NULL, 5, 0, NULL, NULL, NULL, NULL),
(701, 121, 111, 744, 0, 0, NULL, 6, 0, NULL, NULL, NULL, NULL),
(702, 121, 111, 745, 0, 0, NULL, 7, 0, NULL, NULL, NULL, NULL),
(703, 121, 111, 746, 0, 0, NULL, 8, 0, NULL, NULL, NULL, NULL),
(704, 121, 111, 750, 0, 0, NULL, 9, 0, NULL, NULL, NULL, NULL),
(706, 121, 111, 748, 0, 0, NULL, 11, 0, NULL, NULL, NULL, NULL),
(707, 121, 111, 749, 0, 0, NULL, 12, 0, NULL, NULL, NULL, NULL),
(708, 100, 89, 753, 0, 0, NULL, 15, 0, NULL, NULL, NULL, NULL),
(749, 127, 116, 813, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL),
(750, 127, 116, 814, 0, 1, NULL, 1, 0, NULL, NULL, 1, '2016-03-14 07:38:31'),
(751, 127, 116, 815, 0, 0, NULL, 2, 0, NULL, NULL, NULL, NULL),
(752, 5, 10, 751, 0, 0, NULL, 60, 0, NULL, NULL, NULL, NULL),
(753, 5, 10, 752, 0, 0, NULL, 70, 0, NULL, NULL, NULL, NULL),
(754, 126, 117, 810, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(755, 126, 117, 811, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(812, 136, 126, 876, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(813, 136, 126, 877, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(814, 136, 126, 878, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(815, 136, 126, 879, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(816, 137, 127, 880, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(817, 137, 127, 881, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(818, 137, 127, 882, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(819, 137, 127, 883, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(820, 137, 127, 884, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(821, 137, 127, 885, 0, 0, NULL, 60, 0, NULL, NULL, NULL, NULL),
(822, 100, 89, 886, 0, 0, NULL, 45, 0, NULL, NULL, NULL, NULL),
(823, 100, 89, 887, 0, 0, NULL, 47, 0, NULL, NULL, NULL, NULL),
(824, 120, 110, 904, 0, 0, NULL, 45, 0, NULL, NULL, NULL, NULL),
(945, 188, 149, 1164, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(946, 188, 149, 1165, 0, 0, NULL, 20, 1, NULL, NULL, 1, '2016-03-20 16:02:56'),
(947, 188, 149, 1166, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(948, 188, 149, 1167, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(949, 188, 149, 1168, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(951, 188, 149, 1170, 0, 0, NULL, 70, 0, NULL, NULL, NULL, NULL),
(952, 60, 49, 1171, 0, 1, NULL, -90, 1, NULL, NULL, 1, '2016-03-07 15:04:57'),
(953, 189, 150, 1172, 0, 0, NULL, -100, 1, NULL, NULL, NULL, NULL),
(954, 189, 150, 1173, 0, 0, NULL, -50, 0, NULL, NULL, NULL, NULL),
(956, 189, 150, 1175, 0, 0, NULL, 6, 0, NULL, NULL, NULL, NULL),
(958, 189, 150, 1177, 0, 0, NULL, 7, 0, NULL, NULL, NULL, NULL),
(959, 189, 150, 1178, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(960, 189, 150, 1179, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(962, 189, 150, 1181, 0, 0, NULL, -80, 0, NULL, NULL, NULL, NULL),
(963, 189, 150, 1182, 0, 0, NULL, -90, 1, NULL, NULL, NULL, NULL),
(969, 191, 152, 1188, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(970, 191, 152, 1189, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(971, 191, 152, 1190, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(973, 191, 152, 1192, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(974, 5, 10, 1194, 0, 1, NULL, 0, 0, NULL, NULL, NULL, NULL),
(986, 193, 154, 1206, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(987, 193, 154, 1207, 0, 0, NULL, 20, 1, NULL, NULL, 1, '2016-03-20 15:47:39'),
(988, 193, 154, 1208, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(989, 193, 154, 1209, 0, 0, NULL, 40, 1, NULL, NULL, 1, '2016-03-20 15:48:09'),
(990, 193, 154, 1210, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(991, 193, 154, 1211, 0, 0, NULL, 60, 0, NULL, NULL, NULL, NULL),
(992, 193, 154, 1212, 0, 0, NULL, 70, 0, NULL, NULL, NULL, NULL),
(993, 193, 154, 1213, 0, 0, NULL, 80, 0, NULL, NULL, NULL, NULL),
(994, 193, 154, 1214, 0, 0, NULL, 90, 0, NULL, NULL, NULL, NULL),
(996, 193, 154, 1216, 0, 0, NULL, 110, 0, NULL, NULL, NULL, NULL),
(997, 194, 155, 1217, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(998, 194, 155, 1218, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(999, 194, 155, 1219, 0, 0, NULL, 30, 1, NULL, NULL, 1, '2016-03-20 10:09:49'),
(1000, 194, 155, 1221, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(1001, 194, 155, 1226, 0, 0, NULL, 60, 1, NULL, NULL, 1, '2016-03-20 10:10:42'),
(1002, 194, 155, 1222, 0, 0, NULL, 20, 1, NULL, NULL, 1, '2016-03-20 10:09:42'),
(1006, 21, 19, 1227, 0, 0, NULL, 7, 0, NULL, NULL, NULL, NULL),
(1007, 82, 71, 1228, 0, 0, NULL, 10, 0, NULL, NULL, NULL, NULL),
(1008, 195, 156, 1229, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1009, 195, 156, 1230, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1010, 195, 156, 1231, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1011, 195, 156, 1232, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(1012, 195, 156, 1233, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(1013, 195, 156, 1234, 0, 0, NULL, 60, 0, NULL, NULL, NULL, NULL),
(1019, 198, 159, 1240, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1020, 198, 159, 1241, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1021, 198, 159, 1242, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1022, 198, 159, 1243, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(1023, 198, 159, 1244, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(1024, 198, 159, 1245, 0, 0, NULL, 60, 0, NULL, NULL, NULL, NULL),
(1025, 198, 159, 1246, 0, 0, NULL, 70, 0, NULL, NULL, NULL, NULL),
(1027, 198, 159, 1248, 0, 0, NULL, 90, 0, NULL, NULL, NULL, NULL),
(1030, 200, 161, 1251, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1031, 200, 161, 1252, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1032, 200, 161, 1253, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1035, 202, 163, 1256, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1036, 202, 163, 1257, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1037, 202, 163, 1258, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1044, 205, 166, 1265, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1045, 205, 166, 1266, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1046, 205, 166, 1267, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1048, 205, 166, 1269, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(1049, 205, 166, 1270, 0, 0, NULL, 60, 0, NULL, NULL, NULL, NULL),
(1050, 208, 167, 1287, 0, 0, NULL, -100, 1, NULL, NULL, NULL, NULL),
(1051, 208, 167, 1288, 0, 0, NULL, -50, 0, NULL, NULL, NULL, NULL),
(1052, 208, 167, 1289, 0, 0, NULL, 6, 0, NULL, NULL, NULL, NULL),
(1053, 208, 167, 1290, 0, 0, NULL, 7, 0, NULL, NULL, NULL, NULL),
(1054, 208, 167, 1291, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(1055, 208, 167, 1292, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(1056, 208, 167, 1293, 0, 0, NULL, -80, 0, NULL, NULL, NULL, NULL),
(1057, 208, 167, 1294, 0, 0, NULL, -90, 1, NULL, NULL, NULL, NULL),
(1058, 205, 166, 1295, 0, 0, NULL, 55, 0, NULL, NULL, NULL, NULL),
(1059, 205, 166, 1296, 0, 0, NULL, 25, 0, NULL, NULL, NULL, NULL),
(1060, 209, 168, 1297, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1061, 209, 168, 1298, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1062, 209, 168, 1299, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1067, 211, 170, 1304, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1068, 211, 170, 1305, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1069, 211, 170, 1306, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1070, 211, 170, 1307, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(1071, 211, 170, 1308, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(1072, 212, 171, 1309, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1073, 212, 171, 1310, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1074, 214, 173, 1311, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1075, 214, 173, 1312, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1076, 214, 173, 1313, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1077, 214, 173, 1314, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(1078, 214, 173, 1315, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(1080, 214, 173, 1317, 0, 0, NULL, 70, 0, NULL, NULL, NULL, NULL),
(1082, 214, 173, 1319, 0, 0, NULL, 90, 0, NULL, NULL, NULL, NULL),
(1083, 119, 109, 1320, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(1084, 119, 109, 1321, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(1085, 119, 109, 1322, 0, 0, NULL, 60, 0, NULL, NULL, NULL, NULL),
(1086, 215, 174, 1323, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1087, 215, 174, 1324, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1088, 216, 175, 1325, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1089, 216, 175, 1326, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1090, 216, 175, 1327, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1091, 216, 175, 1328, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(1092, 216, 175, 1329, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(1093, 216, 175, 1330, 0, 0, NULL, 60, 0, NULL, NULL, NULL, NULL),
(1099, 222, 181, 1338, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1100, 222, 181, 1339, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1101, 222, 181, 1340, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1102, 222, 181, 1341, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(1103, 222, 181, 1342, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(1106, 222, 181, 1345, 0, 0, NULL, 80, 0, NULL, NULL, NULL, NULL),
(1107, 222, 181, 1346, 0, 0, NULL, 90, 0, NULL, NULL, NULL, NULL),
(1110, 223, 182, 1349, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1111, 223, 182, 1350, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1113, 223, 182, 1352, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(1114, 223, 182, 1353, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(1115, 223, 182, 1354, 0, 0, NULL, 15, 0, NULL, NULL, NULL, NULL),
(1116, 224, 183, 1355, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1117, 224, 183, 1356, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1118, 224, 183, 1357, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1119, 224, 183, 1358, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(1120, 224, 183, 1359, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(1121, 224, 183, 1360, 0, 0, NULL, 60, 0, NULL, NULL, NULL, NULL),
(1122, 224, 183, 1361, 0, 0, NULL, 70, 0, NULL, NULL, NULL, NULL),
(1123, 224, 183, 1362, 0, 0, NULL, 80, 0, NULL, NULL, NULL, NULL),
(1124, 224, 183, 1363, 0, 0, NULL, 90, 0, NULL, NULL, NULL, NULL),
(1125, 224, 183, 1364, 0, 0, NULL, 100, 0, NULL, NULL, NULL, NULL),
(1126, 224, 183, 1365, 0, 0, NULL, 110, 1, NULL, NULL, NULL, NULL),
(1127, 225, 184, 1366, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1128, 225, 184, 1367, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1129, 225, 184, 1368, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1130, 225, 184, 1369, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(1131, 225, 184, 1370, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(1132, 225, 184, 1371, 0, 0, NULL, 60, 0, NULL, NULL, NULL, NULL),
(1133, 225, 184, 1372, 0, 0, NULL, 70, 0, NULL, NULL, NULL, NULL),
(1134, 226, 185, 1373, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1135, 226, 185, 1374, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1136, 226, 185, 1375, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1137, 226, 185, 1376, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(1139, 226, 185, 1378, 0, 0, NULL, 60, 0, NULL, NULL, NULL, NULL),
(1140, 226, 185, 1379, 0, 0, NULL, 70, 0, NULL, NULL, NULL, NULL),
(1141, 227, 186, 1380, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1143, 227, 186, 1382, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1144, 227, 186, 1383, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(1145, 227, 186, 1384, 0, 0, NULL, 35, 0, NULL, NULL, 1, '2016-02-23 13:47:02'),
(1146, 227, 186, 1385, 0, 0, NULL, 12, 0, NULL, NULL, 1, '2016-02-23 13:46:43'),
(1147, 229, 188, 1386, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1148, 229, 188, 1387, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1149, 229, 188, 1388, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1150, 229, 188, 1389, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(1151, 229, 188, 1390, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(1152, 231, 190, 1391, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1153, 231, 190, 1392, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1154, 231, 190, 1393, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1155, 3, 4, 1394, 0, 0, NULL, 7, 0, 1, '2016-03-08 09:35:10', 1, '2016-03-08 09:35:10'),
(1156, 7, 5, 1395, 0, 0, NULL, 160, 0, 1, '2016-03-08 12:10:23', 1, '2016-03-08 12:10:23'),
(1157, 60, 49, 1396, 0, 0, NULL, 2, 0, 1, '2016-03-08 12:41:34', 1, '2016-03-08 12:41:34'),
(1158, 3, 4, 1397, 0, 0, NULL, 20, 0, 1, '2016-03-08 12:45:57', 1, '2016-03-08 12:45:57'),
(1159, 60, 49, 1398, 0, 0, NULL, 10, 0, 1, '2016-03-11 08:32:14', 1, '2016-03-11 08:32:14'),
(1160, 60, 49, 1399, 0, 0, NULL, 12, 0, 1, '2016-03-11 09:22:00', 1, '2016-03-11 09:22:00'),
(1161, 232, 191, 1400, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1162, 232, 191, 1401, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1163, 232, 191, 1402, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1165, 232, 191, 1404, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(1166, 7, 5, 1405, 0, 0, NULL, 118, 0, 1, '2016-03-11 17:07:57', 1, '2016-03-11 17:07:57'),
(1167, 232, 191, 1406, 0, 0, NULL, 40, 0, 1, '2016-03-11 17:41:06', 1, '2016-03-11 17:41:06'),
(1168, 60, 49, 1407, 0, 0, NULL, 7, 0, 1, '2016-03-11 18:12:02', 1, '2016-03-11 18:12:02'),
(1169, 7, 5, 1408, 0, 0, NULL, 155, 0, 1, '2016-03-14 06:51:28', 1, '2016-03-14 06:51:28'),
(1170, 233, 192, 1409, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1171, 233, 192, 1410, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1172, 60, 49, 1411, 0, 0, NULL, -85, 0, 1, '2016-03-14 12:56:17', 1, '2016-03-14 12:56:17'),
(1173, 60, 49, 1412, 0, 0, NULL, -45, 0, 1, '2016-03-14 13:10:31', 1, '2016-03-14 13:10:31'),
(1174, 60, 49, 1413, 0, 0, NULL, 13, 0, 1, '2016-03-14 13:12:59', 1, '2016-03-14 13:12:59'),
(1175, 60, 49, 1414, 0, 0, NULL, 14, 0, 1, '2016-03-14 13:14:51', 1, '2016-03-14 13:14:51'),
(1176, 234, 193, 1415, 0, 0, NULL, -100, 1, NULL, NULL, NULL, NULL),
(1177, 234, 193, 1416, 0, 0, NULL, -50, 0, NULL, NULL, NULL, NULL),
(1178, 234, 193, 1417, 0, 0, NULL, 5, 0, NULL, NULL, NULL, NULL),
(1179, 234, 193, 1418, 0, 0, NULL, 9, 0, NULL, NULL, 1, '2016-03-11 18:11:44'),
(1180, 234, 193, 1419, 0, 0, NULL, 20, 0, NULL, NULL, 1, '2016-03-11 08:31:48'),
(1181, 234, 193, 1420, 0, 0, NULL, 15, 0, NULL, NULL, 1, '2016-03-11 08:31:59'),
(1182, 234, 193, 1421, 0, 0, NULL, 4, 0, NULL, NULL, NULL, NULL),
(1183, 234, 193, 1422, 0, 0, NULL, 3, 0, NULL, NULL, NULL, NULL),
(1184, 234, 193, 1423, 0, 0, NULL, 1, 0, NULL, NULL, NULL, NULL),
(1185, 234, 193, 1428, 0, 0, NULL, -80, 0, NULL, NULL, NULL, NULL),
(1186, 234, 193, 1430, 0, 1, NULL, -90, 1, NULL, NULL, 1, '2016-03-07 15:04:57'),
(1187, 234, 193, 1424, 0, 0, NULL, 2, 0, 1, '2016-03-08 12:41:34', 1, '2016-03-08 12:41:34'),
(1188, 234, 193, 1432, 0, 0, NULL, 10, 0, 1, '2016-03-11 08:32:14', 1, '2016-03-11 08:32:14'),
(1189, 234, 193, 1431, 0, 0, NULL, 12, 0, 1, '2016-03-11 09:22:00', 1, '2016-03-11 09:22:00'),
(1190, 234, 193, 1429, 0, 0, NULL, 7, 0, 1, '2016-03-11 18:12:02', 1, '2016-03-11 18:12:02'),
(1191, 234, 193, 1433, 0, 0, NULL, -85, 0, 1, '2016-03-14 12:56:17', 1, '2016-03-14 12:56:17'),
(1192, 234, 193, 1425, 0, 0, NULL, -45, 0, 1, '2016-03-14 13:10:31', 1, '2016-03-14 13:10:31'),
(1193, 234, 193, 1426, 0, 0, NULL, 13, 0, 1, '2016-03-14 13:12:59', 1, '2016-03-14 13:12:59'),
(1194, 234, 193, 1427, 0, 0, NULL, 14, 0, 1, '2016-03-14 13:14:51', 1, '2016-03-14 13:14:51'),
(1195, 21, 19, 1434, 0, 0, NULL, 10, 0, 1, '2016-03-19 12:29:33', 1, '2016-03-19 12:29:33'),
(1196, 235, 194, 1435, 0, 0, NULL, 10, 1, NULL, NULL, NULL, NULL),
(1197, 235, 194, 1436, 0, 0, NULL, 40, 0, NULL, NULL, NULL, NULL),
(1198, 235, 194, 1437, 0, 0, NULL, 30, 0, NULL, NULL, NULL, NULL),
(1199, 235, 194, 1439, 0, 0, NULL, 50, 0, NULL, NULL, NULL, NULL),
(1200, 235, 194, 1440, 0, 0, NULL, 20, 0, NULL, NULL, NULL, NULL),
(1201, 235, 194, 1441, 0, 0, NULL, 90, 0, NULL, NULL, NULL, NULL),
(1202, 235, 194, 1442, 0, 0, NULL, 80, 0, NULL, NULL, NULL, NULL),
(1203, 235, 194, 1443, 0, 0, NULL, 45, 0, NULL, NULL, NULL, NULL),
(1204, 235, 194, 1444, 0, 0, NULL, 60, 0, NULL, NULL, NULL, NULL),
(1205, 194, 155, 1220, 0, 0, NULL, 35, 1, 1, '2016-03-20 10:12:27', 1, '2016-03-20 10:12:27');

-- --------------------------------------------------------

--
-- Table structure for table `dx_forms_js`
--

CREATE TABLE IF NOT EXISTS `dx_forms_js` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL,
  `title` varchar(500) DEFAULT NULL,
  `js_code` text,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `dx_forms_js`
--

INSERT INTO `dx_forms_js` (`id`, `form_id`, `title`, `js_code`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 5, 'Parāda vai paslēpj laukus atkarībā no izvēlētā tipa', 'var sel = form_object.find("[name=type_id]");\r\nif (sel)\r\n{\r\n	var cur_val = 0;\r\n	if (sel.val() > 0)\r\n	{\r\n		cur_val = sel.val();\r\n	}\r\n	\r\n	var show_show_hide_rel_fields = function(val)\r\n	{\r\n\r\n		//is_html_clean\r\n		if (val == 10)\r\n		{\r\n			form_object.find("input[name=is_clean_html]").parent().parent().show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("input[name=is_clean_html]").parent().parent().hide();\r\n		}\r\n		\r\n		\r\n		//max_lenght\r\n		if (val == 0 || val == 2 || val== 3 || val == 5 || val == 6 || val == 7 || val == 8 || val== 9 || val == 10 || val == 12 || val == 14 || val == 15)\r\n		{\r\n			form_object.find("input[name=max_lenght]").parent().parent().hide();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("input[name=max_lenght]").parent().parent().show();\r\n		}\r\n		\r\n		//is_required\r\n		if (val ==0 || val == 6)\r\n		{\r\n			form_object.find("input[name=is_required]").parent().parent().hide();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("input[name=is_required]").parent().parent().show();\r\n		}\r\n		\r\n		//rel_list_id, rel_display_field_id, rel_view_id, rel_display_formula_field\r\n		if (val == 3 || val == 8 || val == 14)\r\n		{\r\n			form_object.find("[dx_fld_name=rel_list_id]").parent().parent().show();\r\n			form_object.find("[dx_fld_name=rel_display_field_id]").parent().parent().show();\r\n			form_object.find("[dx_fld_name=rel_view_id]").parent().parent().show();\r\n			form_object.find("input[name=rel_display_formula_field]").parent().parent().show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("[dx_fld_name=rel_list_id]").parent().parent().hide();\r\n			form_object.find("[dx_fld_name=rel_display_field_id]").parent().parent().hide();\r\n			form_object.find("[dx_fld_name=rel_view_id]").parent().parent().hide();\r\n			form_object.find("input[name=rel_display_formula_field]").parent().parent().hide();\r\n		}\r\n		\r\n		//default_value\r\n		if (val == 0 || val == 6 || val == 12 || val == 13)\r\n		{\r\n			form_object.find("input[name=default_value]").parent().parent().hide();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("input[name=default_value]").parent().parent().show();\r\n		}\r\n		\r\n		//formula\r\n		if (val == 1 || val == 2 || val == 4 || val == 5 || val == 9 || val == 11)\r\n		{\r\n			form_object.find("input[name=formula]").parent().parent().show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("input[name=formula]").parent().parent().hide();\r\n		}\r\n		\r\n		//is_public_file, is_image_file, is_word_generation, is_multiple_files\r\n		if (val == 12)\r\n		{\r\n			form_object.find("input[name=is_public_file]").parent().parent().show();\r\n			form_object.find("input[name=is_image_file]").parent().parent().show();\r\n            form_object.find("input[name=is_word_generation]").parent().parent().show();\r\n			form_object.find("input[name=is_multiple_files]").parent().parent().show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("input[name=is_public_file]").parent().parent().hide();\r\n			form_object.find("input[name=is_image_file]").parent().parent().hide();\r\n            form_object.find("input[name=is_word_generation]").parent().parent().hide();\r\n			form_object.find("input[name=is_multiple_files]").parent().parent().hide();\r\n		}\r\n		\r\n		//numerator_id\r\n		if (val == 13)\r\n		{\r\n			form_object.find("[dx_fld_name=numerator_id]").parent().parent().show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("[dx_fld_name=numerator_id]").parent().parent().hide();\r\n		}\r\n	}\r\n	\r\n	var change_event = function(e)\r\n	{\r\n		if (e)\r\n		{\r\n			show_show_hide_rel_fields($(this).val());\r\n		}\r\n		else\r\n		{\r\n			show_show_hide_rel_fields(cur_val);\r\n		}\r\n	}\r\n\r\n	change_event(null);\r\n	\r\n	sel.on(''change'', change_event);\r\n}\r\n', NULL, NULL, 1, '2016-03-14 07:02:16'),
(2, 9, 'Parāda vai paslēpj kritērija lauku atkarībā no operācijas lauka vērtības', 'var sel = form_object.find("[name=operation_id]");\r\nif (sel)\r\n{\r\n	var cur_val = 0;\r\n	if (sel.val() > 0)\r\n	{\r\n		cur_val = sel.val();\r\n	}\r\n	\r\n	var show_show_hide_rel_fields = function(val)\r\n	{\r\n		//alert(val);\r\n		//criteria\r\n		if (val == 0 || val == 6 || val == 7)\r\n		{\r\n			form_object.find("input[name=criteria]").parent().parent().hide();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("input[name=criteria]").parent().parent().show();\r\n		}\r\n	}\r\n	\r\n	var change_event = function(e)\r\n	{\r\n		if (e)\r\n		{\r\n			show_show_hide_rel_fields($(this).val());\r\n		}\r\n		else\r\n		{\r\n			show_show_hide_rel_fields(cur_val);\r\n		}\r\n	}\r\n\r\n	change_event(null);\r\n	\r\n	sel.on(''change'', change_event);\r\n}    \r\n\r\n\r\n', NULL, NULL, NULL, NULL),
(3, 8, 'Parāda vai paslēpj SQL lauku, ja izvēlēts skata tips Tabula ar SQL', 'var sel = form_object.find("[name=view_type_id]");\r\nif (sel)\r\n{\r\n	var cur_val = 0;\r\n	if (sel.val() > 0)\r\n	{\r\n		cur_val = sel.val();\r\n	}\r\n	\r\n	var show_show_hide_rel_fields = function(val)\r\n	{		\r\n		/* max_lenght */\r\n		if (val == 9)\r\n		{\r\n			form_object.find("textarea[name=custom_sql]").parent().parent().show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("textarea[name=custom_sql]").parent().parent().hide();\r\n		}\r\n	}\r\n	\r\n	var change_event = function(e)\r\n	{\r\n		if (e)\r\n		{\r\n			show_show_hide_rel_fields($(this).val());\r\n		}\r\n		else\r\n		{\r\n			show_show_hide_rel_fields(cur_val);\r\n		}\r\n	}\r\n\r\n	change_event(null);\r\n	\r\n	sel.on(''change'', change_event);\r\n}\r\n\r\n', NULL, NULL, NULL, NULL),
(4, 98, 'Parāda vai paslēpj darbinieka uzdevuma laukus, ja izvēlēts sistēmisks uzdevums', 'var sel = form_object.find("[name=task_type_id]");\r\nif (sel)\r\n{\r\n	var cur_val = 0;\r\n	if (sel.val() > 0)\r\n	{\r\n		cur_val = sel.val();\r\n	}\r\n	\r\n	var show_show_hide_rel_fields = function(val)\r\n	{		\r\n		/* If system task then hide human related task fields */\r\n		\r\n		/* no_step_nr */\r\n		if ((val != 6 && val != 4) || val == 5)\r\n		{\r\n			form_object.find("input[name=no_step_nr]").parent().parent().show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("input[name=no_step_nr]").parent().parent().hide();\r\n		}\r\n		\r\n		/* field_id, field_value */\r\n		if (val == 4 || val == 5)\r\n		{\r\n			form_object.find("[dx_fld_name=field_id]").parent().parent().show();\r\n			form_object.find("input[name=field_value]").parent().parent().show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("[dx_fld_name=field_id]").parent().parent().hide();\r\n			form_object.find("input[name=field_value]").parent().parent().hide();\r\n		}\r\n		\r\n		/* field_operation_id */\r\n		if (val == 5)\r\n		{\r\n			form_object.find("[dx_fld_name=field_operation_id]").parent().parent().show();		\r\n		}\r\n		else\r\n		{\r\n			form_object.find("[dx_fld_name=field_operation_id]").parent().parent().hide();\r\n		}\r\n		\r\n		/* task_perform_id, employee_id, term_days, notes */\r\n		if (val == 4 || val == 5)\r\n		{\r\n			form_object.find("[dx_fld_name=task_perform_id]").parent().parent().hide();\r\n			form_object.find("[dx_fld_name=employee_id]").parent().parent().parent().hide();\r\n			form_object.find("input[name=term_days]").parent().parent().hide();\r\n			form_object.find("textarea[name=notes]").parent().parent().hide();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("[dx_fld_name=task_perform_id]").parent().parent().show();\r\n			\r\n			if (form_object.find("[dx_fld_name=task_perform_id]").val() == 1)\r\n			{\r\n				form_object.find("[dx_fld_name=employee_id]").parent().parent().parent().show();\r\n			}\r\n			\r\n			form_object.find("input[name=term_days]").parent().parent().show();\r\n			form_object.find("textarea[name=notes]").parent().parent().show();\r\n		}\r\n      \r\n      	if (val == 6)\r\n        {\r\n          	form_object.find("input[name=term_days]").parent().parent().hide();\r\n        }\r\n      \r\n        if (val == 3)\r\n        {\r\n           form_object.find("div[dx_attr=tab]").show();\r\n        }\r\n        else\r\n        {\r\n          form_object.find("div[dx_attr=tab]").hide();\r\n        }\r\n	}\r\n	\r\n	var change_event = function(e)\r\n	{\r\n		if (e)\r\n		{\r\n			show_show_hide_rel_fields($(this).val());\r\n		}\r\n		else\r\n		{\r\n			show_show_hide_rel_fields(cur_val);\r\n		}\r\n	}\r\n\r\n	change_event(null);\r\n	\r\n	sel.on(''change'', change_event);\r\n}\r\n\r\n', NULL, NULL, NULL, NULL),
(5, 98, 'Parāda vai paslēpj darbinieka lauku, to rāda tikai ja izpildes veids ir Darbinieks', 'var sel_p = form_object.find("[dx_fld_name=task_perform_id]");\r\nif (sel_p)\r\n{\r\n	var cur_val = 0;\r\n	if (sel_p.val() > 0)\r\n	{\r\n		cur_val = sel_p.val();\r\n	}\r\n	\r\n	var show_show_hide_rel_fields = function(val)\r\n	{\r\n		/* If system task then hide human related task fields */\r\n		if (val == 1)\r\n		{\r\n			form_object.find("[dx_fld_name=employee_id]").parent().parent().parent().show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("[dx_fld_name=employee_id]").parent().parent().parent().hide();\r\n		}\r\n	}\r\n	\r\n	var change_event = function(e)\r\n	{\r\n		if (e)\r\n		{\r\n			show_show_hide_rel_fields($(this).val());\r\n		}\r\n		else\r\n		{\r\n			show_show_hide_rel_fields(cur_val);\r\n		}\r\n	}\r\n\r\n	change_event(null);\r\n	\r\n	sel_p.on(''change'', change_event);\r\n}', NULL, NULL, NULL, NULL),
(6, 6, 'Parāda vai paslēpj URL lauku atkarībā no izvēlētā formas veida', 'var sel = form_object.find("[name=form_type_id]");\r\nif (sel)\r\n{\r\n	var cur_val = 0;\r\n	if (sel.val() > 0)\r\n	{\r\n		cur_val = sel.val();\r\n	}\r\n	\r\n	var show_show_hide_rel_fields = function(val)\r\n	{\r\n		//max_lenght\r\n		if (val == 2)\r\n		{\r\n			form_object.find("input[name=custom_url]").parent().parent().show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("input[name=custom_url]").parent().parent().hide();\r\n		}\r\n	}\r\n	\r\n	var change_event = function(e)\r\n	{\r\n		if (e)\r\n		{\r\n			show_show_hide_rel_fields($(this).val());\r\n		}\r\n		else\r\n		{\r\n			show_show_hide_rel_fields(cur_val);\r\n		}\r\n	}\r\n\r\n	change_event(null);\r\n	\r\n	sel.on(''change'', change_event);\r\n}', NULL, NULL, NULL, NULL),
(7, 7, 'Nosaka saistīto reģistru no iepriekšējās formas un uzstāda kā noklusēto', 'var field_obj = form_object.find("[name=field_id]");\r\n var field_val = field_obj.val();\r\n\r\nif (field_obj.attr(''type'') != ''hidden'')\r\n{\r\n  var form_id = form_object.find("[name=form_id]").val();\r\n  var prev_form = get_previous_form_by_list(form_object.attr(''id''), form_id);\r\n\r\n  var list_id = $(''#'' + prev_form).find("[name=list_id]").val();\r\n\r\n  var list_sel = form_object.find("[name=list_id]");\r\n\r\n  list_sel.val(list_id);\r\n  list_sel.attr("disabled","disabled");\r\n\r\n  var field_val = field_obj.val();\r\n  if (field_val == 0)\r\n  {  \r\n  	load_binded_field(list_sel.attr(''id''), field_obj.attr(''id''), list_sel.attr(''dx_binded_field_id''), list_sel.attr(''dx_binded_rel_field_id''));\r\n  }\r\n}', NULL, NULL, NULL, NULL),
(9, 3, 'Pievieno reģistru darbību pogu', 'var btns_div = form_object.find(".dx_form_btns_left");\r\nif (btns_div)\r\n{\r\n	btns_div.append( "<div class=''btn-group''><button  type=''button'' class=''btn btn-white dropdown-toggle'' data-toggle=''dropdown'' aria-haspopup=''true'' aria-expanded=''false''><i class=''fa fa-cog''></i> Darbības <i class=''fa fa-caret-down''></i></button><ul class=''dropdown-menu''><li><a href=''#'' id=''generate_" + form_object.attr(''id'') + "''>Ģenerēt reģistru</a></li><li><a href=''#'' id=''copy_" + form_object.attr(''id'') + "''>Kopēt reģistru</a></li><li><a href=''#'' id=''delete_" + form_object.attr(''id'') + "''>Dzēst reģistru</a></li><li><a href=''#'' id=''audit_" + form_object.attr(''id'') + "''>Ģenerēt auditāciju</a></li></ul></div>" );\r\n	\r\n	$("#generate_" + form_object.attr(''id'')).click(function(){\r\n		var item_id = form_object.find("input[name=id]").val();\r\n		var item_url = ''/structure/form/register_generate'';\r\n		var item_title = ''Reģistra ģenerēšana'';\r\n		\r\n		get_popup_item_by_id(item_id, item_url, item_title);\r\n		\r\n	});\r\n	\r\n	$("#delete_" + form_object.attr(''id'')).click(function(){\r\n		var item_id = form_object.find("input[name=id]").val();\r\n		var item_url = ''/structure/form/register_delete'';\r\n		var item_title = ''Reģistra dzēšana'';\r\n		\r\n		get_popup_item_by_id(item_id, item_url, item_title);\r\n		\r\n	});\r\n	\r\n	$("#copy_" + form_object.attr(''id'')).click(function(){\r\n		var item_id = form_object.find("input[name=id]").val();\r\n		var item_url = ''/structure/form/register_copy'';\r\n		var item_title = ''Reģistra kopēšana'';\r\n		\r\n		get_popup_item_by_id(item_id, item_url, item_title);\r\n		\r\n	});\r\n	\r\n	$("#audit_" + form_object.attr(''id'')).click(function(){\r\n		var item_url = ''/structure/form/generate_audit'';\r\n		var item_title = ''Auditācijas ģenerēšana'';\r\n		\r\n		get_popup_item_by_id(0, item_url, item_title);\r\n		\r\n	});\r\n}', NULL, NULL, 1, '2016-03-13 08:46:12'),
(10, 10, 'Uzstāda title_index lauka vērtību - apvieno secību un nosaukumu', 'var tit = form_object.find("input[name=title]");\r\nvar sec = form_object.find("input[name=order_index]");\r\nvar indx = form_object.find("input[name=title_index]");\r\n\r\nif (tit && sec && indx)\r\n{\r\n	var update_fld = function() {\r\n		indx.val("[" + sec.val() + "] " + tit.val()); \r\n	};\r\n	\r\n	tit.focusout(function(){\r\n		update_fld();\r\n	});\r\n	\r\n	sec.focusout(function(){\r\n		update_fld();\r\n	});\r\n}', NULL, NULL, NULL, NULL),
(11, 154, 'Parāda vai paslēpj konfigurācijas laukus atkarībā no izvēlētā lauka tipa', 'var sel = form_object.find("[name=field_type_id]");\r\nif (sel)\r\n{\r\n	var cur_val = 0;\r\n	\r\n  	sel.find("option[value=''0'']").remove();\r\n	sel.find("option[value=''2'']").remove();\r\n	sel.find("option[value=''3'']").remove();\r\n	sel.find("option[value=''4'']").remove();\r\n	sel.find("option[value=''6'']").remove();\r\n	sel.find("option[value=''8'']").remove();\r\n	sel.find("option[value=''10'']").remove();\r\n	sel.find("option[value=''11'']").remove();\r\n	sel.find("option[value=''13'']").remove();\r\n	sel.find("option[value=''14'']").remove();\r\n	sel.find("option[value=''16'']").remove();\r\n	sel.find("option[value=''17'']").remove();\r\n  	\r\n  	if (sel.val() > 0)\r\n	{\r\n		cur_val = sel.val();\r\n	}\r\n  \r\n		\r\n	var show_hide_rel_fields = function(val)\r\n	{\r\n\r\n		//val_varchar\r\n		if (val == 1)\r\n		{\r\n			form_object.find("[name=val_varchar]").parent().parent().show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("[name=val_varchar]").parent().parent().hide();\r\n		}\r\n		\r\n		//val_script\r\n		if (val == 15)\r\n		{\r\n			form_object.find("[name=val_script]").parent().parent().show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("[name=val_script]").parent().parent().hide();\r\n		}\r\n		\r\n		//val_integer\r\n		if (val == 5)\r\n		{\r\n			form_object.find("[name=val_integer]").parent().parent().show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("[name=val_integer]").parent().parent().hide();\r\n		}\r\n		\r\n		//val_date\r\n		if (val == 9)\r\n		{\r\n			form_object.find("[name=val_date]").parent().parent().parent().show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("[name=val_date]").parent().parent().parent().hide();\r\n		}\r\n		\r\n		//val_file_name\r\n		if (val == 12)\r\n		{\r\n			form_object.find("[name=val_file_name]").parent().parent().parent().parent().parent().parent().parent().parent().show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("[name=val_file_name]").parent().parent().parent().parent().parent().parent().parent().parent().hide();\r\n		}\r\n		\r\n		//val_yesno\r\n		if (val == 7)\r\n		{\r\n			form_object.find("[name=val_yesno]").parent().parent().show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find("input[name=val_yesno]").parent().parent().hide();\r\n		}\r\n		\r\n	}\r\n	\r\n	var change_event = function(e)\r\n	{\r\n		if (e)\r\n		{\r\n			show_hide_rel_fields($(this).val());\r\n		}\r\n		else\r\n		{\r\n			show_hide_rel_fields(cur_val);\r\n		}\r\n	}\r\n\r\n	change_event(null);\r\n	\r\n	sel.on(''change'', change_event);\r\n}\r\n', NULL, NULL, NULL, NULL),
(12, 4, 'Pievieno reģistra darbību pogu', 'var btns_div = form_object.find(".dx_form_btns_left");\r\n\r\nvar make_button = function() {\r\n\r\n	if ($("#btn_group_" + form_object.attr(''id'')).length != 0) {\r\n		return; // poga jau ir pievienota\r\n	}\r\n	\r\n	btns_div.append( "<div class=''btn-group'' id=''btn_group_" + form_object.attr(''id'') + "''><button  type=''button'' class=''btn btn-white dropdown-toggle'' data-toggle=''dropdown'' aria-haspopup=''true'' aria-expanded=''false''><i class=''fa fa-cog''></i> Darbības <i class=''fa fa-caret-down''></i></button><ul class=''dropdown-menu''><li><a href=''#'' id=''copy_" + form_object.attr(''id'') + "''>Kopēt skatu</a></li><li><a href=''#'' id=''delete_" + form_object.attr(''id'') + "''>Dzēst skatu</a></li></ul></div>" );\r\n	\r\n	$("#copy_" + form_object.attr(''id'')).click(function(){\r\n		var item_id = form_object.find("input[name=id]").val();\r\n		var item_url = ''/structure/form/view_copy'';\r\n		var item_title = ''Skata kopēšana'';\r\n		\r\n		get_popup_item_by_id(item_id, item_url, item_title);\r\n		\r\n	});\r\n	\r\n	$("#delete_" + form_object.attr(''id'')).click(function(){\r\n		var item_id = form_object.find("input[name=id]").val();\r\n		var item_url = ''/structure/form/view_delete'';\r\n		var item_title = ''Skata dzēšana'';\r\n		\r\n		get_popup_item_by_id(item_id, item_url, item_title);\r\n		\r\n	});\r\n};\r\n\r\nif (btns_div)\r\n{\r\n	make_button();\r\n}', 1, '2016-03-13 08:45:59', 1, '2016-03-13 09:41:06'),
(13, 49, 'Parāda vai paslēpj laukus atkarībā no izvēlētā attēlošanas veida.', 'var sel = form_object.find("[name=content_id]");\r\nif (sel)\r\n{\r\n	var cur_val = 0;\r\n	if (sel.val() > 0)\r\n	{\r\n		cur_val = sel.val();\r\n	}\r\n	\r\n	var show_show_hide_rel_fields = function(val)\r\n	{		\r\n		if (val == 1) // teksts\r\n		{\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=is_top_article]").show();\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=is_static]").show();\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=alternate_url]").show();\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=article_text]").show();\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=author_id]").show();\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=video_galery_id]").show();\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=picture_galery_id]").show();\r\n			\r\n			form_object.find("div[dx_attr=tab] ul.nav-tabs li").show(); // parādam visus tabus\r\n		}\r\n		else\r\n		{\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=is_top_article]").hide()\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=is_static]").hide();\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=alternate_url]").hide();\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=article_text]").hide();\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=author_id]").hide();\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=video_galery_id]").hide();\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=picture_galery_id]").hide();\r\n						\r\n			form_object.find("div[dx_attr=tab] ul.nav-tabs li").first().find("a").trigger( "click" ); // iezīmju tabs ir kā pirmais\r\n			setTimeout(function() {\r\n                form_object.find("div[dx_attr=tab] ul.nav-tabs li").not(".active").hide();\r\n            }, 200);			\r\n			\r\n		}\r\n\r\n		if (val == 2) // ārējā saite\r\n		{			\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=outer_url]").show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=outer_url]").hide()\r\n		}\r\n		\r\n		if (val == 3) // lejuplādējama datne\r\n		{			\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=dwon_file_name]").show();\r\n		}\r\n		else\r\n		{\r\n			form_object.find(".dx-form-field-line[dx_fld_name_form=dwon_file_name]").hide()\r\n		}	\r\n		\r\n	}\r\n	\r\n	var change_event = function(e)\r\n	{\r\n		if (e)\r\n		{\r\n			show_show_hide_rel_fields($(this).val());\r\n		}\r\n		else\r\n		{\r\n			show_show_hide_rel_fields(cur_val);\r\n		}\r\n	}\r\n\r\n	change_event(null);\r\n	\r\n	sel.on(''change'', change_event);\r\n}', 1, '2016-03-14 13:41:15', 1, '2016-03-14 16:06:36'),
(14, 49, 'Automātiski formatē ziņas virsrakstu kā norādes tekstu - URL formātā.', 'var tit = form_object.find("input[name=title]");\r\nvar altern = form_object.find("input[name=alternate_url]");\r\n\r\nif (tit.length && altern.length)\r\n{	\r\n	var slug = function(str) {\r\n	  str = str.replace(/^\\s+|\\s+$/g, ''''); // trim\r\n	  str = str.toLowerCase();\r\n\r\n	  // remove accents, swap ñ for n, etc\r\n	  var from = "āčēģīķļņšūž·/_,:;";\r\n	  var to   = "acegiklnsuz------";\r\n	  for (var i=0, l=from.length ; i<l ; i++) {\r\n		str = str.replace(new RegExp(from.charAt(i), ''g''), to.charAt(i));\r\n	  }\r\n\r\n	  str = str.replace(/[^a-z0-9 -]/g, '''') // remove invalid chars\r\n		.replace(/\\s+/g, ''-'') // collapse whitespace and replace by -\r\n		.replace(/-+/g, ''-''); // collapse dashes\r\n\r\n	  return str;\r\n	};\r\n	\r\n	var change_event = function(e)\r\n	{\r\n		if (e)\r\n		{\r\n			altern.val(slug($(this).val()));\r\n		}		\r\n	}\r\n	\r\n	tit.on(''keyup'', change_event);\r\n	tit.on(''change'', change_event);\r\n}', 1, '2016-03-14 17:09:08', 1, '2016-03-14 17:40:44');

-- --------------------------------------------------------

--
-- Table structure for table `dx_forms_tabs`
--

CREATE TABLE IF NOT EXISTS `dx_forms_tabs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `form_id` int(11) NOT NULL COMMENT 'Reference to form',
  `title` varchar(100) DEFAULT NULL COMMENT 'Tab section title',
  `grid_list_id` int(11) NOT NULL COMMENT 'Reference to register - rendered in the tab as grid',
  `grid_list_field_id` int(11) NOT NULL COMMENT 'Reference to the register''s field used to join',
  `grid_list_field2_id` int(11) DEFAULT NULL COMMENT 'Reference to the register''s field used to join',
  `order_index` int(11) NOT NULL DEFAULT '0' COMMENT 'Tab section order index',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `form_id` (`form_id`,`grid_list_id`,`grid_list_field_id`),
  KEY `grid_list_field2_id` (`grid_list_field2_id`),
  KEY `order_index` (`order_index`),
  KEY `grid_list_id` (`grid_list_id`),
  KEY `grid_list_field_id` (`grid_list_field_id`),
  KEY `grid_list_field2_id_2` (`grid_list_field2_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Datu ievades formas sadaļas - izmanto pakārtoto reģistru datu attēlošanai (1:n). Saskarnē sadaļas attēlojas formas apakšā un sadaļā tiek attēlots pakārtotā reģistra datu saraksts.' AUTO_INCREMENT=73 ;

--
-- Dumping data for table `dx_forms_tabs`
--

INSERT INTO `dx_forms_tabs` (`id`, `form_id`, `title`, `grid_list_id`, `grid_list_field_id`, `grid_list_field2_id`, `order_index`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 4, 'Lauki', 7, 17, NULL, 1, NULL, NULL, NULL, NULL),
(2, 4, 'Skati', 6, 29, NULL, 2, NULL, NULL, NULL, NULL),
(3, 4, 'Formas', 10, 32, NULL, 3, NULL, NULL, NULL, NULL),
(4, 6, 'Lauki', 9, 43, NULL, 1, NULL, NULL, NULL, NULL),
(5, 6, 'Sadaļas', 16, 66, NULL, 2, NULL, NULL, NULL, NULL),
(9, 8, 'Lauki', 11, 37, NULL, 1, NULL, NULL, NULL, NULL),
(10, 19, 'Lomas', 22, 101, NULL, 1, NULL, NULL, NULL, NULL),
(11, 18, 'Lietotāji', 22, 102, NULL, 1, NULL, NULL, NULL, NULL),
(12, 18, 'Reģistri', 23, 104, NULL, 2, NULL, NULL, NULL, NULL),
(13, 4, 'Lomas', 23, 105, NULL, 4, NULL, NULL, NULL, NULL),
(17, 11, 'Izmantots laukos', 7, 21, NULL, 1, NULL, NULL, NULL, NULL),
(18, 3, 'Izmantots reģistros', 3, 4, NULL, 1, NULL, NULL, NULL, NULL),
(19, 4, 'Navigācija', 5, 54, NULL, 5, NULL, NULL, NULL, NULL),
(45, 83, 'Darbinieka vēsture', 95, 581, NULL, 0, NULL, NULL, NULL, NULL),
(47, 6, 'JavaScript', 99, 614, NULL, 30, NULL, NULL, NULL, NULL),
(48, 18, 'Lapas', 101, 623, NULL, 30, NULL, NULL, NULL, NULL),
(49, 89, 'Lomas', 101, 624, NULL, 10, NULL, NULL, NULL, NULL),
(50, 87, 'Izmantots laukos', 7, 611, NULL, 10, NULL, NULL, NULL, NULL),
(52, 4, 'Darbplūsma', 110, 657, NULL, 20, NULL, NULL, NULL, NULL),
(54, 98, 'Aizpildāmie lauki', 115, 693, NULL, 10, NULL, NULL, NULL, NULL),
(55, 83, 'Atbildības darbplūsmās', 110, 660, NULL, 20, NULL, NULL, NULL, NULL),
(56, 49, 'Iezīmes', 127, 814, NULL, 10, NULL, NULL, NULL, NULL),
(61, 150, 'Iezīmes', 127, 814, NULL, 10, NULL, NULL, NULL, NULL),
(62, 150, 'Attēli', 191, 1189, NULL, 5, NULL, NULL, NULL, NULL),
(63, 159, 'Atbilžu varianti', 200, 1253, NULL, 10, NULL, NULL, NULL, NULL),
(64, 161, 'Atbildes', 202, 1258, NULL, 10, NULL, NULL, NULL, NULL),
(66, 167, 'Iezīmes', 127, 814, NULL, 20, NULL, NULL, NULL, NULL),
(67, 167, 'Video', 205, 1266, NULL, 10, NULL, NULL, NULL, NULL),
(68, 175, 'Vēsture', 222, 1339, NULL, 10, NULL, NULL, NULL, NULL),
(69, 183, 'Izpildes vēsture', 225, 1372, NULL, 10, NULL, NULL, NULL, NULL),
(70, 49, 'Datnes', 232, 1401, NULL, 20, 1, '2016-03-11 17:05:19', 1, '2016-03-11 17:05:19'),
(71, 150, 'Ievietots ziņās', 234, 1431, NULL, 20, 1, '2016-03-15 18:32:49', 1, '2016-03-19 11:56:58'),
(72, 167, 'Ievietots ziņās', 234, 1432, NULL, 30, 1, '2016-03-15 18:38:36', 1, '2016-03-19 11:57:28');

-- --------------------------------------------------------

--
-- Table structure for table `dx_forms_types`
--

CREATE TABLE IF NOT EXISTS `dx_forms_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `dx_forms_types`
--

INSERT INTO `dx_forms_types` (`id`, `title`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Standarta', NULL, NULL, NULL, NULL),
(2, 'Pielāgota', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_item_access`
--

CREATE TABLE IF NOT EXISTS `dx_item_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) DEFAULT NULL COMMENT 'Reference to register',
  `list_item_id` int(11) DEFAULT NULL COMMENT 'Reference to item from register',
  `user_id` int(11) DEFAULT NULL COMMENT 'Reference to the user who have rights on item',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `list_id` (`list_id`,`user_id`,`list_item_id`),
  KEY `user_id` (`user_id`),
  KEY `list_id_2` (`list_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Tiesības uz reģistru atsevišķiem ierakstiem. Reģistru ierakstiem var norādīt, kuri lietotāji var piekļūt ierakstam. Ja ierakstam nav norādīts neviens lietotājs, tad var piekļūt visi lietotāji atbilstoši lomām. Ja ierakstam norādīts vismaz viens lietotājs, tad lomu tiesības vairs netiek ņemtas vērā.' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dx_item_statuses`
--

CREATE TABLE IF NOT EXISTS `dx_item_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `is_editable` int(11) NOT NULL DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `dx_item_statuses`
--

INSERT INTO `dx_item_statuses` (`id`, `title`, `is_editable`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Sagatavošana', 1, NULL, NULL, NULL, NULL),
(2, 'Saskaņošana', 0, NULL, NULL, NULL, NULL),
(3, 'Noraidīts', 1, NULL, NULL, NULL, NULL),
(4, 'Apstiprināts', 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_lists`
--

CREATE TABLE IF NOT EXISTS `dx_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_title` varchar(500) DEFAULT NULL COMMENT 'Register title',
  `url_title` varchar(500) DEFAULT NULL COMMENT 'Register URL name - used if register is rendered not by AJAX',
  `item_title` varchar(100) DEFAULT NULL COMMENT 'Title of register items',
  `object_id` int(11) NOT NULL COMMENT 'Reference to data object',
  `template_name` varchar(500) DEFAULT NULL,
  `template_guid` varchar(500) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  `group_id` int(10) unsigned DEFAULT NULL COMMENT 'Grupa',
  `hint` text COMMENT 'Paskaidrojums',
  PRIMARY KEY (`id`),
  KEY `object_id` (`object_id`),
  KEY `dx_lists_group_id_index` (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tabulā tiek reģistrēti visi reģistri. SVS visi dati tiek glabāti reģistros. Katram reģistram saskarnē ir vismaz viens saraksts un vismaz viena datu ievades forma. Reģistrus var savā starpā sasaistīt, piemēram, ir pamatdatu reģistri un ir klasifikaotru reģistri, kuri tiek izmantoti pamatdatu reģistros.' AUTO_INCREMENT=236 ;

--
-- Dumping data for table `dx_lists`
--

INSERT INTO `dx_lists` (`id`, `list_title`, `url_title`, `item_title`, `object_id`, `template_name`, `template_guid`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `group_id`, `hint`) VALUES
(3, 'Reģistri', 'registri', 'Reģistrs', 1, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Sistēmisks reģistrs, kurā tiek definēti visi SVS reģistri.'),
(4, 'Objekti', 'objekti', 'Objekts', 2, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Sistēmisks reģistrs, kurā tiek definēti visi SVS datu objekti. Datu objekts ir datu bāzes tabula. Katram datu objektam var būt izveidoti viens vai vairāki reģistri, ar kuriem definē datu bāzes tabulas datu skatus un datu ievades formas.'),
(5, 'Izvēlnes', 'izvelne', 'Izvēlne', 5, NULL, NULL, NULL, NULL, NULL, NULL, 2, 'Reģistrā iespējams pārvaldīt SVS kreisās puses izvēlnes. Izvēlnes var veidot koka struktūrā trijos līmeņos.'),
(6, 'Skati', 'skati', 'Skats', 6, NULL, NULL, NULL, NULL, NULL, NULL, 1, 'Reģistrā tiek definēti reģistru datu skatījumi - attēlojamās kolonnas, datu atlases nosacījumi un noklusētā datu kārtošana.'),
(7, 'Reģistru lauki', 'registru_lauki', 'Reģistra lauks', 7, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(8, 'Lauku tipi', 'lauku_tipi', 'Lauka tips', 8, NULL, NULL, NULL, NULL, 1, '2016-03-08 21:31:12', 6, NULL),
(9, 'Formu lauki', 'formu_lauki', 'Formas lauks', 9, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(10, 'Formas', 'formas', 'Forma', 10, NULL, NULL, NULL, NULL, 1, '2016-03-08 22:07:38', 1, 'Katram reģistram var norādīt vienu datu ievades formu. SVS struktūra gan pieļauj ievadīt vairākas formas, tomēr pašreizējā SVS versija izmantos tikai pirmo norādīto.'),
(11, 'Skatu lauki', 'skatu_lauki', 'Skata lauks', 11, NULL, NULL, NULL, NULL, NULL, NULL, 1, NULL),
(16, 'Sadaļas', 'sadalas', 'Sadaļa', 12, NULL, NULL, NULL, NULL, 1, '2016-03-08 22:18:16', 1, 'Reģistrā tiek saglabātas formas sadaļas. Katrai sadaļai var norādīt reģistru, no kura tiks attēloti dati, ar iespēju šos datus arī rediģēt/dzēst vai ievadīt jaunu.'),
(20, 'Lomas', 'lomas', 'Loma', 16, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL),
(21, 'Lietotāji', 'lietotaji', 'Lietotājs', 17, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL),
(22, 'Lietotāju lomas', 'lietotaju_lomas', 'Lietotāja loma', 18, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL),
(23, 'Lomu reģistri', 'lomu_registri', 'Lomu reģistrs', 19, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL),
(41, 'Datu modelis', NULL, NULL, 26, NULL, NULL, NULL, NULL, 1, '2016-03-08 21:36:01', 1, 'Reģistrā tiek norādītas sasaistes starp dažādiem reģistriem (relācijas), lai nodrošinātu iespēju skatos iekļaut citu reģistru laukus.'),
(43, 'Lauku operācijas', NULL, NULL, 27, NULL, NULL, NULL, NULL, 1, '2016-03-08 21:31:43', 6, NULL),
(44, 'Secību veidi', NULL, NULL, 28, NULL, NULL, NULL, NULL, 1, '2016-03-08 21:31:56', 6, NULL),
(48, 'Agregāciju veidi', NULL, NULL, 32, NULL, NULL, NULL, NULL, 1, '2016-03-08 21:32:04', 6, NULL),
(60, 'Ziņas', NULL, NULL, 40, NULL, NULL, NULL, NULL, 1, '2016-03-20 09:43:27', 4, 'Reģistrā iespējams pārvaldīt visu datu avotu (uzņēmumu, struktūrvienību) ziņas.'),
(82, 'Vārdadienas', NULL, NULL, 62, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(93, 'Notikumi', NULL, NULL, 72, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(94, 'Darbplūsmu lietotāji', NULL, NULL, 73, NULL, NULL, NULL, NULL, 1, '2016-03-08 09:35:43', 5, NULL),
(95, 'Darbinieku vēsture', NULL, NULL, 74, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(97, 'Portāla statistika', NULL, NULL, 76, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL),
(98, 'Numeratori', NULL, NULL, 77, NULL, NULL, NULL, NULL, 1, '2016-03-08 11:00:04', NULL, NULL),
(99, 'Formu JavaScript', NULL, NULL, 78, NULL, NULL, NULL, NULL, 1, '2016-03-20 15:12:34', 1, 'Reģistrā iespējams pārvaldīt SVS reģistru formās pievienotos JavaScript jQuery izpildes izejas kodus. Ar skriptiem iespējams izveidot specifiskas datu apstrādes, pielāgot formu funkcionalitāti u.c.'),
(100, 'Lapas', NULL, NULL, 79, NULL, NULL, NULL, NULL, 1, '2016-03-20 10:00:56', 2, 'Reģistrs nodrošina portāla lapu pārvaldību. Ir iespējams norādīt lapu HTML un tajā ievietot blokus, izmantojot speciālus kodus.'),
(101, 'Lapu lomas', NULL, NULL, 80, NULL, NULL, NULL, NULL, NULL, NULL, 3, NULL),
(102, 'Skatu veidi', NULL, NULL, 81, NULL, NULL, NULL, NULL, 1, '2016-03-08 21:32:20', 6, NULL),
(103, 'Lauku attēlošanas veidi', NULL, NULL, 82, NULL, NULL, NULL, NULL, 1, '2016-03-08 21:32:35', 6, NULL),
(106, 'Uzdevumu statusi', NULL, NULL, 83, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL),
(107, 'Uzdevumu veidi', NULL, NULL, 84, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL),
(108, 'Uzdevumi', NULL, NULL, 85, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL),
(109, 'Uzdevumu izpildītāju veidi', NULL, NULL, 86, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL),
(110, 'Darbplūsmas', NULL, NULL, 87, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL),
(113, 'Formu veidi', NULL, NULL, 88, NULL, NULL, NULL, NULL, 1, '2016-03-08 21:32:44', 6, NULL),
(114, 'Ierakstu statusi', NULL, NULL, 89, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL),
(115, 'Darbplūsmu lauki', NULL, NULL, 90, NULL, NULL, NULL, NULL, NULL, NULL, 5, NULL),
(119, 'Datu avoti', NULL, NULL, 92, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL),
(120, 'HTML bloki', NULL, NULL, 93, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL),
(121, 'Darbinieki', NULL, NULL, 94, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(126, 'Iezīmes', NULL, NULL, 95, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(127, 'Rakstu iezīmes', NULL, NULL, 96, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(136, 'Laikapstākļu veidi', NULL, NULL, 97, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL),
(137, 'Laika ziņu reģistrs', NULL, NULL, 98, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(188, 'Ziņu veidi', NULL, NULL, 101, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL),
(189, 'Attēlu galerijas', NULL, NULL, 40, NULL, NULL, NULL, NULL, 1, '2016-03-20 09:43:53', 4, NULL),
(191, 'Attēli', NULL, NULL, 102, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(193, 'Konfigurācija', NULL, NULL, 103, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL),
(194, 'Satura bloki', NULL, NULL, 93, NULL, NULL, NULL, NULL, 1, '2016-03-20 09:51:23', 4, 'Intranet lapās ievietoto satura bloku reģistrs. Reģistrs paredzēts portāla satura administratoriem, tāpēc reģistra formā var mainīt tikai satura lauku.'),
(195, 'Iesūtītie jautājumi', NULL, NULL, 104, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(198, 'Aptauju jautājumi', NULL, NULL, 107, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(200, 'Atbilžu varianti', NULL, NULL, 108, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(202, 'Atbildes uz jautājumiem', NULL, NULL, 109, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(205, 'Video', NULL, NULL, 110, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(208, 'Video galerijas', NULL, NULL, 40, NULL, NULL, NULL, NULL, 1, '2016-03-20 09:44:54', 4, 'Video galeriju reģistrs. Video galerijai var pievienot MP4 video datnes un/vai Youtube saites.'),
(209, 'Jautājumu nodaļas', NULL, NULL, 111, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(211, 'Biežāk uzdotie jautājumi', NULL, NULL, 112, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(212, 'Izdevumu tipi', NULL, NULL, 113, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(214, 'Izdevumi', NULL, NULL, 114, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(215, 'Darbību veidi', NULL, NULL, 115, NULL, NULL, NULL, NULL, 1, '2016-03-08 21:33:01', 6, NULL),
(216, 'Lietotāju darbības', NULL, NULL, 116, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL),
(222, 'Izmaiņu vēsture', NULL, NULL, 117, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL),
(223, 'Struktūrvienības', NULL, NULL, 118, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(224, 'Procesi', NULL, NULL, 119, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL),
(225, 'Procesu vēsture', NULL, NULL, 120, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL),
(226, 'Sistēmas', NULL, NULL, 121, NULL, NULL, NULL, NULL, NULL, NULL, 4, NULL),
(227, 'Sistēmu incidenti', NULL, NULL, 122, NULL, NULL, NULL, NULL, 1, '2016-03-20 15:43:32', 2, NULL),
(229, 'Lotus Notes sistēmas', NULL, NULL, 123, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL),
(231, 'Reģistru grupas', NULL, NULL, 124, NULL, NULL, NULL, NULL, 1, '2016-03-08 21:34:19', 6, 'Reģistru grupas tiek izmantotas, lai klasificētu reģistrus pēc to loģiskās funkcionalitātes - atbilstoši grupām tiek ģenerētas lietotāju rokasgrāmatas sadaļas.'),
(232, 'Ziņu datnes', NULL, NULL, 125, NULL, NULL, NULL, NULL, 1, '2016-03-11 16:49:54', 4, 'Ziņām var piesaistīt vienu vai vairākas datnes, piemēram, PDF formātā. Piesaistītās datnes tiks attēlotas ziņas detalizācijas beigās.'),
(233, 'Ziņu papildus veidi', NULL, NULL, 126, NULL, NULL, NULL, NULL, 1, '2016-03-14 12:37:36', 2, 'Šajā reģistrā ir klasifikators ar ziņu papildus veidiem, ar kuriem var noteikt, kādā veidā ziņas jāattēlo ziņu plūsmā.'),
(234, 'Ziņas - galeriju sasaiste', NULL, NULL, 40, NULL, NULL, NULL, NULL, 1, '2016-03-20 09:45:30', 4, 'Šis reģistrs izmantot tos pašus datus ko Ziņu reģistrs. Reģistrs nodrošina galeriju sadaļas "Izmantots ziņās" funkcionalitāti.'),
(235, 'Satura bloku administrēšana', NULL, NULL, 93, NULL, NULL, NULL, NULL, 1, '2016-03-20 09:53:27', 2, 'Reģistrs paredzēts SVS administratoriem, lai pārvaldītu portālā ievietoto satura bloku uzstādījumus.');

-- --------------------------------------------------------

--
-- Table structure for table `dx_lists_fields`
--

CREATE TABLE IF NOT EXISTS `dx_lists_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL COMMENT 'Reference to register',
  `db_name` varchar(100) DEFAULT NULL COMMENT 'Fields name in the object data table - MySQL db name',
  `type_id` int(11) NOT NULL COMMENT 'Reference to fields type',
  `title_list` varchar(100) DEFAULT NULL COMMENT 'Field''s title in the lists',
  `title_form` varchar(100) DEFAULT NULL COMMENT 'Field''s title in the forms',
  `max_lenght` int(11) NOT NULL DEFAULT '0' COMMENT 'Maximum lenght of the data - used for strings',
  `is_required` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Indicates if field is required (0 - no, 1 - yes)',
  `rel_list_id` int(11) DEFAULT NULL COMMENT 'Reference to related register - used for lookup fields to load data in dropdowns from related classifiers',
  `rel_display_field_id` int(11) DEFAULT NULL COMMENT 'Reference to related register field - used to display in dropdowns',
  `rel_parent_field_id` int(11) DEFAULT NULL,
  `rel_view_id` int(11) DEFAULT NULL COMMENT 'Reference to related register view - used to show dropdown values with special criteria',
  `rel_display_formula_field` varchar(4000) DEFAULT NULL COMMENT 'Reference to related register field which is an formula',
  `binded_field_id` int(11) DEFAULT NULL COMMENT 'Reference to field which depends on this field (from the same register) - used in dropdown B depends on value from the dropdown A. Then to A is added jquery script which reloads B when A changes',
  `binded_rel_field_id` int(11) DEFAULT NULL COMMENT 'Reference field which is used in SQL to get related vallues depending on A dropdown (see description of the field binded_field_id)',
  `default_value` varchar(500) DEFAULT NULL COMMENT 'The default value of the field - for strings it can be test, for dropdowns the ID value, for users it can be [ME] which means the ID of current logged in user',
  `formula` varchar(2000) DEFAULT NULL COMMENT 'The SQL formula to aggregate value for this field - can be any SQL formula, in the formula fields must be used in [] and must be used field names which are used in grids',
  `is_public_file` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Indicates if field is public file (0 - no, 1 - yes). Files are stored either in intranet or internet access folders - this mark indicates where to store uploaded files.',
  `is_image_file` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Indicates if file is an image (0 - no, 1 - yes). For images there are shown preview in CMS interface.',
  `numerator_id` int(11) DEFAULT NULL,
  `is_word_generation` int(11) DEFAULT '0',
  `is_multiple_files` tinyint(1) DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  `hint` text COMMENT 'Paskaidrojums',
  `is_clean_html` tinyint(1) DEFAULT '0' COMMENT 'Ir HTML tīrīšana',
  PRIMARY KEY (`id`),
  KEY `list_id` (`list_id`,`rel_list_id`,`binded_field_id`),
  KEY `db_name` (`db_name`),
  KEY `type_id` (`type_id`),
  KEY `rel_list_id` (`rel_list_id`),
  KEY `rel_display_field_id` (`rel_display_field_id`),
  KEY `binded_field_id` (`binded_field_id`),
  KEY `binded_rel_field_id` (`binded_rel_field_id`),
  KEY `formula` (`formula`(255)),
  KEY `rel_view_id` (`rel_view_id`),
  KEY `numerator_id` (`numerator_id`),
  KEY `rel_parent_field_id` (`rel_parent_field_id`),
  KEY `is_word_generation` (`is_word_generation`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tabulā tiek saglabāti visu reģistru lauki. SVS var definēt reģistrus un to laukus, kas saskarnē tiks attēloti kā kolonnas sarakstos vai kā datu ievades lauki formās.' AUTO_INCREMENT=1445 ;

--
-- Dumping data for table `dx_lists_fields`
--

INSERT INTO `dx_lists_fields` (`id`, `list_id`, `db_name`, `type_id`, `title_list`, `title_form`, `max_lenght`, `is_required`, `rel_list_id`, `rel_display_field_id`, `rel_parent_field_id`, `rel_view_id`, `rel_display_formula_field`, `binded_field_id`, `binded_rel_field_id`, `default_value`, `formula`, `is_public_file`, `is_image_file`, `numerator_id`, `is_word_generation`, `is_multiple_files`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hint`, `is_clean_html`) VALUES
(1, 3, 'list_title', 1, 'Nosaukums', 'Nosaukums', 500, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(4, 3, 'object_id', 3, 'Objekts', 'Objekts', 0, 1, 4, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(5, 4, 'db_name', 1, 'Tabula', 'Tabulas nosaukums', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(6, 4, 'title', 1, 'Objekts', 'Objekta nosaukums', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(7, 3, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(8, 4, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(16, 7, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(17, 7, 'list_id', 3, 'Reģistrs', 'Reģistrs', 0, 1, 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(18, 8, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(19, 8, 'title', 1, 'Nosaukums', 'Nosaukums', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(20, 7, 'db_name', 1, 'Nosaukums DB', 'Nosaukums DB', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(21, 7, 'type_id', 3, 'Tips', 'Lauka tips', 0, 1, 8, 19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(22, 7, 'title_list', 1, 'Nosaukums sarakstā', 'Nosaukums sarakstā', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(23, 7, 'title_form', 1, 'Nosaukums formā', 'Nosaukums formā', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(24, 7, 'max_lenght', 5, 'Maksimālais garums', 'Maksimālais garums', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(25, 7, 'is_required', 7, 'Obligāts', 'Obligāts', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(26, 7, 'rel_list_id', 3, 'Uzmeklēšanas saraksts', 'Uzmeklēšanas saraksts', 0, 0, 3, 1, NULL, NULL, NULL, 27, 17, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(27, 7, 'rel_display_field_id', 3, 'Uzmeklēšanas lauks', 'Uzmeklēšanas lauks', 0, 0, 7, 22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(28, 6, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(29, 6, 'list_id', 3, 'Reģistrs', 'Reģistrs', 0, 1, 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(30, 6, 'title', 1, 'Nosaukums', 'Nosaukums', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(31, 6, 'is_default', 7, 'Ir noklusētais', 'Ir noklusētais', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(32, 10, 'list_id', 3, 'Formas reģistrs', 'Formas reģistrs', 0, 1, 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(33, 10, 'title', 1, 'Formas nosaukums', 'Formas nosaukums', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(34, 10, 'zones_count', 5, 'Zonu skaits', 'Zonu skaits', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(35, 10, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(36, 11, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(37, 11, 'view_id', 3, 'Skats', 'Skats', 0, 1, 6, 30, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(38, 11, 'field_id', 3, 'Skata lauks', 'Skata lauks', 0, 1, 7, 22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(39, 11, 'width', 5, 'Platums', 'Platums', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(40, 11, 'order_index', 5, 'Secība', 'Secība', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(41, 11, 'is_item_link', 7, 'Ir saite', 'Ir saite', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(42, 9, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(43, 9, 'form_id', 3, 'Forma', 'Forma', 0, 1, 10, 33, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(44, 9, 'list_id', 3, 'Reģistrs', 'Reģistrs', 0, 1, 3, 1, NULL, NULL, NULL, 45, 17, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(45, 9, 'field_id', 3, 'Lauks', 'Lauks', 0, 1, 7, 22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(46, 9, 'zone_nr', 5, 'Zonas numurs', 'Zonas numurs', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(47, 9, 'is_hidden', 7, 'Ir neredzams', 'Ir neredzams', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(48, 9, 'order_index', 5, 'Secība', 'Secība', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(49, 9, 'is_readonly', 7, 'Ir nerediģējams', 'Ir nerediģējams', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(50, 11, 'list_id', 3, 'Reģistrs', 'Reģistrs', 0, 1, 3, 1, NULL, NULL, NULL, 38, 17, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(51, 5, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(52, 5, 'title', 1, 'Nosaukums', 'Nosaukums', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(53, 5, 'parent_id', 14, 'Vecāks', 'Vecāks', 0, 0, 5, 1194, 53, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(54, 5, 'list_id', 3, 'Reģistrs', 'Reģistrs', 0, 0, 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(55, 5, 'order_index', 5, 'Secība', 'Secība', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(65, 16, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(66, 16, 'form_id', 3, 'Forma', 'Forma', 0, 1, 10, 33, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(67, 16, 'title', 1, 'Nosaukums', 'Nosaukums', 50, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(68, 16, 'grid_list_id', 3, 'Saistītais reģistrs', 'Saistītais reģistrs', 0, 1, 3, 1, NULL, NULL, NULL, 69, 17, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(69, 16, 'grid_list_field_id', 3, 'Saistītā reģistra lauks', 'Saistītā reģistra lauks', 0, 1, 7, 22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(72, 16, 'grid_list_field2_id', 3, 'Saistītā reģistra papildus lauks', 'Saistītā reģistra papildus lauks', 0, 0, 7, 22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(73, 16, 'order_index', 5, 'Secība', 'Secība', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(91, 20, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(92, 20, 'title', 1, 'Nosaukums', 'Nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(93, 20, 'is_system', 7, 'Ir sistēmas', 'Ir sistēmas loma', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(94, 20, 'description', 4, 'Apraksts', 'Apraksts', 2000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(95, 21, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(96, 21, 'login_name', 1, 'Lietotājvārds', 'Lietotājvārds', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(97, 21, 'password', 16, 'Parole', 'Parole', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(98, 21, 'email', 11, 'E-pasts', 'E-pasts', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(99, 21, 'display_name', 1, 'Nosaukums', 'Vārds, uzvārds', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(100, 22, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(101, 22, 'user_id', 8, 'Lietotājs', 'Lietotājs', 0, 1, 21, 99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(102, 22, 'role_id', 3, 'Loma', 'Loma', 0, 1, 20, 92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(103, 23, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(104, 23, 'role_id', 3, 'Loma', 'Loma', 0, 1, 20, 92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(105, 23, 'list_id', 3, 'Reģistrs', 'Reģistrs', 0, 1, 3, 1, NULL, NULL, NULL, 161, 17, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(106, 23, 'is_edit_rights', 7, 'Rediģēšana', 'Ir rediģēšanas tiesības', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(135, 8, 'sys_name', 1, 'Kods', 'Kods', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(136, 8, 'is_readonly', 7, 'Nerediģējams', 'Ir nerediģējams', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(137, 4, 'is_history_logic', 7, 'Vēsture', 'Ir vēstures veidošana', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(147, 7, 'default_value', 1, 'Noklusētā vērtība', 'Noklusētā vērtība', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(161, 23, 'user_field_id', 3, 'Lietotāja ieraksti', 'Lietotāja ieraksti', 0, 0, 7, 23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(163, 7, 'formula', 1, 'Attēlošanas formula', 'Attēlošanas formula', 2000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(165, 41, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(166, 41, 'parent_list_id', 3, 'Galvenais reģistrs', 'Galvenais reģistrs', 0, 1, 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(167, 41, 'child_list_id', 3, 'Saistītais reģistrs', 'Saistītais reģistrs', 0, 1, 3, 1, NULL, NULL, NULL, 168, 17, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(168, 41, 'child_rel_field_id', 3, 'Saites lauks', 'Saites lauks', 0, 1, 7, 22, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(169, 11, 'alias_name', 1, 'Cits nosaukums', 'Cits nosaukums', 200, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(176, 43, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(177, 43, 'title', 1, 'Nosaukums', 'Nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(178, 43, 'sys_name', 1, 'SQL komanda', 'SQL komanda', 50, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(179, 11, 'operation_id', 3, 'Operācija', 'Operācija', 0, 0, 43, 177, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(180, 11, 'criteria', 1, 'Kritērijs', 'Kritērijs', 2000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(181, 11, 'is_hidden', 7, 'Ir neredzams', 'Ir neredzams', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(184, 44, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(185, 44, 'title', 1, 'Nosaukums', 'Nosaukums', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(186, 44, 'sys_name', 1, 'SQL operācija', 'SQL operācija', 50, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(187, 11, 'sort_type_id', 3, 'Kārtošana', 'Kārtošana', 0, 0, 44, 185, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(195, 11, 'is_sum', 7, 'Ir summēšana', 'Ir summēšana', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(196, 6, 'is_hidden_from_tabs', 7, 'Nerādīt sadaļās', 'Nerādīt sadaļās', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 21:40:02', 'Ja norādīta vērtība "Jā", tad skats netiks attēlots reģistra formu sadaļās.', 0),
(197, 6, 'is_hidden_from_main_grid', 7, 'Nerādīt reģistrā', 'Nerādīt reģistrā', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 21:40:41', 'Ja norādīts "Jā", tad skats netiks attēlots reģistra galvenajā skatījumā (lapā).', 0),
(206, 48, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(207, 48, 'title', 1, 'Nosaukums', 'Nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(208, 48, 'sys_name', 1, 'SQL komanda', 'SQL komanda', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(209, 11, 'aggregation_id', 3, 'Agregācijas veids', 'Agregācijas veids', 0, 0, 48, 207, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(257, 7, 'rel_display_formula_field', 1, 'Uzmeklēšanas skata formula', 'Uzmeklēšanas skata formula', 4000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(258, 7, 'rel_view_id', 3, 'Uzmeklēšanas skats', 'Uzmeklēšanas skats', 0, 0, 6, 30, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(259, 5, 'url', 1, 'URL', 'URL', 2000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(260, 21, 'position_title', 1, 'Amats', 'Amats', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(262, 7, 'is_public_file', 7, 'Ir publiska datne', 'Ir publiska datne', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(263, 7, 'is_image_file', 7, 'Ir attēla datne', 'Ir attēla datne', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(286, 60, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(287, 60, 'title', 1, 'Virsraksts', 'Virsraksts', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(288, 60, 'article_text', 10, 'Saturs', 'Saturs', 4000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-14 14:12:33', NULL, 1),
(289, 60, 'picture_name', 12, 'Attēls', 'Attēls', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, 0, NULL, NULL, 1, '2016-03-15 20:00:20', 'Attēls portālā tiks parādīts ziņu plūsmā un/vai aktīvo ziņu slīdrādē, bet netiks parādīts skatot ziņas detalizācijas lapu. Attēla izmēriem jābūt 1420px (platums) x 840px (augstums) vai lielāki izmēri, bet noteikti saglabājot proporciju.', 0),
(290, 60, 'order_index', 5, 'Secība', 'Secība', 10, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100', NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-18 17:30:40', 'Norādot vērtības mazākas kā noklusētā, ir iespējams pacelt ziņu augstāk ziņu plūsmā. Šo var izmantot, lai noteiktas ziņas ilgāk rādītos kā pirmās.', 0),
(291, 60, 'is_active', 7, 'Publicēts', 'Ir publicēts', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 12:48:29', 'Ja lauka vērtība ir "Nē", tad ziņa nav pieejama portālā - ne plūsmā, ne meklētājā.', 0),
(427, 23, 'is_delete_rights', 7, 'Dzēšana', 'Ir dzēšanas tiesības', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-20 15:27:48', NULL, 0),
(428, 23, 'is_new_rights', 7, 'Jauns', 'Var ievadīt jaunu', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-20 15:27:57', NULL, 0),
(429, 82, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(430, 82, 'month', 5, 'Mēnesis', 'Mēnesis', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(431, 82, 'day', 5, 'Diena', 'Diena', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(432, 82, 'txt', 1, 'Vārdi', 'Vārdi', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(546, 60, 'intro_text', 4, 'Ievadteksts', 'Ievadteksts', 1000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 12:49:10', 'Ievadteksts portālā tiek attēlots ziņu plūsmā (un meklēšanas rezultātos).', 0),
(547, 60, 'publish_time', 2, 'Datums', 'Datums', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(566, 93, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(567, 93, 'title', 1, 'Nosaukums', 'Nosaukums', 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(568, 93, 'description', 4, 'Apraksts', 'Apraksts', 1000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(569, 93, 'picture_name', 12, 'Attēls', 'Attēls', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(570, 93, 'event_time_from', 2, 'Datums (no)', 'Datums (no)', 30, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(571, 93, 'event_time_to', 2, 'Datums (līdz)', 'Datums (līdz)', 30, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(572, 93, 'address', 1, 'Adrese', 'Adrese', 200, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(573, 93, 'is_active', 7, 'Aktīvs', 'Aktīvs', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(574, 94, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(575, 94, 'login_name', 1, 'Lietotājvārds', 'Lietotājvārds', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(576, 94, 'email', 11, 'E-pasts', 'E-pasts', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(577, 94, 'display_name', 1, 'Nosaukums', 'Vārds, Uzvārds', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(579, 94, 'position_title', 1, 'Amats', 'Amats', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(580, 95, 'id', 6, 'ID', 'ID', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(581, 95, 'user_id', 3, 'Darbinieks', 'Darbinieks', 100, 1, 94, 577, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(582, 95, 'old_position', 1, 'Iepriekšējais amats', 'Iepriekšējais amats', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(583, 95, 'new_position', 1, 'Jaunais amats', 'Jaunais amats', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(584, 95, 'old_department', 1, 'Iepriekšējā struktūrvienība', 'Iepriekšējā struktūrvienība', 2000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(585, 95, 'new_department', 1, 'Jaunā struktūrvienība', 'Jaunā struktūrvienība', 2000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(586, 95, 'valid_from', 9, 'Spēkā no', 'Spēkā no', 30, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(587, 95, 'valid_to', 9, 'Spēkā līdz', 'Spēkā līdz', 30, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(591, 94, 'picture_name', 12, 'Attēls', 'Attēls', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(592, 94, 'birth_date', 9, 'Dzimšanas datums', 'Dzimšanas datums', 30, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(594, 94, 'description', 10, 'Apraksts', 'Apraksts', 4000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(595, 94, 'phone', 1, 'Tālrunis', 'Tālrunis', 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(596, 94, 'mobile', 1, 'Mobilais', 'Mobilais', 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(597, 94, 'fax', 1, 'Fakss', 'Fakss', 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(598, 60, 'is_top_article', 7, 'Ir aktīvā ziņa', 'Ir aktīvā ziņa', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 12:49:40', 'Norādot "Jā", ziņa tiks attēlota arī aktīvo ziņu slīdrādes blokā.', 0),
(599, 97, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(600, 97, 'title', 1, 'Rādītājs', 'Rādītājs', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(601, 97, 'cnt', 5, 'Skaits', 'Skaits', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(602, 97, 'order_index', 5, 'Secība', 'Secība', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(603, 98, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(604, 98, 'title', 1, 'Nosaukums', 'Nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(605, 98, 'mask', 1, 'Formāts', 'Formāts', 200, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(606, 98, 'next_counter', 5, 'Nākamais numurs', 'Nākamais numurs', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(607, 98, 'current_year', 5, 'Gads', 'Gads', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(611, 7, 'numerator_id', 3, 'Numerators', 'Numerators', 0, 0, 98, 604, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(612, 98, 'counter_lenght', 5, 'Numura garums', 'Numura garums', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(613, 99, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(614, 99, 'form_id', 3, 'Forma', 'Forma', 0, 1, 10, 33, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(615, 99, 'title', 1, 'Nosaukums', 'Nosaukums', 500, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(616, 99, 'js_code', 15, 'JavaScript', 'JavaScript', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 22:03:46', 'SVS ir definēts formas ietvaros pieejams jQuery elements form_object, kuram darbojas visas jQuery metodes, piemēram, ar .find("[name=...]") var atrast atbilstošos formas laukus, "name" jānorāda lauka nosaukums datu bāzē.', 0),
(617, 100, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(618, 100, 'title', 1, 'Nosaukums', 'Nosaukums', 1000, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(619, 100, 'html', 15, 'HTML', 'HTML', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(620, 100, 'is_active', 7, 'Aktīva', 'Aktīva', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(621, 100, 'url_title', 1, 'URL', 'URL', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(622, 101, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(623, 101, 'role_id', 3, 'Loma', 'Loma', 0, 1, 20, 92, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(624, 101, 'page_id', 3, 'Lapa', 'Lapa', 0, 1, 100, 618, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(625, 102, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(626, 102, 'title', 1, 'Nosaukums', 'Nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(627, 103, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(628, 103, 'title', 1, 'Nosaukums', 'Nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(629, 6, 'view_type_id', 3, 'Attēlošana', 'Attēlošanas veids', 0, 1, 102, 626, NULL, NULL, NULL, NULL, NULL, '1', NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 21:57:33', 'Atkarībā no norādītās vērtības, var būt/nebūt pieejams SQL izteiksmes ievadīšanas lauks.', 0),
(630, 11, 'represent_id', 3, 'Attēlošanas veids', 'Attēlošanas veids', 0, 0, 103, 628, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(642, 106, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(643, 106, 'title', 1, 'Nosaukums', 'Nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(644, 107, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(645, 107, 'title', 1, 'Nosaukums', 'Nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(646, 108, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(647, 108, 'v_list_title', 1, 'Reģistrs', 'Reģistrs', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(648, 108, 'v_doc_nr', 1, 'Dok. reģ. nr.', 'Dok. reģ. nr.', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(649, 108, 'v_doc_info', 4, 'Dok. saturs', 'Dok. saturs', 4000, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(650, 108, 'v_task_type', 1, 'Uzdevums', 'Uzdevums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(651, 108, 'v_task_created', 2, 'Uzdots', 'Uzdots', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(652, 108, 'v_task_status', 1, 'Statuss', 'Statuss', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(653, 6, 'custom_sql', 15, 'SQL', 'SQL', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'SELECT * FROM <table_name>', NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 21:48:24', 'Laukā var norādīt vērtību, ja izvēlēts atbilstošs skata veids. Laukā jānorāda SQL izteiksme, kurā var tikt izmantoti sekojoši iebūvētie SVS kodi: [ME] - pašreizējā autorizētā lietotāja ID, no tabulas dx_users, [ITEM_ID] - ja skats tiek izsaukts formas sadaļā, tad šajā parametrā glabājas formas ieraksta ID, piemēram, dokumenta formā jāattēlo saistītie uzdevumi, tad uzdevumu SQL [ITEM_ID] būs dokumenta ID.', 0),
(654, 109, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(655, 109, 'title', 1, 'Nosaukums', 'Nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(656, 110, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(657, 110, 'list_id', 3, 'Reģistrs', 'Reģistrs', 0, 1, 3, 1, NULL, NULL, NULL, 668, 17, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(658, 110, 'task_type_id', 3, 'Uzdevuma veids', 'Uzdevuma veids', 0, 1, 107, 645, NULL, NULL, NULL, NULL, NULL, '1', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(659, 110, 'task_perform_id', 3, 'Izpildītāja veids', 'Izpildītāja veids', 0, 0, 109, 655, NULL, NULL, NULL, NULL, NULL, '1', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(660, 110, 'employee_id', 8, 'Darbinieks', 'Darbinieks', 0, 0, 94, 577, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(661, 110, 'step_nr', 5, 'Soļa numurs', 'Soļa numurs', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '10', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(662, 110, 'yes_step_nr', 5, '''Jā'' solis', '''Jā'' solis', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(664, 110, 'no_step_nr', 5, '''Nē'' solis', '''Nē'' solis', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(665, 110, 'step_title', 1, 'Soļa nosaukums', 'Soļa nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(666, 110, 'notes', 4, 'Piezīmes uzdevumam', 'Piezīmes uzdevumam', 40000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(667, 110, 'term_days', 5, 'Termiņš (dienas)', 'Termiņš (dienas)', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(668, 110, 'field_id', 3, 'Lauks', 'Lauks', 0, 0, 7, 23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(669, 110, 'field_value', 1, 'Vērtība', 'Vērtība', 2000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(670, 110, 'field_operation_id', 3, 'Operācija', 'Operācija', 0, 0, 43, 177, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(679, 113, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(680, 113, 'title', 1, 'Nosaukums', 'Nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(681, 10, 'form_type_id', 3, 'Veids', 'Veids', 0, 1, 113, 680, NULL, NULL, NULL, NULL, NULL, '1', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(682, 10, 'custom_url', 1, 'URL', 'URL', 1000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 22:12:06', 'Lauks pieejams, ja norādīts formas veids "Pielāgota". Laukā var norādīt URL uz resursu, kas atgriež formas HTML ar JSON, kuram ir sekojošas vērtības: success (0 vai 1) un html (formas HTMLs). Tādā veidā reģistram ir iespējams piesaistīt specifiski izstrādātu formu.', 0),
(683, 6, 'url', 1, 'URL', 'URL', 250, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 21:50:13', 'Katram skatam var nodefinēt unikālu tekstuālu saiti. Pēc noklusēšanas visi skati var tikt attēloti ar saiti /skats_{ID}, bet norādot tekstuālo nosaukumu, skatu var attēlot ar saiti /skats_{nosaukums}. Vienlaicīgi darbojas abi skata saites varianti.', 0),
(684, 6, 'is_for_workflow', 7, 'Ir darbplūsmas dati', 'Ir darbplūsmas dati', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 21:51:16', 'Ja norādīts "Jā", tad skats tiek izmantots darbplūsmas datu izgūšanai. Darbplūsmām paredzētie skati patasti ir lietotājiem neredzami, jo tajos kā kolonnas tiek iekļauti tikai daži nepieciešamie lauki.', 0),
(685, 114, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(686, 114, 'title', 1, 'Nosaukums', 'Nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(687, 114, 'is_editable', 7, 'Ieraksts ir rediģējams', 'Ieraksts ir rediģējams', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(689, 108, 'item_id', 5, 'Ieraksta ID', 'Ieraksta ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(690, 108, 'v_employee', 1, 'Izpildītājs', 'Izpildītājs', 120, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(691, 108, 'v_closed_time', 2, 'Pabeigts', 'Pabeigts', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(692, 115, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(693, 115, 'workflow_id', 3, 'Darbplūsmas solis', 'Darbplūsmas solis', 0, 1, 110, 665, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(694, 115, 'list_id', 3, 'Reģistrs', 'Reģistrs', 0, 1, 3, 1, NULL, NULL, NULL, 695, 17, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(695, 115, 'field_id', 3, 'Lauks', 'Lauks', 0, 1, 7, 23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(696, 108, 'task_comment', 4, 'Komentārs', 'Komentārs', 4000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(712, 110, 'agr_izpilditajs', 1, 'Izpildītāja veids2', 'Izpildītāja veids2', 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'case when [Uzdevuma veids] in (4, 5) then ''Sistēma'' else case when [Izpildītāja veids] = 1 then ''Darbinieks'' else case when [Izpildītāja veids] = 2 then ''Tiešais vadītājs'' else ''Dokumenta sagatavotājs'' end end end', 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(713, 6, 'is_for_monitoring', 7, 'Iekļaut monitoringā', 'Iekļaut monitoringā', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 21:53:53', 'Ja norādīts "Jā", tad skats tiek izmantots SVS monitoringa procesā, saskaitot skata atlasīto ierakstu skaitu. Šo izmanto, piemēram, definējot kontroles nosacījumus uz kavētajiem uzdevumiem/dokumentiem. Monitoringa process var nosūtīt e-pastu vai arī monitoringa rezultāti var tikt attēloti speciālā administratoriem pieejamā sadaļā. Piezīme: pašreizējā SVS konfigurācijā monitoringa funkcionalitāte nav paredzēta.', 0),
(722, 6, 'is_for_word_generating', 7, 'Ir Word ģenerēšana', 'Ir Word ģenerēšana', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 21:55:29', 'Ja norādīts "Jā", tad skats tiek izmantots WORD dokumentu ģenerēšanā kā pieejamo lauku vērtību saraksts. WORD dokumentos iekļauj speciālos simbolos lauku nosaukumus no šī skata. Ģenerējot dokumentus, iekļautie lauki tiek aizvietoti ar vērtībām.', 0),
(723, 3, 'template_name', 12, 'Word sagatave', 'Word sagatave', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(724, 7, 'is_word_generation', 7, 'Word ģenerētā datne', 'Word ģenerētā datne', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(725, 119, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(726, 119, 'title', 1, 'Nosaukums', 'Nosaukums', 500, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(727, 119, 'feed_color', 17, 'Krāsa', 'Krāsa', 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(728, 60, 'source_id', 3, 'Datu avots', 'Datu avots', 0, 1, 119, 726, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(729, 120, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(730, 120, 'block_title', 1, 'Virsraksts portālā', 'Virsraksts portālā', 200, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(731, 120, 'title', 1, 'Bloka nosaukums', 'Bloka nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(732, 120, 'comments', 4, 'Paskaidrojums', 'Paskaidrojums', 2000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(733, 120, 'html', 15, 'HTML', 'HTML', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(734, 120, 'source_id', 3, 'Datu avots', 'Datu avots', 0, 0, 119, 726, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(735, 120, 'code', 1, 'Kods', 'Kods', 50, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(736, 120, 'is_active', 7, 'Ir aktīvs', 'Ir aktīvs', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(737, 120, 'is_border_0', 7, 'Ir bez rāmja', 'Ir bez rāmja', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(738, 121, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(739, 121, 'employee_name', 1, 'Darbinieks', 'Darbinieks', 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(740, 121, 'birth_date', 9, 'Dzimšanas diena', 'Dzimšanas diena', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(741, 121, 'phone', 1, 'Tālrunis', 'Tālrunis', 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(742, 121, 'mobile', 1, 'Mobilais', 'Mobilais', 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(743, 121, 'fax', 1, 'Fakss', 'Fakss', 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(744, 121, 'email', 11, 'e-pasts', 'e-pasts', 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(745, 121, 'start_date', 9, 'Uzsāk darbu', 'Uzsāk darbu', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(746, 121, 'end_date', 9, 'Beidz darbu', 'Beidz darbu', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(748, 121, 'position', 1, 'Amats', 'Amats', 30, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(749, 121, 'picture_name', 12, 'Bilde', 'Bilde', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(750, 121, 'source_id', 3, 'Uzņēmums', 'Uzņēmums', 0, 0, 119, 726, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(751, 5, 'fa_icon', 1, 'Ikona', 'Ikona', 200, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(752, 5, 'color', 17, 'Krāsa', 'Krāsa', 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(753, 100, 'source_id', 3, 'Datu avots', 'Datu avots', 0, 0, 119, 726, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(810, 126, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(811, 126, 'name', 1, 'Nosaukums', 'Nosaukums', 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(812, 126, 'link', 1, 'Saite', 'Saite', 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(813, 127, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(814, 127, 'article_id', 5, 'Raksts', 'Raksts', 0, 0, 60, 286, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 12:11:53', 'Sistēmisks lauks, kurā tiek saglabāta norāde uz saistīto rakstu. Šim laukam nevar konfigurēt tipu "Saistītais ieraksts", jo iezīmes tiek pievienotas vairākiem dažādiem reģistriem.', 0),
(815, 127, 'tag_id', 8, 'Iezīme', 'Iezīme', 0, 0, 126, 811, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(876, 136, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(877, 136, 'title', 1, 'Nosaukums', 'Nosaukums', 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(878, 136, 'meteo_code', 1, 'Meteo kods', 'Meteo kods', 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(879, 136, 'file_name', 12, 'Bilde', 'Bilde', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(880, 137, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(881, 137, 'weather_date', 9, 'Datums', 'Datums', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(882, 137, 'weather_type_id', 3, 'Laika tips', 'Laika tips', 0, 0, 136, 877, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(883, 137, 'temper_low', 5, 'Zemākā temperatūra', 'Zemākā temperatūra', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(884, 137, 'temper_high', 5, 'Augstākā temperatūra', 'Augstākā temperatūra', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(885, 137, 'meteo_code', 1, 'Meteo kods', 'Meteo kods', 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(886, 100, 'file_name', 12, 'Fona attēls', 'Fona attēls', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(887, 100, 'content_bg_color', 17, 'Satura fons', 'Satura fons', 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(904, 120, 'is_redactor', 7, 'Ir redaktors', 'Ir redaktors', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1164, 188, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1165, 188, 'code', 1, 'Kods', 'Kods', 10, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1166, 188, 'name', 1, 'Nosaukums', 'Nosaukums', 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1167, 188, 'picture_name', 1, 'Ikonas klase', 'Ikonas klase', 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1168, 188, 'file_name', 12, 'Noklusējuma attēls', 'Noklusējuma attēls', 500, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1170, 188, 'hover_hint', 4, 'Paskaidrojums', 'Paskaidrojums', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1171, 60, 'type_id', 3, 'Ziņas veids', 'Ziņas veids', 0, 0, 188, 1166, NULL, NULL, NULL, NULL, NULL, '1', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1172, 189, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1173, 189, 'title', 1, 'Virsraksts', 'Virsraksts', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0);
INSERT INTO `dx_lists_fields` (`id`, `list_id`, `db_name`, `type_id`, `title_list`, `title_form`, `max_lenght`, `is_required`, `rel_list_id`, `rel_display_field_id`, `rel_parent_field_id`, `rel_view_id`, `rel_display_formula_field`, `binded_field_id`, `binded_rel_field_id`, `default_value`, `formula`, `is_public_file`, `is_image_file`, `numerator_id`, `is_word_generation`, `is_multiple_files`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `hint`, `is_clean_html`) VALUES
(1175, 189, 'picture_name', 12, 'Attēls', 'Attēls', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, 0, NULL, NULL, 1, '2016-03-15 19:15:22', 'Galerijā sīktēla izmērs ir 305px (platums) x 200px (augstums). Sistēma automātiski veido sīktēlu atbilstoši šai proporcijai. Lai nekropļotu attēlu, ieteicams pievienot attēlus tādā pašā izmēru propocijā, piemēram, 915px x 600px.', 0),
(1177, 189, 'is_active', 7, 'Publicēts', 'Ir publicēts', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1178, 189, 'intro_text', 4, 'Ievadteksts', 'Ievadteksts', 1000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1179, 189, 'publish_time', 2, 'Datums', 'Datums', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1181, 189, 'source_id', 3, 'Datu avots', 'Datu avots', 0, 1, 119, 726, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1182, 189, 'type_id', 3, 'Ziņas veids', 'Ziņas veids', 0, 0, 188, 1166, NULL, NULL, NULL, NULL, NULL, '3', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1188, 191, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1189, 191, 'article_id', 3, 'Raksts', 'Raksts', 0, 1, 189, 1173, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1190, 191, 'file_name', 12, 'Attēls', 'Attēls', 500, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, 1, NULL, NULL, NULL, NULL, NULL, 0),
(1192, 191, 'order_index', 5, 'Secība', 'Secība', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100', NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-18 17:29:57', NULL, 0),
(1194, 5, 'title_index', 1, 'Secība un nosaukums', 'Secība un nosaukums', 200, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1206, 193, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1207, 193, 'config_name', 1, 'Iestatījums', 'Iestatījums', 200, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1208, 193, 'config_hint', 4, 'Iestatījuma paskaidrojums', 'Iestatījuma paskaidrojums', 1000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1209, 193, 'field_type_id', 3, 'Lauka tips', 'Lauka tips', 0, 0, 8, 19, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1210, 193, 'val_varchar', 4, 'Teksts', 'Teksts', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1211, 193, 'val_script', 15, 'Skripts', 'Skripts', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1212, 193, 'val_integer', 5, 'Skaitlis', 'Skaitlis', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1213, 193, 'val_date', 9, 'Datums', 'Datums', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1214, 193, 'val_file_name', 12, 'Datne', 'Datne', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1216, 193, 'val_yesno', 7, 'Iestatījums ieslēgts', 'Iestatījums ieslēgts', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1217, 194, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1218, 194, 'block_title', 1, 'Virsraksts portālā', 'Virsraksts portālā', 200, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1219, 194, 'title', 1, 'Bloka nosaukums', 'Bloka nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1220, 194, 'comments', 4, 'Paskaidrojums', 'Paskaidrojums', 2000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1221, 194, 'html', 10, 'Saturs', 'Saturs', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1222, 194, 'code', 1, 'Kods', 'Kods', 50, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1223, 194, 'is_active', 7, 'Ir aktīvs', 'Ir aktīvs', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1224, 194, 'is_border_0', 7, 'Ir bez rāmja', 'Ir bez rāmja', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1225, 194, 'is_redactor', 7, 'Ir redaktors', 'Ir redaktors', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1226, 194, 'source_id', 3, 'Datu avots', 'Datu avots', 0, 0, 119, 726, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1227, 21, 'ad_login', 1, 'AD lietotājs', 'AD lietotājs', 200, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1228, 82, 'spec_day', 1, 'Svētku diena', 'Svētku diena', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1229, 195, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1230, 195, 'source_id', 3, 'Datu avots', 'Datu avots', 0, 0, 119, 726, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1231, 195, 'question', 4, 'Jautājums', 'Jautājums', 2000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1232, 195, 'email', 1, 'E-pasts', 'E-pasts', 250, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1233, 195, 'asked_time', 2, 'Reģistrēts', 'Reģistrēts', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1234, 195, 'answer_time', 2, 'Atbildēts', 'Atbildēts', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1240, 198, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1241, 198, 'question', 1, 'Jautājuma teksts', 'Jautājuma teksts', 1000, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1242, 198, 'is_active', 7, 'Ir aktīvs jautājums', 'Ir aktīvs jautājums', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1243, 198, 'date_from', 2, 'Attēlošanas sākuma datums', 'Attēlošanas sākuma datums', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1244, 198, 'date_to', 2, 'Attēlošanas noslēguma datums', 'Attēlošanas noslēguma datums', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1245, 198, 'is_multi_answer', 7, 'Ir iespējamas vairākas atbildes', 'Ir iespējamas vairākas atbildes', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1246, 198, 'picture_name', 12, 'Jautājuma attēla nosaukums', 'Jautājuma attēla nosaukums', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1248, 198, 'source_id', 3, 'Datu avots', 'Datu avots', 0, 0, 119, 726, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1251, 200, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1252, 200, 'option_text', 1, 'Atbilžu variants', 'Atbilžu variants', 1000, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1253, 200, 'dailyquest_question_id', 3, 'Jautājums', 'Jautājums', 0, 1, 198, 1241, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1256, 202, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1257, 202, 'client_ip', 1, 'Lietotāja IP', 'Lietotāja IP', 45, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1258, 202, 'dailyquest_option_id', 3, 'Atbildes variants', 'Atbildes variants', 0, 1, 200, 1252, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1265, 205, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1266, 205, 'article_id', 3, 'Galerija', 'Galerija', 0, 0, 60, 287, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1267, 205, 'file_name', 12, 'Video datne', 'Video datne', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1269, 205, 'youtube_url', 1, 'YouTube saite', 'YouTube saite', 1000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1270, 205, 'order_index', 5, 'Secība', 'Secība', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100', NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-18 17:30:18', NULL, 0),
(1287, 208, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1288, 208, 'title', 1, 'Virsraksts', 'Virsraksts', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1289, 208, 'picture_name', 12, 'Attēls', 'Attēls', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, 0, NULL, NULL, 1, '2016-03-15 19:16:06', 'Galerijā sīktēla izmērs ir 305px (platums) x 200px (augstums). Sistēma automātiski veido sīktēlu atbilstoši šai proporcijai. Lai nekropļotu attēlu, ieteicams pievienot attēlus tādā pašā izmēru propocijā, piemēram, 915px x 600px.', 0),
(1290, 208, 'is_active', 7, 'Publicēts', 'Ir publicēts', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1291, 208, 'intro_text', 4, 'Ievadteksts', 'Ievadteksts', 1000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1292, 208, 'publish_time', 2, 'Datums', 'Datums', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1293, 208, 'source_id', 3, 'Datu avots', 'Datu avots', 0, 1, 119, 726, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1294, 208, 'type_id', 3, 'Ziņas veids', 'Ziņas veids', 0, 0, 188, 1166, NULL, NULL, NULL, NULL, NULL, '4', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1295, 205, 'prev_file_name', 12, 'Attēls', 'Attēls', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1296, 205, 'title', 1, 'Nosaukums', 'Nosaukums', 200, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1297, 209, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1298, 209, 'is_active', 7, 'Ir aktīva nodaļa', 'Ir aktīva nodaļa', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1299, 209, 'section_name', 1, 'Nodaļas nosaukums', 'Nodaļas nosaukums', 1000, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1304, 211, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1305, 211, 'is_active', 7, 'Ir aktīvs jautājums', 'Ir aktīvs jautājums', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1306, 211, 'question', 4, 'Jautājuma teksts', 'Jautājuma teksts', 2000, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1307, 211, 'answer', 4, 'Atbilde', 'Atbilde', 4000, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1308, 211, 'faq_section_id', 3, 'Nodaļa', 'Nodaļa', 0, 1, 209, 1299, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1309, 212, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1310, 212, 'title', 1, 'Nosaukums', 'Nosaukums', 200, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1311, 214, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1312, 214, 'publish_type_id', 3, 'Tips', 'Tips', 0, 0, 212, 1310, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1313, 214, 'nr', 1, 'Izdevuma numurs', 'Izdevuma numurs', 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1314, 214, 'pub_date', 9, 'Izdevuma datums', 'Izdevuma datums', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1315, 214, 'prev_file_name', 12, 'Attēla datne', 'Attēla datne', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1317, 214, 'file_name', 12, 'PDF datne', 'PDF datne', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1319, 214, 'order_index', 5, 'Secība', 'Secība', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1320, 119, 'tag_id', 8, 'Raksturīgā iezīme', 'Raksturīgā iezīme', 0, 0, 126, 811, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1321, 119, 'icon_class', 1, 'Ikonas klase', 'Ikonas klase', 200, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1322, 119, 'is_for_search', 7, 'Rādīt meklēšanas rīkos', 'Rādīt meklēšanas rīkos', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1323, 215, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1324, 215, 'title', 1, 'Nosaukums', 'Nosaukums', 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1325, 216, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1326, 216, 'type_id', 3, 'Notikuma veids', 'Notikuma veids', 0, 0, 215, 1324, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1327, 216, 'user_id', 3, 'Lietotājs', 'Lietotājs', 0, 0, 21, 99, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1328, 216, 'event_time', 2, 'Notikuma laiks', 'Notikuma laiks', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1329, 216, 'list_id', 3, 'Reģistrs', 'Reģistrs', 0, 0, 3, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1330, 216, 'item_id', 5, 'Ieraksta ID', 'Ieraksta ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1338, 222, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1339, 222, 'event_id', 3, 'Notikums', 'Notikums', 0, 0, 216, 1328, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1340, 222, 'field_id', 3, 'Lauks', 'Lauks', 0, 0, 7, 23, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1341, 222, 'old_val_txt', 10, 'Vecā vērtība', 'Vecā vērtība', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1342, 222, 'new_val_txt', 10, 'Jaunā vērtība', 'Jaunā vērtība', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1343, 222, 'old_val_rel_id', 5, 'Vecais saistītā ieraksta ID', 'Vecais saistītā ieraksta ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1344, 222, 'new_val_rel_id', 5, 'Jaunais saistītā ieraksta ID', 'Jaunais saistītā ieraksta ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1345, 222, 'old_val_file_name', 12, 'Vecais datnes nosaukums', 'Vecais datnes nosaukums', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1346, 222, 'new_val_file_name', 12, 'Jaunais datnes nosaukums', 'Jaunais datnes nosaukums', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1347, 222, 'old_val_file_guid', 1, 'Vecais datnes GUID', 'Vecais datnes GUID', 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1348, 222, 'new_val_file_guid', 1, 'Jaunais datnes GUID', 'Jaunais datnes GUID', 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1349, 223, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1350, 223, 'source_id', 3, 'Datu avots', 'Datu avots', 0, 0, 119, 726, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1352, 223, 'title', 4, 'Nosaukums', 'Nosaukums', 1000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1353, 223, 'code', 1, 'HR kods', 'HR kods', 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1354, 223, 'parent_id', 14, 'Augstākā struktūrvienība', 'Augstākā struktūrvienība', 0, 0, 223, 1352, 1354, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1355, 224, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1356, 224, 'name', 1, 'Nosaukums', 'Nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1357, 224, 'code', 1, 'Kods', 'Kods', 50, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1358, 224, 'url', 1, 'Piekļuves adrese', 'Piekļuves adrese', 250, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1359, 224, 'user_name', 1, 'Piekļuves lietotāja vārds', 'Piekļuves lietotāja vārds', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1360, 224, 'password', 1, 'Piekļuves parole', 'Piekļuves parole', 50, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1361, 224, 'schedule_from', 5, 'Strādā no (stundas)', 'Strādā no (stundas)', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1362, 224, 'schedule_to', 5, 'Strādā līdz (stundas)', 'Strādā līdz (stundas)', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1363, 224, 'schedule_every_minutes', 5, 'Izpilda ik pēc noteiktajām minūtēm', 'Izpilda ik pēc noteiktajām minūtēm', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1364, 224, 'employee_id', 8, 'Atbildīgais darbinieks', 'Atbildīgais darbinieks', 0, 0, 121, 739, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-18 18:49:03', NULL, 0),
(1365, 224, 'last_executed_time', 2, 'Pēdējais procesa izpildes laiks', 'Pēdējais procesa izpildes laiks', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1366, 225, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1367, 225, 'is_success', 7, 'Ir veiksmīga', 'Ir veiksmīga', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1368, 225, 'register_time', 2, 'Reģistrācijas laiks', 'Reģistrācijas laiks', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1369, 225, 'start_time', 2, 'Procesa sākums', 'Procesa sākums', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1370, 225, 'end_time', 2, 'Procesa beigas', 'Procesa beigas', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1371, 225, 'message', 4, 'Paziņojums', 'Paziņojums', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1372, 225, 'process_id', 3, 'Saistītais process', 'Saistītais process', 0, 1, 224, 1356, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1373, 226, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1374, 226, 'name', 1, 'Nosaukums', 'Nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1375, 226, 'url', 1, 'Interneta adrese', 'Interneta adrese', 250, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1376, 226, 'picture_name', 12, 'Attēls', 'Attēls', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, 0, NULL, NULL, 1, '2016-02-23 12:51:07', NULL, 0),
(1378, 226, 'source_id', 3, 'Datu avots', 'Datu avots', 0, 0, 119, 726, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1379, 226, 'employee_id', 8, 'Atbildīgais darbinieks', 'Atbildīgais darbinieks', 0, 1, 121, 739, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-02-23 12:52:48', NULL, 0),
(1380, 227, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1381, 227, 'is_crash', 7, 'Nepieejamība', 'Nepieejamība', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-02-23 12:53:54', NULL, 0),
(1382, 227, 'details', 4, 'Piezīmes', 'Piezīmes', 4000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-02-23 12:54:07', NULL, 0),
(1383, 227, 'solved_time', 2, 'Atrisināšanas laiks', 'Atrisināšanas laiks', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-02-23 12:54:25', NULL, 0),
(1384, 227, 'planned_resolve_time', 2, 'Plānotais novēršanas datums un laiks', 'Plānotais novēršanas datums un laiks', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1385, 227, 'system_id', 3, 'Sistēma', 'Sistēma', 0, 1, 226, 1374, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1386, 229, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1387, 229, 'title', 1, 'Sistēmas nosaukums', 'Sistēmas nosaukums', 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1388, 229, 'json_url', 4, 'JSON pieprasījuma URL', 'JSON pieprasījuma URL', 1000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1389, 229, 'sys_color_class', 1, 'Krāsas klases nosaukums', 'Krāsas klases nosaukums', 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-01 09:31:56', NULL, 0),
(1390, 229, 'source_id', 5, 'Datu avots', 'Datu avots', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-01 09:32:12', NULL, 0),
(1391, 231, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1392, 231, 'title', 1, 'Nosaukums', 'Nosaukums', 200, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1393, 231, 'order_index', 5, 'Secība', 'Secība', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1394, 3, 'group_id', 3, 'Reģistra grupa', 'Reģistra grupa', 0, 0, 231, 1392, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-08 09:34:31', 1, '2016-03-08 09:34:31', NULL, 0),
(1395, 7, 'hint', 4, 'Paskaidrojums', 'Paskaidrojums', 1000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-08 12:09:52', 1, '2016-03-08 12:09:52', NULL, 0),
(1396, 60, 'is_static', 7, 'Nerādīt ziņu plūsmā', 'Nerādīt ziņu plūsmā', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-08 12:41:01', 1, '2016-03-08 20:50:30', 'Norādot vērtību "Jā", ziņa netiks rādīta ziņu plūsmā, bet to varēs atrast ar meklētāju. Šo lauku izmanto, lai veidotu, piemēram, statiskus rakstus, kas tiek iekļauti kā bloki portāla lapās.', 0),
(1397, 3, 'hint', 4, 'Paskaidrojums', 'Paskaidrojums', 1000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-08 12:45:36', 1, '2016-03-08 12:45:36', NULL, 0),
(1398, 60, 'video_galery_id', 8, 'Video galerija', 'Video galerija', 0, 0, 208, 1288, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-11 08:31:01', 1, '2016-03-11 08:31:01', 'Laukā iespējams norādīt ar ziņu saistīto video galeriju, kas tiks attēlota ziņas beigās.', 0),
(1399, 60, 'picture_galery_id', 8, 'Attēlu galerija', 'Attēlu galerija', 0, 0, 189, 1173, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-11 09:21:21', 1, '2016-03-11 09:21:43', 'Laukā iespējams norādīt ar ziņu saistīto attēlu galeriju, kas tiks attēlota ziņas beigās.', 0),
(1400, 232, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1401, 232, 'article_id', 3, 'Ziņa', 'Ziņa', 0, 0, 60, 287, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1402, 232, 'file_name', 12, 'Datne', 'Datne', 500, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, 0, 1, NULL, NULL, 1, '2016-03-11 17:12:41', 'Ziņai var pievienot datnes, piemēram, PDF, Word, Excel u.c. Pievienotās datnes portālā tiks attēlotas zem ziņas teksta - ziņas detalizācijas lapā.', 0),
(1404, 232, 'order_index', 5, 'Secība', 'Secība', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1405, 7, 'is_multiple_files', 7, 'Ir vairākas datnes', 'Ir vairākas datnes', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-11 17:07:26', 1, '2016-03-11 17:07:26', 'Norādot "Jā", laukā varēs pievienot vairākas datnes uzreiz, arī izmantojot drag&drop iespēju.', 0),
(1406, 232, 'title', 1, 'Nosaukums', 'Nosaukums', 300, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-11 17:40:32', 1, '2016-03-11 17:40:45', 'Ja nosaukums netiek norādīts, tad portālā tiks attēlots datnes nosaukums ar visu datnes paplašinājumu.', 0),
(1407, 60, 'author_id', 8, 'Autors', 'Autors', 0, 0, 121, 739, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-11 18:11:15', 1, '2016-03-11 18:11:15', 'Kā autoru var norādīt Latvenergo vai meitas uzņēmumu darbiniekus, kas tiek importēti no Oracle HR sistēmas. Ja jānorāda kāds cits cilvēks, tad tas ir jāievada ziņas tekstā manuāli.', 0),
(1408, 7, 'is_clean_html', 7, 'Ir HTML tīrīšana', 'Ir HTML tīrīšana', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-14 06:46:58', 1, '2016-03-14 07:00:20', 'Ja vērtība "Jā", tad HTML ievades laukiem tiks veikta HTML tagu izņemšana un teksts tiks saglabāts laukā, kura nosaukuma sākums sakrīt ar HTML lauka nosaukumu un beidzas ar _dx_clean. HTML tagu tīrīšana nepieciešama, lai darbotos meklēšana tekstā.', 0),
(1409, 233, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1410, 233, 'title', 1, 'Nosaukums', 'Nosaukums', 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-14 12:56:48', NULL, 0),
(1411, 60, 'content_id', 3, 'Attēlošanas veids', 'Attēlošanas veids', 0, 0, 233, 1410, NULL, NULL, NULL, NULL, NULL, '1', NULL, 0, 0, NULL, 0, 0, 1, '2016-03-14 12:55:45', 1, '2016-03-14 12:55:45', 'Atkarībā no izvēlētā attēlošanas veida, ziņu plūsmā tiks nodrošināta atbilstoša funkcionalitāte: standarta ziņa, ziņa kā lejuplādējama datne vai saite uz ārējo resursu.', 0),
(1412, 60, 'alternate_url', 1, 'Ziņas norāde', 'Ziņas norāde', 250, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-14 13:09:58', 1, '2016-03-14 13:09:58', 'Laukā iespējams ievadīt ziņas norādi - garās interneta saites nosaukumu. Ziņām var būt 2 veida saites: īsā /ieraksts/{id}un garā /ieraksts/{teksts} .', 0),
(1413, 60, 'outer_url', 1, 'Ārējā saite', 'Ārējā saite', 1000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-14 13:12:17', 1, '2016-03-14 13:12:17', 'Ja norādīts ziņas attēlošanas veids "Ārējā saite", tad šajā laukā var norādīt URL uz kādu ārējo resursu. Ziņu plūsmā, spiežot uz ziņas virsraksta, atvērsies lapa ar norādīto URL.', 0),
(1414, 60, 'dwon_file_name', 12, 'Lejuplādējamā datne', 'Lejuplādējamā datne', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, 0, 0, 1, '2016-03-14 13:14:27', 1, '2016-03-14 13:14:27', 'Ja norādīts ziņas attēlošanas veids "Datne", tad šajā laukā var piesaistīt datni. Ziņu plūsmā, nospiežot uz ziņas nosaukuma, tiks lejuplādēta attiecīgā datne.', 0),
(1415, 234, 'id', 6, 'ID', 'ID', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1416, 234, 'title', 1, 'Virsraksts', 'Virsraksts', 100, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1417, 234, 'article_text', 10, 'Saturs', 'Saturs', 4000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-14 14:12:33', NULL, 1),
(1418, 234, 'picture_name', 12, 'Attēls', 'Attēls', 500, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, NULL, 0, 0, NULL, NULL, 1, '2016-03-15 20:00:20', 'Attēls portālā tiks parādīts ziņu plūsmā un/vai aktīvo ziņu slīdrādē, bet netiks parādīts skatot ziņas detalizācijas lapu. Attēla izmēriem jābūt 1420px (platums) x 840px (augstums) vai lielāki izmēri, bet noteikti saglabājot proporciju.', 0),
(1419, 234, 'order_index', 5, 'Secība', 'Secība', 10, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '100', NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-18 17:30:40', 'Norādot vērtības mazākas kā noklusētā, ir iespējams pacelt ziņu augstāk ziņu plūsmā. Šo var izmantot, lai noteiktas ziņas ilgāk rādītos kā pirmās.', 0),
(1420, 234, 'is_active', 7, 'Publicēts', 'Ir publicēts', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 12:48:29', 'Ja lauka vērtība ir "Nē", tad ziņa nav pieejama portālā - ne plūsmā, ne meklētājā.', 0),
(1421, 234, 'intro_text', 4, 'Ievadteksts', 'Ievadteksts', 1000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 12:49:10', 'Ievadteksts portālā tiek attēlots ziņu plūsmā (un meklēšanas rezultātos).', 0),
(1422, 234, 'publish_time', 2, 'Datums', 'Datums', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1423, 234, 'is_top_article', 7, 'Ir aktīvā ziņa', 'Ir aktīvā ziņa', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 12:49:40', 'Norādot "Jā", ziņa tiks attēlota arī aktīvo ziņu slīdrādes blokā.', 0),
(1424, 234, 'is_static', 7, 'Nerādīt ziņu plūsmā', 'Nerādīt ziņu plūsmā', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-08 12:41:01', 1, '2016-03-08 20:50:30', 'Norādot vērtību "Jā", ziņa netiks rādīta ziņu plūsmā, bet to varēs atrast ar meklētāju. Šo lauku izmanto, lai veidotu, piemēram, statiskus rakstus, kas tiek iekļauti kā bloki portāla lapās.', 0),
(1425, 234, 'alternate_url', 1, 'Ziņas norāde', 'Ziņas norāde', 250, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-14 13:09:58', 1, '2016-03-14 13:09:58', 'Laukā iespējams ievadīt ziņas norādi - garās interneta saites nosaukumu. Ziņām var būt 2 veida saites: īsā /ieraksts/{id}un garā /ieraksts/{teksts} .', 0),
(1426, 234, 'outer_url', 1, 'Ārējā saite', 'Ārējā saite', 1000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-14 13:12:17', 1, '2016-03-14 13:12:17', 'Ja norādīts ziņas attēlošanas veids "Ārējā saite", tad šajā laukā var norādīt URL uz kādu ārējo resursu. Ziņu plūsmā, spiežot uz ziņas virsraksta, atvērsies lapa ar norādīto URL.', 0),
(1427, 234, 'dwon_file_name', 12, 'Lejuplādējamā datne', 'Lejuplādējamā datne', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 0, NULL, 0, 0, 1, '2016-03-14 13:14:27', 1, '2016-03-14 13:14:27', 'Ja norādīts ziņas attēlošanas veids "Datne", tad šajā laukā var piesaistīt datni. Ziņu plūsmā, nospiežot uz ziņas nosaukuma, tiks lejuplādēta attiecīgā datne.', 0),
(1428, 234, 'source_id', 3, 'Datu avots', 'Datu avots', 0, 1, 119, 726, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1429, 234, 'author_id', 8, 'Autors', 'Autors', 0, 0, 121, 739, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-11 18:11:15', 1, '2016-03-11 18:11:15', 'Kā autoru var norādīt Latvenergo vai meitas uzņēmumu darbiniekus, kas tiek importēti no Oracle HR sistēmas. Ja jānorāda kāds cits cilvēks, tad tas ir jāievada ziņas tekstā manuāli.', 0),
(1430, 234, 'type_id', 3, 'Ziņas veids', 'Ziņas veids', 0, 0, 188, 1166, NULL, NULL, NULL, NULL, NULL, '1', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1431, 234, 'picture_galery_id', 8, 'Attēlu galerija', 'Attēlu galerija', 0, 0, 189, 1173, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-11 09:21:21', 1, '2016-03-11 09:21:43', 'Laukā iespējams norādīt ar ziņu saistīto attēlu galeriju, kas tiks attēlota ziņas beigās.', 0),
(1432, 234, 'video_galery_id', 8, 'Video galerija', 'Video galerija', 0, 0, 208, 1288, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-11 08:31:01', 1, '2016-03-11 08:31:01', 'Laukā iespējams norādīt ar ziņu saistīto video galeriju, kas tiks attēlota ziņas beigās.', 0),
(1433, 234, 'content_id', 3, 'Attēlošanas veids', 'Attēlošanas veids', 0, 0, 233, 1410, NULL, NULL, NULL, NULL, NULL, '1', NULL, 0, 0, NULL, 0, 0, 1, '2016-03-14 12:55:45', 1, '2016-03-14 12:55:45', 'Atkarībā no izvēlētā attēlošanas veida, ziņu plūsmā tiks nodrošināta atbilstoša funkcionalitāte: standarta ziņa, ziņa kā lejuplādējama datne vai saite uz ārējo resursu.', 0),
(1434, 21, 'source_id', 3, 'Datu avots', 'Datu avots', 0, 0, 119, 726, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, 1, '2016-03-19 12:28:16', 1, '2016-03-19 12:28:16', 'Ja nav norādīts, lietotājam pieejami visu datu avotu dati no visiem reģistriem, uz kuriem lietotājam ir tiesības un kuros ir definēta datu avotu loģika. Ja norādīts, tad reģistros pieejami tikai atbilstošā datu avota ieraksti.', 0),
(1435, 235, 'id', 6, 'ID', 'ID', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1436, 235, 'block_title', 1, 'Virsraksts portālā', 'Virsraksts portālā', 200, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1437, 235, 'title', 1, 'Bloka nosaukums', 'Bloka nosaukums', 200, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1438, 235, 'comments', 4, 'Paskaidrojums', 'Paskaidrojums', 2000, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1439, 235, 'html', 10, 'Saturs', 'Saturs', 0, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1440, 235, 'code', 1, 'Kods', 'Kods', 50, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1441, 235, 'is_active', 7, 'Ir aktīvs', 'Ir aktīvs', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1442, 235, 'is_border_0', 7, 'Ir bez rāmja', 'Ir bez rāmja', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1443, 235, 'is_redactor', 7, 'Ir redaktors', 'Ir redaktors', 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '1', NULL, 0, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, 0),
(1444, 235, 'source_id', 3, 'Datu avots', 'Datu avots', 0, 0, 119, 726, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, NULL, 0, 0, NULL, NULL, 1, '2016-03-20 10:07:49', 'Ja datu avots netiek norādīts, tad bloka saturu varēs norādīt tikai lietotāji, kuriem būs piekļuve visiem datu avotiem.', 0);

-- --------------------------------------------------------

--
-- Table structure for table `dx_lists_groups`
--

CREATE TABLE IF NOT EXISTS `dx_lists_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nosaukums',
  `order_index` int(11) NOT NULL DEFAULT '0' COMMENT 'Secība',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `dx_lists_groups_order_index_index` (`order_index`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

--
-- Dumping data for table `dx_lists_groups`
--

INSERT INTO `dx_lists_groups` (`id`, `title`, `order_index`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'SVS izveide', 10, NULL, NULL, NULL, NULL),
(2, 'SVS administrēšana un konfigurācija', 20, NULL, NULL, NULL, NULL),
(3, 'Lietotāju tiesību administrēšana', 30, NULL, NULL, NULL, NULL),
(4, 'Satura administrēšana', 40, NULL, NULL, NULL, NULL),
(5, 'Darbplūsmas', 50, NULL, NULL, NULL, NULL),
(6, 'SVS sistēmiskie klasifikatori', 45, 1, '2016-03-08 21:28:26', 1, '2016-03-08 21:34:58');

-- --------------------------------------------------------

--
-- Table structure for table `dx_menu`
--

CREATE TABLE IF NOT EXISTS `dx_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT NULL COMMENT 'Reference to parent menu item - menus can be hierarchical',
  `title` varchar(100) DEFAULT NULL COMMENT 'Menu title',
  `list_id` int(11) DEFAULT NULL COMMENT 'Reference to register',
  `url` varchar(2000) DEFAULT NULL COMMENT 'URL - can be intranet or internet',
  `is_target_blank` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Indicates if page must be opened in new window (0 - no, 1 - yes)',
  `full_path` text COMMENT 'Full path to the item - concatenated all parents recursively',
  `order_index` int(11) NOT NULL DEFAULT '0' COMMENT 'Order index of the menu item',
  `fa_icon` varchar(100) DEFAULT NULL COMMENT 'Icon name - https://fortawesome.github.io/Font-Awesome/icons/',
  `color` varchar(50) DEFAULT NULL,
  `title_index` varchar(200) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`,`list_id`),
  KEY `orer_index` (`order_index`),
  KEY `list_id` (`list_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Kreisās puses hierarhiska izvēlne' AUTO_INCREMENT=219 ;

--
-- Dumping data for table `dx_menu`
--

INSERT INTO `dx_menu` (`id`, `parent_id`, `title`, `list_id`, `url`, `is_target_blank`, `full_path`, `order_index`, `fa_icon`, `color`, `title_index`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(12, NULL, 'Sistēma', NULL, NULL, 0, '', 100, 'icon-wrench', NULL, '[100] Sistēma', NULL, NULL, NULL, NULL),
(13, 19, 'Lietotāji', 21, NULL, 0, '', 15, NULL, NULL, '[15] Lietotāji', NULL, NULL, 1, '2016-03-01 10:09:21'),
(14, 65, 'Reģistri', 3, NULL, 0, '', 114, NULL, NULL, '[114] Reģistri', NULL, NULL, NULL, NULL),
(15, 19, 'Lomas', 20, NULL, 0, '', 2, NULL, NULL, '[2] Lomas', NULL, NULL, NULL, NULL),
(16, 156, 'Izvēlnes', 5, NULL, 0, '', 100, NULL, NULL, '[100] Izvēlnes', NULL, NULL, 1, '2016-03-20 10:01:51'),
(17, 65, 'Objekti', 4, NULL, 0, '', 112, NULL, NULL, '[112] Objekti', NULL, NULL, NULL, NULL),
(19, 155, 'Tiesības', NULL, NULL, 0, '', 275, NULL, NULL, '[275] Tiesības', NULL, NULL, 1, '2016-03-01 09:55:33'),
(24, 63, 'Lauku tipi', 8, NULL, 0, '', 50, NULL, NULL, '[50] Lauku tipi', NULL, NULL, NULL, NULL),
(45, 65, 'Datu modelis', 41, NULL, 0, '', 116, NULL, NULL, '[116] Datu modelis', NULL, NULL, NULL, NULL),
(48, 63, 'Lauku operācijas', 43, NULL, 0, NULL, 5, NULL, NULL, '[5] Lauku operācijas', NULL, NULL, NULL, NULL),
(49, 63, 'Secību veidi', 44, NULL, 0, NULL, 10, NULL, NULL, '[10] Secību veidi', NULL, NULL, NULL, NULL),
(52, 63, 'Agregāciju veidi', 48, NULL, 0, NULL, 20, NULL, NULL, '[20] Agregāciju veidi', NULL, NULL, NULL, NULL),
(63, 12, 'Iestatījumi', NULL, NULL, 0, NULL, 120, NULL, NULL, '[120] Iestatījumi', NULL, NULL, NULL, NULL),
(65, 12, 'Struktūra', NULL, NULL, 0, NULL, 110, NULL, NULL, '[110] Struktūra', NULL, NULL, NULL, NULL),
(71, 180, 'Ziņas', 60, NULL, 0, NULL, 10, NULL, NULL, '[10] Ziņas', NULL, NULL, NULL, NULL),
(107, NULL, 'Latvenergo', NULL, 'latvenergo', 0, NULL, 300, 'iconle-le_logo', NULL, '[300] Latvenergo', NULL, NULL, NULL, NULL),
(108, NULL, 'Sadales tīkls', NULL, 'sadales_tikli', 0, NULL, 400, 'iconle-le_logo', NULL, '[400] Sadales tīkls', NULL, NULL, NULL, NULL),
(109, NULL, 'Elektrum', NULL, 'elektrum', 0, NULL, 500, 'iconle-le_logo', NULL, '[500] Elektrum', NULL, NULL, NULL, NULL),
(110, NULL, 'Personāls', NULL, 'personals', 0, NULL, 600, 'fa fa-users', NULL, '[600] Personāls', NULL, NULL, NULL, NULL),
(126, 156, 'Lapas', 100, NULL, 0, NULL, 900, NULL, NULL, '[900] Lapas', NULL, NULL, 1, '2016-03-20 10:02:26'),
(127, 63, 'Skatu veidi', 102, NULL, 0, NULL, 300, NULL, NULL, '[300] Skatu veidi', NULL, NULL, NULL, NULL),
(128, 63, 'Lauku attēlošanas veidi', 103, NULL, 0, NULL, 800, NULL, NULL, '[800] Lauku attēlošanas veidi', NULL, NULL, NULL, NULL),
(133, 63, 'Uzdevumu statusi', 106, NULL, 0, NULL, 500, NULL, NULL, '[500] Uzdevumu statusi', NULL, NULL, NULL, NULL),
(134, 63, 'Uzdevumu veidi', 107, NULL, 0, NULL, 700, NULL, NULL, '[700] Uzdevumu veidi', NULL, NULL, NULL, NULL),
(136, 63, 'Uzdevumu izpildītāju veidi', 109, NULL, 0, NULL, 650, NULL, NULL, '[650] Uzdevumu izpildītāju veidi', NULL, NULL, NULL, NULL),
(137, 12, 'Darbplūsmas', 110, NULL, 0, NULL, 130, NULL, NULL, '[130] Darbplūsmas', NULL, NULL, NULL, NULL),
(140, 63, 'Formu veidi', 113, NULL, 0, NULL, 1000, NULL, NULL, '[1000] Formu veidi', NULL, NULL, NULL, NULL),
(141, 63, 'Ieraksta statusi', 114, NULL, 0, NULL, 1500, NULL, NULL, '[1500] Ieraksta statusi', NULL, NULL, NULL, NULL),
(142, 65, 'JavaScript', 99, NULL, 0, NULL, 118, NULL, NULL, '[118] JavaScript', NULL, NULL, NULL, NULL),
(147, 215, 'Datu avoti', 119, NULL, 0, NULL, 15, NULL, NULL, '[15] Datu avoti', NULL, NULL, 1, '2016-03-01 09:59:41'),
(149, NULL, 'Galerijas', NULL, 'attelu_galerijas', 0, NULL, 700, 'fa fa-file-image-o', NULL, '[700] Galerijas', NULL, NULL, NULL, NULL),
(150, 156, 'HTML bloki', 120, NULL, 0, NULL, 650, NULL, NULL, '[650] HTML bloki', NULL, NULL, 1, '2016-03-20 09:57:29'),
(151, NULL, 'Izdevumi', NULL, 'izdevumi', 0, NULL, 800, 'fa fa-newspaper-o', NULL, '[800] Izdevumi', NULL, NULL, NULL, NULL),
(152, NULL, 'Darba sistēmas', NULL, '/', 0, NULL, 900, 'fa fa-cogs', NULL, '[900] Darba sistēmas', NULL, NULL, NULL, NULL),
(153, NULL, 'Struktūrvienību lapas', NULL, '/', 0, NULL, 930, 'fa fa-sitemap', NULL, '[930] Struktūrvienību lapas', NULL, NULL, NULL, NULL),
(154, NULL, 'Forums', NULL, '/', 0, NULL, 960, 'fa fa-comment', NULL, '[960] Forums', NULL, NULL, NULL, NULL),
(155, NULL, 'Administrācija', NULL, NULL, 0, NULL, 200, 'fa fa-cog', NULL, '[200] Administrācija', NULL, NULL, NULL, NULL),
(156, 155, 'Portāla iestatījumi', NULL, NULL, 0, NULL, 270, NULL, NULL, '[270] Portāla iestatījumi', NULL, NULL, NULL, NULL),
(157, 172, 'Darbinieki', 121, NULL, 0, NULL, 40, NULL, NULL, '[40] Darbinieki', NULL, NULL, NULL, NULL),
(158, 172, 'Svētku dienas', 82, NULL, 0, NULL, 50, NULL, NULL, '[50] Svētku dienas', NULL, NULL, NULL, NULL),
(172, 155, 'Personāldaļa', NULL, NULL, 0, NULL, 241, NULL, NULL, '[241] Personāldaļa', NULL, NULL, 1, '2016-03-01 09:51:05'),
(180, 155, 'Portāla saturs', NULL, NULL, 0, NULL, 210, NULL, NULL, '[210] Portāla saturs', NULL, NULL, 1, '2016-03-20 09:58:02'),
(181, 215, 'Iezīmes', 126, NULL, 0, NULL, 20, NULL, NULL, '[20] Iezīmes', NULL, NULL, 1, '2016-03-01 09:56:45'),
(182, 155, 'Laika ziņas', NULL, NULL, 0, NULL, 255, NULL, NULL, '[255] Laika ziņas', NULL, NULL, NULL, NULL),
(183, 182, 'Laikapstākļu veidi', 136, NULL, 0, NULL, 0, NULL, NULL, '[0] Laikapstākļu veidi', NULL, NULL, NULL, NULL),
(184, 182, 'Ziņas', 137, NULL, 0, NULL, 2, NULL, NULL, '[2] Ziņas', NULL, NULL, NULL, NULL),
(186, 215, 'Ziņu veidi', 188, NULL, 0, NULL, 10, NULL, NULL, '[10] Ziņu veidi', NULL, NULL, 1, '2016-03-01 09:56:28'),
(187, 180, 'Attēlu galerijas', 189, NULL, 0, NULL, 20, NULL, NULL, '[20] Attēlu galerijas', NULL, NULL, NULL, NULL),
(188, 156, 'Konfigurācija', 193, NULL, 0, NULL, 1000, NULL, NULL, '[1000] Konfigurācija', NULL, NULL, 1, '2016-03-20 10:02:06'),
(189, 180, 'Satura bloki', 194, NULL, 0, NULL, 25, NULL, NULL, '[25] Satura bloki', NULL, NULL, NULL, NULL),
(190, 172, 'Iesūtītie jautājumi', 195, NULL, 0, NULL, 60, NULL, NULL, '[60] Iesūtītie jautājumi', NULL, NULL, NULL, NULL),
(194, 172, 'Notikumu kalendārs', 93, NULL, 0, NULL, 70, NULL, NULL, '[70] Notikumu kalendārs', NULL, NULL, NULL, NULL),
(195, 198, 'Aptauju jautājumi', 198, NULL, 0, NULL, 10, NULL, NULL, '[10] Aptauju jautājumi', NULL, NULL, NULL, NULL),
(196, 198, 'Atbildes uz jautājumiem', 202, NULL, 0, NULL, 20, NULL, NULL, '[20] Atbildes uz jautājumiem', NULL, NULL, NULL, NULL),
(197, 180, 'Video galerijas', 208, NULL, 0, NULL, 22, NULL, NULL, '[22] Video galerijas', NULL, NULL, NULL, NULL),
(198, 155, 'Aptaujas', NULL, NULL, 0, NULL, 245, NULL, NULL, '[245] Aptaujas', NULL, NULL, NULL, NULL),
(199, 201, 'Jautājumu kategorijas', 209, NULL, 0, NULL, 10, NULL, NULL, '[10] Jautājumu kategorijas', NULL, NULL, NULL, NULL),
(200, 201, 'Jautājumi', 211, NULL, 0, NULL, 20, NULL, NULL, '[20] Jautājumi', NULL, NULL, NULL, NULL),
(201, 155, 'Biežāk uzdotie jautājumi', NULL, NULL, 0, NULL, 247, NULL, NULL, '[247] Biežāk uzdotie jautājumi', NULL, NULL, NULL, NULL),
(202, 214, 'Izdevumu tipi', 212, NULL, 0, NULL, 10, NULL, NULL, '[10] Izdevumu tipi', NULL, NULL, 1, '2016-03-01 09:49:40'),
(203, 214, 'Izdevumu reģistrs', 214, NULL, 0, NULL, 20, NULL, NULL, '[20] Izdevumu reģistrs', NULL, NULL, 1, '2016-03-01 09:50:08'),
(204, 63, 'Darbību veidi', 215, NULL, 0, NULL, 2000, NULL, NULL, '[2000] Darbību veidi', NULL, NULL, NULL, NULL),
(205, 19, 'Lietotāju darbības', 216, NULL, 0, NULL, 20, NULL, NULL, '[20] Lietotāju darbības', NULL, NULL, NULL, NULL),
(206, 172, 'Struktūrvienības', 223, NULL, 0, NULL, 80, NULL, NULL, '[80] Struktūrvienības', NULL, NULL, NULL, NULL),
(207, 211, 'Procesi', 224, NULL, 0, NULL, 20, NULL, NULL, '[20] Procesi', NULL, NULL, 1, '2016-03-01 09:37:42'),
(208, 213, 'Sistēmas', 226, NULL, 0, NULL, 30, NULL, NULL, '[30] Sistēmas', 1, '2016-02-23 12:51:40', 1, '2016-03-01 09:44:04'),
(209, 213, 'Sistēmu incidenti', 227, NULL, 0, NULL, 40, NULL, NULL, '[40] Sistēmu incidenti', 1, '2016-02-23 12:55:12', 1, '2016-03-01 09:44:19'),
(211, 155, 'Integrācijas', NULL, NULL, 0, NULL, 280, NULL, NULL, '[280] Integrācijas', 1, '2016-03-01 09:35:27', 1, '2016-03-01 09:35:27'),
(212, 211, 'Lotus Notes sistēmas', 229, NULL, 0, NULL, 10, NULL, NULL, '[10] Lotus Notes sistēmas', 1, '2016-03-01 09:36:01', 1, '2016-03-01 09:36:01'),
(213, 155, 'Darba sistēmas', NULL, NULL, 0, NULL, 257, NULL, NULL, '[257] Darba sistēmas', 1, '2016-03-01 09:43:44', 1, '2016-03-01 09:43:44'),
(214, 155, 'Izdevumi', NULL, NULL, 0, NULL, 244, NULL, NULL, '[244] Izdevumi', 1, '2016-03-01 09:48:42', 1, '2016-03-01 09:48:42'),
(215, 155, 'Ziņu iestatījumi', NULL, NULL, 0, NULL, 260, NULL, NULL, '[260] Ziņu iestatījumi', 1, '2016-03-01 09:55:57', 1, '2016-03-01 09:55:57'),
(216, 65, 'Reģistru grupas', 231, NULL, 0, NULL, 113, NULL, NULL, '[113] Reģistru grupas', 1, '2016-03-08 09:33:43', 1, '2016-03-08 09:33:43'),
(217, 215, 'Ziņu papildus veidi', 233, NULL, 0, NULL, 12, NULL, NULL, '[12] Ziņu papildus veidi', 1, '2016-03-14 12:38:11', 1, '2016-03-14 12:38:11'),
(218, 156, 'Satura bloki', 235, NULL, 0, NULL, 700, NULL, NULL, '[700] Satura bloki', 1, '2016-03-20 09:54:42', 1, '2016-03-20 09:54:42');

-- --------------------------------------------------------

--
-- Table structure for table `dx_model`
--

CREATE TABLE IF NOT EXISTS `dx_model` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_list_id` int(11) NOT NULL COMMENT 'Relation to the parent register',
  `child_list_id` int(11) NOT NULL COMMENT 'Relation to the child register',
  `child_rel_field_id` int(11) NOT NULL COMMENT 'Relation to che child register field which is used to join registers',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_list_id` (`parent_list_id`,`child_list_id`,`child_rel_field_id`),
  KEY `child_rel_field_id` (`child_rel_field_id`),
  KEY `child_list_id` (`child_list_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Sistēmiska tabula, kurā var definēt relācijas starp reģistriem. Tas nepieciešams, lai varētu definēt skatus, kuros izmanto kolonnas no dažādiem reģistriem. Piemēram, ir klienti, projekti un uzdevumi. Lai uzdevumu reģistra skatā ietvertu kolonnu ar klienta nosaukumu, ir jānodefinē datu modelis Klients-Projekts, Projekts-Uzdevums. Tad SVS varēs automātiski saprast, kādā veidā datu objekts Uzdevums sasaistās ar datu objektu Klients.' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `dx_model`
--

INSERT INTO `dx_model` (`id`, `parent_list_id`, `child_list_id`, `child_rel_field_id`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 5, 5, 53, NULL, NULL, NULL, NULL),
(2, 198, 200, 1253, NULL, NULL, NULL, NULL),
(3, 200, 202, 1258, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_numerators`
--

CREATE TABLE IF NOT EXISTS `dx_numerators` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `mask` varchar(200) DEFAULT NULL,
  `next_counter` int(11) NOT NULL DEFAULT '0',
  `current_year` int(11) NOT NULL DEFAULT '0',
  `counter_lenght` int(11) NOT NULL DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dx_objects`
--

CREATE TABLE IF NOT EXISTS `dx_objects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `db_name` varchar(100) DEFAULT NULL COMMENT 'MySQL table name',
  `title` varchar(100) DEFAULT NULL COMMENT 'Title of the object - used in CMS interface',
  `is_multi_registers` tinyint(11) NOT NULL DEFAULT '0' COMMENT 'Indictes if object can operate with multiple registers (then field multi_list_id must be defined in the table structure) (0 - no, 1 - yes)',
  `is_history_logic` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Indicates if object audits data changes in dx_change_log table (0 - no, 1 - yes)',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `db_name` (`db_name`),
  KEY `is_multi_registers` (`is_multi_registers`),
  KEY `is_history_logic` (`is_history_logic`),
  KEY `title` (`title`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tabulā tiek reģistrēti datu objekti - tabulas no datu bāzes. SVS katrs reģistrs ir piesaistīts datu bāzes tabulai. Vienai tabulai var piesaistī arī vairākus reģistrus, piemēra, lai definētu dažādas piekļuves tiesības uz atsevišķiem laukiem, jo tad katram reģistram būtu sava datu ievades forma, bet ieraksti glabātos vienā tabulā.' AUTO_INCREMENT=127 ;

--
-- Dumping data for table `dx_objects`
--

INSERT INTO `dx_objects` (`id`, `db_name`, `title`, `is_multi_registers`, `is_history_logic`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'dx_lists', 'Reģistri', 0, 1, NULL, NULL, NULL, NULL),
(2, 'dx_objects', 'Objekti', 0, 1, NULL, NULL, NULL, NULL),
(4, 'dx_data', 'Dati', 1, 1, NULL, NULL, NULL, NULL),
(5, 'dx_menu', 'Izvēlne', 0, 1, NULL, NULL, NULL, NULL),
(6, 'dx_views', 'Skati', 0, 1, NULL, NULL, NULL, NULL),
(7, 'dx_lists_fields', 'Reģistru lauki', 0, 1, NULL, NULL, NULL, NULL),
(8, 'dx_field_types', 'Lauku tipi', 0, 1, NULL, NULL, NULL, NULL),
(9, 'dx_forms_fields', 'Formu lauki', 0, 1, NULL, NULL, NULL, NULL),
(10, 'dx_forms', 'Forma', 0, 1, NULL, NULL, NULL, NULL),
(11, 'dx_views_fields', 'Skatu lauki', 0, 1, NULL, NULL, NULL, NULL),
(12, 'dx_forms_tabs', 'Sadaļas', 0, 1, NULL, NULL, NULL, NULL),
(16, 'dx_roles', 'Lomas', 0, 1, NULL, NULL, NULL, NULL),
(17, 'dx_users', 'Lietotāji', 0, 1, NULL, NULL, NULL, NULL),
(18, 'dx_users_roles', 'Lietotāju lomas', 0, 1, NULL, NULL, NULL, NULL),
(19, 'dx_roles_lists', 'Lomu reģistri', 0, 1, NULL, NULL, NULL, NULL),
(26, 'dx_model', 'Datu modelis', 0, 1, NULL, NULL, NULL, NULL),
(27, 'dx_field_operations', 'Lauku operācijas', 0, 1, NULL, NULL, NULL, NULL),
(28, 'dx_sort_types', 'Secību veidi', 0, 1, NULL, NULL, NULL, NULL),
(32, 'dx_aggregation_types', 'Agregāciju veidi', 0, 1, NULL, NULL, NULL, NULL),
(40, 'in_articles', 'Ziņas', 0, 1, NULL, NULL, NULL, NULL),
(62, 'in_saints_days', 'Vārdadienas', 0, 1, NULL, NULL, NULL, NULL),
(70, 'in_documents', 'Lotus Notes dokumenti', 0, 1, NULL, NULL, NULL, NULL),
(72, 'in_events', 'Notikumi', 0, 1, NULL, NULL, NULL, NULL),
(73, 'dx_users', 'Darbplūsmu lietotāji', 0, 1, NULL, NULL, 1, '2016-03-08 09:28:59'),
(74, 'in_employee_changes', 'Darbinieku vēsture', 0, 1, NULL, NULL, NULL, NULL),
(76, 'in_total_stat', 'Kopējā statistika', 0, 1, NULL, NULL, NULL, NULL),
(77, 'dx_numerators', 'Numeratori', 0, 1, NULL, NULL, NULL, NULL),
(78, 'dx_forms_js', 'Formu JavaScript', 0, 1, NULL, NULL, NULL, NULL),
(79, 'dx_pages', 'Lapas', 0, 1, NULL, NULL, NULL, NULL),
(80, 'dx_roles_pages', 'Lapu reģistri', 0, 1, NULL, NULL, NULL, NULL),
(81, 'dx_views_types', 'Skatu veidi', 0, 1, NULL, NULL, NULL, NULL),
(82, 'dx_field_represent', 'Lauku attēlošanas veidi', 0, 1, NULL, NULL, NULL, NULL),
(83, 'dx_tasks_statuses', 'Uzdevumu statusi', 0, 1, NULL, NULL, NULL, NULL),
(84, 'dx_tasks_types', 'Uzdevumu veidi', 0, 1, NULL, NULL, NULL, NULL),
(85, 'dx_tasks', 'Uzdevumi', 0, 1, NULL, NULL, NULL, NULL),
(86, 'dx_tasks_perform', 'Uzdevumu izpildītāju veidi', 0, 1, NULL, NULL, NULL, NULL),
(87, 'dx_workflows', 'Darbplūsmas', 0, 1, NULL, NULL, NULL, NULL),
(88, 'dx_forms_types', 'Formu veidi', 0, 1, NULL, NULL, NULL, NULL),
(89, 'dx_item_statuses', 'Ierakstu statusi', 0, 1, NULL, NULL, NULL, NULL),
(90, 'dx_workflows_fields', 'Darbplūsmu lauki', 0, 1, NULL, NULL, NULL, NULL),
(92, 'in_sources', 'Datu avoti', 0, 1, NULL, NULL, NULL, NULL),
(93, 'in_html', 'HTML bloki', 0, 1, NULL, NULL, NULL, NULL),
(94, 'in_employees', 'Darbinieki', 0, 1, NULL, NULL, NULL, NULL),
(95, 'in_tags', 'Iezīmes', 0, 1, NULL, NULL, NULL, NULL),
(96, 'in_tags_article', 'Rakstu iezīmes', 0, 1, NULL, NULL, NULL, NULL),
(97, 'in_weather_types', 'Laikapstākļu veidi', 0, 1, NULL, NULL, NULL, NULL),
(98, 'in_weather', 'Laika ziņu reģistrs', 0, 1, NULL, NULL, NULL, NULL),
(101, 'in_article_types', 'Ziņu veidi', 0, 1, NULL, NULL, NULL, NULL),
(102, 'in_articles_img', 'Attēli', 0, 1, NULL, NULL, NULL, NULL),
(103, 'dx_config', 'Konfigurācija', 0, 1, NULL, NULL, 1, '2016-03-20 15:52:28'),
(104, 'in_questions', 'Iesūtītie jautājumi', 0, 1, NULL, NULL, NULL, NULL),
(107, 'in_dailyquest_questions', 'Aptauju jautājumi', 0, 1, NULL, NULL, NULL, NULL),
(108, 'in_dailyquest_options', 'Atbilžu varianti', 0, 1, NULL, NULL, NULL, NULL),
(109, 'in_dailyquest_answers', 'Atbildes uz jautājumiem', 0, 1, NULL, NULL, NULL, NULL),
(110, 'in_articles_vid', 'Video', 0, 1, NULL, NULL, NULL, NULL),
(111, 'in_faq_section', 'Jautājumu nodaļas', 0, 1, NULL, NULL, NULL, NULL),
(112, 'in_faq_question', 'Biežāk uzdotie jautājumi', 0, 1, NULL, NULL, NULL, NULL),
(113, 'in_publish_types', 'Izdevumi tipi', 0, 1, NULL, NULL, NULL, NULL),
(114, 'in_publish', 'Izdevumi', 0, 1, NULL, NULL, NULL, NULL),
(115, 'dx_db_event_types', 'Darbību veidi', 0, 1, NULL, NULL, NULL, NULL),
(116, 'dx_db_events', 'Lietotāju darbības', 0, 1, NULL, NULL, NULL, NULL),
(117, 'dx_db_history', 'Izmaiņu vēsture', 0, 0, NULL, NULL, NULL, NULL),
(118, 'in_departments', 'Struktūrvienības', 0, 1, NULL, NULL, NULL, NULL),
(119, 'in_processes', 'Procesi', 0, 1, NULL, NULL, NULL, NULL),
(120, 'in_processes_log', 'Procesu vēsture', 0, 0, NULL, NULL, NULL, NULL),
(121, 'in_systems', 'Sistēmas', 0, 1, 1, '2016-02-23 12:49:15', 1, '2016-02-23 12:49:15'),
(122, 'in_incidents', 'Sistēmu incidenti', 0, 1, 1, '2016-02-23 12:53:16', 1, '2016-02-23 12:53:16'),
(123, 'in_documents_lotus', 'Lotus Notes sistēmas', 0, 1, 1, '2016-03-01 09:29:59', 1, '2016-03-01 09:30:43'),
(124, 'dx_lists_groups', 'Reģistra grupas', 0, 1, 1, '2016-03-08 09:30:55', 1, '2016-03-08 09:32:32'),
(125, 'in_articles_files', 'Ziņu datnes', 0, 1, 1, '2016-03-11 16:44:05', 1, '2016-03-11 16:44:05'),
(126, 'in_articles_contents', 'Ziņu papildus veidi', 0, 1, 1, '2016-03-14 12:35:51', 1, '2016-03-14 12:35:51');

-- --------------------------------------------------------

--
-- Table structure for table `dx_pages`
--

CREATE TABLE IF NOT EXISTS `dx_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) DEFAULT NULL,
  `html` text,
  `is_active` int(11) DEFAULT '0',
  `url_title` varchar(200) DEFAULT NULL,
  `is_default` int(11) NOT NULL DEFAULT '0',
  `file_name` varchar(500) DEFAULT NULL,
  `file_guid` varchar(50) DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `content_bg_color` varchar(100) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url_title` (`url_title`),
  KEY `is_default` (`is_default`),
  KEY `dx_pages_source_id_index` (`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `dx_pages`
--

INSERT INTO `dx_pages` (`id`, `title`, `html`, `is_active`, `url_title`, `is_default`, `file_name`, `file_guid`, `source_id`, `content_bg_color`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Latvenergo', '<div class="row"><!-- Centrālā daļa -->\n<div class="col-lg-8"><!-- Ziņas--> [[OBJ=TOPARTICLES|SOURCE=1|ARTICLEPAGE=2]] [[OBJ=FEEDARTICLES|SOURCE=1|ARTICLEPAGE=2]]</div>\n<!-- Labās puses josla -->\n<div class="col-lg-4">[[OBJ=CLOUD|SOURCE=1]] [[OBJ=HTML|CODE=LABUMI]] [[OBJ=HTML|CODE=DARBAPIED|SOURCE=4]] [[OBJ=WRITEQ|SOURCE=0]]</div>\n</div>', 1, 'latvenergo', 0, 'x_plavinu_HES_001024860017.jpg', 'a14dddc1-d1f2-4426-8087-1bbfebae1e38.jpg', 1, 'rgba(233,237,239,0.9)', NULL, NULL, 1, '2016-02-29 17:36:16'),
(2, 'Ieraksta informācija', '<div class="row"><!-- Centrālā daļa -->\r\n<div class="col-lg-12">\r\n[[OBJ=ITEM]]\r\n</div>\r\n</div>', 1, 'ieraksts', 0, NULL, NULL, NULL, 'rgba(233,237,239,0.9)', NULL, NULL, NULL, NULL),
(3, 'Sākums', '<div class="row"><!-- Centrālā daļa -->\r\n<div class="col-lg-8"><!-- Ziņas--> [[OBJ=TOPARTICLES|SOURCE=0|ARTICLEPAGE=2]] [[OBJ=FEEDARTICLES|SOURCE=0|ARTICLEPAGE=2]]</div>\r\n<!-- Labās puses josla -->\r\n<div class="col-lg-4">[[OBJ=CLOUD|SOURCE=0]] [[OBJ=HTML|CODE=NOZAR]] [[OBJ=HTML|CODE=LABUMI]] [[OBJ=HTML|CODE=DARBAPIED|SOURCE=4]] [[OBJ=DAILYQUEST|SOURCE=0|HAS_LEGEND=1|TITLE=Nedēļas_jautājums]] [[OBJ=WRITEQ|SOURCE=0]] [[OBJ=SYSSTATUS|SOURCE=1]]</div>\r\n</div>', 1, 'sakums', 1, 'x_Daugava_Riga.jpg', '936989b7-e21a-44c1-bb9b-54b5e01c1474.jpg', NULL, 'rgba(233,237,239,0.9)', NULL, NULL, 1, '2016-03-11 19:26:08'),
(5, 'Elektrum', '<div class="row"><!-- Centrālā daļa -->\n<div class="col-lg-8"><!-- Ziņas--> [[OBJ=TOPARTICLES|SOURCE=2|ARTICLEPAGE=2]] [[OBJ=FEEDARTICLES|SOURCE=2|ARTICLEPAGE=2]]</div>\n<!-- Labās puses josla -->\n<div class="col-lg-4">[[OBJ=CLOUD|SOURCE=2]] [[OBJ=HTML|CODE=LABUMI]] [[OBJ=HTML|CODE=DARBAPIED|SOURCE=4]] [[OBJ=WRITEQ|SOURCE=0]]</div>\n</div>', 1, 'elektrum', 0, 'elektrum_111734612.jpg', '34e67c23-404a-4b19-934b-3b1f28e2ee27.jpg', 2, 'rgba(233,237,239,0.9)', NULL, NULL, NULL, NULL),
(6, 'Sadales tīkli', '<div class="row"><!-- Centrālā daļa -->\n<div class="col-lg-8"><!-- Ziņas--> [[OBJ=TOPARTICLES|SOURCE=3|ARTICLEPAGE=2]] [[OBJ=FEEDARTICLES|SOURCE=3|ARTICLEPAGE=2]]</div>\n<!-- Labās puses josla -->\n<div class="col-lg-4">[[OBJ=CLOUD|SOURCE=3]] [[OBJ=HTML|CODE=LABUMI]] [[OBJ=HTML|CODE=DARBAPIED|SOURCE=4]] [[OBJ=WRITEQ|SOURCE=0]]</div>\n</div>', 1, 'sadales_tikli', 0, 'ziema_putns.jpg', '2954a241-808b-4bb5-9005-54cad52b9c64.jpg', 3, 'rgba(233,237,239,0.9)', NULL, NULL, NULL, NULL),
(7, 'Dzimšanas dienas šodien', '<div class="row"><!-- Centrālā daļa -->\r\n  <div class="col-lg-12">\r\n    [[OBJ=EMPLBIRTH]]\r\n  </div>\r\n</div>', 1, 'dzimsanas_dienas_sodien', 0, 'Tulips.jpg', 'af534cec-e65a-4d2e-a5d6-6a1099e730ab.jpg', 4, 'rgba(233,237,239,0.9)', NULL, NULL, NULL, NULL),
(8, 'Dzimšanas dienas šomēnes', '<div class="row"><!-- Centrālā daļa -->\r\n  <div class="col-lg-12">\r\n    [[OBJ=EMPLBIRTH|THISDAY=0]]\r\n  </div>\r\n</div>', 1, 'dzimsanas_dienas_somenes', 0, 'Tulips.jpg', 'd7de20a0-1ab0-4098-8eca-f9aa001527b6.jpg', 4, 'rgba(233,237,239,0.9)', NULL, NULL, NULL, NULL),
(9, 'Personāls', '<div class="row"><!-- Centrālā daļa -->\n<div class="col-lg-8"><!-- Ziņas--> [[OBJ=TOPARTICLES|SOURCE=4|ARTICLEPAGE=2]] [[OBJ=FEEDARTICLES|SOURCE=4|ARTICLEPAGE=2]]</div>\n<!-- Labās puses josla -->\n<div class="col-lg-4">[[OBJ=HTML|CODE=IZMAINAS|SOURCE=4]] [[OBJ=HTML|CODE=LABUMI]] [[OBJ=HTML|CODE=DARBAPIED|SOURCE=4]]</div>\n</div>', 1, 'personals', 0, 'Penguins.jpg', 'cfa79c93-e820-42e9-be12-b7bf227835ac.jpg', 4, 'rgba(233,237,239,0.9)', NULL, NULL, 1, '2016-02-29 17:30:47'),
(10, 'Darbinieku izmaiņas', '[[OBJ=EMPLCHANGES]]', 1, 'darbinieku_izmainas', 0, NULL, NULL, 4, 'rgba(233,237,239,0.9)', NULL, NULL, NULL, NULL),
(11, 'Attēlu galerijas', '<div class="row">\r\n  <!-- Centrālā daļa -->\r\n  <div class="col-lg-12">\r\n    <h1>Galerijas</h1>\r\n    [[OBJ=GALERIES|SOURCE=0|ARTICLEPAGE=2]]\r\n  </div>\r\n</div>', 1, 'attelu_galerijas', 0, NULL, NULL, NULL, 'rgba(233,237,239,0.9)', NULL, NULL, NULL, NULL),
(13, 'Izdevumi', '<div class="row">\n  <!-- Centrālā daļa -->\n  <div class="col-lg-12">\n    <h1>Izdevumi</h1>\n    [[OBJ=PUBLISH]]\n  </div>\n</div>', 1, 'izdevumi', 0, NULL, NULL, NULL, 'rgba(233,237,239,0.9)', NULL, NULL, NULL, NULL),
(14, 'Biežāk uzdotie jautājumi', '[[OBJ=FAQ|SOURCE=0]]', 1, 'bui', 0, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2016-03-16 14:17:52'),
(15, 'Kalendārs', '[[OBJ=CALENDAR|SOURCE=0]]', 1, 'kalendars', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_roles`
--

CREATE TABLE IF NOT EXISTS `dx_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL COMMENT 'Role name - used in CMS interface',
  `is_system` int(11) NOT NULL DEFAULT '0' COMMENT 'Role system code - used in CMS PHP code',
  `description` varchar(2000) DEFAULT NULL COMMENT 'Description of the role - used in CMS interface as an explanation of the role permissions',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Lietotāju lomas - katrai lomai var nodefinēt reģistrus un darbības, kuras var veikt ar reģistriem.' AUTO_INCREMENT=27 ;

--
-- Dumping data for table `dx_roles`
--

INSERT INTO `dx_roles` (`id`, `title`, `is_system`, `description`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Sistēmas pārvaldība', 1, 'Tiesības izveidot sistēmas objektus - reģistrus, formas, skatus, izvēlnes u.c. Pilnas tiesības uz saturu. Tiesības veidot jaunas lomas un piesaistīt reģistrus lomām. Šo lomu nedrīkst dzēst. Loma pieejama tikai šīs pašas lomas lietotājiem.', NULL, NULL, 1, '2016-03-20 17:59:47'),
(2, 'Lietotāju pārvaldība', 1, 'Tiesības administrēt lietotājus - veidot jaunus lietotājus, piesaistīt tos lomām.', NULL, NULL, 1, '2016-03-20 16:32:52'),
(23, 'Intranet lietotājs', 0, 'Intranet portāla lietotāji - pieejamas publiskās lapas. Veidojot jaunas publiskās portāla lapas, tām obligāti jāpiesaista šī loma.', NULL, NULL, 1, '2016-03-20 18:00:52'),
(24, 'Portāla saturs', 0, 'Tiesības veidot visa veida ziņas un norādīt satura bloku saturu.', 1, '2016-03-20 09:30:59', 1, '2016-03-20 09:30:59'),
(25, 'Portāla pārvaldība', 0, 'Tiesības veidot HTML un satura objektus, lapas, konfigurēt integrācijas, konfigurēt portāla uzstādījumus', 1, '2016-03-20 15:34:04', 1, '2016-03-20 15:34:04'),
(26, 'Saistītie dati', 0, 'Tiesības pārvaldīt saistītos datus, piemēram, biežāk uzdotos jautājumus, laika ziņas u.c.', 1, '2016-03-21 07:21:12', 1, '2016-03-21 07:21:12');

-- --------------------------------------------------------

--
-- Table structure for table `dx_roles_lists`
--

CREATE TABLE IF NOT EXISTS `dx_roles_lists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL COMMENT 'Reference to role',
  `list_id` int(11) NOT NULL COMMENT 'Reference to register',
  `user_field_id` int(11) DEFAULT NULL COMMENT 'Reference to user - if set then it means that user can access only to the items which he created',
  `is_edit_rights` int(11) NOT NULL DEFAULT '0' COMMENT 'Indicates if user can edit allready saved item (0 - no, 1 - yes)',
  `is_delete_rights` int(11) NOT NULL DEFAULT '1' COMMENT 'Indicates if user can delete item (0 - no, 1 - yes)',
  `is_new_rights` int(11) NOT NULL DEFAULT '1' COMMENT 'Indicates if user can add new items to the register (0 - no, 1 - yes)',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dx_roles_list_uniq` (`role_id`,`list_id`),
  KEY `role_id` (`role_id`,`list_id`,`is_edit_rights`),
  KEY `user_field_id` (`user_field_id`),
  KEY `is_edit_rights` (`is_edit_rights`),
  KEY `list_id` (`list_id`),
  KEY `is_delete_rights` (`is_delete_rights`),
  KEY `is_new_rights` (`is_new_rights`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Tiesību lomās iekļautie reģistri un atļautās darbības - skatīšanās, labošana, dzēšana un tml.' AUTO_INCREMENT=267 ;

--
-- Dumping data for table `dx_roles_lists`
--

INSERT INTO `dx_roles_lists` (`id`, `role_id`, `list_id`, `user_field_id`, `is_edit_rights`, `is_delete_rights`, `is_new_rights`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 1, 10, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(2, 1, 9, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(3, 1, 8, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(4, 1, 4, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(5, 1, 3, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(6, 1, 7, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(7, 1, 16, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(8, 1, 6, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(9, 1, 11, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(10, 2, 21, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(11, 2, 22, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(12, 2, 20, NULL, 0, 0, 0, NULL, NULL, 1, '2016-03-20 16:22:48'),
(13, 1, 5, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(34, 1, 41, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(40, 1, 43, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(41, 1, 44, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(45, 1, 48, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(122, 1, 97, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(124, 1, 98, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(127, 1, 99, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(128, 1, 100, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(130, 1, 101, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(131, 1, 102, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(132, 1, 103, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(137, 1, 106, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(138, 1, 107, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(139, 1, 108, NULL, 0, 0, 0, NULL, NULL, NULL, NULL),
(140, 1, 109, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(141, 1, 110, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(145, 1, 113, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(148, 1, 114, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(150, 1, 115, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(157, 1, 119, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(158, 1, 120, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(159, 1, 121, NULL, 0, 0, 0, NULL, NULL, 1, '2016-03-16 14:23:17'),
(160, 1, 82, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(164, 1, 127, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(165, 1, 126, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(175, 1, 60, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(176, 1, 136, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(177, 1, 137, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(180, 1, 188, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(181, 1, 189, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(182, 1, 191, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(183, 1, 193, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(185, 2, 23, NULL, 0, 0, 0, NULL, NULL, 1, '2016-03-20 16:21:38'),
(186, 1, 195, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(189, 1, 93, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(190, 1, 198, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(191, 1, 200, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(192, 1, 202, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(193, 1, 208, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(194, 1, 205, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(195, 1, 209, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(196, 1, 211, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(197, 1, 212, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(198, 1, 214, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(199, 1, 215, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(200, 1, 216, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(201, 1, 222, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(202, 1, 223, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(203, 1, 224, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(204, 1, 225, NULL, 1, 1, 1, NULL, NULL, NULL, NULL),
(205, 1, 226, NULL, 1, 1, 1, 1, '2016-02-23 12:49:41', 1, '2016-02-23 12:49:41'),
(206, 1, 227, NULL, 1, 1, 1, 1, '2016-02-23 12:54:43', 1, '2016-02-23 12:54:43'),
(207, 1, 229, NULL, 1, 1, 1, 1, '2016-03-01 09:32:31', 1, '2016-03-01 09:32:31'),
(208, 1, 231, NULL, 1, 1, 1, 1, '2016-03-08 09:33:08', 1, '2016-03-08 09:33:08'),
(209, 1, 232, NULL, 1, 1, 1, 1, '2016-03-11 17:04:27', 1, '2016-03-11 17:04:27'),
(210, 1, 233, NULL, 1, 1, 1, 1, '2016-03-14 12:37:45', 1, '2016-03-14 12:37:45'),
(211, 1, 234, NULL, 1, 0, 0, 1, '2016-03-19 11:55:35', 1, '2016-03-19 11:55:35'),
(212, 24, 60, NULL, 1, 1, 1, 1, '2016-03-20 09:31:29', 1, '2016-03-20 09:31:29'),
(213, 24, 189, NULL, 1, 1, 1, 1, '2016-03-20 09:31:40', 1, '2016-03-20 09:31:40'),
(214, 24, 208, NULL, 1, 1, 1, 1, '2016-03-20 09:31:52', 1, '2016-03-20 09:31:52'),
(215, 24, 205, NULL, 1, 1, 1, 1, '2016-03-20 09:32:05', 1, '2016-03-20 09:32:05'),
(216, 24, 234, NULL, 0, 0, 0, 1, '2016-03-20 09:32:36', 1, '2016-03-20 09:32:36'),
(217, 24, 232, NULL, 1, 1, 1, 1, '2016-03-20 09:33:02', 1, '2016-03-20 09:33:02'),
(218, 24, 191, NULL, 1, 1, 1, 1, '2016-03-20 09:33:20', 1, '2016-03-20 09:33:20'),
(219, 24, 194, NULL, 1, 0, 0, 1, '2016-03-20 09:34:03', 1, '2016-03-20 09:34:03'),
(220, 24, 126, NULL, 1, 1, 1, 1, '2016-03-20 09:34:38', 1, '2016-03-20 09:34:38'),
(221, 24, 127, NULL, 1, 1, 1, 1, '2016-03-20 09:35:20', 1, '2016-03-20 09:35:20'),
(222, 1, 235, NULL, 1, 1, 1, 1, '2016-03-20 09:53:47', 1, '2016-03-20 09:53:47'),
(223, 25, 119, NULL, 1, 1, 1, 1, '2016-03-20 15:36:10', 1, '2016-03-20 15:36:10'),
(224, 25, 120, NULL, 1, 1, 1, 1, '2016-03-20 15:36:27', 1, '2016-03-20 15:36:27'),
(225, 25, 222, NULL, 0, 0, 0, 1, '2016-03-20 15:36:49', 1, '2016-03-20 15:39:14'),
(226, 25, 5, NULL, 1, 1, 1, 1, '2016-03-20 15:37:05', 1, '2016-03-20 15:37:05'),
(227, 25, 193, NULL, 1, 0, 0, 1, '2016-03-20 15:37:17', 1, '2016-03-20 15:46:45'),
(228, 25, 100, NULL, 1, 1, 1, 1, '2016-03-20 15:37:30', 1, '2016-03-20 15:37:30'),
(229, 25, 101, NULL, 1, 1, 1, 1, '2016-03-20 15:37:43', 1, '2016-03-20 15:37:43'),
(230, 25, 216, NULL, 0, 0, 0, 1, '2016-03-20 15:38:00', 1, '2016-03-20 15:39:05'),
(231, 25, 229, NULL, 1, 1, 1, 1, '2016-03-20 15:38:23', 1, '2016-03-20 15:38:23'),
(232, 25, 224, NULL, 1, 1, 0, 1, '2016-03-20 15:38:45', 1, '2016-03-20 15:38:45'),
(233, 25, 225, NULL, 0, 0, 0, 1, '2016-03-20 15:38:56', 1, '2016-03-20 15:38:56'),
(234, 25, 235, NULL, 1, 1, 1, 1, '2016-03-20 15:39:42', 1, '2016-03-20 15:39:42'),
(235, 25, 226, NULL, 1, 1, 1, 1, '2016-03-20 15:40:00', 1, '2016-03-20 15:40:00'),
(236, 25, 227, NULL, 1, 1, 1, 1, '2016-03-20 15:44:05', 1, '2016-03-20 15:44:05'),
(237, 25, 188, NULL, 1, 0, 0, 1, '2016-03-20 16:01:25', 1, '2016-03-20 16:01:25'),
(238, 25, 233, NULL, 1, 0, 0, 1, '2016-03-20 16:01:48', 1, '2016-03-20 16:01:48'),
(239, 1, 20, NULL, 1, 1, 1, 1, '2016-03-20 16:20:27', 1, '2016-03-20 16:20:27'),
(240, 1, 21, NULL, 1, 1, 1, 1, '2016-03-20 16:20:50', 1, '2016-03-20 16:20:50'),
(241, 1, 23, NULL, 1, 1, 1, 1, '2016-03-20 16:21:27', 1, '2016-03-20 16:21:27'),
(242, 1, 22, NULL, 1, 1, 1, 1, '2016-03-20 16:24:01', 1, '2016-03-20 16:24:01'),
(244, 24, 188, NULL, 0, 0, 0, 1, '2016-03-20 18:22:17', 1, '2016-03-20 18:22:17'),
(245, 24, 233, NULL, 0, 0, 0, 1, '2016-03-20 18:22:36', 1, '2016-03-20 18:22:36'),
(246, 24, 119, NULL, 0, 0, 0, 1, '2016-03-20 18:22:54', 1, '2016-03-20 18:22:54'),
(247, 25, 121, NULL, 1, 0, 0, 1, '2016-03-20 18:24:11', 1, '2016-03-20 18:24:19'),
(248, 24, 121, NULL, 0, 0, 0, 1, '2016-03-20 18:24:26', 1, '2016-03-20 18:24:26'),
(249, 25, 126, NULL, 1, 1, 1, 1, '2016-03-20 18:30:06', 1, '2016-03-20 18:30:06'),
(250, 25, 3, NULL, 0, 0, 0, 1, '2016-03-20 18:31:12', 1, '2016-03-20 18:31:12'),
(251, 25, 8, NULL, 0, 0, 0, 1, '2016-03-20 18:32:32', 1, '2016-03-20 18:32:32'),
(252, 25, 215, NULL, 0, 0, 0, 1, '2016-03-20 18:35:28', 1, '2016-03-20 18:35:28'),
(253, 26, 198, NULL, 1, 1, 1, 1, '2016-03-21 07:21:39', 1, '2016-03-21 07:21:39'),
(254, 26, 202, NULL, 0, 1, 0, 1, '2016-03-21 07:21:48', 1, '2016-03-21 07:27:30'),
(255, 26, 200, NULL, 1, 1, 1, 1, '2016-03-21 07:21:58', 1, '2016-03-21 07:21:58'),
(256, 26, 211, NULL, 1, 1, 1, 1, '2016-03-21 07:22:10', 1, '2016-03-21 07:22:10'),
(257, 26, 95, NULL, 0, 0, 0, 1, '2016-03-21 07:22:24', 1, '2016-03-21 07:22:24'),
(258, 26, 195, NULL, 1, 1, 0, 1, '2016-03-21 07:22:45', 1, '2016-03-21 07:22:45'),
(259, 26, 214, NULL, 1, 1, 1, 1, '2016-03-21 07:22:59', 1, '2016-03-21 07:22:59'),
(260, 26, 212, NULL, 1, 1, 1, 1, '2016-03-21 07:23:11', 1, '2016-03-21 07:23:11'),
(261, 26, 209, NULL, 1, 1, 1, 1, '2016-03-21 07:23:26', 1, '2016-03-21 07:23:26'),
(262, 26, 137, NULL, 1, 1, 1, 1, '2016-03-21 07:23:41', 1, '2016-03-21 07:23:41'),
(263, 26, 136, NULL, 1, 1, 1, 1, '2016-03-21 07:23:51', 1, '2016-03-21 07:23:51'),
(264, 26, 93, NULL, 1, 1, 1, 1, '2016-03-21 07:24:13', 1, '2016-03-21 07:24:13'),
(265, 26, 223, NULL, 0, 0, 0, 1, '2016-03-21 07:24:43', 1, '2016-03-21 07:24:43'),
(266, 26, 82, NULL, 1, 1, 1, 1, '2016-03-21 07:24:58', 1, '2016-03-21 07:24:58');

-- --------------------------------------------------------

--
-- Table structure for table `dx_roles_pages`
--

CREATE TABLE IF NOT EXISTS `dx_roles_pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `page_id` int(11) NOT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dx_roles_pages_uniq` (`role_id`,`page_id`),
  KEY `role_id` (`role_id`,`page_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=37 ;

--
-- Dumping data for table `dx_roles_pages`
--

INSERT INTO `dx_roles_pages` (`id`, `role_id`, `page_id`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(8, 23, 1, NULL, NULL, NULL, NULL),
(9, 23, 2, NULL, NULL, NULL, NULL),
(10, 23, 3, NULL, NULL, NULL, NULL),
(15, 23, 7, NULL, NULL, NULL, NULL),
(17, 23, 8, NULL, NULL, NULL, NULL),
(19, 23, 10, NULL, NULL, NULL, NULL),
(21, 23, 5, NULL, NULL, NULL, NULL),
(23, 23, 6, NULL, NULL, NULL, NULL),
(25, 23, 9, NULL, NULL, NULL, NULL),
(27, 23, 11, NULL, NULL, NULL, NULL),
(31, 23, 13, NULL, NULL, NULL, NULL),
(33, 23, 14, NULL, NULL, NULL, NULL),
(35, 23, 15, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_sort_types`
--

CREATE TABLE IF NOT EXISTS `dx_sort_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT 'Sort order title - visible in CMS interface',
  `sys_name` varchar(50) NOT NULL COMMENT 'Sort order code - used in PHP code',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Sistēmisks klasifikators - kārtošanas veidi. Izmanto definējot datu skatus, norādot pēc kuras kolonnas un kādā veidā veikt noklusēto kārtošanu.' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `dx_sort_types`
--

INSERT INTO `dx_sort_types` (`id`, `title`, `sys_name`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Augoši', 'ASC', NULL, NULL, NULL, NULL),
(2, 'Dilstoši', 'DESC', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_tasks`
--

CREATE TABLE IF NOT EXISTS `dx_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `item_reg_nr` varchar(200) DEFAULT NULL,
  `item_info` varchar(4000) DEFAULT NULL,
  `task_type_id` int(11) DEFAULT NULL,
  `task_created_time` datetime NOT NULL,
  `task_details` varchar(4000) DEFAULT NULL,
  `task_status_id` int(11) DEFAULT NULL,
  `task_employee_id` int(11) DEFAULT NULL,
  `task_comment` varchar(4000) DEFAULT NULL,
  `task_closed_time` datetime DEFAULT NULL,
  `step_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `list_id` (`list_id`,`item_id`,`item_reg_nr`,`task_type_id`,`task_created_time`,`task_status_id`,`task_employee_id`),
  KEY `task_type_id` (`task_type_id`),
  KEY `task_status_id` (`task_status_id`),
  KEY `task_employee_id` (`task_employee_id`),
  KEY `step_id` (`step_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dx_tasks_perform`
--

CREATE TABLE IF NOT EXISTS `dx_tasks_perform` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `dx_tasks_perform`
--

INSERT INTO `dx_tasks_perform` (`id`, `title`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Darbinieks', NULL, NULL, NULL, NULL),
(2, 'Tiešais vadītājs', NULL, NULL, NULL, NULL),
(3, 'Dokumenta sagatavotājs', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_tasks_statuses`
--

CREATE TABLE IF NOT EXISTS `dx_tasks_statuses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `dx_tasks_statuses`
--

INSERT INTO `dx_tasks_statuses` (`id`, `title`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Procesā', NULL, NULL, NULL, NULL),
(2, 'Izpildīts', NULL, NULL, NULL, NULL),
(3, 'Noraidīts', NULL, NULL, NULL, NULL),
(4, 'Anulēts', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_tasks_types`
--

CREATE TABLE IF NOT EXISTS `dx_tasks_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `dx_tasks_types`
--

INSERT INTO `dx_tasks_types` (`id`, `title`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Saskaņot', NULL, NULL, NULL, NULL),
(2, 'Izpildīt', NULL, NULL, NULL, NULL),
(3, 'Papildināt un saskaņot', NULL, NULL, NULL, NULL),
(4, 'Uzstādīt vērtību', NULL, NULL, NULL, NULL),
(5, 'Kritērijs', NULL, NULL, NULL, NULL),
(6, 'Informācija', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_users`
--

CREATE TABLE IF NOT EXISTS `dx_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login_name` varchar(200) DEFAULT NULL COMMENT 'User login name',
  `passw` varchar(200) DEFAULT NULL COMMENT 'Password - not encrypted, for first time register',
  `password` varchar(100) DEFAULT NULL COMMENT 'Password - encrypted',
  `email` varchar(200) DEFAULT NULL COMMENT 'User email',
  `display_name` varchar(200) DEFAULT NULL COMMENT 'First and last name',
  `position_title` varchar(200) DEFAULT NULL COMMENT 'Position name',
  `dashboard_id` int(11) DEFAULT NULL COMMENT 'Default page to load after login',
  `remember_token` int(100) DEFAULT NULL COMMENT 'Used by Laravel framevork to provide authentification (redirecting to initial request after login)',
  `picture_name` varchar(500) DEFAULT NULL COMMENT 'User profile picture file name',
  `picture_guid` varchar(100) DEFAULT NULL COMMENT 'User profiule picture file guid - all files are saved in file system with GUIDs as file name',
  `birth_date` date DEFAULT NULL COMMENT 'User''s birth date',
  `valid_from` date DEFAULT NULL COMMENT 'Date from wich user is valid',
  `valid_to` date DEFAULT NULL COMMENT 'Date till which user is valid',
  `department_id` int(11) DEFAULT NULL COMMENT 'Reference to the department',
  `description` text COMMENT 'Brief description of the employee',
  `phone` varchar(50) DEFAULT NULL COMMENT 'Phone number',
  `mobile` varchar(50) DEFAULT NULL COMMENT 'Mobile number',
  `fax` varchar(50) DEFAULT NULL COMMENT 'Fax number',
  `is_blocked` tinyint(1) NOT NULL,
  `auth_attempts` int(11) NOT NULL DEFAULT '0',
  `last_attempt` datetime DEFAULT NULL,
  `ad_login` varchar(200) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL COMMENT 'Datu avots',
  PRIMARY KEY (`id`),
  KEY `login_name` (`login_name`),
  KEY `dashboard_id` (`dashboard_id`),
  KEY `department_id` (`department_id`),
  KEY `dx_users_ad_login_index` (`ad_login`),
  KEY `dx_users_source_id_index` (`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='SVS lietotāji un organizācijas darbinieki. Uz šo tabulu parasti vienmēr jāveido vismaz 2 reģistri - lietotāju reģistrs un darbinieku reģistrs. Parasti SVS lietotāji ir arī uzņēmuma darbinieki. Lietotājus pārvalda sistēmas administrators, bet darbiniekus personāldaļa. Tādā veidā SVS lietotāji ir automātiski integrēti arī ar uzņēmuma personālu.' AUTO_INCREMENT=23 ;

--
-- Dumping data for table `dx_users`
--

INSERT INTO `dx_users` (`id`, `login_name`, `passw`, `password`, `email`, `display_name`, `position_title`, `dashboard_id`, `remember_token`, `picture_name`, `picture_guid`, `birth_date`, `valid_from`, `valid_to`, `department_id`, `description`, `phone`, `mobile`, `fax`, `is_blocked`, `auth_attempts`, `last_attempt`, `ad_login`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `source_id`) VALUES
(1, 'admin', 'laime911', '$2y$10$S803OIF2qhP8ZliilBZI9eYGCMXhhvAkiWuXXcG3J.Pp2hnImjv2q', 'janis.macans@latvenergo.lv', 'Jānis Mačāns', 'Sistēmas administrators', NULL, 0, NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, 0, 0, '2016-03-20 18:23:44', NULL, NULL, NULL, 1, '2016-03-20 17:55:02', NULL),
(2, 'public', NULL, NULL, NULL, '[Publisks lietotājs]', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, 'Publiskais anonīmais portāla lietotājs', NULL, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 'renars', NULL, '$2y$10$9ggAapZie4XJqufvAjgym.M6wTfN8MFBsEAbuoTcs3VO8DpW.BKue', 'renars.osis@latvenergo.lv', 'Renārs Osis', 'Projektu vadītājs', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2016-03-21 07:18:34', NULL, 1, '2016-03-19 12:34:36', 1, '2016-03-20 18:06:43', NULL),
(19, 'vija', NULL, '$2y$10$nYpciLCfWvLxjbsAjSADnuBo8XWLZuSozDBktPNG3Ginub95jdsLK', 'vija.liduma@latvenergo.lv', 'Vija Līduma', 'Sistēmu administratore', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2016-03-20 18:28:43', NULL, 1, '2016-03-20 15:34:58', 1, '2016-03-20 15:34:58', NULL),
(20, 'janis', NULL, '$2y$10$UOGUl/j3B5JvsVMUrboP7umVDDRgTJ3lvrjZMRJ0HvJTNA4ERKSKW', 'janis.siders@latvenergo.lv', 'Jānis Siders', 'Projektu vadītājs', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2016-03-20 18:36:35', NULL, 1, '2016-03-20 17:33:10', 1, '2016-03-20 17:33:10', NULL),
(21, 'arturs', NULL, '$2y$10$K/0jb8JPNWoVEP9xqSFkweAMBL378G4fwf/SWMP6FkJQOfk48Miqq', 'arturs.zalitis@latvenergo.lv', 'Artūrs Zālītis', 'Portāla satura administrators', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2016-03-21 07:28:58', NULL, 1, '2016-03-20 18:03:06', 1, '2016-03-20 18:03:37', 3),
(22, 'inga', NULL, '$2y$10$x3vu7gbupfWsqp21zIsaau6YKR3V288KDlYA69/3p4miF1v33SmWy', 'inga.kaulina@latvenergo.lv', 'Inga Kauliņa', 'Portāla satura administratore', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, '2016-03-20 18:04:46', NULL, 1, '2016-03-20 18:04:15', 1, '2016-03-20 18:04:15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `dx_users_roles`
--

CREATE TABLE IF NOT EXISTS `dx_users_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'Reference to user',
  `role_id` int(11) NOT NULL COMMENT 'Reference to role',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`,`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Informācija par SVS lietotāja lomām. Katrs lietotājs var būt vairākās lomās.' AUTO_INCREMENT=57 ;

--
-- Dumping data for table `dx_users_roles`
--

INSERT INTO `dx_users_roles` (`id`, `user_id`, `role_id`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 1, 1, NULL, NULL, NULL, NULL),
(40, 2, 23, NULL, NULL, NULL, NULL),
(42, 18, 24, 1, '2016-03-20 09:31:10', 1, '2016-03-20 09:31:10'),
(43, 18, 23, 1, '2016-03-20 10:38:12', 1, '2016-03-20 10:38:12'),
(44, 19, 25, 1, '2016-03-20 15:35:03', 1, '2016-03-20 15:35:03'),
(45, 19, 2, 1, '2016-03-20 16:23:17', 1, '2016-03-20 16:23:17'),
(46, 1, 23, 1, '2016-03-20 16:45:33', 1, '2016-03-20 16:45:33'),
(48, 19, 23, 19, '2016-03-20 17:23:01', 19, '2016-03-20 17:23:01'),
(50, 20, 1, 1, '2016-03-20 17:33:16', 1, '2016-03-20 17:33:16'),
(51, 20, 23, 1, '2016-03-20 17:33:26', 1, '2016-03-20 17:33:26'),
(52, 21, 23, 1, '2016-03-20 18:03:18', 1, '2016-03-20 18:03:18'),
(53, 21, 24, 1, '2016-03-20 18:03:26', 1, '2016-03-20 18:03:26'),
(54, 22, 23, 1, '2016-03-20 18:04:24', 1, '2016-03-20 18:04:24'),
(55, 22, 24, 1, '2016-03-20 18:04:31', 1, '2016-03-20 18:04:31'),
(56, 18, 26, 1, '2016-03-21 07:21:20', 1, '2016-03-21 07:21:20');

-- --------------------------------------------------------

--
-- Table structure for table `dx_views`
--

CREATE TABLE IF NOT EXISTS `dx_views` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL COMMENT 'Reference to register',
  `title` varchar(500) DEFAULT NULL COMMENT 'View''s title',
  `view_type_id` int(11) DEFAULT NULL,
  `is_default` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Indicates if view is default which will be loaded in CMS interface for specific register (0 - no, 1 - yes)',
  `is_hidden_from_main_grid` tinyint(4) DEFAULT '0' COMMENT 'Indicates if view is hidden from register''s grid form (0 - no, 1 - yes)',
  `is_hidden_from_tabs` tinyint(4) DEFAULT '0' COMMENT 'Indicates if view is hidden in forms sub-section tab grid (0 - no, 1 - yes)',
  `is_for_workflow` int(11) DEFAULT '0',
  `custom_sql` text,
  `url` varchar(250) DEFAULT NULL,
  `is_for_monitoring` int(11) DEFAULT '0',
  `is_for_word_generating` int(11) DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`),
  KEY `list_id` (`list_id`),
  KEY `is_default` (`is_default`),
  KEY `is_hidden_from_main_grid` (`is_hidden_from_main_grid`,`is_hidden_from_tabs`),
  KEY `view_type_id` (`view_type_id`),
  KEY `is_for_workflow` (`is_for_workflow`),
  KEY `is_for_monitoring` (`is_for_monitoring`),
  KEY `is_for_word_generating` (`is_for_word_generating`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Reģistru datu skati - skatā var norādīt pieejamās kolonnas, secību, kārtošanu, datu atlases nosacījumus. Vienam reģistram var būt vairāki datu skati. Var definēt, vai skati redzami formu sadaļās vai galvenajos sarakstos. Ja reģistram ir vairāki skati, tad virs sarakstiem rādās izkrītošā izvēlne ar visiem pieejamajiem reģistra skatiem.' AUTO_INCREMENT=252 ;

--
-- Dumping data for table `dx_views`
--

INSERT INTO `dx_views` (`id`, `list_id`, `title`, `view_type_id`, `is_default`, `is_hidden_from_main_grid`, `is_hidden_from_tabs`, `is_for_workflow`, `custom_sql`, `url`, `is_for_monitoring`, `is_for_word_generating`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 3, 'Visi reģistri', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(2, 4, 'Visi objekti', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(7, 7, 'Reģistru lauki', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(8, 6, 'Skati', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(9, 10, 'Formas', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(10, 9, 'Formu lauki', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(11, 11, 'Visu skatu lauki', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(12, 5, 'Visas izvēlnes', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(13, 8, 'Visi lauku tipi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(18, 16, 'Sadaļas', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(22, 20, 'Lomas', 9, 1, 0, 0, 0, 'select * from (select dx_roles.id as id, dx_roles.title as dx_roles_title, title, is_system, description FROM dx_roles WHERE id != 1 or [ME] in (SELECT user_id FROM dx_users_roles WHERE role_id = 1)) t WHERE 1=1', NULL, 0, 0, NULL, NULL, 1, '2016-03-20 18:43:06'),
(23, 21, 'Lietotāji', 9, 1, 0, 0, 0, 'select * from (select dx_users.id as id, login_name, password, email, dx_users.display_name as dx_users_display_name, display_name, position_title, ad_login, in_sources.title as in_sources_1_title from dx_users left join in_sources on dx_users.source_id = in_sources.id WHERE dx_users.id not in (SELECT user_id FROM dx_users_roles WHERE role_id = 1) or [ME] in (SELECT user_id FROM dx_users_roles WHERE role_id = 1)) t where 1=1', NULL, 0, 0, NULL, NULL, 1, '2016-03-20 18:44:25'),
(24, 22, 'Lietotāju lomas', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(25, 23, 'Lomu reģistri', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(34, 41, 'Datu modelis', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(37, 43, 'Lauku operācijas', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(41, 44, 'Secību veidi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(47, 48, 'Agregāciju veidi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(66, 60, 'Ziņas', 1, 1, 0, 1, 0, NULL, NULL, 0, 0, NULL, NULL, 1, '2016-03-20 09:43:41'),
(88, 82, 'Vārdadienas', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(90, 60, 'Aktīvās ziņas', 1, 0, 1, 1, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(109, 93, 'Notikumu kalendārs', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(110, 94, 'Darbplūsmu lietotāji', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, 1, '2016-03-08 09:29:24'),
(111, 95, 'Darbinieku vēsture', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(113, 97, 'Portāla statistika', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(114, 98, 'Numeratori', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(115, 99, 'Formu JavaScript', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(116, 100, 'Lapas', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(117, 101, 'Lapu lomas', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(118, 102, 'Skatu veidi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(119, 103, 'Lauku attēlošanas veidi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(122, 106, 'Uzdevumu statusi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(123, 107, 'Uzdevumu veidi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(124, 108, 'Mani aktuālie uzdevumi', 9, 1, 0, 1, 0, 'select * from\r\n(\r\nselect \r\n	t.id, \r\n    l.list_title as v_list_title, \r\n    t.item_reg_nr as v_doc_nr, \r\n    t.item_info as v_doc_info, \r\n    s.title as v_task_status, \r\n    tt.title as v_task_type, \r\n    t.task_created_time as v_task_created,\r\n      t.item_id\r\nfrom \r\n	dx_tasks t \r\n    inner join dx_lists l on t.list_id = l.id \r\n    inner join dx_tasks_statuses s on t.task_status_id=s.id \r\n    inner join dx_tasks_types tt on t.task_type_id = tt.id\r\nwhere\r\n	t.task_employee_id = [ME]\r\n    AND t.task_closed_time is null\r\n) tb\r\nWHERE 1 = 1\r\n\r\n\r\n\r\n\r\n\r\n', 'aktualie_uzdevumi', 0, 0, NULL, NULL, 1, '2016-03-12 09:52:53'),
(125, 109, 'Uzdevumu izpildītāju veidi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(126, 110, 'Darbplūsmas', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(129, 113, 'Formu veidi', 1, 1, 0, 0, 0, '    \r\n', NULL, 0, 0, NULL, NULL, NULL, NULL),
(130, 114, 'Ierakstu statusi', 1, 1, 0, 0, 0, '    \r\n', 'ierakstu_statusi', 0, 0, NULL, NULL, NULL, NULL),
(131, 108, 'Dokumenta uzdevumi', 9, 0, 1, 0, 0, 'select * from\n(\nselect \n	t.id, \n    u.display_name as v_employee,  \n    tt.title as v_task_type, \n    t.task_created_time as v_task_created,\n    s.title as v_task_status,\n    t.item_id,\n    t.task_closed_time as v_closed_time,\n    t.task_comment\nfrom \n	dx_tasks t \n    inner join dx_lists l on t.list_id = l.id \n    inner join dx_tasks_statuses s on t.task_status_id=s.id \n    inner join dx_tasks_types tt on t.task_type_id = tt.id\n    inner join dx_users u on t.task_employee_id=u.id\nWHERE t.item_id = [ITEM_ID]\n) tb\nWHERE 1=1\n', NULL, 0, 0, NULL, NULL, NULL, NULL),
(132, 115, 'Darbplūsmu lauki', 1, 1, 0, 0, 0, '    \r\n', 'darbplusmu_lauki', 0, 0, NULL, NULL, NULL, NULL),
(135, 108, 'Mani pabeigtie uzdevumi', 9, 0, 0, 1, 0, 'select * from\r\n(\r\nselect \r\n	t.id, \r\n    l.list_title as v_list_title, \r\n    t.item_reg_nr as v_doc_nr, \r\n    t.item_info as v_doc_info, \r\n    s.title as v_task_status, \r\n    tt.title as v_task_type, \r\n    t.task_created_time as v_task_created,\r\n      t.item_id,\r\n    t.task_closed_time as v_closed_time,\r\n  t.task_comment\r\nfrom \r\n	dx_tasks t \r\n    inner join dx_lists l on t.list_id = l.id \r\n    inner join dx_tasks_statuses s on t.task_status_id=s.id \r\n    inner join dx_tasks_types tt on t.task_type_id = tt.id\r\nwhere\r\n	t.task_employee_id = [ME]\r\n    AND t.task_closed_time is not null\r\n) tb\r\nWHERE 1=1', 'pabeigtie_uzdevumi', 0, 0, NULL, NULL, 1, '2016-03-12 09:53:03'),
(139, 119, 'Datu avoti', 1, 1, 0, 0, 0, NULL, 'datu_avoti', 0, 0, NULL, NULL, NULL, NULL),
(140, 120, 'HTML bloki', 1, 1, 0, 0, 0, NULL, 'html_bloki', 0, 0, NULL, NULL, NULL, NULL),
(141, 121, 'Aktuālie', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(142, 121, 'Vairs nestrādā', 1, 0, 0, 0, 0, 'select e.id, e.employee_name,e.email, e.phone, s.title from in_employees e, in_sources s where e.source_id=s.id and end_date is not NULL', NULL, 0, 0, NULL, NULL, NULL, NULL),
(147, 127, 'Raksta iezīme', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(156, 126, 'Iezīmes', 1, 1, 0, 0, 0, NULL, 'iezimes', 0, 0, NULL, NULL, NULL, NULL),
(157, 136, 'Laikapstākļu veidi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(158, 137, 'Laika ziņas', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(196, 188, 'Ziņu veidi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(197, 189, 'Attēlu galerijas', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, 1, '2016-03-20 09:44:06'),
(200, 191, 'Attēli', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(202, 193, 'Konfigurācija', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(203, 194, 'Satura bloki', 1, 1, 0, 0, 0, NULL, 'satura_bloki', 0, 0, NULL, NULL, 1, '2016-03-20 09:51:51'),
(204, 195, 'Jautājumi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(207, 198, 'Dienas jautājumi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(209, 200, 'Atbilžu varianti', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(211, 202, 'Atbildes uz jautājumiem', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(214, 205, 'Video', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(217, 208, 'Video galerijas', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, 1, '2016-03-20 09:46:08'),
(218, 209, 'Jautājumu nodaļas', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(220, 211, 'Biežāk uzdotie jautājumi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(221, 212, 'Izdevumu tipi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(223, 214, 'Izdevumi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(224, 215, 'Darbību veidi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(225, 216, 'Lietotāju darbības', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(231, 222, 'Izmaiņu vēsture', 1, 1, 1, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(232, 223, 'Struktūrvienības', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(233, 224, 'Procesi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(234, 225, 'Procesu vēsture', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(235, 226, 'Sistēmas', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(236, 227, 'Sistēmu incidenti', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(238, 229, 'Lotus Notes sistēmas', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(240, 231, 'Reģistru grupas', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(241, 232, 'Ziņu datnes', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(246, 233, 'Ziņu papildus veidi', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, NULL, NULL),
(247, 60, 'Ziņas galerijām', 1, 0, 1, 0, 0, NULL, NULL, 0, 0, 1, '2016-03-15 18:34:33', 1, '2016-03-15 18:34:51'),
(250, 234, 'Ziņas - galeriju sasaiste', 1, 1, 1, 0, 0, NULL, NULL, 0, 0, 1, '2016-03-15 18:34:33', 1, '2016-03-20 09:45:47'),
(251, 235, 'Satura bloki', 1, 1, 0, 0, 0, NULL, NULL, 0, 0, NULL, NULL, 1, '2016-03-20 09:51:51');

-- --------------------------------------------------------

--
-- Table structure for table `dx_views_fields`
--

CREATE TABLE IF NOT EXISTS `dx_views_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `list_id` int(11) NOT NULL COMMENT 'Reference to register',
  `view_id` int(11) NOT NULL COMMENT 'Reference to view',
  `field_id` int(11) NOT NULL COMMENT 'Reference to register''s field',
  `width` int(11) NOT NULL DEFAULT '100' COMMENT 'Width of grid column in pixels',
  `order_index` int(11) NOT NULL DEFAULT '0' COMMENT 'Order of column in the grid',
  `align` enum('left','right','center') NOT NULL DEFAULT 'left' COMMENT 'Alignment of the content in the grid''s cell',
  `is_item_link` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Indicates if an clickable link will be put on the cell''s text (0 - no, 1 - yes)',
  `alias_name` varchar(200) DEFAULT NULL COMMENT 'If provided then different title for the grid column',
  `operation_id` int(11) DEFAULT NULL COMMENT 'Reference to the filter criteria operation',
  `criteria` varchar(2000) DEFAULT NULL COMMENT 'Filter criteria - wildcards can be used',
  `is_hidden` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Indicated if field is hidden. NB! All fields must contain collumn ID - at least as hidden',
  `sort_type_id` int(11) DEFAULT NULL COMMENT 'Reference to sorting',
  `is_sum` tinyint(4) DEFAULT '0' COMMENT 'Indicates if field have SUM row - used for currency fields (0 - no, 1 - yes)',
  `aggregation_id` int(11) DEFAULT NULL COMMENT 'Reference to aggregation type',
  `represent_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_uniq_view_field` (`view_id`,`field_id`) COMMENT 'Skatu lauku var iekļaut vienu reizi',
  KEY `view_id` (`view_id`,`field_id`,`order_index`),
  KEY `aligb` (`align`),
  KEY `is_item_link` (`is_item_link`),
  KEY `list_id` (`list_id`),
  KEY `alias_name` (`alias_name`),
  KEY `operation_id` (`operation_id`,`is_hidden`),
  KEY `sort_type_id` (`sort_type_id`),
  KEY `is_sum` (`is_sum`),
  KEY `aggregation_id` (`aggregation_id`),
  KEY `field_id` (`field_id`),
  KEY `represent_id` (`represent_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Reģistru datu skatu lauki. Var norādīt, kādas kolonnas, kādā secībā un pēc kādiem nosacījumiem tiek attēlotas sarakstos.' AUTO_INCREMENT=1298 ;

--
-- Dumping data for table `dx_views_fields`
--

INSERT INTO `dx_views_fields` (`id`, `list_id`, `view_id`, `field_id`, `width`, `order_index`, `align`, `is_item_link`, `alias_name`, `operation_id`, `criteria`, `is_hidden`, `sort_type_id`, `is_sum`, `aggregation_id`, `represent_id`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 3, 1, 7, 100, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 3, 1, 1, 100, 2, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 3, 1, 4, 100, 5, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 4, 2, 8, 100, 0, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 4, 2, 6, 100, 1, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 7, 7, 16, 100, 1, 'left', 0, NULL, NULL, NULL, 1, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 7, 7, 17, 100, 2, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 7, 7, 23, 100, -10, 'left', 1, 'Nosaukums', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 6, 8, 28, 100, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 6, 8, 29, 100, 2, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 6, 8, 30, 100, 3, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 10, 9, 35, 100, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 10, 9, 33, 100, 2, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 9, 10, 42, 100, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 9, 10, 44, 100, 2, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 9, 10, 43, 100, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 9, 10, 45, 100, 4, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 11, 11, 36, 100, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 11, 11, 50, 100, 2, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 11, 11, 37, 100, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 11, 11, 38, 100, 4, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 11, 11, 40, 100, 5, 'left', 0, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 5, 12, 51, 100, 1, 'left', 1, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 5, 12, 54, 100, 5, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 5, 12, 53, 100, 2, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 5, 12, 52, 100, 3, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 8, 13, 18, 100, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 8, 13, 19, 100, 2, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 5, 12, 55, 100, 60, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 16, 18, 65, 100, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 16, 18, 66, 100, 2, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 16, 18, 67, 100, 3, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 16, 18, 73, 100, 6, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 10, 9, 32, 100, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 20, 22, 91, 80, 1, 'left', 1, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, 1, '2016-03-20 15:13:37'),
(68, 20, 22, 92, 200, 2, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, 1, '2016-03-20 15:14:35'),
(70, 21, 23, 95, 100, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 21, 23, 96, 150, 2, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 21, 23, 98, 200, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 21, 23, 99, 200, 4, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 22, 24, 100, 100, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 22, 24, 101, 150, 2, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 22, 24, 102, 200, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 23, 25, 103, 100, 1, 'left', 1, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, 1, '2016-03-20 15:28:18'),
(78, 23, 25, 104, 150, 2, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(79, 23, 25, 105, 100, 3, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, 1, '2016-03-20 15:28:30'),
(80, 23, 25, 106, 100, 20, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, 1, '2016-03-20 15:28:46'),
(96, 8, 13, 135, 100, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 7, 7, 21, 100, 4, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(120, 41, 34, 165, 100, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(121, 41, 34, 166, 150, 2, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(122, 41, 34, 167, 150, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(123, 41, 34, 168, 100, 4, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(132, 9, 10, 48, 50, 5, 'left', 0, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(135, 43, 37, 176, 100, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(136, 43, 37, 177, 150, 2, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(137, 43, 37, 178, 100, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(171, 44, 41, 184, 80, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(172, 44, 41, 185, 100, 2, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(173, 44, 41, 186, 100, 100, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(201, 48, 47, 206, 80, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(202, 48, 47, 207, 150, 2, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(203, 48, 47, 208, 100, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(295, 60, 66, 286, 50, 1, 'left', 1, NULL, NULL, NULL, 0, 2, 0, NULL, NULL, NULL, NULL, 1, '2016-03-18 17:55:17'),
(296, 60, 66, 287, 300, 2, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(300, 60, 66, 291, 50, 7, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(436, 82, 88, 429, 70, 0, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(437, 82, 88, 430, 80, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(438, 82, 88, 431, 80, 2, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(439, 82, 88, 432, 0, 3, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(450, 60, 90, 286, 50, 0, 'left', 1, NULL, NULL, NULL, 0, 2, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(451, 60, 90, 287, 0, 0, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, 1, NULL, NULL, NULL, NULL),
(454, 60, 90, 291, 0, -300, 'left', 0, NULL, 1, '1', 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(570, 4, 2, 5, 100, 5, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(571, 60, 66, 546, 100, 4, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(572, 60, 66, 547, 100, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(591, 93, 109, 566, 70, 0, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(592, 93, 109, 567, 0, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(593, 93, 109, 568, 0, 2, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(594, 93, 109, 569, 0, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(595, 93, 109, 570, 0, 4, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(596, 93, 109, 571, 0, 5, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(597, 93, 109, 572, 0, 6, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(598, 93, 109, 573, 70, 7, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(599, 94, 110, 574, 70, 0, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(600, 94, 110, 575, 100, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(601, 94, 110, 576, 120, 2, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(602, 94, 110, 577, 120, 3, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(604, 94, 110, 579, 150, 5, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(605, 95, 111, 580, 70, 0, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(606, 95, 111, 581, 0, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(607, 95, 111, 582, 0, 2, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(608, 95, 111, 583, 0, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(609, 95, 111, 584, 0, 4, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(610, 95, 111, 585, 0, 5, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(611, 95, 111, 586, 0, 6, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(612, 95, 111, 587, 0, 7, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(616, 94, 110, 595, 100, 6, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(617, 94, 110, 596, 100, 7, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(618, 60, 90, 547, 0, 0, 'left', 0, NULL, NULL, NULL, 0, 2, 0, NULL, 3, NULL, NULL, NULL, NULL),
(619, 60, 90, 598, 100, -200, 'left', 0, NULL, 1, '1', 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(620, 97, 113, 599, 100, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(621, 97, 113, 600, 200, 2, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(622, 97, 113, 601, 100, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(623, 98, 114, 603, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(624, 98, 114, 604, 200, 10, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(625, 98, 114, 605, 100, 20, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(626, 98, 114, 606, 100, 30, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(627, 99, 115, 613, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(628, 99, 115, 614, 100, 20, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(629, 99, 115, 615, 200, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(631, 100, 116, 617, 50, 10, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(632, 100, 116, 618, 200, 20, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(633, 100, 116, 620, 100, 30, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(634, 101, 117, 622, 50, 10, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(635, 101, 117, 624, 200, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(636, 101, 117, 623, 200, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(637, 102, 118, 625, 50, 10, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(638, 102, 118, 626, 200, 20, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(639, 103, 119, 627, 50, 10, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(640, 103, 119, 628, 200, 20, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(641, 6, 8, 629, 100, 40, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(642, 60, 90, 289, 0, 0, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, 2, NULL, NULL, NULL, NULL),
(643, 60, 90, 546, 0, 0, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, 4, NULL, NULL, NULL, NULL),
(644, 60, 90, 288, 0, 0, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, 5, NULL, NULL, NULL, NULL),
(645, 7, 7, 611, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(657, 106, 122, 642, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(658, 106, 122, 643, 200, 10, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(659, 107, 123, 644, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(660, 107, 123, 645, 200, 10, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(661, 108, 124, 646, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(662, 108, 124, 647, 100, 10, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(663, 108, 124, 648, 100, 20, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(664, 108, 124, 649, 300, 30, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(665, 108, 124, 650, 200, 40, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(666, 108, 124, 651, 100, 50, 'left', 0, NULL, NULL, NULL, 0, 2, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(667, 108, 124, 652, 100, 70, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(668, 109, 125, 654, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(669, 109, 125, 655, 200, 10, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(670, 110, 126, 656, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(671, 110, 126, 661, 100, 10, 'left', 0, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(672, 110, 126, 665, 200, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(673, 110, 126, 658, 200, 15, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(674, 110, 126, 657, 100, 5, 'left', 0, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(675, 110, 126, 662, 50, 12, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(676, 110, 126, 664, 50, 13, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(685, 113, 129, 679, 50, 10, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(686, 113, 129, 680, 200, 20, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(687, 114, 130, 685, 50, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(688, 114, 130, 686, 200, 20, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(689, 114, 130, 687, 100, 30, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(690, 108, 124, 689, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(691, 108, 131, 646, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(692, 108, 131, 650, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(693, 108, 131, 652, 100, 50, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(694, 108, 131, 651, 120, 40, 'left', 0, NULL, NULL, NULL, 0, 2, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(695, 108, 131, 689, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(696, 108, 131, 690, 120, 15, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(697, 108, 131, 691, 120, 45, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(698, 115, 132, 692, 50, 5, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(699, 115, 132, 693, 300, 10, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(700, 115, 132, 694, 200, 20, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(701, 115, 132, 695, 100, 30, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(702, 108, 131, 696, 300, 60, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(704, 110, 126, 660, 100, 30, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(711, 7, 7, 20, 100, -5, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(720, 110, 126, 712, 100, 26, 'left', 0, 'Izpildītāja veids', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(721, 108, 135, 646, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(722, 108, 135, 689, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(723, 108, 135, 647, 100, 10, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(724, 108, 135, 648, 100, 20, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(725, 108, 135, 649, 200, 30, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(726, 108, 135, 650, 100, 0, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(727, 108, 135, 651, 120, 50, 'left', 0, NULL, NULL, NULL, 0, 2, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(728, 108, 135, 691, 120, 60, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(729, 108, 135, 652, 100, 70, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(730, 108, 135, 696, 300, 90, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(752, 119, 139, 725, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(753, 119, 139, 726, 200, 10, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(754, 119, 139, 727, 50, 20, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(755, 120, 140, 729, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(756, 120, 140, 735, 50, 10, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(757, 120, 140, 731, 200, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(758, 120, 140, 732, 300, 30, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(759, 121, 141, 738, 0, 0, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(760, 121, 141, 739, 0, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(761, 121, 141, 744, 0, 2, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(762, 121, 141, 741, 0, 3, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(763, 121, 141, 750, 0, 4, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(764, 121, 142, 738, 0, 0, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(765, 121, 142, 739, 0, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(766, 121, 142, 744, 0, 2, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(767, 121, 142, 741, 0, 3, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(768, 121, 142, 750, 0, 4, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(769, 100, 116, 621, 0, 40, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(770, 121, 142, 746, 0, 0, 'left', 0, NULL, 7, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(771, 100, 116, 753, 0, 50, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(796, 127, 147, 813, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(797, 127, 147, 815, 0, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(798, 127, 147, 814, 0, 2, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(839, 120, 140, 734, 100, 60, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(840, 60, 66, 728, 100, 80, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(841, 126, 156, 810, 50, 10, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(842, 126, 156, 811, 200, 20, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(843, 60, 66, 598, 50, 40, 'left', 0, 'Ir aktīvā', NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(844, 136, 157, 876, 0, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(845, 136, 157, 877, 0, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(846, 136, 157, 878, 0, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(847, 136, 157, 879, 0, 40, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(848, 137, 158, 880, 0, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(849, 137, 158, 881, 0, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(850, 137, 158, 882, 0, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(851, 137, 158, 883, 0, 40, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(852, 137, 158, 884, 0, 50, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(853, 137, 158, 885, 0, 60, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1008, 188, 196, 1164, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1009, 188, 196, 1165, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1010, 188, 196, 1166, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1011, 188, 196, 1167, 100, 40, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1012, 188, 196, 1168, 100, 50, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1014, 188, 196, 1170, 100, 70, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1015, 189, 197, 1172, 50, 1, 'left', 1, NULL, NULL, NULL, 0, 2, 0, NULL, NULL, NULL, NULL, 1, '2016-03-18 17:56:09'),
(1016, 189, 197, 1173, 300, 2, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1017, 189, 197, 1177, 50, 7, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1018, 189, 197, 1178, 100, 4, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1019, 189, 197, 1179, 100, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1021, 189, 197, 1181, 100, 80, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1030, 189, 197, 1182, 0, 0, 'left', 0, NULL, 1, '''Attēli''', 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1036, 191, 200, 1188, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1037, 191, 200, 1189, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1038, 191, 200, 1190, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1040, 191, 200, 1192, 100, 50, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1042, 5, 12, 259, 100, 10, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1054, 193, 202, 1206, 100, 10, 'left', 1, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1055, 193, 202, 1207, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1056, 193, 202, 1208, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1057, 193, 202, 1209, 100, 40, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1065, 120, 140, 904, 0, 0, 'left', 0, NULL, 1, '''Nē''', 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1066, 194, 203, 1217, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1067, 194, 203, 1219, 200, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1068, 194, 203, 1220, 300, 30, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1069, 194, 203, 1226, 100, 60, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1070, 194, 203, 1222, 50, 10, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1071, 194, 203, 1225, 0, 0, 'left', 0, NULL, 1, '''Jā''', 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1072, 195, 204, 1229, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1073, 195, 204, 1230, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1074, 195, 204, 1231, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1075, 195, 204, 1232, 100, 40, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1076, 195, 204, 1233, 100, 50, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1077, 195, 204, 1234, 100, 60, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1083, 198, 207, 1240, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1084, 198, 207, 1241, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1085, 198, 207, 1242, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1086, 198, 207, 1243, 100, 40, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1087, 198, 207, 1244, 100, 50, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1088, 198, 207, 1245, 100, 60, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1089, 198, 207, 1246, 100, 70, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1091, 198, 207, 1248, 100, 90, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1094, 200, 209, 1251, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1095, 200, 209, 1252, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1096, 200, 209, 1253, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1099, 202, 211, 1256, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1100, 202, 211, 1257, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1101, 202, 211, 1258, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1102, 198, 211, 1241, 200, 10, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1109, 205, 214, 1265, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1110, 205, 214, 1266, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1111, 205, 214, 1267, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1113, 205, 214, 1269, 100, 50, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1114, 205, 214, 1270, 100, 60, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1115, 208, 217, 1287, 50, 1, 'left', 1, NULL, NULL, NULL, 0, 2, 0, NULL, NULL, NULL, NULL, 1, '2016-03-18 17:56:36'),
(1116, 208, 217, 1288, 300, 2, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1117, 208, 217, 1290, 50, 7, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1118, 208, 217, 1291, 100, 4, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1119, 208, 217, 1292, 100, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1120, 208, 217, 1293, 100, 80, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1121, 208, 217, 1294, 0, 0, 'left', 0, NULL, 1, '''Video''', 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1122, 205, 214, 1296, 200, 25, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1123, 209, 218, 1297, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1124, 209, 218, 1298, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1125, 209, 218, 1299, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1130, 211, 220, 1304, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1131, 211, 220, 1305, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1132, 211, 220, 1306, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1133, 211, 220, 1307, 100, 40, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1134, 211, 220, 1308, 100, 50, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1135, 212, 221, 1309, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1136, 212, 221, 1310, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1137, 214, 223, 1311, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1138, 214, 223, 1312, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1139, 214, 223, 1313, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1140, 214, 223, 1314, 100, 40, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1141, 214, 223, 1315, 100, 50, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1143, 214, 223, 1317, 100, 70, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1145, 214, 223, 1319, 100, 90, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1146, 215, 224, 1323, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1147, 215, 224, 1324, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1148, 216, 225, 1325, 100, 10, 'left', 1, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1149, 216, 225, 1326, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1150, 216, 225, 1327, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1151, 216, 225, 1328, 100, 40, 'left', 1, NULL, NULL, NULL, 0, 2, 0, NULL, NULL, NULL, NULL, 1, '2016-03-20 15:51:27'),
(1152, 216, 225, 1329, 100, 50, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1153, 216, 225, 1330, 100, 60, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1159, 222, 231, 1338, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1160, 222, 231, 1339, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1161, 222, 231, 1340, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1162, 222, 231, 1341, 100, 40, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1163, 222, 231, 1342, 100, 50, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1170, 223, 232, 1349, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1171, 223, 232, 1350, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1173, 223, 232, 1352, 100, 40, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1174, 223, 232, 1353, 100, 50, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1175, 224, 233, 1355, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1176, 224, 233, 1356, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1177, 224, 233, 1357, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1184, 224, 233, 1364, 100, 100, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1185, 224, 233, 1365, 100, 110, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1186, 225, 234, 1366, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1187, 225, 234, 1367, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1188, 225, 234, 1368, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1189, 225, 234, 1369, 100, 40, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1190, 225, 234, 1370, 100, 50, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1191, 225, 234, 1371, 100, 60, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1192, 225, 234, 1372, 100, 70, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1193, 226, 235, 1373, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1194, 226, 235, 1374, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1198, 226, 235, 1378, 100, 60, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1199, 226, 235, 1379, 100, 70, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1200, 227, 236, 1380, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1201, 227, 236, 1381, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1202, 227, 236, 1382, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1203, 227, 236, 1383, 100, 40, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1204, 227, 236, 1384, 100, 50, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1205, 227, 236, 1385, 100, 60, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1206, 229, 238, 1386, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1207, 229, 238, 1387, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1208, 229, 238, 1388, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1209, 229, 238, 1389, 100, 40, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1210, 229, 238, 1390, 100, 50, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1211, 60, 66, 1171, 50, 0, 'left', 0, NULL, 1, '''Ziņas''', 1, NULL, 0, NULL, NULL, 1, '2016-03-07 15:03:58', 1, '2016-03-07 15:03:58'),
(1212, 231, 240, 1391, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1213, 231, 240, 1392, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1214, 231, 240, 1393, 100, 30, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, 1, '2016-03-08 21:29:09'),
(1215, 3, 1, 1394, 200, 50, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, '2016-03-08 21:30:28', 1, '2016-03-08 21:30:28'),
(1216, 232, 241, 1400, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1217, 232, 241, 1401, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1218, 232, 241, 1402, 100, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1220, 232, 241, 1404, 100, 50, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1245, 233, 246, 1409, 100, 10, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1246, 233, 246, 1410, 100, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1247, 60, 247, 286, 50, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, '2016-03-15 18:34:33', NULL, NULL),
(1248, 60, 247, 287, 300, 2, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, '2016-03-15 18:34:33', NULL, NULL),
(1250, 60, 247, 546, 100, 4, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, '2016-03-15 18:34:33', NULL, NULL),
(1251, 60, 247, 547, 100, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, '2016-03-15 18:34:33', NULL, NULL),
(1255, 60, 247, 1398, 100, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, 1, '2016-03-15 18:35:43', 1, '2016-03-15 18:35:43'),
(1256, 60, 247, 1399, 100, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, 1, '2016-03-15 18:36:04', 1, '2016-03-15 18:36:04'),
(1257, 60, 247, 1171, 0, 0, 'left', 0, NULL, 1, '''Ziņas''', 1, NULL, 0, NULL, NULL, 1, '2016-03-15 18:42:25', 1, '2016-03-15 18:43:16'),
(1274, 234, 250, 1415, 50, 1, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, '2016-03-15 18:34:33', NULL, NULL),
(1275, 234, 250, 1416, 300, 2, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, '2016-03-15 18:34:33', NULL, NULL),
(1276, 234, 250, 1421, 100, 4, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, '2016-03-15 18:34:33', NULL, NULL),
(1277, 234, 250, 1422, 100, 3, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, '2016-03-15 18:34:33', NULL, NULL),
(1278, 234, 250, 1430, 0, 0, 'left', 0, NULL, 1, '''Ziņas''', 1, NULL, 0, NULL, NULL, 1, '2016-03-15 18:42:25', 1, '2016-03-15 18:43:16'),
(1279, 234, 250, 1432, 100, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, 1, '2016-03-15 18:35:43', 1, '2016-03-15 18:35:43'),
(1280, 234, 250, 1431, 100, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, 1, '2016-03-15 18:36:04', 1, '2016-03-15 18:36:04'),
(1281, 235, 251, 1435, 0, 0, 'left', 0, NULL, NULL, NULL, 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1282, 235, 251, 1437, 200, 20, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1283, 235, 251, 1438, 300, 30, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1284, 235, 251, 1440, 50, 10, 'left', 1, NULL, NULL, NULL, 0, 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1285, 235, 251, 1443, 0, 0, 'left', 0, NULL, 1, '''Jā''', 1, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1286, 235, 251, 1444, 100, 60, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(1287, 20, 22, 94, 200, 30, 'left', 1, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, '2016-03-20 15:14:26', 1, '2016-03-20 15:14:26'),
(1288, 23, 25, 428, 100, 10, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, '2016-03-20 15:29:06', 1, '2016-03-20 15:29:06'),
(1289, 23, 25, 427, 100, 30, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, '2016-03-20 15:29:24', 1, '2016-03-20 15:29:24'),
(1290, 6, 8, 197, 100, 50, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, '2016-03-20 16:58:57', 1, '2016-03-20 16:58:57'),
(1291, 6, 8, 196, 100, 60, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, '2016-03-20 16:59:11', 1, '2016-03-20 16:59:11'),
(1292, 6, 8, 31, 100, 80, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, '2016-03-20 16:59:29', 1, '2016-03-20 16:59:29'),
(1297, 21, 23, 1434, 100, 100, 'left', 0, NULL, NULL, NULL, 0, NULL, 0, NULL, NULL, 1, '2016-03-20 17:45:20', 1, '2016-03-20 17:45:20');

-- --------------------------------------------------------

--
-- Table structure for table `dx_views_types`
--

CREATE TABLE IF NOT EXISTS `dx_views_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `dx_views_types`
--

INSERT INTO `dx_views_types` (`id`, `title`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Tabula', NULL, NULL, NULL, NULL),
(9, 'Tabula ar SQL', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `dx_workflows`
--

CREATE TABLE IF NOT EXISTS `dx_workflows` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `step_title` varchar(200) DEFAULT NULL,
  `list_id` int(11) DEFAULT NULL,
  `task_type_id` int(11) DEFAULT NULL,
  `task_perform_id` int(11) DEFAULT NULL,
  `employee_id` int(11) DEFAULT NULL,
  `step_nr` int(11) DEFAULT NULL,
  `yes_step_nr` int(11) DEFAULT NULL,
  `no_step_nr` int(11) DEFAULT NULL,
  `notes` varchar(4000) DEFAULT NULL,
  `term_days` int(11) NOT NULL DEFAULT '1',
  `field_id` int(11) DEFAULT NULL,
  `field_value` varchar(2000) DEFAULT NULL,
  `field_operation_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `list_id` (`list_id`,`task_type_id`,`task_perform_id`,`employee_id`,`step_nr`),
  KEY `yes_step_id` (`yes_step_nr`),
  KEY `no_step_id` (`no_step_nr`),
  KEY `field_id` (`field_id`),
  KEY `field_operation_id` (`field_operation_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `dx_workflows_fields`
--

CREATE TABLE IF NOT EXISTS `dx_workflows_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `workflow_id` int(11) DEFAULT NULL,
  `list_id` int(11) DEFAULT NULL,
  `field_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `workflow_id` (`workflow_id`,`list_id`,`field_id`),
  KEY `list_id` (`list_id`),
  KEY `field_id` (`field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `connection` text COLLATE utf8_unicode_ci NOT NULL,
  `queue` text COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_articles`
--

CREATE TABLE IF NOT EXISTS `in_articles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `intro_text` text COLLATE utf8_unicode_ci,
  `article_text` text COLLATE utf8_unicode_ci,
  `picture_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture_guid` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order_index` tinyint(4) NOT NULL DEFAULT '100',
  `is_active` tinyint(4) NOT NULL DEFAULT '1',
  `is_top_article` int(11) NOT NULL DEFAULT '0',
  `publish_time` datetime DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `type_id` int(11) NOT NULL DEFAULT '1',
  `is_static` tinyint(1) DEFAULT '0',
  `is_searchable` tinyint(1) NOT NULL DEFAULT '1',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  `video_galery_id` int(10) unsigned DEFAULT NULL COMMENT 'Video galerija',
  `picture_galery_id` int(10) unsigned DEFAULT NULL COMMENT 'Attēlu galerija',
  `author_id` int(10) unsigned DEFAULT NULL COMMENT 'Ziņas autors',
  `article_text_dx_clean` text COLLATE utf8_unicode_ci COMMENT 'Ziņas teksts bez HTML',
  `content_id` int(10) unsigned DEFAULT '1' COMMENT 'Ziņas veids',
  `alternate_url` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Ziņas norāde',
  `outer_url` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Ārējā saite',
  `dwon_file_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Lejuplādējamā datne',
  `dwon_file_guid` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Lejuplādējamās datnes GUID',
  PRIMARY KEY (`id`),
  UNIQUE KEY `in_articles_alternate_url_unique` (`alternate_url`),
  KEY `be_articles_order_index_index` (`order_index`),
  KEY `source_id` (`source_id`),
  KEY `in_articles_video_galery_id_index` (`video_galery_id`),
  KEY `in_articles_picture_galery_id_index` (`picture_galery_id`),
  KEY `in_articles_author_id_index` (`author_id`),
  KEY `in_articles_content_id_index` (`content_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=53 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_articles_contents`
--

CREATE TABLE IF NOT EXISTS `in_articles_contents` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nosaukums',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `in_articles_contents`
--

INSERT INTO `in_articles_contents` (`id`, `title`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Teksts', NULL, NULL, NULL, NULL),
(2, 'Ārējā saite', NULL, NULL, NULL, NULL),
(3, 'Datne', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `in_articles_files`
--

CREATE TABLE IF NOT EXISTS `in_articles_files` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(10) unsigned DEFAULT NULL COMMENT 'Ziņa',
  `title` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Datne',
  `file_guid` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Datnes unikālais GUID',
  `order_index` int(11) NOT NULL DEFAULT '0' COMMENT 'Secība',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `in_articles_files_order_index_index` (`order_index`),
  KEY `in_articles_files_article_id_index` (`article_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=13 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_articles_img`
--

CREATE TABLE IF NOT EXISTS `in_articles_img` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(11) unsigned DEFAULT NULL COMMENT 'Raksts',
  `file_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Attēls',
  `file_guid` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order_index` int(11) NOT NULL DEFAULT '0' COMMENT 'Secība',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=241 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_articles_vid`
--

CREATE TABLE IF NOT EXISTS `in_articles_vid` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(10) unsigned DEFAULT NULL COMMENT 'Galerija',
  `title` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nosaukums',
  `prev_file_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Attēla datne',
  `prev_file_guid` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Video datne',
  `file_guid` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `youtube_url` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'YouTube saite',
  `order_index` int(11) NOT NULL DEFAULT '0' COMMENT 'Secība',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `in_articles_vid_article_id_index` (`article_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_article_types`
--

CREATE TABLE IF NOT EXISTS `in_article_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Kods',
  `name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nosaukums',
  `picture_name` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Ikonas klase',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  `file_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Noklusējuma attēls',
  `file_guid` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `hover_hint` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Paskaidrojums',
  `is_for_galeries` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `in_article_types`
--

INSERT INTO `in_article_types` (`id`, `code`, `name`, `picture_name`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `file_name`, `file_guid`, `hover_hint`, `is_for_galeries`) VALUES
(1, 'text', 'Ziņas', '', NULL, NULL, NULL, NULL, 'article_placeholder.jpg', 'article_placeholder.jpg', 'Ziņa', 0),
(2, 'text', 'Personāla jaunumi', 'fa-users', NULL, NULL, NULL, NULL, 'personal_placeholder.jpg', 'personal_placeholder.jpg', 'Personāla ziņa', 0),
(3, 'img', 'Attēli', 'fa-picture-o', NULL, NULL, NULL, NULL, 'picture_placeholder.jpg', 'picture_placeholder.jpg', 'Attēlu galerija', 1),
(4, 'vid', 'Video', 'fa-video-camera', NULL, NULL, NULL, NULL, 'video_placeholder.jpg', 'video_placeholder.jpg', 'Video galerija', 1);

-- --------------------------------------------------------

--
-- Table structure for table `in_dailyquest_answers`
--

CREATE TABLE IF NOT EXISTS `in_dailyquest_answers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_ip` varchar(45) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Klienta IP adrese',
  `dailyquest_option_id` int(10) unsigned NOT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `in_dailyquest_answers_client_ip_index` (`client_ip`),
  KEY `in_dailyquest_answers_dailyquest_option_id_index` (`dailyquest_option_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_dailyquest_options`
--

CREATE TABLE IF NOT EXISTS `in_dailyquest_options` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `option_text` varchar(1000) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Atbilžu variants',
  `dailyquest_question_id` int(10) unsigned NOT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `in_dailyquest_options_dailyquest_question_id_index` (`dailyquest_question_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_dailyquest_questions`
--

CREATE TABLE IF NOT EXISTS `in_dailyquest_questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question` varchar(1000) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Jautājuma teksts',
  `is_active` tinyint(1) DEFAULT NULL COMMENT 'Ir aktīvs jautājums',
  `date_from` datetime DEFAULT NULL COMMENT 'Attēlošanas sākuma datums',
  `date_to` datetime DEFAULT NULL COMMENT 'Attēlošanas noslēguma datums',
  `is_multi_answer` tinyint(1) DEFAULT NULL COMMENT 'Ir iespējamas vairākas atbildes',
  `picture_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Jautājuma attēla nosaukums',
  `picture_guid` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Jautājuma attēla identifikators',
  `source_id` int(11) DEFAULT NULL COMMENT 'Datu avots',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `in_dailyquest_questions_source_id_index` (`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_departments`
--

CREATE TABLE IF NOT EXISTS `in_departments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source_id` int(11) DEFAULT NULL COMMENT 'Datu avots',
  `parent_id` int(10) unsigned DEFAULT NULL COMMENT 'Augstākā struktūrvienība',
  `title` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nosaukums',
  `code` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'HR kods',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `in_departments_code_unique` (`code`),
  KEY `in_departments_parent_id_index` (`parent_id`),
  KEY `in_departments_source_id_index` (`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

--
-- Dumping data for table `in_departments`
--

INSERT INTO `in_departments` (`id`, `source_id`, `parent_id`, `title`, `code`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 1, NULL, 'Finanšu departaments', NULL, NULL, NULL, NULL, NULL),
(2, 1, NULL, 'Juridiskais departaments', NULL, NULL, NULL, NULL, NULL),
(3, 1, NULL, 'Drošības departaments', NULL, NULL, NULL, NULL, NULL),
(4, 1, 1, 'Finanšu analīzes nodaļa', NULL, NULL, NULL, NULL, NULL),
(5, 1, 1, 'Finanšu audita nodaļa', NULL, NULL, NULL, NULL, NULL),
(6, 1, 1, 'Risku novēršanas nodaļa', NULL, NULL, NULL, NULL, NULL),
(7, 1, 2, 'Juridisko dokumentu nodaļa', NULL, NULL, NULL, NULL, NULL),
(8, 1, 2, 'Revīzijas nodaļa', NULL, NULL, NULL, NULL, NULL),
(9, 1, 2, 'Sekretariāts', NULL, NULL, NULL, NULL, NULL),
(10, 1, 3, 'Drošības inventāra nodaļa', NULL, NULL, NULL, NULL, NULL),
(11, 1, 3, 'Apsardzes dienests', NULL, NULL, NULL, NULL, NULL),
(12, 1, 3, 'Sistēmu monitoringa nodaļa', NULL, NULL, NULL, NULL, NULL),
(13, 1, 7, 'Līgumu daļa', NULL, NULL, NULL, NULL, NULL),
(18, 1, NULL, 'Rīgas pilsētas Tehnisko datu uzturēšanas nodaļa', '30EF85', NULL, NULL, NULL, NULL),
(19, 1, NULL, 'Dienvidrietumu elektroenerģijas uzskaites daļa', '30EU30', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `in_documents`
--

CREATE TABLE IF NOT EXISTS `in_documents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `doc_nr` varchar(100) DEFAULT NULL,
  `doc_date` date DEFAULT NULL,
  `doc_kind_id` int(11) DEFAULT NULL COMMENT 'Dokumenta veids',
  `doc_title` varchar(2000) DEFAULT NULL,
  `doc_system_id` int(11) DEFAULT NULL COMMENT 'Sistēma',
  `imported_time` datetime DEFAULT NULL COMMENT 'Importēšanas datums/laiks',
  `lotus_item_id` int(11) DEFAULT NULL COMMENT 'Lotus ieraksta ID',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  `unid` char(36) DEFAULT NULL COMMENT 'Dokumenta unikālais identifikators',
  `noteid` varchar(50) DEFAULT NULL,
  `siblings` int(11) DEFAULT NULL,
  `version` decimal(6,2) DEFAULT NULL COMMENT 'Versijas numurs',
  `doc_department_id` int(10) unsigned DEFAULT NULL COMMENT 'Nodaļa',
  PRIMARY KEY (`id`),
  KEY `doc_kind_id` (`doc_kind_id`),
  KEY `doc_system_id` (`doc_system_id`),
  KEY `lotus_item_id` (`lotus_item_id`),
  KEY `in_documents_doc_department_id_index` (`doc_department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Lotus Notes sistēmu dokumenti' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_documents_kind`
--

CREATE TABLE IF NOT EXISTS `in_documents_kind` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(500) DEFAULT NULL,
  `code` varchar(20) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `in_documents_kind`
--

INSERT INTO `in_documents_kind` (`id`, `title`, `code`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Normatīvais akts', NULL, NULL, NULL, NULL, NULL),
(2, 'Rīkojums', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `in_documents_lotus`
--

CREATE TABLE IF NOT EXISTS `in_documents_lotus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL COMMENT 'Sistēmas nosaukums',
  `json_url` varchar(1000) DEFAULT NULL COMMENT 'JSON pieprasījuma URL',
  `sys_color_class` varchar(20) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  `source_id` int(10) unsigned DEFAULT NULL COMMENT 'Nodaļa',
  PRIMARY KEY (`id`),
  KEY `in_documents_lotus_source_id_index` (`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Lotus Notes sistēmu klasifikators' AUTO_INCREMENT=3 ;

--
-- Dumping data for table `in_documents_lotus`
--

INSERT INTO `in_documents_lotus` (`id`, `title`, `json_url`, `sys_color_class`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `source_id`) VALUES
(1, 'Latvenergo DIVS', 'http://local.le/rest_test/#readviewentries#/#JSON#/#startCounter#/#download_count#', 'green-jungle', NULL, NULL, NULL, NULL, 1),
(2, 'Sadales Tīkls DIVS', 'http://local.le/rest_test/#readviewentries#/#JSON#/#startCounter#/#download_count#', 'blue-steel', NULL, NULL, NULL, NULL, 3);

-- --------------------------------------------------------

--
-- Table structure for table `in_doc_departments`
--

CREATE TABLE IF NOT EXISTS `in_doc_departments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nodaļas nosaukums',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_employees`
--

CREATE TABLE IF NOT EXISTS `in_employees` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_name` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  `code` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `office_address` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `left_from` date DEFAULT NULL,
  `left_to` date DEFAULT NULL,
  `left_reason_id` int(10) unsigned DEFAULT NULL,
  `substit_empl_id` int(10) unsigned DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mobile` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fax` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `source_id` int(11) DEFAULT NULL,
  `position` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `picture_guid` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `office_cabinet` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `department_id` int(10) unsigned DEFAULT NULL COMMENT 'Struktūrvienība',
  PRIMARY KEY (`id`),
  KEY `in_employees_birth_date_index` (`birth_date`),
  KEY `in_employees_employee_name_index` (`employee_name`),
  KEY `in_employees_code_index` (`code`),
  KEY `in_employees_manager_id_index` (`manager_id`),
  KEY `in_employees_left_from_index` (`left_from`),
  KEY `in_employees_left_to_index` (`left_to`),
  KEY `in_employees_left_reason_id_index` (`left_reason_id`),
  KEY `in_employees_substit_empl_id_index` (`substit_empl_id`),
  KEY `in_employees_source_id_index` (`source_id`),
  KEY `in_employees_department_id_index` (`department_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_employees_history`
--

CREATE TABLE IF NOT EXISTS `in_employees_history` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `employee_id` int(11) DEFAULT NULL,
  `old_source_id` int(11) DEFAULT NULL,
  `new_source_id` int(11) DEFAULT NULL,
  `old_position` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `new_position` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `old_department` varchar(4000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `new_department` varchar(4000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  `old_department_id` int(10) unsigned DEFAULT NULL COMMENT 'Iepriekšējais departaments',
  `new_department_id` int(10) unsigned DEFAULT NULL COMMENT 'Jaunais departaments',
  PRIMARY KEY (`id`),
  KEY `in_employees_history_employee_id_index` (`employee_id`),
  KEY `in_employees_history_old_source_id_index` (`old_source_id`),
  KEY `in_employees_history_new_source_id_index` (`new_source_id`),
  KEY `in_employees_history_valid_from_index` (`valid_from`),
  KEY `in_employees_history_old_department_id_index` (`old_department_id`),
  KEY `in_employees_history_new_department_id_index` (`new_department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_employee_changes`
--

CREATE TABLE IF NOT EXISTS `in_employee_changes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `old_position` varchar(500) DEFAULT NULL,
  `new_position` varchar(500) DEFAULT NULL,
  `old_department` varchar(2000) DEFAULT NULL,
  `new_department` varchar(2000) DEFAULT NULL,
  `valid_from` date DEFAULT NULL,
  `valid_to` date DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_events`
--

CREATE TABLE IF NOT EXISTS `in_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` text,
  `picture_name` varchar(500) DEFAULT NULL,
  `picture_guid` varchar(100) DEFAULT NULL,
  `event_time_from` datetime DEFAULT NULL,
  `event_time_to` datetime DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `is_active` int(11) NOT NULL DEFAULT '0',
  `source_id` int(11) DEFAULT NULL COMMENT 'Datu avots',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `in_events_source_id_index` (`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_faq_question`
--

CREATE TABLE IF NOT EXISTS `in_faq_question` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) DEFAULT NULL COMMENT 'Ir aktīvs jautājums',
  `question` varchar(2000) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Jautājuma teksts',
  `answer` varchar(4000) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Atbilde',
  `faq_section_id` int(10) unsigned NOT NULL COMMENT 'Nodaļa',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `in_faq_question_faq_section_id_index` (`faq_section_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_faq_section`
--

CREATE TABLE IF NOT EXISTS `in_faq_section` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_active` tinyint(1) DEFAULT NULL COMMENT 'Ir aktīva nodaļa',
  `section_name` varchar(1000) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Nodaļas nosaukums',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_faq_section_source`
--

CREATE TABLE IF NOT EXISTS `in_faq_section_source` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `faq_section_id` int(10) unsigned NOT NULL COMMENT 'Nodaļas identifikators',
  `source_id` int(11) DEFAULT NULL COMMENT 'Datu avots',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `in_faq_section_source_source_id_index` (`source_id`),
  KEY `in_faq_section_source_faq_section_id_index` (`faq_section_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_html`
--

CREATE TABLE IF NOT EXISTS `in_html` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Bloka nosaukums',
  `block_title` varchar(200) DEFAULT NULL COMMENT 'Bloka virsraksts portālā',
  `title` varchar(200) DEFAULT NULL COMMENT 'Bloka nosaukums',
  `comments` varchar(2000) DEFAULT NULL COMMENT 'Paskaidrojums',
  `html` text COMMENT 'HTML',
  `source_id` int(11) DEFAULT NULL COMMENT 'Datu avots',
  `code` varchar(50) DEFAULT NULL COMMENT 'Kods',
  `is_active` int(11) DEFAULT '0' COMMENT 'Ir aktīvs',
  `is_border_0` int(11) DEFAULT '0' COMMENT 'Ir rāmis',
  `is_redactor` tinyint(1) DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `in_html_uniq` (`source_id`,`code`) COMMENT 'Nodrošina kodu unikalitāti',
  KEY `code` (`code`),
  KEY `is_active` (`is_active`,`is_border_0`),
  KEY `source_id` (`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Dumping data for table `in_html`
--

INSERT INTO `in_html` (`id`, `block_title`, `title`, `comments`, `html`, `source_id`, `code`, `is_active`, `is_border_0`, `is_redactor`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(8, 'Labumi darbiniekiem', 'Labumu sistēmas saišu bloks', NULL, '<p><a href="http://www.latvenergo.lv" target="_blank">Veselības apdro&scaron;inā&scaron;ana</a></p>\r\n<p><a href="http://www.latvenergo.lv" target="_blank">Labumu izvēles sistēma</a></p>\r\n<p><a href="http://www.latvenergo.lv" target="_blank">Piedāvājumi darbiniekiem</a></p>', NULL, 'LABUMI', 1, 0, 1, NULL, NULL, NULL, NULL),
(9, 'Darba piedāvājumi', 'Darba piedāvājumi', NULL, '<p><a href="http://www.latvenergo.lv">Elektromontieris (Ludza)</a></p>\r\n<p><a href="http://www.latvenergo.lv">Projektu vadītājs (Rīga)</a></p>\r\n<p><a href="http://www.latvenergo.lv">Tehniskās informācijas tehniķis (Liepāja)</a></p>', 4, 'DARBAPIED', 1, 0, 1, NULL, NULL, NULL, NULL),
(13, NULL, 'Personāla izmaiņu saišu bloks', NULL, '<a href="/darbinieku_izmainas">Personāla izmaiņas</a>', 4, 'IZMAINAS', 1, 0, 1, NULL, NULL, NULL, NULL),
(14, 'Nozares ziņas', 'Nozares ziņas', NULL, '<p><a href="http://www.latvenergo.lv" target="_blank">Mediju monitorings</a></p>\r\n<p><a href="http://www.latvenergo.lv" target="_blank">Nozare.lv</a></p>\r\n<p><a href="http://www.latvenergo.lv" target="_blank">Eiropas Savienības aktualitā&scaron;u apskats</a></p>', NULL, 'NOZAR', 1, 0, 1, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `in_incidents`
--

CREATE TABLE IF NOT EXISTS `in_incidents` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `created_time` datetime DEFAULT NULL,
  `is_crash` int(11) NOT NULL DEFAULT '0',
  `details` varchar(4000) DEFAULT NULL,
  `solved_time` datetime DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  `planned_resolve_time` datetime DEFAULT NULL COMMENT 'Plānotais novēršanas datums un laiks',
  `system_id` int(10) unsigned NOT NULL COMMENT 'Sistēma',
  PRIMARY KEY (`id`),
  KEY `created_time` (`created_time`,`is_crash`,`solved_time`),
  KEY `in_incidents_system_id_index` (`system_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_left_reasons`
--

CREATE TABLE IF NOT EXISTS `in_left_reasons` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `created_user_id` int(10) unsigned DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(10) unsigned DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `in_left_reasons`
--

INSERT INTO `in_left_reasons` (`id`, `title`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(3, 'Komandējumā', NULL, NULL, NULL, NULL),
(4, 'Atvaļinājumā', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `in_portal_log`
--

CREATE TABLE IF NOT EXISTS `in_portal_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `log_time` datetime DEFAULT NULL,
  `url` varchar(4000) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_processes`
--

CREATE TABLE IF NOT EXISTS `in_processes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Nosaukums',
  `code` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Kods',
  `url` varchar(250) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Piekļuves adrese',
  `user_name` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Piekļuves lietotāja vārds',
  `password` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Piekļuves parole',
  `schedule_from` tinyint(4) NOT NULL COMMENT 'Strādā no (stundas)',
  `schedule_to` tinyint(4) NOT NULL COMMENT 'Strādā līdz (stundas)',
  `schedule_every_minutes` int(11) NOT NULL COMMENT 'Izpilda ik pēc noteiktajām minūtēm',
  `employee_id` int(10) unsigned DEFAULT NULL COMMENT 'Atbildīgais darbinieks',
  `last_executed_time` datetime DEFAULT NULL COMMENT 'Pēdējais procesa izpildes laiks',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  `arguments` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `get_method` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `in_processes_employee_id_index` (`employee_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `in_processes`
--

INSERT INTO `in_processes` (`id`, `name`, `code`, `url`, `user_name`, `password`, `schedule_from`, `schedule_to`, `schedule_every_minutes`, `employee_id`, `last_executed_time`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`, `arguments`, `get_method`) VALUES
(1, 'Darbinieku sinhronizēšana', 'EMPLOYEE', 'test', 'test', 'test', 7, 22, 5, NULL, '2016-02-22 17:57:24', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'Dokumentu sinhronizēšana', 'LOTUSNOTES', '', NULL, NULL, 1, 2, 65, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `in_processes_log`
--

CREATE TABLE IF NOT EXISTS `in_processes_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `is_success` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Ir veiksmīga',
  `register_time` datetime DEFAULT NULL COMMENT 'Reģistrācijas laiks',
  `start_time` datetime DEFAULT NULL COMMENT 'Procesa sākums',
  `end_time` datetime DEFAULT NULL COMMENT 'Procesa beigas',
  `message` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Paziņojums',
  `process_id` int(10) unsigned NOT NULL COMMENT 'Saistītais process',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `in_processes_log_process_id_index` (`process_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_publish`
--

CREATE TABLE IF NOT EXISTS `in_publish` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `publish_type_id` int(10) unsigned DEFAULT NULL COMMENT 'Tips',
  `nr` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Izdevuma numurs',
  `pub_date` date DEFAULT NULL COMMENT 'Izdevuma datums',
  `prev_file_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Attēla datne',
  `prev_file_guid` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'PDF datne',
  `file_guid` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `order_index` int(11) NOT NULL DEFAULT '0' COMMENT 'Secība',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `in_publish_publish_type_id_index` (`publish_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_publish_types`
--

CREATE TABLE IF NOT EXISTS `in_publish_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Nosaukums',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_questions`
--

CREATE TABLE IF NOT EXISTS `in_questions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `source_id` int(11) DEFAULT NULL,
  `question` text COLLATE utf8_unicode_ci,
  `email` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asked_time` datetime DEFAULT NULL,
  `answer_time` datetime DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `in_questions_source_id_index` (`source_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=28 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_saints_days`
--

CREATE TABLE IF NOT EXISTS `in_saints_days` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `month` int(11) NOT NULL,
  `day` int(11) NOT NULL,
  `txt` varchar(200) NOT NULL,
  `spec_day` varchar(500) DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `month` (`month`,`day`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=367 ;

--
-- Dumping data for table `in_saints_days`
--

INSERT INTO `in_saints_days` (`id`, `month`, `day`, `txt`, `spec_day`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 1, 1, 'Laimnesis, Solvita, Solvija', NULL, NULL, NULL, NULL, NULL),
(2, 1, 2, 'Indulis, Iva, Ivis, Ivo', NULL, NULL, NULL, NULL, NULL),
(3, 1, 3, 'Miervalda, Miervaldis, Ringolds', NULL, NULL, NULL, NULL, NULL),
(4, 1, 4, 'Spodra, Ilva, Ilvita', NULL, NULL, NULL, NULL, NULL),
(5, 1, 5, 'Sīmanis, Zintis', NULL, NULL, NULL, NULL, NULL),
(6, 1, 6, 'Spulga, Arnita', NULL, NULL, NULL, NULL, NULL),
(7, 1, 7, 'Rota, Zigmārs, Digmārs, Juliāns', NULL, NULL, NULL, NULL, NULL),
(8, 1, 8, 'Gatis, Ivanda', NULL, NULL, NULL, NULL, NULL),
(9, 1, 9, 'Kaspars, Aksels, Alta', NULL, NULL, NULL, NULL, NULL),
(10, 1, 10, 'Tatjana, Dorisa', NULL, NULL, NULL, NULL, NULL),
(11, 1, 11, 'Smaida, Franciska', NULL, NULL, NULL, NULL, NULL),
(12, 1, 12, 'Reinis, Reina, Reinholds, Renāts', NULL, NULL, NULL, NULL, NULL),
(13, 1, 13, 'Harijs, Ārijs, Āris, Aira', NULL, NULL, NULL, NULL, NULL),
(14, 1, 14, 'Roberta, Roberts, Raitis, Raits', NULL, NULL, NULL, NULL, NULL),
(15, 1, 15, 'Fēlikss, Felicita', NULL, NULL, NULL, NULL, NULL),
(16, 1, 16, 'Lidija, Lida', NULL, NULL, NULL, NULL, NULL),
(17, 1, 17, 'Tenis, Dravis', NULL, NULL, NULL, NULL, NULL),
(18, 1, 18, 'Antons, Antis, Antonijs', NULL, NULL, NULL, NULL, NULL),
(19, 1, 19, 'Andulis, Alnis', NULL, NULL, NULL, NULL, NULL),
(20, 1, 20, 'Oļģerts, Aļģirds, Aļģis, Orests', NULL, NULL, NULL, NULL, NULL),
(21, 1, 21, 'Agnese, Agnija, Agne', NULL, NULL, NULL, NULL, NULL),
(22, 1, 22, 'Austris', NULL, NULL, NULL, NULL, NULL),
(23, 1, 23, 'Grieta, Strauta, Rebeka', NULL, NULL, NULL, NULL, NULL),
(24, 1, 24, 'Krišs, Ksenija, Eglons, Egle', NULL, NULL, NULL, NULL, NULL),
(25, 1, 25, 'Zigurds, Sigurds, Sigvards', NULL, NULL, NULL, NULL, NULL),
(26, 1, 26, 'Ansis, Agnis, Agneta', NULL, NULL, NULL, NULL, NULL),
(27, 1, 27, 'Ilze, Ildze, Izolde', NULL, NULL, NULL, NULL, NULL),
(28, 1, 28, 'Kārlis, Spodris', NULL, NULL, NULL, NULL, NULL),
(29, 1, 29, 'Aivars, Valērijs', NULL, NULL, NULL, NULL, NULL),
(30, 1, 30, 'Tīna, Valentīna, Pārsla', NULL, NULL, NULL, NULL, NULL),
(31, 1, 31, 'Tekla, Violeta', NULL, NULL, NULL, NULL, NULL),
(32, 2, 1, 'Brigita, Indra, Indris, Indars', NULL, NULL, NULL, NULL, NULL),
(33, 2, 2, 'Spīdola, Sonora', NULL, NULL, NULL, NULL, NULL),
(34, 2, 3, 'Aīda, Vida, Ida', NULL, NULL, NULL, NULL, NULL),
(35, 2, 4, 'Daila, Veronika, Dominiks', NULL, NULL, NULL, NULL, NULL),
(36, 2, 5, 'Agate, Selga, Silga, Sinilga', NULL, NULL, NULL, NULL, NULL),
(37, 2, 6, 'Dārta, Dace, Dora', NULL, NULL, NULL, NULL, NULL),
(38, 2, 7, 'Nelda, Rihards, Ričards, Rišards', NULL, NULL, NULL, NULL, NULL),
(39, 2, 8, 'Aldona, Česlavs', NULL, NULL, NULL, NULL, NULL),
(40, 2, 9, 'Simona, Apolonija', NULL, NULL, NULL, NULL, NULL),
(41, 2, 10, 'Paulīne, Paula, Jasmīna', NULL, NULL, NULL, NULL, NULL),
(42, 2, 11, 'Laima, Laimdota', NULL, NULL, NULL, NULL, NULL),
(43, 2, 12, 'Karlīna, Līna', NULL, NULL, NULL, NULL, NULL),
(44, 2, 13, 'Malda, Melita', NULL, NULL, NULL, NULL, NULL),
(45, 2, 14, 'Valentīns', NULL, NULL, NULL, NULL, NULL),
(46, 2, 15, 'Alvils, Olafs, Olavs, Aloizs', NULL, NULL, NULL, NULL, NULL),
(47, 2, 16, 'Jūlija, Džuljeta', NULL, NULL, NULL, NULL, NULL),
(48, 2, 17, 'Donats, Konstance', NULL, NULL, NULL, NULL, NULL),
(49, 2, 18, 'Kora, Kintija', NULL, NULL, NULL, NULL, NULL),
(50, 2, 19, 'Zane, Zuzanna', NULL, NULL, NULL, NULL, NULL),
(51, 2, 20, 'Vitauts, Smuidra, Smuidris', NULL, NULL, NULL, NULL, NULL),
(52, 2, 21, 'Eleonora, Ariadne', NULL, NULL, NULL, NULL, NULL),
(53, 2, 22, 'Ārija, Rigonda, Adrians, Adriāna, Adrija', NULL, NULL, NULL, NULL, NULL),
(54, 2, 23, 'Haralds, Almants', NULL, NULL, NULL, 1, '2016-02-23 15:17:55'),
(55, 2, 24, 'Diāna, Dina, Dins', NULL, NULL, NULL, NULL, NULL),
(56, 2, 25, 'Alma, Annemarija', NULL, NULL, NULL, NULL, NULL),
(57, 2, 26, 'Evelīna, Mētra, Aurēlija', NULL, NULL, NULL, NULL, NULL),
(58, 2, 27, 'Līvija, Līva, Andra', NULL, NULL, NULL, NULL, NULL),
(59, 2, 28, 'Skaidrīte, Skaidra, Justs', NULL, NULL, NULL, NULL, NULL),
(60, 2, 29, '', NULL, NULL, NULL, NULL, NULL),
(61, 3, 1, 'Ivars, Ilgvars', NULL, NULL, NULL, NULL, NULL),
(62, 3, 2, 'Lavīze, Luīze, Laila', NULL, NULL, NULL, NULL, NULL),
(63, 3, 3, 'Tālis, Tālavs, Marts', NULL, NULL, NULL, NULL, NULL),
(64, 3, 4, 'Alise, Auce, Enija', NULL, NULL, NULL, NULL, NULL),
(65, 3, 5, 'Austra, Aurora', NULL, NULL, NULL, NULL, NULL),
(66, 3, 6, 'Vents, Centis, Gotfrīds', NULL, NULL, NULL, NULL, NULL),
(67, 3, 7, 'Ella, Elmīra', NULL, NULL, NULL, NULL, NULL),
(68, 3, 8, 'Dagmāra, Marga, Margita', NULL, NULL, NULL, NULL, NULL),
(69, 3, 9, 'Ēvalds', NULL, NULL, NULL, NULL, NULL),
(70, 3, 10, 'Silvija, Laimrota, Liliāna', NULL, NULL, NULL, NULL, NULL),
(71, 3, 11, 'Konstantīns, Agita', NULL, NULL, NULL, NULL, NULL),
(72, 3, 12, 'Aija, Aiva, Aivis', NULL, NULL, NULL, NULL, NULL),
(73, 3, 13, 'Ernests, Balvis', NULL, NULL, NULL, NULL, NULL),
(74, 3, 14, 'Matilde, Ulrika', NULL, NULL, NULL, NULL, NULL),
(75, 3, 15, 'Amilda, Amalda, Imalda', NULL, NULL, NULL, NULL, NULL),
(76, 3, 16, 'Guntis, Guntars, Guntris', NULL, NULL, NULL, NULL, NULL),
(77, 3, 17, 'Ģertrūde, Gerda', NULL, NULL, NULL, NULL, NULL),
(78, 3, 18, 'Ilona, Adelīna', NULL, NULL, NULL, NULL, NULL),
(79, 3, 19, 'Jāzeps, Juzefa', NULL, NULL, NULL, NULL, NULL),
(80, 3, 20, 'Made, Irbe', NULL, NULL, NULL, NULL, NULL),
(81, 3, 21, 'Una, Unigunde, Dzelme, Benedikts, Benedikta', NULL, NULL, NULL, NULL, NULL),
(82, 3, 22, 'Tamāra, Dziedra, Gabriela, Gabriels', NULL, NULL, NULL, NULL, NULL),
(83, 3, 23, 'Mirdza, Žanete, Žanna', NULL, NULL, NULL, NULL, NULL),
(84, 3, 24, 'Kazimirs, Izidors', NULL, NULL, NULL, NULL, NULL),
(85, 3, 25, 'Māra, Mārīte, Mare, Marita', NULL, NULL, NULL, NULL, NULL),
(86, 3, 26, 'Eiženija, Ženija', NULL, NULL, NULL, NULL, NULL),
(87, 3, 27, 'Gusts, Gustavs, Tālrīts', NULL, NULL, NULL, NULL, NULL),
(88, 3, 28, 'Gunta, Ginta, Gunda', NULL, NULL, NULL, NULL, NULL),
(89, 3, 29, 'Aldonis, Agija', NULL, NULL, NULL, NULL, NULL),
(90, 3, 30, 'Nanija, Ilgmārs', NULL, NULL, NULL, NULL, NULL),
(91, 3, 31, 'Gvido, Atvars', NULL, NULL, NULL, NULL, NULL),
(92, 4, 1, 'Dagnis, Dagne', NULL, NULL, NULL, NULL, NULL),
(93, 4, 2, 'Irmgarde', NULL, NULL, NULL, NULL, NULL),
(94, 4, 3, 'Daira, Dairis, Daiva', NULL, NULL, NULL, NULL, NULL),
(95, 4, 4, 'Valda, Herta, Ārvalds, Ārvalda, Ārvaldis', NULL, NULL, NULL, NULL, NULL),
(96, 4, 5, 'Aivija, Vija, Vidaga', NULL, NULL, NULL, NULL, NULL),
(97, 4, 6, 'Dzinta, Zinta, Vīlips, Filips', NULL, NULL, NULL, NULL, NULL),
(98, 4, 7, 'Zina, Zinaīda, Helmuts', NULL, NULL, NULL, NULL, NULL),
(99, 4, 8, 'Edgars, Danute, Dana, Dans', NULL, NULL, NULL, NULL, NULL),
(100, 4, 9, 'Valērija, Žubīte, Alla', NULL, NULL, NULL, NULL, NULL),
(101, 4, 10, 'Anita, Anitra, Annika, Zīle', NULL, NULL, NULL, NULL, NULL),
(102, 4, 11, 'Hermanis, Vilmārs', NULL, NULL, NULL, NULL, NULL),
(103, 4, 12, 'Jūlijs, Ainis', NULL, NULL, NULL, NULL, NULL),
(104, 4, 13, 'Egils, Egīls, Nauris', NULL, NULL, NULL, NULL, NULL),
(105, 4, 14, 'Strauja, Gudrīte', NULL, NULL, NULL, NULL, NULL),
(106, 4, 15, 'Aelita, Gastons', NULL, NULL, NULL, NULL, NULL),
(107, 4, 16, 'Mintauts, Alfs, Bernadeta', NULL, NULL, NULL, NULL, NULL),
(108, 4, 17, 'Rūdolfs, Rūdis, Viviāna', NULL, NULL, NULL, NULL, NULL),
(109, 4, 18, 'Laura, Jadviga', NULL, NULL, NULL, NULL, NULL),
(110, 4, 19, 'Vēsma, Fanija', NULL, NULL, NULL, NULL, NULL),
(111, 4, 20, 'Mirta, Ziedīte', NULL, NULL, NULL, NULL, NULL),
(112, 4, 21, 'Marģers, Anastasija', NULL, NULL, NULL, NULL, NULL),
(113, 4, 22, 'Armands, Armanda', NULL, NULL, NULL, NULL, NULL),
(114, 4, 23, 'Jurģis, Juris, Georgs', NULL, NULL, NULL, NULL, NULL),
(115, 4, 24, 'Visvaldis, Ritvaldis, Nameda', NULL, NULL, NULL, NULL, NULL),
(116, 4, 25, 'Līksma, Bārbala', NULL, NULL, NULL, NULL, NULL),
(117, 4, 26, 'Alīna, Sandris, Rūsiņš', NULL, NULL, NULL, NULL, NULL),
(118, 4, 27, 'Tāle, Raimonda, Raina, Klementīne', NULL, NULL, NULL, NULL, NULL),
(119, 4, 28, 'Gundega, Terēze', NULL, NULL, NULL, NULL, NULL),
(120, 4, 29, 'Vilnis, Raimonds, Laine', NULL, NULL, NULL, NULL, NULL),
(121, 4, 30, 'Lilija, Liāna', NULL, NULL, NULL, NULL, NULL),
(122, 5, 1, 'Ziedonis', NULL, NULL, NULL, NULL, NULL),
(123, 5, 2, 'Zigmunds, Zigismunds, Sigmunds', NULL, NULL, NULL, NULL, NULL),
(124, 5, 3, 'Gints, Uvis', NULL, NULL, NULL, NULL, NULL),
(125, 5, 4, 'Vizbulīte, Vijolīte, Viola', NULL, NULL, NULL, NULL, NULL),
(126, 5, 5, 'Ģirts, Ģederts', NULL, NULL, NULL, NULL, NULL),
(127, 5, 6, 'Gaidis, Didzis', NULL, NULL, NULL, NULL, NULL),
(128, 5, 7, 'Henriete, Henrijs, Jete, Enriko', NULL, NULL, NULL, NULL, NULL),
(129, 5, 8, 'Staņislavs, Staņislava, Stefānija', NULL, NULL, NULL, NULL, NULL),
(130, 5, 9, 'Klāvs, Einārs, Ervīns', NULL, NULL, NULL, NULL, NULL),
(131, 5, 10, 'Maija, Paija', NULL, NULL, NULL, NULL, NULL),
(132, 5, 11, 'Milda, Karmena, Manfreds', NULL, NULL, NULL, NULL, NULL),
(133, 5, 12, 'Valija, Ināra, Ina, Inārs', NULL, NULL, NULL, NULL, NULL),
(134, 5, 13, 'Irēna, Irīna, Ira, Iraīda', NULL, NULL, NULL, NULL, NULL),
(135, 5, 14, 'Krišjānis, Elfa, Elvita, Aivita', NULL, NULL, NULL, NULL, NULL),
(136, 5, 15, 'Sofija, Taiga, Arita, Airita', NULL, NULL, NULL, NULL, NULL),
(137, 5, 16, 'Edvīns, Edijs', NULL, NULL, NULL, NULL, NULL),
(138, 5, 17, 'Herberts, Umberts, Dailis', NULL, NULL, NULL, NULL, NULL),
(139, 5, 18, 'Inese, Inesis, Ēriks', NULL, NULL, NULL, NULL, NULL),
(140, 5, 19, 'Lita, Sibilla, Teika', NULL, NULL, NULL, NULL, NULL),
(141, 5, 20, 'Venta, Salvis, Selva', NULL, NULL, NULL, NULL, NULL),
(142, 5, 21, 'Ernestīne, Ingmārs, Akvelīna', NULL, NULL, NULL, NULL, NULL),
(143, 5, 22, 'Emīlija, Kalendāros neierakstīto vārdu diena', NULL, NULL, NULL, NULL, NULL),
(144, 5, 23, 'Leokādija, Leontīne, Lonija, Ligija', NULL, NULL, NULL, NULL, NULL),
(145, 5, 24, 'Ilvija, Marlēna, Ziedone', NULL, NULL, NULL, NULL, NULL),
(146, 5, 25, 'Anšlavs, Junora', NULL, NULL, NULL, NULL, NULL),
(147, 5, 26, 'Eduards, Edvards, Edvarts, Varis', NULL, NULL, NULL, NULL, NULL),
(148, 5, 27, 'Dzidra, Dzidris, Gunita, Loreta', NULL, NULL, NULL, NULL, NULL),
(149, 5, 28, 'Vilis, Vilhelms', NULL, NULL, NULL, NULL, NULL),
(150, 5, 29, 'Maksis, Maksims, Raivis, Raivo', NULL, NULL, NULL, NULL, NULL),
(151, 5, 30, 'Vitolds, Lolita, Letīcija', NULL, NULL, NULL, NULL, NULL),
(152, 5, 31, 'Alīda, Jūsma', NULL, NULL, NULL, NULL, NULL),
(153, 6, 1, 'Biruta, Mairita, Bernedīne', NULL, NULL, NULL, NULL, NULL),
(154, 6, 2, 'Emma, Lība', NULL, NULL, NULL, NULL, NULL),
(155, 6, 3, 'Inta, Ineta, Intra', NULL, NULL, NULL, NULL, NULL),
(156, 6, 4, 'Elfrīda, Sintija, Sindija', NULL, NULL, NULL, NULL, NULL),
(157, 6, 5, 'Igors, Ingvars, Margots', NULL, NULL, NULL, NULL, NULL),
(158, 6, 6, 'Ingrīda, Ardis', NULL, NULL, NULL, NULL, NULL),
(159, 6, 7, 'Gaida, Arnis, Arno', NULL, NULL, NULL, NULL, NULL),
(160, 6, 8, 'Frīdis, Frīda, Mundra', NULL, NULL, NULL, NULL, NULL),
(161, 6, 9, 'Ligita, Gita', NULL, NULL, NULL, NULL, NULL),
(162, 6, 10, 'Malva, Anatols, Anatolijs', NULL, NULL, NULL, NULL, NULL),
(163, 6, 11, 'Ingus, Mairis, Vidvuds', NULL, NULL, NULL, NULL, NULL),
(164, 6, 12, 'Lenora, Nora, Ija', NULL, NULL, NULL, NULL, NULL),
(165, 6, 13, 'Zigfrīds, Ainārs, Uva', NULL, NULL, NULL, NULL, NULL),
(166, 6, 14, 'Tija, Saivis, Saiva, Sentis, Santis', NULL, NULL, NULL, NULL, NULL),
(167, 6, 15, 'Baņuta, Žermēna, Vilija, Vits', NULL, NULL, NULL, NULL, NULL),
(168, 6, 16, 'Justīne, Juta', NULL, NULL, NULL, NULL, NULL),
(169, 6, 17, 'Artūrs, Artis', NULL, NULL, NULL, NULL, NULL),
(170, 6, 18, 'Alberts, Madis', NULL, NULL, NULL, NULL, NULL),
(171, 6, 19, 'Viktors, Nils', NULL, NULL, NULL, NULL, NULL),
(172, 6, 20, 'Rasma, Rasa, Maira', NULL, NULL, NULL, NULL, NULL),
(173, 6, 21, 'Emīls, Egita, Monvīds', NULL, NULL, NULL, NULL, NULL),
(174, 6, 22, 'Ludmila, Laimdots, Laimiņš', NULL, NULL, NULL, NULL, NULL),
(175, 6, 23, 'Līga', NULL, NULL, NULL, NULL, NULL),
(176, 6, 24, 'Jānis', NULL, NULL, NULL, NULL, NULL),
(177, 6, 25, 'Milija, Maiga', NULL, NULL, NULL, NULL, NULL),
(178, 6, 26, 'Ausma, Ausmis, Inguna, Ingūna, Inguns', NULL, NULL, NULL, NULL, NULL),
(179, 6, 27, 'Malvīne, Malvis', NULL, NULL, NULL, NULL, NULL),
(180, 6, 28, 'Viesturs, Viestards, Kitija', NULL, NULL, NULL, NULL, NULL),
(181, 6, 29, 'Pēteris, Pāvils, Pauls, Paulis', NULL, NULL, NULL, NULL, NULL),
(182, 6, 30, 'Tālivaldis, Mareks', NULL, NULL, NULL, NULL, NULL),
(183, 7, 1, 'Imants, Rimants, Ingars, Intars', NULL, NULL, NULL, NULL, NULL),
(184, 7, 2, 'Lauma, Ilvars, Halina', NULL, NULL, NULL, NULL, NULL),
(185, 7, 3, 'Benita, Everita, Emerita, Verita', NULL, NULL, NULL, NULL, NULL),
(186, 7, 4, 'Uldis, Ulvis, Sandis, Sandijs', NULL, NULL, NULL, NULL, NULL),
(187, 7, 5, 'Andžejs, Andžs, Edīte, Esmeralda', NULL, NULL, NULL, NULL, NULL),
(188, 7, 6, 'Anrijs, Arkādijs', NULL, NULL, NULL, NULL, NULL),
(189, 7, 7, 'Alda, Maruta', NULL, NULL, NULL, NULL, NULL),
(190, 7, 8, 'Antra, Adele, Ada', NULL, NULL, NULL, NULL, NULL),
(191, 7, 9, 'Zaiga, Asne, Asna', NULL, NULL, NULL, NULL, NULL),
(192, 7, 10, 'Lija, Olīvija, Olivers, Odrija', NULL, NULL, NULL, NULL, NULL),
(193, 7, 11, 'Leonora, Svens', NULL, NULL, NULL, NULL, NULL),
(194, 7, 12, 'Indriķis, Ints, Namejs', NULL, NULL, NULL, NULL, NULL),
(195, 7, 13, 'Margrieta, Margarita', NULL, NULL, NULL, NULL, NULL),
(196, 7, 14, 'Oskars, Ritvars, Anvars', NULL, NULL, NULL, NULL, NULL),
(197, 7, 15, 'Egons, Egmonts, Egija, Henriks, Heinrihs', NULL, NULL, NULL, NULL, NULL),
(198, 7, 16, 'Hermīne, Estere, Liepa', NULL, NULL, NULL, NULL, NULL),
(199, 7, 17, 'Aleksis, Alekss, Aleksejs', NULL, NULL, NULL, NULL, NULL),
(200, 7, 18, 'Rozālija, Roze', NULL, NULL, NULL, NULL, NULL),
(201, 7, 19, 'Jautrīte, Kamila, Digna, Sāra', NULL, NULL, NULL, NULL, NULL),
(202, 7, 20, 'Ritma, Ramona', NULL, NULL, NULL, NULL, NULL),
(203, 7, 21, 'Melisa, Meldra, Meldris', NULL, NULL, NULL, NULL, NULL),
(204, 7, 22, 'Marija, Marika, Marina', NULL, NULL, NULL, NULL, NULL),
(205, 7, 23, 'Magda, Magone, Mērija, Magdalēna', NULL, NULL, NULL, NULL, NULL),
(206, 7, 24, 'Kristīne, Kristīna, Krista, Kristiāna, Kristiāns', NULL, NULL, NULL, NULL, NULL),
(207, 7, 25, 'Jēkabs, Žaklīna', NULL, NULL, NULL, NULL, NULL),
(208, 7, 26, 'Anna, Ance, Annija', NULL, NULL, NULL, NULL, NULL),
(209, 7, 27, 'Marta, Dita, Dite', NULL, NULL, NULL, NULL, NULL),
(210, 7, 28, 'Cecīlija, Cilda', NULL, NULL, NULL, NULL, NULL),
(211, 7, 29, 'Edmunds, Vidmants, Edžus', NULL, NULL, NULL, NULL, NULL),
(212, 7, 30, 'Valters, Renārs, Regnārs', NULL, NULL, NULL, NULL, NULL),
(213, 7, 31, 'Rūta, Ruta, Angelika, Sigita', NULL, NULL, NULL, NULL, NULL),
(214, 8, 1, 'Albīns, Albīna', NULL, NULL, NULL, NULL, NULL),
(215, 8, 2, 'Normunds, Stefans', NULL, NULL, NULL, NULL, NULL),
(216, 8, 3, 'Augusts', NULL, NULL, NULL, NULL, NULL),
(217, 8, 4, 'Romāns, Romualds, Romualda', NULL, NULL, NULL, NULL, NULL),
(218, 8, 5, 'Osvalds, Arvils', NULL, NULL, NULL, NULL, NULL),
(219, 8, 6, 'Askolds, Aisma', NULL, NULL, NULL, NULL, NULL),
(220, 8, 7, 'Alfrēds, Fredis, Madars', NULL, NULL, NULL, NULL, NULL),
(221, 8, 8, 'Mudīte, Vladislavs, Vladislava', NULL, NULL, NULL, NULL, NULL),
(222, 8, 9, 'Madara, Genoveva, Genovefa', NULL, NULL, NULL, NULL, NULL),
(223, 8, 10, 'Brencis, Inuta, Audris', NULL, NULL, NULL, NULL, NULL),
(224, 8, 11, 'Olga, Zita, Liega, Zigita', NULL, NULL, NULL, NULL, NULL),
(225, 8, 12, 'Vizma, Klāra', NULL, NULL, NULL, NULL, NULL),
(226, 8, 13, 'Elvīra, Velga, Rēzija', NULL, NULL, NULL, NULL, NULL),
(227, 8, 14, 'Zelma, Zemgus, Virma', NULL, NULL, NULL, NULL, NULL),
(228, 8, 15, 'Zenta, Dzelde, Zelda', NULL, NULL, NULL, NULL, NULL),
(229, 8, 16, 'Astra, Astrīda', NULL, NULL, NULL, NULL, NULL),
(230, 8, 17, 'Vineta, Oļegs', NULL, NULL, NULL, NULL, NULL),
(231, 8, 18, 'Liene, Liena, Helēna, Elena, Ellena', NULL, NULL, NULL, NULL, NULL),
(232, 8, 19, 'Melānija, Imanta', NULL, NULL, NULL, NULL, NULL),
(233, 8, 20, 'Bernhards, Boriss, Rojs', NULL, NULL, NULL, NULL, NULL),
(234, 8, 21, 'Janīna, Linda', NULL, NULL, NULL, NULL, NULL),
(235, 8, 22, 'Rudīte, Everts', NULL, NULL, NULL, NULL, NULL),
(236, 8, 23, 'Vitālijs, Ralfs, Valgudis', NULL, NULL, NULL, NULL, NULL),
(237, 8, 24, 'Bērtulis, Boļeslavs', NULL, NULL, NULL, NULL, NULL),
(238, 8, 25, 'Ludvigs, Ludis, Ivonna, Patrīcija, Patriks', NULL, NULL, NULL, NULL, NULL),
(239, 8, 26, 'Natālija, Tālija, Broņislavs, Broņislava', NULL, NULL, NULL, NULL, NULL),
(240, 8, 27, 'Žanis, Jorens, Alens', NULL, NULL, NULL, NULL, NULL),
(241, 8, 28, 'Auguste, Guste', NULL, NULL, NULL, NULL, NULL),
(242, 8, 29, 'Armīns, Vismants, Aiga', NULL, NULL, NULL, NULL, NULL),
(243, 8, 30, 'Alvis, Jolanta, Samanta', NULL, NULL, NULL, NULL, NULL),
(244, 8, 31, 'Vilma, Aigars', NULL, NULL, NULL, NULL, NULL),
(245, 9, 1, 'Ilmārs, Iluta, Austrums', NULL, NULL, NULL, NULL, NULL),
(246, 9, 2, 'Elīza, Lizete, Zete', NULL, NULL, NULL, NULL, NULL),
(247, 9, 3, 'Berta, Bella', NULL, NULL, NULL, NULL, NULL),
(248, 9, 4, 'Dzintra, Dzintara, Dzintars', NULL, NULL, NULL, NULL, NULL),
(249, 9, 5, 'Klaudija, Persijs, Vaida', NULL, NULL, NULL, NULL, NULL),
(250, 9, 6, 'Maigonis, Magnuss, Mariuss', NULL, NULL, NULL, NULL, NULL),
(251, 9, 7, 'Regīna, Ermīns', NULL, NULL, NULL, NULL, NULL),
(252, 9, 8, 'Ilga', NULL, NULL, NULL, NULL, NULL),
(253, 9, 9, 'Bruno, Telma', NULL, NULL, NULL, NULL, NULL),
(254, 9, 10, 'Jausma, Albertīne', NULL, NULL, NULL, NULL, NULL),
(255, 9, 11, 'Signe, Signija', NULL, NULL, NULL, NULL, NULL),
(256, 9, 12, 'Erna, Evita, Eva', NULL, NULL, NULL, NULL, NULL),
(257, 9, 13, 'Iza, Izabella', NULL, NULL, NULL, NULL, NULL),
(258, 9, 14, 'Sanita, Santa, Sanda, Sanija, Sandija', NULL, NULL, NULL, NULL, NULL),
(259, 9, 15, 'Sandra, Sondra, Gunvaldis, Gunvaris', NULL, NULL, NULL, NULL, NULL),
(260, 9, 16, 'Asja, Asnate, Dāgs', NULL, NULL, NULL, NULL, NULL),
(261, 9, 17, 'Vera, Vaira, Vairis', NULL, NULL, NULL, NULL, NULL),
(262, 9, 18, 'Liesma, Elita, Alita', NULL, NULL, NULL, NULL, NULL),
(263, 9, 19, 'Verners, Muntis', NULL, NULL, NULL, NULL, NULL),
(264, 9, 20, 'Guntra, Marianna, Ginters', NULL, NULL, NULL, NULL, NULL),
(265, 9, 21, 'Modris, Matīss, Mariss', NULL, NULL, NULL, NULL, NULL),
(266, 9, 22, 'Māris, Maigurs, Mārica', NULL, NULL, NULL, NULL, NULL),
(267, 9, 23, 'Vanda, Veneranda, Venija', NULL, NULL, NULL, NULL, NULL),
(268, 9, 24, 'Agris, Agrita', NULL, NULL, NULL, NULL, NULL),
(269, 9, 25, 'Rodrigo, Rauls', NULL, NULL, NULL, NULL, NULL),
(270, 9, 26, 'Gundars, Kurts, Knuts', NULL, NULL, NULL, NULL, NULL),
(271, 9, 27, 'Ādolfs, Ilgonis', NULL, NULL, NULL, NULL, NULL),
(272, 9, 28, 'Sergejs, Svetlana, Lana', NULL, NULL, NULL, NULL, NULL),
(273, 9, 29, 'Miķelis, Mikus, Miks, Mihails', NULL, NULL, NULL, NULL, NULL),
(274, 9, 30, 'Elma, Elna, Menarda', NULL, NULL, NULL, NULL, NULL),
(275, 10, 1, 'Zanda, Zandis, Lāsma', NULL, NULL, NULL, NULL, NULL),
(276, 10, 2, 'Ilma, Skaidris', NULL, NULL, NULL, NULL, NULL),
(277, 10, 3, 'Elza, Ilizana', NULL, NULL, NULL, NULL, NULL),
(278, 10, 4, 'Modra, Francis, Dmitrijs', NULL, NULL, NULL, NULL, NULL),
(279, 10, 5, 'Amālija, Amēlija', NULL, NULL, NULL, NULL, NULL),
(280, 10, 6, 'Monika, Zilgma, Zilga', NULL, NULL, NULL, NULL, NULL),
(281, 10, 7, 'Daumants, Druvvaldis', NULL, NULL, NULL, NULL, NULL),
(282, 10, 8, 'Aina, Anete', NULL, NULL, NULL, NULL, NULL),
(283, 10, 9, 'Elga, Helga, Elgars', NULL, NULL, NULL, NULL, NULL),
(284, 10, 10, 'Arvīds, Arvis, Druvis', NULL, NULL, NULL, NULL, NULL),
(285, 10, 11, 'Monta, Tince, Silva', NULL, NULL, NULL, NULL, NULL),
(286, 10, 12, 'Valfrīds, Kira', NULL, NULL, NULL, NULL, NULL),
(287, 10, 13, 'Irma, Mirga', NULL, NULL, NULL, NULL, NULL),
(288, 10, 14, 'Vilhelmīne, Minna', NULL, NULL, NULL, NULL, NULL),
(289, 10, 15, 'Eda, Hedviga, Helvijs', NULL, NULL, NULL, NULL, NULL),
(290, 10, 16, 'Daiga, Dinija, Dinārs', NULL, NULL, NULL, NULL, NULL),
(291, 10, 17, 'Gaits, Gaitis, Karīna', NULL, NULL, NULL, NULL, NULL),
(292, 10, 18, 'Rolands, Rolanda, Ronalds, Erlends, Lūkass', NULL, NULL, NULL, NULL, NULL),
(293, 10, 19, 'Elīna, Drosma, Drosmis', NULL, NULL, NULL, NULL, NULL),
(294, 10, 20, 'Leonīda, Leonīds', NULL, NULL, NULL, NULL, NULL),
(295, 10, 21, 'Urzula, Severīns', NULL, NULL, NULL, NULL, NULL),
(296, 10, 22, 'Īrisa, Irīda, Airisa', NULL, NULL, NULL, NULL, NULL),
(297, 10, 23, 'Daina, Dainis, Dainida', NULL, NULL, NULL, NULL, NULL),
(298, 10, 24, 'Renāte, Modrīte, Mudrīte', NULL, NULL, NULL, NULL, NULL),
(299, 10, 25, 'Beāte, Beatrise', NULL, NULL, NULL, NULL, NULL),
(300, 10, 26, 'Amanta, Amanda, Kaiva', NULL, NULL, NULL, NULL, NULL),
(301, 10, 27, 'Lilita, Irita, Ita', NULL, NULL, NULL, NULL, NULL),
(302, 10, 28, 'Ņina, Ninona, Antoņina, Oksana', NULL, NULL, NULL, NULL, NULL),
(303, 10, 29, 'Laimonis, Laimis, Elvijs, Elvis, Elva', NULL, NULL, NULL, NULL, NULL),
(304, 10, 30, 'Adīna, Nadīna, Ulla', NULL, NULL, NULL, NULL, NULL),
(305, 10, 31, 'Valts, Rinalds, Rinalda', NULL, NULL, NULL, NULL, NULL),
(306, 11, 1, 'Ikars', NULL, NULL, NULL, NULL, NULL),
(307, 11, 2, 'Vivita, Viva, Dzīle', NULL, NULL, NULL, NULL, NULL),
(308, 11, 3, 'Ērika, Dagnija', NULL, NULL, NULL, NULL, NULL),
(309, 11, 4, 'Atis, Otomārs, Oto', NULL, NULL, NULL, NULL, NULL),
(310, 11, 5, 'Šarlote, Lote', NULL, NULL, NULL, NULL, NULL),
(311, 11, 6, 'Linards, Leons, Leo, Leonards, Leonarda', NULL, NULL, NULL, NULL, NULL),
(312, 11, 7, 'Helma, Lotārs', NULL, NULL, NULL, NULL, NULL),
(313, 11, 8, 'Aleksandra, Agra', NULL, NULL, NULL, NULL, NULL),
(314, 11, 9, 'Teodors', NULL, NULL, NULL, NULL, NULL),
(315, 11, 10, 'Mārtiņš, Mārcis, Marks, Markuss', NULL, NULL, NULL, NULL, NULL),
(316, 11, 11, 'Ojārs, Nellija, Rainers', NULL, NULL, NULL, NULL, NULL),
(317, 11, 12, 'Kaija, Kornēlija', NULL, NULL, NULL, NULL, NULL),
(318, 11, 13, 'Eižens, Jevgeņijs, Jevgeņija', NULL, NULL, NULL, NULL, NULL),
(319, 11, 14, 'Fricis, Vikentijs, Vincents', NULL, NULL, NULL, NULL, NULL),
(320, 11, 15, 'Leopolds, Undīne, Unda', NULL, NULL, NULL, NULL, NULL),
(321, 11, 16, 'Banga, Glorija', NULL, NULL, NULL, NULL, NULL),
(322, 11, 17, 'Hugo, Uga, Uģis', NULL, NULL, NULL, NULL, NULL),
(323, 11, 18, 'Aleksandrs, Doloresa, Brīve', NULL, NULL, NULL, NULL, NULL),
(324, 11, 19, 'Elizabete, Betija, Liza, Līze', NULL, NULL, NULL, NULL, NULL),
(325, 11, 20, 'Anda, Andīna, Vigo', NULL, NULL, NULL, NULL, NULL),
(326, 11, 21, 'Zeltīte, Andis', NULL, NULL, NULL, NULL, NULL),
(327, 11, 22, 'Aldis, Alfons, Aldris', NULL, NULL, NULL, NULL, NULL),
(328, 11, 23, 'Zigrīda, Zigfrīda, Zigrīds', NULL, NULL, NULL, NULL, NULL),
(329, 11, 24, 'Velta, Velda', NULL, NULL, NULL, NULL, NULL),
(330, 11, 25, 'Katrīna, Kate, Kadrija, Trīne, Katrīne', NULL, NULL, NULL, NULL, NULL),
(331, 11, 26, 'Konrāds, Sebastians', NULL, NULL, NULL, NULL, NULL),
(332, 11, 27, 'Lauris, Norberts', NULL, NULL, NULL, NULL, NULL),
(333, 11, 28, 'Rita, Vita, Olita', NULL, NULL, NULL, NULL, NULL),
(334, 11, 29, 'Ignats, Virgīnija', NULL, NULL, NULL, NULL, NULL),
(335, 11, 30, 'Andrievs, Andrejs, Andris', NULL, NULL, NULL, NULL, NULL),
(336, 12, 1, 'Arnolds, Emanuels', NULL, NULL, NULL, NULL, NULL),
(337, 12, 2, 'Meta, Sniedze', NULL, NULL, NULL, NULL, NULL),
(338, 12, 3, 'Evija, Raita, Jogita', NULL, NULL, NULL, NULL, NULL),
(339, 12, 4, 'Baiba, Barbara, Barba', NULL, NULL, NULL, NULL, NULL),
(340, 12, 5, 'Sabīne, Sarma, Klaudijs', NULL, NULL, NULL, NULL, NULL),
(341, 12, 6, 'Nikolajs, Niklāvs, Niks, Nikola', NULL, NULL, NULL, NULL, NULL),
(342, 12, 7, 'Antonija, Anta, Dzirkstīte', NULL, NULL, NULL, NULL, NULL),
(343, 12, 8, 'Gunārs, Gunis, Vladimirs', NULL, NULL, NULL, NULL, NULL),
(344, 12, 9, 'Tabita, Sarmīte', NULL, NULL, NULL, NULL, NULL),
(345, 12, 10, 'Guna, Judīte', NULL, NULL, NULL, NULL, NULL),
(346, 12, 11, 'Voldemārs, Valdemārs, Valdis', NULL, NULL, NULL, NULL, NULL),
(347, 12, 12, 'Otīlija, Iveta', NULL, NULL, NULL, NULL, NULL),
(348, 12, 13, 'Lūcija, Veldze', NULL, NULL, NULL, NULL, NULL),
(349, 12, 14, 'Auseklis, Gaisma', NULL, NULL, NULL, NULL, NULL),
(350, 12, 15, 'Hanna, Johanna, Jana', NULL, NULL, NULL, NULL, NULL),
(351, 12, 16, 'Alvīne', NULL, NULL, NULL, NULL, NULL),
(352, 12, 17, 'Hilda, Teiksma', NULL, NULL, NULL, NULL, NULL),
(353, 12, 18, 'Kristofers, Kristaps, Krists, Kristers, Klinta', NULL, NULL, NULL, NULL, NULL),
(354, 12, 19, 'Lelde, Sarmis', NULL, NULL, NULL, NULL, NULL),
(355, 12, 20, 'Arta, Minjona', NULL, NULL, NULL, NULL, NULL),
(356, 12, 21, 'Tomass, Toms, Saulcerīte', NULL, NULL, NULL, NULL, NULL),
(357, 12, 22, 'Saulvedis', NULL, NULL, NULL, NULL, NULL),
(358, 12, 23, 'Viktorija, Balva', NULL, NULL, NULL, NULL, NULL),
(359, 12, 24, 'Ādams, Ieva', NULL, NULL, NULL, NULL, NULL),
(360, 12, 25, 'Stella, Larisa', NULL, NULL, NULL, NULL, NULL),
(361, 12, 26, 'Dainuvīte, Megija, Gija', NULL, NULL, NULL, NULL, NULL),
(362, 12, 27, 'Elmārs, Helmārs, Inita', NULL, NULL, NULL, NULL, NULL),
(363, 12, 28, 'Inga, Ivita, Irvita, Ingeborga', NULL, NULL, NULL, NULL, NULL),
(364, 12, 29, 'Solveiga, Ilgona', NULL, NULL, NULL, NULL, NULL),
(365, 12, 30, 'Dāniels, Daniela, Dāvids, Dāvis, Daniels', NULL, NULL, NULL, NULL, NULL),
(366, 12, 31, 'Silvestrs, Silvis, Kalvis', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `in_sources`
--

CREATE TABLE IF NOT EXISTS `in_sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(500) DEFAULT NULL,
  `code` varchar(100) DEFAULT NULL,
  `feed_color` varchar(50) DEFAULT NULL,
  `is_for_search` tinyint(1) DEFAULT '0',
  `icon_class` varchar(200) DEFAULT NULL,
  `tag_id` int(10) unsigned DEFAULT NULL COMMENT 'Raksturīgā iezīme',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `in_source_uniq_code` (`code`) COMMENT 'Datu avota kodam jābūt unikālam',
  KEY `code` (`code`),
  KEY `in_sources_is_for_search_index` (`is_for_search`),
  KEY `in_sources_tag_id_index` (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `in_sources`
--

INSERT INTO `in_sources` (`id`, `title`, `code`, `feed_color`, `is_for_search`, `icon_class`, `tag_id`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Latvenergo', NULL, 'rgba(116,242,0,1)', 1, 'iconle-le_logo', 6, NULL, NULL, NULL, NULL),
(2, 'Elektrum', NULL, 'rgba(248,151,40,1)', 1, 'fa fa-bolt', 3, NULL, NULL, NULL, NULL),
(3, 'Sadales tīkls', NULL, 'rgba(0,67,250,1)', 1, 'fa fa-plug', 7, NULL, NULL, NULL, NULL),
(4, 'Personāls', NULL, 'rgba(139,128,128,1)', 0, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `in_systems`
--

CREATE TABLE IF NOT EXISTS `in_systems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Nosaukums',
  `url` varchar(250) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Interneta adrese',
  `picture_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Attēla nosaukums',
  `picture_guid` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Attēla identifikators',
  `source_id` int(11) DEFAULT NULL COMMENT 'Datu avots',
  `employee_id` int(10) unsigned NOT NULL COMMENT 'Atbildīgais darbinieks',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `in_systems_source_id_index` (`source_id`),
  KEY `in_systems_employee_id_index` (`employee_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_tags`
--

CREATE TABLE IF NOT EXISTS `in_tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=40 ;

--
-- Dumping data for table `in_tags`
--

INSERT INTO `in_tags` (`id`, `name`, `link`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(3, 'Elektrum', NULL, NULL, NULL, NULL, NULL),
(6, 'Latvenergo', NULL, NULL, NULL, NULL, NULL),
(7, 'Sadales tīkls', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `in_tags_article`
--

CREATE TABLE IF NOT EXISTS `in_tags_article` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `article_id` int(11) unsigned NOT NULL,
  `tag_id` int(11) unsigned NOT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_id` (`article_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=61 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_total_stat`
--

CREATE TABLE IF NOT EXISTS `in_total_stat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  `cnt` int(11) DEFAULT '0',
  `order_index` int(11) DEFAULT '0',
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=71 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_visit_log`
--

CREATE TABLE IF NOT EXISTS `in_visit_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_guid` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8_unicode_ci,
  `ip` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `visit_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_weather`
--

CREATE TABLE IF NOT EXISTS `in_weather` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `weather_date` date NOT NULL,
  `weather_type_id` int(11) NOT NULL,
  `temper_low` int(11) DEFAULT NULL,
  `temper_high` int(11) DEFAULT NULL,
  `meteo_code` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `in_weather_weather_date_unique` (`weather_date`),
  KEY `in_weather_weather_type_id_index` (`weather_type_id`),
  KEY `in_weather_meteo_code_index` (`meteo_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `in_weather_types`
--

CREATE TABLE IF NOT EXISTS `in_weather_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `meteo_code` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_name` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `file_guid` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_user_id` int(11) DEFAULT NULL,
  `created_time` datetime DEFAULT NULL,
  `modified_user_id` int(11) DEFAULT NULL,
  `modified_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `in_weather_types_meteo_code_index` (`meteo_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

--
-- Dumping data for table `in_weather_types`
--

INSERT INTO `in_weather_types` (`id`, `title`, `meteo_code`, `file_name`, `file_guid`, `created_user_id`, `created_time`, `modified_user_id`, `modified_time`) VALUES
(1, 'Saulains un sniegs', NULL, 'w_saule_sniegs.gif', 'w_saule_sniegs.gif', NULL, NULL, NULL, NULL),
(2, 'Apmācies, stiprs sniegs', NULL, 'w_saule_sniegs.gif', 'w_saule_sniegs.gif', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_reserved_reserved_at_index` (`queue`,`reserved`,`reserved_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2015_12_01_155605_add_color_field_to_menu', 1),
('2015_12_02_create_in_tags_table', 2),
('2015_12_03_create_in_tags_articles', 3),
('2015_12_04_221112_add_ad_fields_to_dx_users', 4),
('2015_12_06_170339_create_table_in_visit_log', 5),
('2015_12_07_174810_add_background_img_field_dx_pages', 6),
('2015_12_07_230325_create_table_in_employees', 6),
('2015_12_09_172950_new_table_in_weather_types', 7),
('2015_12_09_173329_new_table_in_weather', 7),
('2015_12_09_181545_add_special_day_to_in_saints_days', 7),
('2015_12_09_191627_add_source_id_to_dx_pages', 7),
('2015_12_10_163716_new_table_in_article_types', 8),
('2015_12_10_164152_add_fields_to_in_articles', 8),
('2015_12_11_114134_update_article_type_pictures', 8),
('2015_12_11_173624_add_password_field', 8),
('2015_12_11_181848_redefine_password_field_in_register', 8),
('2015_12_13_132035_remove_language_fields_in_articles', 8),
('2015_12_14_151913_create_table_in_employee_history', 9),
('2015_12_18_234906_new_table_dx_config', 10),
('2015_12_19_190245_new_table_in_articles_img', 11),
('2015_12_20_153612_add_field_to_in_article_type', 12),
('2015_12_21_105217_update_forms_width_field', 13),
('2015_12_26_102835_add_content_bg_color_to_dx_pages', 14),
('2015_12_31_144456_add_html_field_option_to_in_html', 15),
('2016_01_03_153049_add_index_field_to_dx_menu', 16),
('2016_01_08_101554_add_employee_nr_to_in_employees', 17),
('2016_01_08_152831_add_is_for_search_in_sources', 18),
('2016_01_18_234218_create_in_questions_table', 19),
('2016_01_21_214250_add_is_galeries_field_to_in_articles_type', 20),
('2016_01_25_150050_add_fields_to_in_employees_left', 21),
('2016_01_25_151736_add_foreignkeys_to_in_employees', 22),
('2015_12_08_233016_add_fields_to_in_employees', 6),
('2016_01_25_175539_fill_test_data_employees', 24),
('2016_01_25_181600_add_cabinet_to_in_employees', 25),
('2016_01_25_222310_add_left_reasons_values', 26),
('2016_01_25_224118_add_icon_field_to_in_sources', 27),
('2016_01_27_222609_add_sys_color_in_documents_lotus', 28),
('2016_01_31_190455_add_multiple_mark_in_dx_lists_fields', 28),
('2016_02_02_084807_new_table_in_dailyquest_questions', 29),
('2016_02_02_084855_new_table_in_dailyquest_options', 29),
('2016_02_02_084908_new_table_in_dailyquest_answers', 29),
('2016_02_06_094628_create_table_in_articles_vid', 30),
('2016_02_08_104654_create_table_in_publish', 31),
('2016_02_08_073356_new_table_in_faq_section', 32),
('2016_02_08_073407_new_table_in_faq_question', 32),
('2016_02_08_073430_new_table_in_faq_section_source', 32),
('2016_02_08_190202_add_source_col_in_events', 32),
('2016_02_09_000813_add_foreign_on_delete', 32),
('2016_02_09_153430_insert_dx_config_chart_color', 33),
('2016_02_11_173138_add_config_for_slides_transition', 34),
('2016_02_12_185123_add_tag_field_to_data_source', 35),
('2016_02_16_162644_create_table_dx_files_headers', 36),
('2016_02_15_124713_new_table_in_systems', 37),
('2016_02_17_184912_create_db_history_tables', 38),
('2016_02_21_131031_create_table_in_departments', 39),
('2016_02_18_145236_create_jobs_table', 40),
('2016_02_18_145257_create_failed_jobs_table', 40),
('2016_02_18_145530_create_processes_tables', 40),
('2016_02_22_112550_add_employees_history_department_id', 41),
('2016_02_26_062007_add_columns_for_in_documents', 42),
('2016_02_29_091303_add_click2call_configs', 42),
('2016_02_29_121806_add_columns_in_processes', 43),
('2016_02_29_130336_add_columns_in_documents_lotus', 43),
('2016_03_01_092936_update_processes', 44),
('2016_03_02_102511_update_chart_color_config', 45),
('2016_03_07_121152_make_picture_not_required', 46),
('2016_03_07_164657_doc_generation_db_structures', 47),
('2016_03_10_141515_add_galeries_relations_to_article', 48),
('2016_03_11_124304_create_articles_files', 49),
('2016_03_11_180346_add_author_to_article', 50),
('2016_03_14_063859_add_clean_html_to_articles', 51),
('2016_03_14_102710_create_articles_contents', 52),
('2016_03_18_183624_set_employee_not_required_for_process', 53),
('2016_03_19_120904_add_sources_rights', 54);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dx_change_log`
--
ALTER TABLE `dx_change_log`
  ADD CONSTRAINT `dx_fk_change_log_lists` FOREIGN KEY (`list_id`) REFERENCES `dx_lists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_change_log_lists_fields` FOREIGN KEY (`field_id`) REFERENCES `dx_lists_fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_change_log_users` FOREIGN KEY (`user_id`) REFERENCES `dx_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dx_config`
--
ALTER TABLE `dx_config`
  ADD CONSTRAINT `fk_dx_config_field_type_id` FOREIGN KEY (`field_type_id`) REFERENCES `dx_field_types` (`id`);

--
-- Constraints for table `dx_data`
--
ALTER TABLE `dx_data`
  ADD CONSTRAINT `dx_fk_data_lists` FOREIGN KEY (`multi_list_id`) REFERENCES `dx_lists` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dx_db_events`
--
ALTER TABLE `dx_db_events`
  ADD CONSTRAINT `dx_db_events_list_id_foreign` FOREIGN KEY (`list_id`) REFERENCES `dx_lists` (`id`),
  ADD CONSTRAINT `dx_db_events_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `dx_db_event_types` (`id`),
  ADD CONSTRAINT `dx_db_events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `dx_users` (`id`);

--
-- Constraints for table `dx_db_history`
--
ALTER TABLE `dx_db_history`
  ADD CONSTRAINT `dx_db_history_event_id_foreign` FOREIGN KEY (`event_id`) REFERENCES `dx_db_events` (`id`),
  ADD CONSTRAINT `dx_db_history_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `dx_lists_fields` (`id`);

--
-- Constraints for table `dx_forms`
--
ALTER TABLE `dx_forms`
  ADD CONSTRAINT `dx_fk_forms_lists` FOREIGN KEY (`list_id`) REFERENCES `dx_lists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_forms_types` FOREIGN KEY (`form_type_id`) REFERENCES `dx_forms_types` (`id`);

--
-- Constraints for table `dx_forms_fields`
--
ALTER TABLE `dx_forms_fields`
  ADD CONSTRAINT `dx_fk_forms_fields_forms` FOREIGN KEY (`form_id`) REFERENCES `dx_forms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_forms_fields_lists` FOREIGN KEY (`list_id`) REFERENCES `dx_lists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_forms_fields_lists_fields` FOREIGN KEY (`field_id`) REFERENCES `dx_lists_fields` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dx_forms_tabs`
--
ALTER TABLE `dx_forms_tabs`
  ADD CONSTRAINT `dx_fk_forms_tabs_forms` FOREIGN KEY (`form_id`) REFERENCES `dx_forms` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_forms_tabs_lists` FOREIGN KEY (`grid_list_id`) REFERENCES `dx_lists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_forms_tabs_lists_fields_1` FOREIGN KEY (`grid_list_field_id`) REFERENCES `dx_lists_fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_forms_tabs_lists_fields_2` FOREIGN KEY (`grid_list_field2_id`) REFERENCES `dx_lists_fields` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dx_item_access`
--
ALTER TABLE `dx_item_access`
  ADD CONSTRAINT `dx_fk_item_access_lists` FOREIGN KEY (`list_id`) REFERENCES `dx_lists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_item_access_users` FOREIGN KEY (`user_id`) REFERENCES `dx_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dx_lists`
--
ALTER TABLE `dx_lists`
  ADD CONSTRAINT `dx_fk_list_object` FOREIGN KEY (`object_id`) REFERENCES `dx_objects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_lists_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `dx_lists_groups` (`id`);

--
-- Constraints for table `dx_lists_fields`
--
ALTER TABLE `dx_lists_fields`
  ADD CONSTRAINT `dx_fk_liests_fields_lists_fields_display` FOREIGN KEY (`rel_display_field_id`) REFERENCES `dx_lists_fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_lists_fields_field_types` FOREIGN KEY (`type_id`) REFERENCES `dx_field_types` (`id`),
  ADD CONSTRAINT `dx_fk_lists_fields_lists` FOREIGN KEY (`list_id`) REFERENCES `dx_lists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_lists_fields_lists_fields_binded` FOREIGN KEY (`binded_field_id`) REFERENCES `dx_lists_fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_lists_fields_lists_fields_binded_rel_field_id` FOREIGN KEY (`binded_rel_field_id`) REFERENCES `dx_lists_fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_lists_fields_lists_rel_list_id` FOREIGN KEY (`rel_list_id`) REFERENCES `dx_lists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_lists_fields_views` FOREIGN KEY (`rel_view_id`) REFERENCES `dx_views` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dx_menu`
--
ALTER TABLE `dx_menu`
  ADD CONSTRAINT `dx_fk_menu_lists` FOREIGN KEY (`list_id`) REFERENCES `dx_lists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_menu_parent` FOREIGN KEY (`parent_id`) REFERENCES `dx_menu` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dx_model`
--
ALTER TABLE `dx_model`
  ADD CONSTRAINT `dx_fk_model_lists_child` FOREIGN KEY (`child_list_id`) REFERENCES `dx_lists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_model_lists_fields` FOREIGN KEY (`child_rel_field_id`) REFERENCES `dx_lists_fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_model_lists_parent` FOREIGN KEY (`parent_list_id`) REFERENCES `dx_lists` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dx_roles_lists`
--
ALTER TABLE `dx_roles_lists`
  ADD CONSTRAINT `dx_fk_roles_lists_lists` FOREIGN KEY (`list_id`) REFERENCES `dx_lists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_roles_lists_lists_fields` FOREIGN KEY (`user_field_id`) REFERENCES `dx_lists_fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_roles_lists_roles` FOREIGN KEY (`role_id`) REFERENCES `dx_roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dx_tasks`
--
ALTER TABLE `dx_tasks`
  ADD CONSTRAINT `fk_dx_lists_dx_tasks` FOREIGN KEY (`list_id`) REFERENCES `dx_lists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_dx_tasks_dx_users` FOREIGN KEY (`task_employee_id`) REFERENCES `dx_users` (`id`),
  ADD CONSTRAINT `fk_dx_tasks_statuses` FOREIGN KEY (`task_status_id`) REFERENCES `dx_tasks_statuses` (`id`),
  ADD CONSTRAINT `fk_dx_tasks_types` FOREIGN KEY (`task_type_id`) REFERENCES `dx_tasks_types` (`id`);

--
-- Constraints for table `dx_users`
--
ALTER TABLE `dx_users`
  ADD CONSTRAINT `dx_users_source_id_foreign` FOREIGN KEY (`source_id`) REFERENCES `in_sources` (`id`);

--
-- Constraints for table `dx_users_roles`
--
ALTER TABLE `dx_users_roles`
  ADD CONSTRAINT `dx_fk_users_roles_roles` FOREIGN KEY (`role_id`) REFERENCES `dx_roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_users_roles_users` FOREIGN KEY (`user_id`) REFERENCES `dx_users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dx_views`
--
ALTER TABLE `dx_views`
  ADD CONSTRAINT `dx_fk_views_lists` FOREIGN KEY (`list_id`) REFERENCES `dx_lists` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dx_views_fields`
--
ALTER TABLE `dx_views_fields`
  ADD CONSTRAINT `dx_fk_views_fields_aggregation_types` FOREIGN KEY (`aggregation_id`) REFERENCES `dx_aggregation_types` (`id`),
  ADD CONSTRAINT `dx_fk_views_fields_field_operations` FOREIGN KEY (`operation_id`) REFERENCES `dx_field_operations` (`id`),
  ADD CONSTRAINT `dx_fk_views_fields_lists` FOREIGN KEY (`list_id`) REFERENCES `dx_lists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_views_fields_lists_fields` FOREIGN KEY (`field_id`) REFERENCES `dx_lists_fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_views_fields_sort_types` FOREIGN KEY (`sort_type_id`) REFERENCES `dx_sort_types` (`id`),
  ADD CONSTRAINT `dx_fk_views_fields_views` FOREIGN KEY (`view_id`) REFERENCES `dx_views` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `dx_workflows_fields`
--
ALTER TABLE `dx_workflows_fields`
  ADD CONSTRAINT `dx_fk_workflow_dield_field` FOREIGN KEY (`field_id`) REFERENCES `dx_lists_fields` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `dx_fk_workflow_field_list` FOREIGN KEY (`list_id`) REFERENCES `dx_lists` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_dx_workflows` FOREIGN KEY (`workflow_id`) REFERENCES `dx_workflows` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `in_articles`
--
ALTER TABLE `in_articles`
  ADD CONSTRAINT `in_articles_author_id_foreign` FOREIGN KEY (`author_id`) REFERENCES `in_employees` (`id`),
  ADD CONSTRAINT `in_articles_content_id_foreign` FOREIGN KEY (`content_id`) REFERENCES `in_articles_contents` (`id`),
  ADD CONSTRAINT `in_articles_picture_galery_id_foreign` FOREIGN KEY (`picture_galery_id`) REFERENCES `in_articles` (`id`),
  ADD CONSTRAINT `in_articles_video_galery_id_foreign` FOREIGN KEY (`video_galery_id`) REFERENCES `in_articles` (`id`);

--
-- Constraints for table `in_articles_files`
--
ALTER TABLE `in_articles_files`
  ADD CONSTRAINT `in_articles_files_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `in_articles` (`id`);

--
-- Constraints for table `in_articles_img`
--
ALTER TABLE `in_articles_img`
  ADD CONSTRAINT `fk_in_articles_img_article_id` FOREIGN KEY (`article_id`) REFERENCES `in_articles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `in_articles_vid`
--
ALTER TABLE `in_articles_vid`
  ADD CONSTRAINT `in_articles_vid_article_id_foreign` FOREIGN KEY (`article_id`) REFERENCES `in_articles` (`id`);

--
-- Constraints for table `in_dailyquest_answers`
--
ALTER TABLE `in_dailyquest_answers`
  ADD CONSTRAINT `in_dailyquest_answers_dailyquest_option_id_foreign` FOREIGN KEY (`dailyquest_option_id`) REFERENCES `in_dailyquest_options` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `in_dailyquest_options`
--
ALTER TABLE `in_dailyquest_options`
  ADD CONSTRAINT `in_dailyquest_options_dailyquest_question_id_foreign` FOREIGN KEY (`dailyquest_question_id`) REFERENCES `in_dailyquest_questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `in_dailyquest_questions`
--
ALTER TABLE `in_dailyquest_questions`
  ADD CONSTRAINT `in_dailyquest_questions_source_id_foreign` FOREIGN KEY (`source_id`) REFERENCES `in_sources` (`id`);

--
-- Constraints for table `in_departments`
--
ALTER TABLE `in_departments`
  ADD CONSTRAINT `in_departments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `in_departments` (`id`),
  ADD CONSTRAINT `in_departments_source_id_foreign` FOREIGN KEY (`source_id`) REFERENCES `in_sources` (`id`);

--
-- Constraints for table `in_documents`
--
ALTER TABLE `in_documents`
  ADD CONSTRAINT `fk_in_documents_doc_kind_id` FOREIGN KEY (`doc_kind_id`) REFERENCES `in_documents_kind` (`id`),
  ADD CONSTRAINT `fk_in_documents_doc_system_id` FOREIGN KEY (`doc_system_id`) REFERENCES `in_documents_lotus` (`id`),
  ADD CONSTRAINT `in_documents_doc_department_id_foreign` FOREIGN KEY (`doc_department_id`) REFERENCES `in_doc_departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `in_employees`
--
ALTER TABLE `in_employees`
  ADD CONSTRAINT `in_employees_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `in_departments` (`id`),
  ADD CONSTRAINT `in_employees_left_reason_id_foreign` FOREIGN KEY (`left_reason_id`) REFERENCES `in_left_reasons` (`id`),
  ADD CONSTRAINT `in_employees_substit_empl_id_foreign` FOREIGN KEY (`substit_empl_id`) REFERENCES `in_employees` (`id`);

--
-- Constraints for table `in_employees_history`
--
ALTER TABLE `in_employees_history`
  ADD CONSTRAINT `in_employees_history_new_department_id_foreign` FOREIGN KEY (`new_department_id`) REFERENCES `in_departments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `in_employees_history_old_department_id_foreign` FOREIGN KEY (`old_department_id`) REFERENCES `in_departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `in_events`
--
ALTER TABLE `in_events`
  ADD CONSTRAINT `in_events_source_id_foreign` FOREIGN KEY (`source_id`) REFERENCES `in_sources` (`id`);

--
-- Constraints for table `in_faq_question`
--
ALTER TABLE `in_faq_question`
  ADD CONSTRAINT `in_faq_question_faq_section_id_foreign` FOREIGN KEY (`faq_section_id`) REFERENCES `in_faq_section` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `in_faq_section_source`
--
ALTER TABLE `in_faq_section_source`
  ADD CONSTRAINT `in_faq_section_source_faq_section_id_foreign` FOREIGN KEY (`faq_section_id`) REFERENCES `in_faq_section` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `in_faq_section_source_source_id_foreign` FOREIGN KEY (`source_id`) REFERENCES `in_sources` (`id`);

--
-- Constraints for table `in_html`
--
ALTER TABLE `in_html`
  ADD CONSTRAINT `fk_in_html_source_id` FOREIGN KEY (`source_id`) REFERENCES `in_sources` (`id`);

--
-- Constraints for table `in_incidents`
--
ALTER TABLE `in_incidents`
  ADD CONSTRAINT `in_incidents_system_id_foreign` FOREIGN KEY (`system_id`) REFERENCES `in_systems` (`id`);

--
-- Constraints for table `in_processes`
--
ALTER TABLE `in_processes`
  ADD CONSTRAINT `in_processes_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `in_employees` (`id`);

--
-- Constraints for table `in_processes_log`
--
ALTER TABLE `in_processes_log`
  ADD CONSTRAINT `in_processes_log_process_id_foreign` FOREIGN KEY (`process_id`) REFERENCES `in_processes` (`id`);

--
-- Constraints for table `in_publish`
--
ALTER TABLE `in_publish`
  ADD CONSTRAINT `in_publish_publish_type_id_foreign` FOREIGN KEY (`publish_type_id`) REFERENCES `in_publish_types` (`id`);

--
-- Constraints for table `in_sources`
--
ALTER TABLE `in_sources`
  ADD CONSTRAINT `in_sources_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `in_tags` (`id`);

--
-- Constraints for table `in_systems`
--
ALTER TABLE `in_systems`
  ADD CONSTRAINT `in_systems_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `in_employees` (`id`),
  ADD CONSTRAINT `in_systems_source_id_foreign` FOREIGN KEY (`source_id`) REFERENCES `in_sources` (`id`);

--
-- Constraints for table `in_tags_article`
--
ALTER TABLE `in_tags_article`
  ADD CONSTRAINT `fk_in_tags_article_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `in_tags` (`id`),
  ADD CONSTRAINT `fk_in_tags_article_article_id` FOREIGN KEY (`article_id`) REFERENCES `in_articles` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
