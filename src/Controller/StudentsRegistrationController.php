<?php

declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\ORM\TableRegistry;

class StudentsRegistrationController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
    }

    public function register()
    {

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $usersTable = TableRegistry::getTableLocator()->get('Users');

        $student = $studentsTable->newEmptyEntity();
        $user = $usersTable->newEmptyEntity();

        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Validate required fields
            $errors = [];

            if (empty($data['name'])) {
                $errors['name'] = 'Nome é obrigatório';
            }

            if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'E-mail válido é obrigatório';
            }

            if (empty($data['username'])) {
                $errors['username'] = 'Nome de usuário é obrigatório';
            } elseif (strlen($data['username']) < 3) {
                $errors['username'] = 'Nome de usuário deve ter pelo menos 3 caracteres';
            }

            if (empty($data['password'])) {
                $errors['password'] = 'Senha é obrigatória';
            } elseif (strlen($data['password']) < 6) {
                $errors['password'] = 'Senha deve ter pelo menos 6 caracteres';
            }

            if (empty($data['confirm_password']) || $data['password'] !== $data['confirm_password']) {
                $errors['confirm_password'] = 'Confirmação de senha não confere';
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

                    $this->Flash->error(__('Erro ao criar conta: ' . $e->getMessage()));
                }
            }
        }

        $this->set(compact('student', 'user'));
    }
}
