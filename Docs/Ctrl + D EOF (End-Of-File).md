<!-- Author: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Email: danielperezdev@proton.me -->

# *El atajo de teclado **Ctrl + D** en Linux tiene diferentes funciones según el contexto en el que se use, pero generalmente está relacionado con enviar la señal **EOF (End-of-File)** o cerrar una sesión.*

## **En la Terminal (CLI)**

- **Señal:** **EOF (End-of-File)**
- **Función:** *Indica el final de entrada estándar (stdin). Esto es útil para:*
  - *Salir de un intérprete de línea de comandos (como `bash` o `python`) cuando no hay más procesos ejecutándose.*
  - *Finalizar la entrada estándar en un programa que espera datos.*
- **Ejemplo:**

  ```bash
  cat
  (Ctrl + D)  # Finaliza la entrada estándar
  ```

### **En Programas Interactivos (como `vim` o `less`)**

- **Función:** *Desplazar hacia abajo media página en los documentos o buffers que estás visualizando.*

### **En Shells Multiusuario (como `tmux` o `screen`)**

- **Función:** *Cierra el panel actual (si no está en uso) o envía EOF al programa en ejecución en el panel.*

### **En Contexto de Programación**

- **Función:** *En lenguajes como C o Python, simula el final de la entrada estándar, lo que puede ser útil para pruebas y depuración.*

- *Si necesitas enviar una señal específica distinta a **EOF**, deberías usar herramientas como `kill` o combinarla con otras teclas (`Ctrl + C` para SIGINT, por ejemplo).*
