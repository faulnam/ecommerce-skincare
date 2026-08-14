import re

with open('database/seeders/ShopeeProductsSeeder.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace all occurrences of [:\s]+([^\n\r.]+) with something safer:
# We will change [:\s]+ to \s*[:\-]\s* so it strictly requires a colon or dash.
# We will change ([^\n\r.]+) to ([a-zA-Z0-9\s\(\)\-\/]{1,40}) which only matches up to 40 characters of normal text.

def fix_regex(match):
    # match.group(1) is the spec name like Shape, Balance, Weight
    keyword = match.group(1)
    return r"\b" + keyword + r"\s*[:\-]\s*([a-zA-Z0-9\s\(\)\-\/]{1,40})"

# The original looks like: preg_match('/\bShape[:\s]+([^\n\r.]+)/i'
# Or \bPlay\s*Style[:\s]+([^\n\r.]+)
# We can do a broader regex replace for the structured formats:

content = re.sub(r"'\/\\\\b([a-zA-Z\\s\(\)\|]+)\[:\\\\s\]\+\(\[\^\\\\n\\\\r\.\]\+\)\/i'", r"'\/\\b\1\\s*[:\\-]\\s*([a-zA-Z0-9\\s\(\)\\-\\/]{1,40})/i'", content)


# Also fix the cleanup section to be absolutely bulletproof:
old_cleanup = """        // Clean up the extracted value
        if ($value) {
            $value = trim($value);
            // Remove common trailing junk
            $value = preg_replace('/[\s,\.]+$/', '', $value);
            // Truncate if too long
            if (mb_strlen($value) > 150) {
                $value = mb_substr($value, 0, 150);
            }
            return $value ?: null;
        }"""

new_cleanup = """        // Clean up the extracted value
        if ($value) {
            $value = trim($value);
            
            // Fix data leak bug where regex captures entire paragraph
            if (mb_strlen($value) > 40) {
                // Split by common sentence separators to isolate the actual spec value
                $parts = preg_split('/[,\|\-—:]/', $value);
                if (count($parts) > 1 && mb_strlen(trim($parts[0])) > 2) {
                    $value = trim($parts[0]);
                }
                
                // Final safety limit to guarantee no DB truncated text leakage
                if (mb_strlen($value) > 40) {
                    $value = mb_substr($value, 0, 40);
                }
            }
            
            // Remove common trailing junk
            $value = preg_replace('/[\s,\.]+$/', '', $value);
            
            return $value ?: null;
        }"""

content = content.replace(old_cleanup, new_cleanup)

with open('database/seeders/ShopeeProductsSeeder.php', 'w', encoding='utf-8') as f:
    f.write(content)
print("Seeder fixed successfully!")
