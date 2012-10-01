<?php
class LoginPage extends Page {
    
    public function setArguments($args) {
        $this->template = new PHPTAL("templates/login.xhtml");
        $this->kotobaDocumentRoot = "/kotoba-zzz/kotoba-ib"; // TODO Get it from config
        $this->currentStylesheet = $args["stylesheet"];
        $this->title = $args["title"];
        $this->version = Config::version;
        
        // more vars here
    }
}
?>
