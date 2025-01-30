<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# **Para buscar el fichero más pesado dentro de un directorio en Linux, puedes usar el comando `find` combinado con `du` y `sort`.**

---

## **1. Buscar el fichero más grande dentro de un directorio específico**

**Usa este comando para buscar el fichero más pesado en el directorio actual y sus subdirectorios:**

```bash
find . -type f -exec du -h {} + | sort -rh | head -n 1
```

**Explicación:**

- **`find . -type f`:** *Busca todos los ficheros (`-type f`) en el directorio actual (`.`) y subdirectorios.*
- **`du -h {}`:** *Calcula el tamaño de cada fichero en un formato legible para humanos (`-h`).*
- **`sort -rh`:** *Ordena los resultados por tamaño en orden descendente (`-r`) y human-readable (`-h`).*
- **`head -n 1`:** *Muestra solo el fichero más grande.*

---

### **2. Mostrar los 10 ficheros más grandes**

- **Si deseas ver una lista de los ficheros más grandes, modifica el comando para incluir más resultados:**

```bash
find . -type f -exec du -h {} + | sort -rh | head -n 10
```

- *Esto mostrará los **10 ficheros más grandes** en el directorio actual.*

---

### **3. Buscar el fichero más grande en todo el sistema**

- *Para buscar en todo el sistema, necesitas permisos de administrador (root):*

```bash
sudo find / -type f -exec du -h {} + 2>/dev/null | sort -rh | head -n 1
```

**Nota:**

- **`/`:** *Especifica que la búsqueda comienza desde la raíz del sistema.*
- **`2>/dev/null`:** *Oculta los mensajes de error de ficheros o directorios inaccesibles.*

---

### **4. Solo nombres de ficheros**

- *Si solo quieres el nombre y la ruta del fichero más grande (sin el tamaño):*

```bash
find . -type f -printf "%s \t %p \n" | sort -nr | head -n 1
```

**Explicación:**

- **`%s`:** *Imprime el tamaño en bytes.*
- **`%p`:** *Imprime la ruta completa del fichero.*
- **`sort -nr`:** *Ordena numéricamente (`-n`) en orden descendente (`-r`).*
- **`head -n 1`:** *Muestra el primer resultado.*
