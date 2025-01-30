<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# **En PostgreSQL, la cláusula `RETURNING` en una sentencia `INSERT`, `UPDATE` o `DELETE` devuelve filas como resultado de la operación. Es útil para obtener datos sobre las filas afectadas por la consulta, como valores de columnas o incluso el número de filas afectadas.**

*En tu caso:*

```sql
DELETE FROM directory 
WHERE path IN (...)
RETURNING *;
```

## **¿Qué hace `RETURNING *` aquí?**

1. **Devuelve las filas eliminadas:**
   - *`RETURNING *` devuelve todas las columnas de las filas que fueron eliminadas por la sentencia `DELETE`.*
   - *Básicamente, es como una combinación de `DELETE` y un `SELECT` para las filas eliminadas.*

2. **Uso típico en aplicaciones:**
   - *Puedes capturar las filas eliminadas para usarlas después en tu lógica (como registro, auditoría, etc.).*
   - *Si ejecutas esta sentencia en un entorno interactivo como `psql`, verás las filas eliminadas en la salida.*

---

### **Ejemplo sencillo**

#### **Dado una tabla `directory`**

```sql
SELECT * FROM directory;
```

```sql
 id_user |    name    |              path               |     parent_directory
---------+------------+---------------------------------+--------------------------
       1 | Directory1 | /var/www/html/Directory1        | /var/www/html
       2 | Directory2 | /var/www/html/Directory2        | /var/www/html
       3 | Directory3 | /var/www/html/Directory3        | /var/www/html
       2 | Videos     | /var/www/html/Directory2/Videos | /var/www/html/Directory2
       1 | Photos     | /var/www/html/Directory1/Photos | /var/www/html/Directory1
(5 rows)
```

Si ejecutas:

```sql
DELETE FROM directory 
WHERE path LIKE '/var/www/html/Directory1/Photos%' AND id_user = 1
RETURNING *;
```

**Resultado:**

```bash
 id_user |  name  |              path               |     parent_directory
---------+--------+---------------------------------+--------------------------
       1 | Photos | /var/www/html/Directory1/Photos | /var/www/html/Directory1
(1 row)

DELETE 1
```

#### **Las filas también se eliminan**

**Después de ejecutar la sentencia, si haces un `SELECT * FROM directory`, solo quedará:**

```sql
SELECT * FROM directory
```

```sql
 id_user |    name    |              path               |     parent_directory
---------+------------+---------------------------------+--------------------------
       1 | Directory1 | /var/www/html/Directory1        | /var/www/html
       2 | Directory2 | /var/www/html/Directory2        | /var/www/html
       3 | Directory3 | /var/www/html/Directory3        | /var/www/html
       2 | Videos     | /var/www/html/Directory2/Videos | /var/www/html/Directory2
(4 rows)
```

---

### **Aplicación práctica**

**En tu procedimiento almacenado:**

- *Aunque incluyes `RETURNING *`, **no estás capturando ni usando el resultado**.*
- *Si no necesitas realmente las filas eliminadas para algún propósito (como auditoría o mostrar detalles), puedes eliminar `RETURNING *` sin afectar la funcionalidad principal del procedimiento.*

*Si decides capturar las filas eliminadas, puedes almacenarlas en una variable temporal o usarlas para más lógica. Ejemplo:*

```sql
DO $$
DECLARE
    deleted_rows RECORD;
BEGIN
    -- Eliminar el directorio
    DELETE FROM directory
    WHERE path = '/var/www/html/Directory2/Videos' AND id_user = 2
    RETURNING * INTO deleted_rows;

    -- Usar los valores eliminados para, por ejemplo, hacer un registro
    RAISE NOTICE 'Deleted directory: %', deleted_rows.path;
END $$;
```

- **Salida**

```sql
NOTICE:  Deleted directory: /var/www/html/Directory2/Videos
DO
```

*En resumen, `RETURNING *` es opcional y sirve para recuperar información sobre las filas eliminadas en la misma consulta.*
