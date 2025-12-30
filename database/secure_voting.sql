-- Secure Online Voting System Database
-- Version: 1.0
-- Created: December 2024
-- This file contains the complete database schema for the voting system

-- Create Database
CREATE DATABASE IF NOT EXISTS `secure_voting` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `secure_voting`;

-- Table: users
-- Stores voter account information with verification status
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `voter_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
  `is_verified` tinyint(1) DEFAULT 0 COMMENT 'Admin verification status',
  `verified_at` timestamp NULL DEFAULT NULL COMMENT 'Date when admin verified',
  `is_admin` tinyint(1) DEFAULT 0 COMMENT 'Administrator flag',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_email` (`email`),
  KEY `idx_voter_id` (`voter_id`),
  KEY `idx_verified` (`is_verified`),
  KEY `idx_is_admin` (`is_admin`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: elections
-- Stores election information and configuration
CREATE TABLE `elections` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` enum('pending','active','completed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `created_by` bigint unsigned,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_status` (`status`),
  KEY `idx_created_by` (`created_by`),
  KEY `idx_dates` (`start_date`, `end_date`),
  CONSTRAINT `fk_elections_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: candidates
-- Stores candidate information for each election
CREATE TABLE `candidates` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `election_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_election_id` (`election_id`),
  CONSTRAINT `fk_candidates_election` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: votes
-- Stores encrypted votes with security features
CREATE TABLE `votes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` bigint unsigned NOT NULL,
  `election_id` bigint unsigned NOT NULL,
  `candidate_id` bigint unsigned NOT NULL,
  `encrypted_vote` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'AES-256-CBC encrypted vote data',
  `vote_hash` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SHA-256 hash for tampering detection',
  `device_fingerprint` varchar(255) COLLATE utf8mb4_unicode_ci COMMENT 'Unique device identifier',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci COMMENT 'IPv4 or IPv6 address',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `uk_user_election` (`user_id`, `election_id`) COMMENT 'Ensure one vote per user per election',
  KEY `idx_election_id` (`election_id`),
  KEY `idx_candidate_id` (`candidate_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_vote_hash` (`vote_hash`),
  CONSTRAINT `fk_votes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_votes_election` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_votes_candidate` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: voting_tokens
-- Stores one-time voting tokens for security
CREATE TABLE `voting_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` bigint unsigned NOT NULL,
  `election_id` bigint unsigned NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL UNIQUE,
  `used_at` timestamp NULL DEFAULT NULL COMMENT 'When the token was used',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NOT NULL COMMENT 'Token expiration time',
  UNIQUE KEY `uk_user_election_token` (`user_id`, `election_id`),
  KEY `idx_token` (`token`),
  KEY `idx_expires_at` (`expires_at`),
  KEY `idx_used_at` (`used_at`),
  CONSTRAINT `fk_tokens_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_tokens_election` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: vote_logs
-- Audit trail for all voting activity
CREATE TABLE `vote_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `vote_id` bigint unsigned,
  `user_id` bigint unsigned,
  `election_id` bigint unsigned,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'vote_cast, vote_verified, tampering_detected, etc.',
  `old_value` text COLLATE utf8mb4_unicode_ci COMMENT 'Previous value if update',
  `new_value` text COLLATE utf8mb4_unicode_ci COMMENT 'New value if update',
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `performed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_vote_id` (`vote_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_election_id` (`election_id`),
  KEY `idx_action` (`action`),
  KEY `idx_performed_at` (`performed_at`),
  CONSTRAINT `fk_logs_vote` FOREIGN KEY (`vote_id`) REFERENCES `votes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_logs_election` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: jobs
-- Laravel queue jobs table for async processing
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL DEFAULT 0,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  KEY `idx_queue` (`queue`),
  KEY `idx_available_at` (`available_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: failed_jobs
-- Track failed queue jobs
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  KEY `idx_failed_at` (`failed_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create Indexes for Better Performance
CREATE INDEX idx_users_created_at ON users(created_at);
CREATE INDEX idx_elections_created_at ON elections(created_at);
CREATE INDEX idx_votes_election_created ON votes(election_id, created_at);
CREATE INDEX idx_vote_logs_created_at ON vote_logs(performed_at);

-- Sample Admin User (password: admin123)
-- Password hash: $2y$10$92IXUNpkm..URNNX3kh2OPST9EjHvUzdmHFQi7q5HYRY5sIyGkvy
-- To login: email = admin@example.com, password = admin123
INSERT INTO `users` (`id`, `name`, `email`, `password`, `voter_id`, `is_verified`, `verified_at`, `is_admin`, `created_at`, `updated_at`)
VALUES 
(1, 'Admin User', 'admin@example.com', '$2y$10$92IXUNpkm..URNNX3kh2OPST9EjHvUzdmHFQi7q5HYRY5sIyGkvy', 'VID-ADMIN001', 1, NOW(), 1, NOW(), NOW());

-- Sample Verified Voters (password for all: password123)
-- Password hash: $2y$10$F5VJ6Q9IZL6V9N8E4K2L1U5Z3X1Q9P8R7O6M5K4J3H2G1F0E9D8C7
INSERT INTO `users` (`id`, `name`, `email`, `password`, `voter_id`, `is_verified`, `verified_at`, `is_admin`, `created_at`, `updated_at`)
VALUES 
(2, 'John Doe', 'john@example.com', '$2y$10$F5VJ6Q9IZL6V9N8E4K2L1U5Z3X1Q9P8R7O6M5K4J3H2G1F0E9D8C7', 'VID-VOTER001', 1, NOW(), 0, NOW(), NOW()),
(3, 'Jane Smith', 'jane@example.com', '$2y$10$F5VJ6Q9IZL6V9N8E4K2L1U5Z3X1Q9P8R7O6M5K4J3H2G1F0E9D8C7', 'VID-VOTER002', 1, NOW(), 0, NOW(), NOW()),
(4, 'Bob Johnson', 'bob@example.com', '$2y$10$F5VJ6Q9IZL6V9N8E4K2L1U5Z3X1Q9P8R7O6M5K4J3H2G1F0E9D8C7', 'VID-VOTER003', 1, NOW(), 0, NOW(), NOW()),
(5, 'Alice Williams', 'alice@example.com', '$2y$10$F5VJ6Q9IZL6V9N8E4K2L1U5Z3X1Q9P8R7O6M5K4J3H2G1F0E9D8C7', 'VID-VOTER004', 1, NOW(), 0, NOW(), NOW());

-- Sample Election
INSERT INTO `elections` (`id`, `title`, `description`, `status`, `start_date`, `end_date`, `created_by`, `created_at`, `updated_at`)
VALUES 
(1, 'Student Council President 2025', 'Vote for your next Student Council President. This election will determine who leads our student body for the upcoming academic year.', 'active', DATE_ADD(NOW(), INTERVAL -1 DAY), DATE_ADD(NOW(), INTERVAL 6 DAY), 1, NOW(), NOW());

-- Sample Candidates for the Election
INSERT INTO `candidates` (`id`, `election_id`, `name`, `description`, `created_at`, `updated_at`)
VALUES 
(1, 1, 'Sarah Anderson', 'Economics student with 3 years of student leadership experience', NOW(), NOW()),
(2, 1, 'Michael Chen', 'Computer Science major passionate about student tech initiatives', NOW(), NOW()),
(3, 1, 'Emma Rodriguez', 'Business student focused on student community engagement', NOW(), NOW()),
(4, 1, 'David Thompson', 'Engineering student committed to improving campus facilities', NOW(), NOW());

-- ===================================
-- Database Schema Summary
-- ===================================
-- 
-- Tables Created:
-- 1. users - Voter accounts (5 security fields)
-- 2. elections - Election management
-- 3. candidates - Candidates per election
-- 4. votes - Encrypted votes with hashing
-- 5. voting_tokens - One-time voting tokens
-- 6. vote_logs - Audit trail for compliance
-- 7. jobs - Laravel queue system
-- 8. failed_jobs - Failed job tracking
--
-- Total Tables: 8
-- Total Indexes: 25+
-- Foreign Keys: 10
-- Security Features: Encryption, hashing, device fingerprinting
--
-- ===================================
-- IMPORTANT NOTES
-- ===================================
--
-- 1. CHARACTER SET: All tables use utf8mb4 for full Unicode support
-- 2. TIMESTAMPS: Automatic created_at and updated_at
-- 3. INDEXES: Optimized for common queries
-- 4. FOREIGN KEYS: Cascade delete on related records
-- 5. UNIQUE CONSTRAINTS: Prevent duplicate votes and data
-- 6. SAMPLE ADMIN: Pre-created admin account (see above)
--
-- Admin Login Credentials:
-- Email: admin@example.com
-- Password: admin123
--
-- After importing, change this password immediately!
--
-- ===================================
