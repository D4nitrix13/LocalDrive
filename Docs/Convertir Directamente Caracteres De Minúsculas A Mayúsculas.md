<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# **Problema es que `sed` no tiene una opción integrada para convertir directamente caracteres de minúsculas a mayúsculas. Sin embargo, puedes usar `sed` junto con el comando `tr`, o bien utilizar la extensión POSIX de `sed` si está disponible en tu sistema. A continuación, te muestro una solución**

## ***Forma correcta con `sed` y `tr`***

- **Convertir Mayuscula**

```bash
echo "daniel benjamin perez morales" | tr '[:lower:]' '[:upper:]'
```

- **Convertir Minuscula**

```bash
echo "DANIEL BENJAMIN PEREZ MORALES" | tr '[:upper:]' '[:lower:]'
```

### **Explicación**

1. **`tr '[:lower:]' '[:upper:]'`:** *Convierte todos los caracteres en minúscula a mayúscula.*
2. **`tr '[:upper:]' '[:lower:]'`:** *Convierte todos los caracteres en mayúscula a minúscula.*

### **Alternativa con `awk`**

**Si prefieres usar `awk`, puedes hacerlo de la siguiente manera:**

- **Convertir Mayuscula**

```bash
echo "daniel benjamin perez morales" | awk '{print toupper($0)}'
```

- **Convertir Minuscula**

```bash
echo "DANIEL BENJAMIN PEREZ MORALES" | awk '{print tolower($0)}'
```

### **Alternativa con `sed` (POSIX)**

**Algunos sistemas permiten usar transformaciones POSIX con `sed`:**

- **Convertir Mayuscula**

```bash
echo "daniel benjamin perez morales" | sed 'y/abcdefghijklmnopqrstuvwxyz/ABCDEFGHIJKLMNOPQRSTUVWXYZ/'
```

- **Convertir Minuscula**

```bash
echo "DANIEL BENJAMIN PEREZ MORALES" | sed 'y/ABCDEFGHIJKLMNOPQRSTUVWXYZ/abcdefghijklmnopqrstuvwxyz/'
```
