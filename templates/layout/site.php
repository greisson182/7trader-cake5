<?php
$pageTitle = $title_layout ?? ($Config->title ?? '7 Trader');
$pageDescription = $Config->description ?? 'Treinamentos de day trade no mercado financeiro com price action e gatilhos. Curso, mentoria em grupo e mentoria particular.';
$pageImage = $Config->image ?? null;
$canonicalUrl = $this->Url->build($this->getRequest()->getUri()->getPath() ?: '/', ['fullBase' => true]);
$baseUrl = $this->Url->build('/', ['fullBase' => true]);
$logoUrl = $this->Url->build('/site/images/logo-light.png', ['fullBase' => true]);
$defaultOgImage = $this->Url->build('/site/images/trading-chart.svg', ['fullBase' => true]);
$ogImage = $pageImage;
if (empty($ogImage)) {
    $ogImage = $defaultOgImage;
} elseif (!preg_match('#^https?://#', (string)$ogImage)) {
    $ogImage = $this->Url->build((string)$ogImage, ['fullBase' => true]);
}
$isHome = $this->getRequest()->getUri()->getPath() === '/';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle) ?></title>
    <link rel="icon" href="/favicon.png" type="image/x-icon">
    <link rel="canonical" href="<?= h($canonicalUrl) ?>">
    <meta name="description" content="<?= h($pageDescription) ?>">
    <meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">

    <meta property="og:locale" content="pt_BR">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= h($Config->title ?? '7 Trader') ?>">
    <meta property="og:title" content="<?= h($pageTitle) ?>">
    <meta property="og:description" content="<?= h($pageDescription) ?>">
    <meta property="og:url" content="<?= h($canonicalUrl) ?>">
    <meta property="og:image" content="<?= h($ogImage) ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= h($pageTitle) ?>">
    <meta name="twitter:description" content="<?= h($pageDescription) ?>">
    <meta name="twitter:image" content="<?= h($ogImage) ?>">
    <meta name="theme-color" content="#00ff88">

    <script type="application/ld+json"><?= json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'Organization',
                    '@id' => $baseUrl . '#organization',
                    'name' => $Config->title ?? '7 Trader',
                    'url' => $baseUrl,
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => $logoUrl,
                    ],
                ],
                [
                    '@type' => 'WebSite',
                    '@id' => $baseUrl . '#website',
                    'url' => $baseUrl,
                    'name' => $Config->title ?? '7 Trader',
                    'publisher' => [
                        '@id' => $baseUrl . '#organization',
                    ],
                ],
                ...($isHome ? [[
                    '@type' => 'Service',
                    '@id' => $baseUrl . '#treinamentos-daytrade',
                    'name' => 'Treinamentos de Day Trade (Price Action e Gatilhos)',
                    'provider' => [
                        '@id' => $baseUrl . '#organization',
                    ],
                    'areaServed' => 'BR',
                    'offers' => [
                        [
                            '@type' => 'Offer',
                            'name' => 'Curso',
                            'priceCurrency' => 'BRL',
                            'price' => '497.00',
                            'url' => $baseUrl . '#planos',
                            'availability' => 'https://schema.org/InStock',
                        ],
                        [
                            '@type' => 'Offer',
                            'name' => 'Mentoria em grupo',
                            'priceCurrency' => 'BRL',
                            'price' => '997.00',
                            'url' => $baseUrl . '#planos',
                            'availability' => 'https://schema.org/InStock',
                        ],
                        [
                            '@type' => 'Offer',
                            'name' => 'Mentoria particular',
                            'priceCurrency' => 'BRL',
                            'price' => '2997.00',
                            'url' => $baseUrl . '#planos',
                            'availability' => 'https://schema.org/InStock',
                        ],
                    ],
                ]] : []),
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?></script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <?= $this->Html->css('/site/css/site.css') ?>

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top trading-navbar">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?= $this->Url->build('/') ?>">
                <img src="/site/images/logo-dark.png" alt="7 Trader" class="img-logo">
            </a>

            <button class="navbar-toggler" type="button" aria-label="Abrir menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="navbar-collapse collapse">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
                    <li class="nav-item"><a class="nav-link trading-nav-link" href="/#metodologia">Metodologia</a></li>
                    <li class="nav-item"><a class="nav-link trading-nav-link" href="/#ofertas">O que você recebe</a></li>
                    <li class="nav-item"><a class="nav-link trading-nav-link" href="/#planos">Planos</a></li>
                    <li class="nav-item"><a class="nav-link trading-nav-link" href="/#faq">FAQ</a></li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-success" href="/#planos">Quero começar</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-light" href="/login">Área do aluno</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <?= $this->Flash->render() ?>
    <main class="site-main">
        <?= $this->fetch('content') ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->Html->script('/site/js/site.js') ?>
</body>
</html>
