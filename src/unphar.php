<?php

class UnPhar {

    const NAME = "UnPhar";
    const VERSION = "v1.5";
    const AUTHOR = "KennFatt";

    const INVALID_MESSAGE = 0;
    const INVALID_INPUT = 1;

    /** @var string */
    private $outputPath = "";

	/**
	 * UnPhar constructor.
	 */
    public function __construct()
    {
        $this->init();
    }

    /**
     * Initiate program
     *
     * @return void
     */
    public function init() : void
    {
        if (version_compare(phpversion(), "7.1.0", "<")) {
            $this->close("[Critical] Requires PHP Version >= 7.1.0");
        }

        cli_set_process_title(UnPhar::NAME . " - " . UnPhar::VERSION . " @" . UnPhar::AUTHOR);

        if (!is_dir(getcwd(). DS . "phars"))
        	@mkdir(getcwd(). DS . "phars");

        if (!is_dir(getcwd(). DS . "phars" . DS . "extracted"))
        	@mkdir(getcwd(). DS . "phars" . DS . "extracted");

        $this->sendMessage("
        Hello! This project is used for extracting Phar file (PHP Archiver) into source code.
        Creator: https://kennan.xyz/
        Github: https://www.github.com/KennFatt
        ");

        $this->sendMessage("
        Please select one option to continue:
        > extract - Extract phar file(s).
        > exit - Exit program.
        NOTE: Before extracting, you should place phar file(s) into ".getcwd()."/phars/"." folder!
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
     * @param string $message
     * @return void
     */
    public function close(string $message = "") : void
    {
        $this->outputPath = "";

        if ($message !== "") {
            $this->sendMessage($message);
        }

        $this->sendMessage("Thank you for using " . UnPhar::NAME . " by " . UnPhar::AUTHOR . " !");
        exit;
    }

    /**
     * Extracting process.
     *
     * @return void
     */
    public function processExecute() : void
    {
        /** @var \Phar[] */
        $scannedFiles = [];

        $this->outputPath = getcwd(). DS . "phars" . DS . "extracted" . DS;

        foreach (scandir(getcwd(). DS . "phars" . DS) as $id => $fileName) {
            if ($fileName === "." or $fileName === ".." or $fileName === "extracted" or !strpos($fileName, ".phar")) continue;
            $scannedFiles[$fileName] = new Phar(getcwd(). DS . "phars" . DS . "$fileName", 0);
        }

        /** @var int */
        $totalFiles = count($scannedFiles);

        if ($totalFiles <= 0) {
            $this->close("[Error] Could not find Phar files in " .getcwd()."/phars/ directory!");
            return;
        }

        foreach ($scannedFiles as $name => $pharClass) {
            $this->sendMessage("[$totalFiles] Extracting $name...");

            $mName = explode(".", $name);
            $name = $mName[0];

            $pharClass->extractTo($this->outputPath. DS . "unphar-{$name}" . DS, null, true);
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
     * @return string
     */
    public function sendMessage(string $message) : string
    {
        $message = $message !== "" ? $message : $this->errorCause(UnPhar::INVALID_MESSAGE);
        echo $message . PHP_EOL;
        return $message;
    }

    /**
     * Get input from STDIN.
     *
     * @return string
     */
    public function readLine() : string
    {
        $input = trim((string) fgets(STDIN));
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

define("DS", DIRECTORY_SEPARATOR);

new UnPhar();