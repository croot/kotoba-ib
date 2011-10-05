<?php
/* *******************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/

/**
 * Kotoba mark script.
 * @package api
 */

/**
 * Размечает текст.
 * Основные правила разметки можно найти в файле http://coyc.net/wakaba_mark.htm
 * Дополнения и техническое описание находится в файле /res/Kotoba mark 1.txt
 * @param text string <p>Текст для разметки.</p>
 * @param board string <p>Доска.</p>
 * @param simple boolean <p>Текст не является параграфом.</p>
 * @return string
 * Возвращает изменённую строку.
 */
// TODO: запилить функцию clean_string
function wakaba_mark($text, $board, $simple = false) {
    $res = '';
    $lines = preg_split('/(?:\r\n|\n|\r)/', trim($text, "\r\n"));

    while (isset($lines[0])) {
        // skip empty lines
        if (preg_match('/^\s*$/', $lines[0])) {
            array_shift($lines);
        // format lists
        } elseif (preg_match('/^(1\.|\*) /', $lines[0], $match)) {
            if ($match[1] == '1.') {
                $tag = 'ol';
                $re = '[0-9]+\.';
                $skip = true;
            } else {
                $tag = 'ul';
                $re = '\*';
                $skip = false;
            }
            $html = '';
            while (isset($lines[0]) && preg_match("/^($re)(?: |\t)(.*)/", $lines[0], $match))
            {
                $spaces = strlen($match[1]) + 1;
                $item = $match[2] . "\n";
                array_shift($lines);

                while (isset($lines[0]) && preg_match("/^(?: {1,$spaces}|\t)(.*)/", $lines[0], $match))
                {
                    $item .= $match[1] . "\n";
                    array_shift($lines);
                }
                $html .= '<li>' . wakaba_mark($item, $board, true) . '</li>';

                if ($skip) {
                    while (isset($lines[0]) && preg_match('/^\s*$/', $lines[0])) {
                        array_shift($lines);
                    }
                }
            }
            $res .= "<$tag>$html</$tag>";
        // format code blocks
        } elseif (preg_match('/^(?:    |\t)/', $lines[0])) {
            $code = array();
            while (isset($lines[0]) && preg_match('/^(?:    |\t)(.*)/', $lines[0], $match))
            {
                $code[] = $match[1];
                array_shift($lines);
            }
            $res .= '<pre><code>' . implode('<br />', $code) . '</code></pre>';
        // quote blocks
        } elseif (preg_match('/^&gt;/', $lines[0])) {
            $quote = array();
            while (isset($lines[0]) && preg_match('/^(&gt;.*)/', $lines[0], $match))
            {
                $quote[] = $match[1];
                array_shift($lines);
            }
            $res .= '<blockquote class="unkfunc">' . wakaba_spanmark($quote, $board) . '</blockquote>';
        // all other
        } else {
            $text = array();
            while (isset($lines[0]) && preg_match('/^(?:\s*$|1\. |\* |&gt;|    |\t)/', $lines[0]) === 0) {
                $text[] = array_shift($lines);

            }
            if (!isset($lines[0]) && $simple) {
                $res .= wakaba_spanmark($text, $board);
            } else {
                $res .= '<p>' . wakaba_spanmark($text, $board) . '</p>';
            }
        }
        $simple = false;
    }

    return $res;
}

