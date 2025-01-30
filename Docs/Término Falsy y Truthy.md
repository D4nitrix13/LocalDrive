<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# ***En programación, el término `"falsy"` hace referencia a los valores que son considerados `"falsos"` cuando se evalúan en un contexto booleano, como una condición `if` o un bucle `while`.***

- *Los valores **falsy** son aquellos que, en un contexto de comparación booleana, se interpretan como `false`. Dependiendo del lenguaje de programación, los valores falsy pueden variar, pero generalmente incluyen:*

1. **`false`** *(el valor booleano false).*
2. **`0`** *(el número cero).*
3. **`""`** *o* **`''` (cadena vacía)**.
4. **`null`** *o* **`undefined`** *(en JavaScript, por ejemplo).*
5. **`NaN`** *(Not-a-Number, usado en JavaScript y otros lenguajes).*
6. **`None`** *(en Python).*

- *Cualquier valor que no sea falsy generalmente se considera **truthy**, es decir, que se evalúa como verdadero en un contexto booleano.*

## **Ejemplo en JavaScript**

```javascript
if (0) {
  console.log('Esto no se ejecuta porque 0 es falsy');
} else {
  console.log('Esto se ejecuta');
}

if ('Hola') {
  console.log('Esto se ejecuta porque la cadena no está vacía (truthy)');
}
```

### **Ejemplo en Python**

```python
if not 0:
    print("0 es falsy en Python")

if "Hola":
    print("Las cadenas no vacías son truthy en Python")
```

*El concepto de valores falsy es importante cuando se trata de evaluaciones condicionales, ya que permite escribir código más conciso sin necesidad de comparaciones explícitas con `false` o `null`, entre otros valores.*
