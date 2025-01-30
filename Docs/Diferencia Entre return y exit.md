<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# **Problema puede estar relacionado con el manejo de las sesiones y cómo se está verificando si la clave `flash` está definida dentro de `$_SESSION`. Al agregar el código `if (isset($_SESSION["flash"])) { die("existe"); }`, estás forzando la ejecución a comprobar la existencia de esa clave antes de que se llegue a la parte del código donde debería mostrarse el mensaje. Esto "activa" o asegura que la sesión esté correctamente inicializada para la verificación.**

## **Solución Paso a Paso**

1. **Asegúrate de que la sesión está iniciada correctamente:**
   - *Verifica que `session_start()` está incluido al inicio de todos los Ficheros relevantes. Si falta en alguno, las claves de sesión no estarán disponibles.*

2. **Valida correctamente si la clave `flash` existe:**
   - *Usa `isset($_SESSION["flash"])` para comprobar si la clave está configurada antes de intentar acceder a ella. Esto ya lo estás haciendo, pero asegúrate de que no hay otro código que sobrescriba o elimine la clave antes de que se procese.*

3. **Elimina la clave `flash` después de usarla:**
   - *El código ya incluye `unset($_SESSION["flash"]);`, lo cual es correcto. Esto asegura que el mensaje solo se muestre una vez.*

4. **Asegúrate de redirigir correctamente:**
   *Cuando configures la clave `flash` y redirijas al usuario con `header()`, asegúrate de que no hay salida previa al encabezado. Si hay un espacio o texto antes de `header("Location: ...")`, la redirección fallará.*

### ***Código Mejorado***

```php
// Configurar flash y redirigir
$_SESSION["flash"] = [
   "message" => "Add Directory $newNameDirectory",
   "class" => "alert alert-primary d-flex align-items-center",
   "aria-label" => "Info:",
   "xlink:href" => "#info-fill"
];

header("Location: {$_SERVER['PHP_SELF']}?directory=$directoryPrincipal");
exit; // Asegura que no se ejecute código adicional después de la redirección
```

### **Prueba**

1. *Configura el mensaje `flash` en el fichero correspondiente.*
2. *Redirige con `header()`.*
3. *En la página destino, verifica que el mensaje se muestra correctamente.*
4. *Si persiste el problema, depura con `print_r($_SESSION);` para analizar el estado actual de las sesiones.*

- *Si todo está correctamente configurado, el mensaje debería mostrarse sin necesidad de agregar `die()`.*

- *El comportamiento que observaste al usar `exit` en lugar de `return null` tiene que ver con cómo se procesa el flujo del script en PHP:*

### **Diferencia entre `return` y `exit` en este contexto**

1. **`return null`:**
   - *Solo detiene la ejecución de la función actual.*
   - *Si la función que contiene el `return` está dentro de un script más grande, el flujo de ejecución del script continuará después de que la función retorne.*
   - *Si hay código después de la llamada a esa función en el mismo fichero, este seguirá ejecutándose.*

2. **`exit`:**
   - *Termina la ejecución del script inmediatamente.*
   - *Evita que cualquier código posterior (en el fichero actual o en otros Ficheros requeridos/incluidos) se ejecute.*
   - *Es útil cuando quieres asegurar que el navegador procese un encabezado `Location:` sin interrupciones.*

### **Por qué funcionó con `exit`**

- *Cuando se configura un encabezado con `header("Location: ...")`, PHP necesita detener cualquier salida o procesamiento adicional para enviar correctamente los encabezados HTTP al navegador. Si el script continúa ejecutándose después de `header()`, pueden ocurrir los siguientes problemas:*

- **Salida previa a los encabezados:** *Si hay algún `echo`, espacio, o salida antes del `header()`, PHP generará un error diciendo que "los encabezados ya han sido enviados".*
- **Procesamiento innecesario:** *Código adicional después de `header()` podría sobrescribir valores en la sesión, alterar el flujo esperado, o generar salida no deseada.*

*Al usar `exit`, garantizas que:*

- *La redirección se realiza inmediatamente.*
- *No se ejecuta código adicional que pueda interferir con la sesión o los encabezados HTTP.*

### **Por qué `return null` no funcionó**

- *En este caso, como `return null` solo detenía la ejecución dentro de la función, el script probablemente seguía ejecutando otros bloques de código en el fichero que podían estar sobrescribiendo o eliminando la clave `flash` de `$_SESSION`, evitando que el mensaje se mostrara.*

### **Recomendación**

**Siempre que utilices `header("Location: ...")` para redirigir al usuario, **usa `exit` o `exit()` después de la llamada** para evitar problemas con la ejecución del código restante. Ejemplo:**

```php
header("Location: somepage.php");
exit; // Detiene la ejecución inmediatamente
```

- **Esto es una práctica estándar para manejar redirecciones seguras y evitar comportamientos inesperados.**
