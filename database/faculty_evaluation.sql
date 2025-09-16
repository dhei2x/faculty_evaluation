-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 17, 2025 at 01:51 AM
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
-- Database: `faculty_evaluation`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_years`
--

CREATE TABLE `academic_years` (
  `id` int(11) NOT NULL,
  `year` varchar(9) NOT NULL,
  `semester` enum('1st','2nd','Summer') NOT NULL,
  `is_active` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `academic_years`
--

INSERT INTO `academic_years` (`id`, `year`, `semester`, `is_active`, `created_at`) VALUES
(2, '2024-2025', '2nd', 0, '2025-06-10 07:07:59'),
(3, '2024-2025', 'Summer', 0, '2025-06-10 07:07:59'),
(4, '2024-2025', '1st', 1, '2025-07-29 15:22:29');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `class_name` varchar(100) NOT NULL,
  `academic_year` varchar(20) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class_name`, `academic_year`, `created_at`) VALUES
(1, 'B.S ENTREPRENEURSHIP 1A', '2024-2025', '2025-06-11 04:26:18'),
(2, 'B.S PUBLIC ADMINISTRATION 1A', '2024-2025', '2025-06-11 04:26:18'),
(3, 'B.S ACCOUNTANCY 1A', '2024-2025', '2025-06-12 09:27:50'),
(5, 'B.S ACCOUNTING INFORMATION SYSTEM 1A', '2024-2025', '2025-07-29 15:14:59');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_criteria`
--

CREATE TABLE `evaluation_criteria` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_criteria`
--

INSERT INTO `evaluation_criteria` (`id`, `name`) VALUES
(1, 'CLASS DECORUM'),
(2, 'ATTENDANCE AND PUNCTUALITY'),
(3, 'SENSITIVITY TO ACADEMIC FREEDOM'),
(4, 'EFFECTIVENESS AND FAMILIARITY'),
(5, 'STUDENT AFFAIRS/CONCERNS '),
(6, 'ORGANIZED/SYSTEMATIC TEACHING'),
(7, 'FAIRNESS AND EQUALITY');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_report`
--

CREATE TABLE `evaluation_report` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `faculty_id` int(11) NOT NULL,
  `academic_year_id` int(11) DEFAULT NULL,
  `criteria_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `comment` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `evaluation_report`
--

