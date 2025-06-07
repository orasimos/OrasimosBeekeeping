<?php

namespace Cart;

use Alerts\AlertService;
use Configuration\Database;
use Product\ProductService;
use Product\Product;
use User\UserService;

//Κλάση CartDetail που αναπαριστά μία εγγραφή στον πίνακα Carts της βάσης
//Επίσης παρέχει τη μέθοδο getValue() που επιστρέφει τη συνολική αξία της εγγραφής
class CartDetail
{
    /**
     * Constructor της κλάσης CartDetail
     * @param int $id
     * @param int $userId
     * @param int $productId
     * @param int $quantity
     * @param float $price
     * @param bool $completed
     * @param Product $productInfo
     */
    public function __construct(
        public int $id,
        public int $userId,
        public int $productId,
        public int $quantity,
        public float $price,
        public bool $completed,
        public Product $productInfo
    ) {}
    /**
     * Επιστρέφει τη συνολική αξία του προϊόντος στο καλάθι
     * @return float η συνολική αξία του προϊόντος στο καλάθι
     */
    public function getValue(): float
    {
        return $this->quantity * $this->price;
    }
}

/**
 * Summary of CartService
 * Διαχειρίζεται τη λογική του καλαθιού αγορών παρέχοντας functions και επικοινωνία με τη βάση δεδομένων.
 */
class CartService
{
    /**
     * Summary of getCartDetails
     * Επιστρέφει όλα τα προϊόντα που έχει ο συνδεδεμένος χρήστης στο καλάθι του
     * είτε pending είτε ολοκληρωμένων παραγγελιών
     * @return CartDetail[] array από αντικείμενα της κλάσης CartDetail
     */
    public static function getCartDetails(): array
    {
        //Ανάκτηση userId από το Session
        $userId = UserService::getUserInfo()->userId;
        //Ανάκτηση εγγραφών του καλαθιού του χρήστη
        $queryData = Database::getQueryData(
            '
                SELECT Id, UserId, ProductId, Quantity, Completed, CompletedOn, Price 
                FROM Carts
                WHERE UserId = ?
            ',
            'i',
            $userId
        );
        //Αρχικοποίηση array εγγραφών του καλαθιού.
        $cartDetails = [];
        //Για κάθε εγγραφή που επεστράφη από το παραπάνω query εισάγεται στο array
        //ένα νέο αντικείμενο της κλασης CartDetail.
        foreach ($queryData as $q) {
            $cartDetails[] = new CartDetail(
                (int) $q['Id'],
                (int) $q['UserId'],
                (int) $q['ProductId'],
                (int) $q['Quantity'],
                (float) $q['Price'],
                (bool) $q['Completed'],
                ProductService::getProductById((int) $q['ProductId'])
            );
        }
        //Επιστροφή του array των CartDetails
        return $cartDetails;
    }
    
    /**
     * Summary of getPendingCartItem
     * Ανάκτηση pending προϊόντος από το καλάθι του χρήστη με βάση το productId
     * @param int $productId τo productId - Foreign key στον πίνακα Products
     * @param int $userId το userId - Foreign key στον πίνακα Users
     * @return ?CartDetail αντικείμενο της κλάσης CartDetail ή null αν δεν βρεθεί
     */
    public static function getPendingCartItem(int $productId, int $userId): ?CartDetail
    {
        //Ανάκτηση των pending εγγραφών του προϊόντος που έχει ο χρήστης στο καλάθι
        $queryData = Database::getQueryData(
            '
                SELECT Id, UserId, ProductId, Quantity, Completed, CompletedOn, Price 
                FROM Carts
                WHERE UserId = ?
                    AND ProductId = ?
                    AND Completed = 0
            ',
            'ii',
            $userId,
            $productId
        );

        //Επιστροφή null αν δεν βρέθηκαν εγγραφές
        if (empty($queryData))
            return null;
        //Αρχικοποίηση array
        $cartItems = [];
        //Για κάθε εγγραφή που επεστράφη εισάγεται ένα νέο αντικείμενο CartDetail στο array
        foreach ($queryData as $q) {
            $cartItems[] = new CartDetail(
                (int) $q['Id'],
                (int) $q['UserId'],
                (int) $q['ProductId'],
                (int) $q['Quantity'],
                (float) $q['Price'],
                (bool) $q['Completed'],
                ProductService::getProductById((int) $q['ProductId'])
            );
        }
        //Επιστροφή του πρώτου αντικειμένου από το array, αφού επιτρέπεται να υπάρχει 
        //μόνο μία pending εγγραφή του προϊόντος στη βάση
        return $cartItems[0];
    }

