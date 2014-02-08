<?php if(!defined('IS_CMS')) die();

/***************************************************************
*
* Plugin fuer moziloCMS, welches ein Schnittstelle für die einfach Verwendung von Bootstrap bereit stellt
* by black-night - Daniel Neef
* 
***************************************************************/
require_once(PLUGIN_DIR_REL."Bootstrap/classes.php");
class Bootstrap extends Plugin {
	
	const TYP_MEDIA = "media";
	const TYP_PANEL = "panel"; 
	const TYP_WELL  = "well";
	const TYP_LABEL = "label";
	const TYP_BUTTON= "button";
	const TYP_TAB   = "tab";
	const TYP_COLLAPS = "collaps";
	const TYP_BADGE = "badge";
	const TYP_GRID  = "grid";
	const TYP_NAVBAR  = "navbar";
	const TYP_JUMPOTRON = "jumbotron";
	const TYP_GLYPHICON = "glyphicon";
	const TYP_CAROUSEL = "carousel";
	
	protected $lang_admin;
	private $lang_cms;	
	
    /***************************************************************
    * 
    * Gibt den HTML-Code zurueck, mit dem die Plugin-Variable ersetzt 
    * wird.
    * 
    ***************************************************************/
    function getContent($value) {
        $values = explode("|", $value);

        global $syntax;
        $syntax->insert_jquery_in_head('jquery');
        $this->insert_in_head_on_top($this->getHead());
        
        $result = '';
        
        if (count($values) > 0) {
        	switch (strtolower($values[0])) {
        		case self::TYP_MEDIA:
        			$result .= $this->typMedia($values);
        			break;
        		case self::TYP_PANEL:
        			$result .= $this->typPanel($values);
        			break;
        		case self::TYP_WELL:
        			$result .= $this->typWell($values);
        			break;
        		case self::TYP_LABEL:
        			$result .= $this->typLabel($values);
        			break;
        		case self::TYP_BUTTON:
        			$result .= $this->typButton($values);
        			break;
        		case self::TYP_TAB:
        			$result .= $this->typTab($values);
        			break;
        		case self::TYP_COLLAPS:
        			$result .= $this->typCollaps($values);
        			break;
        		case self::TYP_BADGE:
        			$result .= $this->typBadge($values);
        			break;
        		case self::TYP_GRID:
        			$result .= $this->typGrid($values);
        			break;
    			case self::TYP_NAVBAR:
    			    $result .= $this->typNavbar();
    			    break;
			    case self::TYP_JUMPOTRON:
			        $result .= $this->typJumbotron($values);
			        break;
		        case self::TYP_GLYPHICON:
		            $result .= $this->typGlyphicon($values);
		            break;
	            case self::TYP_CAROUSEL:
	                $result .= $this->typCarousel($values);
	                break;
        	}
        }
        		
        return $result;
    } // function getContent
    
    
    
    /***************************************************************
    * 
    * Gibt die Konfigurationsoptionen als Array zurueck.
    * 
    ***************************************************************/
    function getConfig() {
        global $ADMIN_CONF;        
       
        $config = array();
        $config["--admin~~"] = array(
                "buttontext" => $this->lang_admin->getLanguageValue("config_Bootstrap_carouselBtnText"),
                "description" => $this->lang_admin->getLanguageValue("config_Bootstrap_carouselBtn"),
                "datei_admin" => "adminCarousel.php"
        );        
        return $config;            
    } // function getConfig
    
    
    
