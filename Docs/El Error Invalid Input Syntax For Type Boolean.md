<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# **El Error Invalid Input Syntax For Type Boolean**

> [!WARNING]
> *El error **`Invalid input syntax for type boolean: ""`** indica que el parámetro `:shared_from_a_directory` se está enviando vacío (`''`) en lugar de un valor booleano (`true` o `false`).*

---

## **Solución es usar `bindValue()` con el tipo de dato correcto:**

```php
$statement->bindValue( ":shared_from_a_directory", false, PDO::PARAM_BOOL );
```

**Ejemplo completo:**

```php
$statement = $connection->prepare(
    "SELECT function_insert_data_shared_file(
        :id_user_property, :email_destinatario, :shared_from_a_directory, :path
    ) AS value"
);
$statement->bindValue( ":id_user_property", $_SESSION['user']['id'], PDO::PARAM_INT );
$statement->bindValue( ":email_destinatario", $emailDestinatario, PDO::PARAM_STR );
$statement->bindValue( ":shared_from_a_directory", false, PDO::PARAM_BOOL );
$statement->bindValue( ":path", addslashes($pathFile), PDO::PARAM_STR );
$statement->execute();
```

---

### **Resumen de soluciones**

- **Asegurar que el valor no sea `NULL` o una cadena vacía (`""`)**  
- **Usar `bindValue()` con `PDO::PARAM_BOOL` para pasar valores booleanos correctamente**  
