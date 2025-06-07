<?php
require_once __DIR__ . "/../../services/config.php";
require_once __DIR__ . "/../../services/cart-service.php";
require_once __DIR__ . "/../../services/user-service.php";

use Configuration\Router;
use Cart\CartService;
use User\UserService;

if (!UserService::getUserIsLoggedIn()) {
    header('Location: ' . Router::getRoute('products'));
    exit;
}

?>

<section>
    <div class="container py-5">
        <h2 class="mb-4">Your Cart</h2>

        <div class="accordion" id="cartAccordion">
            <!-- Pending Orders Accordion Item -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingPending">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapsePending" aria-expanded="true" aria-controls="collapsePending">
                        Pending Orders
                    </button>
                </h2>
                <div id="collapsePending" class="accordion-collapse collapse show" aria-labelledby="headingPending"
                    data-bs-parent="#cartAccordion">
                    <div class="accordion-body">
                        <?php if (count($pendingItems) === 0): ?>
                            <div>Your Cart is empty...</div>
                        <?php else: ?>
                            <!-- Pending Orders Table -->
                            <div class="table-responsive">

                                <table class="table align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col"></th>
                                            <th scope="col">Product</th>
                                            <th scope="col">Price (€)</th>
                                            <th scope="col">Quantity</th>
                                            <th scope="col">Subtotal (€)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($pendingItems as $pi): ?>
                                            <tr onclick="window.location.href='<?= Router::getRoute('product') ?>&productId=<?= $pi->productId ?>'"
                                                style="cursor: pointer;" name="cartItem" 
                                                data-cartId="<?= $pi->id ?>"
                                                data-quantity="<?= $pi->quantity ?>"
                                                >
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-danger fw-bolder"
                                                        onclick="event.stopPropagation(); removeFromCart(<?= $pi->id ?>);">−</button>
                                                </td>
                                                <td><?= htmlspecialchars($pi->productInfo->nameEng) ?></td>
                                                <td id="price<?= $pi->id ?>"><?= number_format($pi->price, 2) ?></td>
                                                <td>
                                                    <input name="qntInput" id="qnt-<?= $pi->id ?>" type="number"
                                                        class="form-control form-control-sm" value="<?= $pi->quantity ?>"
                                                        min="1" onclick="event.stopPropagation();" readonly>
                                                </td>
                                                <td id="totValue<?= $pi->id ?>">
                                                    <?= number_format($pi->quantity * $pi->price, 2) ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Complete Order Button -->
                            <div class="d-flex justify-content-end mt-3">
                                <button type="button" class="btn btn-primary" id='completeBtn'>
                                    Complete Order
                                </button>
                            </div>

                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Completed Orders Accordion Item -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingCompleted">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseCompleted" aria-expanded="false" aria-controls="collapseCompleted">
                        Completed Orders
                    </button>
                </h2>
                <div id="collapseCompleted" class="accordion-collapse collapse" aria-labelledby="headingCompleted"
                    data-bs-parent="#cartAccordion">
                    <div class="accordion-body">
                        <!-- Completed Orders Table -->
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">Product</th>
                                        <th scope="col">Price (€)</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Subtotal (€)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($completedItems as $pi) {
                                        echo '<tr>';
                                        echo '<td>' . $pi->productInfo->nameEng . '</td>';
                                        echo '<td>' . $pi->price . '</td>';
                                        echo '<td><input type="number" class="form-control form-control-sm" value="' . $pi->quantity . '" min="1" readonly></td>';
                                        echo '<td>' . $pi->quantity * $pi->price . '</td>';
                                        echo '</tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const inputs = document.getElementsByName('qntInput');
        const completeBtn = document.getElementById('completeBtn');

        if (completeBtn && (!inputs || inputs.length === 0))
            completeBtn.disabled = true;

        if (completeBtn) {
            completeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                completeOrder();
            });
        }

    });

    function removeFromCart(cartId) {
        if (!confirm("Are you sure you want to remove this item?")) {
            return;
        }

        fetch(`index.php?route=cart&action=removeFromCart&cartId=${cartId}`, {
            method: 'POST',
            credentials: 'same-origin'
        })
            .then(res => res.json())
            .then(json => {
                location.reload();
            });
    }

    function completeOrder() {
        const cartItems = document.getElementsByName('cartItem');
        let ids = [];
        let orderTotal = 0;
        cartItems.forEach(i => {
            const id = i.getAttribute('data-cartId');
            const valueElmnt = document.getElementById('totValue'+id);
            const orderValue = parseFloat(valueElmnt.textContent);
            orderTotal += orderValue;
            ids.push(id);
        });

        let msg = `Your total is: ${orderTotal}€. Do you wish to proceed?`
        if (!confirm(msg))
            return;

        const idsString = ids.join(',');

        fetch(`index.php?route=cart&action=completeOrder&cartIds=${idsString}`,
            {
                method: 'POST',
                credentials: 'same-origin'
            }
        )
            .then(res => res.json())
            .then(json => {
                location.reload();
            })
    }

</script>