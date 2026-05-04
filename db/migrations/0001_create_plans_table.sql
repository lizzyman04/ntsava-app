CREATE TABLE IF NOT EXISTS plans (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT NULL,
    storage_limit_bytes BIGINT UNSIGNED NOT NULL,
    bandwidth_limit_bytes BIGINT UNSIGNED NOT NULL,
    max_file_size_bytes BIGINT UNSIGNED NOT NULL DEFAULT 10485760,
    allowed_mime_types TEXT NOT NULL DEFAULT '["jpg","jpeg","png","gif","webp"]',
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