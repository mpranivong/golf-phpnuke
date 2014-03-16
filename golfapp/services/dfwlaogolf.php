<?php

date_default_timezone_set('America/Chicago');

class PlayerDto {
	public $id;
	public $user_id;
	public $username;
	public $email;
	public $signup_id;
	public $signup_name;
	public $signup_email;
	public $signup_phone;
	public $signup_handicap;
	public $signup_ghin;
	public $signup_time;
	public $signup_group;
	public $flight_id;
	public $signup_gross_standing;
	public $signup_net_standing;
	public $tournament_id;
	public $player_handicap_temp;
	public $player_member_date;
	public $player_member;
	public $fb_email;
	public $round_score_temp;
	public $round_id;
	public $round_score_diff;
	public $round_putts;
	public $round_par3;
	public $round_par4;
	public $round_par5;
	public $round_girs;
	public $round_fairways;
	public $round_added_by;
};

class TournamentDto {
	public $id;
	public $players;
};

class DfwLaoGolf {

	private $sql_;

	public function __construct($dsn, $username=null, $password=null, $driver_options=array())
	{	
		$this->sql_ = new PDO($dsn, $username, $password, $driver_options);
	}

	public function getUserFromFacebook($fb_email)
	{
		if ($fb_email == null)
		{
			echo '{"error":{"text": "Facebook email cannot be null."}}';
			return;		
		}

		$sql = 'select user_id from nuke_golf_players where fb_email=\''.$fb_email.'\'';

		$ret = $this->query($sql, null, $error);
		echo '{"items":'. json_encode($ret) .'}';
	}

	public function getPlayersInTournament($tournament_id=null, $userid=-1, $exclude_list=null)
	{
		if ($tournament_id == null)
		{
			echo '{"error":{"text": "Tournament id cannot be null."}}';
			return;
		}

		$sql = 'select u.username, u.user_email, '.
				'gts.*, IF(ISNULL(gp.player_handicap_temp),99,gp.player_handicap_temp) player_handicap_temp, '.
				'gp.player_member_date, gp.player_member, gp.fb_email, '.
				'gr.round_score_temp, gr.round_id, gr.round_score_diff, gr.round_putts, gr.round_par3, gr.round_par4, gr.round_par5, '.
				'gr.round_girs, gr.round_fairways, gr.round_ryder_points, gr.round_added_by, '.
				'gr2.round_score_temp round_score_temp2, gr2.round_id round_id2, '.
				'UNIX_TIMESTAMP(signup_time) as signup_time_unix, flight_net_awards, flight_gross_awards, '.
				'player_handicap_temp, gtf.teebox_id, UNIX_TIMESTAMP(player_member_renew_date) player_member_renew_date_unix, '.
				'UNIX_TIMESTAMP(player_member_date) player_member_date_unix, '.
				' gtf.flight_name, gp.fb_email '.
				' from nuke_golf_tournament_signups gts '.
				'left join nuke_users u on u.user_id=gts.user_id '.
				'left join nuke_golf_rounds gr on (gr.user_id=gts.user_id and gr.tournament_id=gts.tournament_id and gr.user_id > 0) '.
				'left join nuke_golf_rounds gr2 on (gr2.signup_id=gts.signup_id and gr2.tournament_id=gts.tournament_id and gr2.signup_id > 0) '.
				'left join nuke_golf_tournament_flights gtf on gtf.flight_id=gts.flight_id '.
				'left join nuke_golf_players gp on gp.user_id=gts.user_id '.
				'where gts.tournament_id='.$tournament_id.' and u.username is not null and gts.signup_withdraw = 0';

		if ($userid != -1)
		{
			$sql .= ' and u.user_id = '.$userid;
		}

		$sql .= ' group by gts.signup_id';
		$sql .= ' order by signup_group, gtf.flight_name, u.username';

		$ret = $this->query($sql, $error);
		if ($ret != null)
		{
			echo '{"items":'. json_encode($ret) .'}';
		}
		else
		{
			echo '{"error":{"text":'. $error .'}}';
		}		
	}

