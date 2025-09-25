<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\StudentProgressTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\StudentProgressTable Test Case
 */
class StudentProgressTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\StudentProgressTable
     */
    protected $StudentProgress;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.StudentProgress',
        'app.Students',
        'app.Courses',
        'app.Videos',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('StudentProgress') ? [] : ['className' => StudentProgressTable::class];
        $this->StudentProgress = $this->getTableLocator()->get('StudentProgress', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->StudentProgress);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\StudentProgressTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\StudentProgressTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
