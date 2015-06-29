# Yii2 image cutter

This is a fork [Patroklo/yii2-widget-upload-crop](https://github.com/Patroklo/yii2-widget-upload-crop)

#### Features:
- Upload image
- Crop image
- Use Imagine
- Cache sorting to subdirectories

![cutter](https://cloud.githubusercontent.com/assets/9282021/8411519/fd601b0e-1e8c-11e5-83a5-1f8c4195f562.jpg)

### Composer

The preferred way to install this extension is through [Composer](http://getcomposer.org/).

Either run ```php composer.phar require sadovojav/yii2-image-cutter "dev-master"```

or add ```"sadovojav/yii2-image-cutter": "dev-master"``` to the require section of your ```composer.json```

### Use

* Add to the model behavior

```php
    use sadovojav\cutter\behaviors\CutterBehavior;

    public function behaviors()
    {
        return [
            'image' => [
                'class' => CutterBehavior::className(),
                'attribute' => 'image',
                'baseDir' => '/uploads/crop',
                'basePath' => '@webroot',
                'quality' => 100 //default 92
            ],
        ]
    }
    
    public function rules()
    {
        return [
            ['image', 'file', 'extensions' => 'jpg, jpeg, png', 'mimeTypes' => 'image/jpeg, image/png'],
        ];
    }
```

* Use in view
> Without client validation

```php
    <div class="form-group">
        <label class="control-label">Image</label>
        <?= \sadovojav\cutter\Cutter::widget([
            'model' => $model,
            'attribute' => 'image'
        ]); ?>
    </div>
```

or

> With client validation

```php
    <?= $form->field($model, 'image')->widget(\sadovojav\cutter\Cutter::className(), [
        //options
    ]); ?>
```

## Widget method options

* model (string) (obligatory)
> Defines the model that will be used to make the form input field.

* attribute (string) (obligatory)
> Defines the model attribute that will be used to make de form input field.

* imageOptions (array) (optional)
> List with options that will be added to the image field that will be used to define the crop data in the modal. The format should be ['option' => 'value'].

* jcropOptions (array) (optional)
> List with options that will be added in javaScript while creating the crop object. For more information about which options can be added you can [read this web](https://github.com/fengyuanchen/cropper#options).