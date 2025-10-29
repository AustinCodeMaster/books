class ShoppingCart {
    constructor() {
        // Initialize cart from localStorage or empty array
        this.items = JSON.parse(localStorage.getItem('cartItems')) || [];
        this.bindEvents();
        this.updateCartCount();
    }

    bindEvents() {
        // Add click event listeners to all "Add to Cart" buttons
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', (e) => this.addToCart(e));
        });
    }

    async addToCart(e) {
        const button = e.target;
        const bookData = {
            id: button.dataset.bookId,
            name: button.dataset.bookName,
            price: parseFloat(button.dataset.bookPrice),
            author: button.dataset.bookAuthor,
            quantity: 1
        };

        try {
            // First save to database
            const response = await fetch('/books/Layouts/add_to_cart_handler.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    bookName: bookData.name,
                    bookPrice: bookData.price
                })
            });

            const result = await response.json();
            
            if (result.success) {
                // If database save was successful, update local cart
                // Check if item already exists in cart
                const existingItem = this.items.find(item => item.id === bookData.id);
                if (existingItem) {
                    existingItem.quantity += 1;
                } else {
                    bookData.cartId = result.cartId; // Store database ID
                    this.items.push(bookData);
                }

                // Save to localStorage and update UI
                this.saveCart();
                this.updateCartCount();
                this.showAddedToCartMessage(bookData.name);
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Failed to add item to cart:', error);
            alert('Failed to add item to cart: ' + error.message);
        }
    }

    saveCart() {
        localStorage.setItem('cartItems', JSON.stringify(this.items));
    }

    updateCartCount() {
        const count = this.items.reduce((total, item) => total + item.quantity, 0);
        const cartCountElement = document.querySelector('.cart-count');
        if (cartCountElement) {
            cartCountElement.textContent = count;
        }
    }

    showAddedToCartMessage(bookName) {
        // Remove existing toast if present
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) {
            existingToast.remove();
        }

        // Create new toast notification
        const toast = document.createElement('div');
        toast.className = 'toast-notification';
        toast.textContent = `${bookName} added to cart!`;
        document.body.appendChild(toast);

        // Add animation class
        setTimeout(() => {
            toast.classList.add('show');
        }, 10);

        // Remove after 2 seconds
        setTimeout(() => {
            toast.classList.add('hide');
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    }
}

// Initialize cart when page loads
document.addEventListener('DOMContentLoaded', () => {
    new ShoppingCart();
});