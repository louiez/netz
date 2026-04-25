var cnt = 0;
var xmlHttp = new Array();

// Function to update the ping status lights
function ping_lights(name,ip,active){
	$.get("ping-live.php?ip="+ip+"&t="+((new Date()).valueOf()),function(data,status){
			if (trim(data) != "-1"){
			document.getElementById(name).src="img/online.gif";
			document.getElementById(name+'x').innerHTML = "&nbsp;&nbsp;("+data+"ms)";
			}else{
			document.getElementById(name).src="img/down.gif";
			document.getElementById(name+'x').innerHTML = "";
			}
			});
}
function pause(milliseconds) {
	var dt = new Date();
	while ((new Date()) - dt <= milliseconds) { /* Do nothing */ }
}

/* 
   Function to call functions in valip.js
 */
function validate_ip(){
	// Load WAN text box objects
	wan_ip_text_box=document.getElementById("txtpublicipaddress");
	wan_gateway_text_box=document.getElementById("txtdefaultgateway");
	wan_netmask_text_box=document.getElementById("txtnetmask");
	// Test if IP is Private (RFC 1918)
	if (CheckPublic(wan_ip_text_box.value) == "False"){
		alert ("Private ADDRESSes !!!!");
	}
	// Now check if IP info is valid
	Val_Ip(wan_ip_text_box.value,wan_gateway_text_box.value,wan_netmask_text_box.value);
}       

var change_made = false;
function on_change_made(e){
	// change font color and background cols on changed field
	change_made = true;
	e.style.color = "red";
	e.style.backgroundColor = "yellow";
	// get the site id
	site=document.getElementById("txtstorenumber").value
		// build URL adding random date to end to stop browser caching... Yes IE we are talking about you
		x="site_set_get_edit_flag.php?site="+site+"&edit=1&t="+((new Date()).valueOf());
	// jquery get function
	$.get(x);
	return true;
}

function ping_store(ip){
	window.open("ping.php?tamsip=" + ip,"","width=450,height=400");
}

function show_group(){
	var pingstring = "";
	if (document.getElementById("ping1").checked == true) {pingstring = "&Ping=ON"}
	//window.location = "ops.php?group=" + escape(document.getElementById("txtgroup").value) + pingstring ;
        window.location = "ops.php?group=" + encodeURIComponent(document.getElementById("txtgroup").value) + pingstring;
}

function add_new(){
	// query an invalid store number to get a blank page
	window.location = "add-site.php";
}

function delete_record(){
	var ans;
	ans = prompt("Are you sure you want to delete this record ? \n Type Upper case  YES to confirm Deletion","");
	if (ans == "YES") {
		myform.txtdelete.value = "yes";
		myform.submit();
		return true;
	}
	return false;
}

function show_image(url){
	var features;
	features='width=' + (screen.availWidth -30) + ',height=';
	features = features + (screen.availHeight - 30) + ',scrollbars=yes' + ',left=0,top=0,resizable=yes';
	window.open(url,"",features);
	return false;
}


function on_load_check(){
	document.myform2.txtadvancedsearch.focus();
}



function getBrowserInfo() {
  const browserData = {
    name: 'Unknown',
    version: 'Unknown',
    platform: navigator.platform,
  };

  // Use navigator.userAgentData if available (newer browsers)
  if (navigator.userAgentData) {
    browserData.name = navigator.userAgentData.brands[0].brand;
    browserData.version = navigator.userAgentData.brands[0].version;
  }
  // Fallback to userAgent if userAgentData is not available
  else if (navigator.userAgent) {
    const userAgent = navigator.userAgent;
    if (userAgent.includes("Chrome")) {
      browserData.name = "Chrome";
      browserData.version = userAgent.match(/Chrome\/([0-9\.]+)/)[1];
    } else if (userAgent.includes("Firefox")) {
      browserData.name = "Firefox";
      browserData.version = userAgent.match(/Firefox\/([0-9\.]+)/)[1];
    } else if (userAgent.includes("Safari")) {
      browserData.name = "Safari";
      browserData.version = userAgent.match(/Version\/([0-9\.]+)/)[1];
    } else if (userAgent.includes("Edge")) {
      browserData.name = "Edge";
      browserData.version = userAgent.match(/Edg\/([0-9\.]+)/)[1];
    }
  }

  return browserData;
}

console.log(getBrowserInfo());

function sync_conn_type(e){
	document.getElementById('conn-type-text').value = e.value;
	document.getElementById('conn-type-select').value = e.value;
	document.getElementById('conn-type-select').style.color = "red";
	document.getElementById('conn-type-select').style.backgroundColor = "yellow";
}

