<?php

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class WelcomeController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();

    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->checkSession();
    }

    public function index()
    {

        if ($this->isStudent()) {
            return $this->redirect(['controller' => 'Students', 'action' => 'dashboard']);
        }

        // Load necessary tables
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $studiesTable = TableRegistry::getTableLocator()->get('Studies');
        $usersTable = TableRegistry::getTableLocator()->get('Users');

        // Total de estudantes
        $totalStudents = $studentsTable->find()->count();

        // Estudantes ativos (com usuários ativos)
        $activeStudents = $studentsTable->find()
            ->innerJoinWith('Users', function ($q) {
                return $q->where(['Users.active' => 1]);
            })
            ->count();

        // Total de estudos
        $totalStudies = $studiesTable->find()->count();

        // Performance geral
        $totalTrades = $studiesTable->find()->select(['total' => $studiesTable->find()->func()->sum('wins + losses')])->first()->total ?? 0;
        $totalWins = $studiesTable->find()->select(['total' => $studiesTable->find()->func()->sum('wins')])->first()->total ?? 0;
        $totalLosses = $studiesTable->find()->select(['total' => $studiesTable->find()->func()->sum('losses')])->first()->total ?? 0;
        $totalProfitLoss = $studiesTable->find()->select(['total' => $studiesTable->find()->func()->sum('profit_loss')])->first()->total ?? 0;

        // Taxa de acerto geral
        $overallWinRate = $totalTrades > 0 ? round(($totalWins / $totalTrades) * 100, 2) : 0;

        // Top 5 estudantes por profit/loss
        $topStudents = $studentsTable->find()
            ->select([
                'Students.name',
                'Students.id',
                'total_profit_loss' => $studentsTable->find()->func()->sum('Studies.profit_loss'),
                'total_studies' => $studentsTable->find()->func()->count('Studies.id'),
                'total_wins' => $studentsTable->find()->func()->sum('Studies.wins'),
                'total_losses' => $studentsTable->find()->func()->sum('Studies.losses')
            ])
            ->leftJoinWith('Studies')
            ->innerJoinWith('Users', function ($q) {
                return $q->where(['Users.active' => 1]);
            })
            ->groupBy(['Students.id', 'Students.name'])
            ->orderByDesc('total_profit_loss')
            ->limit(5)
            ->toArray();

        // Estudantes com pior performance
        $worstStudents = $studentsTable->find()
            ->select([
                'Students.name',
                'Students.id',
                'total_profit_loss' => $studentsTable->find()->func()->sum('Studies.profit_loss'),
                'total_studies' => $studentsTable->find()->func()->count('Studies.id'),
                'total_wins' => $studentsTable->find()->func()->sum('Studies.wins'),
                'total_losses' => $studentsTable->find()->func()->sum('Studies.losses')
            ])
            ->leftJoinWith('Studies')
            ->innerJoinWith('Users', function ($q) {
                return $q->where(['Users.active' => 1]);
            })
            ->groupBy(['Students.id', 'Students.name'])
            ->orderByAsc('total_profit_loss')
            ->limit(5)
            ->toArray();

        // Atividade recente (últimos 10 estudos)
        $recentActivity = $studiesTable->find()
            ->contain(['Students'])
            ->orderByDesc('Studies.study_date')
            ->orderByDesc('Studies.created')
            ->limit(10)
            ->toArray();

        // Dados para gráficos - estudos por mês
        $monthlyData = $studiesTable->find()
            ->select([
                'year' => 'YEAR(Studies.study_date)',
                'month' => 'MONTH(Studies.study_date)',
                'total_studies' => 'COUNT(*)',
                'total_wins' => 'SUM(Studies.wins)',
                'total_losses' => 'SUM(Studies.losses)',
                'total_profit_loss' => 'SUM(Studies.profit_loss)'
            ])
            ->where([
                'Studies.study_date >=' => new \DateTime('-12 months')
            ])
            ->groupBy(['YEAR(Studies.study_date)', 'MONTH(Studies.study_date)'])
            ->orderByAsc('year')
            ->orderByAsc('month')
            ->toArray();

        // Pass all data to the view
        $this->set(compact(
            'totalStudents',
            'activeStudents',
            'totalStudies',
            'totalTrades',
            'totalWins',
            'totalLosses',
            'totalProfitLoss',
            'overallWinRate',
            'topStudents',
            'worstStudents',
            'recentActivity',
            'monthlyData'
        ));
    }
}
