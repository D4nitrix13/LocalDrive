<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# **Comando de Docker que ejecuta un contenedor de base de datos y utiliza `psql`, la herramienta de línea de comandos de PostgreSQL, para ejecutar una consulta SQL dentro del contenedor.**

## **Comando completo:**

```bash
docker container exec -itu0 -ePGPASSWORD=root container-db psql -hlocalhost -Upostgres -p5432 -dlocal_drive -ac"select * from directory"
```

### **Desglose de los flags:**

1. **`docker container exec`:**
   *Ejecuta un comando dentro de un contenedor en ejecución. Especifica que quieres ejecutar un comando dentro de un contenedor que ya está en marcha.*

2. **`-i` (Interactivo):**  
   *Esta flag indica que la entrada estándar del contenedor debe permanecer abierta, lo que significa que el contenedor recibirá datos de entrada (stdin) de manera interactiva. Es comúnmente utilizada cuando se interactúa con un contenedor de forma directa, como con una terminal.*

3. **`-t` (Terminal):**  
   *Activa la asignación de un pseudo-terminal. Esto es útil cuando estás interactuando con un proceso que necesita acceso a un terminal, como cuando ejecutas un cliente interactivo de base de datos como `psql`.*

4. **`-u0` (Usuario):**  
   *Especifica el **usuario** que ejecutará el comando dentro del contenedor. El `0` normalmente se refiere a un identificador de usuario con privilegios administrativos (es decir, el usuario `root`). En este caso, parece que hay un error tipográfico; lo correcto sería `-u` seguido del nombre del usuario, y este valor debería ser el nombre de usuario con el que se ejecutará el comando.*

5. **`-ePGPASSWORD=root` (Variable de entorno):**  
   *Establece una variable de entorno dentro del contenedor. En este caso, estás configurando la variable `PGPASSWORD` para que el cliente `psql` pueda autenticarse sin pedir la contraseña. El valor de `PGPASSWORD=root` indica que la contraseña del usuario `postgres` será `root`.*

6. **`container-db`:**
   *Es el **nombre del contenedor** en el cual ejecutarás el comando. En este caso, el contenedor se llama `container-db`.*

7. **`psql`:**
   *Es el cliente de línea de comandos de PostgreSQL, utilizado para interactuar con la base de datos PostgreSQL.*

8. **`-h localhost` (Host):**  
   *Especifica el **host** donde se encuentra la base de datos a la que se conectará el cliente `psql`. `localhost` significa que la base de datos está en el mismo contenedor o en la máquina local.*

9. **`-U postgres` (Usuario de base de datos):**  
   *Establece el **usuario de la base de datos** con el cual te autenticarás para ejecutar las consultas. Aquí el usuario es `postgres`, que es el usuario por defecto en PostgreSQL.*

10. **`-p 5432` (Puerto):**  
   *Especifica el **puerto** de conexión de PostgreSQL. El puerto por defecto para PostgreSQL es `5432`.*

11. **`-d local_drive` (Base de datos):**  
   *Define la **base de datos** a la que te conectarás. En este caso, la base de datos es `local_drive`.*

12. **`-a` (Sin salida de cabecera):**  
   *Este flag suprime la salida de los nombres de las columnas en el resultado. El comando `psql` normalmente imprime los encabezados de las columnas antes de mostrar los resultados de la consulta. El flag `-a` elimina esa parte para que solo se muestren los resultados crudos.*

13. **`-c "select * from directory"`:**
   *El flag `-c` le indica a `psql` que ejecute un **comando SQL** específico. En este caso, el comando SQL es `"select * from directory"`, que selecciona todos los registros de la tabla `directory`.*

### **Resumen de la función de cada parte del comando:**

- **`docker container exec -itu0 -ePGPASSWORD=root container-db`:** *Ejecuta un comando en el contenedor `container-db`, permitiendo interacción con el terminal, y configurando la contraseña de PostgreSQL como `root`.*
- **`psql -h localhost -U postgres -p 5432 -d local_drive`:** *Conecta al cliente `psql` a la base de datos `local_drive` utilizando el usuario `postgres` y el puerto `5432` en el host `localhost`.*
- **`-a -c "select * from directory"`:** *Ejecuta la consulta SQL `SELECT * FROM directory` y muestra el resultado sin encabezados.*

### **Conclusión:**

*El comando completo se usa para conectarse a un contenedor de Docker que tiene PostgreSQL en ejecución, ejecutar una consulta `SELECT` en la base de datos `local_drive` y mostrar los resultados sin encabezados. La variable de entorno `PGPASSWORD` se establece para evitar tener que ingresar la contraseña de forma interactiva.*
