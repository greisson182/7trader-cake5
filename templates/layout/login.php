<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/favicon.png" type="image/x-icon">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - Sistema Backtest</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="/adm/css/login.css" rel="stylesheet">
</head>

<body>
    <!-- Market Grid Pattern -->
    <div class="market-grid"></div>
    
    <!-- Background Particles -->
    <div class="background-particles">
        <div class="particle" style="left: 10%; top: 20%; width: 4px; height: 4px; animation-delay: 0s;"></div>
        <div class="particle" style="left: 80%; top: 30%; width: 6px; height: 6px; animation-delay: 1s;"></div>
        <div class="particle" style="left: 60%; top: 70%; width: 3px; height: 3px; animation-delay: 2s;"></div>
        <div class="particle" style="left: 20%; top: 80%; width: 5px; height: 5px; animation-delay: 3s;"></div>
        <div class="particle" style="left: 90%; top: 60%; width: 4px; height: 4px; animation-delay: 4s;"></div>
    </div>

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-8 col-lg-6">
                <div class="login-card">
                    <div class="login-header">
                        <img src="/adm/images/logo-dark.png" alt="7 Trader" class="img-fluid">
                    </div>

                    <div class="login-body">
                        <?= $this->Flash->render() ?>
                        <?= $this->fetch('content') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>