<?php
$jsonDosyasi = 'kanallar.json';
$kanallar = json_decode(file_get_contents($jsonDosyasi), true);

// URL'den seçili kanal ID'sini al
$seciliKanal = null;
if (isset($_GET['kanal_id'])) {
    foreach ($kanallar as $kanal) {
        if ($kanal['id'] == $_GET['kanal_id']) {
            $seciliKanal = $kanal;
            break;
        }
    }
} else if (!empty($kanallar)) {
    // Eğer hiçbir kanal seçilmemişse, ilk kanalı varsayılan olarak seç
    $seciliKanal = $kanallar[0];
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Yayın Sitesi</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
</head>
<body>
    <div class="container">
        <aside class="kanal-listesi">
            <h3>KANALLAR</h3>
            <ul>
                <?php foreach ($kanallar as $kanal): ?>
                    <li>
                        <a href="?kanal_id=<?php echo $kanal['id']; ?>">
                            <img src="<?php echo htmlspecialchars($kanal['logo']); ?>" alt="<?php echo htmlspecialchars($kanal['isim']); ?>">
                            <span><?php echo htmlspecialchars($kanal['isim']); ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </aside>

        <main class="player-alani">
            <?php if ($seciliKanal): ?>
                <div id="player-container">
                    <video id="hls-player" style="display: none; width: 100%; height: 100%;"></video>
                    </div>
            <?php else: ?>
                <p>İzlemek için lütfen bir kanal seçin veya admin panelinden kanal ekleyin.</p>
            <?php endif; ?>
        </main>
    </div>

    <script>
    // PHP'den seçili kanal bilgilerini JavaScript'e aktarıyoruz
    const seciliKanal = <?php echo json_encode($seciliKanal); ?>;
    const playerContainer = document.getElementById('player-container');
    const videoElement = document.getElementById('hls-player');

    if (seciliKanal) {
        if (seciliKanal.tip === 'hls') {
            // Yayın tipi HLS ise HLS.js oynatıcısını kur
            videoElement.style.display = 'block'; // video etiketini görünür yap

            if (Hls.isSupported()) {
                const hls = new Hls();
                hls.loadSource(seciliKanal.url);
                hls.attachMedia(videoElement);
                hls.on(Hls.Events.MANIFEST_PARSED, function() {
                    videoElement.play();
                });
            } else if (videoElement.canPlayType('application/vnd.apple.mpegurl')) {
                // HLS.js desteklenmiyorsa (Safari gibi), tarayıcının kendi oynatıcısını kullan
                videoElement.src = seciliKanal.url;
                videoElement.addEventListener('loadedmetadata', function() {
                    videoElement.play();
                });
            }
        } else if (seciliKanal.tip === 'iframe') {
            // Yayın tipi iframe ise, bir iframe elementi oluştur ve ekle
            playerContainer.innerHTML = ''; // İçeriği temizle
            const iframe = document.createElement('iframe');
            iframe.src = seciliKanal.url;
            iframe.width = '100%';
            iframe.height = '100%';
            iframe.frameBorder = '0';
            iframe.allow = 'autoplay; encrypted-media';
            iframe.allowFullscreen = true;
            playerContainer.appendChild(iframe);
        }
    }
    </script>
</body>
</html>
