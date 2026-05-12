<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Middleware;

use DlangAT\StatusPage\Model\UserSettings;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final class UserSettingsMiddleware implements MiddlewareInterface
{
    public function __construct(
        private UserSettings $userSettings,
    ) {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $cookies = $request->getCookieParams();
        $hasCookies = count($cookies) > 0;

        if ($hasCookies) {
            $this->userSettings->applyFromCookies($cookies);
        }

        $response = $handler->handle($request);

        if ($hasCookies || $this->userSettings->isDirty()) {
            foreach ($this->userSettings->toCookies() as $cookie) {
                $response = $cookie->appendToResponse($response);
            }
        }

        return $response;
    }
}
