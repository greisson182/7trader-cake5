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

    /**
     * Listagem de mentorias para o aluno (compra/status), separada da
     * listagem administrativa (index).
     */
    public function indexStudents()
    {
        try {
            $mentorships = $this->fetchTable('Mentorships')->find()
                ->where(['is_active' => 1])
                ->orderBy(['order_position' => 'ASC', 'created' => 'ASC'])
                ->toArray();

            // Não há tabela de matrícula de mentoria — a posse é derivada do
            // pedido aprovado (mesma lógica de CoursesController::indexStudents
            // para inscrições de curso).
            $mentorshipOrders = [];
            if ($this->isStudent()) {
                $studentId = $this->getCurrentStudentId();
                $approvedOrders = $this->fetchTable('Orders')->find()
                    ->where([
                        'student_id' => $studentId,
                        'item_type' => 'mentorship',
                        'status' => 'approved',
                    ])
                    ->toArray();

                foreach ($approvedOrders as $order) {
                    $mentorshipOrders[$order->item_id] = $order;
                }
            }

            $this->set('mentorships', $mentorships);
            $this->set('mentorshipOrders', $mentorshipOrders);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao carregar mentorias: ' . $e->getMessage());
            return $this->redirect(['controller' => 'Welcome', 'action' => 'index']);
        }
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
                    'thumbnail' => $data['thumbnail'] ?? '',
                    'price' => (float)($data['price'] ?? 0.00),
                    'is_active' => isset($data['is_active']) ? 1 : 0,
                    'order_position' => $nextPosition,
                ];

                $uploadedThumbnail = $this->handleThumbnailUpload();
                if ($uploadedThumbnail !== null) {
                    $mentorshipData['thumbnail'] = $uploadedThumbnail;
                }

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
                    // Mantém a thumbnail atual por padrão: só troca se um novo arquivo for enviado.
                    'thumbnail' => $mentorship->thumbnail,
                    'price' => (float)($data['price'] ?? 0.00),
                    'is_active' => isset($data['is_active']) ? 1 : 0,
                ];

                $uploadedThumbnail = $this->handleThumbnailUpload();
                if ($uploadedThumbnail !== null) {
                    $mentorshipData['thumbnail'] = $uploadedThumbnail;
                }

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

    // Gerenciar vídeos da mentoria (mesmo modelo de módulos/vídeos dos cursos)
    public function videos($mentorshipId = null)
    {
        try {
            $mentorship = $this->fetchTable('Mentorships')->get($mentorshipId);

            $videos = $this->fetchTable('MentorshipVideos')->find()
                ->where(['mentorship_id' => $mentorshipId])
                ->orderBy(['order_position' => 'ASC', 'created' => 'ASC'])
                ->toArray();

            $modules = $this->fetchTable('MentorshipModules')->find()
                ->where(['mentorship_id' => $mentorshipId])
                ->orderBy(['order_position' => 'ASC', 'created' => 'ASC'])
                ->toArray();

            $videosByModule = [];
            $ungroupedVideos = [];
            foreach ($videos as $video) {
                if ($video->module_id) {
                    $videosByModule[$video->module_id][] = $video;
                } else {
                    $ungroupedVideos[] = $video;
                }
            }
            foreach ($modules as $module) {
                $module->mentorship_videos = $videosByModule[$module->id] ?? [];
            }

            $this->set('mentorship', $mentorship);
            $this->set('videos', $videos);
            $this->set('modules', $modules);
            $this->set('ungroupedVideos', $ungroupedVideos);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Mentoria não encontrada.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao carregar vídeos: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    public function addModule($mentorshipId = null)
    {
        try {
            $mentorship = $this->fetchTable('Mentorships')->get($mentorshipId);

            if ($this->request->is('post')) {
                $data = $this->request->getData();

                if (empty($data['title'])) {
                    $this->Flash->error('O título do módulo é obrigatório.');
                    $this->set('mentorship', $mentorship);
                    $this->set('formData', $data);
                    return null;
                }

                $modulesTable = $this->fetchTable('MentorshipModules');

                $maxPosition = $modulesTable->find()
                    ->select(['max_pos' => $modulesTable->find()->func()->max('order_position')])
                    ->where(['mentorship_id' => $mentorshipId])
                    ->first();
                $nextPosition = (int)($maxPosition->max_pos ?? 0) + 1;

                $module = $modulesTable->newEmptyEntity();
                $module = $modulesTable->patchEntity($module, [
                    'mentorship_id' => $mentorshipId,
                    'title' => $data['title'],
                    'description' => $data['description'] ?? '',
                    'order_position' => $nextPosition,
                    'is_active' => isset($data['is_active']) ? 1 : 0,
                ]);

                if ($modulesTable->save($module)) {
                    $this->Flash->success('Módulo adicionado com sucesso!');
                    return $this->redirect(['action' => 'videos', $mentorshipId]);
                }

                $this->Flash->error('Erro ao salvar módulo: ' . $this->flattenEntityErrors($module));
                $this->set('formData', $data);
            }

            $this->set('mentorship', $mentorship);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Mentoria não encontrada.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao adicionar módulo: ' . $e->getMessage());
            return $this->redirect(['action' => 'videos', $mentorshipId]);
        }
    }

    public function editModule($moduleId = null)
    {
        if (!$moduleId) {
            $this->Flash->error('ID do módulo é obrigatório.');
            return $this->redirect(['action' => 'index']);
        }

        try {
            $modulesTable = $this->fetchTable('MentorshipModules');
            $module = $modulesTable->get($moduleId);

            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();

                if (empty($data['title'])) {
                    $this->Flash->error('O título do módulo é obrigatório.');
                    $this->set('module', $module);
                    return null;
                }

                $module = $modulesTable->patchEntity($module, [
                    'title' => $data['title'],
                    'description' => $data['description'] ?? '',
                    'is_active' => isset($data['is_active']) ? 1 : 0,
                ]);

                if ($modulesTable->save($module)) {
                    $this->Flash->success('Módulo atualizado com sucesso!');
                    return $this->redirect(['action' => 'videos', $module->mentorship_id]);
                }

                $this->Flash->error('Erro ao atualizar módulo: ' . $this->flattenEntityErrors($module));
            }

            $this->set('module', $module);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Módulo não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao editar módulo: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    public function deleteModule($moduleId = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        try {
            $modulesTable = $this->fetchTable('MentorshipModules');
            $module = $modulesTable->get($moduleId);
            $mentorshipId = $module->mentorship_id;

            // Vídeos do módulo ficam "sem módulo" (FK ON DELETE SET NULL), não são apagados.
            if ($modulesTable->delete($module)) {
                $this->Flash->success('Módulo excluído. Os vídeos continuam na mentoria, sem módulo.');
            } else {
                $this->Flash->error('Erro ao excluir módulo.');
            }

            return $this->redirect(['action' => 'videos', $mentorshipId]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Módulo não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao excluir módulo: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Atualiza o módulo e a ordem de uma lista de vídeos de uma vez (drag &
     * drop entre módulos na tela de gerenciar vídeos).
     */
    public function reorderVideos()
    {
        $this->request->allowMethod(['post']);
        $this->autoRender = false;

        try {
            $data = $this->request->getData();
            $videoIds = $data['video_ids'] ?? [];
            $moduleId = !empty($data['module_id']) ? (int)$data['module_id'] : null;

            if (empty($videoIds)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Nenhum vídeo informado.']));
            }

            $mentorshipVideosTable = $this->fetchTable('MentorshipVideos');

            $videos = $mentorshipVideosTable->find()
                ->where(['id IN' => $videoIds])
                ->toArray();

            if (empty($videos)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Vídeos não encontrados.']));
            }

            $mentorshipId = $videos[0]->mentorship_id;

            if ($moduleId) {
                $module = $this->fetchTable('MentorshipModules')->find()
                    ->where(['id' => $moduleId, 'mentorship_id' => $mentorshipId])
                    ->first();

                if (!$module) {
                    return $this->response->withStatus(400)->withType('application/json')
                        ->withStringBody(json_encode(['success' => false, 'message' => 'Módulo inválido para esta mentoria.']));
                }
            }

            $videosById = [];
            foreach ($videos as $video) {
                $videosById[$video->id] = $video;
            }

            $connection = $mentorshipVideosTable->getConnection();
            $connection->transactional(function () use ($videoIds, $videosById, $moduleId, $mentorshipId, $mentorshipVideosTable) {
                $position = 1;
                foreach ($videoIds as $videoId) {
                    $video = $videosById[$videoId] ?? null;
                    if (!$video || $video->mentorship_id !== $mentorshipId) {
                        continue;
                    }

                    $video = $mentorshipVideosTable->patchEntity($video, [
                        'module_id' => $moduleId,
                        'order_position' => $position,
                    ]);
                    $mentorshipVideosTable->save($video);
                    $position++;
                }
            });

            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => true]));
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => $e->getMessage()]));
        }
    }

    public function addVideo($mentorshipId = null)
    {
        try {
            $mentorship = $this->fetchTable('Mentorships')->get($mentorshipId);

            $modules = $this->fetchTable('MentorshipModules')->find()
                ->where(['mentorship_id' => $mentorshipId])
                ->orderBy(['order_position' => 'ASC', 'created' => 'ASC'])
                ->toArray();

            if ($this->request->is('post')) {
                $data = $this->request->getData();

                if (empty($data['title']) || empty($data['video_url'])) {
                    $this->Flash->error('Título e URL do vídeo são obrigatórios.');
                    $this->set('mentorship', $mentorship);
                    $this->set('modules', $modules);
                    $this->set('formData', $data);
                    return null;
                }

                $mentorshipVideosTable = $this->fetchTable('MentorshipVideos');

                $maxPosition = $mentorshipVideosTable->find()
                    ->select(['max_pos' => $mentorshipVideosTable->find()->func()->max('order_position')])
                    ->where(['mentorship_id' => $mentorshipId])
                    ->first();
                $nextPosition = (int)($maxPosition->max_pos ?? 0) + 1;

                $durationSeconds = ((int)($data['duration_minutes'] ?? 0) * 60) + (int)($data['duration_seconds'] ?? 0);

                $videoData = [
                    'mentorship_id' => $mentorshipId,
                    'module_id' => !empty($data['module_id']) ? (int)$data['module_id'] : null,
                    'title' => $data['title'],
                    'description' => $data['description'] ?? '',
                    'video_url' => $data['video_url'],
                    'video_type' => $data['video_type'] ?? 'youtube',
                    'duration_seconds' => $durationSeconds,
                    'order_position' => $nextPosition,
                    'is_preview' => isset($data['is_preview']) ? 1 : 0,
                    'is_active' => isset($data['is_active']) ? 1 : 0,
                ];

                $video = $mentorshipVideosTable->newEmptyEntity();
                $video = $mentorshipVideosTable->patchEntity($video, $videoData);

                if ($mentorshipVideosTable->save($video)) {
                    $this->Flash->success('Vídeo adicionado com sucesso!');
                    return $this->redirect(['action' => 'videos', $mentorshipId]);
                } else {
                    $this->Flash->error('Erro ao salvar vídeo: ' . $this->flattenEntityErrors($video));
                    $this->set('formData', $data);
                }
            }

            $this->set('mentorship', $mentorship);
            $this->set('modules', $modules);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Mentoria não encontrada.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao adicionar vídeo: ' . $e->getMessage());
            return $this->redirect(['action' => 'videos', $mentorshipId]);
        }
    }

    public function editVideo($videoId = null)
    {
        if (!$videoId) {
            $this->Flash->error('ID do vídeo é obrigatório.');
            return $this->redirect(['action' => 'index']);
        }

        try {
            $mentorshipVideosTable = $this->fetchTable('MentorshipVideos');
            $video = $mentorshipVideosTable->get($videoId);

            $modules = $this->fetchTable('MentorshipModules')->find()
                ->where(['mentorship_id' => $video->mentorship_id])
                ->orderBy(['order_position' => 'ASC', 'created' => 'ASC'])
                ->toArray();

            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();

                if (empty($data['title']) || empty($data['video_url'])) {
                    $this->Flash->error('Título e URL do vídeo são obrigatórios.');
                    $this->set('video', $video);
                    $this->set('modules', $modules);
                    return null;
                }

                $durationSeconds = ((int)($data['duration_minutes'] ?? 0) * 60) + (int)($data['duration_seconds'] ?? 0);

                $videoData = [
                    'module_id' => !empty($data['module_id']) ? (int)$data['module_id'] : null,
                    'title' => $data['title'],
                    'description' => $data['description'] ?? '',
                    'video_url' => $data['video_url'],
                    'video_type' => $data['video_type'] ?? 'youtube',
                    'duration_seconds' => $durationSeconds,
                    'is_active' => isset($data['is_active']) ? 1 : 0,
                ];

                $video = $mentorshipVideosTable->patchEntity($video, $videoData);

                if ($mentorshipVideosTable->save($video)) {
                    $this->Flash->success('Vídeo atualizado com sucesso!');
                    return $this->redirect(['action' => 'videos', $video->mentorship_id]);
                } else {
                    $this->Flash->error('Erro ao atualizar vídeo: ' . $this->flattenEntityErrors($video));
                }
            }

            $this->set('video', $video);
            $this->set('modules', $modules);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Vídeo não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao editar vídeo: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    public function deleteVideo($videoId = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        try {
            $mentorshipVideosTable = $this->fetchTable('MentorshipVideos');
            $video = $mentorshipVideosTable->get($videoId);
            $mentorshipId = $video->mentorship_id;

            if ($mentorshipVideosTable->delete($video)) {
                $this->Flash->success('Vídeo excluído com sucesso!');
            } else {
                $this->Flash->error('Erro ao excluir vídeo.');
            }

            return $this->redirect(['action' => 'videos', $mentorshipId]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Vídeo não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao excluir vídeo: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    public function watchVideo($videoId = null)
    {
        if (!$videoId) {
            $this->Flash->error('ID do vídeo não fornecido.');
            return $this->redirect(['action' => 'index']);
        }

        try {
            $video = $this->fetchTable('MentorshipVideos')->find()
                ->select([
                    'MentorshipVideos.id',
                    'MentorshipVideos.title',
                    'MentorshipVideos.description',
                    'MentorshipVideos.video_url',
                    'MentorshipVideos.video_type',
                    'MentorshipVideos.duration_seconds',
                    'MentorshipVideos.mentorship_id',
                    'MentorshipVideos.is_active',
                    'MentorshipVideos.created',
                    'MentorshipVideos.modified',
                    'mentorship_name' => 'Mentorships.name',
                ])
                ->leftJoinWith('Mentorships')
                ->where([
                    'MentorshipVideos.id' => $videoId,
                    'MentorshipVideos.is_active' => 1,
                ])
                ->first();

            if (!$video) {
                $this->Flash->error('Vídeo não encontrado ou inativo.');
                return $this->redirect(['action' => 'index']);
            }

            $this->set('video', $video);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao carregar vídeo: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    // ===== Mentoria particular: cada compra é uma instância isolada, com seus
    // próprios módulos/vídeos (gravações das sessões daquele aluno específico). =====

    /**
     * Lista os alunos (instâncias) de uma mentoria particular.
     */
    public function instances($mentorshipId = null)
    {
        try {
            $mentorship = $this->fetchTable('Mentorships')->get($mentorshipId);

            $instances = $this->fetchTable('MentorshipInstances')->find()
                ->contain(['Students', 'Orders'])
                ->where(['MentorshipInstances.mentorship_id' => $mentorshipId])
                ->orderBy(['MentorshipInstances.created' => 'DESC'])
                ->toArray();

            foreach ($instances as $instance) {
                $instance->video_count = $this->fetchTable('MentorshipVideos')->find()
                    ->where(['instance_id' => $instance->id])
                    ->count();
            }

            $this->set('mentorship', $mentorship);
            $this->set('instances', $instances);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Mentoria não encontrada.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao carregar alunos: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    // Gerenciar vídeos de UMA instância (um aluno específico de mentoria particular)
    public function instanceVideos($instanceId = null)
    {
        try {
            $instance = $this->fetchTable('MentorshipInstances')->find()
                ->contain(['Students', 'Mentorships'])
                ->where(['MentorshipInstances.id' => $instanceId])
                ->firstOrFail();

            $videos = $this->fetchTable('MentorshipVideos')->find()
                ->where(['instance_id' => $instanceId])
                ->orderBy(['order_position' => 'ASC', 'created' => 'ASC'])
                ->toArray();

            $modules = $this->fetchTable('MentorshipModules')->find()
                ->where(['instance_id' => $instanceId])
                ->orderBy(['order_position' => 'ASC', 'created' => 'ASC'])
                ->toArray();

            $videosByModule = [];
            $ungroupedVideos = [];
            foreach ($videos as $video) {
                if ($video->module_id) {
                    $videosByModule[$video->module_id][] = $video;
                } else {
                    $ungroupedVideos[] = $video;
                }
            }
            foreach ($modules as $module) {
                $module->mentorship_videos = $videosByModule[$module->id] ?? [];
            }

            $this->set('instance', $instance);
            $this->set('videos', $videos);
            $this->set('modules', $modules);
            $this->set('ungroupedVideos', $ungroupedVideos);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Aluno/instância não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao carregar vídeos: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    public function addInstanceModule($instanceId = null)
    {
        try {
            $instance = $this->fetchTable('MentorshipInstances')->contain(['Students'])->get($instanceId);

            if ($this->request->is('post')) {
                $data = $this->request->getData();

                if (empty($data['title'])) {
                    $this->Flash->error('O título do módulo é obrigatório.');
                    $this->set('instance', $instance);
                    $this->set('formData', $data);
                    return null;
                }

                $modulesTable = $this->fetchTable('MentorshipModules');

                $maxPosition = $modulesTable->find()
                    ->select(['max_pos' => $modulesTable->find()->func()->max('order_position')])
                    ->where(['instance_id' => $instanceId])
                    ->first();
                $nextPosition = (int)($maxPosition->max_pos ?? 0) + 1;

                $module = $modulesTable->newEmptyEntity();
                $module = $modulesTable->patchEntity($module, [
                    'mentorship_id' => $instance->mentorship_id,
                    'instance_id' => $instanceId,
                    'title' => $data['title'],
                    'description' => $data['description'] ?? '',
                    'order_position' => $nextPosition,
                    'is_active' => isset($data['is_active']) ? 1 : 0,
                ]);

                if ($modulesTable->save($module)) {
                    $this->Flash->success('Módulo adicionado com sucesso!');
                    return $this->redirect(['action' => 'instanceVideos', $instanceId]);
                }

                $this->Flash->error('Erro ao salvar módulo: ' . $this->flattenEntityErrors($module));
                $this->set('formData', $data);
            }

            $this->set('instance', $instance);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Aluno/instância não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao adicionar módulo: ' . $e->getMessage());
            return $this->redirect(['action' => 'instanceVideos', $instanceId]);
        }
    }

    public function editInstanceModule($moduleId = null)
    {
        if (!$moduleId) {
            $this->Flash->error('ID do módulo é obrigatório.');
            return $this->redirect(['action' => 'index']);
        }

        try {
            $modulesTable = $this->fetchTable('MentorshipModules');
            $module = $modulesTable->get($moduleId);

            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();

                if (empty($data['title'])) {
                    $this->Flash->error('O título do módulo é obrigatório.');
                    $this->set('module', $module);
                    return null;
                }

                $module = $modulesTable->patchEntity($module, [
                    'title' => $data['title'],
                    'description' => $data['description'] ?? '',
                    'is_active' => isset($data['is_active']) ? 1 : 0,
                ]);

                if ($modulesTable->save($module)) {
                    $this->Flash->success('Módulo atualizado com sucesso!');
                    return $this->redirect(['action' => 'instanceVideos', $module->instance_id]);
                }

                $this->Flash->error('Erro ao atualizar módulo: ' . $this->flattenEntityErrors($module));
            }

            $this->set('module', $module);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Módulo não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao editar módulo: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    public function deleteInstanceModule($moduleId = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        try {
            $modulesTable = $this->fetchTable('MentorshipModules');
            $module = $modulesTable->get($moduleId);
            $instanceId = $module->instance_id;

            if ($modulesTable->delete($module)) {
                $this->Flash->success('Módulo excluído. Os vídeos continuam na mentoria, sem módulo.');
            } else {
                $this->Flash->error('Erro ao excluir módulo.');
            }

            return $this->redirect(['action' => 'instanceVideos', $instanceId]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Módulo não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao excluir módulo: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    public function reorderInstanceVideos()
    {
        $this->request->allowMethod(['post']);
        $this->autoRender = false;

        try {
            $data = $this->request->getData();
            $videoIds = $data['video_ids'] ?? [];
            $moduleId = !empty($data['module_id']) ? (int)$data['module_id'] : null;

            if (empty($videoIds)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Nenhum vídeo informado.']));
            }

            $mentorshipVideosTable = $this->fetchTable('MentorshipVideos');

            $videos = $mentorshipVideosTable->find()
                ->where(['id IN' => $videoIds])
                ->toArray();

            if (empty($videos)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Vídeos não encontrados.']));
            }

            $instanceId = $videos[0]->instance_id;

            if ($moduleId) {
                $module = $this->fetchTable('MentorshipModules')->find()
                    ->where(['id' => $moduleId, 'instance_id' => $instanceId])
                    ->first();

                if (!$module) {
                    return $this->response->withStatus(400)->withType('application/json')
                        ->withStringBody(json_encode(['success' => false, 'message' => 'Módulo inválido para esta instância.']));
                }
            }

            $videosById = [];
            foreach ($videos as $video) {
                $videosById[$video->id] = $video;
            }

            $connection = $mentorshipVideosTable->getConnection();
            $connection->transactional(function () use ($videoIds, $videosById, $moduleId, $instanceId, $mentorshipVideosTable) {
                $position = 1;
                foreach ($videoIds as $videoId) {
                    $video = $videosById[$videoId] ?? null;
                    if (!$video || $video->instance_id !== $instanceId) {
                        continue;
                    }

                    $video = $mentorshipVideosTable->patchEntity($video, [
                        'module_id' => $moduleId,
                        'order_position' => $position,
                    ]);
                    $mentorshipVideosTable->save($video);
                    $position++;
                }
            });

            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => true]));
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => $e->getMessage()]));
        }
    }

    public function addInstanceVideo($instanceId = null)
    {
        try {
            $instance = $this->fetchTable('MentorshipInstances')->contain(['Students'])->get($instanceId);

            $modules = $this->fetchTable('MentorshipModules')->find()
                ->where(['instance_id' => $instanceId])
                ->orderBy(['order_position' => 'ASC', 'created' => 'ASC'])
                ->toArray();

            if ($this->request->is('post')) {
                $data = $this->request->getData();

                if (empty($data['title']) || empty($data['video_url'])) {
                    $this->Flash->error('Título e URL do vídeo são obrigatórios.');
                    $this->set('instance', $instance);
                    $this->set('modules', $modules);
                    $this->set('formData', $data);
                    return null;
                }

                $mentorshipVideosTable = $this->fetchTable('MentorshipVideos');

                $maxPosition = $mentorshipVideosTable->find()
                    ->select(['max_pos' => $mentorshipVideosTable->find()->func()->max('order_position')])
                    ->where(['instance_id' => $instanceId])
                    ->first();
                $nextPosition = (int)($maxPosition->max_pos ?? 0) + 1;

                $durationSeconds = ((int)($data['duration_minutes'] ?? 0) * 60) + (int)($data['duration_seconds'] ?? 0);

                $videoData = [
                    'mentorship_id' => $instance->mentorship_id,
                    'instance_id' => $instanceId,
                    'module_id' => !empty($data['module_id']) ? (int)$data['module_id'] : null,
                    'title' => $data['title'],
                    'description' => $data['description'] ?? '',
                    'video_url' => $data['video_url'],
                    'video_type' => $data['video_type'] ?? 'youtube',
                    'duration_seconds' => $durationSeconds,
                    'order_position' => $nextPosition,
                    'is_preview' => 0,
                    'is_active' => isset($data['is_active']) ? 1 : 0,
                ];

                $video = $mentorshipVideosTable->newEmptyEntity();
                $video = $mentorshipVideosTable->patchEntity($video, $videoData);

                if ($mentorshipVideosTable->save($video)) {
                    $this->Flash->success('Vídeo adicionado com sucesso!');
                    return $this->redirect(['action' => 'instanceVideos', $instanceId]);
                } else {
                    $this->Flash->error('Erro ao salvar vídeo: ' . $this->flattenEntityErrors($video));
                    $this->set('formData', $data);
                }
            }

            $this->set('instance', $instance);
            $this->set('modules', $modules);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Aluno/instância não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao adicionar vídeo: ' . $e->getMessage());
            return $this->redirect(['action' => 'instanceVideos', $instanceId]);
        }
    }

    public function editInstanceVideo($videoId = null)
    {
        if (!$videoId) {
            $this->Flash->error('ID do vídeo é obrigatório.');
            return $this->redirect(['action' => 'index']);
        }

        try {
            $mentorshipVideosTable = $this->fetchTable('MentorshipVideos');
            $video = $mentorshipVideosTable->get($videoId);

            $modules = $this->fetchTable('MentorshipModules')->find()
                ->where(['instance_id' => $video->instance_id])
                ->orderBy(['order_position' => 'ASC', 'created' => 'ASC'])
                ->toArray();

            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();

                if (empty($data['title']) || empty($data['video_url'])) {
                    $this->Flash->error('Título e URL do vídeo são obrigatórios.');
                    $this->set('video', $video);
                    $this->set('modules', $modules);
                    return null;
                }

                $durationSeconds = ((int)($data['duration_minutes'] ?? 0) * 60) + (int)($data['duration_seconds'] ?? 0);

                $videoData = [
                    'module_id' => !empty($data['module_id']) ? (int)$data['module_id'] : null,
                    'title' => $data['title'],
                    'description' => $data['description'] ?? '',
                    'video_url' => $data['video_url'],
                    'video_type' => $data['video_type'] ?? 'youtube',
                    'duration_seconds' => $durationSeconds,
                    'is_active' => isset($data['is_active']) ? 1 : 0,
                ];

                $video = $mentorshipVideosTable->patchEntity($video, $videoData);

                if ($mentorshipVideosTable->save($video)) {
                    $this->Flash->success('Vídeo atualizado com sucesso!');
                    return $this->redirect(['action' => 'instanceVideos', $video->instance_id]);
                } else {
                    $this->Flash->error('Erro ao atualizar vídeo: ' . $this->flattenEntityErrors($video));
                }
            }

            $this->set('video', $video);
            $this->set('modules', $modules);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Vídeo não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao editar vídeo: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    public function deleteInstanceVideo($videoId = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        try {
            $mentorshipVideosTable = $this->fetchTable('MentorshipVideos');
            $video = $mentorshipVideosTable->get($videoId);
            $instanceId = $video->instance_id;

            if ($mentorshipVideosTable->delete($video)) {
                $this->Flash->success('Vídeo excluído com sucesso!');
            } else {
                $this->Flash->error('Erro ao excluir vídeo.');
            }

            return $this->redirect(['action' => 'instanceVideos', $instanceId]);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Vídeo não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao excluir vídeo: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    /**
     * Visualizar detalhes de uma mentoria (aluno)
     */
    public function viewStudents($mentorshipId = null)
    {
        try {
            $mentorship = $this->fetchTable('Mentorships')->find()
                ->where(['id' => $mentorshipId, 'is_active' => 1])
                ->first();

            if (!$mentorship) {
                $this->Flash->error('Mentoria não encontrada.');
                return $this->redirect(['action' => 'indexStudents']);
            }

            // Total real de vídeos/duração da mentoria (todos os ativos, não só os
            // visíveis antes da compra) — usado no card de informações gerais.
            $allActiveVideos = $this->fetchTable('MentorshipVideos')->find()
                ->select(['duration_seconds'])
                ->where(['mentorship_id' => $mentorshipId, 'is_active' => 1])
                ->toArray();
            $mentorship->video_count = count($allActiveVideos);
            $mentorship->total_duration = array_sum(array_column($allActiveVideos, 'duration_seconds'));

            // Posse derivada de pedido aprovado (não há tabela de matrícula de mentoria).
            $isAcquired = false;
            $order = null;
            $progress = [];
            // Mentoria particular: cada aluno enxerga só a própria instância (gravações
            // exclusivas dele), nunca o conteúdo de outro aluno.
            $instanceId = null;

            if ($this->isStudent()) {
                $studentId = $this->getCurrentStudentId();

                $order = $this->fetchTable('Orders')->find()
                    ->where([
                        'student_id' => $studentId,
                        'item_type' => 'mentorship',
                        'item_id' => $mentorshipId,
                        'status' => 'approved',
                    ])
                    ->first();
                $isAcquired = (bool)$order;

                if ($isAcquired && $mentorship->type === 'particular') {
                    $instance = $this->fetchTable('MentorshipInstances')->find()
                        ->where(['mentorship_id' => $mentorshipId, 'student_id' => $studentId])
                        ->first();
                    $instanceId = $instance->id ?? null;
                    // Sem instância ainda (ex.: webhook não processou), trata como não adquirida.
                    $isAcquired = (bool)$instanceId;
                }

                if ($isAcquired) {
                    $progressData = $this->fetchTable('MentorshipProgress')->find()
                        ->where(['student_id' => $studentId, 'mentorship_id' => $mentorshipId])
                        ->toArray();

                    foreach ($progressData as $p) {
                        $progress[$p->video_id] = $p;
                    }
                }
            }

            if ($mentorship->type === 'particular') {
                // Sem instância, o aluno não vê nada (não existe preview de conteúdo privado).
                $videosCondition = ['is_active' => 1, 'instance_id' => $instanceId ?? -1];
                $modulesCondition = ['is_active' => 1, 'instance_id' => $instanceId ?? -1];
            } else {
                $videosCondition = ['mentorship_id' => $mentorshipId, 'is_active' => 1];
                $modulesCondition = ['mentorship_id' => $mentorshipId, 'is_active' => 1];
                if (!$isAcquired) {
                    $videosCondition['is_preview'] = 1;
                }
            }

            $videos = $this->fetchTable('MentorshipVideos')->find()
                ->where($videosCondition)
                ->orderBy(['order_position' => 'ASC', 'created' => 'ASC'])
                ->toArray();

            $modules = $this->fetchTable('MentorshipModules')->find()
                ->where($modulesCondition)
                ->orderBy(['order_position' => 'ASC', 'created' => 'ASC'])
                ->toArray();

            $videosByModule = [];
            $ungroupedVideos = [];
            foreach ($videos as $video) {
                if ($video->module_id) {
                    $videosByModule[$video->module_id][] = $video;
                } else {
                    $ungroupedVideos[] = $video;
                }
            }
            $modules = array_values(array_filter($modules, function ($module) use ($videosByModule) {
                return !empty($videosByModule[$module->id]);
            }));
            foreach ($modules as $module) {
                $module->mentorship_videos = $videosByModule[$module->id];
            }

            $this->set('mentorship', $mentorship);
            $this->set('videos', $videos);
            $this->set('modules', $modules);
            $this->set('ungroupedVideos', $ungroupedVideos);
            $this->set('isAcquired', $isAcquired);
            $this->set('order', $order);
            $this->set('progress', $progress);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao carregar mentoria: ' . $e->getMessage());
            return $this->redirect(['action' => 'indexStudents']);
        }
    }

    /**
     * Assistir um vídeo específico da mentoria
     */
    public function watchStudents($mentorshipId = null, $videoId = null)
    {
        try {
            if (!$this->isStudent()) {
                $this->Flash->error('Apenas estudantes podem assistir vídeos.');
                return $this->redirect(['action' => 'indexStudents']);
            }

            $studentId = $this->getCurrentStudentId();

            $mentorship = $this->fetchTable('Mentorships')->find()
                ->where(['id' => $mentorshipId, 'is_active' => 1])
                ->first();

            if (!$mentorship) {
                $this->Flash->error('Mentoria não encontrada.');
                return $this->redirect(['action' => 'indexStudents']);
            }

            $video = $this->fetchTable('MentorshipVideos')->find()
                ->where([
                    'id' => $videoId,
                    'mentorship_id' => $mentorshipId,
                    'is_active' => 1,
                ])
                ->first();

            if (!$video) {
                $this->Flash->error('Vídeo não encontrado.');
                return $this->redirect(['action' => 'viewStudents', $mentorshipId]);
            }

            $canWatch = false;
            $instanceId = null;

            if ($mentorship->type === 'particular') {
                // Conteúdo privado: só o dono da instância pode assistir — nunca há
                // preview público de gravação de sessão de outro aluno.
                $instance = $this->fetchTable('MentorshipInstances')->find()
                    ->where(['mentorship_id' => $mentorshipId, 'student_id' => $studentId])
                    ->first();
                $instanceId = $instance->id ?? null;
                $canWatch = $instanceId !== null && (int)$video->instance_id === (int)$instanceId;
            } elseif ($video->is_preview) {
                // Grupo: vídeo de preview sempre libera; fora isso, precisa ter pedido aprovado.
                $canWatch = true;
            } else {
                $order = $this->fetchTable('Orders')->find()
                    ->where([
                        'student_id' => $studentId,
                        'item_type' => 'mentorship',
                        'item_id' => $mentorshipId,
                        'status' => 'approved',
                    ])
                    ->first();
                $canWatch = (bool)$order;
            }

            if (!$canWatch) {
                $this->Flash->error('Você precisa adquirir esta mentoria para assistir este vídeo.');
                return $this->redirect(['action' => 'viewStudents', $mentorshipId]);
            }

            $allVideosCondition = $mentorship->type === 'particular'
                ? ['instance_id' => $instanceId, 'is_active' => 1]
                : ['mentorship_id' => $mentorshipId, 'is_active' => 1];

            $allVideos = $this->fetchTable('MentorshipVideos')->find()
                ->select(['id', 'title', 'order_position', 'is_preview', 'duration_seconds'])
                ->where($allVideosCondition)
                ->orderBy(['order_position' => 'ASC', 'created' => 'ASC'])
                ->toArray();

            $currentProgress = $this->fetchTable('MentorshipProgress')->find()
                ->where([
                    'student_id' => $studentId,
                    'mentorship_id' => $mentorshipId,
                    'video_id' => $videoId,
                ])
                ->first();

            $allProgress = $this->fetchTable('MentorshipProgress')->find()
                ->where(['student_id' => $studentId, 'mentorship_id' => $mentorshipId])
                ->toArray();

            $progress = [];
            foreach ($allProgress as $prog) {
                $progress[$prog->video_id] = $prog;
            }

            if (!$currentProgress) {
                $mentorshipProgressTable = $this->fetchTable('MentorshipProgress');
                $newProgress = $mentorshipProgressTable->newEmptyEntity();
                $newProgress = $mentorshipProgressTable->patchEntity($newProgress, [
                    'student_id' => $studentId,
                    'mentorship_id' => $mentorshipId,
                    'video_id' => $videoId,
                    'watched_at' => date('Y-m-d H:i:s'),
                ]);
                $mentorshipProgressTable->save($newProgress);
                $currentProgress = $newProgress;
            } else {
                $mentorshipProgressTable = $this->fetchTable('MentorshipProgress');
                $currentProgress->watched_at = date('Y-m-d H:i:s');
                $mentorshipProgressTable->save($currentProgress);
            }

            $this->set('mentorship', $mentorship);
            $this->set('video', $video);
            $this->set('allVideos', $allVideos);
            $this->set('progress', $progress);
            $this->set('currentProgress', $currentProgress);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao carregar vídeo: ' . $e->getMessage());
            return $this->redirect(['action' => 'viewStudents', $mentorshipId]);
        }
    }

    /**
     * Atualizar progresso do vídeo de mentoria
     */
    public function updateProgress()
    {
        if (!$this->request->is('post')) {
            return $this->response->withStatus(405);
        }

        try {
            if (!$this->isStudent()) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Acesso negado']));
            }

            $studentId = $this->getCurrentStudentId();
            $data = $this->request->getData();

            $videoId = $data['video_id'] ?? null;
            $mentorshipId = $data['mentorship_id'] ?? null;
            $watchTime = $data['watch_time'] ?? 0;
            $completed = $data['completed'] ?? false;

            if (!$videoId || !$mentorshipId) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Dados inválidos']));
            }

            $video = $this->fetchTable('MentorshipVideos')->find()
                ->select(['id', 'duration_seconds'])
                ->where(['id' => $videoId])
                ->first();

            if (!$video) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Vídeo não encontrado']));
            }

            $watchTime = max(0, min((int)$watchTime, (int)$video->duration_seconds));

            $mentorshipProgressTable = $this->fetchTable('MentorshipProgress');
            $progress = $mentorshipProgressTable->find()
                ->where(['student_id' => $studentId, 'mentorship_id' => $mentorshipId, 'video_id' => $videoId])
                ->first();

            if ($progress) {
                $progressData = [
                    'watched_seconds' => $watchTime,
                    'watched_at' => date('Y-m-d H:i:s'),
                    'last_watched' => date('Y-m-d H:i:s'),
                ];

                if ($completed) {
                    $progressData['completed'] = true;
                    if (!$progress->completed_at) {
                        $progressData['completed_at'] = date('Y-m-d H:i:s');
                    }
                }

                $progress = $mentorshipProgressTable->patchEntity($progress, $progressData);
            } else {
                $progress = $mentorshipProgressTable->newEmptyEntity();
                $progressData = [
                    'student_id' => $studentId,
                    'mentorship_id' => $mentorshipId,
                    'video_id' => $videoId,
                    'watched_seconds' => $watchTime,
                    'watched_at' => date('Y-m-d H:i:s'),
                    'last_watched' => date('Y-m-d H:i:s'),
                ];

                if ($completed) {
                    $progressData['completed'] = true;
                    $progressData['completed_at'] = date('Y-m-d H:i:s');
                }

                $progress = $mentorshipProgressTable->patchEntity($progress, $progressData);
            }

            if ($mentorshipProgressTable->save($progress)) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => true]));
            } else {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Erro ao salvar progresso']));
            }
        } catch (\Exception $e) {
            return $this->response->withType('application/json')
                ->withStringBody(json_encode(['success' => false, 'message' => $e->getMessage()]));
        }
    }

    private function flattenEntityErrors(\Cake\Datasource\EntityInterface $entity): string
    {
        $messages = [];
        foreach ($entity->getErrors() as $fieldErrors) {
            foreach ($fieldErrors as $message) {
                $messages[] = $message;
            }
        }

        return implode(' ', $messages);
    }

    /**
     * Processa o upload do campo de arquivo "thumbnail_upload" (se enviado) e devolve
     * o caminho público da imagem salva em webroot/adm/images/mentorships/, ou null se
     * nenhum arquivo foi enviado (mantendo o valor de thumbnail já informado).
     */
    private function handleThumbnailUpload(): ?string
    {
        $file = $this->request->getUploadedFiles()['thumbnail_upload'] ?? null;

        if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file->getError() !== UPLOAD_ERR_OK) {
            throw new \Exception('Erro ao enviar a imagem. Tente novamente.');
        }

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $extension = strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));

        if (!in_array($extension, $allowedExtensions, true)) {
            throw new \Exception('Formato de imagem inválido. Use JPG, PNG, WEBP ou GIF.');
        }

        $maxSize = 5 * 1024 * 1024;
        if ($file->getSize() > $maxSize) {
            throw new \Exception('A imagem deve ter no máximo 5MB.');
        }

        $uploadDir = WWW_ROOT . 'adm' . DS . 'images' . DS . 'mentorships' . DS;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $file->moveTo($uploadDir . $filename);

        return '/adm/images/mentorships/' . $filename;
    }
}
