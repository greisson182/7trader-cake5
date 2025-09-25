<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

class StudentProgressController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->StudentProgress->find()
            ->contain(['Students', 'Courses', 'Videos']);
        $studentProgress = $this->paginate($query);

        $this->set(compact('studentProgress'));
    }

    /**
     * View method
     *
     * @param string|null $id Student Progres id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $studentProgres = $this->StudentProgress->get($id, contain: ['Students', 'Courses', 'Videos']);
        $this->set(compact('studentProgres'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $studentProgres = $this->StudentProgress->newEmptyEntity();
        if ($this->request->is('post')) {
            $studentProgres = $this->StudentProgress->patchEntity($studentProgres, $this->request->getData());
            if ($this->StudentProgress->save($studentProgres)) {
                $this->Flash->success(__('The student progres has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student progres could not be saved. Please, try again.'));
        }
        $students = $this->StudentProgress->Students->find('list', limit: 200)->all();
        $courses = $this->StudentProgress->Courses->find('list', limit: 200)->all();
        $videos = $this->StudentProgress->Videos->find('list', limit: 200)->all();
        $this->set(compact('studentProgres', 'students', 'courses', 'videos'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Student Progres id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $studentProgres = $this->StudentProgress->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $studentProgres = $this->StudentProgress->patchEntity($studentProgres, $this->request->getData());
            if ($this->StudentProgress->save($studentProgres)) {
                $this->Flash->success(__('The student progres has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The student progres could not be saved. Please, try again.'));
        }
        $students = $this->StudentProgress->Students->find('list', limit: 200)->all();
        $courses = $this->StudentProgress->Courses->find('list', limit: 200)->all();
        $videos = $this->StudentProgress->Videos->find('list', limit: 200)->all();
        $this->set(compact('studentProgres', 'students', 'courses', 'videos'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Student Progres id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $studentProgres = $this->StudentProgress->get($id);
        if ($this->StudentProgress->delete($studentProgres)) {
            $this->Flash->success(__('The student progres has been deleted.'));
        } else {
            $this->Flash->error(__('The student progres could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
