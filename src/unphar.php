<?php

class UnPhar {

    const NAME = "UnPhar";
    const VERSION = "v1.0";
    const AUTHOR = "KennFatt";

    const INVALID_MESSAGE = 0;
    const INVALID_INPUT = 1;

    /** @var \Phar */
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

        $this->sendMessage("
        Hello! This project is used for extracting Phar files (PHP Archiver) to source code.
        Creator: @KennFatt
        Github: https://www.github.com/KennFatt
        ");

        $this->sendMessage("
        Do you want execute this program? (Type y for yes).
        ");

        if (strtolower($this->readLine()) === "y") {
            $this->processExecute();
        } else {
            $this->sendMessage("Force closing...");
            $this->close();
        }
    }

    /**
     * Close the program.
     * TODO: Add close message and force close value
     * 
     * @return mixed
     */
    public function close()
    {
        if (isset($this->pharFile)) $this->pharFile = null;
        if (isset($this->outputPath)) $this->outputPath = "";

        $this->sendMessage("Thank you for using " . UnPhar::NAME . "!");
        exit;
    }

    /**
     * Extracting process.
     *
     * @return mixed
     */
    public function processExecute()
    {
        $pharName = "";

        $this->sendMessage("Please insert Phar name (Ex: Lib.phar): ");
        
        if ($this->readLine() !== "" and strpos($this->tempInput, ".phar")) {
            $this->pharFile = new Phar($this->tempInput);
            $pharName = explode(".", $this->tempInput)[0];
        } else {
            $this->errorCause(UnPhar::INVALID_INPUT);
        }

        $this->sendMessage("Please insert a path for extracting data: ");
        
        if ($this->readLine() !== "") {
            if (is_dir($this->tempInput)) {
                $this->outputPath = $this->tempInput;

                if (!is_dir($this->outputPath.$pharName."-master")) @mkdir($this->outputPath.$pharName."-master");
                $this->outputPath = $this->outputPath.$pharName."-master";

            } else {
                $this->sendMessage("Invalid directory! Force closing...");
                $this->close();
            }
        } else {
            $this->errorCause(UnPhar::INVALID_INPUT);
        }

        $this->sendMessage("Extracting a phar, please wait...");
        $this->pharFile->extractTo($this->outputPath, null, true);
        $this->sendMessage("Extracting succeed! File located at " . $this->outputPath);
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
     * Check if input is valid value or not.
     *
     * @param string $message
     *
     * @return bool
     */
    public function getInput(string $message) : bool
    {
        if ($message !== "") {
            return true;
        } else {
            $this->errorCause(UnPhar::INVALID_INPUT);
            return false;
        }
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
                $this->sendMessage("Invalid input! Input must be a string and not null!");
                $this->sendMessage("Force closing...");
                $this->close();
                return true;
            
            case UnPhar::INVALID_MESSAGE:
                $this->sendMessage("Invalid message! Message must be a string and not null!");
                $this->sendMessage("Force closing...");
                $this->close();
                return true;
            
            default:
                $this->sendMessage("[Error] @param $cause is unknown.");
                $this->sendMessage("Force closing...");
                $this->close();
                return false;
        }
    }
}

function run() {
    $class = new UnPhar();
}

run();