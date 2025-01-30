<!-- Author: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Email: danielperezdev@proton.me -->

# ***¿Dónde Se Encuentra Ese Fichero Temporal?***

- *El fichero subido se guarda en un directorio temporal del servidor, cuyo lugar depende de la configuración de PHP. Si necesitas encontrar o configurar esa ubicación, puedes verificar la opción `upload_tmp_dir` en el fichero `php.ini`, que especifica el directorio donde PHP guarda los Ficheros subidos temporalmente.*

## **¿Cómo Almacenar El Fichero Permanentemente?**

*Si quieres mover el fichero a una ubicación permanente en tu servidor, debes usar la función `move_uploaded_file()`*

## ***Ejemplo de guardar el fichero en una ubicación permanente***

```php
<?php
if (isset($_FILES["uploadedFile"]) && $_FILES["uploadedFile"]["error"] === UPLOAD_ERR_OK) {
    // Ruta temporal
    $tmp_name = $_FILES["uploadedFile"]["tmp_name"];
    
    // Ruta permanente donde quieres guardar el fichero
    $upload_dir = "uploads/";
    $file_name = $_FILES["uploadedFile"]["name"];
    $destination = $upload_dir . basename($file_name);
    
    // Mover el fichero a la ruta permanente
    if ( move_uploaded_file($tmp_name, $destination) ) echo "El fichero se ha guardado correctamente en: " . $destination;
    else echo "Error al mover el fichero.";

} else echo "Error al subir el fichero.";
?>
```

- **`move_uploaded_file($tmp_name, $destination)`:** *Mueve el fichero subido desde su ubicación temporal a una ubicación permanente.*
- **`$upload_dir`:** *Especifica el directorio donde deseas guardar el fichero en tu servidor. Asegúrate de que este directorio exista y tenga permisos de escritura.*

*Si la carpeta `uploads/` no existe, debes crearla en tu servidor y asegurarte de que tiene permisos de escritura, como `chmod 777` o los permisos adecuados según tu servidor.*
