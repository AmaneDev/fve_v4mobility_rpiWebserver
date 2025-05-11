// animace pocasi podle intenzity svetla

function fadeIn(el) {
    el.style.opacity = 0;
    el.style.display = 'block';
    el.classList.add('fade-in');
}

function fadeOut(el) {
    el.style.opacity = 0;
    setTimeout(() => {
        el.style.display = 'none';
    }, 500); // musí být shodné s časem animace
}


function getWeatherAnim(item) {
    const lux = parseFloat(item.intenzitaS);
    const sunImg = document.querySelector('.sun');
    const cloudImg = document.querySelector('.cloud');
    const container = document.getElementById('weatherAnimContainer');

    sunImg.classList.remove('rotate', 'fade-in');
    cloudImg.classList.remove('fade-in');

    if (!isNaN(lux)) {
        if (lux === 0) {
            // chybna data - skryje se vse
            fadeOut(sunImg);
            fadeOut(cloudImg);
        } else if (lux > 0 && lux <= 5000) {
            fadeOut(sunImg);
            fadeIn(cloudImg);
        } else if (lux > 5000 && lux <= 32000) {
            fadeOut(cloudImg);
            fadeIn(sunImg);
            sunImg.classList.add('rotate');
        } else {
            // mimo očekávaný rozsah
            fadeOut(sunImg);
            fadeOut(cloudImg);
            showError(container, 'Chyba: Intenzita světla mimo rozsah.');
            container.style.filter = 'none';
        }
    } else {
        showError(container, 'Chyba: Neplatná intenzita světla.');
    }
}

