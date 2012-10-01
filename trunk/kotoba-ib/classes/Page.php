<?php
abstract class Page {
    protected $template = NULL;
    protected $kotobaDocumentRoot;
    protected $currentStylesheet;
    protected $title;
    protected $version;
    
    public function render() {
        $this->template->kotobaDocumentRoot = $this->kotobaDocumentRoot;
        $this->template->currentStylesheet = $this->currentStylesheet;
        $this->template->title = $this->title;
        $this->template->version = $this->version;
                
        return $this->template->execute();
    }
}
?>
