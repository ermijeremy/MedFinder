<?php
require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/functions.php';

class InventoryService
{
    // Paginated inventory list for the pharmacy owner panel.
    public static function getList(
        int    $pharmacy_id,
        string $search = '',
        string $status = '',
        int    $page   = 1
    ): array {
        $where  = ['i.pharmacy_id = :pid'];
        $params = [':pid' => $pharmacy_id];

        if ($search !== '') {
            $where[]       = '(m.medicine_name LIKE :s1 OR m.generic_name LIKE :s2)';
            $params[':s1'] = '%' . $search . '%';
            $params[':s2'] = '%' . $search . '%';
        }
        if (in_array($status, ['in_stock', 'limited', 'out_of_stock'], true)) {
            $where[]         = 'i.status = :st';
            $params[':st']   = $status;
        }

        $whereSQL = 'WHERE ' . implode(' AND ', $where);

        $total = (int) db_query(
            "SELECT COUNT(*) FROM inventory i
               JOIN medicines m USING (medicine_id) $whereSQL",
            $params
        )->fetchColumn();

        $pag = paginate($total, ITEMS_PER_PAGE, $page);
        $params[':lim'] = $pag['per_page'];
        $params[':off'] = $pag['offset'];

        $rows = db_query(
            "SELECT i.inventory_id, i.quantity, i.price, i.status,
                    i.expiry_date, i.restock_note, i.notes, i.updated_at,
                    m.medicine_id, m.medicine_name, m.generic_name, m.category
               FROM inventory i
               JOIN medicines m USING (medicine_id)
              $whereSQL
           ORDER BY i.status ASC, m.medicine_name ASC
              LIMIT :lim OFFSET :off",
            $params
        )->fetchAll();

        return ['rows' => $rows, 'pagination' => $pag];
    }

    // Get a single inventory row — verifying it belongs to the given pharmacy.
    public static function getOne(int $inventory_id, int $pharmacy_id): ?array
    {
        $row = db_query(
            'SELECT i.*, m.medicine_name, m.generic_name, m.category
               FROM inventory i
               JOIN medicines m USING (medicine_id)
              WHERE i.inventory_id = :iid AND i.pharmacy_id = :pid
              LIMIT 1',
            [':iid' => $inventory_id, ':pid' => $pharmacy_id]
        )->fetch();

        return $row ?: null;
    }

    // Add a new inventory item.
    public static function add(int $pharmacy_id, array $data): int
    {
        db_query(
            'INSERT INTO inventory
                (pharmacy_id, medicine_id, quantity, price, status,
                 expiry_date, restock_note, notes)
             VALUES
                (:pid, :mid, :qty, :price, :status,
                 :exp, :note, :notes)
             ON DUPLICATE KEY UPDATE
                quantity     = VALUES(quantity),
                price        = VALUES(price),
                status       = VALUES(status),
                expiry_date  = VALUES(expiry_date),
                restock_note = VALUES(restock_note),
                notes        = VALUES(notes)',
            [
                ':pid'    => $pharmacy_id,
                ':mid'    => (int)$data['medicine_id'],
                ':qty'    => (int)$data['quantity'],
                ':price'  => (float)$data['price'],
                ':status' => $data['status'],
                ':exp'    => $data['expiry_date']  ?: null,
                ':note'   => $data['restock_note'] ?? null,
                ':notes'  => $data['notes']        ?? null,
            ]
        );
        return db_last_id();
    }

    // Update an existing inventory row (ownership-checked).
    public static function update(int $inventory_id, int $pharmacy_id, array $data): void
    {
        db_query(
            'UPDATE inventory
                SET quantity     = :qty,
                    price        = :price,
                    status       = :status,
                    expiry_date  = :exp,
                    restock_note = :note,
                    notes        = :notes
              WHERE inventory_id = :iid
                AND pharmacy_id  = :pid',
            [
                ':qty'    => (int)$data['quantity'],
                ':price'  => (float)$data['price'],
                ':status' => $data['status'],
                ':exp'    => $data['expiry_date']  ?: null,
                ':note'   => $data['restock_note'] ?? null,
                ':notes'  => $data['notes']        ?? null,
                ':iid'    => $inventory_id,
                ':pid'    => $pharmacy_id,
            ]
        );
    }

    // Delete an inventory row (ownership-checked).
    public static function delete(int $inventory_id, int $pharmacy_id): void
    {
        db_query(
            'DELETE FROM inventory
              WHERE inventory_id = :iid AND pharmacy_id = :pid',
            [':iid' => $inventory_id, ':pid' => $pharmacy_id]
        );
    }

    // Recent 10 inventory updates for the pharmacy dashboard widget.
    public static function getRecent(int $pharmacy_id): array
    {
        return db_query(
            'SELECT i.inventory_id, i.quantity, i.status, i.updated_at,
                    m.medicine_name
               FROM inventory i
               JOIN medicines m USING (medicine_id)
              WHERE i.pharmacy_id = :pid
           ORDER BY i.updated_at DESC
              LIMIT 10',
            [':pid' => $pharmacy_id]
        )->fetchAll();
    }
}
