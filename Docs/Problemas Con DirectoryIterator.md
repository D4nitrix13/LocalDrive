<!-- Author: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Email: danielperezdev@proton.me -->

# **Problemas Con DirectoryIterator**

*Cuando se utiliza `DirectoryIterator`, puede haber problemas al manejar rutas que contienen caracteres especiales, como espacios, caracteres acentuados o símbolos no ASCII. Esto ocurre porque `DirectoryIterator` depende de que la ruta proporcionada sea válida y no siempre maneja correctamente codificaciones o caracteres no estándar.*

*Por otro lado, `scandir` es una función más robusta para estos casos, ya que devuelve un arreglo simple con los nombres de los Ficheros y directorios dentro del directorio especificado, sin depender de objetos iterables. Esto permite evitar errores relacionados con caracteres especiales.*

**Ejemplo de código funcional con `scandir`:**

```php
$directoryPath = scandir(urldecode($_SESSION["shareDirectory"]));
foreach ($directoryPath as $index => $entry) {
    if ($entry === '.' || $entry === '..') continue;
    var_dump($entry);
}
```

## **Solución**

- *Si la ruta del directorio o los nombres de los Ficheros contienen caracteres especiales, se recomienda utilizar `scandir` en lugar de `DirectoryIterator` para garantizar compatibilidad y evitar errores inesperados.*
