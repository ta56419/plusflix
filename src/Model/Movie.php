<?php
namespace App\Model;
use App\Service\Database;
class Movie
{
    private ?int $id = null;
    private ?string $title = null;
    private ?int $year = null;
    private ?string $director = null;
    private ?string $description = null;
    private ?float $duration = null;
    /**
     * @var Genre[] $genres
     */
    private array $genres = [];
    /**
     * @var Platform[] $availability
     */
    private array $availability = [];
    public function getId(): ?int
    {
        return $this->id;
    }
    public function setId(?int $id): Movie
    {
        $this->id = $id;
        return $this;
    }
    public function getTitle(): ?string
    {
        return $this->title;
    }
    public function setTitle(?string $title): Movie
    {
        $this->title = $title;
        return $this;
    }
    public function getYear(): ?int
    {
        return $this->year;
    }
    public function setYear(?int $year): Movie
    {
        $this->year = $year;
        return $this;
    }
    public function getDirector(): ?string
    {
        return $this->director;
    }
    public function setDirector(?string $director): Movie
    {
        $this->director = $director;
        return $this;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function setDescription(?string $description): Movie
    {
        $this->description = $description;
        return $this;
    }
    public function getDuration(): ?float
    {
        return $this->duration;
    }
    public function setDuration(?float $duration): Movie
    {
        $this->duration = $duration;
        return $this;
    }
    public static function fromArray($array): Movie
    {
        $movie = new self();
        $movie->fill($array);
        return $movie;
    }
    public function fill($array): Movie
    {
        if (isset($array['id']) && ! $this->getId()) {
            $this->setId($array['id']);
        }
        if (isset($array['title'])) {
            $this->setTitle($array['title']);
        }
        if (isset($array['year'])) {
            $this->setYear(\intval($array['year']));
        }
        if (isset($array['director'])) {
            $this->setDirector($array['director']);
        }
        if (isset($array['description'])) {
            $this->setDescription($array['description']);
        }
        if (isset($array['duration'])) {
            $this->setDuration(\floatval($array['duration']));
        }
        if (isset($array['genres'])) {
            $this->setGenres($array['genres']);
        } elseif (isset($array['genreIds'])) {
            $this->setGenres(Genre::findAllByIds($array['genreIds']));
        }
        if (isset($array['availability'])) {
            $this->setAvailability($array['availability']);
        } elseif (isset($array['availabilityIds'])) {
            $this->setAvailability(Platform::findAllByIds($array['availabilityIds']));
        }
        return $this;
    }
    public static function findAll(): array
    {
        return self::findByCriteria();
    }
    public static function findByCriteria(string $q = '', array $genreIds = [], array $platformIds = [], ?array $movieIds = null, int $limit = 10): array
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT DISTINCT m.* FROM movies m';
        $joins = [];
        $where = [];
        $params = [];
        if (!empty($genreIds)) {
            $joins[] = 'JOIN movie_genres mg ON m.id = mg.movie_id';
            $genrePlaceholders = [];
            foreach ($genreIds as $index => $genreId) {
                $placeholder = ':genreId_' . $index;
                $genrePlaceholders[] = $placeholder;
                $params[$placeholder] = $genreId;
            }
            $where[] = "mg.genre_id IN (" . implode(',', $genrePlaceholders) . ")";
        }
        if (!empty($platformIds)) {
            $joins[] = 'JOIN availability a ON m.id = a.movie_id';
            $platformPlaceholders = [];
            foreach ($platformIds as $index => $platformId) {
                $placeholder = ':platformId_' . $index;
                $platformPlaceholders[] = $placeholder;
                $params[$placeholder] = $platformId;
            }
            $where[] = "a.platform_id IN (" . implode(',', $platformPlaceholders) . ")";
        }
        if ($q) {
            $escapedQ = self::escapeLikeSpecialChars($q);
            $like = '%' . $escapedQ . '%';
            $where[] = '(m.title LIKE :search_title OR m.director LIKE :search_director OR m.description LIKE :search_description)';
            $params[':search_title'] = $like;
            $params[':search_director'] = $like;
            $params[':search_description'] = $like;
        }
        if ($movieIds !== null) {
            if (empty($movieIds)) {
                return [];
            }
            $placeholders = [];
            foreach ($movieIds as $index => $movieId) {
                $placeholder = ':movieId_' . $index;
                $placeholders[] = $placeholder;
                $params[$placeholder] = $movieId;
            }
            $where[] = "m.id IN (" . implode(',', $placeholders) . ")";
        }
        if (!empty($joins)) {
            $sql .= ' ' . implode(' ', $joins);
        }
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY m.title ASC';
        $sql .= ' LIMIT ' . (int)$limit;

        $statement = $pdo->prepare($sql);
        $statement->execute($params);
        $moviesArray = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (empty($moviesArray)) {
            return [];
        }
        return self::buildMoviesWithRelations($moviesArray);
    }
    public static function findTopByRating(int $limit = 3): array
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT m.*, AVG(r.rating) as avg_rating
                FROM movies m
                LEFT JOIN reviews r ON m.id = r.movie_id
                GROUP BY m.id
                ORDER BY avg_rating DESC, m.title ASC
                LIMIT :limit';
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':limit', $limit, \PDO::PARAM_INT);
        $statement->execute();
        $moviesArray = $statement->fetchAll(\PDO::FETCH_ASSOC);
        if (empty($moviesArray)) {
            return [];
        }
        return self::buildMoviesWithRelations($moviesArray);
    }
    private static function buildMoviesWithRelations(array $moviesArray): array
    {
        $movieIds = array_column($moviesArray, 'id');
        $genresByMovie = self::getGenresByMovie($movieIds);
        $platformsByMovie = self::getPlatformsByMovie($movieIds);
        $movies = [];
        foreach ($moviesArray as $movieArray) {
            $movieArray['genres'] = $genresByMovie[$movieArray['id']] ?? [];
            $movieArray['availability'] = $platformsByMovie[$movieArray['id']] ?? [];
            $movies[] = self::fromArray($movieArray);
        }
        return $movies;
    }
    private static function getGenresByMovie(array $movieIds): array
    {
        $pdo = Database::getPDO();
        $genresSql = 'SELECT mg.movie_id, g.id, g.name
                      FROM genres g
                      JOIN movie_genres mg ON g.id = mg.genre_id
                      WHERE mg.movie_id IN (' . implode(',', array_map('intval', $movieIds)) . ')';
        $genresStatement = $pdo->prepare($genresSql);
        $genresStatement->execute();
        $genresByMovie = [];
        foreach ($genresStatement->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            if (!isset($genresByMovie[$row['movie_id']])) {
                $genresByMovie[$row['movie_id']] = [];
            }
            $genresByMovie[$row['movie_id']][] = Genre::fromArray($row);
        }
        return $genresByMovie;
    }
    private static function getPlatformsByMovie(array $movieIds): array
    {
        $pdo = Database::getPDO();
        $platformsSql = 'SELECT a.movie_id, p.id, p.name, p.price
                         FROM platforms p
                         JOIN availability a ON p.id = a.platform_id
                         WHERE a.movie_id IN (' . implode(',', array_map('intval', $movieIds)) . ')';
        $platformsStatement = $pdo->prepare($platformsSql);
        $platformsStatement->execute();
        $platformsByMovie = [];
        foreach ($platformsStatement->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            if (!isset($platformsByMovie[$row['movie_id']])) {
                $platformsByMovie[$row['movie_id']] = [];
            }
            $platformsByMovie[$row['movie_id']][] = Platform::fromArray($row);
        }
        return $platformsByMovie;
    }
    public static function find($id): ?Movie
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT * FROM movies WHERE id = :id';
        $statement = $pdo->prepare($sql);
        $statement->execute([':id' => $id]);
        $movieArray = $statement->fetch(\PDO::FETCH_ASSOC);
        if (! $movieArray) {
            return null;
        }
        $movies = self::buildMoviesWithRelations([$movieArray]);
        if (empty($movies)) {
            return null;
        }
        return $movies[0];
    }
    private static function escapeLikeSpecialChars(string $term): string
    {
        return str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $term);
    }
    public function save(): void
    {
        $pdo = Database::getPDO();
        if (! $this->getId()) {
            $sql = "INSERT INTO movies (title, year, director, description, duration) VALUES (:title, :year, :director, :description, :duration)";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':title' => $this->getTitle(),
                ':year' => $this->getYear(),
                ':director' => $this->getDirector(),
                ':description' => $this->getDescription(),
                ':duration' => $this->getDuration(),
            ]);
            $this->setId($pdo->lastInsertId());
        } else {
            $sql = "UPDATE movies SET title = :title, year = :year, director = :director, description = :description, duration = :duration WHERE id = :id";
            $statement = $pdo->prepare($sql);
            $statement->execute([
                ':title' => $this->getTitle(),
                ':year' => $this->getYear(),
                ':director' => $this->getDirector(),
                ':description' => $this->getDescription(),
                ':duration' => $this->getDuration(),
                ':id' => $this->getId(),
            ]);
        }
        $this->saveGenres();
        $this->saveAvailability();
    }
    private function saveGenres(): void
    {
        $this->clearGenres();
        foreach ($this->getGenres() as $genre) {
            $this->addGenre($genre->getId());
        }
    }
    private function clearGenres(): void
    {
        $pdo = Database::getPDO();
        $sql = 'DELETE FROM movie_genres WHERE movie_id = :movie_id';
        $statement = $pdo->prepare($sql);
        $statement->execute([':movie_id' => $this->getId()]);
    }
    private function saveAvailability(): void
    {
        $this->clearAvailability();
        foreach ($this->getAvailability() as $platform) {
            $this->addAvailability($platform->getId());
        }
    }
    private function clearAvailability(): void
    {
        $pdo = Database::getPDO();
        $sql = 'DELETE FROM availability WHERE movie_id = :movie_id';
        $statement = $pdo->prepare($sql);
        $statement->execute([':movie_id' => $this->getId()]);
    }
    public function delete(): void
    {
        $pdo = Database::getPDO();
        $sql = "DELETE FROM movies WHERE id = :id";
        $statement = $pdo->prepare($sql);
        $statement->execute([
            ':id' => $this->getId(),
        ]);
        $this->setId(null);
        $this->setTitle(null);
        $this->setYear(null);
        $this->setDirector(null);
        $this->setDescription(null);
        $this->setDuration(null);
        $this->setGenres([]);
        $this->setAvailability([]);
    }
    public function getGenres(): array
    {
        return $this->genres;
    }
    public function setGenres(array $genres): Movie
    {
        $this->genres = $genres;
        return $this;
    }
    public function addGenre(int $genreId): void
    {
        $pdo = Database::getPDO();
        $sql = 'INSERT IGNORE INTO movie_genres (movie_id, genre_id) VALUES (:movie_id, :genre_id)';
        $statement = $pdo->prepare($sql);
        $statement->execute([':movie_id' => $this->getId(), ':genre_id' => $genreId]);
    }
    public function getAvailability(): array {
        return $this->availability;
    }
    public function setAvailability(array $platforms): Movie
    {
        $this->availability = $platforms;
        return $this;
    }
    public function addAvailability(int $platformId): void
    {
        $pdo = Database::getPDO();
        $sql = 'INSERT IGNORE INTO availability (movie_id, platform_id) VALUES (:movie_id, :platform_id)';
        $statement = $pdo->prepare($sql);
        $statement->execute([':movie_id' => $this->getId(), ':platform_id' => $platformId]);
    }
    public function addReview(Review $review): void
    {
        $review->setMovieId($this->getId());
        $review->save();
    }
    public function getReviews(): array
    {
        return Review::findByMovieId($this->getId());
    }
    public function getAverageRating(): ?float
    {
        $pdo = Database::getPDO();
        $sql = 'SELECT AVG(rating) as avg_rating FROM reviews WHERE movie_id = :movie_id';
        $statement = $pdo->prepare($sql);
        $statement->execute([':movie_id' => $this->getId()]);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        return $result['avg_rating'] ? (float) $result['avg_rating'] : null;
    }
}
