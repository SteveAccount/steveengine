<?php

namespace SteveEngine;

class UploadService {
    public array    $allowedFormats;
    public int      $allowedSize;
    public string   $uploadFolder;
    public array    $fileSignatures = [
        "ico"   => ["00 00 01 00"],
        "gif"   => ["47 49 46 38 37 61", "47 49 46 38 39 61"],
        "tif"   => ["49 49 2A 00"],
        "jpg"   => ["FF D8 DD DB", "FF D8 FF EE", "FF D8 FF E0"],
        "doc"   => ["d0 cf 11 e0 a1 b1 1a e1"],
        "xls"   => ["d0 cf 11 e0 a1 b1 1a e1"],
        "png"   => ["89 50 4E 47 0D 0A 1A 0A"],
        "pdf"   => ["25 50 44 46 2D"],
        "bmp"   => ["42 4d"],
        "webp"  => ["52 49 46 46 ?? ?? ?? ?? 57 45 42 50"],
    ];
    public array $errors = [];

    public function __construct(array $allowedFormats, int $allowedSize, string $uploadFolder) {
        $this->allowedFormats   = $allowedFormats ?? ["jpg", "pdf"];
        $this->allowedSize      = $allowedSize ?? 10000000;
        $this->uploadFolder     = $uploadFolder;
    }

    public function checkFiles(array $files) {
        $errors = [];

        foreach ($files as $file) {
            // Méret ellenőrzése
            if ($file["size"] > $this->allowedSize) {
                $errors[] = "A(z) " . $file["name"] . " fájl mérete túl nagy.";
                unlink($file["tmp_name"]);
                continue;
            }

            // Van-e signature a vizsgált fájlhoz?
            $fileExtension = pathinfo($file["name"])["extension"];
            $fileSignature = $this->fileSignatures[$fileExtension] ?? null;

            if (!$fileSignature) {
                $errors[] = "A fájl típusa nem ellenőrizhető.";
                unlink($file["tmp_name"]);
                continue;
            }

            // A vizsgált fájl típusa megfelelő-e?
            if (!in_array($fileExtension, $this->allowedFormats)) {
                $errors[] = "A fájl típusa nem megfelelő.";
                unlink($file["tmp_name"]);
                continue;
            }

            // A kiterjesztés és a signature egyezik?
            $signatureLength = count(explode(" ", $fileSignature[0]));
            $f              = fopen($file["tmp_name"], "rb");
            $header         = fgets($f, $signatureLength);
            fclose($f);

            $readSignature = "";
            foreach (unpack("C*", $header) as $byte) {
                $value  = dechex($byte);
                $hex    = strtoupper(str_repeat("0",2-strlen($value)) . $value);
                $readSignature .= $readSignature === "" ? $hex : " $hex";
            }

            $signatureCheck = false;
            foreach ($fileSignature as $signature) {
                if (str_starts_with($signature, $readSignature) || str_starts_with($readSignature, $signature)) {
                    $signatureCheck = true;
                }
            }

            if (!$signatureCheck) {
                $errors[] = "A fájl típusa nem megfelelő, manipulált.";
                unlink($file["tmp_name"]);
                continue;
            }

            // Minden rendebn, a fájl menthető.
            $targetPath = $this->uploadFolder . DIRECTORY_SEPARATOR . $file["name"];
            move_uploaded_file($file["tmp_name"], $targetPath);
        }

        return response($errors);
    }
}