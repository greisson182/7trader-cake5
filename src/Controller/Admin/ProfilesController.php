<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

class ProfilesController extends AppController
{
    public function index()
    {
        $query = $this->Profiles->find();
        $profiles = $this->paginate($query);

        $this->set(compact('profiles'));
    }

    public function view($id = null)
    {
        $profile = $this->Profiles->get($id, contain: ['Permissions', 'Users']);
        $this->set(compact('profile'));
    }

    public function add()
    {
        $profile = $this->Profiles->newEmptyEntity();
        if ($this->request->is('post')) {
            $profile = $this->Profiles->patchEntity($profile, $this->request->getData());
            if ($this->Profiles->save($profile)) {
                $this->Flash->success(__('The profile has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The profile could not be saved. Please, try again.'));
        }
        $permissions = $this->Profiles->Permissions->find('list', limit: 200)->all();
        $this->set(compact('profile', 'permissions'));
    }

    public function edit($id = null)
    {
        $profile = $this->Profiles->get($id, contain: ['Permissions']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $profile = $this->Profiles->patchEntity($profile, $this->request->getData());
            if ($this->Profiles->save($profile)) {
                $this->Flash->success(__('The profile has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The profile could not be saved. Please, try again.'));
        }
        $permissions = $this->Profiles->Permissions->find('list', limit: 200)->all();
        $this->set(compact('profile', 'permissions'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $profile = $this->Profiles->get($id);
        if ($this->Profiles->delete($profile)) {
            $this->Flash->success(__('The profile has been deleted.'));
        } else {
            $this->Flash->error(__('The profile could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
