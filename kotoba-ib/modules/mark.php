<?php
/*************************************
 * Этот файл является частью Kotoba. *
 * Файл license.txt содержит условия *
 * распространения Kotoba.           *
 *************************************/
/*********************************
 * This file is part of Kotoba.  *
 * See license.txt for more info.*
 *********************************/
// Скрипт разметки.
/**
 * Размечает текст.
 * Основные правила разметки можно найти в файле http://coyc.net/wakaba_mark.htm
 * Дополнения и техническое описание находится в файле /res/Kotoba mark 1.txt
 * @param text string <p>Ссылка на текст для разметки.</p>
 * @param board array <p>Доска.</p>
 */
function kotoba_mark(&$text, $board)
{
	/*
	 * Перед началом разметки текста все специальные символы должны быть
	 * заменены на их html аналоги (например > на &gt;). А так же все косые \
	 * должны быть заменены на две косые \\.
	 */
	$output = '';
	$text = str_replace("\r\n", "\n", $text);
	$text = str_replace("\r", "\n", $text);
	$text = str_replace("\f", '', $text);
// Шаг 1. Выделение кода.
	// Если в тексте есть ` перед которой не стоит \
	if(preg_match('/(?<!\\\\)\`/', $text) == 1)
	{
		// Удалим текст, схожий с меткой кода.
		$text = preg_replace('/code:\d+/', '', $text);
		$code_blocks = array();
		$is_code_block = false;
		$is_slash = false;		// Экранирование ` внутри блока кода.
		$code_block_num = 0;
		$output = '';
		for($i = 0; $i < strlen($text); $i++)
		{
			if($text[$i] == '`')
			{
				if($is_code_block)
				{
					if($is_slash)
					{
						$code_blocks[$code_block_num] .= $text[$i];
						$is_slash = false;
						continue;
					}
					if(isset($code_blocks[$code_block_num]))
					{
						$output .= "code:$code_block_num";
						$code_block_num++;
					}
					else
						$output .= '``';	// Пустой блок кода. Просто `` в тексте.
					$is_code_block = false;
				}
				else
					$is_code_block = true;
			}
			else
			{
				if($is_code_block)
				{
					if($text[$i] == '\\' && !$is_slash)
					{
						$is_slash = true;
						continue;
					}
					else
						$is_slash = false;
					if(isset($code_blocks[$code_block_num]))
						$code_blocks[$code_block_num] .= $text[$i];
					else
						$code_blocks[$code_block_num] = $text[$i];
				}
				else
					$output .= $text[$i];
			}
		}
	}
	else
		$output = $text;
// Шаг 2. Выделение спойлеров.
	if(preg_match('/\%\%/', $output) == 1)
	{
		if(isset($code_blocks) && count($code_blocks) > 0)
			$text_blocks = preg_split('/(code:\d+)/', $output, -1,
				PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		else
			$text_blocks[] = $output;
		$spoilers = array();
		$spoiler_num = 0;
		$output = '';
		for($i = 0; $i < count($text_blocks); $i++)
		{
			$text_blocks[$i] = preg_replace('/spoiler:\d+/', '',
				$text_blocks[$i]);
			if(preg_match('/code:\d+/', $text_blocks[$i]) == 1)
			{
				$output .= $text_blocks[$i];
				continue;
			}
			$tokens = preg_split('/(\n|\%\%)/', $text_blocks[$i], -1,
				PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			$is_spoiler = false;
			$spoiler_pos = -1;
			for($j = 0; $j < count($tokens); $j++)
			{
				if($tokens[$j] == '%%')
				{
					if($is_spoiler)
					{
						$is_spoiler = false;
//						$output = mb_substr($output, 0, $spoiler_pos)
//							. "spoiler:$spoiler_num"
//							. mb_substr($output, $spoiler_pos,
//								mb_strlen($output));
						$output .= "spoiler:$spoiler_num";
						$spoiler_num++;
					}
					else
					{
//						$spoiler_pos = mb_strlen($output);
						$is_spoiler = true;
					}
					continue;
				}
				if($tokens[$j] == "\n")
				{
					if($is_spoiler)	// Спойлер не может быть многострочным.
					{
						$is_spoiler = false;
//						$output = mb_substr($output, 0, $spoiler_pos)
//							. "%%$spoilers[$spoiler_num]";
						$output .= "%%$spoilers[$spoiler_num]";
						unset($spoilers[$spoiler_num]);
					}
					else
						$output .= $tokens[$j];
					continue;
				}
				if($is_spoiler)
					$spoilers[$spoiler_num] .= $tokens[$j];
				else
					$output .= $tokens[$j];
			}
			if($is_spoiler)
			{
//				$output = mb_substr($output, 0, $spoiler_pos)
//					. "%%$spoilers[$spoiler_num]";
				$output .= "%%$spoilers[$spoiler_num]";
				unset($spoilers[$spoiler_num]);
			}
		}// text_blocks
	}
// Шаг 3. Выделение ссылок.
	// TODO Придумать проверку на сслыки.
	if(1 == 1)
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
							$links[$link_num++] = "<a href=\"" . Config::DIR_PATH . "/{$post_data['board']}/{$post_data['thread']}#{$post_data['post']}\">{$tokens[$j]}</a>";
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
							$links[$link_num++] = "<a href=\"" . Config::DIR_PATH . "/{$post_data['board']}/{$post_data['thread']}#{$post_data['post']}\">{$tokens[$j]}</a>";
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
	if(isset($code_blocks) && count($code_blocks) > 0)	// Восстановление кода.
		for($i = 0; $i < count($code_blocks); $i++)
			$output = str_replace("code:$i", "<pre>$code_blocks[$i]</pre>",
				$output);
	if(isset($lists) && count($lists) > 0)	// Восстановление списоков.
		for($i = 0; $i < count($lists); $i++)
			$output = str_replace("list:$i", $lists[$i], $output);
	if(isset($quotes) && count($quotes) > 0)	// Восстановление цитат.
		for($i = 0; $i < count($quotes); $i++)
			$output = str_replace("quote:$i",
				"<blockquote class=\"unkfunc\">$quotes[$i]</blockquote>",
				$output);
	if(isset($links) && count($links) > 0)	// Восстановление ссылок.
		for($i = 0; $i < count($links); $i++)
			$output = str_replace("link:$i", $links[$i], $output);
	if(isset($spoilers) && count($spoilers) > 0)	// Восстановление спойлеров.
		for($i = 0; $i < count($spoilers); $i++)
			$output = str_replace("spoiler:$i",
				"<span class=\"spoiler\">$spoilers[$i]</span>",
				$output);
	// Удаление лишних переносов.
	$output = str_replace("<\/blockquote>\n", '</blockquote>', $output);
	$text = $output;
}
/**
 * Расставляет теги в строке по заданному разделителю.
 * @param line string <p>Строка для расстановки тегов.</p>
 * @param delimeter string <p>Разделитель.</p>
 * @param tag string <p>Тег.</p>
 * @return string
 * Возвращает изменённую строку.
 */
function basic_mark(&$line, $delimeter, $tag)
{
	$regDelimeter = '';
	$tokens = array();
	for($i = 0; $i < strlen($delimeter); $i++)
		$regDelimeter .= "\\$delimeter[$i]";
	$lines = preg_split('/(\n)/', $line, -1,
		PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
	for($i = 0; $i < count($lines); $i++)
	{
		if($lines[$i] == "\n")
			$tokens[] = $lines[$i];
		else
		{
			$openMarks = preg_split("/((?: |\t|^)$regDelimeter(?!$regDelimeter|\s))/",
				$lines[$i], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

			for($j = 0; $j < count($openMarks); $j++)
			{
				if(preg_match("/((?: |\t|^)$regDelimeter)(?!$regDelimeter|\s)/",
						$openMarks[$j], $matches) == 1)
					$tokens[] = $matches[1];
				else
				{
					$closeMarks = preg_split("/((?<!$regDelimeter|\s)$regDelimeter(?: |\t|$))/",
						$openMarks[$j], -1,
						PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
					for($k = 0; $k < count($closeMarks); $k++)
						$tokens[] = $closeMarks[$k];
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
			if($style)	// Открывающая.
			{
				$line .= "<$tag>$text</$tag>";
				if(count($matches) > 0) $line .= $matches[1];
				$style = false;
				$text = '';
            }
			else		// Закрывающая.
			{
				$style = true;
				if(count($matches) > 0) $line .= $matches[1];
            }
			continue;
        }
		if(preg_match("/( |\t)$regDelimeter(?!$regDelimeter|\s)/", $tokens[$i],
				$matches) == 1)		// Открывающая метка.
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
				$matches) == 1)	// Закрывающая метка.
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
?>