<?php

namespace sadovojav\cutter;

/**
 * Class CutterAsset
 * @package sadovojav\cutter
 */
class CutterAsset extends \yii\web\AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset'
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/assets';

        $this->js[] = (YII_DEBUG ? 'js/cropper.js' : 'js/cropper.min.js');
        $this->js[] = (YII_DEBUG ? 'js/cutter.js' : 'js/cutter.min.js');

        $this->css[] = (YII_DEBUG ? 'css/cropper.css' : 'css/cropper.min.css');
        $this->css[] = (YII_DEBUG ? 'css/cutter.css' : 'css/cutter.min.css');

        parent::init();
    }
}