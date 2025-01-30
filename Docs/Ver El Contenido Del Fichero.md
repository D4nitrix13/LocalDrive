<!-- Author: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Email: danielperezdev@proton.me -->

# ***Para Ver El Contenido Del Fichero Que Se Subió En Tu Código Php, Puedes Usar La Función `File_Get_Contents()` En El Fichero Temporal Que Se Encuentra En `$_FILES['uploadedFile']['tmp_name']`.***

```php
<?php
// Verificar si el fichero fue subido correctamente
if ( isset($_FILES["uploadedFile"]) && $_FILES["uploadedFile"]["error"] === UPLOAD_ERR_OK ) {
    // Obtener la ruta temporal del fichero subido
    $tmp_name = $_FILES["uploadedFile"]["tmp_name"];
    
    // Leer el contenido del fichero
    $file_content = file_get_contents($tmp_name);
    
    // Mostrar el contenido del fichero (en este caso lo estamos mostrando con var_dump)
    var_dump($file_content);
} else {
    echo "Error al subir el fichero.";
}
?>
```

1. **`$_FILES["uploadedFile"]["tmp_name"]`:** *Esta es la ruta temporal donde PHP almacena el fichero subido.*
2. **`file_get_contents($tmp_name)`:** *Esta función lee el contenido del fichero en la ruta temporal.*
3. **`var_dump($file_content)`:** *Muestra el contenido del fichero. Si es un fichero de texto como `main.py`, podrás ver el contenido en formato texto.*
