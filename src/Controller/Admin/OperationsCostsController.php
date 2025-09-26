<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

class OperationsCostsController extends AppController
{
    public function index()
    {
        $query = $this->OperationsCosts->find()
            ->contain(['Markets', 'Students', 'Accounts']);
        $operationsCosts = $this->paginate($query);

        $this->set(compact('operationsCosts'));
    }

    public function view($id = null)
    {
        $operationsCost = $this->OperationsCosts->get($id, contain: ['Markets', 'Students', 'Accounts']);
        $this->set(compact('operationsCost'));
    }

    public function add()
    {
        $operationsCost = $this->OperationsCosts->newEmptyEntity();
        
        // Se for estudante, definir automaticamente o student_id
        if ($this->isStudent()) {
            $currentStudentId = $this->getCurrentStudentId();
            $operationsCost->student_id = $currentStudentId;
        }
        
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            // Se for estudante, forçar o student_id para o estudante atual
            if ($this->isStudent()) {
                $data['student_id'] = $this->getCurrentStudentId();
            }
            
            $operationsCost = $this->OperationsCosts->patchEntity($operationsCost, $data);
            if ($this->OperationsCosts->save($operationsCost)) {
                $this->Flash->success(__('The operations cost has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The operations cost could not be saved. Please, try again.'));
        }
        
        $markets = $this->OperationsCosts->Markets->find('list', limit: 200)->all();
        $accounts = $this->OperationsCosts->Accounts->find('list', limit: 200)->all();
        
        $this->set(compact('operationsCost', 'markets', 'accounts'));
    }

    public function edit($id = null)
    {
        $operationsCost = $this->OperationsCosts->get($id, contain: []);
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            
            // Se for estudante, forçar o student_id para o estudante atual
            if ($this->isStudent()) {
                $data['student_id'] = $this->getCurrentStudentId();
            }
            
            $operationsCost = $this->OperationsCosts->patchEntity($operationsCost, $data);
            if ($this->OperationsCosts->save($operationsCost)) {
                $this->Flash->success(__('The operations cost has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The operations cost could not be saved. Please, try again.'));
        }
        $markets = $this->OperationsCosts->Markets->find('list', limit: 200)->all();
        $accounts = $this->OperationsCosts->Accounts->find('list', limit: 200)->all();
        $this->set(compact('operationsCost', 'markets', 'accounts'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $operationsCost = $this->OperationsCosts->get($id);
        if ($this->OperationsCosts->delete($operationsCost)) {
            $this->Flash->success(__('The operations cost has been deleted.'));
        } else {
            $this->Flash->error(__('The operations cost could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
