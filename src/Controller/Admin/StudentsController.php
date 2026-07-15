<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Service\StudentService;
use App\Service\SettingsService;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\Datasource\Exception\RecordNotFoundException;
use Exception;

class StudentsController extends AppController
{
    private StudentService $_studentService;
    private SettingsService $_settingsService;

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Flash');
        $this->_studentService = new StudentService();
        $this->_settingsService = new SettingsService();
        $this->checkSession();
    }

    public function beforeFilter(EventInterface $event): ?Response
    {
        parent::beforeFilter($event);

        // Skip authentication for AJAX endpoints that handle their own authentication
        $action = $this->request->getParam('action');
        if (in_array($action, ['get_filtered_stats_ajax', 'get_calendar_data_ajax', 'get_monthly_stats_ajax'])) {
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
            if ($this->isStudent()) {
                $currentStudentId = $this->getCurrentStudentId();
                return $this->redirect(['action' => 'dashboard', $currentStudentId]);
            }

            $students = $this->_studentService->getAllWithUsers();
            $this->set(compact('students'));
        } catch (Exception $e) {
            $this->Flash->error(__('Erro ao carregar estudantes: ' . $e->getMessage()));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
    }

    public function view(int $id)
    {
        try {
            if ($this->isStudent()) {
                $currentStudentId = $this->getCurrentStudentId();
                if ($currentStudentId != $id) {
                    $this->Flash->error(__('Acesso negado. Você só pode visualizar seus próprios dados.'));
                    return $this->redirect(['action' => 'index']);
                }
            }

            $student = $this->_studentService->getWithUser($id);
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

            if (empty($data['name']) || empty($data['username']) || empty($data['email']) || empty($data['password'])) {
                $this->Flash->error(__('Todos os campos são obrigatórios.'));
                $this->set(compact('student', 'user'));
                return null;
            }

            if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $this->Flash->error(__('Email inválido.'));
                $this->set(compact('student', 'user'));
                return null;
            }

            $duplicateMessage = $this->_studentService->findDuplicateUser($data['email'], $data['username']);
            if ($duplicateMessage) {
                $this->Flash->error($duplicateMessage);
                $this->set(compact('student', 'user'));
                return null;
            }

            try {
                $this->_studentService->create($data);

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
        try {
            $student = $this->_studentService->getWithUser($id);

            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();

                $duplicateMessage = $this->_studentService->findDuplicateUser(
                    $data['email'],
                    $data['username'],
                    $student->user->id
                );
                if ($duplicateMessage) {
                    $this->Flash->error($duplicateMessage);
                    $this->set(compact('student'));
                    return null;
                }

                $this->_studentService->update($id, $data);

                $this->Flash->success(__('Estudante atualizado com sucesso.'));
                return $this->redirect(['action' => 'index']);
            }

            $this->set(compact('student'));
        } catch (RecordNotFoundException $e) {
            $this->Flash->error($e->getMessage());
            return $this->redirect(['action' => 'index']);
        } catch (Exception $e) {
            $this->Flash->error(__('Erro ao atualizar estudante: ' . $e->getMessage()));
        }
    }

    public function delete(int $id): ?Response
    {
        $this->request->allowMethod(['post', 'delete']);

        try {
            $this->_studentService->delete($id);

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
            if ($this->isStudent()) {
                $currentStudentId = $this->getCurrentStudentId();
                return $this->redirect(['action' => 'dashboard', $currentStudentId]);
            }

            $metrics = $this->_studentService->getOverviewMetrics();
            $currentDate = date('Y-m-d');
            $currentYear = date('Y');
            $currentMonth = date('m');

            $this->set(array_merge($metrics, compact('currentDate', 'currentYear', 'currentMonth')));
        } catch (Exception $e) {
            $this->Flash->error(__('Erro ao carregar métricas: ' . $e->getMessage()));
            return $this->redirect(['controller' => 'Dashboard', 'action' => 'index']);
        }
    }

    public function dashboard($id = null)
    {
        try {
            if (!$id) {
                $currentUser = $this->getCurrentUser();
                if ($currentUser && $currentUser->role === 'student') {
                    $id = $currentUser->student_id;
                } else {
                    $this->Flash->error(__('Por favor, selecione um estudante para visualizar o dashboard.', 'error'));
                    return $this->redirect(['action' => 'index']);
                }
            }

            $selectedYear = $this->request->getQuery('year', date('Y'));

            $dashboardData = $this->_studentService->getDashboardData((int)$id, $selectedYear);
            $student = $dashboardData['student'];
            $markets = $dashboardData['markets'];
            $monthlyData = $dashboardData['monthlyData'];
            $overallStats = $dashboardData['overallStats'];
            $chartDataByMonth = $dashboardData['chartDataByMonth'];

            $studentsTable = $this->fetchTable('Students');
            $topStudents = $studentsTable->find()
                ->select([
                    'Students.name', 'Students.id',
                    'total_profit_loss' => $studentsTable->find()->func()->sum('Studies.profit_loss'),
                    'total_studies' => $studentsTable->find()->func()->count('Studies.id'),
                    'total_wins' => $studentsTable->find()->func()->sum('Studies.wins'),
                    'total_losses' => $studentsTable->find()->func()->sum('Studies.losses')
                ])
                ->leftJoinWith('Studies', fn($q) => $q->where(['YEAR(Studies.study_date)' => $selectedYear]))
                ->matching('Users', fn($q) => $q->where(['Users.active' => 1]))
                ->groupBy(['Students.id', 'Students.name'])
                ->orderByDesc('total_profit_loss')
                ->limit(5)
                ->toArray();

            $worstStudents = $studentsTable->find()
                ->select([
                    'Students.name', 'Students.id',
                    'total_profit_loss' => $studentsTable->find()->func()->sum('Studies.profit_loss'),
                    'total_studies' => $studentsTable->find()->func()->count('Studies.id'),
                    'total_wins' => $studentsTable->find()->func()->sum('Studies.wins'),
                    'total_losses' => $studentsTable->find()->func()->sum('Studies.losses')
                ])
                ->leftJoinWith('Studies', fn($q) => $q->where(['YEAR(Studies.study_date)' => $selectedYear]))
                ->matching('Users', fn($q) => $q->where(['Users.active' => 1]))
                ->groupBy(['Students.id', 'Students.name'])
                ->orderByAsc('total_profit_loss')
                ->limit(5)
                ->toArray();

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
                    'month_key' => $monthKey, 'year' => $data['year'],
                    'month' => $data['month'], 'markets' => $data['markets']
                ];
            }

            $currentMonth = (int)$this->request->getQuery('month', date('n'));
            $currentYear = (int)$this->request->getQuery('year', date('Y'));
            $calendarData = $this->get_calendar_data((int)$id, $currentYear, $currentMonth);

            $accountsTable = $this->fetchTable('Accounts');
            $accounts = $accountsTable->find()
                ->where(['active' => 1])
                ->orderBy(['name' => 'ASC'])
                ->toArray();

            $this->set(compact(
                'student', 'monthlyData', 'overallStats',
                'chartLabels', 'chartDataByMonth', 'chartProfitLoss',
                'chartWinRate', 'chartDataDetailed', 'markets',
                'accounts', 'selectedYear', 'topStudents',
                'worstStudents', 'calendarData', 'currentMonth', 'currentYear'
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

        // Organizar dados por dia
        $dailyData = [];
        foreach ($studies as $study) {

            // Calcular custo de operação para cada estudo
            $amount_cost = $studiesTable->getCostTrades($study);
            $study['profit_loss'] = $amount_cost + (float)$study['profit_loss'];


            $day = (int)$study->study_date->format('j');
            if (!isset($dailyData[$day])) {
                $dailyData[$day] = [
                    'profit_loss' => 0,
                    'wins' => 0,
                    'losses' => 0,
                    'trades' => 0,
                    'studies_count' => 0,
                    'study_id' => $study->id // Adicionar ID do estudo
                ];
            }

            $dailyData[$day]['profit_loss'] += $study->profit_loss;
            $dailyData[$day]['wins'] += $study->wins;
            $dailyData[$day]['losses'] += $study->losses;
            $dailyData[$day]['trades'] += ($study->wins + $study->losses);
            $dailyData[$day]['studies_count']++;

            // Se há múltiplos estudos no mesmo dia, manter o ID do primeiro
            if (!isset($dailyData[$day]['study_id'])) {
                $dailyData[$day]['study_id'] = $study->id;
            }
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

            // Buscar todos os estudos que atendem aos critérios de filtro
            $studies = $studiesTable->find()
                ->where($conditions)
                ->toArray();

            $overallStats = [
                'total_studies' => 0,
                'total_wins' => 0,
                'total_losses' => 0,
                'total_trades' => 0,
                'total_profit_loss' => 0,
                'avg_profit_loss' => 0,
                'first_study_date' => null,
                'last_study_date' => null
            ];

            foreach ($studies as $key => $study) {
                // Calcular custo de operação para cada estudo
                $amount_cost = $studiesTable->getCostTrades($study);
                $studies[$key]['profit_loss'] = $amount_cost + (float)$study['profit_loss'];

                // Acumular estatísticas
                $overallStats['total_studies']++;
                $overallStats['total_wins'] += $study['wins'];
                $overallStats['total_losses'] += $study['losses'];
                $overallStats['total_trades'] += $study['wins'] + $study['losses'];
                $overallStats['total_profit_loss'] += $studies[$key]['profit_loss'];
                
                // Definir datas de primeiro e último estudo
                if ($overallStats['first_study_date'] === null || $study['study_date'] < $overallStats['first_study_date']) {
                    $overallStats['first_study_date'] = $study['study_date'];
                }
                if ($overallStats['last_study_date'] === null || $study['study_date'] > $overallStats['last_study_date']) {
                    $overallStats['last_study_date'] = $study['study_date'];
                }
            }

            // Calcular média de profit/loss
            if ($overallStats['total_studies'] > 0) {
                $overallStats['avg_profit_loss'] = $overallStats['total_profit_loss'] / $overallStats['total_studies'];
            }

            // Calcular taxa de acerto
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

    /**
     * AJAX endpoint para buscar estatísticas mensais ou anuais
     */
    public function get_monthly_stats_ajax($studentId = null, $year = null, $month = null)
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

        // Usar ano atual se não fornecido
        $year = $year ?: date('Y');
        // Se mês não fornecido, buscar dados do ano inteiro
        $isYearlyData = !$month;
        if (!$isYearlyData) {
            $month = $month ?: date('n');
        }

        try {
            // Obter parâmetros de filtro
            $accountId = $this->request->getQuery('account_id', 0);
            $marketId = $this->request->getQuery('market_id', 0);

            $studiesTable = $this->fetchTable('Studies');

            // Construir query com filtros
            $conditions = [
                'Studies.student_id' => $studentId,
                'YEAR(Studies.study_date)' => $year
            ];

            // Adicionar filtro de mês apenas se fornecido
            if (!$isYearlyData) {
                $conditions['MONTH(Studies.study_date)'] = $month;
            }

            if ($accountId) {
                $conditions['Studies.account_id'] = $accountId;
            }

            if ($marketId) {
                $conditions['Studies.market_id'] = $marketId;
            }

            // Buscar estatísticas
            $studies = $studiesTable->find()
                ->where($conditions)
                ->toArray();


            $monthlyStats = [
                'total_studies' => 0,
                'total_wins' => 0,
                'total_losses' => 0,
                'total_trades' => 0,
                'total_profit_loss' => 0,
                'profit_loss_cost' => 0,
                'amount_cost' => 0
            ];

            foreach ($studies as $key => $study) {
                // Calcular custo de operação para cada estudo
                $amount_cost = $studiesTable->getCostTrades($study);
                $studies[$key]['profit_loss_cost'] = $amount_cost + (float)$study['profit_loss'];

                $studies[$key]['profit_loss'] = $studies[$key]['profit_loss_cost'];

                // Acumular estatísticas
                $monthlyStats['total_studies']++;
                $monthlyStats['total_wins'] += $study['wins'];
                $monthlyStats['total_losses'] += $study['losses'];
                $monthlyStats['total_trades'] += $study['wins'] + $study['losses'];
                $monthlyStats['total_profit_loss'] += $studies[$key]['profit_loss'];
                $monthlyStats['amount_cost'] += $amount_cost;
            }

            // Calcular taxa de acerto
            $totalTrades = $monthlyStats['total_trades'];
            $winRate = $totalTrades > 0 ? round(($monthlyStats['total_wins'] / $totalTrades) * 100, 2) : 0;
            $monthlyStats['win_rate'] = $winRate;

            $this->set([
                'success' => true,
                'data' => $monthlyStats
            ]);
            $this->viewBuilder()->setOption('serialize', ['success', 'data']);
        } catch (\Exception $e) {
            $this->set([
                'success' => false,
                'message' => 'Erro ao buscar estatísticas mensais: ' . $e->getMessage()
            ]);
            $this->viewBuilder()->setOption('serialize', ['success', 'message']);
        }
    }
}
