<?php
// Veritabanı bağlantısı
try {
    $db = new PDO('sqlite:tv_database.sqlite');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $kanallar = $db->query("SELECT * FROM kanallar ORDER BY isim ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Veritabanı bulunamazsa veya hata olursa, boş bir kanallar dizisi oluştur
    $kanallar = [];
    // İsteğe bağlı olarak ekrana bir hata mesajı yazdırılabilir
    // die("Site şu anda bakımdadır.");
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Canlı TV İzle</title>
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Canlı TV</h1>
    <div class="player-wrapper" id="player-container">
        <p style="text-align: center; color: white; padding-top: 25%; font-size: 1.2em;">Lütfen izlemek için bir kanal seçin.</p>
    </div>

    <h2>Kanallar</h2>
    <div class="channel-list">
        <?php foreach ($kanallar as $kanal): ?>
            <div class="channel-box" onclick="playStream('<?= $kanal['tip'] ?>', '<?= htmlspecialchars($kanal['url']) ?>')">
                <img src="<?= htmlspecialchars($kanal['logo']) ?>" alt="<?= htmlspecialchars($kanal['isim']) ?> Logosu">
                <p><?= htmlspecialchars($kanal['isim']) ?></p>
            </div>
        <?php endforeach; ?>
        <?php if (empty($kanallar)): ?>
            <p>Gösterilecek kanal bulunamadı.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    const playerContainer = document.getElementById('player-container');
    let hls;

    function playStream(type, url) {
        // Önceki yayını temizle
        playerContainer.innerHTML = '';
        if (hls) {
            hls.destroy();
        }

        if (type === 'm3u8') {
            const video = document.createElement('video');
            video.id = 'videoPlayer';
            video.controls = true;
            playerContainer.appendChild(video);

            if (Hls.isSupported()) {
                hls = new Hls();
                hls.loadSource(url);
                hls.attachMedia(video);
                hls.on(Hls.Events.MANIFEST_PARSED, () => video.play());
            } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                video.src = url;
                video.addEventListener('loadedmetadata', () => video.play());
            }
        } else if (type === 'iframe') {
            const iframe = document.createElement('iframe');
            iframe.src = url;
            iframe.setAttribute('frameborder', '0');
            iframe.setAttribute('allow', 'autoplay; encrypted-media; picture-in-picture');
            iframe.setAttribute('allowfullscreen', '');
            playerContainer.appendChild(iframe);
        }
    }
</script>

</body>
</html>
