-- Migration: Add is_approved column to users table and super_admin role
-- Date: 2025-11-21

-- Add is_approved column if it doesn't exist
ALTER TABLE `users` 
ADD COLUMN `is_approved` tinyint(1) NOT NULL DEFAULT 1 
AFTER `is_active`;

-- Add super_admin role if it doesn't exist
INSERT IGNORE INTO `roles` (`id`, `code`) VALUES (4, 'super_admin');

-- Create SuperAdmin user (password hash for "superadmin123")
INSERT INTO `users` (`id`, `email`, `password_hash`, `name`, `is_active`, `is_approved`, `created_at`)
VALUES (
    8,
    'superadmin@local.test',
    '$2y$10$K9Uw6xGZHqX5V8mN2pQ7feL0R3S4T5U6V7W8X9Y0Z1A2B3C4D5E6',
    'SuperAdmin',
    1,
    1,
    NOW()
) ON DUPLICATE KEY UPDATE `email`=`email`;

-- Assign super_admin role to SuperAdmin user
INSERT IGNORE INTO `user_role` (`user_id`, `role_id`)
SELECT 8, `id` FROM `roles` WHERE `code` = 'super_admin';
