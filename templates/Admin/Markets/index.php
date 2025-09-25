<?php
/**
 * @var \App\View\AppView $this
 * @var array $markets
 */
?>
<div class="markets index content fade-in-up">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="display-6 fw-bold mb-2">
                <i class="bi bi-graph-up me-3" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                Mercados
            </h1>
            <p class="text-muted mb-0">Gerenciar mercados disponíveis para estudos</p>
        </div>
        <?php if (isset($currentUser) && $currentUser['role'] === 'admin'): ?>
            <a href="/admin/markets/add" class="btn btn-primary btn-lg">
            <i class="bi bi-plus-circle me-2"></i>
            Novo Mercado
        </a>
        <?php endif; ?>
    </div>

    <?php if (!empty($markets)): ?>
        <!-- Markets Grid -->
        <div class="row g-4">
            <?php foreach ($markets as $market): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card glass h-100 market-card">
                        <div class="card-body d-flex flex-column">
                            <!-- Market Header -->
                            <div class="text-center mb-3">
                                <div class="market-icon mx-auto mb-3">
                                    <i class="bi bi-graph-up-arrow"></i>
                                </div>
                                <h5 class="card-title fw-bold mb-1"><?= h($market['name']) ?></h5>
                                <p class="text-muted small mb-0">
                                    <span class="badge bg-secondary"><?= h($market['code']) ?></span>
                                </p>
                            </div>

                            <!-- Market Info -->
                            <div class="market-info mb-3 flex-grow-1">
                                <?php if (!empty($market['description'])): ?>
                                    <div class="mb-3">
                                        <small class="text-muted">Descrição:</small>
                                        <p class="small mb-0"><?= h($market['description']) ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="info-item">
                                            <i class="bi bi-hash text-primary"></i>
                                            <span class="small text-muted">ID</span>
                                            <div class="fw-semibold"><?= h($market['id']) ?></div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-item">
                                            <i class="bi bi-currency-exchange text-warning"></i>
                                            <span class="small text-muted">Moeda</span>
                                            <div class="fw-semibold">
                                                <?php
                                                $currencyIcons = [
                                                    'BRL' => '🇧🇷 BRL',
                                                    'USD' => '🇺🇸 USD', 
                                                    'EUR' => '🇪🇺 EUR'
                                                ];
                                                echo $currencyIcons[$market['currency']] ?? $market['currency'];
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row g-2 mt-2">
                                    <div class="col-6">
                                        <div class="info-item">
                                            <i class="bi bi-circle-fill <?= $market['active'] ? 'text-success' : 'text-danger' ?>"></i>
                                            <span class="small text-muted">Status</span>
                                            <div class="fw-semibold small">
                                                <?= $market['active'] ? 'Ativo' : 'Inativo' ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-item">
                                            <i class="bi bi-calendar-plus text-info"></i>
                                            <span class="small text-muted">Criado</span>
                                            <div class="fw-semibold small"><?= date('d/m/Y', strtotime($market['created_at'])) ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="market-actions">
                                <div class="d-grid gap-2">
                                    <a href="/admin/markets/view/<?= $market['id'] ?>" class="btn btn-primary">
                                        <i class="fas fa-eye me-1"></i>Ver Detalhes
                                    </a>
                                    
                                    <?php if (isset($currentUser) && $currentUser['role'] === 'admin'): ?>
                                        <div class="btn-group" role="group">
                                            <a href="/admin/markets/edit/<?= $market['id'] ?>" class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="post" action="/admin/markets/delete/<?= $market['id'] ?>" style="display: inline;" 
                                                  onsubmit="return confirm('Tem certeza que deseja excluir este mercado?');">
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="bi bi-graph-up display-1 text-muted mb-4"></i>
                <h3 class="text-muted mb-3">Nenhum mercado encontrado</h3>
                <p class="text-muted mb-4">Não há mercados cadastrados no sistema.</p>
                <?php if (isset($currentUser) && $currentUser['role'] === 'admin'): ?>
                    <a href="/admin/markets/add" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Criar Primeiro Mercado
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.market-card {
    transition: all 0.3s ease;
    border: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.market-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.market-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.info-item {
    text-align: center;
    padding: 0.5rem;
    border-radius: 8px;
    background: rgba(var(--bs-primary-rgb), 0.05);
}

.info-item i {
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    display: block;
}

.market-actions .btn {
    border-radius: 8px;
    font-weight: 500;
}

.empty-state {
    max-width: 400px;
    margin: 0 auto;
}
</style>