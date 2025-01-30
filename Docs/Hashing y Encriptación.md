<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# **Hashing y Encriptación:**

- **Ambos son técnicas utilizadas para la manipulación de datos, pero tienen propósitos y características diferentes.**

---

## **Hashing**

**Definición:** *El **hashing** es el proceso de transformar una entrada (como un fichero, mensaje o contraseña) en una secuencia de longitud fija mediante una función hash. El resultado es conocido como **valor hash** o **digest**.*

- **Características**:
  - **Unidireccional:** *No se puede revertir (ir hacia atrás). Una vez que los datos se han convertido en un hash, no puedes obtener los datos originales a partir de ese hash.*
  - **Longitud fija:** *El hash tiene una longitud fija, sin importar el tamaño de los datos de entrada (por ejemplo, SHA-256 genera siempre un hash de 256 bits).*
  - **Determinístico:** *Para los mismos datos de entrada, siempre producirá el mismo valor hash.*

- **Propósito:** *Principalmente para **verificar integridad** de los datos. Se utiliza en la comparación de datos para asegurarse de que no han sido modificados (por ejemplo, comprobación de integridad en descargas de ficheros).*

- **Uso común:** *Contraseñas almacenadas en bases de datos, sumas de verificación, integridad de datos, firmas digitales.*

### **Ventajas del Hashing**

- **No reversible:** *No puede ser descifrado, lo que proporciona un alto nivel de seguridad para contraseñas.*
- **Rápido:** *Las funciones hash suelen ser muy rápidas para procesar grandes volúmenes de datos.*
- **Consistencia:** *Siempre produce el mismo valor hash para la misma entrada.*

#### **Desventajas del Hashing**

- **Colisiones:** *Existe una pequeña posibilidad de que dos entradas diferentes generen el mismo valor hash.*
- **No adecuado para transmitir datos sensibles:** *Ya que no se puede revertir, no sirve para enviar datos que deban ser descifrados de vuelta a su forma original.*

---

### **Encriptación**

**Definición:** *La **encriptación** es el proceso de convertir datos en un formato ilegible (cifrado) para proteger la información, utilizando una clave y un algoritmo de encriptación. Solo alguien con la clave correcta puede desencriptar y acceder a los datos originales.*

- **Características**:
  - **Bidireccional:** *Es reversible, lo que significa que los datos pueden ser restaurados a su forma original utilizando una clave de desencriptación.*
  - **Clave de encriptación:** *Los datos son encriptados y desencriptados utilizando una clave. Puede ser simétrica (misma clave para encriptar y desencriptar) o asimétrica (clave pública para encriptar, clave privada para desencriptar).*
  - **Protege la confidencialidad:** *La encriptación se utiliza para proteger la confidencialidad de los datos.*

- **Propósito:** ***Proteger la confidencialidad** de los datos durante la transmisión o almacenamiento. La información encriptada no es legible sin la clave adecuada.*

- **Uso común:** *Transacciones bancarias, comunicación segura (SSL/TLS), almacenamiento de información sensible (como números de tarjeta de crédito).*

#### **Ventajas de la Encriptación**

- **Confidencialidad:** *Asegura que solo las partes autorizadas puedan acceder a la información.*
- **Reversible:** *Los datos pueden ser restaurados a su forma original si se tiene la clave adecuada.*
- **Protección en tránsito:** *Evita que los datos sean leídos si se interceptan mientras viajan por una red.*

#### **Desventajas de la Encriptación**

- **Requiere gestión de claves:** *La seguridad depende de la correcta gestión de las claves.*
- **Lento:** *Puede ser más lento que el hashing, especialmente con algoritmos fuertes o grandes volúmenes de datos.*
- **Vulnerabilidad a ataques de fuerza bruta:** *Si las claves no son suficientemente fuertes, los atacantes pueden descifrar la información.*

---

### **Diferencias clave entre Hashing y Encriptación**

| **Característica**   | **Hashing**                                                               | **Encriptación**                                            |
| -------------------- | ------------------------------------------------------------------------- | ----------------------------------------------------------- |
| **Direccionalidad**  | *Unidireccional (irreversible)*                                           | *Bidireccional (reversible)*                                |
| **Propósito**        | *Verificar integridad o almacenamiento seguro de datos (ej. contraseñas)* | *Proteger la confidencialidad de datos*                     |
| **Tamaño de salida** | *Fijo (dependiendo de la función de hash)*                                | *Variable (según el algoritmo de encriptación)*             |
| **Uso común**        | *Contraseñas, firmas digitales, sumas de verificación*                    | *Comunicaciones seguras, almacenamiento de datos sensibles* |
| **Reversible**       | *No*                                                                      | *Sí*                                                        |

---

### **¿Cuál es mejor?**

- *La elección entre hashing y encriptación depende del **propósito** y los **requisitos** de seguridad:*

- **Hashing** *es mejor cuando:*
  - *Necesitas verificar la integridad de los datos (por ejemplo, comprobar contraseñas sin almacenarlas en texto claro).*
  - *No necesitas recuperar los datos originales, solo validar su existencia o integridad.*
  - *Estás trabajando con datos que no deben ser revertidos, como contraseñas de usuarios.*

- **Encriptación** *es mejor cuando:*
  - *Necesitas proteger la confidencialidad de los datos y permitir su recuperación (desencriptación) posterior.*
  - *Los datos deben ser accesibles solo para ciertas personas o sistemas (por ejemplo, cuando se transmiten datos sensibles).*

- *En resumen, **hashing** es ideal para verificar la integridad de los datos, mientras que **encriptación** es ideal para proteger la confidencialidad.*
