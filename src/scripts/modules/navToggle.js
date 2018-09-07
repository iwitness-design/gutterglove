const hamburgerTrigger = () => {
  const hamburger = document.querySelector('.hamburger');
  const nav = document.querySelector('.c-nav');

  hamburger.addEventListener('click', () => {
    hamburger.classList.toggle('is-active');
  });

  nav.addEventListener('click', () => {
    nav.classList.toggle('is-active');
  });
};

export default hamburgerTrigger;
