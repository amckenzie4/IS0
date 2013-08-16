<?php
# This is php.

?>

<script type="text/javascript">

	//declare request variable
	var request = null;

	// Retreive and output data if it's ready
	function dataToDiv(divId,message)
	 { // Begin function dataToDiv
      	document.getElementById(divId).innerHTML = message;
    } // End function dataToDiv

   // Declare our function for setting the AJAX
   // XMLHttpRequest to variable request.
	function setAJAX() 
	 { // Begin function setAJAX
      // Set request variable for most modern
      // browsers
		if(window.XMLHttpRequest)
       { // code for IE7+, Firefox, Chrome, Opera, Safari
	      request = new XMLHttpRequest();
	      return true;
       }
      // Set request variable for older IE versions.
      else if(window.ActiveXObject)
       { // cod for IE5 and IE6
         request =  new ActiveXObject("Microsoft.XMLHTTP");
        return true;
       }
      // Return false if we cannot set the request
      // variable.
      else 
		 {
			return false;
		 }
	 } // End function setAJAX()


	// declare function testValue.  This is used for checking
	// human-input variables
	function testValue(input_name,input_value,divId)
	 { // Begin function testValue
		if(setAJAX())
		 { // Begin if setAJAX
			// Set the URL
			input_value = input_value.replace(/ /g, "%20");
			var url = "<?php echo $mod_to_include; ?>/scripts/test_value.php?input=" + input_name + "&value=" + input_value;

//document.getElementById("debug").innerHTML = "url is " + url + "<br>divId is " + divId;

         // Set the onreadystatechange to find function
         // getData when ready.
			request.onreadystatechange=function()
			 {
				if (request.readyState==4 && request.status==200)
				 {
					dataToDiv(divId,request.responseText);
				 }
			 }


 
         // Open and send the request.
         request.open("GET", url, true);
         request.send();
//document.getElementById(divId).innerHTML = "test";
        } // End if setAJAX
        // Alert the user that we cannot do AJAX in their
        // browser.
        else 
			{
          alert('Your browser does not support ajax');
         }

	 } // End function testValue


// User created functions go here.

	// Declare the nextFree function
	function nextFree(subnet,divId)
	 { // Begin function nextFree
		// Check to see if the request variable can be set, and set it.
		if(setAJAX())
		 { // Begin if(setAJAX)
			if(subnet == "")
			 {
				document.getElementByID(divId).innerHTML = "";
			 }



			// When the data is ready, send it to dataToDiv
			request.onreadystatechange=function()
			 {
				if (request.readyState==4 && request.status==200)
				 {
					//dataToDiv(divId,request.responseText);
					document.getElementById('add_mod_form').elements.namedItem(divId).value = request.responseText;
				 }
			 }

			// Set the query URL
			var url = "modules/hosts/scripts/next_free.php?q="+subnet;
//document.getElementById("debug").innerHTML = "url is " + url;

			// Open and send the request
			request.open("GET",url,true);
			request.send();
		 } // End if(setAJAX)
		else { alert('Your browser does not support AJAX'); }
	 } // End function nextFree





</script>

<?php
