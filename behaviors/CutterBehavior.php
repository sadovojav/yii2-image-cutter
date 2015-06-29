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
    /**
     * @var
     */
    public $attribute;

    /**
     * @var
     */
    public $baseDir;

    /**
     * @var
     */
    public $basePath;

    /**
     * @var int
     */
    public $quality = 92;

    /**
     * @return array
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'upload',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'upload',
            ActiveRecord::EVENT_BEFORE_DELETE => 'delete',
        ];
    }

    public function upload()
    {
        if ($uploadImage = UploadedFile::getInstance($this->owner, $this->attribute)) {
            if (!$this->owner->isNewRecord) {
                $this->delete();
            }

            $croppingFileName = md5($uploadImage->name . $this->quality . filemtime($uploadImage->tempName));
            $croppingFileExt = strrchr($uploadImage->name, '.');
            $croppingFileDir = substr($croppingFileName, 0, 2);

            $croppingFileBasePath = Yii::getAlias($this->basePath) . $this->baseDir;

            if (!is_dir($croppingFileBasePath)) {
                mkdir($croppingFileBasePath, 0755, true);
            }

            $croppingFilePath = Yii::getAlias($this->basePath) . $this->baseDir . DIRECTORY_SEPARATOR . $croppingFileDir;

            if (!is_dir($croppingFilePath)) {
                mkdir($croppingFilePath, 0755, true);
            }

            $croppingFile = $croppingFilePath . DIRECTORY_SEPARATOR . $croppingFileName . $croppingFileExt;

            $cropping = $_POST[$this->attribute . '-cropping'];

            $imageTmp = Image::getImagine()->open($uploadImage->tempName);
            $imageTmp->rotate($cropping['dataRotate']);

            $image = Image::getImagine()->create($imageTmp->getSize());
            $image->paste($imageTmp, new Point(0, 0));

            $point = new Point($cropping['dataX'], $cropping['dataY']);
            $box = new Box($cropping['dataWidth'], $cropping['dataHeight']);

            $image->crop($point, $box);
            $image->save($croppingFile, ['quality' => $this->quality]);

            $this->owner->{$this->attribute} = $this->baseDir . DIRECTORY_SEPARATOR . $croppingFileDir
                . DIRECTORY_SEPARATOR . $croppingFileName . $croppingFileExt;
        } elseif (isset($_POST[$this->attribute . '-remove']) && $_POST[$this->attribute . '-remove']) {
            $this->delete();
        } elseif (isset($this->owner->oldAttributes[$this->attribute])) {
            $this->owner->{$this->attribute} = $this->owner->oldAttributes[$this->attribute];
        }
    }

    public function delete()
    {
        $file = Yii::getAlias($this->basePath) . $this->owner->oldAttributes[$this->attribute];

        if (is_file($file) && file_exists($file)) {
            unlink(Yii::getAlias($this->basePath) . $this->owner->oldAttributes[$this->attribute]);
        }
    }
}
