-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 27, 2026 at 04:14 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE DATABASE auradent_db;
USE auradent_db;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `auradent_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `booked_by` enum('patient','receptionist') DEFAULT 'receptionist',
  `dentist_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `appointment_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `expected_duration_minutes` int(11) DEFAULT NULL,
  `buffer_minutes` int(11) DEFAULT 10,
  `actual_start_time` datetime DEFAULT NULL,
  `delay_minutes` int(11) DEFAULT 0,
  `status` enum('pending','confirmed','rejected','completed','cancelled','cancellation_requested','no-show') DEFAULT 'pending',
  `queue_number` int(10) UNSIGNED DEFAULT NULL,
  `queue_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `arrival_time` datetime DEFAULT NULL,
  `email_confirmation_sent` tinyint(1) DEFAULT 0,
  `email_reminder_sent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `cancel_requested_at` datetime DEFAULT NULL,
  `cancel_approved_by` int(11) DEFAULT NULL,
  `cancelled_by` enum('PATIENT','STAFF') DEFAULT NULL,
  `cancel_reason` text DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancellation_denied_at` datetime DEFAULT NULL,
  `cancel_attempts` int(11) DEFAULT 0,
  `original_status_before_request` varchar(50) DEFAULT NULL,
  `cancellation_denied_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `booked_by`, `dentist_id`, `service_id`, `appointment_date`, `end_date`, `appointment_time`, `end_time`, `expected_duration_minutes`, `buffer_minutes`, `actual_start_time`, `delay_minutes`, `status`, `queue_number`, `queue_date`, `remarks`, `arrival_time`, `email_confirmation_sent`, `email_reminder_sent`, `created_at`, `updated_at`, `cancel_requested_at`, `cancel_approved_by`, `cancelled_by`, `cancel_reason`, `cancelled_at`, `cancellation_denied_at`, `cancel_attempts`, `original_status_before_request`, `cancellation_denied_reason`) VALUES
(9, 5, 'receptionist', 1, NULL, '2026-03-22', '2026-03-22', '10:22:00', '01:22:00', NULL, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-03-20 01:22:41', '2026-03-20 09:22:41', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(10, 6, 'receptionist', 1, NULL, '2026-03-20', '2026-03-19', '10:34:00', '00:34:00', NULL, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-03-20 01:34:59', '2026-03-20 09:34:59', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(11, 7, 'receptionist', 1, NULL, '2026-03-21', '2026-03-21', '21:16:00', '22:15:00', NULL, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-03-20 13:15:28', '2026-03-20 21:15:28', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(12, 8, 'receptionist', 1, NULL, '2026-03-20', '2026-03-21', '22:16:00', '12:15:00', NULL, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-03-20 13:49:50', '2026-03-20 21:49:50', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(13, 9, 'receptionist', 1, NULL, '2026-03-21', '2026-03-21', '22:17:00', '22:18:00', NULL, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-03-20 14:06:26', '2026-03-20 22:06:26', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(14, 10, 'receptionist', 2, NULL, '2026-03-20', '2026-03-20', '23:20:00', '23:25:00', NULL, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-03-20 14:20:54', '2026-03-20 22:20:54', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(18, 14, 'receptionist', 1, NULL, '2026-03-23', '2026-03-23', '13:19:00', '14:19:00', NULL, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-03-23 04:20:11', '2026-03-23 12:20:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(19, 15, 'receptionist', 1, NULL, '2026-03-24', '2026-03-24', '13:42:00', '15:42:00', NULL, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-03-23 04:44:11', '2026-03-23 12:44:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(24, 20, 'receptionist', 2, NULL, '2026-03-24', '2026-03-24', '13:38:00', '15:38:00', NULL, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-03-23 05:39:45', '2026-03-23 13:39:45', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(25, 21, 'receptionist', 1, NULL, '2026-03-25', '2026-03-25', '17:27:00', '18:27:00', NULL, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-03-23 06:27:24', '2026-03-23 14:27:24', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(26, 6, 'receptionist', 2, NULL, '2026-03-24', '2026-03-24', '20:41:00', '21:41:00', NULL, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-03-23 12:41:16', '2026-03-23 20:41:16', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(27, 22, 'receptionist', 1, NULL, '2026-03-26', '2026-03-26', '11:22:00', '00:22:00', NULL, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-03-24 03:23:06', '2026-03-24 11:23:06', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(28, 23, 'receptionist', 1, NULL, '2026-03-24', '2026-03-24', '02:40:00', '03:40:00', NULL, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-03-24 03:41:07', '2026-03-24 11:41:07', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(31, 26, 'receptionist', 2, NULL, '2026-04-11', '2026-04-11', '15:27:00', '16:27:00', NULL, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-03-25 07:28:20', '2026-04-25 16:00:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(32, 5, 'receptionist', 2, NULL, '2026-04-11', '2026-04-11', '20:00:00', '21:00:00', NULL, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-04-04 00:22:14', '2026-04-25 16:00:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(33, 40, 'receptionist', 2, NULL, '2026-04-11', '2026-04-11', '22:17:00', '23:17:00', NULL, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-04-10 14:18:17', '2026-04-25 16:00:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(52, 38, 'receptionist', 2, NULL, '2026-04-13', '2026-04-13', '12:00:00', '12:48:00', 48, 10, NULL, 0, 'completed', 1, '2026-04-13', NULL, NULL, 0, 0, '2026-04-13 11:41:31', '2026-04-25 16:00:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(53, 38, 'receptionist', 2, NULL, '2026-04-13', '2026-04-13', '13:00:00', '13:40:00', 40, 10, NULL, 0, 'completed', 2, '2026-04-13', NULL, NULL, 0, 0, '2026-04-13 12:09:29', '2026-04-25 16:00:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(54, 40, 'receptionist', 2, NULL, '2026-04-13', '2026-04-13', '12:00:00', '12:48:00', 48, 10, NULL, 0, 'completed', 3, '2026-04-13', NULL, NULL, 0, 0, '2026-04-13 12:12:51', '2026-04-25 16:00:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(55, 7, 'receptionist', 2, NULL, '2026-04-14', '2026-04-14', '10:00:00', '10:30:00', 30, 10, NULL, 0, 'completed', 1, '2026-04-14', NULL, NULL, 0, 0, '2026-04-13 13:05:28', '2026-04-25 16:00:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(56, 40, 'receptionist', 2, NULL, '2026-04-14', '2026-04-14', '13:00:00', '13:48:00', 48, 10, NULL, 0, 'completed', 2, '2026-04-14', NULL, NULL, 0, 0, '2026-04-14 02:43:48', '2026-04-25 16:00:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(57, 40, 'receptionist', 2, NULL, '2026-04-14', '2026-04-14', '15:30:00', '16:40:00', 70, 10, NULL, 0, 'completed', 3, '2026-04-14', NULL, NULL, 0, 0, '2026-04-14 03:03:34', '2026-04-25 16:00:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(58, 43, 'patient', 2, NULL, '2026-04-21', '2026-04-21', '14:00:00', '15:10:00', 70, 10, NULL, 0, 'completed', 4, '2026-04-14', 'ayoko sayo', NULL, 0, 0, '2026-04-14 03:39:02', '2026-04-25 16:00:11', '2026-04-20 18:49:14', NULL, '', 'Approved by receptionist (patient requested)', '2026-04-20 18:50:54', '2026-04-20 18:45:55', 0, NULL, NULL),
(59, 44, 'receptionist', 1, NULL, '2026-04-14', '2026-04-14', '13:00:00', '13:30:00', 30, 10, NULL, 0, 'completed', 1, '2026-04-14', NULL, NULL, 0, 0, '2026-04-14 07:59:01', '2026-04-25 16:00:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(60, 46, 'receptionist', 2, NULL, '2026-04-14', '2026-04-14', '09:00:00', '09:30:00', 30, 10, NULL, 0, 'completed', 5, '2026-04-14', NULL, NULL, 0, 0, '2026-04-14 08:52:28', '2026-04-14 16:52:28', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(61, 43, 'receptionist', 2, NULL, '2026-04-17', '2026-04-17', '09:00:00', '09:40:00', 40, 10, NULL, 0, 'completed', 1, '2026-04-17', NULL, NULL, 0, 0, '2026-04-17 05:27:38', '2026-04-17 13:27:38', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(62, 43, 'receptionist', 2, NULL, '2026-04-17', '2026-04-17', '10:00:00', '10:30:00', 30, 10, NULL, 0, 'completed', 2, '2026-04-17', NULL, NULL, 0, 0, '2026-04-17 06:03:12', '2026-04-17 14:03:12', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(63, 43, 'receptionist', 2, NULL, '2026-04-17', '2026-04-17', '10:30:00', '11:00:00', 30, 10, NULL, 0, 'completed', 3, '2026-04-17', NULL, NULL, 0, 0, '2026-04-17 06:05:04', '2026-04-17 14:05:04', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(64, 43, 'receptionist', 2, NULL, '2026-04-18', '2026-04-18', '11:00:00', '11:30:00', 30, 10, NULL, 0, 'completed', 4, '2026-04-17', NULL, NULL, 0, 0, '2026-04-17 06:26:05', '2026-04-25 16:00:11', NULL, NULL, 'STAFF', 'asdasd', '2026-04-18 08:38:01', NULL, 0, NULL, NULL),
(65, 43, 'receptionist', 2, NULL, '2026-04-18', '2026-04-18', '08:00:00', '08:30:00', 30, 10, NULL, 0, 'completed', 1, '2026-04-18', NULL, NULL, 0, 0, '2026-04-18 00:42:31', '2026-04-18 08:42:31', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(66, 43, 'receptionist', 2, NULL, '2026-04-18', '2026-04-18', '09:00:00', '09:30:00', 30, 10, NULL, 0, 'completed', 2, '2026-04-18', NULL, NULL, 0, 0, '2026-04-18 02:16:53', '2026-04-25 16:00:11', '2026-04-22 11:21:50', NULL, 'STAFF', 'Cancellation request approved by staff', '2026-04-22 11:22:07', NULL, 1, NULL, NULL),
(67, 46, 'receptionist', 2, 7, '2026-04-19', '2026-04-19', '10:00:00', '11:10:00', 70, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-04-18 11:19:55', '2026-04-18 19:19:55', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(68, 46, 'receptionist', 2, 7, '2026-04-19', '2026-04-19', '11:30:00', '12:10:00', 40, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-04-18 11:23:49', '2026-04-18 19:23:49', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(69, 46, 'patient', 2, 7, '2026-04-19', '2026-04-19', '09:00:00', '09:40:00', 40, 10, NULL, 0, 'completed', NULL, NULL, 'ayoko ngani', NULL, 0, 0, '2026-04-18 11:27:23', '2026-04-25 16:00:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(70, 46, 'patient', 2, 7, '2026-04-19', '2026-04-19', '09:00:00', '09:40:00', 40, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-04-18 12:03:46', '2026-04-25 16:00:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(71, 46, 'patient', 2, 2, '2026-04-19', '2026-04-19', '10:00:00', '10:45:00', 45, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-04-18 12:04:05', '2026-04-18 20:04:05', '2026-04-18 21:33:28', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(72, 46, 'patient', 2, 7, '2026-04-19', '2026-04-19', '13:30:00', '14:10:00', 40, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-04-18 13:34:15', '2026-04-18 21:34:15', '2026-04-18 21:34:26', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(75, 46, 'receptionist', 2, NULL, '2026-04-19', '2026-04-19', '09:00:00', '09:40:00', 40, 10, NULL, 0, 'completed', 1, '2026-04-19', NULL, NULL, 0, 0, '2026-04-18 14:19:06', '2026-04-18 22:19:06', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(76, 46, 'patient', 2, NULL, '2026-04-19', '2026-04-19', '09:00:00', '09:40:00', 40, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-04-18 14:22:29', '2026-04-18 22:22:29', '2026-04-19 22:42:33', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(84, 43, 'receptionist', 1, NULL, '2026-04-20', '2026-04-20', '09:00:00', '09:30:00', 30, 10, NULL, 0, 'completed', 1, '2026-04-20', NULL, NULL, 0, 0, '2026-04-19 13:42:56', '2026-04-25 16:00:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(85, 43, 'receptionist', 1, NULL, '2026-04-23', '2026-04-23', '09:30:00', '10:55:00', 85, 10, NULL, 0, 'completed', 2, '2026-04-20', NULL, NULL, 0, 0, '2026-04-19 14:08:31', '2026-04-25 16:00:11', '2026-04-22 11:17:05', NULL, 'STAFF', 'Cancellation request approved by staff', '2026-04-22 11:21:24', '2026-04-20 19:26:24', 2, NULL, NULL),
(86, 43, 'receptionist', 3, NULL, '2026-04-21', '2026-04-21', '09:00:00', '10:25:00', 85, 10, NULL, 0, 'completed', 1, '2026-04-21', NULL, NULL, 0, 0, '2026-04-19 14:19:20', '2026-04-19 22:19:20', '2026-04-20 19:10:21', NULL, NULL, NULL, NULL, '2026-04-20 19:10:34', 2, NULL, NULL),
(87, 46, 'patient', 2, NULL, '2026-04-21', '2026-04-21', '09:00:00', '09:40:00', 40, 10, NULL, 0, 'completed', NULL, NULL, 'testing', NULL, 0, 0, '2026-04-20 10:10:04', '2026-04-20 18:10:04', '2026-04-20 18:10:13', NULL, NULL, NULL, NULL, '2026-04-20 18:14:31', 0, NULL, NULL),
(88, 43, 'patient', 2, NULL, '2026-04-22', '2026-04-22', '09:00:00', '09:40:00', 40, 10, NULL, 0, 'completed', 1, '2026-04-22', 'sadasd', NULL, 0, 0, '2026-04-21 11:52:25', '2026-04-21 19:52:25', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(89, 46, 'patient', 2, NULL, '2026-04-22', '2026-04-22', '10:00:00', '10:40:00', 40, 10, NULL, 0, 'completed', NULL, NULL, 'asdasd', NULL, 0, 0, '2026-04-21 13:22:06', '2026-04-25 16:00:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(90, 43, 'patient', 2, NULL, '2026-04-22', '2026-04-22', '13:00:00', '13:40:00', 40, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-04-22 03:22:28', '2026-04-22 11:22:28', '2026-04-22 11:23:35', NULL, NULL, NULL, NULL, '2026-04-22 11:41:28', 2, NULL, 'asasd'),
(91, 43, 'patient', 2, NULL, '2026-04-22', '2026-04-22', '13:00:00', '13:30:00', 30, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-04-22 03:47:27', '2026-04-22 11:47:27', '2026-04-22 11:47:34', NULL, NULL, NULL, NULL, '2026-04-22 11:47:44', 1, NULL, 'ayoko'),
(92, 43, 'receptionist', 2, NULL, '2026-04-22', '2026-04-22', '13:30:00', '14:10:00', 40, 10, NULL, 0, 'completed', 2, '2026-04-22', NULL, NULL, 0, 0, '2026-04-22 03:48:10', '2026-04-22 11:48:10', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(93, 38, 'receptionist', 2, NULL, '2026-04-22', '2026-04-22', '14:30:00', '15:00:00', 30, 10, NULL, 0, 'completed', 3, '2026-04-22', NULL, NULL, 0, 0, '2026-04-22 03:50:25', '2026-04-22 11:50:25', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(94, 47, 'receptionist', 1, NULL, '2026-04-22', '2026-04-22', '14:00:00', '14:59:00', 59, 10, NULL, 0, 'completed', 1, '2026-04-22', NULL, NULL, 0, 0, '2026-04-22 05:42:10', '2026-04-25 16:00:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(95, 48, 'receptionist', 1, NULL, '2026-04-22', '2026-04-22', '15:00:00', '15:59:00', 59, 10, NULL, 0, 'completed', 2, '2026-04-22', NULL, NULL, 0, 0, '2026-04-22 05:52:40', '2026-04-25 16:00:11', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(96, 43, 'receptionist', 2, NULL, '2026-04-25', '2026-04-25', '15:00:00', '15:30:00', 30, 10, NULL, 0, 'completed', 1, '2026-04-25', NULL, NULL, 0, 0, '2026-04-25 06:30:57', '2026-04-25 14:33:28', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(97, 40, 'receptionist', 1, NULL, '2026-04-25', '2026-04-25', '15:00:00', '15:45:00', 45, 10, NULL, 0, 'completed', 1, '2026-04-25', NULL, NULL, 0, 0, '2026-04-25 06:33:51', '2026-04-25 15:45:52', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(98, 40, 'receptionist', 1, NULL, '2026-04-26', '2026-04-26', '09:00:00', '09:40:00', 40, 10, NULL, 0, 'completed', 1, '2026-04-26', NULL, NULL, 0, 0, '2026-04-25 06:38:51', '2026-04-25 16:00:11', '2026-04-25 15:44:46', NULL, 'STAFF', 'Cancellation request approved by staff', '2026-04-25 15:45:02', NULL, 1, NULL, NULL),
(99, 40, 'receptionist', 2, NULL, '2026-04-26', '2026-04-26', '09:00:00', '09:40:00', 40, 10, NULL, 0, 'completed', 1, '2026-04-26', NULL, NULL, 0, 0, '2026-04-25 07:38:32', '2026-04-25 16:00:11', '2026-04-25 15:42:51', NULL, 'STAFF', 'Cancellation request approved by staff', '2026-04-25 15:43:07', NULL, 1, NULL, NULL),
(100, 40, 'receptionist', 1, NULL, '2026-04-26', '2026-04-26', '09:00:00', '09:30:00', 30, 10, NULL, 0, 'completed', 2, '2026-04-26', NULL, NULL, 0, 0, '2026-04-25 07:46:15', '2026-04-25 16:00:11', '2026-04-25 15:46:40', NULL, 'STAFF', 'Cancellation request approved by staff', '2026-04-25 15:46:59', NULL, 1, NULL, NULL),
(101, 40, 'receptionist', 2, NULL, '2026-04-26', '2026-04-26', '10:00:00', '10:30:00', 30, 10, NULL, 0, 'completed', 2, '2026-04-26', NULL, NULL, 0, 0, '2026-04-25 07:50:26', '2026-04-25 16:00:11', '2026-04-25 15:50:38', NULL, 'STAFF', 'Cancellation request approved by staff', '2026-04-25 15:50:53', NULL, 1, NULL, NULL),
(102, 40, 'receptionist', 2, NULL, '2026-04-26', '2026-04-26', '09:30:00', '10:00:00', 30, 10, NULL, 0, 'completed', 3, '2026-04-26', NULL, NULL, 0, 0, '2026-04-25 07:55:21', '2026-04-25 16:00:11', '2026-04-25 15:57:56', NULL, NULL, NULL, NULL, '2026-04-25 15:58:06', 2, NULL, 'kulit mo'),
(103, 40, 'patient', 1, NULL, '2026-04-26', '2026-04-26', '09:00:00', '09:40:00', 40, 10, NULL, 0, 'completed', NULL, NULL, NULL, NULL, 0, 0, '2026-04-25 08:00:33', '2026-04-25 16:07:19', '2026-04-25 16:04:28', NULL, NULL, NULL, NULL, '2026-04-25 16:04:49', 2, NULL, 'ayoko ngani'),
(104, 40, 'receptionist', 1, NULL, '2026-04-27', '2026-04-27', '09:00:00', '09:30:00', 30, 10, NULL, 0, 'cancelled', 3, '2026-04-26', NULL, NULL, 0, 0, '2026-04-25 08:07:40', '2026-04-27 09:12:36', '2026-04-27 09:11:16', NULL, 'STAFF', 'Cancellation request approved by staff', '2026-04-27 09:12:36', '2026-04-25 16:08:31', 2, NULL, 'denied'),
(105, 40, 'receptionist', 2, NULL, '2026-04-27', '2026-04-27', '09:10:00', '09:14:00', 30, 10, NULL, 0, 'no-show', 1, '2026-04-27', NULL, NULL, 0, 0, '2026-04-27 01:14:12', '2026-04-27 09:16:10', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(106, 40, 'patient', 2, NULL, '2026-04-28', '2026-04-28', '10:00:00', '10:30:00', 30, 10, NULL, 0, 'rejected', NULL, NULL, 'NO NO NO', NULL, 0, 0, '2026-04-27 01:25:30', '2026-04-27 09:25:42', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(107, 40, 'patient', 1, NULL, '2026-04-27', '2026-04-27', '10:00:00', '10:45:00', 45, 10, NULL, 0, 'rejected', NULL, NULL, 'TESTING REJECTION', NULL, 0, 0, '2026-04-27 01:31:46', '2026-04-27 09:32:02', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(108, 40, 'patient', 2, NULL, '2026-04-27', '2026-04-27', '10:00:00', '10:30:00', 30, 10, NULL, 0, 'rejected', NULL, NULL, 'TESTING CANCELLATION', NULL, 0, 0, '2026-04-27 01:37:53', '2026-04-27 09:38:14', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(109, 40, 'receptionist', 2, NULL, '2026-04-27', '2026-04-27', '10:00:00', '11:10:00', 70, 10, NULL, 0, 'confirmed', 2, '2026-04-27', NULL, NULL, 0, 0, '2026-04-27 01:50:36', '2026-04-27 09:50:36', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL),
(110, 40, 'receptionist', 2, NULL, '2026-04-27', '2026-04-27', '15:00:00', '16:10:00', 70, 10, NULL, 0, 'confirmed', 3, '2026-04-27', NULL, NULL, 0, 0, '2026-04-27 01:50:52', '2026-04-27 09:50:52', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `appointment_cancel_requests`
--

CREATE TABLE `appointment_cancel_requests` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','denied') DEFAULT 'pending',
  `denial_reason` text DEFAULT NULL,
  `action_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `action_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment_cancel_requests`
