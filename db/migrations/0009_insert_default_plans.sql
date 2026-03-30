INSERT IGNORE INTO plans (name, slug, storage_limit_bytes, bandwidth_limit_bytes, price, currency, sort_order, description) VALUES
('Free', 'free', 1073741824, 21474836480, 0.00, 'MZN', 1, 'Perfeito para começar. 1GB de armazenamento e 20GB de banda mensal.'),
('Plus', 'plus', 5368709120, 53687091200, 100.00, 'MZN', 2, 'Ideal para pequenos projetos. 5GB de armazenamento e 50GB de banda mensal.'),
('Pro', 'pro', 53687091200, 214748364800, 500.00, 'MZN', 3, 'Para profissionais. 50GB de armazenamento e 200GB de banda mensal.'),
('Business', 'business', 0, 0, 0.00, 'MZN', 4, 'Sob consulta. Entre em contato para um plano personalizado.');