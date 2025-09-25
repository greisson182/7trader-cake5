<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

class PermissionsProfilesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->PermissionsProfiles->find()
            ->contain(['Permissions', 'Profiles']);
        $permissionsProfiles = $this->paginate($query);

        $this->set(compact('permissionsProfiles'));
    }

    /**
     * View method
     *
     * @param string|null $id Permissions Profile id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $permissionsProfile = $this->PermissionsProfiles->get($id, contain: ['Permissions', 'Profiles']);
        $this->set(compact('permissionsProfile'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $permissionsProfile = $this->PermissionsProfiles->newEmptyEntity();
        if ($this->request->is('post')) {
            $permissionsProfile = $this->PermissionsProfiles->patchEntity($permissionsProfile, $this->request->getData());
            if ($this->PermissionsProfiles->save($permissionsProfile)) {
                $this->Flash->success(__('The permissions profile has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The permissions profile could not be saved. Please, try again.'));
        }
        $permissions = $this->PermissionsProfiles->Permissions->find('list', limit: 200)->all();
        $profiles = $this->PermissionsProfiles->Profiles->find('list', limit: 200)->all();
        $this->set(compact('permissionsProfile', 'permissions', 'profiles'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Permissions Profile id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $permissionsProfile = $this->PermissionsProfiles->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $permissionsProfile = $this->PermissionsProfiles->patchEntity($permissionsProfile, $this->request->getData());
            if ($this->PermissionsProfiles->save($permissionsProfile)) {
                $this->Flash->success(__('The permissions profile has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The permissions profile could not be saved. Please, try again.'));
        }
        $permissions = $this->PermissionsProfiles->Permissions->find('list', limit: 200)->all();
        $profiles = $this->PermissionsProfiles->Profiles->find('list', limit: 200)->all();
        $this->set(compact('permissionsProfile', 'permissions', 'profiles'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Permissions Profile id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $permissionsProfile = $this->PermissionsProfiles->get($id);
        if ($this->PermissionsProfiles->delete($permissionsProfile)) {
            $this->Flash->success(__('The permissions profile has been deleted.'));
        } else {
            $this->Flash->error(__('The permissions profile could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
