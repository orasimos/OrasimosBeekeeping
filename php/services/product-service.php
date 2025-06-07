<?php

namespace Product;

use Configuration\Database;
use Configuration\AppDetails;

class Product
{
    private string $baseImgUrl = "/assets/img/products/";

    public function __construct(
        public int $id,
        public string $name,
        public string $nameEng,
        public ProductType $type,
        public string $description,
        public string $descriptionEng,
        public string $nutritionalValue,
        public string $nutritionalValueEng,
        public string $imageName,
        public float $price
    ) {}

    public function getImageUrl(): string
    {
        return AppDetails::getBaseUrl() . $this->baseImgUrl . $this->imageName;
    }
}

class ProductType
{
    public function __construct(
        public int $id,
        public string $description,
        public string $descriptionEng
    ) {}
}

class ProductService
{
    private const BASE_SQL = <<<'SQL'
        SELECT
            p.Id,
            p.Name,
            p.NameEng,            
            p.Description,
            p.DescriptionEng,
            p.NutritionalValue,
            p.NutritionalValueEng,
            p.ImageName,
            IFNULL(pp.Price,0) AS Price,
            pp.Date,
            p.Type,
            pt.Description AS TypeDescription,
            pt.DescriptionEng AS TypeDescriptionEng
        FROM Products p
        INNER JOIN ProductTypes pt ON pt.Id = p.Type
        LEFT OUTER JOIN (
            SELECT MAX(Date) AS MaxDate, ProductId
            FROM ProductPrices
            GROUP BY ProductId
        ) AS ppDate ON ppDate.ProductId = p.Id
        LEFT OUTER JOIN ProductPrices AS pp ON pp.ProductId = p.Id AND pp.Date = ppDate.MaxDate
    SQL;

    /**
     * Core fetch method.
     *
     * @param string $where additional WHERE clause for fine filtering
     * @param string $types mysqli bind_param type string
     * @param mixed[] $params values to bind
     * @return Product[]
     */
    private static function fetch(
        string $where = '',
        string $types = '',
        array $params = [],
        ?int $limit = null,
        ?int $offset = null
    ): array {
        $sql = self::BASE_SQL . "\n" . $where;
        if ($limit !== null) {
            $sql .= "\nLIMIT " . intval($limit);
            if ($offset !== null) {
                $sql .= " OFFSET " . intval($offset);
            }
        }

        $queryData = Database::getQueryData($sql, $types, ...$params);
        if ($queryData === false) {
            throw new \RuntimeException("Invalid query sent to database.");
        }

        $products = [];
        foreach ($queryData as $q) {
            $products[] = new Product(
                (int) $q['Id'],
                (string) $q['Name'],
                (string) $q['NameEng'],
                new ProductType(
                    (int) $q['Type'],
                    (string) $q['TypeDescription'],
                    (string) $q['TypeDescriptionEng']
                ),
                (string) $q['Description'],
                (string) $q['DescriptionEng'],
                (string) $q['NutritionalValue'],
                (string) $q['NutritionalValueEng'],
                (string) $q['ImageName'],
                (float) $q['Price']
            );
        }
        return $products;
    }


    /**
     * @param string $limit Specify row count to fetch
     * @param string $offset Specify the dataset offset
     * @return Product[] */
    public static function getProducts(?int $limit = null, ?int $offset = null)
    {
        $products = self::fetch('', '', [],  $limit, $offset);
        return $products;
    }

    public static function getProductById(int $productId): Product
    {
        $products = self::fetch(
            'WHERE p.Id = ?',
            'i',
            [$productId]
        );

        if (empty($products))
            throw new \RuntimeException("Product #{$productId} not found.");

        return $products[0];
    }

    /**
     * @param int $type The product type Id
     * @param string $limit Specify row count to fetch
     * @param string $offset Specify the dataset offset
     * @return Product[]
     *  */
    public static function getProductsByType(int $type, ?int $limit, ?int $offset)
    {
        $products = self::fetch(
            'WHERE p.Type = ?',
            'i',
            [$type]
        );

        if (empty($products))
            throw new \RuntimeException('No products found.');

        return $products;
    }

    /** @return ProductType[] */
    public static function getProductTypes(): array
    {
        $queryData = Database::getQueryData(
            'SELECT Id, Description, DescriptionEng FROM ProductTypes',
            ''
        );

        if ($queryData === false) {
            throw new \RuntimeException('Failed to fetch Product Types from database.');
        }

        $productTypes = [];
        foreach ($queryData as $q) {
            $productTypes[] = new ProductType(
                (int) $q['Id'],
                (string) $q['Description'],
                (string) $q['DescriptionEng']
            );
        }

        return $productTypes;
    }

    /** @return Product[] */
    public static function getRandomProductsByType(): array
    {
        $allProducts = self::fetch();

        $byType = [];
        foreach ($allProducts as $p) {
            $byType[$p->type->id][] = $p;
        }

        $randomProducts = [];
        foreach ($byType as $type => $group) {
            if (count($group) > 0) {
                $randomProducts[$type] = $group[array_rand($group)];
            }
        }

        return $randomProducts;
    }
}
