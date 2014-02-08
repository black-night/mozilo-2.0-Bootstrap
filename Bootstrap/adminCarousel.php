<?php if(!defined('IS_ADMIN') or !IS_ADMIN) die();

class Bootstrap_Carousel extends Bootstrap {
	const TAB_NEW_CAROUSEL = 'newCarousel';
	const TAB_SHOW_CAROUSEL = 'showCarousel';
	
	
	
	public function __construct($plugin) {
	    $this->lang_admin = $plugin->lang_admin;
	    $this->PLUGIN_SELF_DIR = $plugin->PLUGIN_SELF_DIR;
	}	

	public function output() {	    
	    $html  = $this->getHead();
	    $html .= '<style type="text/css"> body { background-color: #F8F8F8; } </style>';
	    $html .= '<script type="text/javascript" src="'.URL_BASE.PLUGIN_DIR_NAME.'/Bootstrap/js/admin.js"></script>';
	    	    		
		$deleteId = '';
		$deleteId = getRequestValue('delete','get',false);
		$this->deleteCarousel($deleteId);
		
		if (getRequestValue('bsid','post',false) !== false) {
		    $html .= $this->saveCarousel(getRequestValue('bsid','post',false));
		}elseif (getRequestValue('bsCarouselName','post',false) <> '') {
		    $html .= $this->saveNewCarousel();
		}
		
		$html .= '<div id="bs-admin-carousel" class="d_mo-td-content-width ui-tabs ui-widget ui-widget-content ui-corner-all mo-ui-tabs" style="position:relative;width:96%;margin:auto auto;">';
		$html .= '<ul id="js-menu-tabs" class="mo-menu-tabs ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-top">'
              .'<li class="ui-state-default ui-corner-top'.$this->isActiveTabHTML(self::TAB_SHOW_CAROUSEL).'"><a href="'.PLUGINADMIN_GET_URL.'&amp;actab='.self::TAB_SHOW_CAROUSEL.'" title="'.$this->lang_admin->getLanguageValue("config_Bootstrap_showCarousel").'" tabindex="0"><span class="mo-bold">'.$this->lang_admin->getLanguageValue("config_Bootstrap_showCarousel").'</span></a></li>'
              .'<li class="ui-state-default ui-corner-top'.$this->isActiveTabHTML(self::TAB_NEW_CAROUSEL).'"><a href="'.PLUGINADMIN_GET_URL.'&amp;actab='.self::TAB_NEW_CAROUSEL.'" title="'.$this->lang_admin->getLanguageValue("config_Bootstrap_newCarousel").'" tabindex="0"><span class="mo-bold">'.$this->lang_admin->getLanguageValue("config_Bootstrap_newCarousel").'</span></a></li>';	          		
		$html .= '</ul>';
		$html .= '<div class="d_plugins mo-ui-tabs-panel ui-widget-content ui-corner-bottom mo-no-border-top">';
		$html .= '<div style="padding: 5px;">';		
		
		if (!getRequestValue('actab','get',false) or getRequestValue('actab','get',false) == self::TAB_SHOW_CAROUSEL) {
			$html .= $this->getShowCarouselHTML();
		}elseif (getRequestValue('actab','get',false) == self::TAB_NEW_CAROUSEL) {
			$html .= $this->getNewCarouselHTML(getRequestValue('edit','get',false));			
		}
		
		$html .= '</div></div></div>';	
		return $html;
	}	
	
	private function deleteCarousel($id) {
		if ($id <> '') {
			return bsDatabase::deleteEntry($id,$this->PLUGIN_SELF_DIR."data/carousel.data.php");
		}else 
			return true;
	}
	
