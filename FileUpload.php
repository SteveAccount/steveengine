<?php

namespace SteveEngine;

class FileUpload{
    public static function uploadFileToTemp() : string {
        $tempDirectory = implode(DIRECTORY_SEPARATOR, [config()->get("appPath"), "Uploads", "Temp"]);
        if (!file_exists($tempDirectory)) {
            mkdir($tempDirectory, 0777, true);
        }

        $info       = pathinfo($_FILES["filepond"]["name"]);
        $id         = (new \DateTime())->format("YmdHisu");
        $fileName   = $id . "." . $info["extension"];
        $target     = implode(DIRECTORY_SEPARATOR, [config()->get("appPath"), "Uploads", "Temp", $fileName]);
        move_uploaded_file($_FILES["filepond"]["tmp_name"], $target);

        return $fileName;
    }

    public static function revertFileFromTemp() {
        $fileName   = file_get_contents('php://input');
        $path       = implode(DIRECTORY_SEPARATOR, [config()->get("appPath"), "Uploads", "Temp", $fileName]);

        if ($files = glob($path, GLOB_ERR)) {
            foreach ($files as $file) {
                unlink($file);
            }
        }
    }

    public static function saveFile(string $path, int $id, string $fileName) {
        $searchPath = implode(DIRECTORY_SEPARATOR, [config()->get("appPath"), "Uploads", "Temp", $fileName]);

        if ($files = glob($searchPath, GLOB_ERR)) {
            $sourcePath = $files[0];

            $pathParts          = [config()->get("appPath"), "Uploads"];
            $pathParts          = array_merge($pathParts, explode("/", $path));
            array_push($pathParts, $id);
            $targetDirectory    = implode(DIRECTORY_SEPARATOR, $pathParts);

            if (!file_exists($targetDirectory)) {
                mkdir($targetDirectory, 0777, true);
            }

            array_push($pathParts, $fileName);
            $targetPath = implode(DIRECTORY_SEPARATOR, $pathParts);
            rename($sourcePath, $targetPath);
        }
    }
}