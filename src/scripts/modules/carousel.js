// Glide carousel
// new Glide('.glide').mount();
import Glide from '@glidejs/glide';

const glideCarousel = () => {
  const carousel = new Glide('.c-slider--carousel', {
    type: 'carousel',
    perView: 3,
    gap: 0,
    breakpoints: {
      1023: {
        perView: 2,
      },
      600: {
        perView: 1,
      },
    },
  });

  carousel.mount();
};

export default glideCarousel;
