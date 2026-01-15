import os
import json

# İçerik dizini yolu
içerik_dir = 'c:\\Users\\PC\\Desktop\\mail giriş\\içerik'

# Tüm linkleri toplamak için boş bir liste
all_links = []

# 1. İçerik dizinindeki linkleri al
print("İçerik dizinindeki linkler alınıyor...")
for isim in os.listdir(içerik_dir):
    isim_path = os.path.join(içerik_dir, isim)
    if os.path.isdir(isim_path):
        # 1.txt dosyasını bul
        link_file = os.path.join(isim_path, '1.txt')
        if os.path.exists(link_file):
            with open(link_file, 'r', encoding='utf-8') as f:
                link = f.read().strip()
                # Linki listeye ekle
                all_links.append({
                    'isim': isim,
                    'link': link
                })
                print(f"  - {isim}: {link}")

# 2. Admin panelinden eklenen linkleri de eklemek için basit bir seçenek
print("\nAdmin panelinden eklenen linkleri eklemek istiyor musunuz? (y/n): ")
add_admin_links = input().lower()

if add_admin_links == 'y':
    admin_links_file = 'admin_links.json'
    if os.path.exists(admin_links_file):
        with open(admin_links_file, 'r', encoding='utf-8') as f:
            admin_links = json.load(f)
            all_links.extend(admin_links)
            print(f"\nAdmin panelinden {len(admin_links)} link eklendi.")
    else:
        print("\nadmin_links.json dosyası bulunamadı.")

# 3. Links JSON dosyasına yaz
with open('links.json', 'w', encoding='utf-8') as f:
    json.dump(all_links, f, ensure_ascii=False, indent=2)

print(f"\nToplam {len(all_links)} link bulundu ve links.json dosyasına kaydedildi.")
