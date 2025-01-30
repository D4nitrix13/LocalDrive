<!-- Author: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Email: danielperezdev@proton.me -->

# ***Ver El Contenido De Un Procedimiento Almacenado (Procedure) En Postgresql***

*Para ver el contenido de un procedimiento almacenado (procedure) en PostgreSQL, puedes consultar la tabla del catálogo del sistema `pg_proc` y las tablas relacionadas, o usar una consulta que recupere el texto de la definición del procedimiento.*

## **Consultar la definición del procedimiento usando `pg_proc`**

*Puedes consultar la tabla `pg_proc` para obtener información sobre los procedimientos almacenados. Para ver la definición de un procedimiento, puedes hacer lo siguiente:*

```sql
SELECT pg_get_functiondef(pg_proc.oid)
FROM pg_proc
WHERE proname = 'stored_procedure_insert_data_files';
```

- **pg_proc:** *Es la tabla del sistema que contiene información sobre todas las funciones y procedimientos en la base de datos.*
- **pg_proc.oid:** *Es el identificador único de objeto (Object Identifier) de una función específica en la base de datos.*
- **proname:** *Es la columna en la tabla pg_proc que almacena el nombre de la función o procedimiento.*
- **pg_get_functiondef:** *Esta función toma el oid de la función y devuelve el código completo de la función como un texto.*

- *Este query obtiene el texto completo de la definición del procedimiento, reemplazando `'stored_procedure_insert_data_files'` con el nombre del procedimiento que desees ver.*

- **Resultado**

```sql
SELECT pg_get_functiondef(pg_proc.oid)
FROM pg_proc
WHERE proname = 'stored_procedure_insert_data_files';
                                                                                                                       pg_get_functiondef
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 CREATE OR REPLACE PROCEDURE public.stored_procedure_insert_data_files(IN id_user_param bigint, IN name_param text, IN type_param text, IN path_param text, IN size_param bigint, IN file_creation_date_param timestamp with time zone, OUT result_message text)+
  LANGUAGE plpgsql                                                                                                                                                                                                                                              +
 AS $procedure$                                                                                                                                                                                                                                                 +
 BEGIN                                                                                                                                                                                                                                                          +
     -- Insertar datos en la tabla files                                                                                                                                                                                                                        +
     INSERT INTO files (id_user, name, type, path, size, file_creation_date)                                                                                                                                                                                    +
     VALUES (id_user_param, name_param, type_param, path_param, size_param, file_creation_date_param);                                                                                                                                                          +
                                                                                                                                                                                                                                                                +
     -- Asignar mensaje de éxito al parámetro result_message                                                                                                                                                                                                    +
     result_message := format(                                                                                                                                                                                                                                  +
         'File inserted successfully! Details: User ID = %s, Name = %s, Type = %s, Path = %s, Size = %s bytes, Creation Date = %s.',                                                                                                                            +
         id_user_param, name_param, type_param, path_param, size_param, file_creation_date_param                                                                                                                                                                +
     );                                                                                                                                                                                                                                                         +
 EXCEPTION                                                                                                                                                                                                                                                      +
     WHEN OTHERS THEN                                                                                                                                                                                                                                           +
         -- En Caso De Error, Asignar Mensaje De Error Al Parámetro Result                                                                                                                                                                                      +
         result_message := 'Error inserting file data: ' ||                                                                                                                                                                                                     +
           'User ID: ' || id_user_param || ', ' ||                                                                                                                                                                                                              +
           'File Name: ' || name_param || ', ' ||                                                                                                                                                                                                               +
           'File Type: ' || type_param || ', ' ||                                                                                                                                                                                                               +
           'Path: ' || path_param || ', ' ||                                                                                                                                                                                                                    +
           'Size: ' || size_param || ' bytes.' ||                                                                                                                                                                                                               +
           'Creation Date: ' || file_creation_date_param;                                                                                                                                                                                                       +
 END;                                                                                                                                                                                                                                                           +
 $procedure$                                                                                                                                                                                                                                                    +

(1 row)
```

### **Ver detalles sobre el procedimiento usando `pg_catalog`**

- *Si quieres más detalles sobre el procedimiento, como los parámetros, puedes consultar la vista `pg_catalog.pg_proc` junto con `pg_catalog.pg_type` para obtener los tipos de parámetros:*

```sql
SELECT 
    p.proname,
    pg_catalog.pg_get_function_result(p.oid) AS result_type,
    pg_catalog.pg_get_function_arguments(p.oid) AS arguments,
    pg_catalog.pg_get_functiondef(p.oid) AS definition
FROM pg_catalog.pg_proc p
WHERE p.proname = 'stored_procedure_insert_data_files';
```

