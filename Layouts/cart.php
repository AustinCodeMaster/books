<?php
session_start();
require_once '../conf.php';

// Check if user is authenticated
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: ../forms/form.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - <?php echo $conf['site_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/cart-styles.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><?php echo $conf['site_name']; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="books.php">Books</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="cart.php">Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">Orders</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../forms/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-12">
                <h2>Shopping Cart</h2>
                <div id="cart-items">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cart Item Template -->
    <template id="cart-template">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Author</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="cart-body">
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-end"><strong>Grand Total:</strong></td>
                        <td colspan="2"><strong id="grand-total">$0.00</strong></td>
                    </tr>
                </tfoot>
            </table>
            <div class="d-flex justify-content-between">
                <a href="books.php" class="btn btn-secondary">Continue Shopping</a>
                <button id="checkout-btn" class="btn btn-success">Proceed to Checkout</button>
            </div>
        </div>
    </template>

    <!-- Empty Cart Template -->
    <template id="empty-cart-template">
        <div class="text-center">
            <p>Your cart is empty.</p>
            <a href="books.php" class="btn btn-primary">Browse Books</a>
        </div>
    </template>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/cart.js"></script>
    <script>
        class CartPage {
            constructor() {
                this.items = JSON.parse(localStorage.getItem('cartItems')) || [];
                this.displayCart();
            }

            displayCart() {
                const cartContainer = document.getElementById('cart-items');
                
                if (this.items.length === 0) {
                    const template = document.getElementById('empty-cart-template');
                    cartContainer.innerHTML = template.innerHTML;
                    return;
                }

                const template = document.getElementById('cart-template');
                cartContainer.innerHTML = template.innerHTML;

                const cartBody = document.getElementById('cart-body');
                let grandTotal = 0;

                this.items.forEach((item, index) => {
                    const total = item.price * item.quantity;
                    grandTotal += total;

                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.name}</td>
                        <td>${item.author}</td>
                        <td>$${item.price.toFixed(2)}</td>
                        <td>
                            <div class="input-group input-group-sm" style="max-width: 120px;">
                                <button class="btn btn-outline-secondary" type="button" onclick="cartPage.updateQuantity(${index}, -1)">-</button>
                                <input type="text" class="form-control text-center" value="${item.quantity}" readonly>
                                <button class="btn btn-outline-secondary" type="button" onclick="cartPage.updateQuantity(${index}, 1)">+</button>
                            </div>
                        </td>
                        <td>$${total.toFixed(2)}</td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="cartPage.removeItem(${index})">Remove</button>
                        </td>
                    `;
                    cartBody.appendChild(row);
                });

                document.getElementById('grand-total').textContent = `$${grandTotal.toFixed(2)}`;

                // Add checkout button handler
                document.getElementById('checkout-btn').addEventListener('click', () => this.checkout());
            }

            updateQuantity(index, change) {
                const item = this.items[index];
                const newQuantity = item.quantity + change;
                
                if (newQuantity > 0) {
                    item.quantity = newQuantity;
                    localStorage.setItem('cartItems', JSON.stringify(this.items));
                    this.displayCart();
                } else if (newQuantity === 0) {
                    this.removeItem(index);
                }
            }

            removeItem(index) {
                this.items.splice(index, 1);
                localStorage.setItem('cartItems', JSON.stringify(this.items));
                this.displayCart();
            }

            checkout() {
                // Here you'll add the code to save to your database
                alert('Checkout functionality coming soon!');
            }
        }

        // Initialize cart page
        const cartPage = new CartPage();
    </script>
</body>
</html>