--

INSERT INTO `appointment_cancel_requests` (`id`, `appointment_id`, `patient_id`, `reason`, `status`, `denial_reason`, `action_by`, `created_at`, `action_at`) VALUES
(1, 85, 43, 'asdasdasd', 'denied', 'asdasd', 6, '2026-04-20 11:25:59', '2026-04-20 19:26:24'),
(2, 85, 43, 'asdasdasd', 'approved', NULL, 6, '2026-04-22 03:17:05', '2026-04-22 11:21:24'),
(3, 66, 43, 'hays', 'approved', NULL, 6, '2026-04-22 03:21:50', '2026-04-22 11:22:07'),
(4, 90, 43, 'asdasddsa', 'denied', 'asasd', 6, '2026-04-22 03:22:48', '2026-04-22 11:41:28'),
(5, 90, 43, 'asdad', 'denied', 'asasd', 6, '2026-04-22 03:23:35', '2026-04-22 11:41:28'),
(6, 91, 43, 'testing', 'denied', 'ayoko', 6, '2026-04-22 03:47:34', '2026-04-22 11:47:44'),
(7, 99, 40, 'ayoko na', 'approved', NULL, 6, '2026-04-25 07:42:51', '2026-04-25 15:43:07'),
(8, 98, 40, 'asdasd', 'approved', NULL, 6, '2026-04-25 07:44:46', '2026-04-25 15:45:02'),
(9, 100, 40, 'asdasdasd', 'approved', NULL, 6, '2026-04-25 07:46:40', '2026-04-25 15:46:58'),
(10, 101, 40, 'asdadasd', 'approved', NULL, 6, '2026-04-25 07:50:38', '2026-04-25 15:50:52'),
(11, 102, 40, 'asdasd', 'denied', 'reason from dental', 6, '2026-04-25 07:55:47', '2026-04-25 15:56:01'),
(12, 102, 40, 'asdasdasdas', 'denied', 'kulit mo', 6, '2026-04-25 07:57:56', '2026-04-25 15:58:06'),
(13, 103, 40, 'asdasdasd', 'denied', 'no no no', 6, '2026-04-25 08:01:29', '2026-04-25 16:01:38'),
(14, 103, 40, 'again', 'denied', 'ayoko ngani', 6, '2026-04-25 08:04:28', '2026-04-25 16:04:49'),
(15, 104, 40, 'ayoko na', 'denied', 'denied', 6, '2026-04-25 08:08:17', '2026-04-25 16:08:31'),
(16, 104, 40, 'TEST CANCEL', 'approved', NULL, 6, '2026-04-27 01:11:16', '2026-04-27 09:12:36');

-- --------------------------------------------------------

--
-- Table structure for table `appointment_confirmations`
--

CREATE TABLE `appointment_confirmations` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `confirmation_token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `is_confirmed` tinyint(1) DEFAULT 0,
  `confirmed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment_confirmations`
--

INSERT INTO `appointment_confirmations` (`id`, `appointment_id`, `confirmation_token`, `expires_at`, `is_confirmed`, `confirmed_at`, `created_at`) VALUES
(1, 52, '69e9bd07cb3f8c214bce94f72b215382275bead33bd7f23cfe628ecf46b7f77e', '2026-04-15 19:41:31', 0, NULL, '2026-04-13 19:41:31'),
(2, 54, 'b81264f924a463a7574b4b21d1f8059e5960f651119de73c0d6dcc01d64ca067', '2026-04-15 20:12:51', 1, '2026-04-13 21:03:18', '2026-04-13 20:12:51'),
(3, 55, 'dce107fd4dd9be34337178e8cfddd7e4d8db0c8f612478f599914ef3a93f8b50', '2026-04-15 21:05:28', 0, NULL, '2026-04-13 21:05:28'),
(4, 56, '9d6d12b419a3a1c508d8f48824f304138398ada7a7ca358ec1f15248a93cee0c', '2026-04-16 10:43:48', 0, NULL, '2026-04-14 10:43:48'),
(5, 57, '1f95de5f15d908c15755a70f1a39a0eafcd11b7214acc8aa8d1e7f2547a8ec10', '2026-04-16 11:03:34', 0, NULL, '2026-04-14 11:03:34'),
(6, 58, 'ef1c08fce832ab650f7e718c71714852b155fca535ef05ff036e67122a3da7f2', '2026-04-16 11:39:02', 1, '2026-04-14 11:41:55', '2026-04-14 11:39:02'),
(7, 59, '5959ca7079cda045bd0e163db91759b187be57674c7025a756e8d3c3bb68789a', '2026-04-16 15:59:01', 0, NULL, '2026-04-14 15:59:01'),
(8, 60, '43ff1f797b23314dbca21497819308fb0d3e40d56aecfd52ae76becc768de818', '2026-04-16 16:52:28', 0, NULL, '2026-04-14 16:52:28'),
(9, 61, '5bc7f097ced2171501992fb8c4b91a673da122ae88ac252ac10e97f4867a1469', '2026-04-19 13:27:38', 0, NULL, '2026-04-17 13:27:38');

-- --------------------------------------------------------

--
-- Table structure for table `appointment_services`
--

CREATE TABLE `appointment_services` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_level` enum('Standard','Simple','Moderate','Severe') DEFAULT 'Standard'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment_services`
--

INSERT INTO `appointment_services` (`id`, `appointment_id`, `service_id`, `service_level`) VALUES
(8, 9, 1, 'Standard'),
(9, 10, 1, 'Standard'),
(10, 11, 1, 'Standard'),
(11, 12, 1, 'Standard'),
(12, 13, 1, 'Standard'),
(13, 14, 1, 'Standard'),
(17, 18, 1, 'Standard'),
(18, 19, 1, 'Standard'),
(24, 24, 1, 'Standard'),
(25, 24, 2, 'Moderate'),
(26, 25, 2, 'Standard'),
(27, 26, 1, 'Standard'),
(28, 27, 1, 'Standard'),
(36, 31, 1, 'Standard'),
(37, 32, 3, 'Standard'),
(38, 33, 1, 'Standard'),
(57, 52, 7, 'Severe'),
(58, 53, 7, 'Moderate'),
(59, 54, 7, 'Severe'),
(60, 55, 1, 'Standard'),
(61, 56, 7, 'Severe'),
(62, 57, 7, 'Severe'),
(63, 58, 7, 'Severe'),
(64, 59, 7, 'Simple'),
(65, 60, 7, 'Simple'),
(66, 61, 7, 'Moderate'),
(67, 62, 7, 'Simple'),
(68, 63, 7, 'Simple'),
(69, 64, 7, 'Simple'),
(70, 65, 7, 'Simple'),
(71, 66, 7, 'Simple'),
(72, 72, 7, 'Moderate'),
(73, 72, 7, 'Moderate'),
(74, 76, 7, 'Moderate'),
(75, 84, 7, 'Standard'),
(76, 85, 7, 'Moderate'),
(77, 85, 1, 'Moderate'),
(78, 86, 7, 'Moderate'),
(79, 86, 1, 'Moderate'),
(83, 87, 1, 'Moderate'),
(84, 88, 7, 'Moderate'),
(85, 89, 7, 'Moderate'),
(86, 90, 7, 'Moderate'),
(87, 91, 7, 'Standard'),
(88, 92, 7, 'Moderate'),
(89, 93, 7, 'Simple'),
(90, 94, 1, 'Severe'),
(91, 95, 1, 'Severe'),
(92, 96, 7, 'Simple'),
(93, 97, 1, 'Moderate'),
(94, 98, 7, 'Moderate'),
(95, 99, 7, 'Moderate'),
(96, 100, 7, 'Simple'),
(97, 101, 7, 'Simple'),
(98, 102, 7, 'Simple'),
(99, 103, 7, 'Moderate'),
(100, 104, 7, 'Simple'),
(101, 105, 1, 'Simple'),
(102, 106, 1, 'Standard'),
(103, 107, 1, 'Moderate'),
(104, 108, 1, 'Standard'),
(105, 109, 7, 'Severe'),
(106, 110, 7, 'Severe');

-- --------------------------------------------------------

--
-- Table structure for table `dentists`
--

CREATE TABLE `dentists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT 'default.png',
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `extension_name` enum('Jr','Sr','I','II','III','IV','V','VI','VII') DEFAULT NULL,
  `dentist_type` enum('Regular','On-call') DEFAULT 'Regular',
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `gender` enum('Male','Female','Other') NOT NULL,
  `birthdate` date NOT NULL,
  `house_number` varchar(20) DEFAULT NULL,
  `street` varchar(100) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `region` varchar(100) DEFAULT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dentists`
--

INSERT INTO `dentists` (`id`, `user_id`, `profile_pic`, `first_name`, `middle_name`, `last_name`, `extension_name`, `dentist_type`, `status`, `gender`, `birthdate`, `house_number`, `street`, `barangay`, `city`, `province`, `region`, `contact_number`, `created_at`, `updated_at`) VALUES
(1, 4, '1773833518_975ffcbb9bb3190c119c.jpg', 'Vimmy', 'Clyde', 'Tan', '', 'Regular', 'Active', 'Male', '2006-09-07', '0878', '', 'Makiling', 'City Of Calamba', 'Laguna', 'Region IV-A (CALABARZON)', '09123123123', '2026-03-18 19:31:58', '2026-04-02 20:30:28'),
(2, 5, '1773836443_9d99c4cf0c918feb96a7.jpg', 'Vim', 'Clyde', 'Tan', '', 'Regular', 'Active', 'Male', '2006-09-07', '0878', 'Purok 5', 'Valdez (Biding)', 'Marcos', 'Ilocos Norte', 'Region I (Ilocos Region)', '09123312354', '2026-03-18 20:20:43', '2026-04-02 20:30:12'),
(3, 37, 'default.png', 'Harvie', 'Amurao', 'Tan', 'II', 'On-call', 'Active', 'Male', '2001-12-21', '0878', 'Purok 5', 'Makiling', 'City Of Calamba', 'Laguna', NULL, '09221123232', '2026-04-02 20:53:16', '2026-04-02 21:05:51');

-- --------------------------------------------------------

--
-- Table structure for table `email_queue`
--

CREATE TABLE `email_queue` (
  `id` int(10) UNSIGNED NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `recipient_name` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `template` varchar(50) NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`payload`)),
  `status` enum('pending','sent','failed') DEFAULT 'pending',
  `scheduled_at` datetime DEFAULT NULL,
  `sent_at` datetime DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `retry_count` tinyint(3) UNSIGNED DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_queue`