    /***************************************************************
    * 
    * Gibt die Plugin-Infos als Array zurueck. 
    * 
    ***************************************************************/
    function getInfo() {
        global $ADMIN_CONF;        
        $dir = $this->PLUGIN_SELF_DIR;
        $language = $ADMIN_CONF->get("language");
        $this->lang_admin = new Language($dir."sprachen/admin_language_".$language.".txt");
        $info = array(
            // Plugin-Name
            "<b>".$this->lang_admin->getLanguageValue("config_Bootstrap_plugin_name")."</b> \$Revision: 1 $",
            // CMS-Version
            "2.0",
            // Kurzbeschreibung
            $this->lang_admin->getLanguageValue("config_Bootstrap_plugin_desc"),
            // Name des Autors
           "black-night",
            // Download-URL
            array("http://software.black-night.org","Software by black-night"),
            # Platzhalter => Kurzbeschreibung
            array('{Bootstrap|Badge|Zahl}' => $this->lang_admin->getLanguageValue("config_Bootstrap_badge"),
            	  '{Bootstrap|Button|Text|Type|Größe}' => $this->lang_admin->getLanguageValue("config_Bootstrap_button"),
            	  '{Bootstrap|Collaps|Titel|Text}' => $this->lang_admin->getLanguageValue("config_Bootstrap_collaps"),
            	  '{Bootstrap|Grid|Clear}' => $this->lang_admin->getLanguageValue("config_Bootstrap_gridclear"),
            	  '{Bootstrap|Grid|Spalte|Offset|Inhalt}' => $this->lang_admin->getLanguageValue("config_Bootstrap_grid"),
            	  '{Bootstrap|Label|Text|Type}' => $this->lang_admin->getLanguageValue("config_Bootstrap_label"),
            	  '{Bootstrap|Media|Bild|Titel|Text}' => $this->lang_admin->getLanguageValue("config_Bootstrap_media"),
            	  '{Bootstrap|Panel|Titel|Text|Fuß|Type}' => $this->lang_admin->getLanguageValue("config_Bootstrap_panel"),
            	  '{Bootstrap|Tab|Titel|Text}' => $this->lang_admin->getLanguageValue("config_Bootstrap_tab"),
            	  '{Bootstrap|Well|Text|Type}' => $this->lang_admin->getLanguageValue("config_Bootstrap_well"),
                  '{Bootstrap|Jumbotron|Text}' => $this->lang_admin->getLanguageValue("config_Bootstrap_jumbotron"),
                  '{Bootstrap|Glyphicon|Typ}' => $this->lang_admin->getLanguageValue("config_Bootstrap_glyphicon"),
                  '{Bootstrap|Carousel|Name}' => $this->lang_admin->getLanguageValue("config_Bootstrap_carousel")
                 )
            );
            return $info;        
    } // function getInfo
    
    /***************************************************************
    *
    * Interne Funktionen
    *
    ***************************************************************/
        
    protected function getHead() {
    	$head = '<style type="text/css"> @import "'.URL_BASE.PLUGIN_DIR_NAME.'/Bootstrap/css/bootstrap.min.css"; </style>'
     			.'<script type="text/javascript" src="'.URL_BASE.PLUGIN_DIR_NAME.'/Bootstrap/js/bootstrap.min.js"></script>'
    			;
		return $head;
    } //function getHead   

    private function typMedia($values) {
    	if (count($values) == 4) {
    		$result  = '<div class="media"><a class="pull-left">';
			$result .= '<img class="media-object" src="'.$this->getFileUrl($values[1]).'" alt=""></a>';
			$result .= '<div class="media-body"><h4 class="media-heading">'.$values[2].'</h4>'.$values[3].'</div>';
    		$result .= '</div>';
    		return $result;
    	}else {
    		return false;
    	}
    }
    
    private function typPanel($values) {
    	if (count($values) >= 4) {
    		if (count($values) == 5) 
    			$PanelTyp = $values[4];
    		else 
    			$PanelTyp = '';
    		$result  = '<div class="panel '.$this->getPanelTyp($PanelTyp).'">';
    		if (strlen($values[1])>0) 
    			$result .= '<div class="panel-heading panel-title">'.$values[1].'</div>';
    		if (strlen($values[2])>0)
    			$result .= '<div class="panel-body">'.$values[2].'</div>';
    		if (strlen($values[3])>0)    		
    			$result .= '<div class="panel-footer">'.$values[3].'</div>';
    		$result .= '</div>';
    		return $result;
    	}else{
    		return false;
    	}
    }
    
    private function typWell($values) {
    	if (count($values) >= 2) {
    		if (count($values) == 3)
    			$WellTyp = $values[2];
    		else
    			$WellTyp = '';
    		$result  = '<div class="well '.$this->getWellTyp($WellTyp).'">'.$values[1].'</div>';
    		return $result;
    	}else{
    		return false;
    	}    	
    }
    
    private function typLabel($values) {
    	if (count($values) >= 2) {
    		if (count($values) == 3)
    			$LabelTyp = $values[2];
    		else
    			$LabelTyp = '';
    		$result  = '<span class="label '.$this->getLabelTyp($LabelTyp).'">'.$values[1].'</span>';
    		return $result;
    	}else{
    		return false;
    	}
    }
    
    private function typBadge($values) {
    	if (count($values) == 2) {
    		$result  = '<span class="badge">'.$values[1].'</span>';
    		return $result;
    	}else{
    		return false;
    	}
    }    
    
