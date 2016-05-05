<?php

namespace sadovojav\cutter;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\bootstrap\Modal;
use yii\bootstrap\ButtonGroup;

/**
 * Class Cutter
 * @package sadovojav\cutter\widgets
 */
class Cutter extends \yii\widgets\InputWidget
{
    /**
     * Image options
     * @var
     */
    public $imageOptions;

    /**
     * Use the height of the current window for the form image cropping
     * @var bool
     */
    public $useWindowHeight = true;

    /**
     * Cropper options
     * @var array
     */
    public $cropperOptions = [];

    /**
     * Default cropper options
     * @var array
     */
    public $defaultCropperOptions = [
        'rotatable' => true,
        'zoomable' => true,
        'movable' => true,
    ];

    private $view;

    public function init()
    {
        parent::init();

        $this->view = $this->getView();

        AssetBundle::register($this->view);

        $this->cropperOptions = array_merge($this->cropperOptions, $this->defaultCropperOptions);
    }

    public function run()
    {
        if (is_null($this->imageOptions)) {
            $this->imageOptions = [
                'class' => 'img-responsive',
            ];
        }

        $this->imageOptions['id'] = Yii::$app->getSecurity()->generateRandomString(10);

        $inputField = Html::getInputId($this->model, $this->attribute);

        echo Html::beginTag('div', ['class' => 'image-cutter', 'id' => $inputField . '-cutter']);
        echo Html::activeFileInput($this->model, $this->attribute);

        $previewImage = Html::tag('div', null, ['class' => 'dummy']);;
        $previewImage .= Html::beginTag('div', ['class' => 'img-container']);
        $previewImage .= Html::tag('span', Yii::t('sadovojav/cutter/cutter', 'Click to upload image'));
        $previewImage .= Html::img($this->model->{$this->attribute} ? $this->model->{$this->attribute} : null, [
            'class' => 'preview-image',
        ]);
        $previewImage .= Html::endTag('div');

        echo Html::label($previewImage, Html::getInputId($this->model, $this->attribute), [
            'class' => 'dropzone',
        ]);

        echo Html::checkbox($this->attribute . '-remove', false, [
            'label' => Yii::t('sadovojav/cutter/cutter', 'Remove')
        ]);

        Modal::begin([
            'header' => Html::tag('h4', Yii::t('sadovojav/cutter/cutter', 'Cutter'), ['class' => 'modal-title']),
            'closeButton' => false,
            'footer' => $this->getModalFooter($inputField),
            'size' => Modal::SIZE_LARGE,
        ]);

        echo Html::beginTag('div', ['class' => 'image-container']);
        echo Html::img(null, $this->imageOptions);
        echo Html::endTag('div');

        echo Html::tag('br');

        echo Html::beginTag('div', ['class' => 'row']);
        echo Html::beginTag('div', ['class' => 'col-md-2']);
        echo Html::label(Yii::t('sadovojav/cutter/cutter', 'Aspect ratio'), $inputField . '-aspectRatio');
        echo Html::textInput($this->attribute . '-aspectRatio', isset($this->cropperOptions['aspectRatio']) ? $this->cropperOptions['aspectRatio'] : 0, ['id' => $inputField . '-aspectRatio', 'class' => 'form-control']);
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'col-md-2']);
        echo Html::label(Yii::t('sadovojav/cutter/cutter', 'Angle'), $inputField . '-dataRotate');
        echo Html::textInput($this->attribute . '-cropping[dataRotate]', '', ['id' => $inputField . '-dataRotate', 'class' => 'form-control']);
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'col-md-2']);
        echo Html::label(Yii::t('sadovojav/cutter/cutter', 'Position') . ' (x)', $inputField . '-dataX');
        echo Html::textInput($this->attribute . '-cropping[dataX]', '', ['id' => $inputField . '-dataX', 'class' => 'form-control']);
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'col-md-2']);
        echo Html::label(Yii::t('sadovojav/cutter/cutter', 'Position') . ' (y)', $inputField . '-dataY');
        echo Html::textInput($this->attribute . '-cropping[dataY]', '', ['id' => $inputField . '-dataY', 'class' => 'form-control']);
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'col-md-2']);
        echo Html::label(Yii::t('sadovojav/cutter/cutter', 'Width'), $inputField . '-dataWidth');
        echo Html::textInput($this->attribute . '-cropping[dataWidth]', '', ['id' => $inputField . '-dataWidth', 'class' => 'form-control']);
        echo Html::endTag('div');

        echo Html::beginTag('div', ['class' => 'col-md-2']);
        echo Html::label(Yii::t('sadovojav/cutter/cutter', 'Height'), $inputField . '-dataHeight');
        echo Html::textInput($this->attribute . '-cropping[dataHeight]', '', ['id' => $inputField . '-dataHeight', 'class' => 'form-control']);
        echo Html::endTag('div');
        echo Html::endTag('div');

        Modal::end();

        echo Html::endTag('div');

        $options = [
            'inputField' => $inputField,
            'useWindowHeight' => $this->useWindowHeight,
            'cropperOptions' => $this->cropperOptions
        ];

        $options = Json::encode($options);

        $this->view->registerJs('jQuery("#' . $inputField . '").cutter(' . $options . ');');
    }

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['sadovojav/cutter/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@vendor/sadovojav/yii2-image-cutter/messages',
            'fileMap' => [
                'sadovojav/cutter/cutter' => 'cutter.php',
            ],
        ];
    }

    private function getModalFooter($inputField)
    {
        return Html::beginTag('div', [
            'class' => 'btn-toolbar pull-left'
        ]) .
        ButtonGroup::widget([
            'encodeLabels' => false,
            'buttons' => [
                [
                    'label' => '<i class="glyphicon glyphicon-move"></i>',
                    'options' => [
                        'type' => 'button',
                        'data-method' => 'setDragMode',
                        'data-option' => 'move',
                        'class' => 'btn btn-primary',
                        'title' => Yii::t('sadovojav/cutter/cutter', 'Drag mode "move"'),
                    ]
                ],
                [
                    'label' => '<i class="glyphicon glyphicon-scissors"></i>',
                    'options' => [
                        'type' => 'button',
                        'data-method' => 'setDragMode',
                        'data-option' => 'crop',
                        'class' => 'btn btn-primary',
                        'data-title' => Yii::t('sadovojav/cutter/cutter', 'Drag mode "crop"'),
                    ]
                ],
            ],
            'options' => [
                'class' => 'pull-left'
            ]
        ]) .
        ButtonGroup::widget([
            'encodeLabels' => false,
            'buttons' => [
                [
                    'label' => '<i class="glyphicon glyphicon-ok"></i>',
                    'options' => [
                        'type' => 'button',
                        'data-method' => 'crop',
                        'class' => 'btn btn-primary',
                        'data-title' => Yii::t('sadovojav/cutter/cutter', 'Crop'),
                    ]
                ],
                [
                    'label' => '<i class="glyphicon glyphicon-refresh"></i>',
                    'options' => [
                        'type' => 'button',
                        'data-method' => 'reset',
                        'class' => 'btn btn-primary',
                        'title' => Yii::t('sadovojav/cutter/cutter', 'Refresh'),
                    ]
                ],
                [
                    'label' => '<i class="glyphicon glyphicon-remove"></i>',
                    'options' => [
                        'type' => 'button',
                        'data-method' => 'clear',
                        'class' => 'btn btn-primary',
                        'title' => Yii::t('sadovojav/cutter/cutter', 'Remove'),
                    ]
                ],
            ],
            'options' => [
                'class' => 'pull-left'
            ]
        ]) .
        ButtonGroup::widget([
            'encodeLabels' => false,
            'buttons' => [
                [
                    'label' => '<i class="glyphicon glyphicon-zoom-in"></i>',
                    'options' => [
                        'type' => 'button',
                        'data-method' => 'zoom',
                        'data-option' => '0.1',
                        'class' => 'btn btn-primary',
                        'title' => Yii::t('sadovojav/cutter/cutter', 'Zoom In'),
                    ],
                    'visible' => $this->cropperOptions['zoomable']
                ],
                [
                    'label' => '<i class="glyphicon glyphicon-zoom-out"></i>',
                    'options' => [
                        'type' => 'button',
                        'data-method' => 'zoom',
                        'data-option' => '-0.1',
                        'class' => 'btn btn-primary',
                        'title' => Yii::t('sadovojav/cutter/cutter', 'Zoom Out'),
                    ],
                    'visible' => $this->cropperOptions['zoomable']
                ],
                [
                    'label' => '<i class="glyphicon glyphicon-share-alt  icon-flipped"></i>',
                    'options' => [
                        'type' => 'button',
                        'data-method' => 'rotate',
                        'data-option' => '45',
                        'class' => 'btn btn-primary',
                        'title' => Yii::t('sadovojav/cutter/cutter', 'Rotate left'),
                    ],
                    'visible' => $this->cropperOptions['rotatable']
                ],
                [
                    'label' => '<i class="glyphicon glyphicon-share-alt"></i>',
                    'options' => [
                        'type' => 'button',
                        'data-method' => 'rotate',
                        'data-option' => '-45',
                        'class' => 'btn btn-primary',
                        'title' => Yii::t('sadovojav/cutter/cutter', 'Rotate right'),
                    ],
                    'visible' => $this->cropperOptions['rotatable']
                ],
            ],
            'options' => [
                'class' => 'pull-left'
            ]
        ]) .
        ButtonGroup::widget([
            'encodeLabels' => false,
            'buttons' => [
                [
                    'label' => '<i class="glyphicon glyphicon-glyphicon glyphicon-resize-full"></i>',
                    'options' => [
                        'type' => 'button',
                        'data-method' => 'setAspectRatio',
                        'data-target' => '#' . $inputField . '-aspectRatio',
                        'class' => 'btn btn-primary',
                        'title' => Yii::t('sadovojav/cutter/cutter', 'Set aspect ratio'),
                    ]
                ],
                [
                    'label' => '<i class="glyphicon glyphicon-upload"></i>',
                    'options' => [
                        'type' => 'button',
                        'data-method' => 'setData',
                        'class' => 'btn btn-primary',
                        'title' => Yii::t('sadovojav/cutter/cutter', 'Set data'),
                    ]
                ],
            ],
            'options' => [
                'class' => 'pull-left'
            ]
        ]) .
        Html::endTag('div') .
        Html::button(Yii::t('sadovojav/cutter/cutter', 'Cancel'), [
            'id' => $this->imageOptions['id'] . '_button_cancel', 'class' => 'btn btn-danger'
        ]) . Html::button(Yii::t('sadovojav/cutter/cutter', 'Accept'), [
            'id' => $this->imageOptions['id'] . '_button_accept', 'class' => 'btn btn-success'
        ]);
    }
}