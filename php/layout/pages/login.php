<?php
// Import απαιτούμενων services
require_once __DIR__ . "/../../services/config.php";
require_once __DIR__ . "/../../services/alert-service.php";
use Configuration\Router;
use User\UserService;
use Alerts\AlertService;
// Εκχώριση του action parameter σε μεταβλητή ώστε να εμφανιστεί
// η κατάλληλη φόρμα ανάλογα με το tab που έχει επιλέξει ο χρήστης
$formMode = $_GET['action'] ?? 'login';
// δημιουργία url για κλήση στον σέρβερ
// index.php/route=login&action=login ή register
$formActionUrl = Router::getRoute('login') . '&action=' . $formMode;
// Αν ο χρήστης ανακατευθύνθηκε από τη σελίδα ενός προϊόντος αποθηκεύονται τα στοιχεία
// ώστε να ανακατευθυνθεί πάλι στη σελίδα του προϊόντος
$pendingProductId = isset($_GET['productId']) ? (int)$_GET['productId'] : null;
$pendingQty = isset($_GET['quantity']) ? (int)$_GET['quantity'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Εκχώριση των inputs της φόρμας login σε μεταβλητές
        $username = filter_input(INPUT_POST, 'username', FILTER_UNSAFE_RAW);
        $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
        // Εκχώριση των έξτρα inputs της φόρμας register σε μεταβλητές
        if ($formMode === 'register') {
            $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
            $firstname = filter_input(INPUT_POST, 'firstname', FILTER_UNSAFE_RAW);
            $lastname = filter_input(INPUT_POST, 'lastname', FILTER_UNSAFE_RAW);

            // κλήση της static function για εγγραφή του χρήστη στη βάση
            UserService::registerUser($username, $password, $email, $firstname, $lastname);
        } else {
            // κλήση της static function για login του χρήστη
            UserService::loginUser($username, $password);
        }
        // Αν υπάρχουν στοιχεία προϊόντος ανακατεύθυνση στη σελίδα του προϊόντος
        $postProductId = isset($_POST['productId']) ? (int)$_POST['productId'] : null;
        $postQty = isset($_POST['quantity']) ? (int)$_POST['quantity'] : null;
        if ($postProductId != null && $postQty != null && $postQty > 0) {
            header('Location: ' . Router::getRoute('product') . '&productId=' . $postProductId . '&quantity=' . $postQty);
            exit;
        }
        // Ανακατεύθυνση στο home
        header('Location: ' . Router::getRoute('home'));
        exit;
    } catch (Exception $ex) {
        // Αν προκύψει σφάλμα ("invalid username or password" ή άλλο) κατά την εγγραφή 
        // ή το login το μήνυμα του exception
        // εμφανίζεται στο κάτω μέρος της σελίδας ώστε να ενημερωθεί ο χρήστης
        AlertService::add($ex->getMessage(), 'danger');
    }
}
?>

