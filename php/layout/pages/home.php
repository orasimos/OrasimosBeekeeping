<?php
// Import των απαραίτητων services
require_once __DIR__ . "/../../services/config.php";
require_once __DIR__ . "/../../services/product-service.php";

use Configuration\Router;
use Product\ProductService;

// Δημιουργία array με ένα τυχαίο προϊόν για κάθε κατηγορία
$randomProducts = ProductService::getRandomProductsByType();

?>

<!-- Section με λογότυπο, καλωσόρισμα και κουμπί μετάβασης στη σελίδα προϊόντων -->
<section class="p-5 text-center bg-light rounded-3">
    <div class="container">
        <!-- Λογότυπο -->
        <img 
            src="assets/img/logo-192x192.png" 
            alt="<?= $appname ?>"
            class="img-fuid mb-3"
            style="max-height: 100px;"
        >
        <!-- Όνομα εταιρίας = Όνομα εφαρμογής -->
        <h2 class="display-6 fw-bold"><?= $appname ?></h2>
        <!-- Μήνυμα καλωσορίσματος στην ιστοσελίδα -->
        <p class="lead">
            Welcome to <?= $appname ?>! Browse our artisan
            beekeeping products and find everything you need for hive health.
        </p>
        <!-- Κουμππί μετάβασης στη σελίδα προϊόντων -->
        <a href="<?= Router::getRoute('products') ?>" class="btn btn-primary btn-lg">
            Shop Now
        </a>
    </div>
</section>
<!-- Section με τυχαία προϊόντα -->
<section class="pb-5">
    <br>
    <div class="row">
        <h4>Our most popular products:</h4>
    </div>
    <br>
    <div class="row row-cols-1 row-cols-md-4 g-4" id="product-list">
        <!-- 
            Για κάθε προϊον που περιέχεται στο array $randomProducts δημιουργείται
            μία κάρτα με τη φωτογραφία, την περιγραφή και την τιμή του
        -->
        <?php foreach ($randomProducts as $product): ?>
            <div class="col product-card" data-type="<?= $product->type->id ?>" data-id="<?= $product->id ?>"
                style="cursor: pointer;">
                <div class="card h-100">
                    <!-- 
                        Το όνομα του αρχείου της εικόνας είναι αποθηκευμένο στη βάση και η κλάση Product 
                        έχει μία βοηθητική μέθοδο για την ανάκτησή του πλήρους url
                    -->
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
    // Δημιουργία event listeners στις κάρτες των προϊόντων για μετάβαση στη σελίδα του 
    // εικονιζόμενου προϊόντος
    document.addEventListener('DOMContentLoaded', () => {
        // array με τα elements των καρτών
        const productCards = document.querySelectorAll('.product-card');

        // function ανακατεύθυνσης στην αντίστοιχη σελίδα
        // Δεχεται ως input το click event
        function navigateToProductPage(event) {
            // Εκχώριση του element το οποίο πατήθηκε στη μεταβλητή el
            let el = event.target;
            // Επειδή μπορεί να πατηθεί οποιοδήποτε element μέσα στην κάρτα
            // τρέχει το loop μέσα στο οποίο εκχωρείται στη μεταβλητή el το parentElement της el.
            // Αυτό γιατί το id του προϊόντος υπάρχει ως data-id στο div με κλάση .product-card
            while (el && !el.classList.contains('product-card')) {
                el = el.parentElement;
            }
            // Αν δεν βρεθεί το απαιτούμενο div τερματίζεται η function
            if (!el) return;

            // Ανάκτηση του productId από το data-id του div που προαναφέρθηκε
            const productId = el.getAttribute('data-id');
            // Αν δεν έχει οριστεί ή είναι 0 τότε τερματίζεται η function
            if (!productId)
                return;

            //Ανακατεύθυνση στη σελίδα του προϊόντος με το Id του.
            window.location.href = `index.php?route=product&productId=${productId}`;
        }

        // Δημιουργία click event listeners στις καρτες προϊόντων
        productCards.forEach(pc => {
            pc.addEventListener('click', e => {
                e.preventDefault();
                // Κλήση της παραπάνω function για ανακατεύθυνση
                navigateToProductPage(e);
            })
        })
    });
</script>