<?php
require_once __DIR__ . "/../../services/config.php";
require_once __DIR__ . "/../../services/product-service.php";
require_once __DIR__ . "/../../services/user-service.php";

use Configuration\Router;
use Product\ProductService;
use User\UserService;

if (!isset($_GET['productId']) || !is_numeric($_GET['productId'])) {
    header('Location: ' . Router::getRoute('products'));
    exit;
}

$productId = $_GET['productId'];
$product = ProductService::getProductById($productId);

$isUserLoggedIn = UserService::getUserIsLoggedIn();
$userInfo = UserService::getUserInfo();

$quantity = isset($_GET['quantity']) ? $_GET['quantity'] : 1;
$cartItem = null;
if (count($pendingItems) > 0) {
    foreach ($pendingItems as $item) {
        if ($item->productId == $productId) {
            $cartItem = $item;
            $quantity = $item->quantity;
            break;
        }
    }
}

?>

<section class="pt-5">
    <div class="row">
        <!-- Product Image -->
        <div class="col-md-6 mb-2">
            <img src="<?= htmlspecialchars($product->getImageUrl(), ENT_QUOTES, 'UTF-8') ?>" class="img-fluid rounded"
                alt="<?= htmlspecialchars($product->nameEng, ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <!-- Product Details -->
        <div class="col-md-6">
            <h1 class="mb-3"><?= htmlspecialchars($product->nameEng, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="h4 text-primary mb-4"><?= number_format($product->price, 2) ?> €</p>

            <h5>Description</h5>
            <p><?= nl2br(htmlspecialchars($product->descriptionEng, ENT_QUOTES, 'UTF-8')) ?></p>

            <?php if (!empty($product->nutritionalValueEng)): ?>
                <h5 class="mt-4">Nutritional Value</h5>
                <p><?= nl2br(htmlspecialchars($product->nutritionalValueEng, ENT_QUOTES, 'UTF-8')) ?></p>
            <?php endif; ?>

            <div class="mt-3">
                <!-- 
                    Κρυφό div στο οποίο αποθηκεύονται ως data attributes
                    τα στοιχεία του προϊόντος, αν ο χρήστης είναι loggedin
                    και τα urls των σελίδων login και cart. Αυτό γίνεται για να αποκτήσεις πρόσβαση η
                    javascript σε αυτά τα δεδομένα.
                -->
                <div id="infoDiv" 
                    data-productId="<?= $productId ?>" 
                    data-productPrice="<?= $product->price ?>"
                    data-userIsLoggedIn="<?= $isUserLoggedIn ? '1' : '0' ?>"
                    data-loginRoute="<?= Router::getRoute('login') ?>"
                    data-cartRoute="<?= Router::getRoute('cart') ?>">
                </div>
                <div class="container mb-3 w-100">
                    <!-- Αν το προϊόν υπάρχει ήδη στο καλάθι εμφανίζεται σχετικό μήνυμα -->
                    <div class="row">
                        <span class="text-success"><?= $cartItem != null ? 'Already in your cart' : '' ?></span>
                    </div>
                    <!-- Input ορισμού ποσότητας -->
                    <div class="form-group row">
                        <label for="quantity" class="col-sm-4 col-form-label">Quantity:</label>
                        <div class="col-sm-8">
                            <!-- 
                                Αν το προϊόν υπάρχει στο καλάθι 
                                τότε το input παίρνει την ποσότητα που υπάρχει στο καλάθι 
                                διαφορετικά 1.
                            -->
                            <input type="number" id="quantity" name="quantity" class="form-control"
                                value="<?= $quantity ?? 1 ?>" min="1" required>
                        </div>
                    </div>
                </div>
                <div class="container">
                    <div class="row">
                        <!-- Εμφάνιση της συνολικής αξίας των τεμαχίων -->
                        <div class="col-sm-4">Total:</div>
                        <div class="col-sm-4">
                            <p id="order-value" data-orderValue="<?= $orderValue ?>">
                        </div>
                        <!-- 
                            Κουμπί προσθήκης στο καλάθι. Αν το προϊόν υπάρχει ήδη τότε αναγράφει
                            "Update Cart", διαφορετικά "Add to Cart"
                        -->
                        <div class="col-sm-4">
                            <button id="add-to-cart-btn" class="btn btn-success w-100">
                                <i class="bi bi-cart-plus me-1"></i>
                                <?= $cartItem === null ? 'Add to Cart' : 'Update Cart' ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    //Event listener φόρτωσης του DOM
    document.addEventListener('DOMContentLoaded', () => {
        //Ανεύρεση του κρυφού div με τα στοιχεία του προϊόντως
        const infoDiv = document.getElementById('infoDiv');
        //Εκχώριση των στοιχείων σε ένα αντικείμενο για ευκολότερη διαχείριση
        const info = {
            productId: parseInt(infoDiv.getAttribute('data-productId')),
            productPrice: parseFloat(infoDiv.getAttribute('data-productPrice')),
            userIsLoggedIn: infoDiv.getAttribute('data-userIsLoggedIn') === ('1' || 'true'),
            loginRoute: infoDiv.getAttribute('data-loginRoute'),
            cartRoute: infoDiv.getAttribute('data-cartRoute')
        }
        //Ανεύρεση του κουμπιού προσθήκης στο καλάθι
        const addToCartBtn = document.getElementById('add-to-cart-btn');
        //Ανεύρεση του input της ποσότητας
        const quantityInput = document.getElementById('quantity');
        //Ανεύρεση του <p> που αναφέρει τη συνολική αξία
        const orderVal = document.getElementById('order-value');

        //Εμφάνιση της συνολικής αξίας με το σωστό format
        fixTotalValue(parseInt(quantityInput.value));
        //Event listener για διαχείριση της μεταβολής της ποσότητας
        quantityInput.addEventListener('input', (e) => {
            const q = parseInt(e.target.value);
            //Ενημέρωση συνολικής αξίας
            fixTotalValue(q);
            //Απενεργοποίηση του κουμπιού προσθήκης στο καλάθι αν η είσοδος του χρήστη είναι κενή ή 0
            addToCartBtn.disabled = (!q || q < 0) ? true : false;
        });
        
        //Event listener για προσθήκη στο καλάθι
        addToCartBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const productId = info.productId;
            const quantity = parseInt(quantityInput.value)
            //Αν ο χρήστης είναι συνδεδεμένος πραγματοποιείται κλήση στο endpoint για προσθήκη στο καλάθι
            if (info.userIsLoggedIn) {
                //Πραγματοποίηση κλήσης
                fetch(`index.php?route=cart&action=addToCart&productId=${productId}&quantity=${quantity}`, {
                    method: 'POST',
                    credentials: 'same-origin'
                })
                //Μετατροπή της απάντησης σε json
                .then(response => response.json())
                //Διαχείριση της απάντησης
                .then(json => {
                    //Αν η απάντηση είναι επιτυχής ανακατεύθυνση του χρήστη στο καλάθι
                    if (json.success)
                        window.location.href = info.cartRoute;
                    else {
                        //Διαφορετικά επαναφόρτωση της σελίδας
                        window.location.reload();
                    }
                });                
            } else {
                //Αν ο χρήστης δεν είναι συνδεδεμένος ανακατεύθυνση στο login με τα στοιχεία της φόρμας
                //ώστε μετά το login να επιστρέψει στη σελίδα του προϊόντος και να συμπληρωθεί αυτόματα
                //η ποσότητα που είχε επιλέξει προηγουμένως.
                window.location.href = info.loginRoute + '&action=login&productId=' + productId + '&quantity=' + quantity;
            }
        })

        //Δέχεται ως είσοδο την ποσότητα και υπολογίζει και εμφανίζει τη συνολική αξία
        function fixTotalValue(quant) {
            const q = parseInt(quant);
            const total = (q * info.productPrice).toFixed(2);
            //Ορισμός του text content σε κενό αν το αποτέλεσμα δεν είναι αριθμός
            orderVal.textContent = isNaN(total) ? '' : total.toString() + ' €';
        }
    });
</script>