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