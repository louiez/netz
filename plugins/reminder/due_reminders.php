<div  id="display_div" style="z-index: 10;
        background-color: blue;
        position: absolute; 
        left: -500px; 
        top: 150px; 
        width: 400px; 
        padding: 10px; 
        border: black 3px solid" >
        <div onmousedown="grab(this.parentNode)" 
		onmouseover="document.body.style.cursor = 'move';"
		onmouseout="document.body.style.cursor = 'default';"
		style="position:relative;
                background-color: darkblue;
                left: 0px;
                top: -10px;
                width: 100%;
                height: 15px; 
                border: black 1px solid">
        </div>
                <center><b> Reminders </b></center>
        <table id="display_table"  style="color: white;border-width: 2px;border-color: white;border-style: inset; width: 100%" >
	<thead>
		<tr><td style="text-align:center; font-weight:bold">
			Reminder name
		</td><td style="text-align:center; font-weight:bold">
			Date and Time
                </td><td style="text-align:center; font-weight:bold; white-space:nowrap;">
                        &nbsp;
                </td><td style="text-align:center; font-weight:bold; white-space:nowrap;">
                        &nbsp;
                </td>
		</tr>
	</thead>
	<tbody>
		<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		<!--  dummy -->	
	</tbody>
        </table>
	<br>
        <center>Description</center>
	<br>
        <textarea onkeydown="this.blur();" rows="5" COLS="20" style="width:100%" id="alert_description"name="alert_description"></textarea>
	<br><br>
			<center>
                        <input class="button" 
                                type="button" 
                                name="close" 
                                value="Close" 
                                onclick="show_hide(document.getElementById('display_div'), 'dismiss')">
			</center>
</div>
