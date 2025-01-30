<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# *El **`&`** en la función `getDirContents($dir, &$results = array())` indica que el parámetro `$results` es **pasado por referencia**.*

## **¿Qué significa pasar por referencia?**

*Cuando un parámetro se pasa por referencia, cualquier cambio que se haga sobre ese parámetro dentro de la función afectará directamente la variable original fuera de la función, sin necesidad de devolverla explícitamente.*

- **En este caso:**
- **Por valor** *(sin `&`): Si pasas `$results` a la función, cualquier modificación de `$results` dentro de la función no afectaría la variable `$results` que está fuera de la función. La función necesitaría devolver el valor para actualizar la variable.*
- **Por referencia** *(con `&`): Cualquier cambio realizado sobre `$results` dentro de la función se reflejará directamente en la variable original fuera de la función.*

### **Ejemplo con y sin referencia**

#### **Sin referencia (por valor)**

```php
<?php

function exampleWithoutReference(int $var): void {
  $var = $var * 2;
  return;
}

$number = 5;
exampleWithoutReference($number);
echo $number;  // Salida: 5 (el valor original no cambia)
?>
```

#### **Con referencia**

```php
function exampleWithReference(int &$var): void {
    $var = $var * 2;
    return;
}

$number = 5;
exampleWithReference($number);
echo $number;  // Salida: 10 (el valor original cambia)
```

### **En tu código**

```php
function getDirContents($dir, &$results = array()) {
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $results[] = $path;
        } else if ($value != "." && $value != "..") {
            getDirContents($path, $results);
            $results[] = $path;
        }
    }

    return $results;
}
```

- *La función `getDirContents()` obtiene el contenido de un directorio de manera recursiva.*
- *El parámetro `$results` se pasa por referencia, lo que significa que el array `$results` se va llenando dentro de la función sin necesidad de devolverlo.*
- *En cada llamada recursiva, la función actualiza el mismo array `$results` y se va llenando con las rutas de los archivos y directorios.*

### **Resumen**

*El **`&`** permite que `$results` se actualice dentro de la función `getDirContents()` y que esos cambios sean reflejados fuera de la función, sin necesidad de devolver el array completo.*
