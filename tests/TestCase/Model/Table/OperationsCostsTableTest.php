<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\OperationsCostsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\OperationsCostsTable Test Case
 */
class OperationsCostsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\OperationsCostsTable
     */
    protected $OperationsCosts;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.OperationsCosts',
        'app.Markets',
        'app.Students',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('OperationsCosts') ? [] : ['className' => OperationsCostsTable::class];
        $this->OperationsCosts = $this->getTableLocator()->get('OperationsCosts', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->OperationsCosts);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\OperationsCostsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\OperationsCostsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
