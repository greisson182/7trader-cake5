<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\PermissionsProfilesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\PermissionsProfilesTable Test Case
 */
class PermissionsProfilesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\PermissionsProfilesTable
     */
    protected $PermissionsProfiles;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.PermissionsProfiles',
        'app.Permissions',
        'app.Profiles',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('PermissionsProfiles') ? [] : ['className' => PermissionsProfilesTable::class];
        $this->PermissionsProfiles = $this->getTableLocator()->get('PermissionsProfiles', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->PermissionsProfiles);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @link \App\Model\Table\PermissionsProfilesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @link \App\Model\Table\PermissionsProfilesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
