<?php

namespace sadovojav\cutter\behaviors;

use Yii;
use yii\imagine\Image;
use Imagine\Image\Box;
use Imagine\Image\Point;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

/**
 * Class CutterBehavior
 * @package sadovojav\cutter\behavior
 */
class CutterBehavior extends \yii\behaviors\AttributeBehavior
{
    public $attribute;

    public $baseDir;

    public $basePath;

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_VALIDATE => 'upload',
            ActiveRecord::EVENT_BEFORE_DELETE => 'delete',
        ];
    }

    public function upload()
    {
        if ($uploadImage = UploadedFile::getInstance($this->owner, $this->attribute)) {
            if (!$this->owner->isNewRecord) {
                $this->delete();
            }

            $cropping = $_POST[$this->attribute . '-cropping'];

            $croppingFileName = md5($uploadImage->name . filemtime($uploadImage->tempName));
            $croppingFileExt = strrchr($uploadImage->name, '.');

            $croppingFilePath = Yii::getAlias($this->basePath) . $this->baseDir;

            $croppingFile = $croppingFilePath . DIRECTORY_SEPARATOR . $croppingFileName . $croppingFileExt;

            if (!is_dir($croppingFilePath)) {
                mkdir($croppingFilePath, 0755, true);
            }

            $image = Image::getImagine()->open($uploadImage->tempName);
            $point = new Point($cropping['x'], $cropping['y']);
            $box = new Box($cropping['width'], $cropping['height']);
            $image->crop($point, $box);
            $image->save($croppingFile);

            $this->owner->{$this->attribute} = $this->baseDir . DIRECTORY_SEPARATOR . $croppingFileName
                . $croppingFileExt;
        } elseif ($this->owner->oldAttributes[$this->attribute]) {
            $this->owner->{$this->attribute} = $this->owner->oldAttributes[$this->attribute];
        }
    }

    public function delete()
    {
        if (file_exists(Yii::getAlias($this->basePath) . $this->owner->oldAttributes[$this->attribute])) {
            unlink(Yii::getAlias($this->basePath) . $this->owner->oldAttributes[$this->attribute]);
        }
    }
}
