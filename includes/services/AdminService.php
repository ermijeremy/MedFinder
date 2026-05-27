<?php
require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/functions.php';

class AdminService
{
    /** Dashboard stat cards */
    public static function getDashboardStats(): array
    {
        $pharma = db_query(
            "SELECT
                COUNT(*)                        AS total,
                SUM(status = 'pending')         AS pending,
                SUM(status = 'active')          AS active,
                SUM(status = 'suspended')       AS suspended
               FROM pharmacies"
        )->fetch();

        $medicines = (int) db_query(
            'SELECT COUNT(*) FROM medicines WHERE is_active = 1'
        )->fetchColumn();

        $searches_today = (int) db_query(
            "SELECT COUNT(*) FROM search_logs WHERE DATE(search_date) = CURDATE()"
        )->fetchColumn();

        return [
            'total_pharmacies'  => (int)($pharma['total']     ?? 0),
            'pending'           => (int)($pharma['pending']   ?? 0),
            'active'            => (int)($pharma['active']    ?? 0),
            'suspended'         => (int)($pharma['suspended'] ?? 0),
            'total_medicines'   => $medicines,
            'searches_today'    => $searches_today,
        ];
    }

    // Get all pharmacies with optional status filter, paginated.
    public static function getPharmacies(
        string $status = '',
        string $search = '',
        int    $page   = 1
    ): array {
        $where  = [];
        $params = [];

        if (in_array($status, ['pending', 'active', 'suspended'], true)) {
            $where[]        = 'p.status = :st';
            $params[':st']  = $status;
        }
        if ($search !== '') {
            $where[]       = '(p.pharmacy_name LIKE :s1 OR p.owner_name LIKE :s2)';
            $params[':s1'] = '%' . $search . '%';
            $params[':s2'] = '%' . $search . '%';
        }

        $whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $total = (int) db_query(
            "SELECT COUNT(*) FROM pharmacies p $whereSQL", $params
        )->fetchColumn();

        $pag = paginate($total, ITEMS_PER_PAGE, $page);
        $params[':lim'] = $pag['per_page'];
        $params[':off'] = $pag['offset'];

        $rows = db_query(
            "SELECT p.pharmacy_id, p.pharmacy_name, p.owner_name,
                    p.email, p.phone, p.license_number,
                    p.status, p.created_at,
                    n.name AS neighborhood_name
               FROM pharmacies p
               LEFT JOIN neighborhoods n USING (neighborhood_id)
              $whereSQL
           ORDER BY p.created_at DESC
              LIMIT :lim OFFSET :off",
            $params
        )->fetchAll();

        return ['rows' => $rows, 'pagination' => $pag];
    }

    // Get a single pharmacy for admin review/view page.
    public static function getPharmacy(int $id): ?array
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

    /** Approve a pending pharmacy. */
    public static function approvePharmacy(int $id): void
    {
        db_query(
            "UPDATE pharmacies
                SET status = 'active', rejection_reason = NULL
              WHERE pharmacy_id = :id",
            [':id' => $id]
        );
    }

    /** Reject/suspend a pharmacy with an optional reason. */
    public static function rejectPharmacy(int $id, string $reason = ''): void
    {
        db_query(
            "UPDATE pharmacies
                SET status = 'suspended', rejection_reason = :reason
              WHERE pharmacy_id = :id",
            [':reason' => $reason ?: null, ':id' => $id]
        );
    }

    /** Toggle a pharmacy between active and suspended. */
    public static function togglePharmacyStatus(int $id): void
    {
        db_query(
            "UPDATE pharmacies
                SET status = IF(status = 'active', 'suspended', 'active')
              WHERE pharmacy_id = :id",
            [':id' => $id]
        );
    }

    /** Permanently delete a pharmacy */
    public static function deletePharmacy(int $id): void
    {
        db_query(
            'DELETE FROM pharmacies WHERE pharmacy_id = :id',
            [':id' => $id]
        );
    }

    /** Get pending pharmacies list for dashboard widget */
    public static function getPendingPharmacies(int $limit = 10): array
    {
        return db_query(
            "SELECT p.pharmacy_id, p.pharmacy_name, p.owner_name,
                    p.email, p.phone, p.created_at,
                    n.name AS neighborhood_name
               FROM pharmacies p
               LEFT JOIN neighborhoods n USING (neighborhood_id)
              WHERE p.status = 'pending'
           ORDER BY p.created_at ASC
              LIMIT :lim",
            [':lim' => $limit]
        )->fetchAll();
    }
}
