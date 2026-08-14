import re

def remove_starvie_from_marquee(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # The marquee uses this array:
    # @foreach(['alpha', 'bullpadel', 'arronax', 'babolat', 'head', 'nox', 'starvie'] as $brand)
    # We only want to remove it from the marquee array.
    
    # We can just look for @foreach(['alpha', 'bullpadel', 'arronax', 'babolat', 'head', 'nox', 'starvie'] as $brand)
    # and replace it with @foreach(['alpha', 'bullpadel', 'arronax', 'babolat', 'head', 'nox'] as $brand)
    
    old_array = "['alpha', 'bullpadel', 'arronax', 'babolat', 'head', 'nox', 'starvie']"
    new_array = "['alpha', 'bullpadel', 'arronax', 'babolat', 'head', 'nox']"
    
    # Check if we should only replace it if it's right after @foreach
    content = content.replace(f"@foreach({old_array} as $brand)", f"@foreach({new_array} as $brand)")

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

remove_starvie_from_marquee('resources/views/pages/home_luxury.blade.php')
print("Marquee updated.")
