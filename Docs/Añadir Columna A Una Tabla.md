<!-- Author: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Email: danielperezdev@proton.me -->

# ***Añadir Columna A Una Tabla***

```sql
ALTER TABLE users ADD COLUMN id BIGSERIAL;
```

## ***Definir la columna como clave primaria***

```sql
ALTER TABLE users ADD PRIMARY KEY (id);
```

### Verificación

*Ejecuta `\d users` nuevamente para asegurarte de que la columna `id` fue añadida correctamente y está configurada como clave primaria.*

### **Notas**

1. **BIGSERIAL:**
   - *Crea automáticamente una secuencia que se utiliza para rellenar los valores.*
   - *Es útil para identificar de manera única cada fila en la tabla.*

2. **PRIMARY KEY:**
   - *Marca la columna como identificador único y no permite valores `NULL`.*
   - *Implica automáticamente un índice único en la columna.*
