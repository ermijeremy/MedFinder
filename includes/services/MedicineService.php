<?php
require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/functions.php';

class MedicineService
{
    /** Get a paginated list of active medicines */
    public static function getAll(string $search = '', string $category = '', int $page = 1): array
    {
        $where  = ['m.is_active = 1'];
        $params = [];

        if ($search !== '') {
            $where[]          = '(m.medicine_name LIKE :s1 OR m.generic_name LIKE :s2)';
            $params[':s1']    = '%' . $search . '%';
            $params[':s2']    = '%' . $search . '%';
        }
        if ($category !== '') {
            $where[]          = 'm.category = :cat';
            $params[':cat']   = $category;
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);

        // Total count for pagination
        $total = (int) db_query(
            "SELECT COUNT(*) FROM medicines m $whereSQL",
            $params
        )->fetchColumn();

        $pag    = paginate($total, ITEMS_PER_PAGE, $page);
        $params[':limit']  = $pag['per_page'];
        $params[':offset'] = $pag['offset'];

        $rows = db_query(
            "SELECT m.medicine_id, m.medicine_name, m.generic_name,
                    m.category, m.description, m.created_at,
                    COUNT(i.inventory_id) AS pharmacy_count
               FROM medicines m
               LEFT JOIN inventory i USING (medicine_id)
              $whereSQL
           GROUP BY m.medicine_id
           ORDER BY m.medicine_name ASC
              LIMIT :limit OFFSET :offset",
            $params
        )->fetchAll();

        return ['rows' => $rows, 'pagination' => $pag];
    }

    /** Get all active medicines as a flat list for dropdown menus. */
    public static function getDropdownList(): array
    {
        return db_query(
            'SELECT medicine_id, medicine_name, generic_name, category
               FROM medicines
              WHERE is_active = 1
           ORDER BY medicine_name ASC'
        )->fetchAll();
    }

    /** Get active medicines not currently in the given pharmacy's inventory. */
    public static function getAvailableForPharmacy(int $pharmacy_id): array
    {
        return db_query(
            'SELECT medicine_id, medicine_name, generic_name, category
               FROM medicines
              WHERE is_active = 1
                AND medicine_id NOT IN (
                    SELECT medicine_id FROM inventory WHERE pharmacy_id = :pid
                )
           ORDER BY medicine_name ASC',
            [':pid' => $pharmacy_id]
        )->fetchAll();
    }

    /** Get a single medicine by ID. */
    public static function getById(int $id): ?array
    {
        $row = db_query(
            'SELECT * FROM medicines WHERE medicine_id = :id LIMIT 1',
            [':id' => $id]
        )->fetch();

        return $row ?: null;
    }

    /** Insert a new medicine */
    public static function add(array $data): int
    {
        db_query(
            'INSERT INTO medicines (medicine_name, generic_name, category, description)
             VALUES (:name, :generic, :category, :desc)',
            [
                ':name'     => $data['medicine_name'],
                ':generic'  => $data['generic_name']  ?? null,
                ':category' => $data['category']      ?? null,
                ':desc'     => $data['description']   ?? null,
            ]
        );
        return db_last_id();
    }

    /** Update medicine catalog details. */
    public static function update(int $id, array $data): void
    {
        db_query(
            'UPDATE medicines
                SET medicine_name = :name,
                    generic_name  = :generic,
                    category      = :category,
                    description   = :desc
              WHERE medicine_id = :id',
            [
                ':name'     => $data['medicine_name'],
                ':generic'  => $data['generic_name']  ?? null,
                ':category' => $data['category']      ?? null,
                ':desc'     => $data['description']   ?? null,
                ':id'       => $id,
            ]
        );
    }

    /** Soft-delete: set is_active = 0. The medicine still exists in inventory history */
    public static function delete(int $id): bool
    {
        return (bool)db_query(
            'UPDATE medicines SET is_active = 0 WHERE medicine_id = :id',
            [':id' => $id]
        );
    }

    /** Count how many pharmacies have this medicine in stock */
    public static function getInventoryCount(int $id): int
    {
        $res = db_query(
            'SELECT COUNT(*) as cnt FROM inventory WHERE medicine_id = :id',
            [':id' => $id]
        )->fetch();
        return (int)($res['cnt'] ?? 0);
    }

    // return up to 10 medicine names matching $query.
    public static function suggest(string $query): array
    {
        if (strlen($query) < 2) return [];

        return db_query(
            'SELECT medicine_id, medicine_name, generic_name
               FROM medicines
              WHERE is_active = 1
                AND (medicine_name LIKE :q1 OR generic_name LIKE :q2)
           ORDER BY medicine_name ASC
              LIMIT 10',
            [':q1' => '%' . $query . '%', ':q2' => '%' . $query . '%']
        )->fetchAll();
    }

    // Get all distinct categories for filter dropdowns.
    public static function getCategories(): array
    {
        return db_query(
            'SELECT DISTINCT category
               FROM medicines
              WHERE is_active = 1 AND category IS NOT NULL
           ORDER BY category ASC'
        )->fetchAll(PDO::FETCH_COLUMN);
    }
}
