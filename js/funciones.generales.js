// JavaScript Document
function DetectarTecla(e)
{
	var key;
	if(window.event || !e.which) // IE
	{
		key = e.keyCode; // para IE
	}
	else if(e) // netscape
	{
		key = e.which;
	}
	else
	{
		return true;
	}

	return key;
}
$.fn.setCursorPosition = function(pos) {
  this.each(function(index, elem) {
    if (elem.setSelectionRange) {
      elem.setSelectionRange(pos, pos);
    } else if (elem.createTextRange) {
      var range = elem.createTextRange();
      range.collapse(true);
      range.moveEnd('character', pos);
      range.moveStart('character', pos);
      range.select();
    }
  });
  return this;
};
var limpiarHTML = function(id){
	if ($.browser.msie){
        $("#"+id).replaceWith($("#"+id).clone(true));    
    }
    else{
        $("#"+id).val("");
    }
}
