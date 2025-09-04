// Bu dosya senin kanal listen. Yeni kanal eklemek için sadece bu dosyayı düzenle.
// Süslü parantez {} içindeki bir bloğu kopyalayıp sona bir virgül , ekleyerek yeni kanal ekleyebilirsin.

const kanallar = [
    {
        isim: "Kanal 1 (M3U8)",
        logo: "https://via.placeholder.com/100x60.png?text=Kanal+1",
        tip: "m3u8", // Yayın tipi: 'm3u8' veya 'iframe' olabilir
        url: "https://test-streams.mux.dev/x36xhzz/x36xhzz.m3u8"
    },
    {
        isim: "Kanal 2 (Iframe)",
        logo: "https://via.placeholder.com/100x60.png?text=Kanal+2",
        tip: "iframe",
        url: "https://www.youtube.com/embed/live_stream?channel=UCv_v_8_-_y_S2pk4aXlO9MA&autoplay=1"
    },
    {
        isim: "Başka Bir Kanal",
        logo: "https://via.placeholder.com/100x60.png?text=Kanal+3",
        tip: "m3u8",
        url: "https://demo.unified-streaming.com/k8s/features/stable/video/tears-of-steel/tears-of-steel.ism/.m3u8"
    }
    // YENİ KANAL EKLEMEK İÇİN BURAYA VİRGÜL KOYUP YUKARIDAKİ BLOKLARDAN BİRİNİ KOPYALAYIP YAPIŞTIR
];
