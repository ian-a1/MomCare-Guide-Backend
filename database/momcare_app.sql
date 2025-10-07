-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 27, 2025 at 04:03 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `momcare_app`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `appointment_date` datetime NOT NULL,
  `doctor_name` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `type` enum('prenatal','ultrasound','blood_test','other') DEFAULT 'prenatal',
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `title`, `description`, `appointment_date`, `doctor_name`, `location`, `type`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Prenatal Checkup', 'Regular monthly checkup', '2025-06-02 10:00:00', 'Dr. Josefina Santos', 'Manila General Hospital', 'prenatal', 'scheduled', '2025-09-27 07:18:58', '2025-09-27 07:18:58'),
(2, 1, 'Blood Test', 'Glucose screening test', '2025-06-15 14:30:00', 'Dr. Maria Cruz', 'Lab Center', 'blood_test', 'scheduled', '2025-09-27 07:18:58', '2025-09-27 07:18:58'),
(3, 1, 'Ultrasound', '3D ultrasound scan', '2025-06-28 11:15:00', 'Dr. Ana Reyes', 'Imaging Center', 'ultrasound', 'scheduled', '2025-09-27 07:18:58', '2025-09-27 07:18:58'),
(4, 2, 'sas', 'asa', '2001-12-12 01:01:00', 'as', 'as', 'ultrasound', 'scheduled', '2025-09-27 07:24:25', '2025-09-27 07:24:25'),
(5, 4, 'sasa', 'asa', '2001-12-12 00:12:00', 'ass', 'asa', 'ultrasound', 'scheduled', '2025-09-27 07:45:28', '2025-09-27 07:45:28'),
(6, 5, 'sas', 'asa', '2001-12-12 01:01:00', '12', 'sa', 'ultrasound', 'scheduled', '2025-09-27 13:30:45', '2025-09-27 13:30:45'),
(7, 5, 'sas', 'asa', '2001-12-12 01:01:00', '12', 'sa', 'ultrasound', 'scheduled', '2025-09-27 13:30:45', '2025-09-27 13:30:45'),
(8, 5, 'sas', 'asa', '2001-12-12 01:01:00', '12', 'sa', 'ultrasound', 'scheduled', '2025-09-27 13:30:45', '2025-09-27 13:30:45'),
(9, 5, 'sas', 'asa', '2001-12-12 01:01:00', '12', 'sa', 'ultrasound', 'scheduled', '2025-09-27 13:30:45', '2025-09-27 13:30:45'),
(10, 5, 'sas', 'asa', '2001-12-12 01:01:00', '12', 'sa', 'ultrasound', 'scheduled', '2025-09-27 13:30:45', '2025-09-27 13:30:45'),
(11, 5, 'sas', 'asa', '2001-12-12 01:01:00', '12', 'sa', 'ultrasound', 'scheduled', '2025-09-27 13:30:45', '2025-09-27 13:30:45'),
(12, 5, 'sas', 'asa', '2001-12-12 01:01:00', '12', 'sa', 'ultrasound', 'scheduled', '2025-09-27 13:30:45', '2025-09-27 13:30:45'),
(13, 5, 'sas', 'asa', '2001-12-12 01:01:00', '12', 'sa', 'ultrasound', 'scheduled', '2025-09-27 13:30:45', '2025-09-27 13:30:45'),
(14, 5, 'sas', 'asa', '2001-12-12 01:01:00', '12', 'sa', 'ultrasound', 'scheduled', '2025-09-27 13:30:45', '2025-09-27 13:30:45'),
(15, 5, 'sas', 'asa', '2001-12-12 01:01:00', '12', 'sa', 'ultrasound', 'scheduled', '2025-09-27 13:30:45', '2025-09-27 13:30:45'),
(16, 5, 'sas', 'asa', '2001-12-12 01:01:00', '12', 'sa', 'ultrasound', 'scheduled', '2025-09-27 13:30:45', '2025-09-27 13:30:45'),
(17, 5, 'sas', 'asa', '2001-12-12 01:01:00', '12', 'sa', 'ultrasound', 'scheduled', '2025-09-27 13:30:45', '2025-09-27 13:30:45'),
(18, 5, 'sas', 'asa', '2001-12-12 01:01:00', '12', 'sa', 'ultrasound', 'scheduled', '2025-09-27 13:30:45', '2025-09-27 13:30:45'),
(19, 5, 'sas', 'asa', '2001-12-12 01:01:00', '12', 'sa', 'ultrasound', 'scheduled', '2025-09-27 13:30:46', '2025-09-27 13:30:46'),
(20, 6, 'wqw', 'qwq', '2001-12-12 00:12:00', '121', 'as', 'ultrasound', 'scheduled', '2025-09-27 13:46:17', '2025-09-27 13:46:17'),
(21, 7, 'wqwq', 'wq', '2001-12-12 00:12:00', 'saa', 'sa', 'prenatal', 'scheduled', '2025-09-27 14:01:22', '2025-09-27 14:01:22');