	public function getTournaments($id=-1)
	{
		$currentyear = date("Y");

		// Same as DFWLaoGolf.com
		// $sql = 'select tournament_results, tournament_id, tournament_name, UNIX_TIMESTAMP(tournament_date) as tournament_date, 
		//         course_name, course_url, UNIX_TIMESTAMP(tournament_deadline) tournament_deadline, tournament_photo_url 
		//         from '.$user_prefix.'_golf_tournaments gt 
		//         left join '.$user_prefix.'_golf_courses gc on gc.course_id=gt.course_id 
		//         left join '.$user_prefix.'_golf_course_teeboxes gct on gct.teebox_id=gt.teebox_id ';
		// $sql .= 'where year(tournament_date) = '. $currentyear;
		// $sql .= ' order by tournament_date';

		// Only grab the latest tournament based on the current date
		// Debug Only
// 		$sql = 'select curdate(), month(curdate()), year(curdate()), month(tournament_date), year(tournament_date), tournament_date as td, tournament_id, tournament_name, UNIX_TIMESTAMP(tournament_date) as tournament_date, course_name, gt.course_id'.
//         ' from nuke_golf_tournaments gt'.
//         ' left join nuke_golf_courses gc on gc.course_id=gt.course_id';

		$sql = 'select gt.tournament_id, gt.tournament_name, gt.tournament_deadline, '.
				'gt.tournament_date as unformatted_date, '.
				'gt.tournament_format, '.
				'UNIX_TIMESTAMP(gt.tournament_date) as tournament_date, '.
				'gc.course_name, '.
				'gt.course_id '.
				'from nuke_golf_tournaments gt '.
				'left join nuke_golf_courses gc on gc.course_id=gt.course_id ';

		// Testing
		if ($id == -1)
		{
			// Select only tournaments for the current year up to the current month
// 			$sql .= 'where (UNIX_TIMESTAMP(tournament_date) !=0) and (year(curdate()) = year(tournament_date)) and (month(curdate()) >= month(tournament_date)) ';
			$sql .= 'where (UNIX_TIMESTAMP(tournament_date) !=0) and (year(curdate()) = year(tournament_date)) ';
					
		}
		else
		{
			$sql .= 'where tournament_id = '.$id;
		}

		// Most current tournament should be at the top
		$sql .= ' order by tournament_date desc';

		$ret = $this->query($sql, $error);
		if ($ret != null)
		{
			echo '{"items":'. json_encode($ret) .'}'; 
		}
		else
		{
			echo '{"error":{"text":'. $error .'}}';
		}
	}
	
	public function getTournamentFlights($tournamentId = null)
	{
		if ($tournamentId == null)
		{
			echo '{"error":{"text":"Tournament id cannot be null"}}';
			return;
		}
		
		date_default_timezone_set('UTC');
		$currentYear = date('Y');
		
		$sql = 'SELECT '.
				'flight_name '.
				'FROM nuke_golf_tournament_flights '.
				'where tournament_id = '.$tournamentId;
		
		$ret = $this->query($sql, $error);
		if ($ret != null)
		{
			echo '{"items":'. json_encode($ret) .'}';
		}
		else
		{
			echo '{"error":{"text":'. $error .'}}';
		}
	}
	
	public function getTournamentResults($tournamentId = null)
	{
		$currentYear = date('Y');
		
		$sql = 'SELECT '.
				'gt.tournament_id, gt.tournament_name, gt.tournament_date, '.
				'gc.course_name, '.
				'gtf.flight_name, '.
				'u.name, u.username, u.user_email, '.
				'gts.signup_gross_standing, gts.signup_net_standing, gts.signup_handicap, '.
				'gr.round_score, gr.round_score_temp, '.
				'SUM(gs.score_value) as SCORE '.
				'FROM nuke_golf_tournaments gt '.
				'left join nuke_golf_courses gc ON gc.course_id = gt.course_id '.
				'left join nuke_golf_rounds gr on gr.tournament_id = gt.tournament_id '.
				'left join nuke_users u on u.user_id = gr.user_id '.
				'left join nuke_golf_scores gs on gr.round_id = gs.round_id '.
				'left join nuke_golf_holes gh on gh.hole_id = gs.hole_id '.
				'left join nuke_golf_tournament_signups gts on gts.tournament_id = gt.tournament_id and gts.user_id = u.user_id '.
				'left join nuke_golf_tournament_flights gtf on gtf.tournament_id = gt.tournament_id and gtf.flight_id = gts.flight_id '.
				'where ';
		
		if ($tournamentId == null)
		{
			$sql .= "year(gt.tournament_date) = $currentYear";
		}
		else
		{
			$sql .= "gt.tournament_id = $tournamentId";			
		}
		
		$sql .= ' and u.username IS NOT NULL '.
				'group by gt.tournament_name, u.username '.
				'order by gt.tournament_date, gtf.flight_name, gts.signup_gross_standing, gts.signup_net_standing, gr.round_score_temp';
		
		$ret = $this->query($sql, $error);
		if ($ret != null)
		{
			// Parse results to single out additional information for the frontend.
			foreach ($ret as &$user)
			{	
				// Set the photo file for the player
				// Current directory is /golfapp/services
				$player_photo_file = '../../modules/Golf/photos/members/'.strtolower($user->username).'.jpg';
				
				$user->photo_file = "modules/Golf/photos/members/imageholder.jpg";
				if (file_exists($player_photo_file))
				{
					$user->photo_file = 'modules/Golf/photos/members/'.strtolower($user->username).'.jpg';
				}
				
				// Create Array of winners for each tournament
				$k = "$user->tournament_id";
				// Set the winners for each tournament
				if ($user->signup_gross_standing == "1")
				{
					$flight_name = "GROSS-".$user->flight_name;
					$winners->$k->$flight_name = $user;
				}
				if ($user->signup_net_standing == "1")
				{
					$flight_name = "NET-".$user->flight_name;
					$winners->$k->$flight_name = $user;
				}
				
				// Get side contest winners if one is not set already
				if ($sideContest->$k == null)
				{
					$ret2 = $this->getTournamentSideContestsCommon($user->tournament_id);
					$sideContest->$k = $ret2->data;
				}
			}

			$final_ret->users = $ret;
			$final_ret->winners = $winners;
			$final_ret->contests = $sideContest;
			
			// Get side contest winners
			echo '{"items":'. json_encode($final_ret) .'}';				
		}
		else
		{
			if ($error == null)
			{
				echo '{"error":{"text":null}}';
			}
			else
			{
				echo '{"error":{"text":'. $error .'}}';				
			}
		}
	}
	
