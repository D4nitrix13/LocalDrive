-- Autor: Daniel Benjamin Perez Morales
-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13
-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13
-- Correo electrónico: danielperezdev@proton.me

-- Triggers
CREATE TRIGGER trigger_user_email_history
AFTER UPDATE ON users
FOR EACH ROW
EXECUTE FUNCTION function_log_email_update();

CREATE TRIGGER trigger_deleted_user_history
AFTER DELETE ON users
FOR EACH ROW
EXECUTE FUNCTION function_register_deleted_user();