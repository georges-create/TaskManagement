-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql203.infinityfree.com
-- Generation Time: May 21, 2026 at 06:12 AM
-- Server version: 11.4.10-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41014471_task_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `drop_requests`
--

CREATE TABLE `drop_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drop_requests`
--

INSERT INTO `drop_requests` (`id`, `user_id`, `unit_id`, `reason`, `status`, `created_at`) VALUES
(23, 17, 90, 'Have Many', 'approved', '2026-02-24 07:55:24'),
(24, 16, 89, 'sick', 'rejected', '2026-02-24 07:59:20');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('admin','lecturer','student') NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `role`, `message`, `is_read`, `created_at`) VALUES
(13, 4, 'admin', 'Lecturer submitted a drop request for \'Ethics in IT\'', 0, '2026-02-09 18:46:16'),
(14, 10, 'student', 'Your lecturer submitted a drop request for \'Ethics in IT\'', 0, '2026-02-09 18:46:17'),
(15, 4, 'admin', 'Lecturer submitted a drop request for \'Digital Systems\'', 0, '2026-02-10 09:21:37'),
(16, 10, 'student', 'Your lecturer submitted a drop request for \'Digital Systems\'', 0, '2026-02-10 09:21:37'),
(17, 12, 'lecturer', 'Student Organizational Behavior submitted a drop request for unit Economics I.', 0, '2026-02-10 11:13:57'),
(18, 4, 'admin', 'Student Organizational Behavior requested to drop unit Economics I.', 0, '2026-02-10 11:13:57'),
(19, 4, 'admin', 'Lecturer submitted a drop request for \'Business Communication\'', 0, '2026-02-10 11:27:05'),
(20, 10, 'student', 'Your lecturer submitted a drop request for \'Business Communication\'', 0, '2026-02-10 11:27:05'),
(21, 13, 'student', 'Your lecturer submitted a drop request for \'Business Communication\'', 0, '2026-02-10 11:27:05'),
(22, 4, 'admin', 'Lecturer submitted a drop request for \'IT Fundamentals\'', 0, '2026-02-12 06:25:08'),
(23, 10, 'student', 'Your lecturer submitted a drop request for \'IT Fundamentals\'', 0, '2026-02-12 06:25:08'),
(24, 4, 'admin', 'Lecturer submitted a drop request for \'Educational Psychology\'', 0, '2026-02-13 09:50:13'),
(25, 10, 'student', 'Your lecturer submitted a drop request for \'Educational Psychology\'', 0, '2026-02-13 09:50:13'),
(26, 14, 'student', 'Your lecturer submitted a drop request for \'Educational Psychology\'', 0, '2026-02-13 09:50:13'),
(27, 4, 'admin', 'Lecturer submitted a drop request for \'Teaching Methodologies\'', 0, '2026-02-24 07:55:24'),
(28, 16, 'student', 'Your lecturer submitted a drop request for \'Teaching Methodologies\'', 0, '2026-02-24 07:55:24'),
(29, 17, 'lecturer', 'Student Purity submitted a drop request for unit Curriculum Studies.', 0, '2026-02-24 07:59:20'),
(30, 4, 'admin', 'Student Purity requested to drop unit Curriculum Studies.', 0, '2026-02-24 07:59:20');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`) VALUES
(1, 4, '03db63f557fcdcc7da7343ad03536df6', '2026-02-09 19:46:07');

-- --------------------------------------------------------

--
-- Table structure for table `timetable`
--

CREATE TABLE `timetable` (
  `id` int(11) NOT NULL,
  `unit_id` int(11) NOT NULL,
  `day` enum('Monday','Tuesday','Wednesday','Thursday','Friday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `venue_id` int(11) DEFAULT NULL,
  `venue` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timetable`
--

