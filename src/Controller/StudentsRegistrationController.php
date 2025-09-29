<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use App\Middleware\RateLimitMiddleware;
use Cake\Http\Exception\TooManyRequestsException;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

class StudentsRegistrationController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    public function checkUsername()
    {
        $this->autoRender = false;

        $this->request->allowMethod(['post']);
        
        $username = $this->request->getData('username');
        
        // Sanitize and validate input
        if (empty($username) || !is_string($username)) {
            $this->set([
                'exists' => false,
                'message' => ''
            ]);
            $this->viewBuilder()->setOption('serialize', ['exists', 'message']);
            return;
        }

        // Sanitize username
        $username = trim(strip_tags($username));
        
        // Additional validation
        if (strlen($username) < 3 || strlen($username) > 50) {
            echo json_encode([
                'exists' => false,
                'message' => 'Nome de usuário deve ter entre 3 e 50 caracteres'
            ]);
            exit;
        }

        $usersTable = $this->fetchTable('Users');
        $user = $usersTable->find()->where(['username' => $username])->first();
        
        $exists = !empty($user);
        $message = $exists ? 'Este nome de usuário já está em uso' : 'Nome de usuário disponível';
        
        echo json_encode([
            'exists' => $exists,
            'message' => $message
        ]);
        exit;
    }

    public function register()
    {
        // Apply rate limiting
        $rateLimiter = new RateLimitMiddleware(5, 300, 'rate_limit'); // 5 attempts per 5 minutes
        $clientIp = $this->getClientIp();
        
        // Check rate limit before processing
        $remainingAttempts = $rateLimiter->getRemainingAttempts($clientIp);
        if ($remainingAttempts <= 0) {
            $timeUntilReset = $rateLimiter->getTimeUntilReset($clientIp);
            $this->Flash->error('Muitas tentativas de registro. Tente novamente em ' . ceil($timeUntilReset / 60) . ' minutos.');
        }

        $studentsTable = $this->fetchTable('Students');
        $usersTable = $this->fetchTable('Users');

        $student = $studentsTable->newEmptyEntity();
        $user = $usersTable->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Validate required fields
            $errors = [];

            // Sanitize and validate name
            if (empty($data['name']) || !is_string($data['name'])) {
                $errors['name'] = 'Nome é obrigatório';
            } else {
                $data['name'] = trim(strip_tags($data['name']));
                if (strlen($data['name']) < 2 || strlen($data['name']) > 100) {
                    $errors['name'] = 'Nome deve ter entre 2 e 100 caracteres';
                }
            }

            // Sanitize and validate email
            if (empty($data['email']) || !is_string($data['email'])) {
                $errors['email'] = 'E-mail é obrigatório';
            } else {
                $data['email'] = trim(strtolower($data['email']));
                if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = 'E-mail válido é obrigatório';
                }
            }

            // Sanitize and validate username
            if (empty($data['username']) || !is_string($data['username'])) {
                $errors['username'] = 'Nome de usuário é obrigatório';
            } else {
                $data['username'] = trim(strip_tags($data['username']));
                if (strlen($data['username']) < 3 || strlen($data['username']) > 50) {
                    $errors['username'] = 'Nome de usuário deve ter entre 3 e 50 caracteres';
                } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
                    $errors['username'] = 'Nome de usuário deve conter apenas letras, números e underscore';
                }
            }

            // Validate password
            if (empty($data['password']) || !is_string($data['password'])) {
                $errors['password'] = 'Senha é obrigatória';
            } /*elseif (strlen($data['password']) < 8) {
                $errors['password'] = 'Senha deve ter pelo menos 8 caracteres';
            } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/', $data['password'])) {
                $errors['password'] = 'Senha deve conter pelo menos uma letra minúscula, uma maiúscula e um número';
            }*/

            // Validate password confirmation
            if (empty($data['confirm_password']) || $data['password'] !== $data['confirm_password']) {
                $errors['confirm_password'] = 'Confirmação de senha não confere';
            }

            // Sanitize phone if provided
            if (!empty($data['phone'])) {
                $data['phone'] = trim(strip_tags($data['phone']));
                if (strlen($data['phone']) > 20) {
                    $errors['phone'] = 'Telefone deve ter no máximo 20 caracteres';
                }
            }

            // Check for existing email in students table
            $existingStudent = $studentsTable->find()->where(['email' => $data['email']])->first();
            if ($existingStudent) {
                $errors['email'] = 'Este e-mail já está cadastrado';
            }

            // Check for existing username in users table
            $existingUser = $usersTable->find()->where(['username' => $data['username']])->first();
            if ($existingUser) {
                $errors['username'] = 'Este nome de usuário já está em uso';
            }

            // Check for existing email in users table
            $existingUserEmail = $usersTable->find()->where(['email' => $data['email']])->first();
            if ($existingUserEmail) {
                $errors['email'] = 'Este e-mail já está cadastrado';
            }

            if (!empty($errors)) {
                // Record failed attempt for rate limiting
                $this->recordFailedAttempt($clientIp);
                
                foreach ($errors as $field => $message) {
                    $this->Flash->error($message);
                }
            } else {
                // Start transaction
                $connection = $studentsTable->getConnection();
                $connection->begin();

                try {
                    // Create student entity
                    $student = $studentsTable->patchEntity($student, [
                        'name' => trim($data['name']),
                        'email' => strtolower(trim($data['email'])),
                        'phone' => !empty($data['phone']) ? trim($data['phone']) : null
                    ]);

                    // Validate and save student
                    if (!$studentsTable->save($student)) {
                        $studentErrors = $student->getErrors();
                        $errorMessages = [];
                        foreach ($studentErrors as $field => $fieldErrors) {
                            foreach ($fieldErrors as $error) {
                                $errorMessages[] = $error;
                            }
                        }
                        throw new \Exception('Erro nos dados do estudante: ' . implode(', ', $errorMessages));
                    }

                    // Create user entity
                    $user = $usersTable->patchEntity($user, [
                        'username' => trim($data['username']),
                        'email' => strtolower(trim($data['email'])),
                        'password' => password_hash(trim($data['password']), PASSWORD_DEFAULT),
                        'role' => 'student',
                        'student_id' => $student->id,
                        'active' => true,
                        'status' => 'active',
                        'blocked' => false,
                        'name' => trim($data['name']),
                        'profile_id' => 2,
                        'groupp_id' => 2
                    ]);

                    // Validate and save user
                    if (!$usersTable->save($user)) {
                        $userErrors = $user->getErrors();
                        $errorMessages = [];
                        foreach ($userErrors as $field => $fieldErrors) {
                            foreach ($fieldErrors as $error) {
                                $errorMessages[] = $error;
                            }
                        }
                        throw new \Exception('Erro na conta de usuário: ' . implode(', ', $errorMessages));
                    }

                    // Commit transaction
                    $connection->commit();

                    $this->Flash->success(__('Conta criada com sucesso! Você pode fazer login agora.'));
                    return $this->redirect(['prefix' => 'Admin', 'controller' => 'Users', 'action' => 'login']);
                } catch (\Exception $e) {
                    // Rollback transaction
                    $connection->rollback();
                    
                    // Record failed attempt for rate limiting
                    $this->recordFailedAttempt($clientIp);

                    $this->Flash->error(__('Erro ao criar conta: ' . $e->getMessage()));
                }
            }
        }

        $this->set(compact('student', 'user'));
    }

   
}
