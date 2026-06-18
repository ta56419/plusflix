<?php
namespace App\Model;

use App\Service\Config;
use App\Service\Database;

class Platform
{
    private ?int $id = null;
    private ?string $name = null;
    private ?float $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Platform
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Platform
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): Platform
    {
        $this->price = $price;

        return $this;
    }

    public static function fromArray($array): Platform
    {
        $platform = new self();
        $platform->fill($array);

        return $platform;
    }

    public function fill($array): Platform
    {
        if (isset($array['id']) && ! $this->getId()) {
            $this->setId($array['id']);
        }
        if (isset($array['name'])) {
            $this->setName($array['name']);
        }
        if (isset($array['price'])) {
            $this->setPrice($array['price']);
        }

        return $this;
    }

    public static function findAll(): array
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM platforms';
        $statement = $pdo->prepare($sql);
        $statement->execute();

        $platforms = [];
        $platformsArray = $statement->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($platformsArray as $platformArray) {
            $platforms[] = self::fromArray($platformArray);
        }

        return $platforms;
    }

    public static function find($id): ?Platform
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM platforms WHERE id = :id';
        $statement = $pdo->prepare($sql);
        $statement->execute([':id' => $id]);

        $platformArray = $statement->fetch(\PDO::FETCH_ASSOC);
        if (! $platformArray) {
            return null;
        }
        $platform = Platform::fromArray($platformArray);

        return $platform;
    }

    public static function findAllByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $pdo = Database::getPDO();
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM platforms WHERE id IN ($placeholders)";
        $statement = $pdo->prepare($sql);
        $statement->execute($ids);

        $platforms = [];
        $platformsArray = $statement->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($platformsArray as $platformArray) {
            $platforms[] = self::fromArray($platformArray);
        }

        return $platforms;
    }

    public static function findByMovieId(int $movieId): array
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT p.* FROM platforms p
                JOIN availability a ON p.id = a.platform_id
                WHERE a.movie_id = :movie_id';
        $statement = $pdo->prepare($sql);
        $statement->execute([':movie_id' => $movieId]);

        $platforms = [];
        $platformsArray = $statement->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($platformsArray as $platformArray) {
            $platforms[] = self::fromArray($platformArray);
        }

        return $platforms;
    }

    public function save(): void
    {
        $pdo = Database::getPDO();
        if (! $this->getId()) {
            $sql = "INSERT INTO platforms (name, price) VALUES (:name, :price)";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':name' => $this->getName(),
                ':price' => $this->getPrice(),
            ]);

            $this->setId((int) $pdo->lastInsertId());
        } else {
            $sql = "UPDATE platforms SET name = :name, price = :price WHERE id = :id";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':name' => $this->getName(),
                ':price' => $this->getPrice(),
                ':id' => $this->getId(),
            ]);
        }
    }

    public function delete(): void
    {
        $pdo = Database::getPDO();
        $sql = "DELETE FROM platforms WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->execute([
            ':id' => $this->getId(),
        ]);

        $this->setId(null);
        $this->setName(null);
        $this->setPrice(null);
    }
}
