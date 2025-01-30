<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# ***Error No Se Encontró La Biblioteca De PostgreSQL (`libpq`) Ni El Fichero De Cabecera Necesario (`libpq-fe.h`) Para Compilar La Extensión `pdo_pgsql`. Esto Ocurre Porque Los Paquetes De Desarrollo Necesarios No Están Instalados En Tu Contenedor.***

- *[Foro](https://stackoverflow.com/questions/10085039/postgresql-pdoexception-with-message-could-not-find-driver "https://stackoverflow.com/questions/10085039/postgresql-pdoexception-with-message-could-not-find-driver")*
- **Solucionarlo**

## **Instala las dependencias necesarias**

**En tu contenedor Docker, instala las bibliotecas de desarrollo de PostgreSQL:**

```bash
apt-get update && apt-get install -y libpq-dev
```

---

### **Vuelve a intentar instalar `pdo_pgsql`**

**Después de instalar `libpq-dev`, intenta nuevamente ejecutar:**

```bash
docker-php-ext-install pdo_pgsql
```

**Esto debería completar la instalación de la extensión correctamente.**

---

#### **Verifica que `pdo_pgsql` esté instalado**

*Ejecuta el siguiente comando para confirmar que la extensión está habilitada:*

```bash
php -m | grep pdo_pgsql
```

```bash
root@ce83460b7791:/# php -m | grep pdo_pgsql
pdo_pgsql
```

---

### **Conclusión**

- *El problema se debe a que `libpq-dev` no está instalado en el contenedor. Al instalar esta dependencia, `pdo_pgsql` se compilará y habilitará correctamente. Recuerda actualizar el Dockerfile para que estos pasos sean permanentes en tu imagen de Docker.*
