<div class="image-grid">
    <?php foreach ($images as $image): ?>
        <div class="image-card">
            <img src="<?= $image->image_path ?>" alt="User uploaded image">
        </div>
    <?php endforeach; ?>
</div>