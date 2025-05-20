-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 30, 2024 at 09:41 AM
-- Server version: 8.0.40-0ubuntu0.22.04.1
-- PHP Version: 8.2.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `m2_module`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_picture` varchar(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_super` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `profile_picture`, `is_super`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'developer@yopmail.com', '$2y$10$DMg1GtsjkvcGyqOreeexEuq7J5sNWvWjNgxzO68/mh2RAR0v0uGIC', '', 1, 'pMECRBUJBYqLcMMzY6ddZJAnNRjX0C7Yh6lZpxnZYDSAK3RYzGaYfQUBAun0', '2022-06-14 09:08:09', '2024-07-26 06:41:52');

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `android_version` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `ios_version` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `android_force_update` tinyint(1) NOT NULL DEFAULT '1',
  `ios_force_update` tinyint(1) NOT NULL DEFAULT '1',
  `is_maintenance` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `android_version`, `ios_version`, `android_force_update`, `ios_force_update`, `is_maintenance`, `created_at`, `updated_at`) VALUES
(1, '1.0.0', '1.0', 0, 0, 0, '2022-06-14 09:10:38', '2023-10-10 13:06:56');

-- --------------------------------------------------------

--
-- Table structure for table `block_users`
--

CREATE TABLE `block_users` (
  `id` int NOT NULL,
  `blocked_by` int NOT NULL,
  `blocked_to` int NOT NULL,
  `group_code` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` int NOT NULL,
  `sender` int UNSIGNED DEFAULT NULL,
  `receiver` int UNSIGNED DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `message_type` enum('TEXT','IMAGE','VIDEO','CLEARED') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `group_code` varchar(10) DEFAULT NULL,
  `group_type` varchar(100) NOT NULL DEFAULT 'SINGLE',
  `is_delivered` tinyint(1) DEFAULT '0',
  `is_seen` tinyint(1) DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_by` int NOT NULL DEFAULT '0',
  `blocked_user` int NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cleared_broadcast_notifications`
--

CREATE TABLE `cleared_broadcast_notifications` (
  `id` int NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `notification_id` int NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delete_requests`
--

CREATE TABLE `delete_requests` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `reason` longtext NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `title`, `subject`, `slug`, `content`, `keywords`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Verification Email', 'Verify Email OTP', 'email-verification', '<div><b>Dear User,</b></div><div>Welcome to {{APP_NAME}}</div><div>We appreciate your interest in {{APP_NAME}} and welcome you to our community. To ensure the security of your account, we require you to complete the OTP (One-Time Password) verification process</div><div>OTP : {{OTP}}</div><div>Please use this OTP to verify your account.</div><div>If you have any questions or need assistance, don\'t hesitate to contact our support team at {{APP_EMAIL}}</div>', '{link},{url}', 1, '2024-03-28 08:56:03', '2024-03-28 08:56:03'),
(2, 'Reset Password', 'Reset Password Verification Code', 'reset-password', '<div><b>Dear User,</b></div><div>We appreciate your interest in {{APP_NAME}}.To ensure the security of your account, we require you to complete the OTP (One-Time Password) verification process to reset your password.</div><div>OTP : {{OTP}}</div><div>Please use this OTP to verify your account.</div><div>If you have any questions or need assistance, don\'t hesitate to contact our support team at {{APP_EMAIL}}.</div>', '{link},{url}', 1, '2024-03-28 08:56:03', '2024-03-28 08:56:03'),
(3, 'Admin Forgot Password', 'Forgot Password', 'forgot-password', '<p style=\"font-size: 14px;\"><span style=\"font-weight: bolder; font-size: 0.875rem;\">Dear Admin,</span><br></p><p style=\"font-size: 14px;\">We have received a request to reset the password for your admin account on&nbsp;<span style=\"text-align: center; font-size: 0.875rem; font-weight: initial;\">{{APP_NAME}}.&nbsp;</span><span style=\"font-weight: initial;\">If you did not request this change, please ignore this email.</span></p><p style=\"font-size: 14px;\">To reset your password, please click on the following link:</p><p style=\"font-size: 14px;\">{{URL}}</p><p style=\"font-size: 14px;\"><br></p><p style=\"font-size: 14px;\">Thank you,</p><p style=\"font-size: 14px;\">The&nbsp;<span style=\"text-align: center; font-size: 0.875rem; font-weight: initial;\">{{APP_NAME}}</span><span style=\"font-weight: initial;\">&nbsp;Team</span></p>', '{link},{url}', 1, '2024-03-28 08:58:41', '2024-07-26 06:43:18'),
(4, 'Support', 'Contact Us', 'contact-us', '<p>Hello,</p><p>This mail is from {{USER_EMAIL}} ({{USER_NAME}}).</p><p>{{MESSAGE}}</p>', '{link},{url}', 1, '2024-03-28 08:58:41', '2024-03-28 08:58:41'),
(5, 'OTP Verification', 'OTP Verification', 'otp-verification', '<div style=\"text-size-adjust: 100%;\"><span style=\"text-size-adjust: 100%; font-weight: bolder;\">Dear User</span></div><table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\" style=\"text-size-adjust: 100%; margin-top: auto; margin-bottom: auto; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; border-spacing: 0px !important; table-layout: fixed !important; margin-right: auto !important; margin-left: auto !important;\"></table><div style=\"text-size-adjust: 100%;\">Welcome to {{APP_NAME}}</div><table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\" style=\"text-size-adjust: 100%; margin-top: auto; margin-bottom: auto; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; border-spacing: 0px !important; table-layout: fixed !important; margin-right: auto !important; margin-left: auto !important;\"></table><div style=\"text-size-adjust: 100%;\">We appreciate your interest in {{APP_NAME}}&nbsp;and welcome you to our community. To ensure the security of your account, we require you to complete the OTP (One-Time Password) verification process</div><table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\" style=\"text-size-adjust: 100%; margin-top: auto; margin-bottom: auto; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; border-spacing: 0px !important; table-layout: fixed !important; margin-right: auto !important; margin-left: auto !important;\"></table><div style=\"text-size-adjust: 100%;\">OTP : {{OTP}}</div><table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\" style=\"text-size-adjust: 100%; margin-top: auto; margin-bottom: auto; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; border-spacing: 0px !important; table-layout: fixed !important; margin-right: auto !important; margin-left: auto !important;\"></table><div style=\"text-size-adjust: 100%;\">Please use this OTP to verify your account.</div><table align=\"center\" role=\"presentation\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\" style=\"text-size-adjust: 100%; margin-top: auto; margin-bottom: auto; background-image: initial; background-position: initial; background-size: initial; background-repeat: initial; background-attachment: initial; background-origin: initial; background-clip: initial; border-spacing: 0px !important; table-layout: fixed !important; margin-right: auto !important; margin-left: auto !important;\"></table><div style=\"text-size-adjust: 100%;\">If you have any questions or need assistance, don\'t hesitate to contact our support team at {{APP_EMAIL}}</div>', '{link},{url}', 1, '2024-03-28 08:59:45', '2024-03-28 08:59:45'),
(6, 'Account delete request', 'Account delete request', 'account-deletion', '<div>Hello,</div><div>Account delete request from {{USER_EMAIL}} ({{USER_NAME}}).</div><div>{{MESSAGE}}</div>', '{link},{url}', 1, '2024-03-28 08:59:45', '2024-03-28 08:59:45'),
(7, 'Account Active', 'Account Active', 'account-active', '<div>Hello,</div><div>Account delete request from {{USER_EMAIL}} ({{USER_NAME}}).</div><div>{{MESSAGE}}</div>', '{link},{url}', 1, '2024-03-28 08:59:45', '2024-03-28 08:59:45'),
(8, 'Account Inactive', 'Account Inactive', 'account-inactive', '<div>Hello,</div><div>Account delete request from {{USER_EMAIL}} ({{USER_NAME}}).</div><div>{{MESSAGE}}</div>', '{link},{url}', 1, '2024-03-28 08:59:45', '2024-03-28 08:59:45'),
(9, 'Support', 'Support Response', 'support-response', '<p>Hello {{USER_NAME}},</p><p>We appreciate your interest in {{APP_NAME}} .</p><p>{{MESSAGE}}</p>\n', '{link},{url}', 1, '2024-03-28 08:59:45', '2024-03-28 08:59:45');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `friends`
--

