<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class UsersController extends AppController
{
    public function index()
    {
        $query = $this->Users->find()
            ->contain(['Students', 'Groupps', 'Profiles']);
        $users = $this->paginate($query);

        $this->set(compact('users'));
    }

    public function view($id = null)
    {
        $user = $this->Users->get($id, contain: ['Students', 'Groupps', 'Profiles']);
        $this->set(compact('user'));
    }

    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $students = $this->Users->Students->find('list', limit: 200)->all();
        $groupps = $this->Users->Groupps->find('list', limit: 200)->all();
        $profiles = $this->Users->Profiles->find('list', limit: 200)->all();
        $this->set(compact('user', 'students', 'groupps', 'profiles'));
    }

    public function edit($id = null)
    {
        $this->checkSession();

        $user = $this->Users->get($id, contain: ['Students']);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Verificar se o email já existe em outro usuário
            $existingUser = $this->Users->find()
                ->where(['email' => $data['email'], 'id !=' => $id])
                ->first();

            if ($existingUser) {
                $this->Flash->error('Este email já está cadastrado para outro usuário.');
                $this->set(compact('user'));
                return;
            }

            // Verificar se o username já existe em outro usuário (se fornecido)
            if (!empty($data['username']) && $data['username'] !== $user->username) {
                $existingUsername = $this->Users->find()
                    ->where(['username' => $data['username'], 'id !=' => $id])
                    ->first();

                if ($existingUsername) {
                    $this->Flash->error('Este username já está em uso por outro usuário.');
                    $this->set(compact('user'));
                    return;
                }
            }

            // Verificar senha atual se uma nova senha foi fornecida
            if (!empty($data['new_password'])) {
                if (!empty($data['current_password'])) {
                    if (!password_verify($data['current_password'], $user->password)) {
                        $this->Flash->error('Senha atual incorreta.');
                        $this->set(compact('user'));
                        return;
                    }
                }
                // Substituir new_password por password para o ORM
                $data['password'] = password_hash($data['new_password'], PASSWORD_DEFAULT);
                unset($data['new_password'], $data['current_password']);
            } else {
                // Remover campos de senha se não foram fornecidos
                unset($data['password'], $data['new_password'], $data['current_password']);
            }

            $user = $this->Users->patchEntity($user, $data);

            if ($this->Users->save($user)) {
                // Atualizar dados do estudante se o usuário for um estudante
                if ($user->role === 'student' && $user->student_id) {
                    $studentsTable = $this->fetchTable('Students');
                    $student = $studentsTable->get($user->student_id);
                    $student->email = $user->email;
                    $studentsTable->save($student);
                }

                $this->Flash->success('Perfil atualizado com sucesso!');

                // Se for edição de perfil próprio, atualizar sessão
                if ($id === $this->getCurrentUser()['id']) {

                    $session = $this->request->getSession();

                    $Usuario = $this->Users->find('all')->contain(['Groupps', 'Profiles'])->where(['Users.id' => $id])->first();

                    $session->write('logado', $Usuario);

                    return $this->redirect(['action' => 'edit']);
                }

                return $this->redirect(['action' => 'edit', $user->id]);
            }

            $this->Flash->error('O usuário não pôde ser salvo. Tente novamente.');
        }

        $students = $this->Users->Students->find('list', limit: 200)->all();
        $groupps = $this->Users->Groupps->find('list', limit: 200)->all();
        $profiles = $this->Users->Profiles->find('list', limit: 200)->all();

        $this->set(compact('user', 'students', 'groupps', 'profiles'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function blocked($id = null)
    {
        $this->autoRender = false;
        $res = new \stdClass();
        $res->status = 0;

        $Settings = $this->fetchTable('Settings');

        $Config = $Settings->find('all')->first();

        $this->checkSession();
        $checkPermission = $this->checkPermission(37);

        $this->request->allowMethod(['post', 'delete']);

        try {

            if ($this->request->is('post')) {

                $user = $this->Users->get($id);

                if ($user->user_status == 'i' && $user->groupp_id == 2) {

                    $usersdb = $this->Users->find('all')->where(['groupp_id' => 2, 'blocked' => '0'])->count();

                    if ($usersdb >= $Config->accesses) {

                        $res->msg = 'Limite de ' . $Config->accesses . ' acesso foi exedido, entre em contato com o administrador do sistema para contratar novos acessos.';

                        echo json_encode($res);
                        exit;
                    }
                }

                if ($checkPermission) {

                    $registro = $this->request->getData();

                    $user->blocked = $registro['blocked'];

                    if ($user->user_status == 'i') {

                        $user->user_status = 'a';
                    } else {

                        $user->user_status = 'i';
                    }

                    if ($this->Users->save($user)) {
                        $res->status = 1;
                    }
                } else {
                    $res->msg = "OOOPS! Acesso negado, seu perfil de usuário não tem permissão necessária.";
                }
            }

            echo json_encode($res);
            exit;
        } catch (\Exception $e) {
            $res->msg = $e->getMessage();
        }
    }

    public function login()
    {
        $Entusuario = $this->Users->newEmptyEntity();
        $session = $this->request->getSession();

        if ($session->check('logado')) {
            return $this->redirect(['controller' => 'Welcome', 'action' => 'index']);
        }

        if ($this->request->is('post')) {

            $registro = $this->request->getData();

            $User = $this->Users->find()->contain(['Groupps', 'Profiles'])->where([
                'or' => [
                    'username' => $registro['user'],
                    'email' => $registro['user'],
                ]
            ])->first();

            if (isset($User->id)) {

                if ($User->blocked || $User->status == 'i') {
                    $this->Flash->error(__('Conta inativada ou bloqueada, entre em contato para mais informações.'));
                } else {

                    $registro['password2'] = password_hash(trim($registro['pass']), PASSWORD_DEFAULT);

                    if (!empty($User->password) && password_verify(trim($registro['pass']), $User->password)) {

                        $Usuario = $this->Users->get($User->id);

                        $Usuario->last_login = date("Y-m-d H:i:s");

                        if ($this->request->getSession()->id()) {
                            $Usuario->session_id = $this->request->getSession()->id();
                        } else {

                            session_start();

                            $Usuario->session_id = $this->request->getSession()->id();
                        }

                        $this->Users->save($Usuario);

                        $Usuario = $this->Users->find('all')->contain(['Groupps', 'Profiles'])->where(['Users.id' => $User->id])->first();

                        $session->write('logado', $Usuario);

                        $this->Flash->success(__($this->saudacao() . ' ' . $User->name . '.'));

                        if ($this->request->getQuery('r')) {
                            $this->redirectApp(base64_decode($this->request->getQuery('r')));
                        } else {
                            return $this->redirect(['controller' => 'Welcome', 'action' => 'index']);
                        }
                    } else {
                        $this->Flash->error(__('Dados inválidos.'));
                    }
                }
            } else {
                $this->Flash->error(__('Dados inválidos.'));
            }
        }

        $this->set(['User' => $Entusuario]);
    }

    public function recover()
    {

        if ($this->request->is('post')) {

            $InitTokens = $this->fetchTable('Tokens');
            $Settings = $this->fetchTable('Settings');
            $InitSendings = $this->fetchTable('Sendings');

            $Config = $Settings->find('all')->first();

            $registro = $this->request->getData();

            if (isset($registro['email']) && $registro['email'] == "") {
                $this->Flash->error(__('Informe o e-mail.'));
            } else {

                $User = $this->Users->find()->contain(['Groupps', 'Profiles'])->where([
                    'or' => [
                        'email' => $registro['email']
                    ]
                ])->first();

                if (isset($User->id)) {

                    $newToken = strtoupper(md5(uniqid(rand(), true)) . md5(uniqid(rand(), true)));

                    $token = $InitTokens->newEmptyEntity();

                    $saveToken = [];
                    $saveToken['token'] = $newToken;
                    $saveToken['user_id'] = $User->id;

                    $token = $InitTokens->patchEntity($token, $saveToken);

                    if ($InitTokens->save($token)) {

                        $html = @file_get_contents(HOME . 'email/recuperar.php');

                        $html = str_ireplace(array(
                            "%nome%",
                            "%token%",
                        ), array(
                            $User->name,
                            $newToken,
                        ), $html);

                        $data = [];
                        $data['destination'] = trim($User->email);
                        $data['body'] = $html;
                        $data['subject'] = 'Recuperação de Senha';
                        $data['from'] = ("$Config->title <$Config->email>");

                        $url = $Config->aws_api . "/send";

                        $this->sendCurl($url, $data);

                        $sendingdb = $InitSendings->find('all')->where([' MONTH(month) ' => date('m'), ' YEAR(month) ' => date('Y')])->first();

                        if (isset($sendingdb->id)) {

                            $sendingdb->amount = $sendingdb->amount + 1;

                            $InitSendings->save($sendingdb);
                        }

                        $this->Flash->success(__('Um novo acesso foi enviado para seu e-mail.'));
                    }
                } else {
                    $this->Flash->error(__('E-mail não encontrato.'));
                }
            }
        }

        $this->set(['User' => '']);
    }

    public function pass_recover($token = null)
    {

        $InitTokens = $this->fetchTable('Tokens');

        $tokendb = $InitTokens->find('all')->where(['token' => $token, 'created BETWEEN NOW() -INTERVAL 10 HOUR AND NOW()'])->first();

        if (!isset($tokendb->token)) {

            $this->Flash->error(__('Token inválido.'));
            return $this->redirect(['action' => 'login']);
        }

        if ($this->request->is('post')) {

            $registro = $this->request->getData();

            if ($registro['pass'] == "") {
                $this->Flash->error(__('Informe a senha.'));
                return $this->redirect(['action' => 'pass_recover', $token]);
            }

            if ($registro['repeat_pass'] == "") {
                $this->Flash->error(__('Confirme a senha.'));
                return $this->redirect(['action' => 'pass_recover', $token]);
            }

            $registro['pass'] = trim($registro['pass']);
            $registro['repeat_pass'] = trim($registro['repeat_pass']);

            if ($registro['pass'] != $registro['repeat_pass']) {
                $this->Flash->error(__('As senhas não são iguais.'));
                return $this->redirect(['action' => 'pass_recover', $token]);
            }

            $salt = $this->Users->geraSaltAleatorio();

            $user = $this->Users->get($tokendb->user_id);

            $user->pass = md5(trim($registro['pass'] .  $salt));
            $user->salt = $salt;

            if ($this->Users->save($user)) {
                $this->Flash->success(__('Nova senha definida com sucesso.'));
                return $this->redirect(['action' => 'login']);
            }
        }

        $this->set(['User' => '']);
    }

    public function logout()
    {
        $this->checkSession();

        $session = $this->request->getSession();
        $logado = $session->read('logado');

        $Usuario = $this->Users->get($logado->id);

        $this->Users->save($Usuario);

        $this->autoRender = false;

        $session->delete('logado');
        return $this->redirect(['controller' => 'Users', 'action' => 'login']);
    }
}
