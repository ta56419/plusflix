CREATE table availability
(
    id INT NOT NULL AUTO_INCREMENT,
    movie_id INT NOT NULL,
    platform_id INT NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY unique_movie_platform (movie_id, platform_id),
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (platform_id) REFERENCES platforms(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
