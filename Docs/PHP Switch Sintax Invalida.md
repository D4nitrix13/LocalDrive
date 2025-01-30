<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# *No, en PHP **`switch ... endif`** no existe como una sintaxis válida. La estructura de control `switch` en PHP debe utilizar siempre las llaves `{}` para encapsular los bloques de código. A diferencia de `if` y `endif`, que tienen una forma alternativa de sintaxis para código embebido (como en plantillas HTML), `switch` no tiene esa opción.*

## ***Sintaxis válida de `switch`***

```php
switch ($variable) {
    case 1:
        echo "Caso 1";
        break;
    case 2:
        echo "Caso 2";
        break;
    default:
        echo "Caso por defecto";
        break;
}
```

### ***¿Por qué no existe `switch ... endif`?***

- *`endif` se usa como una alternativa a las llaves para `if`, principalmente en contextos donde se mezcla PHP con HTML. Sin embargo, para `switch`, su estructura y bloques requieren manejar múltiples casos (`case`, `default`), lo que no es compatible con una sintaxis simplificada como `endif`.*

### ***Alternativa para estructuras simples***

- *Si necesitas evitar llaves y estás usando un `if`, puedes usar `if ... endif` en lugar de `switch` si es apropiado para tu caso. Por ejemplo:*

```php
<?php if ($variable == 1): ?>
    <p>Opción 1</p>
<?php elseif ($variable == 2): ?>
    <p>Opción 2</p>
<?php else: ?>
    <p>Opción por defecto</p>
<?php endif; ?>
```
