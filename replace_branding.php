<?php

function preserveCaseReplace($search, $replace, $subject) {
    return preg_replace_callback('/' . $search . '/i', function($matches) use ($replace) {
        $match = $matches[0];
        $alphaMatch = preg_replace('/[^a-zA-Z]/', '', $match);
        
        if (strlen($alphaMatch) > 0 && ctype_upper($alphaMatch)) {
            return strtoupper($replace);
        } elseif (strlen($alphaMatch) > 0 && ctype_upper($alphaMatch[0])) {
            return ucfirst(strtolower($replace));
        } else {
            return strtolower($replace);
        }
    }, $subject);
}

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator('.'));
$filesModified = 0;

$targets = [
    'norapadel',
    'nora\s*padel',
    'padel',
    'racket',
    'raket',
    'nora'
];

foreach ($it as $f) {
    if ($f->isFile()) {
        $path = $f->getPathname();
        $filename = $f->getFilename();
        
        // Skip ignored directories & scripts
        if (preg_match('/vendor|node_modules|\.git|storage/i', $path)) continue;
        if ($filename === 'replace_branding.php' || $filename === 'search.php') continue;
        
        // Explicitly exclude .env and .env.example
        if (strpos($filename, '.env') !== false) continue;
        
        // Target text-based file extensions
        if (preg_match('/\.(php|blade\.php|js|css|json|vue|html|md|txt|xml|sql)$/i', $filename)) {
            $originalContent = file_get_contents($path);
            $content = $originalContent;
            
            foreach ($targets as $target) {
                $content = preserveCaseReplace($target, 'hijab', $content);
            }
            
            if ($content !== $originalContent) {
                file_put_contents($path, $content);
                echo "Updated: $path\n";
                $filesModified++;
            }
        }
    }
}

echo "\nTotal files modified: $filesModified\n";
