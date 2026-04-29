document.addEventListener('DOMContentLoaded', () => {
    // Theme Toggle
    const themeToggle = document.getElementById('themeToggle');
    const body = document.documentElement;
    const themeIcon = themeToggle.querySelector('i');

    const savedTheme = localStorage.getItem('theme') || 'light';
    body.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);

    themeToggle.addEventListener('click', () => {
        const currentTheme = body.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        body.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        updateThemeIcon(newTheme);
        // Ensure background is updated on theme toggle if we have a current condition
        if(currentConditionForBg && currentIsDayForBg !== null) {
            updateBackground(currentConditionForBg, currentIsDayForBg);
        }
    });

    function updateThemeIcon(theme) {
        if (theme === 'dark') {
            themeIcon.classList.replace('fa-moon', 'fa-sun');
        } else {
            themeIcon.classList.replace('fa-sun', 'fa-moon');
        }
    }

    // Modal Logic
    const subscribeBtn = document.getElementById('subscribeBtn');
    const subscribeModal = document.getElementById('subscribeModal');
    const closeModal = document.getElementById('closeModal');
    const subscribeForm = document.getElementById('subscribeForm');
    const btnGoogle = document.querySelector('.btn-google');

    // --- Authentication State Management ---
    function checkAuthState() {
        const loggedInUser = localStorage.getItem('skycast_user');
        if (loggedInUser) {
            subscribeBtn.innerHTML = '<i class="fa-solid fa-user"></i> Profile';
            subscribeBtn.style.background = '#10b981';
            
            // Update modal for logged-in user
            const modalContent = document.querySelector('.modal-content');
            modalContent.querySelector('h2').innerText = 'User Profile';
            modalContent.querySelector('p').innerText = `Logged in as: ${loggedInUser}`;
            
            const formContainer = document.getElementById('formContainer');
            if (formContainer) formContainer.style.display = 'none';
            if (btnGoogle) btnGoogle.style.display = 'none';
            const dividers = document.querySelectorAll('.divider');
            dividers.forEach(d => d.style.display = 'none');
            
            // Add or show Logout button
            if (!document.getElementById('btnLogout')) {
                const logoutBtn = document.createElement('button');
                logoutBtn.id = 'btnLogout';
                logoutBtn.className = 'btn-submit';
                logoutBtn.style.background = '#ef4444';
                logoutBtn.style.marginTop = '1.5rem';
                logoutBtn.innerHTML = '<i class="fa-solid fa-right-from-bracket"></i> Logout Securely';
                logoutBtn.addEventListener('click', () => {
                    localStorage.removeItem('skycast_user');
                    window.location.reload();
                });
                modalContent.appendChild(logoutBtn);
            }
        }
    }
    
    checkAuthState(); // Run on page load

    subscribeBtn.addEventListener('click', () => {
        subscribeModal.classList.add('active');
    });
    closeModal.addEventListener('click', () => subscribeModal.classList.remove('active'));
    subscribeModal.addEventListener('click', (e) => { if (e.target === subscribeModal) subscribeModal.classList.remove('active'); });

    // UI Toggle between Login and Register
    const loginForm = document.getElementById('loginForm');
    const showLogin = document.getElementById('showLogin');
    const showRegister = document.getElementById('showRegister');
    const modalTitle = document.querySelector('.modal-content h2');
    const modalDesc = document.querySelector('.modal-content p');

    if (showLogin && showRegister) {
        showLogin.addEventListener('click', () => {
            subscribeForm.style.display = 'none';
            loginForm.style.display = 'block';
            modalTitle.innerText = 'Welcome Back';
            modalDesc.innerText = 'Login to access your SkyCast profile.';
        });
        showRegister.addEventListener('click', () => {
            loginForm.style.display = 'none';
            subscribeForm.style.display = 'block';
            modalTitle.innerText = 'Join SkyCast';
            modalDesc.innerText = 'Get daily weather updates directly to your inbox.';
        });
    }

    // Handle Subscription (Register)
    subscribeForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = document.getElementById('emailInput').value;
        const password = document.getElementById('passwordInput').value;
        const btnSubmit = subscribeForm.querySelector('.btn-submit');
        const originalText = btnSubmit.innerText;
        
        btnSubmit.innerText = 'Creating Account...';
        btnSubmit.disabled = true;

        try {
            const response = await fetch('php/subscribe.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: email, password: password })
            });
            const data = await response.json();

            if (data.success) {
                alert(data.message);
                localStorage.setItem('skycast_user', email); // Save session
                subscribeModal.classList.remove('active');
                subscribeForm.reset();
                checkAuthState(); // Update UI to Profile mode
            } else {
                alert(data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Something went wrong. Please try again later.');
        } finally {
            btnSubmit.innerText = originalText;
            btnSubmit.disabled = false;
        }
    });

    // Handle Login
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const btnSubmit = loginForm.querySelector('.btn-submit');
            const originalText = btnSubmit.innerText;
            
            btnSubmit.innerText = 'Logging in...';
            btnSubmit.disabled = true;

            try {
                const response = await fetch('php/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: email, password: password })
                });
                const data = await response.json();

                if (data.success) {
                    localStorage.setItem('skycast_user', email);
                    subscribeModal.classList.remove('active');
                    loginForm.reset();
                    checkAuthState();
                    alert(data.message);
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Login failed. Please check your connection.');
            } finally {
                btnSubmit.innerText = originalText;
                btnSubmit.disabled = false;
            }
        });
    }

    btnGoogle.addEventListener('click', () => {
        btnGoogle.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Redirecting to Google...';
        // Redirect user to the real Google login page
        window.location.href = 'php/google_login.php';
    });

    // --- Weather App Logic ---
    const searchInput = document.getElementById('searchInput');
    const searchBtn = document.getElementById('searchBtn');
    const locationBtn = document.getElementById('locationBtn');

    // UI Elements
    const cityEl = document.getElementById('cityName');
    const dateEl = document.getElementById('date');
    const tempMainEl = document.getElementById('tempMain');
    const conditionTextEl = document.getElementById('conditionText');
    const feelsLikeEl = document.getElementById('feelsLike');
    const weatherIconLargeEl = document.querySelector('.weather-icon-large i');
    
    // Highlight Elements
    const windValEl = document.getElementById('windVal');
    const humidityValEl = document.getElementById('humidityVal');
    const humidityBarEl = document.getElementById('humidityBar');
    const sunriseValEl = document.getElementById('sunriseVal');
    const sunsetValEl = document.getElementById('sunsetVal');
    const uvValEl = document.getElementById('uvVal');
    const uvStatusEl = document.getElementById('uvStatus');
    
    // Forecast Element
    const dailyForecastEl = document.getElementById('dailyForecast');

    let currentConditionForBg = '';
    let currentIsDayForBg = null;

    function formatDate(dateObj) {
        return dateObj.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    }
    dateEl.innerText = formatDate(new Date());

    function getShortDay(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { weekday: 'short' });
    }

    function formatTime(isoString) {
        if (!isoString) return '--:--';
        const date = new Date(isoString);
        return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    }

    function getWeatherDetails(code, isDay) {
        let condition = "Clear"; let icon = "fa-sun"; let color = "#FFD700";
        if (isDay === 0) { icon = "fa-moon"; color = "#c4c4c4"; }

        if (code === 0) { condition = "Clear Sky"; }
        else if ([1, 2, 3].includes(code)) { condition = "Partly Cloudy"; icon = isDay ? "fa-cloud-sun" : "fa-cloud-moon"; color = "#d1d5db"; }
        else if ([45, 48].includes(code)) { condition = "Foggy"; icon = "fa-smog"; color = "#9ca3af"; }
        else if ([51, 53, 55].includes(code)) { condition = "Drizzle"; icon = "fa-cloud-rain"; color = "#60a5fa"; }
        else if ([61, 63, 65, 80, 81, 82].includes(code)) { condition = "Rain"; icon = "fa-cloud-showers-heavy"; color = "#3b82f6"; }
        else if ([71, 73, 75, 85, 86].includes(code)) { condition = "Snow"; icon = "fa-snowflake"; color = "#e0f2fe"; }
        else if ([95, 96, 99].includes(code)) { condition = "Thunderstorm"; icon = "fa-bolt"; color = "#f59e0b"; }

        return { condition, icon, color };
    }

    function getUVStatus(uv) {
        if (uv <= 2) return "Low";
        if (uv <= 5) return "Moderate";
        if (uv <= 7) return "High";
        if (uv <= 10) return "Very High";
        return "Extreme";
    }

    async function fetchWeatherByCoords(lat, lon, cityName) {
        try {
            const res = await fetch(`https://api.open-meteo.com/v1/forecast?latitude=${lat}&longitude=${lon}&current=temperature_2m,relative_humidity_2m,apparent_temperature,is_day,weather_code,wind_speed_10m,surface_pressure&daily=weather_code,temperature_2m_max,temperature_2m_min,sunrise,sunset,uv_index_max&timezone=auto`);
            const data = await res.json();
            
            updateUI(data, cityName);
        } catch (error) {
            console.error("Weather fetch error", error);
            cityEl.innerText = "Error Loading Data";
        }
    }

    async function searchWeather(city) {
        if (!city) return;
        cityEl.innerText = "Searching...";
        weatherIconLargeEl.className = "fa-solid fa-spinner fa-spin";
        
        try {
            let lat, lon, actualCityName;

            // 1. Try Open-Meteo Geocoding first (Fast and reliable for most places)
            let geoRes = await fetch(`https://geocoding-api.open-meteo.com/v1/search?name=${encodeURIComponent(city)}&count=1&language=en&format=json`);
            let geoData = await geoRes.json();

            if (geoData.results && geoData.results.length > 0) {
                const location = geoData.results[0];
                lat = location.latitude;
                lon = location.longitude;
                actualCityName = location.name;
            } else {
                // 2. Fallback to OpenStreetMap (Nominatim) for small towns (Hosanna, Durame, Angecha, etc.)
                // Try with explicit ', Ethiopia' to help it find local small towns easily
                geoRes = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(city + ', Ethiopia')}&format=json&limit=1`);
                geoData = await geoRes.json();

                if (!geoData || geoData.length === 0) {
                    // Try without 'Ethiopia' as a last resort
                    geoRes = await fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(city)}&format=json&limit=1`);
                    geoData = await geoRes.json();
                }

                if (!geoData || geoData.length === 0) {
                    alert("City not found. Please try again or check the spelling.");
                    cityEl.innerText = "City Not Found";
                    weatherIconLargeEl.className = "fa-solid fa-circle-exclamation";
                    return;
                }

                const location = geoData[0];
                lat = location.lat;
                lon = location.lon;
                // Nominatim returns a full display name, so we just take the first part (the city name)
                actualCityName = location.display_name.split(',')[0]; 
            }
            
            await fetchWeatherByCoords(lat, lon, actualCityName);
        } catch (error) {
            console.error("Geocoding error", error);
            cityEl.innerText = "Error";
            weatherIconLargeEl.className = "fa-solid fa-circle-exclamation";
        }
    }

    function updateUI(data, cityName) {
        const current = data.current;
        const daily = data.daily;

        cityEl.innerText = cityName;
        tempMainEl.innerHTML = `${Math.round(current.temperature_2m)}°<span class="unit">C</span>`;
        feelsLikeEl.innerText = `${Math.round(current.apparent_temperature)}°C`;
        
        const details = getWeatherDetails(current.weather_code, current.is_day);
        conditionTextEl.innerText = details.condition;
        weatherIconLargeEl.className = `fa-solid ${details.icon}`;
        weatherIconLargeEl.style.color = details.color;

        // Highlights
        windValEl.innerText = current.wind_speed_10m;
        humidityValEl.innerText = current.relative_humidity_2m;
        humidityBarEl.style.width = `${current.relative_humidity_2m}%`;
        
        sunriseValEl.innerText = formatTime(daily.sunrise[0]);
        sunsetValEl.innerText = formatTime(daily.sunset[0]);

        const uvMax = daily.uv_index_max[0];
        uvValEl.innerText = uvMax;
        uvStatusEl.innerText = getUVStatus(uvMax);

        // Background
        currentConditionForBg = details.condition;
        currentIsDayForBg = current.is_day;
        updateBackground(details.condition, current.is_day);

        // 7-Day Forecast
        dailyForecastEl.innerHTML = '';
        for (let i = 0; i < 7; i++) {
            if (!daily.time[i]) break;
            const dayName = i === 0 ? 'Today' : getShortDay(daily.time[i]);
            const code = daily.weather_code[i];
            const max = Math.round(daily.temperature_2m_max[i]);
            const min = Math.round(daily.temperature_2m_min[i]);
            const dayDetails = getWeatherDetails(code, 1);

            const item = document.createElement('div');
            item.className = 'daily-item';
            item.innerHTML = `
                <span class="daily-day">${dayName}</span>
                <i class="fa-solid ${dayDetails.icon} daily-icon" style="color: ${dayDetails.color};"></i>
                <div class="daily-temps">
                    <span class="temp-max">${max}°</span>
                    <span class="temp-min">${min}°</span>
                </div>
            `;
            dailyForecastEl.appendChild(item);
        }
    }

    function updateBackground(condition, isDay) {
        const body = document.documentElement;
        if(body.getAttribute('data-theme') === 'dark') {
            body.style.background = 'var(--bg-gradient)'; // Keep dark gradient in dark mode
            return; 
        }
        
        const condLower = condition.toLowerCase();
        if(condLower.includes('rain') || condLower.includes('drizzle')) {
            body.style.background = 'linear-gradient(135deg, #bdc3c7 0%, #2c3e50 100%)';
        } else if (condLower.includes('cloud')) {
            body.style.background = 'linear-gradient(135deg, #d7d2cc 0%, #304352 100%)';
        } else if (condLower.includes('clear')) {
            body.style.background = 'linear-gradient(135deg, #56CCF2 0%, #2F80ED 100%)';
        } else if (!isDay) {
            body.style.background = 'linear-gradient(135deg, #141E30 0%, #243B55 100%)';
        } else {
             body.style.background = 'var(--bg-gradient)'; 
        }
    }

    // Geolocation Logic
    locationBtn.addEventListener('click', () => {
        if ("geolocation" in navigator) {
            locationBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
            navigator.geolocation.getCurrentPosition(
                async (position) => {
                    const lat = position.coords.latitude;
                    const lon = position.coords.longitude;
                    
                    // Reverse geocoding to get city name
                    try {
                        // Using BigDataCloud for reverse geocoding as it's free and doesn't block User-Agents like Nominatim
                        const geoRes = await fetch(`https://api.bigdatacloud.net/data/reverse-geocode-client?latitude=${lat}&longitude=${lon}&localityLanguage=en`);
                        const geoData = await geoRes.json();
                        const cityName = geoData.city || geoData.locality || "My Location";
                        await fetchWeatherByCoords(lat, lon, cityName);
                    } catch(e) {
                        await fetchWeatherByCoords(lat, lon, "My Location");
                    }
                    locationBtn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i>';
                },
                (error) => {
                    console.error("Geolocation error:", error);
                    let msg = "Could not get location. ";
                    if (error.code === 1) msg += "Please allow location access in your browser settings.";
                    else if (error.code === 2) msg += "Position unavailable (Check your internet or GPS).";
                    else if (error.code === 3) msg += "Location request timed out.";
                    alert(msg);
                    locationBtn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i>';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        } else {
            alert("Geolocation is not supported by your browser");
        }
    });

    // Event Listeners
    searchInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') { searchWeather(searchInput.value); searchInput.value = ''; }
    });
    searchBtn.addEventListener('click', () => {
        searchWeather(searchInput.value); searchInput.value = '';
    });

    // Initial load
    searchWeather("Addis Ababa");
});
