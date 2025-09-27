<?php

declare(strict_types=1);

namespace App\Middleware;

use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Cake\Cache\Cache;

class RateLimitMiddleware implements MiddlewareInterface
{
    private int $maxAttempts;
    private int $timeWindow;
    private string $cacheConfig;

    public function __construct(int $maxAttempts = 5, int $timeWindow = 300, string $cacheConfig = 'default')
    {
        $this->maxAttempts = $maxAttempts;
        $this->timeWindow = $timeWindow; // 5 minutes by default
        $this->cacheConfig = $cacheConfig;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $clientIp = $this->getClientIp($request);
        $key = 'rate_limit_' . md5($clientIp);
        
        // Get current attempts from cache
        $attempts = Cache::read($key, $this->cacheConfig) ?: [];
        $now = time();
        
        // Clean old attempts (outside time window)
        $attempts = array_filter($attempts, function($timestamp) use ($now) {
            return ($now - $timestamp) < $this->timeWindow;
        });
        
        // Check if rate limit exceeded
        if (count($attempts) >= $this->maxAttempts) {
            return $this->createRateLimitResponse();
        }
        
        // Add current attempt
        $attempts[] = $now;
        
        // Store updated attempts
        Cache::write($key, $attempts, $this->cacheConfig);
        
        return $handler->handle($request);
    }

    private function getClientIp(ServerRequestInterface $request): string
    {
        $serverParams = $request->getServerParams();
        
        // Check for IP from various headers (for proxy/load balancer scenarios)
        $ipHeaders = [
            'HTTP_CF_CONNECTING_IP',     // Cloudflare
            'HTTP_CLIENT_IP',            // Proxy
            'HTTP_X_FORWARDED_FOR',      // Load balancer/proxy
            'HTTP_X_FORWARDED',          // Proxy
            'HTTP_X_CLUSTER_CLIENT_IP',  // Cluster
            'HTTP_FORWARDED_FOR',        // Proxy
            'HTTP_FORWARDED',            // Proxy
            'REMOTE_ADDR'                // Standard
        ];
        
        foreach ($ipHeaders as $header) {
            if (!empty($serverParams[$header])) {
                $ip = $serverParams[$header];
                // Handle comma-separated IPs (X-Forwarded-For can contain multiple IPs)
                if (strpos($ip, ',') !== false) {
                    $ip = trim(explode(',', $ip)[0]);
                }
                // Validate IP
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        // Fallback to REMOTE_ADDR
        return $serverParams['REMOTE_ADDR'] ?? '127.0.0.1';
    }

    private function createRateLimitResponse(): ResponseInterface
    {
        $response = new Response();
        $response = $response->withStatus(429, 'Too Many Requests');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response = $response->withHeader('Retry-After', (string)$this->timeWindow);
        
        $body = json_encode([
            'error' => 'Rate limit exceeded',
            'message' => 'Muitas tentativas. Tente novamente em ' . ($this->timeWindow / 60) . ' minutos.',
            'retry_after' => $this->timeWindow
        ]);
        
        $response->getBody()->write($body);
        
        return $response;
    }

    public function getRemainingAttempts(string $clientIp): int
    {
        $key = 'rate_limit_' . md5($clientIp);
        $attempts = Cache::read($key, $this->cacheConfig) ?: [];
        $now = time();
        
        // Clean old attempts
        $attempts = array_filter($attempts, function($timestamp) use ($now) {
            return ($now - $timestamp) < $this->timeWindow;
        });
        
        return max(0, $this->maxAttempts - count($attempts));
    }

    public function getTimeUntilReset(string $clientIp): int
    {
        $key = 'rate_limit_' . md5($clientIp);
        $attempts = Cache::read($key, $this->cacheConfig) ?: [];
        
        if (empty($attempts)) {
            return 0;
        }
        
        $oldestAttempt = min($attempts);
        $resetTime = $oldestAttempt + $this->timeWindow;
        
        return max(0, $resetTime - time());
    }
}