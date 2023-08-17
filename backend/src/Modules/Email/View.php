<?php

namespace Modules\Email;

class View
{
    protected string|null $template = null;

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function escape($data): string
    {
        return htmlspecialchars((string)$data, ENT_QUOTES, 'UTF-8');
    }

    public function render(array $data): string
    {
        extract($data);
        $base = dirname(__FILE__);
        ob_start();
        include($base . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . $this->template . ".php");
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}