INSERT INTO `timetable` (`id`, `unit_id`, `day`, `start_time`, `end_time`, `venue_id`, `venue`, `created_at`) VALUES
(18, 90, 'Monday', '08:00:00', '11:00:00', 23, '', '2026-02-24 07:50:25'),
(19, 89, 'Tuesday', '11:00:00', '14:00:00', 17, '', '2026-02-24 07:51:27'),
(20, 86, 'Friday', '08:00:00', '11:00:00', 9, '', '2026-02-24 07:52:00');

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `program` varchar(50) DEFAULT NULL,
  `lecturer_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `year` varchar(10) NOT NULL DEFAULT 'Y1',
  `semester` varchar(10) NOT NULL DEFAULT 'SEM1',
  `code` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `units`
--

INSERT INTO `units` (`id`, `name`, `program`, `lecturer_id`, `created_at`, `year`, `semester`, `code`) VALUES
(6, 'Introduction to Programming', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM1', 'BCSIT101'),
(7, 'Mathematics I', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM1', 'BCSIT102'),
(8, 'Computer Basics', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM1', 'BCSIT103'),
(9, 'Communication Skills', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM1', 'BCSIT104'),
(10, 'Digital Systems', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM1', 'BCSIT105'),
(11, 'Data Structures', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM1', 'BCSIT201'),
(12, 'Database Systems', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM1', 'BCSIT202'),
(13, 'Operating Systems', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM1', 'BCSIT203'),
(14, 'Networking Fundamentals', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM1', 'BCSIT204'),
(15, 'Web Development', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM1', 'BCSIT205'),
(16, 'Software Engineering', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM1', 'BCSIT301'),
(17, 'Mobile App Development', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM1', 'BCSIT302'),
(18, 'Artificial Intelligence', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM1', 'BCSIT303'),
(19, 'Cloud Computing', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM1', 'BCSIT304'),
(20, 'Cybersecurity', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM1', 'BCSIT305'),
(21, 'Project Management', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM1', 'BCSIT401'),
(22, 'Advanced Database Systems', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM1', 'BCSIT402'),
(23, 'Machine Learning', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM1', 'BCSIT403'),
(24, 'Advanced Networking', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM1', 'BCSIT404'),
(25, 'Capstone Project', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM1', 'BCSIT405'),
(26, 'Mathematics II', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM2', 'BCSIT106'),
(27, 'Programming II', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM2', 'BCSIT107'),
(28, 'Introduction to Databases', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM2', 'BCSIT108'),
(29, 'ICT Essentials', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM2', 'BCSIT109'),
(30, 'Ethics in IT', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM2', 'BCSIT110'),
(31, 'Software Development', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM2', 'BCSIT206'),
(32, 'Data Communications', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM2', 'BCSIT207'),
(33, 'System Analysis', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM2', 'BCSIT208'),
(34, 'Programming III', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM2', 'BCSIT209'),
(35, 'Information Security', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM2', 'BCSIT210'),
(36, 'Artificial Neural Networks', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM2', 'BCSIT306'),
(37, 'Big Data Analytics', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM2', 'BCSIT307'),
(38, 'Embedded Systems', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM2', 'BCSIT308'),
(39, 'Advanced Software Engineering', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM2', 'BCSIT309'),
(40, 'DevOps Practices', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM2', 'BCSIT310'),
(41, 'Research Methods', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM2', 'BCSIT406'),
(42, 'IoT Applications', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM2', 'BCSIT407'),
(43, 'Data Mining', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM2', 'BCSIT408'),
(44, 'Project Deployment', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM2', 'BCSIT409'),
(45, 'Capstone Completion', 'BCSIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM2', 'BCSIT410'),
(46, 'Intro to Information Technology', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM1', 'BBIT101'),
(47, 'Computer Applications', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM1', 'BBIT102'),
(48, 'Office Productivity', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM1', 'BBIT103'),
(49, 'Business Communication', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM1', 'BBIT104'),
(50, 'IT Fundamentals', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM1', 'BBIT105'),
(51, 'Networking Essentials', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM1', 'BBIT201'),
(52, 'Database Management', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM1', 'BBIT202'),
(53, 'Systems Analysis', 'BBIT', 17, '2026-02-09 18:39:06', 'Y2', 'SEM1', 'BBIT203'),
(54, 'Web Design', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM1', 'BBIT204'),
(55, 'Programming Basics', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM1', 'BBIT205'),
(56, 'Project Management', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM1', 'BBIT301'),
(57, 'Advanced Database', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM1', 'BBIT302'),
(58, 'E-Commerce', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM1', 'BBIT303'),
(59, 'Information Security', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM1', 'BBIT304'),
(60, 'Software Development', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM1', 'BBIT305'),
(61, 'Research Methods', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM1', 'BBIT401'),
(62, 'IT Governance', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM1', 'BBIT402'),
(63, 'Cloud Computing', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM1', 'BBIT403'),
(64, 'Capstone Project', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM1', 'BBIT404'),
(65, 'Business Analytics', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM1', 'BBIT405'),
(66, 'Intro to Networking', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM2', 'BBIT106'),
(67, 'Database Basics', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM2', 'BBIT107'),
(68, 'IT Support', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM2', 'BBIT108'),
(69, 'Business Ethics', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM2', 'BBIT109'),
(70, 'Digital Literacy', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y1', 'SEM2', 'BBIT110'),
(71, 'Software Engineering', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM2', 'BBIT206'),
(72, 'Programming II', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM2', 'BBIT207'),
(73, 'Mobile Applications', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM2', 'BBIT208'),
(74, 'Data Communications', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM2', 'BBIT209'),
(75, 'Information Systems', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y2', 'SEM2', 'BBIT210'),
(76, 'AI Fundamentals', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM2', 'BBIT306'),
(77, 'Big Data', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM2', 'BBIT307'),
(78, 'IT Project', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM2', 'BBIT308'),
(79, 'Advanced Networking', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM2', 'BBIT309'),
(80, 'IT Security', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y3', 'SEM2', 'BBIT310'),
(81, 'Research Project', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM2', 'BBIT406'),
(82, 'IoT in Business', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM2', 'BBIT407'),
(83, 'Data Mining', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM2', 'BBIT408'),
(84, 'Cloud Solutions', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM2', 'BBIT409'),
(85, 'Capstone Completion', 'BBIT', NULL, '2026-02-09 18:39:06', 'Y4', 'SEM2', 'BBIT410'),
(86, 'Foundations of Education', 'BED', 17, '2026-02-09 18:39:43', 'Y1', 'SEM1', 'BED101'),
(87, 'Educational Psychology', 'BED', NULL, '2026-02-09 18:39:43', 'Y1', 'SEM1', 'BED102'),
(88, 'Philosophy of Education', 'BED', NULL, '2026-02-09 18:39:43', 'Y1', 'SEM1', 'BED103'),
(89, 'Curriculum Studies', 'BED', 17, '2026-02-09 18:39:43', 'Y1', 'SEM1', 'BED104'),
(90, 'Teaching Methodologies', 'BED', 17, '2026-02-09 18:39:43', 'Y1', 'SEM1', 'BED105'),
(91, 'Child Development', 'BED', NULL, '2026-02-09 18:39:43', 'Y2', 'SEM1', 'BED201'),
(92, 'Assessment Strategies', 'BED', NULL, '2026-02-09 18:39:43', 'Y2', 'SEM1', 'BED202'),
(93, 'Classroom Management', 'BED', NULL, '2026-02-09 18:39:43', 'Y2', 'SEM1', 'BED203'),
(94, 'Instructional Materials', 'BED', NULL, '2026-02-09 18:39:43', 'Y2', 'SEM1', 'BED204'),
(95, 'Educational Technology', 'BED', NULL, '2026-02-09 18:39:43', 'Y2', 'SEM1', 'BED205'),
(96, 'Special Needs Education', 'BED', NULL, '2026-02-09 18:39:43', 'Y3', 'SEM1', 'BED301'),
(97, 'Inclusive Education', 'BED', NULL, '2026-02-09 18:39:43', 'Y3', 'SEM1', 'BED302'),
(98, 'Subject Pedagogy I', 'BED', NULL, '2026-02-09 18:39:43', 'Y3', 'SEM1', 'BED303'),
(99, 'Subject Pedagogy II', 'BED', NULL, '2026-02-09 18:39:43', 'Y3', 'SEM1', 'BED304'),
(100, 'Educational Research I', 'BED', NULL, '2026-02-09 18:39:43', 'Y3', 'SEM1', 'BED305'),
(101, 'Educational Leadership', 'BED', NULL, '2026-02-09 18:39:43', 'Y4', 'SEM1', 'BED401'),
(102, 'School Administration', 'BED', NULL, '2026-02-09 18:39:43', 'Y4', 'SEM1', 'BED402'),
(103, 'Curriculum Development', 'BED', NULL, '2026-02-09 18:39:43', 'Y4', 'SEM1', 'BED403'),
(104, 'Assessment and Evaluation', 'BED', NULL, '2026-02-09 18:39:43', 'Y4', 'SEM1', 'BED404'),
(105, 'Capstone Teaching Practice', 'BED', NULL, '2026-02-09 18:39:43', 'Y4', 'SEM1', 'BED405'),
(106, 'Language Acquisition', 'BED', NULL, '2026-02-09 18:39:44', 'Y1', 'SEM2', 'BED106'),
(107, 'Sociology of Education', 'BED', NULL, '2026-02-09 18:39:44', 'Y1', 'SEM2', 'BED107'),
(108, 'Introduction to ICT', 'BED', NULL, '2026-02-09 18:39:44', 'Y1', 'SEM2', 'BED108'),
(109, 'Environmental Education', 'BED', NULL, '2026-02-09 18:39:44', 'Y1', 'SEM2', 'BED109'),
(110, 'Ethics and Values', 'BED', NULL, '2026-02-09 18:39:44', 'Y1', 'SEM2', 'BED110'),
(111, 'Teaching Practice I', 'BED', NULL, '2026-02-09 18:39:44', 'Y2', 'SEM2', 'BED206'),
(112, 'Educational Assessment', 'BED', NULL, '2026-02-09 18:39:44', 'Y2', 'SEM2', 'BED207'),
(113, 'Pedagogical Skills', 'BED', NULL, '2026-02-09 18:39:44', 'Y2', 'SEM2', 'BED208'),
(114, 'Subject Pedagogy III', 'BED', NULL, '2026-02-09 18:39:44', 'Y2', 'SEM2', 'BED209'),
(115, 'Educational Research II', 'BED', NULL, '2026-02-09 18:39:44', 'Y2', 'SEM2', 'BED210'),
(116, 'Subject Pedagogy IV', 'BED', NULL, '2026-02-09 18:39:44', 'Y3', 'SEM2', 'BED306'),
(117, 'ICT in Education', 'BED', NULL, '2026-02-09 18:39:44', 'Y3', 'SEM2', 'BED307'),
(118, 'Guidance and Counseling', 'BED', NULL, '2026-02-09 18:39:44', 'Y3', 'SEM2', 'BED308'),
(119, 'Specialized Teaching', 'BED', NULL, '2026-02-09 18:39:44', 'Y3', 'SEM2', 'BED309'),
(120, 'Education Policy', 'BED', NULL, '2026-02-09 18:39:44', 'Y3', 'SEM2', 'BED310'),
(121, 'Teaching Practice II', 'BED', NULL, '2026-02-09 18:39:44', 'Y4', 'SEM2', 'BED406'),
(122, 'Educational Leadership II', 'BED', NULL, '2026-02-09 18:39:44', 'Y4', 'SEM2', 'BED407'),
(123, 'Curriculum Evaluation', 'BED', NULL, '2026-02-09 18:39:44', 'Y4', 'SEM2', 'BED408'),
(124, 'Research Project', 'BED', NULL, '2026-02-09 18:39:44', 'Y4', 'SEM2', 'BED409'),
(125, 'Capstone Teaching Completion', 'BED', NULL, '2026-02-09 18:39:44', 'Y4', 'SEM2', 'BED410'),
(126, 'Introduction to Business', 'BBM', NULL, '2026-02-09 18:39:44', 'Y1', 'SEM1', 'BBM101'),
(127, 'Principles of Management', 'BBM', NULL, '2026-02-09 18:39:44', 'Y1', 'SEM1', 'BBM102'),
(128, 'Business Communication', 'BBM', NULL, '2026-02-09 18:39:44', 'Y1', 'SEM1', 'BBM103'),
(129, 'Accounting I', 'BBM', NULL, '2026-02-09 18:39:44', 'Y1', 'SEM1', 'BBM104'),
(130, 'Economics I', 'BBM', NULL, '2026-02-09 18:39:44', 'Y1', 'SEM1', 'BBM105'),
(131, 'Marketing Principles', 'BBM', NULL, '2026-02-09 18:39:44', 'Y2', 'SEM1', 'BBM201'),
(132, 'Financial Management I', 'BBM', NULL, '2026-02-09 18:39:44', 'Y2', 'SEM1', 'BBM202'),
(133, 'Human Resource Management', 'BBM', NULL, '2026-02-09 18:39:44', 'Y2', 'SEM1', 'BBM203'),
(134, 'Organizational Behavior', 'BBM', NULL, '2026-02-09 18:39:44', 'Y2', 'SEM1', 'BBM204'),
(135, 'Business Law', 'BBM', NULL, '2026-02-09 18:39:44', 'Y2', 'SEM1', 'BBM205'),
(136, 'Strategic Management', 'BBM', NULL, '2026-02-09 18:39:44', 'Y3', 'SEM1', 'BBM301'),
(137, 'Operations Management', 'BBM', NULL, '2026-02-09 18:39:44', 'Y3', 'SEM1', 'BBM302'),
(138, 'Corporate Finance', 'BBM', NULL, '2026-02-09 18:39:44', 'Y3', 'SEM1', 'BBM303'),
(139, 'Entrepreneurship', 'BBM', NULL, '2026-02-09 18:39:44', 'Y3', 'SEM1', 'BBM304'),
(140, 'Business Ethics', 'BBM', NULL, '2026-02-09 18:39:44', 'Y3', 'SEM1', 'BBM305'),
(141, 'Project Planning', 'BBM', NULL, '2026-02-09 18:39:44', 'Y4', 'SEM1', 'BBM401'),
(142, 'Advanced Marketing', 'BBM', NULL, '2026-02-09 18:39:44', 'Y4', 'SEM1', 'BBM402'),
(143, 'Investment Analysis', 'BBM', NULL, '2026-02-09 18:39:44', 'Y4', 'SEM1', 'BBM403'),
(144, 'International Business', 'BBM', NULL, '2026-02-09 18:39:44', 'Y4', 'SEM1', 'BBM404'),
(145, 'Capstone Business Project', 'BBM', NULL, '2026-02-09 18:39:44', 'Y4', 'SEM1', 'BBM405'),
(146, 'Accounting II', 'BBM', NULL, '2026-02-09 18:39:44', 'Y1', 'SEM2', 'BBM106'),
(147, 'Economics II', 'BBM', NULL, '2026-02-09 18:39:44', 'Y1', 'SEM2', 'BBM107'),
(148, 'Business Mathematics', 'BBM', NULL, '2026-02-09 18:39:44', 'Y1', 'SEM2', 'BBM108'),
(149, 'Information Systems', 'BBM', NULL, '2026-02-09 18:39:44', 'Y1', 'SEM2', 'BBM109'),
(150, 'Entrepreneurship Basics', 'BBM', NULL, '2026-02-09 18:39:44', 'Y1', 'SEM2', 'BBM110'),
(151, 'Financial Management II', 'BBM', NULL, '2026-02-09 18:39:44', 'Y2', 'SEM2', 'BBM206'),
(152, 'Marketing II', 'BBM', NULL, '2026-02-09 18:39:44', 'Y2', 'SEM2', 'BBM207'),
(153, 'Operations II', 'BBM', NULL, '2026-02-09 18:39:44', 'Y2', 'SEM2', 'BBM208'),
(154, 'Strategic HRM', 'BBM', NULL, '2026-02-09 18:39:44', 'Y2', 'SEM2', 'BBM209'),
(155, 'Business Research Methods', 'BBM', NULL, '2026-02-09 18:39:44', 'Y2', 'SEM2', 'BBM210'),
(156, 'Project Management II', 'BBM', NULL, '2026-02-09 18:39:44', 'Y3', 'SEM2', 'BBM306'),
(157, 'Advanced Operations', 'BBM', NULL, '2026-02-09 18:39:44', 'Y3', 'SEM2', 'BBM307'),
(158, 'Corporate Governance', 'BBM', NULL, '2026-02-09 18:39:44', 'Y3', 'SEM2', 'BBM308'),
(159, 'Business Analytics', 'BBM', NULL, '2026-02-09 18:39:44', 'Y3', 'SEM2', 'BBM309'),
(160, 'Innovation Management', 'BBM', NULL, '2026-02-09 18:39:44', 'Y3', 'SEM2', 'BBM310'),
(161, 'Leadership & Management', 'BBM', NULL, '2026-02-09 18:39:44', 'Y4', 'SEM2', 'BBM406'),
(162, 'Global Business', 'BBM', NULL, '2026-02-09 18:39:44', 'Y4', 'SEM2', 'BBM407'),
(163, 'Investment Strategies', 'BBM', NULL, '2026-02-09 18:39:44', 'Y4', 'SEM2', 'BBM408'),
(164, 'Business Simulation', 'BBM', NULL, '2026-02-09 18:39:44', 'Y4', 'SEM2', 'BBM409');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','lecturer','student') NOT NULL,
  `program` varchar(50) DEFAULT NULL,
  `year` varchar(10) DEFAULT NULL,
  `semester` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `program`, `year`, `semester`, `created_at`) VALUES
(4, 'George', 'georgejuma147@gmail.com', '$2y$10$OBHdg0Y09VKfwW3LuKSlOOvD60Uv0w.8ODFr/WQpQD4oGbWoyfkYq', 'admin', '', '', '', '2026-02-08 14:22:40'),
(16, 'Purity', 'purity@gmail.com', '$2y$10$4DF58qO3SCok.K6gm2Rsq.msdRv2briH0I.WCcQZbHoBz84zpNj4q', 'student', 'BED', 'Y1', 'SEM1', '2026-02-24 07:37:45'),
(17, 'laz', 'laz@gmail.com', '$2y$10$scVoOSObFLzo6rlFcb.QhOYfPSDpo2aoMb7LFaS2BhgsaZj1/JUmi', 'lecturer', '', '', '', '2026-02-24 07:43:16');

-- --------------------------------------------------------

--
-- Table structure for table `venues`
--

CREATE TABLE `venues` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venues`
--

