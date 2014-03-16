$('#tournamentListPage').live('pageinit', function(event) {
	getTournamentList();
});

var serviceURL = "/golfapp/services/";
var tournaments;


// $('#tournamentListPage').bind('pageinit', function(event) {
// 	getTournamentList();
// });

function getTournamentList() {
	console.log("getTournamentList");
	$.getJSON(serviceURL + 'getTournaments.php', function(data) {
		console.log("getJson");
		$('#tournamentList li').remove();
		tournaments = data.items;		
		$.each(tournaments, function(index, tournament) {
			var date = new Date(tournament.tournament_date * 1000);
			$('#tournamentList').append('<li><a href="#">' +
					'<h4>' + tournament.tournament_name + '</h4> ' +
					'<p>' + date.toDateString() + '</p>' +					
					'</a></li>');
		});
		$('#tournamentList').listview('refresh');
	});
}