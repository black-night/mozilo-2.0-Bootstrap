<?php if(!defined('IS_CMS')) die();

/***************************************************************
 *
* Datenbank verwaltung fuer Plug-in Bootstrap
* by black-night - Daniel Neef
*
***************************************************************/

class bsCarouselItem {
    public $Img;
    public $Content;
    
    public function __construct($Img,$Content) {
        global $specialchars;
        $this->Img = htmlspecialchars(trim($Img));
        $this->Content = htmlspecialchars(trim($Content));
    }
}

class bsCarousel {
	public $ID;
	public $Name;
	public $Items = array();
	
	public function __construct($Name,$ID="") {
		global $specialchars;
		if ($ID == "") {
			$this->ID = $this->guid();
		}else{
			$this->ID = $ID;
		}
		$this->Name = htmlspecialchars(trim($Name));
	}
	
	protected function guid(){
		if (function_exists('com_create_guid')){
			return com_create_guid();
		}else{
			mt_srand((double)microtime()*10000);//optional for php 4.2.0 and up.
			$charid = strtoupper(md5(uniqid(rand(), true)));
			$hyphen = chr(45);// "-"
			$uuid = chr(123)// "{"
			.substr($charid, 0, 8).$hyphen
			.substr($charid, 8, 4).$hyphen
			.substr($charid,12, 4).$hyphen
			.substr($charid,16, 4).$hyphen
			.substr($charid,20,12)
			.chr(125);// "}"
			return $uuid;
		}
	}	
	
	public static function compare($a,$b) {
		return strcmp($a->Name,$b->Name);
	}
}

class bsDatabase {
	private static function lookFile($filename) {
		if (!file_exists($filename))
			touch($filename);
		$fp = fopen($filename, "c+");
		$retries = 0;
		$max_retries = 100;
		
		if (!$fp) {
			return false;
		}
		
		// keep trying to get a lock as long as possible
		do {
			if ($retries > 0) {
				usleep(rand(1, 10000));
			}
			$retries += 1;
		} while (!flock($fp, LOCK_EX) and $retries <= $max_retries);
		
		// couldn't get the lock, give up
		if ($retries == $max_retries) {
			return false;
		}		
		return $fp;
	}
	
	private static function unlookFile($fileHandle) {
		flock($fileHandle, LOCK_UN);
		fclose($fileHandle);		
		return true;
	}
		
	public static function deleteEntry($id,$filename) {
		$fp = self::lookFile($filename);
		if (!$fp) 
			return false;	
		$newData = array();
		if (filesize($filename) > 0) {
			$newData = fread($fp,filesize($filename));
			$newData = trim(str_replace("<?php die(); ?>","",$newData));
			$newData = unserialize($newData);			
		}
				
		for ($i = 0; $i < count($newData); $i++) {
			$Carousel = $newData[$i];
			if ($Carousel->ID == $id) {
				unset($newData[$i]);
				break;
			}
		}
		$newData = array_values($newData);
		
		rewind($fp);
		ftruncate($fp,0);
		
		fwrite($fp, "<?php die(); ?>\n".serialize($newData));
		self::unlookFile($fp);

		return true;	
	}
	
	public static function loadArray($filename) {
		if (!file_exists($filename))
			touch($filename);		
		$data = file_get_contents($filename);
		$data = trim(str_replace("<?php die(); ?>","",$data));
		return unserialize($data);
	}
	
	public static function saveArray($filename, $data) {
		$fp = self::lookFile($filename);
		if (!$fp) 
			return false;
		rewind($fp);
		ftruncate($fp,0);		

		fwrite($fp, "<?php die(); ?>\n".serialize($data));
		self::unlookFile($fp);

		return true;		
	}
	
	public static function appendArray($filename, $data) {
		$fp = self::lookFile($filename);
		if (!$fp) 
			return false;
		$newData = array();
		if (filesize($filename) > 0) {
			$newData = fread($fp,filesize($filename));
			$newData = trim(str_replace("<?php die(); ?>","",$newData));
			$newData = unserialize($newData);						
		}
				
		$newData[] = $data;
		
		rewind($fp);
		ftruncate($fp,0);

		fwrite($fp, "<?php die(); ?>\n".serialize($newData));
		self::unlookFile($fp);

		return true;		
	}
	
	public static function LoadCarousel($filename, $name) {
	    $data = self::loadArray($filename);
	    foreach ($data as $value) {
	        if ($value->Name == $name) {
	            return $value;
	        };
	    }
	    return false;
	}
	
	public static function LoadCarouselByID($filename, $id) {
	    $data = self::loadArray($filename);
	    foreach ($data as $value) {
	        if ($value->ID == $id) {
	            return $value;
	        };
	    }
	    return false;
	}	
}

?>