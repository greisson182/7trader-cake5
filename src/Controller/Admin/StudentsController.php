<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Cake\Datasource\Exception\RecordNotFoundException;
use Exception;

class StudentsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Flash');
    }

    public function beforeFilter(EventInterface $event): ?Response
    {
        parent::beforeFilter($event);

        // Skip authentication for AJAX endpoints that handle their own authentication
        $action = $this->request->getParam('action');
        if (in_array($action, ['get_filtered_stats_ajax', 'get_calendar_data_ajax'])) {
            return null;
        }

        $this->checkSession();

        // Verificar se é admin ou estudante
        if (!$this->isAdmin() && !$this->isStudent()) {
            $this->Flash->error(__('Acesso negado.'));
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

        return null;
    }

    public function index()
    {
        try {
            $studentsTable = $this->fetchTable('Students');

            // Se for estudante, redirecionar para seu próprio dashboard
            if ($this->isStudent()) {
                $currentStudentId = $this->getCurrentStudentId();
                return $this->redirect(['action' => 'dashboard', $currentStudentId]);
            }

            // Buscar estudantes com dados do usuário associado
            $students = $studentsTable->find()
                ->contain(['Users'])
                ->where(['Users.active' => 1])
                ->orderBy(['Students.name' => 'ASC'])
                ->toArray();

            $this->set(compact('students'));
        } catch (Exception $e) {
            $this->Flash->error(__('Erro ao carregar estudantes: ' . $e->getMessage()));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
    }

    public function view(int $id)
    {
        try {
            $studentsTable = $this->fetchTable('Students');

            // Verificar se estudante pode acessar este registro
            if ($this->isStudent()) {
                $currentStudentId = $this->getCurrentStudentId();
                if ($currentStudentId != $id) {
                    $this->Flash->error(__('Acesso negado. Você só pode visualizar seus próprios dados.'));
                    return $this->redirect(['action' => 'index']);
                }
            }

            $student = $studentsTable->find()
                ->contain(['Users'])
                ->where(['Students.id' => $id])
                ->first();

            if (!$student) {
                throw new RecordNotFoundException(__('Estudante não encontrado.'));
            }

            $this->set(compact('student'));
        } catch (RecordNotFoundException $e) {
            $this->Flash->error($e->getMessage());
            return $this->redirect(['action' => 'index']);
        } catch (Exception $e) {
            $this->Flash->error(__('Erro ao carregar estudante: ' . $e->getMessage()));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function add()
    {
        $studentsTable = $this->fetchTable('Students');
        $usersTable = $this->fetchTable('Users');

        $student = $studentsTable->newEmptyEntity();
        $user = $usersTable->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Validação básica
            if (empty($data['name']) || empty($data['username']) || empty($data['email']) || empty($data['password'])) {
                $this->Flash->error(__('Todos os campos são obrigatórios.'));
                $this->set(compact('student', 'user'));
            }

            // Validação de email
            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->Flash->error(__('Email inválido.'));
                $this->set(compact('student', 'user'));
            }

            try {
                // Validar dados únicos
                $existingUser = $usersTable->find()
                    ->where([
                        'OR' => [
                            ['email' => $data['email']],
                            ['username' => $data['username']]
                        ]
                    ])
                    ->first();

                if ($existingUser) {
                    if ($existingUser->email === $data['email']) {
                        $this->Flash->error(__('Este email já está em uso.'));
                    } else {
                        $this->Flash->error(__('Este nome de usuário já está em uso.'));
                    }
                    $this->set(compact('student', 'user'));
                }

                // Usar transação
                $connection = $studentsTable->getConnection();
                $connection->transactional(function () use ($data, $usersTable, $studentsTable, &$user, &$student) {
                    // Criar usuário
                    $userData = [
                        'username' => $data['username'],
                        'email' => $data['email'],
                        'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                        'role' => 'student',
                        'active' => 1
                    ];

                    $user = $usersTable->patchEntity($user, $userData, [
                        'validate' => 'default',
                        'fieldList' => ['username', 'email', 'password', 'role', 'active']
                    ]);

                    if ($user->hasErrors()) {
                        $errors = [];
                        foreach ($user->getErrors() as $field => $error) {
                            $errors[] = is_array($error) ? implode(', ', $error) : $error;
                        }
                        throw new Exception(__('Erro de validação: ' . implode(', ', $errors)));
                    }

                    if (!$usersTable->save($user)) {
                        throw new Exception(__('Erro ao criar usuário.'));
                    }

                    // Criar estudante
                    $studentData = [
                        'name' => $data['name'],
                        'user_id' => $user->id
                    ];

                    $student = $studentsTable->patchEntity($student, $studentData, [
                        'validate' => 'default',
                        'fieldList' => ['name', 'user_id']
                    ]);

                    if ($student->hasErrors()) {
                        $errors = [];
                        foreach ($student->getErrors() as $field => $error) {
                            $errors[] = is_array($error) ? implode(', ', $error) : $error;
                        }
                        throw new Exception(__('Erro de validação: ' . implode(', ', $errors)));
                    }

                    if (!$studentsTable->save($student)) {
                        throw new Exception(__('Erro ao criar estudante.'));
                    }

                    // Atualizar student_id no usuário
                    $user->student_id = $student->id;
                    if (!$usersTable->save($user)) {
                        throw new Exception(__('Erro ao atualizar referência do estudante.'));
                    }
                });

                $this->Flash->success(__('Estudante criado com sucesso.'));
                return $this->redirect(['action' => 'index']);
            } catch (Exception $e) {
                $this->Flash->error(__('Erro ao criar estudante: ' . $e->getMessage()));
            }
        }

        $this->set(compact('student', 'user'));
    }

    public function edit(int $id)
    {
        $studentsTable = $this->fetchTable('Students');
        $usersTable = $this->fetchTable('Users');

        try {
            $student = $studentsTable->find()
                ->contain(['Users'])
                ->where(['Students.id' => $id])
                ->first();

            if (!$student) {
                throw new RecordNotFoundException(__('Estudante não encontrado.'));
            }

            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();

                // Verificar dados únicos (excluindo o usuário atual)
                $existingUser = $usersTable->find()
                    ->where([
                        'OR' => [
                            ['email' => $data['email']],
                            ['username' => $data['username']]
                        ],
                        'id !=' => $student->user->id
                    ])
                    ->first();

                if ($existingUser) {
                    if ($existingUser->email === $data['email']) {
                        $this->Flash->error(__('Este email já está em uso.'));
                    } else {
                        $this->Flash->error(__('Este nome de usuário já está em uso.'));
                    }
                    $this->set(compact('student'));
                }

                // Usar transação
                $connection = $studentsTable->getConnection();
                $connection->transactional(function () use ($data, $usersTable, $studentsTable, $student) {
                    // Atualizar estudante
                    $studentData = [
                        'name' => $data['name']
                    ];
                    $student = $studentsTable->patchEntity($student, $studentData);
                    if (!$studentsTable->save($student)) {
                        throw new Exception(__('Erro ao atualizar estudante.'));
                    }

                    // Atualizar usuário
                    $userData = [
                        'username' => $data['username'],
                        'email' => $data['email'],
                        'active' => isset($data['active']) ? 1 : 0
                    ];

                    // Atualizar senha se fornecida
                    if (!empty($data['password'])) {
                        $userData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
                    }

                    $student->user = $usersTable->patchEntity($student->user, $userData);
                    if (!$usersTable->save($student->user)) {
                        throw new Exception(__('Erro ao atualizar usuário.'));
                    }
                });

                $this->Flash->success(__('Estudante atualizado com sucesso.'));
                return $this->redirect(['action' => 'index']);
            }

            $this->set(compact('student'));
        } catch (RecordNotFoundException $e) {
            $this->Flash->error($e->getMessage());
            return $this->redirect(['action' => 'index']);
        } catch (Exception $e) {
            $this->Flash->error(__('Erro ao atualizar estudante: ' . $e->getMessage()));
            $this->set(compact('student'));
        }
    }

    public function delete(int $id): Response
    {
        $this->request->allowMethod(['post', 'delete']);

        $studentsTable = $this->fetchTable('Students');
        $usersTable = $this->fetchTable('Users');
        $studiesTable = $this->fetchTable('Studies');

        try {
            $student = $studentsTable->find()
                ->contain(['Users'])
                ->where(['Students.id' => $id])
                ->first();

            if (!$student) {
                throw new RecordNotFoundException(__('Estudante não encontrado.'));
            }

            // Verificar se há estudos associados
            $studiesCount = $studiesTable->find()
                ->where(['student_id' => $id])
                ->count();

            if ($studiesCount > 0) {
                $this->Flash->error(__('Não é possível excluir este estudante pois ele possui estudos associados.'));
                return $this->redirect(['action' => 'index']);
            }

            // Usar transação para deletar estudante e usuário
            $connection = $studentsTable->getConnection();
            $connection->transactional(function () use ($studentsTable, $usersTable, $student) {
                // Deletar usuário associado
                if ($student->user) {
                    if (!$usersTable->delete($student->user)) {
                        throw new Exception(__('Erro ao deletar usuário associado.'));
                    }
                }

                // Deletar estudante
                if (!$studentsTable->delete($student)) {
                    throw new Exception(__('Erro ao deletar estudante.'));
                }
            });

            $this->Flash->success(__('Estudante deletado com sucesso.'));
            return $this->redirect(['action' => 'index']);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error($e->getMessage());
            return $this->redirect(['action' => 'index']);
        } catch (Exception $e) {
            $this->Flash->error(__('Erro ao deletar estudante: ' . $e->getMessage()));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function metrics()
    {
        try {
            $studentsTable = $this->fetchTable('Students');
            $studiesTable = $this->fetchTable('Studies');
            $usersTable = $this->fetchTable('Users');

            // Se for estudante, mostrar apenas suas métricas
            if ($this->isStudent()) {
                $currentStudentId = $this->getCurrentStudentId();
                return $this->redirect(['action' => 'dashboard', $currentStudentId]);
            }

            // Métricas gerais
            $totalStudents = $studentsTable->find()
                ->innerJoinWith('Users', function ($q) {
                    return $q->where(['Users.active' => 1]);
                })
                ->count();

            $activeStudents = $studentsTable->find()
                ->innerJoinWith('Users', function ($q) {
                    return $q->where(['Users.active' => 1]);
                })
                ->count();

            $totalStudies = $studiesTable->find()->count();

            // Estudantes com mais estudos
            $topStudentsByStudies = $studentsTable->find()
                ->select([
                    'Students.id',
                    'Students.name',
                    'total_studies' => $studentsTable->find()->func()->count('Studies.id')
                ])
                ->leftJoinWith('Studies')
                ->innerJoinWith('Users', function ($q) {
                    return $q->where(['Users.active' => 1]);
                })
                ->groupBy(['Students.id', 'Students.name'])
                ->orderByDesc('total_studies')
                ->limit(10)
                ->toArray();

            // Estudantes com melhor performance (profit/loss)
            $topStudentsByProfit = $studentsTable->find()
                ->select([
                    'Students.id',
                    'Students.name',
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
                ->limit(10)
                ->toArray();

            // Estudos recentes
            $recentStudies = $studiesTable->find()
                ->contain(['Students'])
                ->orderByDesc('Studies.study_date')
                ->limit(20)
                ->toArray();

            // Estatísticas gerais dos estudos
            $overallStatsQuery = $studiesTable->find()
                ->select([
                    'total_studies' => $studiesTable->find()->func()->count('*'),
                    'total_wins' => $studiesTable->find()->func()->sum('wins'),
                    'total_losses' => $studiesTable->find()->func()->sum('losses'),
                    'total_profit_loss' => $studiesTable->find()->func()->sum('profit_loss'),
                    'avg_profit_loss' => $studiesTable->find()->func()->avg('profit_loss')
                ])
                ->first();

            $overallStats = $overallStatsQuery ? $overallStatsQuery->toArray() : [
                'total_studies' => 0,
                'total_wins' => 0,
                'total_losses' => 0,
                'total_profit_loss' => 0,
                'avg_profit_loss' => 0
            ];

            // Calcular métricas adicionais
            $overallStats['total_trades'] = $overallStats['total_wins'] + $overallStats['total_losses'];
            $overallStats['overall_win_rate'] = $overallStats['total_trades'] > 0
                ? round(($overallStats['total_wins'] / $overallStats['total_trades']) * 100, 2)
                : 0;

            // Data atual para contexto
            $currentDate = date('Y-m-d');
            $currentYear = date('Y');
            $currentMonth = date('m');

            $this->set(compact(
                'totalStudents',
                'activeStudents',
                'totalStudies',
                'topStudentsByStudies',
                'topStudentsByProfit',
                'recentStudies',
                'overallStats',
                'currentDate',
                'currentYear',
                'currentMonth'
            ));
        } catch (Exception $e) {
            $this->Flash->error(__('Erro ao carregar métricas: ' . $e->getMessage()));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
    }

    public function dashboard($id = null)
    {
        try {
            // Se não foi passado ID, pegar o estudante atual (se for estudante logado)
            if (!$id) {

                $currentUser = $this->getCurrentUser();
                if ($currentUser && $currentUser->role === 'student') {
                    $id = $currentUser->student_id;
                } else {
                    $this->Flash->error(__('Por favor, selecione um estudante para visualizar o dashboard.', 'error'));
                    return $this->redirect(['action' => 'index']);
                }
            }


            // Carregar os models necessários
            $studentsTable = $this->fetchTable('Students');
            $studiesTable = $this->fetchTable('Studies');
            $marketsTable = $this->fetchTable('Markets');

            // Buscar estudante com dados do usuário associado usando CakePHP ORM
            $student = $studentsTable->find()
                ->contain(['Users'])
                ->where(['Students.id' => $id])
                ->first();

            if (!$student) {
                $this->Flash->error(__('Estudante não encontrado.', 'error'));
                return $this->redirect(['action' => 'index']);
            }


            // Buscar mercados ativos para o filtro
            $markets = $marketsTable->find()
                ->where(['active' => 1])
                ->orderBy(['name' => 'ASC'])
                ->toArray();


            // Obter ano selecionado do parâmetro GET
            $selectedYear = $this->request->getQuery('year', date('Y'));

            // Buscar dados dos estudos agrupados por mês usando CakePHP ORM
            $monthlyData = $studiesTable->find()
                ->select([
                    'year' => 'YEAR(study_date)',
                    'month' => 'MONTH(study_date)',
                    'month_name' => 'MONTHNAME(study_date)',
                    'total_studies' => 'COUNT(*)',
                    'total_wins' => 'SUM(wins)',
                    'total_losses' => 'SUM(losses)',
                    'total_trades' => 'SUM(wins + losses)',
                    'avg_win_rate' => 'ROUND(AVG(CASE WHEN (wins + losses) > 0 THEN (wins / (wins + losses)) * 100 ELSE 0 END), 2)',
                    'total_profit_loss' => 'SUM(profit_loss)',
                    'first_study' => 'MIN(study_date)',
                    'last_study' => 'MAX(study_date)',
                    'market_ids' => 'GROUP_CONCAT(DISTINCT market_id)',
                    'account_ids' => 'GROUP_CONCAT(DISTINCT Studies.account_id)',
                    'account_names' => 'GROUP_CONCAT(DISTINCT Accounts.name)'
                ])
                ->contain(['Accounts'])
                ->where([
                    'student_id' => $id,
                    'YEAR(study_date)' => $selectedYear
                ])
                ->groupBy(['YEAR(study_date)', 'MONTH(study_date)'])
                ->orderBy(['year' => 'DESC', 'month' => 'DESC'])
                ->toArray();

            // Calcular estatísticas gerais usando CakePHP ORM
            $overallStatsQuery = $studiesTable->find()
                ->select([
                    'total_studies' => 'COUNT(*)',
                    'total_wins' => 'SUM(wins)',
                    'total_losses' => 'SUM(losses)',
                    'total_profit_loss' => 'SUM(profit_loss)',
                    'first_study_date' => 'MIN(study_date)',
                    'last_study_date' => 'MAX(study_date)'
                ])
                ->where([
                    'student_id' => $id,
                    'YEAR(study_date)' => $selectedYear
                ])
                ->first();

            $overallStats = $overallStatsQuery ? $overallStatsQuery->toArray() : [
                'total_studies' => 0,
                'total_wins' => 0,
                'total_losses' => 0,
                'total_profit_loss' => 0,
                'first_study_date' => null,
                'last_study_date' => null
            ];

            // Calcular métricas adicionais
            $overallStats['total_trades'] = $overallStats['total_wins'] + $overallStats['total_losses'];
            $overallStats['overall_win_rate'] = $overallStats['total_trades'] > 0
                ? round(($overallStats['total_wins'] / $overallStats['total_trades']) * 100, 2)
                : 0;

            // Buscar dados para gráfico usando CakePHP ORM
            $chartDataRaw = $studiesTable->find()
                ->select([
                    'month_key' => "DATE_FORMAT(Studies.study_date, '%Y-%m')",
                    'year' => 'YEAR(Studies.study_date)',
                    'month' => 'MONTH(Studies.study_date)',
                    'profit_loss' => 'SUM(Studies.profit_loss)',
                    'wins' => 'SUM(Studies.wins)',
                    'losses' => 'SUM(Studies.losses)',
                    'Studies.market_id',
                    'Studies.account_id',
                    'Markets.currency',
                    'market_name' => 'Markets.name',
                    'market_code' => 'Markets.code',
                    'account_name' => 'Accounts.name'
                ])
                ->contain(['Markets', 'Accounts'])
                ->where([
                    'Studies.student_id' => $id,
                    'YEAR(Studies.study_date)' => $selectedYear
                ])
                ->groupBy(['YEAR(Studies.study_date)', 'MONTH(Studies.study_date)', 'Studies.market_id', 'Studies.account_id'])
                ->orderBy(['year' => 'ASC', 'month' => 'ASC', 'Markets.name' => 'ASC'])
                ->toArray();

            // Preparar dados para o gráfico - agregados por mês
            $chartDataByMonth = [];
            foreach ($chartDataRaw as $data) {
                $monthKey = $data['month_key'];
                if (!isset($chartDataByMonth[$monthKey])) {
                    $chartDataByMonth[$monthKey] = [
                        'year' => $data['year'],
                        'month' => $data['month'],
                        'profit_loss' => 0,
                        'wins' => 0,
                        'losses' => 0,
                        'markets' => []
                    ];
                }
                $chartDataByMonth[$monthKey]['profit_loss'] += $data['profit_loss'];
                $chartDataByMonth[$monthKey]['wins'] += $data['wins'];
                $chartDataByMonth[$monthKey]['losses'] += $data['losses'];
                $chartDataByMonth[$monthKey]['markets'][] = [
                    'market_id' => $data['market_id'],
                    'account_id' => $data['account_id'],
                    'currency' => $data['currency'],
                    'market_name' => $data['market_name'],
                    'market_code' => $data['market_code'],
                    'account_name' => $data['account_name'],
                    'profit_loss' => $data['profit_loss'],
                    'wins' => $data['wins'],
                    'losses' => $data['losses']
                ];
            }

            // Top 5 estudantes por profit/loss (para comparação)
            $topStudents = $studentsTable->find()
                ->select([
                    'Students.name',
                    'Students.id',
                    'total_profit_loss' => $studentsTable->find()->func()->sum('Studies.profit_loss'),
                    'total_studies' => $studentsTable->find()->func()->count('Studies.id'),
                    'total_wins' => $studentsTable->find()->func()->sum('Studies.wins'),
                    'total_losses' => $studentsTable->find()->func()->sum('Studies.losses')
                ])
                ->leftJoinWith('Studies', function ($q) use ($selectedYear) {
                    return $q->where(['YEAR(Studies.study_date)' => $selectedYear]);
                })
                ->innerJoinWith('Users', function ($q) {
                    return $q->where(['Users.active' => 1]);
                })
                ->groupBy(['Students.id', 'Students.name'])
                ->orderByDesc('total_profit_loss')
                ->limit(5)
                ->toArray();

            // Estudantes com pior performance (para comparação)
            $worstStudents = $studentsTable->find()
                ->select([
                    'Students.name',
                    'Students.id',
                    'total_profit_loss' => $studentsTable->find()->func()->sum('Studies.profit_loss'),
                    'total_studies' => $studentsTable->find()->func()->count('Studies.id'),
                    'total_wins' => $studentsTable->find()->func()->sum('Studies.wins'),
                    'total_losses' => $studentsTable->find()->func()->sum('Studies.losses')
                ])
                ->leftJoinWith('Studies', function ($q) use ($selectedYear) {
                    return $q->where(['YEAR(Studies.study_date)' => $selectedYear]);
                })
                ->innerJoinWith('Users', function ($q) {
                    return $q->where(['Users.active' => 1]);
                })
                ->groupBy(['Students.id', 'Students.name'])
                ->orderByAsc('total_profit_loss')
                ->limit(5)
                ->toArray();

            // Preparar dados para o gráfico
            $chartLabels = [];
            $chartProfitLoss = [];
            $chartWinRate = [];
            $chartDataDetailed = [];

            foreach ($chartDataByMonth as $monthKey => $data) {
                $chartLabels[] = date('M Y', mktime(0, 0, 0, $data['month'], 1, $data['year']));
                $chartProfitLoss[] = (float)$data['profit_loss'];
                $totalTrades = $data['wins'] + $data['losses'];
                $chartWinRate[] = $totalTrades > 0 ? round(($data['wins'] / $totalTrades) * 100, 2) : 0;
                $chartDataDetailed[] = [
                    'month_key' => $monthKey,
                    'year' => $data['year'],
                    'month' => $data['month'],
                    'markets' => $data['markets']
                ];
            }

            // Buscar dados do calendário para o mês atual
            $currentMonth = (int)$this->request->getQuery('month', date('n'));
            $currentYear = (int)$this->request->getQuery('year', date('Y'));
            
            $calendarData = $this->get_calendar_data($id, $currentYear, $currentMonth);

            // Buscar contas ativas para o filtro
            $accountsTable = $this->fetchTable('Accounts');
            $accounts = $accountsTable->find()
                ->where(['active' => 1])
                ->orderBy(['name' => 'ASC'])
                ->toArray();

            $this->set(compact(
                'student',
                'monthlyData',
                'overallStats',
                'chartLabels',
                'chartDataByMonth',
                'chartProfitLoss',
                'chartWinRate',
                'chartDataDetailed',
                'markets',
                'accounts',
                'selectedYear',
                'topStudents',
                'worstStudents',
                'calendarData',
                'currentMonth',
                'currentYear'
            ));
        } catch (\Exception $e) {
            $this->Flash->error(__('Erro ao carregar dashboard: ' . $e->getMessage(), 'error'));
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Método para buscar dados do calendário
     */
    private function get_calendar_data(int $studentId, int $year, int $month, int $accountId = 0, int $marketId = 0): array
    {
        $studiesTable = $this->fetchTable('Studies');
        
        // Buscar todos os estudos do mês
        $conditions = [
            'student_id' => $studentId,
            'YEAR(study_date)' => $year,
            'MONTH(study_date)' => $month
        ];
        
        // Adicionar filtro por conta se especificado
        if ($accountId > 0) {
            $conditions['account_id'] = $accountId;
        }
        
        // Adicionar filtro por mercado se especificado
        if ($marketId > 0) {
            $conditions['market_id'] = $marketId;
        }
        
        $studies = $studiesTable->find()
            ->where($conditions)
            ->orderBy(['study_date' => 'ASC'])
            ->toArray();

        // Debug: log the query and results
        error_log("Calendar data query conditions: " . json_encode($conditions));
        error_log("Studies found: " . count($studies));

        // Organizar dados por dia
        $dailyData = [];
        foreach ($studies as $study) {
            $day = (int)$study->study_date->format('j');
            if (!isset($dailyData[$day])) {
                $dailyData[$day] = [
                    'profit_loss' => 0,
                    'wins' => 0,
                    'losses' => 0,
                    'trades' => 0,
                    'studies_count' => 0
                ];
            }
            
            $dailyData[$day]['profit_loss'] += $study->profit_loss;
            $dailyData[$day]['wins'] += $study->wins;
            $dailyData[$day]['losses'] += $study->losses;
            $dailyData[$day]['trades'] += ($study->wins + $study->losses);
            $dailyData[$day]['studies_count']++;
        }

        // Calcular métricas adicionais e converter para array
        $dailyArray = [];
        foreach ($dailyData as $day => &$data) {
            $data['day'] = $day;
            $data['win_rate'] = $data['trades'] > 0 ? round(($data['wins'] / $data['trades']) * 100, 2) : 0;
            $data['r_risk_return'] = $data['losses'] > 0 ? round($data['wins'] / $data['losses'], 2) : ($data['wins'] > 0 ? 999 : 0);
            $data['total_trades'] = $data['trades']; // Alias for JavaScript compatibility
            $data['r_risk'] = $data['r_risk_return']; // Alias for JavaScript compatibility
            $dailyArray[] = $data;
        }

        // Calcular resumo semanal
        $weeklyData = $this->calculateWeeklyData($dailyData, $year, $month);

        return [
            'daily' => $dailyArray,
            'weekly' => $weeklyData,
            'month' => $month,
            'year' => $year
        ];
    }

    /**
     * Calcular dados semanais
     */
    private function calculateWeeklyData(array $dailyData, int $year, int $month): array
    {
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $firstDayOfWeek = date('w', mktime(0, 0, 0, $month, 1, $year));
        
        $weeks = [];
        $currentWeek = 1;
        $weekStart = 1 - $firstDayOfWeek;
        
        for ($week = 1; $week <= 6; $week++) {
            $weekEnd = $weekStart + 6;
            $weekData = [
                'week' => $week,
                'profit_loss' => 0,
                'trades' => 0,
                'wins' => 0,
                'losses' => 0,
                'days_count' => 0,
                'active_days' => 0
            ];
            
            for ($day = max(1, $weekStart); $day <= min($daysInMonth, $weekEnd); $day++) {
                $weekData['days_count']++;
                if (isset($dailyData[$day])) {
                    $weekData['profit_loss'] += $dailyData[$day]['profit_loss'];
                    $weekData['trades'] += $dailyData[$day]['trades'];
                    $weekData['wins'] += $dailyData[$day]['wins'];
                    $weekData['losses'] += $dailyData[$day]['losses'];
                    $weekData['active_days']++;
                }
            }
            
            $weekData['win_rate'] = $weekData['trades'] > 0 ? round(($weekData['wins'] / $weekData['trades']) * 100, 2) : 0;
            $weekData['days'] = $weekData['active_days']; // Alias for JavaScript compatibility
            
            if ($weekData['days_count'] > 0) {
                $weeks[] = $weekData;
            }
            
            $weekStart = $weekEnd + 1;
            if ($weekStart > $daysInMonth) break;
        }
        
        return $weeks;
    }

    /**
     * AJAX endpoint para buscar dados do calendário
     */
    public function get_calendar_data_ajax($studentId = null, $year = null, $month = null)
    {
        $this->request->allowMethod(['get']);
        $this->viewBuilder()->setClassName('Json');
        
        // Obter ID do estudante
        if (!$studentId) {
            $currentUser = $this->getCurrentUser();
            if ($currentUser && $currentUser->role === 'student') {
                $studentId = $currentUser->student_id;
            }
        }
        
        if (!$studentId) {
            $this->set([
                'success' => false,
                'message' => 'ID do estudante não fornecido'
            ]);
            $this->viewBuilder()->setOption('serialize', ['success', 'message']);
            return;
        }
        
        // Usar ano e mês atuais se não fornecidos
        $year = $year ?: date('Y');
        $month = $month ?: date('n');
        
        try {
            // Obter parâmetros de filtro
            $accountId = $this->request->getQuery('account_id', 0);
            $marketId = $this->request->getQuery('market_id', 0);
            
            // Chamar o método privado para buscar os dados
            $calendarData = $this->get_calendar_data(
                (int)$studentId,
                (int)$year,
                (int)$month,
                (int)$accountId,
                (int)$marketId
            );
            
            $this->set([
                'success' => true,
                'data' => $calendarData
            ]);
            $this->viewBuilder()->setOption('serialize', ['success', 'data']);
        } catch (\Exception $e) {
            $this->set([
                'success' => false,
                'message' => 'Erro ao buscar dados do calendário: ' . $e->getMessage()
            ]);
            $this->viewBuilder()->setOption('serialize', ['success', 'message']);
        }
    }

    /**
     * AJAX endpoint para buscar estatísticas filtradas
     */
    public function get_filtered_stats_ajax($studentId = null)
    {
        $this->request->allowMethod(['get']);
        $this->viewBuilder()->setClassName('Json');
        
        // Obter ID do estudante
        if (!$studentId) {
            $currentUser = $this->getCurrentUser();
            if ($currentUser && $currentUser->role === 'student') {
                $studentId = $currentUser->student_id;
            }
        }
        
        if (!$studentId) {
            $this->set([
                'success' => false,
                'message' => 'ID do estudante não fornecido'
            ]);
            $this->viewBuilder()->setOption('serialize', ['success', 'message']);
            return;
        }
        
        try {
            // Obter parâmetros de filtro
            $accountId = $this->request->getQuery('account_id');
            $marketId = $this->request->getQuery('market_id');
            $selectedYear = $this->request->getQuery('year', date('Y'));
            
            $studiesTable = $this->fetchTable('Studies');
            
            // Construir query com filtros
            $conditions = [
                'Studies.student_id' => $studentId,
                'YEAR(Studies.study_date)' => $selectedYear
            ];
            
            if ($accountId) {
                $conditions['Studies.account_id'] = $accountId;
            }
            
            if ($marketId) {
                $conditions['Studies.market_id'] = $marketId;
            }
            
            // Buscar estatísticas filtradas
            $overallStatsQuery = $studiesTable->find()
                ->select([
                    'total_studies' => 'COUNT(*)',
                    'total_wins' => 'SUM(wins)',
                    'total_losses' => 'SUM(losses)',
                    'total_profit_loss' => 'SUM(profit_loss)',
                    'avg_profit_loss' => 'AVG(profit_loss)',
                    'first_study_date' => 'MIN(study_date)',
                    'last_study_date' => 'MAX(study_date)'
                ])
                ->where($conditions)
                ->first();
            
            $overallStats = $overallStatsQuery ? $overallStatsQuery->toArray() : [
                'total_studies' => 0,
                'total_wins' => 0,
                'total_losses' => 0,
                'total_profit_loss' => 0,
                'avg_profit_loss' => 0,
                'first_study_date' => null,
                'last_study_date' => null
            ];
            
            // Calcular métricas adicionais
            $overallStats['total_trades'] = $overallStats['total_wins'] + $overallStats['total_losses'];
            $overallStats['overall_win_rate'] = $overallStats['total_trades'] > 0
                ? round(($overallStats['total_wins'] / $overallStats['total_trades']) * 100, 2)
                : 0;
            
            $this->set([
                'success' => true,
                'data' => $overallStats
            ]);
            $this->viewBuilder()->setOption('serialize', ['success', 'data']);
        } catch (\Exception $e) {
            $this->set([
                'success' => false,
                'message' => 'Erro ao buscar estatísticas: ' . $e->getMessage()
            ]);
            $this->viewBuilder()->setOption('serialize', ['success', 'message']);
        }
    }

    public function monthlyStudies(?int $studentId = null, ?int $year = null, ?int $month = null)
    {
        try {

            $studentsTable = $this->fetchTable('Students');
            $studiesTable = $this->fetchTable('Studies');

            // Verificar se estudante pode acessar este registro
            if ($this->isStudent()) {
                $currentStudentId = $this->getCurrentStudentId();
                if ($currentStudentId != $studentId) {
                    $this->Flash->error(__('Acesso negado. Você só pode visualizar seus próprios dados.'));
                    return $this->redirect(['action' => 'index']);
                }
            }

            // Buscar dados do estudante
            $student = $studentsTable->find()
                ->contain(['Users'])
                ->where(['Students.id' => $studentId])
                ->first();

            if (!$student) {
                throw new RecordNotFoundException(__('Estudante não encontrado.'));
            }

            // Buscar estudos do mês específico
            $studies = $studiesTable->find()
                ->where([
                    'student_id' => $studentId,
                    'YEAR(study_date)' => $year,
                    'MONTH(study_date)' => $month
                ])
                ->orderByDesc('study_date')
                ->toArray();

            // Calcular estatísticas do mês
            $totalStudies = count($studies);
            $totalWins = array_sum(array_column($studies, 'wins'));
            $totalLosses = array_sum(array_column($studies, 'losses'));
            $totalTrades = $totalWins + $totalLosses;
            $totalProfitLoss = array_sum(array_column($studies, 'profit_loss'));
            $winRate = $totalTrades > 0 ? ($totalWins / $totalTrades) * 100 : 0;

            // Nome do mês em português
            $monthNames = [
                1 => 'Janeiro',
                2 => 'Fevereiro',
                3 => 'Março',
                4 => 'Abril',
                5 => 'Maio',
                6 => 'Junho',
                7 => 'Julho',
                8 => 'Agosto',
                9 => 'Setembro',
                10 => 'Outubro',
                11 => 'Novembro',
                12 => 'Dezembro'
            ];
            $monthName = $monthNames[(int)$month] ?? 'Mês';

            $this->set(compact(
                'student',
                'studies',
                'year',
                'month',
                'monthName',
                'totalStudies',
                'totalWins',
                'totalLosses',
                'totalTrades',
                'totalProfitLoss',
                'winRate'
            ));
        } catch (RecordNotFoundException $e) {
            $this->Flash->error($e->getMessage());
            return $this->redirect(['action' => 'index']);
        } catch (Exception $e) {
            $this->Flash->error(__('Erro ao carregar estudos mensais: ' . $e->getMessage()));
            return $this->redirect(['action' => 'index']);
        }
    }
}
