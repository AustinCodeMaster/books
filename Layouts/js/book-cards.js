// Book card functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize ratings
    initializeRatings();
    
    // Add to cart functionality
    initializeAddToCart();
});

function initializeRatings() {
    document.querySelectorAll('.rating').forEach(rating => {
        const score = parseFloat(rating.dataset.rating);
        const stars = '★'.repeat(Math.floor(score)) + '☆'.repeat(5 - Math.floor(score));
        rating.innerHTML = `<span class="stars">${stars}</span> <span class="score">${score}</span>`;
    });
}

function initializeAddToCart() {
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const bookId = this.dataset.bookId;
            const price = this.dataset.price;
            
            // Add animation
            this.classList.add('added-to-cart');
            this.textContent = 'Added to Cart!';
            
            // Send to server
            addToCart(bookId, price, this);
        });
    });
}

async function addToCart(bookId, price, button) {
    try {
        const response = await fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                book_id: bookId,
                price: price
            })
        });

        const data = await response.json();
        
        if (data.success) {
            // Update cart count if you have one
            if (data.cartCount) {
                updateCartCount(data.cartCount);
            }
        } else {
            // Reset button if failed
            setTimeout(() => {
                button.classList.remove('added-to-cart');
                button.textContent = 'Add to Cart';
            }, 2000);
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        // Reset button on error
        setTimeout(() => {
            button.classList.remove('added-to-cart');
            button.textContent = 'Add to Cart';
        }, 2000);
    }
}

function updateCartCount(count) {
    const cartCounter = document.querySelector('.cart-count');
    if (cartCounter) {
        cartCounter.textContent = count;
        cartCounter.classList.add('cart-updated');
        setTimeout(() => cartCounter.classList.remove('cart-updated'), 300);
    }
}

// Optional: Add smooth loading animation
document.addEventListener('DOMContentLoaded', function() {
    const bookCards = document.querySelectorAll('.book-card');
    bookCards.forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('visible');
        }, index * 100);
    });
});