<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# ***Para Añadir, Eliminar, Actualizar Y Filtrar Elementos De Una Lista En Php, Puedes Utilizar Funciones Y Estructuras Básicas De Arrays. Aquí Hay Ejemplos Que Cubren Cada Caso:***

## **Añadir Elementos a una Lista**

**Puedes usar `array_push()` o simplemente asignar un valor al final del array.**

```php
$list = ["apple", "uva", "cherry"];
// Añadir un elemento
$list[] = "date"; // Método directo
array_push($list, "orange"); // Usando array_push

print_r($list);
```

## **Eliminar Elementos de una Lista**

- **Puedes usar `unset()` para eliminar elementos por índice o `array_diff()` para eliminar valores específicos.**

```php
$list = ["apple", "uva", "cherry"];
// Eliminar por índice
unset($list[1]); // Elimina "uva"
$list = array_values($list); // Reindexar el array

// Eliminar por valor
$list = array_diff($list, ["cherry"]); // Elimina "cherry"

print_r($list);
```

## **Actualizar Elementos en una Lista**

**Para actualizar un elemento, accede directamente por índice.**

```php
$list = ["apple", "uva", "cherry"];
// Actualizar el segundo elemento
$list[1] = "blueberry"; // Cambia "uva" a "blueberry"

print_r($list);
```

## **Filtrar Elementos de una Lista**

- **Usa `array_filter()` para aplicar una función de filtrado.**

```php
$list = ["apple", "uva", "cherry"];
// Filtrar elementos que contengan la letra 'a'
$filtered = array_filter($list, function($item) {
    return strpos($item, 'a') !== false;
});

print_r($filtered);
```

## ***Completo: Operaciones en una Lista***

```php
$list = ["apple", "uva", "cherry"];

// Añadir elementos
$list[] = "date"; 
array_push($list, "orange");

// Eliminar por índice
unset($list[1]); // Elimina "uva"
$list = array_values($list); // Reindexar

// Actualizar un elemento
$list[1] = "blueberry"; // Cambia "cherry" a "blueberry"

// Filtrar elementos que contengan la letra 'e'
$filtered = array_filter($list, function($item) {
    return strpos($item, 'e') !== false;
});

print_r($list);
print_r($filtered);
```

```bash
Array
(
    [0] => apple
    [1] => blueberry
    [2] => orange
)
Array
(
    [1] => blueberry
    [2] => orange
)
```
