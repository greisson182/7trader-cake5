<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * MentorshipInstance Entity — uma mentoria particular é única por aluno: cada
 * pedido aprovado do tipo 'particular' gera uma instância própria, com seus
 * próprios módulos/vídeos (gravações das sessões daquele aluno específico).
 *
 * @property int $id
 * @property int $mentorship_id
 * @property int $student_id
 * @property int $order_id
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 *
 * @property \App\Model\Entity\Mentorship $mentorship
 * @property \App\Model\Entity\Student $student
 * @property \App\Model\Entity\Order $order
 * @property \App\Model\Entity\MentorshipModule[] $mentorship_modules
 * @property \App\Model\Entity\MentorshipVideo[] $mentorship_videos
 */
class MentorshipInstance extends Entity
{
    protected array $_accessible = [
        'mentorship_id' => true,
        'student_id' => true,
        'order_id' => true,
        'created' => true,
        'modified' => true,
        'mentorship' => true,
        'student' => true,
        'order' => true,
        'mentorship_modules' => true,
        'mentorship_videos' => true,
    ];
}
