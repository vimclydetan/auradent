CREATE DATABASE auradent_db;
USE auradent_db;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `dentist_id` int(11) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `appointment_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `appointment_time` time NOT NULL,
  `end_time` time DEFAULT NULL,
  `status` enum('Pending','Confirmed','Completed','Cancelled') DEFAULT 'Pending',
  `region` varchar(100) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `appointments` (`id`, `patient_id`, `dentist_id`, `service_id`, `appointment_date`, `end_date`, `appointment_time`, `end_time`, `status`, `region`, `remarks`, `created_at`) VALUES
(9, 5, 1, NULL, '2026-03-22', '2026-03-22', '10:22:00', '01:22:00', 'Confirmed', '04', NULL, '2026-03-20 01:22:41'),
(10, 6, 1, NULL, '2026-03-20', '2026-03-19', '10:34:00', '00:34:00', 'Confirmed', 'Region IV-A (CALABARZON)', NULL, '2026-03-20 01:34:59'),
(11, 7, 1, NULL, '2026-03-21', '2026-03-21', '21:16:00', '22:15:00', 'Confirmed', 'Region IV-A (CALABARZON)', NULL, '2026-03-20 13:15:28'),
(12, 8, 1, NULL, '2026-03-20', '2026-03-21', '22:16:00', '12:15:00', 'Pending', NULL, NULL, '2026-03-20 13:49:50'),
(13, 9, 1, NULL, '2026-03-21', '2026-03-21', '22:17:00', '22:18:00', 'Pending', NULL, NULL, '2026-03-20 14:06:26'),
(14, 10, 2, NULL, '2026-03-20', '2026-03-20', '23:20:00', '23:25:00', 'Pending', NULL, NULL, '2026-03-20 14:20:54');

CREATE TABLE `appointment_services` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `service_level` enum('Standard','Simple','Moderate','Severe') DEFAULT 'Standard'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `appointment_services` (`id`, `appointment_id`, `service_id`, `service_level`) VALUES
(8, 9, 1, 'Standard'),
(9, 10, 1, 'Standard'),
(10, 11, 1, 'Standard'),
(11, 12, 1, 'Standard'),
(12, 13, 1, 'Standard'),
(13, 14, 1, 'Standard');

CREATE TABLE `billing` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `payment_status` enum('Paid','Unpaid','Partial') DEFAULT 'Unpaid',
  `billing_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `billing_items` (
  `id` int(11) NOT NULL,
  `billing_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `dentists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT 'default.png',
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `extension_name` enum('Jr','Sr','I','II','III','IV','V','VI','VII') DEFAULT NULL,
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

INSERT INTO `dentists` (`id`, `user_id`, `profile_pic`, `first_name`, `middle_name`, `last_name`, `extension_name`, `gender`, `birthdate`, `house_number`, `street`, `barangay`, `city`, `province`, `region`, `contact_number`, `created_at`, `updated_at`) VALUES
(1, 4, '1773833518_975ffcbb9bb3190c119c.jpg', 'Vimmy', 'Clyde', 'Tan', '', 'Male', '2006-09-07', '0878', 'Purok 5', 'Makiling', 'Calamba City', 'Laguna', NULL, '09123123123', '2026-03-18 19:31:58', '2026-03-20 21:47:11'),
(2, 5, '1773836443_9d99c4cf0c918feb96a7.jpg', 'Vim', 'Clyde', 'Tan', '', 'Male', '2006-09-07', '0878', 'Purok 5', 'Makiling', 'City Of Calamba', 'Laguna', NULL, '091231231211', '2026-03-18 20:20:43', '2026-03-20 21:47:13');

CREATE TABLE `emergency_contacts` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `contact_first_name` varchar(100) NOT NULL,
  `contact_last_name` varchar(100) NOT NULL,
  `contact_middle_name` varchar(100) DEFAULT NULL,
  `relationship` enum('Spouse','Parent','Child','Sibling','Guardian','Friend','Other') NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `mobile_number` varchar(20) NOT NULL,
  `secondary_number` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `medical_conditions` (
  `id` int(11) NOT NULL,
  `condition_key` varchar(100) NOT NULL,
  `condition_label` varchar(150) NOT NULL,
  `category` enum('cardiovascular','respiratory','neurological','infectious','metabolic','dental','other') DEFAULT 'other',
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `is_critical` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `medical_conditions` (`id`, `condition_key`, `condition_label`, `category`, `is_active`, `sort_order`, `is_critical`, `created_at`, `updated_at`) VALUES
(1, 'high_blood_pressure', 'High blood pressure', 'cardiovascular', 1, 0, 1, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
(2, 'low_blood_pressure', 'Low blood pressure', 'cardiovascular', 1, 0, 0, '2026-03-18 11:37:37', '2026-03-18 11:37:37'),
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
(71, 'allergy_local_anesthetic', 'Local Anesthetic', '', 1, 0, 1, '2026-03-21 09:11:46', '2026-03-21 09:11:46'),
(72, 'allergy_penicillin', 'Penicillin, Antibiotics', '', 1, 0, 1, '2026-03-21 09:11:46', '2026-03-21 09:11:46'),
(73, 'allergy_latex', 'Latex', '', 1, 0, 1, '2026-03-21 09:11:46', '2026-03-21 09:11:46'),
(74, 'allergy_sulfa', 'Sulfa Drugs', '', 1, 0, 1, '2026-03-21 09:11:46', '2026-03-21 09:11:46'),
(75, 'allergy_aspirin', 'Aspirin', '', 1, 0, 1, '2026-03-21 09:11:46', '2026-03-21 09:11:46');

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `name_suffix` enum('Jr.','Sr.','II','III','IV','V') DEFAULT NULL,
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
  `secondary_mobile` varchar(20) DEFAULT NULL,
  `home_phone` varchar(20) DEFAULT NULL,
  `office_phone` varchar(20) DEFAULT NULL,
  `fax_number` varchar(20) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('Male','Female','Prefer not to say') NOT NULL,
  `civil_status` enum('Single','Married','Widowed','Separated','Divorced') DEFAULT NULL,
  `occupation` varchar(100) DEFAULT NULL,
  `guardian_id` int(11) DEFAULT NULL,
  `has_insurance` tinyint(1) DEFAULT 0,
  `insurance_provider` varchar(100) DEFAULT NULL,
  `insurance_policy_number` varchar(50) DEFAULT NULL,
  `insurance_valid_until` date DEFAULT NULL,
  `referred_by` varchar(150) DEFAULT NULL,
  `referral_source` enum('Friend','Family','Online','Walk-in','Other') DEFAULT 'Walk-in',
  `reason_for_consultation` text DEFAULT NULL,
  `previous_dentist` varchar(150) DEFAULT NULL,
  `last_dental_visit` date DEFAULT NULL,
  `profile_image_path` varchar(255) DEFAULT NULL,
  `signature_path` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `patients` (`id`, `user_id`, `first_name`, `middle_name`, `last_name`, `name_suffix`, `house_number`, `building_name`, `street_name`, `subdivision`, `barangay`, `city`, `province`, `postal_code`, `region`, `country`, `primary_mobile`, `secondary_mobile`, `home_phone`, `office_phone`, `fax_number`, `birthdate`, `gender`, `civil_status`, `occupation`, `guardian_id`, `has_insurance`, `insurance_provider`, `insurance_policy_number`, `insurance_valid_until`, `referred_by`, `referral_source`, `reason_for_consultation`, `previous_dentist`, `last_dental_visit`, `profile_image_path`, `signature_path`, `created_at`, `updated_at`) VALUES
(5, 6, 'Vim', 'Clyde', 'Tan', '', NULL, NULL, NULL, NULL, '043405026', '043405', '0434', NULL, '04', 'Philippines', '09851215274', NULL, NULL, NULL, NULL, '2026-03-04', 'Male', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Walk-in', NULL, NULL, NULL, NULL, NULL, '2026-03-20 09:22:41', '2026-03-20 09:22:41'),
(6, 7, 'Lei Margarette', '', 'Tan', '', NULL, NULL, NULL, NULL, 'Makiling', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09121231234', NULL, NULL, NULL, NULL, '2006-09-07', 'Female', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Walk-in', NULL, NULL, NULL, NULL, NULL, '2026-03-20 09:34:59', '2026-03-20 09:34:59'),
(7, 8, 'Jasmine Jane', 'Manalang', 'Gonzales', '', NULL, NULL, NULL, NULL, 'Santa Anastacia', 'Santo Tomas', 'Batangas', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09123456789', NULL, NULL, NULL, NULL, '2004-11-02', 'Female', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Walk-in', NULL, NULL, NULL, NULL, NULL, '2026-03-20 21:15:28', '2026-03-20 21:15:28'),
(8, 9, 'Jas', '', 'Tan', '', NULL, NULL, NULL, NULL, 'Makiling', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09123902192', NULL, NULL, NULL, NULL, '2004-03-20', 'Female', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Walk-in', NULL, NULL, NULL, NULL, NULL, '2026-03-20 21:49:50', '2026-03-20 21:49:50'),
(9, 10, 'Jasmine', 'Jin', 'Gon', '', NULL, NULL, NULL, NULL, 'Santa Anastacia', 'Santo Tomas', 'Batangas', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09123456789', NULL, NULL, NULL, NULL, '2004-03-20', 'Male', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Walk-in', NULL, NULL, NULL, NULL, NULL, '2026-03-20 22:06:26', '2026-03-20 22:06:26'),
(10, 11, 'Jake', '', 'Cuenca', '', NULL, NULL, NULL, NULL, 'Makiling', 'City Of Calamba', 'Laguna', NULL, 'Region IV-A (CALABARZON)', 'Philippines', '09851215274', NULL, NULL, NULL, NULL, '2006-07-09', 'Male', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, 'Walk-in', NULL, NULL, NULL, NULL, NULL, '2026-03-20 22:20:54', '2026-03-20 22:20:54');

CREATE TABLE `patient_medical_records` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `physician_name` varchar(150) DEFAULT NULL,
  `physician_specialty` varchar(100) DEFAULT NULL,
  `physician_clinic` varchar(150) DEFAULT NULL,
  `physician_address` text DEFAULT NULL,
  `physician_phone` varchar(20) DEFAULT NULL,
  `physician_email` varchar(100) DEFAULT NULL,
  `blood_type` enum('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `blood_pressure_systolic` int(11) DEFAULT NULL,
  `blood_pressure_diastolic` int(11) DEFAULT NULL,
  `is_good_health` tinyint(1) DEFAULT 1,
  `is_under_medical_treatment` tinyint(1) DEFAULT 0,
  `has_serious_illness_or_surgery` tinyint(1) DEFAULT 0,
  `serious_illness_details` text DEFAULT NULL,
  `is_hospitalized` tinyint(1) DEFAULT 0,
  `hospitalization_details` text DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `medical_conditions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`medical_conditions`)),
  `bleeding_time` varchar(50) DEFAULT NULL,
  `current_medications` text DEFAULT NULL,
  `is_taking_medication` tinyint(1) DEFAULT 0,
  `smoking_status` enum('Never','Former','Current') DEFAULT 'Never',
  `alcohol_consumption` enum('Never','Occasional','Regular','Heavy') DEFAULT 'Never',
  `pregnancy_status` enum('Not Applicable','Not Pregnant','Pregnant','Breastfeeding') DEFAULT 'Not Applicable',
  `is_nursing` tinyint(1) DEFAULT 0,
  `is_taking_birth_control` tinyint(1) DEFAULT 0,
  `dental_anxiety_level` enum('None','Mild','Moderate','Severe') DEFAULT 'None',
  `preferred_anesthesia` enum('None','Local','Nitrous','General') DEFAULT 'Local',
  `form_completed_at` datetime DEFAULT NULL,
  `form_completed_by` int(11) DEFAULT NULL,
  `form_signed_at` datetime DEFAULT NULL,
  `form_signed_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `has_levels` tinyint(1) DEFAULT 0,
  `price_simple` decimal(10,2) DEFAULT NULL,
  `price_moderate` decimal(10,2) DEFAULT NULL,
  `price_severe` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `services` (`id`, `service_name`, `description`, `price`, `status`, `has_levels`, `price_simple`, `price_moderate`, `price_severe`) VALUES
(1, 'Tooth Extraction', 'asdasda', 6000.00, 'active', 0, NULL, NULL, NULL),
(2, 'JACK', '', 0.00, 'active', 1, 2500.00, 4500.00, 10000.00);

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','dentist','staff','patient') NOT NULL DEFAULT 'patient',
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `is_active`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$5ePTldNYfWzJeSUHjQj83.SMLN0w9eadlab.FzUxpvlvBnpaJsW9e', 'admin@auradent.com', 'admin', 1, NULL, '2026-03-18 11:39:18', '2026-03-18 11:39:18'),
(4, 'vim_dentist', '$2y$10$x1ROzN/pn7Y9k2./h908neovM0xrdSpJRpgz5p7iitiLBi30XaSWy', 'vimclydetan123@gmail.com', 'dentist', 1, NULL, '2026-03-18 19:31:58', '2026-03-18 19:31:58'),
(5, 'vimvim', '$2y$10$zRmG.1LDafzLcOIh9SUAFuuIQaH580XqjejSFBvzOykktGTDR7EIO', 'vimclydetan0293@gmail.com', 'dentist', 1, NULL, '2026-03-18 20:20:43', '2026-03-18 20:20:43'),
(6, 'vimclyde', '$2y$10$.8.60SDpz1VY4pmNvw/evuzhEZWMVsVtx.G41wVnxPYvn2e0Y0NW2', 'vimclydetan@gmail.com', 'patient', 1, NULL, '2026-03-20 09:22:41', '2026-03-20 09:22:41'),
(7, 'leilei', '$2y$10$amXmUlKe3Rn/xlgBBV0y9uu402TYr17PsEaMMmFDDAqy9EO.cAoai', 'lei@gmail.com', 'patient', 1, NULL, '2026-03-20 09:34:59', '2026-03-20 09:34:59'),
(8, 'jasmine', '$2y$10$F.MrgiAbeZuvcRzlOgWrOeCZQwECUFOER1ZiF5PR5eDQMSKWWkyTy', 'jasminejanegonzales8@gmail.com', 'patient', 1, NULL, '2026-03-20 21:15:28', '2026-03-20 21:15:28'),
(9, 'jasm', '$2y$10$RIMRYGzFECQ2FPJECool5Ok5YfHWVCYxYCHqDuWcH88MCFHg6NkEO', 'jasminejanegonzales82@gmail.com', 'patient', 1, NULL, '2026-03-20 21:49:50', '2026-03-20 21:49:50'),
(10, 'jasjas', '$2y$10$pailAkSkvaFJxXezk/5ENO/0WTgRyBWHzk2rF9GxczaBkvdLapmBa', 'jas222@gmail.com', 'patient', 1, NULL, '2026-03-20 22:06:26', '2026-03-20 22:06:26'),
(11, 'jakecu', '$2y$10$IJCwq2H5VQxXNQ4xmocx8.jNkq1My6AVbf2wbVNH2RSsQLlxfn1Sy', 'vimclydet23123n26@gmail.com', 'patient', 1, NULL, '2026-03-20 22:20:54', '2026-03-20 22:20:54');


ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

ALTER TABLE `appointment_services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `service_id` (`service_id`);

ALTER TABLE `billing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

ALTER TABLE `billing_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `billing_id` (`billing_id`),
  ADD KEY `service_id` (`service_id`);

ALTER TABLE `dentists`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `emergency_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_patient` (`patient_id`),
  ADD KEY `idx_primary` (`is_primary`);

ALTER TABLE `medical_conditions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `condition_key` (`condition_key`),
  ADD KEY `idx_active` (`is_active`),
  ADD KEY `idx_category` (`category`);

ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `guardian_id` (`guardian_id`),
  ADD KEY `idx_name` (`last_name`,`first_name`),
  ADD KEY `idx_birthdate` (`birthdate`),
  ADD KEY `idx_mobile` (`primary_mobile`),
  ADD KEY `idx_city` (`city`),
  ADD KEY `idx_province` (`province`);

ALTER TABLE `patient_medical_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `patient_id` (`patient_id`),
  ADD KEY `idx_patient` (`patient_id`),
  ADD KEY `idx_blood_type` (`blood_type`);

ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_role` (`role`);


ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

ALTER TABLE `appointment_services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

ALTER TABLE `billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `billing_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `dentists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `emergency_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `medical_conditions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

ALTER TABLE `patient_medical_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;


ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

ALTER TABLE `appointment_services`
  ADD CONSTRAINT `appointment_services_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_services_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

ALTER TABLE `billing_items`
  ADD CONSTRAINT `billing_items_ibfk_1` FOREIGN KEY (`billing_id`) REFERENCES `billing` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `billing_items_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`);

ALTER TABLE `emergency_contacts`
  ADD CONSTRAINT `emergency_contacts_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `patients_ibfk_2` FOREIGN KEY (`guardian_id`) REFERENCES `patients` (`id`) ON DELETE SET NULL;

ALTER TABLE `patient_medical_records`
  ADD CONSTRAINT `patient_medical_records_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;
COMMIT;
