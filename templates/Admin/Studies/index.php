<?php
$csrfToken = $this->request->getAttribute('csrfToken');
?>
<link rel="stylesheet" href="/adm/css/studies.css">

<div class="studies index content">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-chart-bar gradient-text"></i> Estudos de Mercado</h3>
        <div class="d-flex gap-2">
            <button id="importCsvBtn" class="btn btn-success btn-with-icon">
                <i class="fas fa-file-csv me-2"></i>Importar CSV
            </button>
            <a href="/admin/studies/add" class="btn btn-primary btn-with-icon">
                <i class="fas fa-plus me-2"></i>Novo Estudo
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <label for="marketFilter" class="form-label mb-2">
                        <i class="fas fa-filter me-2"></i>Mercado
                    </label>
                    <select id="marketFilter" class="form-select">
                        <option value="">Todos os mercados</option>
                        <?php if (!empty($markets)): ?>
                            <?php foreach ($markets as $market): ?>
                                <option value="<?= h($market['id']) ?>"><?= h($market['name']) ?> (<?= h($market['code']) ?>)</option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="accountFilter" class="form-label mb-2">
                        <i class="fas fa-user-circle me-2"></i>Conta
                    </label>
                    <select id="accountFilter" class="form-select">
                        <option value="">Todas as contas</option>
                        <?php if (!empty($accounts)): ?>
                            <?php foreach ($accounts as $account): ?>
                                <option value="<?= h($account['id']) ?>"><?= h($account['name']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="yearFilter" class="form-label mb-2">
                        <i class="fas fa-calendar me-2"></i>Ano
                    </label>
                    <select id="yearFilter" class="form-select">
                        <option value="">Todos os anos</option>
                        <?php
                        $years = [];
                        if (!empty($studiesByMonth)) {
                            foreach ($studiesByMonth as $monthKey => $monthData) {
                                $year = substr($monthKey, 0, 4);
                                if (!in_array($year, $years)) {
                                    $years[] = $year;
                                }
                            }
                            rsort($years); // Mais recente primeiro
                            foreach ($years as $year) {
                                echo "<option value=\"$year\">$year</option>";
                            }
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label mb-2">&nbsp;</label>
                    <button id="clearFilters" class="btn btn-outline-secondary d-block">
                        <i class="fas fa-times me-2"></i>Limpar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($studiesByMonth)): ?>
        <?php foreach ($studiesByMonth as $monthKey => $monthData): ?>
            <div class="card month-card mb-4" data-month-key="<?= h($monthKey) ?>">
                <div class="card-header month-header" style="cursor: pointer;">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h5 class="mb-0">
                                <i class="fas fa-calendar-alt me-2"></i>
                                <?= h($monthData['display']) ?>
                            </h5>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-item">
                                <span class="stat-label">Estudos</span>
                                <span class="stat-value"><?= h($monthData['total_studies']) ?></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-item">
                                <span class="stat-label">Gain</span>
                                <span class="stat-value text-success"><?= h($monthData['total_wins']) ?></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="stat-item">
                                <span class="stat-label">Loss</span>
                                <span class="stat-value text-danger"><?= h($monthData['total_losses']) ?></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <?php
                            $totalTrades = $monthData['total_wins'] + $monthData['total_losses'];
                            $winRate = $totalTrades > 0 ? round($monthData['total_wins'] / $totalTrades * 100, 2) : 0;
                            ?>
                            <div class="stat-item">
                                <span class="stat-label">Taxa de Acerto</span>
                                <span class="stat-value <?= $winRate >= 50 ? 'text-success' : 'text-warning' ?>">
                                    <?= $winRate ?>%
                                </span>
                            </div>
                        </div>
                        <div class="col-md-1 text-end">
                            <i class="fas fa-chevron-down expand-icon transition-all"></i>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-3">
                            <div class="stat-item">
                                <span class="stat-label">P&L Total</span>
                                <span class="stat-value <?= $monthData['total_profit_loss'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                    <?= $this->Currency::formatForUser($monthData['total_profit_loss'], $monthData['studies'][0]['user']['currency'] ?? 'BRL') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body month-studies" style="display: none;">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Estudante</th>
                                    <th>Mercado</th>
                                    <th>Conta</th>
                                    <th>Data do Estudo</th>
                                    <th>Gain</th>
                                    <th>Loss</th>
                                    <th>Taxa de Acerto</th>
                                    <th>P&L</th>
                                    <th class="actions">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($monthData['studies'] as $study): ?>
                                    <tr class="clickable-row study-row"
                                        data-study-id="<?= h($study['id']) ?>"
                                        data-market-id="<?= h($study['market_id'] ?? '') ?>"
                                        data-account-id="<?= h($study['account_id'] ?? '') ?>"
                                        style="cursor: pointer;">
                                        <td><?= h($study['id']) ?></td>
                                        <td><?= isset($study['student_name']) && $study['student_name'] ? '<a href="/admin/students/view/' . h($study['student_id']) . '">' . h($study['student_name']) . '</a>' : 'N/A' ?></td>
                                        <td>
                                            <?php if (isset($study['market_name']) && $study['market_name']): ?>
                                                <span class="badge bg-info"><?= h($study['market_name']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (isset($study['account_name']) && $study['account_name']): ?>
                                                <span class="badge bg-secondary"><?= h($study['account_name']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= h($study['study_date'] ?? '') ?></td>
                                        <td><span class="badge bg-success"><?= h($study['wins'] ?? 0) ?></span></td>
                                        <td><span class="badge bg-danger"><?= h($study['losses'] ?? 0) ?></span></td>
                                        <td>
                                            <?php
                                            $total_trades = ($study['wins'] ?? 0) + ($study['losses'] ?? 0);
                                            $win_rate = $total_trades > 0 ? round(($study['wins'] ?? 0) / $total_trades * 100, 2) : 0;
                                            ?>
                                            <span class="badge <?= $win_rate >= 50 ? 'bg-success' : 'bg-warning' ?>">
                                                <?= $win_rate ?>%
                                            </span>
                                        </td>
                                        <td class="<?= $study['profit_loss'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                            <?= $this->Currency::formatForUser($study['profit_loss'] ?? 0, $study['user']['currency'] ?? 'BRL') ?>
                                        </td>
                                        <td class="actions" onclick="event.stopPropagation();">
                                            <a href="/admin/studies/view/<?= h($study['id']) ?>" class="btn btn-sm btn-outline-primary me-1" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="/admin/studies/edit/<?= h($study['id']) ?>" class="btn btn-sm btn-outline-secondary me-1" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="post" action="/admin/studies/delete/<?= h($study['id']) ?>" style="display: inline;" onsubmit="return confirm('Tem certeza que deseja excluir # <?= h($study['id']) ?>?');">
                                                <input type="hidden" name="_csrfToken" value="<?= $csrfToken ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum estudo encontrado</h5>
                <p class="text-muted">Comece criando seu primeiro estudo de market replay.</p>
                <a href="/admin/studies/add" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Criar Primeiro Estudo
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle month studies visibility
        document.querySelectorAll('.month-header').forEach(header => {
            header.addEventListener('click', function() {
                const card = this.closest('.month-card');
                const studiesBody = card.querySelector('.month-studies');
                const expandIcon = this.querySelector('.expand-icon');

                if (studiesBody.style.display === 'none') {
                    studiesBody.style.display = 'block';
                    expandIcon.classList.remove('fa-chevron-down');
                    expandIcon.classList.add('fa-chevron-up');
                    card.classList.add('expanded');
                } else {
                    studiesBody.style.display = 'none';
                    expandIcon.classList.remove('fa-chevron-up');
                    expandIcon.classList.add('fa-chevron-down');
                    card.classList.remove('expanded');
                }
            });
        });

        // Adicionar funcionalidade de clique nas linhas da tabela
        document.addEventListener('click', function(e) {
            const clickableRow = e.target.closest('.clickable-row');
            if (clickableRow) {
                const studyId = clickableRow.getAttribute('data-study-id');
                if (studyId) {
                    window.location.href = '/admin/studies/view/' + studyId;
                }
            }
        });

        const marketFilter = document.getElementById('marketFilter');
        const accountFilter = document.getElementById('accountFilter');
        const yearFilter = document.getElementById('yearFilter');
        const clearFilters = document.getElementById('clearFilters');
        const autoUpdate = document.getElementById('autoUpdate');
        let autoUpdateInterval;

        // Função para recalcular valores do month-header baseado nos estudos visíveis
        function updateMonthHeaderValues(card) {
            const visibleRows = card.querySelectorAll('.study-row[style=""], .study-row:not([style*="display: none"])');
            
            let totalStudies = 0;
            let totalWins = 0;
            let totalLosses = 0;
            let totalProfitLoss = 0;

            visibleRows.forEach(row => {
                // Extrair valores das células da tabela
                const cells = row.querySelectorAll('td');
                if (cells.length >= 9) {
                    const wins = parseInt(cells[5].textContent.trim()) || 0;
                    const losses = parseInt(cells[6].textContent.trim()) || 0;
                    
                    // Extrair P&L da célula (remover formatação de moeda)
                    const profitLossText = cells[8].textContent.trim();
                    const profitLossValue = parseFloat(profitLossText.replace(/[^\d,-]/g, '').replace(',', '.')) || 0;
                    
                    totalStudies++;
                    totalWins += wins;
                    totalLosses += losses;
                    totalProfitLoss += profitLossValue;
                }
            });

            // Calcular taxa de acerto
            const totalTrades = totalWins + totalLosses;
            const winRate = totalTrades > 0 ? (totalWins / totalTrades * 100).toFixed(2) : 0;

            // Atualizar valores no header
            const header = card.querySelector('.month-header');
            
            // Atualizar estudos
            const studiesValue = header.querySelector('.stat-item:nth-child(1) .stat-value');
            if (studiesValue) studiesValue.textContent = totalStudies;

            // Atualizar gains
            const gainsValue = header.querySelector('.stat-item:nth-child(2) .stat-value');
            if (gainsValue) gainsValue.textContent = totalWins;

            // Atualizar losses
            const lossesValue = header.querySelector('.stat-item:nth-child(3) .stat-value');
            if (lossesValue) lossesValue.textContent = totalLosses;

            // Atualizar taxa de acerto
            const winRateValue = header.querySelector('.stat-item:nth-child(4) .stat-value');
            if (winRateValue) {
                winRateValue.textContent = winRate + '%';
                winRateValue.className = 'stat-value ' + (winRate >= 50 ? 'text-success' : 'text-warning');
            }

            // Atualizar P&L
            const profitLossElement = header.querySelector('.row.mt-2 .stat-value');
            if (profitLossElement) {
                // Formatar valor com moeda
                const formattedValue = new Intl.NumberFormat('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }).format(totalProfitLoss);
                
                profitLossElement.textContent = formattedValue;
                profitLossElement.className = 'stat-value ' + (totalProfitLoss >= 0 ? 'text-success' : 'text-danger');
            }
        }

        function filterStudies() {
            const selectedMarketId = marketFilter.value;
            const selectedAccountId = accountFilter.value;
            const selectedYear = yearFilter.value;
            const monthCards = document.querySelectorAll('.month-card');

            monthCards.forEach(card => {
                const monthKey = card.dataset.monthKey;
                const cardYear = monthKey ? monthKey.substring(0, 4) : '';

                // Verificar filtro de ano
                const yearMatch = !selectedYear || cardYear === selectedYear;

                // Verificar filtro de mercado e conta
                let marketMatch = true;
                let accountMatch = true;

                if (selectedMarketId || selectedAccountId) {
                    const studyRows = card.querySelectorAll('.study-row');

                    if (selectedMarketId) {
                        marketMatch = Array.from(studyRows).some(row =>
                            row.dataset.marketId === selectedMarketId
                        );
                    }

                    if (selectedAccountId) {
                        accountMatch = Array.from(studyRows).some(row =>
                            row.dataset.accountId === selectedAccountId
                        );
                    }
                }

                if (yearMatch && marketMatch && accountMatch) {
                    card.style.display = '';

                    // Filtrar estudos dentro do card
                    const studyRows = card.querySelectorAll('.study-row');
                    studyRows.forEach(row => {
                        let showRow = true;

                        if (selectedMarketId && row.dataset.marketId !== selectedMarketId) {
                            showRow = false;
                        }

                        if (selectedAccountId && row.dataset.accountId !== selectedAccountId) {
                            showRow = false;
                        }

                        row.style.display = showRow ? '' : 'none';
                    });

                    // Recalcular valores do month-header baseado nos estudos visíveis
                    updateMonthHeaderValues(card);
                } else {
                    card.style.display = 'none';
                }
            });

            // Mostrar mensagem se nenhum estudo for encontrado
            const hasVisibleCards = Array.from(monthCards).some(card => card.style.display !== 'none');
            let noResultsMessage = document.getElementById('noResultsMessage');

            if (!hasVisibleCards && (selectedMarketId || selectedAccountId || selectedYear)) {
                if (!noResultsMessage) {
                    noResultsMessage = document.createElement('div');
                    noResultsMessage.id = 'noResultsMessage';
                    noResultsMessage.className = 'card mt-4';
                    noResultsMessage.innerHTML = `
                    <div class="card-body text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Nenhum estudo encontrado</h5>
                        <p class="text-muted">Não há estudos para os filtros selecionados.</p>
                    </div>
                `;
                    document.querySelector('.studies.index.content').appendChild(noResultsMessage);
                }
                noResultsMessage.style.display = '';
            } else if (noResultsMessage) {
                noResultsMessage.style.display = 'none';
            }
        }

        function startAutoUpdate() {
            if (autoUpdate.checked) {
                autoUpdateInterval = setInterval(() => {
                    // Recarregar a página mantendo os filtros atuais
                    const currentMarket = marketFilter.value;
                    const currentYear = yearFilter.value;
                    const url = new URL(window.location);

                    if (currentMarket) {
                        url.searchParams.set('market', currentMarket);
                    } else {
                        url.searchParams.delete('market');
                    }

                    if (currentYear) {
                        url.searchParams.set('year', currentYear);
                    } else {
                        url.searchParams.delete('year');
                    }

                    window.location.href = url.toString();
                }, 30000); // Atualizar a cada 30 segundos
            }
        }

        function stopAutoUpdate() {
            if (autoUpdateInterval) {
                clearInterval(autoUpdateInterval);
                autoUpdateInterval = null;
            }
        }

        // Event listeners
        if (marketFilter) {
            marketFilter.addEventListener('change', filterStudies);
        }
        if (accountFilter) {
            accountFilter.addEventListener('change', filterStudies);
        }
        if (yearFilter) {
            yearFilter.addEventListener('change', filterStudies);
        }

        // Função para restaurar valores originais do month-header
        function restoreOriginalValues() {
            const monthCards = document.querySelectorAll('.month-card');
            monthCards.forEach(card => {
                // Mostrar todas as linhas
                const studyRows = card.querySelectorAll('.study-row');
                studyRows.forEach(row => {
                    row.style.display = '';
                });
                
                // Recalcular com todos os estudos visíveis
                updateMonthHeaderValues(card);
            });
        }

        if (clearFilters) {
            clearFilters.addEventListener('click', function() {
                if (marketFilter) marketFilter.value = '';
                if (accountFilter) accountFilter.value = '';
                if (yearFilter) yearFilter.value = '';
                
                // Restaurar valores originais antes de aplicar filtros
                restoreOriginalValues();
                filterStudies();
            });
        }

        if (autoUpdate) {
            autoUpdate.addEventListener('change', function() {
                if (this.checked) {
                    startAutoUpdate();
                } else {
                    stopAutoUpdate();
                }
            });
        }

        // Inicializar atualização automática se estiver habilitada
        if (autoUpdate && autoUpdate.checked) {
            startAutoUpdate();
        }

        // Aplicar filtros baseados nos parâmetros da URL ao carregar
        const urlParams = new URLSearchParams(window.location.search);
        const marketParam = urlParams.get('market');
        const yearParam = urlParams.get('year');

        if (marketParam && marketFilter) {
            marketFilter.value = marketParam;
        }
        if (yearParam && yearFilter) {
            yearFilter.value = yearParam;
        }

        // Botão de importar CSV
        const importCsvBtn = document.getElementById('importCsvBtn');
        if (importCsvBtn) {
            importCsvBtn.addEventListener('click', function() {
                showStudySelectionModal();
            });
        }

        function showStudySelectionModal() {
            // Criar modal para seleção de estudo
            const modalHtml = `
                <div class="modal fade" id="csvImportModal" tabindex="-1" aria-labelledby="csvImportModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="csvImportModalLabel">Importar CSV de Operações</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="studySelect" class="form-label">Selecione o Estudo:</label>
                                    <select class="form-select" id="studySelect" required>
                                        <option value="">Escolha um estudo...</option>
                                        <?php foreach ($studies as $study): ?>
                                            <option value="<?= $study['id'] ?>"><?= h($study['title'] ?? 'Estudo sem título') ?> - <?= is_string($study['study_date']) ? date('d/m/Y', strtotime($study['study_date'])) : $study['study_date']->format('d/m/Y') ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="csvFileInput" class="form-label">Arquivo CSV:</label>
                                    <input type="file" class="form-control" id="csvFileInput" accept=".csv" required>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-primary" id="confirmImportBtn">Importar</button>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remover modal existente se houver
            const existingModal = document.getElementById('csvImportModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Adicionar modal ao DOM
            document.body.insertAdjacentHTML('beforeend', modalHtml);

            // Mostrar modal
            const modal = new bootstrap.Modal(document.getElementById('csvImportModal'));
            modal.show();

            // Evento do botão confirmar
            document.getElementById('confirmImportBtn').addEventListener('click', function() {
                const studyId = document.getElementById('studySelect').value;
                const fileInput = document.getElementById('csvFileInput');
                const file = fileInput.files[0];

                if (!studyId) {
                    alert('Por favor, selecione um estudo.');
                    return;
                }

                if (!file) {
                    alert('Por favor, selecione um arquivo CSV.');
                    return;
                }

                modal.hide();
                importCsvFile(file, studyId);
            });
        }

        function importCsvFile(file, studyId) {
            const formData = new FormData();
            formData.append('csv_file', file);
            formData.append('study_id', studyId);
            formData.append('_csrfToken', '<?= $csrfToken ?>');

            // Mostrar loading
            importCsvBtn.disabled = true;
            importCsvBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Importando...';

            fetch('/admin/studies/import-csv', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('CSV importado com sucesso! ' + data.message);
                    location.reload(); // Recarregar a página para mostrar os novos dados
                } else {
                    alert('Erro ao importar CSV: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro:', error);
                alert('Erro ao importar CSV. Verifique o console para mais detalhes.');
            })
            .finally(() => {
                // Restaurar botão
                importCsvBtn.disabled = false;
                importCsvBtn.innerHTML = '<i class="fas fa-file-csv me-2"></i>Importar CSV';
            });
        }

        // Aplicar filtros iniciais
        filterStudies();
    });
</script>