    private function typButton($values) {
    	if (count($values) >= 2) {
    		if (count($values) >= 3)
    			$ButtonTyp = $values[2];
    		else
    			$ButtonTyp = '';
    		if (count($values) == 4)
    			$ButtonSize = $values[3];
    		else
    			$ButtonSize = '';    		
    		$result  = '<button type="button" class="btn '.$this->getButtonTyp($ButtonTyp).' '.$this->getButtonSize($ButtonSize).'">'.$values[1].'</button>';
    		return $result;
    	}else{
    		return false;
    	}
    }
    
    private function typTab($values) {
    	if (count($values) >= 3) {
    		$tabs = array();
    		$offset = rand();
    		for ($i = 2; $i < count($values); $i++) {
    			$tabs[$values[$i-1]] = $values[$i];
    			$i++;
    		}    	
    		$nav  = '<ul class="nav nav-tabs">';
    		$content  = '<div class="tab-content">';    		
    		$nav .= '<li class="active"><a href="#'.md5(key($tabs).$offset).'" data-toggle="tab">'.key($tabs).'</a></li>';
    		$content .= '<div id="'.md5(key($tabs).$offset).'" class="tab-pane fade in active">'.current($tabs).'</div>';
    		next($tabs);
    		while (current($tabs) !== false) {
    			$nav .= '<li><a href="#'.md5(key($tabs).$offset).'" data-toggle="tab">'.key($tabs).'</a></li>';
    			$content .= '<div id="'.md5(key($tabs).$offset).'" class="tab-pane fade">'.current($tabs).'</div>';
    			next($tabs);
    		}
    		$nav .= '</ul>';
    		$content .= '</div>';
    		
    		$result = $nav.$content;
    		return $result;
    	}else{
    		return false;
    	}
    } 

    private function typCollaps($values) {
    	if (count($values) >= 3) {
    		$tabs = array();
    		$offset = rand();
    		for ($i = 2; $i < count($values); $i++) {
    			$tabs[$values[$i-1]] = $values[$i];
    			$i++;
    		}
    		$result  = '<div class="panel-group" id="'.md5('collaps'.$offset).'"><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title">'
    			   	   .'<a class="accordion-toggle" data-toggle="collapse" data-parent="#'.md5('collaps'.$offset).'" href="#'.md5(key($tabs).$offset).'">'
    			       .key($tabs).'</a></h4></div>'
    			       .'<div id="'.md5(key($tabs).$offset).'" class="panel-collapse collapse in"><div class="panel-body">'.current($tabs).'</div></div></div>';
    		next($tabs);
    		while (current($tabs) !== false) {
	    		$result .= '<div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title">'
	    			   	   .'<a class="accordion-toggle" data-toggle="collapse" data-parent="#'.md5('collaps'.$offset).'" href="#'.md5(key($tabs).$offset).'">'
	    			       .key($tabs).'</a></h4></div>'
	    			       .'<div id="'.md5(key($tabs).$offset).'" class="panel-collapse collapse"><div class="panel-body">'.current($tabs).'</div></div></div>';
    			next($tabs);
    		}
    		$result .= '</div>';
    		return $result;
    	}else{
    		return false;
    	}
    }

    private function typGrid($values) {
    	if (count($values) == 2) { // Grid|Clear
    		$options = explode(",", $values[1]);
    		$result  = '<div class="clearfix '.$this->getGridVisibleClasses($options).'"></div>';    		
    		return $result;
    	}elseif (count($values) == 3) { // Grid|Spalten|Inhalt
    		$options = explode(",", $values[1]);
			$result  = '<div class="'.$this->getGridClasses($options).'">';
			$result .= $values[2];
    		$result .= '</div>';
    		return $result;
    	}elseif (count($values) == 4) { // Grid|Spalten|Offset|Inhalt
    		$options = explode(",", $values[1]);
    		$optionsOffset = explode(",", $values[2]);
    		$result  = '<div class="'.$this->getGridClasses($options).' '.$this->getGridOffsetClasses($optionsOffset).'">';
    		$result .= $values[3];
    		$result .= '</div>';
    		return $result;
    	}else{
    		return false;
    	}
    }    
    
    private function getGridVisibleClasses($options) {
    	$result = '';
    	foreach ($options as $option) {
			$result .= $this->getGridVisibleTyp($option).' ';
    	}
    	return trim($result);
    }

    private function getGridOffsetClasses($options) {
    	$result = '';
    	foreach ($options as $option) {
    		$col = explode("=",$option);
    		if (count($col) == 2)
    			$result .= $this->getGridOffsetTyp($col[0]).$col[1].' ';
    	}
    	return trim($result);
    }    
    
