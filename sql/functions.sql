-- Autor: Daniel Benjamin Perez Morales
-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13
-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13
-- Correo electrónico: danielperezdev@proton.me

-- Functions
CREATE OR REPLACE FUNCTION function_insert_data_shared_file(
    IN id_user_property_param BIGINT,
    IN email_destinatario_param TEXT,
    IN shared_from_a_directory_param BOOLEAN,
    IN path_param TEXT
)
RETURNS BOOLEAN AS $BodyQueryFunction$
DECLARE
    result_message TEXT := '';
BEGIN
    CALL stored_procedure_insert_data_shared_file(id_user_property_param::BIGINT, email_destinatario_param::TEXT, shared_from_a_directory_param::BOOLEAN, path_param::TEXT, result_message::TEXT);

    RAISE NOTICE 'Message: %', result_message;
    RETURN TRUE;
EXCEPTION
    WHEN OTHERS THEN
        RAISE WARNING 'Error: %', SQLERRM;
        RETURN FALSE;
END;
$BodyQueryFunction$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION function_insert_data_shared_directory(
    IN id_user_property_param BIGINT,
    IN email_destinatario_param TEXT,
    IN path_param TEXT
)
RETURNS BOOLEAN AS $BodyQueryFunction$
DECLARE
    result_message TEXT := '';
BEGIN
    CALL stored_procedure_insert_data_shared_directory(id_user_property_param::BIGINT, email_destinatario_param::TEXT, path_param::TEXT, result_message::TEXT);

    RAISE NOTICE 'Message: %', result_message;
    RETURN TRUE;
EXCEPTION
    WHEN OTHERS THEN
        RAISE WARNING 'Error: %', SQLERRM;
        RETURN FALSE;
END;
$BodyQueryFunction$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION function_insert_data_notification(
    IN id_user_remitente_param BIGINT,
    IN email_param TEXT,
    IN motivo_param TEXT,
    IN message_param TEXT
)
RETURNS BOOLEAN AS $BodyQueryFunction$
DECLARE
    result_message TEXT := '';
    visto_param BOOLEAN := false;
BEGIN
    CALL stored_procedure_insert_data_notification(id_user_remitente_param::BIGINT, email_param::TEXT, motivo_param::TEXT, message_param::TEXT, visto_param::BOOLEAN, result_message::TEXT);

    RAISE NOTICE 'Message: %', result_message;
    RETURN TRUE;
EXCEPTION
    WHEN OTHERS THEN
        RAISE WARNING 'Error: %', SQLERRM;
        RETURN FALSE;
END;
$BodyQueryFunction$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION function_delete_data_user(
    IN id_user_param BIGINT
)
RETURNS BOOLEAN AS $BodyQueryFunction$
DECLARE
    result_message TEXT := '';
BEGIN
    CALL stored_procedure_delete_data_user(id_user_param::BIGINT, result_message::TEXT);

    RAISE NOTICE 'Message: %', result_message;
    RETURN TRUE;
EXCEPTION
    WHEN OTHERS THEN
        RAISE WARNING 'Message: %', SQLERRM;
        RETURN FALSE;
END;
$BodyQueryFunction$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION function_delete_directory(
    IN path_param TEXT
)
RETURNS BOOLEAN AS $BodyQueryFunction$
DECLARE
    result_message TEXT := '';
BEGIN
    CALL stored_procedure_delete_directory_entry(path_param::TEXT, result_message::TEXT);

    RAISE NOTICE 'Message: %', result_message;
    RETURN TRUE;
EXCEPTION
    WHEN OTHERS THEN
        RAISE WARNING 'Message: %', SQLERRM;
        RETURN FALSE;
END;
$BodyQueryFunction$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION function_log_email_update()
RETURNS TRIGGER AS $BodyQueryFunction$
BEGIN
    IF OLD.email <> NEW.email THEN
        INSERT INTO user_email_history (id_user, email)
        VALUES (OLD.id, OLD.email);
    END IF;
    RETURN NEW;
END;
$BodyQueryFunction$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION function_register_deleted_user()
RETURNS TRIGGER AS $BodyQueryFunction$
DECLARE
    result_message TEXT := '';
BEGIN
    RAISE NOTICE 'Trigger activated for user %', OLD.name;  
    CALL stored_procedure_register_deleted_user(OLD.name::TEXT, OLD.email::TEXT, CURRENT_TIMESTAMP::TIMESTAMPTZ, result_message::TEXT);

    result_message := format(
        'User successfully deleted! Name = %s, Email = %s.',
        OLD.name, OLD.email
    );

    RAISE NOTICE 'Message: %', result_message;
    RETURN OLD;
EXCEPTION
    WHEN OTHERS THEN
        RAISE WARNING 'Error while registering deleted user: %', SQLERRM;
        RETURN OLD;
END;
$BodyQueryFunction$ LANGUAGE plpgsql;


CREATE OR REPLACE FUNCTION function_insert_data_files(
    IN id_user_param BIGINT,
    IN name_param TEXT,
    IN type_param TEXT,
    IN path_param TEXT,
    IN size_param BIGINT,
    IN file_creation_date_param TIMESTAMPTZ
)
RETURNS BOOLEAN AS $BodyQueryFunction$
DECLARE
    result_message TEXT := '';
BEGIN
    -- Llamar al procedimiento sin pasar 'result_message' como parámetro
    CALL stored_procedure_insert_data_files(
        id_user_param::BIGINT,
        name_param::TEXT,
        type_param::TEXT,
        path_param::TEXT,
        size_param::BIGINT,
        file_creation_date_param::TIMESTAMPTZ,
        result_message::TEXT
    );
    RAISE NOTICE 'Message: %', result_message;
    RETURN TRUE;
EXCEPTION
    WHEN OTHERS THEN
        -- Si Ocurrió Un Error, Capturamos El Error Y Devolvemos False
        RETURN FALSE;
END;
$BodyQueryFunction$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION function_update_data_files(
    IN id_user_param BIGINT,
    IN name_param TEXT,
    IN type_param TEXT,
    IN size_param BIGINT,
    IN file_creation_date_param TIMESTAMPTZ
)
RETURNS BOOLEAN AS $BodyQueryFunction$
DECLARE
    result_message TEXT := '';
BEGIN
    -- Llamar al procedimiento sin pasar 'result_message' como parámetro
    CALL stored_procedure_update_data_files(
        id_user_param::BIGINT,
        name_param::TEXT,
        type_param::TEXT,
        size_param::BIGINT,
        file_creation_date_param::TIMESTAMPTZ,
        result_message::TEXT
    );
    
    RAISE NOTICE 'Message: %', result_message;
    RETURN TRUE;
EXCEPTION
    WHEN OTHERS THEN
        RAISE WARNING 'Error: %', SQLERRM;  -- Puedes Usar SQLERRM Para Obtener Más Detalles Del Error
        RETURN FALSE;
END;
$BodyQueryFunction$ LANGUAGE plpgsql;

CREATE OR REPLACE FUNCTION function_delete_data_files(
    IN id_user_param BIGINT
)
RETURNS BOOLEAN AS $BodyQueryFunction$
DECLARE
    result_message TEXT := '';
BEGIN
    CALL stored_procedure_delete_data_storage(
        id_user_param::BIGINT,
        result_message::TEXT
    );
    RAISE NOTICE 'Message: %', result_message;
    RETURN TRUE;
EXCEPTION
    WHEN OTHERS THEN
        RETURN FALSE;
END;
$BodyQueryFunction$ LANGUAGE plpgsql;