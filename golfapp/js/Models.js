var DatabaseModel = {
		_getTournaments: 
			function(tournament_id=-1) 
			{
				var ret;
				$.getJSON('/golfapp/services/getTournaments.php?id='+tournament_id, function(data) {
					ret =  data.items;
				});
				return ret;
			},
		_getSideContests: 
			function(tournament_id=-1) 
			{
				var ret;
				$.getJSON('/golfapp/services/getTournamentSideContests.php?id='+tournament_id, function(data) {
					ret = data.items;
				});
				return ret;
			}
}

var TournamentModel = {
		init: 
			function(id) 
			{
				this.tournament_id = id;			
			},

};

var TournamentListModel = {
		init: 
			function() 
			{
				this.tournaments_ = this._getTournaments();
			},
		_getTournaments: 
			function() 
			{
				
			},
};