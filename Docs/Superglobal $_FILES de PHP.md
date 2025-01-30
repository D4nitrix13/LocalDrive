<!-- Author: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Email: danielperezdev@proton.me -->

# **Superglobal `$_FILES` de PHP, que contiene información sobre el fichero subido mediante un formulario.**

1. **`name`:**
   - **Tipo:** *`string`*
   - **Descripción**: *El nombre original del fichero que se subió. Es el nombre que tenía en la computadora del usuario.*
   - **Ejemplo en tu caso:** *`"grubtheme.txt"`*

2. **`full_path`:**
   - **Tipo:** *`string`*
   - **Descripción**: *La ruta completa proporcionada por el navegador del usuario. En la mayoría de los casos coincide con el nombre del fichero, ya que los navegadores modernos no envían la ruta completa por razones de privacidad.*
   - **Ejemplo en tu caso:** *`"grubtheme.txt"`*

3. **`type`:**
   - **Tipo:** *`string`*
   - **Descripción**: *El tipo MIME del fichero, determinado por el navegador del usuario. Indica el tipo de contenido del fichero.*
   - **Ejemplo en tu caso:** *`"text/plain"` (fichero de texto plano)*

4. **`tmp_name`:**
   - **Tipo:** *`string`*
   - **Descripción**: *La ubicación temporal donde PHP almacena el fichero en el servidor después de recibirlo.*
   - **Nota**: *Esta ruta es válida solo durante la ejecución del script; el fichero debe ser movido a una ubicación permanente si deseas conservarlo.*
   - **Ejemplo en tu caso:** *`"/tmp/phps48n0gogpd7veV6Poxt"`*

5. **`error`:**
   - **Tipo:** *`int`*
   - **Descripción**: *Un código que indica si ocurrió un error durante la subida del fichero.*
   - **Valores posibles**: **
     - **`0`:** *No hubo errores.*
     - **`1`:** *El fichero excede el tamaño máximo permitido definido en `upload_max_filesize` en `php.ini`.*
     - **`2`:** *El fichero excede el tamaño máximo permitido por el atributo `MAX_FILE_SIZE` del formulario HTML.*
     - **`3`:** *El fichero se subió parcialmente.*
     - **`4`:** *No se subió ningún fichero.*
     - **`6`:** *Falta una carpeta temporal en el servidor.*
     - **`7`:** *Falló al escribir el fichero en el disco.*
     - **`8`:** *Una extensión de PHP detuvo la subida del fichero.*
   - **Ejemplo en tu caso:** *`0` (indica que no hubo errores)*

6. **`size`:**
   - **Tipo:** *`int`*
   - **Descripción**: *El tamaño del fichero en bytes.*
   - **Ejemplo en tu caso:** *`1457` (el fichero tiene 1457 bytes, aproximadamente 1.4 KB)*

```php
array(1) {
  ["uploadedFile"]=> array(6) {
   ["name"]=> string(13) "grubtheme.txt" // Nombre del fichero subido
   ["full_path"]=> string(13) "grubtheme.txt" // Ruta proporcionada por el navegador
   ["type"]=> string(10) "text/plain" // Tipo MIME (fichero de texto plano)
   ["tmp_name"]=> string(27) "/tmp/phps48n0gogpd7veV6Poxt" // Ruta temporal en el servidor
   ["error"]=> int(0) // Sin errores
   ["size"]=> int(1457) // Tamaño del fichero en bytes
  }
}
```

> [!IMPORTANT]
> **El fichero en `tmp_name` es temporal.** *Debes moverlo a un directorio seguro si deseas conservarlo, utilizando `move_uploaded_file()`.*

- **Validar siempre el fichero antes de procesarlo.** *Comprueba el tipo MIME, la extensión y el tamaño para evitar riesgos de seguridad.*
