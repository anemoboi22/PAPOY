-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 09, 2025 at 12:56 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `papoydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `year` varchar(255) NOT NULL,
  `effective_year` int(4) NOT NULL,
  `semester` varchar(50) NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `descriptive_title` varchar(255) NOT NULL,
  `co_prerequisite` varchar(255) DEFAULT NULL,
  `units` int(11) NOT NULL,
  `lec_hours` int(11) NOT NULL,
  `lab_hours` int(11) DEFAULT NULL,
  `total_hours` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `admin_id`, `course_id`, `year`, `effective_year`, `semester`, `course_code`, `descriptive_title`, `co_prerequisite`, `units`, `lec_hours`, `lab_hours`, `total_hours`) VALUES
(55, 1, 91, 'First Year', 2021, '1st Semester', 'GEC-RPH', 'READINGS IN PHILIPPINE HISTORY', 'NONE', 3, 3, 0, 3),
(56, 1, 91, 'First Year', 2021, '1st Semester', 'GEC-MMW', 'MATHEMATICS IN THE MODERN WORLD', 'NONE', 3, 3, 0, 3),
(57, 1, 91, 'First Year', 2021, '1st Semester', 'GEE-TEM', 'THE ENTREPRENEURIAL MIND', 'NONE', 3, 3, 0, 3),
(58, 1, 91, 'First Year', 2021, '1st Semester', 'CC 111', 'INTRODUCTION TO COMPUTING', 'NONE', 3, 2, 3, 5),
(59, 1, 91, 'First Year', 2021, '1st Semester', 'CC 112', 'COMPUTER PROGRAMMING 1(LEC)', 'NONE', 2, 2, 0, 2),
(60, 1, 91, 'First Year', 2021, '1st Semester', 'CC 112 L', 'COMPUTER PROGRAMMING 1(LAB)', 'NONE', 3, 0, 9, 9),
(61, 1, 91, 'First Year', 2021, '1st Semester', 'AP 1', 'MULTIMEDIA', 'NONE', 3, 2, 3, 5),
(62, 1, 91, 'First Year', 2021, '1st Semester', 'PE 1', 'PHYSICAL EDUCATION 1', 'NONE', 2, 2, 0, 2),
(63, 1, 91, 'First Year', 2021, '1st Semester', 'NSTP 1', 'NATIONAL SERVICE TRAINING PROGRAM 1', 'NONE', 3, 3, 0, 3),
(64, 1, 91, 'First Year', 2021, '2nd Semester', 'GEC-PC', 'PURPOSIVE COMMUNICATION', 'NONE', 3, 3, 0, 3),
(65, 1, 91, 'First Year', 2021, '2nd Semester', 'GEC-STS', 'SCIENCE, TECHNOLOGY, AND SOCIETY', 'NONE', 3, 3, 0, 3),
(66, 1, 91, 'First Year', 2021, '2nd Semester', 'GEC-US', 'UNDERSTANDING THE SELF', 'NONE', 3, 3, 0, 3),
(67, 1, 91, 'First Year', 2021, '2nd Semester', 'CC 123', 'COMPUTER PROGRAMMING 2(LEC)', 'CC 112, CC 112 L', 2, 2, 0, 2),
(68, 1, 91, 'First Year', 2021, '2nd Semester', 'CC 123 L', 'COMPUTER PROGRAMMING 2(LAB)', 'CC 112, CC 112 L', 3, 0, 9, 9),
(69, 1, 91, 'First Year', 2021, '2nd Semester', 'PC 121', 'DISCRETE MATHEMATICS', 'NONE', 3, 3, 0, 3),
(70, 1, 91, 'First Year', 2021, '2nd Semester', 'AP 2', 'DIGITAL LOGIC DESIGN', 'CC 111', 3, 2, 3, 5),
(71, 1, 91, 'First Year', 2021, '2nd Semester', 'PE 2', 'PHYSICAL EDUCATION 2', 'NONE', 2, 2, 0, 2),
(72, 1, 91, 'First Year', 2021, '2nd Semester', 'NSTP 2', 'NATIONAL SERVICE TRAINING PROGRAM 2', 'NSTP 1', 3, 3, 0, 3),
(73, 1, 91, 'Second Year', 2021, '1st Semester', 'GEC-E', 'ETHICS', 'NONE', 3, 3, 0, 3),
(74, 1, 91, 'Second Year', 2021, '2nd Semester', 'GEC-TCW', 'THE CONTEMPORARY WORLD', 'NONE', 3, 3, 0, 3),
(75, 1, 91, 'Third Year', 2021, '1st Semester', 'GEC-FE', 'FUNCTIONAL ENGLISH', 'NONE', 3, 3, 0, 3),
(76, 1, 91, 'Third Year', 2021, '2nd Semester', 'GEC-AA', 'ART APPRECIATION', 'NONE', 3, 3, 0, 3),
(77, 1, 91, 'Fourth Year', 2021, '1st Semester', 'PC 4112', 'INFORMATION ASSURANCE AND SECURITY 2(LEC)', 'PC 3211, PC 3211 L', 2, 2, 0, 2),
(78, 1, 91, 'Fourth Year', 2021, '2nd Semester', 'PC 4215', 'ON-THE-JOB TRAINING(OJT)', '4TH YEAR STANDING', 9, 0, 0, 729),
(79, 1, 91, 'Second Year', 2021, '1st Semester', 'GEE-ES', 'ENVIRONMENTAL SCIENCE', 'NONE', 3, 3, 0, 3),
(80, 1, 91, 'Second Year', 2021, '1st Semester', 'GEC-LWR', 'LIFE AND WORKS OF RIZAL', 'NONE', 3, 3, 0, 3),
(81, 1, 91, 'First Year', 2021, '2nd Semester', 'GEE-GSPS', 'GENDER AND SOCIETY WITH PEACE STUDIES', 'NONE', 3, 3, 0, 3),
(82, 1, 91, 'Second Year', 2021, '1st Semester', 'PC 212', 'QUANTITIVE METHODS(MODELING & SIMULATION)', 'PC 121', 3, 3, 0, 3),
(83, 1, 91, 'Second Year', 2021, '1st Semester', 'CC 214', 'DATA STRUCTURES AND ALGORITHMS(LEC)', 'CC 123, CC 123 L', 2, 2, 0, 2),
(84, 1, 91, 'Second Year', 2021, '1st Semester', 'CC 214 L', 'DATA STRUCTURES AND ALGORITHMS(LAB)', 'CC 123, CC 123 L', 3, 0, 9, 9),
(85, 1, 91, 'Second Year', 2021, '1st Semester', 'P ELEC 1', 'OBJECT-ORIENTED PROGRAMMING', 'CC 123, CC 123 L, AP 1', 3, 2, 3, 5),
(86, 1, 91, 'Second Year', 2021, '1st Semester', 'P ELEC 2', 'WEB SYSTEMS AND TECHNOLOGIES', 'CC 123, CC 123 L, AP 1', 3, 2, 3, 5),
(87, 1, 91, 'Second Year', 2021, '1st Semester', 'PE 3', 'PHYSICAL EDUCATION 3', 'NONE', 2, 2, 0, 2),
(88, 1, 91, 'Third Year', 2021, '1st Semester', 'PC 315', 'NETWORKING 2(LEC)', 'PC 224', 2, 2, 0, 2),
(89, 1, 91, 'Second Year', 2021, '2nd Semester', 'PC 223', 'INTEGRATIVE PROGRAMMING AND TECHONOLOGIES 1', 'CC 123, CC 123 L', 3, 2, 0, 2),
(90, 1, 91, 'Second Year', 2021, '2nd Semester', 'PC 224', 'NETWORKING 1', 'AP 2', 3, 2, 3, 5),
(91, 1, 91, 'Second Year', 2021, '2nd Semester', 'CC 225', 'INFORMATION MANAGEMENT(LEC)', 'CC 214, CC 214 L', 2, 2, 0, 2),
(92, 1, 91, 'Second Year', 2021, '2nd Semester', 'CC 225 L', 'INFORMATION MANAGEMENT(LEC)', 'CC 214, CC 214 L', 3, 0, 9, 9),
(93, 1, 91, 'Second Year', 2021, '2nd Semester', 'P ELEC 3', 'PLATFORM TECHNOLOGIES', '2ND YEAR STANDING', 3, 2, 3, 5),
(94, 1, 91, 'Second Year', 2021, '2nd Semester', 'AP 3', 'ASP.NET', 'CC 123, CC 123 L', 3, 2, 3, 5),
(95, 1, 91, 'Second Year', 2021, '2nd Semester', 'PE 4', 'PHYSICAL EDUCATION 4', 'NONE', 2, 2, 0, 2),
(96, 1, 91, 'Third Year', 2021, '1st Semester', 'PC 315', 'NETWORKING 2(LAB)', 'PC 224', 3, 0, 9, 9),
(97, 1, 91, 'Third Year', 2021, '1st Semester', 'PC 316', 'SYSTEMS INTEGRATION AND ARCHITECTURE 1', 'PC 223', 3, 2, 3, 5),
(98, 1, 91, 'Third Year', 2021, '1st Semester', 'PC 317', 'INTRODUCTION OF HUMAN COMPUTER INTERACTION', 'AP1, CC 225, CC 225 L', 3, 2, 3, 5),
(99, 1, 91, 'Third Year', 2021, '1st Semester', 'PC 318', 'DATABASE MANAGEMENT SYSTEMS', 'CC 225, CC 225 L', 3, 2, 3, 5),
(100, 1, 91, 'Third Year', 2021, '1st Semester', 'CC 316', 'APPLICATIONS DEVELOPMENT AND EMERGING TECHNOLOGIES', 'CC 214, CC 214 L', 3, 2, 3, 5),
(101, 1, 91, 'Third Year', 2021, '2nd Semester', 'GEE-PEE', 'PEOPLE AND THE EARTH\'S ECOSYSTEMS', 'NONE', 3, 3, 0, 3),
(102, 1, 91, 'Third Year', 2021, '2nd Semester', 'PC 329', 'CAPSTONE PROJECT AND RESEARCH 1', '3RD YEAR STANDING', 3, 3, 0, 3),
(103, 1, 91, 'Third Year', 2021, '2nd Semester', 'PC 3210', 'SOCIAL AND PROFESSIONAL ISSUES', 'NONE', 3, 3, 0, 3),
(104, 1, 91, 'Third Year', 2021, '2nd Semester', 'PC 3211', 'INFORMATION ASSURANCE AND SECURITY 1(LEC)', 'PC 315, PC 315 L', 2, 2, 0, 2),
(105, 1, 91, 'Third Year', 2021, '2nd Semester', 'PC 3211 L', 'INFORMATION ASSURANCE AND SECURITY 1(LAB)', 'PC 315, PC 315 L', 3, 0, 9, 9),
(106, 1, 91, 'Third Year', 2021, '2nd Semester', 'AP 4', 'iOS MOBILE APPLICATION DEVELOMENT CROSS-PLATFORM', 'PC 223', 3, 2, 3, 5),
(107, 1, 91, 'Third Year', 2021, '2nd Semester', 'AP 5', 'TECHNOLOGY AND THE APPLICATION OF THE INTERNET OF THINGS', 'CC 316', 3, 2, 3, 5),
(108, 1, 91, 'Fourth Year', 2021, '1st Semester', 'PC 4112 L', 'INFORMATION ASSURANCE AND SECURITY 2(LAB)', 'PC 3211, PC 3211 L', 3, 0, 9, 9),
(109, 1, 91, 'Fourth Year', 2021, '1st Semester', 'PC 4113', 'SYSTEMS ADMINISTRATION AND MAINTAENANCE', 'PC 3211, PC 3211 L', 3, 2, 3, 5),
(110, 1, 91, 'Fourth Year', 2021, '1st Semester', 'P ELEC 4', 'SYSTEMS INTEGRATION AND ARCHITECTURE 2', 'PC 316', 3, 2, 3, 5),
(111, 1, 91, 'Fourth Year', 2021, '1st Semester', 'AP 6', 'CROSS-PLATFORM SCRIPT DEVELOPMENT TECHNOLOGY', 'CC 316, PC 3211, PC 3211 L', 3, 2, 3, 5),
(112, 1, 91, 'Fourth Year', 2021, '1st Semester', 'PC 4114', 'CAPSTONE PROJECT AND RESEARCH 2', 'PC 329', 3, 3, 0, 3),
(114, 1, 91, 'First Year', 2018, '1st Semester', 'GEC-RPH', 'READINGS IN PHILIPPINE HISTORY', 'NONE', 3, 3, 0, 3),
(116, 1, 92, 'First Year', 2021, '1st Semester', 'GEC-RPH', 'READINGS IN PHILIPPINE HISTORY', 'NONE', 3, 3, 0, 3),
(117, 1, 91, 'Second Year', 2018, '1st Semester', 'GEC-E', 'ETHICS', 'NONE', 3, 3, 0, 3),
(118, 1, 91, 'Third Year', 2018, '1st Semester', 'GEC-KAF', 'KOMUNIKASYON SA AKADEMIKONG FILIPINO', 'NONE', 3, 3, 0, 3),
(119, 1, 91, 'Fourth Year', 2018, '1st Semester', 'PC 4112', 'INFORMATION ASSURANCE AND SECURITY 2(LEC)', 'PC 3211, PC 3221 L', 2, 2, 0, 2),
(120, 1, 91, 'Fourth Year', 2018, '2nd Semester', 'PC 4215', 'PRACTICUM', '4TH YEAR STANDING', 13, 0, 0, 702),
(121, 1, 91, 'First Year', 2018, '1st Semester', 'GEC-MMW', 'MATHEMATICS IN THE MODERN WORLD', 'NONE', 3, 3, 0, 3),
(122, 1, 91, 'First Year', 2018, '1st Semester', 'GEE-TEM', 'THE ENTREPRENEURIRAL MIND', 'NONE', 3, 3, 0, 3),
(123, 1, 91, 'First Year', 2018, '1st Semester', 'CC 111', 'INTRODUCTION TO COMPUTING', 'NONE', 3, 2, 3, 5),
(124, 1, 91, 'First Year', 2018, '1st Semester', 'CC 112', 'PROGRAMMING 1(LEC)', 'NONE', 2, 2, 0, 2),
(125, 1, 91, 'First Year', 2018, '1st Semester', 'CC 112 L', 'PROGAMMING 1(LAB)', 'NONE', 3, 0, 9, 9),
(126, 1, 91, 'First Year', 2018, '1st Semester', 'AP 1', 'MULTIMEDIA', 'NONE', 3, 2, 3, 5),
(127, 1, 91, 'First Year', 2018, '1st Semester', 'PE 1', 'PHYSICAL EDUCATION', 'NONE', 2, 2, 0, 2),
(128, 1, 91, 'First Year', 2018, '1st Semester', 'NSTP 1', 'NATIONAL SERVICE TRAINING PROGRAM 1', 'NONE', 3, 3, 0, 3),
(129, 1, 91, 'First Year', 2018, '2nd Semester', 'GEC-PC', 'PURPOSIVE COMMUNICATION', 'NONE', 3, 3, 0, 3),
(130, 1, 91, 'First Year', 2018, '2nd Semester', 'GEC-STS', 'SCIENCE, TECHNOLOGY AND SOCIETY', 'NONE', 3, 3, 0, 3),
(131, 1, 91, 'First Year', 2018, '2nd Semester', 'GEC-US', 'UNDERSTANDING THE SELF', 'NONE', 3, 3, 0, 3),
(132, 1, 91, 'First Year', 2018, '2nd Semester', 'GEE-RRES', 'RELIGIONS, RELIGOUS EXPERIENCES AND SPIRITUALITY', 'NONE', 3, 3, 0, 3),
(133, 1, 91, 'First Year', 2018, '2nd Semester', 'CC 123', 'PROGRAMMING 2(LEC)', 'CC 112, CC 112 L', 2, 2, 0, 2),
(134, 1, 91, 'First Year', 2018, '2nd Semester', 'CC 123 L', 'PROGRAMMING 2(LAB)', 'CC 112, CC 122 L', 3, 0, 9, 9),
(135, 1, 91, 'First Year', 2018, '2nd Semester', 'PC 121', 'DISCRETE MATHEMATICS', 'GEC-MMW', 3, 0, 3, 3),
(136, 1, 91, 'First Year', 2018, '2nd Semester', 'AP 2', 'DIGITAL LOGIC DESIGN', 'CC 112, CC 112 L', 3, 2, 3, 5),
(137, 1, 91, 'First Year', 2018, '2nd Semester', 'PE 2', 'PHYSICAL EDUCATION 2', 'NONE', 2, 2, 0, 2),
(138, 1, 91, 'First Year', 2018, '2nd Semester', 'NSTP 2', 'NATIONAL SERVICE TRAINING PROGRAM 2', 'NSTP 1', 3, 3, 0, 3),
(139, 1, 91, 'Second Year', 2018, '1st Semester', 'GEE-ES', 'ENVIRONMENTAL SCIENCE', 'NONE', 3, 3, 0, 3),
(140, 1, 91, 'Second Year', 2018, '1st Semester', 'GEC-LWR', 'LIFE AND WORKS OF RIZAL', 'NONE', 3, 3, 0, 3),
(141, 1, 91, 'Second Year', 2018, '1st Semester', 'PC 212', 'QUANTITATIVE METHODS', '2ND YEAR STANDING', 3, 3, 0, 3),
(142, 1, 91, 'Second Year', 2018, '1st Semester', 'CC 214', 'DATA STRUCTURES AND ALGORITHMS(LEC)', 'CC 123, CC 123 L', 2, 2, 0, 2),
(143, 1, 91, 'Second Year', 2018, '1st Semester', 'CC 214 L', 'DATA STRUCTURES AND ALGORITHMS(LAB)', 'CC 123, CC 123 L', 3, 0, 9, 9),
(144, 1, 91, 'Second Year', 2018, '1st Semester', 'P ELEC 1', 'PROFESSIONAL ELECTIVE 1', '2ND YEAR STANDING', 3, 2, 3, 5),
(145, 1, 91, 'Second Year', 2018, '1st Semester', 'P ELEC 2', 'PROFESSIONAL ELECTIVE 2', '2ND YEAR STANDING', 3, 2, 3, 5),
(146, 1, 91, 'Second Year', 2018, '1st Semester', 'PE 3', 'PHYSICAL EDUCATION 3', 'NONE', 2, 2, 0, 2),
(147, 1, 91, 'Second Year', 2018, '2nd Semester', 'GEC-TCW', 'THE CONTEMPORARY WORLD', 'NONE', 3, 3, 0, 3),
(148, 1, 91, 'Second Year', 2018, '2nd Semester', 'PC 223', 'INTEGRATIVE AND TECHNOLOGIES 1', 'CC 123, CC 123 L', 3, 2, 3, 5),
(149, 1, 91, 'Second Year', 2018, '2nd Semester', 'PC 224', 'NETWORKING 1', 'AP 2', 3, 2, 3, 5),
(150, 1, 91, 'Second Year', 2018, '2nd Semester', 'CC 225', 'INFORMATION MANAGEMENT(LEC)', 'CC 214, CC 214 L', 2, 2, 0, 2),
(151, 1, 91, 'Second Year', 2018, '2nd Semester', 'CC 225 L', 'INFORMATION MANAGEMENT(LAB)', 'CC 214, CC 214 L', 3, 0, 9, 9),
(152, 1, 91, 'Second Year', 2018, '2nd Semester', 'P ELEC 3', 'PROFESSIONAL ELECTIVE 3', '2ND YEAR STANDING', 3, 2, 3, 5),
(153, 1, 91, 'Second Year', 2018, '2nd Semester', 'AP 3', 'ASP.NET', 'CC 112, CC 112 L', 3, 2, 3, 5),
(154, 1, 91, 'Second Year', 2018, '2nd Semester', 'PE 4', 'PHYSICAL EDUCATION 4', 'NONE', 2, 2, 0, 2),
(155, 1, 91, 'Third Year', 2018, '1st Semester', 'PC 315', 'NETWORKING 2(LEC)', 'PC 224', 2, 2, 0, 2),
(156, 1, 91, 'Third Year', 2018, '1st Semester', 'PC 315 L', 'NETWORKING 2(LAB)', 'PC 224', 3, 0, 9, 9),
(157, 1, 91, 'Third Year', 2018, '1st Semester', 'PC 316', 'SYSTEMS INTEGRATION AND ARCHITECTURE 1', 'PC 223', 3, 2, 3, 5),
(158, 1, 91, 'Third Year', 2018, '1st Semester', 'PC 317', 'INTRODUCTION TO HUMAN COMPUTER INTERACTION', 'CC 112, CC 112 L', 3, 2, 3, 5),
(159, 1, 91, 'Third Year', 2018, '1st Semester', 'PC 318', 'DATABASE MANAGEMENT SYSTEMS', 'CC 225, CC 225 L', 3, 2, 3, 5),
(160, 1, 91, 'Third Year', 2018, '1st Semester', 'CC 316', 'APPLICATIONS DEVELOPMENT AND EMERGING TECHNOLOGIES', 'CC 112', 3, 2, 3, 5),
(161, 1, 91, 'Third Year', 2018, '2nd Semester', 'GEC-AA', 'ART APPRECIATION', 'NONE', 3, 3, 0, 3),
(162, 1, 91, 'Third Year', 2018, '2nd Semester', 'GEC-PPTP', 'PAGBASA AT PAGSULAT TUNGO SA PANANALIKSIK', 'GEC-KAF', 3, 3, 0, 3),
(163, 1, 91, 'Third Year', 2018, '2nd Semester', 'PC 329', 'CAPSTONE PROJECT AND RESEARCH 1(TECHNOPRENEURSHIP 1)', 'CC 316', 3, 3, 0, 3),
(164, 1, 91, 'Third Year', 2018, '2nd Semester', 'PC 3210', 'SOCIAL AND PROFESSIONAL ISSUES', '3RD YEAR STANDING', 3, 3, 0, 3),
(165, 1, 91, 'Third Year', 2018, '2nd Semester', 'PC 3211', 'INFORMATION ASSURANCE AND SECURITY 1(LEC)', 'PC 224', 2, 2, 0, 2),
(166, 1, 91, 'Third Year', 2018, '2nd Semester', 'PC 3211 L', 'INFORMATION ASSURANCE AND SECURITY 1(LAB)', 'PC 224', 3, 0, 9, 9),
(167, 1, 91, 'Third Year', 2018, '2nd Semester', 'AP 4', 'iOS MOBILE APPLICATION DEVELOPMENT CROSS-PLATFORM', 'PC 223', 3, 2, 3, 5),
(168, 1, 91, 'Third Year', 2018, '2nd Semester', 'AP 5', 'TECHNOLOGY AND APPLICATION OF THE INTERNET OF THINGS', '3RD YEAR STANDING', 3, 2, 3, 5),
(169, 1, 91, 'Fourth Year', 2018, '1st Semester', 'PC 4112 L', 'INFORMATION ASSURANCE AND SECURITY 2(LAB)', 'PC 3211, PC 3221 L', 3, 0, 9, 9),
(170, 1, 91, 'Fourth Year', 2018, '1st Semester', 'PC 4113', 'SYSTEMS ADMINISTRATION AND MAINTENANCE', 'PC 3211, PC 3211 L', 3, 2, 3, 5),
(171, 1, 91, 'Fourth Year', 2018, '1st Semester', 'PC 4114', 'CAPSTONE PROJECT AND RESEARCH 2(TECHNOPRENEURSHIP 2)', 'PC 329', 3, 3, 0, 3),
(172, 1, 91, 'Fourth Year', 2018, '1st Semester', 'P ELEC 4', 'PROFESSIONAL ELECTIVE 4', '4TH YEAR STANDING', 3, 2, 3, 5),
(173, 1, 91, 'Fourth Year', 2018, '1st Semester', 'AP 6', 'CROSS-PLATFORM SCRIPT DEVELOPMENT TECHNOLOGY', 'CC 3210, PC 3211, PC 3211 L', 3, 2, 3, 5),
(174, 1, 61, 'First Year', 2021, '1st Semester', 'EMATH 111', 'CALCULUS 1', 'NONE', 5, 5, 0, 5),
(175, 1, 61, 'First Year', 2021, '1st Semester', 'ECHEM', 'CHEMISTRY FOR ENGINEERING (LEC)', 'NONE', 3, 3, 0, 3),
(176, 1, 61, 'First Year', 2021, '1st Semester', 'ECHEML', 'CHEMISTRY FOR ENGINEERING (LAB)', 'NONE', 1, 0, 3, 3),
(177, 1, 61, 'First Year', 2021, '1st Semester', 'BES-CFP', 'COMPUTER FUNDAMENTALS AND PROGRAMMING', 'NONE', 2, 0, 6, 6),
(178, 1, 61, 'First Year', 2021, '1st Semester', 'IE-IPC 111', 'INTRODUCTION TO ENGINEERING', 'NONE', 2, 1, 3, 4),
(179, 1, 61, 'First Year', 2021, '1st Semester', 'IE-AC 111', 'PRINCIPLES OF ECONOMICS', 'NONE', 3, 3, 0, 3),
(180, 1, 61, 'First Year', 2021, '1st Semester', 'IE-TECH 111', 'PNEUMATICS AND PROGRAMMABLE LOGIC CONTROLLER', 'NONE', 3, 0, 9, 9),
(181, 1, 61, 'First Year', 2021, '1st Semester', 'PE 1', 'PHYSICAL EDUCATION 1', 'NONE', 2, 2, 0, 2),
(182, 1, 61, 'First Year', 2021, '1st Semester', 'NSTP 1', 'NATIONAL SERVICE TRAINING PROGRAM', 'NONE', 3, 3, 0, 3),
(183, 1, 61, 'First Year', 2021, '2nd Semester', 'EMATH 122', 'CALCULUS 2', 'EMATH 111', 5, 5, 0, 5),
(184, 1, 61, 'First Year', 2021, '2nd Semester', 'EPHYS', 'PHYSICS FOR ENGINEERS (LEC)', 'EMATH 111', 3, 3, 0, 3),
(185, 1, 61, 'First Year', 2021, '2nd Semester', 'EPHYSL', 'PHYSICS FOR ENGINEERS (LAB)', 'EMATH 111', 1, 0, 3, 3),
(186, 1, 61, 'First Year', 2021, '2nd Semester', 'BES-CAD', 'COMPUTER-AIDED DRAFTING ', 'NONE', 1, 0, 3, 3),
(187, 1, 61, 'First Year', 2021, '2nd Semester', 'IE - PC 121', 'STATISTICAL ANALYSIS  FOR INDUSTRIAL ENGINEERING 1', 'NONE', 3, 3, 0, 3),
(188, 1, 61, 'First Year', 2021, '2nd Semester', 'IE - IAC 121', 'BASIC ACCOUNTING', 'NONE', 3, 3, 0, 3),
(189, 1, 61, 'First Year', 2021, '2nd Semester', 'GEC - PC', 'PURPOSIVE COMMUNICATION', 'NONE', 3, 3, 0, 3),
(190, 1, 61, 'First Year', 2021, '2nd Semester', 'GEC - US', 'UNDERSTANDING THE SELF', 'NONE', 3, 3, 0, 3),
(191, 1, 61, 'First Year', 2021, '2nd Semester', 'PE 2', 'PHYSICAL EDUCATION 2', 'NONE', 2, 2, 0, 2),
(192, 1, 61, 'First Year', 2021, '2nd Semester', 'NSTP 2', 'NATIONAL SERVICE TRAINING PROGRAM 2', 'NSTP 1', 3, 3, 0, 3),
(193, 1, 61, 'Second Year', 2021, '1st Semester', 'EMATH 213', 'DIFFERENTIAL EQUATIONS', 'EMATH 122', 3, 3, 0, 3),
(194, 1, 61, 'Second Year', 2021, '1st Semester', 'BES-EMECH', 'ENGINEERING MECHANICS', 'EPHYS', 3, 3, 0, 3),
(195, 1, 61, 'Second Year', 2021, '1st Semester', 'IE - PC 212', 'INDUSTRIAL MATERIALS AND PROCESSES ', 'ECHEM & EPHYS', 3, 3, 0, 3),
(196, 1, 61, 'Second Year', 2021, '1st Semester', 'IE - PC 212L', 'INDUSTRIAL MATERIALS AND PROCESSES (LAB)', 'ECHEML & EPHYSL', 2, 0, 6, 6),
(197, 1, 61, 'Second Year', 2021, '1st Semester', 'IE - PC 213', 'STATISTICAL ANALYSIS FOR INDUSTRIAL ENGINEERING 2', 'IE - PC 121', 3, 3, 0, 3),
(198, 1, 61, 'Second Year', 2021, '1st Semester', 'IE - PC 214', 'INDUSTRIAL ORGANIZATION AND MANAGEMENT', '2nd Year Standing', 3, 3, 0, 3),
(199, 1, 61, 'Second Year', 2021, '1st Semester', 'IE - AC 212', 'FINANCIAL ACCOUNTING', 'IE - IAC 121', 3, 3, 0, 3),
(200, 1, 61, 'Second Year', 2021, '1st Semester', 'GEC-MMW', 'MATHEMATICS IN THE MODERN WORLD', 'NONE', 3, 3, 0, 3),
(201, 1, 61, 'Second Year', 2021, '1st Semester', 'GEE - TEM', 'THE ENTREPRENEURAL MIND', 'NONE', 3, 3, 0, 3),
(202, 1, 61, 'Second Year', 2021, '1st Semester', 'PE 3', 'PHYSICAL EDUCATION 3', 'NONE', 2, 2, 0, 2),
(203, 1, 61, 'Second Year', 2021, '2nd Semester', 'IE - PC 225', 'ADVANCE MATHEMATICS FOR INDUSTRIAL ENGINEERING', 'EMATH 213', 3, 3, 0, 3),
(204, 1, 61, 'Second Year', 2021, '2nd Semester', 'IE - PC 226', 'WORK STUDY AND MEASUREMENT', 'IE-PC 212, IE-PC 213, IE-PC 214', 4, 3, 3, 6),
(205, 1, 61, 'Second Year', 2021, '2nd Semester', 'IE - PC 227', 'INFORMATION SYSTEM', 'BES-CFP', 3, 3, 0, 3),
(206, 1, 61, 'Second Year', 2021, '2nd Semester', 'IE - PC 228', 'SYSTEM DYNAMICS', 'NONE', 3, 3, 0, 3),
(207, 1, 61, 'Second Year', 2021, '2nd Semester', 'IE - PE 221', 'PROJECT MANAGEMENT', '2nd Year Standing', 3, 3, 0, 3),
(208, 1, 61, 'Second Year', 2021, '2nd Semester', 'BES-EE', 'ENGINEERING ECONOMICS', '2nd Year Standing', 3, 3, 0, 3),
(209, 1, 61, 'Second Year', 2021, '2nd Semester', 'GEC - TCW', 'THE CONTEMPORARY WORLD', 'NONE', 3, 3, 0, 3),
(210, 1, 61, 'Second Year', 2021, '2nd Semester', 'GEC - LWR', 'LIFE AND WORKS OF RIZAL', 'NONE', 3, 3, 0, 3),
(211, 1, 61, 'Second Year', 2021, '2nd Semester', 'GEE - LIE', 'LIVING IN THE IT ERA', 'NONE', 3, 3, 0, 3),
(212, 1, 61, 'Second Year', 2021, '2nd Semester', 'PE 4', 'PHYSICAL EDUCATION 4', 'NONE', 2, 2, 0, 2),
(213, 1, 61, 'Third Year', 2021, '1st Semester', 'IE - PC 319', 'OPERATION RESEARCH 1', 'IE-PC 225', 3, 3, 0, 3),
(214, 1, 61, 'Third Year', 2021, '1st Semester', 'IE - PC 3110', 'QUALITY MANAGEMENT SYSTEM', 'IE-PC 213, IE-PC 226', 3, 3, 0, 3),
(215, 1, 61, 'Third Year', 2021, '1st Semester', 'IE - PC 3111', 'ERGONOMICS 1', 'IE-PC 226', 3, 2, 3, 5),
(216, 1, 61, 'Third Year', 2021, '1st Semester', 'IE - PC 3112', 'OPERATIONS MANAGEMENT 1', 'IE-PC 319; IE-PC 3110', 3, 3, 0, 3),
(217, 1, 61, 'Third Year', 2021, '1st Semester', 'IE - AC 313', 'MANAGERIAL ACCOUNTING', 'IE-AC 212', 3, 3, 0, 3),
(218, 1, 61, 'Third Year', 2021, '1st Semester', 'IE - AC 314', 'THERMODYNAMICS', 'EMATH 122', 3, 3, 0, 3),
(219, 1, 61, 'Third Year', 2021, '1st Semester', 'BES - T', 'TECHNOPRENEURSHIP 101', 'NONE', 3, 3, 0, 3),
(220, 1, 61, 'Third Year', 2021, '1st Semester', 'BES - OSH', 'BASIC OCCUPATIONAL SAFETY AND HEALTH', '3rd Year Standing', 3, 3, 0, 3),
(221, 1, 61, 'Third Year', 2021, '1st Semester', 'IE - IPC 312', 'METHODOLOGY OF RESEARCH', '3rd Year Standing', 3, 2, 3, 5),
(222, 1, 61, 'Third Year', 2021, '2nd Semester', 'IE - PC 3213', 'OPRATIONAL RESEARCH 2', 'IE-PC 319', 3, 3, 0, 3),
(223, 1, 61, 'Third Year', 2021, '2nd Semester', 'IE - PC 3214', 'OPERATIONAL MANAGEMENT 2', 'IE-IPC 3112', 4, 3, 3, 6),
(224, 1, 61, 'Third Year', 2021, '2nd Semester', 'IE - PC 3215', 'ERGONOMICS 2', 'IE-PC 3111', 3, 2, 3, 5),
(225, 1, 61, 'Third Year', 2021, '2nd Semester', 'IE - IPC 323', 'RESEARCH WRITING', 'IE-IPC 312', 2, 0, 0, 0),
(226, 1, 61, 'Third Year', 2021, '2nd Semester', 'IE - IPC 324/BA-M211', 'MARKETING MANAGEMENT ', '3rd Year Standing', 3, 3, 0, 3),
(227, 1, 61, 'Third Year', 2021, '2nd Semester', 'IE - PE 322', 'ENTERPRISE RESOURCE PLANNING', 'IE-PC 227', 3, 2, 1, 3),
(228, 1, 61, 'Third Year', 2021, '2nd Semester', 'GEC - E', 'ETHICS', 'NONE', 3, 3, 0, 3),
(229, 1, 61, 'Third Year', 2021, '2nd Semester', 'GEE-ES', 'ENVIRONMENTAL SCIENCE', 'NONE', 3, 3, 0, 3),
(230, 1, 61, 'Third Year', 2021, 'Summer', 'IE-PC 400', 'IE INDUSTRY IMMERSION', '4th Year Standing', 3, 0, 0, 360),
(231, 1, 61, 'Fourth Year', 2021, '1st Semester', 'IE - PC 4116', 'PROJECT FEASIBILITY', 'IE-AC 314, IE-PC 3214', 3, 2, 3, 5),
(232, 1, 61, 'Fourth Year', 2021, '1st Semester', 'IE - PC 4117', 'SUPPLLY CHAIN MANAGEMENT', 'IE-PC 3214', 3, 3, 0, 3),
(233, 1, 61, 'Fourth Year', 2021, '1st Semester', 'IE - PC 4118', 'SYSTEM ENGINEERING', '4th Year Standing', 3, 3, 0, 3),
(234, 1, 61, 'Fourth Year', 2021, '1st Semester', 'IE-PE-413', 'PACKAGING TECHNOLOGY', '4th Year Standing', 3, 3, 0, 3),
(235, 1, 61, 'Fourth Year', 2021, '1st Semester', 'IE - AC 415', 'ELEMENTARY ELECTRICAL ENGINEERING', 'EPHYS', 3, 3, 0, 3),
(236, 1, 61, 'Fourth Year', 2021, '1st Semester', 'IE - AC 416', 'ENVIRONMENTAL ENGINEERING SCIENCES', 'GEE-ES', 3, 3, 0, 3),
(237, 1, 61, 'Fourth Year', 2021, '1st Semester', 'GEC-RPH', 'READINGS IN THE PHILIPPINE HISTORY', 'None', 3, 3, 0, 3),
(238, 1, 61, 'Fourth Year', 2021, '2nd Semester', 'IE - PC 4219', 'IE CAPSTONE PROJECT', '4th Year Standing', 3, 1, 6, 7),
(239, 1, 61, 'Fourth Year', 2021, '2nd Semester', 'IE - PC 4220', 'ENGINEERING VALUES AND ETHICS', '4th Year Standing', 3, 3, 0, 3),
(240, 1, 61, 'Fourth Year', 2021, '2nd Semester', 'IE - IPC 425', 'HUMAN RESOURCES PLANNING', '4th Year Standing', 3, 3, 0, 3),
(241, 1, 61, 'Fourth Year', 2021, '2nd Semester', 'IE - PE 424', 'LEAN SIX SIGMA ', '4th Year Standing', 3, 3, 0, 3),
(242, 1, 61, 'Fourth Year', 2021, '2nd Semester', 'IE - PE 425', 'INTELLECTUAL PROPERTY RIGHTS ', '4th Year Standing', 3, 3, 0, 3),
(243, 1, 61, 'Fourth Year', 2021, '2nd Semester', 'GEC - AA', 'ART APPRECIATION', 'NONE', 3, 3, 0, 3),
(244, 1, 61, 'Fourth Year', 2021, '2nd Semester', 'GEC - STS', 'SCIENCE, TECHNOLOGY AND SOCIETY', 'NONE', 3, 3, 0, 3),
(256, 1, 114, 'First Year', 2021, '1st Semester', 'GEC-RPH', 'READINGS IN PHILIPPINE HISTORY', 'NONE', 3, 3, 0, 3);

-- --------------------------------------------------------

--
-- Table structure for table `course_history`
--

CREATE TABLE `course_history` (
  `history_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `old_courseID` int(11) DEFAULT NULL,
  `course_name` varchar(255) DEFAULT NULL,
  `start_date` year(4) DEFAULT NULL,
  `end_date` year(4) DEFAULT NULL,
  `years_stayed` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `grade` varchar(20) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grades`
--

INSERT INTO `grades` (`id`, `user_id`, `course_id`, `grade`, `created_at`, `updated_at`) VALUES
(752, 79, 55, '1.7', '2025-01-20 14:55:50', NULL),
(753, 79, 56, '1.8', '2025-01-20 14:55:50', NULL),
(754, 79, 57, '2.4', '2025-01-20 14:55:50', NULL),
(755, 79, 58, '2.5', '2025-01-20 14:55:50', NULL),
(756, 79, 59, '2.6', '2025-01-20 14:55:50', '2025-02-03 16:39:59'),
(757, 79, 60, '3.0', '2025-01-20 14:55:50', NULL),
(758, 79, 61, 'INC', '2025-01-20 14:55:50', '2025-01-20 15:05:45'),
(759, 79, 62, '2.5', '2025-01-20 14:55:50', NULL),
(760, 79, 63, '2.1', '2025-01-20 14:55:50', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `grades_summary`
--

CREATE TABLE `grades_summary` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `grade_id` int(11) NOT NULL,
  `year` varchar(20) NOT NULL,
  `semester` varchar(20) NOT NULL,
  `total_units` int(5) NOT NULL,
  `total_grade` decimal(3,1) DEFAULT NULL,
  `gpa` decimal(3,1) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `time` varchar(255) NOT NULL,
  `type` enum('info','success','warning','error') NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `action` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `message`
--

INSERT INTO `message` (`id`, `user_id`, `message`, `time`, `type`, `is_read`, `created_at`, `updated_at`, `action`) VALUES
(736, 79, 'Your scholarship is active.', 'It lasts for 1 years, 11 months, and 10 days.', 'info', 1, '2025-01-20 06:53:22', '2025-02-01 03:31:46', NULL),
(737, 79, 'Your expected graduation date has passed, and no extension year is recorded.', '02:53 PM', 'error', 1, '2025-01-20 06:53:22', '2025-01-20 06:53:43', NULL),
(738, 79, 'You are still enrolled but have not graduated. Please ensure your grades are in order.', '02:54 PM', 'warning', 1, '2025-01-20 06:54:15', '2025-01-20 07:00:29', NULL),
(739, 79, 'The course: COMPUTER PROGRAMMING 1(LEC) has a missing or incomplete grade (INC).', '02:55 PM', 'warning', 1, '2025-01-20 06:55:50', '2025-02-01 03:31:46', NULL),
(740, 79, 'The course: MULTIMEDIA has a missing or incomplete grade (INC).', '03:05 PM', 'warning', 1, '2025-01-20 07:05:45', '2025-02-01 03:31:46', NULL),
(741, 79, 'Your scholarship is active.', 'It lasts for 1 years, 10 months, and 29 days.', 'info', 1, '2025-02-01 03:31:40', '2025-02-01 03:31:46', NULL),
(742, 79, 'You are still enrolled but have not graduated. Please ensure your grades are in order.', '11:31 AM', 'warning', 1, '2025-02-01 03:31:40', '2025-02-01 03:31:46', NULL),
(743, 79, 'The course: COMPUTER PROGRAMMING 1(LEC) has a missing or incomplete grade (INC).', '11:31 AM', 'warning', 1, '2025-02-01 03:31:40', '2025-02-01 03:31:46', NULL),
(744, 79, 'The course: MULTIMEDIA has a missing or incomplete grade (INC).', '11:31 AM', 'warning', 1, '2025-02-01 03:31:40', '2025-02-01 03:31:46', NULL),
(745, 79, 'Your scholarship is active.', 'It lasts for 1 years, 10 months, and 27 days.', 'info', 0, '2025-02-03 08:38:27', '2025-02-03 08:38:27', NULL),
(746, 79, 'You are still enrolled but have not graduated. Please ensure your grades are in order.', '04:38 PM', 'warning', 0, '2025-02-03 08:38:27', '2025-02-03 08:38:27', NULL),
(747, 79, 'The course: COMPUTER PROGRAMMING 1(LEC) has a missing or incomplete grade (INC).', '04:38 PM', 'warning', 0, '2025-02-03 08:38:27', '2025-02-03 08:38:27', NULL),
(748, 79, 'The course: MULTIMEDIA has a missing or incomplete grade (INC).', '04:38 PM', 'warning', 0, '2025-02-03 08:38:27', '2025-02-03 08:38:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `overall_summary`
--

CREATE TABLE `overall_summary` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_units` int(11) NOT NULL,
  `gwa` decimal(3,2) DEFAULT NULL,
  `grades_summary_id` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `ID` int(10) NOT NULL,
  `AdminName` varchar(120) DEFAULT NULL,
  `UserName` varchar(120) DEFAULT NULL,
  `MobileNumber` varchar(20) DEFAULT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Password` varchar(200) DEFAULT NULL,
  `campus_number` int(11) DEFAULT NULL,
  `AdminRegdate` timestamp NULL DEFAULT current_timestamp(),
  `ProfileImage` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`ID`, `AdminName`, `UserName`, `MobileNumber`, `Email`, `Password`, `campus_number`, `AdminRegdate`, `ProfileImage`) VALUES
(1, 'admin', 'Jan Benedict Mondejar', '639632116717', 'admin12345@gmail.com', '7d276cd540df7140aa713fd73f85807d', 5, '2019-10-11 04:36:52', 0x89504e470d0a1a0a0000000d4948445200000200000002000806000000f478d4fa0000000473424954080808087c086488000000097048597300000b1300000b1301009a9c180000001974455874536f667477617265007777772e696e6b73636170652e6f72679bee3c1a0000200049444154789cecdd777c9bd5bdf8f1cf916cd9b22def38ce72f62201024909615c42ca8606c22dab3f12a0a5b717e808e3b6b484db16687b694b499965930452287b176853282bac4012b2c9b2e3784f79ca929edf1f4a9cc47b483ae791beefd7cb2f6c59cff7f912c93adfe79cf39ca32ccb42086136f5f9bfdd400ea81c20078b9c437e0e7da5629104ca0524edfb72814a0a3dce418fef7b4ee8cfbf15942ff45f5a011f16ada042dfb73faef63d4e235015fa52a1ff5a87fe6c1d37ab393aff3242888152520008a18ffae21d17300a8bb1a0c600638131403ea8831a7adcfb8e08fda7fdcf561d1ad0eae231d441cfef705ce4e234d35e18a82aa014d805ec04b50b8b9d409175c2d1be8e118510d12105801011a4be5ce5000ada1b778bd07ff7ff0cc30187410d7734e30481bdec2f0a6027d6414502145a27ce08763ca310223ca40010224cd4da7fe60247803a1c8b2342df330d701bd2e0da2d4e33b0015887c53a50eb8175d67f1c59d9f1682144ff490120443fa975ff7001538123420dbd3a9c50633f6cdf33ecdae0da254e09ec2b082cd685be679375d211329c20443f480120442fd4fab7c763713c7002a839c014200130bda18ca7387e6033a88f80f7b1f8c09a7bf8f68e6711421c200580100751ebdf4a008e82f606ff7820df80064ee2f43f4e29a80f80f7810f802facb9d3fd1d8f10225e490120e29afaeacd74600e703ca813b0980da41cf48cd07fcc6ce0244effe23461f131a8fd05c147d6c9d3ea3b4610225e480120e28adaf07707700c1667823a03980538f6fdd6ee0d9cc4e95f9c20f019a8bf63f106f08935ef30b9eb40c40d290044cc531bdec807753a7006701a906d78c32471f4c4a906de02fe0eea4d6bded4d28ed185882552008898a336be9e00ea38e00c2cce0066803af453df5e0d93c4897e1c0bf8128bbf132a083eb4be3945e60f889822058088096ae3eb19c079c07ce01450e980490d8ac4b1779c7ae01fc0cbc08bd637a7d4753c4208bb910240d896daf49a07980fea222c4e27b4d6fdfedf86fe636e832271ec1bc787c59ba09e065eb64e99eced78b41076200580b015b5e9d55450e70017016702c931d0a0481cfbc66901de009e06f5aa75caa4c68e918430951400c2786af3ab6ee02ce0222cce0695d2e1192634041247e23461f11af034f0ba75ea24d91151184d0a006124b5f91507701aa845c0b78034c02e0d81c491380dc02ba096036f59a74e94db0b8571a4001046515b5e1989c57781ef11da45efd027d8af21903812a71078048b47add326eee9f80c217491024068a7b6bcec04ce06be0fea4c2c9c07fdf6d027dbbb219038f11a27744c00d41bc043c06bd66913021d9f2d4434490120b4515b5e1e4be84aff0a60f8be473b7c881af2012e7124ce60e2743e662ff018f08875da849d1d8f12221aa4001051a5b6be94089c0bfc17963a854e9fa25200489c188cd3ed315858ea1fc083c04bd6e9e3db3a3e4188489102404485dafa622ea8ab816b803c00ac4e1f864801207162324eb7c770f0df4139702fa8fbacd3c755767ea210e12505808828b5edc589585c075c06ca7dc82fa5009038f112a7db63e8e2ef403503cbb0f89375c6b86d9d0f10223ca4001011a1b6bd7802703d301feba0ddf60e260580c4899738dd1e435705c0fee707092d3d7c8775c6b8f73b1f28c4e0480120c2466d7bc1092c006e0035bbfd17dd7e884a012071e2244eb7c7d0530170f0631f037f045eb0ce182b770f88b09002400c9afafaf95450dfc56231306edfa3079e200580c489f738dd1e435f0b80fddfecc4e24e508f5a678e916587c5a0480120064c7dfd7c3ab038f4a5b2ba6db8a5009038f11ea7db63e86f01b03f4e0db014586a9d39a6be7350217a270580e8b7d0153f3f047e0a64ef7bb4970fac0e8f81140012277ee2747b0c032d00f6ff540dfc1eb8477a04447f490120fa4c6d7f2e19b80a4bddc8fe5bf90efc560a00892371a25f00ec578ea5fe0fb8df3a6b744be79308d1991400a2576afb732ee04ae0266078bf1b6e2900244ebcc7e9f618c25500ec8fb317f80df0b075d6685fe7270971801400a25b6afbb309c0e5a06e060ada7f210580c49138fd8bd3ed3184bb00d8af10d4adc0e3d65905fece4f16420a00d105b5e35907f0ffb0f825307ed00db714001227dee3747b0c912a00f63f673b16bf069eb4ce2e902d89c5211cba131066513b9e9d0bac019603e3f566238418a4f184fe96d7a8d70ae7ea4d4598467a0004006ac73363813f823abffdc1705db94b0f80c489f738dd1e43a47b003ac6791eb8c13a7bd4cece0144bc910220cea99dcfa461f10be03a2029220db714001227dee3747b0cd12e00005a813f61f15beb9c510d9d0389782105409c523bffa6086dd0f33b2cf20ffacd816fa500903812273c71ba3d061d05c0fec74b41fd1c58669d33521a8238247300e290daf9b7e3814f80c7e0e0c65f081147f2097d067ca25edd73bcee6444f4490f401c513bff5600dc0e5c7cd0a391bf72971e008913ef71ba3d069d3d001de33c05fccc3a676461e7e0221649011007d4aea79dc0b558ead7404a87df4a012071248e1400fb3561a95f02775adf1a21bb0ec638190288716ad7d333808f813fd0a9f117428843a410faacf858bd523c43773222b2a4072046a95d4f2583fa15703d9000e8bb72971e008913ef71ba3d06d37a000e8ee307ee00f52beb5bc3657f8118243d003148ed7a6a2eb00ef819fb1b7f2184e89f04429f21ebd42b7be76ace454480f400c410b5fba94ce00f587c0f50c65cb94b0f80c489f738dd1e83c93d00071f6361f108f03fd6fce1b59d0f1276243d003142ed7eea7c6023a15dfbba6a59851062a014a1cf968deae5bde7f7f664610fd20360736af75ff381fb402d687fd0b42b77e9019038f11ea7db63b04b0f40c7382f00575bf38795760e20ec427a006c4cedfeeb7c603db0a0b7e70a2144182d00d6ab974be6eb4e440c9cf400d8902a5c9902ea4f58fce0a0470f7c6bda95bbf400489c788fd3ed31d8b507e0e0380f80bace3a37bfa973306132e901b01955b8f26842dbf5fea0b7e70a214414fc0058a35e2a3d5a7722a27fa407c02654e14a077003701b9068ab2b77e9019038f11ea7db6388851e80fd3fb7014b803f5ae7e6073b0716a6911e001b50852b4702ff20b48e7fa2e6748410a22b89843ea3fea15e2a1da93b19d13b29000ca78a9ebc80d0a23e27ebce450821fae064609d7ab1ec02dd89889ec91080a154d19369c0ddc0e5b6efba9721008913ef71ba3d0613baee2319e771e047d679431b3a3f41e8263d000652454f4e063e012ed79c8a10420cc6e5c027eac5b2c9ba13119d4901601855f4c479841affa9ba7311428830980a7ca25e2c3f4f7722e25032046008b5e7090716b70137823ab43fcdee5df732042071e23d4eb7c7606ad77d24e258c0ff61b1c45a9027770918407a000ca0f63c9103bc01fc9c2eff928410c2f614a1cfb837d40be539ba9311520068a7f63c7134f039709aee5c8410220a4e033e572f94cbc2419a4901a091dab3e272e00360b4de4c841022aa46031fa8172a2ed79d483c9339001aa83d2b5cc052e02aa3c7dc4d8bd31ecbc663c112273ee3747b0c7618bb8f749cfb81c5d68221bece414424490f4094a9e21543817780ab34a722841026b80a78473d5f31547722f1460a802852c5cba702ab8139ba7311420883cc0156abe72be5f6e7289202204a54f1f293810f81319a53114208138d013e54cf579eac3b917821054014a8e2e58b8037814cddb9082184c1328137d5f3958b7427120fa400883055bcfc57c03264173f2184e88b4460997abef257ba13897572174084a8bdcb5c58ea61606187dfd867d6bd6971da63d97836b8c489cf38dd1e839d67ef4723ce0a2c75a5f59f3972874004480f4004a8bdcb320975f92fecedb9420821bab51078533d5725c3a71120054098a9bdcbc6129aec3757732a4208110be6021faae7aac6ea4e24d6480110466aefb299846ef3935b598410227ca602abd57355337527124ba4000813b5f7f1138055409eee5c84102206e501abd473d527e84e2456480110066aefe3a7121af34fd79d8b1042c4b074e04df55cf5a9ba13890552000c922a79fc5ce0152045772e42081107528057d4b3d5e7ea4ec4eea400180455f2f825c0b34092ee5c8410228e2401cfaa67ab2fd19d889d49013040aae4b12b81278004ddb90821441c4a009e50cfd65ca93b11bb9202600054c9638b8107917f3f2184d0c9013ca89ead59ac3b113b92abd77e52258f2d016ed59d87103d49540a8fd389c7e1c4e34c20cde13ce8e7035f58e00d04f006827883817ddf07680804438fedfbb94d560c15e652c09dead99a34ebdb59b7e94ec64ea400e80755f2d8edc04f75e72104805329c6ba92999c9cc294643793dbbf52189a18dead27cadadad8d2dc12fa6a6966f3beef77b6fa08487120cc70ab7ab6c6637d3beb67ba13b10bd90ba08f54e963b763756cfc7b5b2fdce66bef9b16a73d968dd7841f609c148783e3d33238292d9369ee142627bb199fe4c6a5bafa378c1e9f65b1bd25540c6c686ae6ddfa063ea86fa029180c3dc166ffce118bd3ed3198b6f6be0de3747a6d7e6f5d2045405f4801d007aaf4d125a06eedff8784cd1b5cd3e2b4c7b27143d0c7382ea5989d9ac1bcb44ce6a567726c6abaf6c6beaf7c96c56a6f03abeabcacaaade7636f13be433e67ccf9778e5a9c6e8fc1860dae6971ba7c6d6eb62ec894e1805e4801d00b55fae862e0ce817d48d8bcc1352d4e7b2c1b3704ddc45116cc4c49679e2793799e4c4e4ccb24c5111b734c9b8241deab6f6055ad975575f57cded04ce78f1d7bbd5e52009814a7dbd7e65aeb82cca59d038afda400e8812a7df44a42b3fd95140006c4698f65e386a0439c09496e16660f6561763e635dc91d0f8a493b5b7cac28af624579355fb7b4ee7bd41eafd780e3747b0c366c704d8bd3ed6b6301ff655d90f970e7a002a400e8962a7df41242f7f9efbb0c9302407b9cf658366e082cc872267051d6501665e7332735be578ffec8dbc8f2b22a9eaeaca5c61f30f2f50a4b9c6e8fc1860dae69717a7c6d82c0a5d605997fed1c584801d00555fac8b9a09ee590bb24a400d01ea73d96fd1a8244e5e0acf41c1665e5734e460e2e151bddfbe1e2b32c5eadae63795935af57d7d3d6f175b0e9eb6e564319ab717a7d6dfca0be6d5d90f152e7e0f14d0a800e54d923a762f10aa80ecbfb4a01a03d4e7b2cfb340419ce047e983b929f0c19c59084f0de9a17ab2adafcfcb9b8827b4a2aa9f307420fdaec75ef14a7db63b061836b5a9c3ebd36ad587ccbba30e3edce27885f52001c44953d7202f0261629e1f990b079836b5a9cf658e63704b9ce44ae1d328a6b724792e194e53606a2ce1fe0de924aee2caea0d2e7c70eafbb14003ae2f4f9b569024eb72ecc78bff349e2931400fba8b2476602ab80f4f07d48d8bcc1352d4e7b2c731b82e18949dc3064343fc8194e8ac3d931981880a66090074aaaf86351057b7d6d07fdc69cd7bdd738dd1e830d1b5cd3e2f4ebb5a907e65917667cdef944f1470a0040953d3216580de40161fc90b079836b5a9cf658e61500635d6e7e96379acbb3879124e3fb11d11ab478bcac9adb8bcad9d9e2c384d7bdcf71ba3d061b36b8a6c5e9f76b530e1c6b5d98b1b3f3c9e24bdc1700aaece14c501f0253db1f9402c0cc38edb1cc2900321d09fc76d804be9f3d82049b2cd463777ecbe2a1926a7eb1ab94dab64087df4a01107f7106f4da6c02759c75617a6de713c68fb82e0054d9c32ee04d50730ff985140066c6698f654601b0306b187f1c3691bc0457c783441494b7f9b9617b092bca6a0e7a540a80f88b33e0d7e61de074ebc2745fe793c68778efab7c1898ab3b09612f8725a7f2cef8992c1f354d1a7f8df21213583e6514ef1c399ec352e263112511567309b501712b6e0b0055fef0af8085baf310f691e270f27fc326f0e5c4633929354b773a629f933253f972d6447e37363f66964f1651b3503d5dff2bdd49e812974300aafce145c0b2c87713dabccbddb438edb1a2df853bdf3384bb864f61749c2cd76b57bb5bdaf8f1d77b79b9b27edf2366bc7fba3f061b76b99b16272caff165d645e9cb3b9f3cb6c55d01a0ca1f3a19d49b40a21400368bd31e2b7a1fe01e47220f8c98ca2599f95de42a4cf5d7f25a7eb0a5186fa0c30b2a05400cc609cb67441ba8d3ad8b3cffea9c40ec8aab0240953f3415f810542610858942366f704d8bd31e2b3a1fe033923dfcade0482626a57491a730ddb6e6562edc50c4970d2d071e94022006e384ed33a21638cebac8b3a97312b1296e06cc54f9434381d7814cddb908f35d9d338ad513664be36f6313dd49ac3e7a3c578fc8d69d8ab0874ce075f5b477a8ee44a2252e0a0055fe900b780118a3391561b80c6702cf141cc9bdc3a7ca823e3120c9a1b877e2709e9956404682accc287a350678413ded8d8bdb7be2e5136e2930477712c26cb3dce9ac9930876f67c4cd0540dcf8f69074d6cc1ccf2c8f5b772ac27c7308b519312fe60b0055f1d0e5c055baf31066fb614e011f8c3b86712e692062d538b78b0f8e1ec70f47ca9080e8d555ea29efe5ba9388b4982e0054c5834703f7ebce43984b017fcc9fccddc3a6e2922eff98e77228ee9e388c3f4ec8ef6aba991007bb5f3dd570b4ee242229663ff154c58339c0f380dcb82dba94a014cb461ecef5b96374a722a2ecfa51392c9b2afb37881e2503cfaba71a7274271229315900a88a071dc04a60b4ee5c8499521c4e5e2a388a8599c375a7223459989fc94b871790e28cc98f41111ea38195eaa986987c93c4e4ff14701b709aee248499b29d89fc73cc3738cb3344772a42b3b372d2f8e78cd16427ca1d02a25ba7116a53624ecc1500aae2c1f3801b75e721cc342a3199f7c7cee6d814590e42841c9beee6fda3c7302a3951772ac25c37aaa71aced39d44b8c55401a02a1e980c2ca3cbe5a444bc9b9a94c607638f656a529aee548461a6a626f1c1d163989a9aa43b156126052c534f354ed69d4838c54c01a02a1f4823b4d84fbaee5c8479c6b952f8d79863189528734245d7462527f2afa34633ce1d176bc088fe4b075e507f6d8c992b8804dd0984d1ddc054dd4908f30c4d48e2add1b3189a107f1fecf5810085be56bc8100de4080866060dff741bc8100001ea733f4e57092e674e2713af0389c14242591ee8cafb1f1a1ae04de9a51c0f19fefa6cce7d79d8e30cf54426dcd15ba13098798d80c48553e7001f0376d1b7d98b6694eacc6698fd5f7d7265d25f0ced8d91c951cdb1d43c53e1f1b5a9ad8dcdcc4e6966636b734b3a5b999bd6d3e06f33730dce56272723253dceed0577232d352dc8c70c57631f585b785b95feca6be2db8ef11d90cc8dc385a360cbbd0ba24f599cec9da8bed0b0055f9c048601d902505408cc7698fd5b7d726493978bd6016f35263ef36debd6d3efe555fcbbfbc75fccb5bc78ed67d3bde45e96f605c721227a7a77372463a27a7a733dc157b13e856d53472d69745b4062da40030398e9602a00638c2ba24754fe784edc3d60580aa7cc001fc033819d0b7d5a7690d65acc6698fd5fb6be3c0c1d32367f0edf4fc2ece613f4160557d0dcfd656f12f6f2d5b9b9bf7fd46cb875fa7389392933939ddc3b773b39997911e33938b9e2dafe7a2af8a09767caf4a0160501c6d7f03ff024eb12e490d6253762f007e0adcdefe801400b11da73d56efafcd7dc3a67355564117f1ed65734b13cbabca58515dce1e5f2b067df8751b67a4cbc5c2bc1c160dc9618adbfe932eefdf53c3d55bca0e7d500a0083e268fd1bf8997549eaef3b276d0fb62d0054e55f8e06b51a38d0f768c087dfa1716cdee09a16a73d56cfafcdff0e99c8af874cec22b63dd407023c515dc6b2ea323e69f076f8ad511f7ebdc639263595cbf272b9342fdbd6130a7fb9a3925b76561e78400a0083e268fd1b680375ac7549ca9ace899bcf960580aafc4b0ab006d4a1f7641af6e167fb06d7b438edb1ba7f6dcef1e4f1f2a8595d7d8c18afcadfc6d2f262eea928a636e047ef7b37bc7132139cfc70581e8b87e7919360bf9b8f2c60feda3dbc5ad970e00129000c89a3fd6f600b70b475494a53c7df98ceae43757f02626a4106317805896e960d3fd2768d7f499b8f1b8ab733faab8fb9ad74f7bec63fb6d4fa03dc5654c2e8cfd673c3ae624a7c6dba53ea17052c3b6c1805b25aa0e86c32a136c9766cd703a0aafe321f8b97f6fd74e82f8dbbfab1f915b76971da63757e6d1250fc7bccb1cc71677511d34c657e1fb794ece691aa125a8341cc7aef46364e92527c6f682eff5b90cfd044fb34aa1fd535f31f9f17e2efeace00e901d014c798bf8173adefa4bcdcf1b726b3550f80aafa4b3ef088ee3c84797e9737d9368d7fc0b2b8a7a298c91b3fe1beca625a2ddb4e221eb0d6a0c57d25154cfe7c23f7945410b0c985c89c0c37bf9b209b48892e3da25636d9eab6235b1500c07d40aeee248459ce4ecbe3fa9c71bad3e893d58df57c63cb67fc68cf36ea62b0abbfbfeafc017eb4bd886facddc26a6fa3ee74fae4fa826ccece8d99d56045f8e4126aa36cc3360580aabaff7c6081ee3c84594625ba5936fc08e3c7fdabfc6d5c59b499e3b67ece17cd0dbad331ce170dcd1cb7762b576e2ba4aacdecc24801cba60d6354b2fd26338a885ba056369faf3b89beb2c51c0055757f26b011d430c0c4b19f6ee2d87cccddb438edb1428f2528c5bba3e7709ce15dffef34d4f09ddd1b2969f3d9e8bdab2fce3057222ba78c616e86d957d91fd63573d26745f8db3f43650e809e38e6bc77f7fd5c021c667dc75ddbf199a6b14b0fc01f8061ba93106659923bd1e8c63f88c5ada5bb3865fb97a1c65ff44989af8d53d66fe7d6c2524c9e1d715c869b2563636f99693168c308b559c633be074055dd3f175805281b547e1de2d8fc8adbb438edb114135ca97c35ee3f485266d6b0e57e1f97eedec8dbdeea7d8fd8edbd6b469c53b33c3c31793479896676b7b7062da6afdec5d74dbd6cba04065c29c76a1c23dfbb1630cffa8efb9d8ecf3689999f9efba8aafb938107e9f25d21e2d9dd43a719dbf8ffbbb196195b3e39a8f11703f5768d97196bb6f0ef3a33e74d243914774fced39d86308f021e542b9b8d5e0bdbcc4fd0037e05d8774d571111e77bf23923cdcc5bb19eaa2de354e9f20fab125f1ba7aedbce5315660ea99e9193caf9791edd6908f34c24d48619cbd8024055df3f03b85e771ec22ca90e274b871ea63b8d2edd5d59c477767f852f0eefeb8f349f65f19d4dbbb9bbb8b2f7276bb074521ea94e633f4e853ed7ab279b67e84ea23b46be6355f57d4ee061c0cc813fa1cdcdb9931895e8d69d462737956ce7c7c55b3b0f0f8ab0b1801f6f2fe6a65d25ba53e964547202378fcdd69d86304f02f0b07ab2c5c89db08c2c00806b8199ba93106699ea4ae3ba6cb316fc09581657166de2b7e5bb74a712377e5b58ce955b8b8c5b3df0bad1594c4d75e94e43986726a136cd38c61500aafabe02e0d7baf310e6b937ff70129539f3412d6051d1061ea9deab3b95b8f34869358bb61419d5e392a814f74e910981a24bbf564fb614e84ea223e30a00e07620457712c22c0b3cf99c9c62d63dd78bf76e61654da9ee34e2d6caf25a167f6d56f17572760a0bf2cc5ec048689142a86d338a510580aabeef78e062dd7908f3dc9c63d6cd20bf29dfc95d9545bad3887b771557f29bc272dd691ce2e6716615aac21817ab275b8ed79dc4c18c290054f57d0a58aa3b0f619eb3d2f2382a3943771aed1eac2e6649e976dd69887d96ec2ce5c11273d65c38ca93c459b9a9bad310665aaa9e6c31661cd3980200b80c98a53b09619e25065dfd3f5f57ce557b36eb4e437470d5d63d3c5f59a73b8d764ba41740746d16a1b6ce08461400aafade34e077baf310e6999792cb1c43d6fbdfd8d2c8c2a20d048d9a7a260082c0c24d456c6c6ad59d0a0073329299972d539944977ea79e6c3562a288110500f00b205f7712c23c37e54ed09d0200cdc1201716aea32918d09d8ae8465330c8851b0b690e9ab110d34db22e80e85a3ea1364f3bed0580aab9772c709dee3c8479e6b8b3989792ab3b0d007eb477331b5acc5c8f5e1cb0a1b1851f6d33e3ce8079d929ccc9307a2978a1cf75ea89d6b1ba93d05e00007f0492742721ccb3246792ee140078b2b68447aa8b75a721fae891d21a9e2c3363df8025e3a4174074298950dba795d60240d5dc3b17385f670ec24c3392d3392b4dffa22a5b5a1bf9efe24dbad310fdf4df5b8bd962c07c80b3725399e191eb1bd1a5f3d513ad737526a0ad005035f73a90dbfe4437fe2b73b4ee14b0802bf66ca041c6fd6da72110e48acd7b8c98aef95f23cdb985551867a97aa2555b3bacb307e0ff01476a3cbf30944b39b8d83342771a3c5a53cc474de6dc5a26fae7a3fa261e2da9d19d06170ff5e0721873ebb730cb9184da422db41400aae6de04e0973ace2dccf7adb4a1643913b5e6501d68e3c6d2ad5a73108377e38e52aadbf4f6e064253af8962c0c24baf74bf544ab969d6f75f5005c0e8cd7746e61b845192375a7c02f4ab751e96fd39d8618a4cab600bfd859a63b0d160d4fd79d8230d778426d62d445bd005035f7b8809ba37d5e610f439c2ece4cd53bf9efd3e63a1e9259ff31e3a1926a3ef5366bcde1ccdc1486b88cdc125e98e166f5842fea7b49ebe801b812306e5b4461864bd24790a8f4de9d7a4df16659ed2f86042db866abdeb5011295e2927c8fd61c84d10a08b58d5115d54f5a55734f32705334cf29ec6551fa28ade77fd55bc1a7cd32f12fd67cea6de6d52aafd61c641840f4e226f5842faa2b4745fb52eb2a607894cf296c625a9287999a77fdbbb57c87d6f38bc8b975b7de6d8367a627312d2deabdbcc23e86136a23a3266a0580aabd2715b8315ae713f67369badec97f6f3754f189dcf617b33ea96fe6ed6abdcb395f3a4c8601448f6e542b7c51bb65249a3d003f04f42fed268c7586e6c97fb756c8d57facbb757785d6f39f912b3b048a1ee5116a2ba3222a0580aabd271df86934ce25ec29dbe9e2c8247dddffef3656f35ea3fe45634464bd57dbc8bbb58ddace7fa42789ec44b91b40f4e8a76a852f2a1346a2d503b018905d3144b7e6a6e4a073adb4db2b776a3cbb88a6db0b2bb59d5b0173b3dddace2f6c219b509b1971112f0054eddda944e97f46d8d7c96e7ddbfe96f85b79aba14adbf94574bd55dd4889cfafedfc27674901207ab558ad688bf85c8068f4007c17c88ac279848dcd4bd55700acac2d2160c97dfff1226059ac2cd337d9739ef40088de65116a3b232aa20580aabddb095c1bc97308fb1b9a90c4612e7db3a397d7ea5d244644dff2d25a6de73e2ccdc550591550f4ee5a87699d060000200049444154b5a22da26f9448f7002c00c646f81cc2e67476ffaf6df1b2ae45ef023122fad635b4b0b6a145dbf94f965e00d1bbb184dad088897401704384e38b1870728abe0260855cfdc7ad151a7b014ece96db01459f44b40d8d5801a06aef3e01981da9f82276ccd354005884c6ff457c5a5956a76dc707990720fa68b65ad17642a48247b207e0fa08c6163122dd91c004979ebdd2d7b77829f1b76a39b7d0afc4e767bda66180092989a427e8ddf44ad846c4dad288bc0355eddd1381f991882d62cb64579ab673af6aacd6766e61865535fa16059a9c9aa8eddcc256e6ab156d132311385225e875118c2d6288d60240eefd8f7bab34ae0a3839453606127de220d4a646247058a9babb7381cbc21d57c4265d0540c0b2785796fe8d7befd63612d03411407a00443f5ca696b7857db25424aed2af0664868be8135d05c0e72df5d407f5ad0627cc50ef0ff2b9b759cbb927a74a0f80e83337a1b635acc25a00a8babb12816bc21953c4365d058074ff8bfd74cd03902100d14fd7a8e5feb0761b85bb07e05c64cb5fd1470a98a8e90e80cf9af52d052bccf29957cf9d00135313b56e80256c278f501b1b36e12e00fe2bccf1440c2b4874e3567a9644ddea6bd2725e619ead4d7a6e05753b1405ee042de716b615d636366c0580aabb6b2c704ab8e289d8a7abfb3f88c5b6567db3bf8559b635fb08ea9a0828c300a27f4e51cbfd615b5e3f9c3d00df03e9d1127d3731514f0150d4d6428b15d4726e619e96a045516b9b96734f4c913b0144bf28426d6d5884a50050757739812bc2114bc48f2ca79e0fbf2d72f52f3ad8a26918202b51760514fd76855aee0fcb1b275c3d006703c3c3144bc4098f43cff8a78cff8b8eb636f9b49cd793209da6a2df86136a73072d5c05c0f7c31447c4115d05c0369ff40088436dd355003865c154312061697307fdee5375778d04ce0c432e22cee82a00aa037ac67b85b9aafd012de7f5c886406260ce54cbfd23071b241cefbeef02329025fa2d4d5301e00dc80a80e2505ebf9e49a169d2032006c649a8ed1d9441bdfb54dd9f1d847146a2882fba7a00bc413d577bc25cde809e02407a00c4207c4f2d0f0cea0d34d877df6940c120638838e571e8e938f2ca1e00a2036d05805326018a012b20d4060fd8600b8045833c5ec4315d3d000dd203203a68901e00614f836a8307fcee53f57f7603df1accc9457cd33704203d00e250bae600c85d006290bea5960506bcfbee60de7d67017a9672133141d73e00cdd203203a680eea2900dc32042006278d505b3c208329002e1ac4b14268eb8a4fd134f740982b45d395b8ae9e07115306dc160fe85dafeaff9c4a98562212f1ab36a8e77e7c5d430fc25cbabae2eba400108377b65a1618d0beea037dd79f03a40cf0582100a893024018420a0061632984dae47e1be8bb5ebaffc5a0d5695a914fd7ed87c25cba66e34b0120c264406d72bfdff5aafecf1e64e95f1106759a66e37b9cd203200e253d00c2e6ce54cb029efe1e349077fd7c207900c70971085d430069d203203ad0b524af1400224c9209b5cdfd329077bd74ff8bb0a8d3b4267faed3a5e5bcc25cb9897a7a85a4001061d4efb6b95f0580aaff7306707a7f4f22445774f5004c74c9fc5571a889297a8ac2ba36290044d89cae960532fa73407f7b00ce03e4f2498485ae390093930674c78c886193751500d20320c2c745a88deeb3fe1600fd1e6310a23bbaee0298e49202401c6a92b6024056a51461d5af36bacf0580aa5f9a009cd2ef7484e886ae21801189c9a4ca4440b14faad3c188a4442de7961e001166a7a865c13e4f68e94f0fc071407afff311a26bc5fe162de755c044e90510fb4c74bbd0b5227f71ab6c4c25c22a9d505bdd27fd2900cee87f2e42746f93af019fa5e70a48e60188fd748dffb759169b1bf5f4828998d6e7b65a0a00a14d9b156493af41cbb98f71f76bb2ac8861c7a40f7837d541d9dcd8862f686939b78869e12d0054fdd27c60c680d311a21beb5aebb59c775e5ab696f30af3cccbd2d31bb4cedbaae5bc22e6cd50cb82f97d79625f7b004e076dc3642286ad6dadd372de2393d3c971ea99f825cc9193e8e4c8343d0b9baef3fab49c57c43c451fd7ebe96b0120ddff2222d6b6e8e90150c0dc54e9058877733353b55dd9ac6b901e0011317d6ab37b2d009477a903386dd0e908d1055d3d0020c300425ff73f480f8088a8d3d4e3c15edbf7bef4001c03c827a588888a808f124db7037e333547cb798539bea9a900a86c0bb0576e011491934da8edee515f0a00d9fa574494ae898093935219e3d233035ce837263991c929495ace2d57ff220a7a6dbbfb5200c8f8bf88a8b59a0a00804b33866b3bb7d0ebd2fc4c6de7963b004414f4da76f7580028efd2746056d8d211a20b3a0b808599c3b49d5be8b570a8c602a0417a0044c4cd528f077b5cbdb7b71e80397d788e1083f2694b8db6734f4a4a65b62c0a147766a7bbb56d0004f0599d9e792f22ae3808b5e13d3ea127c7872f1721bab6cdd7c8564d2b02022ccc92618078b35063f77f618b9ff5d20320a2a3c736bcb702e08430262244b75e6e28d376ee8b338691a8649dab7891a81417e7e9ebf579b9bc51dbb945dce9b10defb60050de3b1380d9614f47882ebcd450a2eddc39ce44e67bf2b49d5f44d7fc5c0f3989fab6837ea9420a001135b3d5e356b7db03f7d40370149012fe7c84e8ecc3e61a2a03faba457f3e649cb6738be8faf9e85c6de7aef30779b7ba59dbf945dc4921d49677a9a70240c6ff45d404b17855e330c04c773a677af4350c223aceccf130d3a36fed87372a9b68b36407401155ddb6e53d150032fe2fa24ae73000c012e90588794b460fd17afe97caf54d761571abdbb65c7a008431de6aaaa0c50a6a3bff712999b23f400c9b9795ca7119fa4635db2c8b372a9bb49d5fc4adfef50028ef9de3813eed272c44b8340503fca3b1426b0e4bf2a41720562d19ad77a2e7bbd5cdd4f9f515b8226ee5abc7adf15dfda2bb1e00b9fa175abcd450aaf5fc27a7664b2f400c9a9795cac999fa76fe0399fd2fb4eab24defae0090f17fa1c52b8d65e89e2275cff0a9b22e400c49548a7b26ea5fece96519ff17fa74d9a6775700f4b87ca0109152e66f655563a5d61ca626a5725dee18ad3988f0b96e542e5335edfab7df07b5cd14b6c8f6bf429b2edbf44e0580f2dee902a6443c1d21baf1e79a1dba53e0e6bc71142426eb4e430c52417222376b9ef90fb0b4b056770a22be4d518f5b9d36bfe8aa07602ad0edca414244daab0d657cedd33b5e9aea70b274b8d4c176b774c230529d7af733dbd5dcc60bd2fd2ff44a20d4b61fa2abbf8c23229f8b10ddb380bb6a76ea4e8305e9799c258b03d9d659391e16e4f6b81b6a54dc5d544740f7c41621ba68dba50010467aacae88ba609bee347878e461e425e8db36560c4c9e2b8187278fd49d065e7f90478aeb74a72104f4b100383c0a8908d1a386a09f476a8b74a7c1b084249e18351db927c03e14f0c4d4510c73e91fc97c6c6fbddcfb2f4cd1a96d971e0061acbb6b7612d07e53209c9a96c3cff3c6ea4e43f4d1cf0bf238352b4d771a042df8b34cfe13e6e8b9074079efcc0586452d1d217ab0abad8997bcfa36083ad82d43c773626a96ee34442f4ecc48e59631666cedfc7245033b9af50f6309b1cf30f5b875c8a4a68e3d0072f52f8cb2d4805b02019c4af1d782c3c94d48d49d8ae8466e62027f9d3a0aa7218b38dd5958a33b05213a3aa48def5800c8f8bf30ca7b4dd5ac69316312d588c4245e187d24c94aef6d65a2b36487e28569a31991644681b6a6be957fd734eb4e43888e0e69e3a5074018eff6aaedba536877426a264f8f3edc98ab4c11ea9d79fab0d19c90a177adff83ddbeab5a770a4274a5c71e0029008471fee6ddcb7b4de67ca0ce4f1fc203233aada921347960d208e6e7e8bfdf7fbff76b9bf95b9957771a4274a5eb024035dce900a6453d1d21fae047655f197147c07edfcb1ece6ff327e84e23eefd766c3edfcb3767f7c6a0053fde5cae3b0d21ba334d3d66b5b7fb07f7001400eee8e72344efd6b6d6f3606da1ee340ef1f3bc31fc24b740771a71eb272372f979811933fef77ba8b88e2fbcadbad310a23b6e426d3d7068013026eaa908d10f4b2a365315f0e94ee3104b474ce2a6a16374a711776e2a18cad2f1fab7f83d584d5b80255febddc952883e18b3ff9b830b0059e94418ad3ad0c6928a2dbad3e8e4b6fcf1dc3562120e592f30e21cc05de34770db987cdda974f2cb1d5554b60574a721446fdadb7ae90110b6f2604d215fb6d4eb4ea3931fe58e62e5e869b8e416c1887129c5caa9a3f9d108f33668faaaa195fbf6c8aa7fc216c6ecff467a0084ad04b1f851d906dd6974e9a2cca1bc36f648d21c4edda9c49c34a783d7a68fe5a22199ba53e9d28fb794cb8e7fc22ea40740d8d7fb4dd5acacdfab3b8d2e9de2c9e6a389b398929ca23b9598312525998f664ce2942c8fee54baf46cb9977fd534e94e4388be1ab3ff1be90110b6f43f659b6808fa75a7d1a5e9c9a97c36e91b2cca366f9cda6e16e565f3d98c494c4f4dd69d4a979a8316376cadd09d8610fd71680f806af8930b306b4aad103dd8eb6fe127651b75a7d1ad548793650587f158c154526448a0df521c0e1e9b54c0b2c905a43acd9d5771c3d67276b7c8863fc25686abc770c1811e805174bd35b010c67ab4b68827ea8a75a7d1a3cbb387f1e9a4594c4b3667995ad34d4b49e6d3a32671f9507316f8e9ca33655e99f827ecc841a8cd6f6ff4a5fb5fd8d255a55fb1c5d7a83b8d1e1d969ccae79367714bfe58921d52677727d9e1e096d1c3f8fca8291c96626697ff7edb9bdbb87253a9ee348418a8b170a00018a32f0f2106ae21e8e7c23d6b68b182ba53e95192727073fe18364c3986b3d27374a7639cb3b2d2d970f4146e2ec827c961f67a0aad418b0bd71553ef37fb3d27440fc680f4008818b0aed5cbe25273e7031c6c5c929bd7c61fc17363a731ca95a43b1ded4625b9786eea385e9b369e71c9f6f8f7b87e5b396bbc2dbad3106230a40740c48e076a0a79babe44771a7d767ee610364d3d865b868d25db69c61ef6d1949d90c02da387b1e9e8c3383fc7cc7bfbbbf24cb9977bf7d4e84e4388c11a03070a00b95f49d8def7f7aee76b9f7deec74e7538b9397f34bba71fcb1f468c273fd1a53ba588cb7725f2873123d8fd8de9dc3c6a98d133fc3b0a8dfbdba7c814a207f970a000904149617bde7df3015a0d9f0fd0519ac3c90d4347b173da6cee19359182181c1a28487271cfb80276ce9cce0d23869266a3861ff68dfbaf97717f113372400a001163be68a9e7bad24dbad318906487836b860ce7eb69b3797edc34cecbccc1a5cc9e10d71397529c9793c9f353c6f3f5cce95c336c886def8290717f11637200120efe418858705fcd6e46bbdcfc34679cee54062451291664e6b22033976abf9fa76aca595155ceea46f33641eacab19e54160ec9e1e2dc6cb213127a3fc070bfdf5d25e3fe22d6e40028bc77b8812650d069338b7d571f56879ff7b3ba784ce21c14a7ababb78e710e7a8ec4e9394e7bacbebd36f70f9bc67f671574710e7bdad6dacc733595fcd35bcb07de7a9a83414cf81b703b1c1c9f96c63733d3f9cf9c2c26269b7d0f7f7ffca5b886ab369732e87fe76e8fa18bbf838eaf8dc4e9398efebf019bc6495178ef1809141992508cc5b179836b5a9cf6587d7b6d14b07cf8915c9a197bab5cfb2c8b8f1aea59e5ad63557d2d1f377a69b3aca8fc0d242ac5ecb434e665a4332f239d399e345b0f5574e789d23a166dd8bbef9f480a0073e398de0e181b6794c27bc791c09786241463716cdee09a16a73d56df5f9b0414cf8c3a8af33c43bb3857ec680906d9dadacce6e66636b784febba525f4d5180c0ee86f20d5e16492dbcd1477325392dd4cd9f7fda4e464db8ee5f7d58b155e2ef8aa187f70ff3f901400e6c631bd1d3036ce8c0464fc5fc430bf657171d197bc52308b53d362f7ad9eec7070843b9523dc87ee39600155fe36bc8100de4090866060dff7a12f008fd319fa723849dbffbdd3494e4242571fc731efedea462ede508cdfeaf4c92a442cc9910240c4bc562bc879456b786bf42c8e4fc9d29d4e542920372191dc84f85b6c68203ea86be2bcf545b406a5f117312fc7811400220e3405039c5df8395fb4d86326bd88be2fbc2d9cbdb690a680dceb2fe2821400227ed405fc9cb6eb3336b636e84e45186663632ba77db99b3a59e847c40f2900447ca90cf83861e7c7fcabb15a772ac210ffaa69e484cf7751d916d09d8a10d1240580883f3581364edff5290fd7ecd19d8ad0ece1921a4e5f5b488d5f1a7f1177721c406aaf4f1322c6b45916df2fdec00da55b0876be8f46c4b8a005377c5dcaf737ef0dad9f2044fc497500b1b7f388107d7447e52e16ecfe9286a05c01c68b864090055f1572475195ee5484d029c901c4fe1ea442f4e0656f3927ecf898a236d9ec25d615b5b671c29a9dbc5ce9d59d8a10bab9a4074008606d8b97d9db3fe6d3e63adda98808f9d4dbcceccfb6b3b6410a3d21d8d703200580104089bf9593767cca737565ba531161f65c453d277db193129f5f772a429842860084d86f4462123f1f328e1352e36bb5c0787042460a3f1f9dcb8824591151887d5c09480f80887327a566f3c39c02cef3e4911083bbda0918ea4ae0e6d179fcbc60082f567ab9674f35efd636ea4e4b089d92a400107129d5e16461e608aec92e607a729aee7444942428c5b787a4f3ed21e97cd5d8cabdc5d5ac28ada351560014f12749e1bd63375060c8f6843116c7e6dbef9a16a73dd6c05f9b49ae54aec92ee0b2cc11643813ba38bf883775fe20cb4a6bb9774f355b9b7cfb1e0df36744b7c760c3ed774d8b637a3b606c9c4285f78e5260a82109c5581c9b37b8a6c5698fd5bfd74601e77886f2c3ec024e4dcb8dcb2d6e45ef2ce0edea06eed953c3ab950da1b79414003688637a3b606c9c3219021031ed5ccf507e9d379123933dba53118653c069d9699c969dc6da86167eb9a392972a64bd0011b3e42e00119bce4c1bc2a7638fe3c582a3a5f117fd76645a322f1e31924fbf31963373648e888849b21090882ddf4ccde1c3b17378bd6016b3dc19bad31136372b3d99d7678ce2c359a3f966b66c9b22624a924377064284c38929d9bc33e658fe317a3673dc721fbf08af39196efe71d428de39ba8013335374a7234458388056dd49083150c7ba3379abe018fe3de6584e4ac9d69d8e88712765a5f0ef9905bc75f4288ecd70eb4e4788c1684d007c8094b4c256a626a5f1c7bca99c9596a73b1511874ecd4ee5d4ec545eaf6ce4866de56c6af4f57e901066f1490f80b095148793dfe54d61edd8ff90c65f6877566e2a6b8f1dc3ef260c21c52937980a5b69950240d8c6b969f96c1a37971b73c693284bf60a43242ac58d63b2d934672ce70e913b06846db43a080d010861acb18929bc3af2185e1c398b8244197715662a484ee0c519c379f5a8118c75cba643c278320420cce5520e96e44c64c3b893385bbafb854d9c9d9bca86e3c6b0645c362e87f4540963c9108030d329a943583f762eb70e998c5b3975a72344bfb81d8a5bc7e7b27e4e01a764cb1c6b612419021066199e90cc53c367f2f6a86399e492855784bd4d4a71f1f6cc113c75783ec39364f3296114190210e6b8c0339c0d63e77251fa70dda908115617e57bd870fc682e182a93048531640840e897ea70f248fe0cfe367c16990e993c256253668283bf1d99cf23d3f24875ca22ac42bbf6858084d0626672267f1d369389d2dd1f163ecbc21b08d0100ce00d043a7f1f08e20d1ef81e20cde1c0e34cc0e37492e674e0713843dfeffbafc7e920cd19fade25b75f0eda7747a47362969b4bd695f279bd5c7f096d7c09480f80d04001ff933d81db72a790a8e46aa8af1a8301b6b634b3b9a599cd2d4d6cd9f77d91af958640005fd022927b8fbb50a4399d8c4a7231253999c96e3753dc6ea6b89399949c2c57b67d343125918f668f64c9b66afeb0b3b6f3cb2344e4b526008dbab310f165784232cbf38fe69b294374a762ace2b6d60e8d7c139b5b9ad8e3f3851a8bee1aee08f35916d57e3fd57e3f6b1b9b38b89050c0c8a424a6b89399e24e66b23b9929c9c94c497133c225433b1d252ac5ed9372382d278545ebcbd9dbead79d92882f8d094095ee2c44fc38372d9f47861e458ed3a53b1563340503bcdf50c72a6f2def36d4f15573230dc1003d5eb91bc8028a5a7d14b5fa78bbb6fec08328d29c0ea6a7b83929c3c3bc0c0f27a4a791e290de02806fe6b85977fc48bef755052f95cbf598889a2a29004454b895933b864ce7aaccb1ba53d1aed50ab2bab19e55de5a5635d4f071633d6d5d76ddc78e864090d5de46567b1bb97d4f198928667b529997192a088ef5a49214c78be6e4243a79f1a87cee2faae7fa2d553407645040449c140022f2c626a6f0f2f063999e94ae3b152dfc96c5674d5e5635d4b0ca5bc3878d753407e3fb03becdb278bfbe81f7eb1bb88512dc0e27c7a5a7322fc3c3bccc3466a5a5901087130eaf1a95ce8959c9cc5f53cace261912101125058088ac13dd393c3f6c36b971d6e55fe6f7f1544d196f79ab79afa1166f3034e35ed7d8bde99a8341fe59ebe59f355e003c4e272766a4715aa6878b876431d4153f8be84c4f73f1c9b12338ff8b32deab69d19d8e885d550aef1df3807f8673a6b0c4d91fa7ab0ff98e710e7a4e8cc5b922bd80bfe4cdc01527b3fc5bac202fd555b0bca69437ebab095807ff83d8edbd6b4e1ca7529c9ee961d1d06ccecdc920394e860a7c418bffde50c963c55e3aff5d76f16fd0e9efb2e3bf6987e362268eb9ef5dc3e37c537a0044d83950dc9e3b9d1bb226e84e25e22ce0fdc65a965797f04c5d3975013f5dff518a810a5816af57d7f37ab5978c042717e466b06868362764a4c6743f8acba178f4f0211c96e6e2675bab89f35123117e550aef1d238122432a92188b63cf2bf7c1c4497324b032ff1b7c2b35bf8be7c48eaf7d4daca829654575093b7d1dbb6963e1bd6b7e9cb1c9492ccccb62e1d02c26b8637b88e995f226beb3b622b47893f62b6ed3e2d8efbd6b489c510aef1d6ea0c99084622c8e7d1aee70c419ed4ce195e173383c4627fbd506fc3c5d5bc6f29abd7cd854177ad0b8f75c7cc6392e3d8545f9595c342493cc84d8dc3d72bdd7c7b7d694b1bb695f2f5347b66bb8c315c7deef5d8d7152946559a8863f3581721b90508cc5b147c31d8e38c725e7f0c2b063c9732675710e7bdbee6be6f6f29d2caf29a1d5eaf02630ee3d17df71921c0e160dcde267a386303e067b05ca7d0116ac29e7c39a2e1670b55dc31dae38b1f1de8d729c66eb0a52f6cfce92790062c016790a5835e2c4986bfc37b6367069d1574cdefa010f5517d36a0575a7247ad11ab478a8a49ac99f6ce5d24d456c6c8cad95cef35c4e561d93cfa211b2aba018942a002900c4a0fc226b32cb86ce22298666faaf69aee73f77af65fad60f79b2b6a4c36c7e610701cbe2c9b25aa67fba8dffdc50c81a6fb3ee94c226c9a15876442ebf189fa13b15615f55000907ff20447ffc3a7b2aff9b3d55771a61f341532dbf29dfc11bde4adda98830b180e72bea79bea29e33b33ddc347a08c767a4e84e2b2c7e33298b2487e297db6a75a722ece79002a0546322c2866ecf99ce4fb326e94e232cde6ea8e237153b78b7b1a68bf134112bdea8f6f24695979332d3b869742ea766dbbf1bfd7f276492ec50fc6c4b8dee5484bd94c281026097be3c84dd2ccd3d829f64daff1eff57bc15dc56be9d4f9af7cde8ef6a02928839efd636f26e6d23c7a4bb59327a08dfcaf5e84e69507e3a2e83248762f1462902449fed8203730076eacb43d88502ee1b7294ed1bff2dad8d9cb2eb53e6ef5e7350e32fe2cd27f5cdcc5f5fc8295fee664b934f773a83f29331e9dc373d5b4a58d1573be14001b04b5f1ec20e1c281ece9bc955e9e374a73260cdc1004bcab771c4f60ff867834c7b1121ffac69e4884fb6b36447b9ad3769baaac0c3c347e410272b258bc1d905d20320fac0896259de2cbeeb19a33b95017bcd5bc1b4edeff39b8aedf8e4763ed1812f68f19b5d954cfbf86b5eab6ad09dce807d77641acb8ec8c5294580e8d9213d0045807c2a8a4e129462e5d063b8d453a03b9501296c6b6641e11ace29fc9c9dbed8b9154c44c6cee636ce595bc882f57b286c69d39dce805c3a229595338690204580e85a90509b1f2a00acb4eb7cc05e9d1909f3242a07cf0c9dc385692375a7d26f6d96c5ed953b98faf57bbce82dd39d8eb099172bbc4cfd7807b7efaea2cd86eb405c382c85678e1e42a2922a4074b2d7ba021f1ce801001906101d3c326426e7a50ed79d46bfbddb54cd8c1def7363f9169a8201dde9089b6a0a04b9f1eb72667cbc93776b9a74a7d36fe70d75f3c81132315074d2ded61f5c00ec8a7e1ec254bfcb9ece42cf68dd69f44b55c0c7a2e2b5ccddb59a8dadf61dc71566d9d8d8cadc35852cda5042559bbd0aca852352f9ed944cdd6908b3ecdaff8df400884eae4e1fcf8d995374a7d12f1f34d53063c7fbaca82bd69d8a88512b4aeb98f1f14e3ea8b5d75c921bc7a773f568fb2f7a24c2467a0044d716a48ee0eedc19bad3e8330bb8bd6a3b7377af664f5b8bee74448cdbd3ea67ee9a426edf5565ab4523ef9e96cd82fcd85802590cdaaefddf480f8068775c720e4fe61d83c326a38695011f67157dc28de55bf0db70a296b027bf6571e3d7159cf5c51e2a6d3224e050b0f2a81c8ecb8aad1d3bc580480f8038d494440fafe49f805b3975a7d227ef35553363c77bfcbda142772a224efdbdaa9119ab77f19e4d8604921d8a57be91cb94b444dda908bd76edffe6e002a010b0c73b5984d53067326f0c3b916c874b772abdb280df567ecdc9bb5753ec972e7fa15771ab9f933f2fe4b73bed3124909de8e08d6372c94fb247a12fc2ae99505b0f1c54005869d706810d3a3212fa781c09bc3eec44c624a4ea4ea557e58156ce28fc989b2ab610b0c5c7ad8807010b6eda5ec9195feca1dc67fe90c0187702af1f938b47560a8a471bac2b54fba27f8e0ebf5c17e564844689cac17379c731c365fe6d42ef34553163e7bf79ab51bafc8599deaa6a62c6eaddbc638335038e4a77f1dccc5c592828fe1cd2c64b0110c79666cfe054f750dd69f4ea77555f734ae16a4afcadba5311a24725ad7e4ef97c0fbfdb59ad3b955e9d3a2499a5d33274a721a2abc702607d1413111a5d9c5ac0d5e9666feb1bc4e2ead2f5fca262b374f90bdb0858f08baf2bb97a5339a66f2e78f598342e1e2eb707c69143da78e901884353123d3c943b4b771a3d6ab5825c54fc39f7d7eed69d8a100372ff9e5a2e5a5f42abe155c043476431252d41771a223abaef01b03cd73aef3dcd0000200049444154560225514d4744558a72f24cde71a439ccfd83f706fd9c55f431cf7ae5ad28ecedd9b206cefa622f5ebfb99bada625289e9999438aec211ceb4aaccb55e5c10f74ec0100e9058869f7e7ce64bacbdc71bf327f2b27157ec8aaa6cade9f2c840dacaa6ee2a4cff65066f01d02d33d89dc7f7896ee344464756adbbb2a00641e408cbad2338e45696374a7d1ad1d6d4d1c5ff8015fb4d4e94e4588b0fac2dbcaf19f14b1a3b94d772add5a3432852b0bccbf1d580c58a7b65d7a00e2c40c572677e71ca53b8d6e7dd95ac7f185efb1bdad51772a4244c4f6a6368eff640f5f7acdbd9be5eee999cc4897950263549f7a00a4008831e98e449e19723cc9862ef3fb4e532527157e40a9dce627625c696b80933e2de69d6a33175d4d76289e99954dba2c12148bfa54006c02fc91cf4544cb63b9c73021d1cced409ff79670c69ed5d407e52d27e243bd3fc8196bf6f27c5983ee54ba34213581c766c87c8018e327d4b61fa253016079aef5019ba3919188bcc5e993383f65a4ee34baf444fd1e2ed8fb19ad96b933a4858884d6a0c505eb4a79a2c4ab3b952e9d3fcccde271665e348801d96c5dae7c1d1fecaa0700e0a3082723a2e0b0c4746ecf3a52771a5d7aa3b18c2b4abf20280bfc883815b4e08aafca79a3d2cca5836f9f9ace611e736f1716fdd2659bde5d01f07e04131151e040f168ee31b854772fb13eab5b6af8f6de4ff15bd2f88bf8e6b72cbefd6529ab6bcddbd9d2e5503c7a64160e990e100bba6cd3bb6b1d3e886022220a16a74f6276528eee343ad9e4f3724ef16a9a2c73ef8916229a9a0216e7ac29655363a71e5aed6667b9583c5686026240976d7a970580e5b9763b501ad17444c44c484ce3b6ac2374a7d1c91e7f33a7eff988aa80791f7442e854d516e0f4cf4ad8d362de64d8dba67898902a430136566a5daeb677f58b9efa87a517c08614f070ce31b80dbbe5af3ae0e3f43d1f51e437f3f62721742b6af173fae72554b7993529d6ed543c7c64263212605bddb6e53d1500320fc086fedb33819392f374a77188262bc039c5abd9e83373c6b310a6d8d8e0e39c35a53405cc9a1f73528e8bff1e23ab04da54b76db9f400c4908284146ecf9ca13b8d43f82d8b0bf77eca472de6ef8f2e84093eaa6de1c2b565c64d92bdfdb0740adc66f52c8a3e19500fc0178099f7a7882e3d98fd0d3c06edf2670157967fc16b8d329d4488fe78ada2892bbfaa34ea26594f82e2c1233375a721faa789505bdea56e0b00cb73ad1ff838121989f0bb3c6d2ca7bb87e94ee3103fabdcc0b2fa42dd6908614bcb8abdfc6c8b593d67a7e72571f9a814dd6988befbd8ba5c753bb3b4b79bc4651e800d0c73baf95396591bfdacf016f1879a6dbad310c2d6feb0b396157bcd5a32f84fd3d319966cdefa22a24b3db6e1bdbd8a320fc00696661f4d96c3a53b8d769b7d5eae2aff52771a42c484ab3654b0b9d19c6d84b3121d2c9d9ea13b0dd1373db6e1bd15001f0166dd93220e313b29870b530a74a7d1aed90a7061e927340665a11f21c2a1316071e197e5341b7467c085c393999d25db061b2e482fcbfaf75800589ec5f5c067e1cc4884d71f0cebfaff71c53ad6b7d6eb4e438898b2deebe3c79baa74a771883f1c96ae3b05d1b3cfaccb1d3d7e18f76520e7ef614a4684d9b9292339316988ee34daadf416f170dd2edd690811931edee365658939f3014ecc71716e7eb2ee3444f77a6dbbfb5200bc118644449825a0f8bf4c7376fadbdad6c00f64dc5f8888fac157556c35683ec0ff1de6214196083455af6d775f0a804f00b3ee45115c99369e29896674c1b5ec1bf76f089ab78eb910b1a42110e4c22fca69099a311f604a5a02578e96db020d544da8edee51af0580e5591c04de0a4746223cd25402bfca385c771aed1657ae636d6b9dee3484880b6bbd3e166f34e79aec57533ca449378069deb22e77f43a81bfaf3773ca3c0083fc4ffa610c759a31f6f674c31e1ea8dba93b0d21e2ca03455e9e2e69d49d060043931cfccf04d927c0307d6ab3fb5a00bc0946ad4a19b78639dd5c9f3e45771a007cddd6c0f7cbd7e84e4388b8f4fdafaaf8bac98cf900d74f4895c581cc61116ab37bd5a757cc4a5f5c0ac80c2f03fc3ae3705295fef5fe03585c54fa095e19f717420baf3fc8455f5460c2f200a94ec5afa7a4e94e43847c695de6e8d3062cfd29d9641840b3c31233f86eda78dd69007057ed76d6b4d6ea4e4388b8b6a6dec75dbbcd5877e3bb05291ce6d17f7122fade564b016023b7641c8113fd936df6fa5bf865f546dd690821805f6eab656fabfe95379d0a6e915e001344a400f81030a3d48c4313123c2c4819a93b0d00aead5c275dff4218c2eb0f72ed2633ee0a58302c9909a94edd69c4b37a426d759ff4b900b0d217fb817f0c24233178d7a64fc161c0d5ffdbcde5fcad618fee34841007f95b49236f57b6e84e0387826bc7cb1d011afdc3baccd1e7abb3fe4edb7cb99fcf176190eb48e28ad471bad3a0d50a724dc517bad3104274e19a0d55b41ab040d015056e725d72478026fd6aa3fbfb2abd08f8fa798c18a4ab3d13712bfddd6abfafddc2b63673d62217421cb0add1cfef77e81fa5753b15578f95d50135f0116aa3fbac5f058095fe933afa787fa1088f64e5e4879ec9bad360475b23bfadd9a23b0d21440f7efb751d3b9af4cfcff9e1d814929dfa872ce3cc9bd665ce7e2dc93a907e9aa707708c18a0cb52c731c491a43b0d7e58f9252d96fe99c64288eeb5042d7eb841ff84c021490e2e1b65c66aa571a4df6df3400a809701fdb34de28003c5751efdabfe3ddf58cc1b4d7d5a574208a1d91b15cd3c5fdaa43b0dae9b908a433a01a2a58501ccd1eb770160a5ffc48b6c111c15f3dd239994a077c7bfc6a09fc5956bb5e62084e89fc51b6b68d4bc44e0a43427f3f3f5f75ec68937accb9cdefe1e34d0a99a320c10053778a6ea4e815b6a3651e46fd69d8610a21f8a5afcdcb24dff0e9d37c82641d132a03679a005c0ab80fe3ea61836c795cbf14943b4e6501e68e5eebaafb5e620841898bb777b29f7e99db7737c4e2273b213b5e610079a08b5c9fd36a002c04aff4923f0da408e157d737dbafeabff3b6bb7d12c13ff84b0a5e680c59d3bfbdd2b1c76d74b2f40a4bd665de61cd0ded08359ad4186012224d791c4fc64bdcbfed606dbb8af7ebbd61c84108373dfee066adb825a73989f9f240b0345d680dbe2c1bc2aaf03b22a4c045c92328644a5f70fe6aeba6dd407cdd86b5c083130f5fe2077edd2db0b90e8804b46ca2d8111d240a82d1e9001b73256fa4f9a8157067abce8dec2d4b15acfdf10f4f36719fb172226fc79a79706bfde3b0216ca9a0091f28a759973c0b3b4077b99b97c90c78b0ea62666f00d578ed61cfee2dd417550567c16221654b705f94ba1de5e806f642532d593a035871835a83678b005c05b40e1206388832c4cd17bf5df6205b8a376abd61c8410e175c70e2f2d9a370a925e80b02b24d4060fd8a00a002be327ffbfbd3b8fceaabef338febe490824a010c21a055c6ad79176b0d5bacc8ce306ae554441c0a08250dc8e75da3a9ba77aba598edb1f6d1d5bb18875ab1585d66ad102670e8c758e5a97691129061015290886000959bef3c78d18923c92e53ecff72e9fd7391c2079eef7f73d81e4f7797ef7dedf6d01e6f7a6867cac8880e9ce4ffd9bbf733d9b9bb5d1a3489a6c6e6866fedbbe976c4d1fd5573b03466bbe5517f7ea0acf28ae34bb0fd0bd621138b9df704615fb3d45abd15a98b7430ffc1149a379eb76d2687eab00a3ca8a397948a9dbf829d34c38f7f64aaf03800dbc6e13da1a3812d5e5beeffe7f59b7918d4ddadf49248d36ee69e2979b7cbfbfab751a202a4f5b75c9a6de1689ea5eb39f475427b3ca83122e2c1fed367e0bc60f77ac761b5f44f2ef87eb6af1bc14e0c2aabe94eb31c1518864ce8d2a003c05bc1b51ad4c9a58368a0181df55b28fef7a87b58ddad64124cdd6ee6ae271c727050e28099858a50704f5d2bb44b4136f2401c0065ed70cfc228a5a59e5bdfc3fbfb6c6757c11298cf96ff768d7d8c8548fd669805efa8555974472dd5d94dbcdcd077cef3349a811c5659cda6f84dbf8ef35d7f3dc9ef7ddc61791c2796e6b3def35f85db77dead05246f4d3d6c03d644478e75d64ff0a36f0ba1ae0b9a8ea65c9f8be2329c2efbcd883751b68567613c984668307dff13b0d5014c0f861ba1ba0879eb3ea92c8966ba38e613f8bb85e264ce857e53afec29d1b5cc71791c25ab8c9f734c084e10a003d14e91c1b7500580c6c89b866aa1511707abf916ee3bfb27707afeffdd06d7c1129bcd77736f24aaddfc3be4e1f56aa4d81ba6f0be11c1b994803800dbcae11f8499435d3ee2ba5955416f95d15bbb04eeffe45b2c87315a0b2b488af0ceae3367e42fdc4aa4b224d6df9b812e3a7408f9f4e94359ecbff4d663c54a747398864d143efecc6f321813a0dd02d7b08e7d648451e006ce0b55b81fba3ae9b569e0160e99ecdbcaf7dff4532e9fd866696fecdeffb5f01a05beeb7ea3e5ba32e9aaf7b31ee007af590822ca8282a757df4efc2baf56e638b88bf858e5b037fa5a20f157d74214017b410cea991cb4b00b041d7ae0596e4a3769a9cde7724c54eb7ff7dd8d2c8e2dddabc5124cb166fdec3874d3eefd58a83f0624039a02576699fb5f9289ccfdd186ecf63ed5498d0ef10b7b11fdbf536f5a687388a64597d8bf1ab77fd2ed9d269802ec9db5c9ab7006083ae5d09bc90affa6930def1fcfffd5afe17117c4f038c5700389017ecd23e2bf3553cdffb31de96e7fa8935b64f0555c5652e63ffadb98155f5915f4f222209b4ea8306b634f89c06a8ea57c4d8817e0f414b80bccea1f90e004f007aca4c273cdffd2fafdfa28d7f45040837975fbecdef6e006d0b9c530de11c9a37790d0036e8da66e0ce7c8e9154c7970e711b7bd91e6dd628221f5bb6b5c16dece3076b0520873bedd23e79bd50ab108f64ba0fd85e807112659ce3ed7fcbeb150044e463cbb6f90580718314003ab19d70eeccabbc07001b74ed2ee0ae7c8f932495457d1953dcdf65ec779af7f066e34e97b145249efebaab898d7b7cee0a1a535e4c65a91e0fdcce5d76699fbcefd55ca8affa5dc007051a2bf68e717cf7afe57f11e9ccb2ad7ed7011ca35580b63ea0406f9a0b12006cd035b5c0bc428c9504e3fa0c761b5bcbff22d2199d06888d797669696d21062ae4bacb8fd1a3820138a6d42f002cdbf3bedbd822125f9e17026a05609f2d84736541142c00d8a06b7601b7166abc383ba68fcf2980b79a76b1a1c96fd30f1189af77ea9b5953d7e432f631150a00ad6eb54b4b0bf69ce6425f79713790e90de82b8a4a39bc6480cbd8cb75fe5f443ec132a7fd000e2f2fd68381c2b9f1ee420e58d0006015d7d403df2fe49871e379fe7f59bd96ff4524b73f389e061857d1c76dec98f8be4d2f2d6802f3b8f7e25e60a3c3b8b1e0b5fc0f5a0110914fb6625b83db2ea119bf0e6023e1dc5850050f005671cd5ee0bb851e372ec6395d00f846632def35fbdde62322f1b76d6f0bafd636ba8c9df13b01be6bd34bf7167a50afdd171600eb9cc676e5b502f0c7866d2ee38a48b2bcb0bde0f31090e9158075847362c1b90400abb8ba09b8c5636c4ffd83128e2c39c865ec35dafd4f44bae00da73b018e1c504cff924c5e08788b4defebf245f7dc7ff141e055c7f10b6e4c717fbcfe7bbfd158907d254424e1bc6e050c8031e599db12f855c2b9d085db57db2aae6e01aef71adfc368a7dbff00ded00a80887481570000185356ec36b693eb6d7adf16afc15de396555cbd0258e4d943218d767a00509319eb9aea5cc616916459bfa78986169f7b0132b602b0c8a6f75de1d9401cbedadf04fc6e3e2d20af2700bed55447a3b9854c114990160b9f0ee8614c796656001a08e73e57ee01c02aaeae01eef0eea310bc560074fe5f44bac3eb3440865600eeb0e97d6bbc9b88cb57fb07c066ef26f26d8cd335003aff2f22dda10090579b09e73c77b1f86adbe0abeb807ff3ee23dfbc5600d668054044ba618d4e01e4d3bfd9b4beb1b8282b1601a0d5fdc08bde4de44b3101871497bb8cad150011e90eaf158091fd8a288dd3ac14bd1709e7ba5888cd97da065f65a4f8b6c0aae2724a9c7601d0350022d21d5e01a0288043d37d2be0f536ad9fd7e3163a884d0000b0c157ad021ef1ee231fbc96ffb73637f0418bcfd69e22924cdb1b5bd8d2e073e7508aaf0378c4a6f55be5dd445b71fc4adf08ecf66e226a7e770068f95f44ba4fd701446a37e1dc162bb10b0036f8aa8dc077bcfb88da98129f00f0669302808874df5add0910a5efd8b47e1bbd9b682fae5fe93b8197bc9b88d2c8a2329771b76bf95f447a607ba3cf2980aa7e719d967aec25c2392d7662f995b6c1573503b300bf4da923d6bfa88fcbb83b5b7c9eed2d22c9b6b3c927009417a7ea89804dc02c9bd6afd9bb91cec4320000d8e0b9af00b77bf71195f2c0e7bc565d4b6a32948814505db3cfc5eae5e97a24f0ed36adec15ef2672896d00687533b0d6bb8928f40f4a5cc6dd690a0022d27d3b9b9c02407aae015c4b3887c556ac038055cead076603b1b96fb2a7cabd02804e0188480f789d02284bc729000366dbd4b27aef463e49ac03008055ce5d01ccf7eea3b7bc02409d560044a407eadc5600521100e6dbd4b215de4d1c48ec0340ab6f01ef7937d11b5ed700ecd4350022d2037ea700121f00de239cb3622f1101c02ae7ee00aef1eea337dcae01d0290011e901bf53002ec346e91a9b5ab6c3bb89ae48440000b0cab98b8027bcfbe8299d0210912471bb0b20d92b004fd8d4b245de4d7455620240abab80adde4df484df45800a0022d27d3a05d06d5b09e7a8c4485400b0caaf6f06667af7d1137eb701ea148088749fee02e8b69936b57cb37713dd91a8000060955f5f02dce3dd47771413501af87ca977b5c472032a1189b95d4ea700fa144102f702bac7a6962ff16ea2bb1217005add00acf16ea2abbc96ff7759132dc9df4241441cb4985f0848d86e806b08e7a4c4f199997ac9867c7d77b0f5bfa6027f047c36d9ef86668c5b76bedafab7a00bdb1ab5fbcfdfe9eb3fe11ba4f5f5bb4ceffe45a4e76e59534bff92d6f789fbfd1ceac2046d5d99c4dbbca64d7da7b30f3dd1084cb54bca13f908fbc02cb9ef1083adf77c1bf8d1be0f1874fc8fd9d9841bb4797d27c74456a7b36f80f6753afb06509d4eebecab15a77f63d5519d2ed4c9790c9d7c1fb41f5b753eb98eebbff18d7649ff791d9b4e86a49e02f8c86dc072ef26444424739613ce418995e8006043e6b400d5c076ef5e44442433b603d57649ffe49cace844a20300800d99b30998e3dd87888864c61cbba4ff26ef267a2bf10100c086cc790c58e0dd878888a4de02bba4ff63de4d44211501a0d5b5c06aef26444424b55613ce35a9909a006043e6d4011700b5debd888848ead40217d825fdebbc1b894a6a0200800d9db306984117eeb4171111e9220366d894fe89d980ae2b521500006ce8ec27815bbdfb101191d4b8d5a60c78d2bb89a8a52e00b4fa4f60a97713222292784b09e794d4496500b0a1b35b80a9c006ef5e444424b13600536dca8044dfef9f4b2a0300800d9dbd0d9808d47bf722222289530f4cb42903b67937922fa90d00003674f6cbc05cef3e44442471e6da94012f7b37914fa90e000036f4ca05c0ddce6d88884872dc6d530e5ae0dd44bea53e00b4ba1e78debb09111189bde709e78cd4cb4400b06157ee25dc2468bd732b2222125feb810b6cf2417bbd1b29844c0400001b76e5fbc059c00eef5e44442476760067d9e483def76ea450321300006cd895ab09ef0c68f4ee45444462a3119868930fcad4f364321500006cd895cb8159de7d8888486cccb2c9072df76ea2d0321700006cd8ac85c02dde7d888888bb5b6cf2c10bbd9bf090c9000060c366dd0c3ce0dc868888f879c0261f7cb377135e321b005acd0256783721222205b7828c9f0ece7400b0e1b33eba3d3053177e888864dc6ae002bbf8e04cdcee974ba60300800d9fb503381bd8e2dd8b8888e4dd16e06cbbf8e0ccdf129ef9000060c367d610ee1150ebdd8b8888e44d2d70965d3cb0c6bb913850006865c367be44b812b0dbbb17111189dc6ee06cbb78e04bde8dc48502401b367ce64ae07ca0c1bb171111894c0370be5d3c70a5772371a200d08e0d9ff92c301968f2ee4544447aad09986c170f7cd6bb91b85100e8848d98b918a8065abc7b1111911e6b01aaeda2818bbd1b892305801c6cc4150f037300f3ee454444bacd803976d1a087bd1b892b05804f6023aeb817b8c1bb0f1111e9b61beca241f77a3711670a00076023aeb80bb8c9bb0f1111e9b29beca24177793711770a005d6023aef81e30cfbb0f111139a07976d1a0ef793791040a005d64232ebf1185001191389b671755dce8dd4452280074838dbcfc46743a4044248e6eb2499afcbb4301a09b6ce4e5df03be81ee0e1011890303be61932ab4ecdf4d0a003d60232fbf0b988df6091011f1d402ccb64915bae0af0714007ac8465e7e2f301ded182822e2a109986e932a74ab5f0f2900f4828dbcec6160127a76808848213500936cd2606df2d30b0a00bd64232f5b0c9c8b9e2228225208bb81736dd2606defdb4b0a0011b0aacb9e05c6133e6b5a4444f2a316186f170ed6837d22a0001011abba6c25700ab0c5bb17119114da029c62170ed6237d23a2001021ab9af112f05560b5772f222229b21af8aa5d58f992772369a2001031ab9a51039c00ac706e4544240d560027d8859535de8da48d02401e58d58c1d84d7043ce0dd8b8848823d008cb70b2b777837924625de0da49555cdd80b5407ef2c7c0bf88e773f222209738b4d1c72b3771369a615803cb343aa6f0666008dcead88882441233043937ffe290014801d52bd90f0948096b1444472db018cb78943167a3792050a00056287542f27bc3870bd732b222271b41e38c1260e59eedd4856280014901d52bd9af036c1e7bd7b11118991e781afdac421ba85ba8014000acc0eb9f47de064e06ee7564444e2e06ee0649b38f47def46b22630d363edbd049b1eb80c82bb817e40f854ebf033fbbfd0dafdfda3d758bbbfef7b7d4aebecabd5feb8f6c7b4794dceb15547750a5827e73174f27dd07eecd4d6a98760ae5d307441c783a510b402e0c80ebd74017022b0c1b915119142da009ca8c9df970280333b74facbc031c052ef5e44440a6029708c5d30ec65ef46b24e012006ecd0e9db8033811fd2c922a288480a18e1cfb833ed8261dbbc9b115d03103bc1dbbf3c1f82fb8183f77d30e9e7ee750d80ea64bd4ece6348e2b9fb9ed4a98560869d3fecc98e2f142f5a0188191b35fd49e058f44441114987d5c0b19afce3470120866cd4b43584216081732b2222bdb10038d6ce1fbec6bb11e948a700622e78fbc18bb0e01ea0a2dd6792b374af5300aa93f53a398f21ee4bf73dadb31d0be6d8f9c31feb3880c485560062ce464d7b0c180b2cf7ee4544a40b96036335f9c79f024002d8e8a99b80d3801bd1530545249e1a097f469d665f1bb1c9bb1939309d02489860e343e3808720f84c6296ee750a4075b25e27e731c46de9bea775d60053ed6b23746f7f82680520616cf4d4978171c03ddebd888810fe2c1aa7c93f79b4029060c18687cf03e6034362fdce5d2b00aa93f53a398fc1fb9d7b6fea6c0566da792397742c2249a0158004b331972c018e069ef0ee454432e509e0684dfec9a615809408363c3211f831303276efdcb502a03a59af93f31892b602f01e708d9d57b5a8e38192345a0148091b336511f079e05e3af9312422d20b46f8b3e5f39afcd3432b002914ac7fe464e067101cb5df27b402a03aaae35327e731246105602d30dbcead5ad1f1c592645a0148213b6cca0ac2cd837e0434f976232209d544f83364ac26ff74d20a40ca05eb1ffd12e1d2dd315a01501dd571aa93f318e2ba02f01230cbce3de4958e2f90b4d00a40cad961935f018e03be05ec766e4744e26d37e1cf8ae334f9a79f56003224a8f9d568c225bd296d3eaa1500d5511dad00003c02dc68e71cbab16371492305800c0a6a7e75227017f0650500d5519dcc07801781ebed9c4357752c2a69a653001964875fbc0a3816b81cd8ecdc8e88f8d84cf833e0584dfed9a415808c0b6a1e1b80f1efc00d405fad00a88eeae4a14ece63f058016800eec0f8819d33aaae6321c90a0500012078ebb1c381db2098b8ef830a00aaa33ad1d4c9790c850e008b806fdad9a36a3a1690ac510090fd046ffdfa64c2eb03bea800a03aaa13519d9cc750a800f02a70bd9d3d7a45c70325ab740d80ecc78e98b482f071c3d5c03adf6e44a497d6117e2f8fd3e42fed690540720ad6fdba04b80c829b80d1fb3ea11500d5519deed5c9790cf95a01d808c177810576d668ed062a9d520090030ad63d5e0acc02fe03a85200501dd5e9669d9cc71075007817f83e70af9d35666fc717897c4c0140ba2c58f7783f602e16fc2b30acdd671500544775fc02c0162cb815b8dbce1a53df7110918e1400a4db82bf2eea0f5c037c1b18dcfa510500d5519dc207800f8079c08fedccc376752c2e929b0280f458f0d7450703d787bf820a0500d5519d1c75721e434f03c076c2bb75eeb2330fabed5854e4c01400a4d7c21581e00a8ceb81235a3ffaf10b14005427eb75721e437703400dc69d10dca777fcd25b0a00129960ed13c5c005c03721386edf2714005427eb75721e435703c00bc06dc01336e1f0e68e4544ba4f0140f22258fbe449c0bf00e7611fed37a100a03a19ad93f3183e2900b4004b80db6dc2112b3b1e28d23b0a009257c1da278fc2b801980141d97e9f5400509dacd4c9790c9d05803dc0fd1877d88423d6763c40241a0a005210c19b4f0e81e02ae06a3eba8550014075b25227e731b4fd3ed802fc04829fdaf823b6767ca148b41400a4a0823717f701be06ccc682d3e8f01351014075525827e73118163c07fc0c586ce38f6c6cff02917c51001037c19a25870333099f495ed5fa510500d5495f9d8ec7bc0bfc02986f677caaa6fd512285a00020ee82354b8a81b3812b213813a3b8cd67f77f71d22702d5c9669df09866089e067e0e3c65677c4a57f38b2b0500899560cd6f0ec5b882706560746c7e80ab8eeaf4bcce46603ec67d76c6519bdabf42c48b0280c452f0c66f8a803320a806ce050600499f0854273b75ea80df40b010586aa71fd5d27e04116f0a00127bc11bbf2d03ce0226639c0d4179bb57c47922509decd4d98df114f028f03b3bfdd37bda571589130500499460f56ffb43700e30193813e817c3894075b253a71e781a781482dfda699fd6f6bc92180a009258c1eaa70e02ce836032c678a0b4cd67c3df9237a1a84efcebecc5f83d048f024becb4cfec6c7fb448122800482a4234208f0000025949444154047ff9dd40e07ce03ce034080e069232a1a84efcebd402cf116ecdfba49dfad90fdb1f2192340a00923ac15f7e5702c109c0048c09c09720d8ffa7beff84a23af1ae63c02b18cf00cf40f03f76ea679bdabf4a24c9140024f5823f3f3d0282f1c004e00c6070822726d5c95f9d0f80a58413feefed94cf6d6e5f5d244d14002453823f3f53041c8b71260413802f439ba715c67362529dfcd469015e84e0198ca781ffb5533eafdbf52433140024d382fffbfdc1c0f1c089109c84711cd0e636c3444f70aab3ff31bb315e806025b00a78defef90bb5ed2b8864850280481bc1eb4b4b80bf074e044e82e0446044422638d5d9bfce660856011f4df87fb293ff4ee7f1455a2900881c40f0fab34762fb02c1f1c0678112c07b82539d8feb34016f40f03cb01263959d7cf4baf6a388c8c7140044ba2978edb952e073c0588cb1101c0d8c0546b6be22ce13651aeabc07bc06c1eb18af857f66b5fdd3d8bdedab8a486e0a002211095efdc310680d04c6d8f0cf7c01284bf884eb55670ff067e0b570a20f5e075eb37ffce2d6f6478b48f7290088e451f0cab222c2a71a1e061c8e11fefed1dfa10a288ac9845be83a2dc0bb400d04eb811a8cf56dfebed1fee14bba2a5f244f1400441c057f5a510a8cc2f60b0587012320a8c4a8042a81b2d623c2dfe21f00f600db30b641b00dd80c6d2677a30678db4e1aa7657b11270a00220910bcf4df654025046120d8170c828f024225d01fa32f04a540dfd65fa510f40d3f4e9b8fb7be26fcf66f80606ff83b0d847bdd374010fe79dfc783d68fb30bd816fe0ac2df6dffbfdb095fd693f04462eeff016511bda3c8b3995c0000000049454e44ae426082);

-- --------------------------------------------------------

--
-- Table structure for table `tblcourses`
--

CREATE TABLE `tblcourses` (
  `course_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `course_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblcourses`
