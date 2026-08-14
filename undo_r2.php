<?php

$dir = new RecursiveDirectoryIterator(__DIR__ . '/app/Http/Controllers');
$iterator = new RecursiveIteratorIterator($dir);

$filesModified = 0;

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        
        $newContent = $content;
        
        // Replace store('path', 'r2') with store('path', 'public')
        $newContent = preg_replace("/->store\(([^,]+),\s*'r2'\)/", "->store($1, 'public')", $newContent);
        $newContent = preg_replace('/->store\(([^,]+),\s*"r2"\)/', '->store($1, \'public\')', $newContent);
        
        // Replace storeAs('path', $filename, 'r2') with 'public'
        $newContent = preg_replace("/->storeAs\(([^,]+),\s*([^,]+),\s*'r2'\)/", "->storeAs($1, $2, 'public')", $newContent);
        $newContent = preg_replace('/->storeAs\(([^,]+),\s*([^,]+),\s*"r2"\)/', '->storeAs($1, $2, \'public\')', $newContent);
        
        // Replace Storage::disk('r2') with Storage::disk('public')
        $newContent = str_replace("Storage::disk('r2')", "Storage::disk('public')", $newContent);
        $newContent = str_replace('Storage::disk("r2")', "Storage::disk('public')", $newContent);

        if ($content !== $newContent) {
            file_put_contents($path, $newContent);
            $filesModified++;
            echo "Updated: " . $file->getFilename() . "\n";
        }
    }
}

echo "Total files modified: $filesModified\n";
