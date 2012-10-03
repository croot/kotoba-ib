<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

namespace ru\azuchan\kotoba\pages;

class LoginPage extends Page {
    
    public function __construct($title) {
        $this->template = new \PHPTAL("templates/login.xhtml");
        $this->title = $title;
    }
    
    public function render() {
        return parent::render();
    }
}
?>
