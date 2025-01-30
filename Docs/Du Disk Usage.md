<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# ***En Linux, puedes usar el comando `du` (disk usage) para obtener el tamaño total de un directorio, incluyendo su contenido de forma recursiva. Aquí hay varias formas de hacerlo:***

## ***Obtener el tamaño total del directorio (recursivamente)***

```bash
du -sh /path/directory
```

- **`-s`:** *Muestra solo el tamaño total del directorio especificado.*
- **`-h`:** *Formato legible para humanos (e.g., KB, MB, GB).*

### ***Ver el tamaño de cada subdirectorio (detallado)***

```bash
du -h /path/directory
```

*Esto mostrará el tamaño de cada subdirectorio dentro de la ruta especificada.*

### ***Filtrar solo el tamaño total del directorio***

**Si solo necesitas el tamaño total y no las subcarpetas intermedias:**

```bash
du -sh /path/directory
```

### ***Excluir ciertos Ficheros o directorios***

**Si deseas excluir algunos Ficheros o subdirectorios, puedes usar la opción `--exclude`:**

```bash
du -sh --exclude="*.log" /path/directory
```

*Esto excluirá todos los Ficheros con la extensión `.log`.*

### ***Usar `find` combinado con `du` para mayor control***

*Si necesitas procesar directorios específicos, puedes usar `find`:*

```bash
find /path/directory -type d -exec du -sh {} \;
```

*Esto recorrerá cada subdirectorio y ejecutará `du -sh` en él.*