	private function getTournamentSideContestsCommon($tournamentId=null)
	{
		$currentYear = date('Y');
		
		// 1 = Cloest to the Pin
		// 2 = Longest Drive
		// 3 = Hole In One
		$sql = 'SELECT '.
				'tsc.contest_type, '.
				'u.username, '.
				'gtf.flight_name, '.
				'gh.hole_par, gh.hole_number '.
				'from nuke_golf_tournament_side_contests tsc '.
				'left join nuke_golf_tournament_flights gtf on gtf.flight_id = tsc.flight_id '.
				'left join nuke_golf_holes gh on gh.hole_id = tsc.hole_id '.
				'left join nuke_golf_tournaments gt on gt.tournament_id = tsc.tournament_id '.
				'left join nuke_golf_tournament_signups gts on gts.signup_id = tsc.winner_signup_id '.
				'left join nuke_users u on u.user_id= gts.user_id '.
				'where ';
		
		if ($tournamentId == null)
		{
			$sql .= "year(gt.tournament_date) = $currentYear";
		}
		else
		{
			$sql .= "tsc.tournament_id = $tournamentId";
		}

		$ret = $this->query($sql, $error);
		if ($ret == null)
		{
			$data->data = null;
			$data->success = false;
			$data->errorString = $error;
		}
		else
		{
			$data->data = $ret;
			$data->success = true;
			$data->errorString = null;
		}
		
		return $data;
	}
	
	public function getTournamentSideContests($tournamentId = null)
	{
		$ret = $this->getTournamentSideContestsCommon($tournamentId);
		if ($ret->success == true)
		{
			echo '{"items":'. json_encode($ret->data) .'}';				
		}
		else
		{
			echo '{"error":{"text":'. $ret->errorString .'}}';				
		}
	}

	public function kwe1($year=null)
	{
		if ($year == null)
		{
			$year = date("Y");
		}
		
		$sql = 'SELECT '.
				'gr.user_id, '.
				'u.name, '.
				'u.username, '.
				'SUM(gs.score_value) as PAR3_TOTAL, '.
				'count(distinct(gt.tournament_id)) as TOURNAMENTS_PLAYED '.
				'FROM nuke_golf_tournaments gt '.
				'left join nuke_golf_courses gc ON gc.course_id = gt.course_id '.
				'left join nuke_golf_rounds gr on gr.tournament_id = gt.tournament_id '.
				'left join nuke_users u on u.user_id = gr.user_id '.
				'left join nuke_golf_scores gs on gr.round_id = gs.round_id '.
				'left join nuke_golf_holes gh on gh.hole_id = gs.hole_id '.
				'where year(gt.tournament_date) = '.$year.' and gh.hole_par = 3 and gr.user_id != 0 and u.username is not null '.
				'group by u.username '.
				'order by TOURNAMENTS_PLAYED desc, PAR3_TOTAL asc, tournament_date, u.username';
	
		$ret = $this->query($sql, $error);
		if ($ret != null)
		{
			printf("<p><table border=1>\n");
			printf("<tr><h2>Table1: This is a list of all players and their par3 totals for all tournaments in $year.</h2></tr>\n");
			printf("<tr>\n");
			foreach($ret[0] as $key => $row0)
			{
				printf("<td>".$key."</td>");
			}
			printf("</tr>\n");
			foreach ($ret as $row)
			{
				printf("<tr>\n");
				foreach ($row as $value)
				{
					printf("<td>".$value."</td>");
				}
				printf("</tr>\n");
			}
			printf("</table></p>\n");
		}
		else
		{
			printf("Table1: Sql Error: ".print_r($error,true));
		}		
	}
	
