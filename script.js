// HTML elemanlarına ID'leri üzerinden ulaşıyoruz
const videoPlayer = document.getElementById('video-player');
const m3u8UrlInput = document.getElementById('m3u8-url');
const playButton = document.getElementById('play-button');

// Butona tıklandığında ne olacağını belirleyen olay dinleyici
playButton.addEventListener('click', () => {
    // Input alanındaki linki alıyoruz
    const m3u8Url = m3u8UrlInput.value.trim(); // trim() ile başındaki ve sonundaki boşlukları temizliyoruz

    // Eğer link boş ise kullanıcıyı uyar ve fonksiyondan çık
    if (!m3u8Url) {
        alert("Lütfen geçerli bir M3U8 linki girin.");
        return;
    }

    // Tarayıcının HLS.js'i destekleyip desteklemediğini kontrol et
    if (Hls.isSupported()) {
        // Yeni bir HLS oynatıcı nesnesi oluştur
        const hls = new Hls();
        
        // M3U8 linkini HLS nesnesine yükle
        hls.loadSource(m3u8Url);
        
        // HLS nesnesini HTML'deki <video> elemanına bağla
        hls.attachMedia(videoPlayer);
        
        // Medya bağlandıktan sonra videoyu otomatik olarak oynat
        hls.on(Hls.Events.MEDIA_ATTACHED, () => {
            console.log("Video ve HLS başarıyla bağlandı!");
            videoPlayer.play();
        });

    } else if (videoPlayer.canPlayType('application/vnd.apple.mpegurl')) {
        // HLS.js desteklenmiyorsa (örn: Safari), tarayıcının kendi desteğini kullan
        // Safari gibi tarayıcılar m3u8'i doğal olarak destekler
        videoPlayer.src = m3u8Url;
        videoPlayer.addEventListener('loadedmetadata', () => {
            videoPlayer.play();
        });
    } else {
        // Hiçbir şekilde HLS desteği yoksa kullanıcıyı bilgilendir
        alert("Üzgünüz, tarayıcınız HLS yayınlarını desteklemiyor.");
    }
});
