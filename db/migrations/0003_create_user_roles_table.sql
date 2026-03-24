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