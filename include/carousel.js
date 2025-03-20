document.addEventListener("DOMContentLoaded", () => {
  const slides = document.querySelectorAll(".carousel-item");
  let currentIndex = 0;
  let autoSlideInterval;

  const updateCarousel = (index) => {
    slides.forEach((slide, i) => {
      slide.classList.toggle("active", i === index);
      slide.classList.toggle("background-blur", i !== index);
    });
  };

  const showNextSlide = () => {
    currentIndex = (currentIndex + 1) % slides.length;
    updateCarousel(currentIndex);
  };

  const showPrevSlide = () => {
    currentIndex = (currentIndex - 1 + slides.length) % slides.length;
    updateCarousel(currentIndex);
  };

  const startAutoSlide = () => {
    autoSlideInterval = setInterval(showNextSlide, 3000);
  };

  const stopAutoSlide = () => {
    clearInterval(autoSlideInterval);
  };

  document
    .querySelector(".carousel-container")
    .addEventListener("mouseenter", stopAutoSlide);
  document
    .querySelector(".carousel-container")
    .addEventListener("mouseleave", startAutoSlide);

  updateCarousel(currentIndex);
  startAutoSlide();
});
