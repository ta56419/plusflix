<?php
namespace App\Model;

use App\Service\Database;

class Favorite
{
    public ?int $id = null;
    public int $movie_id;
    public ?string $user_id = null;
    public ?string $created_at = null;

    public static function findByMovieAndUser(int $movieId, ?string $userId): ?Favorite
    {
        if (!$userId) {
            return null;
        }

        $db = new Database();
        $sql = "SELECT * FROM favorites WHERE movie_id = :movieId AND user_id = :userId LIMIT 1";
        $statement = $db->getPdo()->prepare($sql);
        $statement->execute([':movieId' => $movieId, ':userId' => $userId]);
        $data = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return self::fromArray($data);
    }

    public static function isFavorited(int $movieId, ?string $userId): bool
    {
        return self::findByMovieAndUser($movieId, $userId) !== null;
    }

    public static function getCountByMovie(int $movieId): int
    {
        $db = new Database();
        $sql = "SELECT COUNT(*) as count FROM favorites WHERE movie_id = :movieId";
        $statement = $db->getPdo()->prepare($sql);
        $statement->execute([':movieId' => $movieId]);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        return (int) $result['count'];
    }

    public function save(): bool
    {
        $db = new Database();

        if ($this->id === null) {
            $sql = "INSERT INTO favorites (movie_id, user_id) VALUES (:movieId, :userId)";
            $statement = $db->getPdo()->prepare($sql);
            $result = $statement->execute([
                ':movieId' => $this->movie_id,
                ':userId' => $this->user_id,
            ]);

            if ($result) {
                $this->id = $db->getPdo()->lastInsertId();
            }

            return $result;
        } else {
            $sql = "UPDATE favorites SET movie_id = :movieId, user_id = :userId WHERE id = :id";
            $statement = $db->getPdo()->prepare($sql);
            return $statement->execute([
                ':id' => $this->id,
                ':movieId' => $this->movie_id,
                ':userId' => $this->user_id,
            ]);
        }
    }

    public function delete(): bool
    {
        if ($this->id === null) {
            return false;
        }

        $db = new Database();
        $sql = "DELETE FROM favorites WHERE id = :id";
        $statement = $db->getPdo()->prepare($sql);
        return $statement->execute([':id' => $this->id]);
    }

    public static function deleteByMovieAndUser(int $movieId, ?string $userId): bool
    {
        if (!$userId) {
            return false;
        }

        $db = new Database();
        $sql = "DELETE FROM favorites WHERE movie_id = :movieId AND user_id = :userId";
        $statement = $db->getPdo()->prepare($sql);
        return $statement->execute([':movieId' => $movieId, ':userId' => $userId]);
    }

    public static function fromArray(array $data): self
    {
        $favorite = new self();
        $favorite->id = (int) $data['id'];
        $favorite->movie_id = (int) $data['movie_id'];
        $favorite->user_id = $data['user_id'] ?? null;
        $favorite->created_at = $data['created_at'] ?? null;

        return $favorite;
    }
}
