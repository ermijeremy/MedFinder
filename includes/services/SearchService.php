<?php
require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/functions.php';

class SearchService
{
    // Search for pharmacies that stock a given medicine.
    public static function search(
        string $query,
        int    $neighborhood_id = 0,
        string $status          = '',
        string $sort            = 'updated',
        int    $page            = 1
    ): array {
        $where  = [
            "p.status = 'active'",
            "(m.medicine_name LIKE :q1 OR m.generic_name LIKE :q2)",
        ];
        $params = [':q1' => '%' . $query . '%', ':q2' => '%' . $query . '%'];

        if ($neighborhood_id > 0) {
            $where[]     = 'p.neighborhood_id = :nbhd';
            $params[':nbhd'] = $neighborhood_id;
        }

        if (in_array($status, ['in_stock', 'limited', 'out_of_stock'], true)) {
            $where[]        = 'i.status = :status';
            $params[':status'] = $status;
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);

        $orderSQL = match ($sort) {
            'price_asc'  => 'i.price ASC',
            'price_desc' => 'i.price DESC',
            default       => 'i.updated_at DESC',
        };

        // Total count
        $total = (int) db_query(
            "SELECT COUNT(*)
               FROM inventory i
               JOIN medicines  m USING (medicine_id)
               JOIN pharmacies p USING (pharmacy_id)
              $whereSQL",
            $params
        )->fetchColumn();

        $pag = paginate($total, ITEMS_PER_PAGE, $page);
        $params[':limit']  = $pag['per_page'];
        $params[':offset'] = $pag['offset'];

        $rows = db_query(
            "SELECT p.pharmacy_id,
                    p.pharmacy_name,
                    p.phone,
                    p.address,
                    p.operating_hours,
                    p.logo,
                    n.name           AS neighborhood_name,
                    m.medicine_name,
                    m.generic_name,
                    i.price,
                    i.status         AS stock_status,
                    i.restock_note,
                    i.updated_at
               FROM inventory i
               JOIN medicines  m USING (medicine_id)
               JOIN pharmacies p USING (pharmacy_id)
               LEFT JOIN neighborhoods n ON n.neighborhood_id = p.neighborhood_id
              $whereSQL
           ORDER BY $orderSQL
              LIMIT :limit OFFSET :offset",
            $params
        )->fetchAll();

        // Log the search
        self::log($query, $neighborhood_id ?: null, $status, $total);

        return [
            'rows'       => $rows,
            'pagination' => $pag,
            'query_info' => [
                'query'           => $query,
                'neighborhood_id' => $neighborhood_id,
                'status'          => $status,
                'sort'            => $sort,
            ],
        ];
    }

    // Get the most-searched terms over the last 30 days.
    // Used by the admin dashboard chart.
    public static function getTopSearches(int $limit = 10): array
    {
        return db_query(
            'SELECT search_query, COUNT(*) AS cnt
               FROM search_logs
              WHERE search_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
           GROUP BY search_query
           ORDER BY cnt DESC
              LIMIT :lim',
            [':lim' => $limit]
        )->fetchAll();
    }

    // Count today's total searches. Used for admin stats card.
    public static function countToday(): int
    {
        return (int) db_query(
            "SELECT COUNT(*) FROM search_logs
              WHERE DATE(search_date) = CURDATE()"
        )->fetchColumn();
    }

    // Record a search event in search_logs.
    private static function log(
        string $query,
        ?int   $neighborhood_id,
        string $status_filter,
        int    $results_count
    ): void {
        $ip      = $_SERVER['REMOTE_ADDR'] ?? '';
        $ipHash  = $ip ? hash('sha256', $ip) : null;

        db_query(
            'INSERT INTO search_logs
                (search_query, neighborhood_id, status_filter, results_count, ip_hash)
             VALUES
                (:q, :nbhd, :sf, :rc, :ip)',
            [
                ':q'    => substr($query, 0, 200),
                ':nbhd' => $neighborhood_id,
                ':sf'   => $status_filter ?: null,
                ':rc'   => $results_count,
                ':ip'   => $ipHash,
            ]
        );
    }
}
