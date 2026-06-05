<section class="content-section dark">
    <div class="container">
        <h1 class="section-title neon"><?= h($page->title ?? 'Blog') ?></h1>
        <?php if (!empty($page->subtitle)) : ?>
            <p class="section-subtitle"><?= h($page->subtitle) ?></p>
        <?php endif; ?>

        <div class="row g-4">
            <?php if (!empty($blogs)) : ?>
                <?php foreach ($blogs as $post) : ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="modern-card h-100">
                            <h2 class="h5 mb-2"><?= h($post->title ?? '') ?></h2>
                            <?php if (!empty($post->summary)) : ?>
                                <div class="text-muted mb-3"><?= $post->summary ?></div>
                            <?php endif; ?>
                            <a class="btn btn-outline-success w-100" href="<?= $this->Url->build('/' . ($post->slug ?? '')) ?>">Ler mais</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="col-12">
                    <div class="modern-card">
                        <div class="text-muted">Nenhum conteúdo disponível no momento.</div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
