-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2026 at 06:03 AM
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
-- Database: `crm_api`
--

-- --------------------------------------------------------

--
-- Table structure for table `designations`
--

CREATE TABLE `designations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `designations`
--

INSERT INTO `designations` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Manager', NULL, NULL),
(2, 'Sales Executive', NULL, NULL),
(3, 'Marketing', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interactions`
--

CREATE TABLE `interactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `interaction_type_id` bigint(20) UNSIGNED NOT NULL,
  `interaction_date` datetime NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `interactions`
--

INSERT INTO `interactions` (`id`, `lead_id`, `user_id`, `interaction_type_id`, `interaction_date`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 1, '2026-04-01 10:00:00', 'Initial call with Amit. Interested in product demo.', '2026-04-26 07:34:33', '2026-04-26 07:34:33'),
(2, 1, 2, 2, '2026-04-03 11:30:00', 'Sent product brochure via email.', '2026-04-26 07:34:33', '2026-04-26 07:34:33'),
(3, 2, 2, 3, '2026-04-05 14:00:00', 'Meeting at Kapoor Solutions office. Discussed pricing.', '2026-04-26 07:34:33', '2026-04-26 07:34:33'),
(4, 3, 3, 1, '2026-04-06 09:00:00', 'Follow-up call with Ravi. Ready to move forward.', '2026-04-26 07:34:33', '2026-04-26 07:34:33'),
(5, 4, 3, 4, '2026-04-08 16:00:00', 'Site visit to Singh & Co. Deal confirmed.', '2026-04-26 07:34:33', '2026-04-26 07:34:33'),
(7, 6, 3, 1, '2026-04-10 11:00:00', 'Called Anita. Scheduled a demo next week.', '2026-04-26 07:34:33', '2026-04-26 07:34:33'),
(8, 7, 2, 3, '2026-04-11 15:00:00', 'Online meeting with Suresh. Needs more time.', '2026-04-26 07:34:33', '2026-04-26 07:34:33'),
(9, 8, 3, 2, '2026-04-12 09:30:00', 'Emailed Kavita with updated pricing sheet.', '2026-04-26 07:34:33', '2026-04-26 07:34:33'),
(10, 3, 3, 3, '2026-04-14 13:00:00', 'Second meeting with Ravi. Contract to be signed.', '2026-04-26 07:34:33', '2026-04-26 07:52:12'),
(14, 6, 3, 1, '2026-05-02 20:31:00', 'Called again for a meeting at office', '2026-05-04 15:02:53', '2026-05-04 15:02:53');

-- --------------------------------------------------------

--
-- Table structure for table `interaction_types`
--

CREATE TABLE `interaction_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `interaction_types`
--

INSERT INTO `interaction_types` (`id`, `name`) VALUES
(1, 'call'),
(2, 'email'),
(3, 'meeting'),
(4, 'visit');

-- --------------------------------------------------------

--
-- Table structure for table `leads`
--

