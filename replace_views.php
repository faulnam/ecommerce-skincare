<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views');
$iterator = new RecursiveIteratorIterator($dir);

$filesModified = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') { // blade.php
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        
        $newContent = $content;
        
        // Replace asset('storage/...') with config('filesystems.disks.r2.url').'/...'
        $newContent = str_replace("asset('storage/", "config('filesystems.disks.r2.url').'/", $newContent);
        $newContent = str_replace('asset("storage/', "config('filesystems.disks.r2.url').'/", $newContent);

        if ($content !== $newContent) {
            file_put_contents($path, $newContent);
            $filesModified++;
            echo "Updated View: " . $file->getFilename() . "\n";
        }
    }
}

echo "Total view files modified: $filesModified\n";
