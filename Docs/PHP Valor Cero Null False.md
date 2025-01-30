<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# *En PHP, un valor `0` (cero) **no es lo mismo que `null`**. El valor `0` es un número válido y se considera "falsy", pero no es lo mismo que `null` o una variable no definida. Sin embargo, hay algunas distinciones importantes a tener en cuenta:*

1. **`0` no es igual a `null`,** *pero es considerado "falsy", es decir, en una evaluación booleana (`if`, `while`, etc.), `0` se considera como `false`.*

2. **`null` es un valor especial** *que representa la ausencia de valor o la ausencia de una variable.*

- *Para el caso específico que mencionas, si estás trabajando con un array que tiene un valor `0` (como en `["error"] => int(0)`), puedes usar una de las siguientes formas para verificar si el valor es `0`, `null` o vacío:*

## ***Verificar si el valor es `0`, `null` o vacío***

- *Si quieres comprobar si el valor es exactamente `0`, `null`, o vacío (por ejemplo, en el caso del error en un formulario o alguna condición similar), puedes usar:*

```php
if ( $array['error'] === null ) {
    // El valor es null
} elseif ($array['error'] === 0) {
    // El valor es 0
} elseif (empty($array['error'])) {
    // El valor es vacío (esto incluiría null, 0, una cadena vacía, etc.)
}
```

### **En un contexto de "falsy" valores**

- *Si deseas comprobar si el valor **es falsy** (lo que incluye `null`, `false`, `0`, `""`, etc.):*

```php
if ( !$array['error'] ) {
    // Esto capturará null, false, 0, "", etc.
}
```

### ***Comparación estricta (`===`) y no estricta (`==`)***

- **`===`:** *Compara el valor y el tipo, por lo que `0 === null` sería `false`.*
- **`==`:** *Compara solo el valor, convirtiendo ambos valores a un tipo común. Por ejemplo, `0 == null` sería `true`.*

### **Ejemplo completo**

```php
$array = [
    "error" => 0,
    "size" => 262
];

if ($array["error"] === null) {
    echo "Es null";
} elseif ($array["error"] === 0) {
    echo "Es 0";
} elseif (empty($array["error"])) {
    echo "Es vacío";
} else {
    echo "Tiene otro valor";
}
```

- *Este código te permitirá identificar si el valor es `null`, `0`, vacío o algo más.*

- **`null`** *representa la ausencia de un valor.*
- **`0`** *es un número que, aunque es "falsy", **no es igual a `null`**.*
- *Usar `===` asegura que estés comparando tanto el tipo como el valor de manera estricta.*
- *Si solo te interesa saber si un valor es "falsy", usa `empty()` o la negación `!`.*
