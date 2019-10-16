-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 16, 2019 at 07:52 AM
-- Server version: 5.7.26
-- PHP Version: 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dling`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
CREATE TABLE IF NOT EXISTS `address` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `address` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `point` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`id`, `address`, `mobile`, `point`, `name`) VALUES
(2, 'fhfg fhf dgdfgdfgfdgfdgfd', '09367270443', '35.725820252566784,51.40535116195679', 'بیژن مرتضوی'),
(3, 'رسالت کرمان جنوبی ', '09367270445', '35.730924083863805,51.477502584457405', 'رسول مسعودی');

-- --------------------------------------------------------

--
-- Table structure for table `center`
--

DROP TABLE IF EXISTS `center`;
CREATE TABLE IF NOT EXISTS `center` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `manager` bigint(20) NOT NULL,
  `phone` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `address` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  `point` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_manager` (`manager`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `file`
--

DROP TABLE IF EXISTS `file`;
CREATE TABLE IF NOT EXISTS `file` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `filetype_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_filetype` (`filetype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `filetype`
--

DROP TABLE IF EXISTS `filetype`;
CREATE TABLE IF NOT EXISTS `filetype` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `center_id` bigint(20) NOT NULL,
  `order_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `priority` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `carrier` int(11) NOT NULL,
  `address_id` bigint(20) NOT NULL,
  `bill` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_file`
--

DROP TABLE IF EXISTS `order_file`;
CREATE TABLE IF NOT EXISTS `order_file` (
  `order_id` bigint(20) NOT NULL,
  `file_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_product`
--

DROP TABLE IF EXISTS `order_product`;
CREATE TABLE IF NOT EXISTS `order_product` (
  `order_id` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `id` bigint(20) NOT NULL,
  `title` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `price` int(11) NOT NULL,
  `extra` int(11) NOT NULL,
  `desc` varchar(4000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `category` int(11) NOT NULL,
  `fi` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `title`, `price`, `extra`, `desc`, `url`, `category`, `fi`) VALUES
(1, 'ريز نمرات دبيرستان ، پيش دانشگاهي ', 150000, 180000, NULL, 'ترجمه_رسمی_ريز_نمرات_دبيرستان_پيش_دانشگاهي', 2, b'1'),
(2, 'ريزنمرات دانشگاه ', 180000, 220000, NULL, 'ترجمه_رسمی_ريزنمرات_دانشگاه', 2, b'1'),
(3, 'كارت شناسايي', 200000, 250000, NULL, 'ترجمه_رسمی_كارت_شناسايي', 1, b'0'),
(4, 'كارت معافيت', 200000, 250000, NULL, 'ترجمه_رسمی_كارت_معافيت', 1, b'0'),
(5, 'كارت ملي', 200000, 250000, NULL, 'ترجمه_رسمی_كارت_ملي', 1, b'0'),
(6, 'شناسنامه', 250000, 320000, NULL, 'ترجمه_رسمی_شناسنامه', 1, b'0'),
(7, 'ابلاغيه، اخطار قضايي', 300000, 360000, NULL, 'ترجمه_رسمی_ابلاغيه_اخطار_قضايي', 3, b'0'),
(8, 'برگه مرخصي', 300000, 360000, NULL, 'ترجمه_رسمی_برگه_مرخصي', 1, b'0'),
(9, 'توصيه نامه تحصيلي(بعد از تحصيلات سوم راهنمايي)', 300000, 360000, NULL, 'ترجمه_رسمی_توصيه_نامه_تحصيلي_بعد_از_تحصيلات_سوم_راهنمايي', 2, b'0'),
(10, 'جواز اشتغال به كار', 300000, 360000, NULL, 'ترجمه_رسمی_جواز_اشتغال_به_كار', 5, b'0'),
(11, 'حكم بازنشستگي(كوچك)', 300000, 360000, NULL, 'ترجمه_رسمی_حكم_بازنشستگي_كوچك', 5, b'0'),
(12, 'دفترچه بيمه', 300000, 360000, NULL, 'ترجمه_رسمی_دفترچه_بيمه', 1, b'0'),
(13, 'ديپلم پايان تحصيلات متوسطه يا پيش دانشگاهي', 300000, 360000, NULL, 'ترجمه_رسمی_ديپلم_پايان_تحصيلات_متوسطه_يا_پيش_دانشگاهي', 2, b'0'),
(14, 'ريزنمرات دبستان ، راهنمايي', 300000, 360000, NULL, 'ترجمه_رسمی_ريزنمرات_دبستان_راهنمايي', 2, b'1'),
(15, 'سند تلفن همراه', 300000, 360000, NULL, 'ترجمه_رسمی_سند_تلفن_همراه', 3, b'0'),
(16, 'فيش مستمري(كوچك)', 300000, 360000, NULL, 'ترجمه_رسمی_فيش_مستمري_كوچك', 5, b'0'),
(17, 'كارت بازرگاني هوشمند', 300000, 360000, NULL, 'ترجمه_رسمی_كارت_بازرگاني_هوشمند', 1, b'0'),
(18, 'كارت عضويت نظام مهندسي', 300000, 360000, NULL, 'ترجمه_رسمی_كارت_عضويت_نظام_مهندسي', 1, b'0'),
(19, 'كارت نظام پزشكي', 300000, 360000, NULL, 'ترجمه_رسمی_كارت_نظام_پزشكي', 1, b'0'),
(20, 'كارت واكسيناسيون تا سه نوع واكسن', 300000, 360000, NULL, 'ترجمه_رسمی_كارت_واكسيناسيون_تا_سه_نوع_واكسن', 1, b'0'),
(21, 'كارت پايان خدمت', 300000, 360000, NULL, 'ترجمه_رسمی_كارت_پايان_خدمت', 1, b'0'),
(22, 'گزارش ورود و خروج از كشور', 300000, 360000, NULL, 'ترجمه_رسمی_گزارش_ورود_و_خروج_از_كشور', 3, b'0'),
(23, 'گواهي اشتغال به تحصيل', 300000, 360000, NULL, 'ترجمه_رسمی_گواهي_اشتغال_به_تحصيل', 2, b'0'),
(24, 'گواهي تجرد، تولد، فوت', 300000, 360000, NULL, 'ترجمه_رسمی_گواهي_تجرد_تولد_فوت', 1, b'0'),
(25, 'گواهينامه رانندگي', 300000, 360000, NULL, 'ترجمه_رسمی_گواهينامه_رانندگي', 1, b'0'),
(26, 'گواهي ريز نمرات دانشگاهي', 300000, 360000, NULL, 'ترجمه_رسمی_گواهي_ريز_نمرات_دانشگاهي', 2, b'0'),
(27, 'گواهي عدم خسارت خودرو (نيم برگ)', 300000, 360000, NULL, 'ترجمه_رسمی_گواهي_عدم_خسارت_خودرو_نيم_برگ', 3, b'0'),
(28, 'گواهي عدم سوءپيشينه', 300000, 360000, NULL, 'ترجمه_رسمی_گواهي_عدم_سوءپيشينه', 1, b'0'),
(29, 'گذرنامه (بدون رواديد)', 350000, 450000, NULL, 'ترجمه_رسمی_گذرنامه_بدون_رواديد', 1, b'0'),
(30, 'روزنامه رسمي تغييرات و تصميمات (كوچك)', 400000, 500000, NULL, 'ترجمه_رسمی_روزنامه_رسمي_تغييرات_و_تصميمات_كوچك', 4, b'0'),
(31, 'اساسنامه ثبت شركتها فرمي', 450000, 550000, NULL, 'ترجمه_رسمی_اساسنامه_ثبت_شركتها_فرمي', 4, b'1'),
(32, 'اوراق مشاركت و اوراق قرضه', 450000, 550000, NULL, 'ترجمه_رسمی_اوراق_مشاركت_و_اوراق_قرضه', 5, b'0'),
(33, 'انواع قبض (ماليات، پرداخت بيمه، آب، برق و غیره)', 450000, 550000, NULL, 'ترجمه_رسمی_انواع_قبض_ماليات_پرداخت_بيمه_آب_برق_و_غیره', 5, b'0'),
(34, 'برگ تشحيص ماليات ، ماليات قطعي ', 450000, 550000, NULL, 'ترجمه_رسمی_برگ_تشحيص_ماليات_ماليات_قطعي', 5, b'1'),
(35, 'برگ آزمايش پزشكي(كوچك)', 450000, 550000, NULL, 'ترجمه_رسمی_برگ_آزمايش_پزشكي_كوچك', 1, b'0'),
(36, 'برگ جلب ، احضاريه', 450000, 550000, NULL, 'ترجمه_رسمی_برگ_جلب_احضاريه', 3, b'0'),
(37, 'برگ سابقه بيمه تامين اجتماعي', 450000, 550000, NULL, 'ترجمه_رسمی_برگ_سابقه_بيمه_تامين_اجتماعي', 5, b'1'),
(38, 'پروانه دائم پزشكي', 450000, 550000, NULL, 'ترجمه_رسمی_پروانه_دائم_پزشكي', 5, b'0'),
(39, 'پروانه مطب ، پروانه مسئوليت فني', 450000, 550000, NULL, 'ترجمه_رسمی_پروانه_مطب_پروانه_مسئوليت_فني', 4, b'0'),
(40, 'پروانه نشر و انتشارات', 450000, 550000, NULL, 'ترجمه_رسمی_پروانه_نشر_و_انتشارات', 4, b'0'),
(41, 'پروانه وكالت', 450000, 550000, NULL, 'ترجمه_رسمی_پروانه_وكالت', 4, b'0'),
(42, 'پرينتهاي بانكي كوچك (تا 10 سطر)', 450000, 550000, NULL, 'ترجمه_رسمی_پرينتهاي_بانكي_كوچك_تا_10_سطر', 5, b'0'),
(43, 'تقدير نامه و لوح سپاس ، حكم قهرماني (كوچك)', 450000, 550000, NULL, 'ترجمه_رسمی_تقدير_نامه_و_لوح_سپاس_حكم_قهرماني_كوچك', 1, b'0'),
(44, 'ثبت علائم تجاري ، ثبت اختراع', 450000, 550000, NULL, 'ترجمه_رسمی_ثبت_علائم_تجاري_ثبت_اختراع', 4, b'0'),
(45, 'جواز دفن', 450000, 550000, NULL, 'ترجمه_رسمی_جواز_دفن', 1, b'0'),
(46, 'جواز كسب', 450000, 550000, NULL, 'ترجمه_رسمی_جواز_كسب', 4, b'0'),
(47, 'ريز مكالمات تلفن ', 450000, 550000, NULL, 'ترجمه_رسمی_ريز_مكالمات_تلفن', 3, b'1'),
(48, 'سر فصل دروس ', 450000, 550000, NULL, 'ترجمه_رسمی_سر_فصل_دروس', 2, b'1'),
(49, 'سند وسائط نقليه سبك', 450000, 550000, NULL, 'ترجمه_رسمی_سند_وسائط_نقليه_سبك', 3, b'0'),
(50, 'فيش حقوقي (كوچك)', 450000, 550000, NULL, 'ترجمه_رسمی_فيش_حقوقي_كوچك', 5, b'0'),
(51, 'فيش مستمري (بزرگ)', 450000, 550000, NULL, 'ترجمه_رسمی_فيش_مستمري_بزرگ', 5, b'0'),
(52, 'كارت مباشرت', 450000, 550000, NULL, 'ترجمه_رسمی_كارت_مباشرت', 4, b'0'),
(53, 'كارنامه توصيفي ابتدائي ', 450000, 550000, NULL, 'ترجمه_رسمی_كارنامه_توصيفي_ابتدائي', 2, b'1'),
(54, 'كارت واكسيناسيون بيش از سه نوع واكسن', 450000, 550000, NULL, 'ترجمه_رسمی_كارت_واكسيناسيون_بيش_از_سه_نوع_واكسن', 1, b'0'),
(55, 'گواهي اشتغال به كار بدون شرح وظايف', 450000, 550000, NULL, 'ترجمه_رسمی_گواهي_اشتغال_به_كار_بدون_شرح_وظايف', 5, b'0'),
(56, 'گواهي بانكي يا سپرده بانكي', 450000, 550000, NULL, 'ترجمه_رسمی_گواهي_بانكي_يا_سپرده_بانكي', 5, b'0'),
(57, 'گواهي پايان تحصيلات كارداني، كارشناسي، كارشناسي ارشد، دكترا', 450000, 550000, NULL, 'ترجمه_رسمی_گواهي_پايان_تحصيلات_كارداني_كارشناسي_كارشناسي_ارشد_دكترا', 2, b'0'),
(58, 'گواهي فني و حرفه اي (يك رو)', 450000, 550000, NULL, 'ترجمه_رسمی_گواهي_فني_و_حرفه_اي_يك_رو', 2, b'0'),
(59, 'گواهي عدم خسارت خودرو (تمام برگ)', 450000, 550000, NULL, 'ترجمه_رسمی_گواهي_عدم_خسارت_خودرو_تمام_برگ', 3, b'0'),
(60, 'گواهي_ها (ساير موارد)', 450000, 550000, NULL, 'ترجمه_رسمی_گواهي_ها_ساير_موارد', 3, b'1'),
(61, 'ليست بيمه كاركنان كوچك (تا ده نفر)', 450000, 550000, NULL, 'ترجمه_رسمی_ليست_بيمه_كاركنان_كوچك_تا_ده_نفر', 4, b'0'),
(62, 'حكم افزايش حقوق، حكم بازنشستگي', 500000, 620000, NULL, 'ترجمه_رسمی_حكم_افزايش_حقوق_حكم_بازنشستگي', 5, b'0'),
(63, 'آگهي تأسيس (ثبت شركتها، روزنامه رسمي)', 600000, 720000, NULL, 'ترجمه_رسمی_آگهي_تأسيس_ثبت_شركتها_روزنامه_رسمي', 4, b'0'),
(64, 'اجاره نامه، بنچاق و صلح نامه محضري ', 600000, 720000, NULL, 'ترجمه_رسمی_اجاره_نامه_بنچاق_و_صلح_نامه_محضري', 3, b'1'),
(65, 'اساسنامه، ثبت شركت غير فرمي ', 600000, 720000, NULL, 'ترجمه_رسمی_اساسنامه_ثبت_شركت_غير_فرمي', 4, b'1'),
(66, 'بيمه شخص ثالث، قرارداد بيمه ', 600000, 720000, NULL, 'ترجمه_رسمی_بيمه_شخص_ثالث_قرارداد_بيمه', 3, b'1'),
(67, 'ترازنامه شركتها، اظهار نامه مالياتي ', 600000, 720000, NULL, 'ترجمه_رسمی_ترازنامه_شركتها_اظهار_نامه_مالياتي', 4, b'1'),
(68, 'تقديرنامه، لوح سپاس و حكم قهرماني ( بزرگ)', 600000, 720000, NULL, 'ترجمه_رسمی_تقديرنامه_لوح_سپاس_و_حكم_قهرماني__بزرگ', 1, b'0'),
(69, 'پروانه پايان كار ساختمان ', 600000, 720000, NULL, 'ترجمه_رسمی_پروانه_پايان_كار_ساختمان', 4, b'1'),
(70, 'پروانه دفترچه اي يا شناسنامه ساختمان ', 600000, 720000, NULL, 'ترجمه_رسمی_پروانه_دفترچه_اي_يا_شناسنامه_ساختمان', 4, b'1'),
(71, 'پروانه مهندسي ', 600000, 720000, NULL, 'ترجمه_رسمی_پروانه_مهندسي', 4, b'1'),
(72, 'پرينتهاي بانكي بزرگ (بيش از 10 سطر)', 600000, 720000, NULL, 'ترجمه_رسمی_پرينتهاي_بانكي_بزرگ_بيش_از_10_سطر', 5, b'0'),
(73, 'حكم اعضاي هيئت علمي، حكم كارگزيني', 600000, 720000, NULL, 'ترجمه_رسمی_حكم_اعضاي_هيئت_علمي_حكم_كارگزيني', 5, b'0'),
(74, 'دفترچه بازرگاني', 600000, 720000, NULL, 'ترجمه_رسمی_دفترچه_بازرگاني', 4, b'0'),
(75, 'روزنامه رسمي تغييرات و تصميمات (بزرگ)', 600000, 720000, NULL, 'ترجمه_رسمی_روزنامه_رسمي_تغييرات_و_تصميمات_بزرگ', 4, b'0'),
(76, 'فيش حقوقي (بزرگ)', 600000, 720000, NULL, 'ترجمه_رسمی_فيش_حقوقي_بزرگ', 5, b'0'),
(77, 'قرارداد استخدامي ', 600000, 720000, NULL, 'ترجمه_رسمی_قرارداد_استخدامي', 5, b'1'),
(78, 'كارت شناسايي كارگاه ', 600000, 720000, NULL, 'ترجمه_رسمی_كارت_شناسايي_كارگاه', 4, b'1'),
(79, 'گواهي پزشكي ، گزارش پزشكي ، گزارش پزشكي قانوني ', 600000, 720000, NULL, 'ترجمه_رسمی_گواهي_پزشكي_گزارش_پزشكي_گزارش_پزشكي_قانوني', 1, b'1'),
(80, 'گواهي حصر وراثت', 600000, 720000, NULL, 'ترجمه_رسمی_گواهي_حصر_وراثت', 3, b'0'),
(81, 'ليست بيمه كاركنان بزرگ ', 600000, 720000, NULL, 'ترجمه_رسمی_ليست_بيمه_كاركنان_بزرگ', 4, b'1'),
(82, 'مبايعه نامه و اجاره نامه با كد رهگيري ', 600000, 720000, NULL, 'ترجمه_رسمی_مبايعه_نامه_و_اجاره_نامه_با_كد_رهگيري', 3, b'1'),
(83, 'موافقت اصولي', 600000, 720000, NULL, 'ترجمه_رسمی_موافقت_اصولي', 4, b'0'),
(84, 'دفترچه وكالت', 650000, 820000, NULL, 'ترجمه_رسمی_دفترچه_وكالت', 3, b'0'),
(85, 'اوراق محضري ', 750000, 900000, NULL, 'ترجمه_رسمی_اوراق_محضري', 3, b'1'),
(86, 'بارنامه ', 750000, 900000, NULL, 'ترجمه_رسمی_بارنامه', 4, b'1'),
(87, 'برگ آزمايش پزشكي (بزرگ)', 750000, 900000, NULL, 'ترجمه_رسمی_برگ_آزمايش_پزشكي_بزرگ', 1, b'0'),
(88, 'برگ سبز گمركي ', 750000, 900000, NULL, 'ترجمه_رسمی_برگ_سبز_گمركي', 3, b'1'),
(89, 'برگ نظريه كارشناسي ملك', 750000, 900000, NULL, 'ترجمه_رسمی_برگ_نظريه_كارشناسي_ملك', 3, b'0'),
(90, 'پروانه بهره برداري (پشت و رو)', 750000, 900000, NULL, 'ترجمه_رسمی_پروانه_بهره_برداري_پشت_و_رو', 4, b'0'),
(91, 'جواز تأسيس', 750000, 900000, NULL, 'ترجمه_رسمی_جواز_تأسيس', 4, b'0'),
(92, 'سند ازدواج يا رونوشت آن', 750000, 900000, NULL, 'ترجمه_رسمی_سند_ازدواج_يا_رونوشت_آن', 1, b'0'),
(93, 'سند وسائط نقليه سنگين', 750000, 900000, NULL, 'ترجمه_رسمی_سند_وسائط_نقليه_سنگين', 3, b'0'),
(94, 'سند مالكيت (دفترچه اي)', 750000, 900000, NULL, 'ترجمه_رسمی_سند_مالكيت_دفترچه_اي', 3, b'0'),
(95, 'قيمنامه ', 750000, 900000, NULL, 'ترجمه_رسمی_قيمنامه', 3, b'1'),
(96, 'گواهي اشتغال به كار با شرح وظايف', 750000, 900000, NULL, 'ترجمه_رسمی_گواهي_اشتغال_به_كار_با_شرح_وظايف', 5, b'0'),
(97, 'ماليات بر ارث ', 750000, 900000, NULL, 'ترجمه_رسمی_ماليات_بر_ارث', 5, b'1'),
(98, 'وكالتنامه (نيم برگ)', 750000, 900000, NULL, 'ترجمه_رسمی_وكالتنامه_نيم_برگ', 3, b'0'),
(99, 'اظهارنامه ، تقاضاي ثبت شركت ، شركتنامه (پشت و رو)', 1050000, 1260000, NULL, 'ترجمه_رسمی_اظهارنامه_تقاضاي_ثبت_شركت_شركتنامه_پشت_و_رو', 4, b'0'),
(100, 'اوراق قضايي ', 1050000, 1260000, NULL, 'ترجمه_رسمی_اوراق_قضايي', 3, b'1'),
(101, 'سند طلاق يا رونوشت آن', 1050000, 1260000, NULL, 'ترجمه_رسمی_سند_طلاق_يا_رونوشت_آن', 1, b'0'),
(102, 'سند مالكيت (تك برگي)', 1050000, 1260000, NULL, 'ترجمه_رسمی_سند_مالكيت_تك_برگي', 3, b'0'),
(103, 'قرارداد ', 1050000, 1260000, NULL, 'ترجمه_رسمی_قرارداد', 3, b'1'),
(104, 'وكالتنامه بزرگ ', 1050000, 1260000, NULL, 'ترجمه_رسمی_وكالتنامه_بزرگ', 3, b'1');

-- --------------------------------------------------------

--
-- Table structure for table `product_filetype`
--

DROP TABLE IF EXISTS `product_filetype`;
CREATE TABLE IF NOT EXISTS `product_filetype` (
  `product_id` bigint(20) NOT NULL,
  `filetype_id` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `score`
--

DROP TABLE IF EXISTS `score`;
CREATE TABLE IF NOT EXISTS `score` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `center_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  `mail` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `mobile` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `mail`, `mobile`, `password`) VALUES
(9, 'بیژن مرتضوی', 'r.masoudi@chmail.ir', '09367270444', '123456'),
(10, 'رسول مسعودی', 'rmrasoulmasoudi@gmail.com', '09367270443', 'x51ygv2i'),
(11, 'فصقلی', 'ppp@ooo.ll', '09375689988', 'llllll'),
(12, 'piopi', 'oo@rr.yy', '09368754236', 'yyyyyy');

-- --------------------------------------------------------

--
-- Table structure for table `user_address`
--

DROP TABLE IF EXISTS `user_address`;
CREATE TABLE IF NOT EXISTS `user_address` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `address_id` bigint(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_user_address_user` (`user_id`),
  KEY `fk_user_address_address` (`address_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `user_address`
--

INSERT INTO `user_address` (`id`, `user_id`, `address_id`) VALUES
(1, 10, 2),
(2, 10, 3);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `center`
--
ALTER TABLE `center`
  ADD CONSTRAINT `fk_manager` FOREIGN KEY (`manager`) REFERENCES `user` (`id`);

--
-- Constraints for table `file`
--
ALTER TABLE `file`
  ADD CONSTRAINT `fk_filetype` FOREIGN KEY (`filetype_id`) REFERENCES `filetype` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
