<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Dosya yolları
$linksFile = 'links.json';
$photosDir = 'photos/';

// Eğer photos dizini yoksa oluştur
if (!is_dir($photosDir)) {
    mkdir($photosDir, 0755, true);
}

// Eğer links.json yoksa boş bir dizi oluştur
if (!file_exists($linksFile)) {
    file_put_contents($linksFile, json_encode([], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

// İşlem türünü al
$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        handleAddLink();
        break;
    case 'edit':
        handleEditLink();
        break;
    case 'delete':
        handleDeleteLink();
        break;
    case 'get':
        handleGetLinks();
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Geçersiz işlem']);
}

function handleAddLink() {
    global $linksFile, $photosDir;
    
    $name = $_POST['name'] ?? '';
    $url = $_POST['url'] ?? '';
    
    if (empty($name) || empty($url)) {
        echo json_encode(['success' => false, 'message' => 'İsim ve link alanları zorunludur']);
        return;
    }
    
    // Fotoğraf yükleme
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['success' => false, 'message' => 'Fotoğraf yüklenemedi']);
        return;
    }
    
    $file = $_FILES['photo'];
    $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($fileExt, $allowedExts)) {
        echo json_encode(['success' => false, 'message' => 'Yalnızca resim dosyaları yüklenebilir']);
        return;
    }
    
    // Benzersiz dosya adı oluştur
    $fileName = uniqid() . '.' . $fileExt;
    $filePath = $photosDir . $fileName;
    
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        echo json_encode(['success' => false, 'message' => 'Fotoğraf kaydedilemedi']);
        return;
    }
    
    // Linkleri oku
    $links = json_decode(file_get_contents($linksFile), true);
    
    // Yeni linki ekle
    $newLink = [
        'isim' => $name,
        'link' => $url,
        'foto' => 'photos/' . $fileName
    ];
    
    $links[] = $newLink;
    
    // Dosyaya yaz
    file_put_contents($linksFile, json_encode($links, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    
    echo json_encode(['success' => true, 'message' => 'Link eklendi', 'link' => $newLink]);
}

function handleEditLink() {
    global $linksFile, $photosDir;
    
    $index = intval($_POST['index'] ?? -1);
    $name = $_POST['name'] ?? '';
    $url = $_POST['url'] ?? '';
    
    if ($index < 0 || empty($name) || empty($url)) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz parametreler']);
        return;
    }
    
    // Linkleri oku
    $links = json_decode(file_get_contents($linksFile), true);
    
    if (!isset($links[$index])) {
        echo json_encode(['success' => false, 'message' => 'Link bulunamadı']);
        return;
    }
    
    // Mevcut fotoğrafı sakla
    $currentPhoto = $links[$index]['foto'];
    
    // Eğer yeni fotoğraf yüklendiyseniz
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['photo'];
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($fileExt, $allowedExts)) {
            echo json_encode(['success' => false, 'message' => 'Yalnızca resim dosyaları yüklenebilir']);
            return;
        }
        
        // Benzersiz dosya adı oluştur
        $fileName = uniqid() . '.' . $fileExt;
        $filePath = $photosDir . $fileName;
        
        if (!move_uploaded_file($file['tmp_name'], $filePath)) {
            echo json_encode(['success' => false, 'message' => 'Fotoğraf kaydedilemedi']);
            return;
        }
        
        // Eski fotoğrafı sil
        if ($currentPhoto && file_exists($currentPhoto)) {
            unlink($currentPhoto);
        }
        
        $newPhoto = 'photos/' . $fileName;
    } else {
        $newPhoto = $currentPhoto;
    }
    
    // Linki güncelle
    $links[$index] = [
        'isim' => $name,
        'link' => $url,
        'foto' => $newPhoto
    ];
    
    // Dosyaya yaz
    file_put_contents($linksFile, json_encode($links, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    
    echo json_encode(['success' => true, 'message' => 'Link güncellendi']);
}

function handleDeleteLink() {
    global $linksFile;
    
    $index = intval($_GET['index'] ?? -1);
    
    if ($index < 0) {
        echo json_encode(['success' => false, 'message' => 'Geçersiz index']);
        return;
    }
    
    // Linkleri oku
    $links = json_decode(file_get_contents($linksFile), true);
    
    if (!isset($links[$index])) {
        echo json_encode(['success' => false, 'message' => 'Link bulunamadı']);
        return;
    }
    
    // Fotoğrafı sil
    $photo = $links[$index]['foto'];
    if ($photo && file_exists($photo)) {
        unlink($photo);
    }
    
    // Linki sil
    array_splice($links, $index, 1);
    
    // Dosyaya yaz
    file_put_contents($linksFile, json_encode($links, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    
    echo json_encode(['success' => true, 'message' => 'Link silindi']);
}

function handleGetLinks() {
    global $linksFile;
    
    $links = json_decode(file_get_contents($linksFile), true);
    echo json_encode(['success' => true, 'links' => $links]);
}
?>