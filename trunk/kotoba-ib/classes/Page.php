<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

abstract class Page {
    protected $template = NULL;
    
    private $kotobaDocumentRoot = Config::DIR_PATH;
    private $currentStylesheet = "kusaba.css"; // TODO Get it from user settings
    private $version = Config::version;
    
    protected $title;
    
    public function render() {
        $this->template->kotobaDocumentRoot = $this->kotobaDocumentRoot;
        $this->template->currentStylesheet = $this->currentStylesheet;
        $this->template->version = $this->version;
        
        $this->template->title = $this->title;
                
        return $this->template->execute();
    }
}
?>
