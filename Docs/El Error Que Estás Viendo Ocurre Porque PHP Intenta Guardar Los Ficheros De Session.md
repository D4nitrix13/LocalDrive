<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# **El Error Que Estás Viendo Ocurre Porque PHP Intenta Guardar Los Ficheros De Session**

```bash
[Thu Jan 23 23:42:15 2025] PHP Warning:  session_start(): open(/App/LocalDrive/sessions/sess_ce38242aae9a76f07615fbebfa0c7fd2, O_RDWR) failed: No such file or directory (2) in /App/home.php on line 2
[Thu Jan 23 23:42:15 2025] PHP Warning:  session_start(): Failed to read session data: files (path: /App/LocalDrive/sessions) in /App/home.php on line 2
```

> [!WARNING]
> **El error que estás viendo ocurre porque PHP intenta guardar los archivos de sesión en un directorio especificado (`/App/LocalDrive/sessions`), pero dicho directorio ha sido eliminado o no existe.**

---

## **Solución: Crear el directorio y configurar los permisos**

1. **Crear el directorio que falta**  
   **Puedes crear nuevamente el directorio usando el comando `mkdir`:**

   ```bash
   mkdir -p /App/LocalDrive/sessions
   ```

   - **`-p`:** *Crea todos los directorios necesarios en la ruta si no existen.*

2. **Establecer los permisos adecuados**  
   *Asegúrate de que PHP pueda escribir y leer en ese directorio. Normalmente, esto implica dar permisos de lectura y escritura al usuario que ejecuta el servidor web (por ejemplo, `www-data` en servidores Apache o Nginx):*

   ```bash
   chmod 700 /App/LocalDrive/sessions
   chown www-data:www-data /App/LocalDrive/sessions
   ```

   - **`chmod 700`:** *Solo el propietario tendrá acceso.*
   - **`chown`:** *Cambia el propietario del directorio al usuario y grupo del servidor web.*

---

### **Configurar el directorio de sesiones en PHP**

*Asegúrate de que el directorio de sesiones esté configurado correctamente en el archivo `php.ini`. Busca o añade esta línea:*

```ini
session.save_path = "/App/LocalDrive/sessions"
```

- *Si ya existe una configuración anterior para `session.save_path`, cámbiala para que apunte a `/App/LocalDrive/sessions`.*
- *Si no tienes acceso para modificar `php.ini`, puedes configurar esto dinámicamente en tu aplicación PHP:*

   ```php
   ini_set('session.save_path', '/App/LocalDrive/sessions');
   session_start();
   ```

---

### **Comprobación del problema**

*Después de realizar los pasos anteriores, prueba nuevamente tu aplicación. Para asegurarte de que los archivos de sesión están siendo creados correctamente, ejecuta:*

```bash
ls -l /App/LocalDrive/sessions
```

**Deberías ver los archivos de sesión (nombres que empiezan con `sess_`).**

---

### **¿Qué hacer si el problema persiste?**

1. **Verifica los logs de PHP**  
   *Si los pasos anteriores no solucionan el problema, revisa los mensajes de error en los logs de PHP. Esto te ayudará a identificar si hay permisos incorrectos o algún problema en el código.*

2. **Verifica el archivo de configuración cargado**  
   *Asegúrate de que PHP está utilizando el archivo de configuración correcto. Ejecuta el siguiente comando para ver qué `php.ini` se está usando:*

   ```bash
   php --ini
   ```

### **Explicación de las diferencias entre la configuración con y sin archivo `php.ini` cargado**

- **Cuando ejecutas el comando `phpinfo()` o revisas la salida de la configuración de PHP, puedes observar información relacionada con el archivo de configuración `php.ini`, que es crucial para la configuración de PHP y sus extensiones. Veamos las dos situaciones que mencionas y qué significan.**

---

### **1. Cuando **no** hay archivo `php.ini` cargado**

**Salida:**

```bash
Configuration File (php.ini) Path: /usr/local/etc/php
Loaded Configuration File:         (none)
Scan for additional .ini files in: /usr/local/etc/php/conf.d
Additional .ini files parsed:      /usr/local/etc/php/conf.d/docker-php-ext-pdo_pgsql.ini,
/usr/local/etc/php/conf.d/docker-php-ext-sodium.ini
```

#### **Interpretación**

