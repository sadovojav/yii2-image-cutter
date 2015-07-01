<?php

namespace sadovojav\cutter;

use Yii;
use yii\bootstrap\ButtonGroup;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Json;

/**
 * Class Cutter
 * @package sadovojav\cutter\widgets
 */
class Cutter extends \yii\widgets\InputWidget
{
    /** @var array */
    public $imageOptions;

    /** @var array */
    public $jcropOptions = [];

    /** @var array */
    public $defaultJcropOptions = [
        'rotatable' => true,
        'zoomable' => true,
        'movable' => true,
    ];

    public function init()
    {
        parent::init();

        $this->registerTranslations();

        $this->jcropOptions = array_merge($this->jcropOptions, $this->defaultJcropOptions);
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

        echo Html::beginTag('div', ['id' => $inputField . '-cutter']);
            echo Html::activeFileInput($this->model, $this->attribute);

            echo Html::beginTag('div', [
                'class' => 'preview-pane',
                'style' => $this->model->{$this->attribute} ? 'display:block' : 'display:none'
            ]);

                echo Html::beginTag('div', ['class' => 'preview-container']);
                    echo Html::img($this->model->{$this->attribute} ? $this->model->{$this->attribute} : null, [
                        'class' => 'preview-image img-responsive',
                    ]);
                echo Html::endTag('div');
            echo Html::endTag('div');

            echo Html::checkbox($this->attribute . '-remove', false, [
                'label' => Yii::t('sadovojav/cutter/default', 'REMOVE')
            ]);

            Modal::begin([
                'header' => Html::tag('h4', Yii::t('sadovojav/cutter/default', 'CUTTER')),
                'closeButton' => [],
                'footer' => $this->getModalFooter($inputField),
                'size' => Modal::SIZE_LARGE,
            ]);

            echo Html::beginTag('div', ['class' => 'image-container']);
                echo Html::img(null, $this->imageOptions);
            echo Html::endTag('div');

            echo Html::tag('br');

            echo Html::beginTag('div', ['class' => 'row']);
                echo Html::beginTag('div', ['class' => 'col-md-2']);
                    echo Html::label(Yii::t('sadovojav/cutter/default', 'ASPECT_RATIO'), $inputField . '-aspectRatio');
                    echo Html::textInput($this->attribute . '-aspectRatio', isset($this->jcropOptions['aspectRatio']) ? $this->jcropOptions['aspectRatio'] : 0, ['id' => $inputField . '-aspectRatio', 'class' => 'form-control']);
                echo Html::endTag('div');

                echo Html::beginTag('div', ['class' => 'col-md-2']);
                    echo Html::label(Yii::t('sadovojav/cutter/default', 'ANGLE'), $inputField . '-dataRotate');
                    echo Html::textInput($this->attribute . '-cropping[dataRotate]', '', ['id' => $inputField . '-dataRotate', 'class' => 'form-control']);
                echo Html::endTag('div');

                echo Html::beginTag('div', ['class' => 'col-md-2']);
                    echo Html::label(Yii::t('sadovojav/cutter/default', 'POSITION') . ' (X)', $inputField . '-dataX');
                    echo Html::textInput($this->attribute . '-cropping[dataX]', '', ['id' => $inputField . '-dataX', 'class' => 'form-control']);
                echo Html::endTag('div');

                echo Html::beginTag('div', ['class' => 'col-md-2']);
                    echo Html::label(Yii::t('sadovojav/cutter/default', 'POSITION') . ' (Y)', $inputField . '-dataY');
                    echo Html::textInput($this->attribute . '-cropping[dataY]', '', ['id' => $inputField . '-dataY', 'class' => 'form-control']);
                echo Html::endTag('div');

                echo Html::beginTag('div', ['class' => 'col-md-2']);
                    echo Html::label(Yii::t('sadovojav/cutter/default', 'WIDTH'), $inputField . '-dataWidth');
                    echo Html::textInput($this->attribute . '-cropping[dataWidth]', '', ['id' => $inputField . '-dataWidth', 'class' => 'form-control']);
                echo Html::endTag('div');

                echo Html::beginTag('div', ['class' => 'col-md-2']);
                    echo Html::label(Yii::t('sadovojav/cutter/default', 'HEIGHT'), $inputField . '-dataHeight');
                    echo Html::textInput($this->attribute . '-cropping[dataHeight]', '', ['id' => $inputField . '-dataHeight', 'class' => 'form-control']);
                echo Html::endTag('div');
            echo Html::endTag('div');

            Modal::end();

        echo Html::endTag('div');

        $view = $this->getView();

        CutterAsset::register($view);

        $jcropOptions = [
            'inputField' => $inputField,
            'jcropOptions' => $this->jcropOptions
        ];

        $jcropOptions = Json::encode($jcropOptions);

        $view->registerJs('jQuery("#' . $inputField . '").cutter(' . $jcropOptions . ');');
    }

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['sadovojav/cutter/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@vendor/sadovojav/yii2-image-cutter/messages',
            'fileMap' => [
                'sadovojav/cutter/default' => 'default.php',
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
                            'title' => Yii::t('sadovojav/cutter/default', 'DRAG_MODE_MOVE'),
                        ]
                    ],
                    [
                        'label' => '<i class="glyphicon glyphicon-scissors"></i>',
                        'options' => [
                            'type' => 'button',
                            'data-method' => 'setDragMode',
                            'data-option' => 'crop',
                            'class' => 'btn btn-primary',
                            'data-title' => Yii::t('sadovojav/cutter/default', 'DRAG_MODE_CROP'),
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
                            'data-title' => Yii::t('sadovojav/cutter/default', 'CROP'),
                        ]
                    ],
                    [
                        'label' => '<i class="glyphicon glyphicon-refresh"></i>',
                        'options' => [
                            'type' => 'button',
                            'data-method' => 'reset',
                            'class' => 'btn btn-primary',
                            'title' => Yii::t('sadovojav/cutter/default', 'REFRESH'),
                        ]
                    ],
                    [
                        'label' => '<i class="glyphicon glyphicon-remove"></i>',
                        'options' => [
                            'type' => 'button',
                            'data-method' => 'clear',
                            'class' => 'btn btn-primary',
                            'title' => Yii::t('sadovojav/cutter/default', 'REMOVE'),
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
                            'title' => Yii::t('sadovojav/cutter/default', 'ZOOM_IN'),
                        ],
                        'visible' => $this->jcropOptions['zoomable']
                    ],
                    [
                        'label' => '<i class="glyphicon glyphicon-zoom-out"></i>',
                        'options' => [
                            'type' => 'button',
                            'data-method' => 'zoom',
                            'data-option' => '-0.1',
                            'class' => 'btn btn-primary',
                            'title' => Yii::t('sadovojav/cutter/default', 'ZOOM_OUT'),
                        ],
                        'visible' => $this->jcropOptions['zoomable']
                    ],
                    [
                        'label' => '<i class="glyphicon glyphicon-share-alt  icon-flipped"></i>',
                        'options' => [
                            'type' => 'button',
                            'data-method' => 'rotate',
                            'data-option' => '45',
                            'class' => 'btn btn-primary',
                            'title' => Yii::t('sadovojav/cutter/default', 'ROTATE_LEFT'),
                        ],
                        'visible' => $this->jcropOptions['rotatable']
                    ],
                    [
                        'label' => '<i class="glyphicon glyphicon-share-alt"></i>',
                        'options' => [
                            'type' => 'button',
                            'data-method' => 'rotate',
                            'data-option' => '-45',
                            'class' => 'btn btn-primary',
                            'title' => Yii::t('sadovojav/cutter/default', 'ROTATE_RIGHT'),
                        ],
                        'visible' => $this->jcropOptions['rotatable']
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
                            'title' => Yii::t('sadovojav/cutter/default', 'SET_ASPECT_RATIO'),
                        ]
                    ],
                    [
                        'label' => '<i class="glyphicon glyphicon-upload"></i>',
                        'options' => [
                            'type' => 'button',
                            'data-method' => 'setData',
                            'class' => 'btn btn-primary',
                            'title' => Yii::t('sadovojav/cutter/default', 'SET_DATA'),
                        ]
                    ],
                ],
                'options' => [
                    'class' => 'pull-left'
                ]
            ]) .
            Html::endTag('div') .
            Html::button(Yii::t('sadovojav/cutter/default', 'CANCEL'), [
                'id' => $this->imageOptions['id'] . '_button_cancel', 'class' => 'btn btn-danger'
            ]) . Html::button(Yii::t('sadovojav/cutter/default', 'ACCEPT'), [
                'id' => $this->imageOptions['id'] . '_button_accept', 'class' => 'btn btn-success'
            ]);
    }
}