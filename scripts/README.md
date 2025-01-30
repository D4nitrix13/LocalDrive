<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# **Pasos para usar el script:**

1. *Crea un fichero con el nombre deseado, por ejemplo: `generateFile.sh`.*
2. *Copia el script anterior en ese fichero.*
3. *Dale permisos de ejecución al script:*

   ```bash
   chmod +x generateFile.sh
   ```

4. Ejecuta el script:

   ```bash
   ./generateFile.sh
   ```

## **Explicación del script:**

- **`dd if=/dev/zero`:** *Utiliza un flujo de entrada de datos llenos de ceros.*
- **`of="$FILE_NAME"`:** *Especifica el nombre del fichero de salida.*
- **`bs=1M`:** *Tamaño del bloque de datos (1 MB).*
- **`count=$((FILE_SIZE / 1024 / 1024))`:** *Calcula cuántos bloques de 1 MB son necesarios para generar 5 GB.*
- **`status=progress`:** *Muestra el progreso durante la creación del fichero.*

### **Resultado:**

*El fichero generado será un fichero binario lleno de ceros llamado `file.bin` que pesa exactamente 5 GB.*
