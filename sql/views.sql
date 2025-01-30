CREATE OR REPLACE VIEW view_user_file_sizes AS
SELECT 
    u.email, 
    MAX(f.size) AS max_size, 
    MIN(f.size) AS min_size
FROM users u
INNER JOIN files f ON f.id_user = u.id
GROUP BY u.email;

-- Example Usages
-- SELECT * FROM view_user_file_sizes;