<?php

function folderSize(string $dir): int
{
    $count_size = 0;
    $count = 0;
    $dir_array = scandir($dir);
    foreach ($dir_array as $key => $filename) {
        if ($filename != ".." && $filename != ".") {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $filename)) {
                $new_foldersize = foldersize($dir . DIRECTORY_SEPARATOR . $filename);
                $count_size = $count_size + $new_foldersize;
            } else if (is_file($dir . DIRECTORY_SEPARATOR . $filename)) {
                $count_size = $count_size + filesize($dir . DIRECTORY_SEPARATOR . $filename);
                $count++;
            }
        }
    }
    return $count_size;
}

function sizeFormat(int $bytes): string
{
    $kb = 1024;
    $mb = $kb * 1024;
    $gb = $mb * 1024;
    $tb = $gb * 1024;

    if (($bytes >= 0) && ($bytes < $kb)) {
        return $bytes . ' B';
    } elseif (($bytes >= $kb) && ($bytes < $mb)) {
        return ceil($bytes / $kb) . ' KB';
    } elseif (($bytes >= $mb) && ($bytes < $gb)) {
        return ceil($bytes / $mb) . ' MB';
    } elseif (($bytes >= $gb) && ($bytes < $tb)) {
        return ceil($bytes / $gb) . ' GB';
    } elseif ($bytes >= $tb) {
        return ceil($bytes / $tb) . ' TB';
    } else {
        return $bytes . ' B';
    }
}

function availableSpaceMb(int $bytes, float $maximumSpace = 9536.74): float
{
    $kb = 1024;
    $mb = $kb * 1024;
    return $maximumSpace - ceil($bytes / $mb);
}

function availableSpaceGb(int $bytes, int $maximumSpace = 10): float
{
    $kb = 1024;
    $mb = $kb * 1024;
    $gb = $mb * 1024;
    return $maximumSpace - ceil($bytes / $gb);
}
?>