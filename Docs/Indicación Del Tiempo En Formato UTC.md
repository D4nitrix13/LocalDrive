<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# ***El formato `2024-12-26 00:43:06+00` incluye una indicación del tiempo en formato UTC (`+00`). Esto es válido y significa que la fecha y hora están almacenadas con una zona horaria. Dependiendo de tus necesidades, puedes:***

1. **Mostrar la fecha sin el indicador de la zona horaria (`+00`):**
   - *Puedes procesar el dato al recuperarlo y formatearlo usando funciones en PHP o SQL.*

2. **Convertir la fecha a una zona horaria específica:**
   - *Por ejemplo, si necesitas mostrar la fecha y hora en una zona horaria local.*

---

## **Formatear la Fecha sin el Indicador UTC**

**Cuando recuperes la fecha, utiliza funciones de PHP para ajustar el formato. Por ejemplo:**

```php
<?php
// Recupera la fecha de la base de datos (Esto viene de tu base de datos)
$dateFromDatabase = "2024-12-26 00:43:06+00";

// Convierte la fecha a un formato sin zona horaria
$formattedDate = date("Y-m-d H:i:s", strtotime($dateFromDatabase));

echo "Fecha formateada: " . $formattedDate;
// Salida: 2024-12-26 00:43:06
```

---

### **2. Convertir a una Zona Horaria Local**

**Si necesitas ajustar a una zona horaria específica, usa `DateTime` y `DateTimeZone`:**

```php
$dateFromDatabase = "2024-12-26 00:43:06+00"; // UTC por defecto

// Crear el objeto DateTime en UTC
$dateTime = new DateTime($dateFromDatabase, new DateTimeZone("UTC"));

// Convertir a una zona horaria local
$dateTime->setTimezone(new DateTimeZone("America/Managua")); // Cambia por tu zona horaria

echo "Fecha en zona local: " . $dateTime->format("Y-m-d H:i:s");
// Salida: 2024-12-25 18:43:06 (dependiendo de la zona horaria)
```

---

### **3. Si Deseas Almacenar Sin Zona Horaria**

- **Si no deseas incluir la zona horaria en la base de datos, ajusta el tipo de datos de tu columna para que no incluya zona horaria explícita. En PostgreSQL:**

```sql
SELECT * FROM files;
 id | id_user |  name   |     type      |       path        | size |      file_creation_date
----+---------+---------+---------------+-------------------+------+-------------------------------
  1 |       1 | main.py | text/x-python | /App/Code/main.py |  515 | 2025-01-09 03:41:45.043363+00
(1 row)
```

```sql
ALTER TABLE files ALTER COLUMN file_creation_date TYPE TIMESTAMP WITHOUT TIME ZONE;
ALTER TABLE
```

```sql
SELECT * FROM files;
 id | id_user |  name   |     type      |       path        | size |     file_creation_date
----+---------+---------+---------------+-------------------+------+----------------------------
  1 |       1 | main.py | text/x-python | /App/Code/main.py |  515 | 2025-01-09 03:41:45.043363
(1 row)
```

```sql
ALTER TABLE files ALTER COLUMN modification_date TYPE TIMESTAMP WITHOUT TIME ZONE;
```

- **Esto almacenará la fecha y hora sin el indicador `+00`.**

---

### **Resumen**

- *Si no necesitas la zona horaria, convierte el tipo de columna a `TIMESTAMP WITHOUT TIME ZONE`.*
- *Si necesitas formatear la fecha en PHP, usa `date()` o `DateTime` para ajustar el formato al mostrarla.*
- *Si necesitas manejar zonas horarias, usa `DateTimeZone` para convertirla según la ubicación deseada.*