    /**
     * Summary of addToCart
     * Προσθέτει ένα προϊόν στο καλάθι του χρήστη
     * @param int $productId το productId - Foreign key στον πίνακα Products
     * @param int $quantity η επιλεγμένη ποσότητα
     * @throws \RuntimeException σε περίπτωση που δεν εισαχθεί εγγραφή στον πίνακα
     * @return void
     */
    public static function addToCart(int $productId, int $quantity): void
    {
        //Ανάκτηση στοιχείων προϊόντος
        $product = ProductService::getProductById($productId);
        //Ανάκτηση στοιχείων χρήστη από το Session
        $userInfo = UserService::getUserInfo();
        //Ανάκτηση πιθανώς ήδη υπάρχουσας εγγραφής με το ίδιο προϊόν
        $existingCartItem = self::getPendingCartItem($productId, $userInfo->userId);
        //Αν το προϊόν υπάρχει ήδη τότε ενημερώνεται η ποσότητα της εγγραφής.
        if ($existingCartItem !== null) {  
            $affectedRows = Database::executeNonQuery(
                '
                    UPDATE Carts
                    SET Quantity = ?
                    WHERE Id = ?
                ',
                //Αν θα επιστραφεί το id της εγγραφής που έγινε update, 
                //διαφορετικά επιστρέφεται το πλήθος updated rows
                false,
                'ii',
                $quantity,
                $existingCartItem->id
            );
        //Διαφορετικά εισάγεται νέα εγγραφή στον πίνακα Carts
        } else {
            $affectedRows = Database::executeNonQuery(
                "
                    INSERT INTO Carts (UserId, ProductId, Quantity, Price)
                    VALUES (?, ?, ?, ?)
                ",
                //Αν θα επιστραφεί το id της εγγραφής που έγινε insert,
                //διαφορετικά επιστρέφεται το πλήθος inserted rows
                false,
                'iiid',
                $userInfo->userId,
                $productId,
                $quantity,
                $product->price
            );
        }
        //Αν το πλήθος των affected rows είναι είναι μικρότερο ή ίσο με 0 
        //σημαίνει ότι δεν ενημερώθηκε η βάση
        if ($affectedRows <= 0)
            throw new \RuntimeException("Failed to add product to cart.");
        //Προσθήκη του μηνύματος επιτυχίας στα alerts ώστε να εμφανιστεί στον χρήστη
        AlertService::add("Added to cart successfully", "success");
    }
    
    /**
     * Summary of removeFromCart
     * αφαιρεί ένα προϊόν από το καλάθι του χρήστη
     * @param int $cartId το id της εγγραφής που πρόκειται να διαγραφεί
     * @throws \RuntimeException
     * @return void
     */
    public static function removeFromCart(int $cartId): void
    {
        //Delete της εγγραφής από τη βάση με βάση το id
        $affectredRows = Database::executeNonQuery(
            "DELETE FROM Carts WHERE Id = ?",
            false,
            'i',
            $cartId
        );
        //Αν δεν επηρεαστούν εγγραφές σημαίνει οτι η βάση δεν ενημερώθηκε.
        if ($affectredRows <= 0)
            throw new \RuntimeException("Failed to remove from cart.");
        //Προσθήκη του μηνύματος επιτυχίας στα alerts ώστε να εμφανιστεί στον χρήστη
        AlertService::add("Removed from cart", "success");
    }
    /**
     * Summary of completeOrder
     * Μαρκάρει το σύνολο των pending προϊόντων του καλαθιού ως Completed με βάση τα ids τους
     * @param string $cartIds τα ids των εγγραφών του πίνακα Carts σε μορφή comma separated string
     * @throws \RuntimeException
     * @return void
     */
    public static function completeOrder(string $cartIds): void
    {
        //Δημιουργία array με τα αντίστοιχα int values του string cartIds
        $cartIds = array_map('intval', explode(',', $cartIds));
        //Αρχικοποίηση μεταβλητής που καταγράφει τις αποτυχημένες ενημερώσεις
        $failedCount = 0;
        //Για κάθε id ενημέρωση της εγγραφής στη βάση δεδομένων με Completed = 1 
        //και CompletedOn το τρέχω TimeStamp
        foreach ($cartIds as $cartId) {
            $affectedRows = Database::executeNonQuery(
                "UPDATE Carts SET Completed = 1, CompletedOn = CURRENT_TIMESTAMP() WHERE Id = ?",
                false,
                "i",
                $cartId
            );
            //Αν δεν επηρεαστούν εγγραφές αυξάνεται το πλήθος των αποτυχημένων ενημερώσεων κατά 1
            if ($affectedRows <= 0)
                $failedCount++;
        }
        //Αν τελικά το πλήθος των αποτυχημένων ενημερώσεων είναι μεγαλύτερο του 0
        //δημιουργείται exception ώστε να ενημερωθεί ο χρήστης.
        if ($failedCount > 0)
            throw new \RuntimeException("Some items are unable to be shipped. Please try again");
        //Προσθήκη του μηνύματος επιτυχίας στα alerts ώστε να εμφανιστεί στον χρήστη.
        AlertService::add("Your order will ship shortly", "success");
    }
}
