<?php
require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/functions.php';

class CustomerService
{
    /**
     * Get all customers with optional search/filter and pagination.
     */
    public static function getAllCustomers(
        string $search = '',
        int    $page   = 1
    ): array {
        $where  = [];
        $params = [];
        $limit  = ITEMS_PER_PAGE ?? 15;
        $offset = ($page - 1) * $limit;

        if ($search !== '') {
            $where[]           = "(c.first_name LIKE :search OR c.last_name LIKE :search OR c.email LIKE :search)";
            $params[':search'] = "%$search%";
        }

        $where_sql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        // Get total count
        $count_sql = "SELECT COUNT(*) FROM customers c $where_sql";
        $total     = (int) db_query($count_sql, $params)->fetchColumn();

        // Get paginated results
        $sql = "
            SELECT 
                c.*,
                COUNT(f.favorite_id) as favorite_count
            FROM customers c
            LEFT JOIN customer_favorites f ON c.customer_id = f.customer_id
            $where_sql
            GROUP BY c.customer_id
            ORDER BY c.created_at DESC
            LIMIT :limit OFFSET :offset
        ";

        $params[':limit']  = $limit;
        $params[':offset'] = $offset;

        $stmt   = db_query($sql, $params);
        $rows   = $stmt->fetchAll();
        $pages  = ceil($total / $limit);

        return [
            'rows'       => $rows,
            'pagination' => [
                'current' => $page,
                'total'   => $pages,
                'count'   => $total,
                'per_page' => $limit,
            ],
        ];
    }

    /**
     * Get a single customer by ID.
     */
    public static function getCustomer(int $id): ?array
    {
        $sql = "
            SELECT 
                c.*,
                COUNT(f.favorite_id) as favorite_count
            FROM customers c
            LEFT JOIN customer_favorites f ON c.customer_id = f.customer_id
            WHERE c.customer_id = :id
            GROUP BY c.customer_id
        ";

        return db_query($sql, [':id' => $id])->fetch() ?: null;
    }

    /**
     * Get customer by email.
     */
    public static function getCustomerByEmail(string $email): ?array
    {
        return db_query(
            "SELECT * FROM customers WHERE email = :email",
            [':email' => $email]
        )->fetch() ?: null;
    }

    /**
     * Check if email exists (for validation).
     */
    public static function emailExists(string $email, ?int $exclude_id = null): bool
    {
        $sql = "SELECT COUNT(*) FROM customers WHERE email = :email";
        $params = [':email' => $email];

        if ($exclude_id !== null) {
            $sql .= " AND customer_id != :id";
            $params[':id'] = $exclude_id;
        }

        return (int) db_query($sql, $params)->fetchColumn() > 0;
    }

    /**
     * Create a new customer.
     */
    public static function createCustomer(
        string $first_name,
        string $last_name,
        string $email,
        string $password,
        string $phone = ''
    ): ?int {
        if (self::emailExists($email)) {
            return null;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        try {
            db_query(
                "
                INSERT INTO customers 
                (first_name, last_name, email, phone, password, is_active)
                VALUES (:fn, :ln, :em, :ph, :pw, 1)
                ",
                [
                    ':fn' => $first_name,
                    ':ln' => $last_name,
                    ':em' => $email,
                    ':ph' => $phone,
                    ':pw' => $hash,
                ]
            );

            return db_last_id();
        } catch (PDOException $e) {
            error_log("CustomerService::createCustomer Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update customer profile.
     */
    public static function updateCustomer(
        int    $id,
        string $first_name,
        string $last_name,
        string $email,
        string $phone = '',
        string $photo = null
    ): bool {
        // Check if email is taken by another customer
        if (self::emailExists($email, $id)) {
            return false;
        }

        try {
            $sql = "
                UPDATE customers 
                SET first_name = :fn, last_name = :ln, email = :em, phone = :ph
            ";
            $params = [
                ':id' => $id,
                ':fn' => $first_name,
                ':ln' => $last_name,
                ':em' => $email,
                ':ph' => $phone,
            ];

            if ($photo !== null) {
                $sql .= ", photo = :photo";
                $params[':photo'] = $photo;
            }

            $sql .= " WHERE customer_id = :id";

            $result = db_query($sql, $params);

            return true;
        } catch (PDOException $e) {
            error_log("CustomerService::updateCustomer Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Update customer password.
     */
    public static function updatePassword(int $id, string $new_password): bool
    {
        $hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);

        try {
            $result = db_query(
                "UPDATE customers SET password = :pw WHERE customer_id = :id",
                [':id' => $id, ':pw' => $hash]
            );

            return $result->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("CustomerService::updatePassword Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verify password (for login).
     */
    public static function verifyPassword(string $email, string $password): ?array
    {
        $customer = self::getCustomerByEmail($email);

        if (!$customer || !password_verify($password, $customer['password'])) {
            return null;
        }

        if (!$customer['is_active']) {
            throw new Exception('Your account has been deactivated. Please contact support.');
        }

        return $customer;
    }

    /**
     * Toggle customer active status.
     */
    public static function toggleActive(int $id): void
    {
        db_query(
            "UPDATE customers SET is_active = NOT is_active WHERE customer_id = :id",
            [':id' => $id]
        );
    }

    /**
     * Delete a customer and their favorites.
     */
    public static function deleteCustomer(int $id): bool
    {
        try {
            // Favorites are cascade deleted due to foreign key
            $result = db_query(
                "DELETE FROM customers WHERE customer_id = :id",
                [':id' => $id]
            );

            return $result->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("CustomerService::deleteCustomer Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Add a pharmacy to customer favorites.
     */
    public static function addFavorite(int $customer_id, int $pharmacy_id): bool
    {
        try {
            db_query(
                "
                INSERT INTO customer_favorites (customer_id, pharmacy_id)
                VALUES (:cid, :pid)
                ON DUPLICATE KEY UPDATE favorite_id = favorite_id
                ",
                [':cid' => $customer_id, ':pid' => $pharmacy_id]
            );

            return true;
        } catch (PDOException $e) {
            error_log("CustomerService::addFavorite Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Remove a pharmacy from customer favorites.
     */
    public static function removeFavorite(int $customer_id, int $pharmacy_id): bool
    {
        try {
            $result = db_query(
                "DELETE FROM customer_favorites WHERE customer_id = :cid AND pharmacy_id = :pid",
                [':cid' => $customer_id, ':pid' => $pharmacy_id]
            );

            return $result->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("CustomerService::removeFavorite Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get customer's favorite pharmacies.
     */
    public static function getFavorites(int $customer_id): array
    {
        $sql = "
            SELECT p.* 
            FROM customer_favorites cf
            JOIN pharmacies p ON cf.pharmacy_id = p.pharmacy_id
            WHERE cf.customer_id = :id AND p.status = 'active'
            ORDER BY cf.created_at DESC
        ";

        return db_query($sql, [':id' => $customer_id])->fetchAll() ?: [];
    }

    /**
     * Check if customer has favorited a pharmacy.
     */
    public static function isFavorited(int $customer_id, int $pharmacy_id): bool
    {
        $count = db_query(
            "SELECT COUNT(*) FROM customer_favorites WHERE customer_id = :cid AND pharmacy_id = :pid",
            [':cid' => $customer_id, ':pid' => $pharmacy_id]
        )->fetchColumn();

        return (int)$count > 0;
    }
}
