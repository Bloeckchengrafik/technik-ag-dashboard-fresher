<?php

namespace Modules\Email;

class View
{
    protected string|null $template = null;

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function escape($data): mixed
    {
        // if not string: return as is
        if (!is_string($data)) {
            return $data;
        }

        $str = htmlspecialchars((string)$data, HTML_ENTITIES | HTML_SPECIALCHARS | ENT_QUOTES, 'UTF-8');

        return str_replace(
            ['ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß'],
            ['&auml;', '&ouml;', '&uuml;', '&Auml;', '&Ouml;', '&Uuml;', '&szlig;'],
            $str
        );
    }

    public function render(array $data): string
    {
        $clean = [];
        foreach ($data as $key => $value) {
            $clean[$key] = $this->escape($value);
        }

        extract($clean);
        $base = dirname(__FILE__);
        ob_start();
        include($base . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . $this->template . ".php");
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }
}