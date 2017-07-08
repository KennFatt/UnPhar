<?php

class UnPhar {

    const NAME = "UnPhar";
    const VERSION = "v1.2";
    const AUTHOR = "KennFatt";

    const INVALID_MESSAGE = 0;
    const INVALID_INPUT = 1;

    /** @var \Phar|null */
    private $pharFile = null;

    /** @var string */
    private $outputPath = "";
    private $tempInput = "";

    public function __construct()
    {
        $this->init();
    }

    /**
     * Initiate program
     */
    public function init()
    {
        cli_set_process_title(UnPhar::NAME . " - " . UnPhar::VERSION . " @" . UnPhar::AUTHOR);

        // for multiple phars
        if (!is_dir(getcwd()."\\phars")) @mkdir(getcwd()."\\phars");
        if (!is_dir(getcwd()."\\phars\\extracted")) @mkdir(getcwd()."\\phars\\extracted");

        $this->sendMessage("
        Hello! This project is used for extracting Phar file (PHP Archiver) into source code.
        Creator: @KennFatt
        Github: https://www.github.com/KennFatt
        ");

        $this->sendMessage("
        Please select one option to continue:
        > single - Extracting single Phar file.
        > multiple - Extracting multiple Phar files.
        > exit - Exit program.
        NOTE: For multiple you should place phar file(s) into ".getcwd()."\\phars\\"." folder!
        ");

        if (strtolower($this->readLine()) === "single") {
            $this->processExecute();
        } elseif (strtolower($this->tempInput) === "multiple") {
            $this->processExecute(true);
        } else {
            $this->close("Force closing program!");
        }
    }

    /**
     * Close the program.
     * 
     * @return mixed
     */
    public function close(string $message = "")
    {
        if (isset($this->pharFile)) $this->pharFile = null;
        if (isset($this->outputPath)) $this->outputPath = "";

        if ($message !== "") {
            $this->sendMessage($message);
        }

        $this->sendMessage("Thank you for using " . UnPhar::NAME . "!");
        exit;
    }

    /**
     * Extracting process.
     *
     * @return void
     */
    public function processExecute(bool $multiple = false)
    {
        if ($multiple) {
            $scannedFiles = [];

            $this->outputPath = getcwd()."\\phars\\extracted\\";

            foreach (scandir(getcwd()."\\phars\\") as $id => $fileName) {
                if ($fileName == "." or $fileName == ".." or $fileName == "extracted" or !strpos($fileName, ".phar")) {
                    $this->sendMessage("[Error] Ignored files $fileName");
                    continue;
                }
                $scannedFiles[$fileName] = new Phar(getcwd()."\\phars\\$fileName");
            }

            $totalFiles = count($scannedFiles);

            if ($totalFiles <= 0) {
                $this->close("[Error] Could not find Phar files in " .getcwd()."\\phars\ directory!");
                return;
            }

            foreach ($scannedFiles as $name => $pharClass) {
                $this->sendMessage("[$totalFiles] Extracting $name...");
                $pharClass->extractTo($this->outputPath."\\$name\\", null, true);
                $totalFiles--;
            }

            $this->sendMessage("Extracting succeed! File located at " . $this->outputPath);
            return;
        }

        $pharName = "";

        $this->sendMessage("Please insert Phar name (Ex: Lib.phar): ");
        
        if ($this->readLine() !== "" and strpos($this->tempInput, ".phar")) {

            if (!is_file($this->tempInput)) {
                $this->close("Invalid Phar file!");
            }

            $this->pharFile = new Phar($this->tempInput);
            $pharName = explode(".", $this->tempInput)[0];
        } else {
            $this->errorCause(UnPhar::INVALID_INPUT);
        }

        $this->sendMessage("Please insert a path for extracting data (Ex: C:\Users\KENNAN\Desktop\): ");
        
        if ($this->readLine() !== "") {
            if (is_dir($this->tempInput)) {
                $this->outputPath = $this->tempInput;

                if (!is_dir($this->outputPath.$pharName."-master")) @mkdir($this->outputPath.$pharName."-master");
                $this->outputPath = $this->outputPath.$pharName."-master";

            } else {
                $this->close("Invalid directory!");
            }
        } else {
            $this->errorCause(UnPhar::INVALID_INPUT);
        }

        $this->sendMessage("Extracting a phar, please wait...");
        $this->pharFile->extractTo($this->outputPath, null, true);
        $this->sendMessage("Extracting succeed! File located at " . $this->outputPath);
        return;
    }

    /**
     * Sending message to client.
     *
     * @param string $message
     *
     * @return mixed
     */
    public function sendMessage(string $message)
    {
        $message = $message !== "" ? $message : $this->errorCause(UnPhar::INVALID_MESSAGE);
        echo $message . PHP_EOL;
    }

    /**
     * Get input from STDIN.
     *
     * @return string
     */
    public function readLine() : string
    {
        $input = trim((string) fgets(STDIN));
        $this->tempInput = $input;
        return $input;
    }

    /**
     * A Function to handle error.
     *
     * @param int $cause
     *
     * @return bool
     */
    public function errorCause(int $cause) : bool
    {
        switch ($cause) {
            case UnPhar::INVALID_INPUT:
                $this->close("Invalid input! Input must be a string and not null!");
                return true;
            
            case UnPhar::INVALID_MESSAGE:
                $this->close("Invalid message! Message must be a string and not null!");
                return true;
            
            default:
                $this->close("[Error] @param $cause is unknown.");
                return false;
        }
    }
}

function run() {
    $class = new UnPhar();
}

run();