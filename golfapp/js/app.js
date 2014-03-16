$(document).one("mobileinit", function() {
	$(document).one("pageinit", onPageInit);
});

// All Storage Keys are stored in an object so that they can be removed upon logout
var KEYS = {
		USER: "dfwlaogolf.user",
		FBUSER: "dfwlaogolf.fbuser",
		FBACCOUNTS: "dfwlaogolf.fbaccounts",
		TOURNAMNET: "dfwlaogolf.tournament",
		COURSE: "dfwlaogolf.course",
		GROUP: "dfwlaogolf.group"
};

function onShowPageSelectTournament() 
{
	console.log("onShowPageSelectTournament");

	var fbuser = Utils.getStorage(KEYS.FBUSER);
	var currentDate = new Date();
	var year = currentDate.getFullYear();
	$('#page_selectTournament_header').text(year + " Tournaments");
	$.getJSON('/golfapp/services/getTournaments.php', function(data) {

		$('#tournamentList li').remove();

		// Add List dividers
		$('#tournamentList').append('<li data-role="list-divider" id="upcomingTournaments">Future Tournaments</li>');
		$('#tournamentList').append('<li data-role="list-divider" id="previousTournaments">Past Tournaments</li>');

		var tournaments = data.items;
		for (var i in tournaments)
		{
			var tournament = tournaments[i];
			date = new Date(tournament.tournament_date * 1000);

			// Separate the tournaments into the proper list dividers
			if (Utils.dateCompare(date, currentDate) >= 0)
			{
				$('#upcomingTournaments').after(
						'<li id='+tournament.tournament_id+'><a href="#page_selectUser">' +

//						FIXME - Make this fit on a mobile screen
//						'<div class="ui-grid-a">'+
//						'<div class="ui-block-a" style="text-align:left;border-right-style:solid;border-color:grey;border-width:2px">'+date.getDate()+' '+Utils.getFullMonth(date)+'</div>'+
//						'<div class="ui-block-b" style="text-align:left"><h3>'+tournament.tournament_name+'</h3></div>'+
//						'</div>'+				
						
						'<h3>' + tournament.tournament_name + '</h3> ' +
						'<p>' + $.datepicker.formatDate('DD, MM d, yy', date) + '</p>' +
				'</a></li>');
			}
			else
			{
				$('#previousTournaments').after(
						'<li id='+tournament.tournament_id+'><a href="#page_tournamentResults">' +
						
//						FIXME - Make this fit on a mobile screen
//						'<div class="ui-grid-a">'+
//						'<div class="ui-block-a" style="text-align:left;border-right-style:solid;border-color:grey;border-width:2px"><div class="ui-bar">'+date.getDate()+'<br>'+Utils.getFullMonth(date)+'</div></div>'+
//						'<div class="ui-block-b" style="text-align:left"><div class="ui-bar"><h3>'+tournament.tournament_name+'</h3></div></div>'+
//						'</div>'+			
						
						'<h3>' + tournament.tournament_name + '</h3> ' +
						'<p>' + $.datepicker.formatDate('MM d, yy', date) + '</p>' +
				'</a></li>');
			}

			// Add click event listener to each item with corresponding tournament data
			$('#'+tournament.tournament_id).click(tournament, function(event) {
				console.log('Tournament clicked was '+event.data.tournament_name);
				console.log(event.data);
				Utils.setStorage(KEYS.TOURNAMENT, event.data);
			});
		}
		$('#tournamentList').append('<li data-role="list-divider"></li>');
		$('#tournamentList').listview('refresh');
	});
}

