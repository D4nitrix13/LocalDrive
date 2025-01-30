#!/usr/bin/env bash

# Autor: Daniel Benjamin Perez Morales
# GitHub: https://github.com/DanielBenjaminPerezMoralesDev13
# GitLab: https://gitlab.com/DanielBenjaminPerezMoralesDev13
# Correo electrónico: danielperezdev@proton.me

# Nombre del fichero
FILE_NAME="file.bin"

# Tamaño del fichero en bytes (5 GB = 5 * 1024^3)
FILE_SIZE=$((5 * 1024 * 1024 * 1024))

# Crear el fichero de 5 GB
dd if=/dev/zero of="$FILE_NAME" bs=1M count=$((FILE_SIZE / 1024 / 1024 )) status=progress

# Confirmar el tamaño del fichero creado
if [ -f "$FILE_NAME" ]; then
    echo "Fichero '$FILE_NAME' de $((FILE_SIZE / 1024 / 1024 )) GB creado con éxito."
    ls -lh "$FILE_NAME"
else
    echo "Error al crear el fichero."
fi
