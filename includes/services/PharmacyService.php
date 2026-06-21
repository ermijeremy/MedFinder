<?php
require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/functions.php';

class PharmacyService
{
    /** Get an active pharmacy for the public detail page. */
    public static function getPublicById(int $id): ?array
    {
        $row = db_query(
            "SELECT p.*, n.name AS neighborhood_name
               FROM pharmacies p
               LEFT JOIN neighborhoods n USING (neighborhood_id)
              WHERE p.pharmacy_id = :id AND p.status = 'active'
              LIMIT 1",
            [':id' => $id]
        )->fetch();
        return $row ?: null;
    }

    /** Get full pharmacy row (any status) for owner dashboard. */
    public static function getById(int $id): ?array
    {
        $row = db_query(
            'SELECT p.*, n.name AS neighborhood_name
               FROM pharmacies p
               LEFT JOIN neighborhoods n USING (neighborhood_id)
              WHERE p.pharmacy_id = :id LIMIT 1',
            [':id' => $id]
        )->fetch();
        return $row ?: null;
    }

    /** Featured pharmacies for home page, ordered by stock size. */
    public static function getFeatured(int $limit = 3): array
    {
        return db_query(
            "SELECT p.pharmacy_id, p.pharmacy_name, p.phone,
                    p.operating_hours, p.logo,
                    n.name AS neighborhood_name,
                    COUNT(i.inventory_id)        AS medicine_count,
                    SUM(i.status = 'in_stock')   AS in_stock_count
               FROM pharmacies p
               LEFT JOIN neighborhoods n USING (neighborhood_id)
               LEFT JOIN inventory     i USING (pharmacy_id)
              WHERE p.status = 'active'
           GROUP BY p.pharmacy_id
           ORDER BY in_stock_count DESC, medicine_count DESC
              LIMIT :lim",
            [':lim' => $limit]
        )->fetchAll();
    }

    /** Get inventory for a pharmacy. */
    public static function getInventory(int $pharmacy_id, bool $include_out = true): array
    {
        $extra = $include_out ? '' : "AND i.status != 'out_of_stock'";
        return db_query(
            "SELECT i.inventory_id, i.quantity, i.price, i.status,
                    i.expiry_date, i.restock_note, i.updated_at,
                    m.medicine_name, m.generic_name, m.category
               FROM inventory i
               JOIN medicines m USING (medicine_id)
              WHERE i.pharmacy_id = :pid $extra
           ORDER BY i.status ASC, m.medicine_name ASC",
            [':pid' => $pharmacy_id]
        )->fetchAll();
    }

    /** Dashboard stats for the pharmacy owner panel. */
    public static function getStats(int $pharmacy_id): array
    {
        $inv = db_query(
            "SELECT COUNT(*) AS total_meds,
                    SUM(quantity > 0 AND quantity <= :thr) AS low_stock,
                    SUM(status = 'out_of_stock')           AS out_of_stock
               FROM inventory WHERE pharmacy_id = :pid",
            [':pid' => $pharmacy_id, ':thr' => LOW_STOCK_THRESHOLD]
        )->fetch();

        $views = (int) db_query(
            "SELECT COUNT(*) FROM search_logs
              WHERE search_date >= DATE_SUB(NOW(), INTERVAL 7 DAY)"
        )->fetchColumn();

        return [
            'total_meds'   => (int)($inv['total_meds']    ?? 0),
            'low_stock'    => (int)($inv['low_stock']     ?? 0),
            'out_of_stock' => (int)($inv['out_of_stock']  ?? 0),
            'search_views' => $views,
        ];
    }

    /** Update pharmacy profile (name, contact, hours, optional logo). */
    public static function updateProfile(int $pharmacy_id, array $data): void
    {
        $logoSQL = isset($data['logo']) ? ', logo = :logo' : '';
        $params  = [
            ':name'  => $data['pharmacy_name'],
            ':owner' => $data['owner_name'],
            ':phone' => $data['phone'],
            ':addr'  => $data['address'],
            ':nbhd'  => $data['neighborhood_id'],
            ':hours' => $data['operating_hours'] ?? null,
            ':id'    => $pharmacy_id,
        ];
        if (isset($data['logo'])) $params[':logo'] = $data['logo'];

        db_query(
            "UPDATE pharmacies
                SET pharmacy_name = :name, owner_name = :owner,
                    phone = :phone, address = :addr,
                    neighborhood_id = :nbhd, operating_hours = :hours $logoSQL
              WHERE pharmacy_id = :id",
            $params
        );
    }


    public static function updatePassword(int $pharmacy_id, string $newPassword): void
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        db_query(
            'UPDATE pharmacies SET password = :pass WHERE pharmacy_id = :id',
            [':pass' => $hash, ':id' => $pharmacy_id]
        );
    }
}
