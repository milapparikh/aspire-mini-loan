DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mini-aspire-loan.failed_jobs: ~0 rows (approximately)
DELETE FROM `failed_jobs`;

DROP TABLE IF EXISTS `loan`;
CREATE TABLE IF NOT EXISTS `loan` (
  `id_loan` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `remain_capital` decimal(10,2) DEFAULT 0.00,
  `term` int(11) DEFAULT 0,
  `loan_status` varchar(100) NOT NULL DEFAULT 'Pending',
  `is_approve` enum('Yes','No','None') NOT NULL DEFAULT 'None',
  `application_date` date DEFAULT NULL,
  `approve_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_loan`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table mini-aspire-loan.loan: ~2 rows (approximately)
DELETE FROM `loan`;
INSERT INTO `loan` (`id_loan`, `id_user`, `amount`, `remain_capital`, `term`, `loan_status`, `is_approve`, `application_date`, `approve_date`, `created_at`, `updated_at`) VALUES
	(1, 1, 10000.00, 6000.00, 3, 'Pending', 'Yes', '2022-09-18', '2022-10-08', '2022-09-18 09:55:32', '2022-09-18 11:41:48'),
	(2, 3, 13000.00, 13000.00, 3, 'Pending', 'None', '2022-09-18', NULL, '2022-09-18 17:43:01', '2022-09-18 17:43:01');

-- Dumping structure for table mini-aspire-loan.migrations
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELETE FROM `migrations`;
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_resets_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- Dumping structure for table mini-aspire-loan.password_resets
DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mini-aspire-loan.password_resets: ~0 rows (approximately)
DELETE FROM `password_resets`;

-- Dumping structure for table mini-aspire-loan.personal_access_tokens
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mini-aspire-loan.personal_access_tokens: ~0 rows (approximately)
DELETE FROM `personal_access_tokens`;

-- Dumping structure for table mini-aspire-loan.repayment
DROP TABLE IF EXISTS `repayment`;
CREATE TABLE IF NOT EXISTS `repayment` (
  `id_repayment` int(11) NOT NULL AUTO_INCREMENT,
  `id_loan` int(11) NOT NULL,
  `repayment_date` date DEFAULT NULL,
  `installment_amount` decimal(10,2) DEFAULT NULL,
  `paid_amount` decimal(10,2) DEFAULT NULL,
  `remain_loan_capital` decimal(10,2) DEFAULT NULL,
  `repayment_status` enum('Paid','Pending') NOT NULL DEFAULT 'Pending',
  `paid_date` date DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_repayment`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4;

-- Dumping data for table mini-aspire-loan.repayment: ~3 rows (approximately)
DELETE FROM `repayment`;
INSERT INTO `repayment` (`id_repayment`, `id_loan`, `repayment_date`, `installment_amount`, `paid_amount`, `remain_loan_capital`, `repayment_status`, `paid_date`, `created_at`, `updated_at`) VALUES
	(1, 1, '2022-09-25', 3333.33, 4000.00, 10000.00, 'Paid', '2022-09-18', '2022-09-18 17:11:48', '2022-09-18 17:11:48'),
	(2, 1, '2022-10-02', 3333.33, NULL, 6666.67, 'Pending', NULL, '2022-09-18 17:11:48', '2022-09-18 17:11:48'),
	(3, 1, '2022-10-09', 2666.66, NULL, 2666.66, 'Pending', NULL, '2022-09-18 17:11:48', '2022-09-18 17:11:48');

-- Dumping structure for table mini-aspire-loan.users
DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_type` enum('C','A') COLLATE utf8mb4_unicode_ci DEFAULT 'C',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table mini-aspire-loan.users: ~3 rows (approximately)
DELETE FROM `users`;
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role_type`, `created_at`, `updated_at`) VALUES (1, 'milap parikh', 'milap@test.com', NULL, '$2y$10$s6MyK1NwGcn57Z5Gfp6yMODKlSsyQwOTkD7ndj5l9JnhP94IFOeVC', NULL, 'C', '2022-09-18 02:21:00', '2022-09-18 02:21:00');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role_type`, `created_at`, `updated_at`) VALUES (2, 'milap parikh', 'Admin@test.com', NULL, '$2y$10$s6MyK1NwGcn57Z5Gfp6yMODKlSsyQwOTkD7ndj5l9JnhP94IFOeVC', NULL, 'A', '2022-09-18 02:23:01', '2022-09-18 02:23:01');
INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role_type`, `created_at`, `updated_at`) VALUES (3, 'test shah', 'test2@test.com', NULL, '$2y$10$s6MyK1NwGcn57Z5Gfp6yMODKlSsyQwOTkD7ndj5l9JnhP94IFOeVC', NULL, 'C', '2022-09-18 17:42:18', '2022-09-18 17:42:18');
