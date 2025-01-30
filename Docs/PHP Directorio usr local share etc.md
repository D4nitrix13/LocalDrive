<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# **En el directorio (`/usr/local/etc/php`), no parece haber un fichero `php.ini` activo directamente, pero sí hay plantillas para configuraciones de desarrollo y producción**

- **[php.ini-development](php.ini-development "php.ini-development"):** *Configuración recomendada para entornos de desarrollo.*
- **[php.ini-production](php.ini-production "php.ini-production"):** *Configuración recomendada para entornos de producción.*

```bash
ls -lA
```

```bash
ls -lA
drwxr-xr-x root root 4.0 KB Fri Dec 20 21:33:30 2024 conf.d
.rw-r--r-- root root  67 KB Fri Dec 20 21:33:25 2024 php.ini-development
.rw-r--r-- root root  67 KB Fri Dec 20 21:33:25 2024 php.ini-production
```

```bash
tree -Cf
```

```bash
tree -Cf
.
|-- ./conf.d
|   `-- ./conf.d/docker-php-ext-sodium.ini
|-- ./php.ini-development
`-- ./php.ini-production

2 directories, 3 files
```

## **Pasos para configurar y activar `php.ini`**

1. **Selecciona la plantilla adecuada:**
   *Si estás en un entorno de desarrollo, copia `php.ini-development`. Si es un entorno de producción, copia `php.ini-production`.*

   ```bash
   cp php.ini-development php.ini
   ```

2. **Edita el nuevo fichero `php.ini`:**
   *Abre el fichero recién creado y ajusta las configuraciones necesarias para aceptar Ficheros de hasta 3 GB:*

   ```bash
   nano php.ini
   ```

   **Busca y modifica estas líneas:**

   ```ini
   upload_max_filesize = 10G
   post_max_size = 10G
   max_execution_time = 300
   max_input_time = 300
   memory_limit = 512M
   ```

3. **Guarda y aplica los cambios:**
   - *Guarda los cambios en el editor (`Ctrl+O` y luego `Ctrl+X` en `nano`).*
   - *Reinicia tu servidor PHP o contenedor para que tome los nuevos ajustes.*

4. **Verifica la configuración:**
   *Puedes usar un script PHP con `phpinfo()` o el siguiente comando en tu contenedor para confirmar que los valores han sido aplicados:*

   ```bash
   php -i | grep -E 'upload_max_filesize|post_max_size|memory_limit'
   ```

   ```bash
   memory_limit => 512M => 512M
   post_max_size => 3G => 3G
   upload_max_filesize => 3G => 3G
   ```

5. **Reinicia el servidor web:**
   **Dependiendo de tu configuración:**
   - *Apache:*

   ```bash
   service apache2 restart
   ```

   ```ini
   upload_max_filesize = 3G  ; Cambia este valor según tus necesidades
   post_max_size = 3G        ; Debe ser mayor o igual a `upload_max_filesize`
   max_execution_time = 300  ; Opcional, para permitir más tiempo de ejecución
   max_input_time = 300      ; Opcional, para permitir más tiempo de entrada
   memory_limit = 512M       ; Ajusta según lo necesario
   ```
