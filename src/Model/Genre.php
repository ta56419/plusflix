<?php
namespace App\Model;

use App\Service\Config;
use App\Service\Database;

class Genre
{
    private ?int $id = null;
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Genre
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Genre
    {
        $this->name = $name;

        return $this;
    }

    public static function fromArray($array): Genre
    {
        $genre = new self();
        $genre->fill($array);

        return $genre;
    }

    public function fill($array): Genre
    {
        if (isset($array['id']) && ! $this->getId()) {
            $this->setId($array['id']);
        }
        if (isset($array['name'])) {
            $this->setName($array['name']);
        }

        return $this;
    }

    public static function findAll(): array
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM genres';
        $statement = $pdo->prepare($sql);
        $statement->execute();

        $genres = [];
        $genresArray = $statement->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($genresArray as $genreArray) {
            $genres[] = self::fromArray($genreArray);
        }

        return $genres;
    }

    public static function find($id): ?Genre
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM genres WHERE id = :id';
        $statement = $pdo->prepare($sql);
        $statement->execute(['id' => $id]);

        $genreArray = $statement->fetch(\PDO::FETCH_ASSOC);
        if (! $genreArray) {
            return null;
        }
        $genre = Genre::fromArray($genreArray);

        return $genre;
    }

    public static function findAllByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $pdo = Database::getPDO();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM genres WHERE id IN ($placeholders)";
        $statement = $pdo->prepare($sql);
        $statement->execute($ids);

        $genres = [];
        $genresArray = $statement->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($genresArray as $genreArray) {
            $genres[] = self::fromArray($genreArray);
        }

        return $genres;
    }

    /**
     * @return Genre[]
     */
    public static function findByMovieId(int $movieId): array
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT g.* FROM genres g
                JOIN movie_genres mg ON g.id = mg.genre_id
                WHERE mg.movie_id = :movie_id';
        $statement = $pdo->prepare($sql);
        $statement->execute(['movie_id' => $movieId]);
        $genres = [];
        foreach ($statement->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $genres[] = Genre::fromArray($row);
        }

        return $genres;
    }

    public function save(): void
    {
        $pdo = Database::getPDO();
        if (! $this->getId()) {
            $sql = "INSERT INTO genres (name) VALUES (:name)";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':name' => $this->getName(),
            ]);

            $this->setId((int) $pdo->lastInsertId());
        } else {
            $sql = "UPDATE genres SET name = :name WHERE id = :id";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':name' => $this->getName(),
                ':id' => $this->getId(),
            ]);
        }
    }

    public function delete(): void
    {
        $pdo = Database::getPDO();
        $sql = "DELETE FROM genres WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->execute([
            ':id' => $this->getId(),
        ]);

        $this->setId(null);
        $this->setName(null);
    }

    public function getMovies(): array
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT m.* FROM movies m
                JOIN movie_genres mg ON m.id = mg.movie_id
                WHERE mg.genre_id = :genre_id';
        $statement = $pdo->prepare($sql);
        $statement->execute(['genre_id' => $this->getId()]);

        $movies = [];
        foreach ($statement->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $movies[] = Movie::fromArray($row);
        }
        return $movies;
    }
}
