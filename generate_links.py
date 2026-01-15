import os
import json

# İçerik dizini yolu
içerik_dir = 'c:\\Users\\PC\\Desktop\\mail giriş\\içerik'

links = []

# İçerik dizinindeki tüm klasörleri gezin
for isim in os.listdir(içerik_dir):
    isim_path = os.path.join(içerik_dir, isim)
    if os.path.isdir(isim_path):
        # 1.txt dosyasını bul
        link_file = os.path.join(isim_path, '1.txt')
        if os.path.exists(link_file):
            with open(link_file, 'r', encoding='utf-8') as f:
                link = f.read().strip()
                # Linki listeye ekle
                links.append({
                    'isim': isim,
                    'link': link
                })

# Links JSON dosyasına yaz
with open('links.json', 'w', encoding='utf-8') as f:
    json.dump(links, f, ensure_ascii=False, indent=2)

print(f'Toplam {len(links)} link bulundu ve links.json dosyasına kaydedildi.')
