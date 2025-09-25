<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use App\Controller\Component\MonthsComponent;
use App\Controller\Component\GlobalComponent;
use Exception;

class StudiesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
    }

    public function index()
    {
        try {
            $studiesTable = $this->fetchTable('Studies');
            $marketsTable = $this->fetchTable('Markets');

            // Se for estudante, mostrar apenas seus próprios estudos
            if ($this->isStudent()) {

                $studentId = $this->getCurrentStudentId();
                if (!$studentId) {
                    $this->Flash->error(__('Usuário estudante não está associado a nenhum registro de estudante.', 'error'));
                    return $this->redirect(['action' => 'index']);
                }

                $studies = $studiesTable->find()->contain(['Accounts'])
                    ->select([
                        'Studies.id',
                        'Studies.student_id',
                        'Studies.market_id',
                        'Studies.account_id',
                        'Studies.study_date',
                        'Studies.wins',
                        'Studies.losses',
                        'Studies.profit_loss',
                        'Studies.notes',
                        'Studies.created',
                        'Studies.modified',
                        'student_name' => 'Students.name',
                        'market_name' => 'Markets.name',
                        'account_name' => 'Accounts.name',
                        'currency' => 'Markets.currency'
                    ])
                    ->leftJoinWith('Students')
                    ->leftJoinWith('Markets')
                    ->where(['Studies.student_id' => $studentId])
                    ->orderByDesc('Studies.study_date')
                    ->toArray();
            } else {
                // Admin vê todos os estudos
                $studies = $studiesTable->find()->contain(['Accounts'])
                    ->select([
                        'Studies.id',
                        'Studies.student_id',
                        'Studies.market_id',
                        'Studies.account_id',
                        'Studies.study_date',
                        'Studies.wins',
                        'Studies.losses',
                        'Studies.profit_loss',
                        'Studies.notes',
                        'Studies.created',
                        'Studies.modified',
                        'account_name' => 'Accounts.name',
                        'student_name' => 'Students.name',
                        'market_name' => 'Markets.name',
                        'currency' => 'Markets.currency'
                    ])
                    ->leftJoinWith('Students')
                    ->leftJoinWith('Markets')
                    ->orderByDesc('Studies.study_date')
                    ->toArray();
            }

            // Adicionar dados do usuário a cada estudo para compatibilidade com templates
            foreach ($studies as &$study) {
                $study['user'] = [
                    'currency' => $study['currency'] ?? 'BRL'
                ];
            }

            unset($study); // <-- quebra a referência aqui

            // agrupar estudos por mês/ano
            $studiesByMonth = []; // garantir que começa vazio
            foreach ($studies as $study) {

                if (empty($study['study_date'])) {
                    continue;
                }

                try {

                    $monthYear = $study['study_date']->format('Y-m');

                    $monthYearDisplay = MonthsComponent::getMonthNamePortuguese((int)$study['study_date']->format('m')) . ' ' . $study['study_date']->format('Y');
                    $study['study_date'] = GlobalComponent::DataView($study['study_date']);

                    if (!isset($studiesByMonth[$monthYear])) {
                        $studiesByMonth[$monthYear] = [
                            'display'           => $monthYearDisplay,
                            'studies'           => [],
                            'total_studies'     => 0,
                            'total_wins'        => 0,
                            'total_losses'      => 0,
                            'total_profit_loss' => 0.0,
                        ];
                    }

                    $wins       = (int)($study['wins'] ?? 0);
                    $losses     = (int)($study['losses'] ?? 0);
                    $profitLoss = (float)($study['profit_loss'] ?? 0.0);

                    // adicionar estudo ao grupo
                    $studiesByMonth[$monthYear]['studies'][$study['id']] = $study;
                    $studiesByMonth[$monthYear]['total_studies'] += 1;
                    $studiesByMonth[$monthYear]['total_wins']    += $wins;
                    $studiesByMonth[$monthYear]['total_losses']  += $losses;
                    $studiesByMonth[$monthYear]['total_profit_loss'] += $profitLoss;
                } catch (\Exception $e) {
                    error_log("Erro ao processar data do estudo ID {$study['id']}: " . $e->getMessage());
                }
            }

            // Ordenar por mês/ano (mais recente primeiro)
            krsort($studiesByMonth);

            // Buscar mercados para o filtro
            $markets = $marketsTable->find()
                ->select(['id', 'name', 'code'])
                ->where(['active' => 1])
                ->orderBy(['name' => 'ASC'])
                ->toArray();

            // Buscar contas para o filtro
            $accountsTable = $this->fetchTable('Accounts');
            $accounts = $accountsTable->find()
                ->select(['id', 'name'])
                ->orderByDesc('name')
                ->toArray();

            $this->set('studiesByMonth', $studiesByMonth);
            $this->set('studies', $studies); // Manter compatibilidade
            $this->set('markets', $markets); // Para o filtro
            $this->set('accounts', $accounts); // Para o filtro de conta


        } catch (Exception $e) {
            $this->Flash->error(__('Error loading studies: ' . $e->getMessage(), 'error'));
        }
    }

    public function view($id = null)
    {
        try {
            $studiesTable = $this->fetchTable('Studies');

            $study = $studiesTable->find()
                ->select([
                    'Studies.id',
                    'Studies.student_id',
                    'Studies.market_id',
                    'Studies.account_id',
                    'Studies.study_date',
                    'Studies.wins',
                    'Studies.losses',
                    'Studies.profit_loss',
                    'Studies.notes',
                    'Studies.created',
                    'Studies.modified',
                    'student_name' => 'Students.name',
                    'student_email' => 'Students.email',
                    'market_name' => 'Markets.name',
                    'market_code' => 'Markets.code',
                    'market_description' => 'Markets.description',
                    'currency' => 'Markets.currency',
                    'username' => 'Users.username',
                    'role' => 'Users.role',
                    'active' => 'Users.active'
                ])
                ->leftJoinWith('Students')
                ->leftJoinWith('Markets')
                ->leftJoinWith('Students.Users')
                ->where(['Studies.id' => $id])
                ->first();

            if ($study) {
                // Adicionar dados do usuário ao array do study para compatibilidade com o template
                $study['user'] = [
                    'currency' => $study['currency'] ?? 'BRL', // Agora vem do market
                    'username' => $study['username'],
                    'role' => $study['role'],
                    'active' => $study['active']
                ];

                // Adicionar dados do estudante ao array do study para compatibilidade com o template
                $study['student'] = [
                    'id' => $study['student_id'],
                    'name' => $study['student_name'],
                    'email' => $study['student_email'] ?? $study['username'] // Usar email do estudante ou username como fallback
                ];

                // Adicionar dados do mercado ao array do study
                if ($study['market_name']) {
                    $study['market'] = [
                        'name' => $study['market_name'],
                        'code' => $study['market_code'],
                        'description' => $study['market_description']
                    ];
                } else {
                    $study['market'] = null;
                }

                // Calcular campos derivados
                $study['total_trades'] = $study['wins'] + $study['losses'];
                $study['win_rate'] = $study['total_trades'] > 0 ? ($study['wins'] / $study['total_trades']) * 100 : 0;
            }

            if (!$study) {
            $this->Flash->error(__('Study not found.', 'error'));
            return $this->redirect(['action' => 'index']);
        }

            // Verificar se estudante pode acessar este estudo
            if ($this->isStudent()) {
                $studentId = $this->getCurrentStudentId();
                if ($study['student_id'] != $studentId) {
                    $this->Flash->error(__('Acesso negado. Você só pode visualizar seus próprios estudos.', 'error'));
                    return $this->redirect(['action' => 'index']);
                }
            }

            $this->set('study', $study);
        } catch (Exception $e) {
            $this->Flash->error(__('Error loading study: ' . $e->getMessage(), 'error'));
            return $this->redirect(['action' => 'index']);
        }
    }

    public function add()
    {
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Se for estudante, forçar o student_id para o estudante logado
            if ($this->isStudent()) {
                $data['student_id'] = $this->getCurrentStudentId();
            }

            try {
                $studiesTable = $this->fetchTable('Studies');

                $study = $studiesTable->newEmptyEntity();

                $study = $studiesTable->patchEntity($study, [
                    'student_id' => $data['student_id'],
                    'market_id' => $data['market_id'],
                    'account_id' => $data['account_id'],
                    'study_date' => $data['study_date'],
                    'wins' => $data['wins'],
                    'losses' => $data['losses'],
                    'profit_loss' => $data['profit_loss'],
                    'notes' => $data['notes'] ?? ''
                ]);

                if ($studiesTable->save($study)) {
                    $this->Flash->success(__('Estudo criado com sucesso!', 'success'));
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('The study could not be saved. Please, try again.', 'error'));
                }
            } catch (Exception $e) {
                $this->Flash->error(__('The study could not be saved. Please, try again.', 'error'));
            }
        }

        // Se for estudante, passar o ID do estudante atual para o template
        if ($this->isStudent()) {
            $this->set('currentStudentId', $this->getCurrentStudentId());
        }

        // Carregar mercados e contas para os dropdowns
        try {
            $marketsTable = $this->fetchTable('Markets');
            $accountsTable = $this->fetchTable('Accounts');

            $markets = $marketsTable->find()
                ->select(['id', 'name', 'code'])
                ->where(['active' => 1])
                ->orderBy(['name' => 'ASC'])
                ->toArray();

            $accounts = $accountsTable->find()
                ->select(['id', 'name'])
                ->orderByDesc('name')
                ->toArray();

            $this->set('accounts', $accounts);
            $this->set('markets', $markets);
        } catch (Exception $e) {
            $this->set('accounts', []);
            $this->set('markets', []);
        }
    }

    public function edit($id = null)
    {
        $studiesTable = $this->fetchTable('Studies');

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            try {

                $study = $studiesTable->find()
                    ->select(['id', 'student_id'])
                    ->where(['id' => $id])
                    ->first();


                if (!$study) {
                    $this->Flash->error(__('Estudo não encontrado.', 'error'));
                    return $this->redirect(['action' => 'index']);
                }

                // Security validation
                $currentUser = $this->getCurrentUser();

                if (!$this->getCurrentUser()) {
            $this->Flash->error(__('Acesso negado. Faça login para continuar.', 'error'));
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

                if ($this->isStudent()) {

                    $currentStudentId = $this->getCurrentStudentId();

                    if ($study['student_id'] != $currentStudentId) {
                        $this->Flash->error(__('Acesso negado. Você só pode editar seus próprios estudos.', 'error'));
                        return $this->redirect(['action' => 'index']);
                    }
                    // Force the student_id to be the current student's ID to prevent tampering
                    $data['student_id'] = $currentStudentId;
                }
                
                $study = $studiesTable->patchEntity($study, [
                    'student_id' => $data['student_id'],
                    'market_id' => $data['market_id'],
                    'account_id' => $data['account_id'],
                    'study_date' => $data['study_date'],
                    'wins' => $data['wins'],
                    'losses' => $data['losses'],
                    'profit_loss' => $data['profit_loss'],
                    'notes' => $data['notes'] ?? ''
                ]);
                
                if ($studiesTable->save($study)) {
                    $this->Flash->success(__('Estudo atualizado com sucesso!', 'success'));
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error(__('O estudo não pôde ser atualizado. Tente novamente.', 'error'));
                }
            } catch (Exception $e) {
                $this->Flash->error(__('O estudo não pôde ser atualizado. Tente novamente.', 'error'));
            }
        }

        // Get current study data
        try {
            $study = $studiesTable->find()
                ->select([
                    'Studies.id',
                    'Studies.student_id',
                    'Studies.market_id',
                    'Studies.account_id',
                    'Studies.study_date',
                    'Studies.wins',
                    'Studies.losses',
                    'Studies.profit_loss',
                    'Studies.notes',
                    'Studies.created',
                    'Studies.modified',
                    'student_name' => 'Students.name'
                ])
                ->leftJoinWith('Students')
                ->where(['Studies.id' => $id])
                ->first();

                $study['study_date'] = $study['study_date']->format('Y-m-d');

            if (!$study) {
            $this->Flash->error(__('Study not found.', 'error'));
            return $this->redirect(['action' => 'index']);
        }

            // Security check: Only allow editing if user is logged in and owns the study
            $currentUser = $this->getCurrentUser();
            if (!$this->getCurrentUser()) {
            $this->Flash->error(__('Acesso negado. Faça login para continuar.', 'error'));
            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }

            // If user is a student, they can only edit their own studies
            if ($this->isStudent()) {
                $currentStudentId = $this->getCurrentStudentId();
                if ($study['student_id'] != $currentStudentId) {
                    $this->Flash->error(__('Acesso negado. Você só pode editar seus próprios estudos.', 'error'));
                    return $this->redirect(['action' => 'index']);
                }
            }
            // Admins can edit any study (no additional check needed)

            // Calculate additional fields for display
            $totalTrades = ($study['wins'] ?? 0) + ($study['losses'] ?? 0);
            $winRate = $totalTrades > 0 ? round(($study['wins'] / $totalTrades) * 100, 2) : 0;

            $study['total_trades'] = $totalTrades;
            $study['win_rate'] = $winRate;

            $this->set('study', $study);
            $this->set('studentName', $study['student_name']);
        } catch (Exception $e) {
            $this->Flash->error(__('Error loading study: ' . $e->getMessage(), 'error'));
            return $this->redirect(['action' => 'index']);
        }

        // Get students for dropdown
        try {
            $studentsTable = $this->fetchTable('Students');
            $studentsData = $studentsTable->find()
                ->select(['id', 'name'])
                ->orderBy(['name' => 'ASC'])
                ->toArray();

            // Convert to associative array with id as key and name as value
            $students = [];
            foreach ($studentsData as $student) {
                $students[$student['id']] = $student['name'];
            }

            $this->set('students', $students);
        } catch (Exception $e) {
            $this->set('students', []);
        }

        // Carregar mercados e contas para os dropdowns
        try {
            $marketsTable = $this->fetchTable('Markets');
            $accountsTable = $this->fetchTable('Accounts');

            $markets = $marketsTable->find()
                ->select(['id', 'name', 'code'])
                ->where(['active' => 1])
                ->orderBy(['name' => 'ASC'])
                ->toArray();

            $accounts = $accountsTable->find()
                ->select(['id', 'name'])
                ->orderByDesc('name')
                ->toArray();

            $this->set('markets', $markets);
            $this->set('accounts', $accounts);
        } catch (Exception $e) {
            $this->set('markets', []);
            $this->set('accounts', []);
        }
    }

    public function delete($id = null)
    {
        $this->checkSession();
        $this->autoRender = false;

        try {
            $studiesTable = $this->fetchTable('Studies');

            if ($this->request->is('post')) {
                $study = $studiesTable->get($id);

                if ($studiesTable->delete($study)) {
                    $this->Flash->success(__('Estudo excluído com sucesso!', 'success'));
                } else {
                    $this->Flash->error(__('The study could not be deleted. Please, try again.', 'error'));
                }
            }
        } catch (Exception $e) {
            $this->Flash->error(__('The study could not be deleted. Please, try again.', 'error'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function importCsv()
    {
        $this->request->allowMethod(['post']);
        
        try {
            if (!$this->request->getData('csv_file')) {
                throw new Exception('Nenhum arquivo CSV foi enviado.');
            }

            $csvFile = $this->request->getData('csv_file');
            
            // Verificar se é um arquivo CSV
            if ($csvFile->getClientMediaType() !== 'text/csv' && 
                pathinfo($csvFile->getClientFilename(), PATHINFO_EXTENSION) !== 'csv') {
                throw new Exception('O arquivo deve ser um CSV válido.');
            }

            // Ler o arquivo CSV
            $csvContent = file_get_contents($csvFile->getStream()->getMetadata('uri'));
            $lines = explode("\n", $csvContent);
            
            if (empty($lines)) {
                throw new Exception('O arquivo CSV está vazio.');
            }

            $operationsTable = $this->fetchTable('Operations');
            $studiesTable = $this->fetchTable('Studies');
            
            $importedCount = 0;
            $errors = [];
            
            // Processar as linhas do CSV
            $headerProcessed = false;
            $csvData = [];
            
            foreach ($lines as $lineIndex => $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                // Pular linhas de cabeçalho até encontrar os dados das operações
                if (!$headerProcessed) {
                    if (strpos($line, 'Ativo') !== false) {
                        $headerProcessed = true;
                        continue; // Pular a linha do cabeçalho
                    }
                    // Capturar informações do cabeçalho
                    if (strpos($line, 'Conta:') !== false) {
                        $csvData['conta'] = trim(str_replace('Conta:', '', $line));
                    } elseif (strpos($line, 'Titular:') !== false) {
                        $csvData['titular'] = trim(str_replace('Titular:', '', $line));
                    } elseif (strpos($line, 'Data Inicial:') !== false) {
                        $csvData['data_inicial'] = trim(str_replace('Data Inicial:', '', $line));
                    } elseif (strpos($line, 'Data Final:') !== false) {
                        $csvData['data_final'] = trim(str_replace('Data Final:', '', $line));
                    }
                    continue;
                }
                
                // Processar linha de dados
                $data = str_getcsv($line, ';');
                
                if (count($data) < 5) {
                    continue; // Pular linhas incompletas
                }
                
                try {
                    // Criar nova operação
                    $operation = $operationsTable->newEmptyEntity();
                    $operation = $operationsTable->patchEntity($operation, [
                        'study_id' => $this->request->getData('study_id'),
                        'asset' => $data[0] ?? null,
                        'open_time' => !empty($data[1]) ? date('Y-m-d H:i:s', strtotime($data[1])) : null,
                        'close_time' => !empty($data[2]) ? date('Y-m-d H:i:s', strtotime($data[2])) : null,
                        'operation_result' => $data[3] ?? null,
                        'operation_result_percent' => $data[4] ?? null,
                        'conta' => $csvData['conta'] ?? null,
                        'titular' => $csvData['titular'] ?? null,
                        'data_inicial' => !empty($csvData['data_inicial']) ? date('Y-m-d', strtotime($csvData['data_inicial'])) : null,
                        'data_final' => !empty($csvData['data_final']) ? date('Y-m-d', strtotime($csvData['data_final'])) : null,
                    ]);
                    
                    if ($operationsTable->save($operation)) {
                        $importedCount++;
                    } else {
                        $errors[] = "Linha " . ($lineIndex + 1) . ": Erro ao salvar operação";
                    }
                    
                } catch (Exception $e) {
                    $errors[] = "Linha " . ($lineIndex + 1) . ": " . $e->getMessage();
                }
            }
            
            $message = "$importedCount operações importadas com sucesso.";
            if (!empty($errors)) {
                $message .= " Erros: " . implode(', ', array_slice($errors, 0, 3));
                if (count($errors) > 3) {
                    $message .= " e mais " . (count($errors) - 3) . " erros.";
                }
            }
            
            $this->response = $this->response->withType('application/json');
            $this->set([
                'success' => true,
                'message' => $message,
                'imported_count' => $importedCount,
                'errors_count' => count($errors)
            ]);
            $this->viewBuilder()->setOption('serialize', ['success', 'message', 'imported_count', 'errors_count']);
            
        } catch (Exception $e) {
            $this->response = $this->response->withType('application/json');
            $this->set([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            $this->viewBuilder()->setOption('serialize', ['success', 'message']);
        }
    }

    public function byStudent($studentId = null)
    {
        if (!$studentId) {
            $this->Flash->error(__('Invalid student ID.', 'error'));
            return $this->redirect(['controller' => 'Students', 'action' => 'index']);
        }

        try {
            $studentsTable = $this->fetchTable('Students');
            $studiesTable = $this->fetchTable('Studies');

            $student = $studentsTable->get($studentId);

            $this->paginate = [
                'conditions' => ['Studies.student_id' => $studentId],
                'contain' => ['Students'],
                'orderBy' => ['Studies.study_date' => 'DESC']
            ];

            $studies = $this->paginate($studiesTable);

            $this->set(compact('studies', 'student'));
        } catch (Exception $e) {
            $this->Flash->error(__('Error loading student studies: ' . $e->getMessage(), 'error'));
            return $this->redirect(['controller' => 'Students', 'action' => 'index']);
        }
    }
}
