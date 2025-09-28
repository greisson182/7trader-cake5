<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

class ErrorController extends AppController
{
    public function initialize(): void
    {
        // Only add parent::initialize() if you are confident your `AppController` is safe.
    }

    public function beforeFilter(EventInterface $event): void
    {
    }

    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);

        $this->viewBuilder()->setTemplatePath('Error');
    }

    public function afterFilter(EventInterface $event): void
    {
    }
}
