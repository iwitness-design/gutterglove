// Glide fullwidth slider
import Glide from '@glidejs/glide';

const glideSlider = () => {
  const slider = new Glide('.c-slider--fullwidth', {
    perView: 1,
  });

  slider.mount();
};

export default glideSlider;