CREATE TABLE `leads` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `source_id` bigint(20) UNSIGNED NOT NULL,
  `status_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_to` bigint(20) UNSIGNED NOT NULL,
  `score` int(11) NOT NULL DEFAULT 0,
  `ai_analysis` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leads`
--

INSERT INTO `leads` (`id`, `name`, `email`, `phone`, `company`, `source_id`, `status_id`, `assigned_to`, `score`, `ai_analysis`, `created_at`, `updated_at`) VALUES
(1, 'Amit Verma', 'amit.verma@example.com', '9876543210', 'Verma Enterprises', 1, 1, 2, 55, 'Initial engagement from a likely decision-maker shows promise but requires further qualification to determine intent.', '2026-04-26 07:34:33', '2026-05-04 14:53:07'),
(2, 'Sneha Kapoor', 'sneha.kapoor@example.com', '9123456780', 'Kapoor Solutions', 2, 1, 2, 35, 'The lead is in the very early discovery stage with only one initial interaction recorded.', '2026-04-26 07:34:33', '2026-05-04 14:53:21'),
(3, 'Ravi Mehta', 'ravi.mehta@example.com', '9988776655', 'Mehta Corp', 4, 2, 3, 65, 'Repeat interaction suggests genuine interest from a corporate entity, though further discovery is needed.', '2026-04-26 07:34:33', '2026-05-04 14:53:47'),
(4, 'Pooja Singh', 'pooja.singh@example.com', '9871234560', 'Singh & Co', 3, 1, 3, 35, 'Low engagement with only one interaction suggests an early-stage prospect that requires significant further qualification.', '2026-04-26 07:34:33', '2026-05-04 14:54:26'),
(6, 'Anita Rao', 'anita.rao@example.com', '9654321098', 'Rao Technologies', 1, 1, 3, 75, 'High-authority lead with multiple touchpoints indicating steady interest and potential decision-making power.', '2026-04-26 07:34:33', '2026-05-04 15:03:11'),
(7, 'Suresh Nair', 'suresh.nair@example.com', '9543210987', 'Nair Logistics', 6, 1, 2, 50, 'High-fit profile as a potential decision-maker in the logistics sector, though engagement is in the very early stages.', '2026-04-26 07:34:33', '2026-05-04 21:56:01'),
(8, 'Kavita Sharma', 'kavita.sharma@example.com', '9432109876', 'Sharma Retail', 2, 3, 3, 45, 'Early-stage lead with minimal interaction history and limited company data.', '2026-04-26 07:34:33', '2026-05-04 21:55:49');

-- --------------------------------------------------------

--
-- Table structure for table `lead_statuses`
--

CREATE TABLE `lead_statuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lead_statuses`
--

INSERT INTO `lead_statuses` (`id`, `name`) VALUES
(1, 'new'),
(2, 'contacted'),
(3, 'qualified'),
(4, 'converted'),
(5, 'lost');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(2, '2019_08_19_000000_create_failed_jobs_table', 1),
(3, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(4, '2026_04_16_190243_create_roles_table', 1),
(5, '2026_04_16_190331_create_designations_table', 1),
(6, '2026_04_16_190348_create_users_table', 1),
(7, '2026_04_16_190414_create_role_user_table', 1),
(8, '2026_04_16_190537_create_sources_table', 1),
(9, '2026_04_16_190629_create_lead_statuses_table', 1),
(10, '2026_04_16_190653_create_leads_table', 1),
(11, '2026_04_16_190721_create_interaction_types_table', 1),
(12, '2026_04_16_190739_create_interactions_table', 1),
(13, '2026_04_16_190809_create_task_statuses_table', 1),
(14, '2026_04_16_190827_create_tasks_table', 1),
(15, '2026_04_16_194518_create_permissions_table', 1),
(16, '2026_04_16_194519_create_permission_user_table', 1),
(17, '2026_05_02_055903_add_ai_analysis_to_leads_table', 2),
(18, '2026_05_02_072757_create_otps_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `otps`
--

CREATE TABLE `otps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp` varchar(255) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'full_access', NULL, NULL),
(2, 'read', NULL, NULL),
(3, 'write', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permission_user`
--

CREATE TABLE `permission_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_user`
--

