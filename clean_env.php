<?php
$content = file_get_contents('.env');
$content = str_replace("\0", "", $content);
$lines = explode("\n", $content);
$cleaned = [];
foreach ($lines as $line) {
    if (strpos($line, 'PAYLABS_MOCK_MODE') !== false) continue;
    $cleaned[] = $line;
}
$cleaned[] = 'PAYLABS_MOCK_MODE=true';
file_put_contents('.env', implode("\n", $cleaned));
echo "Done";
