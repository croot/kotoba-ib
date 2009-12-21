/*
 * Based on function from kusaba.
 */
function insert(number)
{
	var my_form = document.forms.postform.text;
	if(my_form)
	{
		if(my_form.createTextRange && my_form.caretPos)
		{
			var caret_pos = my_form.caretPos;
			caret_pos.text = caret_pos.text.charAt(caret_pos.text.length - 1) == " " ? number + " " : number;
		}
		else
		{
			if(my_form.setSelectionRange)
			{
				var selection_pos = my_form.selectionStart;
				var end = my_form.selectionEnd;
				my_form.value = my_form.value.substr(0,selection_pos) + number + my_form.value.substr(end);
				my_form.setSelectionRange(selection_pos + number.length, selection_pos + number.length);
			}
			else
			{
				my_form.value += number + " ";
			}
		}
		my_form.focus();
	}
}
/*
 * Based on examples from http://javascript.ru/ui/draganddrop thanks a lot to
 * it's author!
 */
var resizeMaster = (function ()
{
	var resizer;
	var mouseOffset;
	var savedMouseMove;
	var savedMouseUp;
	var savedDragStart;
	var savedSelectStart;

	function mouseUp(e)
	{
	    resizer = null;

		// Restore handlers
	    document.onmousemove = savedMouseMove;
	    document.onmouseup = savedMouseUp;
	    document.ondragstart = savedDragStart;
	    document.body.onselectstart = savedSelectStart;
	}

    function getMouseOffset(e)
	{
		return {x:e.pageX - removePX(mytextarea.style.width), y:e.pageY - removePX(mytextarea.style.height)};
	}

    function mouseMove(e)
	{
	    e = fixEvent(e);
	    with(mytextarea.style)
		{
			width = e.pageX - mouseOffset.x + 'px';
			height = e.pageY - mouseOffset.y + 'px';
	    }
		return false;
	}

	function mouseDown(e)
	{
	    e = fixEvent(e);
	    if(e.which != 1)
			return;
	    resizer = this;
	    mouseOffset = getMouseOffset(e);

		// Save handlers
		savedMouseMove = document.onmousemove;
	    savedMouseUp = document.onmouseup;
	    savedDragStart = document.ondragstart;
	    savedSelectStart = document.body.onselectstart;

	    document.onmousemove = mouseMove;
		document.onmouseup = mouseUp;

		// We don't want select a text
		document.ondragstart = function() { return false };
		document.body.onselectstart = function() { return false };

	    return false;
	}

	function fixEvent(e)
	{
	    // For IE
	    e = e || window.event;

	    // Add pageX/pageY for IE
	    if ( e.pageX == null && e.clientX != null )
		{
	        var html = document.documentElement;
	        var body = document.body;
	        e.pageX = e.clientX + (html && html.scrollLeft || body && body.scrollLeft || 0) - (html.clientLeft || 0);
	        e.pageY = e.clientY + (html && html.scrollTop || body && body.scrollTop || 0) - (html.clientTop || 0);
	    }

	    // Add which for IE
	    if (!e.which && e.button)
		{
	        e.which = e.button & 1 ? 1 : ( e.button & 2 ? 3 : ( e.button & 4 ? 2 : 0 ) );
	    }

	    return e;
	}

	function removePX(value)
	{
		var indexOfPX = value.indexOf("px");
		if(indexOfPX >= 0)
			return value.substring(0, indexOfPX);
	}

    return {

        setResizer: function(element) {
            element.onmousedown = mouseDown;
        }
    }
}())