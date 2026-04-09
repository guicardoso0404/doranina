function updateQuantity(id, amount) {
  const cart = getCart();
  const item = cart.find(product => product.id === id);
  if (!item) return;

  item.quantidade += amount;

  if (item.quantidade <= 0) {
    const filtered = cart.filter(product => product.id !== id);
    saveCart(filtered);
    showToast('Item removido do carrinho.');
  } else {
    saveCart(cart);
  }

  renderCartPage();
  renderCheckoutPage();
}

function removeItem(id) {
  const cart = getCart();
  const item = cart.find(product => product.id === id);
  const filtered = cart.filter(product => product.id !== id);
  saveCart(filtered);
  if (item) showToast(item.nome + ' foi removido do carrinho.');
  renderCartPage();
  renderCheckoutPage();
}

function clearCart() {
  localStorage.removeItem(CART_KEY);
  updateCartCount();
  renderCartPage();
  renderCheckoutPage();
  showToast('Carrinho limpo.');
}

function renderCartPage() {
  const cartItemsEl = document.getElementById('cart-items');
  const emptyEl = document.getElementById('empty-cart');
  const subtotalEl = document.getElementById('summary-subtotal');
  const totalEl = document.getElementById('summary-total');
  const goCheckout = document.getElementById('go-checkout');
  if (!cartItemsEl) return;

  const cart = getCart();
  if (!cart.length) {
    cartItemsEl.innerHTML = '';
    if (emptyEl) emptyEl.classList.remove('hidden');
    if (subtotalEl) subtotalEl.textContent = 'R$ 0,00';
    if (totalEl) totalEl.textContent = 'R$ 0,00';
    if (goCheckout) {
      goCheckout.classList.add('disabled');
      goCheckout.setAttribute('aria-disabled', 'true');
      goCheckout.addEventListener('click', preventCheckout);
    }
    return;
  }

  if (emptyEl) emptyEl.classList.add('hidden');
  if (goCheckout) {
    goCheckout.classList.remove('disabled');
    goCheckout.removeAttribute('aria-disabled');
    goCheckout.removeEventListener('click', preventCheckout);
  }

  let total = 0;
  cartItemsEl.innerHTML = cart.map(item => {
    const subtotal = item.preco * item.quantidade;
    total += subtotal;

    const image = item.imagem
      ? '<img src="' + item.imagem + '" alt="' + item.nome + '">'
      : '<div class="cart-thumb-placeholder"><i class="bi bi-cake2-fill"></i></div>';

    return `
      <article class="cart-item">
        <div class="cart-thumb">${image}</div>
        <div class="cart-item-info">
          <h3>${item.nome}</h3>
          <p>${formatPrice(item.preco)} cada</p>
          <div class="qty-controls">
            <button type="button" onclick="updateQuantity(${item.id}, -1)">-</button>
            <span>${item.quantidade}</span>
            <button type="button" onclick="updateQuantity(${item.id}, 1)">+</button>
          </div>
        </div>
        <div class="cart-item-side">
          <strong>${formatPrice(subtotal)}</strong>
          <button type="button" class="link-btn" onclick="removeItem(${item.id})">Remover</button>
        </div>
      </article>
    `;
  }).join('');

  if (subtotalEl) subtotalEl.textContent = formatPrice(total);
  if (totalEl) totalEl.textContent = formatPrice(total);
}

function preventCheckout(event) {
  event.preventDefault();
}

function renderCheckoutPage() {
  const listEl = document.getElementById('checkout-items');
  const totalEl = document.getElementById('checkout-total');
  const totalInput = document.getElementById('total_input');
  const itensInput = document.getElementById('itens_json');
  const form = document.getElementById('checkout-form');
  if (!listEl || !totalEl || !totalInput || !itensInput) return;

  const cart = getCart();
  if (!cart.length) {
    listEl.innerHTML = '<div class="empty-inline">Seu carrinho está vazio. Volte ao catálogo para adicionar produtos.</div>';
    totalEl.textContent = 'R$ 0,00';
    totalInput.value = '0';
    itensInput.value = '[]';
    if (form) {
      form.addEventListener('submit', event => event.preventDefault(), { once: true });
    }
    return;
  }

  let total = 0;
  listEl.innerHTML = cart.map(item => {
    const subtotal = item.preco * item.quantidade;
    total += subtotal;
    return `
      <div class="mini-item">
        <div>
          <strong>${item.nome}</strong>
          <small>${item.quantidade}x ${formatPrice(item.preco)}</small>
        </div>
        <span>${formatPrice(subtotal)}</span>
      </div>
    `;
  }).join('');

  totalEl.textContent = formatPrice(total);
  totalInput.value = total.toFixed(2);
  itensInput.value = JSON.stringify(cart);
}

document.addEventListener('DOMContentLoaded', () => {
  renderCartPage();
  renderCheckoutPage();
});


function initCheckoutDelivery() {
  const tipoEntrega = document.getElementById('tipo_entrega');
  const enderecoInput = document.getElementById('cliente_endereco');
  const enderecoGroup = document.getElementById('endereco-group');
  const enderecoHelp = document.getElementById('endereco-help');

  if (!tipoEntrega || !enderecoInput) return;

  const syncDeliveryFields = () => {
    const isMotoboy = tipoEntrega.value === 'motoboy';
    enderecoInput.required = isMotoboy;
    enderecoInput.disabled = !isMotoboy;

    if (isMotoboy) {
      enderecoInput.placeholder = 'Rua, número, bairro e referência';
      if (enderecoHelp) {
        enderecoHelp.textContent = 'Preencha o endereço para a entrega por motoboy.';
      }
      if (enderecoGroup) {
        enderecoGroup.classList.remove('is-disabled');
      }
    } else {
      enderecoInput.value = '';
      enderecoInput.placeholder = 'Retirada no local';
      if (enderecoHelp) {
        enderecoHelp.textContent = 'Para retirada no local, o endereço não é necessário.';
      }
      if (enderecoGroup) {
        enderecoGroup.classList.add('is-disabled');
      }
    }
  };

  tipoEntrega.addEventListener('change', syncDeliveryFields);
  syncDeliveryFields();
}
