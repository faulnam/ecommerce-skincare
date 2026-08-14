<?php

$file = __DIR__ . '/ShopeeProductsSeeder.php';
$content = file_get_contents($file);
$lines = explode("\n", $content);
$fixed = 0;

foreach ($lines as &$line) {
    if (!preg_match("/^\s*'description'\s*=>\s*'/", $line)) continue;
    if (!preg_match("/'\s*,\s*$/", $line)) continue;

    if (!preg_match("/^(\s*'description'\s*=>\s*')(.+)'(\s*,\s*)$/", $line, $m)) continue;

    $prefix = $m[1];
    $inner  = $m[2];
    $suffix = $m[3];

    // Cek apakah ada single quote yang tidak di-escape di dalam konten
    $test = preg_replace("/\\\\./", "", $inner);
    if (strpos($test, "'") !== false) {
        // Ubah ke double-quoted string dan escape " di dalamnya
        $inner = str_replace('"', '\\"', $inner);
        $line = preg_replace("/'$/", '"', $prefix) . $inner . '"' . $suffix;
        $fixed++;
    }
}
unset($line);

$content = implode("\n", $lines);
file_put_contents($file, $content);
echo "Fixed $fixed descriptions\n";
