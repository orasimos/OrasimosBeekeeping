<?php

namespace Configuration;

class Config
{
    private static ?array $data = null;

    public static function getConfig(): array
    {
        if (self::$data === null) {
            $path = __DIR__ . "/../../appsettings.json";
            $json = file_get_contents($path);
            if ($json === false) {
                throw new \RuntimeException("Could not find config file at {$path}");
            }
            self::$data = json_decode($json, true);
        }

        return self::$data;
    }

    public static function get(string $section)
    {
        $config = self::getConfig();
        if (!isset($config[$section]) || !is_array($config[$section])) {
            throw new \OutOfBoundsException("Missing section {$section} in config file");
        }

        return $config[$section];
    }
}

/**
 * Summary of Database
 * Κλαση με στατικές functions για τη διευκόλυνση επικοινωνίας με τη βάση δεδομένων.
 */
class Database
{
    //Αρχικοποίηση της σύνδεσης με τη βάση ως null
    private static ?\mysqli $conn = null;

    /**
     * Summary of getConnection
     * Επιστρέφει το connection με τη βάση δεδομένων.
     * Αν το connection είναι null τότε το αρχικοποιεί.
     * @throws \RuntimeException
     * @return \mysqli|null η σύνδεση με τη βάση
     */
    private static function getConnection(): \mysqli
    {
        //Αν το $conn είναι null        
        if (self::$conn === null) {
            //Ανάκτηση στοιχείων για σύνδεση με τη βάση
            $dbConfig = Config::get("DbConnection");

            $host = $dbConfig["host"] ?? 'localhost';
            $port = $dbConfig["port"];
            $dbName = $dbConfig["dbName"];
            $user = $dbConfig["user"];
            $pass = $dbConfig["pass"];

            //Δημιουργία του connection και εκχώρηση στην $conn
            self::$conn = new \mysqli($host, $user, $pass, $dbName, $port);
            if (self::$conn->connect_error) {
                throw new \RuntimeException(
                    "DB Connection failed ({self::\$conn->connect_errno}): "
                        . self::$conn->connect_error
                );
            }
        }
        //Επιστροφή του connection
        return self::$conn;
    }

    /**
     * Επιστρέφει την τιμή ενός πεδίου από τη βάση
     * @param string $sql Το sql query
     * @param string $types οι τύποι των παραμέτρων (comma separated string)
     * @param array $params το array των παραμέτρων
     * @throws \RuntimeException
     */
    public static function getScalar(string $sql, string $types = '', ...$params)
    {
        $con = self::getConnection();
        //Προετοιμασία του sql statement
        $db = $con->prepare($sql);
        //Αν αποτύχει η προετοιμασία
        if (!$db)
            throw new \RuntimeException($con->error);
        //Αν έχουν οριστεί τύποι δεδομένων τοτε bind τις παραμέτρους στο statement
        if ($types !== '') {
            $db->bind_param($types, ...$params);
        }
        //Εκτέλεση του query
        $db->execute();
        //Bind το αποτέλεσμα στη μεταβλητή $result
        $db->bind_result($result);
        //Λήψη του αποτελέσματος
        $db->fetch();
        //Κλείσιμο του prepared satement
        $db->close();
        //Επιστροφή του αποτελέσματος
        return $result;
    }

    /**
     * Επιστρέφει τα data rows του query
     * @param string $sql Το sql query
     * @param string $types οι τύποι των παραμέτρων (comma separated string)
     * @param array $params το array των παραμέτρων
     * @throws \RuntimeException
     * @return array το array με τα data rows
     */
    public static function getQueryData(string $sql, string $types = '', ...$params)
    {
        $con = self::getConnection();
        $db = $con->prepare($sql);

        if (!$db)
            throw new \RuntimeException($con->error);

        if ($types !== '') {
            $db->bind_param($types, ...$params);
        }

        $db->execute();
        $result = $db->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $db->close();

        return $rows;
    }

    /**
     * Summary of executeNonQuery
     * @param string $sql Το sql query
     * @param bool $outputInserted true: για επιστροφή του inserted, false: για affected rows
     * @param string $types οι τύποι των παραμέτρων (comma separated string)
     * @param array $params το array των παραμέτρων
     * @throws \RuntimeException
     * @return int|string ανάλογα με την τιμή του outoutInserted
     */
    public static function executeNonQuery(string $sql, bool $outputInserted, string $types = '', ...$params)
    {
        $con = self::getConnection();
        $db = $con->prepare($sql);

        if (!$db)
            throw new \RuntimeException($con->error);

        if ($types !== '') {
            $db->bind_param($types, ...$params);
        }

        $executed = $db->execute();
        if (! $executed) {
            // Execution failed (could be a constraint or other SQL error)
            error_log("mysqli::execute failed: " . $db->error);
            throw new \RuntimeException("Execute error: " . $db->error);
        }

        $output = null;
        if ($outputInserted)
            $output = $con->insert_id;
        else
            $output = $db->affected_rows;
        $db->close();
        return $output;
    }
}

class AppDetails
{
    private static ?array $details = null;

    public static function getAppDetails(): array
    {
        if (self::$details === null) {
            self::$details = Config::get("AppDetails");
        }

        return self::$details;
    }

    public static function getAppName(): string
    {
        return htmlspecialchars(self::getAppDetails()["name"], ENT_QUOTES, 'UTF-8');
    }

    public static function getBaseUrl(): string
    {
        return htmlspecialchars(self::getAppDetails()["baseUrl"], ENT_QUOTES, 'UTF-8');
    }
}

class Router
{
    // Ορισμός base path
    private const BASE_PATH = 'php/layout/pages/';

    // Ορισμός επιτρεπόμενων urls και ισοδυναμία με τα αντίστοιχα αρχεία .php
    private static ?array $whitelist = [
        'home' => self::BASE_PATH . 'home.php',
        'products' => self::BASE_PATH . 'products.php',
        'product' => self::BASE_PATH . 'product.php',
        'contact' => self::BASE_PATH . 'contact.php',
        'about' => self::BASE_PATH . 'about.php',
        'login' => self::BASE_PATH . 'login.php',
        'cart' => self::BASE_PATH . 'cart.php',
        'logout' => self::BASE_PATH . 'logout.php'
    ];

    // Βοηθητική function για ανάκτηση επιτρεπόμενων routes
    public static function getWhitelist(): array
    {
        return self::$whitelist;
    }

    // Έλεγχος αν ένα route ανήκει στα επιτρεπόμενα
    public static function isInWhitelist(string $route): bool
    {
        return array_key_exists($route, self::getWhitelist());
    }

    // Βοηθητική μέθοδος για ανάκτηση url συμπεριλαμβανομένου του route
    public static function getRoute(string $route): string
    {
        return "index.php?route=" . $route;
    }
}
