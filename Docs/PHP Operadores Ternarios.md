<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# ***En PHP, puedes usar operadores ternarios para simplificar condiciones y asignaciones en una sola línea. La sintaxis básica es:***

```php
$resultado = (condición) ? value_true : value_false;
```

1. **Asignación básica:**

   ```php
   $edad = 18;
   $mensaje = ($edad >= 18) ? "Eres mayor de edad" : "Eres menor de edad";
   echo $mensaje; // Salida: Eres mayor de edad
   ```

2. **Uso dentro de una función:**

   ```php
   function verificarAcceso($usuario) {
      return ($usuario == "admin") ? "Acceso permitido" : "Acceso denegado";
   }
   echo verificarAcceso("admin"); // Salida: Acceso permitido
   ```

3. **Encadenamiento de ternarios:**

   ```php
   $numero = 5;
   $tipo = ($numero > 0) ? "Positivo" : (($numero < 0) ? "Negativo" : "Cero");
   echo $tipo; // Salida: Positivo
   ```

4. **Ternario para valores predeterminados:**

   ```php
   $nombre = $_GET['nombre'] ?? "Invitado";
   echo "Hola, " . $nombre; // Si no se pasa "nombre", muestra "Hola, Invitado"
   ```

5. **Imprimir directamente:**

   ```php
   echo ($activo) ? "Usuario activo" : "Usuario inactivo";
   ```

## **Notas**

- *PHP también tiene el **operador de fusión nula** (`??`) para comprobar si una variable está definida y no es nula:*

  ```php
  $valor = $variable ?? "Valor predeterminado";
  ```

- *Usa ternarios con moderación para evitar confusión en código complejo. Para condiciones más largas, es mejor usar `if-else` para mayor claridad.*
