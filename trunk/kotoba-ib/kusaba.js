function insert(_10)
{
	var _11 = document.forms.Reply_form.Message_text;

	if(_11)
	{
		if(_11.createTextRange && _11.caretPos)
		{
			var _12 = _11.caretPos;
			_12.text = _12.text.charAt(_12.text.length - 1) == " " ? _10 + " " : _10;
		}
		else
		{
			if(_11.setSelectionRange)
			{
				var _13 = _11.selectionStart;
				var end = _11.selectionEnd;
				_11.value = _11.value.substr(0,_13) + _10 + _11.value.substr(end);
				_11.setSelectionRange(_13 + _10.length, _13 + _10.length);
			}
			else
			{
				_11.value += _10 + " ";
			}
		}

		_11.focus();
	}
}