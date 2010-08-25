<?php
/* ***********************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * TeX works routines. Tank you http://www.linuxjournal.com/article/7870
 */

/**
 * Creates a valid tex document.
 * @param string $code TeX code.
 */
function wrap($code) {
    return <<<EOS
\documentclass[10pt]{article}

% add additional packages here
\usepackage{amsmath}
\usepackage{amsfonts}
\usepackage{amssymb}

\pagestyle{empty}
\begin{document}
$code
\end{document}
EOS;
}
/**
 * not doen
 */
function transform($text) {
    preg_match_all("/\[tex\](.*?)\[\/tex\]/si", $text, $matches);

    for ($i = 0; $i < count($matches[0]); $i++) {
        $position = strpos($text, $matches[0][$i]);
        $thunk = $matches[1][$i];
        $hash = md5($thunk);
        $full_name = $this->CACHE_DIR . "/" . $hash . ".png";
        $url = $this->URL_PATH . "/" . $hash . ".png";
        if (!is_file($full_name)) {
            $this->render_latex($thunk, $hash);
            $this->cleanup($hash);
        }
        $text = substr_replace($text, "<img src=\"$url\" alt=\"Formula: $i\" />", $position, strlen($matches[0][$i]));
    }

    return $text;
}
/**
 * not done
 */
function render_latex($code, $hash) {
    $text = $this->wrap($code);

    $current_dir = getcwd();
    chdir($this->TMP_DIR);

    // create temporary LaTeX file
    $fp = fopen($this->TMP_DIR . "/$hash.tex", "w+");
    fputs($fp, $text);
    fclose($fp);

    // run LaTeX to create temporary DVI file
    $command = $this->LATEX_PATH . " --interaction=nonstopmode " . $hash . ".tex";
    exec($command);

    // run dvips to create temporary PS file
    $command = $this->DVIPS_PATH . " -E $hash" . ".dvi -o " . "$hash.ps";
    exec($command);

    // run PS file through ImageMagick to
    // create PNG file
    $command = $this->CONVERT_PATH . " -density 120 $hash.ps $hash.png";
    exec($command);

    // copy the file to the cache directory
    copy("$hash.png", $this->CACHE_DIR . "/$hash.png");

    chdir($current_dir);
}
/**
 * not doen
 */
function cleanup($hash) {
    $current_dir = getcwd();
    chdir($this->TMP_DIR);

    unlink($this->TMP_DIR . "/$hash.tex");
    unlink($this->TMP_DIR . "/$hash.aux");
    unlink($this->TMP_DIR . "/$hash.log");
    unlink($this->TMP_DIR . "/$hash.dvi");
    unlink($this->TMP_DIR . "/$hash.ps");
    unlink($this->TMP_DIR . "/$hash.png");

    chdir($current_dir);
}
?>
