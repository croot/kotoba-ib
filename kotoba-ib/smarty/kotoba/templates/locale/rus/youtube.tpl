{* Smarty *}
{*
Код встроенного видео с youtube.com.

Описание переменных:
    $code - код видео.
*}
<object width="220" height="182"> {* width="320" height="265" *}
	<param name="movie" value="http://www.youtube.com/v/{$code}&fs=1&color1=0x234900&color2=0x4e9e00"></param>
	<param name="allowFullScreen" value="true"></param>
	<param name="allowscriptaccess" value="always"></param>
	<embed src="http://www.youtube.com/v/{$code}&fs=1&color1=0x234900&color2=0x4e9e00"
		type="application/x-shockwave-flash"
		allowscriptaccess="always"
		allowfullscreen="true"
		width="220"
		height="182"></embed>
</object>