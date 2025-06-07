<?php

// Ορισμός string_types ώστε η PHP να μην κάνει αφθαίρετο casting
declare(strict_types=1);

// Έναρξη session αν δεν είναι ήδη ενεργό
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Import των κλάσεων - services της εφαρμογής
require_once __DIR__ . "/php/services/config.php";
require_once __DIR__ . "/php/services/user-service.php";
require_once __DIR__ . "/php/services/product-service.php";
require_once __DIR__ . "/php/services/alert-service.php";
require_once __DIR__ . "/php/services/cart-service.php";

use Configuration\AppDetails;
use Configuration\Router;
use User\UserService;
use Product\ProductService;
use Alerts\AlertService;
use Cart\CartService;

// Ανάκτηση ονόματος εφαρμογής για εμφάνιση στο tab
$appname = AppDetails::getAppName();
// Ανάκτηση στοιχείων συνδεδεμένου χρήστη
$userInfo = UserService::getUserInfo();
// Ανάκτηση κατάστασης σύνδεσης χρήστη
$isUserLoggedIn = UserService::getUserIsLoggedIn();

// Εκχώριση τρέχοντος route σε μεταβλητή για ευκολότερη διαχείριση
// Αν είναι κενό ή δεν υπάρχει στα επιτρεπόμενα routes τότε ορίζεται το 'home'
$route = trim($_GET['route'] ?? '/home', '/');
if (!Router::isInWhitelist($route)) {
    http_response_code(404);
    $route = 'home';
}

// Ανάκτηση ενέργειας για τη διαχείριση των requests
$action = $_GET['action'] ?? '';

//Έλεγχος του route και του action
if ($route === 'cart' && $action === 'addToCart') {
    //Έλεγχος και εκχώρηση των παραμέτρων της κλήσης σε μεταβλητές
    $productId = isset($_REQUEST['productId']) && is_numeric($_REQUEST['productId'])
        ? (int) $_REQUEST['productId']
        : 0;
    $quantity = isset($_REQUEST['quantity']) && is_numeric($_REQUEST['quantity'])
        ? (int) $_REQUEST['quantity']
        : 0;

    //Αν δεν είναι έγκυρες απάντηση με BadRequest(Error 400)
    if ($productId < 1 || $quantity < 1) {
        http_response_code(400);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'error' => 'Invalid product or quantity'
        ]);
        exit;
    }

    try {
        //Κλήση της addToCart για εισαγωγή του προϊόντος στο καλάθι
        CartService::addToCart($productId, $quantity);
        //Επιστροφή απάντησης με Ok(200) και το success = true
        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    } catch (Exception $ex) {
        //Διαχείριση των Exceptions που μπορεί να προκύψουν κατά την εισαγωγή στο καλάθι
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => $ex->getMessage()
        ]);
        exit;
    }
}

try {
    $cartDetails = CartService::getCartDetails();
    $pendingItems = array_filter($cartDetails, function ($i) {
        return $i->completed == false;
    });
    $completedItems = array_filter($cartDetails, function ($i) {
        return $i->completed == true;
    });
} catch (Exception $e) {

}

if ($route === 'cart') {
    if ($action === 'removeFromCart') {
        $cartId = isset($_REQUEST['cartId']) && is_numeric($_REQUEST['cartId'])
            ? (int) $_REQUEST['cartId']
            : 0;

        try {
            CartService::removeFromCart($cartId);
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Removed from cart!'
            ]);
            exit;
        } catch (Exception $ex) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $ex->getMessage()
            ]);
        }
    }

    if ($action === 'completeOrder') {
        $cartIds = isset($_REQUEST['cartIds'])
            ? $_REQUEST['cartIds']
            : '';
        try {
            CartService::completeOrder($cartIds);
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Removed from cart!'
            ]);
            exit;
        } catch (Exception $ex) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $ex->getMessage()
            ]);
        }
    }
}

if ($route === 'logout') {
    UserService::logoutUser();
    header('Location: ' . Router::getRoute('home'));
}

?>

<!-- Layout εφαρμογής -->
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Αρχικοποίηση και links στα απαραίτητα imports -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Ορισμός ονόματος εφαρμογής στο browser tab -->
    <title><?= $appname ?></title>
    <!-- Ορισμός favicon στο browser tab -->
    <link rel="icon" href="assets/img/logo-192x192.png">
    <!-- Link στο Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css"
        integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Link στα google fonts για το Roboto font της εφαρμογής -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <!-- Link στο styles.css με το custom CSS της εφαρμογής -->
    <link rel="stylesheet" href="styles.css">
</head>

<body class="pt-5">
    <!-- Εμφάνιση header -->
    <?php require __DIR__ . '/php/layout/shared/header.php'; ?>

    <!-- Εμφάνιση επιμέρους σελίδων - Προσομοίωση RouterOutlet -->
    <main class="container bg-body-tertiary" style="min-height: 100vh">
        <?php
            // Έλεγχος αν το requested url ανήκει στα επιτρεπόμενα
            // Αν δεν ανήκει τότε ορισμός του route στο 'home'
            if (!Router::isInWhitelist($route)) {
                $route = 'home';
            }
            // Εμφάνιση της απαιτούμενης σελίδας
            require __DIR__ . '/' . Router::getWhitelist()[$route];
        ?>
    </main>

    <!-- Εμφάνιση footer -->
    <?php require __DIR__ . '/php/layout/shared/footer.php'; ?>

    <!-- Εμφάνιση alerts αν υπάρχουν -->
    <?php AlertService::display(); ?>

    <!-- Φόρτωση scripts του Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous">
    </script>

    <!-- Event listener για εμφάνιση των ειδοποιήσεων μόλις φορτώσει η σελίδα -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.toast').forEach(t => {
                new bootstrap.Toast(t, {
                    delay: 5000
                }).show();
            });
        });
    </script>
</body>

</html>