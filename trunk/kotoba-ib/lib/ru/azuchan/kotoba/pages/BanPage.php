<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

namespace ru\azuchan\kotoba\pages;

class BanPage extends Page {
    private $ip;
    private $reason;
    
    public function __construct($title, $ip, $reson) {
        $this->template = new \PHPTAL("templates/ban.xhtml");
        $this->title = $title;
        $this->ip = $ip;
        $this->reson = $reson;
    }
    
    public function render() {
        $this->template->ip = $this->ip;
        $this->template->reason = $this->reason;
        
        return parent::render();
    }
}
?>
