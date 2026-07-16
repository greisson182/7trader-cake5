<?php

declare(strict_types=1);

namespace App\Service;

use Cake\ORM\TableRegistry;
use Cake\ORM\Table;
use Cake\Datasource\Exception\RecordNotFoundException;
use Exception;

class StudentService
{
    private Table $_studentsTable;
    private Table $_usersTable;
    private Table $_studiesTable;
    private Table $_marketsTable;
    private Table $_mentorshipsTable;
    private Table $_courseEnrollmentsTable;
    private Table $_ordersTable;

    public function __construct()
    {
        $this->_studentsTable = TableRegistry::getTableLocator()->get('Students');
        $this->_usersTable = TableRegistry::getTableLocator()->get('Users');
        $this->_studiesTable = TableRegistry::getTableLocator()->get('Studies');
        $this->_marketsTable = TableRegistry::getTableLocator()->get('Markets');
        $this->_mentorshipsTable = TableRegistry::getTableLocator()->get('Mentorships');
        $this->_courseEnrollmentsTable = TableRegistry::getTableLocator()->get('CourseEnrollments');
        $this->_ordersTable = TableRegistry::getTableLocator()->get('Orders');
    }

    /**
     * Lista de estudantes com dados agregados pra tela de cards do admin:
     * curso(s) em que está inscrito, mentoria comprada, total de estudos
     * registrados e último acesso (via Users.last_login).
     */
    public function getAllWithUsers(): array
    {
        $students = $this->_studentsTable->find()
            ->contain([
                'Users' => function ($q) {
                    return $q->where(['Users.active' => 1, 'Users.status !=' => 'd']);
                },
                'CourseEnrollments' => function ($q) {
                    return $q->contain(['Courses' => function ($q2) {
                        return $q2->select(['id', 'title']);
                    }]);
                },
                'Orders' => function ($q) {
                    return $q->select(['id', 'student_id', 'item_type', 'item_id'])
                        ->where([
                            'Orders.item_type' => 'mentorship',
                            'Orders.status' => 'approved',
                        ]);
                },
            ])
            ->orderBy(['Students.name' => 'ASC'])
            ->toArray();

        if (empty($students)) {
            return $students;
        }

        $studentIds = array_map(fn($student) => $student->id, $students);

        $studyCounts = [];
        $studyCountRows = $this->_studiesTable->find()
            ->select([
                'student_id',
                'total' => $this->_studiesTable->find()->func()->count('*'),
            ])
            ->where(['student_id IN' => $studentIds])
            ->groupBy(['student_id'])
            ->toArray();
        foreach ($studyCountRows as $row) {
            $studyCounts[$row->student_id] = (int)$row->total;
        }

        $mentorshipNames = [];
        foreach ($this->_mentorshipsTable->find()->select(['id', 'name'])->toArray() as $mentorship) {
            $mentorshipNames[$mentorship->id] = $mentorship->name;
        }

        foreach ($students as $student) {
            $student->study_count = $studyCounts[$student->id] ?? 0;

            $student->course_titles = array_map(
                fn($enrollment) => $enrollment->course->title ?? null,
                array_filter($student->course_enrollments ?? [], fn($enrollment) => $enrollment->course !== null)
            );

            $student->mentorship_names = array_values(array_filter(array_map(
                fn($order) => $mentorshipNames[$order->item_id] ?? null,
                $student->orders ?? []
            )));
        }

        // Estudantes "excluídos" (status = 'd') ficam com user nulo pelo filtro
        // acima e não aparecem na lista, embora o registro continue no banco.
        return array_values(array_filter($students, fn($student) => $student->user !== null));
    }

    public function findDuplicateUser(string $email, string $username, ?int $excludeUserId = null): ?string
    {
        $query = $this->_usersTable->find()
            ->where(['OR' => [['email' => $email], ['username' => $username]]]);

        if ($excludeUserId !== null) {
            $query->where(['id !=' => $excludeUserId]);
        }

        $existingUser = $query->first();

        if (!$existingUser) {
            return null;
        }

        return $existingUser->email === $email
            ? __('Este email já está em uso.')
            : __('Este nome de usuário já está em uso.');
    }

    public function getWithUser(int $id): \App\Model\Entity\Student
    {
        $student = $this->_studentsTable->find()
            ->contain(['Users'])
            ->where(['Students.id' => $id])
            ->first();

        if (!$student) {
            throw new RecordNotFoundException(__('Estudante não encontrado.'));
        }

        return $student;
    }

