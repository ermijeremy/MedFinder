<?php
require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/functions.php';

class NotificationService
{
    public static function notify(int $customer_id, string $title, string $message, string $type = 'general'): bool
    {
        // For simulation, we log to database. In production, we'd also call mail()
        error_log("NOTIFICATION [To: $customer_id]: $title - $message");

        try {
            db_query(
                "INSERT INTO notifications (customer_id, title, message, type)
                 VALUES (:cid, :t, :m, :type)",
                [
                    ':cid'  => $customer_id,
                    ':t'    => $title,
                    ':m'    => $message,
                    ':type' => $type
                ]
            );
            return true;
        } catch (PDOException $e) {
            error_log("NotificationService::notify Error: " . $e->getMessage());
            return false;
        }
    }

    public static function getUnread(int $customer_id): array
    {
        return db_query(
            "SELECT * FROM notifications WHERE customer_id = :cid AND is_read = 0 ORDER BY created_at DESC",
            [':cid' => $customer_id]
        )->fetchAll();
    }

    public static function markAsRead(int $notification_id): void
    {
        db_query(
            "UPDATE notifications SET is_read = 1 WHERE notification_id = :id",
            [':id' => $notification_id]
        );
    }
}