--

INSERT INTO `email_queue` (`id`, `recipient_email`, `recipient_name`, `subject`, `template`, `payload`, `status`, `scheduled_at`, `sent_at`, `error_message`, `retry_count`, `created_at`, `updated_at`) VALUES
(2, 'test@auradent.local', 'Test Patient', 'Test Email', 'appointment_confirmation', '{\"patient_name\":\"Test\",\"queue_number\":\"99\",\"appointment_date\":\"April 15, 2026\",\"scheduled_time\":\"10:00 AM\",\"dentist_name\":\"Dr. Test\",\"service_name\":\"Consultation\"}', 'sent', NULL, '2026-04-13 12:03:28', 'You did not specify a SMTP hostname.<br>Unable to send email using SMTP. Your server might not be configured to send mail using this method.<br><pre>Date: Mon, 13 Apr 2026 11:56:22 +0800\nTo: test@auradent.local\nFrom: &quot;\\&quot;Auradent Test&quot; &lt;test@auradent.local&gt;\nReturn-Path: &lt;test@auradent.local&gt;\nSubject: =?UTF-8?Q?Test=20Email?=\nReply-To: &lt;test@auradent.local&gt;\nUser-Agent: CodeIgniter\nX-Sender: test@auradent.local\nX-Mailer: CodeIgniter\nX-Priority: 3 (Normal)\nMessage-ID: &lt;69dc6966438513.88904399@auradent.local&gt;\nMime-Version: 1.0\n\n</pre>', 1, '2026-04-13 11:56:15', '2026-04-13 12:03:28'),
(5, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Your Auradent Appointment - Queue #2', 'appointment_confirmation', '{\"patient_name\":\"Brylle Kaizer\",\"patient_fullname\":\"Brylle Kaizer Tan\",\"queue_number\":2,\"appointment_date\":\"April 13, 2026\",\"scheduled_time\":\"12:00 PM\",\"dentist_name\":\"Vim Tan\",\"service_name\":\"Dental Service\",\"clinic_name\":\"Auradent Dental Clinic\",\"clinic_phone\":\"+63 2 8123 4567\",\"clinic_address\":\"Calamba, Laguna\"}', 'sent', NULL, '2026-04-13 13:31:14', 'You did not specify a SMTP hostname.<br>Unable to send email using SMTP. Your server might not be configured to send mail using this method.<br><pre>Date: Mon, 13 Apr 2026 13:16:26 +0800\r\nTo: mcoc6215@gmail.com\r\nFrom: &quot;Auradent Dental Clinic&quot; &lt;noreply@auradent.local&gt;\r\nReturn-Path: &lt;noreply@auradent.local&gt;\r\nSubject: =??Q?=59=6F=75=72=20=41=75=72=61=64=65=6E=74=20=41=70=70=6F=69=6E=74=6D=65?= =??Q?=6E=74=20=2D=20=51=75=65=75=65=20=23=32?=\r\nReply-To: &lt;noreply@auradent.local&gt;\r\nUser-Agent: CodeIgniter\r\nX-Sender: noreply@auradent.local\r\nX-Mailer: CodeIgniter\r\nX-Priority: 3 (Normal)\r\nMessage-ID: &lt;69dc7c2ae78ba5.37900116@auradent.local&gt;\r\nMime-Version: 1.0\r\n\n</pre>', 2, '2026-04-13 13:13:37', '2026-04-13 13:31:14'),
(17, 'chaishan666@gmail.com', 'Bradley Jones Folloso', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Bradley Jones\",\"patient_fullname\":\"Bradley Jones Folloso\",\"queue_number\":1,\"appointment_date\":\"April 13, 2026\",\"scheduled_time\":\"12:00 PM\",\"dentist_name\":\"Vim Tan\",\"service_name\":\"Dental Service\",\"clinic_name\":\"Auradent Dental Clinic\",\"clinic_phone\":\"+63 2 8123 4567\",\"clinic_address\":\"Calamba, Laguna\",\"appointment_id\":52,\"confirmation_token\":\"69e9bd07cb3f8c214bce94f72b215382275bead33bd7f23cfe628ecf46b7f77e\"}', 'sent', NULL, NULL, 'Undefined variable $appointment_time', 3, '2026-04-13 19:41:31', '2026-04-25 14:33:00'),
(18, 'chaishan666@gmail.com', 'Bradley Jones Folloso', 'Reminder: Dental Appointment Tomorrow (Queue #1)', 'reminder_24h', '{\"patient_name\":\"Bradley Jones\",\"patient_fullname\":\"Bradley Jones Folloso\",\"queue_number\":1,\"appointment_date\":\"April 13, 2026\",\"scheduled_time\":\"12:00 PM\",\"dentist_name\":\"Vim Tan\",\"service_name\":\"Dental Service\",\"clinic_name\":\"Auradent Dental Clinic\",\"clinic_phone\":\"+63 2 8123 4567\",\"clinic_address\":\"Calamba, Laguna\"}', 'sent', '2026-04-12 12:00:00', '2026-04-13 19:42:20', NULL, 0, '2026-04-13 19:41:31', '2026-04-13 19:42:20'),
(19, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Brylle Kaizer\",\"queue_number\":3,\"appointment_date\":\"April 13, 2026\",\"appointment_time\":\"12:00 PM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":54,\"confirmation_token\":\"b81264f924a463a7574b4b21d1f8059e5960f651119de73c0d6dcc01d64ca067\"}', 'sent', NULL, '2026-04-13 20:13:12', NULL, 0, '2026-04-13 20:12:51', '2026-04-13 20:13:12'),
(20, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Reminder: Dental Appointment Tomorrow (Queue #3)', 'reminder_24h', '{\"patient_name\":\"Brylle Kaizer\",\"queue_number\":3,\"appointment_date\":\"April 13, 2026\",\"appointment_time\":\"12:00 PM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":54,\"confirmation_token\":\"b81264f924a463a7574b4b21d1f8059e5960f651119de73c0d6dcc01d64ca067\"}', 'sent', '2026-04-12 12:00:00', '2026-04-13 20:13:16', NULL, 0, '2026-04-13 20:12:51', '2026-04-13 20:13:16'),
(21, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Your Auradent Appointment - Queue #3', 'appointment_confirmation', '{\"patient_name\":\"Brylle Kaizer\",\"queue_number\":\"3\",\"appointment_date\":\"April 13, 2026\",\"scheduled_time\":\"12:00 PM\",\"dentist_name\":\"Tan\",\"service_name\":\"Dental Service\"}', 'sent', NULL, '2026-04-13 21:06:06', NULL, 0, '2026-04-13 21:03:18', '2026-04-13 21:06:06'),
(22, 'jasminejanegonzales8@gmail.com', 'Jasmine Jane Gonzales', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Jasmine Jane\",\"queue_number\":1,\"appointment_date\":\"April 14, 2026\",\"appointment_time\":\"10:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":55,\"confirmation_token\":\"dce107fd4dd9be34337178e8cfddd7e4d8db0c8f612478f599914ef3a93f8b50\"}', 'sent', NULL, '2026-04-13 21:06:09', NULL, 0, '2026-04-13 21:05:28', '2026-04-13 21:06:09'),
(24, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Brylle Kaizer\",\"queue_number\":2,\"appointment_date\":\"April 14, 2026\",\"appointment_time\":\"01:00 PM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":56,\"confirmation_token\":\"9d6d12b419a3a1c508d8f48824f304138398ada7a7ca358ec1f15248a93cee0c\"}', 'sent', NULL, '2026-04-14 11:39:18', NULL, 0, '2026-04-14 10:43:48', '2026-04-14 11:39:18'),
(25, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Reminder: Dental Appointment Tomorrow (Queue #2)', 'reminder_24h', '{\"patient_name\":\"Brylle Kaizer\",\"queue_number\":2,\"appointment_date\":\"April 14, 2026\",\"appointment_time\":\"01:00 PM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":56,\"confirmation_token\":\"9d6d12b419a3a1c508d8f48824f304138398ada7a7ca358ec1f15248a93cee0c\"}', 'sent', '2026-04-13 13:00:00', '2026-04-14 11:39:21', NULL, 0, '2026-04-14 10:43:48', '2026-04-14 11:39:21'),
(26, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Brylle Kaizer\",\"queue_number\":3,\"appointment_date\":\"April 14, 2026\",\"appointment_time\":\"03:30 PM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":57,\"confirmation_token\":\"1f95de5f15d908c15755a70f1a39a0eafcd11b7214acc8aa8d1e7f2547a8ec10\"}', 'sent', NULL, '2026-04-14 11:39:25', NULL, 0, '2026-04-14 11:03:34', '2026-04-14 11:39:25'),
(27, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Reminder: Dental Appointment Tomorrow (Queue #3)', 'reminder_24h', '{\"patient_name\":\"Brylle Kaizer\",\"queue_number\":3,\"appointment_date\":\"April 14, 2026\",\"appointment_time\":\"03:30 PM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":57,\"confirmation_token\":\"1f95de5f15d908c15755a70f1a39a0eafcd11b7214acc8aa8d1e7f2547a8ec10\"}', 'sent', '2026-04-13 15:30:00', '2026-04-14 11:39:29', NULL, 0, '2026-04-14 11:03:34', '2026-04-14 11:39:29'),
(28, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":4,\"appointment_date\":\"April 14, 2026\",\"appointment_time\":\"02:00 PM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":58,\"confirmation_token\":\"ef1c08fce832ab650f7e718c71714852b155fca535ef05ff036e67122a3da7f2\"}', 'sent', NULL, '2026-04-14 11:39:32', NULL, 0, '2026-04-14 11:39:02', '2026-04-14 11:39:32'),
(29, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Reminder: Dental Appointment Tomorrow (Queue #4)', 'reminder_24h', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":4,\"appointment_date\":\"April 14, 2026\",\"appointment_time\":\"02:00 PM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":58,\"confirmation_token\":\"ef1c08fce832ab650f7e718c71714852b155fca535ef05ff036e67122a3da7f2\"}', 'sent', '2026-04-13 14:00:00', '2026-04-14 11:39:36', NULL, 0, '2026-04-14 11:39:02', '2026-04-14 11:39:36'),
(30, 'jai@gmail.com', 'Jaijai Vivas', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Jaijai Vivas\",\"queue_number\":1,\"appointment_date\":\"April 14, 2026\",\"appointment_time\":\"01:00 PM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":59,\"confirmation_token\":\"5959ca7079cda045bd0e163db91759b187be57674c7025a756e8d3c3bb68789a\"}', 'sent', NULL, '2026-04-17 14:03:51', NULL, 0, '2026-04-14 15:59:01', '2026-04-17 14:03:51'),
(31, 'jai@gmail.com', 'Jaijai Vivas', 'Reminder: Dental Appointment Tomorrow (Queue #1)', 'reminder_24h', '{\"patient_name\":\"Jaijai Vivas\",\"queue_number\":1,\"appointment_date\":\"April 14, 2026\",\"appointment_time\":\"01:00 PM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":59,\"confirmation_token\":\"5959ca7079cda045bd0e163db91759b187be57674c7025a756e8d3c3bb68789a\"}', 'sent', '2026-04-13 13:00:00', '2026-04-17 14:03:55', NULL, 0, '2026-04-14 15:59:01', '2026-04-17 14:03:55'),
(32, 'asjdajsdjasd@gmail.com', 'Teresita Amurao', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Teresita Amurao\",\"queue_number\":5,\"appointment_date\":\"April 14, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":60,\"confirmation_token\":\"43ff1f797b23314dbca21497819308fb0d3e40d56aecfd52ae76becc768de818\"}', 'sent', NULL, '2026-04-17 14:03:59', NULL, 0, '2026-04-14 16:52:28', '2026-04-25 14:33:00'),
(33, 'asjdajsdjasd@gmail.com', 'Teresita Amurao', 'Reminder: Dental Appointment Tomorrow (Queue #5)', 'reminder_24h', '{\"patient_name\":\"Teresita Amurao\",\"queue_number\":5,\"appointment_date\":\"April 14, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":60,\"confirmation_token\":\"43ff1f797b23314dbca21497819308fb0d3e40d56aecfd52ae76becc768de818\"}', 'sent', '2026-04-13 09:00:00', '2026-04-17 14:04:04', NULL, 0, '2026-04-14 16:52:28', '2026-04-25 14:33:00'),
(34, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":1,\"appointment_date\":\"April 17, 2026\",\"scheduled_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":61,\"confirmation_token\":\"5bc7f097ced2171501992fb8c4b91a673da122ae88ac252ac10e97f4867a1469\"}', 'sent', NULL, '2026-04-17 14:04:08', NULL, 0, '2026-04-17 13:27:38', '2026-04-25 14:33:00'),
(35, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Reminder: Dental Appointment Tomorrow (Queue #1)', 'reminder_24h', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":1,\"appointment_date\":\"April 17, 2026\",\"scheduled_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":61,\"confirmation_token\":\"5bc7f097ced2171501992fb8c4b91a673da122ae88ac252ac10e97f4867a1469\"}', 'sent', '2026-04-16 09:00:00', NULL, NULL, 0, '2026-04-17 13:27:38', '2026-04-25 14:33:00'),
(36, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Your Auradent Appointment - Queue #2', 'appointment_confirmation', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":2,\"appointment_date\":\"April 17, 2026\",\"appointment_time\":\"10:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":62,\"confirmation_token\":\"94740fd2de495a73929463fd07da0cb8724874c7d6f709800e0651f4932156bf\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-17 14:03:12', '2026-04-25 14:33:00'),
(37, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Reminder: Dental Appointment Tomorrow (Queue #2)', 'reminder_24h', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":2,\"appointment_date\":\"April 17, 2026\",\"appointment_time\":\"10:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":62,\"confirmation_token\":\"94740fd2de495a73929463fd07da0cb8724874c7d6f709800e0651f4932156bf\"}', 'sent', '2026-04-16 10:00:00', NULL, NULL, 0, '2026-04-17 14:03:12', '2026-04-25 14:33:00'),
(38, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Your Auradent Appointment - Queue #3', 'appointment_confirmation', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":3,\"appointment_date\":\"April 17, 2026\",\"appointment_time\":\"10:30 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":63,\"confirmation_token\":\"c28e0b1660641d0f59575be32618f76fd7444077e415c8d5269df682b79df77f\"}', 'sent', NULL, '2026-04-17 14:06:53', 'Undefined variable $scheduled_time', 1, '2026-04-17 14:05:04', '2026-04-17 14:06:53'),
(39, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Reminder: Dental Appointment Tomorrow (Queue #3)', 'reminder_24h', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":3,\"appointment_date\":\"April 17, 2026\",\"appointment_time\":\"10:30 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":63,\"confirmation_token\":\"c28e0b1660641d0f59575be32618f76fd7444077e415c8d5269df682b79df77f\"}', 'sent', '2026-04-16 10:30:00', '2026-04-17 14:05:29', NULL, 0, '2026-04-17 14:05:04', '2026-04-17 14:05:29'),
(40, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":4,\"appointment_date\":\"April 17, 2026\",\"appointment_time\":\"11:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":64,\"confirmation_token\":\"4885a846d909091480ec152506b646fb8920eec3916ebebf0e1136b35f9dbe95\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-17 14:26:05', '2026-04-25 14:33:00'),
(41, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":1,\"appointment_date\":\"April 18, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":65,\"confirmation_token\":\"c5d5845f1408c0e861e8aec871cbb1d6e2f41495fcdfcb58936abc9bccdebbf2\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-18 08:42:31', '2026-04-25 14:33:00'),
(42, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":2,\"appointment_date\":\"April 18, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":66,\"confirmation_token\":\"a53f33e144cbf9fa688b26db786fe69f0af14c45c5b95f64a673ac1e345714b8\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-18 10:16:53', '2026-04-25 14:33:00'),
(43, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_cancelled', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"2\",\"appointment_date\":\"April 18, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"66\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-18 10:48:32', '2026-04-25 14:33:00'),
(44, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_cancelled', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"2\",\"appointment_date\":\"April 18, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"66\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-18 10:53:57', '2026-04-25 14:33:00'),
(45, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Your Auradent Appointment - Queue #2', 'appointment_confirmation', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"2\",\"appointment_date\":\"April 18, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"66\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-18 11:00:49', '2026-04-25 14:33:00'),
(46, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Your Auradent Appointment - Queue #2', 'appointment_confirmation', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"2\",\"appointment_date\":\"April 18, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"66\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-18 11:03:09', '2026-04-25 14:33:00'),
(47, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_cancelled', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"2\",\"appointment_date\":\"April 18, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"66\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-18 11:08:14', '2026-04-25 14:33:00'),
(48, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Your Auradent Appointment - Queue #2', 'appointment_confirmation', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"2\",\"appointment_date\":\"April 18, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"66\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-18 11:12:44', '2026-04-25 14:33:00'),
(49, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Your Auradent Appointment - Queue #2', 'appointment_confirmation', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"2\",\"appointment_date\":\"April 18, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"66\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-18 11:22:08', '2026-04-25 14:33:00'),
(50, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Your Auradent Appointment - Queue #2', 'appointment_confirmation', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"2\",\"appointment_date\":\"April 18, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"66\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-18 11:40:19', '2026-04-25 14:33:00'),
(51, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Your Auradent Appointment - Queue #2', 'appointment_confirmation', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"2\",\"appointment_date\":\"April 18, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"66\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-18 11:43:44', '2026-04-25 14:33:00'),
(52, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Your Auradent Appointment - Queue #2', 'appointment_confirmation', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"2\",\"appointment_date\":\"April 18, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"66\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-18 11:46:19', '2026-04-25 14:33:00'),
(53, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Your Auradent Appointment - Queue #2', 'appointment_confirmation', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"2\",\"appointment_date\":\"April 18, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"66\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-18 18:07:19', '2026-04-25 14:33:00'),
(54, 'asjdajsdjasd@gmail.com', 'Teresita Amurao', 'Auradent Dental Clinic Notification', 'appointment_rejected', '{\"patient_name\":\"Teresita Amurao\",\"queue_number\":\"N\\/A\",\"appointment_date\":\"April 19, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"69\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-18 19:28:50', '2026-04-25 14:33:00'),
(55, 'asjdajsdjasd@gmail.com', 'Teresita Amurao', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Teresita Amurao\",\"queue_number\":1,\"appointment_date\":\"April 19, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":75,\"confirmation_token\":\"71b892a85ddc1f884ae2f71077bff558a8600b06937668c45ea6ebc3f32b2415\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-18 22:19:06', '2026-04-25 14:33:00'),
(56, 'asjdajsdjasd@gmail.com', 'Teresita Amurao', 'Your Auradent Appointment - Queue #N/A', 'appointment_confirmation', '{\"patient_name\":\"Teresita Amurao\",\"queue_number\":\"N\\/A\",\"appointment_date\":\"April 19, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"76\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-19 15:35:52', '2026-04-25 14:33:00'),
(57, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":1,\"appointment_date\":\"April 20, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":84,\"confirmation_token\":\"ea7eb8e83f2c0a71c5292433335ffa1b8ebd3acf8914f590d0ad11782d57de63\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-19 21:42:57', '2026-04-25 14:33:00'),
(58, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":2,\"appointment_date\":\"April 20, 2026\",\"appointment_time\":\"09:30 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":85,\"confirmation_token\":\"01a77e6dd91177d0c3c95d79d4e4f49520eee5e3dc2ef3c37434efb46466afba\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-19 22:08:31', '2026-04-25 14:33:00'),
(59, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":1,\"appointment_date\":\"April 21, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":86,\"confirmation_token\":\"b536f26f457d2624ba33fd1a25fc9e92c18d545271f312bf3ecdf4cfff1426af\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-19 22:19:20', '2026-04-25 14:33:00'),
(60, 'asjdajsdjasd@gmail.com', 'Teresita Amurao', 'Auradent Dental Clinic Notification', 'cancellation_denied', '{\"patient_name\":\"Teresita Amurao\",\"appointment_date\":\"April 21, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Vim Tan\",\"denial_reason\":\"Amats ka ya\",\"appointment_id\":\"87\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-20 18:14:31', '2026-04-25 14:33:00'),
(61, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_rejected', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"4\",\"appointment_date\":\"April 21, 2026\",\"appointment_time\":\"02:00 PM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"58\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-20 18:42:50', '2026-04-25 14:33:00'),
(62, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Your Auradent Appointment - Queue #4', 'appointment_confirmation', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"4\",\"appointment_date\":\"April 21, 2026\",\"appointment_time\":\"02:00 PM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"58\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-20 18:43:30', '2026-04-25 14:33:00'),
(63, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'cancellation_denied', '{\"patient_name\":\"Francis Jairo Vivas\",\"appointment_date\":\"April 21, 2026\",\"appointment_time\":\"02:00 PM\",\"dentist_name\":\"Dr. Vim Tan\",\"denial_reason\":\"BAWAL\",\"appointment_id\":\"58\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-20 18:45:55', '2026-04-25 14:33:00'),
(64, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_cancelled', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"4\",\"appointment_date\":\"April 21, 2026\",\"appointment_time\":\"02:00 PM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"58\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-20 18:50:54', '2026-04-25 14:33:00'),
(65, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'cancellation_denied', '{\"patient_name\":\"Francis Jairo Vivas\",\"appointment_date\":\"April 21, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Harvie Tan\",\"denial_reason\":\"sample test\",\"appointment_id\":\"86\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-20 18:57:53', '2026-04-25 14:33:00'),
(66, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'cancellation_denied', '{\"patient_name\":\"Francis Jairo Vivas\",\"appointment_date\":\"April 21, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Harvie Tan\",\"denial_reason\":\"asdasdasd\",\"appointment_id\":\"86\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-20 19:07:52', '2026-04-25 14:33:00'),
(67, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'cancellation_denied', '{\"patient_name\":\"Francis Jairo Vivas\",\"appointment_date\":\"April 21, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Harvie Tan\",\"denial_reason\":\"asdsadasd\",\"appointment_id\":\"86\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-20 19:09:47', '2026-04-25 14:33:00'),
(68, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'cancellation_denied', '{\"patient_name\":\"Francis Jairo Vivas\",\"appointment_date\":\"April 21, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Harvie Tan\",\"denial_reason\":\"kulit mo\",\"appointment_id\":\"86\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-20 19:10:34', '2026-04-25 14:33:00'),
(69, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'cancellation_denied', '{\"patient_name\":\"Francis Jairo Vivas\",\"appointment_date\":\"April 20, 2026\",\"appointment_time\":\"09:30 AM\",\"dentist_name\":\"Dr. Vimmy Tan\",\"denial_reason\":\"asdasd\",\"appointment_id\":\"85\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-20 19:26:24', '2026-04-25 14:33:00'),
(70, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'cancellation_denied', '{\"patient_name\":\"Francis Jairo Vivas\",\"appointment_date\":\"April 20, 2026\",\"appointment_time\":\"09:30 AM\",\"dentist_name\":\"Dr. Vimmy Tan\",\"denial_reason\":\"asdasd\",\"appointment_id\":\"85\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-20 19:26:24', '2026-04-25 14:33:00'),
(71, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_pending', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":1,\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":88,\"confirmation_token\":\"6ce5696fe4cee01aff95ceaa8f8971b9d2e0f695345308c8cfb019ff1ca58c06\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 19:52:25', '2026-04-25 14:33:00'),
(72, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Your Auradent Appointment - Queue #1', 'appointment_confirmation', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 19:56:46', '2026-04-25 14:33:00'),
(73, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Your Auradent Appointment - Queue #1', 'appointment_confirmation', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 19:57:40', '2026-04-25 14:33:00'),
(74, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_rejected', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 19:59:06', '2026-04-25 14:33:00'),
(75, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_rejected', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 19:59:28', '2026-04-25 14:33:00'),
(76, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_rejected', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 20:03:39', '2026-04-25 14:33:00'),
(77, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_rejected', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 20:06:03', '2026-04-25 14:33:00'),
(78, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_rejected', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 20:09:38', '2026-04-25 14:33:00'),
(79, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_rejected', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 20:10:04', '2026-04-25 14:33:00'),
(80, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_rejected', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 20:14:53', '2026-04-25 14:33:00'),
(81, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_rejected', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 20:16:09', '2026-04-25 14:33:00'),
(82, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_rejected', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 20:17:37', '2026-04-25 14:33:00'),
(83, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_rejected', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 20:20:50', '2026-04-25 14:33:00'),
(84, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_rejected', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 20:21:18', '2026-04-25 14:33:00'),
(85, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic Notification', 'appointment_rejected', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 20:23:04', '2026-04-25 14:33:00'),
(86, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Your Auradent Appointment - Queue #1', 'appointment_confirmation', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 20:23:30', '2026-04-25 14:33:00'),
(87, 'asjdajsdjasd@gmail.com', 'Teresita Amurao', 'Auradent Dental Clinic: Appointment Request Update', 'appointment_rejected', '{\"patient_name\":\"Teresita Amurao\",\"queue_number\":\"N\\/A\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"10:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"89\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 21:31:21', '2026-04-25 14:33:00'),
(88, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #1)', 'appointment_confirmed', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 21:44:36', '2026-04-25 14:33:00'),
(89, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #1)', 'appointment_confirmed', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"1\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"88\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-21 21:48:41', '2026-04-25 14:33:00'),
(90, 'jairovivas25@gmail.com', 'Francis Jairo Vivas', 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #N/A)', 'appointment_confirmed', '{\"patient_name\":\"Francis Jairo Vivas\",\"queue_number\":\"N\\/A\",\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"01:00 PM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"90\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-22 11:22:40', '2026-04-25 14:33:00'),
(91, 'bradley@gmail.com', 'Bradley Jones Folloso', 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #3)', 'appointment_confirmed', '{\"patient_name\":\"Bradley Jones Folloso\",\"queue_number\":3,\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"02:30 PM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":93}', 'sent', NULL, NULL, NULL, 0, '2026-04-22 11:50:25', '2026-04-25 14:33:00'),
(92, 'bradfolloso@gmail.com', 'Bradley Folloso', 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #1)', 'appointment_confirmed', '{\"patient_name\":\"Bradley Folloso\",\"queue_number\":1,\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"02:00 PM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":94}', 'sent', NULL, NULL, NULL, 0, '2026-04-22 13:42:10', '2026-04-25 14:33:00'),
(93, 'jaitest@gmail.com', 'Jairo Testing', 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #2)', 'appointment_confirmed', '{\"patient_name\":\"Jairo Testing\",\"queue_number\":2,\"appointment_date\":\"April 22, 2026\",\"appointment_time\":\"03:00 PM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":95}', 'sent', NULL, NULL, NULL, 0, '2026-04-22 13:52:40', '2026-04-25 14:33:00'),
(94, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #1)', 'appointment_confirmed', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":1,\"appointment_date\":\"April 26, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"appointment_id\":98}', 'sent', NULL, '2026-04-25 15:31:12', 'Undefined variable $clinicPhone', 1, '2026-04-25 14:38:51', '2026-04-25 15:31:12'),
(95, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #1)', 'appointment_confirmed', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":1,\"appointment_date\":\"April 26, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"patient_code\":\"PAT-00007\"}', 'sent', NULL, '2026-04-25 15:38:55', NULL, 0, '2026-04-25 15:38:32', '2026-04-25 15:38:55'),
(96, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Cancellation Request Approved', 'appointment_cancellation_approved', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":\"1\",\"appointment_date\":\"April 26, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"99\"}', 'sent', NULL, '2026-04-25 15:43:39', NULL, 0, '2026-04-25 15:43:07', '2026-04-25 15:43:39'),
(97, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Cancellation Request Approved', 'appointment_cancellation_approved', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":\"1\",\"appointment_date\":\"April 26, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"98\"}', 'sent', NULL, '2026-04-25 15:45:09', NULL, 0, '2026-04-25 15:45:02', '2026-04-25 15:45:09'),
(98, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #2)', 'appointment_confirmed', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":2,\"appointment_date\":\"April 26, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"patient_code\":\"PAT-00007\"}', 'sent', NULL, '2026-04-25 15:46:24', NULL, 0, '2026-04-25 15:46:15', '2026-04-25 15:46:24'),
(99, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Cancellation Request Approved', 'appointment_cancellation_approved', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":\"2\",\"appointment_date\":\"April 26, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"100\"}', 'sent', NULL, '2026-04-25 15:47:07', NULL, 0, '2026-04-25 15:46:59', '2026-04-25 15:47:07'),
(100, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #2)', 'appointment_confirmed', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":2,\"appointment_date\":\"April 26, 2026\",\"appointment_time\":\"10:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"patient_code\":\"PAT-00007\"}', 'sent', NULL, '2026-04-25 15:51:03', NULL, 0, '2026-04-25 15:50:27', '2026-04-25 15:51:03'),
(101, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Cancellation Request Approved', 'appointment_cancellation_approved', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":\"2\",\"appointment_date\":\"April 26, 2026\",\"appointment_time\":\"10:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"101\",\"patient_code\":\"PAT-00007\"}', 'sent', NULL, '2026-04-25 15:51:06', NULL, 0, '2026-04-25 15:50:53', '2026-04-25 15:51:06'),
(102, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #3)', 'appointment_confirmed', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":3,\"appointment_date\":\"April 26, 2026\",\"appointment_time\":\"09:30 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"patient_code\":\"PAT-00007\"}', 'sent', NULL, '2026-04-25 15:56:18', NULL, 0, '2026-04-25 15:55:21', '2026-04-25 15:56:18'),
(103, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Cancellation Request Denied', 'cancellation_denied', '{\"patient_name\":\"Brylle Kaizer Tan\",\"appointment_date\":\"April 26, 2026\",\"appointment_time\":\"09:30 AM\",\"dentist_name\":\"Dr. Vim Tan\",\"denial_reason\":\"reason from dental\",\"appointment_id\":\"102\",\"patient_code\":\"N\\/A\"}', 'failed', NULL, NULL, 'Email template not found: emails/cancellation_denied', 3, '2026-04-25 15:56:01', '2026-04-25 16:04:35'),
(104, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Cancellation Request Update', 'appointment_cancellation_denied', '{\"patient_name\":\"Brylle Kaizer Tan\",\"appointment_date\":\"April 26, 2026\",\"appointment_time\":\"09:30 AM\",\"dentist_name\":\"Dr. Vim Tan\",\"denial_reason\":\"kulit mo\",\"appointment_id\":\"102\",\"patient_code\":\"N\\/A\"}', 'sent', NULL, '2026-04-25 15:58:30', NULL, 0, '2026-04-25 15:58:06', '2026-04-25 15:58:30'),
(105, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #N/A)', 'appointment_confirmed', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":\"N\\/A\",\"appointment_date\":\"April 26, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"103\",\"patient_code\":\"PAT-00007\"}', 'sent', NULL, NULL, NULL, 0, '2026-04-25 16:00:57', '2026-04-25 16:01:16'),
(106, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Cancellation Request Update', 'appointment_cancellation_denied', '{\"patient_name\":\"Brylle Kaizer Tan\",\"appointment_date\":\"April 26, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Vimmy Tan\",\"denial_reason\":\"no no no\",\"appointment_id\":\"103\",\"patient_code\":\"N\\/A\"}', 'sent', NULL, '2026-04-25 16:01:50', NULL, 0, '2026-04-25 16:01:38', '2026-04-25 16:01:50'),
(107, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Cancellation Request Update', 'appointment_cancellation_denied', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":\"N\\/A\",\"appointment_date\":\"April 26, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Vimmy Tan\",\"denial_reason\":\"ayoko ngani\",\"appointment_id\":\"103\",\"patient_code\":\"PAT-00007\"}', 'sent', NULL, '2026-04-25 16:04:56', NULL, 0, '2026-04-25 16:04:49', '2026-04-25 16:04:56'),
(108, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #3)', 'appointment_confirmed', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":3,\"appointment_date\":\"April 26, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"patient_code\":\"PAT-00007\"}', 'sent', NULL, '2026-04-25 16:08:07', NULL, 0, '2026-04-25 16:07:40', '2026-04-25 16:08:07'),
(109, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Cancellation Request Update', 'appointment_cancellation_denied', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":\"3\",\"appointment_date\":\"April 26, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Vimmy Tan\",\"denial_reason\":\"denied\",\"appointment_id\":\"104\",\"patient_code\":\"PAT-00007\"}', 'sent', NULL, '2026-04-25 16:08:56', NULL, 0, '2026-04-25 16:08:31', '2026-04-25 16:08:56'),
(110, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Cancellation Request Approved', 'appointment_cancellation_approved', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":\"3\",\"appointment_date\":\"April 27, 2026\",\"appointment_time\":\"09:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"104\",\"patient_code\":\"PAT-00007\"}', 'sent', NULL, '2026-04-27 09:13:11', NULL, 0, '2026-04-27 09:12:36', '2026-04-27 09:13:11'),
(111, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #1)', 'appointment_confirmed', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":1,\"appointment_date\":\"April 27, 2026\",\"appointment_time\":\"09:30 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"patient_code\":\"PAT-00007\"}', 'sent', NULL, '2026-04-27 10:00:35', NULL, 0, '2026-04-27 09:14:12', '2026-04-27 10:00:35'),
(112, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Appointment Status Update', 'appointment_no-show', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":\"1\",\"appointment_date\":\"April 27, 2026\",\"appointment_time\":\"09:10 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"105\",\"patient_code\":\"PAT-00007\"}', 'sent', NULL, '2026-04-27 09:21:55', NULL, 0, '2026-04-27 09:16:10', '2026-04-27 09:21:55'),
(113, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Appointment Request Update', 'appointment_rejected', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":\"N\\/A\",\"appointment_date\":\"April 28, 2026\",\"appointment_time\":\"10:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"106\",\"patient_code\":\"PAT-00007\"}', 'sent', NULL, '2026-04-27 09:31:09', NULL, 0, '2026-04-27 09:25:42', '2026-04-27 09:31:09'),
(114, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Appointment Request Update', 'appointment_rejected', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":\"N\\/A\",\"appointment_date\":\"April 27, 2026\",\"appointment_time\":\"10:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"107\",\"patient_code\":\"PAT-00007\",\"rejection_reason\":\"The requested time slot is no longer available.\"}', 'sent', NULL, '2026-04-27 09:32:23', NULL, 0, '2026-04-27 09:32:02', '2026-04-27 09:32:23'),
(115, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Appointment Request Update', 'appointment_rejected', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":\"N\\/A\",\"appointment_date\":\"April 27, 2026\",\"appointment_time\":\"10:00 AM\",\"dentist_name\":\"Dr. Tan\",\"appointment_id\":\"108\",\"patient_code\":\"PAT-00007\",\"rejection_reason\":\"TESTING CANCELLATION\"}', 'sent', NULL, '2026-04-27 09:38:22', NULL, 0, '2026-04-27 09:38:14', '2026-04-27 09:38:22'),
(116, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #2)', 'appointment_confirmed', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":2,\"appointment_date\":\"April 27, 2026\",\"appointment_time\":\"10:00 AM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"patient_code\":\"PAT-00007\"}', 'sent', NULL, '2026-04-27 09:51:03', NULL, 0, '2026-04-27 09:50:36', '2026-04-27 09:51:03'),
(117, 'mcoc6215@gmail.com', 'Brylle Kaizer Tan', 'Auradent Dental Clinic: Your Appointment is Confirmed (Queue #3)', 'appointment_confirmed', '{\"patient_name\":\"Brylle Kaizer Tan\",\"queue_number\":3,\"appointment_date\":\"April 27, 2026\",\"appointment_time\":\"03:00 PM\",\"dentist_name\":\"Dr. Tan\",\"service_name\":\"Dental Service\",\"patient_code\":\"PAT-00007\"}', 'sent', NULL, '2026-04-27 09:51:06', NULL, 0, '2026-04-27 09:50:52', '2026-04-27 09:51:06');

-- --------------------------------------------------------

--
-- Table structure for table `guardian_information`
--

CREATE TABLE `guardian_information` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `contact_first_name` varchar(100) NOT NULL,
  `contact_last_name` varchar(100) NOT NULL,
  `contact_middle_name` varchar(100) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `mobile_number` varchar(20) NOT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `username`, `ip_address`, `status`, `created_at`) VALUES
(1, 6, 'vimclyde', '::1', 'success', '2026-04-14 12:05:22'),
(2, 6, 'vimclyde', '::1', 'success', '2026-04-14 12:22:18'),
(3, 6, 'vimclyde', '::1', 'success', '2026-04-14 15:21:17'),
(4, 5, 'vimvim', '::1', 'success', '2026-04-14 15:30:17'),
(5, 6, 'vimclyde', '::1', 'success', '2026-04-14 15:57:03'),
(6, 6, 'vimclyde', '::1', 'success', '2026-04-17 13:08:19'),
(7, 6, 'vimclyde', '::1', 'success', '2026-04-18 07:51:26'),
(8, 6, 'vimclyde', '::1', 'success', '2026-04-18 18:00:20'),
(9, 6, 'vimclyde', '::1', 'success', '2026-04-19 14:59:39'),
(10, 6, 'vimclyde', '::1', 'success', '2026-04-19 21:28:11'),
(11, 1, 'admin', '::1', 'success', '2026-04-19 21:48:06'),
(12, 6, 'vimclyde', '::1', 'success', '2026-04-19 21:51:33'),
(13, 6, 'vimclyde', '::1', 'success', '2026-04-20 18:09:00'),
(14, 5, 'vimvim', '::1', 'success', '2026-04-21 19:22:46'),
(15, 6, 'vimclyde', '::1', 'success', '2026-04-21 19:51:47'),
(16, 5, 'vimvim', '::1', 'success', '2026-04-21 20:26:43'),
(17, 6, 'vimclyde', '::1', 'success', '2026-04-21 21:19:15'),
(18, 6, 'vimclyde', '::1', 'success', '2026-04-22 11:16:40'),
(19, 6, 'vimclyde', '::1', 'success', '2026-04-22 11:57:13'),
(20, 5, 'vimvim', '::1', 'success', '2026-04-22 13:30:54'),
(21, 6, 'vimclyde', '::1', 'success', '2026-04-22 13:39:48'),
(22, 6, 'vimclyde', '::1', 'success', '2026-04-25 14:28:10'),
(23, 6, 'vimclyde', '::1', 'wrong_password', '2026-04-25 14:54:47'),
(24, 6, 'vimclyde', '::1', 'success', '2026-04-25 14:54:53'),
(25, 6, 'vimclyde', '::1', 'success', '2026-04-27 09:10:14');

-- --------------------------------------------------------

--
-- Table structure for table `medical_conditions`
--

CREATE TABLE `medical_conditions` (
  `id` int(11) NOT NULL,
  `condition_key` varchar(100) NOT NULL,
  `condition_label` varchar(150) NOT NULL,
  `category` enum('cardiovascular','respiratory','neurological','infectious','metabolic','other','allergy') DEFAULT 'other',
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `is_critical` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `medical_conditions`
--

INSERT INTO `medical_conditions` (`id`, `condition_key`, `condition_label`, `category`, `is_active`, `sort_order`, `is_critical`, `created_at`, `updated_at`) VALUES
(1, 'high_blood_pressure', 'High blood pressure', 'cardiovascular', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(2, 'low_blood_pressure', 'Low blood pressure', 'cardiovascular', 1, 0, 0, '2026-03-18 11:37:37', '2026-04-10 23:11:58'),
(3, 'heart_disease', 'Heart disease', 'cardiovascular', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(4, 'heart_murmur', 'Heart murmur', 'cardiovascular', 1, 0, 0, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(5, 'angina', 'Angina', 'cardiovascular', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(6, 'rheumatic_fever', 'Rheumatic fever', 'cardiovascular', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(7, 'heart_attack', 'Heart attack', 'cardiovascular', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(8, 'heart_surgery', 'Heart surgery', 'cardiovascular', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(9, 'chest_pain', 'Chest pain', 'cardiovascular', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(10, 'asthma', 'Asthma', 'respiratory', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(11, 'emphysema', 'Emphysema', 'respiratory', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(12, 'respiratory_problems', 'Respiratory problems', 'respiratory', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(13, 'tuberculosis', 'Tuberculosis', 'infectious', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(14, 'epilepsy_convulsion', 'Epilepsy / Convulsion', 'neurological', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(15, 'fainting_seizures', 'Fainting seizures', 'neurological', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(16, 'head_injuries', 'Head injuries', 'neurological', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(17, 'hiv_aids', 'AIDS or HIV Infection', 'infectious', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(18, 'std', 'Sexually transmitted disease', 'infectious', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(19, 'hepatitis_liver', 'Hepatitis / Liver disease', 'infectious', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(20, 'diabetes', 'Diabetes', 'metabolic', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(21, 'rapid_weight_loss', 'Rapid weight loss', 'metabolic', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(22, 'anemia', 'Anemia', 'metabolic', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(23, 'cancer_tumor', 'Cancer / Tumor', 'other', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(24, 'stomach_troubles', 'Stomach troubles', 'other', 1, 0, 0, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(25, 'bleeding_problems', 'Bleeding problems', 'other', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(26, 'swollen_ankles', 'Swollen ankles', 'other', 1, 0, 0, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(27, 'arthritis_rheumatism', 'Arthritis / Rheumatism', 'other', 1, 0, 0, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(28, 'radiation_therapy', 'Radiation Therapy', 'other', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(29, 'kidney_disease', 'Kidney disease', 'other', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(30, 'hay_fever_allergies', 'Hay fever / Allergies', 'respiratory', 1, 0, 0, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(69, 'thyroid_problem', 'Thyroid problem', 'other', 1, 0, 1, '2026-03-21 09:11:46', '2026-03-21 09:11:46'),
(70, 'stroke', 'Stroke', 'neurological', 1, 0, 1, '2026-03-21 09:11:46', '2026-03-21 09:11:46'),
(76, 'local_anesthetic', 'Local Anesthetic', 'allergy', 1, 0, 0, '2026-04-10 13:11:34', '2026-04-10 13:11:34'),
(77, 'penicillin_antibiotics', 'Penicillin, Antibiotics', 'allergy', 1, 0, 0, '2026-04-10 13:11:34', '2026-04-10 13:11:34'),
(78, 'latex', 'Latex', 'allergy', 1, 0, 0, '2026-04-10 13:11:34', '2026-04-10 13:11:34'),
(79, 'sulfa_drugs', 'Sulfa Drugs', 'allergy', 1, 0, 0, '2026-04-10 13:11:34', '2026-04-10 13:11:34'),
(80, 'aspirin', 'Aspirin', 'allergy', 1, 0, 0, '2026-04-10 13:11:34', '2026-04-10 13:11:34'),
(101, '_', 'OTHER ALLERGY', 'allergy', 1, 0, 0, '2026-04-10 22:18:17', '2026-04-10 22:18:17'),
(102, 'asdad', 'Asdad', 'allergy', 1, 0, 0, '2026-04-22 13:52:40', '2026-04-22 13:52:40');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `patient_code` varchar(20) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `name_suffix` enum('Jr.','Sr.','II','III','IV','V') DEFAULT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `house_number` varchar(20) DEFAULT NULL,
  `building_name` varchar(100) DEFAULT NULL,
  `street_name` varchar(150) DEFAULT NULL,
  `subdivision` varchar(100) DEFAULT NULL,
  `barangay` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `region` varchar(50) DEFAULT NULL,
  `country` varchar(50) DEFAULT 'Philippines',
  `primary_mobile` varchar(20) NOT NULL,
  `home_number` varchar(20) DEFAULT NULL,
  `office_number` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('Male','Female','Prefer not to say') NOT NULL,
  `guardian_id` int(11) DEFAULT NULL,
  `has_insurance` tinyint(1) DEFAULT 0,
  `insurance_provider` varchar(100) DEFAULT NULL,
  `insurance_valid_until` date DEFAULT NULL,
  `referred_by` varchar(150) DEFAULT NULL,
  `reason_for_consultation` text DEFAULT NULL,
  `previous_dentist` varchar(150) DEFAULT NULL,
  `last_dental_visit` date DEFAULT NULL,
  `signature_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `patient_code`, `user_id`, `first_name`, `middle_name`, `last_name`, `name_suffix`, `nickname`, `house_number`, `building_name`, `street_name`, `subdivision`, `barangay`, `city`, `province`, `postal_code`, `region`, `country`, `primary_mobile`, `home_number`, `office_number`, `email`, `birthdate`, `gender`, `guardian_id`, `has_insurance`, `insurance_provider`, `insurance_valid_until`, `referred_by`, `reason_for_consultation`, `previous_dentist`, `last_dental_visit`, `signature_path`, `created_at`, `updated_at`) VALUES
(5, NULL, 6, 'Vim', 'Clyde', 'Tan', '', NULL, NULL, NULL, NULL, NULL, '043405026', '043405', '0434', NULL, '04', 'Philippines', '09851215274', NULL, NULL, NULL, '2026-03-04', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-20 09:22:41', '2026-03-20 09:22:41'),
(6, NULL, 7, 'Lei Margarette', NULL, 'Tan', '', NULL, NULL, NULL, NULL, NULL, 'Makiling', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09121231234', NULL, NULL, NULL, '2006-09-07', 'Female', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-20 09:34:59', '2026-03-24 12:11:42'),
(7, 'PAT-00008', 8, 'Jasmine Jane', 'Manalang', 'Gonzales', '', NULL, NULL, NULL, NULL, NULL, 'Santa Anastacia', 'Santo Tomas', 'Batangas', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09123456789', NULL, NULL, NULL, '2004-11-02', 'Female', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-20 21:15:28', '2026-04-13 21:04:51'),
(8, NULL, 9, 'Jas', '', 'Gonzales', '', NULL, NULL, NULL, NULL, NULL, 'Makiling', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09123902192', NULL, NULL, 'jasminejanegonzales8@gmail.com', '2004-03-20', 'Female', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-20 21:49:50', '2026-04-13 21:04:37'),
(9, NULL, 10, 'Jasmine', 'Jin', 'Gon', '', NULL, NULL, NULL, NULL, NULL, 'Santa Anastacia', 'Santo Tomas', 'Batangas', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09123456789', NULL, NULL, NULL, '2004-03-20', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-20 22:06:26', '2026-03-20 22:06:26'),
(10, NULL, 11, 'Jake', '', 'Cuenca', '', NULL, NULL, NULL, NULL, NULL, 'Makiling', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09851215274', NULL, NULL, NULL, '2006-07-09', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-20 22:20:54', '2026-03-20 22:20:54'),
(14, NULL, 15, 'Gabrell', 'Amurao', 'Tan', '', NULL, NULL, NULL, NULL, NULL, 'Makiling', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09123456784', NULL, NULL, NULL, '2006-10-06', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-23 12:20:11', '2026-03-23 12:20:11'),
(15, NULL, 16, 'Gabrell', 'Am', 'Tan', '', NULL, NULL, NULL, NULL, NULL, 'Makiling', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09123456781', NULL, NULL, NULL, '2003-03-23', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-23 12:44:11', '2026-03-23 12:44:11'),
(20, NULL, 21, 'TEST', 'AMURAO', 'TEST', '', NULL, NULL, NULL, NULL, NULL, 'Lecheria', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09851215277', NULL, NULL, NULL, '2026-03-24', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-23 13:39:45', '2026-03-23 13:39:45'),
(21, NULL, 22, 'NEW', 'TEST', 'asdasda', '', NULL, NULL, NULL, NULL, NULL, 'Makiling', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09123456783', NULL, NULL, NULL, '2006-03-23', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-23 14:27:24', '2026-03-23 14:27:24'),
(22, NULL, 23, 'Receptionist', 'Test', 'Appointment', 'Jr.', NULL, NULL, NULL, NULL, NULL, 'Makiling', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09123344556', NULL, NULL, NULL, '2003-03-24', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-24 11:23:06', '2026-03-24 11:23:06'),
(23, NULL, 24, 'Female', '', 'Appointment', '', NULL, NULL, NULL, NULL, NULL, 'Makiling', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09231233456', NULL, NULL, NULL, '2006-03-24', 'Female', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-24 11:41:07', '2026-03-24 11:41:07'),
(26, 'PAT-00001', 31, 'Jarrel', 'Amurao', 'Tan', 'Jr.', NULL, NULL, NULL, NULL, NULL, 'Makiling', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09851215274', NULL, NULL, NULL, '2015-03-15', 'Female', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-25 15:28:20', '2026-03-25 15:28:20'),
(32, 'PAT-00002', NULL, 'Walkin', 'Walk', 'Walkin Last', 'Jr.', 'Walkin Nickname', '0878', 'Bldg', 'Purok 5', 'Subd', 'Makiling', 'City Of Calamba', 'Laguna', '4027', 'Region IV-A (CALABARZON)', 'Philippines', '09123456789', NULL, NULL, 'walkin@gmail.com', '2003-12-04', 'Female', NULL, 1, 'Provider', NULL, NULL, 'Sakit utin', NULL, NULL, NULL, '2026-03-25 17:00:42', '2026-03-25 17:00:42'),
(35, 'PAT-00003', NULL, 'Walk', 'In', 'Ngani', 'Jr.', 'Walkin Nickname', '0878', 'Bldg', 'Purok 5', 'Subd', 'Makiling', 'City Of Calamba', 'Laguna', '4027', 'Region IV-A (CALABARZON)', 'Philippines', '09123456789', NULL, NULL, 'asdasdasd@gmail.com', '2010-03-27', 'Male', NULL, 1, 'Provider', NULL, NULL, '', NULL, NULL, NULL, '2026-03-26 20:07:07', '2026-03-26 20:07:07'),
(37, 'PAT-00004', 35, 'Harvie', 'Amurao', 'Tan', 'Jr.', NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, 'Philippines', '09221234532', NULL, NULL, 'harvietan@gmail.com', '2001-12-21', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-01 18:25:09', '2026-04-01 18:25:09'),
(38, 'PAT-00005', 36, 'Bradley Jones', NULL, 'Folloso', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, 'Philippines', '09221112323', NULL, NULL, 'bradley@gmail.com', '2006-04-08', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-01 18:35:49', '2026-04-13 20:12:11'),
(39, 'PAT-00006', 39, 'Victor', 'Hernandez', 'Tan', 'Jr.', NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, 'Philippines', '09231112323', NULL, NULL, 'tanvictor1980@gmail.com', '1980-03-05', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-05 19:13:05', '2026-04-05 19:13:05'),
(40, 'PAT-00007', 40, 'Brylle Kaizer', 'Amurao', 'Tan', '', NULL, NULL, NULL, NULL, NULL, 'Makiling', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09122223123', NULL, NULL, 'mcoc6215@gmail.com', '2008-03-30', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-10 22:18:17', '2026-04-13 20:12:35'),
(43, 'PAT-00009', 44, 'Francis Jairo', 'Araojo', 'Vivas', '', NULL, NULL, NULL, NULL, NULL, 'Turbina', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09953278261', NULL, NULL, NULL, '2006-09-25', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-14 11:39:02', '2026-04-14 15:47:10'),
(44, 'PAT-00010', 45, 'Jaijai', '', 'Vivas', '', NULL, NULL, NULL, NULL, NULL, 'Turbina', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09953278261', NULL, NULL, NULL, '2006-09-25', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-14 15:59:01', '2026-04-14 15:59:01'),
(45, 'PAT-00011', 46, 'Vembo', NULL, 'Tan', NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', NULL, NULL, 'Philippines', '', NULL, NULL, 'vembo@gmail.com', '2006-09-07', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-14 16:42:48', '2026-04-16 19:05:23'),
(46, 'PAT-00012', 47, 'Teresita', NULL, 'Amurao', 'Jr.', 'Test Nickname', '0878', 'Bldg Test', 'Purok 5', 'CPR', 'Makiling', 'City Of Calamba', 'Laguna', '4027', 'Region IV-A (CALABARZON)', 'Philippines', '09212222222', '123123', '123123', NULL, '2004-04-14', 'Female', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-14 16:52:28', '2026-04-16 20:25:02'),
(47, 'PAT-00013', 48, 'Bradley', 'Buling', 'Folloso', '', NULL, NULL, NULL, NULL, NULL, 'Canlubang', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09232221231', NULL, NULL, NULL, '2006-09-14', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-22 13:42:10', '2026-04-22 13:42:10'),
(48, 'PAT-00014', 49, 'Jairo', '', 'Testing', '', NULL, NULL, NULL, NULL, NULL, 'Makiling', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09119292019', NULL, NULL, NULL, '2006-09-25', 'Male', NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-22 13:52:40', '2026-04-22 13:52:40');

-- --------------------------------------------------------

--
-- Table structure for table `patient_medical_records`
--

CREATE TABLE `patient_medical_records` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `chief_complaint` text DEFAULT NULL,
  `diagnosis_notes` text DEFAULT NULL,
  `treatment_plan_notes` text DEFAULT NULL,
  `dental_chart_path` varchar(255) DEFAULT NULL,
  `physician_name` varchar(150) DEFAULT NULL,
  `physician_specialty` varchar(100) DEFAULT NULL,
  `physician_address` text DEFAULT NULL,
  `physician_phone` varchar(20) DEFAULT NULL,
  `is_good_health` tinyint(1) DEFAULT NULL,
  `is_under_medical_treatment` tinyint(1) DEFAULT NULL,
  `has_serious_illness` tinyint(1) DEFAULT NULL,
  `serious_illness_details` text DEFAULT NULL,
  `is_hospitalized` tinyint(1) DEFAULT NULL,
  `hospitalization_details` text DEFAULT NULL,
  `is_taking_medication` tinyint(1) DEFAULT NULL,
  `medication_details` text DEFAULT NULL,
  `uses_tobacco` tinyint(1) DEFAULT NULL,
  `uses_drugs` tinyint(1) DEFAULT NULL,
  `medical_conditions` longtext DEFAULT NULL,
  `other_allergy` text DEFAULT NULL,
  `bleeding_time` varchar(50) DEFAULT NULL,
  `is_pregnant` tinyint(1) DEFAULT 0,
  `is_nursing` tinyint(1) DEFAULT 0,
  `is_taking_birth_control` tinyint(1) DEFAULT 0,
  `blood_type` varchar(10) DEFAULT NULL,
  `blood_pressure` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_medical_records`
--

INSERT INTO `patient_medical_records` (`id`, `patient_id`, `chief_complaint`, `diagnosis_notes`, `treatment_plan_notes`, `dental_chart_path`, `physician_name`, `physician_specialty`, `physician_address`, `physician_phone`, `is_good_health`, `is_under_medical_treatment`, `has_serious_illness`, `serious_illness_details`, `is_hospitalized`, `hospitalization_details`, `is_taking_medication`, `medication_details`, `uses_tobacco`, `uses_drugs`, `medical_conditions`, `other_allergy`, `bleeding_time`, `is_pregnant`, `is_nursing`, `is_taking_birth_control`, `blood_type`, `blood_pressure`, `created_at`, `updated_at`) VALUES
(1, 15, NULL, NULL, NULL, NULL, 'Vim', 'asdasd', 'asdas', 'dasdasd', 0, 0, NULL, 'TEST ILLNESS', 1, 'TEST HOSPITALIZED', 1, NULL, 0, 0, '[\"Local Anesthetic\",\"TEST ALLERGY\",\"high_blood_pressure\",\"low_blood_pressure\",\"heart_disease\",\"heart_murmur\",\"angina\",\"rheumatic_fever\",\"heart_attack\",\"heart_surgery\",\"chest_pain\",\"asthma\",\"emphysema\",\"respiratory_problems\",\"hay_fever_allergies\",\"epilepsy_convulsion\",\"fainting_seizures\",\"head_injuries\",\"stroke\",\"tuberculosis\",\"hiv_aids\",\"std\",\"hepatitis_liver\",\"diabetes\",\"rapid_weight_loss\",\"anemia\",\"cancer_tumor\",\"stomach_troubles\",\"bleeding_problems\",\"swollen_ankles\",\"arthritis_rheumatism\",\"radiation_therapy\",\"kidney_disease\",\"thyroid_problem\"]', NULL, '', 0, 0, 0, 'O', '120/80', '2026-03-23 12:44:11', '2026-03-23 12:44:11'),
(2, 16, NULL, NULL, NULL, NULL, '', '', '', '', 0, 0, NULL, '', 0, '', 0, NULL, 0, 0, '[\"local_anesthetic\",\"penicillin_antibiotics\",\"latex\",\"sulfa_drugs\",\"aspirin\",\"high_blood_pressure\",\"low_blood_pressure\",\"heart_disease\",\"heart_murmur\",\"angina\",\"rheumatic_fever\",\"heart_attack\",\"heart_surgery\",\"chest_pain\",\"asthma\",\"emphysema\",\"respiratory_problems\",\"hay_fever_allergies\",\"epilepsy_convulsion\",\"fainting_seizures\",\"head_injuries\",\"stroke\",\"tuberculosis\",\"hiv_aids\",\"std\",\"hepatitis_liver\",\"diabetes\",\"rapid_weight_loss\",\"anemia\",\"cancer_tumor\",\"stomach_troubles\",\"bleeding_problems\",\"swollen_ankles\",\"arthritis_rheumatism\",\"radiation_therapy\",\"kidney_disease\",\"thyroid_problem\"]', NULL, '102', 0, 0, 0, 'A+', '120/80', '2026-03-23 13:08:40', '2026-03-23 13:08:40'),
(4, 18, NULL, NULL, NULL, NULL, 'TEST PHYSICIAN', 'TEST SPECIALTY', 'Test Address', 'TEL TEST', 1, 1, 1, 'TEST ILLNESS', 1, 'TEST HOSPITALIZED', 1, NULL, 1, 1, '[\"local_anesthetic\",\"penicillin_antibiotics\",\"latex\",\"sulfa_drugs\",\"aspirin\",\"emphysema\",\"epilepsy_convulsion\",\"rapid_weight_loss\"]', NULL, '', 0, 0, 1, 'O', '120/90', '2026-03-23 13:22:49', '2026-03-23 13:22:49'),
(5, 19, NULL, NULL, NULL, NULL, 'TEST PHYSICIAN', 'TEST SPECIALTY', 'Test Address', 'TEL TEST', 1, 1, 1, 'TEST ILLNESS', 1, 'TEST HOSPITALIZED', 1, 'TEST MEDICATION', 1, 1, '[\"local_anesthetic\",\"penicillin_antibiotics\",\"latex\",\"sulfa_drugs\",\"aspirin\",\"low_blood_pressure\"]', NULL, '', 0, 0, 0, 'O-', '120/80', '2026-03-23 13:33:32', '2026-03-23 13:33:32'),
(6, 20, NULL, NULL, NULL, NULL, 'TEST PHYSICIAN', 'TEST SPECIALTY', 'Test Address', 'TEL TEST', 1, 1, 1, 'TEST ILLNESS', 1, 'TEST HOSPITALIZED', 1, 'TEST MEDICATION', 1, 1, '[\"local_anesthetic\",\"sulfa_drugs\",\"high_blood_pressure\"]', 'OTHER ALLERGY', '', 0, 0, 0, 'Unknown', '120/90', '2026-03-23 13:39:45', '2026-03-23 13:39:45'),
(7, 21, NULL, NULL, NULL, NULL, '', '', '', '', 0, 0, 0, '', 0, '', 0, '', 0, 0, '[]', '', '', 0, 0, 0, NULL, NULL, '2026-03-23 14:27:24', '2026-03-23 14:27:24'),
(8, 6, NULL, NULL, NULL, NULL, '', '', '', '', 0, 0, 0, NULL, 0, NULL, 0, NULL, 0, 0, '[]', '', '', 0, 0, 0, NULL, NULL, '2026-03-23 20:11:34', '2026-03-23 20:11:34'),
(9, 6, NULL, NULL, NULL, NULL, '', '', '', '', 0, 0, 0, '', 0, '', 0, '', 0, 0, '[]', '', '', 0, 0, 0, NULL, NULL, '2026-03-23 20:41:16', '2026-03-23 20:41:16'),
(10, 22, NULL, NULL, NULL, NULL, '', '', '', '', 1, 1, 1, 'TEST ILLNESS', 1, 'TEST HOSPITALIZED', 1, 'TEST MEDICATION', 1, 1, '[\"local_anesthetic\",\"penicillin_antibiotics\",\"latex\",\"sulfa_drugs\",\"aspirin\",\"low_blood_pressure\"]', 'OTHER ALLERGY', '', 0, 0, 0, 'A-', '120/90', '2026-03-24 11:23:06', '2026-03-24 11:23:06'),
(11, 23, NULL, NULL, NULL, NULL, '', '', '', '', 1, 1, 1, '', 1, '', 1, '', 1, 1, '[\"local_anesthetic\",\"penicillin_antibiotics\",\"latex\",\"sulfa_drugs\",\"aspirin\",\"low_blood_pressure\"]', 'OTHER ALLERGY', '102', 0, 1, 1, 'Unknown', '120/120', '2026-03-24 11:41:07', '2026-03-24 11:41:07'),
(16, 24, NULL, NULL, NULL, NULL, '', NULL, '', '', 0, 0, 0, '', 0, '', 0, '', 0, 0, '[]', NULL, NULL, 0, 0, 0, 'Unknown', NULL, '2026-03-25 13:32:08', '2026-03-25 13:32:08'),
(17, 25, NULL, NULL, NULL, NULL, 'TEST PHYSICIAN', 'TEST SPECIALTY', 'Test Address', 'TEL TEST', 1, 1, 1, 'TEST ILLNESS', 1, 'TEST HOSPITALIZED', 1, 'TEST MEDICATION', 1, 1, '[\"local_anesthetic\",\"penicillin_antibiotics\",\"latex\",\"sulfa_drugs\",\"aspirin\",\"low_blood_pressure\",\"heart_attack\"]', 'OTHER ALLERGY', '', 0, 0, 0, 'Unknown', '120/80', '2026-03-25 15:21:21', '2026-03-25 15:21:21'),
(18, 26, NULL, NULL, NULL, 'uploads/charts/chart_26_1775880489_67eecc8b31cb7335.jpeg', 'TEST PHYSICIAN', 'TEST SPECIALTY', 'Test Address', 'TEL TEST', 1, 1, 1, 'TEST ILLNESS', 1, 'TEST HOSPITALIZED', 1, 'TEST MEDICATION', 1, 1, '[\"local_anesthetic\",\"penicillin_antibiotics\",\"latex\",\"sulfa_drugs\",\"aspirin\",\"low_blood_pressure\"]', 'OTHER ALLERGY', '', 0, 1, 1, 'O-', '120/80', '2026-03-25 15:28:20', '2026-04-11 12:08:09'),
(20, 28, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[]', NULL, NULL, 0, 0, 0, 'Unknown', NULL, '2026-03-25 16:33:56', '2026-03-25 16:33:56'),
(22, 30, NULL, NULL, NULL, NULL, 'TEST PHYSICIAN', NULL, NULL, NULL, 1, 1, 1, 'TEST ILLNESS', 1, NULL, 1, NULL, 1, 1, '[\"low_blood_pressure\"]', NULL, '1 mins', 1, 1, 1, 'A+', '120/80', '2026-03-25 16:49:27', '2026-03-25 16:49:27'),
(23, 31, NULL, NULL, NULL, NULL, 'TEST PHYSICIAN', NULL, NULL, NULL, 1, 1, 1, 'TEST ILLNESS', 1, NULL, 1, NULL, 1, 1, '[\"low_blood_pressure\"]', NULL, '1 mins', 1, 1, 1, 'A+', '120/80', '2026-03-25 16:51:10', '2026-03-25 16:51:10'),
(24, 32, NULL, NULL, NULL, NULL, 'TEST PHYSICIAN', NULL, NULL, NULL, 1, 1, 1, 'TEST ILLNESS', 1, NULL, 1, NULL, 1, 1, '[\"low_blood_pressure\"]', NULL, '1 mins', 1, 1, 1, 'A+', '120/80', '2026-03-25 17:00:42', '2026-03-25 17:00:42'),
(27, 35, NULL, NULL, NULL, NULL, '', '', '', '', 0, 0, 0, '', 0, '', 0, '', 0, 0, '[]', NULL, '2 mins', 0, 0, 0, NULL, '', '2026-03-26 20:07:07', '2026-04-13 18:54:10'),
(29, 5, NULL, NULL, NULL, NULL, '', '', '', '', 0, 0, 0, '', 0, '', 0, '', 0, 0, '[]', '', '', 0, 0, 0, NULL, NULL, '2026-04-04 08:22:14', '2026-04-04 08:22:14'),
(30, 40, 'test', 'nga', 'ni', 'uploads/charts/chart_40_1775874292_246f8ae2.png', NULL, NULL, NULL, NULL, 0, 0, 0, NULL, 0, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-10 22:18:17', '2026-04-25 14:33:51'),
(31, 5, NULL, NULL, NULL, NULL, '', '', '', '', 0, 0, 0, '', 0, '', 0, '', 0, 0, '[]', '', '0m 0s', 0, 0, 0, NULL, NULL, '2026-04-13 08:30:23', '2026-04-13 08:30:23'),
(32, 7, NULL, NULL, NULL, NULL, '', '', '', '', 0, 0, 0, '', 0, '', 0, '', 0, 0, '[]', NULL, NULL, 0, 0, 0, NULL, '', '2026-04-13 09:47:07', '2026-04-13 21:05:28'),
(34, 39, NULL, NULL, NULL, NULL, '', '', '', '', 0, 0, 0, '', 0, '', 0, '', 0, 0, '[]', NULL, NULL, 0, 0, 0, NULL, '', '2026-04-13 18:53:15', '2026-04-13 18:53:15'),
(35, 38, NULL, NULL, NULL, NULL, '', '', '', '', 0, 0, 0, '', 0, '', 0, '', 0, 0, '[]', NULL, NULL, 0, 0, 0, NULL, '', '2026-04-13 19:13:01', '2026-04-13 19:13:01'),
(38, 46, NULL, NULL, NULL, NULL, 'Test', 'Test', 'Test', '123123123', 1, 1, 1, 'TEST ILLNESS', 1, 'TEST HOSP', 1, 'TEST Medi', 1, 1, '[\"aspirin\",\"latex\",\"local_anesthetic\",\"penicillin_antibiotics\",\"sulfa_drugs\",\"heart_surgery\",\"high_blood_pressure\",\"std\",\"_\"]', 'OTHERS', '1m 0s', 1, 1, 1, 'A+', '115/80', '2026-04-16 21:27:08', '2026-04-16 21:27:08'),
(39, 48, NULL, NULL, NULL, NULL, 'Test Name', 'Test Specialty', 'Test Address', '091231231123', 1, 1, 1, 'Test Illness', 1, 'Test Hospitalized', 1, 'Test Medication', 1, 1, '[\"aspirin\",\"sulfa_drugs\",\"high_blood_pressure\",\"asdad\"]', 'asdad', '2m 0s', NULL, NULL, NULL, 'A+', '119/80', '2026-04-22 13:52:40', '2026-04-22 13:52:40'),
(40, 43, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, NULL, 0, NULL, 0, NULL, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-04-25 14:30:57', '2026-04-25 14:30:57');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `has_levels` tinyint(1) DEFAULT 0,
  `price_simple` decimal(10,2) DEFAULT NULL,
  `price_moderate` decimal(10,2) DEFAULT NULL,
  `price_severe` decimal(10,2) DEFAULT NULL,
  `estimated_duration_minutes` int(11) DEFAULT 30,
  `duration_adjustments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`duration_adjustments`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `description`, `price`, `status`, `has_levels`, `price_simple`, `price_moderate`, `price_severe`, `estimated_duration_minutes`, `duration_adjustments`) VALUES
(1, 'Tooth Extraction', '', 0.00, 'active', 1, 500.00, 1000.00, 1500.00, 30, '{\"Simple\":0,\"Moderate\":15,\"Severe\":29}'),
(2, 'JACK', '', 0.00, 'active', 1, 2500.00, 4500.00, 10000.00, 45, '{\"Simple\": -15, \"Moderate\": 0, \"Severe\": 20}'),
(3, 'Consultation', '', 0.00, 'active', 0, NULL, NULL, NULL, 20, NULL),
(7, 'Oral Prophylaxis', '', 0.00, 'active', 1, 3500.00, 5000.00, 7500.00, 30, '{\"Simple\":0,\"Moderate\":10,\"Severe\":40}');

-- --------------------------------------------------------

--
-- Table structure for table `treatment_records`
--

CREATE TABLE `treatment_records` (
  `id` int(11) NOT NULL,
  `visit_id` int(11) DEFAULT NULL,
  `transaction_type` enum('charge','payment','penalty','adjustment') NOT NULL,
  `patient_id` int(11) NOT NULL,
  `dentist_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `tooth_number` varchar(10) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `reference_no` varchar(100) DEFAULT NULL,
  `consent_given` tinyint(1) DEFAULT 0,
  `signature_path` varchar(255) DEFAULT NULL,
  `treatment_date` datetime DEFAULT current_timestamp(),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `treatment_records`
--

INSERT INTO `treatment_records` (`id`, `visit_id`, `transaction_type`, `patient_id`, `dentist_id`, `service_id`, `tooth_number`, `amount`, `description`, `reference_no`, `consent_given`, `signature_path`, `treatment_date`, `created_at`) VALUES
(1, 7, 'charge', 32, 1, 3, '', 0.00, NULL, NULL, 0, NULL, '2026-03-26 19:32:32', '2026-04-27 10:08:48'),
(2, 8, 'charge', 22, 1, 1, '', 6000.00, NULL, NULL, 0, NULL, '2026-03-26 19:40:21', '2026-04-27 10:08:48'),
(3, 8, 'charge', 22, 1, 1, '13', 5500.00, NULL, NULL, 0, NULL, '2026-03-26 19:41:41', '2026-04-27 10:08:48'),
(4, 8, 'charge', 22, 1, 1, '13', 500.00, NULL, NULL, 0, NULL, '2026-03-26 19:54:46', '2026-04-27 10:08:48'),
(5, 11, 'charge', 35, 1, 1, '', 6000.00, NULL, NULL, 0, NULL, '2026-04-05 19:26:31', '2026-04-27 10:08:48'),
(10, 12, 'charge', 6, 1, 1, '14', 6000.00, NULL, NULL, 0, NULL, '2026-04-05 19:37:24', '2026-04-27 10:08:48'),
(11, 12, 'charge', 6, 1, 1, '14', 4800.00, NULL, NULL, 0, NULL, '2026-04-05 19:38:03', '2026-04-27 10:08:48'),
(12, 13, 'charge', 7, 1, 1, '12', 6000.00, NULL, NULL, 0, NULL, '2026-04-05 19:40:23', '2026-04-27 10:08:48'),
(13, 14, 'charge', 5, 1, 1, NULL, 6000.00, NULL, NULL, 0, NULL, '2026-04-05 19:40:50', '2026-04-27 10:08:48'),
(14, 14, 'charge', 5, 1, 1, NULL, 4000.00, NULL, NULL, 0, NULL, '2026-04-05 19:42:04', '2026-04-27 10:08:48'),
(15, 14, 'charge', 5, 1, 1, NULL, 3000.00, NULL, NULL, 0, NULL, '2026-04-05 19:42:37', '2026-04-27 10:08:48'),
(16, 14, 'charge', 5, 1, 1, NULL, 2200.00, NULL, NULL, 0, NULL, '2026-04-05 19:47:40', '2026-04-27 10:08:48'),
(17, 15, 'charge', 8, 1, 1, NULL, 6000.00, NULL, NULL, 0, NULL, '2026-04-05 19:48:09', '2026-04-27 10:08:48'),
(18, 14, 'charge', 5, 1, 1, NULL, 1200.00, NULL, NULL, 0, NULL, '2026-04-05 19:52:19', '2026-04-27 10:08:48'),
(19, 14, 'charge', 5, 1, 1, NULL, 1000.00, NULL, NULL, 0, NULL, '2026-04-05 19:53:58', '2026-04-27 10:08:48'),
(20, 14, 'charge', 5, 1, 1, NULL, 900.00, NULL, NULL, 0, NULL, '2026-04-05 19:55:56', '2026-04-27 10:08:48'),
(21, 14, 'charge', 5, 1, 1, NULL, 800.00, NULL, NULL, 0, NULL, '2026-04-05 19:58:31', '2026-04-27 10:08:48'),
(22, 14, 'charge', 5, 1, 1, NULL, 700.00, NULL, NULL, 0, NULL, '2026-04-05 20:00:12', '2026-04-27 10:08:48'),
(23, 14, 'charge', 5, 1, 1, NULL, 600.00, NULL, NULL, 0, NULL, '2026-04-05 20:00:33', '2026-04-27 10:08:48'),
(24, 14, 'charge', 5, 1, 1, NULL, 500.00, NULL, NULL, 0, NULL, '2026-04-05 20:01:25', '2026-04-27 10:08:48'),
(25, 14, 'charge', 5, 1, 1, NULL, 400.00, NULL, NULL, 0, NULL, '2026-04-05 20:03:16', '2026-04-27 10:08:48'),
(26, 14, 'charge', 5, 1, 1, NULL, 300.00, NULL, NULL, 0, NULL, '2026-04-05 20:04:04', '2026-04-27 10:08:48'),
(27, 14, 'charge', 5, 1, 1, NULL, 200.00, NULL, NULL, 0, NULL, '2026-04-05 20:04:49', '2026-04-27 10:08:48'),
(28, 14, 'charge', 5, 1, 1, NULL, 100.00, NULL, NULL, 0, NULL, '2026-04-05 20:05:41', '2026-04-27 10:08:48'),
(29, 16, 'charge', 6, 2, 1, NULL, 6000.00, NULL, NULL, 0, NULL, '2026-04-05 20:07:55', '2026-04-27 10:08:48'),
(30, 17, 'charge', 26, 2, 1, NULL, 6000.00, NULL, NULL, 0, NULL, '2026-04-08 17:29:33', '2026-04-27 10:08:48'),
(31, 18, 'charge', 38, 2, 7, NULL, 3500.00, NULL, NULL, 0, NULL, '2026-04-22 11:56:13', '2026-04-27 10:08:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','dentist','receptionist','patient') NOT NULL DEFAULT 'patient',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', '$2y$10$5ePTldNYfWzJeSUHjQj83.SMLN0w9eadlab.FzUxpvlvBnpaJsW9e', 'admin@auradent.com', 'admin', 1, NULL, '2026-03-18 11:39:18', '2026-03-18 11:39:18', NULL),
(4, 'dentist', '$2y$10$5ePTldNYfWzJeSUHjQj83.SMLN0w9eadlab.FzUxpvlvBnpaJsW9e', 'vimclydetan123@gmail.com', 'dentist', 1, NULL, '2026-03-18 19:31:58', '2026-04-02 20:13:36', NULL),
(5, 'vimvim', '$2y$10$5ePTldNYfWzJeSUHjQj83.SMLN0w9eadlab.FzUxpvlvBnpaJsW9e', 'vimclydetan0293@gmail.com', 'dentist', 1, NULL, '2026-03-18 20:20:43', '2026-04-09 19:41:20', NULL),
(6, 'vimclyde', '$2y$10$5ePTldNYfWzJeSUHjQj83.SMLN0w9eadlab.FzUxpvlvBnpaJsW9e', 'vimclydetan@gmail.com', 'receptionist', 1, NULL, '2026-03-20 09:22:41', '2026-03-24 09:38:29', NULL),
(7, 'leilei', '$2y$10$5ePTldNYfWzJeSUHjQj83.SMLN0w9eadlab.FzUxpvlvBnpaJsW9e', 'lei@gmail.com', 'patient', 1, NULL, '2026-03-20 09:34:59', '2026-03-23 16:53:07', NULL),
(8, 'jasmine', '$2y$10$F.MrgiAbeZuvcRzlOgWrOeCZQwECUFOER1ZiF5PR5eDQMSKWWkyTy', 'jasminejanegonzales8@gmail.com', 'patient', 1, NULL, '2026-03-20 21:15:28', '2026-03-20 21:15:28', NULL),
(9, 'jasm', '$2y$10$RIMRYGzFECQ2FPJECool5Ok5YfHWVCYxYCHqDuWcH88MCFHg6NkEO', 'jasminejanegonzales82@gmail.com', 'patient', 1, NULL, '2026-03-20 21:49:50', '2026-03-20 21:49:50', NULL),
(10, 'jasjas', '$2y$10$pailAkSkvaFJxXezk/5ENO/0WTgRyBWHzk2rF9GxczaBkvdLapmBa', 'jas222@gmail.com', 'patient', 1, NULL, '2026-03-20 22:06:26', '2026-03-20 22:06:26', NULL),
(11, 'jakecu', '$2y$10$IJCwq2H5VQxXNQ4xmocx8.jNkq1My6AVbf2wbVNH2RSsQLlxfn1Sy', 'vimclydet23123n26@gmail.com', 'patient', 1, NULL, '2026-03-20 22:20:54', '2026-03-20 22:20:54', NULL),
(15, 'gabrelltan', '$2y$10$U/yKr27Ky2mL7aHGr2kDUubI3dvVU40XuuC4W5HRYpxgk03WgrvjG', 'gab@gmail.com', 'patient', 1, NULL, '2026-03-23 12:20:11', '2026-03-23 12:20:11', NULL),
(16, 'gab12', '$2y$10$e4.li7sd2QfHWnzdvPWWGe4hu8McmTMziUVkgr8hrKklx4XLsmYUe', 'gab2@gmail.com', 'patient', 1, NULL, '2026-03-23 12:44:11', '2026-03-23 12:44:11', NULL),
(21, 'testuser', '$2y$10$SJjV/.tnS/tY0piFPglY6eTDKbncZA5Cb9fVQXA2UX4XfbDK/kOAS', 'user@gmail.com', 'patient', 1, NULL, '2026-03-23 13:39:45', '2026-03-23 13:39:45', NULL),
(22, 'testvim', '$2y$10$IsKWzHesEoiV5vvpbmRU7.rkEIg20odvUFRJItmUUNXR9MMnJWiWi', 'vimtest@gmail.com', 'patient', 1, NULL, '2026-03-23 14:27:24', '2026-03-23 14:27:24', NULL),
(23, 'testappointment', '$2y$10$rgvx9U2amDTG9zxVMikGhOZ0IIvUTLL.5WFQEXd22tKfVf84HYIs6', '', 'patient', 1, NULL, '2026-03-24 11:23:06', '2026-04-13 12:33:55', NULL),
(24, 'female', '$2y$10$y9QuW65VVniUMEijZcYmrueet0glo3idQbry0jJsfjeq3.mUEy84C', 'appointment@gmail.com', 'patient', 1, NULL, '2026-03-24 11:41:07', '2026-03-24 11:41:07', NULL),
(31, 'jarreltan', '$2y$10$pUr6axQiYKhzZBZb.znEuuZnlHqpfapmxjx2wC0P6IrqpWphDqVXG', 'jarreltan@gmail.com', 'patient', 1, NULL, '2026-03-25 15:28:20', '2026-03-25 15:28:20', NULL),
(35, 'harvietan', '$2y$10$53Nf6RMHrhI7hoZQlhd8TesbFIgSBYBHd1f9fhvfRv9HD.jpuaKxO', 'harvietan@gmail.com', 'patient', 1, NULL, '2026-04-01 18:25:09', '2026-04-01 18:25:09', NULL),
(36, 'bradleyfolloso', '$2y$10$K5TS8soydWV72YbqYwqvX.92vQvzAKEHvvwt5h4XEeHRhFj6pv2K.', 'bradley@gmail.com', 'patient', 1, NULL, '2026-04-01 18:35:49', '2026-04-13 20:12:00', NULL),
(37, 'harvievie', '$2y$10$h1XHwEL74S/6BaYLdQAhSuzJo3AcO.JybdidmVsxtnbGCRKrcgRzC', 'harvietan7@gmail.com', 'dentist', 1, NULL, '2026-04-02 20:53:16', '2026-04-02 21:05:51', NULL),
(39, 'victortan', '$2y$10$MayFJvia/RMzluzFoagNHuM0Gec5WIrX2rNs2nLP7DGvqHXIW0u2e', 'tanvictor1980@gmail.com', 'patient', 1, NULL, '2026-04-05 19:13:05', '2026-04-05 19:13:05', NULL),
(40, 'bryllekaizer', '$2y$10$5ePTldNYfWzJeSUHjQj83.SMLN0w9eadlab.FzUxpvlvBnpaJsW9e', 'mcoc6215@gmail.com', 'patient', 1, NULL, '2026-04-10 22:18:17', '2026-04-13 20:46:40', NULL),
(44, 'francisjairo', '$2y$10$5ePTldNYfWzJeSUHjQj83.SMLN0w9eadlab.FzUxpvlvBnpaJsW9e', 'jairovivas25@gmail.com', 'patient', 1, NULL, '2026-04-14 11:39:02', '2026-04-20 18:40:47', NULL),
(45, 'jaijai', '$2y$10$avBpkqUFY2u3ChEAbCNR4u662595FIGlkArIwcEZ3JYi12wYyKOtu', 'jai@gmail.com', 'patient', 1, NULL, '2026-04-14 15:59:01', '2026-04-14 15:59:01', NULL),
(46, 'vembo', '$2y$10$I9nVD97z6Idl8Y1yuRFaq.Ze2.6.L.sOoaZSdKo9ZWQS0Y.AF.svK', 'vembo@gmail.com', 'patient', 1, NULL, '2026-04-14 16:42:48', '2026-04-14 16:42:48', NULL),
(47, 'tessie', '$2y$10$nAVWWUHYlPH3RJn4Nz.2I.UNzOP291unRl3fpVA/PRt2RU.LBGPKq', 'asjdajsdjasd@gmail.com', 'patient', 1, NULL, '2026-04-14 16:52:28', '2026-04-14 16:52:28', NULL),
(48, 'folloso', '$2y$10$i4iqsuJQ3t...CmOvXp.5u.GIo.AJKRpvq40BBpb1uKuFuq.CK4YK', 'bradfolloso@gmail.com', 'patient', 1, NULL, '2026-04-22 13:42:10', '2026-04-22 13:42:10', NULL),
(49, 'jaitest', '$2y$10$OxKn1rJ6gPRUisAZs3MFtuJiFvJP21CLwl1R.o2n3U9iGHrEuVySa', 'jaitest@gmail.com', 'patient', 1, NULL, '2026-04-22 13:52:40', '2026-04-22 13:52:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `visits`
--

CREATE TABLE `visits` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `dentist_id` int(11) DEFAULT NULL,
  `visit_type` enum('Appointment','Walk-in') NOT NULL,
  `check_in_time` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visits`
--

INSERT INTO `visits` (`id`, `patient_id`, `appointment_id`, `service_id`, `dentist_id`, `visit_type`, `check_in_time`) VALUES
(7, 32, NULL, 1, 1, 'Walk-in', '2026-03-25 17:00:42'),
(8, 22, 27, 1, 1, 'Appointment', '2026-03-26 19:40:21'),
(11, 35, NULL, 1, 1, 'Walk-in', '2026-03-26 20:07:07'),
(12, 6, 10, 1, 1, 'Appointment', '2026-04-05 19:37:23'),
(13, 7, 11, 1, 1, 'Appointment', '2026-04-05 19:40:23'),
(14, 5, 9, 1, 1, 'Appointment', '2026-04-05 19:40:50'),
(15, 8, 12, 1, 1, 'Appointment', '2026-04-05 19:48:09'),
(16, 6, 26, 1, 2, 'Appointment', '2026-04-05 20:07:55'),
(17, 26, 31, 1, 2, 'Appointment', '2026-04-08 17:29:33'),
(18, 38, 93, 7, 2, 'Appointment', '2026-04-22 11:56:13');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `idx_queue` (`queue_date`,`dentist_id`,`queue_number`),
  ADD KEY `idx_email_pending` (`email_confirmation_sent`,`appointment_date`),
  ADD KEY `idx_arrival` (`arrival_time`);

--
-- Indexes for table `appointment_cancel_requests`
--
ALTER TABLE `appointment_cancel_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `appointment_confirmations`
--
ALTER TABLE `appointment_confirmations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `confirmation_token` (`confirmation_token`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `idx_token` (`confirmation_token`),
  ADD KEY `idx_expires` (`expires_at`);

--
-- Indexes for table `appointment_services`
--
ALTER TABLE `appointment_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `idx_appointment` (`appointment_id`),
  ADD KEY `idx_service` (`service_id`),
  ADD KEY `idx_appointment_service` (`appointment_id`,`service_id`);

--
-- Indexes for table `dentists`
--
ALTER TABLE `dentists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_queue`
--
ALTER TABLE `email_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_process` (`status`,`scheduled_at`,`retry_count`);

--
-- Indexes for table `guardian_information`
--
ALTER TABLE `guardian_information`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient` (`patient_id`),
  ADD KEY `idx_primary` (`is_primary`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `medical_conditions`
--
ALTER TABLE `medical_conditions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `condition_key` (`condition_key`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_category` (`category`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD UNIQUE KEY `patient_code` (`patient_code`),
  ADD KEY `guardian_id` (`guardian_id`),
  ADD KEY `idx_name` (`last_name`,`first_name`),
  ADD KEY `idx_birthdate` (`birthdate`),
  ADD KEY `idx_mobile` (`primary_mobile`),
  ADD KEY `idx_city` (`city`),
  ADD KEY `idx_province` (`province`);

--
-- Indexes for table `patient_medical_records`
--
ALTER TABLE `patient_medical_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `treatment_records`
--
ALTER TABLE `treatment_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visit_id` (`visit_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `dentist_id` (`dentist_id`),
  ADD KEY `service_id` (`service_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);

--
-- Indexes for table `visits`
--
ALTER TABLE `visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `appointment_cancel_requests`
--
ALTER TABLE `appointment_cancel_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `appointment_confirmations`
--
ALTER TABLE `appointment_confirmations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `appointment_services`
--
ALTER TABLE `appointment_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `dentists`
--
ALTER TABLE `dentists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `email_queue`
--
ALTER TABLE `email_queue`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=118;

--
-- AUTO_INCREMENT for table `guardian_information`
--
ALTER TABLE `guardian_information`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `medical_conditions`
--
ALTER TABLE `medical_conditions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `patient_medical_records`
--
ALTER TABLE `patient_medical_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `treatment_records`
--
ALTER TABLE `treatment_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `visits`
--
ALTER TABLE `visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

--
-- Constraints for table `appointment_cancel_requests`
--
ALTER TABLE `appointment_cancel_requests`
  ADD CONSTRAINT `appointment_cancel_requests_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `appointment_confirmations`
--
ALTER TABLE `appointment_confirmations`
  ADD CONSTRAINT `appointment_confirmations_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `appointment_services`
--
ALTER TABLE `appointment_services`
  ADD CONSTRAINT `appointment_services_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`),
  ADD CONSTRAINT `fk_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_service` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `guardian_information`
--
ALTER TABLE `guardian_information`
  ADD CONSTRAINT `guardian_information_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `fk_patient_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patients_ibfk_2` FOREIGN KEY (`guardian_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `treatment_records`
--
ALTER TABLE `treatment_records`
  ADD CONSTRAINT `treatment_records_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `treatment_records_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `treatment_records_ibfk_3` FOREIGN KEY (`dentist_id`) REFERENCES `dentists` (`id`),
  ADD CONSTRAINT `treatment_records_ibfk_4` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

--
-- Constraints for table `visits`
--
ALTER TABLE `visits`
  ADD CONSTRAINT `visits_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `visits_ibfk_2` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
