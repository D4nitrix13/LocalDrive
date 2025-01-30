<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# ***Error ocurre porque PostgreSQL no permite insertar múltiples comandos SQL (como `BEGIN`, `UPDATE` y `COMMIT`) dentro de un solo `prepare()` o `query()` cuando usas un `PDO` preparado, lo que se conoce como `batching` o ejecución de múltiples consultas en un solo llamado***

- **Para corregir esto, debes dividir las transacciones en dos consultas separadas, es decir, primero comienzas la transacción, luego realizas la actualización, y finalmente haces el `COMMIT`.**

- **Aquí te dejo un ejemplo de cómo hacerlo:**

## ***Usando transacciones con PDO***

```php
try {
    // Iniciar la transacción
    $connection->beginTransaction();

    // Preparar la consulta de actualización
    $statement = $connection->prepare("UPDATE notification SET visto = FALSE WHERE id_user_destinatario = :id_user");
    $statement->bindParam(':id_user', $id_user, PDO::PARAM_INT);

    // Ejecutar la consulta de actualización
    $statement->execute();

    // Confirmar los cambios (COMMIT)
    $connection->commit();
} catch (PDOException $e) {
    // Si hay un error, hacer rollback y mostrar el error
    $connection->rollBack();
    echo "Error: " . $e->getMessage();
}
```

### ***Explicación***

1. **`$connection->beginTransaction()`:** *Inicia la transacción.*
2. **`$statement->prepare(...)`:** *Prepara la consulta de actualización. Aquí estamos usando un `prepared statement` para evitar inyecciones SQL.*
3. **`$statement->execute()`:** *Ejecuta la consulta preparada.*
4. **`$connection->commit()`:** *Confirma la transacción, aplicando la actualización.*
5. **`catch (PDOException $e)`:** *Si ocurre algún error, se hace un **rollback** para revertir la transacción y se muestra el mensaje de error.*
