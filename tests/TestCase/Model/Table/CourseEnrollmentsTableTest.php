<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CourseEnrollmentsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CourseEnrollmentsTable Test Case
 */
class CourseEnrollmentsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CourseEnrollmentsTable
     */
    protected $CourseEnrollments;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.CourseEnrollments',
        'app.Students',
        'app.Courses',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('CourseEnrollments') ? [] : ['className' => CourseEnrollmentsTable::class];
        $this->CourseEnrollments = $this->getTableLocator()->get('CourseEnrollments', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->CourseEnrollments);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\CourseEnrollmentsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\CourseEnrollmentsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
