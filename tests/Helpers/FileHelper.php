<?php

namespace codicastudio\Excel\Tests\Helpers;

class FileHelper
{
    public static function absolutePath($fileName, $diskName)
    {
        return config('filesystems.disks.' . $diskName . '.root') . DIRECTORY_SEPARATOR . $fileName;
    }
}