    /**
     * Dados completos pra tela de detalhes do estudante: perfil, estudos
     * recentes com estatísticas agregadas, cursos em que está inscrito,
     * mentoria comprada e último acesso (via Users.last_login).
     */
    public function getDetail(int $id): array
    {
        $student = $this->getWithUser($id);

        $studies = $this->_studiesTable->find()
            ->contain(['Markets', 'Accounts'])
            ->where(['student_id' => $id])
            ->orderByDesc('study_date')
            ->toArray();

        $totalWins = (int)array_sum(array_column($studies, 'wins'));
        $totalLosses = (int)array_sum(array_column($studies, 'losses'));
        $totalTrades = $totalWins + $totalLosses;
        $totalProfitLoss = (float)array_sum(array_column($studies, 'profit_loss'));

        $overallStats = [
            'total_studies' => count($studies),
            'total_wins' => $totalWins,
            'total_losses' => $totalLosses,
            'total_trades' => $totalTrades,
            'win_rate' => $totalTrades > 0 ? ($totalWins / $totalTrades) * 100 : 0.0,
            'total_profit_loss' => $totalProfitLoss,
        ];

        $enrollments = $this->_courseEnrollmentsTable->find()
            ->contain(['Courses' => function ($q) {
                return $q->select(['id', 'title']);
            }])
            ->where(['student_id' => $id])
            ->toArray();
        $courseTitles = array_values(array_filter(array_map(
            fn($enrollment) => $enrollment->course->title ?? null,
            $enrollments
        )));

        $mentorshipOrders = $this->_ordersTable->find()
            ->select(['id', 'item_id'])
            ->where([
                'student_id' => $id,
                'item_type' => 'mentorship',
                'status' => 'approved',
            ])
            ->toArray();

        $mentorshipNames = [];
        if (!empty($mentorshipOrders)) {
            $mentorshipMap = [];
            foreach ($this->_mentorshipsTable->find()->select(['id', 'name'])->toArray() as $mentorship) {
                $mentorshipMap[$mentorship->id] = $mentorship->name;
            }
            $mentorshipNames = array_values(array_filter(array_map(
                fn($order) => $mentorshipMap[$order->item_id] ?? null,
                $mentorshipOrders
            )));
        }

        return [
            'student' => $student,
            'studies' => $studies,
            'overallStats' => $overallStats,
            'courseTitles' => $courseTitles,
            'mentorshipNames' => $mentorshipNames,
        ];
    }

    /**
     * Cria a conta de estudante (Student + User).
     *
     * Aceita tanto uma senha em texto puro (`password`, hasheada aqui) quanto
     * uma senha já hasheada (`password_hash`, usada como está) — o segundo
     * caso é usado pelo OrderService ao confirmar uma compra, onde o hash já
     * foi calculado no momento do checkout e não deve ser hasheado de novo.
     *
     * Se `username` não for informado, um é gerado automaticamente a partir
     * do e-mail.
     */
    public function create(array $data): array
    {
        $connection = $this->_studentsTable->getConnection();
        $user = null;
        $student = null;

        $connection->transactional(function () use ($data, &$user, &$student) {
            $passwordHash = $data['password_hash'] ?? password_hash($data['password'], PASSWORD_DEFAULT);
            $username = $data['username'] ?? $this->generateUsernameFromEmail($data['email']);

            $user = $this->_usersTable->newEmptyEntity();
            $userData = [
                'username' => $username,
                'email' => $data['email'],
                'password' => $passwordHash,
                'role' => 'student',
                'active' => 1,
                'name' => $data['name'],
                'profile_id' => 2,
                'groupp_id' => 2,
            ];

            $user = $this->_usersTable->patchEntity($user, $userData, [
                'fieldList' => ['username', 'email', 'password', 'role', 'active', 'name', 'profile_id', 'groupp_id']
            ]);

            if ($user->hasErrors()) {
                throw new Exception(__('Erro de validação no usuário.'));
            }

            if (!$this->_usersTable->save($user)) {
                throw new Exception(__('Erro ao criar usuário.'));
            }

            $student = $this->_studentsTable->newEmptyEntity();
            $studentData = [
                'name' => $data['name'],
            ];

            $student = $this->_studentsTable->patchEntity($student, $studentData, [
                'fieldList' => ['name']
            ]);

            if ($student->hasErrors()) {
                throw new Exception(__('Erro de validação no estudante.'));
            }

            if (!$this->_studentsTable->save($student)) {
                throw new Exception(__('Erro ao criar estudante.'));
            }

            $user->student_id = $student->id;
            if (!$this->_usersTable->save($user)) {
                throw new Exception(__('Erro ao atualizar referência do estudante.'));
            }
        });

        return ['user' => $user, 'student' => $student];
    }

    private function generateUsernameFromEmail(string $email): string
    {
        $base = strtolower((string)preg_replace('/[^a-z0-9]/i', '', strstr($email, '@', true) ?: $email));
        $base = $base !== '' ? $base : 'aluno';

        $username = $base;
        $suffix = 0;
        while ($this->_usersTable->exists(['username' => $username])) {
            $suffix++;
            $username = $base . $suffix;
        }

        return $username;
    }

