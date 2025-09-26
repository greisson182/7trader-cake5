<?php
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<!DOCTYPE html>
<html lang="pt-BR" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0a0a0a">
    <title>
        7 Trader
        <?= isset($title) ? ' - ' . $title : '' ?>
    </title>
    <link rel="icon" type="image/x-icon" href="/favicon.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Custom Styles -->
    <link href="/adm/css/style.css" rel="stylesheet">

    <script>
        const csrfToken = '<?= $csrfToken ?>';
    </script>
</head>

<body class="fade-in-up">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg glass fixed-top">
        <div class="container">
            <a href="/admin/students/dashboard" class="navbar-brand">
                <strong class="footer-title-admin">7</strong><span style="color:#fff!important; -webkit-text-fill-color: white;">Trader</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <i class="fas fa-bars text-white"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isset($logado->id)): ?>
                        <?php if ($logado->role === 'student'): ?>
                            <li class="nav-item">
                                <a href="/admin/students/dashboard" class="nav-link">
                                    <i class="bi bi-speedometer2 me-1"></i>
                                    Painel
                                </a>
                            </li>
                        <?php elseif ($logado->role === 'admin'): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="dashboardDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-speedometer2 me-1"></i>
                                    Painel
                                </a>
                                <ul class="dropdown-menu glass sub-menu">
                                    <li><a class="dropdown-item" href="/admin">
                                            <i class="bi bi-graph-up-arrow me-2"></i>
                                            Dashboard Administrativo
                                        </a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>

                                    <li><a class="dropdown-item" href="/admin/students">
                                            <i class="bi bi-list-ul me-2"></i>
                                            Ver Todos os Alunos
                                        </a></li>
                                </ul>
                            <li class="nav-item">
                                <a href="/admin/students" class="nav-link">
                                    <i class="bi bi-people me-1"></i>
                                    Alunos
                                </a>
                            </li>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a href="/admin/studies" class="nav-link">
                            <i class="bi bi-journal-text me-1"></i>
                            Estudos
                        </a>
                    </li>

                    <?php if ($logado->role === 'admin') { ?>
                        <li class="nav-item">
                            <a href="/admin/courses" class="nav-link">
                                <i class="bi bi-play-circle me-1"></i>
                                Cursos
                            </a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item">
                            <a href="/admin/courses/courses-students" class="nav-link">
                                <i class="bi bi-play-circle me-1"></i>
                                Cursos
                            </a>
                        </li>
                    <?php } ?>

                </ul>
                <ul class="navbar-nav">

                    <?php if (isset($logado->id)): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>
                                <?= h($logado->name) ?>
                                <span class="badge bg-primary ms-2"><?= ucfirst($logado->role) ?></span>
                            </a>
                            <ul class="dropdown-menu glass dropdown-menu-end sub-menu">
                                <li><a class="dropdown-item" href="/admin/users/edit/<?= $logado->id ?>">
                                        <i class="bi bi-person-gear me-2"></i>
                                        Editar Perfil
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/admin/logout">
                                        <i class="bi bi-box-arrow-right me-2"></i>
                                        Sair
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a href="/login" class="nav-link">
                                <i class="bi bi-box-arrow-in-right me-1"></i>
                                Entrar
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php if ($logado->role === 'admin'): ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="configDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-gear me-1"></i>
                            </a>
                            <ul class="dropdown-menu glass sub-menu">
                                <li>
                                    <a class="dropdown-item" href="/admin/markets">
                                        <i class="bi bi-currency-exchange me-2"></i>
                                        Mercados
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="/admin/students/add">
                                        <i class="bi bi-person-plus me-1"></i>
                                        Adicionar Aluno
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>

                    <!-- Theme Toggle -->
                    <li class="nav-item d-flex align-items-center">
                        <label class="theme-toggle">
                            <input type="checkbox" id="theme-toggle">
                            <span class="theme-slider">
                                <i class="bi bi-sun theme-icon sun-icon"></i>
                                <i class="bi bi-moon theme-icon moon-icon"></i>
                            </span>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main" style="padding-top: 60px;">
        <div class="container">
            <?= $this->Flash->render() ?>
            <?= $this->fetch('content') ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="trading-footer-admin py-4 mt-5 position-relative overflow-hidden">
        <!-- Animação de Candlesticks -->
        <div class="candlestick-container position-absolute w-100 h-100" id="adminFooterCandlesticks"></div>

        <div class="container text-center position-relative" style="z-index: 10;">
            <div class="row align-items-center">
                <div class="col-md-6 text-md-start">
                    <p class="mb-0 footer-brand">
                        <strong class="footer-title-admin">7</strong><span>Trader</span>
                        <span class="footer-subtitle-admin">todos os direitos reservados.</span>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">

                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/adm/js/theme.js"></script>
    <script src="/adm/js/value-colors.js"></script>

    <!-- Custom JavaScript for enhanced UX -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert.classList.contains('show')) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            });
        }, 5000);

        // Add loading state to buttons on form submit
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i> Processando...';
                    submitBtn.disabled = true;
                    submitBtn.classList.add('loading');
                }
            });
        });

        // Add ripple effect to buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.classList.add('btn-ripple');
        });

        // Add slide-in animation to buttons when they appear
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('btn-slide-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.btn').forEach(btn => {
            observer.observe(btn);
        });

        // Add success animation to primary buttons after successful actions
        document.querySelectorAll('.btn-primary').forEach(btn => {
            btn.addEventListener('click', function() {
                setTimeout(() => {
                    this.classList.add('btn-success-pulse');
                    setTimeout(() => {
                        this.classList.remove('btn-success-pulse');
                    }, 600);
                }, 100);
            });
        });

        // Footer Candlestick Animation para Admin com Simulação de Mercado
        class AdminMarketSimulator {
            constructor() {
                this.currentTrend = 'neutral';
                this.trendDuration = 0;
                this.maxTrendDuration = 18;
                this.price = 100;
                this.priceHistory = [];
                this.volatility = 0.4;

                this.updateTrend();
            }

            updateTrend() {
                const trends = ['bullish', 'bearish', 'neutral', 'volatile'];
                const weights = [0.35, 0.35, 0.15, 0.15];

                let random = Math.random();
                let cumulativeWeight = 0;

                for (let i = 0; i < trends.length; i++) {
                    cumulativeWeight += weights[i];
                    if (random <= cumulativeWeight) {
                        this.currentTrend = trends[i];
                        break;
                    }
                }

                this.trendDuration = 0;
                this.maxTrendDuration = Math.random() * 25 + 15;

                switch (this.currentTrend) {
                    case 'volatile':
                        this.volatility = Math.random() * 0.9 + 0.8;
                        break;
                    case 'bullish':
                    case 'bearish':
                        this.volatility = Math.random() * 0.5 + 0.3;
                        break;
                    default:
                        this.volatility = Math.random() * 0.3 + 0.2;
                }
            }

            getMarketData() {
                this.trendDuration++;

                if (this.trendDuration >= this.maxTrendDuration) {
                    this.updateTrend();
                }

                let priceChange = 0;
                const baseChange = (Math.random() - 0.5) * this.volatility;

                switch (this.currentTrend) {
                    case 'bullish':
                        priceChange = baseChange + (Math.random() * 0.4 + 0.15);
                        break;
                    case 'bearish':
                        priceChange = baseChange - (Math.random() * 0.4 + 0.15);
                        break;
                    case 'volatile':
                        priceChange = (Math.random() - 0.5) * this.volatility * 2.5;
                        break;
                    default:
                        priceChange = baseChange * 0.6;
                }

                this.price += priceChange;
                this.priceHistory.push(this.price);

                if (this.priceHistory.length > 60) {
                    this.priceHistory.shift();
                }

                return {
                    trend: this.currentTrend,
                    price: this.price,
                    change: priceChange,
                    volatility: this.volatility,
                    isGreen: priceChange >= 0
                };
            }
        }

        const adminMarketSim = new AdminMarketSimulator();

        function createStaticAdminCandlesticks() {
            const container = document.getElementById('adminFooterCandlesticks');
            if (!container) return;

            // Criar candlesticks estáticos para decoração
            const candlestickPositions = [15, 25, 35, 45, 55, 65, 75, 85, 95, 105];
            const candlestickTypes = [true, false, true, true, false, true, false, true, false, true]; // true = green, false = red
            const candlestickHeights = [20, 25, 35, 50, 22, 28, 24, 26, 28, 30];

            candlestickPositions.forEach((position, index) => {
                const candlestick = document.createElement('div');
                const isGreen = candlestickTypes[index];
                const height = candlestickHeights[index];

                candlestick.className = `admin-footer-candlestick ${isGreen ? 'green' : 'red'} static`;
                candlestick.style.left = position + '%';
                candlestick.style.height = height + 'px';
                candlestick.style.position = 'absolute';
                candlestick.style.bottom = position + 'px';
                candlestick.style.animation = 'none'; // Remove todas as animações
                candlestick.style.opacity = '0.3'; // Deixa mais sutil como decoração

                container.appendChild(candlestick);
            });
        }

        // Criar candlesticks estáticos apenas uma vez
        createStaticAdminCandlesticks();
    </script>
</body>

</html>