- **p.proname:** *Devuelve el nombre de la función o procedimiento almacenado. En este caso, se filtra para que sea 'stored_procedure_insert_data_files'.*
- **pg_catalog.pg_get_function_result(p.oid) AS result_type:** *Devuelve el tipo de dato que la función retorna, por ejemplo, void, integer, text, etc. Esto se obtiene utilizando la función pg_get_function_result, que requiere el OID (Object Identifier) de la función.*
- **pg_catalog.pg_get_function_arguments(p.oid) AS arguments:** *Devuelve los argumentos que acepta la función. Esto se logra mediante la función pg_get_function_arguments, que también toma el OID de la función y devuelve una lista de los tipos de los parámetros.*
- **pg_catalog.pg_get_functiondef(p.oid) AS definition:** *Devuelve la definición completa de la función, es decir, el código SQL que define la función o procedimiento almacenado. Se utiliza la función pg_get_functiondef, que obtiene el código fuente completo.*

**Esto mostrará:**

- *El nombre del procedimiento.*
- *El tipo de resultado (si lo hay).*
- *Los argumentos del procedimiento.*
- *La definición completa del procedimiento.*

**Resultado:**

```sql
SELECT
    p.proname,
    pg_catalog.pg_get_function_result(p.oid) AS result_type,
    pg_catalog.pg_get_function_arguments(p.oid) AS arguments,
    pg_catalog.pg_get_functiondef(p.oid) AS definition
FROM pg_catalog.pg_proc p
WHERE p.proname = 'stored_procedure_insert_data_files';
              proname               | result_type |                                                                                        arguments                                                                                         |                                                                                                                           definition
------------------------------------+-------------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
 stored_procedure_insert_data_files |             | IN id_user_param bigint, IN name_param text, IN type_param text, IN path_param text, IN size_param bigint, IN file_creation_date_param timestamp with time zone, OUT result_message text | CREATE OR REPLACE PROCEDURE public.stored_procedure_insert_data_files(IN id_user_param bigint, IN name_param text, IN type_param text, IN path_param text, IN size_param bigint, IN file_creation_date_param timestamp with time zone, OUT result_message text)+
                                    |             |                                                                                                                                                                                          |  LANGUAGE plpgsql                                                                                                                                                                                                                                              +
                                    |             |                                                                                                                                                                                          | AS $procedure$                                                                                                                                                                                                                                                 +
                                    |             |                                                                                                                                                                                          | BEGIN                                                                                                                                                                                                                                                          +
                                    |             |                                                                                                                                                                                          |     -- Insertar datos en la tabla files                                                                                                                                                                                                                        +
                                    |             |                                                                                                                                                                                          |     INSERT INTO files (id_user, name, type, path, size, file_creation_date)                                                                                                                                                                                    +
                                    |             |                                                                                                                                                                                          |     VALUES (id_user_param, name_param, type_param, path_param, size_param, file_creation_date_param);                                                                                                                                                          +
                                    |             |                                                                                                                                                                                          |                                                                                                                                                                                                                                                                +
                                    |             |                                                                                                                                                                                          |     -- Asignar mensaje de éxito al parámetro result_message                                                                                                                                                                                                    +
                                    |             |                                                                                                                                                                                          |     result_message := format(                                                                                                                                                                                                                                  +
                                    |             |                                                                                                                                                                                          |         'File inserted successfully! Details: User ID = %s, Name = %s, Type = %s, Path = %s, Size = %s bytes, Creation Date = %s.',                                                                                                                            +
                                    |             |                                                                                                                                                                                          |         id_user_param, name_param, type_param, path_param, size_param, file_creation_date_param                                                                                                                                                                +
                                    |             |                                                                                                                                                                                          |     );                                                                                                                                                                                                                                                         +
                                    |             |                                                                                                                                                                                          | EXCEPTION                                                                                                                                                                                                                                                      +
                                    |             |                                                                                                                                                                                          |     WHEN OTHERS THEN                                                                                                                                                                                                                                           +
                                    |             |                                                                                                                                                                                          |         -- En Caso De Error, Asignar Mensaje De Error Al Parámetro Result                                                                                                                                                                                      +
                                    |             |                                                                                                                                                                                          |         result_message := 'Error inserting file data: ' ||                                                                                                                                                                                                     +
                                    |             |                                                                                                                                                                                          |           'User ID: ' || id_user_param || ', ' ||                                                                                                                                                                                                              +
                                    |             |                                                                                                                                                                                          |           'File Name: ' || name_param || ', ' ||                                                                                                                                                                                                               +
                                    |             |                                                                                                                                                                                          |           'File Type: ' || type_param || ', ' ||                                                                                                                                                                                                               +
                                    |             |                                                                                                                                                                                          |           'Path: ' || path_param || ', ' ||                                                                                                                                                                                                                    +
                                    |             |                                                                                                                                                                                          |           'Size: ' || size_param || ' bytes.' ||                                                                                                                                                                                                               +
                                    |             |                                                                                                                                                                                          |           'Creation Date: ' || file_creation_date_param;                                                                                                                                                                                                       +
                                    |             |                                                                                                                                                                                          | END;                                                                                                                                                                                                                                                           +
                                    |             |                                                                                                                                                                                          | $procedure$                                                                                                                                                                                                                                                    +
                                    |             |                                                                                                                                                                                          |
(1 row)
```