-- --------------------------------------------------------

--
-- Table structure for table `emergency_contacts`
--

CREATE TABLE `emergency_contacts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `relationship` varchar(50) DEFAULT NULL,
  `contact_type` enum('mobile','landline','emergency') DEFAULT 'mobile',
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emergency_contacts`
--

INSERT INTO `emergency_contacts` (`id`, `user_id`, `name`, `phone`, `relationship`, `contact_type`, `is_primary`, `created_at`, `updated_at`) VALUES
(1, 1, 'Partner/Spouse', '+63 917 123 4567', 'spouse', 'mobile', 1, '2025-09-27 07:18:58', '2025-09-27 07:18:58'),
(2, 1, 'Dr. Santos (OB-GYN)', '+63 917 234 5678', 'doctor', 'mobile', 0, '2025-09-27 07:18:58', '2025-09-27 07:18:58'),
(3, 1, 'Home Landline', '(02) 8123-4567', 'family', 'landline', 0, '2025-09-27 07:18:58', '2025-09-27 07:18:58'),
(4, 1, 'Emergency (911)', '911', 'emergency', 'emergency', 0, '2025-09-27 07:18:58', '2025-09-27 07:18:58'),
(5, 2, 'sasa', '1212', 'qsas', 'landline', 1, '2025-09-27 07:24:38', '2025-09-27 07:24:38'),
(6, 4, 'saa', 'asasa', 'asaa', 'emergency', 1, '2025-09-27 07:45:38', '2025-09-27 07:45:38'),
(7, 5, 'sa', '112', 'sa', 'emergency', 1, '2025-09-27 13:30:46', '2025-09-27 13:30:46'),
(8, 5, 'sa', '112', 'sa', 'emergency', 1, '2025-09-27 13:30:46', '2025-09-27 13:30:46'),
(9, 5, 'sa', '112', 'sa', 'emergency', 1, '2025-09-27 13:30:46', '2025-09-27 13:30:46'),
(10, 6, 'wqw', 'qwq', 'wq', 'landline', 1, '2025-09-27 13:46:27', '2025-09-27 13:46:27');

-- --------------------------------------------------------

--
-- Table structure for table `forum_posts`
--

CREATE TABLE `forum_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `content` text NOT NULL,
  `tags` varchar(500) DEFAULT NULL,
  `likes_count` int(11) DEFAULT 0,
  `replies_count` int(11) DEFAULT 0,
  `is_pinned` tinyint(1) DEFAULT 0,
  `status` enum('active','hidden','deleted') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_posts`
--

INSERT INTO `forum_posts` (`id`, `user_id`, `title`, `content`, `tags`, `likes_count`, `replies_count`, `is_pinned`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'First trimester morning sickness tips?', 'Hi everyone! I\'m 8 weeks pregnant and struggling with severe morning sickness. What natural remedies have worked for you?', 'first-trimester,morning-sickness,tips', 12, 10, 0, 'active', '2025-09-27 07:19:11', '2025-09-27 13:19:15'),
(2, 1, 'Best prenatal vitamins in the Philippines', 'Can anyone recommend good prenatal vitamins available locally? Looking for something with good folate content.', 'prenatal-vitamins,nutrition,philippines', 15, 8, 0, 'active', '2025-09-27 07:19:11', '2025-09-27 08:18:50'),
(3, 1, 'Exercise during second trimester', 'What exercises are safe during the second trimester? I used to run but not sure if I should continue.', 'second-trimester,exercise,safety', 9, 4, 0, 'active', '2025-09-27 07:19:11', '2025-09-27 07:19:11'),
(4, 4, 'asasa', 'sasa', 'asa', 0, 0, 0, 'active', '2025-09-27 08:18:45', '2025-09-27 08:18:45'),
(5, 5, 'sasaa', 'asas', 'asas', 0, 0, 0, 'active', '2025-09-27 13:26:42', '2025-09-27 13:26:42');

