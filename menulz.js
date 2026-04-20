<!--
// changed the NS6 without the document.all
// and added the &&!navigator.appName=="Opera" to IE
// this forced the code to use the NS6 code for opera browser
// But still work on Firefox, IE as well

//var NS6 = (document.getElementById&&!document.all);
var NS6 = (document.getElementById);
var IE =  (document.all&&!navigator.appName=="Opera");
var NS = (navigator.appName=="Netscape" && navigator.appVersion.charAt(0)=="4");
/*
var NS6 = true;
var IE = false;
var NS = false;
*/
var tempBar='';
var barBuilt=0;
var lastY=0;
var sI=new Array();
var moving=setTimeout('null',1);
function moveOut() 
{
	if (parseInt(ssm.left)<0) 
	{
		clearTimeout(moving);
		moving = setTimeout('moveOut()', slideSpeed);
		slideMenu(10)
	}
	else 
	{
		clearTimeout(moving);
		moving=setTimeout('null',1)
	}
}

function moveBack() 
{
	clearTimeout(moving);
	moving = setTimeout('moveBack1()', waitTime)
}

function moveBack1() 
{
	if (parseInt(ssm.left)>(-menuWidth)) 
	{
		clearTimeout(moving);
		moving = setTimeout('moveBack1()', slideSpeed);
		slideMenu(-10)
	}
	else 
	{
		clearTimeout(moving);moving=setTimeout('null',1)
	}
}

function slideMenu(num)
{
	ssm.left = parseInt(ssm.left)+num;
	if (NS) 
	{
		bssm.clip.right+=num;bssm2.clip.right+=num;
		if(bssm.left+bssm.clip.right>document.width)
		{
			document.width+=num
		}
	}
}

function makeStatic() 
{
	winY=(IE)?document.body.scrollTop:window.pageYOffset;
	if (winY!=lastY&&winY>YOffset-staticYOffset) 
	{
		smooth = .2 * (winY - lastY - YOffset + staticYOffset);
	}
	else if (YOffset-staticYOffset+lastY>YOffset-staticYOffset) 
	{
		smooth = .2 * (winY - lastY - (YOffset-(YOffset-winY)));
	}
	else 
	{
		smooth=0;
	}
	if(smooth > 0)
	{
		 smooth = Math.ceil(smooth);
	}
	else 
	{
		smooth = Math.floor(smooth);
	}
	bssm.top=parseInt(bssm.top)+smooth
	lastY = lastY+smooth;
	setTimeout('makeStatic()', 10)}

function buildBar() 
{
   var dr;
	if(barText.toLowerCase().indexOf('<img')>-1) 
	{
		tempBar=barText;
	}
	else
	{
		for (b=0;b<barText.length;b++) 
		{	
			tempBar+=barText.charAt(b)+"<BR>"
		}
	}
	
	dr= '<td style="padding:2px;text-align:right"  rowspan="100" width="'+barWidth+'" bgcolor="';
//dr= '<td style="text-align:center" rowspan="100" width="'+barWidth+'" bgcolor="';
	//dr=dr + barBGColor +'" valign="' + barVAlign + '" align=center><font face="';
dr=dr + barBGColor + '"><font face="';
	dr=dr + barFontFamily+'" Size="' + barFontSize;
	dr=dr + '" COLOR="' + barFontColor + '"><B>' + tempBar + '</B></font></td>';
	document.write(dr);
}

function initSlide() 
{
	if (NS6||IE)
	{
		ssm=(NS6)?document.getElementById(ID2):document.all(ID2);
		bssm=(NS6)?document.getElementById(ID1).style:document.all(ID1).style;
		bssm.clip="rect(0 "+ssm.offsetWidth+" "+(((IE)?document.body.clientHeight:0)+ssm.offsetHeight)+" 0)";
		bssm.visibility="visible";
		ssm=ssm.style;
		if(NS6)bssm.top=YOffset
	}
	else if (NS) 
	{
		bssm=document.layers[ID3];
		bssm2=bssm.document.layers[ID4];
		ssm=bssm2.document.layers[ID2];
		bssm2.clip.left=0;ssm.visibility = "show";
	}
	else{
                ssm=(NS6)?document.getElementById(ID2):document.all(ID2);
                bssm=(NS6)?document.getElementById(ID1).style:document.all(ID1).style;
                bssm.clip="rect(0 "+ssm.offsetWidth+" "+(((IE)?document.body.clientHeight:0)+ssm.offsetHeight)+" 0)";
                bssm.visibility="visible";
                ssm=ssm.style;
                if(NS6)bssm.top=YOffset

	}
	if (menuIsStatic=="yes") makeStatic();
}

