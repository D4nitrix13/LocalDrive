<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/D4nitrix13 -->
<!-- GitLab: https://gitlab.com/D4nitrix13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# **LocalDrive**

- **Este sistema de almacenamiento diseñado para gestionar y compartir ficheros y directorios de manera segura, eficiente y personalizada. Este proyecto utiliza PHP y PostgreSQL, con Docker para un despliegue y configuración simples.**

## ***Tecnologías Utilizadas***

- **Este proyecto no utiliza frameworks para el backend, lo que significa que ha sido desarrollado con PHP puro. La única dependencia adicional es un cliente para conectarse a la base de datos PostgreSQL. Durante el desarrollo, se han aprendido y aplicado los siguientes conceptos:**
**Bootstrap:** *Diseño y estilización del frontend.*
**Linux:** *Configuración y administración del entorno de desarrollo y despliegue.*
**Docker y Docker Compose:** *Creación y manejo de contenedores para una implementación aislada y eficiente.*
**Protocolo HTTP:** *Uso de los métodos GET y POST para la interacción cliente-servidor.*
**Protocolos TCP y UDP:** *Comprensión de la comunicación en red y gestión de puertos.*
**Protocolo PostgreSQL:** *Manejo de consultas y configuración de la base de datos.*
**Redes:** *Configuración de puertos, hosts y conectividad entre contenedores.*
**CDN (cdnjs):** *Uso de recursos externos para optimizar el rendimiento y reducir la latencia.*
**Cabeceras de petición y respuesta:** *Gestión adecuada de información en las solicitudes y respuestas HTTP.*
**Cookies y sesiones:** *Implementación y manejo de autenticación y almacenamiento de datos temporales del usuario.*

## ***Características Principales***

- **Autenticación de usuarios:** *Sistema seguro de login.*
- **Preferencias de tema:** *Soporte para tema claro y oscuro mediante cookies.*
- **Gestión de ficheros y directorios:**
  - *Subir, actualizar, visualizar, descargar y eliminar ficheros.*
  - *Crear directorios y subdirectorios.*
  - *Compartir ficheros y directorios con otros usuarios.*
- **Notificaciones:** *Sistema para alertar a los usuarios sobre eventos relevantes.*
- **Límites de almacenamiento:** *Restricción de uso a 10GB por usuario.*
- **Base de datos optimizada:**
  - *Tablas y relaciones definidas con claves primarias y foráneas.*
  - *Uso de triggers, funciones y procedimientos almacenados para encapsular la lógica.*
  - *Historial de actualizaciones de correos electrónicos mediante triggers.*
- **Ajustes avanzados:**
  - *Configuración personalizada de `php.ini` para manejar ficheros grandes (hasta 3GB).*
  - *Rutas de sesión personalizadas.*
  - *Aislamiento de contenedores Docker mediante redes dedicadas.*

## **Requisitos**

- **PHP:** *Versión >= `8.4.2`.*
- **PostgreSQL:** *Versión >= `17.2`.*
- **Docker:** *Docker version >= `27.4.0`, build bde2b89.*
- **Bootstrap:** *Version >= `5.3.3`.*
- **Extensiones de PHP:**
  - `pdo_pgsql`

---

## **Instalación y Configuración**  

### **Definir la contraseña de la base de datos de forma segura**  

*Para establecer la contraseña del usuario de la base de datos PostgreSQL, sigue estos pasos:*

---

### **1. Crear el directorio de secretos**  

**Ejecuta el siguiente comando para crear un directorio donde almacenaremos la contraseña:**

```bash
mkdir -p secrets
```  

**Explicación:**  

- **`mkdir -p secrets`:**
  - **`mkdir`:** *Crea un directorio.*
  - **`-p`:** *Evita errores si el directorio ya existe y crea subdirectorios si es necesario.*
  - **`secrets`:** *Nombre del directorio donde guardaremos las credenciales.*

---

### **2. Crear el archivo de contraseña**  

**Dentro del directorio `secrets`, crearemos un archivo que contendrá la contraseña:**

```bash
touch secrets/POSTGRES_PASSWORD.txt
```  

**Explicación:**  

