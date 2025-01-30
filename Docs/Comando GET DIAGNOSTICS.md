<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# **Comando GET DIAGNOSTICS**

> [!CAUTION]
> *El comando `GET DIAGNOSTICS` en PostgreSQL no se utiliza directamente en una consulta SQL estándar. Es una funcionalidad que solo está disponible dentro de bloques PL/pgSQL, como funciones o procedimientos almacenados.*

*Si intentas ejecutarlo directamente en el cliente interactivo de PostgreSQL (`psql`), recibirás un error como el que mencionas.*

## **Cómo usar `GET DIAGNOSTICS` correctamente**

*Si deseas obtener el número de filas afectadas por una operación (como `DELETE`), debes usarlo dentro de un bloque PL/pgSQL.*

### **Ejemplo: Uso de `GET DIAGNOSTICS` en un bloque PL/pgSQL**

```sql
DO $$
DECLARE
    rows_deleted INT;
BEGIN
    DELETE FROM users WHERE id = 1;

    -- Obtener el número de filas afectadas
    GET DIAGNOSTICS rows_deleted = ROW_COUNT;

    -- Imprimir el número de filas eliminadas
    RAISE NOTICE 'Rows deleted: %', rows_deleted;
END;
$$;
```

#### **Explicación**

1. **`DO $$ ... $$`:**
   - *Este bloque ejecuta código PL/pgSQL directamente en el cliente sin necesidad de crear una función o procedimiento almacenado.*

2. **Declaración de la variable:**
   - *`rows_deleted` se declara para almacenar el número de filas afectadas.*

3. **`GET DIAGNOSTICS`:**
   - *Recupera el número de filas afectadas por la última instrucción SQL (en este caso, el `DELETE`).*

4. **`RAISE NOTICE`:**
   - *Imprime un mensaje con el número de filas eliminadas.*

---

#### **Ejemplo de salida**

**Si la fila con `id = 1` existía y fue eliminada:**

```bash
NOTICE: Rows deleted: 1
```

*Si no existía ninguna fila que coincida con la condición:*

```bash
NOTICE: Rows deleted: 0
```

---

### **Alternativa: Obtener el número de filas directamente**

**Si no deseas usar un bloque PL/pgSQL, puedes hacer que PostgreSQL devuelva directamente el número de filas eliminadas usando `RETURNING` en la consulta `DELETE`. Por ejemplo:**

```sql
DELETE FROM users WHERE id = 1 RETURNING id;
```

*Esto devuelve las filas eliminadas directamente, y puedes contarlas en el cliente o desde tu aplicación.*

```sql
DELETE FROM users WHERE id = 1 RETURNING id;
```

- **Salida**

```sql
NOTICE:  Trigger activated for user Daniel
NOTICE:  Inserting into deleted_user_history: id name = Daniel, email = daniel@gmail.com, date_deleted = 2025-01-30 17:02:22.258412+00
NOTICE:  Message: User successfully deleted! Name = Daniel, Email = daniel@gmail.com.
 id
----
  1
(1 row)

DELETE 1
```

```sql
DELETE FROM users WHERE id = 2 RETURNING *;
```

- **Salida**

```sql
NOTICE:  Trigger activated for user Danna
NOTICE:  Inserting into deleted_user_history: id name = Danna, email = danna@gmail.com, date_deleted = 2025-01-30 17:02:43.878653+00
NOTICE:  Message: User successfully deleted! Name = Danna, Email = danna@gmail.com.
 id | name  |      email      |                           password
----+-------+-----------------+--------------------------------------------------------------
  2 | Danna | danna@gmail.com | $2y$12$pTPWkGBmqSsVsFcFy10tUOHO96XN0w9vOo62m3IIEPjG16lHc9DJG
(1 row)

DELETE 1
```
