<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;
use Cake\Event\EventInterface;

class CoursesController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        $this->checkSession();
    }

    public function index()
    {
        try {
            // Buscar todos os cursos com contagem de vídeos usando ORM
            $courses = $this->fetchTable('Courses')->find()
                ->select([
                    'Courses.id',
                    'Courses.title',
                    'Courses.description',
                    'Courses.thumbnail',
                    'Courses.duration_minutes',
                    'Courses.difficulty',
                    'Courses.category',
                    'Courses.instructor',
                    'Courses.price',
                    'Courses.is_free',
                    'Courses.is_active',
                    'Courses.order_position',
                    'Courses.created',
                    'Courses.modified',
                    'video_count' => $this->fetchTable('Courses')->find()->func()->count('CourseVideos.id'),
                    'total_duration' => $this->fetchTable('Courses')->find()->func()->sum('CourseVideos.duration_seconds')
                ])
                ->leftJoinWith('CourseVideos', function ($q) {
                    return $q->where(['CourseVideos.is_active' => 1]);
                })
                ->groupBy(['Courses.id'])
                ->orderBy(['Courses.order_position' => 'ASC', 'Courses.created' => 'DESC'])
                ->toArray();

            $this->set('courses', $courses);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao carregar cursos: ' . $e->getMessage());
            return $this->redirect(['controller' => 'Welcome', 'action' => 'index']);
        }
    }

    public function view($id = null)
    {
        try {
            // Buscar curso usando ORM
            $course = $this->fetchTable('Courses')->get($id);

            // Buscar vídeos do curso usando ORM
            $videos = $this->fetchTable('CourseVideos')->find()
                ->where(['course_id' => $id, 'is_active' => 1])
                ->orderBy(['order_position' => 'ASC'])
                ->toArray();

            // Buscar estatísticas de inscrições usando ORM
            $enrollmentsTable = $this->fetchTable('CourseEnrollments');
            $totalEnrollments = $enrollmentsTable->find()
                ->where(['course_id' => $id, 'is_active' => 1])
                ->count();

            $completedEnrollments = $enrollmentsTable->find()
                ->where([
                    'course_id' => $id,
                    'is_active' => 1,
                    'completed_at IS NOT' => null
                ])
                ->count();

            $stats = [
                'total_enrollments' => $totalEnrollments,
                'completed_enrollments' => $completedEnrollments
            ];

            $this->set('course', $course);
            $this->set('videos', $videos);
            $this->set('stats', $stats);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Curso não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao carregar curso: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    public function add()
    {
        if ($this->request->is('post')) {
            try {
                $coursesTable = $this->fetchTable('Courses');
                $course = $coursesTable->newEmptyEntity();

                $data = $this->request->getData();

                // Validações básicas
                if (empty($data['title'])) {
                    $this->Flash->error('Título é obrigatório.');
                    $this->set('course', $course);
                    return;
                }

                // Buscar próxima posição usando ORM
                $maxPosition = $coursesTable->find()
                    ->select(['max_pos' => $coursesTable->find()->func()->max('order_position')])
                    ->first();
                $nextPosition = ($maxPosition->max_pos ?? 0) + 1;

                // Preparar dados para salvar
                $courseData = [
                    'title' => $data['title'],
                    'description' => $data['description'] ?? '',
                    'thumbnail' => $data['thumbnail'] ?? '',
                    'duration_minutes' => (int)($data['duration_minutes'] ?? 0),
                    'difficulty' => $data['difficulty'] ?? 'Iniciante',
                    'category' => $data['category'] ?? '',
                    'instructor' => $data['instructor'] ?? '',
                    'price' => (float)($data['price'] ?? 0.00),
                    'is_free' => isset($data['is_free']) ? 1 : 0,
                    'is_active' => isset($data['is_active']) ? 1 : 0,
                    'order_position' => $nextPosition
                ];

                $course = $coursesTable->patchEntity($course, $courseData);

                if ($coursesTable->save($course)) {
                    $this->Flash->success('Curso criado com sucesso!');
                    return $this->redirect(['action' => 'index']);
                } else {
                    $this->Flash->error('Erro ao salvar curso. Verifique os dados.');
                }
            } catch (\Exception $e) {
                $this->Flash->error('Erro ao criar curso: ' . $e->getMessage());
            }
        }

        $this->set('course', $this->fetchTable('Courses')->newEmptyEntity());
    }

    public function edit($id = null)
    {
        try {
            $coursesTable = $this->fetchTable('Courses');

            // Buscar curso usando ORM
            $course = $coursesTable->get($id);

            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();

                // Validações básicas
                if (empty($data['title'])) {
                    $this->Flash->error('Título é obrigatório.');
                    $this->set('course', $course);
                    return;
                }

                // Preparar dados para atualização
                $courseData = [
                    'title' => $data['title'],
                    'description' => $data['description'] ?? '',
                    'thumbnail' => $data['thumbnail'] ?? '',
                    'duration_minutes' => (int)($data['duration_minutes'] ?? 0),
                    'difficulty' => $data['difficulty'] ?? 'Iniciante',
                    'category' => $data['category'] ?? '',
                    'instructor' => $data['instructor'] ?? '',
                    'price' => (float)($data['price'] ?? 0.00),
                    'is_free' => isset($data['is_free']) ? 1 : 0,
                    'is_active' => isset($data['is_active']) ? 1 : 0
                ];

                $course = $coursesTable->patchEntity($course, $courseData);

                if ($coursesTable->save($course)) {
                    $this->Flash->success('Curso atualizado com sucesso!');
                    return $this->redirect(['action' => 'view', $id]);
                } else {
                    $this->Flash->error('Erro ao salvar curso. Verifique os dados.');
                }
            }

            $this->set('course', $course);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Curso não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao editar curso: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    public function delete($id = null)
    {
        try {
            $coursesTable = $this->fetchTable('Courses');

            // Buscar curso usando ORM
            $course = $coursesTable->get($id);

            // Verificar se há inscrições ativas usando ORM
            $enrollmentsTable = $this->fetchTable('CourseEnrollments');
            $activeEnrollments = $enrollmentsTable->find()
                ->where(['course_id' => $id, 'is_active' => 1])
                ->count();

            if ($activeEnrollments > 0) {
                $this->Flash->error('Não é possível excluir curso com inscrições ativas. Desative o curso ao invés de excluí-lo.');
                return $this->redirect(['action' => 'view', $id]);
            }

            // Excluir curso usando ORM
            if ($coursesTable->delete($course)) {
                $this->Flash->success("Curso '{$course->title}' excluído com sucesso!");
            } else {
                $this->Flash->error('Erro ao excluir curso.');
            }

            return $this->redirect(['action' => 'index']);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Curso não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao excluir curso: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    // Gerenciar vídeos do curso
    public function videos($courseId = null)
    {
        try {
            // Verificar se curso existe usando ORM
            $course = $this->fetchTable('Courses')->get($courseId);

            // Buscar vídeos do curso usando ORM
            $videos = $this->fetchTable('CourseVideos')->find()
                ->where(['course_id' => $courseId])
                ->orderBy(['order_position' => 'ASC', 'created' => 'ASC'])
                ->toArray();

            $this->set('course', $course);
            $this->set('videos', $videos);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Curso não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao carregar vídeos: ' . $e->getMessage());
            return $this->redirect(['action' => 'index']);
        }
    }

    public function addVideo($courseId = null)
    {
        try {
            // Verificar se curso existe usando ORM
            $course = $this->fetchTable('Courses')->get($courseId);

            if ($this->request->is('post')) {
                $data = $this->request->getData();

                // Validações básicas
                if (empty($data['title']) || empty($data['video_url'])) {
                    $this->Flash->error('Título e URL do vídeo são obrigatórios.');
                    $this->set('course', $course);
                }

                $courseVideosTable = $this->fetchTable('CourseVideos');

                // Buscar próxima posição usando ORM
                $maxPosition = $courseVideosTable->find()
                    ->select(['max_pos' => $courseVideosTable->find()->func()->max('order_position')])
                    ->where(['course_id' => $courseId])
                    ->first();
                $nextPosition = ($maxPosition->max_pos ?? 0) + 1;

                // Preparar dados para salvar
                $videoData = [
                    'course_id' => $courseId,
                    'title' => $data['title'],
                    'description' => $data['description'] ?? '',
                    'video_url' => $data['video_url'],
                    'video_type' => $data['video_type'] ?? 'youtube',
                    'duration_seconds' => (int)($data['duration_seconds'] ?? 0),
                    'order_position' => $nextPosition,
                    'is_preview' => isset($data['is_preview']) ? 1 : 0,
                    'is_active' => isset($data['is_active']) ? 1 : 0
                ];

                $video = $courseVideosTable->newEmptyEntity();
                $video = $courseVideosTable->patchEntity($video, $videoData);

                if ($courseVideosTable->save($video)) {
                    $this->Flash->success('Vídeo adicionado com sucesso!');
                    return $this->redirect(['action' => 'videos', $courseId]);
                } else {
                    $this->Flash->error('Erro ao salvar vídeo. Verifique os dados.');
                }
            }

            $this->set('course', $course);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Curso não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao adicionar vídeo: ' . $e->getMessage());
            return $this->redirect(['action' => 'videos', $courseId]);
        }
    }

    public function editVideo($videoId = null)
    {
        if (!$videoId) {
            $this->Flash->error('ID do vídeo é obrigatório.');
            return $this->redirect(['action' => 'index']);
        }

        try {
            $courseVideosTable = $this->fetchTable('CourseVideos');

            // Buscar o vídeo usando ORM
            $video = $courseVideosTable->get($videoId);

            if ($this->request->is(['patch', 'post', 'put'])) {
                $data = $this->request->getData();

                if (empty($data['title']) || empty($data['video_url'])) {
                    $this->Flash->error('Título e URL do vídeo são obrigatórios.');
                    $this->set('video', $video);
                }

                // Converter minutos para segundos se fornecido
                $durationSeconds = null;
                if (!empty($data['duration_minutes']) && is_numeric($data['duration_minutes'])) {
                    $durationSeconds = (int)$data['duration_minutes'] * 60;
                }

                // Preparar dados para atualização
                $videoData = [
                    'title' => $data['title'],
                    'description' => $data['description'] ?? '',
                    'video_url' => $data['video_url'],
                    'video_type' => $data['video_type'] ?? 'youtube',
                    'duration_seconds' => $durationSeconds,
                    'is_active' => isset($data['is_active']) ? 1 : 0
                ];

                $video = $courseVideosTable->patchEntity($video, $videoData);

                if ($courseVideosTable->save($video)) {
                    $this->Flash->success('Vídeo atualizado com sucesso!');
                    return $this->redirect(['action' => 'videos', $video->course_id]);
                } else {
                    $this->Flash->error('Erro ao atualizar vídeo. Verifique os dados.');
                }
            }

            $this->set('video', $video);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->Flash->error('Vídeo não encontrado.');
            return $this->redirect(['action' => 'index']);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao editar vídeo: ' . $e->getMessage());
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
            // Buscar dados do vídeo usando ORM
            $video = $this->fetchTable('CourseVideos')->find()
                ->select([
                    'CourseVideos.id',
                    'CourseVideos.title',
                    'CourseVideos.description',
                    'CourseVideos.video_url',
                    'CourseVideos.video_type',
                    'CourseVideos.duration_seconds',
                    'CourseVideos.course_id',
                    'Courses.title'
                ])
                ->leftJoinWith('Courses')
                ->where([
                    'CourseVideos.id' => $videoId,
                    'CourseVideos.is_active' => 1
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

    /**
     * Lista todos os cursos disponíveis para estudantes
     */
    public function indexStudents()
    {
        try {
            // Buscar cursos ativos usando ORM
            $courses = $this->fetchTable('Courses')->find()
                ->select([
                    'Courses.id',
                    'Courses.title',
                    'Courses.description',
                    'Courses.thumbnail',
                    'Courses.duration_minutes',
                    'Courses.difficulty',
                    'Courses.category',
                    'Courses.instructor',
                    'Courses.price',
                    'Courses.is_free',
                    'Courses.is_active',
                    'Courses.order_position',
                    'Courses.created',
                    'video_count' => $this->fetchTable('Courses')->find()->func()->count('CourseVideos.id'),
                    'total_duration' => $this->fetchTable('Courses')->find()->func()->sum('CourseVideos.duration_seconds')
                ])
                ->leftJoinWith('CourseVideos', function ($q) {
                    return $q->where(['CourseVideos.is_active' => 1]);
                })
                ->where(['Courses.is_active' => 1])
                ->groupBy(['Courses.id'])
                ->orderBy(['Courses.order_position' => 'ASC', 'Courses.created' => 'DESC'])
                ->toArray();

            // Se for estudante, verificar inscrições usando ORM
            $enrollments = [];
            if ($this->isStudent()) {
                $studentId = $this->getCurrentStudentId();
                $enrollmentData = $this->fetchTable('CourseEnrollments')->find()
                    ->select(['course_id', 'enrolled_at', 'completed_at'])
                    ->where(['student_id' => $studentId])
                    ->toArray();

                foreach ($enrollmentData as $enrollment) {
                    $enrollments[$enrollment->course_id] = $enrollment;
                }
            }

            $this->set('courses', $courses);
            $this->set('enrollments', $enrollments);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao carregar cursos: ' . $e->getMessage());
            return $this->redirect(['controller' => 'Welcome', 'action' => 'index']);
        }
    }

    /**
     * Visualizar detalhes de um curso
     */
    public function viewStudents($courseId = null)
    {
        try {

            // Buscar curso usando ORM
            $course = $this->fetchTable('Courses')->find()
                ->select([
                    'Courses.id',
                    'Courses.title',
                    'Courses.description',
                    'Courses.thumbnail',
                    'Courses.duration_minutes',
                    'Courses.difficulty',
                    'Courses.category',
                    'Courses.instructor',
                    'Courses.price',
                    'Courses.is_free',
                    'Courses.is_active',
                    'Courses.order_position',
                    'Courses.created',
                    'video_count' => $this->fetchTable('Courses')->find()->func()->count('CourseVideos.id'),
                    'total_duration' => $this->fetchTable('Courses')->find()->func()->sum('CourseVideos.duration_seconds')
                ])
                ->leftJoinWith('CourseVideos', function ($q) {
                    return $q->where(['CourseVideos.is_active' => 1]);
                })
                ->where(['Courses.id' => $courseId, 'Courses.is_active' => 1])
                ->groupBy(['Courses.id'])
                ->first();




            if (!$course) {
                $this->Flash->error('Curso não encontrado.');
                return $this->redirect(['action' => 'courses-students']);
            }

            // Verificar se estudante está inscrito
            $isEnrolled = false;
            $enrollment = null;
            $progress = [];

            if ($this->isStudent()) {

                $studentId = $this->getCurrentStudentId();

                // Verificar inscrição usando ORM
                $enrollment = $this->fetchTable('CourseEnrollments')->find()
                    ->where(['student_id' => $studentId, 'course_id' => $courseId])
                    ->first();
                $isEnrolled = (bool)$enrollment;

                // Se inscrito, buscar progresso usando ORM
                if ($isEnrolled) {

                    $progressData = $this->fetchTable('StudentProgress')->find()
                        ->where(['student_id' => $studentId, 'course_id' => $courseId])
                        ->toArray();


                    foreach ($progressData as $p) {
                        $progress[$p->video_id] = $p;
                    }
                }
            }

            // Buscar vídeos do curso usando ORM
            $videosQuery = $this->fetchTable('CourseVideos')->find()
                ->where(['course_id' => $courseId, 'is_active' => 1]);

            // Se não inscrito e curso pago, mostrar apenas previews
            if (!$isEnrolled && !$course->is_free) {
                $videosQuery->where(['is_preview' => 1]);
            }

            $videos = $videosQuery->order(['order_position' => 'ASC', 'created' => 'ASC'])
                ->toArray();

            $this->set('course', $course);
            $this->set('videos', $videos);
            $this->set('isEnrolled', $isEnrolled);
            $this->set('enrollment', $enrollment);
            $this->set('progress', $progress);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao carregar curso: ' . $e->getMessage());
            return $this->redirect(['action' => 'courses-students']);
        }
    }

    /**
     * Assistir um vídeo específico do curso
     */
    public function watchStudents($courseId = null, $videoId = null)
    {
        try {
            // Verificar se é estudante
            if (!$this->isStudent()) {
                $this->Flash->error('Apenas estudantes podem assistir vídeos.');
                return $this->redirect(['action' => 'courses-students']);
            }

            $studentId = $this->getCurrentStudentId();

            // Buscar curso usando ORM
            $course = $this->fetchTable('Courses')->find()
                ->where(['id' => $courseId, 'is_active' => 1])
                ->first();

            if (!$course) {
                $this->Flash->error('Curso não encontrado.');
                return $this->redirect(['action' => 'courses-students']);
            }

            // Buscar vídeo usando ORM
            $video = $this->fetchTable('CourseVideos')->find()
                ->where([
                    'id' => $videoId,
                    'course_id' => $courseId,
                    'is_active' => 1
                ])
                ->first();


            if (!$video) {
                $this->Flash->error('Vídeo não encontrado.');
                return $this->redirect(['action' => 'view-students', $courseId]);
            }

            // Verificar permissão para assistir
            $canWatch = false;

            // Curso gratuito ou vídeo de preview
            if ($course->is_free || $video->is_preview) {
                $canWatch = true;
            } else {
                // Verificar se está inscrito usando ORM
                $enrollment = $this->fetchTable('CourseEnrollments')->find()
                    ->where(['student_id' => $studentId, 'course_id' => $courseId])
                    ->first();
                $canWatch = (bool)$enrollment;
            }

            if (!$canWatch) {
                $this->Flash->error('Você precisa se inscrever neste curso para assistir este vídeo.');
                return $this->redirect(['action' => 'view-students', $courseId]);
            }

            // Buscar todos os vídeos do curso para navegação usando ORM
            $allVideos = $this->fetchTable('CourseVideos')->find()
                ->select(['id', 'title', 'order_position', 'is_preview', 'duration_seconds'])
                ->where(['course_id' => $courseId, 'is_active' => 1])
                ->orderBy(['order_position' => 'ASC', 'created' => 'ASC'])
                ->toArray();

            // Buscar progresso do estudante para o vídeo atual usando ORM
            $currentProgress = $this->fetchTable('StudentProgress')->find()
                ->where([
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'video_id' => $videoId
                ])
                ->first();

            // Buscar progresso de todos os vídeos do curso para a navegação
            $allProgress = $this->fetchTable('StudentProgress')->find()
                ->where([
                    'student_id' => $studentId,
                    'course_id' => $courseId
                ])
                ->toArray();

            // Indexar progresso por video_id para facilitar acesso no template
            $progress = [];
            foreach ($allProgress as $prog) {
                $progress[$prog->video_id] = $prog;
            }

            // Registrar que começou a assistir (se ainda não registrou)
            if (!$currentProgress) {
                $studentProgressTable = $this->fetchTable('StudentProgress');
                $newProgress = $studentProgressTable->newEmptyEntity();
                $progressData = [
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'video_id' => $videoId,
                    'watched_at' => date('Y-m-d H:i:s')
                ];
                $newProgress = $studentProgressTable->patchEntity($newProgress, $progressData);
                $studentProgressTable->save($newProgress);
                $currentProgress = $newProgress;
            } else {
                // Atualizar última visualização usando ORM
                $studentProgressTable = $this->fetchTable('StudentProgress');
                $currentProgress->watched_at = date('Y-m-d H:i:s');
                $studentProgressTable->save($currentProgress);
            }

            $this->set('course', $course);
            $this->set('video', $video);
            $this->set('allVideos', $allVideos);
            $this->set('progress', $progress);
            $this->set('currentProgress', $currentProgress);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao carregar vídeo: ' . $e->getMessage());
            return $this->redirect(['action' => 'view-students', $courseId]);
        }
    }

    /**
     * Inscrever-se em um curso
     */
    public function enroll($courseId = null)
    {
        try {
            if (!$this->isStudent()) {
                $this->Flash->error('Apenas estudantes podem se inscrever em cursos.');
                return $this->redirect(['action' => 'courses-students']);
            }

            $studentId = $this->getCurrentStudentId();

            // Verificar se curso existe e está ativo usando ORM
            $course = $this->fetchTable('Courses')->find()
                ->where(['id' => $courseId, 'is_active' => 1])
                ->first();

            if (!$course) {
                $this->Flash->error('Curso não encontrado.');
                return $this->redirect(['action' => 'courses-students']);
            }

            // Verificar se já está inscrito usando ORM
            $existingEnrollment = $this->fetchTable('CourseEnrollments')->find()
                ->where(['student_id' => $studentId, 'course_id' => $courseId])
                ->first();

            if ($existingEnrollment) {
                $this->Flash->info('Você já está inscrito neste curso.');
                return $this->redirect(['action' => 'view-students', $courseId]);
            }

            // Para cursos pagos, redirecionar para página de pagamento
            if (!$course->is_free) {
                $this->Flash->info('Redirecionando para o pagamento...');
                return $this->redirect(['action' => 'purchase-students', $courseId]);
            }

            // Inscrever no curso usando ORM
            $courseEnrollmentsTable = $this->fetchTable('CourseEnrollments');
            $enrollment = $courseEnrollmentsTable->newEmptyEntity();
            $enrollmentData = [
                'student_id' => $studentId,
                'course_id' => $courseId,
                'enrolled_at' => date('Y-m-d H:i:s')
            ];
            $enrollment = $courseEnrollmentsTable->patchEntity($enrollment, $enrollmentData);

            if ($courseEnrollmentsTable->save($enrollment)) {
                $this->Flash->success('Inscrição realizada com sucesso! Agora você pode assistir aos vídeos.');
            } else {
                $this->Flash->error('Erro ao realizar inscrição. Tente novamente.');
            }

            return $this->redirect(['action' => 'view-students', $courseId]);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao realizar inscrição: ' . $e->getMessage());
            return $this->redirect(['action' => 'view-students', $courseId]);
        }
    }

    /**
     * Atualizar progresso do vídeo
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
            $courseId = $data['course_id'] ?? null;
            $watchTime = $data['watch_time'] ?? 0;
            $completed = $data['completed'] ?? false;

            if (!$videoId || !$courseId) {
                return $this->response->withType('application/json')
                    ->withStringBody(json_encode(['success' => false, 'message' => 'Dados inválidos']));
            }

            // Buscar progresso existente usando ORM
            $studentProgressTable = $this->fetchTable('StudentProgress');
            $progress = $studentProgressTable->find()
                ->where([
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'video_id' => $videoId
                ])
                ->first();

            if ($progress) {
                // Atualizar progresso existente usando ORM
                $progressData = [
                    'watched_seconds' => $watchTime,
                    'watched_at' => date('Y-m-d H:i:s')
                ];

                if ($completed) {
                    $progressData['completed_at'] = date('Y-m-d H:i:s');
                }

                $progress = $studentProgressTable->patchEntity($progress, $progressData);
                

            } else {
                // Criar novo progresso usando ORM
                $progress = $studentProgressTable->newEmptyEntity();
                $progressData = [
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'video_id' => $videoId,
                    'watched_seconds' => $watchTime,
                    'watched_at' => date('Y-m-d H:i:s')
                ];

                if ($completed) {
                    $progressData['completed_at'] = date('Y-m-d H:i:s');
                }

                $progress = $studentProgressTable->patchEntity($progress, $progressData);
            }

            if ($studentProgressTable->save($progress)) {
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

    /**
     * Página de compra do curso
     */
    public function purchaseStudents($courseId = null)
    {
        try {
            if (!$this->isStudent()) {
                $this->Flash->error('Apenas estudantes podem comprar cursos.');
                return $this->redirect(['action' => 'courses-students']);
            }

            $studentId = $this->getCurrentStudentId();

            // Verificar se curso existe e está ativo usando ORM
            $course = $this->fetchTable('Courses')->find()
                ->where(['id' => $courseId, 'is_active' => 1])
                ->first();

            if (!$course) {
                $this->Flash->error('Curso não encontrado.');
                return $this->redirect(['action' => 'courses-students']);
            }

            // Verificar se já está inscrito usando ORM
            $existingEnrollment = $this->fetchTable('CourseEnrollments')->find()
                ->where(['student_id' => $studentId, 'course_id' => $courseId])
                ->first();

            if ($existingEnrollment) {
                $this->Flash->info('Você já possui acesso a este curso.');
                return $this->redirect(['action' => 'view-students', $courseId]);
            }

            // Se for curso gratuito, redirecionar para inscrição
            if ($course->is_free) {
                return $this->redirect(['action' => 'enroll', $courseId]);
            }

            // Para desenvolvimento, simular compra automática
            if ($this->request->is('post')) {
                // Simular processamento de pagamento usando ORM
                $courseEnrollmentsTable = $this->fetchTable('CourseEnrollments');
                $enrollment = $courseEnrollmentsTable->newEmptyEntity();
                $enrollmentData = [
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'enrolled_at' => new \DateTime()
                ];
                $enrollment = $courseEnrollmentsTable->patchEntity($enrollment, $enrollmentData);

                if ($courseEnrollmentsTable->save($enrollment)) {
                    $this->Flash->success('Compra realizada com sucesso! Agora você tem acesso ao curso.');
                } else {
                    $this->Flash->error('Erro ao processar compra. Tente novamente.');
                }

                return $this->redirect(['action' => 'view-students', $courseId]);
            }

            $this->set('course', $course);
        } catch (\Exception $e) {
            $this->Flash->error('Erro ao processar compra: ' . $e->getMessage());
            return $this->redirect(['action' => 'courses-students']);
        }
    }
}