function onShowPageSelectUser()
{
	console.log("onShowPageSelectUser");

	// Check if Facebook email is mapped to a user id
	// If mapped, go directly to enter scores page
	// If not mapped, display list of users for selection
	// If mapped, but user needs to change id ... provide a button
	// If not mapped, store user id in database.

	var isUserMapped = false;
	var fbuser = Utils.getStorage(KEYS.FBUSER);
	var tournament = Utils.getStorage(KEYS.TOURNAMENT);

	$('#selectUserText').remove();
	
	console.log(fbuser);
	// Means user is logged in
	if (fbuser != null)
	{
		console.log("Name: "+fbuser['first_name']);
		console.log("Email: "+fbuser['email']);
		$.getJSON('/golfapp/services/getUserFromFacebook.php?id=' + fbuser['email'], function(data) {
			console.log(data.items.length + " Users mapped to email " + fbuser['email']);
			if (data.items.length == 0)
			{
				isUserMapped = false;
				$('#page_selectUser_title').text('Select User Name');
				$('#selectUserText').append(
						'<p>This looks like your first time logging in. Please select your user name below so that we can properly track your tournament score.</p>')
			}
			else
			{
				user = data.items[0];
				if (user.fb_email == fbuser['email'])
				{
					isUserMapped = true;
					console.log(user.username + " is mapped to email " + fbuser['email']);
					
					// User is logged in and facebook email is already mapped so immediately change
					// page to start round.
					// TODO: Change Page
				}
			}
		});	
	}
	// User is not logged in
	else
	{		
		var date = new Date(tournament.tournament_date * 1000);
		var currentDate = new Date();

		// Tournament Day has not yet arrived
		if (Utils.dateCompare(date, currentDate) > 0)
		{
			$('#selectUserText').append(
					'<p><h3>Tournament starts on '+$.datepicker.formatDate('DD, MM d, yy', date)+
					'. Below are the current players.</h3></p>');
		}
		// Tournament Day
		else if (Utils.dateCompare(date, currentDate) == 0)
		{
			$('#selectUserText').append(
					'<p><h3>Let\'s play golf!. '+
					'Please login if you are playing in the tournament today so you that you can view the live leaderboard or check back again tomorrow for tournament results.</h3></p>');
		}
		// This should not happen. Once tournament day has passed, the tournament results page should be shown.
		else
		{
			console.log("SNH 1.");
		}
	}
	
	if (tournament != null)
	{
		$.getJSON('/golfapp/services/getPlayersInTournaments.php?id=' + tournament.tournament_id, function(data) {
			
			$('#userList li').remove();
			var users = data.items;
			console.log(fbuser);
			for (var i in users)
			{				
				var u = users[i];		
				if (fbuser != null)
				{
					$('#userList').append('<li id=user-' + i + '><a href="#page_enterScore">' +
							'<h4>' + u.username + '</h4> ' +
							'<p>Flight ' + u.flight_name + ', Hcp: ' + u.signup_handicap + '</p>' +
					'</a></li>');

					// Add click event listener to each item with corresponding tournament data
					$('#user-'+i).click(u, function(event) {
						console.log('User clicked was '+event.data.username);
						console.log(event.data);
						Utils.setStorage(KEYS.USER, event.data);
					});
				}
				else
				{
					$('#userList').append('<li id=user-' + i + '><a href="#">' +
							'<h4>' + u.username + '</h4> ' +
							'<p>Flight ' + u.flight_name + ', Hcp: ' + u.signup_handicap + '</p>' +
					'</a></li>');					
				}
			}

			$('#userList').append('<li data-role="list-divider"></li>');
			$('#userList').listview('refresh');
		});
	}
}

function onShowPageEnterScore()
{
	console.log("onShowPageEnterScore");

	// id of this page is tournament id
	// uid is the user id
	var user = Utils.getStorage(KEYS.USER);
	var tournament = Utils.getStorage(KEYS.TOURNAMENT);
	if ((user != null) && (tournament != null))
	{
		$.getJSON('/golfapp/services/getPlayersInTournaments.php?id=' + tournament.tournament_id+'&uid='+user.user_id, function(data) {
			
			var user = data.items[0];
			$('.classTournamentName').text(tournament.tournament_name);
			$('.classCourseName').append('<center><h3>'+tournament.course_name+'</h3></center>');
			$('.classHoleNumber').text('1');
			$('.classHolePar').text('Par 4');
			$('.classUser1Name').append(user.username);
		});
	}
}

//-1 - a < b
//0 - a = b
//1 - b < a
function sortResultsByGross(a, b)
{
	return a.round_score_temp - b.round_score_temp;
}

