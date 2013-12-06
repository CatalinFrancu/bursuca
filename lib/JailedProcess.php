<?php

/**
 * Runs a program using bidirectional pipes to control input and output.
 * It's not really a jailed run; more security checks are needed.
 **/

class JailedProcess {
  const BINARY_NAME = 'binary';
  const DATA_NAME = 'bursuca.dat';

  // Process-related variables
  public $alive;               // is the process still ok
  public $exitCode;            // as returned by the child process
  private $debugName;          // something to prepend to debug messages
  private $workingDir;         // temporary working dir
  private $binaryFullPath;     // full path to the binary copied within the working dir
  private $dataFullPath;       // full path to the binary copied within the working dir
  private $process;            // child resource
  private $pipes;              // child input/output/error file descriptors

  /**
   * Copies the binary and data files to a unique temporary directory.
   * Runs the binary and keeps pipes for input / output
   **/ 
  function __construct($binaryName, $dataName, $debugName) {
    $this->debugName = $debugName;

    // Create a directory
    $this->workingDir = OS::tempdir('/tmp/', 'jp_');

    // Copy files and chmod the binary
    $this->binaryFullPath = $this->workingDir . '/' . self::BINARY_NAME;
    $this->dataFullPath = $this->workingDir . '/' . self::DATA_NAME;
    if (!@copy($binaryName, $this->binaryFullPath)) {
      exit("Nu am găsit programul {$this->programName}\n");
    }
    chmod($this->binaryFullPath, 0755);
    @copy($dataName, $this->dataFullPath);

    // Start the binary
    $this->alive = true;
    $desc = array(
      0 => array("pipe", "r"),  // stdin (from the child's point of view)
      1 => array("pipe", "w"),  // stdout
      2 => array("pipe", "w"),  // stderr
    );
    $this->process = proc_open($this->binaryFullPath, $desc, $this->pipes);
    if (!$this->process) {
      die("Cannot open process for {$this->programName}\n");
    }
  }

  /**
   * Closes all the pipes and make sure the process and all its children are dead.
   **/
  function __destruct() {
    foreach ($this->pipes as $p) {
      fclose($p);
    }
    $status = proc_get_status($this->process);
    $ppid = $status['pid'];
    $pids = preg_split('/\s+/', shell_exec("ps -o pid --no-heading --ppid $ppid"));
    foreach ($pids as $pid) {
      if (is_numeric($pid)) {
        posix_kill($pid, SIGKILL);
      }
    }
  }

  // Check on the child process. If this is the first time we notice it's dead, store the exit code.
  function checkStatus() {
    if ($this->alive) {
      $status = proc_get_status($this->process);
      if (!$status['running']) {
        $this->exitCode = $status['exitcode'];
        $this->alive = false;
        print "Program {$this->debugName} ended with exit code {$this->exitCode}\n";
      }
    }
  }

  function kill() {
    $this->alive = false;
  }

  /* Returns a line of text or null if no input is available */
  function readLine() {
    if (!$this->alive) {
      return null;
    }

    $s =  fgets($this->pipes[1]);
    if ($s === false) {
      // check one more time, since the program apparently ended
      $this->checkStatus();
      return null;
    }
    print "Read from program {$this->debugName}: $s";
    return trim($s);
  }

  function writeLine($s) {
    $this->checkStatus();
    if ($this->alive) {
      fprintf($this->pipes[0], $s . "\n");
      fflush($this->pipes[0]);
      print "Wrote to program {$this->debugName}: $s\n";
    }
  }
}

?>
