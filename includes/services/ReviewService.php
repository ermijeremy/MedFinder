<?php
require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/functions.php';

class ReviewService
{
    public static function addReview(int $pharmacy_id, int $customer_id, int $rating, string $comment): bool
    {
        try {
            db_query(
                "INSERT INTO pharmacy_reviews (pharmacy_id, customer_id, rating, comment)
                 VALUES (:pid, :cid, :r, :c)
                 ON DUPLICATE KEY UPDATE rating = VALUES(rating), comment = VALUES(comment)",
                [
                    ':pid' => $pharmacy_id,
                    ':cid' => $customer_id,
                    ':r'   => $rating,
                    ':c'   => $comment
                ]
            );
            return true;
        } catch (PDOException $e) {
            error_log("ReviewService::addReview Error: " . $e->getMessage());
            return false;
        }
    }

    public static function getPharmacyReviews(int $pharmacy_id): array
    {
        return db_query(
            "SELECT r.*, c.first_name, c.last_name 
             FROM pharmacy_reviews r
             JOIN customers c ON r.customer_id = c.customer_id
             WHERE r.pharmacy_id = :pid
             ORDER BY r.created_at DESC",
            [':pid' => $pharmacy_id]
        )->fetchAll();
    }

    public static function getAverageRating(int $pharmacy_id): float
    {
        $rating = db_query(
            "SELECT AVG(rating) FROM pharmacy_reviews WHERE pharmacy_id = :pid",
            [':pid' => $pharmacy_id]
        )->fetchColumn();
        
        return $rating ? round((float)$rating, 1) : 0.0;
    }

    public static function getReviewCount(int $pharmacy_id): int
    {
        return (int) db_query(
            "SELECT COUNT(*) FROM pharmacy_reviews WHERE pharmacy_id = :pid",
            [':pid' => $pharmacy_id]
        )->fetchColumn();
    }
}