function toggleMenu(currMenu,menuSelect){
	if (document.getElementById) {
		thisMenu = document.getElementById(currMenu)
			thisSelect = document.getElementById(menuSelect)
			if (thisMenu.style.display == "none") {
				thisMenu.style.display = "block" ;
				thisSelect.innerHTML = "[-]";
			}else if (thisMenu.style.display == "block"){
				thisMenu.style.display = "none" ;
				thisSelect.innerHTML = "[+]";
			}
		if (window.XMLHttpRequest){
			// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}else{
			// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		site=document.getElementById("txtstorenumber").value
			m1=document.getElementById('menu1').style.display;
		m2=document.getElementById('menu2').style.display;
		m3=document.getElementById('menu3').style.display;
		m4=document.getElementById('menu4').style.display;
		m5=document.getElementById('menu5').style.display;
		x="save_menu_style.php?site="+site+"&m1="+m1+"&m2="+m2+"&m3="+m3+"&m4="+m4+"&m5="+m5+"&t="+((new Date()).valueOf());
		xmlhttp.open("GET",x,true);
		xmlhttp.send();
		return false 
	}else {
		return true 
	} 
}
function toggleDSLPasswordView(){
	if (document.getElementById) {
		showit = document.getElementById("dslpasshidden").style;
		hideit = document.getElementById("dslpassshow").style;
		if (showit.display == "none") {
			showit.display = "block" ;
			hideit.display = "none";
		}else if (showit.display  == "block"){
			showit.display = "none" ;
			hideit.display = "block";
			$.get("log_password_access.php?view=DSL&site="+site);
		}
		return false ;
	}
	return false;
}

function toggleRouterPasswordView(){
	if (document.getElementById) {
		showit = document.getElementById("routerpasshidden").style;
		hideit = document.getElementById("routerpassshow").style;
		if (showit.display == "none") {
			showit.display = "block" ;
			hideit.display = "none";
		}else if (showit.display  == "block"){
			showit.display = "none" ;
			hideit.display = "block";
			$.get("log_password_access.php?view=Router&site="+site);
		}
		return false ;
	}
	return false;
}
function toggleCPEPasswordView(){
	if (document.getElementById) {
		showit = document.getElementById("cpepasshidden").style;
		hideit = document.getElementById("cpepassshow").style;
		if (showit.display == "none") {
			showit.display = "block" ;
			hideit.display = "none";
		}else if (showit.display  == "block"){
			showit.display = "none" ;
			hideit.display = "block";
			$.get("log_password_access.php?view=CPE&site="+site);
		}
		return false ;
	}
	return false;
}

function reload_me()
{
	setTimeout("window.location.reload()",2000);
}

function openhelp(url)
{
	window.open( url, "","resizable=1,height=300,width=300");
}
function openmap(url)
{
	window.open( url, "","resizable=1,height=525,width=625");
}
function init_copy() {
	clip = new ZeroClipboard.Client();
	clip2 = new ZeroClipboard.Client();
	clip.setHandCursor( true );
	clip2.setHandCursor( true );
	clip.glue( 'd_clip_button' );
	clip2.glue( 'd_clip2_button' );
}

function enumForm(){
	// this is loaded at the bottom of the page 
	// in a script tag so that it can load DOM objects
	var lists = document.getElementsByTagName("INPUT");
	var holder ="";
	init_copy()
		// walk through feilds 
		for (var i = 0; i < lists.length; i++) {
			// see if it is a text box so we can grab the data
			if (lists[i].type == "text"){
				// Add the store number to the clipboard holder
				if (lists[i].title == "Store Number"){
					clip2.setText(tempstore = lists[i].value);
				}
				// if the text box has data
				if (lists[i].value != "" && lists[i].title != "") {
					// append data to variable
					tempname = lists[i].title;
					holder = holder + tempname + " - " +  lists[i].value + "\r\n";
				}
			}
		}
	// get the text from the select text boxes
	var sel = document.getElementsByTagName("SELECT");
	for (var i = 0; i < sel.length; i++) {
		if (sel[i].value != "" && sel[i].title != "") {
			holder = holder + sel[i].title + "-" + sel[i].value + "\r\n" ;
		}
	}i
	// set the text in the clipboard control
	clip.setText(holder);

}
// =============================================================//
//	this is all part of the open_message function below	//
// ==============================================================//
var message_window = [];

Array.prototype.has = function(value) {
	var i;
	for (var i in this) {
		if (i === value) {
			return true;
		}
	}
	return false;
};
function open_message(name,url,width,height,resizable,scrollbars){                                                                  
	var winRef;                                                                                                                 
	// Handle width, heith, resizable and scrollbarsas optional parms
	// If they are not passed the will default without error
	width = (typeof width === "undefined") ? "575" : width;
	height = (typeof height === "undefined") ? "775" : height;
	resizable = (typeof resizable === "undefined") ? "yes" : resizable;
	scrollbars = (typeof scrollbars === "undefined") ? "yes" : scrollbars;
	if (message_window.has(name)) {                                                                                             
		winRef = message_window[name];                                                                                      
	}                                                                                                                           

	if (winRef == null || winRef.closed){                                                                                                                                  
		message_window[name] = window.open(url,'','width='+width+',height='+height+',resizable='+resizable+',scrollbars='+scrollbars);                                                                                                                          
	}else{                                                                                                                      
		winRef.focus();                                                                                                     
	}                                                                                                                           
	return false;                                                                                                               
}                                                              

// =============================================================//
function dateToEepoch(date_string){
	if (date_string == ""){date_string ="01-01-1970 00:00:00"}
	zz=date_string.replace(":", " ");
	zz=zz.replace(":", " ");
	zz=zz.replace("-", " ");        
	zz=zz.replace("-", " ");
	dd= zz.split(" ");
	var datum = new Date(Date.UTC(dd[0],dd[1],dd[2],dd[3],dd[4],dd[5]));
	return (datum.getTime()/1000.0);
}
function trim(str) {
	var i;
	var ws;
	var	str = str.replace(/^\s\s*/, ''),
		ws = /\s/,
		i = str.length;
	while (ws.test(str.charAt(--i)));
	return str.slice(0, i + 1);

}
function check_edit(site){
	var lastChangeLoaded;
	var tmp;
	var tmp_dates ;
	var last_change;
	var edit_flag;
	var server_current_time;
	if (window.XMLHttpRequest){
		// code for IE7+, Firefox, Chrome, Opera, Safari
		var xmlhttp=new XMLHttpRequest();
	}else{
		// code for IE6, IE5
		var xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.onreadystatechange = function() {//Call a function when the state changes.
		if(xmlhttp.readyState == 4 && xmlhttp.status == 200) {
			// alert(xmlhttp.responseText);
			document.getElementById("temp_message").style.backgroundColor = "Yellow";
			document.getElementById("temp_message").style.color = "red";
			tmp = xmlhttp.responseText;
			/* the response text will be a string
			   seperated by "|" so we break it into an arry below */
			tmp_dates = tmp.split("|");
			if (trim(tmp_dates[0]) == ""){
				last_change=dateToEepoch(document.getElementById('pageLoadedDate').value);

			}else{
				last_change=dateToEepoch(trim(tmp_dates[0])) ;
			}

			// if we are not making a change and the Edit Flag (tmp_dates[1]) is set
			// in other words... someone is editing this record
			if (!change_made && tmp_dates[1] != ""){
				edit_flag=dateToEepoch(tmp_dates[1]);
				server_current_time = dateToEepoch(tmp_dates[2]);
				/* if someone starts a change... 
				   and then just refreshes the page or closes the browser
				   the EDIT_FLAG would not have been cleared
				   so we check if the 
				   EDIT_FLAG  (tmp_dates[1]) date/time plus 10 min 
				   is still greater than the server time
				   if it is... we still have an active change by another user 
				   and we lock this user from changes	*/
				if ((edit_flag+300) > server_current_time){
					noWrite=true;
					// be sure "save" element is valid to stop errors
					if (document.getElementById("save")){
						document.getElementById("save").disabled = true;
						document.getElementById("save").display = "none"
					}
					document.getElementById("deleterecord").disabled = true;
					document.getElementById("mon_link").innerHTML ="";
					// document.getElementById("temp_message").innerHTML = "Record Locked No change Allowed";
					document.getElementById("temp_message").innerHTML = "Record Locked by "+tmp_dates[3];
				}else{
					document.getElementById("temp_message").innerHTML = "";
				}

			}else{
				var foey = document.getElementById('pageLoadedDate').value;
				lastChangeLoaded = dateToEepoch(foey);
				document.getElementById("temp_message").innerHTML = "";
				// be sure "save" element is valid to stop errors
				if (document.getElementById("save")){
					document.getElementById("save").disabled = false;
				}
				if (document.getElementById("deleterecord")){document.getElementById("deleterecord").disabled = false;}
				// see if the current page is the same as the sever copy
				// if not... we reload the page
				if (lastChangeLoaded != last_change){
					// set the site number to the location so we reload the same site
					window.location.href = window.location.href+"?site="+site
						window.location.reload();
				}
			}
		}
	}
	x="site_set_get_edit_flag.php?site="+site+"&t="+((new Date()).valueOf());
	xmlhttp.open("GET",x,true);
	xmlhttp.send();
	setTimeout("check_edit('"+site+"')",5000);
}

function autoC(dbField,search,box,e){
	if (window.XMLHttpRequest){
		// code for IE7+, Firefox, Chrome, Opera, Safari
		xxmlhttp=new XMLHttpRequest();
	}else{
		// code for IE6, IE5
		xxmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	var fu = "";

	xxmlhttp.onreadystatechange = function() {//Call a function when the state changes.
		if(xxmlhttp.readyState == 4 && xxmlhttp.status == 200) {
			var dbRtn = xxmlhttp.responseText; 
			var sob  = dbRtn.split('|');
			for (var i = 0; i < sob.length; i++) {
				fu += sob[i] + "\n";
				// dbValues[i]= sob[i];


			}
			document.getElementById("temp_message").innerHTML =fu ;
		}
	}
	x="auto_complete.php?field="+dbField+"&search="+search+"&t="+((new Date()).valueOf());
	xxmlhttp.open("GET",x,true);
	xxmlhttp.send();

	return true;
}

function window_close(){
	if (change_made){
		if (window.XMLHttpRequest){
			// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
		}else{
			// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		x="site_set_get_edit_flag.php?site="+site+"&edit=2&t="+((new Date()).valueOf());
		xmlhttp.open("GET",x,false);
		xmlhttp.send();
		pause(1000);		

	}
}
