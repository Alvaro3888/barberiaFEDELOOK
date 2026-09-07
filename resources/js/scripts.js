document.addEventListener("DOMContentLoaded", function() {
    // Animación de título Fedelook
    const title = document.querySelector(".fedelook-title");
    if(title){
        title.style.opacity = 0;
        setTimeout(() => {
            title.style.transition = "opacity 2s ease-in-out";
            title.style.opacity = 1;
        }, 500);
    }

    // Carruseles autoplay
    document.querySelectorAll('.carousel').forEach(carousel => {
        new bootstrap.Carousel(carousel, {
            interval: 4000,
            ride: 'carousel'
        });
    });
});