    private function getGridClasses($options) {
    	$result = '';
    	foreach ($options as $option) {
    		$col = explode("=",$option);
    		if (count($col) == 2) 
    			$result .= $this->getGridColTyp($col[0]).$col[1].' ';
    	}
    	return trim($result);
    }
    
    private function getGridColTyp($typ) {
    	switch (strtolower($typ)) {
    		case 'xs'  :  return  'col-xs-';
    		case 's'   :
    		case 'sm'  :  return  'col-sm-';
    		case 'l'   :
    		case 'lg'  :  return  'col-lg-';
    		default       :  return  'col-md-';
    	}
    }
    
    private function getGridOffsetTyp($typ) {
    	return $this->getGridColTyp($typ).'offset-';
    }    
    
    private function getGridVisibleTyp($typ) {
    	switch (strtolower($typ)) {
    		case 'xs'  :  return  'visible-xs';
    		case 's'   :
    		case 'sm'  :  return  'visible-sm';
    		case 'm'   :
    		case 'md'  :  return  'visible-md';
    		case 'l'   :
    		case 'lg'  :  return  'visible-lg';
    		default       :  return  '';
    	}    	
    }
    
    private function getFileUrl($value) {
    	global $syntax;
    	// Bei externen Bildern: $value NICHT nach ":" aufsplitten!
    	if (preg_match($syntax->LINK_REGEX, $value)) {
    		$url = $value;
    	}
    	// Ansonsten: Nach ":" aufsplitten
    	else {
    		global $CatPage;
    		global $CMS_CONF;
    	
    		list($cat,$datei) = $CatPage->split_CatPage_fromSyntax($value,true);
    	
    		if(!$CatPage->exists_File($cat,$datei)) { 
    			$url = '';
    		}else{
    			$url = $CatPage->get_srcFile($cat,$datei);
    		}
    	}   
    	return $url; 	
    }
    
    private function typNavbar() {
        global $CatPage;
        $result = '<ul class="nav navbar-nav">';
        
        // Kategorienverzeichnis einlesen
        $CategoriesArray = $CatPage->get_CatArray();
        $CountCategoriesIndex = count($CategoriesArray)-1;
        for ($i = 0; $i <= $CountCategoriesIndex; $i++) {
            // Seitenverzeichnis einlesen
            $PageArray = $CatPage->get_PageArray($CategoriesArray[$i]);
            $CountPageIndex = count($PageArray)-1;
            $result .= "<li".$this->getNavbarLiClass(($CountPageIndex >= 0),$CatPage->is_Activ($CategoriesArray[$i],false)).">";
            if (($CatPage->get_Type($CategoriesArray[$i],false) == 'cat') and ($CountPageIndex > 1)) {
                $result .= '<a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$CatPage->get_HrefText($CategoriesArray[$i],false).'<b class="caret"></b></a>';
            }else{
                $result .= "<a href=\"".$CatPage->get_Href($CategoriesArray[$i],false)."\" target=\"".$CatPage->get_HrefTarget($CategoriesArray[$i],false)."\">".$CatPage->get_HrefText($CategoriesArray[$i],false)."</a>";
            }
            if ($CountPageIndex >= 0) $result .= '<ul class="dropdown-menu">';
            for ($j = 0; $j <= $CountPageIndex; $j++) {
                $pageType = $CatPage->get_Type($CategoriesArray[$i],$PageArray[$j]);
                if (($pageType == '.txt.php') or ($pageType == '.lnk.php')) {
                    $result .= "<li".$this->getNavbarLiClass(false,$CatPage->is_Activ($CategoriesArray[$i],$PageArray[$j])).">";
                    if ($pageType == '.txt.php') {
                        $result .= "<a href=\"".$CatPage->get_Href($CategoriesArray[$i],$PageArray[$j])."\">".$CatPage->get_HrefText($CategoriesArray[$i],$PageArray[$j])."</a>";
                    }elseif ($pageType == '.lnk.php') {
                        $result .= "<a href=\"".$CatPage->get_Href($CategoriesArray[$i],$PageArray[$j])."\" target=\"".$CatPage->get_HrefTarget($CategoriesArray[$i],$PageArray[$j])."\">".$CatPage->get_HrefText($CategoriesArray[$i],$PageArray[$j])."</a>";
                    }
                    $result .= "</li>";
                }
            }
            if ($CountPageIndex >= 0) $result .= "</ul>";
            $result .= "</li>";
        }
        $result .= "</ul>";
        return $result;        
    }
    