### **Usar `\df` en `psql`**

**Si estás usando `psql` (la terminal de PostgreSQL), puedes usar el comando `\df` para ver una lista de procedimientos y funciones definidas:**

```sql
\df
                                                                                                                              List of functions
 Schema |                     Name                      | Result data type |                                                                                   Argument data types                                                                                    | Type
--------+-----------------------------------------------+------------------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+------
 public | function_delete_data_files                    | boolean          | id_user_param bigint                                                                                                                                                                     | func
 public | function_delete_data_user                     | boolean          | id_user_param bigint                                                                                                                                                                     | func
 public | function_insert_data_files                    | boolean          | id_user_param bigint, name_param text, type_param text, path_param text, size_param bigint, file_creation_date_param timestamp with time zone                                            | func
 public | function_insert_data_notification             | boolean          | id_user_remitente_param bigint, email_param text, motivo_param text, message_param text                                                                                                  | func
 public | function_insert_data_shared_directory         | boolean          | id_user_property_param bigint, email_destinatario_param text, path_param text                                                                                                            | func
 public | function_insert_data_shared_file              | boolean          | id_user_property_param bigint, email_destinatario_param text, path_param text                                                                                                            | func
 public | function_log_email_update                     | trigger          |                                                                                                                                                                                          | func
 public | function_register_deleted_user                | trigger          |                                                                                                                                                                                          | func
 public | function_update_data_files                    | boolean          | id_user_param bigint, name_param text, type_param text, size_param bigint, file_creation_date_param timestamp with time zone                                                             | func
 public | stored_procedure_delete_data_storage            |                  | IN id_user_param bigint, OUT result_message text                                                                                                                                         | proc
 public | stored_procedure_delete_data_user             |                  | IN id_user_param bigint, OUT result_message text                                                                                                                                         | proc
 public | stored_procedure_insert_data_files            |                  | IN id_user_param bigint, IN name_param text, IN type_param text, IN path_param text, IN size_param bigint, IN file_creation_date_param timestamp with time zone, OUT result_message text | proc
 public | stored_procedure_insert_data_notification     |                  | IN id_user_remitente_param bigint, IN email_param text, IN motivo_param text, IN message_param text, IN visto_param boolean, OUT result_message text                                     | proc
 public | stored_procedure_insert_data_shared_directory |                  | IN id_user_property_param bigint, IN email_destinatario_param text, IN path_param text, OUT result_message text                                                                          | proc
 public | stored_procedure_insert_data_shared_file      |                  | IN id_user_property_param bigint, IN email_destinatario_param text, IN path_param text, OUT result_message text                                                                          | proc
 public | stored_procedure_register_deleted_user        |                  | IN name_param text, IN email_param text, IN date_deleted_param timestamp with time zone, OUT result_message text                                                                         | proc
 public | stored_procedure_update_data_files            |                  | IN id_user_param bigint, IN name_param text, IN type_param text, IN size_param bigint, IN file_creation_date_param timestamp with time zone, OUT result_message text                     | proc
(17 rows)
```

```sql
\df stored_procedure_insert_data_files
```

```sql
\df stored_procedure_insert_data_files
                                                                                                                        List of functions
 Schema |                Name                | Result data type |                                                                                   Argument data types                                                                                    | Type
--------+------------------------------------+------------------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+------
 public | stored_procedure_insert_data_files |                  | IN id_user_param bigint, IN name_param text, IN type_param text, IN path_param text, IN size_param bigint, IN file_creation_date_param timestamp with time zone, OUT result_message text | proc
(1 row)
```

- **Esto te mostrará los detalles básicos del procedimiento, pero no el cuerpo completo. Si quieres ver la definición completa, deberías usar uno de los métodos anteriores.**

### **Resumen**

- **Consulta SQL:** *Usa `pg_get_functiondef` para obtener la definición completa.*
- **`psql`:** *Usa el comando `\df` para obtener información básica.*
