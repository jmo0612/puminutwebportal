<?php
	Class JmoDateTime extends DateTime{
		public function JmoDateTime($time="now",$format="Y-m-d H:i:s"){
			if($time=="now") {
				parent::__construct();
			}else{
				$tmp=DateTime::createFromFormat($format,$time);
				parent::__construct($tmp->format("Y-m-d H:i:s"));
			}
		}
		public function toString($format="Y-m-d H:i:s") {
      	return $this->format($format);
    	}
    	public function diffInSeconds($date){
    		$d=(int)$this->diff($date)->format("%r%a");
    		$h=(int)$this->diff($date)->format("%r%h");
    		$m=(int)$this->diff($date)->format("%r%i");
    		$s=(int)$this->diff($date)->format("%r%s");
    		return $d*24*60*60+$h*60*60+$m*60+$s;
    	}
    	public function diffInMinutes($date){
    		return (int)($this->diffInSeconds($date)/60);
    	}
    	public function diffInHours($date){
    		return (int)($this->diffInSeconds($date)/3600);
    	} 
    	public function diffInDays($date){
    		return (int)$this->diff($date)->format("%r%a");
    	}
    	public function diffInMonths30($date){
    		return (int)($this->diffInDays($date)/30);
    	}
    	public function diffInYears30($date){
    		return (int)($this->diffInMonths30($date)/12);
    	}
	}
	echo (new JmoDateTime("02/02/2018 00:58:59","d/m/Y H:i:s"))->diffInYears30(new JmoDateTime("28/01/2019 00:58:59","d/m/Y H:i:s"));
	
?>
