<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# ***El método `password_hash` con `PASSWORD_BCRYPT` es un algoritmo de hashing, no de encriptación. Esto significa que no puedes "desencriptarlo", ya que está diseñado para ser unidireccional: convertir una contraseña en un hash seguro que no puede revertirse a su valor original.***

- *En lugar de desencriptar, **verificas** que una contraseña dada coincide con un hash almacenado utilizando la función `password_verify`.*

---

## **1. Crear el Hash:**

```php
// Crear el hash de la contraseña al momento del registro
$hashedPassword = password_hash($_POST["password"], PASSWORD_BCRYPT);
```

### **2. Guardar el Hash en la Base de Datos:**

- **Almacena `$hashedPassword` en la base de datos junto con otros datos del usuario.**

---

#### **3. Verificar la Contraseña al Iniciar Sesión:**

**Cuando el usuario intenta iniciar sesión:**

```php
// Supongamos que $hashedPassword viene de la base de datos (Obtenido de la consulta)
$hashedPassword = $data_user['passwordUser'];

// La contraseña ingresada por el usuario
$passwordIngresada = $_POST["password"];

// Verificar si coincide
if (password_verify($passwordIngresada, $hashedPassword)) {
    echo "La contraseña es correcta.";
    // Aquí puedes iniciar la sesión u otorgar acceso
} else {
    echo "La contraseña es incorrecta.";
    // Manejar el error (por ejemplo, mostrar un mensaje de error)
}
```

---

### **¿Por qué no se puede desencriptar?**

- **Algoritmo unidireccional:** *`PASSWORD_BCRYPT` genera un hash que no contiene la información original de la contraseña. Incluso si dos usuarios ingresan la misma contraseña, los hashes generados serán diferentes debido a un proceso interno llamado "salting".*
  
- **Seguridad:** *Este diseño asegura que, incluso si el hash es comprometido, el atacante no puede revertirlo a la contraseña original.*

---

### **Resumen:**

- *No puedes desencriptar una contraseña hash. En su lugar, compara una contraseña ingresada con el hash almacenado usando `password_verify`. Esto asegura que tu aplicación sea más segura y cumpla con las buenas prácticas de manejo de contraseñas.*
