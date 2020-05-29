<?php

declare(strict_types=1);

/**
 * UnPhar - PHP Utility tool to extracting a Phar (PHP Archive) file in batch mode.
 * 
 * @author KennFatt https://github.com/KennFatt
 * @website https://kennan.xyz/
 */

namespace unphar {

    use Phar;
    use PharException;
    use RecursiveDirectoryIterator;
    use RecursiveIteratorIterator;
    use UnexpectedValueException;

    /** Path to current directory  */
    define("DIRECTORY_ROOT", dirname(__FILE__) . DIRECTORY_SEPARATOR);

    /**
     * Relative path to folder `phars/`.
     * 
     * The folder is used to be the place of all your .phar files.
     */
    define("DIRECTORY_PHARS", DIRECTORY_ROOT . "phars");

    /**
     * Relative path to folder `out/`.
     * 
     * The place for all extracted .phar files.
     */
    define("DIRECTORY_OUT", DIRECTORY_ROOT . "out");

    function cli(): void
    {
        checkDirectory();
        $pharFiles = scanPharFiles();

        if ($pharFiles === []) {
            printf("There is no .phar file inside your `phars/` folder, please try again.\n");
            return;
        }

        extractPharFiles($pharFiles);
    }

    /** Safely check required directories. */
    function checkDirectory(): void
    {
        if (!is_dir(DIRECTORY_PHARS)) {
            mkdir(DIRECTORY_PHARS, 0777);
        }

        if (!is_dir(DIRECTORY_OUT)) {
            mkdir(DIRECTORY_OUT, 0777);
        }
    }

    /**
     * Scan all files inside folder `phars/` and
     *  store it into an array if the file were valid Phar.
     * 
     * @return \Phar[]|array
     */
    function scanPharFiles(): array
    {
        $phars = [];

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(DIRECTORY_PHARS));
        /**
         * @var \SplFileInfo $fileInfo
         */
        foreach ($iterator as $pathName => $fileInfo) {
            if (
                $fileInfo->getFilename() === "." ||
                $fileInfo->getFilename() === ".." ||
                strtolower($fileInfo->getExtension()) !== "phar"
            ) {
                continue;
            }

            try {
                $cleanFileName = explode(".", $fileInfo->getBasename())[0];
                if (!isset($phars[$cleanFileName])) {
                    $phars[$cleanFileName] = new Phar($pathName);
                }
            } catch (UnexpectedValueException $e) {
                printf("File: `%s` is not a valid Phar.\n", $fileInfo->getFilename());
            }
        }

        return $phars;
    }

    /**
     * Extract all given files of Phar to respective directory
     *  inside folder `out/`.
     * 
     * @param \Phar[] $pharFiles Array of scanned Phar files.
     */
    function extractPharFiles(array $pharFiles): void
    {
        $opts = getopt("", ["override"]);
        foreach ($pharFiles as $folderName => $pharFile) {
            $outPath = DIRECTORY_OUT . DIRECTORY_SEPARATOR . $folderName;
            if (is_dir($outPath) && !isset($opts["override"])) {
                printf(
                    "Skipping to extract `%s` because the destination path is already exists!\n",
                    $folderName
                );
                continue;
            }

            @mkdir($outPath);
            try {
                $pharFile->extractTo($outPath, null, true);
                printf(
                    "Successfully extracting `%s` to `%s`\n",
                    $folderName,
                    $outPath
                );
            } catch (PharException $e) {
                printf("%s\n", $e->getMessage());
            }
        }
    }

    \unphar\cli();
};