function onShowPageTournamentResults()
{
	console.log("onShowPageTournamentResults");

	var tournament = Utils.getStorage(KEYS.TOURNAMENT);
	if (tournament != null)
	{
		$('#page_tournamentResults_title').text(tournament.tournament_name);

		var wIterator = new Object;		// winnersList Iterator
		var cIterator = new Object;		// currentsList Iterator
		var rIterator = new Object;		// resultsList Iterator
		var flightCounter = new Object;
		var flightsList;

		$('#leaderboardList li').remove();

		$('#leaderboardList').append('<li data-role="list-divider"><center>Leaderboard</center></li>');

//		// Get the side contests
//		$.getJSON('/golfapp/services/getTournamentSideContests.php?id='+tournament.tournament_id, function(data) {
//		var results = data.items;

//		var contests = new Array();
//		for (var i in results)
//		{
//		// 1 = Closest to the Pin
//		// 2 = Long Drive
//		// 3 = Hole in One
//		if (contests[results[i].contest_type] == null)
//		{
//		switch(results[i].contest_type)
//		{
//		case "1":
//		$('#contestsList').append('<li data-role="list-divider"><center>Closest to the Pin</cener></li>');
//		break;
//		case "2":
//		$('#contestsList').append('<li data-role="list-divider"><center>Longest Drive</cener></li>');
//		break;
//		case "3":
//		$('#contestsList').append('<li data-role="list-divider"><center>Hole In One</cener></li>');
//		break;
//		default:
//		console.log(i + ": Invalid contest type = " + results[i].contest_type);
//		break;
//		}

//		contests[results[i].contest_type] = results[i];
//		}
//		}			
//		});

		// Get the Flights
		// The Flight Names will become list dividers.
		$.getJSON('/golfapp/services/getTournamentFlights.php?id='+tournament.tournament_id, function(data) {
			flightsList = data.items;

			console.log("Flights");
			console.log(flightsList);

//			TODO: Format Side Contests
//			var flightStr = '<li data-role="list-divider">';
//			var numFlights = data.items.length;
//			switch (numFlights)
//			{
//			case 1:
//			flightStr += '<div class="ui-grid-a"><div class="ui-block-a">';
//			break;
//			case 2:
//			flightStr += '<div class="ui-grid-b">';
//			break;
//			case 3:
//			flightStr += '<div class="ui-grid-c">';
//			break;
//			case 4:
//			flightStr += '<div class="ui-grid-d">';
//			break;
//			case 5:
//			flightStr += '<div class="ui-grid-e">';
//			break;
//			default:
//			console.log('Too many flights: '+numFlights);
//			flightStr = '';
//			break;
//			}

			for (var i in flightsList)
			{
				$('#winnersList').append('<ul id="winnersList" data-role="listview" data-inset="true" data-filter="false" data-theme="d" data-divider-theme="d"></ul>');
				$('#winnersList').append('<li data-role="list-divider"><center>Flight '+flightsList[i].flight_name+' Winners</center></li>');
				$('#winnersList').append(
						'<li data-role="list-divider" id="w-'+flightsList[i].flight_name+'">'+
						'<div class="ui-grid-a">'+
						'<div class="ui-block-a">Gross</div>'+
						'<div class="ui-block-b">Net</div>'+
						'</div>'+
				'</li>');
				$('#leaderboardList').append(
						'<li data-role="list-divider" id="r-'+flightsList[i].flight_name+'">'+
						'<div class="ui-grid-c">' +
						'<div class="ui-block-a">Flight '+flightsList[i].flight_name+'</div>'+
						'<div class="ui-block-b">Handicap</div>'+
						'<div class="ui-block-c">Gross Score</div>'+
						'<div class="ui-block-d">Net Score</div>'+
						'</div>'+
				'</li>');

				// Set the first rIterator to be the flight dividers. Maintain a position pointer for each flight
				wIterator[flightsList[i].flight_name] = "w-"+flightsList[i].flight_name;
				rIterator[flightsList[i].flight_name] = "r-"+flightsList[i].flight_name;
				flightCounter[flightsList[i].flight_name] = 1;
			}

			$('#winnersList').listview('refresh');
			$('#leaderboardList').listview('refresh');
		});		

		$.getJSON('/golfapp/services/getTournamentResults.php?id='+tournament.tournament_id, function(data) {

			if (data.error != null)
			{
				$('#winnersList li').remove();
				$('#leaderboardList li').remove();

				$('#winnersList').append('<li>No tournament data available. Please check back again later.</li>');

				$('#winnersList').listview('refresh');
				$('#leaderboardList').listview('refresh');
			}
			else
			{
				var results = data.items;

				// Results will be an object containing the following fields:
				// results->users - array of users object
				// results->winners - object of objects { GROSS-flight_name }
				// results->sideContest - object of side contest winners

				console.log(tournament.tournament_id+": Number of users: "+results.users.length);
				console.log(results.users);
				if (results.users.length == 0)
				{
					$('#winnersList li').remove();
					$('#leaderboardList li').remove();

					$('#winnersList').append('<li>No Users found for this tournament.</li>');

					$('#winnersList').listview('refresh');
					$('#leaderboardList').listview('refresh');
				}
				else
				{			
					// Loop through flights list again, setting the winners for each flight
					for (var i in flightsList)
					{
						var fn = flightsList[i].flight_name;
						var gross = results.winners[tournament.tournament_id]['GROSS-'+fn];
						var net = results.winners[tournament.tournament_id]['NET-'+fn];

						console.log(fn+": Gross: "+gross.username+", Score: "+gross.round_score_temp);
						console.log(fn+": Net: "+net.username+", Score: "+(net.round_score_temp - net.signup_handicap));
						$('#'+wIterator[fn]).after(
								'<li id="w-names-flight-'+fn+'">'+
								'<div class="ui-grid-a">'+
								'<div class="ui-block-a">'+gross.username+'</div>'+
								'<div class="ui-block-b">'+net.username+'</div>'+
								'</div>'+
								'</li>' +
								'<li id="w-scores-flight-'+fn+'">'+
								'<div class="ui-grid-a">'+
								'<div class="ui-block-a">'+gross.round_score_temp+'</div>'+
								'<div class="ui-block-b">'+(net.round_score_temp - net.signup_handicap)+'</div>'+
								'</div>'+
								'</li>'							
						);
						wIterator[fn] = "w-scores-flight-"+fn;
					}

					results.users.sort(sortResultsByGross);			
					for (var i in results.users)
					{	
						var u = results.users[i];
						// Start at the very first position and simply insert players
						$('#'+rIterator[u.flight_name]).after(
								'<li id="r-'+u.username + '">' +
//								'<a href="#test">' +
								'<div class="ui-grid-c">' +
//								'<div class="ui-block-a" style="width=5%"><img height="20" src="../'+u.photo_file + '" border="0"/></div>'+
								'<div class="ui-block-a">'+u.username+'</div>' +
								'<div class="ui-block-b">'+u.signup_handicap+'</div>'+
								'<div class="ui-block-c">'+u.round_score_temp+'</div>'+
								'<div class="ui-block-d">'+(u.round_score_temp - u.signup_handicap)+'</div>'+
								'</div>'+
//								'<p>' + flightCounter[u.flight_name] + ': ' + u.username + ' - ' + u.round_score_temp + '</p> ' +
//								'</a>' +
						'</li>');				

						// Update the position for each flight to the current list "id"
						rIterator[u.flight_name] = 'r-'+u.username;
						flightCounter[u.flight_name]++;
					}
					$('#winnersList').listview('refresh');

					$('#leaderboardList').append('<li data-role="list-divider"></li>');
					$('#leaderboardList').listview('refresh');
				}
			}
		});
	}
}

