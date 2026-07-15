<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\EventInterface;

class MentorshipsController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->checkSession();
    }

    public function index()
    {
        try {
            $mentorships = $this->fetchTable('Mentorships')->find()
                ->orderBy(['order_position' => 'ASC', 'created' => 'DESC'])
                ->toArray();

            $this->set('mentorships', $mentorships);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao carregar mentorias: ' . $e->getMessage());
            return $this->redirect(['controller' => 'Welcome', 'action' => 'index']);
        }
    }

    public function view($id = null)
    {
        try {
            $mentorship = $this->fetchTable('Mentorships')->get($id);

            $ordersTable = $this->fetchTable('Orders');
            $totalOrders = $ordersTable->find()
                ->where(['item_type' => 'mentorship', 'item_id' => $id])
                ->count();

            $approvedOrders = $ordersTable->find()
                ->where(['item_type' => 'mentorship', 'item_id' => $id, 'status' => 'approved'])
                ->count();

            $stats = [
                'total_orders' => $totalOrders,
                'approved_orders' => $approvedOrders,
            ];

            $this->set('mentorship', $mentorship);
            $this->set('stats', $stats);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Mentoria não encontrada.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao carregar mentoria: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    public function add()
    {
        $mentorshipsTable = $this->fetchTable('Mentorships');
        $mentorship = $mentorshipsTable->newEmptyEntity();

        if ($this->request->is('post')) {
            try {
                $data = $this->request->getData();

                if (empty($data['name']) || empty($data['type'])) {
                    $this->Flash->error('Nome e tipo são obrigatórios.');
                    $this->set('mentorship', $mentorship);
                    return null;
                }

                $maxPosition = $mentorshipsTable->find()
                    ->select(['max_pos' => $mentorshipsTable->find()->func()->max('order_position')])
                    ->first();
                $nextPosition = ($maxPosition->max_pos ?? 0) + 1;

                $mentorshipData = [
                    'name' => $data['name'],
                    'type' => $data['type'],
                    'description' => $data['description'] ?? '',
                    'price' => (float)($data['price'] ?? 0.00),
                    'is_active' => isset($data['is_active']) ? 1 : 0,
                    'order_position' => $nextPosition,
                ];

                $mentorship = $mentorshipsTable->patchEntity($mentorship, $mentorshipData);

                if ($mentorshipsTable->save($mentorship)) {
                    $this->Flash->success('Mentoria criada com sucesso!');
                    return $this->redirect(['action' => 'index']);
                }

                $this->Flash->error('Erro ao salvar mentoria. Verifique os dados.');
            } catch (\Exception $e) {
                $this->Flash->error('Erro ao criar mentoria: ' . $e->getMessage());
            }
        }

        $this->set('mentorship', $mentorship);
    }

    public function edit($id = null)
    {
        try {
            $mentorshipsTable = $this->fetchTable('Mentorships');
            $mentorship = $mentorshipsTable->get($id);

            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();

                if (empty($data['name']) || empty($data['type'])) {
                    $this->Flash->error('Nome e tipo são obrigatórios.');
                    $this->set('mentorship', $mentorship);
                    return null;
                }

                $mentorshipData = [
                    'name' => $data['name'],
                    'type' => $data['type'],
                    'description' => $data['description'] ?? '',
                    'price' => (float)($data['price'] ?? 0.00),
                    'is_active' => isset($data['is_active']) ? 1 : 0,
                ];

                $mentorship = $mentorshipsTable->patchEntity($mentorship, $mentorshipData);

                if ($mentorshipsTable->save($mentorship)) {
                    $this->Flash->success('Mentoria atualizada com sucesso!');
                    return $this->redirect(['action' => 'view', $id]);
                }

                $this->Flash->error('Erro ao salvar mentoria. Verifique os dados.');
            }

            $this->set('mentorship', $mentorship);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Mentoria não encontrada.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao editar mentoria: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        try {
            $mentorshipsTable = $this->fetchTable('Mentorships');
            $mentorship = $mentorshipsTable->get($id);

            $ordersCount = $this->fetchTable('Orders')->find()
                ->where(['item_type' => 'mentorship', 'item_id' => $id])
                ->count();

            if ($ordersCount > 0) {
                $this->Flash->error('Não é possível excluir mentoria com pedidos associados. Desative-a ao invés de excluí-la.');
                return $this->redirect(['action' => 'view', $id]);
            }

            if ($mentorshipsTable->delete($mentorship)) {
                $this->Flash->success("Mentoria '{$mentorship->name}' excluída com sucesso!");
            } else {
                $this->Flash->error('Erro ao excluir mentoria.');
            }

            return $this->redirect(['action' => 'index']);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Mentoria não encontrada.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao excluir mentoria: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }
}
