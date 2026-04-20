/*
 * To use put inside the Body a DIV we will call "show_div"
  <div  id="show_div" 
  	style=" position: absolute; 
  	left: 0px; 
  	top: 0px; 
  	padding: 10px;" >
 * that contains everyting in the body
 * add at the bottom outside the DIV 
 * <script type="text/javascript">sizeToFit("show_div");</script>
 *
 */
function getRefToDivMod( divID, oDoc ) {
  if( !oDoc ) { oDoc = document; }
  if( document.layers ) {
        alert("document.layers");
    if( oDoc.layers[divID] ) { return oDoc.layers[divID]; } else {
      for( var x = 0, y; !y && x < oDoc.layers.length; x++ ) {
        y = getRefToDivMod(divID,oDoc.layers[x].document); }
      return y; } }
  if( document.getElementById ) { return oDoc.getElementById(divID); }
  if( document.all ) { return oDoc.all[divID]; }
  return document[divID];
}
function sizeToFit(body_div){
        var x = window ;
        //var odiv = getRefToDivMod( body_div, x.document ); if( !odiv ) { return false; }
        var odiv = document.getElementById(body_div);
	odiv.style.overflow = "hidden";
        //var oW = odiv.clip ? odiv.clip.width : odiv.offsetWidth;
        //var oH = odiv.clip ? odiv.clip.height : odiv.offsetHeight; if( !oH ) { return false; }
        var oW = odiv.scrollWidth;
	var oH = odiv.scrollHeight;
        if(window.innerWidth){
                //Non-IE
                if (document.body.offsetWidth){
                        if (window.innerWidth!=document.body.offsetWidth){
                                functiontype = 'window.innerWidth and document.body.offsetWidth';
                                iWidth = document.body.offsetWidth;
                                iHeight = document.body.offsetHeight;
                        }
                }
                                // Mozill
                                functiontype = 'window.innerWidth';
                                iWidth = window.innerWidth;
                                iHeight = window.innerHeight;
                        
        } 
        else if(document.documentElement.clientHeight)  {
                //IE 6+ in 'standards compliant mode'
                functiontype = 'document.documentElement.clientHeight';
                iWidth = document.documentElement.clientWidth;
                iHeight = document.documentElement.clientHeight;
        } 
        else if(document.body.clientWidth ) {
                //IE 4 compatible
                functiontype = 'document.body.clientWidth';
                iWidth = document.body.clientWidth;
                iHeight = document.body.clientHeight;
        }
//      alert(functiontype);
	// Keeps the script from making the window larger than the screen
	if (oW > screen.availWidth){oW=(screen.availWidth * .9);window.moveTo(0,0);}
	if (oH > screen.availHeight){oH=(screen.availHeight * .9);window.moveTo(0,0);}
        nWidth = (oW - iWidth) + 16;
	//nWidth = (oW - iWidth);
        nHeight = (oH - iHeight) + 16;
        window.resizeBy(nWidth, nHeight);
	//window.moveTo(0,0);
}