--

INSERT INTO `tblcourses` (`course_id`, `department_id`, `course_name`, `created_at`) VALUES
(61, 27, 'Bachelor of Science in Industrial Engineering', '2024-08-18 16:03:03'),
(62, 27, 'Bachelor of Science in Mechanical Engineering', '2024-08-18 16:03:03'),
(87, 51, 'Bachelor of Science in Agriculture Major in Animal Science', '2024-09-05 06:39:37'),
(88, 51, 'Bachelor of Science in Agriculture Major in Agronomy', '2024-09-05 06:39:37'),
(91, 26, 'Bachelor of Science in Information Technology', '2024-10-16 12:57:55'),
(92, 61, 'Bachelor of Elementary Education', '2024-10-23 11:55:32'),
(101, 27, 'Bachelor of Science in Electrical Engineering', '2024-11-21 03:35:41'),
(102, 26, 'Bachelor of Science in Hospitality Management', '2024-11-21 03:36:37'),
(103, 26, 'Bachelor of Industrial Technology Major in Automotive Technology', '2024-11-21 03:37:48'),
(104, 26, 'Bachelor of Industrial Technology Major in Drafting Technology', '2024-11-21 03:38:04'),
(105, 26, 'Bachelor of Industrial Technology Major in Electronics Technology', '2024-11-21 03:38:21'),
(106, 26, 'Bachelor of Industrial Technology Major in Garments Technology', '2024-11-21 03:38:38'),
(107, 26, 'Bachelor of Industrial Technology Major in Welding and Fabrication Technology', '2024-11-21 03:39:06'),
(108, 26, 'Bachelor of Industrial Technology Major in Computer Technology', '2024-11-21 03:39:19'),
(109, 26, 'Bachelor of Industrial Technology Major in Electrical Technology', '2024-11-21 03:39:42'),
(110, 26, 'Bachelor of Industrial Technology Major in Food Preparation and Services Technology', '2024-11-21 03:40:10'),
(111, 26, 'Bachelor of Industrial Technology Major in Machine Shop Technology', '2024-11-21 03:40:24'),
(112, 26, 'Bachelor of Industrial Technology Major in Furniture and Cabinet Making Technology', '2024-11-21 03:40:51'),
(113, 51, 'Bachelor of Science in Agriculture Major in Horticulture', '2024-11-21 03:41:57'),
(114, 51, 'Bachelor of Science in Agriculture', '2024-11-21 03:42:23'),
(115, 61, 'Bachelor of Technology and Livelihood Education', '2024-11-21 03:43:10'),
(116, 61, 'Bachelor of Secondary Education Major in Mathematics', '2024-11-21 03:43:51'),
(117, 61, 'Bachelor of Secondary Education Major in English', '2024-11-21 03:44:03'),
(118, 27, 'Bachelor of Science in Civil Engineering', '2024-11-21 05:04:10');

