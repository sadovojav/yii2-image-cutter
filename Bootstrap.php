<?php

namespace sadovojav\cutter;

use yii\i18n\PhpMessageSource;

/**
 * Class Bootstrap
 * @package sadovojav\cutter
 */
class Bootstrap implements \yii\base\BootstrapInterface
{
    public function bootstrap($app)
    {
        $app->i18n->translations['sadovojav/cutter/*'] = [
            'class' => PhpMessageSource::className(),
            'sourceLanguage' => 'en-US',
            'forceTranslation' => true,
            'basePath' => '@vendor/sadovojav/yii2-image-cutter/messages',
            'fileMap' => [
                'sadovojav/cutter/cutter' => 'cutter.php',
            ],
        ];
    }
}
