<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

class CourseEnrollmentsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->CourseEnrollments->find()
            ->contain(['Students', 'Courses']);
        $courseEnrollments = $this->paginate($query);

        $this->set(compact('courseEnrollments'));
    }

    /**
     * View method
     *
     * @param string|null $id Course Enrollment id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $courseEnrollment = $this->CourseEnrollments->get($id, contain: ['Students', 'Courses']);
        $this->set(compact('courseEnrollment'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $courseEnrollment = $this->CourseEnrollments->newEmptyEntity();
        if ($this->request->is('post')) {
            $courseEnrollment = $this->CourseEnrollments->patchEntity($courseEnrollment, $this->request->getData());
            if ($this->CourseEnrollments->save($courseEnrollment)) {
                $this->Flash->success(__('The course enrollment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course enrollment could not be saved. Please, try again.'));
        }
        $students = $this->CourseEnrollments->Students->find('list', limit: 200)->all();
        $courses = $this->CourseEnrollments->Courses->find('list', limit: 200)->all();
        $this->set(compact('courseEnrollment', 'students', 'courses'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Course Enrollment id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $courseEnrollment = $this->CourseEnrollments->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseEnrollment = $this->CourseEnrollments->patchEntity($courseEnrollment, $this->request->getData());
            if ($this->CourseEnrollments->save($courseEnrollment)) {
                $this->Flash->success(__('The course enrollment has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course enrollment could not be saved. Please, try again.'));
        }
        $students = $this->CourseEnrollments->Students->find('list', limit: 200)->all();
        $courses = $this->CourseEnrollments->Courses->find('list', limit: 200)->all();
        $this->set(compact('courseEnrollment', 'students', 'courses'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Course Enrollment id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseEnrollment = $this->CourseEnrollments->get($id);
        if ($this->CourseEnrollments->delete($courseEnrollment)) {
            $this->Flash->success(__('The course enrollment has been deleted.'));
        } else {
            $this->Flash->error(__('The course enrollment could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
