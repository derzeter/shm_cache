<?php 

//
// For shm_cache class to work, /dev/shm needs to be there (on filesystem), and needs to be of good side (df -h to see)
//

class shm_cache { 
    
    public $path="/dev/shm/";

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
		if (in_array($ext, $this->unsupported)) die();

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
		if (in_array($ext, $this->unsupported)) die();
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
