<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# **En PHP, puedes definir parámetros opcionales para funciones asignándoles valores predeterminados al declararlos, similar a Python. Si no se proporciona un valor para un parámetro opcional al llamar a la función, se utiliza el valor predeterminado.**

**Aquí tienes un ejemplo:**

## **Parámetros opcionales en PHP**

```php
function saludar(string $nombre = "Usuario", string $saludo = "¡Hola!"): string {
    return "$saludo, $nombre!";
}

// Llamadas a la función
echo saludar(); // Usa los valores predeterminados: ¡Hola!, Usuario
echo saludar("Daniel"); // Solo cambia $nombre: ¡Hola!, Daniel
echo saludar("Daniel", "¡Buenos días!"); // Cambia ambos: ¡Buenos días!, Daniel
```
