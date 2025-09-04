<?php
// --- AYARLAR ---
$sifre = "12345"; // Lütfen bu şifreyi değiştirin!
$jsonDosyasi = 'kanallar.json';

// --- Basit Şifre Kontrolü ---
session_start();

// Çıkış yapma isteği
if (isset($_GET['cikis'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

if (isset($_POST['sifre'])) {
    if ($_POST['sifre'] == $sifre) {
        $_SESSION['giris_yapti'] = true;
    }
}

// Giriş yapılmamışsa şifre sorma formunu göster
if (!isset($_SESSION['giris_yapti'])) {
    echo '<form method="POST"><label>Şifre: </label><input type="password" name="sifre" /><input type="submit" value="Giriş Yap" /></form>';
    exit;
}

// --- Kanal Ekleme İşlemi ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kanal_adi'])) {
    // 1. Mevcut kanalları JSON dosyasından oku
    $kanallar = json_decode(file_get_contents($jsonDosyasi), true);

    // 2. Yeni kanal bilgilerini al
    $yeniKanal = [
        'id'        => time(), // Her kanal için benzersiz bir ID oluşturur
        'isim'      => $_POST['kanal_adi'],
        'logo'      => $_POST['logo_url'],
        'url'       => $_POST['yayin_url'],
        'tip'       => $_POST['yayin_tipi'] // 'hls' veya 'iframe'
    ];

    // 3. Yeni kanalı mevcut kanallar dizisine ekle
    $kanallar[] = $yeniKanal;

    // 4. Güncellenmiş diziyi tekrar JSON dosyasına yaz
    file_put_contents($jsonDosyasi, json_encode($kanallar, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // Sayfayı yenileyerek formun tekrar gönderilmesini engelle
    header('Location: admin.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Paneli - Kanal Ekle</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; }
        form { border: 1px solid #ccc; padding: 20px; border-radius: 5px; }
        input, select { width: 100%; padding: 8px; margin-bottom: 10px; box-sizing: border-box; }
        input[type="submit"] { background-color: #007bff; color: white; border: none; cursor: pointer; }
        h2 { text-align: center; }
        a { float: right; }
    </style>
</head>
<body>
    <a href="?cikis=1">Çıkış Yap</a>
    <h2>Yeni Kanal Ekle</h2>
    <form method="POST">
        <label for="kanal_adi">Kanal Adı:</label>
        <input type="text" id="kanal_adi" name="kanal_adi" required>

        <label for="logo_url">Kanal Logo URL'si:</label>
        <input type="text" id="logo_url" name="logo_url" placeholder="Örn: /logos/kanal_a.png" required>

        <label for="yayin_url">Yayın Adresi (m3u8 veya iframe URL):</label>
        <input type="text" id="yayin_url" name="yayin_url" required>

        <label for="yayin_tipi">Yayın Tipi:</label>
        <select id="yayin_tipi" name="yayin_tipi">
            <option value="hls">HLS (.m3u8)</option>
            <option value="iframe">iframe (Gömülü)</option>
        </select>

        <input type="submit" value="Kanalı Ekle">
    </form>
</body>
</html>
