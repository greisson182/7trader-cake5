<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\CourseVideosTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\CourseVideosTable Test Case
 */
class CourseVideosTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\CourseVideosTable
     */
    protected $CourseVideos;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.CourseVideos',
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
        $config = $this->getTableLocator()->exists('CourseVideos') ? [] : ['className' => CourseVideosTable::class];
        $this->CourseVideos = $this->getTableLocator()->get('CourseVideos', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->CourseVideos);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\CourseVideosTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\CourseVideosTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
