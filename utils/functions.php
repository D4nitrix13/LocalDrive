<?php

function getFileExtension(string $directory, string $entryName): string
{
    $extension = pathinfo($directory . DIRECTORY_SEPARATOR . $entryName);
    // pathinfo(): Extrae información del fichero, incluida la extensión.
    // Operador null-coalescente (??): Si pathinfo no encuentra la extensión, devolverá una cadena vacía en lugar de causar un error.
    if (!isset($extension['extension'])) return '';
    return $extension['extension'] ?? '';
}
function detectExtension(string $extension): string
{
    if (empty($extension)) return 'genericFiles';

    global $directoryAppIcon;
    $listExtensionImages = ["png", "img", "jpg", "jpeg"];
    $listExtensionShell = ["sh", "zsh", "fish", "bash"];


    foreach (new DirectoryIterator($directoryAppIcon) as $indice => $entry) {
        if ($entry->isDot()) continue;
        if (pathinfo($entry->getFilename())["filename"] === $extension) return $extension;
        if (in_array($extension, $listExtensionImages)) return "imageFile";
        if (in_array($extension, $listExtensionShell)) return "shell";
    }

    foreach (new DirectoryIterator($directoryAppIcon) as $index => $entry) {
        if ($entry === '.' || $entry === '..' || is_dir($entry)) continue;
        if (pathinfo($entry->getFilename())["filename"] === $extension) return $extension;
        if (in_array($extension, $listExtensionImages)) return "imageFile";
        if (in_array($extension, $listExtensionShell)) return "shell";
    }

    return 'genericFiles';
}

function getDirContents(string $dir, bool $relativePath = false): array
{
    $fileList = array();
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isDir()) continue;
        $path = $file->getPathname();
        if ($relativePath) {
            $path = str_replace($dir, '', $path);
            $path = ltrim($path, '/\\');
        }
        $fileList[] = $path;
    }
    return $fileList;
}
