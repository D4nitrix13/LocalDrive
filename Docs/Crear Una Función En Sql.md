<!-- Author: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Email: danielperezdev@proton.me -->

# ***Para Crear Una Función En Sql Que Reciba Un Número, Lo Multiplique Por 5 Y Lo Retorne, Puedes Utilizar La Siguiente Sintaxis En PostgreSQL:***

```sql
CREATE FUNCTION function_multiply_by_5(IN input_number INT)
RETURNS INT AS $$
BEGIN
    RETURN input_number * 5;
END;
$$ LANGUAGE plpgsql;
```

- **`CREATE FUNCTION`:** *Crea una función llamada `function_multiply_by_5`.*
- **`IN input_number INT`:** *Es el parámetro de entrada de la función, que es un número entero.*
- **`RETURNS INT`:** *Especifica que la función devolverá un valor de tipo `INT` (entero).*
- **`RETURN input_number * 5;`:** *El cuerpo de la función multiplica el número ingresado (`input_number`) por 5 y retorna el resultado.*
- **`LANGUAGE plpgsql`:** *Indica que la función está escrita en el lenguaje PL/pgSQL, que es el lenguaje de procedimientos en PostgreSQL.*

## **Uso de la función**

**Una vez que la función esté creada, puedes llamarla de la siguiente manera:**

```sql
SELECT function_multiply_by_5(10);
```

**Este comando retornará `50`, ya que 10 multiplicado por 5 es igual a 50.**
