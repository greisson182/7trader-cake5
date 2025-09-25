<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\EventInterface;

class StudentsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();

        $this->Users = $this->fetchTable('Users');
        $this->Markets = $this->fetchTable('Markets');
        $this->Studies = $this->fetchTable('Studies');
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        // Comentar temporariamente para teste
        // $this->checkSession();
    }

    public function index()
    {
        $query = $this->Students->find();
        $students = $this->paginate($query);

        $this->set(compact('students'));
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
                    'market_ids' => 'GROUP_CONCAT(DISTINCT market_id)'
                ])
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
                    'Markets.currency',
                    'market_name' => 'Markets.name',
                    'market_code' => 'Markets.code'
                ])
                ->contain(['Markets'])
                ->where([
                    'Studies.student_id' => $id,
                    'YEAR(Studies.study_date)' => $selectedYear
                ])
                ->groupBy(['YEAR(Studies.study_date)', 'MONTH(Studies.study_date)', 'Studies.market_id'])
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
                    'currency' => $data['currency'],
                    'market_name' => $data['market_name'],
                    'market_code' => $data['market_code'],
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
                'selectedYear',
                'topStudents',
                'worstStudents'
            ));
        } catch (\Exception $e) {
            $this->Flash->error(__('Erro ao carregar dashboard: ' . $e->getMessage(), 'error'));
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * View method
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $student = $this->Students->get($id, contain: ['CourseEnrollments', 'StudentProgress', 'Studies', 'Users']);
        $this->set(compact('student'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $student = $this->Students->newEmptyEntity();
        if ($this->request->is('post')) {
            $student = $this->Students->patchEntity($student, $this->request->getData());
            if ($this->Students->save($student)) {
                $this->Flash->success(__('The student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student could not be saved. Please, try again.'));
        }
        $this->set(compact('student'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $student = $this->Students->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $student = $this->Students->patchEntity($student, $this->request->getData());
            if ($this->Students->save($student)) {
                $this->Flash->success(__('The student has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student could not be saved. Please, try again.'));
        }
        $this->set(compact('student'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $student = $this->Students->get($id);
        if ($this->Students->delete($student)) {
            $this->Flash->success(__('The student has been deleted.'));
        } else {
            $this->Flash->error(__('The student could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
