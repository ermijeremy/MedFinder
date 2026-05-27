<?php
require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/functions.php';

class NeighborhoodService
{
    // Get every neighborhood ordered by name.
    public static function getAll(): array
    {
        return db_query(
            'SELECT neighborhood_id, name, description, zone
               FROM neighborhoods
           ORDER BY name ASC'
        )->fetchAll();
    }

    // Get a single neighborhood by ID.
    public static function getById(int $id): ?array
    {
        $row = db_query(
            'SELECT neighborhood_id, name, description, zone
               FROM neighborhoods
              WHERE neighborhood_id = :id
              LIMIT 1',
            [':id' => $id]
        )->fetch();

        return $row ?: null;
    }

    // Insert a new neighborhood.
    public static function add(array $data): int
    {
        db_query(
            'INSERT INTO neighborhoods (name, description, zone)
             VALUES (:name, :desc, :zone)',
            [
                ':name' => $data['name'],
                ':desc' => $data['description'] ?? null,
                ':zone' => $data['zone']        ?? null,
            ]
        );
        return db_last_id();
    }

    // Update an existing neighborhood.
    public static function update(int $id, array $data): void
    {
        db_query(
            'UPDATE neighborhoods
                SET name = :name, description = :desc, zone = :zone
              WHERE neighborhood_id = :id',
            [
                ':name' => $data['name'],
                ':desc' => $data['description'] ?? null,
                ':zone' => $data['zone']        ?? null,
                ':id'   => $id,
            ]
        );
    }

    // Delete a neighborhood.
    public static function delete(int $id): void
    {
        db_query(
            'DELETE FROM neighborhoods WHERE neighborhood_id = :id',
            [':id' => $id]
        );
    }

    // Count how many active pharmacies are in each neighborhood.
    public static function getWithCounts(): array
    {
        return db_query(
            'SELECT n.neighborhood_id, n.name, n.zone,
                    COUNT(p.pharmacy_id) AS pharmacy_count
               FROM neighborhoods n
               LEFT JOIN pharmacies p
                 ON p.neighborhood_id = n.neighborhood_id
                AND p.status = \'active\'
           GROUP BY n.neighborhood_id
           ORDER BY n.name ASC'
        )->fetchAll();
    }
}
