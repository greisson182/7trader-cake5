<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

class CourseVideosController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->CourseVideos->find()
            ->contain(['Courses']);
        $courseVideos = $this->paginate($query);

        $this->set(compact('courseVideos'));
    }

    /**
     * View method
     *
     * @param string|null $id Course Video id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $courseVideo = $this->CourseVideos->get($id, contain: ['Courses']);
        $this->set(compact('courseVideo'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $courseVideo = $this->CourseVideos->newEmptyEntity();
        if ($this->request->is('post')) {
            $courseVideo = $this->CourseVideos->patchEntity($courseVideo, $this->request->getData());
            if ($this->CourseVideos->save($courseVideo)) {
                $this->Flash->success(__('The course video has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course video could not be saved. Please, try again.'));
        }
        $courses = $this->CourseVideos->Courses->find('list', limit: 200)->all();
        $this->set(compact('courseVideo', 'courses'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Course Video id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $courseVideo = $this->CourseVideos->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $courseVideo = $this->CourseVideos->patchEntity($courseVideo, $this->request->getData());
            if ($this->CourseVideos->save($courseVideo)) {
                $this->Flash->success(__('The course video has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The course video could not be saved. Please, try again.'));
        }
        $courses = $this->CourseVideos->Courses->find('list', limit: 200)->all();
        $this->set(compact('courseVideo', 'courses'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Course Video id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $courseVideo = $this->CourseVideos->get($id);
        if ($this->CourseVideos->delete($courseVideo)) {
            $this->Flash->success(__('The course video has been deleted.'));
        } else {
            $this->Flash->error(__('The course video could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
