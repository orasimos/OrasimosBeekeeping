<?php
require_once __DIR__ . "/../../services/config.php";
require_once __DIR__ . "/../../services/product-service.php";

use Product\ProductService;

$productTypes = ProductService::getProductTypes();
$products = ProductService::getProducts();

?>

<section style="padding-top: 7rem !important;">
    <div class="row row-cols-1 row-cols-md-4 g-4" id="product-list">
        <?php foreach ($products as $product): ?>
            <div class="col product-card" data-type="<?= $product->type->id ?>" data-id="<?= $product->id ?>"
                style="cursor: pointer;">
                <div class="card h-100">
                    <img src="<?= htmlspecialchars($product->getImageUrl()) ?>" class="card-img-top"
                        alt="<?= htmlspecialchars($product->nameEng) ?>">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($product->nameEng) ?></h5>
                        <p class="card-text"><?= htmlspecialchars($product->descriptionEng) ?></p>
                        <div class="mt-auto">
                            <span class="fw-bold"><?= number_format($product->price, 2) ?> €</span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const buttons = document.querySelectorAll('.filter-btn');
        const select = document.getElementById('type-select');
        const productCards = document.querySelectorAll('.product-card');

        // Creating the product type filtering function
        function filterBy(type) {
            buttons.forEach(b => b.classList.toggle('active', b.getAttribute('data-type') === type));
            if (select) select.value = type;
            productCards.forEach(card => {
                const cardType = card.getAttribute('data-type');
                card.classList.toggle('d-none', type !== 'all' && cardType !== type);
            });
        }

        function navigateToProductPage(event) {
            let el = event.target;
            while (el && !el.classList.contains('product-card')) {
                el = el.parentElement;
            }
            if (!el) return;

            const productId = el.getAttribute('data-id');
            if (!productId)
                return;

            window.location.href = `index.php?route=product&productId=${productId}`;
        }

        // Event listener for clicking on product type
        buttons.forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                filterBy(btn.getAttribute('data-type'));
            });
        });

        // Event listener for type change
        if (select) {
            select.addEventListener('change', () => {
                filterBy(select.value);
            });
        }

        productCards.forEach(pc => {
            pc.addEventListener('click', e => {
                e.preventDefault();
                navigateToProductPage(e);
            })
        })
    });
</script>