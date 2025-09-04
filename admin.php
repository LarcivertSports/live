<?php
// --- VERİTABANI BAĞLANTISI VE KURULUMU ---
try {
    // SQLite veritabanı dosyasına bağlanıyoruz. Dosya yoksa oluşturulur.
    $db = new PDO('sqlite:tv_database.sqlite');
    // Hata modunu ayarlıyoruz
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 'kanallar' tablosu yoksa oluşturuyoruz
    $db->exec("CREATE TABLE IF NOT EXISTS kanallar (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        isim TEXT NOT NULL,
        logo TEXT,
        tip TEXT NOT NULL,
        url TEXT NOT NULL
    )");
} catch (PDOException $e) {
    die("Veritabanı hatası: " . $e->getMessage());
}

// --- İŞLEMLER (EKLEME VE SİLME) ---

// Form gönderilmişse (yeni kanal ekleme)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['isim'])) {
    $isim = $_POST['isim'];
    $logo = $_POST['logo'];
    $tip = $_POST['tip'];
    $url = $_POST['url'];

    $stmt = $db->prepare("INSERT INTO kanallar (isim, logo, tip, url) VALUES (:isim, :logo, :tip, :url)");
    $stmt->execute([':isim' => $isim, ':logo' => $logo, ':tip' => $tip, ':url' => $url]);
    
    // Sayfanın yeniden post edilmesini önlemek için yönlendirme
    header("Location: admin.php");
    exit;
}

// Silme isteği gelmişse
if (isset($_GET['sil'])) {
    $id = $_GET['sil'];
    $stmt = $db->prepare("DELETE FROM kanallar WHERE id = :id");
    $stmt->execute([':id' => $id]);
    
    header("Location: admin.php");
    exit;
}

// --- VERİLERİ ÇEKME ---
$kanallar = $db->query("SELECT * FROM kanallar ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Paneli - Kanal Yönetimi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Kanal Yönetimi</h1>

    <h2>Mevcut Kanallar</h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Logo</th>
                <th>İsim</th>
                <th>Tip</th>
                <th>URL</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($kanallar as $kanal): ?>
            <tr>
                <td><img src="<?= htmlspecialchars($kanal['logo']) ?>" alt="<?= htmlspecialchars($kanal['isim']) ?>"></td>
                <td><?= htmlspecialchars($kanal['isim']) ?></td>
                <td><?= htmlspecialchars($kanal['tip']) ?></td>
                <td><?= htmlspecialchars($kanal['url']) ?></td>
                <td>
                    <a href="?sil=<?= $kanal['id'] ?>" class="delete-btn" onclick="return confirm('Bu kanalı silmek istediğinizden emin misiniz?');">Sil</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($kanallar)): ?>
                <tr>
                    <td colspan="5" style="text-align:center;">Henüz hiç kanal eklenmemiş.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2>Yeni Kanal Ekle</h2>
    <form class="admin-form" action="admin.php" method="POST">
        <div class="form-group">
            <label for="isim">Kanal Adı</label>
            <input type="text" id="isim" name="isim" required>
        </div>
        <div class="form-group">
            <label for="logo">Kanal Logo URL</label>
            <input type="text" id="logo" name="logo">
        </div>
        <div class="form-group">
            <label for="tip">Yayın Tipi</label>
            <select id="tip" name="tip" required>
                <option value="m3u8">HLS (m3u8)</option>
                <option value="iframe">Iframe (Gömülü)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="url">Yayın URL (m3u8 veya iframe linki)</label>
            <input type="text" id="url" name="url" required>
        </div>
        <div class="form-group">
            <button type="submit">Kanalı Ekle</button>
        </div>
    </form>
</div>

</body>
</html>