INSERT INTO `evaluation_report` (`id`, `student_id`, `faculty_id`, `academic_year_id`, `criteria_id`, `question_id`, `rating`, `created_at`, `comment`) VALUES
(149, 1, 2, 4, 1, 14, 5, '2025-09-12 12:53:26', 'retard'),
(150, 1, 2, 4, 1, 15, 4, '2025-09-12 12:53:26', 'retard'),
(151, 1, 2, 4, 1, 16, 5, '2025-09-12 12:53:26', 'retard'),
(152, 1, 2, 4, 1, 17, 4, '2025-09-12 12:53:26', 'retard'),
(153, 1, 2, 4, 2, 18, 5, '2025-09-12 12:53:26', 'retard'),
(154, 1, 2, 4, 2, 19, 5, '2025-09-12 12:53:26', 'retard'),
(155, 1, 2, 4, 2, 20, 5, '2025-09-12 12:53:26', 'retard'),
(156, 1, 2, 4, 3, 21, 5, '2025-09-12 12:53:26', 'retard'),
(157, 1, 2, 4, 3, 22, 5, '2025-09-12 12:53:26', 'retard'),
(158, 1, 2, 4, 3, 23, 5, '2025-09-12 12:53:26', 'retard'),
(159, 1, 2, 4, 4, 24, 5, '2025-09-12 12:53:26', 'retard'),
(160, 1, 2, 4, 4, 25, 5, '2025-09-12 12:53:26', 'retard'),
(161, 1, 2, 4, 4, 26, 5, '2025-09-12 12:53:26', 'retard'),
(162, 1, 2, 4, 4, 27, 5, '2025-09-12 12:53:26', 'retard'),
(163, 1, 2, 4, 4, 28, 5, '2025-09-12 12:53:26', 'retard'),
(164, 1, 2, 4, 5, 29, 5, '2025-09-12 12:53:26', 'retard'),
(165, 1, 2, 4, 5, 30, 5, '2025-09-12 12:53:26', 'retard'),
(166, 1, 2, 4, 5, 31, 5, '2025-09-12 12:53:26', 'retard'),
(167, 1, 2, 4, 6, 32, 5, '2025-09-12 12:53:26', 'retard'),
(168, 1, 2, 4, 6, 33, 5, '2025-09-12 12:53:26', 'retard'),
(169, 1, 2, 4, 6, 34, 5, '2025-09-12 12:53:26', 'retard'),
(170, 1, 2, 4, 6, 35, 5, '2025-09-12 12:53:26', 'retard'),
(171, 1, 2, 4, 6, 36, 5, '2025-09-12 12:53:26', 'retard'),
(172, 1, 2, 4, 7, 37, 5, '2025-09-12 12:53:26', 'retard'),
(173, 1, 2, 4, 7, 38, 5, '2025-09-12 12:53:26', 'retard'),
(174, 1, 2, 4, 7, 39, 5, '2025-09-12 12:53:26', 'retard'),
(175, 1, 2, 4, 7, 40, 5, '2025-09-12 12:53:26', 'retard'),
(176, 1, 3, 4, 1, 14, 3, '2025-09-12 13:02:03', 'good'),
(177, 1, 3, 4, 1, 15, 3, '2025-09-12 13:02:03', 'good'),
(178, 1, 3, 4, 1, 16, 3, '2025-09-12 13:02:03', 'good'),
(179, 1, 3, 4, 1, 17, 3, '2025-09-12 13:02:03', 'good'),
(180, 1, 3, 4, 2, 18, 3, '2025-09-12 13:02:03', 'good'),
(181, 1, 3, 4, 2, 19, 3, '2025-09-12 13:02:03', 'good'),
(182, 1, 3, 4, 2, 20, 3, '2025-09-12 13:02:03', 'good'),
(183, 1, 3, 4, 3, 21, 3, '2025-09-12 13:02:03', 'good'),
(184, 1, 3, 4, 3, 22, 3, '2025-09-12 13:02:03', 'good'),
(185, 1, 3, 4, 3, 23, 3, '2025-09-12 13:02:03', 'good'),
(186, 1, 3, 4, 4, 24, 3, '2025-09-12 13:02:03', 'good'),
(187, 1, 3, 4, 4, 25, 3, '2025-09-12 13:02:03', 'good'),
(188, 1, 3, 4, 4, 26, 3, '2025-09-12 13:02:03', 'good'),
(189, 1, 3, 4, 4, 27, 3, '2025-09-12 13:02:03', 'good'),
(190, 1, 3, 4, 4, 28, 3, '2025-09-12 13:02:03', 'good'),
(191, 1, 3, 4, 5, 29, 3, '2025-09-12 13:02:03', 'good'),
(192, 1, 3, 4, 5, 30, 3, '2025-09-12 13:02:03', 'good'),
(193, 1, 3, 4, 5, 31, 3, '2025-09-12 13:02:03', 'good'),
(194, 1, 3, 4, 6, 32, 3, '2025-09-12 13:02:03', 'good'),
(195, 1, 3, 4, 6, 33, 3, '2025-09-12 13:02:03', 'good'),
(196, 1, 3, 4, 6, 34, 3, '2025-09-12 13:02:03', 'good'),
(197, 1, 3, 4, 6, 35, 3, '2025-09-12 13:02:03', 'good'),
(198, 1, 3, 4, 6, 36, 3, '2025-09-12 13:02:03', 'good'),
(199, 1, 3, 4, 7, 37, 3, '2025-09-12 13:02:03', 'good'),
(200, 1, 3, 4, 7, 38, 3, '2025-09-12 13:02:03', 'good'),
(201, 1, 3, 4, 7, 39, 3, '2025-09-12 13:02:03', 'good'),
(202, 1, 3, 4, 7, 40, 3, '2025-09-12 13:02:03', 'good'),
(203, 114, 2, 4, 1, 14, 2, '2025-09-12 13:04:29', 'not good'),
(204, 114, 2, 4, 1, 15, 2, '2025-09-12 13:04:29', 'not good'),
(205, 114, 2, 4, 1, 16, 3, '2025-09-12 13:04:29', 'not good'),
(206, 114, 2, 4, 1, 17, 2, '2025-09-12 13:04:29', 'not good'),
(207, 114, 2, 4, 2, 18, 3, '2025-09-12 13:04:29', 'not good'),
(208, 114, 2, 4, 2, 19, 3, '2025-09-12 13:04:29', 'not good'),
(209, 114, 2, 4, 2, 20, 2, '2025-09-12 13:04:29', 'not good'),
(210, 114, 2, 4, 3, 21, 3, '2025-09-12 13:04:29', 'not good'),
(211, 114, 2, 4, 3, 22, 2, '2025-09-12 13:04:29', 'not good'),
(212, 114, 2, 4, 3, 23, 3, '2025-09-12 13:04:29', 'not good'),
(213, 114, 2, 4, 4, 24, 2, '2025-09-12 13:04:29', 'not good'),
(214, 114, 2, 4, 4, 25, 3, '2025-09-12 13:04:29', 'not good'),
(215, 114, 2, 4, 4, 26, 2, '2025-09-12 13:04:29', 'not good'),
(216, 114, 2, 4, 4, 27, 1, '2025-09-12 13:04:29', 'not good'),
(217, 114, 2, 4, 4, 28, 3, '2025-09-12 13:04:29', 'not good'),
(218, 114, 2, 4, 5, 29, 3, '2025-09-12 13:04:29', 'not good'),
(219, 114, 2, 4, 5, 30, 2, '2025-09-12 13:04:29', 'not good'),
(220, 114, 2, 4, 5, 31, 1, '2025-09-12 13:04:29', 'not good'),
(221, 114, 2, 4, 6, 32, 3, '2025-09-12 13:04:29', 'not good'),
(222, 114, 2, 4, 6, 33, 2, '2025-09-12 13:04:29', 'not good'),
(223, 114, 2, 4, 6, 34, 3, '2025-09-12 13:04:29', 'not good'),
(224, 114, 2, 4, 6, 35, 2, '2025-09-12 13:04:29', 'not good'),
(225, 114, 2, 4, 6, 36, 3, '2025-09-12 13:04:29', 'not good'),
(226, 114, 2, 4, 7, 37, 1, '2025-09-12 13:04:29', 'not good'),
(227, 114, 2, 4, 7, 38, 2, '2025-09-12 13:04:29', 'not good'),
(228, 114, 2, 4, 7, 39, 3, '2025-09-12 13:04:29', 'not good'),
(229, 114, 2, 4, 7, 40, 2, '2025-09-12 13:04:29', 'not good'),
(230, 3, 2, 4, 1, 14, 2, '2025-09-13 11:31:33', 'Good heart but, not good enough to teach'),
(231, 3, 2, 4, 1, 15, 2, '2025-09-13 11:31:33', NULL),
(232, 3, 2, 4, 1, 16, 1, '2025-09-13 11:31:33', NULL),
(233, 3, 2, 4, 1, 17, 2, '2025-09-13 11:31:33', NULL),
(234, 3, 2, 4, 2, 18, 2, '2025-09-13 11:31:33', NULL),
(235, 3, 2, 4, 2, 19, 1, '2025-09-13 11:31:33', NULL),
(236, 3, 2, 4, 2, 20, 2, '2025-09-13 11:31:33', NULL),
(237, 3, 2, 4, 3, 21, 2, '2025-09-13 11:31:33', NULL),
(238, 3, 2, 4, 3, 22, 1, '2025-09-13 11:31:33', NULL),
(239, 3, 2, 4, 3, 23, 2, '2025-09-13 11:31:34', NULL),
(240, 3, 2, 4, 4, 24, 1, '2025-09-13 11:31:34', NULL),
(241, 3, 2, 4, 4, 25, 2, '2025-09-13 11:31:34', NULL),
(242, 3, 2, 4, 4, 26, 1, '2025-09-13 11:31:34', NULL),
(243, 3, 2, 4, 4, 27, 2, '2025-09-13 11:31:34', NULL),
(244, 3, 2, 4, 4, 28, 1, '2025-09-13 11:31:34', NULL),
(245, 3, 2, 4, 5, 29, 2, '2025-09-13 11:31:34', NULL),
(246, 3, 2, 4, 5, 30, 1, '2025-09-13 11:31:34', NULL),
(247, 3, 2, 4, 5, 31, 2, '2025-09-13 11:31:34', NULL),
(248, 3, 2, 4, 6, 32, 2, '2025-09-13 11:31:34', NULL),
(249, 3, 2, 4, 6, 33, 1, '2025-09-13 11:31:34', NULL),
(250, 3, 2, 4, 6, 34, 2, '2025-09-13 11:31:34', NULL),
(251, 3, 2, 4, 6, 35, 1, '2025-09-13 11:31:34', NULL),
(252, 3, 2, 4, 6, 36, 2, '2025-09-13 11:31:34', NULL),
(253, 3, 2, 4, 7, 37, 1, '2025-09-13 11:31:34', NULL),
(254, 3, 2, 4, 7, 38, 2, '2025-09-13 11:31:34', NULL),
(255, 3, 2, 4, 7, 39, 1, '2025-09-13 11:31:34', NULL),
(256, 3, 2, 4, 7, 40, 2, '2025-09-13 11:31:34', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

CREATE TABLE `faculties` (
  `id` int(11) NOT NULL,
  `faculty_id` varchar(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`id`, `faculty_id`, `full_name`, `department`, `position`, `created_at`) VALUES
(2, 'F001', 'Dr. Alice Smith', 'Computer Science', 'Professor', '2025-06-18 07:06:09'),
(3, 'F002', 'Mr. Bob Johnson', 'Information Technology', 'Instructor', '2025-06-18 07:06:09'),
(4, 'F003', 'Ms. Carol Davis', 'Math', 'Assistant Professor', '2025-06-18 07:06:09'),
(81, 'F004', 'Daniel Mercado Salazar', 'IT', 'Professor', '2025-09-08 09:26:15'),
(95, 'F006', 'mike quitos', 'Marketing', 'Professor', '2025-09-08 11:04:21'),
(110, 'F005', 'Niggatron Max', 'Marketing', 'Professor', '2025-09-09 10:38:00'),
(113, 'F007', 'Dante Suka Bayu', 'IT', 'Professor', '2025-09-09 11:24:30'),
(115, 'F008', 'MuayThai thailand', 'PE', 'Professor', '2025-09-10 11:56:11');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`) VALUES
(6, 'Niggatron@gcc.edu.ph', '5f830a14be90b96c217ed1e7b2e7a87a', '2025-09-12 13:16:04'),
(7, 'wendreipayad@gcc.edu.ph', 'be7038c019d13b3abaa4a756d49d2774', '2025-09-12 13:17:56');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `criteria_id` int(11) NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `criteria_id`, `text`) VALUES
(14, 1, 'Communicates with a calm demeanor and maintains professionalism throughtout the class.'),
(15, 1, 'Wears appropriate attire when in class.'),
(16, 1, 'Do not perform distracting behavior when teaching, such as eating and chewing, etc.'),
(17, 1, 'Does not use vulgar language, or profanity, and do not utter slanderous or defarnatory statements.'),
(18, 2, 'attend his/her class regulary and on time.'),
(19, 2, 'Dismisses class on time.'),
(20, 2, 'When scheduling a make-up class, he/she communicates with the class and ensure that everyone can attend the rescheduled class and other subject is affected.'),
(21, 3, 'Demonstrate sensitivity to the topic relayed to learners.'),
(22, 3, 'Do not use their position to influence the learners to follow or be against a partisan political activity or engage in any electioneering activities.'),
(23, 3, 'Do not use their position to influence the learners to follow or be against any church or religious sector.'),
(24, 4, 'Demostrate sensitivity to different kinds of learners.'),
(25, 4, 'Allow learners to participate in a discussion properly.'),
(26, 4, 'Accepts correction in the lesson if a learner points out any.'),
(27, 4, 'Discussions and activities are related to the subject course.'),
(28, 4, 'Enchances learners\' self-esteem through proper recognition.'),
(29, 5, 'Willingness to assist students\' subject-related concerns.'),
(30, 5, 'Integrates topics discussed by students and other related topics.'),
(31, 5, 'Raises problems and issues relevant to the topic discussed.'),
(32, 6, 'Preparedness with the lesson and providing instructional materials (ppt/handouts if available)'),
(33, 6, 'Maximizes the use of their class period.'),
(34, 6, 'Provides exercise/activities that is related to the topic.'),
(35, 6, 'Allow students to think independently.'),
(36, 6, 'Explain topics in matters of depth without relying on reading materials.'),
(37, 7, 'Mediates and tends immedialety if there are reported incidents within the class.'),
(38, 7, 'Promotes a healthy exchange of ideas.'),
(39, 7, 'Stimulates students\' desire and motivation, and inspires them to learn.'),
(40, 7, 'Provides ample amount for the deadline/submission of activities.');

-- --------------------------------------------------------

--
-- Table structure for table `register`
--

CREATE TABLE `register` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','faculties','students') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `register`
--

INSERT INTO `register` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'adminuser', 'admin@example.com', 'adminpass', 'admin', '2025-06-18 07:06:02'),
(2, 'faculty1', 'faculty1@example.com', 'pass1', 'faculties', '2025-06-18 07:06:09'),
(3, 'faculty2', 'faculty2@example.com', 'pass2', 'faculties', '2025-06-18 07:06:09'),
(4, 'faculty3', 'faculty3@example.com', 'pass3', 'faculties', '2025-06-18 07:06:09'),
(5, 'faculty4', 'faculty4@example.com', 'pass4', 'faculties', '2025-06-18 07:06:09'),
(6, 'faculty5', 'faculty5@example.com', 'pass5', 'faculties', '2025-06-18 07:06:09'),
(7, 'student1', 'student1@example.com', 's1pass', 'students', '2025-06-18 07:06:15'),
(8, 'student2', 'student2@example.com', 's2pass', 'students', '2025-06-18 07:06:15'),
(9, 'student3', 'student3@example.com', 's3pass', 'students', '2025-06-18 07:06:15');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `course` varchar(50) NOT NULL,
  `year_level` int(11) NOT NULL,
  `section` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `full_name`, `course`, `year_level`, `section`) VALUES
(1, 'STU001', 'John Doe', 'BSCS', 1, 'A'),
(2, 'STU002', 'Jane Roe', 'BSIT', 2, 'B'),
(3, 'STU003', 'Sam Lee', 'BSIT', 1, 'A'),
(4, '2025312067', 'Wendrei Sembrano Payad', 'BSA', 3, 'F'),
(111, '2025311111', 'Michael Steven Salazar', 'BSA', 1, 'D'),
(114, '2025313333', 'manga cum laude', 'BSA', 3, 'D');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `lec_hours` int(11) DEFAULT 0,
  `lab_hours` int(11) DEFAULT 0,
  `units` int(11) DEFAULT 0,
  `prerequisite` varchar(255) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `year_level` int(11) NOT NULL DEFAULT 1,
  `semester` enum('1','2') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `code`, `description`, `created_at`, `lec_hours`, `lab_hours`, `units`, `prerequisite`, `course`, `department`, `year_level`, `semester`) VALUES
(36, 'ACCTREVIEW', 'Basic Accounting Review and Accounting for Partnership and Corporation ', '2025-09-05 05:45:21', 6, 0, 6, 'NONE', 'BSA', NULL, 1, '1'),
(37, 'PA 15 ', 'Human Behavior in Organization ', '2025-09-05 06:04:59', 3, 0, 3, 'PA 9', 'BPA', NULL, 4, '1'),
(38, 'PA 16 ', 'Research Methods in PA 1', '2025-09-05 06:05:31', 3, 0, 3, '4TH YEAR', 'BPA', NULL, 4, '1'),
(39, 'PRACTICUM ', 'Work Integrated Learning ', '2025-09-05 06:06:06', 6, 0, 6, '4TH YEAR', 'BPA', NULL, 4, '1'),
(40, 'PA 17', 'Research Methods in PA 2', '2025-09-05 06:06:38', 3, 0, 3, 'PA 16', 'BPA', NULL, 4, '2'),
(41, 'PA 18', 'Special Topics for Public Administration ', '2025-09-05 06:06:59', 3, 0, 3, '4TH YEAR', 'BPA', NULL, 4, '2'),
(42, 'PA 19 ', 'Ethics and Accountability in Public Service ', '2025-09-05 06:07:16', 3, 0, 3, '4TH YEAR', 'BPA', NULL, 4, '2'),
(45, '1 GE UDS', 'Understanding the Self', '2025-09-05 06:09:31', 3, 0, 3, 'NONE', 'BPA', NULL, 1, '1'),
(46, '2GE HIST', 'Readings in the Philippine History', '2025-09-05 06:11:45', 3, 0, 3, 'NONE', 'BPA', NULL, 1, '1'),
(47, '3GE KOMFIL', 'Kontekstwalisadong Komunikasyon sa Filipino ', '2025-09-05 06:12:04', 3, 0, 3, 'NONE', 'BPA', NULL, 1, '1'),
(48, '4GE ELECT1', 'Social Science and Philosophy', '2025-09-05 06:12:18', 3, 0, 3, 'NONE', 'BPA', NULL, 1, '1'),
(49, 'ELECTIVE 1 ', 'Basic Computer Education ', '2025-09-05 06:12:33', 3, 0, 3, 'NONE', 'BPA', NULL, 1, '1'),
(50, 'NSTP 1', 'Civic Welfare Training Service 1', '2025-09-05 06:12:47', 3, 0, 3, 'NONE', 'BPA', NULL, 1, '1'),
(51, 'PE 1 ', 'Physical Education', '2025-09-05 06:12:58', 3, 0, 2, 'NONE', 'BPA', NULL, 1, '1'),
(52, 'Per Dev ', 'Personality Development ', '2025-09-05 06:13:12', 3, 0, 3, 'NONE', 'BPA', NULL, 1, '1'),
(53, '5GE MATH', 'Math in the Modern World ', '2025-09-05 06:14:27', 3, 0, 3, 'NONE', 'BPA', NULL, 1, '2'),
(54, '6GE COM', 'Purposive Communication', '2025-09-05 06:14:43', 3, 0, 3, 'NONE', 'BPA', NULL, 1, '2'),
(55, '7GE FILDIS ', 'Filipino sa Iba\'t ibang Disiplina', '2025-09-05 06:15:01', 3, 0, 3, 'NONE', 'BPA', NULL, 1, '2'),
(56, '8 GE ART ', 'Art Appreciation', '2025-09-05 06:15:15', 3, 0, 3, 'NONE', 'BPA', NULL, 1, '2'),
(57, 'NSTP 2', 'Civic Welfare Training Service 2 ', '2025-09-05 06:15:34', 3, 0, 3, 'NSTP 1', 'BPA', NULL, 1, '2'),
(58, 'PA 1', 'Intro to Public Administration', '2025-09-05 06:15:53', 3, 0, 3, '', 'BPA', NULL, 1, '2'),
(59, 'PE 2 ', 'PE 2 ', '2025-09-05 06:16:12', 0, 0, 2, 'PE 1', 'BPA', NULL, 1, '2'),
(60, '10GE RIZAL ', 'Life and Works of Rizal ', '2025-09-05 06:16:46', 3, 0, 3, 'NONE', 'BPA', NULL, 2, '1'),
(61, '9GE ELEC 1', 'People and Earth\'s Ecosystems', '2025-09-05 06:17:01', 3, 0, 3, 'NONE', 'BPA', NULL, 2, '1'),
(62, 'ECON ', 'Basic Economics with Land Reform and Taxation', '2025-09-05 06:18:22', 3, 0, 3, 'NONE', 'BPA', NULL, 2, '1'),
(63, 'GE Ethics ', 'Ethics', '2025-09-05 06:18:39', 0, 0, 3, '', 'BPA', NULL, 2, '1'),
(64, 'PA 2 ', 'Philippine Administrative Thought ', '2025-09-05 06:19:12', 3, 0, 3, 'PA 1', 'BPA', NULL, 2, '1'),
(65, 'PE 3 ', 'PE 3 ', '2025-09-05 06:19:48', 0, 0, 2, 'PE 2', 'BPA', NULL, 2, '1'),
(66, 'SPECCORE1', 'Corporate Governance and Social Responsibility', '2025-09-05 06:20:11', 3, 0, 3, '2nd yr', 'BPA', NULL, 2, '1'),
(67, 'SPECCORE2 ', 'Elementary Statistics ', '2025-09-05 06:20:30', 3, 0, 3, '2nd yr', 'BPA', NULL, 2, '1'),
(68, '11 GE ELEC 2', 'Gender and Society ', '2025-09-05 06:20:57', 3, 0, 3, 'NONE', 'BPA', NULL, 2, '2'),
(69, '12 GE ELEC 3 ', 'Indigeneous Creative Crafts ', '2025-09-05 06:28:47', 3, 0, 3, 'NONE', 'BPA', NULL, 2, '2'),
(70, 'PA 3 ', 'Office and Systems Management ', '2025-09-05 06:29:07', 3, 0, 3, '3RD YR', 'BPA', NULL, 2, '2'),
(71, 'PA 4 ', 'Knowledge Management and ICT for PA ', '2025-09-05 06:29:29', 3, 0, 3, 'ELECTIVE 1', 'BPA', NULL, 2, '2'),
(73, 'PE 4', 'PE 4', '2025-09-05 06:37:44', 0, 0, 2, 'PE 3', 'BPA', NULL, 2, '2'),
(75, 'SPECCORE 3 ', 'Philippine Government and Constitution ', '2025-09-05 06:38:32', 3, 0, 3, 'NONE', 'BPA', NULL, 2, '2'),
(76, 'SPECCORE 4', 'Basic Accounting ', '2025-09-05 06:38:56', 3, 0, 3, '3RD YR', 'BPA', NULL, 2, '2'),
(77, 'PA 5', 'Public Accounting and Budgeting', '2025-09-05 06:40:52', 3, 0, 3, 'SPECCORE 4', 'BPA', NULL, 3, '1'),
(78, 'PA 6 ', 'Local and Regional Governance', '2025-09-05 06:41:32', 3, 0, 3, '3RD YR', 'BPA', NULL, 3, '1'),
(79, 'PA 7', 'Public Personnel Administration', '2025-09-05 06:41:50', 3, 0, 3, '3RD YR', 'BPA', NULL, 3, '1'),
(80, 'PA 8', 'Public Fiscal Administration', '2025-09-05 06:42:16', 3, 0, 3, '3RD YR', 'BPA', NULL, 3, '1'),
(81, 'PA 9 ', 'Organization and Management', '2025-09-05 06:42:32', 3, 0, 3, '3RD YR', 'BPA', NULL, 3, '1'),
(82, 'ELECTIVE 2 ', 'Environmental Management', '2025-09-05 06:42:45', 3, 0, 3, '3RD YR', 'BPA', NULL, 3, '1'),
(83, 'ELECTIVE 3', 'Policy Analysis', '2025-09-05 06:43:13', 3, 0, 3, '3RD YR', 'BPA', NULL, 3, '1'),
(84, 'PA 10 ', 'Governance and Development ', '2025-09-05 06:43:40', 3, 0, 3, 'PA 6', 'BPA', NULL, 3, '2'),
(85, 'PA11', 'Administrative Law', '2025-09-05 06:44:02', 3, 0, 3, '3RD YR', 'BPA', NULL, 3, '2'),
(86, 'PA 12', 'Public Policy and Program Administration', '2025-09-05 06:44:33', 3, 0, 3, 'elective 3', 'BPA', NULL, 3, '2'),
(87, 'PA 13 ', 'Politics and Administration ', '2025-09-05 06:44:56', 3, 0, 3, '3RD YR', 'BPA', NULL, 3, '2'),
(88, 'PA 14 ', 'Leadership and Decision Making', '2025-09-05 06:45:25', 3, 0, 3, '3RD YR', 'BPA', NULL, 3, '2'),
(89, 'ELECTIVE 4 ', 'Government Auditing', '2025-09-05 06:45:49', 3, 0, 3, 'PA 5 & PA 8', 'BPA', NULL, 3, '2'),
(90, 'ELECTIVE 5 ', 'Project Development and Management', '2025-09-05 06:46:07', 3, 0, 3, '3RD YR', 'BPA', NULL, 3, '2'),
(91, 'ConWorld ', 'The Contemporary World', '2025-09-05 06:46:27', 3, 0, 3, 'NONE', 'BPA', NULL, 3, '2'),
(92, 'MGTECON', 'Managerial Economics', '2025-09-05 07:06:39', 3, 0, 3, 'NONE', 'BSA', NULL, 1, '1'),
(93, 'MGTSCI', 'Management Science', '2025-09-05 07:07:07', 3, 0, 3, 'NONE', 'BSA', NULL, 1, '1'),
(95, '1GE UDS', 'Understanding the Self', '2025-09-05 07:07:36', 3, 0, 3, 'NONE', 'BSA', NULL, 1, '1'),
(96, '2GE HIST', 'Readings in the Philippine History', '2025-09-05 07:21:53', 3, 0, 3, 'NONE', 'BSA', NULL, 1, '1'),
(97, 'KomFil', 'Kontekstwalisadong Komunikasyon sa Filipino ', '2025-09-05 07:22:15', 3, 0, 3, 'NONE', 'BSA', NULL, 1, '1'),
(98, 'PE 1 ', 'Physical Education', '2025-09-05 07:22:34', 2, 0, 2, 'NONE', 'BSA', NULL, 1, '1'),
(99, 'NSTP 1', 'NSTP 1', '2025-09-05 07:23:17', 3, 0, 3, 'NONE', 'BSA', NULL, 1, '1'),
(100, 'Mngt', '*Organization and Management', '2025-09-05 07:25:00', 3, 0, 0, 'Non-Grade 12 Graudates', 'BSA', NULL, 1, '1'),
(101, 'BusMath', '*Business Mathematics', '2025-09-05 07:25:35', 3, 0, 0, 'Non-Grade 12 Graudates', 'BSA', NULL, 1, '1'),
(103, 'OMTQM', 'Operations and Management and TQM', '2025-09-05 07:28:29', 3, 0, 3, 'NONE', 'BSA', NULL, 1, '2'),
(105, 'FAR', 'Financial Accounting and Reporting', '2025-09-05 07:37:20', 3, 0, 3, 'ACCTREVIEW', 'BSA', NULL, 1, '2'),
(106, 'CFRAMEWORK', 'Conceptual Framework and Accounting Standards', '2025-09-05 07:49:42', 3, 0, 3, 'ACCTREVIEW', 'BSA', NULL, 1, '2'),
(107, 'COSTACC', 'Cost Accounting and Control', '2025-09-05 07:50:31', 3, 0, 3, 'NONE', 'BSA', NULL, 1, '2'),
(109, '3GE KOMFIL', 'Filipino sa Iba\'t ibang Disiplina', '2025-09-05 07:54:02', 3, 0, 3, 'KOMFIL', 'BSA', NULL, 1, '2'),
(110, '3GE COM', 'Purposive Communication', '2025-09-05 07:54:45', 3, 0, 3, 'NONE', 'BSA', NULL, 1, '2'),
(111, '4GE MATH', 'Math in the Modern World ', '2025-09-05 07:55:48', 3, 0, 3, 'NONE', 'BSA', NULL, 1, '2'),
(112, 'PE 2 ', 'Physical Education 2', '2025-09-05 07:56:16', 2, 0, 2, 'PE 1', 'BSA', NULL, 1, '2'),
(113, 'BusFin', '*Business Finance', '2025-09-05 07:58:26', 3, 0, 0, 'Non-Grade 12 Graudates', 'BSA', NULL, 1, '2'),
(114, 'ApEcon', '*Applied Economics', '2025-09-05 07:58:58', 3, 0, 0, 'Non-Grade 12 Graudates', 'BSA', NULL, 1, '2'),
(115, 'NSTP 2', 'NSTP 2', '2025-09-05 07:59:39', 3, 0, 3, 'NSTP 1', 'BSA', NULL, 1, '2'),
(116, 'ECONDEV', 'Economic Development', '2025-09-05 08:00:56', 3, 0, 3, 'NONE', 'BSA', NULL, 2, '1'),
(117, 'INTERACCT 1', 'Intermediate Accounting 1', '2025-09-05 08:04:21', 6, 0, 6, 'FAR', 'BSA', NULL, 2, '1'),
(118, 'INCOMETAX', 'Income Taxation', '2025-09-05 08:04:56', 3, 0, 3, '2nd yr Standing', 'BSA', NULL, 2, '1'),
(119, 'STRACOST', 'Strategic Cost Management', '2025-09-05 08:08:38', 3, 0, 3, 'COSTACC', 'BSA', NULL, 2, '1'),
(120, 'ITAPPTOOLS', 'IT Application Tools in Business', '2025-09-05 08:09:23', 1, 2, 3, '2nd yr Standing', 'BSA', NULL, 2, '1'),
(122, 'OBLICON', 'Law on Obligations and Contracts', '2025-09-05 08:15:59', 3, 0, 3, 'NONE', 'BSA', NULL, 2, '1'),
(123, 'FINMAR', 'Financial Markets', '2025-09-05 08:16:24', 3, 0, 3, 'NONE', 'BSA', NULL, 2, '1'),
(124, 'PE 3 ', 'Physical Education 3', '2025-09-05 08:19:49', 2, 0, 2, 'PE 2', 'BSA', NULL, 2, '1'),
(125, '5GE art', 'Art Appreciation', '2025-09-05 08:20:09', 3, 0, 3, 'NONE', 'BSA', NULL, 2, '1'),
(126, 'GE ELEC 3', 'Gender and Society ', '2025-09-05 08:23:33', 3, 0, 3, 'NONE', 'BSA', NULL, 2, '1'),
(127, 'INTERACCT II', 'Intermediate Accounting II', '2025-09-05 08:25:25', 6, 0, 6, 'INTERACCT II', 'BSA', NULL, 2, '2'),
(128, 'BUSTAX', 'Business Taxation ', '2025-09-05 08:29:05', 3, 0, 3, 'INCOMETAX', 'BSA', NULL, 2, '2'),
(129, 'BUSLAW', 'Business Laws and Regulations', '2025-09-05 08:37:18', 3, 0, 3, 'OBLICON', 'BSA', NULL, 2, '2'),
(130, 'STATSOFTAPP', 'Statistical Analysis with Software Applications', '2025-09-05 08:38:53', 1, 2, 3, 'ITAPPTOOLS', 'BSA', NULL, 2, '2'),
(131, 'FINMAN', 'Financial Management', '2025-09-05 08:42:25', 3, 0, 3, 'FINMAR', 'BSA', NULL, 2, '2'),
(132, 'SPLCTRANS', 'Accounting for Specialized Transactions', '2025-09-05 08:44:26', 3, 0, 3, 'NONE', 'BSA', NULL, 2, '2'),
(133, '6GE STS', 'Science, Technology and Society', '2025-09-05 08:45:14', 3, 0, 3, 'NONE', 'BSA', NULL, 2, '2'),
(134, '7GE ETHICS', 'Ethics', '2025-09-05 08:45:47', 3, 0, 3, 'NONE', 'BSA', NULL, 2, '2'),
(135, 'PE 4', 'Physical Education 4', '2025-09-05 08:46:20', 2, 0, 2, 'PE 3', 'BSA', NULL, 2, '2'),
(136, '1PRE HBO', 'Human Behavior in Organization ', '2025-09-05 08:47:02', 3, 0, 3, '2nd yr Standing', 'BSA', NULL, 2, '2'),
(137, 'INTERACCT III', 'Intermediate Accounting III', '2025-09-05 08:47:50', 3, 0, 3, 'INTERACCT III', 'BSA', NULL, 3, '1'),
(139, 'AIS', 'Accounting Information System', '2025-09-05 08:49:43', 1, 2, 3, 'ITAPPTOOLS', 'BSA', NULL, 3, '1'),
(140, '2PrE Val', 'Valuation Concepts and Methods ', '2025-09-05 08:50:49', 3, 0, 3, '3rd yr Standing', 'BSA', NULL, 3, '1'),
(141, 'AUDPRIN', 'Auditing and Assurance Principles', '2025-09-05 08:53:26', 3, 0, 3, 'INTERACCT II', 'BSA', NULL, 3, '1'),
(142, 'AACONAPP I ', 'Auditing and Assurance: Concepts and Applications 1', '2025-09-05 08:54:27', 3, 0, 3, 'INTERACCT III', 'BSA', NULL, 3, '1'),
(143, 'REGFREAME', 'Regulatory Framework and Legal Issues in Business', '2025-09-05 08:55:24', 3, 0, 3, 'BUSLAW', 'BSA', NULL, 3, '1'),
(144, 'IBT', 'International Business Trade', '2025-09-05 08:55:58', 3, 0, 3, '3rd yr Standing', 'BSA', NULL, 3, '1'),
(145, '8 GE ELEC1', 'The Entrepreneurial Mind', '2025-09-05 08:57:11', 3, 0, 3, 'NONE', 'BSA', NULL, 3, '1'),
(146, '9 GE ELEC1', 'Environmental Science', '2025-09-05 08:57:58', 3, 0, 3, 'NONE', 'BSA', NULL, 3, '1'),
(147, 'RESMETHODS', 'Accounting Research Methods', '2025-09-05 08:58:55', 3, 0, 3, '3rd yr Standing', 'BSA', NULL, 3, '1'),
(149, 'ACCRESEACH', 'Accounting Research', '2025-09-05 09:01:56', 3, 0, 3, '3rd yr Standing REMETHODS', 'BSA', NULL, 3, '2'),
(150, 'INTTERNSHIP', 'Accounting Internship', '2025-09-05 09:03:12', 6, 0, 6, '4th yr Standing', 'BSA', NULL, 3, '2'),
(151, 'ADVACC', 'Advanced Accounting', '2025-09-05 09:05:00', 3, 0, 3, 'INTERACCT III', 'BSA', NULL, 3, '2'),
(152, 'AUDCIS', 'Auditing in CIS Environment', '2025-09-05 09:05:56', 1, 2, 3, '4th yr Standing', 'BSA', NULL, 4, '1'),
(153, 'STRATMAN', 'Strategic Management', '2025-09-05 09:07:10', 3, 0, 3, '3rd yr Standing ', 'BSA', NULL, 4, '1'),
(155, 'GOVeth', 'Governance, Business Ethics, Risk Management', '2025-09-05 09:09:46', 3, 0, 3, '3rd yr Standing', 'BSA', NULL, 4, '1'),
(157, 'AACONAPP II ', 'Auditing and Assurance: Concepts and Applications 2', '2025-09-05 09:10:56', 3, 0, 3, 'ACCONAPP I', 'BSA', NULL, 4, '1'),
(158, 'AASPECIAL', 'Auditing and Assurance: Specialized Industries', '2025-09-05 09:12:02', 3, 0, 3, 'ACCONAPP I', 'BSA', NULL, 4, '1'),
(159, 'ACCGOVNPO', 'Accounting Government and Non-Profit Organization', '2025-09-05 09:13:30', 3, 0, 3, 'BUSCOMBI', 'BSA', NULL, 4, '1'),
(160, 'BUSCOMBI', 'Accounting for Business Combination', '2025-09-05 09:14:38', 3, 0, 3, 'SPCLTRANS', 'BSA', NULL, 4, '1'),
(161, 'MANACC', 'Management Accounting', '2025-09-05 09:15:19', 3, 0, 3, 'ADVACC', 'BSA', NULL, 4, '1'),
(162, '11 GE CONTEM', 'The Contemporary World', '2025-09-05 09:15:42', 3, 0, 3, 'NONE', 'BSA', NULL, 4, '1'),
(163, '10GE RIZAL ', 'Life and Works of Rizal ', '2025-09-05 09:16:59', 3, 0, 3, 'NONE', 'BSA', NULL, 4, '2'),
(164, '4PrE OA', 'Operations Auditing', '2025-09-05 09:17:46', 3, 0, 3, '4th yr Standing', 'BSA', NULL, 4, '2'),
(165, 'INTEGRA', 'Integrated Review', '2025-09-05 09:18:29', 12, 0, 12, '4th yr Standing', 'BSA', NULL, 4, '2'),
(166, '3PrW TEACH', 'Principles and Methods of Teaching Accounting ', '2025-09-05 09:19:22', 3, 0, 3, '4th yr Standing', 'BSA', NULL, 4, '2'),
(167, 'SBA ', 'Strategic Business Analysis', '2025-09-05 09:20:10', 3, 0, 3, '3rd yr Standing', 'BSA', NULL, 4, '2'),
(168, 'ENTREP1', 'Entrepreneurial Behavior', '2025-09-05 09:40:02', 3, 0, 3, 'NONE', 'BSE', NULL, 1, '1'),
(169, '1GE UDS', 'Understanding the Self', '2025-09-05 09:40:54', 3, 0, 3, 'NONE', 'BSE', NULL, 1, '1'),
(170, '2GE HIST', 'Readings in the Philippine History', '2025-09-05 09:41:07', 3, 0, 3, 'NONE', 'BSE', NULL, 1, '1'),
(171, '3GE ART', 'Art Appreciation', '2025-09-05 09:42:34', 3, 0, 3, 'NONE', 'BSE', NULL, 1, '1'),
(172, 'KomFil', 'Komunikasyon sa Akademikong Filipino ', '2025-09-05 09:43:34', 3, 0, 3, 'NONE', 'BSE', NULL, 1, '1'),
(173, 'PE 1 ', 'Physical Education 1', '2025-09-05 09:43:51', 2, 0, 2, 'NONE', 'BSE', NULL, 1, '1'),
(174, 'NSTP 1', 'Civic Welfare Training Service 1', '2025-09-05 09:44:06', 3, 0, 3, 'NONE', 'BSE', NULL, 1, '1'),
(175, 'MNGT ', '*Organization and Management', '2025-09-05 09:45:09', 3, 0, 0, 'Non-Grade 12 Graudates', 'BSE', NULL, 1, '1'),
(176, 'MKTG', '*Principles pf Marketing', '2025-09-05 09:45:42', 3, 0, 0, 'Non-Grade 12 Graudates', 'BSE', NULL, 1, '1'),
(177, 'BUSMath', '*Business Mathematics', '2025-09-05 09:46:12', 3, 0, 0, 'Non-Grade 12 Graudates', 'BSE', NULL, 1, '1'),
(178, 'ENTREP 2', 'Microeconomics', '2025-09-05 09:47:02', 3, 0, 3, 'NONE', 'BSE', NULL, 1, '2'),
(179, '4GE MATH', 'Math in the Modern World ', '2025-09-05 09:52:02', 3, 0, 3, 'NONE', 'BSE', NULL, 1, '2'),
(180, '5GE COM', 'Purposive Communication', '2025-09-05 09:52:25', 3, 0, 3, 'NONE', 'BSE', NULL, 1, '2'),
(181, '6GE STS', 'Science, Technology and Society', '2025-09-05 09:52:47', 3, 0, 3, 'NONE', 'BSE', NULL, 1, '2'),
(182, 'FilDis', 'Filipino sa Iba\'t ibang Disiplina', '2025-09-05 09:53:02', 3, 0, 3, 'NONE', 'BSE', NULL, 1, '2'),
(183, 'PE 2 ', 'Physical Education 2', '2025-09-05 09:53:28', 0, 0, 2, 'PE 1', 'BSE', NULL, 1, '2'),
(184, 'NSTP 2', 'Civic Welfare Training Service 3', '2025-09-05 09:54:02', 0, 0, 3, 'NSTP 1', 'BSE', NULL, 1, '2'),
(185, 'BusFin', '*Business Finance', '2025-09-05 09:55:30', 3, 0, 0, 'Non-Grade 12 Graudates', 'BSE', NULL, 1, '2'),
(187, 'ENTREP 3', 'Opportunity Seeking', '2025-09-05 09:57:02', 3, 0, 3, 'ENTREP 1', 'BSE', NULL, 2, '1'),
(188, 'ELECTIVE 1 ', 'Entrepreneurial Behavior in Organization', '2025-09-05 09:57:36', 3, 0, 3, 'ENTREP 1', 'BSE', NULL, 2, '1'),
(189, '7GE ETHICS', 'Ethics', '2025-09-05 09:57:53', 3, 0, 3, 'NONE', 'BSE', NULL, 2, '1'),
(190, '8GE Rizal', 'Life and Works of Rizal ', '2025-09-05 09:58:14', 3, 0, 3, 'NONE', 'BSE', NULL, 2, '1'),
(191, '9 GE ELEC1', 'Gender and Society ', '2025-09-05 09:58:32', 3, 0, 3, 'NONE', 'BSE', NULL, 2, '1'),
(192, 'PE 3 ', 'Physical Education 3', '2025-09-05 09:58:54', 0, 0, 2, 'PE 2', 'BSE', NULL, 2, '1'),
(193, 'ENTREP 4', 'Market Research and Consumer Behavior', '2025-09-05 10:01:05', 3, 0, 3, 'ENTREP 3', 'BSE', NULL, 2, '2'),
(195, 'ENTREP 6', 'Pricing and Costing ', '2025-09-05 10:02:44', 3, 0, 3, '2nd yr Standing', 'BSE', NULL, 2, '2'),
(196, 'ENTREP 7', 'Human Resource Management ', '2025-09-05 10:03:20', 3, 0, 3, '2nd yr Standing', 'BSE', NULL, 2, '2'),
(197, 'ENTREP 5', 'Innovation Management', '2025-09-05 10:04:04', 3, 0, 3, 'ENTREP 3', 'BSE', NULL, 2, '2'),
(198, '10GE ELEC 3', 'Environmental Science', '2025-09-05 10:04:51', 3, 0, 3, 'NONE', 'BSE', NULL, 2, '2'),
(199, '11 GE ELEC 2', 'Personality Development ', '2025-09-05 10:05:22', 3, 0, 3, 'NONE', 'BSE', NULL, 2, '2'),
(200, 'PE 4', 'Physical Education 4 ', '2025-09-05 10:06:54', 0, 0, 2, 'PE 3', 'BSE', NULL, 2, '2'),
(201, 'ENTREP 8', 'Financial Management (Financial Analysis fir Decision Making)', '2025-09-05 10:07:54', 3, 0, 3, '3rd yr Standing', 'BSE', NULL, 3, '1'),
(202, 'CBMEC TQM', 'Production and Operations Management(TQM)', '2025-09-05 10:08:44', 3, 0, 3, '3rd yr Standing', 'BSE', NULL, 3, '1'),
(203, '12GE Cworld', 'The Contemporary World', '2025-09-05 10:09:09', 3, 0, 3, 'NONE', 'BSE', NULL, 3, '1'),
(204, 'TRACK 1', 'Special Track 1(Business Project in Service Management)', '2025-09-05 10:10:06', 3, 0, 3, '3rd yr Standing', 'BSE', NULL, 3, '1'),
(205, 'ELECTIVE 2 ', 'Entrepreneurial Marketing Strategy', '2025-09-05 10:11:04', 3, 0, 3, '3rd yr Standing', 'BSE', NULL, 3, '1'),
(207, 'ENTREP 9', 'Programs and Policies on Entreprise Development', '2025-09-05 10:14:58', 3, 0, 3, '3rd yr Standing', 'BSE', NULL, 3, '1'),
(208, 'ELECTIVE ', 'Microfinancing', '2025-09-05 10:16:39', 3, 0, 3, '3rd yr Standing', 'BSE', NULL, 3, '1'),
(209, 'ENTREP 10', 'Business Plan Preparation', '2025-09-05 10:17:22', 3, 0, 3, '3rd yr Standing', 'BSE', NULL, 3, '2'),
(210, 'ENTREP 11', 'International Business Trade', '2025-09-05 10:18:55', 3, 0, 3, '3rd yr Standing', 'BSE', NULL, 3, '2'),
(211, 'ENTREP 12', 'Business Laws and Tax w/focus on law Affecting', '2025-09-05 10:19:45', 3, 0, 3, '3rd yr Standing', 'BSE', NULL, 3, '2'),
(212, 'CBMEC SM', 'Strategic Management', '2025-09-05 10:20:10', 3, 0, 3, '3rd yr Standing', 'BSE', NULL, 3, '2'),
(213, 'TRACK 2', 'Special Track 2(Business Project in Franchising/Distributorship Structure', '2025-09-05 10:21:49', 3, 0, 3, '3rd yr Standing', 'BSE', NULL, 3, '2'),
(214, 'ELECTIVE 4 ', 'Wholesale and Retail Sales Management', '2025-09-05 10:22:21', 3, 0, 3, '3rd yr Standing', 'BSE', NULL, 3, '2'),
(215, 'ENTREP 13', 'Business Plan Implementation(Product Development and Marketing Analysis)', '2025-09-05 10:24:20', 2, 3, 5, 'ENTREP 10', 'BSE', NULL, 4, '1'),
(216, 'ENTREP 14', 'Social Entrepreneurship', '2025-09-05 10:25:50', 0, 0, 3, '3rd yr Standing', 'BSE', NULL, 4, '1'),
(217, 'TRACK 3', 'Special Track 3(Business Project in Agribusiness)', '2025-09-05 10:26:46', 0, 0, 3, '4th yr Standing', 'BSE', NULL, 4, '1'),
(218, 'ENTREP 15', 'Business Plan Implentation II', '2025-09-05 10:27:59', 2, 3, 5, 'ENTREP 13', 'BSE', NULL, 4, '2'),
(219, 'TRACK 4', 'Special Track 4 (Business Project in Manufacturing)', '2025-09-05 10:29:18', 0, 0, 3, '4th yr Standing', 'BSE', NULL, 4, '2'),
(220, 'ACCTREVIEW', 'Basic Accounting Review and Accounting for Partnership and Corporation ', '2025-09-05 10:33:48', 6, 0, 6, 'NONE', 'BSAIS', NULL, 1, '1'),
(221, 'MGTECON', 'Managerial Economics', '2025-09-05 10:34:08', 3, 0, 3, 'NONE', 'BSAIS', NULL, 1, '1'),
(222, 'MGTSCI', 'Management Science', '2025-09-05 10:34:25', 3, 0, 3, 'NONE', 'BSAIS', NULL, 1, '1'),
(223, '1GE UDS', 'Understanding the Self', '2025-09-05 10:34:36', 3, 0, 3, 'NONE', 'BSAIS', NULL, 1, '1'),
(224, '2GE HIST', 'Readings in the Philippine History', '2025-09-05 10:34:50', 3, 0, 3, 'NONE', 'BSAIS', NULL, 1, '1'),
(225, '3GE KOMFIL', 'Kontekstwalisadong Komunikasyon sa Filipino ', '2025-09-05 10:35:07', 3, 0, 3, 'NONE', 'BSAIS', NULL, 1, '1'),
(226, 'PE 1 ', 'Physical Education1', '2025-09-05 10:35:20', 2, 0, 2, 'NONE', 'BSAIS', NULL, 1, '1'),
(227, 'NSTP 1', 'NSTP 1', '2025-09-05 10:35:36', 3, 0, 3, 'NONE', 'BSAIS', NULL, 1, '1'),
(229, 'BusMath', '*Business Mathematics', '2025-09-05 10:37:34', 3, 0, 0, 'Non-Grade 12 Graudates', 'BSAIS', NULL, 1, '1'),
(230, 'MNGT ', '*Organization and Management', '2025-09-05 10:38:19', 3, 0, 0, 'Non-Grade 12 Graudates', 'BSAIS', NULL, 1, '1'),
(231, 'FAR', 'Financial Accounting and Reporting', '2025-09-05 10:40:29', 3, 0, 3, 'ACCTREVIEW', 'BSAIS', NULL, 1, '2'),
(232, 'CMTOM', 'Operations and Management and TQM', '2025-09-05 10:40:52', 3, 0, 3, 'NONE', 'BSAIS', NULL, 1, '2'),
(233, 'COSTACC', 'Cost Accounting and Control', '2025-09-05 10:41:18', 3, 0, 3, 'NONE', 'BSAIS', NULL, 1, '2'),
(234, 'CFRAMEWORK', 'Conceptual Framework and Accounting Standards', '2025-09-05 10:42:02', 3, 0, 3, 'ACCTREVIEW', 'BSAIS', NULL, 1, '2'),
(235, '4GE FILDIS', 'Filipino sa Iba\'t ibang Disiplina', '2025-09-05 10:42:46', 3, 0, 3, 'KOMFIL', 'BSAIS', NULL, 1, '2'),
(236, '5GE COM', 'Purposive Communication', '2025-09-05 10:43:15', 3, 0, 3, 'NONE', 'BSAIS', NULL, 1, '2'),
(237, '6GE MATH', 'Mathematics in the Modern World ', '2025-09-05 10:44:03', 3, 0, 3, 'NONE', 'BSAIS', NULL, 1, '2'),
(238, 'PE 2 ', 'Physical Education 2', '2025-09-05 10:44:34', 2, 0, 2, 'PE 1', 'BSAIS', NULL, 1, '2'),
(239, 'NSTP 2', 'NSTP 2', '2025-09-05 10:45:06', 3, 0, 3, 'NSTP 1', 'BSAIS', NULL, 1, '2'),
(240, 'BusFin', '*Business Finance', '2025-09-05 10:46:19', 3, 0, 0, 'Non-Grade 12 Graudates', 'BSAIS', NULL, 1, '2'),
(241, 'ApEcon', '*Applied Economics', '2025-09-05 10:46:43', 3, 0, 0, 'Non-Grade 12 Graudates', 'BSAIS', NULL, 1, '2'),
(242, 'ECONDEV', 'Economic Development', '2025-09-05 10:47:20', 3, 0, 3, 'NONE', 'BSAIS', NULL, 2, '1'),
(243, 'INTERACCT I', 'Intermediate Accounting I', '2025-09-05 10:47:56', 6, 0, 6, 'FAR', 'BSAIS', NULL, 2, '1'),
(244, 'INCCOMETAX', 'Income Taxation', '2025-09-05 10:48:22', 3, 0, 3, '2nd yr', 'BSAIS', NULL, 2, '1'),
(245, 'STRACOST', 'Strategic Cost Management', '2025-09-05 10:48:55', 3, 0, 3, 'COSTACC', 'BSAIS', NULL, 2, '1'),
(246, 'AIS 1', 'Introduction to Accounting Information System', '2025-09-05 10:49:38', 3, 0, 3, 'NONE', 'BSAIS', NULL, 2, '1'),
(247, 'OBLICON', 'Law on Obligations and Contracts', '2025-09-05 10:50:21', 3, 0, 3, 'NONE', 'BSAIS', NULL, 2, '1'),
(248, 'FINMAR', 'Financial Markets', '2025-09-05 10:50:35', 3, 0, 3, 'NONE', 'BSAIS', NULL, 2, '1'),
(249, 'PE 3 ', 'Physical Education 3', '2025-09-05 10:50:54', 2, 0, 2, 'PE 2', 'BSAIS', NULL, 2, '1'),
(250, 'INTERACCT II', 'Intermediate Accounting II', '2025-09-05 10:52:46', 6, 0, 6, 'INTERACCT I', 'BSAIS', NULL, 2, '2'),
(251, 'BUSTAX', 'Business Taxation ', '2025-09-05 10:53:17', 3, 0, 3, 'INCOMETAX', 'BSAIS', NULL, 2, '2'),
(252, 'BUSLAW', 'Business Laws and Regulations', '2025-09-05 10:53:51', 3, 0, 3, 'OBLICON', 'BSAIS', NULL, 2, '2'),
(253, 'STATSOFTAPP', 'Statistical Analysis with Software Applications', '2025-09-05 10:54:27', 0, 3, 3, 'ITAPPTOOLS', 'BSAIS', NULL, 2, '2'),
(254, 'FINMAN', 'Financial Management', '2025-09-05 10:55:10', 3, 0, 3, 'FINMAR', 'BSAIS', NULL, 2, '2'),
(255, 'ITAPPTOOLS', 'IT Application Tools in Business', '2025-09-05 10:55:41', 0, 3, 3, 'AIS 1', 'BSAIS', NULL, 2, '2'),
(256, '7GE ETHICS', 'Ethics', '2025-09-05 10:56:02', 3, 0, 3, 'NONE', 'BSAIS', NULL, 2, '2'),
(257, 'PE 4', 'Physical Education 4', '2025-09-05 10:56:26', 2, 0, 2, 'PE 3', 'BSAIS', NULL, 2, '2'),
(258, 'INTERACCT III', 'Intermediate Accounting III', '2025-09-05 10:57:09', 3, 0, 3, 'INTERACCT II', 'BSAIS', NULL, 3, '1'),
(259, 'REGFRAME', 'Regulatory Framework and Legal Issues in Business', '2025-09-05 10:57:56', 3, 0, 3, 'OBLICON', 'BSAIS', NULL, 3, '1'),
(260, 'IBT', 'International Business Trade', '2025-09-05 10:58:36', 3, 0, 3, '3rd yr Standing', 'BSAIS', NULL, 3, '1'),
(261, 'ISAD', 'Information System Anaylsis and Design', '2025-09-05 10:59:36', 0, 3, 3, 'AIS 1', 'BSAIS', NULL, 3, '1'),
(262, 'ProMan', 'Project Management', '2025-09-05 11:00:13', 3, 0, 3, '3rd yr Standing', 'BSAIS', NULL, 3, '1'),
(263, 'SBA', 'Strategic Business Analysis', '2025-09-05 11:00:42', 3, 0, 3, '3rd yr Standing', 'BSAIS', NULL, 3, '1'),
(264, '8GE ART', 'Art Appreciation', '2025-09-05 11:01:15', 3, 0, 3, 'NONE', 'BSAIS', NULL, 3, '1'),
(265, '9GE STS', 'Science, Technology and Society', '2025-09-05 11:01:35', 3, 0, 3, 'NONE', 'BSAIS', NULL, 3, '1'),
(266, '10GE ELECTIVE', 'Business Logic', '2025-09-05 11:02:12', 3, 0, 3, 'NONE', 'BSAIS', NULL, 3, '1'),
(267, '11GE CWORLD', 'The Contemporary World', '2025-09-05 11:02:45', 3, 0, 3, 'NONE', 'BSAIS', NULL, 3, '2'),
(268, '12GE RIZAL', 'Life and Works of Rizal ', '2025-09-05 11:03:45', 3, 0, 3, 'NONE', 'BSAIS', NULL, 3, '2'),
(269, 'RESMETHODS', 'Accounting Research Methods', '2025-09-05 11:04:18', 3, 0, 3, '3rd yr Standing', 'BSAIS', NULL, 3, '2'),
(270, 'GOV', 'Governance, Business Ethics, Risk Management', '2025-09-05 11:04:43', 3, 0, 3, '3rd yr Standing', 'BSAIS', NULL, 3, '2'),
(271, 'MIT', 'Managing Information and Technology', '2025-09-05 11:05:56', 0, 3, 3, 'ISAD', 'BSAIS', NULL, 3, '2'),
(272, 'ISOM', 'Information System Operations and Maintenance', '2025-09-05 11:06:57', 0, 3, 3, 'ISAD', 'BSAIS', NULL, 3, '2'),
(273, 'ISM', 'Information Security and Management', '2025-09-05 11:07:31', 0, 3, 3, 'ISAD', 'BSAIS', NULL, 3, '2'),
(274, 'INTERNSHIP', 'Accounting Information System Internship', '2025-09-05 11:08:38', 0, 0, 6, '4th yr Standing', 'BSAIS', NULL, 4, '1'),
(275, 'AIS RESEARCH', 'Accounting Information System Research', '2025-09-05 11:10:31', 0, 0, 3, 'RESMETHODS', 'BSAIS', NULL, 4, '1'),
(276, 'STRATMAN', 'Strategic Management', '2025-09-05 11:11:11', 3, 0, 3, '3rd yr Standing', 'BSAIS', NULL, 4, '2'),
(277, 'DWM', 'Data Warehousing and Management', '2025-09-05 11:12:10', 0, 3, 3, 'MIT & ISM', 'BSAIS', NULL, 4, '2'),
(278, 'MIS', 'Management Information System ', '2025-09-05 11:12:56', 0, 3, 3, 'ISOM & ISM', 'BSAIS', NULL, 4, '2'),
(279, 'ERP MGT', 'Enterprise Resources  Planning and Management', '2025-09-05 11:14:08', 3, 0, 3, '4th yr Standing', 'BSAIS', NULL, 4, '2'),
(280, 'BUS ANALYTICS', 'Business Analysis', '2025-09-05 11:14:46', 3, 0, 3, 'SBA', 'BSAIS', NULL, 4, '2'),
(281, 'Prof Elect 1 (FINMOD', 'Financial Modeling', '2025-09-05 11:15:24', 3, 0, 3, '4th yr Standing', 'BSAIS', NULL, 4, '2');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','faculty','students') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'superadmin', 'superadmin@example.com', '$2y$10$QIfIAKVTRlMmchMuoiv34eL4GpUZigo1eG8c4ZcyRO.ddemF.yuHu', 'admin', '2025-06-18 07:06:02'),
(2, 'F001', 'faculty1@example.com', '$2y$10$bZ/FlVZqln7v9ouAmQ0Za.JjgAMrQ1jtIEUjRwvjMMaNpW/5ijirK', 'faculty', '2025-06-18 07:06:09'),
(3, 'F002', 'faculty2@example.com', '$2y$10$Wz4tpJzLeBPz6WZQy4A9bOl7IY0s7geP0F3wX0dTrY.lQjHcPAbfC', 'faculty', '2025-06-18 07:06:09'),
(4, 'F003', 'faculty3@example.com', '$2y$10$Wz4tpJzLeBPz6WZQy4A9bOl7IY0s7geP0F3wX0dTrY.lQjHcPAbfC', 'faculty', '2025-06-18 07:06:09'),
(7, 'STU001', 'student1@example.com', '$2y$10$aGoVpl3dsTqVCoxuFBL.XO5Jk6OMccGtFRiCLLwvWSgZfE3XUHzI6', 'students', '2025-06-18 07:06:15'),
(8, 'STU002', 'student2@example.com', '$2y$10$Wz4tpJzLeBPz6WZQy4A9bOl7IY0s7geP0F3wX0dTrY.lQjHcPAbfC', 'students', '2025-06-18 07:06:15'),
(9, 'STU003', 'student3@example.com', '$2y$10$dJBcEZFyUKHn/NaM2FknhepG3rltylCbfl5Zs91LhEW8Z.mCbhHL.', 'students', '2025-06-18 07:06:15'),
(57, 'admin', 'admin@gcc.edu.ph', '$2y$10$OUbGVXxNEKQbZPgn.wWJIOIQj6lb5oBBSUmBzKdLn5ErwhSYLsv2y', 'admin', '2025-09-06 06:29:39'),
(71, '2025312067', 'wendreipayad@gcc.edu.ph', '$2y$10$1aw6esQ4Yc77pm8Q8Erx4uqeuIvGW2jd0M.ubmCeHABnxFYQ389Ky', 'students', '2025-09-07 07:46:32'),
(81, 'F004', 'danielsalazar@gcc.edu.ph', '$2y$10$me9fFF.phgMsP1H1MYwMbOHEXEFelIPD9iisLaCE4rpqo.nymIu0y', 'faculty', '2025-09-08 09:26:15'),
(95, 'F006', 'mike@gcc.edu.ph', '$2y$10$tKIeQyOk2vaQm4U/mfAGTOsL5cG.sp6ue9HFQ8aqGv1KXTMD3dKQ6', 'faculty', '2025-09-08 11:04:21'),
(110, 'F005', 'Niggatron@gcc.edu.ph', '$2y$10$/P6Ken3iOBN01H5XhqZpeOyCBqVi.RuHjA1upU6sZpbaz9f9tPtCe', 'faculty', '2025-09-09 10:38:00'),
(111, '2025311111', 'michaelsalazar@gcc.edu.ph', '$2y$10$0T6aorGk/TxFDV4mqygLCusyqAqNePoQqkCfzi3HtWQimcanX9BrG', 'students', '2025-09-09 11:15:47'),
(113, 'F007', 'dantebayu@gcc.edu.ph', '$2y$10$k3jKUzk1Ap9qFwKi.TJWr.iMGwm/7OTGyo6.P5UB8L77VbPC1egK6', 'faculty', '2025-09-09 11:24:30'),
(114, '2025313333', 'magna@gcc.edu.ph', '$2y$10$GrmLQTpQyySiK.EKKxZbzOWrfKzwWcDGNRwrHvJd9pPSJvtinW0WK', 'students', '2025-09-10 11:54:51'),
(115, 'F008', 'MuayThai@gcc.edu.ph', '$2y$10$OcrhQJXTmFR24z.DDCiM1eXskaVOC8mPnRYzXEzREdWgvErrpXa4a', 'faculty', '2025-09-10 11:56:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_years`
--
ALTER TABLE `academic_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `year` (`year`,`semester`),
  ADD UNIQUE KEY `unique_year_semester` (`year`,`semester`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evaluation_criteria`
--
ALTER TABLE `evaluation_criteria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `evaluation_report`
--
ALTER TABLE `evaluation_report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_report_criteria` (`criteria_id`),
  ADD KEY `fk_eval_student` (`student_id`),
  ADD KEY `fk_eval_faculty` (`faculty_id`),
  ADD KEY `fk_eval_question` (`question_id`),
  ADD KEY `fk_eval_year` (`academic_year_id`);

--
-- Indexes for table `faculties`
--
ALTER TABLE `faculties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faculty_id` (`faculty_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `criteria_id` (`criteria_id`);

--
-- Indexes for table `register`
--
ALTER TABLE `register`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`,`course`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_years`
--
ALTER TABLE `academic_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `evaluation_criteria`
--
ALTER TABLE `evaluation_criteria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `evaluation_report`
--
ALTER TABLE `evaluation_report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=257;

--
-- AUTO_INCREMENT for table `faculties`
--
ALTER TABLE `faculties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `register`
--
ALTER TABLE `register`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=282;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `evaluation_report`
--
ALTER TABLE `evaluation_report`
  ADD CONSTRAINT `evaluation_report_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluation_report_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluation_report_ibfk_3` FOREIGN KEY (`criteria_id`) REFERENCES `evaluation_criteria` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluation_report_ibfk_4` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluation_report_ibfk_5` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_eval_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`),
  ADD CONSTRAINT `fk_eval_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`),
  ADD CONSTRAINT `fk_eval_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `fk_eval_year` FOREIGN KEY (`academic_year_id`) REFERENCES `academic_years` (`id`),
  ADD CONSTRAINT `fk_report_criteria` FOREIGN KEY (`criteria_id`) REFERENCES `evaluation_criteria` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_report_faculty` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_report_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_report_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `faculties`
--
ALTER TABLE `faculties`
  ADD CONSTRAINT `fk_faculties_user` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`criteria_id`) REFERENCES `evaluation_criteria` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `fk_students_user` FOREIGN KEY (`id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
