let currentSlide = 0;
const slides = document.querySelectorAll('.slide-box');
const totalSlides = slides.length;

document.addEventListener('DOMContentLoaded', () => {
    const slides = document.querySelectorAll('.slide-box');
    if (slides.length > 0) {
        showSlide(0); 
    }

    setInterval(() => {
        nextSlide();
    }, 3000);
});

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.style.display = (i === index) ? 'block' : 'none';
    });
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    showSlide(currentSlide);
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    showSlide(currentSlide);
}