- **`Configuration File (php.ini) Path: /usr/local/etc/php`:** *Esto indica la ruta donde PHP buscaría el archivo `php.ini` por defecto, si existiera.*
- **`Loaded Configuration File: (none)`:** *Este es el aspecto clave. Significa que PHP **no está cargando un archivo `php.ini` principal**. Esto puede ocurrir si:*
  - *El archivo `php.ini` no existe en la ruta configurada o no ha sido creado.*
  - *PHP no tiene acceso al archivo `php.ini` debido a restricciones de permisos.*
  - *El archivo `php.ini` ha sido deshabilitado o no está configurado correctamente en el entorno de PHP (por ejemplo, en contenedores Docker, algunas configuraciones pueden estar ausentes por defecto).*
  
- **`Scan for additional .ini files in: /usr/local/etc/php/conf.d`:** *PHP buscará archivos de configuración adicionales en el directorio `/usr/local/etc/php/conf.d/`. Los archivos que encuentre aquí **se cargarán**, pero no serán considerados un archivo `php.ini` principal.*
  
- **`Additional .ini files parsed:`:** *Aunque el archivo `php.ini` no está presente, PHP está cargando algunos archivos de configuración adicionales (en este caso, para extensiones de PDO PostgreSQL y Sodium). Estos archivos `.ini` no reemplazan al `php.ini` principal, pero pueden modificar configuraciones específicas de extensiones.*

#### **Posibles Soluciones**

- *Si necesitas configurar PHP y no hay archivo `php.ini`, debes crear uno en la ruta indicada (`/usr/local/etc/php`), ya sea copiando un archivo de configuración por defecto o creando uno desde cero.*

  ```bash
  cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini
  ```

---

### **2. Cuando el archivo `php.ini` está cargado**

**Salida:**

```bash
Configuration File (php.ini) Path: /usr/local/etc/php
Loaded Configuration File:         /usr/local/etc/php/php.ini
Scan for additional .ini files in: /usr/local/etc/php/conf.d
Additional .ini files parsed:      /usr/local/etc/php/conf.d/docker-php-ext-pdo_pgsql.ini,
/usr/local/etc/php/conf.d/docker-php-ext-sodium.ini
```

- **Interpretación**

- **`Configuration File (php.ini) Path: /usr/local/etc/php`:** *Igual que antes, esta es la ruta donde PHP busca el archivo `php.ini`.*
- **`Loaded Configuration File: /usr/local/etc/php/php.ini`:** *Aquí, PHP **sí está cargando un archivo `php.ini`** principal desde la ruta especificada. Este archivo es el que contiene la configuración general de PHP, como la configuración de sesiones, rutas de archivos temporales, configuraciones de base de datos, entre otras.*
- **`Scan for additional .ini files in: /usr/local/etc/php/conf.d`:** *Al igual que en el caso anterior, PHP buscará archivos de configuración adicionales en el directorio `/usr/local/etc/php/conf.d/`, y los archivos que encuentre se cargarán.*
- **`Additional .ini files parsed:`:** *Además del archivo `php.ini`, PHP está cargando archivos de configuración adicionales (en este caso, los archivos de extensión `pdo_pgsql` y `sodium`).*

#### **¿Por qué es importante que `php.ini` esté cargado?**

*El archivo `php.ini` es esencial para la configuración global de PHP, y debe ser correctamente cargado para que puedas configurar aspectos como:*

- **Configuración de sesiones** *(por ejemplo, la ruta de almacenamiento de sesiones, como `session.save_path`).*
- **Configuración de extensiones** *(como PDO, MySQL, etc.).*
- **Control de errores y registros.**
- **Ajustes de rendimiento** *(como los límites de memoria y tiempo de ejecución).*

*Si el archivo `php.ini` está cargado correctamente, puedes modificarlo para personalizar tu entorno de PHP según tus necesidades.*

---

### **Resumen y Soluciones**

- **Sin archivo `php.ini` cargado:**
  - *PHP no está utilizando un archivo `php.ini` principal. Necesitas crear o restaurar ese archivo en la ruta indicada (`/usr/local/etc/php/php.ini`).*
  - -*Mientras tanto, PHP cargará algunos archivos de configuración específicos de extensiones, pero no podrás hacer configuraciones globales personalizadas.*

- **Con archivo `php.ini` cargado:**
  - *PHP está utilizando un archivo `php.ini` para configuraciones globales, y puedes modificarlo para ajustar el comportamiento de PHP en tu servidor.*

---

### **Consideraciones adicionales**

- **Usar rutas absolutas:** *Siempre usa rutas completas en `session.save_path` para evitar problemas con rutas relativas.*
- **Directorio temporal alternativo:** *Si no puedes crear `/App/LocalDrive/sessions`, puedes usar un directorio temporal alternativo, como `/tmp`, configurándolo en `php.ini` o con `ini_set()`.*
