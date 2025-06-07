<?php

namespace User;

use Configuration\Database;
use Alerts\AlertService;

class UserInfo
{
    public function __construct(
        public int $userId,
        public string $username,
        public string $email,
        public string $firstname,
        public string $lastname,
    ) {}
}

class UserService
{
    // Function εγγραφής χρήστη
    public static function registerUser(
        string $username,
        string $password,
        string $email,
        string $firstname,
        string $lastname
    ): UserInfo {
        //Έλεγχος μοναδικότητας username
        if (!self::checkUsernameUnique($username))
            throw new \InvalidArgumentException("Username is already taken.");
        //Έλεγχος μοναδικότητας email
        if (!self::checkEmailUnique($email))
            throw new \InvalidArgumentException("Email is already taken.");
        //Κρυπτογράφηση κωδικού
        $passwordHash = password_hash($password, PASSWORD_DEFAULT, ['cost' => 12]);
        if (!$passwordHash)
            throw new \RuntimeException("Failed to encrypt your password.");
        //Εισαγωγή δεδομένων χρήστη στη βάση
        $userId = Database::executeNonQuery(
            "INSERT INTO Users (Username, Password, Email, Firstname, Lastname) VALUES (?,?,?,?,?)",
            true,
            "sssss",
            $username,
            $passwordHash,
            $email,
            $firstname,
            $lastname
        );
        //Αν δεν επιστραφεί το νέο $userId σημαίνει ότι κάτι πήγε λάθος κατά την εισαγωγή των δεδομένων
        if ($userId <= 0)
            throw new \RuntimeException("Registration failed.");
        //Login του χρήστη μετά από επιτυχημένη εγγραφή
        return self::loginUser($username, $password);
    }
    
    //Function login χρήστη
    public static function loginUser(string $username, string $password): UserInfo
    {
        //Ανάκτηση χρήστη από τη βάση
        $queryData = Database::getQueryData(
            "SELECT Id, Username, Password, Email, Firstname, Lastname FROM Users WHERE Username = ?",
            's',
            $username
        );
        //Αν δεν βρεθεί χρήστης τότε εμφάνισει μηνύματος για λανθασμένο username ή password
        if (!$queryData || count($queryData) === 0)
            throw new \InvalidArgumentException("invalid username or password");
        //Εκχώριση της εγγραφής σε μεταβλητή
        $dbUser = $queryData[0];
        //Έλεγχος ομοιότητας αποθηκευμένου κωδικών
        if (!password_verify($password, $dbUser['Password'])) {
            //Αν οι κωδικοί δεν έιναι ίδιοι ενημέρωση αποτυχημένων προσπαθειών
            //Για μελλοντική χρήση (πχ Timeout ή Υποχρέωση ανανέωσης κωδικού)
            Database::executeNonQuery(
                "UPDATE Users SET FailedAuthAttempts = FailedAuthAttempts + 1 WHERE Id = ?",
                false,
                'i',
                $dbUser['Id']
            );
            throw new \InvalidArgumentException("invalid username or password");
        }
        //Σε περίπτωση που οι κωδικού ταιριάζουν:
        //Ενημέρωση του status σύνδεσης του χρήστη (κυρίως για λόγους debugging) 
        //και επαναφορά αποτυχημένων προσπαθειών
        Database::executeNonQuery(
            "UPDATE Users SET LoginStatus = 1, FailedAuthAttempts = 0 WHERE Id = ?",
            false,
            's',
            $dbUser['Id']
        );
        //Δημιουργία νέου object με τα στοιχεία του χρήστη
        $loggedInUser = new UserInfo(
            (int)$dbUser['Id'],
            $dbUser['Username'],
            $dbUser['Email'],
            $dbUser['Firstname'],
            $dbUser['Lastname']
        );
        //Ορισμός του χρήστη στο τρέχον Session
        self::setCurrentUser($loggedInUser);
        //Εμφάνιση προσωποιημένου μηνύματος καλωσορίσματος
        AlertService::add("Welcome, {$loggedInUser->firstname}!", 'success');
        //Επιστροφή στοιχείων χρήστη από το Session
        return self::getUserInfo();
    }

    public static function logoutUser() {
        $user = self::getUserInfo();
        if ($user->userId == 0)
            return;

        Database::executeNonQuery(
            "UPDATE Users SET LoginStatus = 0 WHERE Id = ?",
            false,
            's',
            $user->userId
        );

        session_unset();
        session_destroy();
    }

    public static function getUserInfo(): UserInfo
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['userInfo'])) {
            return new UserInfo(0, '', '', '', '');
        }

        $sessionUser = $_SESSION['userInfo'];
        return new UserInfo(
            $sessionUser['userId'],
            $sessionUser['username'],
            $sessionUser['email'],
            $sessionUser['firstname'],
            $sessionUser['lastname']
        );
    }

    public static function getUserIsLoggedIn(): bool 
    {
        return self::getUserInfo()->userId > 0;
    }

    private static function checkUsernameUnique(string $username): bool
    {
        $usernameCount = Database::getScalar(
            "SELECT COUNT(*) FROM Users WHERE Username = ?",
            "s",
            $username
        );

        return $usernameCount === 0;
    }

    private static function checkEmailUnique(string $email): bool
    {
        $emailCount = Database::getScalar(
            "SELECT COUNT(*) FROM Users WHERE Email = ?",
            "s",
            $email
        );

        return $emailCount === 0;
    }

    private static function setCurrentUser(UserInfo $userInfo)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['userInfo'] = [
            'userId' => $userInfo->userId,
            'username' => $userInfo->username,
            'email' => $userInfo->email,
            'firstname' => $userInfo->firstname,
            'lastname' => $userInfo->lastname
        ];
    }
}
