-- Autor: Daniel Benjamin Perez Morales
-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13
-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13
-- Correo electrónico: danielperezdev@proton.me

-- Stored Procedures
CREATE OR REPLACE PROCEDURE stored_procedure_insert_data_shared_file(
    IN id_user_property_param BIGINT,
    IN email_destinatario_param TEXT,
    IN shared_from_a_directory_param BOOLEAN,
    IN path_param TEXT,
    OUT result_message TEXT
)
LANGUAGE plpgsql
AS $BodyQueryStoredProcedure$
BEGIN
    -- Debido a la abstracción de la aplicación, el parámetro email_destinatario_param corresponde al email del usuario destinatario.
    INSERT INTO shared_file (id_user_property, id_user_guest, shared_from_a_directory, path)
    VALUES (
        id_user_property_param,
        (SELECT id FROM users WHERE email = email_destinatario_param LIMIT 1),
        shared_from_a_directory_param,
        path_param
    );

    result_message := format(
        'The file %s has been successfully shared to the User with Email %s.',
        path_param, email_destinatario_param
    );
EXCEPTION
    WHEN OTHERS THEN
        result_message := 'Error registering the file ' || path_param || ' for the user with email ' || email_destinatario_param || '. Error: ' || SQLERRM;
END;
$BodyQueryStoredProcedure$;


CREATE OR REPLACE PROCEDURE stored_procedure_insert_data_shared_directory(
    IN id_user_property_param BIGINT,
    IN email_destinatario_param TEXT,
    IN path_param TEXT,
    OUT result_message TEXT
)
LANGUAGE plpgsql
AS $BodyQueryStoredProcedure$
BEGIN
    -- Debido a la abstracción de la aplicación, el parámetro email_destinatario_param corresponde al email del usuario destinatario.
    INSERT INTO shared_directory (id_user_property, id_user_guest, path)
    VALUES (
        id_user_property_param,
        (select id from users where email = email_destinatario_param LIMIT 1),
        path_param
    );

    result_message := format(
        'The directory %s has been successfully shared to the User with Email %s.',
        path_param, email_destinatario_param
    );
EXCEPTION
    WHEN OTHERS THEN
        result_message := 'Error registering the directory ' || path_param || ' for the user with email ' || email_destinatario_param || '. Error: ' || SQLERRM;
END;
$BodyQueryStoredProcedure$;

CREATE OR REPLACE PROCEDURE stored_procedure_insert_data_notification(
    IN id_user_remitente_param BIGINT,
    IN email_param TEXT,
    IN motivo_param TEXT,
    IN message_param TEXT,
    IN visto_param BOOLEAN,
    OUT result_message TEXT
)
LANGUAGE plpgsql
AS $BodyQueryStoredProcedure$
BEGIN
    INSERT INTO notification (id_user_remitente, id_user_destinatario, motivo, message, visto) 
    VALUES (
        id_user_remitente_param,
        (SELECT id FROM users WHERE email = email_param LIMIT 1),
        motivo_param,
        message_param,
        visto_param
    );

    result_message := format(
        'Notification sent to User with Email %s sent successfully.',
        email_param
    );
EXCEPTION
    WHEN OTHERS THEN
        result_message := 'Error registering the notification with the recipient in the users e-mail address ' || email_param || '. Error: ' || SQLERRM;
END;
$BodyQueryStoredProcedure$;

CREATE OR REPLACE PROCEDURE stored_procedure_delete_data_user(
    IN id_user_param BIGINT,
    OUT result_message TEXT
)
LANGUAGE plpgsql
AS $BodyQueryStoredProcedure$
BEGIN
    DELETE FROM user_email_history WHERE id_user = id_user_param;
    DELETE FROM users WHERE id = id_user_param;
    result_message := format(
        'User with ID %s deleted successfully.',
        id_user_param
    );
EXCEPTION
    WHEN OTHERS THEN
        result_message := 'Error deleting user with ID ' || id_user_param || '. Error: ' || SQLERRM;
END;
$BodyQueryStoredProcedure$;


CREATE OR REPLACE PROCEDURE stored_procedure_delete_directory_entry (
    IN path_param TEXT,
    OUT result_message TEXT
)
LANGUAGE plpgsql
AS $BodyQueryStoredProcedure$
DECLARE
    rows_deleted INT; -- Variable para almacenar el número de filas eliminadas
BEGIN
    -- Eliminar los directorios y subdirectorios recursivamente
    DELETE FROM directory 
    WHERE path IN (
        WITH RECURSIVE directories_to_delete AS (
            -- Punto de inicio: el directorio base proporcionado
            SELECT path 
            FROM directory
            WHERE path = path_param
            
            UNION ALL
            
            -- Recursivamente encuentra los subdirectorios
            SELECT d.path
            FROM directory d
            INNER JOIN directories_to_delete p
            ON d.parent_directory = p.path
        )
        SELECT path FROM directories_to_delete
    );

    DELETE FROM shared_directory WHERE path = path_param;
    -- RETURNING *;

    -- Obtener el número de filas eliminadas
    GET DIAGNOSTICS rows_deleted = ROW_COUNT;

    -- Mensaje de éxito
    IF rows_deleted > 0 THEN
        result_message := format(
            'Successfully deleted %s directories starting from path: %s.',
            rows_deleted,
            path_param
        );
    ELSE
        result_message := 'No directories found to delete for path: ' || path_param || '.';
    END IF;

