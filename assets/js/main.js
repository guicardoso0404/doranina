/* DoraNina asset version: 2026-04-09-1 */
const CART_KEY = 'doranina_cart';

function getCart() {
  try {
    return JSON.parse(localStorage.getItem(CART_KEY)) || [];
  } catch (e) {
    return [];
  }
}

function saveCart(cart) {
  localStorage.setItem(CART_KEY, JSON.stringify(cart));
  updateCartCount();
}

function addToCart(id, nome, preco, imagem = '') {
  const cart = getCart();
  const existing = cart.find(item => item.id === id);

  if (existing) {
    existing.quantidade += 1;
  } else {
    cart.push({ id, nome, preco, imagem, quantidade: 1 });
  }

  saveCart(cart);
  showToast(nome + ' foi adicionado ao carrinho.');
}

function updateCartCount() {
  const count = getCart().reduce((acc, item) => acc + item.quantidade, 0);
  document.querySelectorAll('[data-cart-count]').forEach(el => {
    el.textContent = count;
  });
}

function formatPrice(value) {
  return Number(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function showToast(message) {
  let toast = document.getElementById('site-toast');

  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'site-toast';
    toast.className = 'toast-message';
    document.body.appendChild(toast);
  }

  toast.innerHTML = '<i class="bi bi-bag-heart-fill"></i><span>' + message + '</span>';
  toast.classList.add('show');

  clearTimeout(toast._timer);
  toast._timer = setTimeout(() => {
    toast.classList.remove('show');
  }, 2200);
}

function initFilters() {
  const buttons = document.querySelectorAll('.filter-btn');
  const cards = document.querySelectorAll('.product-card');
  if (!buttons.length || !cards.length) return;

  buttons.forEach(btn => {
    btn.addEventListener('click', () => {
      buttons.forEach(item => item.classList.remove('active'));
      btn.classList.add('active');

      const category = btn.dataset.category;
      cards.forEach(card => {
        if (category === 'todos' || card.dataset.category === category) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
}

const menuToggle = document.getElementById('menuToggle');
const mainNav = document.getElementById('mainNav');

if (menuToggle && mainNav) {
  menuToggle.addEventListener('click', () => mainNav.classList.toggle('is-open'));
  mainNav.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', () => mainNav.classList.remove('is-open'));
  });
}

document.addEventListener('DOMContentLoaded', () => {
  updateCartCount();
  initFilters();
});
