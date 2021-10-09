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
    private $imageInfo;
    /**
     * @var array
     */
    private $imageType;

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
            if (!$canResizeIfLess && $requiredWidth > $imageWidth && $requiredHeight > $imageHeight){
                return $this;
            }

            $srcX   = 0;
            $srcY   = 0;
            $ratio  = 1;
            if (!($requiredWidth <= $imageWidth && $requiredHeight <= $imageHeight)){
                $widthRatio             = $requiredWidth / $imageWidth;
                $heightRatio            = $requiredHeight / $imageHeight;
                if ($isNewSizeFix ){
                    $ratio              = $widthRatio >= $heightRatio ? $widthRatio : $heightRatio;
                } else{
                    $ratio              = $widthRatio > $heightRatio ? $heightRatio : $widthRatio;
                    $requiredWidth      = $imageWidth * $ratio;
                    $requiredHeight     = $imageHeight * $ratio;
                }
            }
            $zoomedWidth    = $requiredWidth / $ratio;
            $zoomedHeight   = $requiredHeight / $ratio;
            $srcX           = ($imageWidth - $zoomedWidth) / 2;
            $srcY           = ($imageHeight - $zoomedHeight) / 2;
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
