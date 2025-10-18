const navButtons = document.querySelectorAll('.sidenav .pagebtn');
const frames = Array.from(document.querySelectorAll('.frames'));
const submenuTriggers = document.querySelectorAll('.submenu-trigger');

function activateButton(button) {
  const frameIndex = parseInt(button.dataset.frame, 10);

  if (Number.isNaN(frameIndex) || !frames[frameIndex]) {
    return;
  }

  navButtons.forEach((btn) => btn.classList.remove('active'));
  submenuTriggers.forEach((trigger) => trigger.classList.remove('active'));

  button.classList.add('active');

  frames.forEach((frame, index) => {
    frame.classList.toggle('active', index === frameIndex);
  });

  const desiredSrc = button.dataset.src;
  if (desiredSrc) {
    const frame = frames[frameIndex];
    if (frame.getAttribute('src') !== desiredSrc) {
      frame.setAttribute('src', desiredSrc);
    }
  }

  const parentSubmenu = button.closest('.has-submenu');
  if (parentSubmenu) {
    const trigger = parentSubmenu.querySelector('.submenu-trigger');
    if (trigger) {
      trigger.classList.add('active');
    }
  }
}

navButtons.forEach((button) => {
  button.addEventListener('click', () => activateButton(button));
});

document.addEventListener('DOMContentLoaded', () => {
  const initialButton = Array.from(navButtons).find((btn) => btn.classList.contains('active'));
  if (initialButton) {
    activateButton(initialButton);
  }
});
