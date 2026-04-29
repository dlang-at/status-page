<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Middleware;

use DateTimeImmutable;
use DlangAT\StatusPage\Util\IpAddressMasker;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final class TelemetryMiddleware implements MiddlewareInterface
{
    public function __construct(
        private IpAddressMasker $ipAddressMasker,
        private string $logFilePath,
    ) {
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $timestamp = (new DateTimeImmutable())->format(DateTimeImmutable::RFC3339_EXTENDED);
        $response = $handler->handle($request);


        $remoteAddress = $request->getServerParams()['REMOTE_ADDR'];
        $remoteAddressMasked = $this->ipAddressMasker->mask($remoteAddress, true);

        $url = $request->getRequestTarget();

        $tags = [];

        if ($request->hasHeader('Sec-GPC')) {
            $globalPrivacyControl = $request->getHeaderLine('Sec-GPC');
            if ($globalPrivacyControl === '1') {
                $tags['GPC'] = 1;
            } else {
                $tags['GPC'] = '!bad!';
            }
        }

        if ($request->hasHeader('DNT')) {
            $doNotTrack = $request->getHeaderLine('DNT');
            if ($doNotTrack === '0') {
                $tags['DNT'] = 0;
            } elseif ($doNotTrack === '1') {
                $tags['DNT'] = 1;
            } elseif ($doNotTrack === 'null') {
                $tags['DNT'] = null;
            } else {
                $tags['DNT'] = '!bad!';
            }
        }

        $responseSize = $response->getBody()->getSize();
        $statusCode = $response->getStatusCode();
        $tagsString = json_encode($tags);
        $logLine = "{$timestamp}|{$remoteAddressMasked}|{$statusCode}|{$url}|{$responseSize}|$tagsString\n";

        file_put_contents($this->logFilePath, $logLine, FILE_APPEND | LOCK_EX);

        return $response;
    }
}
