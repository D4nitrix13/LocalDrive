<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# ***`La collation en PostgreSQL` se refiere al conjunto de reglas que determinan cómo se ordenan y comparan los datos de tipo texto. Esto incluye reglas para la clasificación alfabética y sensibilidad a mayúsculas/minúsculas o acentos. Es un concepto clave en bases de datos que necesitan admitir diferentes idiomas o configuraciones regionales.***

## **Explicación de la columna `Collation`**

**En la descripción de la tabla:**

```sql
| Column         | Type                       | Collation | Nullable   | Default             |
| -------------- | -------------------------- | --------- | ---------- | ------------------- |
| `id`           | `bigint`                   |           | `not null` |                     |
| `name`         | `text`                     |           | `not null` |                     |
| `email`        | `text`                     |           | `not null` |                     |
| `date_deleted` | `timestamp with time zone` |           | `not null` | `CURRENT_TIMESTAMP` |
```

- *La columna `Collation` está vacía, lo que significa que la tabla está utilizando la configuración de collation predeterminada de la base de datos.*

---

### **¿Por qué es importante la collation?**

- **La collation afecta las operaciones en datos de tipo texto, como:**

1. **Comparaciones:**
   - *Si `name = 'Álvaro'` y haces una consulta `WHERE name = 'alvaro'`, el resultado dependerá de si la collation distingue entre acentos y mayúsculas.*

2. **Ordenación:**
   - *En una lista ordenada alfabéticamente (`ORDER BY`), diferentes reglas de collation podrían colocar `Álvaro` antes o después de `Alberto`.*

---

### **Especificar una collation**

**Puedes especificar una collation para una columna al momento de crearla. Por ejemplo:**

```sql
CREATE TABLE users (
   id SERIAL PRIMARY KEY,
   name TEXT COLLATE "en_US.utf8", -- Ordenación y comparación según las reglas de configuración regional
   email TEXT NOT NULL
);
```

- **En este caso, `"en_US.utf8"` es una collation codificación se refiere a un locale (configuración regional) .**

- **en_US:**
  - *Idioma: Inglés (en).*
  - *Región: Estados Unidos (US).*

*Esto establece las convenciones de idioma, formato de fecha, moneda, y otras configuraciones regionales propias de los Estados Unidos.*

**utf8 (UTF-8):**

- *Es una codificación de caracteres basada en Unicode que puede representar prácticamente todos los caracteres y símbolos de los idiomas del mundo.*
- *UTF-8 es ampliamente utilizado debido a su compatibilidad con ASCII y su eficiencia en términos de espacio para textos predominantemente en inglés.*

---

### **Cambiar la collation de una columna existente**

- **Si necesitas cambiar la collation de una columna, usa el siguiente comando:**

```sql
ALTER TABLE users
ALTER COLUMN name TYPE TEXT COLLATE "en_US.utf8";
```

> [!NOTE]
> *Cambiar la collation puede requerir que se eliminen índices existentes en la columna, ya que los índices están ligados a las reglas de collation.*

- *La collation es un conjunto de reglas para comparar y ordenar texto.*
- *Afecta operaciones como `ORDER BY`, `LIKE`, y comparaciones en `WHERE`.*
- *Puede especificarse a nivel de columna o usarse la predeterminada de la base de datos.*
- *Es crucial para aplicaciones que requieren soporte para diferentes idiomas o culturas.*
