String.prototype.trim=function(){return this.replace(/^\s+|\s+$/g, '');};
function Val_Ip(ipt,gwt,maskt)
{
var ip_array;
var ip_bin;
var gw_array;
var gw_bin;
var mask_array;
var mask_bin;
var maskcider = "";
var check = "";
var ip = ipt.trim();
var gw = gwt.trim();
var mask = maskt.trim()
        // Check that IP and Gateway and not the same
        if (ip == gw){alert("IP and gateway the same"); return 0;}
        // Check that all three vars have some data
        if (ip == "" || gw == "" || mask == ""){alert("Incomplete Info"); return 0;}
        // Now check if the data is Numeric
        if (IsNumeric(ip) == false || IsNumeric(gw) == false || IsNumeric(mask) == false){alert("Invalid Info"); return 0;}

        // Split the IP address into octets and assign to array
        ip_array=ip.split(".");
        // Create a binary string of the IP
        ip_bin=binfromdec(ip_array[0]) + binfromdec(ip_array[1]) + binfromdec(ip_array[2]) + binfromdec(ip_array[3]);

        // Split the gw address into octets and assign to array
        gw_array=gw.split(".");
        // Create a binary string of the gw
        gw_bin=binfromdec(gw_array[0]) + binfromdec(gw_array[1]) + binfromdec(gw_array[2]) + binfromdec(gw_array[3]);

        // Split the mask into octets and assign to array
        mask_array=mask.split(".");
        // Create a binary string of the mask
        mask_bin=binfromdec(mask_array[0]) + binfromdec(mask_array[1]) + binfromdec(mask_array[2]) + binfromdec(mask_array[3]);

        // see if the conversion to Binary caused errors ie: number > 255
        if (ip_bin.indexOf("err!") != -1){check=check+"Invalid IP address\n"; return;}
        if (gw_bin.indexOf("err!") != -1){check=check+"Invalid gateway address\n"; return;}
        if (mask_bin.indexOf("err!") != -1){check=check+"Invalid Mask\n";return;}

        // show message box with Binary strings
        alert(ip_bin+"\n"+gw_bin+"\n"+mask_bin);



        maskcider = getcider(mask_bin);

        if (maskcider != "err!")
        {
                alert(maskcider + " Bit mask");
        }
        else
        {
                alert("Invalid Mask");
                return;
        }
        //Now lets check the IP address and gateway to see if they are valid for the mask
        var all_one="";
        var all_zero="";
        // create host part all zero and all ones for check below
        for (i=0; i < (gw_bin.length - maskcider) ; i++){
                all_one=all_one+"1";
                all_zero=all_zero+"0";
        }
        // check if gateway host part is all ones
        if (gw_bin.substring(maskcider) == all_one) {
                //alert("Gateway not valid");
                check=check+"Gateway Listed is unusable (all ones)\n";
        }
        // check if gateway is host part is all zeros
        if (gw_bin.substring(maskcider) == all_zero) {
                //alert("Gateway can't be zero");
                check=check+"Gateway can't be zero\n"
        }
        // // check if IP host part is all ones
        if (ip_bin.substring(maskcider) == all_one) {
                //alert("IP not valid");
                check=check+"IP Listed is unusable (all ones)\n"
        }
        // check if IP is host part is all zeros
        if (ip_bin.substring(maskcider) == all_zero) {
                //alert("IP can't be zero");
                check=check+"IP can't be zero\n"
        }
        if (check == ""){
                if (gw_bin.substring(0,maskcider) == ip_bin.substring(0,maskcider)){
                        alert("IP Info ok");
                }else{
                        check=check+"IP and Gateway on different networks"
                        alert(check);
                }
        }else{
                alert(check);
        }


}

function getcider(mask)
{
   var ciderbits = 0;
   var i ;
        for( i=0; i < mask.length ; i++)
        {
                if (mask.charAt(i,1) == "1"){ciderbits++;}else{break;}
        }

        for (i=ciderbits + 1; i< mask.length;i++)
        {
                if (mask.charAt(i,1) == "1"){return "err!";}
        }
        return ciderbits;
}

function binfromdec(num) 
{
        var bit8=0,bit7=0,bit6=0,bit5=0,bit4=0,bit3=0,bit2=0,bit1=0;
        if (num > 255) { return ("err!") }
        if (num & 128) { bit8 = 1 }
        if (num & 64) { bit7 = 1 }
        if (num & 32) { bit6 = 1 }
        if (num & 16) { bit5 = 1 }
        if (num & 8) { bit4 = 1 }
        if (num & 4) { bit3 = 1 }
        if (num & 2) { bit2 = 1 }
        if (num & 1) { bit1 = 1 }
        return (""+bit8+bit7+bit6+bit5+bit4+bit3+bit2+bit1);
}

function IsNumeric(sText)
{
   var ValidChars = "0123456789.";
   var IsNumber=true;
   var Char;
        if(sText == ""){return false}
        for (i = 0; i < sText.length && IsNumber == true; i++) 
        { 
                Char = sText.charAt(i); 
                if (ValidChars.indexOf(Char) == -1) 
                {
                        IsNumber = false;
                }
        }
        return IsNumber;
}

function CheckPublic(ipaddr)
        {
        if (Left(ipaddr,3) == "10." || Left(ipaddr,7) == "192.168" || Left(ipaddr,4) == "127.")
                {
                CheckPublic = "False";
                return CheckPublic;
                }
        switch(Left(ipaddr,6))
                {
                case "172.16":
                        return "False";
                        break;
                case "172.17.":
                        return "False";
                        break;
                case "172.18.":
                        return "False";
                        break;
                case "172.19.":
                        return "False";
                        break;
                case "172.20.":
                        return "False";
                        break;
                case "172.21.":
                        return "False";
                        break;
                case "172.22.":
                        return "False";
                        break;
                case "172.23.":
                        return "False";
                        break;
                case "172.24.":
                        return "False";
                        break;
                case "172.25.":
                        return "False";
                        break;
                case "172.26.":
                        return "False";
                        break;
                case "172.27.":
                        return "False";
                        break;
                case "172.28.":
                        return "False";
                        break;
                case "172.29.":
                        return "False";
                        break;
                case "172.30.":
                        return "False";
                        break;
                case "172.31.":
                        return "False";
                        break;
        }
        return "True";

}

function Left(str, n)
        /***
                IN: str - the string we are LEFTing
                    n - the number of characters we want to return

                RETVAL: n characters from the left side of the string
        ***/
        {
                if (n <= 0)     // Invalid bound, return blank string
                {
                        return "";
                }
                else if (n > String(str).length)   // Invalid bound, return
                {
                        return str;                // entire string
                }
                else // Valid bound, return appropriate substring
                {
                        return String(str).substring(0,n);
                }
        }
