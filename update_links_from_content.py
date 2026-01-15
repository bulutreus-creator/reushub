import os
import json

def update_links_from_content():
    """içerik dizinindeki dosyaları tarayarak links.json'u günceller"""
    
    # Ana dizin yolu
    base_dir = os.path.join(os.getcwd(), 'içerik')
    
    # links.json dosyasının yolu
    links_json_path = os.path.join(os.getcwd(), 'links.json')
    
    # Mevcut linkleri yükle
    try:
        with open(links_json_path, 'r', encoding='utf-8') as f:
            links = json.load(f)
    except FileNotFoundError:
        links = []
    except json.JSONDecodeError:
        links = []
    
    # Yeni linkleri saklayacağımız liste
    new_links = []
    
    # içerik dizinindeki alt dizinleri tarla
    for item_name in os.listdir(base_dir):
        item_path = os.path.join(base_dir, item_name)
        
        if os.path.isdir(item_path):
            # Dizin adı = isim
            isim = item_name
            link = ""
            foto = ""
            
            # Dizin içindeki dosyaları tarla
            for file_name in os.listdir(item_path):
                file_path = os.path.join(item_path, file_name)
                
                # Link dosyası (txt)
                if file_name.endswith('.txt'):
                    with open(file_path, 'r', encoding='utf-8') as f:
                        link = f.read().strip()
                # Fotoğraf dosyası (jpeg, jpg, png, webp)
                elif file_name.endswith(('.jpeg', '.jpg', '.png', '.webp')):
                    # Fotoğraf yolunu relative olarak kaydet
                    foto = f'içerik/{item_name}/{file_name}'
            
            # Yeni linki oluştur
            new_link = {
                "isim": isim,
                "link": link,
                "foto": foto,
                "clickCount": 0
            }
            
            new_links.append(new_link)
    
    # links.json'u güncelle
    with open(links_json_path, 'w', encoding='utf-8') as f:
        json.dump(new_links, f, ensure_ascii=False, indent=2)
    
    print(f"✅ links.json güncellendi! {len(new_links)} link eklendi.")

if __name__ == "__main__":
    update_links_from_content()