	private function getNewCarouselHTML($id) {
	    $name = '';
	    $img = array();
	    $content = array();
	    if ($id !== false) {
	        $Carousel = bsDatabase::LoadCarouselByID($this->PLUGIN_SELF_DIR."data/carousel.data.php",$id);
            if ($Carousel !== false) {
	            $name = $Carousel->Name;
	            foreach ($Carousel->Items as $item) {
	                $img[] = $item->Img;
	                $content[] = $item->Content;
	            }
	        }
	    }
	    if (count($img) == 0) {
	        $img[] = '';
	        $img[] = '';
	    }
		$html  = '';
		$html .= '<ul class="mo-ul"><li class="mo-li ui-widget-content ui-corner-all">';
		$html .= '<form name="bsNewCarousel" method="post" action="#bsNewCarousel">';
		$html .= '<div class="mo-li-head-tag mo-tag-height-from-icon mo-li-head-tag-no-ul mo-middle ui-state-default ui-corner-top">';
		$html .= '<span class="mo-bold">'.$this->lang_admin->getLanguageValue("config_Bootstrap_newName").'</span></div><input type="text" value="'.$name.'" maxlength="20" name="bsCarouselName"  class="form-control" />';
		$html .= '<ul class="mo-in-ul-ul" id="bsCarouselElements">';

		$element  = '<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix" id="bsCarouselItemXXX" data-elementid="XXX">';
		$element .= $this->lang_admin->getLanguageValue("config_Bootstrap_editCarouselImgName").'<br/><input type="text" value="XValueImgX" maxlength="200" name="bsCarouselItemImgXXX" size="50" class="form-control" />';
		$element .= $this->getImgSelectHTML('bsCarouselItemSelectImgXXX','bsCarouselItemImgXXX');
		$element .= '<br/>';
		$element .= $this->lang_admin->getLanguageValue("config_Bootstrap_editCarouselContentName").'<br/><textarea maxlength="1000" name="bsCarouselItemContentXXX" rows="5" cols="47" class="form-control">XValueContentX</textarea>';
		$element .= '<input type="hidden" name="bsToDeleteXXX" value="0" />';
		$element .= '<br/><div class="btn-group"><button name="bsDeleteSlide" type="button" data-destination="bsToDeleteXXX" class="btn btn-danger btn-xs" title="'.$this->lang_admin->getLanguageValue("config_Bootstrap_deleteSlide").'"><span class="glyphicon glyphicon-trash"></span></button>';
		$element .= '<button name="bsMoveSildeUp" type="button" class="btn btn-primary btn-xs" title="'.$this->lang_admin->getLanguageValue("config_Bootstrap_moveSlideUp").'"><span class="glyphicon glyphicon-chevron-up"></span></button>';
		$element .= '<button name="bsMoveSildeDown" type="button" class="btn btn-primary btn-xs" title="'.$this->lang_admin->getLanguageValue("config_Bootstrap_moveSlideDown").'"><span class="glyphicon glyphicon-chevron-down"></span></button></div>';		
		$element .= '</li>';		
		
		for ($i = 0; $i < count($img); $i++) {
		    $xElement = $element;
		    $xElement = str_replace('XValueImgX',$this->getCleanArrayEntry($img,$i),$xElement);
		    $xElement = str_replace('XValueContentX',$this->getCleanArrayEntry($content,$i),$xElement);
		    $xElement = str_replace('XXX',$i,$xElement);
		    
		    $html .= $xElement;		
		}
		$html .= '</ul>';
		$html .= '<input type="hidden" name="bsSlideMaxID" value="'.count($img).'" />';
		if ($id !== false) {
		    $html .= '<input type="hidden" name="bsid" value="'.$id.'" />';
		}
		$html .= '<div class="btn-group"><button name="bsAddSlide" type="button" title="'.$this->lang_admin->getLanguageValue("config_Bootstrap_addSlide").'"  class="btn btn-success btn-sm"><span class="glyphicon glyphicon-plus-sign"></span></button>';
		$html .= '<button name="bsSubmit" type="submit" title="'.$this->lang_admin->getLanguageValue("config_Bootstrap_save").'"  class="btn btn-default btn-sm"><span class="glyphicon glyphicon-ok-sign"></span>&emsp;'.$this->lang_admin->getLanguageValue("config_Bootstrap_save").'</button></div>';
		$html .= '</form>';

		$html .= '</li></ul>';		
		$newElement = $element;
	    $newElement = str_replace('XValueImgX','',$newElement);
	    $newElement = str_replace('XValueContentX','',$newElement);
        $html .= '<script>var newElement = \''.$newElement.'\';</script>';
		return $html;
	}
	
	private function getCleanArrayEntry($array, $key) {
	    if (array_key_exists($key,$array)) {
	        return $array[$key];
	    }else{
	        return '';
	    }
	}
	