EXCEPTION
    -- Manejo de errores
    WHEN OTHERS THEN
        result_message := format(
            'Error deleting directories for path: %s. Error: %s',
            path_param,
            SQLERRM
        );
END;
$BodyQueryStoredProcedure$;

CREATE OR REPLACE PROCEDURE stored_procedure_register_deleted_user(
    IN name_param TEXT, 
    IN email_param TEXT, 
    IN date_deleted_param TIMESTAMPTZ,
    OUT result_message TEXT
)
LANGUAGE plpgsql
AS $BodyQueryStoredProcedure$
BEGIN
    RAISE NOTICE 'Inserting into deleted_user_history: id name = %, email = %, date_deleted = %', 
    name_param, email_param, date_deleted_param;

    INSERT INTO deleted_user_history (name, email, date_deleted)
    VALUES (name_param, email_param, date_deleted_param);
    
    result_message := format(
        'User registration successfully deleted! Details: User Name = %s, Email = %s, Date Deleted = %s.', name_param, email_param, date_deleted_param
    );
EXCEPTION
    WHEN OTHERS THEN
        RAISE WARNING 'stored procedure register deleted user Error: %', SQLERRM;  

        -- En caso de error, asignar mensaje de error al parámetro result_message
        result_message := 'Error registering deleted user: ' ||
                          'User ID: ' || id_user_param || ', ' ||
                          'Name: ' || name_param || ', ' ||
                          'Email: ' || email_param || '. ' ||
                          'Error: ' || SQLERRM;
END;
$BodyQueryStoredProcedure$;

CREATE OR REPLACE PROCEDURE stored_procedure_insert_data_files(
    IN id_user_param BIGINT,
    IN name_param TEXT,
    IN type_param TEXT,
    IN path_param TEXT,
    IN size_param BIGINT,
    IN file_creation_date_param TIMESTAMPTZ,
    OUT result_message TEXT
)
LANGUAGE plpgsql
AS $BodyQueryStoredProcedure$
BEGIN
    -- Insertar datos en la tabla files
    INSERT INTO files (id_user, name, type, path, size, file_creation_date)
    VALUES (id_user_param, name_param, type_param, path_param, size_param, file_creation_date_param);
    
    -- Asignar mensaje de éxito al parámetro result_message
    result_message := format(
        'File inserted successfully! Details: User ID = %s, Name = %s, Type = %s, Path = %s, Size = %s bytes, Creation Date = %s.',
        id_user_param, name_param, type_param, path_param, size_param, file_creation_date_param
    );
EXCEPTION
    WHEN OTHERS THEN
        -- En Caso De Error, Asignar Mensaje De Error Al Parámetro Result
        result_message := 'Error inserting file data: ' ||
          'User ID: ' || id_user_param || ', ' ||
          'File Name: ' || name_param || ', ' ||
          'File Type: ' || type_param || ', ' ||
          'Path: ' || path_param || ', ' ||
          'Size: ' || size_param || ' bytes.' ||
          'Creation Date: ' || file_creation_date_param;
END;
$BodyQueryStoredProcedure$;

CREATE OR REPLACE PROCEDURE stored_procedure_update_data_files(
    IN id_user_param BIGINT,
    IN name_param TEXT,
    IN type_param TEXT,
    IN size_param BIGINT,
    IN file_creation_date_param TIMESTAMPTZ,
    OUT result_message TEXT
)
LANGUAGE plpgsql
AS $BodyQueryStoredProcedure$
BEGIN
    UPDATE files 
    SET type = type_param, size = size_param, file_creation_date = file_creation_date_param 
    WHERE id_user = id_user_param AND name = name_param;

    result_message := format(
        'File updated successfully! Details: User ID = %s, Name = %s, Type = %s, Size = %s bytes, Creation Date = %s.',
        id_user_param, name_param, type_param, size_param, file_creation_date_param
    );
EXCEPTION
    WHEN OTHERS THEN
        -- En caso de error, asignar mensaje de error al parámetro 'result_message'
        result_message := 'Error updating file data: ' ||
          'User ID: ' || id_user_param || ', ' ||
          'File Name: ' || name_param || ', ' ||
          'File Type: ' || type_param || ', ' ||
          'Size: ' || size_param || ' bytes, ' ||
          'Creation Date: ' || file_creation_date_param;
END;
$BodyQueryStoredProcedure$;

CREATE OR REPLACE PROCEDURE stored_procedure_delete_data_storage(
    IN id_user_param BIGINT,
    OUT result_message TEXT
)
LANGUAGE plpgsql
AS $BodyQueryStoredProcedure$
BEGIN
    DELETE FROM files WHERE id_user = id_user_param;
    DELETE FROM directory WHERE id_user = id_user_param;
    DELETE FROM shared_file WHERE id_user_guest = id_user_param;
    DELETE FROM shared_directory WHERE id_user_guest = id_user_param;
    DELETE FROM notification WHERE id_user_destinatario = id_user_param;
    DELETE FROM user_email_history WHERE id_user = id_user_param;
    result_message := format(
        'Files deleted successfully! Details: User ID = %s.',
        id_user_param
    );
EXCEPTION
    WHEN OTHERS THEN
        result_message := 'Error deleting files. User ID: ' || id_user_param || '. Error: ' || SQLERRM;
END;
$BodyQueryStoredProcedure$;