<div class="container-sm pt-5" style="max-width: 500px;">
    <!-- Λίστα με τα 2 κουμπιά για την επιλογή φόρμας login ή register -->
    <ul class="nav nav-pills nav-fill mb-4 mt-4 w-100 p-2">
        <li class="nav-item">
            <a class="nav-link<?= $formMode === 'login' ? ' active' : '' ?>"
                href="<?= Router::getRoute('login') ?>&action=login">Login</a>
        </li>
        <li class="nav-item">
            <a class="nav-link<?= $formMode === 'register' ? ' active' : '' ?>"
                href="<?= Router::getRoute('login') ?>&action=register">Register</a>
        </li>
    </ul>
    
    <?php if ($formMode === 'login'): ?>
        <!-- Φόρμα Login -->
        <form id="<?= $formMode ?>-form" action="<?= $formActionUrl ?>" method="post" class="login-form m-auto mx-2 border border-primary rounded p-4">
            <!-- Διατήρηση των στοιχείων του προϊόντος σε κρυφά inputs -->
            <?php if ($pendingProductId !== null && $pendingQty !== null): ?>
                <input type="hidden" name="productId" value="<?= $pendingProductId ?>">
                <input type="hidden" name="quantity" value="<?= $pendingQty ?>">
            <?php endif; ?>
            <!-- Επικεφαλίδα φόρμας -->
            <h1 class="h3 mb-4 fw-normal" data-tagId="Tag_1">Please Login</h1>
            <!-- Username -->
            <div class="form-floating mb-2">
                <input id="username" name="username" type="text" class="form-control" placeholder="Username..." required data-tagId="Tag_2">
                <label for="username" data-tagId="Tag_3">Username</label>
            </div>
            <!-- Password -->
            <div class="form-floating mb-4">
                <input id="password" name="password" type="password" class="form-control" placeholder="Password..." required data-tagId="Tag_4">
                <label for="password" data-tagId="Tag_5">Password</label>
            </div>
            <button class="btn btn-primary w-100 py-2" type="submit" data-tagId="Tag_6">Log In</button>
        </form>

    <?php else: ?>
        <!-- Φόρμα Register -->
        <form id="<?= $formMode ?>-form" action="<?= $formActionUrl ?>" method="post" class="login-form m-auto mx-2 border border-primary rounded p-4">
            <!-- Διατήρηση των στοιχείων του προϊόντος σε κρυφά inputs -->
            <?php if ($pendingProductId !== null && $pendingQty !== null): ?>
                <input type="hidden" name="productId" value="<?= $pendingProductId ?>">
                <input type="hidden" name="quantity" value="<?= $pendingQty ?>">
            <?php endif; ?>
            <!-- Επικεφαλίδα Φόρμας -->
            <h1 class="h3 mb-4 fw-normal" data-tagId="Tag_7">Please Register</h1>
            <!-- Username -->
            <div class="form-floating mb-2">
                <input id="username" name="username" type="text" class="form-control" placeholder="Username..." required data-tagId="Tag_2">
                <label for="username" data-tagId="Tag_3">Username</label>
            </div>
            <!-- Password -->
            <div class="form-floating mb-4">
                <input id="password" name="password" type="password" class="form-control" placeholder="Password..." required data-tagId="Tag_4">
                <label for="password" data-tagId="Tag_5">Password</label>
            </div>
            <!-- Confirm Password -->
            <div class="form-floating mb-4">
                <input id="confirm-password" name="confirm-password" type="password" class="form-control" placeholder="Confirm Password..." required data-tagId="Tag_8">
                <label for="confirm-password" data-tagId="Tag_9">Confirm Password</label>
            </div>
            <!-- Email -->
            <div class="form-floating mb-2">
                <input id="email" name="email" type="email" class="form-control" placeholder="Email..." required data-tagId="Tag_10">
                <label for="email" data-tagId="Tag_11">Email</label>
            </div>
            <!-- Firstname -->
            <div class="form-floating mb-2">
                <input id="firstname" name="firstname" type="text" class="form-control" placeholder="First Name..." required data-tagId="Tag_12">
                <label for="firstname" data-tagId="Tag_13">First Name</label>
            </div>
            <!-- Lastname -->
            <div class="form-floating mb-2">
                <input id="lastname" name="lastname" type="text" class="form-control" placeholder="Last Name..." required data-tagId="Tag_14">
                <label for="lastname" data-tagId="Tag_15">Last Name</label>
            </div>
            <button class="btn btn-primary w-100 py-2" type="submit" data-tagId="Tag_16">Register</button>
        </form>

    <?php endif; ?>

</div>

<script>
    //Δημιουργία event listener για τον έλγχο ομοιότητας των κωδικών κατά το registration
    (function() {
        //Εκχώριση των elements σε μεταβλητές
        const form = document.getElementById('register-form');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm-password');

        // Αν βρεθεί confirmPassword τότε δημιουργείται event listener για τον έλεγχο ομοιότητας
        if (confirmPassword) {
            confirmPassword.addEventListener('input', () => {
                // Ορισμός μηνύματος αν οι κωδικοί δεν είναι ίδιοι 
                // αλλιώς αφαίρεση μηνύματος αν έχει ήδη εμφανιστεί
                if (confirmPassword.value !== password.value) {
                    confirmPassword.setCustomValidity("Passwords don't match");
                } else {
                    confirmPassword.setCustomValidity("");
                }
            });
        }

        // Δημιουργία event listener για το submit της φόρμας 
        // ώστε να εμφανιστεί το μήνυμα ομοιότητας κωδικών
        if (form) {
            form.addEventListener('submit', e => {
                if (confirmPassword.value !== password.value) {
                    e.preventDefault();
                    //Εμφάνιση μηνύματος
                    confirmPassword.reportValidity();
                }
            });
        }
    })();
</script>