-- --------------------------------------------------------

--
-- Table structure for table `tbldepartment`
--

CREATE TABLE `tbldepartment` (
  `department_id` int(11) NOT NULL,
  `department_name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbldepartment`
--

INSERT INTO `tbldepartment` (`department_id`, `department_name`, `created_at`) VALUES
(26, 'College of Technology', '2024-08-18 13:34:11'),
(27, 'College of Engineering', '2024-08-18 16:03:03'),
(51, 'College of Agriculture', '2024-09-05 06:39:37'),
(61, 'College of Education', '2024-10-23 11:49:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `fullname` varchar(255) DEFAULT NULL,
  `nickName` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `course_id` int(11) DEFAULT NULL,
  `starting_year` year(4) DEFAULT NULL,
  `expected_year` year(4) DEFAULT NULL,
  `extended_year` year(4) DEFAULT NULL,
  `scholarship_name` varchar(255) DEFAULT NULL,
  `scholarship_start` year(4) DEFAULT NULL,
  `scholarship_end` year(4) DEFAULT NULL,
  `ProfileImage` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `fullname`, `nickName`, `email`, `phone_number`, `password`, `student_id`, `course_id`, `starting_year`, `expected_year`, `extended_year`, `scholarship_name`, `scholarship_start`, `scholarship_end`, `ProfileImage`) VALUES
(79, 'Demonstration', 'Demo', 'demo@gmail.com', '639632116717', '7d276cd540df7140aa713fd73f85807d', 5211445, 91, '2021', '2025', '2026', 'Unifast', '2021', '2026', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `fk_admin_id` (`admin_id`);

--
-- Indexes for table `course_history`
--
ALTER TABLE `course_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_course_id` (`course_id`),
  ADD KEY `fk_old_courseID` (`old_courseID`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `grades_ibfk_1` (`user_id`);

--
-- Indexes for table `grades_summary`
--
ALTER TABLE `grades_summary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grades_summary_ibfk_2` (`grade_id`),
  ADD KEY `grades_summary_ibfk_1` (`user_id`);

--
-- Indexes for table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `overall_summary`
--
ALTER TABLE `overall_summary`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`),
  ADD KEY `fk_grades_summary` (`grades_summary_id`);

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tblcourses`
--
ALTER TABLE `tblcourses`
  ADD PRIMARY KEY (`course_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `tbldepartment`
--
ALTER TABLE `tbldepartment`
  ADD PRIMARY KEY (`department_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `fk_course` (`course_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=257;

--
-- AUTO_INCREMENT for table `course_history`
--
ALTER TABLE `course_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=761;

--
-- AUTO_INCREMENT for table `grades_summary`
--
ALTER TABLE `grades_summary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT for table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=749;

--
-- AUTO_INCREMENT for table `overall_summary`
--
ALTER TABLE `overall_summary`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblcourses`
--
ALTER TABLE `tblcourses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `tbldepartment`
--
ALTER TABLE `tbldepartment`
  MODIFY `department_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `tblcourses` (`course_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_admin_id` FOREIGN KEY (`admin_id`) REFERENCES `tbladmin` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `course_history`
--
ALTER TABLE `course_history`
  ADD CONSTRAINT `course_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_course_id` FOREIGN KEY (`course_id`) REFERENCES `tblcourses` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_old_courseID` FOREIGN KEY (`old_courseID`) REFERENCES `tblcourses` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`);

--
-- Constraints for table `grades_summary`
--
ALTER TABLE `grades_summary`
  ADD CONSTRAINT `grades_summary_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_summary_ibfk_2` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `message`
--
ALTER TABLE `message`
  ADD CONSTRAINT `message_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `overall_summary`
--
ALTER TABLE `overall_summary`
  ADD CONSTRAINT `fk_grades_summary` FOREIGN KEY (`grades_summary_id`) REFERENCES `grades_summary` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `overall_summary_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `overall_summary_ibfk_2` FOREIGN KEY (`grades_summary_id`) REFERENCES `grades_summary` (`id`);

--
-- Constraints for table `tblcourses`
--
ALTER TABLE `tblcourses`
  ADD CONSTRAINT `tblcourses_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `tbldepartment` (`department_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_course` FOREIGN KEY (`course_id`) REFERENCES `tblcourses` (`course_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