function wakaba_spanmark($lines, $board) {
    foreach ($lines as $key => $line) {
        $hidden = array();
        // the anal regexps straight ahead
        // keep your ass prepared :3
        // TODO: rm this shiny comment

        // `code`
        $line = preg_replace(
                '/(`+)([^<>]+?)\1/e',
                '"<--".(array_push($hidden, "<code>\\2</code>")-1)."-->"',
                $line);

        // url:
        $line = preg_replace(
                '#((https?|irc|ftp|mailto)://|www\.)(([^\s^$`[\"|<>{}])+)(?=[.,!?)\'*_%]*(\)\s|\)$|\s|$))#Usei',
                '"<--".(array_push($hidden, "<a href=\"\\1\\3\" rel=\"nofollow\">\\1\\3</a>")-1)."-->"',
                $line);

        // google:
        $line = preg_replace(
                '#(google://)(([^\s^$`[\"\|<>{}])+)(?=[.,!?)\'*_%]*(\)\s|\)$|\s|$))#Usei',
                '"<--".(array_push($hidden, "<a href=\"http://www.google.ru/search?q=\\2\" rel=\"nofollow\">google: ".str_replace("+"," ","\\2")."</a>")-1)."-->"',
                $line);

        // wiki:
        $line = preg_replace(
                '#(wiki://)(([^\s^$`[\"|<>{}])+)(?=[.,!?)\'*_%]*(\)\s|\)$|\s|$))#Usei',
                '"<--".(array_push($hidden, "<a href=\"http://en.wikipedia.org/wiki/".str_replace("+","%20","\\2")."\" rel=\"nofollow\">wiki: ".str_replace("+"," ","\\2")."</a>")-1)."-->"',
                $line);

        // (*__|_**) strong em
        $line = preg_replace(
                '/(\*__) ([^<>]+?) (__\*)/x',
                '<strong><em>\\2</em></strong>',
                $line);
        $line = preg_replace(
                '/(_\*\*) ([^<>]+?) (\*\*_)/x',
                '<strong><em>\\2</em></strong>',
                $line);

        // ** __ strong
        $line = preg_replace(
                '/(\*\*|__) ([^<>]+?) \1/x',
                '<strong>\\2</strong>',
                $line);

        // * _ em
        $line = preg_replace(
                '/(\*|_) ([^<>]+?) \1/x',
                '<em>\\2</em>',
                $line);

        // %%spoiler
        $line = preg_replace(
                '/(%%) ([^<>]+?) \1/x',
                '<span class="spoiler">\\2</span>',
                $line);

        // -strike
        $line = preg_replace(
                '/(?<![\*_-]) (-) (?![<>\s\*_-]) ([^<>]+?) (?<![<>\s\*_-]) \1 (?![\*_-])/x',
                '<del>\\2</del>',
                $line);

        // ^H
        $line = preg_replace(
                "/((?:&#?[0-9a-zA-Z]+;|[^&<>])(?<!\^H)(?R)?\^H)/eu",
                '"<del>".mb_substr("\\1",0,(mb_strlen("\\1"))/3)."</del>"',
                $line);

        // ref url
        $line = preg_replace(
                "/(&gt;&gt;(\d+))/e",
                'build_url("\\2", "'.$board.'")',
                $line);
        $line = preg_replace(
                "#(&gt;&gt;&gt;/(\w+?)/(\d+))#e",
                'build_url("\\2","\\3")',
                $line);
//        if (preg_match('/(?<=\s|^)&gt;&gt;(\d+)(?=\s|$)/', $line, $matches) == 1) {
//            if (!$is_posts_data_recived) {
//                $is_posts_data_recived = true;
//                $posts_data = posts_get_all_numbers();
//            }
//            $found = false;
//            foreach ($posts_data as $post_data)
//                if ($post_data['board'] == $board['name']
//                        && $post_data['post'] == $matches[1]) {
//                    $found = true;
//                    $output .= "link:$link_num";
//                    $links[$link_num++] = "<a class=\"ref|{$post_data['board']}|{$post_data['thread']}|{$post_data['post']}\" href=\"" . Config::DIR_PATH . "/{$post_data['board']}/{$post_data['thread']}#{$post_data['post']}\">{$tokens[$j]}</a>";
//                    break;
//                }
//            if (!$found) {
//                $output .= "link:$link_num";
//                $links[$link_num++] = $tokens[$j];
//            }
//            continue;
//        }
//        // Ссылки в пределах имэйджборды.
//        if (preg_match('/(?<=\s|^)\&gt\;\&gt\;\&gt\;\/(\w+?)\/(\d+)(?=\s|$)/', $tokens[$j], $matches) == 1) {
//            if (!$is_posts_data_recived) {
//                $is_posts_data_recived = true;
//                $posts_data = posts_get_all_numbers();
//            }
//            $found = false;
//            foreach ($posts_data as $post_data)
//                if ($post_data['board'] == $matches[1]
//                        && $post_data['post'] == $matches[2]) {
//                    $found = true;
//                    $output .= "link:$link_num";
//                    $links[$link_num++] = "<a class=\"ref|{$post_data['board']}|{$post_data['thread']}|{$post_data['post']}\" href=\"" . Config::DIR_PATH . "/{$post_data['board']}/{$post_data['thread']}#{$post_data['post']}\">{$tokens[$j]}</a>";
//                    break;
//                }
//            if (!$found) {
//                $output .= "link:$link_num";
//                $links[$link_num++] = $tokens[$j];
//            }
//            continue;
//        }

        // back replace
        $line = preg_replace(
                '/<--([0-9]+)-->/e',
                '$hidden[\\1]',
                $line);

        $lines[$key] = $line;
    }
    return implode('<br />', $lines);
}

function build_url($post, $board = false) {
    // TODO: implement posts_check_number() func
    return '<a href="#'.$post.'" onclick="highlight('.$post.')">&gt;&gt;'.$post.'</a>';
}

