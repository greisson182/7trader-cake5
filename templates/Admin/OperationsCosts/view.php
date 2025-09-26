<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\OperationsCost $operationsCost
 */
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye"></i> Visualizar Custo por Contrato
                    </h3>
                    <div class="card-tools">
                        <a href="<?= $this->Url->build(['action' => 'index']) ?>" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                        <a href="<?= $this->Url->build(['action' => 'edit', $operationsCost->id]) ?>" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <?= $this->Form->postLink(
                            '<i class="fas fa-trash"></i> Excluir',
                            ['action' => 'delete', $operationsCost->id],
                            [
                                'confirm' => __('Tem certeza que deseja excluir este custo?'),
                                'class' => 'btn btn-danger btn-sm',
                                'escape' => false
                            ]
                        ) ?>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5><i class="fas fa-info-circle"></i> Informações Básicas</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID:</strong></td>
                                    <td><?= $this->Number->format($operationsCost->id) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Mercado:</strong></td>
                                    <td>
                                        <?php if ($operationsCost->hasValue('market')): ?>
                                            <span class="badge badge-primary">
                                                <?= h($operationsCost->market->name) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">Não definido</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Estudante:</strong></td>
                                    <td>
                                        <?php if ($operationsCost->hasValue('student')): ?>
                                            <span class="badge badge-info">
                                                <?= h($operationsCost->student->name) ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">Não definido</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Custo por Contrato:</strong></td>
                                    <td>
                                        <?php if ($operationsCost->cost_per_contract !== null): ?>
                                            <span class="badge badge-success font-size-lg">
                                                <?= $this->Currency::formatForUser($operationsCost->cost_per_contract, 'BRL') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">Não definido</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5><i class="fas fa-calendar"></i> Período de Vigência</h5>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Data de Início:</strong></td>
                                    <td>
                                        <?php if ($operationsCost->date_start): ?>
                                            <span class="badge badge-success">
                                                <?= $operationsCost->date_start->format('d/m/Y H:i') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">Não definida</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Data de Fim:</strong></td>
                                    <td>
                                        <?php if ($operationsCost->date_end): ?>
                                            <span class="badge badge-warning">
                                                <?= $operationsCost->date_end->format('d/m/Y H:i') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-info">Indefinido</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <?php
                                        $now = new DateTime();
                                        $isActive = true;
                                        
                                        if ($operationsCost->date_start && $operationsCost->date_start > $now) {
                                            $isActive = false;
                                            $status = 'Futuro';
                                            $statusClass = 'secondary';
                                        } elseif ($operationsCost->date_end && $operationsCost->date_end < $now) {
                                            $isActive = false;
                                            $status = 'Expirado';
                                            $statusClass = 'danger';
                                        } else {
                                            $status = 'Ativo';
                                            $statusClass = 'success';
                                        }
                                        ?>
                                        <span class="badge badge-<?= $statusClass ?>">
                                            <?= $status ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-clock"></i> Histórico</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Criado em:</strong></p>
                        <p class="text-muted">
                            <?= $operationsCost->created ? $operationsCost->created->format('d/m/Y H:i:s') : 'Não disponível' ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Última modificação:</strong></p>
                        <p class="text-muted">
                            <?= $operationsCost->modified ? $operationsCost->modified->format('d/m/Y H:i:s') : 'Não disponível' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-calculator"></i> Como o Custo é Aplicado</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle"></i> Cálculo do P&L com Custos</h6>
                    <p class="mb-2">Este custo será automaticamente deduzido do resultado de cada operação:</p>
                    <ul class="mb-2">
                        <li><strong>Resultado Final = Resultado da Operação - (Custo por Contrato × Quantidade de Contratos)</strong></li>
                        <li>O custo é aplicado apenas para operações dentro do período de vigência</li>
                        <li>Operações do mercado e estudante especificados serão afetadas</li>
                    </ul>
                    
                    <?php if ($operationsCost->cost_per_contract): ?>
                        <div class="mt-3">
                            <h6>Exemplo de Cálculo:</h6>
                            <p class="mb-1">
                                <strong>Operação com 5 contratos:</strong><br>
                                Resultado: R$ 100,00<br>
                                Custo: <?= $this->Currency::formatForUser($operationsCost->cost_per_contract, 'BRL') ?> × 5 = <?= $this->Currency::formatForUser($operationsCost->cost_per_contract * 5, 'BRL') ?><br>
                                <strong>Resultado Final: <?= $this->Currency::formatForUser(100 - ($operationsCost->cost_per_contract * 5), 'BRL') ?></strong>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.font-size-lg {
    font-size: 1.1em;
}
</style>