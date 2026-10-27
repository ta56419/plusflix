# PlusFlix

A movie catalog web app where you can browse films, filter by genre or streaming platform, leave reviews with ratings, save favorites, and manage everything through an admin panel. Built on a custom PHP MVC framework with MySQL and Docker.

This is a university project with a small MVC framework. It covers routing, templates, database access and the front controller pattern.

---

## What it does

- **Browse movies** — list view with search by title, director, or description
- **Filter** — narrow results by genre and/or streaming platform
- **Movie details** — full info page with description, duration, director, genres, platforms
- **Reviews** — anyone can leave a rating (1-10) and a comment on a movie
- **Top rated** — homepage shows top 3 movies by average rating
- **Favorites** — add/remove movies to a personal favorites list (session-based, with AJAX toggle)
- **Admin panel** — CRUD for movies, genres and platforms, with a demo role cookie
- **Theme switching** — toggle between dark/light/high-contrast modes
- **Responsive LESS styles** — compiled from `.less` source files

---

## Tech stack

- **PHP 8** with a custom MVC framework
- **MySQL 8.4** (Docker)
- **Docker Compose** (MySQL + Adminer)
- **LESS CSS** (precompiled to minified CSS)
- **ActiveRecord ORM** (custom implementation)
- **Front Controller pattern** (single entry point `index.php`)
- **MVC architecture** (Controllers, Models, Templates)
- **PDO** with prepared statements for all DB queries

---

## Project structure

```
plusflix/
├── config/
│   └── config.dist.php          # DB config template (copy to config.php)
├── public/
│   ├── index.php                # Front controller — all requests go through here
│   └── assets/
│       ├── src/less/            # LESS source files
│       └── dist/                # Compiled CSS
├── sql/                         # Database migrations (run in order)
│   ├── 01-movies.sql
│   ├── 02-reviews.sql
│   ├── 03-genres.sql
│   ├── 04-movie_genres.sql
│   ├── 05-platforms.sql
│   ├── 06-availability.sql
│   └── 07-favorites.sql
├── src/
│   ├── Controller/              # Request handlers
│   │   ├── MovieController.php  # CRUD + search + reviews + favorites
│   │   ├── GenreController.php
│   │   ├── PlatformController.php
│   │   ├── HomeController.php   # Top rated movies
│   │   ├── AdminController.php
│   │   ├── AdminLogInController.php
│   │   ├── ReviewController.php
│   │   └── ToggleStyle.php      # Theme switcher
│   ├── Model/                   # ActiveRecord models
│   │   ├── Movie.php            # Search, filter, relations, avg rating
│   │   ├── Genre.php
│   │   ├── Platform.php
│   │   ├── Review.php
│   │   └── Favorite.php
│   ├── Service/                 # Framework internals
│   │   ├── Config.php
│   │   ├── Database.php         # PDO singleton
│   │   ├── Router.php
│   │   └── Templating.php
│   └── Exception/
├── templates/                   # PHP/HTML views
│   ├── base.html.php            # Main layout
│   ├── nav.html.php
│   ├── movie/                   # Movie views (list, show, create, edit, favorites)
│   ├── genre/
│   ├── platform/
│   ├── admin/
│   └── home/
├── docker-compose.yml           # MySQL 8.4 + Adminer
├── autoload.php                 # PSR-4 style class autoloading
└── LICENSE
```

---

## How to run

### 1. Start the database

```bash
docker compose up -d
```

MySQL runs on port 3306. Adminer, the database browser, is available at `http://localhost:8085`.

### 2. Set up the config

```bash
cp config/config.dist.php config/config.php
```

Edit `config/config.php` and set your DB credentials:

```php
$config['db_dsn'] = 'mysql:host=localhost;port=3306;dbname=plusflix;charset=utf8mb4';
$config['db_user'] = 'plusflix';
$config['db_pass'] = 'plusflixpass';
```

### 3. Run the SQL migrations

Open Adminer at `http://localhost:8085`, log in (`plusflix` / `plusflixpass`), select the `plusflix` database, and run each SQL file from the `sql/` folder in order (01 through 07).

### 4. Compile styles (optional, already compiled)

```bash
lessc public/assets/src/less/style.less public/assets/dist/style.min.css --clean-css --source-map
```

### 5. Start the PHP dev server

```bash
php -S localhost:8000 -t public/
```

Open `http://localhost:8000` in your browser.

Admin panel: `http://localhost:8000/index.php?action=admin-index`

The admin login is a local demo. The access code is checked in
`templates/admin/login.html.php`, which sets a role cookie. Users are not
authenticated on the server.

---

## Database schema

7 tables with foreign keys and cascading deletes:

- **movies** — id, title, year, director, description, duration
- **genres** — id, name
- **movie_genres** — many-to-many link (movie + genre, unique constraint)
- **platforms** — id, name, price
- **availability** — many-to-many link (movie + platform, unique constraint)
- **reviews** — id, movie_id (FK), rating, comment
- **favorites** — id, movie_id (FK), user_id, created_at

---

## Topics covered

- How MVC frameworks work internally (routing, front controller, templating engine)
- ActiveRecord pattern for ORM without Doctrine/Eloquent
- Writing parameterized SQL queries with PDO (no raw string concatenation)
- Docker Compose for local dev database setup
- LESS preprocessing and CSS architecture
- Building a search with multi-criteria filtering (genre + platform + text)

---

**Author:** Andrii Tynskyi
