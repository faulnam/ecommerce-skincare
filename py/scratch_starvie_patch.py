import re

def patch_file(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()

    # 1. Update the array of brands in foreach loops (home_luxury.blade.php mostly)
    content = content.replace(
        "['alpha', 'bullpadel', 'arronax', 'babolat', 'head', 'nox']",
        "['alpha', 'bullpadel', 'arronax', 'babolat', 'head', 'nox', 'starvie']"
    )

    # 2. Update array in new-arrivals.blade.php
    content = content.replace(
        "['Bullpadel','Babolat','Nox','Alpha','Zephyr','Arronax']",
        "['Bullpadel','Babolat','Nox','Alpha','Zephyr','Arronax','Starvie']"
    )

    # 3. Add Starvie desktop filter chip (home_luxury.blade.php format)
    arronax_chip_home = """<a href="{{ route('brand.show', 'arronax') }}" onclick="event.preventDefault(); window.history.pushState(null, '', this.href); applyFilter('brand', 'Arronax');" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-zinc-600 transition hover:border-black hover:text-black" data-brand="Arronax">Arronax</a>"""
    starvie_chip_home = """<a href="{{ route('brand.show', 'starvie') }}" onclick="event.preventDefault(); window.history.pushState(null, '', this.href); applyFilter('brand', 'Starvie');" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-zinc-600 transition hover:border-black hover:text-black" data-brand="Starvie">Starvie</a>"""
    
    if arronax_chip_home in content:
        content = content.replace(
            arronax_chip_home,
            arronax_chip_home + '\n                                      ' + starvie_chip_home
        )

    # 4. Add Starvie mobile checkbox (home_luxury.blade.php format)
    arronax_mobile_home = """<label class="mobile-filter-chip cursor-pointer">
                                              <input type="checkbox" name="mobileBrand" value="Arronax" class="hidden peer" onchange="updateMobileFilterCount()">
                                              <span class="block px-3 py-1.5 text-xs border border-zinc-200 rounded-full text-zinc-600 peer-checked:bg-black peer-checked:text-white peer-checked:border-black transition">Arronax</span>
                                          </label>"""
    starvie_mobile_home = """<label class="mobile-filter-chip cursor-pointer">
                                              <input type="checkbox" name="mobileBrand" value="Starvie" class="hidden peer" onchange="updateMobileFilterCount()">
                                              <span class="block px-3 py-1.5 text-xs border border-zinc-200 rounded-full text-zinc-600 peer-checked:bg-black peer-checked:text-white peer-checked:border-black transition">Starvie</span>
                                          </label>"""
    
    if arronax_mobile_home in content:
        content = content.replace(
            arronax_mobile_home,
            arronax_mobile_home + '\n                                          ' + starvie_mobile_home
        )

    # 5. Add Starvie desktop filter chip (new-arrivals.blade.php format)
    arronax_chip_new = """<a href="{{ route('brand.show', 'arronax') }}" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-zinc-600 transition hover:border-black hover:text-black {{ $selectedBrand === 'Arronax' ? 'bg-black text-white border-black' : '' }}" data-brand="Arronax">Arronax</a>"""
    starvie_chip_new = """<a href="{{ route('brand.show', 'starvie') }}" class="filter-chip rounded-full border border-zinc-200 px-3 py-1 text-xs text-zinc-600 transition hover:border-black hover:text-black {{ $selectedBrand === 'Starvie' ? 'bg-black text-white border-black' : '' }}" data-brand="Starvie">Starvie</a>"""
    
    if arronax_chip_new in content:
        content = content.replace(
            arronax_chip_new,
            arronax_chip_new + '\n                                  ' + starvie_chip_new
        )

    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)

patch_file('resources/views/pages/home_luxury.blade.php')
patch_file('resources/views/pages/new-arrivals.blade.php')
print("Successfully patched both files.")
