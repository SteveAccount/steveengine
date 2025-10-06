<?php

//namespace App\Utils;
//
//use Illuminate\Support\Facades\Log;
namespace SteveEngine;

/**
 * Class Image
 */
class Image{
    /**
     * @var
     */
    private $image;
    /**
     * @var array
     */
    public $imageInfo;
    /**
     * @var array
     */
    public $imageType;

    /**
     * Make a new Image class.
     * @return Image
     */
    public static function new() : Image{
        return new self();
    }

    /**
     * Load an image to Image class.
     * @param string $filename
     * @return Image
     */
    public function load(string $filename) : Image{
        if (file_exists($filename)){
            $this->imageInfo = getimagesize($filename);
            $this->imageType = $this->imageInfo[2];
            switch($this->imageType){
                case IMAGETYPE_JPEG:
                    $this->image = imagecreatefromjpeg($filename);
                    break;
                case IMAGETYPE_PNG:
                    $this->image = imagecreatefrompng($filename);
                    break;
            }    
        }
        return $this;
    }

    /**
     * Resize image if exists.
     * @param array $size
     * @param bool $canResizeIfLess
     * @param bool $canFillNew
     * @return Image
     */
    public function resize(array $size, bool $canResizeIfLess = false, bool $isNewSizeFix = true) : Image{
        if ($this->image && $size !== [0, 0]){
            $requiredWidth  = $size[0];
            $requiredHeight = $size[1];
            $imageWidth     = $this->imageInfo[0];
            $imageHeight    = $this->imageInfo[1];

            //Kell módosítani a képet?
            if (!$canResizeIfLess && $requiredWidth > $imageWidth && $requiredHeight > $imageHeight) {
                return $this;
            }

            $srcX           = 0;
            $srcY           = 0;
            $ratio          = 1;
            $zoomedWidth    = $imageWidth;
            $zoomedHeight   = $imageHeight;

            if ($requiredWidth <= $imageWidth && $requiredHeight <= $imageHeight) {
                $widthRatio     = $imageWidth / $requiredWidth; // 19,2
                $heightRatio    = $imageHeight / $requiredHeight; // 6,2

                if ($isNewSizeFix){
                    $ratio          = $widthRatio >= $heightRatio ? $heightRatio : $widthRatio; //6,2
                    $zoomedWidth    = $requiredWidth * $ratio;//620
                    $zoomedHeight   = $requiredHeight * $ratio;//200
                    $srcX           = ($imageWidth - $zoomedWidth) / 2;
                    $srcY           = ($imageHeight - $zoomedHeight) / 2;
                } else{
                    $ratio          = $widthRatio > $heightRatio ? $widthRatio : $heightRatio; //19,2
                    $requiredWidth  = $imageWidth / $ratio; //100
                    $requiredHeight = $imageHeight / $ratio;//32,29
                }
            }

            $newImage       = imagecreatetruecolor($requiredWidth, $requiredHeight);
            imagecopyresampled($newImage, $this->image, 0, 0, $srcX, $srcY, $requiredWidth, $requiredHeight, $zoomedWidth, $zoomedHeight);
            $this->image = $newImage;
        }
        return $this;
    }


    /**
     * Save image if exists.
     * @param string $filename
     * @return bool
     */
    public function save(string $filename) : bool{
        if ($this->image){
            switch($this->imageType){
                case IMAGETYPE_JPEG:
                    imagejpeg($this->image, $filename);
                    break;
                case IMAGETYPE_PNG:
                    imagepng($this->image, $filename);
                    break;
            }
            return true;
        }
        return false;
    }
}