function kotoba_mark(&$text, $board) {

    /*
     * Перед началом разметки текста все специальные символы должны быть
     * заменены на их html аналоги (например > на &gt;). А так же все косые \
     * должны быть заменены на две косые \\.
     */
        $output = '';

// Шаг 1. Выделение кода.

    // Если в тексте есть ` перед которой не стоит \
        if (preg_match('/(?<!\\\\)\`/', $text) == 1) {

        // Удалим текст, схожий с меткой кода.
        $text = preg_replace('/code:\d+/', '', $text);
        $code_blocks = array();
        $is_code_block = false;
        $is_slash = false;  // Экранирование ` внутри блока кода.
        $code_block_num = 0;
        $output = '';
        for ($i = 0; $i < strlen($text); $i++) {
            if ($text[$i] == '`') {
                if ($is_code_block) {
                    if ($is_slash) {
                        $code_blocks[$code_block_num] .= $text[$i];
                        $is_slash = false;
                        continue;
                    }
                    if (isset($code_blocks[$code_block_num])) {
                        $output .= "code:$code_block_num";
                        $code_block_num++;
                    } else {
                        $output .= '``';    // Пустой блок кода. Просто `` в тексте.
                    }
                    $is_code_block = false;
                } else {
                    if ($is_slash) {
                        $output .= $text[$i];
                        $is_slash = false;
                    } else {
                        $is_code_block = true;
                    }
                }
            } else {
                if ($is_code_block) {
                    if ($text[$i] == '\\') {
                       if (!$is_slash) {
                           $is_slash = true;
                           continue;
                       }
                   } else {
                       $is_slash = false;
                   }
                   if (isset($code_blocks[$code_block_num])) {
                       $code_blocks[$code_block_num] .= $text[$i];
                   } else {
                       $code_blocks[$code_block_num] = $text[$i];
                   }
               } else {
                   if ($text[$i] == '\\') {
                       if (!$is_slash) {
                           $is_slash = true;
                       }
                   } else {
                       $output .= $text[$i];
                       $is_slash = false;
                   }
               }
           }
       }

       /*
        * Message text is over, but we stil read code block. That means single
        * ` character in text, e.g. text `code.
        */
       if ($is_code_block) {
           $is_code_block = false;
           $output .= "`{$code_blocks[$code_block_num]}";
           unset($code_blocks[$code_block_num]);
       }
   } else {
       $output = $text;
   }

// Шаг 2. Выделение спойлеров.

   if (preg_match('/\%\%/', $output) == 1) {
       if (isset($code_blocks) && count($code_blocks) > 0) {
           $text_blocks = preg_split('/(code:\d+)/', $output, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
       } else {
           $text_blocks[] = $output;
       }
       $spoilers = array();
       $spoiler_num = 0;
       $output = '';
       for ($i = 0; $i < count($text_blocks); $i++) {
           $text_blocks[$i] = preg_replace('/spoiler:\d+/', '', $text_blocks[$i]);
           if (preg_match('/code:\d+/', $text_blocks[$i]) == 1) {
               $output .= $text_blocks[$i];
               continue;
           }
           $tokens = preg_split('/(\n|\%\%)/', $text_blocks[$i], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
           $is_spoiler = false;
           $spoiler_pos = -1;
           for ($j = 0; $j < count($tokens); $j++) {
               if ($tokens[$j] == '%%') {
                   if ($is_spoiler) {
                       $is_spoiler = false;
                       $output .= "spoiler:$spoiler_num";
                       $spoiler_num++;
                   } else {
                       $is_spoiler = true;
                   }
                   continue;
               }
               if ($tokens[$j] == "\n") {
                   if ($is_spoiler) {  // Спойлер не может быть многострочным.
                       $is_spoiler = false;
                       $output .= "%%$spoilers[$spoiler_num]\n";
                       unset($spoilers[$spoiler_num]);
                   } else {
                       $output .= $tokens[$j];
                   }
                   continue;
               }
               if ($is_spoiler) {
                   $spoilers[$spoiler_num] = isset($spoilers[$spoiler_num]) ? $spoilers[$spoiler_num] . $tokens[$j] : $tokens[$j];
               } else {
                   $output .= $tokens[$j];
               }
           }
           if ($is_spoiler) {
               $output .= "%%$spoilers[$spoiler_num]";
               unset($spoilers[$spoiler_num]);
           }
       }
   }

// Шаг 3. Выделение ссылок.

        // TODO Придумать проверку на сслыки.
        if(0 == 1)
        {
                if(isset($text_blocks))
                        $text_blocks = null;
                if((isset($code_blocks) && count($code_blocks) > 0) ||
                        (isset($spoilers) && count($spoilers) > 0))
                {
                        $text_blocks = preg_split('/(code:\d+|spoiler:\d+)/', $output, -1,
                                PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                }
                else
                {
                        $text_blocks[] = $output;
                }
                $links = array();
                $link_num = 0;
                $output = '';
                $is_posts_data_recived = false;
                $posts_data = null;
                for($i = 0; $i < count($text_blocks); $i++)
                {
                        $text_blocks[$i] = preg_replace('/(link:\d+)/', '',
                                $text_blocks[$i]);
                        if(preg_match('/(?:code:\d+|spoiler:\d+)/', $text_blocks[$i]) == 1)
                        {
                                $output .= $text_blocks[$i];
                                continue;
                        }
                        $tokens = preg_split('/((?<=\s|^)(?:http|https|irc|ftp):\/\/[^\/?#]*?[^?#]*?(?:\?[^#]*)?(?:#.*?)?(?=\s|$)|' .
                                '(?<=\s|^)\&gt\;\&gt\;\d+(?=\s|$)|' .
                                '(?<=\s|^)\&gt\;\&gt\;\&gt\;\/\w+?\/\d+(?=\s|$)|' .
                                '(?<=\s|^)mailto:(?:\/\/[^\/?#]*?)?[^?#]*?(?:\?[^#]*)?(?:#.*?)?(?=\s|$)|' .
                                '(?<=\s|^)google:\/\/[^?#]*?\/(?=\s|$)|' .
                                '(?<=\s|^)wiki:\/\/[^?#]*?\/(?=\s|$))/', $text_blocks[$i], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                        for($j = 0; $j < count($tokens); $j++)
                        {
                                if(preg_match('/(?<=\s|^)(?:http|https|irc|ftp):\/\/[^\/?#]*?[^?#]*?(?:\?[^#]*)?(?:#.*?)?(?=\s|$)/', $tokens[$j]) == 1 ||
                                        preg_match('/(?<=\s|^)mailto:(?:\/\/[^\/?#]*?)?[^?#]*?(?:\?[^#]*)?(?:#.*?)?(?=\s|$)/', $tokens[$j]) == 1)
                                {
                                        $output .= "link:$link_num";
                                        $links[$link_num] = "<a href=\"$tokens[$j]\">$tokens[$j]</a>";
                                        $link_num++;
                                        continue;
                                }
                                // Ссылки в пределах доски.
                                if(preg_match('/(?<=\s|^)\&gt\;\&gt\;(\d+)(?=\s|$)/', $tokens[$j], $matches) == 1)
                                {
                                        if(!$is_posts_data_recived)
                                        {
                                                $is_posts_data_recived = true;
                                                $posts_data = posts_get_all_numbers();
                                        }
                                        $found = false;
                                        foreach($posts_data as $post_data)
                                                if($post_data['board'] == $board['name']
                                                        && $post_data['post'] == $matches[1])
                                                {
                                                        $found = true;
                                                        $output .= "link:$link_num";
                                                        $links[$link_num++] = "<a class=\"ref|{$post_data['board']}|{$post_data['thread']}|{$post_data['post']}\" href=\"" . Config::DIR_PATH . "/{$post_data['board']}/{$post_data['thread']}#{$post_data['post']}\">{$tokens[$j]}</a>";
                                                        break;
                                                }
                                        if(!$found)
                                        {
                                                $output .= "link:$link_num";
                                                $links[$link_num++] = $tokens[$j];
                                        }
                                        continue;
                                }
                                // Ссылки в пределах имэйджборды.
                                if(preg_match('/(?<=\s|^)\&gt\;\&gt\;\&gt\;\/(\w+?)\/(\d+)(?=\s|$)/', $tokens[$j], $matches) == 1)
                                {
                                        if(!$is_posts_data_recived)
                                        {
                                                $is_posts_data_recived = true;
                                                $posts_data = posts_get_all_numbers();
                                        }
                                        $found = false;
                                        foreach($posts_data as $post_data)
                                                if($post_data['board'] == $matches[1]
                                                        && $post_data['post'] == $matches[2])
                                                {
                                                        $found = true;
                                                        $output .= "link:$link_num";
                                                        $links[$link_num++] = "<a class=\"ref|{$post_data['board']}|{$post_data['thread']}|{$post_data['post']}\" href=\"" . Config::DIR_PATH . "/{$post_data['board']}/{$post_data['thread']}#{$post_data['post']}\">{$tokens[$j]}</a>";
                                                        break;
                                                }
                                        if(!$found)
                                        {
                                                $output .= "link:$link_num";
                                                $links[$link_num++] = $tokens[$j];
                                        }
                                        continue;
                                }
                                if(preg_match('/(?<=\s|^)google:\/\/([^?#]*?)\/(?=\s|$)/', $tokens[$j], $matches) == 1)
                                {
                                        $output .= "link:$link_num";
                                        $links[$link_num++] = "<a href=\"http://www.google.ru/search?q=$matches[1]\">Google: $matches[1]</a>";
                                        continue;
                                }
                                if(preg_match('/(?<=\s|^)wiki:\/\/([^?#]*?)\/(?=\s|$)/', $tokens[$j], $matches) == 1)
                                {
                                        $output .= "link:$link_num";
                                        $links[$link_num++] = "<a href=\"http://en.wikipedia.org/wiki/$matches[1]\">Wiki: $matches[1]</a>";
                                        continue;
                                }
                                $output .= $tokens[$j];
                        }
                }//text_blocks
        }
// Шаг 4. Выделение списков.
        if(preg_match('/(?<=\n|^)(?:\*|\d+\.) /', $output) == 1)
        {
                if(isset($text_blocks))
                        $text_blocks = null;
                if(isset($code_blocks) && count($code_blocks) > 0)
                        $text_blocks = preg_split('/(code:\d+)/', $output, -1,
                                PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                else
                        $text_blocks[] = $output;
                $lists = array();
                $list_num = 0;
                $output = '';
                for($i = 0; $i < count($text_blocks); $i++)
                {
                        $text_blocks[$i] = preg_replace('/(list:\d+)/', '',
                                $text_blocks[$i]);
                        if(preg_match('/code:\d+/', $text_blocks[$i]) == 1)
                        {
                                $output .= $text_blocks[$i];
                                continue;
                        }
                        $tokens = preg_split('/((?<=\n|^)(?:\*|\d+\.) .+(?:\n|$))/',
                                $text_blocks[$i], -1,
                                PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                        $is_list = false;
                        $is_num_list = false;
                        for($j = 0; $j < count($tokens); $j++)
                        {
                                if(preg_match('/(?<=\n|^)\* (.+)(?:\n|$)/', $tokens[$j], $matches) == 1)
                                {
                                        if($is_list)
                                        {
                                                $lists[$list_num] .= "<li>$matches[1]</li>";
                                        }
                                        else
                                        {
                                                if($is_num_list)
                                                {
                                                        $is_num_list = false;
                                                        $lists[$list_num] .= "</ol>";
                                                        $list_num++;
                                                }
                                                $is_list = true;
                                                $output .= "list:$list_num";
                                                $lists[$list_num] = "<ul><li>$matches[1]</li>";
                                        }
                                        continue;
                                }
                                if(preg_match('/(?<=\n|^)\d+\. (.+)(?:\n|$)/', $tokens[$j], $matches) == 1)
                                {
                                        if($is_num_list)
                                        {
                                                $lists[$list_num] .= "<li>$matches[1]</li>";
                                        }
                                        else
                                        {
                                                if($is_list)
                                                {
                                                        $is_list = false;
                                                        $lists[$list_num] .= "</ul>";
                                                        $list_num++;
                                                }
                                                $is_num_list = true;
                                                $output .= "list:$list_num";
                                                $lists[$list_num] = "<ol><li>$matches[1]</li>";
                                        }
                                        continue;
                                }
                                if($is_num_list)
                                {
                                        $is_num_list = false;
                                        $lists[$list_num] .= "</ol>";
                                        $list_num++;
                                }
                                if($is_list)
                                {
                                        $is_list = false;
                                        $lists[$list_num] .= "</ul>";
                                        $list_num++;
                                }
                                $output .= $tokens[$j];
                        }
                        if($is_num_list)
                        {
                                $lists[$list_num] .= "</ol>";
                                $list_num++;
                        }
                        if($is_list)
                        {
                                $lists[$list_num] .= "</ul>";
                                $list_num++;
                        }
                }//text_blocks
        }
// Шаг 5. Выделение цитат.
        if(preg_match('/(?:\n|^|list:\d+|code:\d+)&gt;.+/', $output) == 1)
        {
                if(isset($text_blocks))
                        $text_blocks = null;
                if((isset($code_blocks) && count($code_blocks) > 0)
                        || (isset($lists) && count($lists) > 0))
                {
                        $text_blocks = preg_split('/(code:\d+|list:\d+)/', $output, -1,
                                PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                }
                else
                        $text_blocks[] = $output;
                $quotes = array();
                $quote_num = 0;
                $output = '';
                for($i = 0; $i < count($text_blocks); $i++)
                {
                        $text_blocks[$i] = preg_replace('/(quote:\d+)/', '',
                                $text_blocks[$i]);
                        if(preg_match('/(code:\d+|list:\d+)/', $text_blocks[$i]) == 1)
                        {
                                $output .= $text_blocks[$i];
                                continue;
                        }
                        $tokens = preg_split('/((?<=\n|^)&gt;.+)/', $text_blocks[$i], -1,
                                PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                        for($j = 0; $j < count($tokens); $j++)
                        {
                                if(preg_match('/(?<=\n|^)&gt;.+?/', $tokens[$j]) == 1)
                                {
                                        $output .= "quote:$quote_num";
                                        $quotes[$quote_num++] = $tokens[$j];
                                        continue;
                                }
                                $output .= $tokens[$j];
                        }
                }//text_blocks
        }
// Шаг 6. Применение стилей текста.
        // TODO Придумать проверку на стили текста.
        if(1 == 1)
        {
                // Шаг 1. Однострочные участки текста вне многострочных элементов.
                // (Проверка на однострочность внутри функции basic_mark)
                if(isset($text_blocks))
                        $text_blocks = null;
                if((isset($code_blocks) && count($code_blocks) > 0)
                        || (isset($lists) && count($lists) > 0)
                        || (isset($quotes) && count($quotes) > 0))
                {
                        $text_blocks = preg_split('/(code:\d+|list:\d+|quote:\d+)/',
                                $output, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                }
                else
                        $text_blocks[] = $output;
                $output = '';
                for($i = 0; $i < count($text_blocks); $i++)
                {
                        if(preg_match('/(code:\d+|list:\d+|quote:\d+)/',
                                        $text_blocks[$i]) == 1)
                        {
                                $output .= $text_blocks[$i];
                                continue;
                        }
                        $text_blocks[$i] = basic_mark($text_blocks[$i], '**', 'b');
                        $text_blocks[$i] = basic_mark($text_blocks[$i], '__', 'b');
                        $text_blocks[$i] = basic_mark($text_blocks[$i], '*', 'i');
                        $text_blocks[$i] = basic_mark($text_blocks[$i], '_', 'i');
                        $text_blocks[$i] = basic_mark($text_blocks[$i], '#', 'u');
                        $text_blocks[$i] = basic_mark($text_blocks[$i], '-', 's');
                        $output .= $text_blocks[$i];
                }
                // Шаг 2. Текст внутри цитаты.
                if(isset($quotes) && count($quotes) > 0)
                        for($i = 0; $i < count($quotes); $i++)
                        {
                                $quotes[$i] = basic_mark($quotes[$i], '**', 'b');
                                $quotes[$i] = basic_mark($quotes[$i], '__', 'b');
                                $quotes[$i] = basic_mark($quotes[$i], '*', 'i');
                                $quotes[$i] = basic_mark($quotes[$i], '_', 'i');
                                $quotes[$i] = basic_mark($quotes[$i], '#', 'u');
                                $quotes[$i] = basic_mark($quotes[$i], '-', 's');
                        }
                // Шаг 3. Текст внутри элементов списка.
                if(isset($lists) && count($lists) > 0)
                        for($i = 0; $i < count($lists); $i++)
                        {
                                $tokens = preg_split('/(<ul><li>|<ol><li>|<\/li><li>|<\/li><\/ul>|<\/li><\/ol>)/',
                                        $lists[$i], -1,
                                        PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
                                $lists[$i] = '';
                                for($j = 0; $j < count($tokens); $j++)
                                {
                                        if(preg_match('/(?:<ul><li>|<ol><li>|<\/li><li>|<\/li><\/ul>|<\/li><\/ol>)/',
                                                        $tokens[$j]) == 1)
                                        {
                                                $lists[$i] .= $tokens[$j];
                                                continue;
                                        }
                                        $tokens[$j] = basic_mark($tokens[$j], '**', 'b');
                                        $tokens[$j] = basic_mark($tokens[$j], '__', 'b');
                                        $tokens[$j] = basic_mark($tokens[$j], '*', 'i');
                                        $tokens[$j] = basic_mark($tokens[$j], '_', 'i');
                                        $tokens[$j] = basic_mark($tokens[$j], '#', 'u');
                                        $tokens[$j] = basic_mark($tokens[$j], '-', 's');
                                        $lists[$i] .= $tokens[$j];
                                }
                        }
        }
        // Урежем последовательности пробелов и табов длинее двух вне кода и ссылок.
        if(isset($text_blocks))
                $text_blocks = null;
        if((isset($code_blocks) && count($code_blocks) > 0)
                || (isset($lists) && count($lists) > 0)
                || (isset($quotes) && count($quotes) > 0))
        {
                $text_blocks = preg_split('/(code:\d+|list:\d+)/', $output, -1,
                        PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
        }
        else
                $text_blocks[] = $output;
        $output = '';
        for($i = 0; $i < count($text_blocks); $i++)
        {
                if(preg_match('/(code:\d+|list:\d+)/', $text_blocks[$i]) == 1)
                {
                        $output .= $text_blocks[$i];
                        continue;
                }
                $text_blocks[$i] = preg_replace('/( |\t){2,}/', '$1', $text_blocks[$i]);
                $output .= $text_blocks[$i];
        }
        if(isset($code_blocks) && count($code_blocks) > 0)      // Восстановление кода.
                for($i = 0; $i < count($code_blocks); $i++)
                        $output = str_replace("code:$i",
                                '<pre>' . $code_blocks[$i] . '</pre>',
                                $output);
        if(isset($lists) && count($lists) > 0)  // Восстановление списоков.
                for($i = 0; $i < count($lists); $i++)
                        $output = str_replace("list:$i", $lists[$i], $output);
        if(isset($quotes) && count($quotes) > 0)        // Восстановление цитат.
                for($i = 0; $i < count($quotes); $i++)
                        $output = str_replace("quote:$i",
                                '<blockquote class="unkfunc">' . $quotes[$i] . '</blockquote>',
                                $output);
        if(isset($links) && count($links) > 0)  // Восстановление ссылок.
                for($i = 0; $i < count($links); $i++)
                        $output = str_replace("link:$i", $links[$i], $output);
        if(isset($spoilers) && count($spoilers) > 0)    // Восстановление спойлеров.
                for($i = 0; $i < count($spoilers); $i++)
                        $output = str_replace("spoiler:$i",
                                '<span class="spoiler">' . $spoilers[$i] . '</span>',
                                $output);
        $text = $output;
}
/**
 * Расставляет теги в строке по заданному разделителю.
 * @param string $line Строка для расстановки тегов.
 * @param string $delimeter Разделитель.
 * @param string $tag Тег.
 * @return string
 * Возвращает изменённую строку.
 */
function basic_mark(&$line, $delimeter, $tag) {
    $flags = PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY;
    $regDelimeter = '';
    $tokens = array();

    for ($i = 0; $i < strlen($delimeter); $i++) {
        $regDelimeter .= "\\$delimeter[$i]";
    }

    $lines = preg_split('/(\n)/', $line, -1, $flags);
    for ($i = 0; $i < count($lines); $i++) {
        if ($lines[$i] == "\n") {
            $tokens[] = $lines[$i];
        } else {
            $openMarks = preg_split("/((?: |\t|^)$regDelimeter(?!$regDelimeter|\s))/",
                                    $lines[$i], -1, $flags);
            for ($j = 0; $j < count($openMarks); $j++) {
                $mcount = preg_match("/((?: |\t|^)$regDelimeter)(?!$regDelimeter|\s)/",
                                     $openMarks[$j], $matches);
                if ($mcount == 1) {
                    $tokens[] = $matches[1];
                } else {
                    $closeMarks = preg_split("/((?<!$regDelimeter|\s)$regDelimeter(?: |\t|$))/",
                                             $openMarks[$j], -1, $flags);
                    for ($k = 0; $k < count($closeMarks); $k++) {
                        $tokens[] = $closeMarks[$k];
                    }
                }
            }
        }
    }

    $style = false;
    $line = '';
    $text = '';

        for($i = 0; $i < count($tokens); $i++)
        {
                if($tokens[$i] == "\n")
                {
                        if($style)
                        {
                                $line .= $delimeter . $text . $tokens[$i];
                                $style = false;
                                $text = '';
                        }
                        else
                                $line .= $tokens[$i];
                        continue;
                }
                // Действительные метки в конце и начале строки одинаковые.
                if($tokens[$i] == "$delimeter")
                {
                        if($style)      // Открывающая.
                        {
                                $line .= "<$tag>$text</$tag>";
                                if(count($matches) > 0) $line .= $matches[1];
                                $style = false;
                                $text = '';
            }
                        else            // Закрывающая.
                        {
                                $style = true;
                                if(count($matches) > 0) $line .= $matches[1];
            }
                        continue;
        }
                if(preg_match("/( |\t)$regDelimeter(?!$regDelimeter|\s)/", $tokens[$i],
                                $matches) == 1)         // Открывающая метка.
                {
                        if($style)
                        {
                                $line .= $delimeter . $text . $matches[1];
                                $text = '';
                        }
                        else
                        {
                                $style = true;
                                $line .= $matches[1];
                        }
                        continue;
                }
                if(preg_match("/(?<!$regDelimeter|\s)$regDelimeter( |\t)/", $tokens[$i],
                                $matches) == 1) // Закрывающая метка.
                {
                        if($style)
                        {
                                $line .= "<$tag>$text</$tag>$matches[1]";
                                $style = false;
                                $text = '';
                        }
                        else
                                $line .= $tokens[$i];
                        continue;
                }

                if($style)
                        $text .= $tokens[$i];
                else
                        $line .= $tokens[$i];
        }
        if($text != '')
                $line .= $delimeter . $text;
        return $line;
}
/**
 *
 */
function google_content_handler($content, $argument) {
    if ($content) {
        return "Google: $content";
    }

    return $argument;
}
function wiki_content_handler($content, $argument) {
    if ($content) {
        return "Wiki: $content";
    }

    return $argument;
}
function param_handler($content, $argument) {
    if (!$argument) {
        return preg_replace('/^(Google: )|(Wiki: )/', '', $content);
    }

    return $argument;
}
function lurl_content_handler($content, $argument) {
    return "&gt;&gt;$content";
}
function lurl_param_handler($content, $argument) {
    list ($b, $p) = preg_split("/\//", $argument);
    $p = posts_check_number($p);
    if ( ($b = boards_check_name($b)) == FALSE
            || ($thread = threads_get_by_reply($b, $p)) == FALSE) {

        return "href=\"{error}\"";
    }
    return "class=\"ref|$b|{$thread['original_post']}|$p\" href=\"" . Config::DIR_PATH . "/$b/{$thread['original_post']}#$p\"";
}
function gurl_content_handler($content, $argument) {
    return "&gt;&gt;&gt;$content";
}
function gurl_param_handler($content, $argument) {
    list ($b, $p) = preg_split("/\//", $argument);
    $p = posts_check_number($p);
    if ( ($b = boards_check_name($b)) == FALSE
            || ($thread = threads_get_by_reply($b, $p)) == FALSE) {

        return "href=\"{error}\"";
    }
    return "class=\"ref|$b|{$thread['original_post']}|$p\" href=\"" . Config::DIR_PATH . "/$b/{$thread['original_post']}#$p\"";
}
function bbcode_kotoba_mark($text, $board) {
    static $arrayBBCode = array(
        'i' => array('type'=>BBCODE_TYPE_NOARG, 'open_tag'=>'<i>', 'close_tag'=>'</i>', 'childs'=>'b,s,u'),
        'b' => array('type'=>BBCODE_TYPE_NOARG, 'open_tag'=>'<b>', 'close_tag'=>'</b>', 'childs'=>'i,s,u'),
        'code' => array('type'=>BBCODE_TYPE_NOARG, 'open_tag'=>'<pre>', 'close_tag'=>'</pre>'),
        'spoiler' => array('type'=>BBCODE_TYPE_NOARG, 'open_tag'=>'<span style="color:red;">', 'close_tag'=>'</span>'),
        's' => array('type'=>BBCODE_TYPE_NOARG, 'open_tag'=>'<s>', 'close_tag'=>'</s>', 'childs'=>'b,i'),
        'u' => array('type'=>BBCODE_TYPE_NOARG, 'open_tag'=>'<u>', 'close_tag'=>'</u>', 'childs'=>'b,i'),
        'ul' => array('type'=>BBCODE_TYPE_NOARG, 'open_tag'=>'<ul>', 'close_tag'=>'</ul>', 'childs'=>'li'),
        'ol' => array('type'=>BBCODE_TYPE_NOARG, 'open_tag'=>'<ol>', 'close_tag'=>'</ol>', 'childs'=>'li'),
        'li' => array('type'=>BBCODE_TYPE_NOARG, 'open_tag'=>'<li>', 'close_tag'=>'</li>', 'childs'=>'i,b,s,u,spoiler'),
        'url' => array('type'=>BBCODE_TYPE_OPTARG, 'open_tag'=>'<a href="{PARAM}">', 'close_tag'=>'</a>', 'default_arg'=>'{CONTENT}'),
        'google' => array('type'=>BBCODE_TYPE_OPTARG, 'open_tag'=>'<a href="http://www.google.ru/search?q={PARAM}">', 'close_tag'=>'</a>', 'content_handling'=>'google_content_handler', 'param_handling'=>'param_handler'),
        'wiki' => array('type'=>BBCODE_TYPE_OPTARG, 'open_tag'=>'<a href="http://ru.wikipedia.org/wiki/{PARAM}">', 'close_tag'=>'</a>', 'content_handling'=>'wiki_content_handler', 'param_handling'=>'param_handler'),
        'quote' => array('type'=>BBCODE_TYPE_OPTARG, 'open_tag'=>'<span style="color:green;">', 'close_tag'=>'</span>'),
        'lurl' => array('type'=>BBCODE_TYPE_OPTARG, 'open_tag'=>'<a {PARAM}>', 'close_tag'=>'</a>', 'content_handling'=>'lurl_content_handler', 'param_handling'=>'lurl_param_handler'),
        'gurl' => array('type'=>BBCODE_TYPE_OPTARG, 'open_tag'=>'<a {PARAM}>', 'close_tag'=>'</a>', 'content_handling'=>'gurl_content_handler', 'param_handling'=>'gurl_param_handler')
    );
    static $BBHandler = NULL;

    if ($BBHandler == NULL) {
        $BBHandler = bbcode_create($arrayBBCode);
    }

    $text = preg_replace('/&gt;&gt;&gt;\/(\w+?)\/(\d+)/', '[gurl=$1/$2]/$1/$2[/gurl]', $text);
    $text = preg_replace('/&gt;&gt;(\d+)/', "[lurl=$board/$1]$1[/lurl]", $text);
    $text = preg_replace('/(&gt;.+)/', '[quote]$1[/quote]', $text);

    return bbcode_parse($BBHandler, $text);
}
?>