    public function update(int $id, array $data): void
    {
        $student = $this->getWithUser($id);

        if (!$student->user) {
            throw new Exception(__('Este estudante não possui usuário associado.'));
        }

        $connection = $this->_studentsTable->getConnection();
        $connection->transactional(function () use ($data, $student) {
            $studentData = ['name' => $data['name']];
            $this->_studentsTable->patchEntity($student, $studentData);
            if (!$this->_studentsTable->save($student)) {
                throw new Exception(__('Erro ao atualizar estudante.'));
            }

            $userData = [
                'username' => $data['username'],
                'email' => $data['email'],
                'active' => isset($data['active']) ? 1 : 0
            ];

            if (!empty($data['password'])) {
                $userData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            $student->user = $this->_usersTable->patchEntity($student->user, $userData);
            if (!$this->_usersTable->save($student->user)) {
                throw new Exception(__('Erro ao atualizar usuário.'));
            }
        });
    }

    /**
     * "Exclusão" de estudante é lógica: o registro nunca é removido do banco.
     * Marca o usuário associado com status = 'd' (deletado), preservando o
     * histórico (estudos, matrículas, pedidos) intacto.
     */
    public function delete(int $id): void
    {
        $student = $this->getWithUser($id);

        if (!$student->user) {
            throw new Exception(__('Este estudante não possui usuário associado.'));
        }

        $student->user = $this->_usersTable->patchEntity($student->user, ['status' => 'd']);
        if (!$this->_usersTable->save($student->user)) {
            throw new Exception(__('Erro ao desativar estudante.'));
        }
    }

    public function getOverviewMetrics(): array
    {
        $totalStudents = $this->_studentsTable->find()
            ->matching('Users', fn($q) => $q->where(['Users.active' => 1]))
            ->count();

        $activeStudents = $totalStudents;

        $totalStudies = $this->_studiesTable->find()->count();

        $topStudentsByStudies = $this->_studentsTable->find()
            ->select([
                'Students.id', 'Students.name',
                'total_studies' => $this->_studentsTable->find()->func()->count('Studies.id')
            ])
            ->leftJoinWith('Studies')
            ->matching('Users', fn($q) => $q->where(['Users.active' => 1]))
            ->groupBy(['Students.id', 'Students.name'])
            ->orderByDesc('total_studies')
            ->limit(10)
            ->toArray();

        $topStudentsByProfit = $this->_studentsTable->find()
            ->select([
                'Students.id', 'Students.name',
                'total_profit_loss' => $this->_studentsTable->find()->func()->sum('Studies.profit_loss'),
                'total_studies' => $this->_studentsTable->find()->func()->count('Studies.id'),
                'total_wins' => $this->_studentsTable->find()->func()->sum('Studies.wins'),
                'total_losses' => $this->_studentsTable->find()->func()->sum('Studies.losses')
            ])
            ->leftJoinWith('Studies')
            ->matching('Users', fn($q) => $q->where(['Users.active' => 1]))
            ->groupBy(['Students.id', 'Students.name'])
            ->orderByDesc('total_profit_loss')
            ->limit(10)
            ->toArray();

        $recentStudies = $this->_studiesTable->find()
            ->contain(['Students'])
            ->orderByDesc('Studies.study_date')
            ->limit(20)
            ->toArray();

        $overallStatsQuery = $this->_studiesTable->find()
            ->select([
                'total_studies' => $this->_studiesTable->find()->func()->count('*'),
                'total_wins' => $this->_studiesTable->find()->func()->sum('wins'),
                'total_losses' => $this->_studiesTable->find()->func()->sum('losses'),
                'total_profit_loss' => $this->_studiesTable->find()->func()->sum('profit_loss'),
                'avg_profit_loss' => $this->_studiesTable->find()->func()->avg('profit_loss')
            ])
            ->first();

        $overallStats = $overallStatsQuery ? $overallStatsQuery->toArray() : [
            'total_studies' => 0, 'total_wins' => 0, 'total_losses' => 0,
            'total_profit_loss' => 0, 'avg_profit_loss' => 0
        ];

        $overallStats['total_trades'] = $overallStats['total_wins'] + $overallStats['total_losses'];
        $overallStats['overall_win_rate'] = $overallStats['total_trades'] > 0
            ? round(($overallStats['total_wins'] / $overallStats['total_trades']) * 100, 2)
            : 0;

        return compact(
            'totalStudents', 'activeStudents', 'totalStudies',
            'topStudentsByStudies', 'topStudentsByProfit',
            'recentStudies', 'overallStats'
        );
    }

    public function getDashboardData(int $studentId, string $selectedYear): array
    {
        $student = $this->getWithUser($studentId);

        $markets = $this->_marketsTable->find()
            ->where(['active' => 1])
            ->orderBy(['name' => 'ASC'])
            ->toArray();

        $monthlyData = $this->_studiesTable->find()
            ->select([
                'year' => 'YEAR(study_date)',
                'month' => 'MONTH(study_date)',
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
            ->leftJoin(['Accounts' => 'accounts'], ['Accounts.id = Studies.account_id'])
            ->where(['Studies.student_id' => $studentId, 'YEAR(Studies.study_date)' => $selectedYear])
            ->groupBy(['YEAR(Studies.study_date)', 'MONTH(Studies.study_date)'])
            ->orderBy(['year' => 'DESC', 'month' => 'DESC'])
            ->toArray();

        $monthNames = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
            5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
            9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
        ];

        foreach ($monthlyData as &$data) {
            $data['month_name'] = $monthNames[(int)$data['month']] ?? 'Mês';
        }
        unset($data);

        $studies = $this->_studiesTable->find()
            ->where(['student_id' => $studentId, 'YEAR(study_date)' => $selectedYear])
            ->toArray();

        $overallStats = [
            'total_studies' => 0, 'total_wins' => 0, 'total_losses' => 0,
            'total_profit_loss' => 0, 'first_study_date' => null, 'last_study_date' => null
        ];

        foreach ($studies as $key => $study) {
            $amount_cost = $this->_studiesTable->getCostTrades($study);
            $studies[$key]['profit_loss'] = $amount_cost + (float)$study['profit_loss'];

            $overallStats['total_studies']++;
            $overallStats['total_wins'] += $study['wins'];
            $overallStats['total_losses'] += $study['losses'];
            $overallStats['total_profit_loss'] += $studies[$key]['profit_loss'];
            $overallStats['first_study_date'] = $overallStats['first_study_date'] ?: $study['study_date'];
            $overallStats['last_study_date'] = $study['study_date'];
        }

        $overallStats['total_trades'] = $overallStats['total_wins'] + $overallStats['total_losses'];
        $overallStats['overall_win_rate'] = $overallStats['total_trades'] > 0
            ? round(($overallStats['total_wins'] / $overallStats['total_trades']) * 100, 2)
            : 0;

        $chartDataRaw = $this->_studiesTable->find()
            ->select([
                'month_key' => "DATE_FORMAT(Studies.study_date, '%Y-%m')",
                'year' => 'YEAR(Studies.study_date)',
                'month' => 'MONTH(Studies.study_date)',
                'profit_loss' => 'SUM(Studies.profit_loss)',
                'wins' => 'SUM(Studies.wins)',
                'losses' => 'SUM(Studies.losses)',
                'Studies.market_id', 'Studies.account_id',
                'Markets.currency', 'market_name' => 'Markets.name',
                'market_code' => 'Markets.code', 'account_name' => 'Accounts.name'
            ])
            ->contain(['Markets', 'Accounts'])
            ->where(['Studies.student_id' => $studentId, 'YEAR(Studies.study_date)' => $selectedYear])
            ->groupBy([
                "DATE_FORMAT(Studies.study_date, '%Y-%m')",
                'YEAR(Studies.study_date)', 'MONTH(Studies.study_date)',
                'Studies.market_id', 'Studies.account_id',
                'Markets.currency', 'Markets.name', 'Markets.code', 'Accounts.name'
            ])
            ->orderBy(['year' => 'ASC', 'month' => 'ASC', 'Markets.name' => 'ASC'])
            ->toArray();

        $chartDataByMonth = [];
        foreach ($chartDataRaw as $data) {
            $monthKey = $data['month_key'];
            if (!isset($chartDataByMonth[$monthKey])) {
                $chartDataByMonth[$monthKey] = [
                    'year' => $data['year'], 'month' => $data['month'],
                    'profit_loss' => 0, 'wins' => 0, 'losses' => 0, 'markets' => []
                ];
            }
            $chartDataByMonth[$monthKey]['profit_loss'] += $data['profit_loss'];
            $chartDataByMonth[$monthKey]['wins'] += $data['wins'];
            $chartDataByMonth[$monthKey]['losses'] += $data['losses'];
            $chartDataByMonth[$monthKey]['markets'][] = [
                'market_id' => $data['market_id'], 'account_id' => $data['account_id'],
                'currency' => $data['currency'], 'market_name' => $data['market_name'],
                'market_code' => $data['market_code'], 'account_name' => $data['account_name'],
                'profit_loss' => $data['profit_loss'], 'wins' => $data['wins'], 'losses' => $data['losses']
            ];
        }

        return compact('student', 'markets', 'monthlyData', 'overallStats', 'chartDataByMonth');
    }
}
