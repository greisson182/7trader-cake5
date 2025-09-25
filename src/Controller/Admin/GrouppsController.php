<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

class GrouppsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Groupps->find()
            ->contain(['Types']);
        $groupps = $this->paginate($query);

        $this->set(compact('groupps'));
    }

    /**
     * View method
     *
     * @param string|null $id Groupp id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $groupp = $this->Groupps->get($id, contain: ['Types', 'Users']);
        $this->set(compact('groupp'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $groupp = $this->Groupps->newEmptyEntity();
        if ($this->request->is('post')) {
            $groupp = $this->Groupps->patchEntity($groupp, $this->request->getData());
            if ($this->Groupps->save($groupp)) {
                $this->Flash->success(__('The groupp has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The groupp could not be saved. Please, try again.'));
        }
        $types = $this->Groupps->Types->find('list', limit: 200)->all();
        $this->set(compact('groupp', 'types'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Groupp id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $groupp = $this->Groupps->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $groupp = $this->Groupps->patchEntity($groupp, $this->request->getData());
            if ($this->Groupps->save($groupp)) {
                $this->Flash->success(__('The groupp has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The groupp could not be saved. Please, try again.'));
        }
        $types = $this->Groupps->Types->find('list', limit: 200)->all();
        $this->set(compact('groupp', 'types'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Groupp id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $groupp = $this->Groupps->get($id);
        if ($this->Groupps->delete($groupp)) {
            $this->Flash->success(__('The groupp has been deleted.'));
        } else {
            $this->Flash->error(__('The groupp could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