	public function kwe2($year=null)
	{
		if ($year == null)
		{
			$year = date("Y");
		}
		
		$sql = 'SELECT '.
				'tournament_name, '.
				'tournament_date, '.
				'gc.course_name, '.
				'gr.user_id, '.
				'u.name, '.
				'u.username, '.
				'gh.hole_par, '.
				'SUM(gs.score_value) as PAR3_TOTAL '.
				'FROM nuke_golf_tournaments gt '.
				'left join nuke_golf_courses gc ON gc.course_id = gt.course_id '.
				'left join nuke_golf_rounds gr on gr.tournament_id = gt.tournament_id '.
				'left join nuke_users u on u.user_id = gr.user_id '.
				'left join nuke_golf_scores gs on gr.round_id = gs.round_id '.
				'left join nuke_golf_holes gh on gh.hole_id = gs.hole_id '.
				'where year(gt.tournament_date) = '.$year.' and gh.hole_par = 3 and u.username is not null '.
				'group by u.username, gt.tournament_name '.
				'order by tournament_date, PAR3_TOTAL, u.username';
	
		$ret = $this->query($sql, $error);
		if ($ret != null)
		{
			printf("<p><table border=1>\n");
			printf("<tr><h2>Table2: This is a list of all players and their par 3 totals for each tournament of $year. The table is grouped by tournament and should help support the data in Table 1.</h2></tr>\n");
			printf("<tr>\n");
			foreach($ret[0] as $key => $row0)
			{
				printf("<td>".$key."</td>");
			}
			printf("</tr>\n");
			foreach ($ret as $row)
			{
				printf("<tr>\n");
				foreach ($row as $value)
				{
					printf("<td>".$value."</td>");
				}
				printf("</tr>\n");
			}
			printf("</table></p>\n");				
		}
		else
		{
			printf("Table2: Sql Error: ".print_r($error,true));
		}		
	}
	
	public function kwe3($year=null)
	{
		if ($year == null)
		{
			$year = date("Y");
		}
		
		$sql = 'SELECT tournament_name, tournament_date, '.
				'gc.course_name, gr.user_id, u.name, u.username, '.
				'gh.hole_number, gh.hole_par, gs.score_value FROM nuke_golf_tournaments gt '.
				'left join nuke_golf_courses gc ON gc.course_id = gt.course_id '.
				'left join nuke_golf_rounds gr on gr.tournament_id = gt.tournament_id '.
				'left join nuke_users u on u.user_id = gr.user_id '.
				'left join nuke_golf_scores gs on gr.round_id = gs.round_id '.
				'left join nuke_golf_holes gh on gh.hole_id = gs.hole_id '.
				'where year(gt.tournament_date) = '.$year.' and gh.hole_par = 3 and u.username is not null order by tournament_date, u.username, gh.hole_number';
		
		$ret = $this->query($sql, $error);
		if ($ret != null)			
		{
			printf("<table border=1>\n");
			printf("<tr><h2>Table3: This is a list of all players and their individual scores on par 3s for every tournament in $year. This data should help verify Table 2.</h2></tr>\n");
			printf("<tr>\n");
			foreach($ret[0] as $key => $row0)
			{
				printf("<td>".$key."</td>");	
			}
			printf("</tr>\n");
			foreach ($ret as $row)
			{
				printf("<tr>\n");
				foreach ($row as $value)
				{
					printf("<td>".$value."</td>");
				}
				printf("</tr>\n");
			}
			printf("</table></p>\n");				
		}
		else
		{
			printf("Table3: Sql Error: ".print_r($error,true));
		}		
	}

	public function query($sql, &$error, $className="stdClass", $ctorArgs=array())
	{
		$object = null;
		try {
			$stmt = $this->sql_->query($sql);
			if ($stmt == false)
			{
				$error = "SQL Error: ".print_r($stmt->errorInfo(),true).", Query = $sql";
			}
			else
			{
				$object = $stmt->fetchAll(PDO::FETCH_CLASS, $className, $ctorArgs);
			}
		}
		catch(PDOException $e) {
			$error = $e->getMessage();
		}
	
		return $object;
	}
		
	private function xquery($sql, $args, &$error)
	{
		$object = null;
		try {
			$stmt = $this->sql_->prepare($sql);
			$ret = $stmt->execute($args);
			if ($ret == true)
			{
				$object = $stmt->fetchAll(PDO::FETCH_OBJ);
			}
			else
			{
				$error = "SQL returned error: ".print_r($stmt->errorInfo(),true).", query = $sql";
			}
		}
		catch(PDOException $e) {
			$error = $e->getMessage(); 
		}

		return $object;
	}
}

?>