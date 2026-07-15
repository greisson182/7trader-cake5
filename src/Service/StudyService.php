<?php

declare(strict_types=1);

namespace App\Service;

use Cake\ORM\TableRegistry;
use Cake\ORM\Table;

class StudyService
{
    private Table $_studiesTable;

    public function __construct()
    {
        $this->_studiesTable = TableRegistry::getTableLocator()->get('Studies');
    }

    public function getStudentStudies(int $studentId): array
    {
        return $this->_studiesTable->find()
            ->contain(['Accounts'])
            ->select([
                'Studies.id', 'Studies.student_id', 'Studies.market_id',
                'Studies.account_id', 'Studies.study_date', 'Studies.wins',
                'Studies.losses', 'Studies.profit_loss', 'Studies.notes',
                'Studies.created', 'Studies.modified',
                'student_name' => 'Students.name',
                'market_name' => 'Markets.name',
                'account_name' => 'Accounts.name',
                'currency' => 'Markets.currency'
            ])
            ->leftJoinWith('Students')
            ->leftJoinWith('Markets')
            ->where(['Studies.student_id' => $studentId])
            ->orderByDesc('Studies.study_date')
            ->toArray();
    }

    public function getAllStudies(): array
    {
        return $this->_studiesTable->find()
            ->contain(['Students', 'Markets', 'Accounts'])
            ->select([
                'Studies.id', 'Studies.student_id', 'Studies.market_id',
                'Studies.account_id', 'Studies.study_date', 'Studies.wins',
                'Studies.losses', 'Studies.profit_loss', 'Studies.notes',
                'Studies.created', 'Studies.modified',
                'student_name' => 'Students.name',
                'market_name' => 'Markets.name',
                'account_name' => 'Accounts.name',
                'currency' => 'Markets.currency'
            ])
            ->leftJoinWith('Students')
            ->leftJoinWith('Markets')
            ->orderByDesc('Studies.study_date')
            ->toArray();
    }

    public function findById(int $id): ?\App\Model\Entity\Study
    {
        return $this->_studiesTable->find()
            ->contain(['Students', 'Markets', 'Accounts'])
            ->where(['Studies.id' => $id])
            ->first();
    }

    public function save(array $data): ?\App\Model\Entity\Study
    {
        $study = $this->_studiesTable->newEmptyEntity();
        $study = $this->_studiesTable->patchEntity($study, $data);

        if ($study->hasErrors()) {
            return null;
        }

        return $this->_studiesTable->save($study) ? $study : null;
    }
}
