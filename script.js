document.addEventListener('DOMContentLoaded', () => {
    const cityInput = document.getElementById('city-input');
    const searchButton = document.getElementById('search-button');
    const cityNameElement = document.getElementById('city-name');
    const tempInfoElement = document.getElementById('temperature-info');
    const windInfoElement = document.getElementById('wind-info');
    const hourlyListElement = document.getElementById('hourly-list');

    // Kullanıcının girdiği şehir adına göre koordinatları getiren fonksiyon
    async function getCoordinates(city) {
        try {
            // Google Maps veya OpenWeatherMap gibi bir coğrafi kodlama API'si kullanılabilir
            // Basitlik için varsayılan koordinatları kullanalım
            // Gerçek bir proje için burada API entegrasyonu yapmanız gerekir.
            
            // Örnek olarak İstanbul koordinatlarını kullanıyoruz
            return { latitude: 41.015137, longitude: 28.979530 };
        } catch (error) {
            console.error("Koordinat alınırken hata oluştu:", error);
            return null;
        }
    }

    // Hava durumu verilerini Open-Meteo API'sinden çeken ana fonksiyon
    async function getWeatherData(latitude, longitude) {
        const apiUrl = `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&current=temperature_2m,wind_speed_10m&hourly=temperature_2m,relative_humidity_2m,wind_speed_10m`;
        
        try {
            const response = await fetch(apiUrl);
            const data = await response.json();
            
            // Hava durumu bilgilerini ekrana yerleştirme
            const currentTemp = data.current.temperature_2m;
            const currentWindSpeed = data.current.wind_speed_10m;
            
            tempInfoElement.innerText = `Sıcaklık: ${currentTemp}°C`;
            windInfoElement.innerText = `Rüzgar Hızı: ${currentWindSpeed} km/s`;
            
            // Saatlik tahminleri listeye ekleme
            hourlyListElement.innerHTML = '';
            for (let i = 0; i < 24; i++) {
                const hour = data.hourly.time[i].substring(11, 16);
                const temp = data.hourly.temperature_2m[i];
                const humidity = data.hourly.relative_humidity_2m[i];
                const wind = data.hourly.wind_speed_10m[i];

                const listItem = document.createElement('li');
                listItem.innerHTML = `<strong>${hour}</strong> - Sıcaklık: ${temp}°C, Nem: %${humidity}, Rüzgar: ${wind} km/s`;
                hourlyListElement.appendChild(listItem);
            }
            
        } catch (error) {
            console.error("Hava durumu verisi çekilirken bir hata oluştu:", error);
            tempInfoElement.innerText = "Veri alınamadı, lütfen tekrar deneyin.";
            hourlyListElement.innerHTML = '';
        }
    }

    // Arama butonuna tıklama olayını dinleme
    searchButton.addEventListener('click', async () => {
        const city = cityInput.value.trim();
        if (city) {
            cityNameElement.innerText = city.charAt(0).toUpperCase() + city.slice(1);
            const coords = await getCoordinates(city);
            if (coords) {
                getWeatherData(coords.latitude, coords.longitude);
            } else {
                cityNameElement.innerText = "Geçersiz şehir";
            }
        }
    });

    // Sayfa yüklendiğinde varsayılan bir şehrin hava durumunu gösterelim
    // Örneğin, İzmir'in koordinatları: 38.423733, 27.142838
    cityNameElement.innerText = "İzmir";
    getWeatherData(38.423733, 27.142838);
});
