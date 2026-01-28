<?php

$host = 'db';
$db   = 'local_drive';
$user = 'postgres';
$password = 'root';
$port = '5432';
$dsn = "pgsql:host={$host};port={$port};dbname={$db}";

// DSN significa Data Source Name (Nombre de Fuente de Datos) y es una cadena que se utiliza para definir los parámetros necesarios para establecer una conexión a una base de datos.

// En el contexto de PDO (PHP Data Objects), un DSN contiene información esencial sobre la base de datos a la que se va a conectar, como el tipo de base de datos (por ejemplo, MySQL, PostgreSQL), la ubicación del servidor, el nombre de la base de datos y otros parámetros específicos.
