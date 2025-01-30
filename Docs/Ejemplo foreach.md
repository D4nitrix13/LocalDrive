<!-- Author: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Email: danielperezdev@proton.me -->

# **Ejemplo foreach:**

```php
$lista = ["Daniel", "Benjamin", "Carol"];
$buscar = "Benjamin";
$encontrado = false;

foreach ($lista as $key => $value) {
  if ($value === $buscar) {
    $encontrado = true;
    echo "'$buscar' encontrado en la posición $key.\n";
    break; 
  }
}

if (!$encontrado) {
  echo "'$buscar' no está en la lista.";
}
```
