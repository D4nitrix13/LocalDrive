<!-- Author: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Email: danielperezdev@proton.me -->

# ***Si El Trigger No Está Ejecutándose Al Eliminar Un Usuario, Es Importante Revisar Los Siguientes Aspectos Para Identificar Y Solucionar El Problema:***

## **Verifica que el trigger esté asociado a la tabla correcta**

- **El trigger `trigger_deleted_user_history` debe estar vinculado específicamente a la tabla `users`. Confirma que la tabla existe y que el trigger esté correctamente creado con la siguiente consulta:**

```sql
SELECT tgname, tgrelid::regclass AS table_name, tgtype::integer, tgenabled
FROM pg_trigger
WHERE tgname = 'trigger_deleted_user_history';
```

- **tgname:**
  - **Descripción:** *Es el nombre del trigger.*
  - **Propósito:** *Identifica el trigger por su nombre, definido al crearlo.*
  - **Ejemplo:** *'trigger_deleted_user_history'.*

- **tgrelid::regclass AS table_name:**
  - *Descripción: tgrelid es el identificador de la tabla (OID, Object Identifier) a la que está asociado el trigger.*
  - *Transformación con ::regclass: Convierte el OID en un nombre de tabla legible para humanos.*
  - *Propósito: Indica sobre qué tabla opera el trigger.*
  - *Ejemplo: Si tgrelid corresponde al OID de la tabla users, el resultado será 'users'.*

- **tgtype::integer:**
  - *Descripción: tgtype es un campo que define las propiedades del trigger, como el evento que lo activa (por ejemplo, INSERT, UPDATE, DELETE), el momento en el que se ejecuta (BEFORE, AFTER, INSTEAD OF), y si se aplica a filas o a la operación completa.*
  - *Transformación con ::integer: Devuelve el valor numérico interno que representa las características combinadas del trigger.*
  - *Propósito: Permite analizar las propiedades del trigger. Para interpretarlo, es necesario desglosar el valor numérico en bits:*

- **Valores posibles de tgtype**

| `tgtype` | *Binario*   | *Descripción*              |
| -------- | ----------- | -------------------------- |
| *`1`*    | *`0000001`* | *AFTER*                    |
| *`2`*    | *`0000010`* | *BEFORE*                   |
| *`3`*    | *`0000011`* | *INSTEAD OF*               |
| *`4`*    | *`0000100`* | *INSERT*                   |
| *`8`*    | *`0001000`* | *DELETE*                   |
| *`16`*   | *`0010000`* | *UPDATE*                   |
| *`32`*   | *`0100000`* | *TRUNCATE*                 |
| *`5`*    | *`0000101`* | *AFTER INSERT*             |
| *`9`*    | *`0001001`* | *AFTER DELETE*             |
| *`17`*   | *`0010001`* | *AFTER UPDATE*             |
| *`41`*   | *`0101001`* | *AFTER DELETE y TRUNCATE*  |
| *`10`*   | *`0001010`* | *BEFORE DELETE*            |
| *`18`*   | *`0010010`* | *BEFORE UPDATE*            |
| *`50`*   | *`0110010`* | *BEFORE UPDATE y TRUNCATE* |

- *Ejemplo: Si tgtype es 22, significa que es un trigger BEFORE DELETE (antes de eliminar) y UPDATE (antes de actualizar).*

- **tgenabled:**
  - **Descripción: Indica si el trigger está habilitado, deshabilitado, o habilitado solo para una operación concreta.**
  - **Valores posibles:**
    - **'O' (Enabled):** *Habilitado.*
    - **'D' (Disabled):** *Deshabilitado.*
    - **'R' (Enabled Replica):** *Habilitado solo para replicación.*
    - **'A' (Always):** *Siempre habilitado, incluso en replicación.*
  - *Propósito: Verifica si el trigger está operativo en la tabla.*
  - *Ejemplo: 'O' indica que el trigger está activo y funcionando.*

- *Esto debería devolver el nombre del trigger, la tabla asociada (`users`), el tipo (AFTER DELETE), y si está habilitado (`O` significa que está habilitado).*

```sql
SELECT tgname, tgrelid::regclass AS table_name, tgtype::integer, tgenabled
FROM pg_trigger
WHERE tgname = 'trigger_deleted_user_history';
            tgname            | table_name | tgtype | tgenabled
------------------------------+------------+--------+-----------
 trigger_deleted_user_history | users      |      9 | O
(1 row)
```

---

### Verifica que el trigger esté habilitado

- **Si el trigger aparece deshabilitado, habilítalo manualmente:**

```sql
ALTER TABLE users ENABLE TRIGGER trigger_deleted_user_history;
```

---

### **Comprueba la función asociada**

- *Confirma que la función `function_register_deleted_user` esté correctamente creada. Ejecuta:*

```sql
\df function_register_deleted_user
```

- **Esto debería mostrar detalles de la función, como el esquema y el tipo de retorno (TRIGGER).**

---

### ***Revisa los permisos***

*El usuario que ejecuta la operación DELETE debe tener permisos suficientes para disparar el trigger. Verifica los permisos:*

```sql
\dp users
```

```sql
\dp users
                             Access privileges
 Schema | Name  | Type  | Access privileges | Column privileges | Policies
--------+-------+-------+-------------------+-------------------+----------
 public | users | table |                   |                   |
(1 row)
```

*Asegúrate de que el usuario tenga permisos para realizar DELETE en la tabla `users`.*

---

### **Reproduce el flujo completo con mensajes de depuración**

*Crea un usuario y elimínalo para observar el comportamiento del trigger con mensajes de depuración activados. Por ejemplo:*

```sql
-- Crear un usuario
INSERT INTO users (name, email, password)
VALUES ('Daniel Benjamin Perez Morales', 'daniel@gmail.com', 'password123');

-- Eliminar el usuario
DELETE FROM users WHERE email = 'daniel@gmail.com';
```

- *Mientras realizas esto, activa la salida de mensajes de depuración en PostgreSQL para confirmar que el trigger y la función se ejecutan. Puedes habilitar esto temporalmente:*

```sql
SET client_min_messages TO NOTICE;
```

- *Esto debería mostrar los mensajes generados por `RAISE NOTICE` dentro de `function_register_deleted_user`.*

---

### ***Verifica si hay errores en los logs***

- *Si el trigger no se ejecuta o la función falla silenciosamente, revisa los logs del servidor PostgreSQL. Busca mensajes de error o advertencias relacionadas con `function_register_deleted_user` o `trigger_deleted_user_history`.*

---

#### ***Prueba de eliminación***

```sql
DELETE FROM users WHERE id = 1;
```

**Esto debería al menos mostrar un mensaje `Trigger executed: <name_user>`.**

---

### ***Consulta la tabla `deleted_user_history`***

- **Después de eliminar un usuario, verifica si los datos están presentes en la tabla `deleted_user_history`:**

```sql
SELECT * FROM deleted_user_history;
```
