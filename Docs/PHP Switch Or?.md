<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

# **En PHP no puedes usar directamente `case 1 OR 2:` ni `case 1 || 2:` en una declaración `switch`. La estructura `switch` de PHP no admite condiciones combinadas con `OR` o `||` dentro de un solo `case`.**

## ***Alternativa con `switch`***

**Para manejar múltiples valores en un `switch`, puedes hacer algo como esto:**

```php
switch ($variable) {
    case 1:
    case 2:
        echo "Es 1 o 2";
        break;
    default:
        echo "Otro valor";
        break;
}
```

- **En este ejemplo, si `$variable` es 1 o 2, se ejecutará el mismo bloque de código. El `case 2:` no tiene un bloque de código propio y simplemente "cascada" al `case 1:`, ejecutando la misma lógica para ambos casos.**

### ***Alternativa con `if` y `||`***

**Si necesitas condiciones más complejas con `OR` o `||`, puedes usar `if`:**

```php
if ($variable == 1 || $variable == 2) {
    echo "Es 1 o 2";
} else {
    echo "Otro valor";
}
```

*Este enfoque es útil si necesitas más flexibilidad o condiciones más complejas dentro de una estructura.*
