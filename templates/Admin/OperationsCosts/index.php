<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\OperationsCost> $operationsCosts
 */
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-dollar-sign"></i> Configurações de Custos por Contrato
                    </h3>
                    <div class="card-tools">
                        <a href="<?= $this->Url->build(['action' => 'add']) ?>" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Novo Custo
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (count($operationsCosts) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Mercado</th>
                                        <th>Custo por Contrato</th>
                                        <th>Data Início</th>
                                        <th>Data Fim</th>
                                        <th class="actions">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($operationsCosts as $operationsCost): ?>
                                        <tr>
                                            <td><?= $this->Number->format($operationsCost->id) ?></td>
                                            <td>
                                                <?= $operationsCost->hasValue('market') ? h($operationsCost->market->name) : '-' ?>
                                            </td>
                                            <td>
                                                <?php if ($operationsCost->cost_per_contract !== null): ?>
                                                    <span class="badge badge-success">
                                                        <?= $this->Currency::formatForUser($operationsCost->cost_per_contract, 'BRL') ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge badge-secondary">Não definido</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?= $operationsCost->date_start ? $operationsCost->date_start->format('d/m/Y H:i') : '-' ?>
                                            </td>
                                            <td>
                                                <?= $operationsCost->date_end ? $operationsCost->date_end->format('d/m/Y H:i') : '-' ?>
                                            </td>
                                            <td class="actions">
                                                <div class="btn-group" role="group">
                                                    <a href="<?= $this->Url->build(['action' => 'view', $operationsCost->id]) ?>" 
                                                       class="btn btn-info btn-sm" title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="<?= $this->Url->build(['action' => 'edit', $operationsCost->id]) ?>" 
                                                       class="btn btn-warning btn-sm" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <?= $this->Form->postLink(
                                                        '<i class="fas fa-trash"></i>',
                                                        ['action' => 'delete', $operationsCost->id],
                                                        [
                                                            'confirm' => __('Tem certeza que deseja excluir este custo?'),
                                                            'class' => 'btn btn-danger btn-sm',
                                                            'title' => 'Excluir',
                                                            'escape' => false
                                                        ]
                                                    ) ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="paginator">
                            <ul class="pagination">
                                <?= $this->Paginator->first('<< ' . __('primeiro')) ?>
                                <?= $this->Paginator->prev('< ' . __('anterior')) ?>
                                <?= $this->Paginator->numbers() ?>
                                <?= $this->Paginator->next(__('próximo') . ' >') ?>
                                <?= $this->Paginator->last(__('último') . ' >>') ?>
                            </ul>
                            <p><?= $this->Paginator->counter(__('Página {{page}} de {{pages}}, mostrando {{current}} registro(s) de {{count}} total')) ?></p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Nenhum custo configurado</strong><br>
                            Clique em "Novo Custo" para configurar os custos por contrato para diferentes mercados e estudantes.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-info-circle"></i> Como Funciona</h5>
            </div>
            <div class="card-body">
                <p><strong>Configuração de Custos por Contrato:</strong></p>
                <ul>
                    <li>Configure custos específicos por mercado e estudante</li>
                    <li>Defina períodos de vigência com data de início e fim</li>
                    <li>Os custos serão automaticamente aplicados no cálculo do P&L dos estudos</li>
                    <li>Cada operação terá seu custo deduzido do resultado final</li>
                </ul>
            </div>
        </div>
    </div>
</div>