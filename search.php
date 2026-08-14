<?php
$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'));
foreach ($it as $f) {
    if ($f->isFile()) {
        $path = $f->getPathname();
        // Skip ignored directories
        if (preg_match('/vendor|node_modules|\.git|storage/i', $path)) continue;
        
        // Only look at specific file extensions
        if (preg_match('/\.(php|env.*|js|css)$/i', $f->getFilename())) {
            $c = file_get_contents($path);
            if (stripos($c, 'hijab') !== false || stripos($c, 'hijab') !== false) {
                echo $path . PHP_EOL;
            }
        }
    }
}
