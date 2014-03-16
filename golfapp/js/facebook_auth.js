//////////////////////////
//
// Authentication
// See "Logging the user in" on https://developers.facebook.com/mobile
//
//////////////////////////

//Prompt the user to login and ask for the 'email' permission
function promptLogin() 
{
	FB.login(null, {scope: 'email'});
}

//See https://developers.facebook.com/docs/reference/javascript/FB.logout/
function logout() 
{
	FB.logout();
}

/**
 * Callback function used to handle the login event.
 */
function onFBLogin()
{
	console.log("onFBLogin");
	window.location.reload();
}

/**
 * Callback function used to handle the logout event.
 */
function onFBLogout()
{
	console.log("onFBLogout");
	
	for (var prop in KEYS)
	{
		Utils.removeStorage(KEYS[prop]);
	}

	$.mobile.changePage($("#page_selectTournament"));
	window.location.reload();
}

/**
 * Callback function used to monitor the login status of the user.
 * 
 * @param response Response parameter is described here: https://developers.facebook.com/docs/reference/javascript/FB.getLoginStatus/
 */
function onFBLoginStatus(response)
{
	console.log("onFBLoginStatus");

	if (response.status == 'connected')
	{
		document.body.className = 'connected';

		var fbuser = Utils.getStorage(KEYS.FBUSER);
		if (fbuser != null) 
		{
			console.log(fbuser);
			$('.classUserInfo').text("Welcome " + fbuser['first_name']);
		}
		else
		{
			onFBGetUserInfo();
		}

		$('#login').addClass('invisible');
		$('#logout').removeClass('invisible');
		$('[id=logout]').append('<a id="logoutButton" href="#" data-role="button" data-mini="true" data-inline="true" onClick="FB.logout();">Logout</a>');
		$('[id=logoutButton]').button();
	}
	else
	{	
		document.body.className = 'not_connected';
				
		for (var prop in KEYS)
		{
			console.log("KEYS: "+prop+ " => "+KEYS[prop]);
			Utils.removeStorage(KEYS[prop]);
		}		

		$('#login').removeClass('invisible');
		$('#logout').addClass('invisible');
		$('.classLogoutButton').remove('logoutButton');
	}	

	console.log("onFBLoginStatus: className = " + document.body.className);
}

/** 
 * 
 */
function onFBGetUserInfo()
{
	console.log("onFBGetUserInfo Begin.");

	FB.api('/me', function(response) {		
		// Copy the Facebook user object for later use
		Utils.setStorage(KEYS.FBUSER, response);
		$('.classUserInfo').text("Welcome " + response['first_name']);
	});
	FB.api('/me/accounts', function(response) {
		Utils.setStorage(KEYS.FBACCOUNTS, response);
	});
}