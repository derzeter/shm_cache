<?php

// Exception class

class ForbiddenException extends Exception {};

//
// For shm_cache class to work, /dev/shm needs to be there (on filesystem), and needs to be of good side (df -h to see)
//

class shm_cache {

// path might vary depending of OS - usually in *ix - its /dev/shm/

    public $path="/dev/shm/";

// unsupported file types - So if one tries to create executable files in cache.

    public $unsupported = array(
        'php',
        'phtml'
    );


//
// setpath(parameter[directory of /dev/shm/, with leadeing and last /])
// save the path of server RAM
//

    function setpath($parameter) {
	$this->path = $parameter;
    }

//
// createdir(paramater[directory within /dev/shm/)
// create directory in RAM if caching system relies on multiple directories
//

    function createdir($parameter) {
		if (!file_exists($this->path.$parameter)) {
    			mkdir($this->path.$parameter, 0777, true);
		}
    }


//
// cache_write(dir[directory within shm],parameter[file name to write],data[data to write],time[refresh time in minutes (approx.) - if time=0, force refresh])
// Write a cache file into /dev/shm
// return value : 0 on file created, -1 on error, 1 on file already existing.
//

    function cache_write($dir,$parameter,$data,$time) {

	// Check that cache is not trying to create a forbidden file
	$ext =  strtolower(pathinfo($parameter, PATHINFO_EXTENSION));
		if (in_array($ext, $this->unsupported)) { throw new ForbiddenException(); return 0; } // trying to create forbidden file means exception to throw.

	$err=0;
	clearstatcache();

	if($time>0) {
	       try {
			if(file_exists($this->path.$dir."/".$parameter)) {
				$time_end=filectime($this->path.$dir."/".$parameter); 
				$time_start=time();
				if($time_start>=($time_end+$time*60)) $err=0; else $err=1;
			}
                } catch (Exception $e) {
                        return -1;
                }
	} else $err=1;

	// check time

	if($err==0) {
		try {
			$fp = fopen($this->path.$dir."/".$parameter,"w");
			fputs($fp,$data);
			fclose($fp);
			touch($this->path.$dir."/".$parameter,time());
		} catch (Exception $e) {
			return -1;
		}

	}

	return 0;

    }

//
// cache_write(dir[directory within shm],parameter[file name to write],data)
// Write a cache file into /dev/shm
// return value : 'data' on file read, -1 on error
//

    function cache_read($dir,$parameter) {

	// Check that cache is not trying to read a forbidden file
	$ext =  strtolower(pathinfo($parameter, PATHINFO_EXTENSION));
		if (in_array($ext, $this->unsupported)) { throw new ForbiddenException(); return 0; } // trying to read forbidden file means exception to throw.
	$data="";
	try {
		$fp = fopen($this->path.$dir."/".$parameter,"r");
			while(!feof($fp)) $data.=fgets($fp,4096);
		fclose($fp);
	} catch (Exception $e) {
		return -1;
	}


	return $data;

    }


}
?>