-- --------------------------------------------------------

--
-- Table structure for table `forum_post_likes`
--

CREATE TABLE `forum_post_likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forum_replies`
--

CREATE TABLE `forum_replies` (
  `id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `likes_count` int(11) DEFAULT 0,
  `status` enum('active','hidden','deleted') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forum_replies`
--

INSERT INTO `forum_replies` (`id`, `post_id`, `user_id`, `content`, `likes_count`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Ginger tea really helped me! Also eating small frequent meals instead of large ones.', 5, 'active', '2025-09-27 07:19:11', '2025-09-27 07:19:11'),
(2, 1, 1, 'Try crackers before getting out of bed in the morning. It made a huge difference for me.', 3, 'active', '2025-09-27 07:19:11', '2025-09-27 07:19:11'),
(3, 2, 1, 'I\'ve been taking Obimin Plus and it\'s been great. Available in most Mercury Drug stores.', 7, 'active', '2025-09-27 07:19:11', '2025-09-27 07:19:11'),
(4, 2, 4, 'sasa', 0, 'active', '2025-09-27 08:18:49', '2025-09-27 08:18:49'),
(5, 2, 4, 'asa', 0, 'active', '2025-09-27 08:18:50', '2025-09-27 08:18:50'),
(6, 1, 5, 'yes', 0, 'active', '2025-09-27 13:18:26', '2025-09-27 13:18:26'),
(7, 1, 5, 'test', 0, 'active', '2025-09-27 13:19:15', '2025-09-27 13:19:15');

-- --------------------------------------------------------

--
-- Table structure for table `forum_reply_likes`
--

CREATE TABLE `forum_reply_likes` (
  `id` int(11) NOT NULL,
  `reply_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `library_content`
--

CREATE TABLE `library_content` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `category` enum('recipes','mental-health','exercise') NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `rating` decimal(3,2) DEFAULT 0.00,
  `views_count` int(11) DEFAULT 0,
  `downloads_count` int(11) DEFAULT 0,
  `tags` varchar(500) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `file_url` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT 0,
  `duration_minutes` int(11) DEFAULT 0,
  `difficulty_level` enum('beginner','intermediate','advanced') DEFAULT 'beginner',
  `is_featured` tinyint(1) DEFAULT 0,
  `status` enum('active','draft','archived') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `library_content`
--

INSERT INTO `library_content` (`id`, `title`, `description`, `content`, `category`, `author`, `rating`, `views_count`, `downloads_count`, `tags`, `image_url`, `file_url`, `file_size`, `duration_minutes`, `difficulty_level`, `is_featured`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Healthy Pregnancy Smoothie', 'Nutritious smoothie packed with vitamins for expecting mothers', 'Blend 1 banana, 1 cup spinach, 1/2 cup berries, 1 cup almond milk, 1 tbsp chia seeds. Rich in folate and iron.', 'recipes', 'Nutritionist Maria Santos', 4.80, 0, 0, 'smoothie,healthy,vitamins,folate', NULL, NULL, 0, 0, 'beginner', 1, 'active', '2025-09-27 07:19:11', '2025-09-27 07:19:11'),
(2, 'Filipino Chicken Tinola', 'Traditional Filipino soup perfect for pregnancy nutrition', 'Ginger-based chicken soup with malunggay leaves, rich in calcium and protein for pregnant mothers.', 'recipes', 'Chef Ana Cruz', 4.90, 0, 0, 'filipino,soup,protein,calcium', NULL, NULL, 0, 0, 'intermediate', 1, 'active', '2025-09-27 07:19:11', '2025-09-27 07:19:11'),
(3, 'Iron-Rich Adobo', 'Modified adobo recipe with added iron-rich ingredients', 'Classic Filipino adobo enhanced with liver and dark leafy greens for iron deficiency prevention.', 'recipes', 'Chef Rosa Delgado', 4.70, 0, 0, 'filipino,iron,protein,adobo', NULL, NULL, 0, 0, 'intermediate', 0, 'active', '2025-09-27 07:19:11', '2025-09-27 07:19:11'),
(4, 'Pregnancy Meditation Guide', 'Guided meditation techniques for expectant mothers', 'Learn breathing techniques and mindfulness practices to reduce pregnancy anxiety and stress.', 'mental-health', 'Dr. Patricia Lim', 4.90, 0, 0, 'meditation,anxiety,stress,mindfulness', NULL, NULL, 0, 0, 'beginner', 1, 'active', '2025-09-27 07:19:11', '2025-09-27 07:19:11'),
(5, 'Dealing with Pregnancy Anxiety', 'Comprehensive guide to managing pregnancy-related worries', 'Practical strategies for coping with common pregnancy fears and building emotional resilience.', 'mental-health', 'Psychologist Sarah Torres', 4.80, 0, 0, 'anxiety,coping,emotional-health,support', NULL, NULL, 0, 0, 'beginner', 1, 'active', '2025-09-27 07:19:11', '2025-09-27 07:19:11'),
(6, 'Postpartum Mental Health', 'Understanding and preparing for postpartum emotional changes', 'Essential information about postpartum depression, baby blues, and when to seek professional help.', 'mental-health', 'Dr. Carmen Reyes', 4.70, 0, 0, 'postpartum,depression,support,mental-health', NULL, NULL, 0, 0, 'intermediate', 0, 'active', '2025-09-27 07:19:11', '2025-09-27 07:19:11'),
(7, 'Safe Pregnancy Yoga', 'Gentle yoga routines designed for pregnant women', 'Modified yoga poses and sequences safe for all trimesters, focusing on flexibility and relaxation.', 'exercise', 'Yoga Instructor Lisa Chen', 4.80, 0, 0, 'yoga,flexibility,relaxation,safe', NULL, NULL, 0, 0, 'beginner', 1, 'active', '2025-09-27 07:19:11', '2025-09-27 07:19:11'),
(8, 'Prenatal Swimming Guide', 'Low-impact swimming exercises for pregnancy fitness', 'Water-based exercises that are gentle on joints while maintaining cardiovascular health during pregnancy.', 'exercise', 'Fitness Coach Mark Santos', 4.60, 0, 0, 'swimming,cardio,low-impact,fitness', NULL, NULL, 0, 0, 'intermediate', 0, 'active', '2025-09-27 07:19:11', '2025-09-27 07:19:11'),
(9, 'Third Trimester Stretches', 'Gentle stretching routine for late pregnancy comfort', 'Specific stretches to relieve back pain, hip discomfort, and prepare the body for labor.', 'exercise', 'Physical Therapist Jane Morales', 4.90, 0, 0, 'stretching,back-pain,comfort,labor-prep', NULL, NULL, 0, 0, 'beginner', 1, 'active', '2025-09-27 07:19:11', '2025-09-27 07:19:11');

-- --------------------------------------------------------

--
-- Table structure for table `milestones`
--

CREATE TABLE `milestones` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `week_number` int(11) DEFAULT NULL,
  `status` enum('pending','complete') DEFAULT 'pending',
  `completed_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `milestones`
--

INSERT INTO `milestones` (`id`, `user_id`, `name`, `description`, `week_number`, `status`, `completed_date`, `created_at`, `updated_at`) VALUES
(1, 1, 'Anatomy Scan', 'Detailed ultrasound scan', 20, 'complete', '2025-05-15', '2025-09-27 07:18:58', '2025-09-27 07:18:58'),
(2, 1, 'Glucose Screening', 'Test for gestational diabetes', 24, 'complete', '2025-05-28', '2025-09-27 07:18:58', '2025-09-27 07:18:58'),
(3, 5, 'sa', 'sasas', 1, 'pending', NULL, '2025-09-27 13:30:46', '2025-09-27 13:30:46'),
(4, 5, 'sa', 'sasas', 1, 'pending', NULL, '2025-09-27 13:30:46', '2025-09-27 13:30:46'),
(5, 5, 'sa', 'sasas', 1, 'pending', NULL, '2025-09-27 13:30:46', '2025-09-27 13:30:46');

-- --------------------------------------------------------

--
-- Table structure for table `pregnancy_info`
--

CREATE TABLE `pregnancy_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `due_date` date DEFAULT NULL,
  `current_week` int(11) DEFAULT 1,
  `last_period_date` date DEFAULT NULL,
  `doctor_name` varchar(100) DEFAULT NULL,
  `hospital` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pregnancy_info`
--

INSERT INTO `pregnancy_info` (`id`, `user_id`, `due_date`, `current_week`, `last_period_date`, `doctor_name`, `hospital`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-08-15', 24, NULL, 'Dr. Josefina Santos', 'Manila General Hospital', '2025-09-27 07:18:58', '2025-09-27 07:18:58'),
(2, 7, '0000-00-00', 1, NULL, '', '', '2025-09-27 14:00:55', '2025-09-27 14:00:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `birthdate` date DEFAULT NULL,
  `sex` enum('female','male','other','prefer-not-to-say') DEFAULT NULL,
  `address_line1` varchar(255) DEFAULT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `birthdate`, `sex`, `address_line1`, `address_line2`, `barangay`, `city`, `zip_code`, `created_at`, `updated_at`) VALUES
(1, 'Maria Santos', 'maria@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '1995-03-15', 'female', '123 Main St', NULL, NULL, 'Manila', '1000', '2025-09-27 07:18:58', '2025-09-27 07:18:58'),
(2, 'nem', 'nem@gmail.com', '$2y$10$2fQhUZ9n6Bcn3/iHCVXLm.plllDW6AYhUyBb7QqoyfV7DHXA4okn6', '1998-01-12', 'female', 'qwq', 'qw', 'qw', 'qwq', 'qwq', '2025-09-27 07:23:57', '2025-09-27 07:23:57'),
(3, 'pol', 'pol@gmail.com', '$2y$10$tB4g4rMw0l3WELbA1iYjP.l9hrwYVQQAbR84WnbNcNdn4xAlTtkW2', '2001-10-22', 'female', ',o', '745', 'hu', 'nu', '25', '2025-09-27 07:41:03', '2025-09-27 07:41:03'),
(4, 'aso', 'aso@gmail.com', '$2y$10$qKyJcJzsgT.wyCe9ws3D9OoP7JGMIH2JL9PqMiCMwQyBKOAcgzywS', '2001-01-12', 'female', 'asasa', 'asa', 'as', 'asa', '12', '2025-09-27 07:45:15', '2025-09-27 07:45:15'),
(5, 'qwqwq', 'as@gmail.com', '$2y$10$xhYyj1a1DmdcNK97SaZ55eVWhIpXMb2tUzTrwWknBUKAS/bKXHR.O', '2001-04-12', 'female', 'sasa', 'asasa', 'sa', 'asaa', '121', '2025-09-27 13:18:10', '2025-09-27 13:18:10'),
(6, 'sasa', 'as2@gmail.com', '$2y$10$fOucMuEx/sQfNufOPnX2yu9DpiyvypqrJkLzli63pPrdCL2VIxdKq', '2001-02-12', 'male', 'sas', 'asa', 'sas', 'sas', 'asa', '2025-09-27 13:46:00', '2025-09-27 13:46:00'),
(7, 'sasas1', 'a2w@gmail.com', '$2y$10$zYNxccmU5v.axjy0pznv7OhdkGabDTsxRjUu0TH7zJMSkGY093M3W', '2001-02-12', 'male', 'sas', 'sa', 'as', 'sa', '121', '2025-09-27 13:55:20', '2025-09-27 14:00:55');

-- --------------------------------------------------------

--
-- Table structure for table `user_downloads`
--

CREATE TABLE `user_downloads` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `download_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_downloads_tracking`
--

CREATE TABLE `user_downloads_tracking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `download_path` varchar(255) DEFAULT NULL,
  `file_size_mb` decimal(10,2) DEFAULT NULL,
  `download_status` enum('downloading','completed','failed') DEFAULT 'downloading',
  `progress_percentage` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_downloads_tracking`
--

INSERT INTO `user_downloads_tracking` (`id`, `user_id`, `content_id`, `download_path`, `file_size_mb`, `download_status`, `progress_percentage`, `created_at`, `completed_at`) VALUES
(1, 1, 1, '/downloads/healthy-pregnancy-smoothie.pdf', 2.50, 'completed', 100, '2025-09-27 07:19:11', '2025-09-27 07:19:11'),
(2, 1, 4, '/downloads/pregnancy-meditation-guide.mp3', 15.80, 'completed', 100, '2025-09-27 07:19:11', '2025-09-27 07:19:11'),
(3, 1, 7, '/downloads/safe-pregnancy-yoga.mp4', 125.30, 'completed', 100, '2025-09-27 07:19:11', '2025-09-27 07:19:11');

-- --------------------------------------------------------

--
-- Table structure for table `user_library_interactions`
--

CREATE TABLE `user_library_interactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content_id` int(11) NOT NULL,
  `interaction_type` enum('view','like','bookmark','download','rating') NOT NULL,
  `rating_value` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_library_interactions`
--

INSERT INTO `user_library_interactions` (`id`, `user_id`, `content_id`, `interaction_type`, `rating_value`, `created_at`) VALUES
(1, 1, 1, 'like', NULL, '2025-09-27 07:19:11'),
(2, 1, 1, 'bookmark', NULL, '2025-09-27 07:19:11'),
(3, 1, 1, 'rating', 5, '2025-09-27 07:19:11'),
(4, 1, 2, 'view', NULL, '2025-09-27 07:19:11'),
(5, 1, 4, 'like', NULL, '2025-09-27 07:19:11'),
(6, 1, 4, 'rating', 5, '2025-09-27 07:19:11'),
(7, 1, 7, 'bookmark', NULL, '2025-09-27 07:19:11');

-- --------------------------------------------------------

--
-- Table structure for table `user_medical_info`
--

CREATE TABLE `user_medical_info` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `medications` text DEFAULT NULL,
  `emergency_medical_info` text DEFAULT NULL,
  `insurance_provider` varchar(100) DEFAULT NULL,
  `insurance_number` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_medical_info`
--

INSERT INTO `user_medical_info` (`id`, `user_id`, `blood_type`, `allergies`, `medical_conditions`, `medications`, `emergency_medical_info`, `insurance_provider`, `insurance_number`, `created_at`, `updated_at`) VALUES
(1, 1, 'O+', 'None known', 'None', NULL, NULL, NULL, NULL, '2025-09-27 07:19:11', '2025-09-27 07:19:11'),
(2, 7, 'a', 'a', 'a', 'a', NULL, NULL, NULL, '2025-09-27 14:01:02', '2025-09-27 14:01:02'),
(3, 7, 'a', 'a', 'a', 'a', NULL, NULL, NULL, '2025-09-27 14:01:04', '2025-09-27 14:01:04');

-- --------------------------------------------------------

--
-- Table structure for table `user_profile_settings`
--

CREATE TABLE `user_profile_settings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_appointments` tinyint(1) DEFAULT 1,
  `notification_milestones` tinyint(1) DEFAULT 1,
  `notification_forum` tinyint(1) DEFAULT 1,
  `notification_emergency` tinyint(1) DEFAULT 1,
  `privacy_profile_public` tinyint(1) DEFAULT 0,
  `privacy_show_pregnancy_info` tinyint(1) DEFAULT 1,
  `privacy_allow_messages` tinyint(1) DEFAULT 1,
  `theme_preference` enum('light','dark','auto') DEFAULT 'light',
  `language_preference` varchar(10) DEFAULT 'en',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_profile_settings`
--

INSERT INTO `user_profile_settings` (`id`, `user_id`, `notification_appointments`, `notification_milestones`, `notification_forum`, `notification_emergency`, `privacy_profile_public`, `privacy_show_pregnancy_info`, `privacy_allow_messages`, `theme_preference`, `language_preference`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 0, 1, 1, 'light', 'en', '2025-09-27 07:19:11', '2025-09-27 07:19:11'),
(2, 7, 1, 1, 1, 1, 1, 1, 1, 'light', 'en', '2025-09-27 14:01:07', '2025-09-27 14:01:07');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`id`, `user_id`, `session_token`, `expires_at`, `created_at`) VALUES
(1, 2, 'ad097d5f5da0ac194d8a909a07ab77a89b73edd0155bb02e3499d885734e9b0e', '2025-10-27 01:23:57', '2025-09-27 07:23:57'),
(2, 3, 'e55207bdb8297aacb8925e48c1e71edd82d9e9c140f6614c36a05e676ddaae84', '2025-10-27 01:41:03', '2025-09-27 07:41:03'),
(3, 4, '018e933edd0ae4383b054967dddff1f5cac03dcb65bd4afe5e8c9544fba15ee5', '2025-10-27 02:19:00', '2025-09-27 07:45:15'),
(4, 5, '7d1abeb26a4eb6539196b425fac11c7f346afb1d9e162a7e650e04c412754d20', '2025-10-27 07:24:10', '2025-09-27 13:18:10'),
(5, 6, '49f0baecb9e30ed676e0f741fde5756084eef4d8e2bb924b4e83910fdbc1f5bb', '2025-10-27 07:46:00', '2025-09-27 13:46:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `forum_post_likes`
--
ALTER TABLE `forum_post_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_post_like` (`post_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `forum_reply_likes`
--
ALTER TABLE `forum_reply_likes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_reply_like` (`reply_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `library_content`
--
ALTER TABLE `library_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `milestones`
--
ALTER TABLE `milestones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pregnancy_info`
--
ALTER TABLE `pregnancy_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_downloads`
--
ALTER TABLE `user_downloads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_downloads_tracking`
--
ALTER TABLE `user_downloads_tracking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `content_id` (`content_id`);

--
-- Indexes for table `user_library_interactions`
--
ALTER TABLE `user_library_interactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_content_interaction` (`user_id`,`content_id`,`interaction_type`),
  ADD KEY `content_id` (`content_id`);

--
-- Indexes for table `user_medical_info`
--
ALTER TABLE `user_medical_info`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_profile_settings`
--
ALTER TABLE `user_profile_settings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `forum_post_likes`
--
ALTER TABLE `forum_post_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forum_replies`
--
ALTER TABLE `forum_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `forum_reply_likes`
--
ALTER TABLE `forum_reply_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `library_content`
--
ALTER TABLE `library_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `milestones`
--
ALTER TABLE `milestones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pregnancy_info`
--
ALTER TABLE `pregnancy_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_downloads`
--
ALTER TABLE `user_downloads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_downloads_tracking`
--
ALTER TABLE `user_downloads_tracking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_library_interactions`
--
ALTER TABLE `user_library_interactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_medical_info`
--
ALTER TABLE `user_medical_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `user_profile_settings`
--
ALTER TABLE `user_profile_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  ADD CONSTRAINT `emergency_contacts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_post_likes`
--
ALTER TABLE `forum_post_likes`
  ADD CONSTRAINT `forum_post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_replies`
--
ALTER TABLE `forum_replies`
  ADD CONSTRAINT `forum_replies_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `forum_reply_likes`
--
ALTER TABLE `forum_reply_likes`
  ADD CONSTRAINT `forum_reply_likes_ibfk_1` FOREIGN KEY (`reply_id`) REFERENCES `forum_replies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_reply_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `milestones`
--
ALTER TABLE `milestones`
  ADD CONSTRAINT `milestones_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pregnancy_info`
--
ALTER TABLE `pregnancy_info`
  ADD CONSTRAINT `pregnancy_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_downloads`
--
ALTER TABLE `user_downloads`
  ADD CONSTRAINT `user_downloads_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_downloads_tracking`
--
ALTER TABLE `user_downloads_tracking`
  ADD CONSTRAINT `user_downloads_tracking_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_downloads_tracking_ibfk_2` FOREIGN KEY (`content_id`) REFERENCES `library_content` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_library_interactions`
--
ALTER TABLE `user_library_interactions`
  ADD CONSTRAINT `user_library_interactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_library_interactions_ibfk_2` FOREIGN KEY (`content_id`) REFERENCES `library_content` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_medical_info`
--
ALTER TABLE `user_medical_info`
  ADD CONSTRAINT `user_medical_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profile_settings`
--
ALTER TABLE `user_profile_settings`
  ADD CONSTRAINT `user_profile_settings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