- **`touch secrets/POSTGRES_PASSWORD.txt`:**
  - **`touch`:** *Crea un archivo vacío si no existe o actualiza su fecha de modificación si ya existe.*
  - **`secrets/POSTGRES_PASSWORD.txt`:** *Ruta y nombre del archivo donde se guardará la contraseña.*

---

### **3. Definir la contraseña en el archivo**  

*Para escribir la contraseña "root" en el archivo, ejecuta:*

```bash
echo "root" > secrets/POSTGRES_PASSWORD.txt
```  

**Explicación:**  

- **`echo "root"`:** *Muestra el texto `"root"` en la terminal.*
- **`>`:** *Redirige la salida al archivo, sobrescribiendo cualquier contenido anterior.*
- **`secrets/POSTGRES_PASSWORD.txt`:** *Archivo donde se guardará la contraseña.*

*Si quieres evitar que la contraseña sea visible en el historial del terminal, usa:*

```bash
bash -c 'read -s -p "Ingrese la contraseña: " password && echo "$password" > secrets/POSTGRES_PASSWORD.txt'
```  

*Esto pedirá la contraseña de forma oculta y la guardará en el archivo sin mostrarla en pantalla.*

### **Explicación detallada:**  

1. **`bash -c '...comando...'`**  
   - *Ejecuta el comando dentro de una nueva instancia de `bash`.*
   - *Esto es útil si estás usando una shell diferente (como `zsh` o `fish`), donde `read -p` no funciona correctamente.*

2. **`read -s -p "Ingrese la contraseña: " password`**  
   - **`read`:** *Permite al usuario ingresar un valor desde la terminal.*
   - **`-s`:** *Oculta la entrada del usuario para mayor seguridad (no muestra los caracteres escritos).*
   - **`-p "Ingrese la contraseña: "`:** *Muestra el mensaje de solicitud antes de leer la entrada.*
   - **`password`:** *Almacena la contraseña ingresada en la variable `password`.*

3. **`&&` (Operador lógico AND)**  
   - *Asegura que el siguiente comando (`echo "$password" > secrets/POSTGRES_PASSWORD.txt`) solo se ejecute si el `read` se ejecutó correctamente.*

4. **`echo "$password" > secrets/POSTGRES_PASSWORD.txt`**  
   - **`echo "$password"`:** *Muestra el contenido de la variable `password` (la contraseña ingresada).*
   - **`>`:** *Redirige la salida al archivo `secrets/POSTGRES_PASSWORD.txt`, sobrescribiendo su contenido si ya existe.*

---

### **¿Para qué sirve este comando?**  

- *Permite ingresar una contraseña de forma segura sin que se muestre en pantalla.*
- *Guarda la contraseña en un archivo para que pueda ser utilizada por otras aplicaciones, como Docker o PostgreSQL.*
- *Evita que la contraseña quede en el historial del terminal, mejorando la seguridad.*

---

### **Alternativa sin `bash -c` (si ya estás en `bash`)**  

*Si ya estás en `bash`, puedes ejecutar directamente:*

```bash
read -s -p "Ingrese la contraseña: " password && echo "$password" > secrets/POSTGRES_PASSWORD.txt
```  

*Si usas `zsh` o `fish`, usa esta versión:*

```bash
echo -n "Ingrese la contraseña: " && read -s password && echo "$password" > secrets/POSTGRES_PASSWORD.txt
```  

---

### **4. Verificar que la contraseña se guardó correctamente**  

*Puedes revisar el contenido del archivo con:*

```bash
cat secrets/POSTGRES_PASSWORD.txt
```  

**Nota:** *Evita compartir este archivo o subirlo a repositorios públicos para mantener la seguridad. También puedes usar `.gitignore` para excluirlo del control de versiones.*

---

## **Script de Gestión – `localdrive.sh`**

Para facilitar la administración del proyecto **LocalDrive**, se incluye un script que permite **levantar, detener, reiniciar, eliminar e inicializar** la aplicación completa con un solo comando.

Este script automatiza los comandos de Docker Compose, la creación de secretos y la inicialización de la base de datos.

---

## **📍 1. Dar permisos de ejecución**

Antes de usarlo, asegúrate de darle permisos de ejecución:

```bash
chmod +x localdrive.sh
```