    private function getNavbarLiClass($HasSub,$IsActiv) {
        $result = "";
        if ($IsActiv == true) {
            $result .= " active";
        }
        if ($HasSub == true) {
            $result .= " dropdown";
        }
        $result = trim($result);
        if (strlen($result) > 0) {
            return " class=\"".$result."\"";
        }else{
            return "";
        }
    }
    
    private function typJumbotron($values) {
        if (count($values) == 2) {
            global $syntax;
            $result = '<div class="jumbotron"><div class="container">'.$values[1].'</div></div>';
            $syntax->content = str_replace("<!-- JumbotronPH -->",$result,$syntax->content);
            return false;
        }else
            return false;
    }
    
    private function typGlyphicon($values) {
        if (count($values) == 2) {
            $result = '<span class="glyphicon '.$values[1].'"></span>';
            return $result;
        }else
            return false;
    }
    
    private function typCarousel($values) {
        if (count($values) == 2) {
            global $CatPage;
            $Carousel = bsDatabase::LoadCarousel($this->PLUGIN_SELF_DIR."data/carousel.data.php",$values[1]);
            if ($Carousel !== False) {
                $CarouselID = str_replace('}','',str_replace('{','',$Carousel->ID));
                $result = '<div data-ride="carousel" class="carousel slide" id="Carousel'.$CarouselID.'">';
                $result .= '<ol class="carousel-indicators">';
                $i = 0;
                foreach ($Carousel->Items as $Item) {
                    $result .= '<li data-slide-to="'.$i.'" data-target="#Carousel'.$CarouselID.'"';
                    if ($i == 0) {
                        $result .= ' class="active"';
                    }
                    $result .= '></li>';
                    $i = $i + 1;
                }
                $result .= '</ol><div class="carousel-inner">';
                $i = 0;
                foreach ($Carousel->Items as $Item) {
                    $result .= ' <div class="item';
                    if ($i == 0) {
                        $result .= ' active';
                    }
                    $result .= '">';
                    $i = $i + 1;                    
                    $file = $CatPage->split_CatPage_fromSyntax($Item->Img,true);
                    $result .= '<img src="'.$CatPage->get_srcFile($file[0],$file[1]).'" alt="Slide '.$i.'"   />';
                    $result .= '<div class="carousel-caption">'.$Item->Content.'</div>';
                    $result .= ' </div>';
                }                
                $result .= '</div><a class="left carousel-control" href="#Carousel'.$CarouselID.'" data-slide="prev"><span class="glyphicon glyphicon-chevron-left"></span></a>';
                $result .= '<a class="right carousel-control" href="#Carousel'.$CarouselID.'" data-slide="next"><span class="glyphicon glyphicon-chevron-right"></span></a></div>';
                return  $result;
            }else 
                return false;
        }else
            return false;
    }
    
    private function insert_in_head_on_top($data) {
    	global $syntax;
        if(strpos($syntax->content,"<!-- bootstrap -->") === false) {
            $syntax->content = str_replace(array("<head>","<HEAD>"),"<head>\n<!-- bootstrap -->\n".$data,$syntax->content);
        }
    }
    
    private function getGeneralTyp($typ) {
    	switch (strtolower($typ)) {
    		case 'primär' :
    		case 'primary':  return  'primary';
    		case 'erfolg' :
    		case 'success':  return  'success';
    		case 'info'   :  return  'info';
    		case 'warnung':
    		case 'warning':  return  'warning';
    		case 'risiko' :
    		case 'danger' :  return  'danger';
    		default       :  return  'default';
    	}    	
    }
    
    private function getPanelTyp($typ) {
    	return 'panel-'.$this->getGeneralTyp($typ);
    }

    private function getLabelTyp($typ) {
    	return 'label-'.$this->getGeneralTyp($typ);
    }

    private function getButtonTyp($typ) {
    	return 'btn-'.$this->getGeneralTyp($typ);
    }    

    private function getButtonSize($size) {
        switch (strtolower($size)) {
        	case 's'            :
    		case 'small'        :  return  'btn-sm';
    		case 'l'			:
    		case 'large'        :  return  'btn-lg';
    		case 'xs'           :
    		case 'extra small'  :  return  'btn-xs';    		
    		default             :  return  '';
    	}    
    }
        
    private function getWellTyp($typ) {
    	switch (strtolower($typ)) {
    		case 's'      :
    		case 'small'  :  return  'well-sm';
    		case 'l'      :
    		case 'large'  :  return  'well-lg';
    		default       :  return  '';
    	}    	
    }
    
} // class Bootstrap
?>