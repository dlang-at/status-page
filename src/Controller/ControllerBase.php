<?php

declare(strict_types=1);

namespace DlangAT\StatusPage\Controller;

use DlangAT\StatusPage\Util\TemplateEngine;

class ControllerBase
{
    public function __construct(
        protected TemplateEngine $templateEngine,
    ) {
    }
}
