<!-- Author: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Email: danielperezdev@proton.me -->

# **Usar expresiones regulares**

- *Si quieres verificar si una cadena contiene caracteres que no son letras ni números (es decir, caracteres especiales), puedes utilizar expresiones regulares. En PHP, puedes usar la función `preg_match` para esto.*

```php
$string = "Hello @world!";

if (preg_match('/[^a-zA-Z0-9]/', $string)) {
    echo "La cadena contiene caracteres especiales.";
} else {
    echo "La cadena no contiene caracteres especiales.";
}
```

## Explicación

- `/[^a-zA-Z0-9]/` *es una expresión regular que coincide con cualquier carácter que **no** sea una letra mayúscula (`A-Z`), una letra minúscula (`a-z`), o un número (`0-9`).*
- *Si la cadena contiene cualquier otro carácter, la función `preg_match` devuelve `true`.*

### **Verificar caracteres específicos**

- *Si sabes qué caracteres especiales específicos estás buscando (por ejemplo, comillas, barras invertidas, signos de exclamación, etc.), puedes hacer una verificación más específica. Aquí tienes un ejemplo que busca comillas simples, comillas dobles y barra invertida:*

```php
$string = "Hello 'world'";

$specialChars = ['\'', '"', '\\'];

foreach ($specialChars as $char) {
    if (strpos($string, $char) !== false) {
        echo "La cadena contiene un carácter especial: $char\n";
    }
}
```

- *`strpos($string, $char)` busca la posición de un carácter dentro de la cadena. Si encuentra el carácter, devuelve su posición; si no, devuelve `false`.*
- *Comprobamos cada uno de los caracteres en el arreglo `$specialChars` para ver si están presentes en la cadena.*

### **Usar `preg_match` para buscar caracteres específicos**

- *Si solo te interesa verificar la presencia de ciertos caracteres especiales (por ejemplo, `@`, `#`, `$`), puedes hacerlo con una expresión regular personalizada.*

```php
$string = "Hello @world#123";

if (preg_match('/[!@#$%^&*(),.?":{}|<>]/', $string)) {
    echo "La cadena contiene caracteres especiales.";
} else {
    echo "La cadena no contiene caracteres especiales.";
}
```

- *La expresión regular `[!@#$%^&*(),.?":{}|<>]` coincide con cualquier uno de los caracteres especiales listados.*
- *Si la cadena contiene alguno de estos caracteres, la función `preg_match` devolverá `true`.*

### **Verificar si la cadena contiene espacios**

*Si consideras que los **espacios** también son caracteres especiales, puedes comprobar su presencia con `strpos`:*

```php
$string = "Hello World";

if (strpos($string, ' ') !== false) {
    echo "La cadena contiene espacios.";
} else {
    echo "La cadena no contiene espacios.";
}
```

### **Verificar si la cadena contiene caracteres no alfanuméricos**

*Si solo quieres verificar si la cadena contiene algo que no sea alfanumérico (letras y números), puedes usar `ctype_alnum`:*

```php
$string = "Hello@123";

if (!ctype_alnum($string)) {
    echo "La cadena contiene caracteres especiales.";
} else {
    echo "La cadena solo contiene letras y números.";
}
```

- *`ctype_alnum($string)` devuelve `true` si todos los caracteres de la cadena son alfanuméricos (letras o números). Si no es alfanumérico, devuelve `false`.*
