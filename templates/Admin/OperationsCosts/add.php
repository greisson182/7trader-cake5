<?php

/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\OperationsCost $operationsCost
 * @var \Cake\Collection\CollectionInterface|string[] $markets
 * @var \Cake\Collection\CollectionInterface|string[] $students
 */
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus"></i> Adicionar Custo por Contrato
                    </h3>
                    <div class="card-tools">
                        <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?= $this->Form->create($operationsCost, ['class' => 'needs-validation', 'novalidate' => true]) ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= $this->Form->control('market_id', [
                                    'type' => 'select',
                                    'options' => $markets,
                                    'empty' => 'Selecione um mercado',
                                    'class' => 'form-control',
                                    'label' => [
                                        'text' => 'Mercado *',
                                        'class' => 'form-label'
                                    ],
                                    'required' => true
                                ]) ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= $this->Form->control('account_id', [
                                    'type' => 'select',
                                    'options' => $accounts,
                                    'class' => 'form-control',
                                    'label' => [
                                        'text' => 'Tipo de Conta *',
                                        'class' => 'form-label'
                                    ],
                                    'required' => true
                                ]) ?>
                                <small class="form-text text-muted">
                                    Selecione se o custo se aplica à conta real ou simulador
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= $this->Form->control('cost_per_contract', [
                                    'type' => 'number',
                                    'step' => '0.01',
                                    'min' => '0',
                                    'class' => 'form-control',
                                    'label' => [
                                        'text' => 'Custo por Contrato (R$) *',
                                        'class' => 'form-label'
                                    ],
                                    'placeholder' => 'Ex: 2.50',
                                    'required' => true
                                ]) ?>
                                <small class="form-text text-muted">
                                    Valor em reais que será deduzido por contrato operado
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= $this->Form->control('date_start', [
                                    'type' => 'datetime-local',
                                    'class' => 'form-control',
                                    'label' => [
                                        'text' => 'Data de Início *',
                                        'class' => 'form-label'
                                    ],
                                    'required' => true
                                ]) ?>
                                <small class="form-text text-muted">
                                    A partir de quando este custo será aplicado
                                </small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?= $this->Form->control('date_end', [
                                    'type' => 'datetime-local',
                                    'class' => 'form-control',
                                    'label' => [
                                        'text' => 'Data de Fim',
                                        'class' => 'form-label'
                                    ]
                                ]) ?>
                                <small class="form-text text-muted">
                                    Deixe em branco para aplicar indefinidamente
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mt-4">
                        <div class="d-flex justify-content-between">
                            <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Salvar Custo</button>
                        </div>
                    </div>

                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-lightbulb"></i> Dicas</h5>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li><strong>Mercado:</strong> Selecione o mercado específico para este custo</li>
                    <li><strong>Estudante:</strong> Defina para qual estudante este custo se aplica</li>
                    <li><strong>Custo por Contrato:</strong> Valor que será deduzido de cada operação</li>
                    <li><strong>Período:</strong> Configure quando este custo estará ativo</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validação do formulário
        const form = document.querySelector('.needs-validation');

        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });

        // Validação de datas
        const dateStart = document.querySelector('input[name="date_start"]');
        const dateEnd = document.querySelector('input[name="date_end"]');

        if (dateStart && dateEnd) {
            dateStart.addEventListener('change', function() {
                if (dateEnd.value && dateStart.value > dateEnd.value) {
                    dateEnd.setCustomValidity('A data de fim deve ser posterior à data de início');
                } else {
                    dateEnd.setCustomValidity('');
                }
            });

            dateEnd.addEventListener('change', function() {
                if (dateStart.value && dateEnd.value && dateStart.value > dateEnd.value) {
                    dateEnd.setCustomValidity('A data de fim deve ser posterior à data de início');
                } else {
                    dateEnd.setCustomValidity('');
                }
            });
        }
    });
</script>