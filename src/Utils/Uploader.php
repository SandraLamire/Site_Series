<?php

namespace App\Utils;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{
    public function upload(UploadedFile $file, string $directory, string $name ="")
    {
        // TODO faire vérif que le fichier existe

        // création d'un nouveau nom
        // puis lancer composer require symfony/uid dans le termianl
        $newFileName = $name . "-" . uniquid(). "-" . $file->guessExtension();
        // copie du fichier dans le répertoire de sauvegarde en le renommant
        $file->move($directory, $newFileName);

        return $newFileName;
    }
}

