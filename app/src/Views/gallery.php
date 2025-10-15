<div class="gallery-container">
    <div class="image-grid">
        <?php foreach ($images as $item): ?>
            <?php
            $image = $item['image'];
            $user = $item['user'];
            $likesCount = $item['likes_count'];
            $userHasLiked = $item['user_has_liked'];
            $comments = $item['comments'];
            ?>
            <div class="image-card">
                <div class="card-header">
                    <div class="user-info">
                        <span class="user"><?= htmlspecialchars($user->name) ?></span>
                        <span class="timestamp"><?= date('d-m-Y', strtotime($image->created_at)) ?></span>
                    </div>
                </div>

                <div class="image-wrapper">
                    <img src="/uploads/<?= $image->filename ?>"
                        alt="Image by <?= htmlspecialchars($user->name) ?>">
                </div>

                <div class="card-actions">
                    <?php if ($isLoggedIn): ?>
                        <form method="POST" action="/gallery/like/<?= $image->id ?>" class="like-form">
                            <button type="submit" class="btn-like <?= $userHasLiked ? 'liked' : '' ?>">
                                <span class="heart"><?= $userHasLiked ? '❤️' : '🤍' ?></span>
                                <span class="count"><?= $likesCount ?></span>
                            </button>
                        </form>
                    <?php else: ?>
                        <div class="like-display">
                            <span class="heart">🤍</span>
                            <span class="count"><?= $likesCount ?></span>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="comments-section">
                    <h3>Comments (<?= count($comments) ?>)</h3>
                    <?php if (!empty($comments)): ?>
                        <div class="comments-list">
                            <?php foreach ($comments as $commentData): ?>
                                <?php
                                $comment = $commentData['comment'];
                                $commentUser = $commentData['user'];
                                ?>
                                <div class="comment">
                                    <div class="comment-header">
                                        <span class="comment-user"><?= htmlspecialchars($commentUser->name) ?></span>
                                        <span class="comment-time"><?= date('M j, Y g:i A', strtotime($comment->created_at)) ?></span>
                                    </div>
                                    <p class="comment-text"><?= htmlspecialchars($comment->comment) ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($isLoggedIn): ?>
                        <form method="POST" action="/gallery/comment/<?= $image->id ?>" class="comment-form">
                            <textarea
                                name="comment"
                                placeholder="Add a comment..."
                                rows="2"
                                required></textarea>
                            <button type="submit" class="btn-comment">Post Comment</button>
                        </form>
                    <?php else: ?>
                        <p class="login-prompt">
                            <a href="/auth/login">Log in</a> to comment
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($currentPage > 1): ?>
                <a href="/gallery?page=<?= $currentPage - 1 ?>" class="page-link">← Previous</a>
            <?php endif; ?>

            <span class="page-info">Page <?= $currentPage ?> of <?= $totalPages ?></span>

            <?php if ($currentPage < $totalPages): ?>
                <a href="/gallery?page=<?= $currentPage + 1 ?>" class="page-link">Next →</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>