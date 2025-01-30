<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# **En PHP, puedes usar la función `array_search()` para obtener el índice de un valor dentro de una lista (array). Esta función devuelve el índice del valor si se encuentra en el array, o `false` si no se encuentra.**

```php
array_search(mixed $needle, array $haystack, bool $strict = false): int|string|false
```

- **$needle:** *El valor que buscas.*
- **$haystack:** *El array en el que buscas.*
- **$strict (opcional):** *Si se establece en `true`, también compara el tipo de los valores (estricto).*

```php
$fruits = ["apple", "uva", "cherry", "date"];
$index = array_search("cherry", $fruits);

if ($index !== false) {
    echo "El índice de 'cherry' es: $index"; // Salida: El índice de 'cherry' es: 2
} else {
    echo "'cherry' no se encuentra en la lista.";
}
```

## ***Ejemplo con comparación estricta***

```php
$numbers = [10, "10", 20];
$index = array_search(10, $numbers, true);

if ($index !== false) {
    echo "El índice de 10 es: $index"; // Salida: El índice de 10 es: 0
} else {
    echo "10 no se encuentra en la lista.";
}
```

### **Nota sobre el resultado**

- *Si el valor no está en el array, `array_search()` devuelve `false`. Para evitar confundir esto con un índice válido (como `0`), siempre es una buena práctica usar el operador `!==` para verificar el resultado.*

---

### ***Buscar múltiples índices***

- **Si el valor aparece varias veces y necesitas todos los índices, puedes usar `array_keys()`:**

```php
$array = ["apple", "uva", "cherry", "apple"];
$indices = array_keys($array, "apple");

print_r($indices); // Salida: [0, 3]
```

### **Conclusión**

- *Usa `array_search()` para obtener un solo índice.*
- *Usa `array_keys()` si quieres encontrar todos los índices de un valor en el array.*
