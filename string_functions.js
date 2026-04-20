
function fixit(myString)
{
// this will remove the single quote and change it to `
	var myString2;

	var foundAtPosition = 0;

	while ( foundAtPosition != -1)
	{
   		foundAtPosition = myString.indexOf("'");
   		if (foundAtPosition != -1)
   		{
			myString2 = myString.substring(0,foundAtPosition) + "`" + myString.substring(foundAtPosition + 1, myString.length) ;
			myString = myString2;
      			foundAtPosition++;
   		}
	}
	return myString;
}
function lz_replace(myString,searchstr,replacestr)
{
//this will replace one string with another
	var myString2;

	var foundAtPosition = 0;

	while ( foundAtPosition != -1)
	{
   		foundAtPosition = myString.indexOf(searchstr);
   		if (foundAtPosition != -1)
   		{
			myString2 = myString.substring(0,foundAtPosition) + replacestr + myString.substring(foundAtPosition + searchstr.length, myString.length) ;
			myString = myString2;
      			foundAtPosition++;
   		}
	}
	return myString;
}
function get_file_from_path(path)
{
// gets the file name from a full path/filename
	var i;
	var newstr = "";
	for (i = path.length; i > 0; i--)
	{
		
		if (path.substring(i-1,i) != "\\") 
		{
			newstr = path.substring(i-1,i) + newstr;
		}
		else
		{
			return newstr;
		}
		
	}
	return newstr;
}
