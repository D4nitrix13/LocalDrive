<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# ***Comandos Ejecutar***

- *[Bootstrapcdn/](https://www.bootstrapcdn.com/ "https://www.bootstrapcdn.com/")*
- *[Cdnjs](https://cdnjs.com/ "https://cdnjs.com/")*
- *[Cdnjs libraries Bootswatch](https://cdnjs.com/libraries/bootswatch "https://cdnjs.com/libraries/bootswatch")*

```bash
docker container run \
   -itw/App \
   -p80:8080 \
   --expose 8080 \
   --stop-signal SIGTERM \
   --stop-timeout 10 \
   --init \
   -u0:0 \
   --privileged \
   -aSTDIN -aSTDOUT -aSTDERR \
   --mount type=bind,source="$(pwd)",target=/App \
   --network bridge \
   --restart unless-stopped \
   --platform linux/amd64 \
   --dns 8.8.8.8 \
   --name LocalDrive \
   php:latest \
   /usr/bin/bash -lp
```

## **`-l` (login shell)**

- *La opción `-l` indica que Bash debe actuar como un **shell de inicio de sesión** (login shell). Un shell de inicio de sesión es el tipo de shell que se inicia cuando un usuario inicia sesión en el sistema (por ejemplo, al acceder a través de una terminal o SSH).*

- *En este caso, Bash leerá los Ficheros de configuración específicos de inicio de sesión, como `~/.bash_profile`, `~/.bashrc`, etc.*
- *Se usa para asegurarse de que el entorno del usuario (variables de entorno, configuraciones de usuario) se cargue correctamente.*

   **Ejemplo:** *`bash -l` lanza un shell de inicio de sesión.*

### **`-p` (privileged)**

- *La opción `-p` de Bash se usa para deshabilitar el **procesamiento de Ficheros de inicio**. Esto significa que cuando usas `bash -p`, Bash no leerá los Ficheros de configuración del usuario, como `.bashrc` o `.bash_profile`. Esto es útil si deseas iniciar un shell sin aplicar configuraciones personalizadas.*

- *Se usa cuando quieres un entorno limpio sin las configuraciones del usuario.*

   **Ejemplo:** *`bash -p` inicia un shell sin procesar los Ficheros de configuración.*

### ***Resumen***

- **`-l`:** *Inicia un shell de inicio de sesión (lee los Ficheros de configuración como `.bash_profile`).*
- **`-p`:** *Desactiva el procesamiento de los Ficheros de inicio de Bash.*

- *`passwd -d d4nitrix13`: Esto elimina la contraseña del usuario d4nitrix13, permitiendo iniciar sesión sin necesidad de contraseña.*
- *`su - d4nitrix13`: Cambia al usuario d4nitrix13 después de crearlo, y ahora no pedirá contraseña.*

- *Usar `--user` para especificar un usuario existente: La flag `--user` permite ejecutar procesos dentro del contenedor como un usuario específico (ya sea un nombre de usuario o un UID)*
- *Esto ejecuta el contenedor como el usuario con UID 1000 y GID 1000 (`-u1000:1000`, `-u 1000:1000`, `--user 1000:1000`).*
- *root UID 0 y GID 0 (`-u0:0`, `-u 0:0`, `--user 0:0`).*
- *`--stop-timeout` en Docker se utiliza para especificar el tiempo de espera (en segundos) que Docker da a un contenedor para detenerse de forma ordenada antes de forzar su terminación.*

```bash
php -S 0.0.0.0:8080
```

```bash
d4nitrix13@b1b1eaf215ab:/App$ php -S 0.0.0.0:8080
[Sat Dec 21 22:49:38 2024] PHP 8.4.2 Development Server (http://0.0.0.0:8080) started
```

```bash
php -S 0.0.0.0:8080 &>/dev/null & disown
```

```bash
useradd -ms /usr/bin/bash d4nitrix13
```

```bash
docker container exec --interactive --tty --user root:root --privileged LocalDrive /usr/bin/bash -lp
```
