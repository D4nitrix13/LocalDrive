<!-- Author: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Email: danielperezdev@proton.me -->

# ***Ejemplo de uso de `urlencode() y urldecode()`***

```php
<?php
$encodedString = "Hello%20World%21"; // Cadena codificada
$decodedString = urldecode($encodedString); // Decodificar la cadena

echo $decodedString; // Salida: Hello World!
?>
```

- **`urlencode()`** *codifica caracteres especiales en una cadena para que sean seguros en una URL.*
- **`urldecode()`** *revierte el proceso de codificación, devolviendo la cadena original.*

```php
<?php
// Cadena con caracteres codificados
$encoded = "name%3DDaniel+Perez%26age%3D25";

// Decodificar
$decoded = urldecode($encoded);
echo $decoded; // Salida: name=Daniel Perez&age=25
?>
```
