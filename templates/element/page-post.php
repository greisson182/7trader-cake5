<section class="content-section dark">
    <div class="container">
        <h1 class="section-title neon"><?= h($PostsPage->title ?? 'Post') ?></h1>
        <?php if (!empty($PostsPage->summary)) : ?>
            <p class="section-subtitle"><?= $PostsPage->summary ?></p>
        <?php endif; ?>

        <div class="modern-card">
            <?= $PostsPage->content ?? '' ?>
        </div>
    </div>
</section>
