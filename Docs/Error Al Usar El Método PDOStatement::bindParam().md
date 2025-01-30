<!-- Author: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Email: danielperezdev@proton.me -->

# *Error al usar el método `PDOStatement::bindParam()` con un argumento que no puede ser pasado por referencia. Esto ocurre porque `bindParam` requiere que el segundo argumento sea una variable pasada por referencia, pero probablemente estés pasando un valor directamente o un tipo que no admite referencias.*

## ***Posibles causas***

1. **Usar un valor directo** *en lugar de una variable:*

   ```php
   $statement->bindParam(':name', 'Daniel'); // Incorrecto
   ```

   *Aquí `'Daniel'` es un valor literal y no puede ser pasado por referencia.*

2. **Usar una expresión o resultado de función**:

   ```php
   $statement->bindParam(':age', 25 + 5); // Incorrecto
   ```

   *El resultado de `25 + 5` no es una variable.*
