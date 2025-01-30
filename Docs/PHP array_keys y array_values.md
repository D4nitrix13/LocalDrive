<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# ***En PHP, puedes obtener las claves, los valores o ambos de un diccionario (array asociativo) utilizando funciones integradas como `array_keys`, `array_values` y trabajando directamente con el array. Aquí te muestro cómo hacerlo:***

## ***Obtener solo las claves (`keys`)***

**Usa la función `array_keys`:**

```php
<?php
$diccionario = [
    "nombre" => "Daniel",
    "edad" => 25,
    "pais" => "Nicaragua"
];

$keys = array_keys($diccionario);
print_r($keys);
?>
```

**Resultado:**

```bash
Array
(
    [0] => nombre
    [1] => edad
    [2] => pais
)
```

### ***Obtener solo los valores (`values`)***

**Usa la función `array_values`:**

```php
<?php
$valores = array_values($diccionario);
print_r($valores);
?>
```

**Resultado:**

```bash
Array
(
    [0] => Daniel
    [1] => 25
    [2] => Nicaragua
)
```

### ***Obtener las claves y los valores***

**Puedes recorrer el array con un `foreach` o trabajar con el array directamente:**

```php
<?php
foreach ($diccionario as $key => $value) {
    echo "Clave: $key, Valor: $value\n";
}
?>
```

**Resultado:**

```bash
Clave: nombre, Valor: Daniel
Clave: edad, Valor: 25
Clave: pais, Valor: Nicaragua
```

**Alternativamente, podrías convertir el array en pares de clave y valor:**

```php
<?php
$diccionario = [];
foreach ($diccionario as $key => $value) {
    $diccionario[] = ["clave" => $key, "valor" => $value];
}
print_r($diccionario);
?>
```
