<?php
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
 * Find TeX code in text, transform it to images and put images to text.
 * @param string $text Text.
 * @return string Transformed text.
 */
function transform($text) {
    preg_match_all("/\[tex\](.*?)\[\/tex\]/si", $text, $matches);

    for ($i = 0; $i < count($matches[0]); $i++) {
        $position = strpos($text, $matches[0][$i]);
        $code = $matches[1][$i];
        $hash = md5(preg_replace('/\s/', '', $code));
        $full_name = Config::ABS_PATH . "/latexcache/$hash.png";
        $url = Config::DIR_PATH . "/latexcache/$hash.png";
        if (!is_file($full_name)) {
            render_latex($code, $hash);
            cleanup($hash);
        }
        $text = substr_replace($text, "<img src=\"$url\" alt=\"Formula: $i\" />", $position, strlen($matches[0][$i]));
    }

    return $text;
}
/**
 * Render TeX code and save png image.
 * @param string $code TeX code.
 * @param string $hash Code hash.
 */
function render_latex($code, $hash) {
    $text = wrap($code);

    $current_dir = getcwd();
    chdir(Config::TMP_PATH);

    // Create temporary LaTeX file.
    $fp = fopen(Config::TMP_PATH . "/$hash.tex", "w+");
    fputs($fp, $text);
    fclose($fp);

    // Run LaTeX to create temporary DVI file.
    $command = Config::LATEX_BINARY . " --interaction=nonstopmode $hash.tex";
    exec($command);

    // Run dvips to create temporary PS file.
    $command = Config::DVIPS_BINARY . " -E $hash.dvi -o $hash.ps";
    exec($command);

    // Run PS file through ImageMagick to create PNG file.
    $command = Config::CONVERT_BINARY . " -density 120 $hash.ps $hash.png";
    exec($command);

    // Copy the file to the cache directory.
    copy("$hash.png", Config::ABS_PATH . "/latexcache/$hash.png");

    chdir($current_dir);
    return 0;
}
/**
 * Removes temporary files after render TeX code with specifed hash.
 * @param string $hash Code hash.
 */
function cleanup($hash) {
    $current_dir = getcwd();
    chdir(Config::TMP_PATH);

    unlink(Config::TMP_PATH . "/$hash.tex");
    unlink(Config::TMP_PATH . "/$hash.aux");
    unlink(Config::TMP_PATH . "/$hash.log");
    unlink(Config::TMP_PATH . "/$hash.dvi");
    unlink(Config::TMP_PATH . "/$hash.ps");
    unlink(Config::TMP_PATH . "/$hash.png");

    chdir($current_dir);
}
?>