CREATE TABLE `friends` (
  `id` int NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL COMMENT '''sender id''',
  `friend_id` int UNSIGNED DEFAULT NULL COMMENT '''receiver_id''',
  `request_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 pending 1 accept 2 reject',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `reference_id` int DEFAULT NULL,
  `media_type` enum('Video','Image','Audio','Document') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `media_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(2, '2022_06_14_050835_create_admins_table', 1),
(3, '2022_06_14_050836_create_app_settings_table', 1),
(4, '2022_06_14_050837_create_categories_table', 1),
(5, '2022_06_14_050838_create_email_templates_table', 1),
(6, '2022_06_14_050839_create_failed_jobs_table', 1),
(7, '2022_06_14_050840_create_gift_cards_table', 1),
(8, '2022_06_14_050841_create_media_table', 1),
(9, '2022_06_14_050842_create_pages_table', 1),
(10, '2022_06_14_050843_create_password_resets_table', 1),
(11, '2022_06_14_050845_create_roles_table', 1),
(12, '2022_06_14_050846_create_settings_table', 1),
(13, '2022_06_14_050847_create_thanks_messages_table', 1),
(14, '2022_06_14_050848_create_transactions_table', 1),
(15, '2022_06_14_050849_create_user_devices_table', 1),
(16, '2022_06_14_050850_create_user_friends_table', 1),
(17, '2022_06_14_050851_create_users_table', 1),
(18, '2022_06_15_094201_add_columns_to_users', 2);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `sender_id` int UNSIGNED DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `message` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `is_seen` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=not seen, 1=seen',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` bigint UNSIGNED NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `slug`, `title`, `description`, `created_at`, `updated_at`) VALUES
(1, 'about-us', 'About Us', '', '2022-06-14 10:46:49', '2023-08-17 04:54:43'),
(2, 'terms-and-condition', 'Terms and Conditions', '', '2022-06-14 10:46:49', '2023-08-17 05:11:48'),
(3, 'privacy-policy', 'Privacy Policy', '', '2022-06-14 10:47:52', '2023-08-17 05:00:29');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('appsketiersdev@gmail.com', 'lbStChFWcJ48U5QmP24qSP9z2BkCeIHAwrH6NoepCNbeomHMTWMoZasrEOJW', '2024-07-26 05:04:36');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_users`
--

CREATE TABLE `report_users` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `reporter_id` int NOT NULL,
  `group_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seen_broadcast_notifications`
--

CREATE TABLE `seen_broadcast_notifications` (
  `id` int NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `notification_id` int NOT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `field_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_type` enum('text','image','number','url','date','email') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `field_title`, `field_name`, `field_type`, `value`) VALUES
(1, 'Site Title', 'site_title', 'text', 'M2 module'),
(2, 'Site Logo', 'site_logo', 'image', 'public/uploads/settings/logo.jpg'),
(3, 'Site Favicon', 'site_favicon', 'image', 'public/uploads/settings/1602735060.png'),
(4, 'Website Copyright', 'website_copyright', 'text', '© 2023 M2 Module. All rights reserved.'),
(5, 'Site Email', 'site_email', 'email', 'no-reply@module.app'),
(7, 'Auth Token', 'auth_token', 'text', 'B5BZP2PaMprsVnMgTmrQKU3Txp9iur'),
(12, 'Welcome Text 1', 'welcome_text1', 'text', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen books'),
(13, 'Welcome Text 2', 'welcome_text2', 'text', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen books'),
(14, 'Welcome Text 3', 'welcome_text3', 'text', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen books'),
(15, 'SMTP Bypass', 'smtp_bypass', 'number', '0');

-- --------------------------------------------------------

--
-- Table structure for table `subscription_payments`
--

CREATE TABLE `subscription_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `plan_id` tinyint NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `platform` enum('IOS','ANDROID') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IOS',
  `receipt` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `start_date` date DEFAULT NULL,
  `end_date` date NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '0=Expired, 1 = Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscription_plans`
--

CREATE TABLE `subscription_plans` (
  `id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(20,2) NOT NULL,
  `slug` varchar(105) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_limit` int NOT NULL,
  `duration` enum('day','week','month','year') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1' COMMENT '0= Inactive, 1= Active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `supports`
--

CREATE TABLE `supports` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `response` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `user_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country_code_name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mobile_number` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mobile number with country code',
  `dob` date DEFAULT NULL,
  `profile_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(666) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0= Inacive, 1= Active',
  `location_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0= Inactive, 1= Active',
  `notification_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0= Inactive, 1= Active',
  `latitude` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_verify` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0= Not verify,1= Verify',
  `verified_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `is_two_factor` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0= Off, 1= On',
  `online` tinyint NOT NULL DEFAULT '0',
  `plan_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_premium` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0' COMMENT '0 = free, 1 = paid	',
  `is_free_plan` tinyint NOT NULL DEFAULT '1' COMMENT '1 => Free plan active, 0 => Free plan expire',
  `is_subscription_expired` tinyint NOT NULL DEFAULT '0' COMMENT '0=not expired,1=expired',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `user_name`, `email`, `country_code_name`, `country_code`, `mobile`, `mobile_number`, `dob`, `profile_picture`, `address`, `bio`, `password`, `remember_token`, `status`, `location_status`, `notification_status`, `latitude`, `longitude`, `otp_verify`, `verified_at`, `email_verified_at`, `is_two_factor`, `online`, `plan_id`, `is_premium`, `is_free_plan`, `is_subscription_expired`, `created_at`, `updated_at`) VALUES
(1, 'mike', 'mike@yopmail.com', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '$2y$10$OkWcpwnflCQuHXhDSJCn0eZMVjBoXwzFGFIfFPNVmX2Tw1xo/7V7a', NULL, 1, 1, 1, NULL, NULL, 0, NULL, NULL, 0, 0, NULL, '0', 1, 0, '2024-07-29 12:32:38', '2024-11-28 04:13:29'),
(2, 'john', 'john@yopmail.com', NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '$2y$10$OkWcpwnflCQuHXhDSJCn0eZMVjBoXwzFGFIfFPNVmX2Tw1xo/7V7a', NULL, 1, 1, 1, NULL, NULL, 0, NULL, NULL, 0, 0, NULL, '0', 1, 0, '2024-07-29 12:32:38', '2024-08-02 02:51:36');

-- --------------------------------------------------------

--
-- Table structure for table `user_devices`
--

CREATE TABLE `user_devices` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `device_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_uniqueid` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isdont_askon` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_devices`
--

INSERT INTO `user_devices` (`id`, `user_id`, `device_type`, `device_token`, `device_uniqueid`, `isdont_askon`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, '4f56881b4fe3484e', 0, '2024-08-14 07:18:17', '2024-08-14 07:18:17');

-- --------------------------------------------------------

--
-- Table structure for table `user_otps`
--

CREATE TABLE `user_otps` (
  `id` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(50) NOT NULL,
  `opt_expiry` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL,
  `updated_at` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_otps`
--

INSERT INTO `user_otps` (`id`, `email`, `otp`, `opt_expiry`, `created_at`, `updated_at`) VALUES
(1, 'mike@yopmail.com', '555885', '2024-07-29 05:10:10', '2024-07-26 03:55:29', '2024-07-29 05:05:10'),
(2, 'john@yopmail.com', '763339', '2024-07-26 08:01:16', '2024-07-26 07:56:16', '2024-07-26 07:56:16');

-- --------------------------------------------------------

--
-- Table structure for table `version_control_settings`
--

CREATE TABLE `version_control_settings` (
  `id` int NOT NULL,
  `field_title` varchar(255) NOT NULL,
  `field_name` varchar(255) NOT NULL,
  `field_type` enum('text','image','number','url','date','email','checkbox') NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `version_control_settings`
--

INSERT INTO `version_control_settings` (`id`, `field_title`, `field_name`, `field_type`, `value`) VALUES
(1, 'Android force update', 'android_force_update', 'checkbox', '0'),
(2, 'ios force update', 'ios_force_update', 'checkbox', '0'),
(3, 'Android version', 'android_version', 'text', '0.1'),
(4, 'ios version', 'ios_version', 'text', '0.1'),
(5, 'Android maintenance', 'android_maintenance', 'checkbox', '0'),
(9, 'Android App Link', 'android_app_link', 'text', 'https://m2-module.app/'),
(10, 'Android message', 'android_message', 'text', 'Android Setting'),
(14, 'ios App Link', 'ios_app_link', 'text', 'https://m2-module.app/'),
(15, 'ios message', 'ios_message', 'text', 'ios Setting'),
(16, 'ios maintenance', 'ios_maintenance', 'checkbox', '0');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `block_users`
--
ALTER TABLE `block_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dlt_sender` (`sender`),
  ADD KEY `dlt_receiver` (`receiver`);

--
-- Indexes for table `cleared_broadcast_notifications`
--
ALTER TABLE `cleared_broadcast_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delete_requests`
--
ALTER TABLE `delete_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `friends`
--
ALTER TABLE `friends`
  ADD PRIMARY KEY (`id`),
  ADD KEY `For_Dlt_follower` (`user_id`),
  ADD KEY `For_Dlt_creator` (`friend_id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `report_users`
--
ALTER TABLE `report_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seen_broadcast_notifications`
--
ALTER TABLE `seen_broadcast_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscription_payments`
--
ALTER TABLE `subscription_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delete_subscription_payments` (`user_id`);

--
-- Indexes for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supports`
--
ALTER TABLE `supports`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_devices`
--
ALTER TABLE `user_devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_devices_user_id_index` (`user_id`);

--
-- Indexes for table `user_otps`
--
ALTER TABLE `user_otps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `version_control_settings`
--
ALTER TABLE `version_control_settings`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `block_users`
--
ALTER TABLE `block_users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cleared_broadcast_notifications`
--
ALTER TABLE `cleared_broadcast_notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delete_requests`
--
ALTER TABLE `delete_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `friends`
--
ALTER TABLE `friends`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report_users`
--
ALTER TABLE `report_users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seen_broadcast_notifications`
--
ALTER TABLE `seen_broadcast_notifications`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `subscription_payments`
--
ALTER TABLE `subscription_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscription_plans`
--
ALTER TABLE `subscription_plans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `supports`
--
ALTER TABLE `supports`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_devices`
--
ALTER TABLE `user_devices`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_otps`
--
ALTER TABLE `user_otps`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `version_control_settings`
--
ALTER TABLE `version_control_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `subscription_payments`
--
ALTER TABLE `subscription_payments`
  ADD CONSTRAINT `delete_subscription_payments` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;

--
-- Constraints for table `user_devices`
--
ALTER TABLE `user_devices`
  ADD CONSTRAINT `delete_user_devices` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