INSERT INTO `venues` (`id`, `name`, `created_at`) VALUES
(8, 'Lecture Hall A', '2026-02-09 18:33:34'),
(9, 'Lecture Hall B', '2026-02-09 18:33:34'),
(10, 'Lecture Hall C', '2026-02-09 18:33:34'),
(11, 'Computer Lab 1', '2026-02-09 18:33:34'),
(12, 'Computer Lab 2', '2026-02-09 18:33:34'),
(13, 'Physics Lab', '2026-02-09 18:33:34'),
(14, 'Chemistry Lab', '2026-02-09 18:33:34'),
(15, 'Biology Lab', '2026-02-09 18:33:34'),
(16, 'Library Hall', '2026-02-09 18:33:34'),
(17, 'Auditorium', '2026-02-09 18:33:34'),
(18, 'Seminar Room 1', '2026-02-09 18:33:34'),
(19, 'Seminar Room 2', '2026-02-09 18:33:34'),
(20, 'Seminar Room 3', '2026-02-09 18:33:34'),
(21, 'Conference Room 1', '2026-02-09 18:33:34'),
(22, 'Conference Room 2', '2026-02-09 18:33:34'),
(23, 'Sports Hall', '2026-02-09 18:33:34'),
(24, 'Music Room', '2026-02-09 18:33:34'),
(25, 'Art Studio', '2026-02-09 18:33:34'),
(26, 'Cafeteria', '2026-02-09 18:33:34'),
(27, 'Student Lounge', '2026-02-09 18:33:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `drop_requests`
--
ALTER TABLE `drop_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `timetable`
--
ALTER TABLE `timetable`
  ADD PRIMARY KEY (`id`),
  ADD KEY `unit_id` (`unit_id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecturer_id` (`lecturer_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `drop_requests`
--
ALTER TABLE `drop_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `timetable`
--
ALTER TABLE `timetable`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `venues`
--
ALTER TABLE `venues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `drop_requests`
--
ALTER TABLE `drop_requests`
  ADD CONSTRAINT `drop_requests_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `drop_requests_ibfk_2` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `timetable`
--
ALTER TABLE `timetable`
  ADD CONSTRAINT `timetable_ibfk_1` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `units`
--
ALTER TABLE `units`
  ADD CONSTRAINT `units_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
