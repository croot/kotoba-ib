<?php
require_once($_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/smarty/Smarty.class.php');

class SmartyKotobaSetup extends Smarty
{
	function SmartyKotobaSetup()
	{
		$this->Smarty();

        $this->template_dir = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/smarty/kotoba/templates/';
		$this->compile_dir = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/smarty/kotoba/templates_c/';
		$this->config_dir = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/smarty/kotoba/config/';
		$this->cache_dir = $_SERVER['DOCUMENT_ROOT'] . KOTOBA_DIR_PATH . '/smarty/kotoba/cache/';
        //$this->caching = true;
    }
}
?>
