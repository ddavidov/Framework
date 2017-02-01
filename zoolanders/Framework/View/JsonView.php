<?php

namespace Zoolanders\Framework\View;

/**
 * Class HtmlView
 * @package Zoolanders\Framework\View
 */
class JsonView extends View
{
    protected $type = 'json';

    /**
     * @inheritdoc
     */
    public function render($tpl = null, $data = [])
    {
        if (!empty($data)) {
            $this->data = $data;
        }

        return $this->data;
    }
}
