import pandas as pd
import re

file_path = 'mass_update_basic_info_87778896_20260427110031 (1).xlsx'
try:
    df = pd.read_excel(file_path, skiprows=2)
except Exception as e:
    df = pd.read_excel(file_path, engine='calamine', header=None)

mappings = {}
started = False
for index, row in df.iterrows():
    if not started:
        if str(row[0]).strip() == 'Kode Produk':
            started = True
        continue
    
    name = str(row[2]).strip()
    desc = str(row[3]).strip()
    if name != 'nan' and desc != 'nan' and name and desc:
        mappings[name] = desc

def shorten_and_classify(text):
    text = re.sub(r'\s+', ' ', text)
    sentences = re.split(r'(?<=[.!?])\s+', text)
    sentences = [s.strip() for s in sentences if len(s.strip()) > 5]
    
    siapa = []
    keunggulan = []
    feel = []
    
    siapa_kw = ['designed for', 'perfect for', 'ideal for', 'players', 'intermediate', 'advanced', 'beginner', 'professional', 'suited for']
    feel_kw = ['feel', 'touch', 'comfort', 'power', 'control', 'balance', 'responsive', 'handling', 'vibration', 'impact', 'spin']
    
    used_indices = set()
    
    for i, s in enumerate(sentences):
        s_lower = s.lower()
        if any(kw in s_lower for kw in siapa_kw) and ('player' in s_lower or 'beginner' in s_lower or 'intermediate' in s_lower or 'advanced' in s_lower):
            if len(siapa) < 2:
                siapa.append(s)
                used_indices.add(i)
                
    for i, s in enumerate(sentences):
        if i in used_indices: continue
        s_lower = s.lower()
        if any(kw in s_lower for kw in feel_kw):
            if len(feel) < 2:
                feel.append(s)
                used_indices.add(i)
                
    for i, s in enumerate(sentences):
        if i in used_indices: continue
        if len(keunggulan) < 2:
            keunggulan.append(s)
            used_indices.add(i)
            
    if not siapa and len(sentences) > 0:
        for i, s in enumerate(sentences):
            if i not in used_indices:
                siapa.append(s)
                used_indices.add(i)
                break
                
    if not feel and len(sentences) > 0:
        for i, s in enumerate(sentences):
            if i not in used_indices:
                feel.append(s)
                used_indices.add(i)
                break

    if not keunggulan and len(sentences) > 0:
        for i, s in enumerate(sentences):
            if i not in used_indices:
                keunggulan.append(s)
                used_indices.add(i)
                break
                
    p1 = " ".join(siapa)
    p2 = " ".join(keunggulan)
    p3 = " ".join(feel)
    
    html = ""
    if p1: html += f"<p>{p1}</p>"
    if p2: html += f"<p>{p2}</p>"
    if p3: html += f"<p>{p3}</p>"
    
    return html

def format_description(name, raw_desc):
    clean_name = name.replace('Nora Dynamic Sport', '').replace('Nora Dynamic Sports', '').replace('Nora Padel', '').strip()
    html = f"<h2>About {clean_name}</h2>"
    
    lines = [line.strip() for line in str(raw_desc).split('\n') if line.strip()]
    if lines and (lines[0].lower() in name.lower() or name.lower() in lines[0].lower() or len(lines[0]) < 50):
        raw_desc = " ".join(lines[1:])
    else:
        raw_desc = " ".join(lines)
        
    html += shorten_and_classify(raw_desc)
    return html

seeder_path = 'database/seeders/ShopeeProductsSeeder.php'
with open(seeder_path, 'r', encoding='utf-8') as f:
    content = f.read()

def replacer(match):
    full_match = match.group(0)
    name_match = re.search(r"'name'\s*=>\s*'(.*?)',", full_match)
    if not name_match: return full_match
        
    name = name_match.group(1).strip()
    
    matched_desc = None
    for k, v in mappings.items():
        if name.lower() in k.lower() or k.lower() in name.lower():
            matched_desc = v
            break
            
    if not matched_desc:
        clean_name = name.replace('Nora Dynamic Sport', '').replace('Nora Dynamic Sports', '').replace('Nora Padel', '').strip()
        for k, v in mappings.items():
            if clean_name.lower() in k.lower():
                matched_desc = v
                break
                
    if matched_desc:
        formatted = format_description(name, matched_desc)
        formatted = formatted.replace("'", "\\'")
        new_block = re.sub(r"('description'\s*=>\s*)'(.*?)'(,?)", r"\g<1>'" + formatted + r"'\g<3>", full_match, flags=re.DOTALL)
        return new_block
            
    return full_match

new_content = re.sub(r"\[\s*'name'\s*=>.*?\]", replacer, content, flags=re.DOTALL)

with open(seeder_path, 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Updated seeder successfully with shortened descriptions!")