	private function getShowCarouselHTML() {
	        $html = '';
			$html .= '<ul class="mo-ul"><li class="mo-li ui-widget-content ui-corner-all">';
			$html .= '<div class="mo-li-head-tag mo-tag-height-from-icon mo-li-head-tag-no-ul mo-middle ui-state-default ui-corner-top">';
			$html .= '<span class="mo-bold">'.$this->lang_admin->getLanguageValue("config_Bootstrap_AllCarousels").'</span></div>';
			$html .= '<ul class="mo-in-ul-ul" id="bsAllCarousels">';
			$Carousels = bsDatabase::loadArray($this->PLUGIN_SELF_DIR."data/carousel.data.php");
			if (is_array($Carousels)) {
				foreach ($Carousels as $Carousel) {
					$html .= '<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix">';
					$html .= $this->getCarouselAsHTML($Carousel);
					$html .= '<br/>';
					$html .= '<div class="btn-group">';
					$html .= '<button name="bsEdit" type="button" class="btn btn-primary btn-xs btn-carousel-function" title="'.$this->lang_admin->getLanguageValue("config_Bootstrap_edit").'" data-link="'.PLUGINADMIN_GET_URL.'&amp;actab='.self::TAB_NEW_CAROUSEL.'&amp;edit='.$Carousel->ID.'"><span class="glyphicon glyphicon-edit"></span></button>';
					$html .= '<button name="bsDelete" type="button" class="btn btn-danger btn-xs btn-carousel-function" title="'.$this->lang_admin->getLanguageValue("config_Bootstrap_delete").'" data-link="'.PLUGINADMIN_GET_URL.'&amp;actab='.self::TAB_SHOW_CAROUSEL.'&amp;delete='.$Carousel->ID.'"><span class="glyphicon glyphicon-trash"></span></button>';
					$html .= '</div></li>';
				};
			}else{
				$html .= '<li class="mo-in-ul-li mo-inline ui-widget-content ui-corner-all ui-helper-clearfix">';
				$html .= $this->lang_admin->getLanguageValue("config_Bootstrap_NoCarousels");
				$html .= '</li>';
			}			
			$html .= '</ul></li></ul>';
		
		return $html;
	}
	
	private function isActiveTabHTML($name) {
		$html = ' ui-tabs-selected ui-state-active';
		$activeTab = getRequestValue('actab',false,false);
		if ((!$activeTab) and ($name == self::TAB_SHOW_CAROUSEL))
			return $html;
		elseif ($activeTab == $name)
			return $html;
		else
			return '';
	}
	
	private function getCarouselAsHTML($Carousel) {
		global $specialchars;            
		return $Carousel->Name.'<br/>'. $this->lang_admin->getLanguageValue("config_Bootstrap_CountSlide",count($Carousel->Items)).'<br/>';
	}
	
	private function saveNewCarousel() {
		if ($this->saveCarouselToDatabase()) {
	        return $this->lang_admin->getLanguageValue("config_Bootstrap_CarouselCreateSuccessful");
	    }	 
	}
	
	private function saveCarousel($id) {
	    if ($this->saveCarouselToDatabase($id)) {
	        return $this->lang_admin->getLanguageValue("config_Bootstrap_CarouselSaveSuccessful");
	    }	    
	}
	
	private function saveCarouselToDatabase($id = '') {
	    $Carousel = new bsCarousel(getRequestValue('bsCarouselName','post',false),$id);
	    for ($i = 0; $i < getRequestValue('bsSlideMaxID','post',false); $i++) {
	        if (getRequestValue('bsCarouselItemImg'.$i,'post',false) !== false) {
	            $Carousel->Items[] = new bsCarouselItem(getRequestValue('bsCarouselItemImg'.$i,'post',false),getRequestValue('bsCarouselItemContent'.$i,'post',false));
	        }
	    }
	    if ($id <> '') {
	        $this->deleteCarousel($id);
	    }
	    return bsDatabase::appendArray($this->PLUGIN_SELF_DIR."data/carousel.data.php", $Carousel);	    
	}
	
	private function getImgSelectHTML($name,$nameDestination) {
	    $html  = '';
	    $html .= '<select name="'.$name.'" size="1" data-destination="'.$nameDestination.'" class="form-control input-sm">';
	    $html .= '<option value="">'.$this->lang_admin->getLanguageValue("config_Bootstrap_ImgSelection").'</option>';
	    $img = $this->getImgFileArray();
	    reset($img);
	    for ($i = 0; $i < Count($img); $i++) {	        
	        $html .= '<option value="'.key($img).'">'.$img[key($img)].'</option>';
	        next($img);
	    }
        $html .= '</select>';
        return  $html;	            
	}
	
	private function getImgFileArray() {
	    global $CatPage;
        global $specialchars;
        $files = array();
        $cats = $CatPage->get_CatArray(true,false);
        foreach ($cats as $cat) {
            $catName = $specialchars->rebuildSpecialChars($cat,false,false);
            $imgs = $CatPage->get_FileArray($cat,array(".png",".jpg",".jepg",".gif"));
            foreach ($imgs as $img) {
                $img = $specialchars->rebuildSpecialChars($img,false,false);
                $files[$catName.':'.$img] = $catName.'/'.$img;
            }
        }
        return $files;
	}
	
}

$Bootstrap_Carousel = new Bootstrap_Carousel($plugin);
return $Bootstrap_Carousel->output();
?>