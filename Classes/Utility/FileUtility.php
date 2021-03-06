<?php
declare(strict_types=1);
namespace In2code\Migration\Utility;

use In2code\Migration\Exception\FileNotFoundException;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FileUtility
 */
class FileUtility
{

    /**
     * @param string $file absolute filename like "/var/www/domain.org/public/fileadmin/file.jpg"
     * @return string
     */
    public static function getBase64CodeFromFile(string $file): string
    {
        if (is_file($file)) {
            $content = file_get_contents($file);
            return base64_encode($content);
        }
        return '';
    }

    /**
     * @param string $filename relative filename like "fileadmin/file.jpg"
     * @param string $content base64 encoded content of the file
     * @param bool $overwrite overwrite existing files
     * @return bool
     */
    public static function writeFileFromBase64Code(string $filename, string $content, bool $overwrite = false): bool
    {
        $file = GeneralUtility::getFileAbsFileName($filename);
        self::createFolderIfNotExists($file);
        if ($overwrite === true || is_file($file) === false) {
            return GeneralUtility::writeFile($file, base64_decode($content));
        }
        return false;
    }

    /**
     * @param string $fromPath Absolute path and filename
     * @param string $toPath Absolute path and filename
     * @param bool $overwrite should an possibly existing file be overwritten?
     * @return void
     */
    public static function copyFile(string $fromPath, string $toPath, bool $overwrite = false): void
    {
        if (is_file($fromPath) !== false) {
            self::createFolderIfNotExists($toPath);
            exec('cp ' . ($overwrite === false ? '-n ' : '') . $fromPath . ' ' . $toPath);
        }
    }

    /**
     * @param string $pathAndFilename
     * @return void
     */
    protected static function createFolderIfNotExists(string $pathAndFilename)
    {
        $path = self::getPathFromPathAndFilename($pathAndFilename);
        if (!is_dir($path)) {
            try {
                GeneralUtility::mkdir_deep($path);
            } catch (\Exception $exception) {
                throw new \UnexpectedValueException('Folder ' . $path . ' could not be created', 1549533300);
            }
        }
    }

    /**
     * @param string $pathAndFilename
     * @return string
     */
    protected static function getPathFromPathAndFilename($pathAndFilename)
    {
        $pathInfo = pathinfo($pathAndFilename);
        return $pathInfo['dirname'];
    }
}