function buildMenu() 
{
var dr;
	if (IE||NS6) 
	{
		//alert('IE||NS6');
		dr='<DIV ID="' + ID1 + '" style="visibility:hidden;Position : Absolute ;Left : ';
		dr=dr + XOffset + ' ;Top : ' + YOffset + ' ;Z-Index : 20;width:' + (menuWidth+barWidth+10) 
		dr=dr + '"><DIV ID="' + ID2 + '" style="Position : Absolute ;Left : ';
		dr=dr +(-menuWidth)+' ;Top : 0px ;Z-Index : 21;';
		dr=dr + ((IE)?"width:1px":"") + '" onmouseover="moveOut()" onmouseout="moveBack()">';
		//alert(dr);
		document.write(dr);
	}
	if (NS) 
	{
		//alert('NS');
		dr= '<LAYER name="' + ID3 + '" top="'+YOffset+'" LEFT='+XOffset;
		dr=dr + ' visibility="show" onload="initSlide()"><ILAYER name="' + ID4 + '">';
		dr=dr + '<LAYER visibility="hide" name="' + ID2 + '" bgcolor="'+menuBGColor;
		dr=dr + '" left="'+(-menuWidth)+'" onmouseover="moveOut()" onmouseout="moveBack()">';
		
		document.write(dr);
	}
	if (NS6)
	{
		//alert('NS6');
		dr='<table border="0" cellpadding="0" cellspacing="0" width="'+(menuWidth+barWidth+2);
		dr += '<tbody>';
		dr=dr +'" bgcolor="'+menuBGColor+'"><TR><TD>';
		document.write(dr);
	}
	
	dr='<table border="0" cellpadding="0" cellspacing="1" width="';
	dr=dr + (menuWidth+barWidth+2)+'" bgcolor="'+menuBGColor+'">';
	document.write(dr);
	for(i=0;i<sI.length;i++) 
	{
		if(!sI[i][3])
		{
			sI[i][3]=menuCols;
			sI[i][5]=menuWidth-1;
		}
		else if(sI[i][3]!=menuCols)
		{
			sI[i][5]=Math.round(menuWidth*(sI[i][3]/menuCols)-1);
		}
		if(sI[i-1]&&sI[i-1][4]!="no")
		{
			document.write('<TR>')
		}
		if(!sI[i][1])
		{
		// Menu header		
			dr='<TD style="background-color:'+hdrBGColor+'; width:'+ sI[i][5] + '; color:';
			dr=dr + hdrFontColor + ';font-size:'+ hdrFontSize +';font-family:' + hdrFontFamily + '"';
			dr=dr +'" COLSPAN="'+sI[i][3]+'" ALIGN="'+hdrAlign+'" VALIGN="';
			dr=dr + hdrVAlign + '"><b>&nbsp;' + sI[i][0] + '</TD>'; 
			document.write(dr);
		}
		else 
		{
			if(!sI[i][2])
			{
				sI[i][2]=linkTarget;
			}
			dr='<TD BGCOLOR="'+linkBGColor+'" onmouseover="bgColor=\''+linkOverBGColor;
			dr=dr + '\'" onmouseout="bgColor=\''+linkBGColor+'\'" WIDTH="'+sI[i][5];
			dr=dr +'" COLSPAN="'+sI[i][3]+'"><ILAYER><LAYER onmouseover="bgColor=\''+linkOverBGColor;
			dr=dr +'\'" onmouseout="bgColor=\''+linkBGColor+'\'" WIDTH="100%" ALIGN="';
			dr=dr +linkAlign+'"><DIV  ALIGN="'+linkAlign+'"><FONT face="'+linkFontFamily;
			dr=dr +'" Size="'+linkFontSize+'">&nbsp;<A HREF="'+sI[i][1]+'" target="';
			dr=dr +sI[i][2]+'" CLASS="ssmItems">'+sI[i][0]+'</A></FONT></DIV></LAYER></ILAYER></TD>';
			document.write(dr)
		}
		if(sI[i][4]!="no"&&barBuilt==0){buildBar();barBuilt=1}
		if(sI[i][4]!="no"){document.write('</TR>')}
	}  // END for(i=0;i<sI.length;i++)
	document.write('</tbody></table>')
	if (NS6){document.write('</TD></TR></TABLE>')}
	if (IE||NS6) {document.write('</DIV></DIV>');setTimeout('initSlide();', 1)}
	if (NS) {document.write('</LAYER></ILAYER></LAYER>')}
}  //END Function buildMenu()

function addHdr(name, cols, endrow){sI[sI.length]=[name, '', '', cols, endrow]}

function addItem(name, link, target, cols, endrow){if(!link)link="javascript://";sI[sI.length]=[name, link, target, cols, endrow]}

//-->

//\//////////////////////////////////////////////////////////////////////////////////
//\ ssmItems.js
//\//////////////////////////////////////////////////////////////////////////////////


<!--
/*
Configure menu styles below
NOTE: To edit the link colors, go to the STYLE tags and edit the ssmItems colors
*/
ID1="basessm";   //ID1
ID2="thessm";    //ID2
ID3="basessm1";
ID4="basessm2";



YOffset=40; // no quotes!!
staticYOffset=0; // no quotes!!
XOffset=-2; // no quotes!!
slideSpeed=20 // no quotes!!
waitTime=100; // no quotes!! this sets the time the menu stays out for after the mouse goes off it.
menuBGColor="#555555";
menuIsStatic="no";
menuWidth=150; // Must be a multiple of 10! no quotes!!
menuCols=2;
//////// headers
hdrFontFamily="Arial";
hdrFontSize="8pt";
hdrFontColor="black";
hdrBGColor="#ffffff";
hdrAlign="center";
hdrVAlign="center";
hdrHeight="10";
//////// Links
linkFontFamily='Arial';
linkFontSize="1";
linkBGColor="white";
linkOverBGColor="#FFFF99";
linkTarget="_new";
linkAlign="left";
//////// Side bar
barBGColor="#000000";
barFontFamily="Arial";
barFontSize="1";
barFontColor="#339900";
barVAlign="center";
barWidth=9; // no quotes!!
barText='NETz Menu' // <IMG> tag supported, Ex: '<img src="some.gif" border=0>'

// ssmItems[...]=[name, link, target, colspan, endrow?] - leave 'link' and 'target' blank to make a header


//-->

