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

/*
 * Размечает текст в сообщении $src_text.
 * Основные правила разметки можно найти в файле http://coyc.net/wakaba_mark.htm
 * Дополнения и техническое описание находится в файле /res/Kotoba mark 1.txt
 */
function KotobaMark(&$src_text)
{
	$src_text = str_replace("\r\n", "\n", $src_text);	// Заменим переносы строки Windows на переносы Unix.
	$src_text = str_replace("\r", "\n", $src_text);		// Заменим переносы строки Mac на переносы Unix.
	$src_text = str_replace("\f", '', $src_text);		// TODO Это не будет работать, но всё же хорошо бы удалить переводы страницы.

	// Заметки:
	//
	// Приоритеты:
	//
	// Код  		1 Не содержит никакой разметки.			Многострочный.
	// Спойлер  	2 Не содержит никакой разметки.			Однострочный.
	// Ссылка  		3 Не содержит никакой разметки.			Однострочный.
	// Список		4 Содержит любую однострочную разметку.	Многострочный.
	// Цитата		5 Содержит любую однострочную разметку.	Многострочный.
	// Стили Текста 6 Содержат любую однострочную разметку.	Однострочный.
	//
	// Елемент с более высоким приоритетом отменяет элемент с более низким, если не указано противное.
	// Например, %%спойлер с `кодом`%% даст %%спойлер с <pre>кодом</pre>%%, а не <span class="spoiler">спойлер с `кодом`</span>

	// Шаг 1. Выделение кода.

	if(preg_match('/(?<!\\\\)\`/', $src_text) == 1)
	{
		$src_text = preg_replace('/code:\d+/', '', $src_text);

		$CodeBlocks = array();
		$isCodeBlock = false;
		$isSlash = false;		// Экранирование обратного апострофа внутри кода.
		$CodeBlockNum = 0;
		$output = '';

		for($i = 0; $i < strlen($src_text); $i++)
			if($src_text[$i] == '`')
			{
				if($isCodeBlock)
				{
					if($isSlash)							// Экранированный обратный апостроф.
					{
						$CodeBlocks[$CodeBlockNum] .= $src_text[$i];
						$isSlash = false;
						continue;
					}

					if(isset($CodeBlocks[$CodeBlockNum]))	// Непустой блок кода.
					{
						$output .= "code:$CodeBlockNum";
						$CodeBlockNum++;
					}
					else
						$output .= '``';

					$isCodeBlock = false;
				}
				else
					$isCodeBlock = true;
			}
			else
			{
				if($isCodeBlock)
				{
					if($src_text[$i] == '\\' && !$isSlash)
					{
						$isSlash = true;
						continue;
					}

					$CodeBlocks[$CodeBlockNum] .= $src_text[$i];
				}
				else
					$output .= $src_text[$i];
			}
	}
	else
		$output = $src_text;

	// Шаг 1. Выделение спойлеров.

	if(preg_match('/\%\%/', $output) == 1)
	{
		if(isset($CodeBlocks) && count($CodeBlocks) > 0)
			$TextBlocks = preg_split('/(code:\d+)/', $output, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		else
			$TextBlocks[] = $output;

		$Spoilers = array();
		$SpoilerNum = 0;
		$output = '';

		for($i = 0; $i < count($TextBlocks); $i++)
		{
			$TextBlocks[$i] = preg_replace('/spoiler:\d+/', '', $TextBlocks[$i]);

			if(preg_match('/code:\d+/', $TextBlocks[$i]) == 1)
			{
				$output .= $TextBlocks[$i];
				continue;
			}

			$tokens = preg_split('/(\n|\%\%)/', $TextBlocks[$i], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			$isSpoiler = false;
			$SpoilerPos = -1;

			for($j = 0; $j < count($tokens); $j++)
			{
				if($tokens[$j] == '%%')
				{
					if($isSpoiler)	// Закрывающая метка.
					{
						$isSpoiler = false;
						$output = mb_substr($output, 0, $SopilerPos) . "spoiler:$SpoilerNum" . mb_substr($output, $SopilerPos, mb_strlen($output));
						$SpoilerNum++;
					}
					else			// Открывающая метка.
					{
						$SopilerPos = mb_strlen($output);
						$isSpoiler = true;
					}

					continue;
				}

				if($tokens[$j] == "\n")
				{
					if($isSpoiler)	// Спойлер не может быть многострочным.
					{
						$isSpoiler = false;
						$output = mb_substr($output, 0, $SopilerPos) . "%%$Spoilers[$SpoilerNum]";
						unset($Spoilers[$SpoilerNum]);
					}
					else
						$output .= $tokens[$j];

					continue;
				}

				if($isSpoiler)
					$Spoilers[$SpoilerNum] .= $tokens[$j];
				else
					$output .= $tokens[$j];
			}

			if($isSpoiler)	// Спойлер не может включать в себя код.
			{
				$output = mb_substr($output, 0, $SopilerPos) . "%%$Spoilers[$SpoilerNum]";
				unset($Spoilers[$SpoilerNum]);
			}
		}// TextBlocks
	}

	// Шаг 3. Выделение ссылок.
	// TODO Придумать проверку на сслыки.
	if(1 == 1)
	{
		// Для разбора ссылок на посты в рамках одной доски и всей имэйджборды,
		// следующие переменные должны быть объявлены глобальными и им должны
		// быть присвоены корректные значения.
		global $BOARD_NAME, $BOARD_NUM;
		// А так же константы: KOTOBA_DIR_PATH, KOTOBA_ENABLE_STAT, ERR_GET_POSTS_THREADS,
		// ERR_BOARDS_POSTS_THREADS.
		
		$HEAD = "<html>\n<head>\n\t<title>Mark error page</title>\n\t<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n\t" .
				"<link rel=\"stylesheet\" type=\"text/css\" href=\"" . KOTOBA_DIR_PATH . "/kotoba.css\">\n</head>\n<body>\n";
		$FOOTER = "\n</body>\n</html>";

		if(isset($TextBlocks))
			unset($TextBlocks);

		if((isset($CodeBlocks) && count($CodeBlocks) > 0) || (isset($Spoilers) && count($Spoilers) > 0))
			$TextBlocks = preg_split('/(code:\d+|spoiler:\d+)/', $output, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		else
			$TextBlocks[] = $output;

		$Links = array();
		$LinkNum = 0;
		$output = '';
		$isThreadsDataRecived = false;
		$ThreadsData = array();
		$isBoardsDataRecived = false;
		$BoardsData = array();

		for($i = 0; $i < count($TextBlocks); $i++)
		{
			$TextBlocks[$i] = preg_replace('/(link:\d+)/', '', $TextBlocks[$i]);

			if(preg_match('/(?:code:\d+|spoiler:\d+)/', $TextBlocks[$i]) == 1)
			{
				$output .= $TextBlocks[$i];
				continue;
			}

			$tokens = preg_split('/((?<=\s|^)(?:http|https|irc|ftp):\/\/[^\/?#]*?[^?#]*?(?:\?[^#]*)?(?:#.*?)?(?=\s|$)|' .
				'(?<=\s|^)\&gt\;\&gt\;\d+(?=\s|$)|' .
				'(?<=\s|^)\&gt\;\&gt\;\&gt\;\/\w+?\/\d+(?=\s|$)|' .
				'(?<=\s|^)mailto:(?:\/\/[^\/?#]*?)?[^?#]*?(?:\?[^#]*)?(?:#.*?)?(?=\s|$)|' .
				'(?<=\s|^)google:\/\/[^?#]*?\/(?=\s|$)|' .
				'(?<=\s|^)wiki:\/\/[^?#]*?\/(?=\s|$))/', $TextBlocks[$i], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

			for($j = 0; $j < count($tokens); $j++)
			{
				if(preg_match('/(?<=\s|^)(?:http|https|irc|ftp):\/\/[^\/?#]*?[^?#]*?(?:\?[^#]*)?(?:#.*?)?(?=\s|$)/', $tokens[$j]) == 1 ||
					preg_match('/(?<=\s|^)mailto:(?:\/\/[^\/?#]*?)?[^?#]*?(?:\?[^#]*)?(?:#.*?)?(?=\s|$)/', $tokens[$j]) == 1)
				{
					$output .= "link:$LinkNum";
					$Links[$LinkNum] = "<a href=\"$tokens[$j]\">$tokens[$j]</a>";
					$LinkNum++;
					continue;
				}

				if(preg_match('/(?<=\s|^)\&gt\;\&gt\;(\d+)(?=\s|$)/', $tokens[$j], $matches) == 1)
				{
					if(!isset($BOARD_NAME) || !isset($BOARD_NUM) || !defined('KOTOBA_DIR_PATH') 
						|| !defined('KOTOBA_ENABLE_STAT') || !defined('ERR_GET_POSTS_THREADS'))	// Ссылки не могут быть разобраны.
					{
						$output .= "link:$LinkNum";
						$Links[$LinkNum++] = $tokens[$j];
						continue;
                    }

					if(!$isThreadsDataRecived)	//Получение номеров постов и тредов доски.
					{
						if(($result = mysql_query("select p.`id`, p.`thread` from `posts` p join `threads` t on p.`thread` = t.`id` and p.`board` = t.`board` where p.`board` = $BOARD_NUM and (position('ARCHIVE:YES' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null)")) !== false)
						{
							while(($row = mysql_fetch_array($result, MYSQL_ASSOC)) !== false)
								$ThreadsData[$row['id']] = $row['thread'];

							mysql_free_result($result);
						}
						else
						{
							if(KOTOBA_ENABLE_STAT)
								kotoba_stat(sprintf(ERR_GET_POSTS_THREADS, $BOARD_NAME, mysql_error()));

							die($HEAD . "<span class=\"error\">Ошибка. Не удалось получить номера постов и тредов доски $BOARD_NAME. Прична: " .  mysql_error() . '</span>' . $FOOTER);
						}                                   

						$isThreadsDataRecived = true;
                    }

					if(in_array($matches[1], array_keys($ThreadsData)))	// TODO Номер поста из реги имеет тип string, а номер поста из БД - int
					{
						$output .= "link:$LinkNum";
						$Links[$LinkNum++] = '<a href="' . KOTOBA_DIR_PATH . "/$BOARD_NAME/{$ThreadsData[$matches[1]]}/#$matches[1]\">$tokens[$j]</a>";
						continue;
					}
					else
					{
						$output .= "link:$LinkNum";
						$Links[$LinkNum++] = $tokens[$j];
						continue;
                    }
				}

				if(preg_match('/(?<=\s|^)\&gt\;\&gt\;\&gt\;\/(\w+?)\/(\d+)(?=\s|$)/', $tokens[$j], $matches) == 1)
				{
					if(!isset($BOARD_NAME) || !isset($BOARD_NUM) || !defined('KOTOBA_DIR_PATH') 
						|| !defined('KOTOBA_ENABLE_STAT') || !defined('ERR_BOARDS_POSTS_THREADS'))	// Ссылки не могут быть разобраны.
					{
						$output .= "link:$LinkNum";
						$Links[$LinkNum++] = $tokens[$j];
						continue;
                    }

					if(!$isBoardsDataRecived)
					{
						// Получение номеров постов и тредов всех досок.
						if(($result = mysql_query('select b.`Name` `board`, p.`id`, p.`thread` from `posts` p join `threads` t on p.`thread` = t.`id` and p.`board` = t.`board` join `boards` b on p.`board` = b.`id` where (position(\'ARCHIVE:YES\' in t.`Thread Settings`) = 0 or t.`Thread Settings` is null) order by  p.`board`, p.`thread`, p.`id`')) !== false)
						{
							while(($row = mysql_fetch_array($result, MYSQL_ASSOC)) !== false)
								$BoardsData[$row['board']][$row['id']] = $row['thread'];

							mysql_free_result($result);
						}
						else
						{
							if(KOTOBA_ENABLE_STAT)
								kotoba_stat(sprintf(ERR_BOARDS_POSTS_THREADS, mysql_error()));

							die($HEAD . "<span class=\"error\">Ошибка. Не удалось получить номера постов и тредов на досках. Прична: " .  mysql_error() . '</span>' . $FOOTER);
						}
						
						$isBoardsDataRecived = true;
                    }

					// TODO Номер поста из реги имеет тип string, а номер поста из БД - int
				    if(in_array($matches[1], array_keys($BoardsData), true) && in_array($matches[2], array_keys($BoardsData[$matches[1]])))	// Есть такая доска и тред.
					{
						$output .= "link:$LinkNum";
						$Links[$LinkNum++] = '<a href="' . KOTOBA_DIR_PATH . "/$matches[1]/{$BoardsData[$matches[1]][$matches[2]]}/#$matches[2]\">$tokens[$j]</a>";
						continue;
                    }
					else
					{
						$output .= "link:$LinkNum";
						$Links[$LinkNum++] = $tokens[$j];
						continue;
                    }
				}

				if(preg_match('/(?<=\s|^)google:\/\/([^?#]*?)\/(?=\s|$)/', $tokens[$j], $matches) == 1)
				{
					$output .= "link:$LinkNum";
					$Links[$LinkNum++] = "<a href=\"http://www.google.ru/search?q=$matches[1]\">Google: $matches[1]</a>";
					continue;
				}

				if(preg_match('/(?<=\s|^)wiki:\/\/([^?#]*?)\/(?=\s|$)/', $tokens[$j], $matches) == 1)
				{
					$output .= "link:$LinkNum";
					$Links[$LinkNum++] = "<a href=\"http://en.wikipedia.org/wiki/$matches[1]\">Wiki: $matches[1]</a>";
					continue;
				}

				$output .= $tokens[$j];
			}
		}// TextBlocks
	}

	// Шаг 4. Выделение списков.
	
	if(preg_match('/(?<=\n|^)(?:\*|\d+\.) /', $output) == 1)
	{
		if(isset($TextBlocks))
			unset($TextBlocks);

		if(isset($CodeBlocks) && count($CodeBlocks) > 0)
			$TextBlocks = preg_split('/(code:\d+)/', $output, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		else
			$TextBlocks[] = $output;

		$Lists = array();
		$ListNum = 0;
		$output = '';

		for($i = 0; $i < count($TextBlocks); $i++)
		{
			$TextBlocks[$i] = preg_replace('/(list:\d+)/', '', $TextBlocks[$i]);

			if(preg_match('/code:\d+/', $TextBlocks[$i]) == 1)
			{
				$output .= $TextBlocks[$i];
				continue;
			}

			$tokens = preg_split('/((?<=\n|^)(?:\*|\d+\.) .+(?:\n|$))/', $TextBlocks[$i], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
			$isList = false;
			$isNumList = false;

			for($j = 0; $j < count($tokens); $j++)
			{
				if(preg_match('/(?<=\n|^)\* (.+)(?:\n|$)/', $tokens[$j], $matches) == 1)
				{
					if($isList)
					{
						$Lists[$ListNum] .= "<li>$matches[1]</li>";
					}
					else
					{
						if($isNumList)
						{
							$isNumList = false;
							$Lists[$ListNum] .= "</ol>";
							$ListNum++;
						}

						$isList = true;
						$output .= "list:$ListNum";
						$Lists[$ListNum] = "<ul><li>$matches[1]</li>";
					}

					continue;
				}

				if(preg_match('/(?<=\n|^)\d+\. (.+)(?:\n|$)/', $tokens[$j], $matches) == 1)
				{
					if($isNumList)
					{
						$Lists[$ListNum] .= "<li>$matches[1]</li>";
					}
					else
					{
						if($isList)
						{
							$isList = false;
							$Lists[$ListNum] .= "</ul>";
							$ListNum++;
						}

						$isNumList = true;
						$output .= "list:$ListNum";
						$Lists[$ListNum] = "<ol><li>$matches[1]</li>";
					}

					continue;
				}

				if($isNumList)
				{
					$isNumList = false;
					$Lists[$ListNum] .= "</ol>";
					$ListNum++;
				}

				if($isList)
				{
					$isList = false;
					$Lists[$ListNum] .= "</ul>";
					$ListNum++;
				}

				$output .= $tokens[$j];
			}

			if($isNumList)
			{
				$Lists[$ListNum] .= "</ol>";
				$ListNum++;
			}

			if($isList)
			{
				$Lists[$ListNum] .= "</ul>";
				$ListNum++;
			}
		}// TextBlocks
	}

	// Шаг 5. Выделение цитат.

	if(preg_match('/(?:\n|^|list:\d+|code:\d+)&gt;.+/', $output) == 1)
	{
		if(isset($TextBlocks))
			unset($TextBlocks);

		if((isset($CodeBlocks) && count($CodeBlocks) > 0) || (isset($Lists) && count($Lists) > 0))
			$TextBlocks = preg_split('/(code:\d+|list:\d+)/', $output, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		else
			$TextBlocks[] = $output;

		$Quotes = array();
		$QuoteNum = 0;
		$output = '';

		for($i = 0; $i < count($TextBlocks); $i++)
		{
			$TextBlocks[$i] = preg_replace('/(quote:\d+)/', '', $TextBlocks[$i]);

			if(preg_match('/(code:\d+|list:\d+)/', $TextBlocks[$i]) == 1)
			{
				$output .= $TextBlocks[$i];
				continue;
			}

			$tokens = preg_split('/((?<=\n|^)&gt;.+)/', $TextBlocks[$i], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

			for($j = 0; $j < count($tokens); $j++)
			{
				if(preg_match('/(?<=\n|^)&gt;.+?/', $tokens[$j]) == 1)
				{
					$output .= "quote:$QuoteNum";
					$Quotes[$QuoteNum++] = $tokens[$j];
					continue;
				}

				$output .= $tokens[$j];
			}
		}// TextBlocks
	}

	// Шаг 6. Применение стилей текста.
	// TODO Придумать проверку на стили текста.
	if(1 == 1)
	{
		// Шаг 1. Однострочные участки текста вне многострочных элементов.
		if(isset($TextBlocks))
			unset($TextBlocks);

		if((isset($CodeBlocks) && count($CodeBlocks) > 0) || (isset($Lists) && count($Lists) > 0) || (isset($Quotes) && count($Quotes) > 0))
			$TextBlocks = preg_split('/(code:\d+|list:\d+|quote:\d+)/', $output, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		else
			$TextBlocks[] = $output;

		$output = '';

		for($i = 0; $i < count($TextBlocks); $i++)
		{
			if(preg_match('/(code:\d+|list:\d+|quote:\d+)/', $TextBlocks[$i]) == 1)
			{
				$output .= $TextBlocks[$i];
				continue;
			}

			$TextBlocks[$i] = wak_basic_mark($TextBlocks[$i], '**', 'b');
			$TextBlocks[$i] = wak_basic_mark($TextBlocks[$i], '__', 'b');
			$TextBlocks[$i] = wak_basic_mark($TextBlocks[$i], '*', 'i');
			$TextBlocks[$i] = wak_basic_mark($TextBlocks[$i], '_', 'i');
			$TextBlocks[$i] = wak_basic_mark($TextBlocks[$i], '#', 'u');
			$TextBlocks[$i] = wak_basic_mark($TextBlocks[$i], '-', 's');

			$output .= $TextBlocks[$i];
		}

		// Шаг 2. Текст внутри цитаты.
		if(isset($Quotes) && count($Quotes) > 0)
			for($i = 0; $i < count($Quotes); $i++)
			{
				$Quotes[$i] = wak_basic_mark($Quotes[$i], '**', 'b');
				$Quotes[$i] = wak_basic_mark($Quotes[$i], '__', 'b');
				$Quotes[$i] = wak_basic_mark($Quotes[$i], '*', 'i');
				$Quotes[$i] = wak_basic_mark($Quotes[$i], '_', 'i');
				$Quotes[$i] = wak_basic_mark($Quotes[$i], '#', 'u');
				$Quotes[$i] = wak_basic_mark($Quotes[$i], '-', 's');
			}

		// Шаг 3. Текст внутри элементов списка.
		if(isset($Lists) && count($Lists) > 0)
			for($i = 0; $i < count($Lists); $i++)
			{
				$tokens = preg_split('/(<ul><li>|<ol><li>|<\/li><li>|<\/li><\/ul>|<\/li><\/ol>)/', $Lists[$i], -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
				$Lists[$i] = '';

				for($j = 0; $j < count($tokens); $j++)
				{
					if(preg_match('/(?:<ul><li>|<ol><li>|<\/li><li>|<\/li><\/ul>|<\/li><\/ol>)/', $tokens[$j]) == 1)
					{
						$Lists[$i] .= $tokens[$j];
						continue;
					}

					$tokens[$j] = wak_basic_mark($tokens[$j], '**', 'b');
					$tokens[$j] = wak_basic_mark($tokens[$j], '__', 'b');
					$tokens[$j] = wak_basic_mark($tokens[$j], '*', 'i');
					$tokens[$j] = wak_basic_mark($tokens[$j], '_', 'i');
					$tokens[$j] = wak_basic_mark($tokens[$j], '#', 'u');
					$tokens[$j] = wak_basic_mark($tokens[$j], '-', 's');
					$Lists[$i] .= $tokens[$j];
				}
			}
	}

	if(isset($CodeBlocks) && count($CodeBlocks) > 0)	// Восстановление кода.
		for($i = 0; $i < count($CodeBlocks); $i++)
			$output = preg_replace("/(code:$i)/", "<pre>$CodeBlocks[$i]</pre>", $output);

	if(isset($Lists) && count($Lists) > 0)				// Восстановление списоков.
		for($i = 0; $i < count($Lists); $i++)
			$output = preg_replace("/(list:$i)/", $Lists[$i], $output);

	if(isset($Quotes) && count($Quotes) > 0)			// Восстановление цитат.
		for($i = 0; $i < count($Quotes); $i++)
			$output = preg_replace("/(quote:$i)/", "<blockquote class=\"unkfunc\">$Quotes[$i]</blockquote>", $output);

	if(isset($Links) && count($Links) > 0)				// Восстановление ссылок.
		for($i = 0; $i < count($Links); $i++)
			$output = preg_replace("/(link:$i)/", $Links[$i], $output);

	if(isset($Spoilers) && count($Spoilers) > 0)		// Восстановление спойлеров.
		for($i = 0; $i < count($Spoilers); $i++)
			$output = preg_replace("/(spoiler:$i)/", "<span class=\"spoiler\">$Spoilers[$i]</span>", $output);

	// Удаление лишних переносов.
	$output = preg_replace("/<\/blockquote>\n/", '</blockquote>', $output);

	$src_text = $output;
}

/*
 * Заменяет $delimeterТЕКСТ$delimeter на $tagТЕКСТ$tag в строке $line.
 * Возвращает изменённую строку.
 */
function wak_basic_mark(&$line, $delimeter, $tag)
{
	$parts = explode($delimeter, $line);
	$numparts = count($parts);

	if($numparts > 2)
	{
		$counter = 0;
		$result = array();

		foreach($parts as $part)
		{
			if($counter > 0)
				if($counter % 2 && array_key_exists($counter + 1, $parts))
				{
					if($part != '')
					{
						if(preg_match('/^\S.*$/', $part) == 1)
							$part = "<$tag>" . $part . "</$tag>";
						else
							$part = $delimeter . $part . $delimeter;
					}
					else
						$part = $delimeter . $delimeter;
				}

			array_push($result, $part);
			$counter ++;
		}

		return implode($result);
	}
	else
		return $line;
}
?>