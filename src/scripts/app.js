/*
  Project: Gutterglove
  Author: iWitness Design
 */

import hamburger from './modules/navToggle';
import glideCarousel from './modules/carousel';
import glideSlider from './modules/slider';

if (document.querySelector(('.hamburger'))) {
  hamburger();
}

if (document.querySelector('.c-slider--carousel')) {
  console.log('carousel');
  glideCarousel();
}

if (document.querySelector('.c-slider--fullwidth')) {
  glideSlider();
}