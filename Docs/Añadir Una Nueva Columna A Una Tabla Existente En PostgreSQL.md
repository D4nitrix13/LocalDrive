<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# ***Añadir Una Nueva Columna A Una Tabla Existente En PostgreSQL, puedes usar el comando `ALTER TABLE` con la cláusula `ADD COLUMN`.***

## ***Sintaxis básica***

```sql
ALTER TABLE table_name
ADD COLUMN column_name data_type [constraints];
```

### **Ejemplo simple**

- **Añadamos una nueva columna `phone_number` al tipo `VARCHAR(15)` a la tabla `users`:**

```sql
ALTER TABLE users
ADD COLUMN phone_number VARCHAR(15);
```

---

### ***Ejemplo con restricciones***

**Si deseas añadir restricciones como `NOT NULL` o un valor predeterminado:**

1. **Columna con `NOT NULL`**:

   ```sql
   ALTER TABLE users
   ADD COLUMN date_of_birth DATE NOT NULL;
   ```

   > *Esto fallará si ya existen registros en la tabla porque no se puede asignar `NULL` a las filas existentes. Puedes usar un valor predeterminado temporal y luego actualizar los valores.*

2. **Columna con valor predeterminado**:

   ```sql
   ALTER TABLE users
   ADD COLUMN status TEXT DEFAULT 'active';
   ```

   > *Todas las filas existentes tendrán `'active'` como valor inicial.*

3. **Columna con ambas restricciones**:

   ```sql
   ALTER TABLE users
   ADD COLUMN created_at TIMESTAMPTZ DEFAULT CURRENT_TIMESTAMP NOT NULL;
   ```

---

### ***Añadir varias columnas al mismo tiempo***

- **Puedes usar varias cláusulas `ADD COLUMN` en una sola instrucción:**

```sql
ALTER TABLE users
ADD COLUMN address TEXT,
ADD COLUMN is_active BOOLEAN DEFAULT TRUE;
```

---

### **Verificar después de añadir la columna**

- **Después de realizar el cambio, puedes verificar la estructura de la tabla con:**

```sql
\d users
```

- **Esto muestra todas las columnas y sus tipos de datos.**
