<?php

namespace App\Components;

use Phalcon\Di\Injectable;
use Phalcon\Translate\Adapter\NativeArray;
use Phalcon\Translate\InterpolatorFactory;
use Phalcon\Translate\TranslateFactory;

class Locale extends Injectable
{
    /**
     * Translate the website according to language
     * @return NativeArray
     */
    public function getTranslator(): NativeArray
    {
        // Ask browser what is the best language
        $language = $this->request->get('locale') ?? $this->request->getBestLanguage();
        $messages = [];

        $translationFile = APP_PATH . '/admin/messages/' . $language . '.php';

        if (true !== file_exists($translationFile)) {
            $translationFile = APP_PATH . '/admin/messages/en.php';
        }

        require $translationFile;

        $interpolator = new InterpolatorFactory();
        $factory      = new TranslateFactory($interpolator);

        return $factory->newInstance(
            'array',
            [
                'content' => $messages,
            ]
        );
    }
}