---

## **2. Comandos disponibles**

| Comando                  | Descripción                                                                                      |
| ------------------------ | ------------------------------------------------------------------------------------------------ |
| `./localdrive.sh init`   | Crea el secreto de PostgreSQL si no existe, levanta los servicios e inicializa la base de datos. |
| `./localdrive.sh up`     | Construye y levanta los contenedores en segundo plano.                                           |
| `./localdrive.sh stop`   | Detiene los contenedores sin eliminarlos.                                                        |
| `./localdrive.sh start`  | Inicia nuevamente los contenedores previamente detenidos.                                        |
| `./localdrive.sh down`   | Apaga y elimina contenedores, redes, volúmenes e imágenes locales.                               |
| `./localdrive.sh db`     | Ejecuta únicamente el script SQL de inicialización de base de datos.                             |
| `./localdrive.sh status` | Muestra el estado actual de los contenedores del proyecto.                                       |

---

## **📍 3. Flujo recomendado (primera vez)**

Ejecuta:

```bash
./localdrive.sh init
```

Este comando:

1. Verifica o crea el archivo `secrets/POSTGRES_PASSWORD.txt`
2. Levanta los servicios con Docker
3. Inicializa la base de datos ejecutando `/App/sql/setup.sql`

---

## **📍 4. Ejemplos de uso diario**

**Levantar la aplicación:**

```bash
./localdrive.sh up
```

**Detenerla:**

```bash
./localdrive.sh stop
```

**Reiniciar contenedores ya existentes:**

```bash
./localdrive.sh start
```

**Ver el estado:**

```bash
./localdrive.sh status
```

**Eliminar todo (contenedores + volúmenes + imágenes locales):**

```bash
./localdrive.sh down
```

**Reinicializar base de datos manualmente:**

```bash
./localdrive.sh db
```

---

### **1. Clonar el repositorio**  

*Para obtener el código fuente del proyecto, clona el repositorio desde GitHub con el siguiente comando:*

```bash
git clone git@github.com:D4nitrix13/LocalDrive.git --depth=1 --verbose --ipv4 --progress ~/LocalDrive
cd ~/LocalDrive
```  

**Explicación:**  

- **`git clone`:** *Descarga el repositorio de GitHub.*
- **`--depth=1`:** *Solo clona el último commit para reducir el tamaño de la descarga.*
- **`--verbose`:** *Muestra detalles del proceso de clonación.*
- **`--ipv4`:** *Fuerza el uso de IPv4 para evitar problemas de conectividad.*
- **`--progress`:** *Muestra el progreso de la clonación.*
- **`cd ~/LocalDrive`:** *Accede al directorio del proyecto.*

---

### **2. Levantar los contenedores con Docker**  

*Ejecuta el siguiente comando para construir y levantar los servicios del proyecto:*

```bash
docker compose --profile dev --file docker-compose.yaml --project-name localdrive --project-directory "$(pwd)" up --build --timestamps --remove-orphans --pull always --timeout 10 --detach
```  

**Explicación:**  

- **`docker compose`:** *Ejecuta Docker Compose, herramienta para gestionar contenedores.*
- **`--profile dev`:** *Usa el perfil de desarrollo definido en `docker-compose.yaml`.*
- **`--file docker-compose.yaml`:** *Especifica el archivo de configuración de los contenedores.*
- **`--project-name localdrive`:** *Define el nombre del proyecto en Docker.*
- **`--project-directory "$(pwd)"`:** *Usa el directorio actual como directorio del proyecto.*
- **`up`:** *Levanta los servicios definidos en `docker-compose.yaml`.*
- **`--build`:** *Fuerza la construcción de las imágenes antes de iniciar los contenedores.*
- **`--timestamps`:** *Muestra marcas de tiempo en los logs.*
- **`--remove-orphans`:** *Elimina contenedores no definidos en `docker-compose.yaml`.*
- **`--pull always`:** *Descarga siempre las imágenes más recientes.*
- **`--timeout 10`:** *Espera hasta 10 segundos para detener contenedores antiguos antes de iniciar los nuevos.*
- **`--detach`:** *Ejecuta los contenedores en segundo plano.*

---

### **3. Configurar la base de datos**  

