<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/app/Http/Controllers');
$iterator = new RecursiveIteratorIterator($dir);

$filesModified = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        
        $newContent = $content;
        // Replace store('path', 'public') with store('path', 'r2')
        $newContent = preg_replace("/->store\(([^,]+),\s*'public'\)/", "->store($1, 'r2')", $newContent);
        $newContent = preg_replace('/->store\(([^,]+),\s*"public"\)/', '->store($1, \'r2\')', $newContent);
        
        // Replace Storage::disk('public') with Storage::disk('r2')
        $newContent = str_replace("Storage::disk('public')", "Storage::disk('r2')", $newContent);
        $newContent = str_replace('Storage::disk("public")', "Storage::disk('r2')", $newContent);

        if ($content !== $newContent) {
            file_put_contents($path, $newContent);
            $filesModified++;
            echo "Updated: " . $file->getFilename() . "\n";
        }
    }
}

echo "Total files modified: $filesModified\n";
