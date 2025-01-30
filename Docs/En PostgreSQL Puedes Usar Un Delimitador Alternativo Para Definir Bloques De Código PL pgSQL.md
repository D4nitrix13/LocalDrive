<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# ***En PostgreSQL puedes usar un delimitador alternativo para definir bloques de código PL/pgSQL, como `$BodyQuery$` en lugar de los delimitadores estándar `$$`. Es completamente válido y útil cuando necesitas evitar conflictos con el contenido del código (por ejemplo, cuando el código contiene `$$` en su interior).***

## ***Ejemplo con `$BodyQuery$`:***

**Si prefieres usar `$BodyQuery$` como delimitador, aquí tienes un ejemplo de cómo se vería:**

```sql
CREATE PROCEDURE stored_procedure_insert_data_files(
    IN id_user_param BIGINT,
    IN name_param TEXT,
    IN type_param TEXT,
    IN path_param TEXT,
    IN size_param BIGINT,
    IN file_creation_date_param TIMESTAMPTZ,
    OUT result TEXT
)
LANGUAGE plpgsql
AS $BodyQuery$
BEGIN
    INSERT INTO files (id_user, name, type, path, size, file_creation_date)
    VALUES (id_user_param, name_param, type_param, path_param, size_param, file_creation_date_param);

    result := format(
        'File inserted successfully! Details: User ID = %s, Name = %s, Type = %s, Path = %s, Size = %s bytes, Creation Date = %s.',
        id_user_param, name_param, type_param, path_param, size_param, file_creation_date_param
    );
EXCEPTION
    WHEN OTHERS THEN
        result := 'Error inserting file data';
END;
$BodyQuery$;
```

### ***Explicación***

- *El delimitador `$BodyQuery$` se puede usar en lugar de `$$`. Esto es útil para evitar confusión cuando el cuerpo del procedimiento tiene cadenas que contienen `$$` o cualquier otro carácter especial.*
- *El nombre del delimitador puede ser cualquier palabra o secuencia de caracteres que no esté en uso dentro del código (en este caso, `$BodyQuery$`).*

### ***¿Por qué usar `$BodyQuery$`?***

- **Evitar conflictos con el código:** *Si tienes un bloque de código que contiene `$$` en alguna parte, puedes elegir un nombre para el delimitador que no se use en el cuerpo del código.*
- **Flexibilidad:** *Puedes usar cualquier nombre como delimitador, lo que te da más control sobre el formato.*