function onPageInit()
{
	console.log("onPageInit");

	$("#page_selectUser").on("pageshow", onShowPageSelectUser);
	$("#page_selectTournament").on("pageshow", onShowPageSelectTournament);
	$("#page_enterScore").on("pageshow", onShowPageEnterScore);
	$("#page_tournamentResults").on("pageshow", onShowPageTournamentResults);

	$('.classTextNumbersOnly').keypress(
			function(event) 
			{
				// Allow only backspace (8) and delete (46)
				// Reference: http://www.cambiaresearch.com/articles/15/javascript-char-codes-key-codes
				if (event.keyCode != 46 && event.keyCode != 8) 
				{
					if (!parseInt(String.fromCharCode(event.which))) 
					{
						event.preventDefault();
					}
				}
			}
	);
}

window.fbAsyncInit = function() {
	FB.init({
		appId: '110981712265115',
		channelUrl: '//www.dfwlaogolf.com/golfapp/DfwLaoGolf_channel.html',
		status: true,
		cookie: true,
		xfbml: true,
		oauth: true
	});

	FB.Event.subscribe('auth.login', onFBLogin);
	FB.Event.subscribe('auth.logout', onFBLogout);	
	FB.getLoginStatus(onFBLoginStatus);
};

(function(d){
	var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
	if (d.getElementById(id)) 
	{
		console.log('Unable to find facebook-jssdk');
		return;
	}
	js = d.createElement('script'); js.id = id; js.async = true;
	js.src = "//connect.facebook.net/en_US/all.js";
	ref.parentNode.insertBefore(js, ref);
}(document));