<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\EventInterface;

class OrdersController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->checkSession();
    }

    public function index()
    {
        $ordersTable = $this->fetchTable('Orders');

        $query = $ordersTable->find()
            ->contain(['Students'])
            ->orderBy(['Orders.created' => 'DESC']);

        $status = $this->request->getQuery('status');
        if ($status) {
            $query->where(['status' => $status]);
        }

        $itemType = $this->request->getQuery('item_type');
        if ($itemType) {
            $query->where(['item_type' => $itemType]);
        }

        $orders = $this->paginate($query);

        $this->set(compact('orders', 'status', 'itemType'));
    }

    public function view($id = null)
    {
        try {
            $ordersTable = $this->fetchTable('Orders');
            $order = $ordersTable->find()
                ->contain(['Students'])
                ->where(['Orders.id' => $id])
                ->firstOrFail();

            $itemTitle = null;
            if ($order->item_type === 'course') {
                $course = $this->fetchTable('Courses')->find()
                    ->where(['id' => $order->item_id])
                    ->first();
                $itemTitle = $course->title ?? null;
            } elseif ($order->item_type === 'mentorship') {
                $mentorship = $this->fetchTable('Mentorships')->find()
                    ->where(['id' => $order->item_id])
                    ->first();
                $itemTitle = $mentorship->name ?? null;
            }

            $this->set(compact('order', 'itemTitle'));
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Pedido não encontrado.');
            return $this->redirect(['action' => 'index']);
        }
    }

    public function editNotes($id = null)
    {
        $this->request->allowMethod(['post', 'put', 'patch']);

        try {
            $ordersTable = $this->fetchTable('Orders');
            $order = $ordersTable->get($id);

            $order = $ordersTable->patchEntity($order, [
                'notes' => $this->request->getData('notes'),
            ], ['fieldList' => ['notes']]);

            if ($ordersTable->save($order)) {
                $this->Flash->success('Observações salvas com sucesso!');
            } else {
                $this->Flash->error('Erro ao salvar observações.');
            }
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Pedido não encontrado.');
        }

        return $this->redirect(['action' => 'view', $id]);
    }
}
