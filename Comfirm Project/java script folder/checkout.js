document.addEventListener('DOMContentLoaded', function() {
    const checkbox = document.getElementById('useRegAddress');
    const addrInput = document.getElementById('addrInput');
    const dbAddressInput = document.getElementById('db_address_hidden');

    if (checkbox) {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                addrInput.value = dbAddressInput.value;
            } else {
                addrInput.value = "";
            }
        });
    }

    const cityData = {
        "Johor": ["Johor Bahru", "Tebrau", "Pasir Gudang", "Bukit Indah", "Skudai", "Kluang", "Batu Pahat", "Muar", "Ulu Tiram", "Senai", "Segamat", "Kulai", "Kota Tinggi", "Pontian Kechil", "Tangkak", "Bukit Bakri", "Yong Peng", "Pekan Nenas", "Labis", "Mersing", "Simpang Renggam", "Parit Raja", "Kelapa Sawit", "Buloh Kasap", "Chaah"],
        "Kedah": ["Sungai Petani", "Alor Setar", "Kulim", "Jitra", "Baling", "Pendang", "Langkawi", "Yan", "Sik", "Kuala Nerang", "Pokok Sena", "Bandar Baharu"],
        "Kelantan": ["Kota Bharu", "Pangkal Kalong", "Tanah Merah", "Peringat", "Wakaf Bharu", "Kadok", "Pasir Mas", "Gua Musang", "Kuala Krai", "Tumpat"],
        "Melaka": ["Bandaraya Melaka", "Bukit Baru", "Ayer Keroh", "Klebang", "Masjid Tanah", "Sungai Udang", "Batu Berendam", "Alor Gajah", "Bukit Rambai", "Durian Tunggal", "Serkam", "Bemban", "Lendu"],
        "Negeri Sembilan": ["Seremban", "Port Dickson", "Nilai", "Bahau", "Tampin", "Kuala Pilah"],
        "Pahang": ["Kuantan", "Temerloh", "Bentong", "Mentakab", "Raub", "Jerantut", "Pekan", "Kuala Lipis", "Bandar Jengka", "Bukit Tinggi"],
        "Penang": ["George Town", "Bukit Mertajam", "Sungai Ara", "Gelugor", "Air Itam", "Butterworth", "Perai", "Nibong Tebal", "Permatang Pauh", "Tanjung Tokong", "Kepala Batas", "Tanjung Bungah", "Juru"],
        "Perak": ["Ipoh", "Taiping", "Sitiawan", "Simpang Empat", "Teluk Intan", "Batu Gajah", "Lumut", "Kampung Koh", "Seri Manjung", "Tapah", "Parit Buntar", "Tanjung Malim", "Bidor", "Gerik", "Bercham"],
        "Perlis": ["Kangar", "Kuala Perlis"],
        "Sabah": ["Kota Kinabalu", "Sandakan", "Tawau", "Lahad Datu", "Keningau", "Putatan", "Donggongon", "Semporna", "Kudat", "Bongawan", "Ranau", "Papar", "Pittas", "Tanjung Aru", "Gombizau", "Kota Belud"],
        "Sarawak": ["Kuching", "Miri", "Sibu", "Bintulu", "Limbang", "Sarikei", "Sri Aman", "Kapit", "Batu 8 1/2", "Kota Samarahan"],
        "Selangor": ["Subang Jaya", "Klang", "Ampang Jaya", "Shah Alam", "Petaling Jaya", "Cheras", "Kajang", "Selayang Baru", "Rawang", "Taman Greenwood", "Semenyih", "Banting", "Balakong", "Gombak Setia", "Kuala Selangor", "Serendah", "Bukit Beruntung", "Pengkalan Kundang", "Jenjarom", "Sungai Besar", "Batu Arang", "Tanjung Sepat", "Kuang", "Kuala Kubu Baharu", "Batang Berjuntai", "Bandar Baru Salak Tinggi", "Sekinchan", "Sabak", "Tanjung Karang", "Beranang", "Sungai Pelek"],
        "Terengganu": ["Kuala Terengganu", "Chukai", "Dungun", "Kerteh", "Kuala Berang", "Marang", "Paka", "Jerteh"],
        "Kuala Lumpur": ["Kuala Lumpur", "Ampang", "Cheras", "Kepong", "Setapak"],
        "Putrajaya": ["Putrajaya"],
        "Labuan": ["Victoria"]
    };

    const stateSelect = document.getElementById('stateSelect');
    const citySelect = document.getElementById('citySelect');

    const handleFocus = function() {
        this.size = 5;
    };

    const handleBlur = function() {
        this.size = 1;
    };

    const handleChange = function() {
        this.size = 1;
        this.blur();
    };

    const scrollSelects = document.querySelectorAll('.form-select-scroll');
    scrollSelects.forEach(select => {
        select.addEventListener('focus', handleFocus);
        select.addEventListener('blur', handleBlur);
        select.addEventListener('change', handleChange);
    });

    if (stateSelect && citySelect) {
        stateSelect.addEventListener('change', function() {
            const selectedState = this.value;
            const cities = cityData[selectedState] || [];

            citySelect.innerHTML = '<option value="" disabled selected>Select City</option>';

            if (cities.length > 0) {
                citySelect.disabled = false;
                cities.forEach(city => {
                    const option = document.createElement('option');
                    option.value = city;
                    option.textContent = city;
                    citySelect.appendChild(option);
                });
            } else {
                citySelect.disabled = true;
                const option = document.createElement('option');
                option.textContent = "No cities available";
                citySelect.appendChild(option);
            }
        });
    }
});