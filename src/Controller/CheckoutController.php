<?php

declare(strict_types=1);

namespace App\Controller;

use App\Middleware\RateLimitMiddleware;
use App\Service\MercadoPagoService;
use App\Service\OrderService;
use App\Service\SettingsService;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Routing\Router;
use Exception;

class CheckoutController extends AppController
{
    private OrderService $_orderService;
    private MercadoPagoService $_mercadoPagoService;
    private SettingsService $_settingsService;

    public function initialize(): void
    {
        parent::initialize();
        $this->_settingsService = new SettingsService();
        $this->_orderService = new OrderService();
        $this->_mercadoPagoService = new MercadoPagoService($this->_settingsService);
    }

    public function course($courseId = null)
    {
        return $this->startCheckout('course', (int)$courseId);
    }

    public function mentorship($mentorshipId = null)
    {
        return $this->startCheckout('mentorship', (int)$mentorshipId);
    }

    private function startCheckout(string $itemType, int $itemId)
    {
        try {
            $item = $this->_orderService->getPricedItem($itemType, $itemId);
        } catch (RecordNotFoundException $e) {
            $this->Flash->error($e->getMessage());
            return $this->redirect('/');
        }

        if ($this->request->is('post')) {
            $clientIp = $this->getClientIp();
            $rateLimiter = new RateLimitMiddleware(5, 300, 'rate_limit');

            if ($rateLimiter->getRemainingAttempts($clientIp) <= 0) {
                $this->Flash->error(__('Muitas tentativas. Tente novamente em alguns minutos.'));
                $this->set(compact('item', 'itemType', 'itemId'));
                return $this->render('start');
            }

            try {
                $order = $this->_orderService->createPendingOrder(
                    $itemType,
                    $itemId,
                    $this->request->getData(),
                    $clientIp
                );

                $preference = $this->_mercadoPagoService->createPreference(
                    $order->id,
                    $item['title'],
                    (float)$order->amount,
                    $order->buyer_name,
                    $order->buyer_email,
                    Router::url(['controller' => 'Checkout', 'action' => 'returnSuccess'], true),
                    Router::url(['controller' => 'Checkout', 'action' => 'returnPending'], true),
                    Router::url(['controller' => 'Checkout', 'action' => 'returnFailure'], true),
                    Router::url(['prefix' => 'Api', 'controller' => 'MercadoPagoWebhook', 'action' => 'notify'], true)
                );

                $this->_orderService->attachPreference($order, $preference->id);

                $settings = $this->_settingsService->get();
                $redirectUrl = !empty($settings->mp_sandbox)
                    ? $preference->sandbox_init_point
                    : $preference->init_point;

                return $this->redirect($redirectUrl);
            } catch (Exception $e) {
                $this->recordFailedAttempt($clientIp);
                $this->Flash->error(__('Erro ao iniciar pagamento: ') . $e->getMessage());
            }
        }

        $this->set(compact('item', 'itemType', 'itemId'));
        return $this->render('start');
    }

    public function returnSuccess()
    {
        return $this->showReturnPage('success');
    }

    public function returnPending()
    {
        return $this->showReturnPage('pending');
    }

    public function returnFailure()
    {
        return $this->showReturnPage('failure');
    }

    private function showReturnPage(string $outcome)
    {
        $externalReference = $this->request->getQuery('external_reference');
        $order = null;

        if ($externalReference !== null && ctype_digit((string)$externalReference)) {
            try {
                $order = $this->_orderService->findOrderById((int)$externalReference);
            } catch (RecordNotFoundException $e) {
                $order = null;
            }
        }

        $this->set(compact('order', 'outcome'));

        return $this->render('return');
    }
}
