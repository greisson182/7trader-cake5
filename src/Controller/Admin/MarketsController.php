<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

class MarketsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Markets->find();
        $markets = $this->paginate($query);

        $this->set(compact('markets'));
    }

    /**
     * View method
     *
     * @param string|null $id Market id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $market = $this->Markets->get($id, contain: ['Studies']);
        $this->set(compact('market'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $market = $this->Markets->newEmptyEntity();
        if ($this->request->is('post')) {
            $market = $this->Markets->patchEntity($market, $this->request->getData());
            if ($this->Markets->save($market)) {
                $this->Flash->success(__('The market has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The market could not be saved. Please, try again.'));
        }
        $this->set(compact('market'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Market id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $market = $this->Markets->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $market = $this->Markets->patchEntity($market, $this->request->getData());
            if ($this->Markets->save($market)) {
                $this->Flash->success(__('The market has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The market could not be saved. Please, try again.'));
        }
        $this->set(compact('market'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Market id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $market = $this->Markets->get($id);
        if ($this->Markets->delete($market)) {
            $this->Flash->success(__('The market has been deleted.'));
        } else {
            $this->Flash->error(__('The market could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
