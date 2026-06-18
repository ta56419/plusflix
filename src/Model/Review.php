<?php
namespace App\Model;

use App\Service\Config;
use App\Service\Database;

class Review
{
    private ?int $id = null;
    private ?int $movieId = null;
    private ?int $rating = null;
    private ?string $comment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Review
    {
        $this->id = $id;

        return $this;
    }

    public function getMovieId(): ?int
    {
        return $this->movieId;
    }

    public function setMovieId(?int $movieId): Review
    {
        $this->movieId = $movieId;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): Review
    {
        $this->rating = $rating;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): Review
    {
        $this->comment = $comment;

        return $this;
    }

    public static function fromArray($array): Review
    {
        $review = new self();
        $review->fill($array);

        return $review;
    }

    public function fill($array): Review
    {
        if (isset($array['id']) && ! $this->getId()) {
            $this->setId($array['id']);
        }
        if (isset($array['movie_id'])) {
            $this->setMovieId($array['movie_id']);
        }
        if (isset($array['rating'])) {
            $this->setRating((int) $array['rating']);
        }
        if (isset($array['comment'])) {
            $this->setComment($array['comment']);
        }

        return $this;
    }

    public static function findAll(): array
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM reviews';
        $statement = $pdo->prepare($sql);
        $statement->execute();

        $reviews = [];
        $reviewsArray = $statement->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($reviewsArray as $reviewArray) {
            $reviews[] = self::fromArray($reviewArray);
        }

        return $reviews;
    }

    public static function find($id): ?Review
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM reviews WHERE id = :id';
        $statement = $pdo->prepare($sql);
        $statement->execute(['id' => $id]);

        $reviewArray = $statement->fetch(\PDO::FETCH_ASSOC);
        if (! $reviewArray) {
            return null;
        }
        $review = Review::fromArray($reviewArray);

        return $review;
    }

    public static function findByMovieId($movieId): array
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM reviews WHERE movie_id = :movie_id';
        $statement = $pdo->prepare($sql);
        $statement->execute(['movie_id' => $movieId]);

        $reviews = [];
        $reviewsArray = $statement->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($reviewsArray as $reviewArray) {
            $reviews[] = self::fromArray($reviewArray);
        }

        return $reviews;
    }

    public function save(): void
    {
        $pdo = Database::getPDO();
        if (! $this->getId()) {
            $sql = "INSERT INTO reviews (movie_id, rating, comment) VALUES (:movie_id, :rating, :comment)";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':movie_id' => $this->getMovieId(),
                ':rating' => $this->getRating(),
                ':comment' => $this->getComment(),
            ]);

            $this->setId((int) $pdo->lastInsertId());
        } else {
            $sql = "UPDATE reviews SET movie_id = :movie_id, rating = :rating, comment = :comment WHERE id = :id";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':movie_id' => $this->getMovieId(),
                ':rating' => $this->getRating(),
                ':comment' => $this->getComment(),
                ':id' => $this->getId(),
            ]);
        }
    }

    public function delete(): void
    {
        $pdo = Database::getPDO();
        $sql = "DELETE FROM reviews WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->execute([
            ':id' => $this->getId(),
        ]);

        $this->setId(null);
        $this->setMovieId(null);
        $this->setRating(null);
        $this->setComment(null);
    }
}
