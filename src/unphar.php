<?php

class UnPhar {

    const NAME = "UnPhar";
    const VERSION = "v1.3";
    const AUTHOR = "KennFatt";

    const INVALID_MESSAGE = 0;
    const INVALID_INPUT = 1;

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

        if (!is_dir(getcwd()."\\phars")) @mkdir(getcwd()."\\phars");
        if (!is_dir(getcwd()."\\phars\\extracted")) @mkdir(getcwd()."\\phars\\extracted");

        $this->sendMessage("
        Hello! This project is used for extracting Phar file (PHP Archiver) into source code.
        Creator: @KennFatt
        Github: https://www.github.com/KennFatt
        ");

        $this->sendMessage("
        Please select one option to continue:
        > extract - Extract phar file(s).
        > exit - Exit program.
        NOTE: Before extracting, you should place phar file(s) into ".getcwd()."\\phars\\"." folder!
        ");

        if (strtolower($this->readLine()) === "extract") {
            $this->processExecute();
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
    public function processExecute()
    {
        /** @var \Phar[] */
        $scannedFiles = [];

        $this->outputPath = getcwd()."\\phars\\extracted\\";

        foreach (scandir(getcwd()."\\phars\\") as $id => $fileName) {
            if ($fileName == "." or $fileName == ".." or $fileName == "extracted" or !strpos($fileName, ".phar")) continue;
            $scannedFiles[$fileName] = new Phar(getcwd()."\\phars\\$fileName", 0);
        }

        /** @var int */
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