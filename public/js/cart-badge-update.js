// Animate badge function
function animateBadge(badgeElement) {
    if (!badgeElement) return;
    
    // Add animation class
    badgeElement.classList.add('animate-bounce');
    
    // Remove animation after it completes
    setTimeout(() => {
        badgeElement.classList.remove('animate-bounce');
    }, 1000);
}

// Update cart badge count
function updateCartBadge(newCount) {
    const cartBadges = document.querySelectorAll('.cart-badge');
    cartBadges.forEach(badge => {
        if (newCount > 0) {
            badge.textContent = newCount > 9 ? '9+' : newCount;
            badge.classList.remove('hidden');
            animateBadge(badge);
        } else {
            badge.classList.add('hidden');
        }
    });

    // Update hamburger button badge
    const hamburgerCartBadges = document.querySelectorAll('.hamburger-cart-badge');
    hamburgerCartBadges.forEach(badge => {
        if (newCount > 0) {
            badge.textContent = newCount > 9 ? '9+' : newCount;
            badge.classList.remove('hidden');
            animateBadge(badge);
        } else {
            badge.classList.add('hidden');
        }
    });
}

// Update wishlist badge count
function updateWishlistBadge(newCount) {
    const wishlistBadges = document.querySelectorAll('.wishlist-badge');
    wishlistBadges.forEach(badge => {
        if (newCount > 0) {
            badge.textContent = newCount > 9 ? '9+' : newCount;
            badge.classList.remove('hidden');
            animateBadge(badge);
        } else {
            badge.classList.add('hidden');
        }
    });

    // Update hamburger button badge
    const hamburgerWishlistBadges = document.querySelectorAll('.hamburger-wishlist-badge');
    hamburgerWishlistBadges.forEach(badge => {
        if (newCount > 0) {
            badge.textContent = newCount > 9 ? '9+' : newCount;
            badge.classList.remove('hidden');
            animateBadge(badge);
        } else {
            badge.classList.add('hidden');
        }
    });
}

// Add to Cart Function
function addToCart(productId, event) {
    event.preventDefault();
    event.stopPropagation();
    
    const button = event.target.closest('button');
    if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
    }
    
    fetch(`/customer/cart/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Animate and update cart badge
            const currentCount = parseInt(data.cart_count || 0);
            updateCartBadge(currentCount);
            
            if (button) {
                button.innerHTML = '✓ Added';
                setTimeout(() => {
                    button.disabled = false;
                    button.innerHTML = 'Add to cart';
                }, 1500);
            }
        } else {
            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }
            alert(data.message || 'Gagal menambahkan produk ke keranjang');
            if (button) {
                button.disabled = false;
                button.innerHTML = 'Add to cart';
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan. Silakan coba lagi.');
        if (button) {
            button.disabled = false;
            button.innerHTML = 'Add to cart';
        }
    });
}

// Add to Wishlist Function
function addToWishlist(productId, event) {
    event.preventDefault();
    event.stopPropagation();
    
    const button = event.target.closest('button');
    const icon = button.querySelector('i');
    if (icon) {
        icon.classList.remove('fa-heart');
        icon.classList.add('fa-spinner', 'fa-spin');
    }
    
    fetch(`/customer/wishlist/add/${productId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({})
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Animate and update wishlist badge
            const currentCount = parseInt(data.wishlist_count || 0);
            updateWishlistBadge(currentCount);
            
            if (icon) {
                icon.classList.remove('fa-spinner', 'fa-spin');
                icon.classList.add('fa-heart');
                icon.classList.add('text-rose-500');
            }
            if (button) {
                button.classList.add('text-rose-500');
            }
        } else {
            if (data.redirect) {
                window.location.href = data.redirect;
                return;
            }
            alert(data.message || 'Gagal menambahkan produk ke wishlist');
            if (icon) {
                icon.classList.remove('fa-spinner', 'fa-spin');
                icon.classList.add('fa-heart');
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan. Silakan coba lagi.');
        if (icon) {
            icon.classList.remove('fa-spinner', 'fa-spin');
            icon.classList.add('fa-heart');
        }
    });
}
