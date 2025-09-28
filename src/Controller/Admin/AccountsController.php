<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

class AccountsController extends AppController
{

    public function index()
    {
        $query = $this->Accounts->find();
        $accounts = $this->paginate($query);

        $this->set(compact('accounts'));
    }

    public function view($id = null)
    {
        $account = $this->Accounts->get($id, contain: ['Studies']);
        $this->set(compact('account'));
    }

    public function add()
    {
        $account = $this->Accounts->newEmptyEntity();
        if ($this->request->is('post')) {
            $account = $this->Accounts->patchEntity($account, $this->request->getData());
            if ($this->Accounts->save($account)) {
                $this->Flash->success(__('The account has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The account could not be saved. Please, try again.'));
        }
        $this->set(compact('account'));
    }

    public function edit($id = null)
    {
        $account = $this->Accounts->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $account = $this->Accounts->patchEntity($account, $this->request->getData());
            if ($this->Accounts->save($account)) {
                $this->Flash->success(__('The account has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The account could not be saved. Please, try again.'));
        }
        $this->set(compact('account'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $account = $this->Accounts->get($id);
        if ($this->Accounts->delete($account)) {
            $this->Flash->success(__('The account has been deleted.'));
        } else {
            $this->Flash->error(__('The account could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