INSERT INTO `permission_user` (`id`, `user_id`, `permission_id`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 1),
(5, 5, 1);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'admin', NULL, NULL),
(2, 'user', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL),
(2, 2, 2, NULL, NULL),
(3, 3, 2, NULL, NULL),
(5, 5, 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sources`
--

CREATE TABLE `sources` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sources`
--

INSERT INTO `sources` (`id`, `name`) VALUES
(1, 'website'),
(2, 'facebook'),
(3, 'instagram'),
(4, 'referral'),
(5, 'ads'),
(6, 'other');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `lead_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `status_id` bigint(20) UNSIGNED NOT NULL,
  `priority` enum('low','medium','high') NOT NULL DEFAULT 'medium',
  `due_date` date DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `title`, `lead_id`, `user_id`, `status_id`, `priority`, `due_date`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Send demo to Amit Verma', 1, 2, 2, 'high', '2026-04-22', 'Prepare and send product demo link to Amit.', '2026-04-26 07:34:33', '2026-04-30 02:26:23'),
(2, 'Follow up with Sneha Kapoor', 2, 2, 2, 'medium', '2026-04-20', 'Call Sneha to discuss pricing concerns.', '2026-04-26 07:34:33', '2026-04-26 07:34:33'),
(3, 'Prepare contract for Ravi Mehta', 3, 3, 2, 'high', '2026-04-21', 'Draft and send contract to Ravi for review.', '2026-04-26 07:34:33', '2026-04-26 07:34:33'),
(6, 'Schedule demo for Anita Rao', 6, 3, 3, 'medium', '2026-04-23', 'Book a product demo session with Anita.', '2026-04-26 07:34:33', '2026-05-04 21:57:43'),
(7, 'Send proposal to Suresh Nair', 7, 2, 2, 'medium', '2026-04-24', 'Prepare and send detailed proposal.', '2026-04-26 07:34:33', '2026-04-26 07:34:33'),
(9, 'Update CRM notes for Amit', 1, 1, 3, 'low', '2026-04-15', 'Update all interaction notes in CRM.', '2026-04-26 07:34:33', '2026-04-26 07:34:33'),
(10, 'Weekly report for all leads', 2, 1, 3, 'medium', '2026-04-18', 'Compile weekly lead status report.', '2026-04-26 07:34:33', '2026-04-26 07:34:33');

-- --------------------------------------------------------

--
-- Table structure for table `task_statuses`
--

CREATE TABLE `task_statuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `task_statuses`
--

INSERT INTO `task_statuses` (`id`, `name`) VALUES
(1, 'pending'),
(2, 'in_progress'),
(3, 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `designation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `username`, `password`, `gender`, `image`, `status`, `designation_id`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 'Admin User', 'admin@example.com', 'admin', '$2y$10$W5NMvrJKNjm.nVoyN9AQKu.eTJfKR39b1z80Ni5Lu8j1UoZ2X79xi', 'male', 'avatar-01.jpg', 'active', 1, 'System Admin', '2026-04-26 07:34:32', '2026-04-26 07:34:32'),
(2, 'Kunal Majumdar', 'kunalmajumdar71@gmail.com', 'kunal123', '$2y$10$bAEcNnRFjo1rJGgi9CY3ZOwO2XDDyrBCHNrK9.yQfOXcBTLdRs5N.', 'male', 'uploads/users/m1MJmnz5o1cubfxjRwsHjzwU4m7qxZTlth0pnWIK.jpg', 'active', 2, 'User', '2026-04-26 07:34:32', '2026-04-26 07:35:44'),
(3, 'Priya Das', 'priya@example.com', 'priya123', '$2y$10$y.QLyyFoWvVy77MLTcf/dOoImpGSQtxlaCqOv3SLpuazTrPhK95Lq', 'female', 'uploads/users/Wr5U9uRo6Xob7tdkIgxDxI4nn4F2tMFHErblthDj.jpg', 'active', 2, 'User', '2026-04-26 07:34:33', '2026-04-26 07:35:30'),
(5, 'Test User', 'test@example.com', 'Test User 1', '$2y$10$2O75Bo2Ds6mqGfXhrb9qXOPzjpnHDXkokHim/72FdwmAD5Ej/K04e', 'male', 'uploads/users/1AfEnrhAWOhJhLKE8bFWrMclX86ZZ4GU8Nua6xCr.jpg', 'active', 1, NULL, '2026-05-04 22:20:24', '2026-05-04 22:20:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `designations`
--
ALTER TABLE `designations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `interactions`
--
ALTER TABLE `interactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `interactions_lead_id_foreign` (`lead_id`),
  ADD KEY `interactions_user_id_foreign` (`user_id`),
  ADD KEY `interactions_interaction_type_id_foreign` (`interaction_type_id`);

--
-- Indexes for table `interaction_types`
--
ALTER TABLE `interaction_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leads_source_id_foreign` (`source_id`),
  ADD KEY `leads_status_id_foreign` (`status_id`),
  ADD KEY `leads_assigned_to_foreign` (`assigned_to`);

--
-- Indexes for table `lead_statuses`
--
ALTER TABLE `lead_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otps`
--
ALTER TABLE `otps`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permission_user`
--
ALTER TABLE `permission_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permission_user_user_id_foreign` (`user_id`),
  ADD KEY `permission_user_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_user_user_id_role_id_unique` (`user_id`,`role_id`),
  ADD KEY `role_user_role_id_foreign` (`role_id`);

--
-- Indexes for table `sources`
--
ALTER TABLE `sources`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_lead_id_foreign` (`lead_id`),
  ADD KEY `tasks_user_id_foreign` (`user_id`),
  ADD KEY `tasks_status_id_foreign` (`status_id`);

--
-- Indexes for table `task_statuses`
--
ALTER TABLE `task_statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD KEY `users_designation_id_foreign` (`designation_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `designations`
--
ALTER TABLE `designations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interactions`
--
ALTER TABLE `interactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `interaction_types`
--
ALTER TABLE `interaction_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `leads`
--
ALTER TABLE `leads`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `lead_statuses`
--
ALTER TABLE `lead_statuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `otps`
--
ALTER TABLE `otps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `permission_user`
--
ALTER TABLE `permission_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `role_user`
--
ALTER TABLE `role_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `sources`
--
ALTER TABLE `sources`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `task_statuses`
--
ALTER TABLE `task_statuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `interactions`
--
ALTER TABLE `interactions`
  ADD CONSTRAINT `interactions_interaction_type_id_foreign` FOREIGN KEY (`interaction_type_id`) REFERENCES `interaction_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `interactions_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `interactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_assigned_to_foreign` FOREIGN KEY (`assigned_to`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leads_source_id_foreign` FOREIGN KEY (`source_id`) REFERENCES `sources` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leads_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `lead_statuses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permission_user`
--
ALTER TABLE `permission_user`
  ADD CONSTRAINT `permission_user_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permission_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_lead_id_foreign` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_status_id_foreign` FOREIGN KEY (`status_id`) REFERENCES `task_statuses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tasks_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_designation_id_foreign` FOREIGN KEY (`designation_id`) REFERENCES `designations` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
