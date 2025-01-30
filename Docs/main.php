<!-- Autor: Daniel Benjamin Perez Morales -->
<!-- GitHub: https://github.com/DanielBenjaminPerezMoralesDev13 -->
<!-- GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13 -->
<!-- Correo electrónico: danielperezdev@proton.me -->

<?php
// Ejemplo De Map, Filter, Reduce
$filePath = "/App/main.py,/App/file.php,/App/index.js";

print_r(explode(',', $filePath));
// echo PHP_EOL;  // Imprime una nueva línea

$list = ["13$", "15$", "25$", "12$", "30$", "20$"];
$dollars = array_map(function ($dollar) {
    return (float)substr($dollar, 0, -1);
}, $list);

$expensive = array_filter($dollars, function ($price) {
    return $price >= 20;
});

$suma = array_reduce($expensive, function ($num, $money) {
    return $num + $money;
}, 0);

print_r($suma . PHP_EOL);

print_r(
    array_reduce(
        array_filter(
            array_map(
                function ($dollar) {
                    return (float)substr($dollar, 0, -1);
                },
                ["13$", "15$", "25$", "12$", "30$", "20$"]
            ),
            function ($price) {
                return $price >= 20;
            }
        ),
        function ($num, $money) {
            return $num + $money;
        },
        0
    )
);
?>