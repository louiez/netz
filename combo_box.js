// to use add a text box right above your select code
// then add  <SCRIPT type="text/javascript"> init_combo_list("txtst");</SCRIPT>
// to the very end of your select code
// using the ID of you select box
// still issues with IE... what a suprise
function init_combo_list(sl){
        var selectBox = document.getElementById(sl);
        var textBox = get_previoussibling(selectBox)
        textBox.style.position= "absolute";
        selectBox.style.zIndex = "1";
        textBox.style.zIndex = "2";
        //textBox.left = selectBox.left
        //textBox.top = selectBox.top
//        textBox.style.left = selectBox.offsetLeft
var absCords = findPos(selectBox);
	textBox.style.left = absCords[0] + "px";
//        textBox.style.top = selectBox.offsetTop
	textBox.style.top  = absCords[1] + "px";
	textBox.title= selectBox.title;
        // IE hack //
        // in IE offsetWidth is zero till the page is loaded
        // this lets it wait 1000 ms to finish loading
        var cmd = "init_combo_list('" + sl + "')";
        if (selectBox.offsetWidth == 0 ){ setTimeout(cmd,1000); return;  }
//alert(selectBox.offsetParent.offsetParent.offsetTop)
        textBox.style.width = (parseInt(selectBox.offsetWidth) - 18) + "px"
        selectBox.onchange = function() {
                        var x = get_previoussibling(this)
                        x.value = this.options[this.selectedIndex].text
                        on_change_made(x)
        }
        textBox.value = selectBox.options[selectBox.selectedIndex].text


        textBox.onchange = function() {
                        var xs = get_nextsibling(this)
                        var y = document.createElement('option');
                        y.text=this.value
                        y.value = this.value
                        try{
                                xs.add(y,null); // standards compliant
                        }
                        catch(ex){
                                xs.add(y); // IE only
                        }
                        xs.selectedIndex=xs.length -1
                        on_change_made(this)

        }

}

//check if the previous sibling node is an element node
//Note: Firefox, and most other browsers, 
//will treat empty white-spaces or new lines as text nodes, 
//Internet Explorer will not. So, in the example below, 
//we have a function that checks the node type of the previous sibling node.
function get_previoussibling(n){
        var  ps = n.previousSibling;
        while (ps.nodeType!=1){
                ps = ps.previousSibling;
        }
        return ps;
}
function get_nextsibling(n){
        var x = n.nextSibling;
        while (x.nodeType!=1){
                x = x.nextSibling;
        }
        return x;

}
/*
function findPos(obj) {
	var curleft = curtop = 0;
//alert(obj.offsetParent.nodeName)
	if (obj.offsetParent) {
		do {
			curleft += obj.offsetLeft;
			curtop += obj.offsetTop;
			document.write(obj.style.position +" "+obj.offsetLeft+"<br>");
			obj = obj.offsetParent;
		} while (obj != null);
	return [curleft,curtop];
	}
}
*/
function findPos(obj){
var posX = obj.offsetLeft;var posY = obj.offsetTop;
while(obj.offsetParent){
if(obj==document.getElementsByTagName('body')[0]){break}
else{
posX=posX+obj.offsetParent.offsetLeft;
posY=posY+obj.offsetParent.offsetTop;
obj=obj.offsetParent;
}
}
var posArray=[posX,posY]
return posArray;
}
