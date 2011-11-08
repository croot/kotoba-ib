function insert(number) {
    var my_form = document.forms.postform.text;
    if (my_form) {
        if (my_form.createTextRange && my_form.caretPos) {
            var caret_pos = my_form.caretPos;
            caret_pos.text = caret_pos.text.charAt(caret_pos.text.length - 1) == " " ? number + " " : number;
        } else {
            if (my_form.setSelectionRange) {
                var selection_pos = my_form.selectionStart;
                var end = my_form.selectionEnd;
                my_form.value = my_form.value.substr(0, selection_pos) + number + my_form.value.substr(end);
                my_form.setSelectionRange(selection_pos + number.length, selection_pos + number.length);
            } else {
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

// Next functions copypasted from Kusaba, but who cares?
function addreflinkpreview(e) {
    ainfo = this.getAttribute('class').split('|');

    var previewdiv = document.createElement('div');

    previewdiv.setAttribute("id", "preview" + this.getAttribute('href'));
    previewdiv.setAttribute('class', 'reflinkpreview');
    if (e.pageX) {
        previewdiv.style.left = '' + (e.pageX + 10) + 'px';
    } else {
        previewdiv.style.left = (e.clientX + 10);
    }

    var previewdiv_content = document.createTextNode('');
    previewdiv.appendChild(previewdiv_content);
    var parentelement = this.parentNode;
    var newelement = parentelement.insertBefore(previewdiv, this);

    new Ajax.Request(DIR_PATH + '/post.php?board=' + ainfo[1] + '&thread=' + ainfo[2] + '&post=' + ainfo[3],
    {
        method:'get',
        onSuccess: function(transport){
            var response = transport.responseText || "something went wrong (blank response)";

            newelement.innerHTML = response;
        },
        onFailure: function(){ alert('wut'); }
    });
}

function delreflinkpreview(e) {
    var previewelement = document.getElementById('preview' + this.getAttribute('href'));

    if (previewelement) {
        previewelement.parentNode.removeChild(previewelement);
    }
}

function addpreviewevents() {
    var aelements = document.getElementsByTagName('a');
    var aelement;
    for(var i=0;i<aelements.length;i++){
        aelement = aelements[i];
        if (aelement.getAttribute('class')) {
            if (aelement.getAttribute('class').substr(0, 4) == 'ref|') {
                aelement.addEventListener('mouseover', addreflinkpreview, false)
                aelement.addEventListener('mouseout', delreflinkpreview, false)
            }
        }
    }
}

window.onload=function() {
    addpreviewevents();
}

var path = "http://410chan.ru";

function hide(id) {
    var container = document.getElementById("translation" + id);
    container.innerHTML = "";
    container.style.visibility = false;
}

function toggle_display(id) {
    var container = document.getElementById(id);
    if (container.style.display == 'none') {
        container.style.display = '';
    } else {
        container.style.display = 'none';
    }
}

function translate(id) {
    var container = document.getElementById("translation" + id);
    container.innerHTML = '<img src="' + path + '/css/icons/flower.gif">';

    var obj = document.getElementById("post" + id);
    var text = obj.innerHTML;
    var closelink = "";
    google.language.translate(text, "", "en", function(result) {
        if (!result.error) {
            container.innerHTML = closelink + "<strong>Translated from " + result.detectedSourceLanguage + ":</strong><br>\n" + result.translation;
        }
        else {
            container.innerHTML = closelink + "<strong>Error: " + result.error.message + "</strong>";
        }
    });
}

function expandimg(post, url, thumb_url, w, h, thumb_w, thumb_h) {
    OFFSET = 70;
    thumb = document.getElementById("thumb" + post);
    if (typeof expandimg.expanded == 'undefined' || !expandimg.expanded) {
        if ( (scale = (window.innerWidth - OFFSET) / w) < 1) {
            w = w * scale;
            h *= scale;
        }
        if ( (scale = (window.innerHeight - OFFSET) / h) < 1) {
            w *= scale;
            h = h * scale;
        }
        thumb.innerHTML = '<img src="' + url + '" class="thumb" width="' + w + '" height="' + h + '">';
        expandimg.expanded = true;
    } else {
        thumb.innerHTML = '<img src="' + thumb_url + '" class="thumb" width="' + thumb_w + '" height="' + thumb_h + '">';
        expandimg.expanded = false;
    }
}

function menu_toggle(button, area) {
    var tog=document.getElementById(area);
    if(tog.style.display)    {
        tog.style.display="";
    }    else {
        tog.style.display="none";
    }
    button.innerHTML=(tog.style.display)?'+':'&minus;';
    set_cookie('menu_show_'+area, tog.style.display ? '0' : '1', 30);
}

function menu_removeframes(msg) {
    var boardlinks = document.getElementsByTagName("a");
    for (var i=0; i<boardlinks.length; i++) {
        if (boardlinks[i].className == "boardlink") {
            boardlinks[i].target = "_top";
        }
    }
    document.getElementById("removeframes").innerHTML = msg;
    return false;
}

function mark_italic() {
    var textComponent = document.getElementById('message_area');
    var selectedText;
    // IE version
    if (document.selection != undefined) {
        alert('IE not supported yet.');
    }
    // Mozilla version
    else if (textComponent.selectionStart != undefined) {
        var startPos = textComponent.selectionStart;
        var endPos = textComponent.selectionEnd;
        var length = textComponent.value.length;
        textComponent.value = textComponent.value.substring(0, startPos)
                              + '[i]'
                              + textComponent.value.substring(startPos, endPos)
                              + '[/i]'
                              + textComponent.value.substring(endPos, length);
    }
}

function mark_bold() {
    var textComponent = document.getElementById('message_area');
    var selectedText;
    // IE version
    if (document.selection != undefined) {
        alert('IE not supported yet.');
    }
    // Mozilla version
    else if (textComponent.selectionStart != undefined) {
        var startPos = textComponent.selectionStart;
        var endPos = textComponent.selectionEnd;
        var length = textComponent.value.length;
        textComponent.value = textComponent.value.substring(0, startPos)
                              + '[b]'
                              + textComponent.value.substring(startPos, endPos)
                              + '[/b]'
                              + textComponent.value.substring(endPos, length);
    }
}