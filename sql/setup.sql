-- Autor: Daniel Benjamin Perez Morales
-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13
-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13
-- Correo electrónico: danielperezdev@proton.me

\c postgres

-- Delete
DROP DATABASE IF EXISTS local_drive;

-- Delete Function
DROP FUNCTION IF EXISTS function_insert_data_files;
DROP FUNCTION IF EXISTS function_update_data_files;
DROP FUNCTION IF EXISTS function_delete_data_files;
DROP FUNCTION IF EXISTS function_log_email_update;
DROP FUNCTION IF EXISTS function_register_deleted_user;
DROP FUNCTION IF EXISTS function_delete_data_user;
DROP FUNCTION IF EXISTS function_insert_data_notification;
DROP FUNCTION IF EXISTS function_insert_data_shared_directory;
DROP FUNCTION IF EXISTS function_insert_data_shared_file;
DROP FUNCTION IF EXISTS function_delete_directory;
-- Delete Index
DROP INDEX IF EXISTS index_id_user;

-- Delete Procedures
DROP PROCEDURE IF EXISTS stored_procedure_insert_data_files;
DROP PROCEDURE IF EXISTS stored_procedure_update_data_files;
DROP PROCEDURE IF EXISTS stored_procedure_delete_data_storage;
DROP PROCEDURE IF EXISTS stored_procedure_register_deleted_user;
DROP PROCEDURE IF EXISTS stored_procedure_delete_data_user;
DROP PROCEDURE IF EXISTS stored_procedure_insert_data_notification;
DROP PROCEDURE IF EXISTS stored_procedure_insert_data_shared_directory;
DROP PROCEDURE IF EXISTS stored_procedure_insert_data_shared_file;
DROP PROCEDURE IF EXISTS stored_procedure_delete_directory_entry;
-- Delete Trigger
DROP TRIGGER IF EXISTS trigger_user_email_history ON users;
DROP TRIGGER IF EXISTS trigger_deleted_user_history ON users;

-- Delete View
DROP VIEW iF EXISTS view_user_file_sizes;

-- Database
CREATE DATABASE local_drive;

-- Connect
\c local_drive

\i /App/sql/tables.sql
\i /App/sql/indexs.sql
\i /App/sql/functions.sql
\i /App/sql/stored_procedures.sql
\i /App/sql/triggers.sql
\i /App/sql/views.sql

-- Bloque anónimo DO
--     DO $AnonymousBlock$
--     DECLARE
--         output_message TEXT := '';
--     BEGIN
--         CALL stored_procedure_insert_data_files(
--             id_user_param::BIGINT,
--             name_param::TEXT,
--             type_param::TEXT,
--             path_param::TEXT,
--             size_param::BIGINT,
--             file_creation_date_param::TIMESTAMPTZ,
--             output_message::TEXT,
--         );
--         RAISE WARNING 'Message: %', output_message;
--     END;
--     $AnonymousBlock$;

-- Alterar Columnas
-- ALTER TABLE files ALTER COLUMN modification_date TYPE TIMESTAMP;
-- ALTER TABLE files RENAME COLUMN modification_date TO file_creation_date;
-- ALTER TABLE deleted_user_history ADD COLUMN date_deleted TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP;
