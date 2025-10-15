<div class="image-grid">
    <?php foreach ($images as $image): ?>
        <div class="image-card">
            <div class="image-header">
                <h4><?= htmlspecialchars($image->getAuthor()->name) ?></h4>
                <p class="date"><?= date('d-m-Y', strtotime($image->created_at)) ?></p>
            </div>
            <img src="/uploads/images/full/<?= $image->filename ?>" alt="User uploaded image">
            <div class="image-actions">
                <!-- <button class="like-button">❤️ Like</button>
                <button class="comments">💬 Comment</button> -->
            </div>
            <form action="">
                <input type="text" placeholder="Add a comment..." />
                <button type="submit">Post</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>