<?php
/** @var \App\Model\Movie $movie */
/** @var \App\Service\Router $router */
$title = $movie->getTitle();
$bodyClass = 'show';
ob_start();
?>

<div class="movie-detail-container">
    <div class="movie-hero">
        <!-- banner placeholder -->
        <div class="movie-banner">
            <div class="movie-banner-text">BANNER<br>FILMU</div>
        </div>

        <div class="movie-info-main">
            <div>
                <div class="movie-header-top">
                    <div class="movie-title-section">
                        <h1 class="movie-title"><?= htmlspecialchars($movie->getTitle(), ENT_QUOTES, 'UTF-8') ?></h1>
                    </div>
                     <button id="favoriteBtn" class="add-favorite-btn <?= in_array($movie->getId(), $_SESSION['favorites'] ?? [], true) ? 'filled' : '' ?>" data-movie-id="<?= $movie->getId() ?>" data-is-favorite="<?= in_array($movie->getId(), $_SESSION['favorites'] ?? [], true) ? 'true' : 'false' ?>">
                            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path>
                            </svg>
                        </button>
                </div>

                <div class="movie-meta-grid">
                    <div class="meta-item">
                        <span class="meta-label">Rok</span>
                        <span class="meta-value"><?= $movie->getYear() ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Reżyser</span>
                        <span class="meta-value"><?= htmlspecialchars($movie->getDirector(), ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Czas trwania</span>
                        <span class="meta-value"><?= $movie->getDuration() ?> min</span>
                    </div>
                </div>
            </div>

            <div>
                 <?php if ($movie->getAverageRating()): ?>
                        <div class="movie-rating-badge">
                            ⭐ <?= number_format($movie->getAverageRating(), 1) ?>
                        </div>
                    <?php endif; ?>
                <button id="openReviewModal" class="add-review-btn">
                    Dodaj recenzję
                </button>
            </div>
        </div>
    </div>

    <div class="description-card">
        <span class="section-label">Opis</span>
        <p class="description-text"><?= htmlspecialchars($movie->getDescription(), ENT_QUOTES, 'UTF-8') ?></p>
    </div>

    <div class="genres-section">
        <span class="section-label">Gatunki</span>
        <div class="genre-tags">
            <?php foreach ($movie->getGenres() as $genre): ?>
                <span class="genre-tag"><?= htmlspecialchars($genre->getName(), ENT_QUOTES, 'UTF-8') ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="platforms-section">
        <div class="section-header">
            <h3 class="section-title">Dostępne na platformach</h3>
        </div>
        <div class="platforms-grid">
            <?php foreach ($movie->getAvailability() as $platform):
                $clean_name = preg_replace('/[^a-z0-9]/', '', strtolower($platform->getName()));
                $display_text = ($clean_name === 'disney') ? 'D+' : strtoupper(substr($clean_name, 0, 1));
                $url = match($clean_name) {
                    'hbo' => 'https://www.hbomax.com/pl/pl',
                    'skyshowtime' => 'https://www.skyshowtime.com/pl',
                    'prime' => 'https://www.primevideo.com/',
                    'netflix' => 'https://www.netflix.com/pl/',
                    'disney' => 'https://www.disneyplus.com/',
                    default => '#',
                };
            ?>
                <a href="<?= $url ?>"
                   class="platform-badge <?= htmlspecialchars($clean_name, ENT_QUOTES, 'UTF-8') ?>"
                   title="<?= htmlspecialchars($platform->getName(), ENT_QUOTES, 'UTF-8') ?>"
                   target="_blank">
                    <span class="platform-logo"><?= htmlspecialchars($display_text, ENT_QUOTES, 'UTF-8') ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Reviews -->
    <div class="reviews-section">
        <div class="section-header">
            <h3 class="section-title">Recenzje użytkowników</h3>
        </div>

        <?php if (count($movie->getReviews()) > 0): ?>
            <div class="reviews-container">
                <?php foreach ($movie->getReviews() as $review): ?>
                    <div class="review-card">
                        <div class="review-stars">
                            <?php for ($i = 0; $i < $review->getRating(); $i++): ?>
                                <span class="star-filled">★</span>
                            <?php endfor; ?>
                            <?php for ($i = $review->getRating(); $i < 5; $i++): ?>
                                <span class="star-empty">★</span>
                            <?php endfor; ?>
                        </div>
                        <?php if ($review->getComment()): ?>
                            <p class="review-comment"><?= htmlspecialchars($review->getComment(), ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-reviews">
                Brak recenzji. Bądź pierwszy, który oceni ten film!
            </div>
        <?php endif; ?>
    </div>

    <!-- Review Modal -->
    <div id="reviewModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.7); z-index:1000; justify-content:center; align-items:center;">
        <div style="background:#1a1a2e; border:1px solid #2a2a3e; border-radius:16px; padding:2rem; max-width:500px; width:90%; box-shadow:0 10px 40px rgba(0,0,0,0.5);">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:1.5rem;">
                <h3 style="color:#ffffff; margin:0; font-size:1.5rem;">Dodaj recenzję</h3>
                <button id="closeReviewModal" type="button" style="background:none; border:none; color:#a0a0b0; font-size:1.5rem; cursor:pointer;" onmouseover="this.style.color='#ffffff'" onmouseout="this.style.color='#a0a0b0';">×</button>
            </div>

            <form id="reviewForm" method="POST" action="<?= $router->generatePath('movie-addReview', ['id' => $movie->getId()]) ?>" style="display:none;">
                <input type="hidden" name="action" value="movie-addReview">
                <input type="hidden" name="movie_id" value="<?= $movie->getId() ?>">
                <input type="hidden" name="rating" id="formRating" value="">
                <input type="hidden" name="comment" id="formComment" value="">
            </form>

            <div style="margin-bottom:1.5rem;">
                <label style="display:block; color:#ffffff; font-weight:500; margin-bottom:0.75rem;">Twoja ocena</label>
                <div id="starsContainer" style="display:flex; gap:0.75rem; font-size:2.5rem; cursor:pointer;">
                    <span class="star" data-rating="1" style="color:#4b5563; transition:color 0.2s ease; cursor:pointer;">★</span>
                    <span class="star" data-rating="2" style="color:#4b5563; transition:color 0.2s ease; cursor:pointer;">★</span>
                    <span class="star" data-rating="3" style="color:#4b5563; transition:color 0.2s ease; cursor:pointer;">★</span>
                    <span class="star" data-rating="4" style="color:#4b5563; transition:color 0.2s ease; cursor:pointer;">★</span>
                    <span class="star" data-rating="5" style="color:#4b5563; transition:color 0.2s ease; cursor:pointer;">★</span>
                </div>
                <input type="hidden" id="selectedRating" value="0">
            </div>

            <div style="margin-bottom:1.5rem;">
                <label style="display:block; color:#ffffff; font-weight:500; margin-bottom:0.75rem;">Komentarz (opcjonalne)</label>
                <textarea id="reviewComment" style="width:100%; height:120px; padding:0.75rem; border:1px solid #2a2a3e; border-radius:8px; background:#16162a; color:#ffffff; font-family:'Poppins', sans-serif; font-size:0.95rem; resize:vertical; box-sizing:border-box;" placeholder="Podziel się swoją opinią..."></textarea>
            </div>

            <div style="display:flex; gap:1rem;">
                <button id="submitReview" type="button" style="flex:1; background:#6366f1; color:#ffffff; border:none; padding:0.75rem 1.5rem; border-radius:8px; cursor:pointer; font-weight:500; font-family:'Poppins', sans-serif; font-size:0.95rem; transition:background 0.3s ease;" onmouseover="this.style.background='#4f46e5'" onmouseout="this.style.background='#6366f1';">
                    Opublikuj
                </button>
                <button id="cancelReview" type="button" style="flex:1; background:#2a2a3e; color:#a0a0b0; border:none; padding:0.75rem 1.5rem; border-radius:8px; cursor:pointer; font-weight:500; font-family:'Poppins', sans-serif; font-size:0.95rem; transition:all 0.3s ease;" onmouseover="this.style.background='#3a3a4e'; this.style.color='#ffffff'" onmouseout="this.style.background='#2a2a3e'; this.style.color='#a0a0b0';">
                    Anuluj
                </button>
            </div>
        </div>
    </div>

    <div class="navigation-buttons">
        <a href="<?= $router->generatePath('movie-index') ?>" class="nav-link">
            <span>←</span>
            <span>Powrót do listy filmów</span>
        </a>
        <a href="<?= $router->generatePath('') ?>" class="nav-link">
            <span>←</span>
            <span>Strona główna</span>
        </a>
    </div>
</div>

<script>
window.addEventListener('load', function() {
    document.body.classList.add('loaded');
});
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('reviewModal');
    const openBtn = document.getElementById('openReviewModal');
    const closeBtn = document.getElementById('closeReviewModal');
    const cancelBtn = document.getElementById('cancelReview');
    const submitBtn = document.getElementById('submitReview');
    const starsContainer = document.getElementById('starsContainer');
    const stars = document.querySelectorAll('.star');
    const selectedRatingInput = document.getElementById('selectedRating');
    const reviewForm = document.getElementById('reviewForm');
    const formRating = document.getElementById('formRating');
    const formComment = document.getElementById('formComment');
    const commentInput = document.getElementById('reviewComment');
    let selectedRating = 0;

    openBtn.addEventListener('click', function() {
        modal.style.display = 'flex';
    });

    closeBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        resetForm();
    });

    cancelBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        resetForm();
    });

    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            resetForm();
        }
    });

    stars.forEach(star => {
        star.addEventListener('mouseover', function() {
            const rating = parseInt(this.dataset.rating);
            updateStars(rating);
        });

        star.addEventListener('click', function() {
            selectedRating = parseInt(this.dataset.rating);
            selectedRatingInput.value = selectedRating;
            updateStars(selectedRating);
        });
    });

    starsContainer.addEventListener('mouseleave', function() {
        updateStars(selectedRating);
    });

    function updateStars(rating) {
        stars.forEach(star => {
            const starRating = parseInt(star.dataset.rating);
            if (starRating <= rating) {
                star.style.color = '#fbbf24';
            } else {
                star.style.color = '#4b5563';
            }
        });
    }

    submitBtn.addEventListener('click', function() {
        if (selectedRating === 0) {
            alert('Proszę wybrać ocenę');
            return;
        }

        formRating.value = selectedRating;
        formComment.value = commentInput.value.trim();
        reviewForm.submit();
    });

    function resetForm() {
        selectedRating = 0;
        selectedRatingInput.value = 0;
        commentInput.value = '';
        updateStars(0);
    }
    const favoriteBtn = document.getElementById('favoriteBtn');
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const movieId = this.dataset.movieId;
            const isFavorite = this.dataset.isFavorite === 'true';
            fetch(`/index.php?action=movie-toggleFavorite&id=${movieId}&ajax=1`, {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.dataset.isFavorite = data.isFavorite ? 'true' : 'false';
                    this.classList.toggle('filled', data.isFavorite);
                    const svg = this.querySelector('svg');
                    svg.setAttribute('fill', data.isFavorite ? 'currentColor' : 'none');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }
});
</script>

<?php $main = ob_get_clean();
include __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'base.html.php';
