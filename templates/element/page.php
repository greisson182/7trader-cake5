<section class="content-section dark">
    <div class="container">
        <h1 class="section-title neon"><?= h($page->title ?? 'Página') ?></h1>
        <?php if (!empty($page->subtitle)) : ?>
            <p class="section-subtitle"><?= h($page->subtitle) ?></p>
        <?php endif; ?>

        <div class="modern-card">
            <?= $page->content ?? '' ?>
        </div>
    </div>
</section>