**Para inicializar la base de datos, ejecuta el siguiente comando:**

```bash
docker container exec -itu0 -ePGPASSWORD=root container-db psql -hlocalhost -Upostgres -p5432 -f /App/sql/setup.sql
```  

**Explicación:**  

- **`docker container exec`:** *Ejecuta un comando dentro de un contenedor en ejecución.*
- **`-itu0`:** *Ejecuta el comando como usuario root dentro del contenedor.*
- **`-ePGPASSWORD=root`:** *Define la variable de entorno `PGPASSWORD` para autenticar en PostgreSQL.*
- **`container-db`:** *Nombre del contenedor donde se ejecutará el comando.*
- **`psql -hlocalhost -Upostgres -p5432`:** *Conecta a la base de datos PostgreSQL en el contenedor.*
- **`-f /App/sql/setup.sql`:** *Ejecuta el script SQL de configuración inicial.*

---

## **Administración de Contenedores**  

### **Detener los contenedores**  

*Si deseas detener los contenedores sin eliminarlos, usa:*

```bash
docker compose --profile dev --file docker-compose.yaml --project-name localdrive --project-directory "$(pwd)" stop --timeout 10
```  

**Explicación:**  

- **`stop`:** *Detiene los contenedores sin eliminarlos.*
- **`--timeout 10`:** *Espera hasta 10 segundos antes de forzar la detención.*

---

### **Iniciar los contenedores nuevamente**  

*Para volver a iniciar los contenedores previamente detenidos:*

```bash
docker compose --profile dev --file docker-compose.yaml --project-name localdrive --project-directory "$(pwd)" start
```  

**Explicación:**  

- **`start`:** *Reactiva los contenedores detenidos sin volver a construirlos.*

---

### **Eliminar los servicios y limpiar recursos**  

*Si necesitas eliminar todos los contenedores y limpiar los volúmenes de datos, usa:*

```bash
docker compose --project-name localdrive --project-directory "$(pwd)" down --remove-orphans --timeout 10 --volumes --rmi local
```  

**Explicación:**  

- **`down`:** *Elimina los contenedores y redes definidas en `docker-compose.yaml`.*
- **`--remove-orphans`:** *Borra contenedores que no estén en `docker-compose.yaml`.*
- **`--timeout 10`:** *Espera hasta 10 segundos antes de forzar la eliminación.*
- **`--volumes`:** *Borra los volúmenes de datos asociados.*
- **`--rmi local`:** *Elimina solo las imágenes Docker generadas localmente.*

---

### **Acceder al servicio**  

*Una vez que el sistema esté en funcionamiento, puedes probar su accesibilidad ejecutando:*

```bash
curl -X GET http://172.18.0.3:80
```  

**Explicación:**  

- **`curl -X GET`:** *Envía una solicitud GET a la dirección del servicio.*
- **`http://172.18.0.3:80`:** *Dirección IP y puerto donde está expuesta la aplicación en Docker.*

*Si ves una respuesta del servidor, significa que el servicio está activo y funcionando correctamente.*

---

## **Uso**

1. **Acceso inicial:**
   - *Dirígete a la URL de tu servidor.*
   - *Regístrate o inicia sesión.*

2. **Gestión de ficheros y directorios:**
   - **Usa la interfaz para cargar, organizar y compartir tus ficheros.**

3. **Notificaciones:**
   - *Revisa tus notificaciones para estar al tanto de eventos relevantes.*

## **Contribuciones**

**¡Contribuciones son bienvenidas! Por favor, sigue estos pasos:**

1. *Haz un fork del repositorio.*
2. *Crea una rama para tu funcionalidad:*

   ```bash
   git checkout -b new-functionality
   ```

   ```bash
   git switch --create=new-functionality
   ```

3. **Haz tus cambios y realiza un commit:**

   ```bash
   git commit --message="Nueva Funcionalidad ..." --verbose --verify --all
   ```

4. **Envía tus cambios:**

   ```bash
   git push origin new-functionality
   ```

5. **Crea un Pull Request.**

## **Licencia**

*Este proyecto está bajo la Licencia MIT. Consulta el fichero [LICENSE.md](LICENSE.md "LICENSE.md") para más información.*
