<!-- Author: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Email: danielperezdev@proton.me -->

# *El valor que viene por `$_GET["id"]` sea un **string**, mientras que el valor en `$_SESSION["user"]["id"]` sea un **integer**. Esto es debido a cómo PHP maneja los datos enviados a través de `$_GET`. Los parámetros en las URLs siempre se reciben como cadenas de texto*

## **Diferencia entre `==` y `===` en PHP**

- *`==` (doble igual) compara los valores después de realizar una conversión de tipo si es necesario. Por ejemplo:*

  ```php
  var_dump(3 == "3"); // true
  ```

- *`===` (triple igual) compara tanto el valor como el tipo, es decir, ambos deben ser iguales y del mismo tipo. Por ejemplo:*

  ```php
  var_dump(3 === "3"); // false
  ```

### **Solución**

- *Para evitar este problema, puedes convertir explícitamente el valor de `$_GET["id"]` al tipo entero antes de compararlo con `$_SESSION["user"]["id"]`:*

```php
var_dump((integer)$_SESSION["user"]["id"] === (int)$_GET["id"]);
```

### **Alternativa**

**Si necesitas asegurarte de que ambos valores sean del mismo tipo antes de comparar, verifica los tipos antes:**

```php
if (is_numeric($_GET["id"]) && (integer)$_SESSION["user"]["id"] === (int)$_GET["id"]) {
  echo "Los valores son iguales.";
} else {
  echo "Los valores son diferentes.";
}
```

### **Depuración**

**Si quieres confirmar los tipos de las variables, usa `var_dump()` para inspeccionar el valor y el tipo:**

```php
var_dump($_SESSION["user"]["id"], $_GET["id"]);
```

**Esto imprimirá algo como:**

```bash
int(3)
string(1) "3"
```
