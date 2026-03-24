-- ============================================
-- CDN Database Schema
-- Author: lizzyman04
-- Date: 2026-03-24
-- ============================================

-- --------------------------------------------------
-- 1. Plans table
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS plans (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT NULL,
    storage_limit_bytes BIGINT UNSIGNED NOT NULL,
    bandwidth_limit_bytes BIGINT UNSIGNED NOT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    currency VARCHAR(3) NOT NULL DEFAULT 'MZN',
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_is_active (is_active),
    INDEX idx_currency (currency),
    INDEX idx_slug (slug)
);

-- --------------------------------------------------
-- 2. Users table
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    plan_id INT UNSIGNED NOT NULL,
    storage_limit_bytes BIGINT UNSIGNED NOT NULL,
    storage_used_bytes BIGINT UNSIGNED NOT NULL DEFAULT 0,
    bandwidth_limit_bytes BIGINT UNSIGNED NOT NULL,
    bandwidth_used_bytes BIGINT UNSIGNED NOT NULL DEFAULT 0,
    bandwidth_reset_month INT UNSIGNED NOT NULL,
    status ENUM('active', 'suspended', 'deleted') DEFAULT 'active',
    suspended_reason TEXT NULL,
    suspended_at TIMESTAMP NULL,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_plan_id (plan_id),
    INDEX idx_status (status),
    INDEX idx_email (email),
    INDEX idx_username (username),
    INDEX idx_uuid (uuid),
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE RESTRICT
);

-- --------------------------------------------------
-- 3. User Roles table
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS user_roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    role ENUM('admin', 'user', 'moderator') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_role (user_id, role),
    INDEX idx_user_id (user_id),
    INDEX idx_role (role),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- --------------------------------------------------
-- 4. API Tokens table
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS api_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    token_hash VARCHAR(255) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    permissions JSON NOT NULL DEFAULT '["upload", "delete", "read"]',
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_expires_at (expires_at),
    INDEX idx_token_hash (token_hash),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- --------------------------------------------------
-- 5. Files table
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS files (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    uuid CHAR(36) NOT NULL UNIQUE,
    storage_path VARCHAR(500) NULL,
    original_name VARCHAR(255) NOT NULL,
    size_bytes BIGINT UNSIGNED NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    width INT UNSIGNED NULL,
    height INT UNSIGNED NULL,
    duration_seconds INT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_storage_path (storage_path(191)),
    INDEX idx_created_at (created_at),
    INDEX idx_deleted_at (deleted_at),
    INDEX idx_uuid (uuid),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- --------------------------------------------------
-- 6. Credits table
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS credits (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    currency VARCHAR(3) NOT NULL DEFAULT 'MZN',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_currency (currency),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- --------------------------------------------------
-- 7. Credit Transactions table
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS credit_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type ENUM('purchase', 'upgrade_plan', 'downgrade_plan', 'admin_add', 'admin_remove') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'MZN',
    balance_after DECIMAL(10,2) NOT NULL,
    description VARCHAR(255) NULL,
    reference_id VARCHAR(100) NULL,
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_type (type),
    INDEX idx_created_at (created_at),
    INDEX idx_currency (currency),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- --------------------------------------------------
-- 8. Notifications table
-- --------------------------------------------------
CREATE TABLE IF NOT EXISTS notifications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    type ENUM('system', 'plan_expiring', 'storage_warning', 'bandwidth_warning', 'upgrade_request', 'payment_received', 'suspended') NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    metadata JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_type (type),
    INDEX idx_created_at (created_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- --------------------------------------------------
-- 9. Insert default plans
-- --------------------------------------------------
INSERT IGNORE INTO plans (name, slug, storage_limit_bytes, bandwidth_limit_bytes, price, currency, sort_order, description) VALUES
('Free', 'free', 1073741824, 21474836480, 0.00, 'MZN', 1, 'Perfeito para começar. 1GB de armazenamento e 20GB de banda mensal.'),
('Plus', 'plus', 5368709120, 53687091200, 100.00, 'MZN', 2, 'Ideal para pequenos projetos. 5GB de armazenamento e 50GB de banda mensal.'),
('Pro', 'pro', 53687091200, 214748364800, 499.00, 'MZN', 3, 'Para profissionais. 50GB de armazenamento e 200GB de banda mensal.'),
('Business', 'business', 0, 0, 0.00, 'MZN', 4, 'Sob consulta. Entre em contato para um plano personalizado.');

-- --------------------------------------------------
-- 10. Insert default admin user
-- Nota: A senha deve ser alterada após primeiro login
-- --------------------------------------------------
-- INSERT IGNORE INTO users (uuid, username, email, password_hash, plan_id, storage_limit_bytes, bandwidth_limit_bytes, bandwidth_reset_month, status)
-- VALUES (
--     UUID(),
--     'admin',
--     'admin@cdn.tudocomlizzyman.com',
--     '$2y$10$YourHashedPasswordHere', -- Use password_hash('admin123', PASSWORD_DEFAULT)
--     1,
--     1073741824,
--     21474836480,
--     DATE_FORMAT(NOW(), '%Y%m'),
--     'active'
-- );

-- INSERT IGNORE INTO user_roles (user_id, role)
-- SELECT id, 'admin' FROM users WHERE username = 'admin';