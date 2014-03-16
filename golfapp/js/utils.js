var Utils = {
		getShortMonth:
			function(date) 
			{
				if (this.months == null)
				{
					this.shortMonths = new Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec");
				}
				return this.shortMonths[date.getMonth()];
			},		
		getFullMonth: 
			function(date) 
			{
				if (this.fullMonths == null)
				{
					this.fullMonths = new Array("January","February","March","April","May","June","July","August","September","October","November","December");
				}
				return this.fullMonths[date.getMonth()];
			},
		getUrlVars: 
			function() 
			{
				var vars = [], hash;
				var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
	
				// console.log("Hashes: "+hashes);
				for(var i = 0; i < hashes.length; i++)
				{
					hash = hashes[i].split('=');
					vars.push(hash[0]);
					vars[hash[0]] = hash[1];
				}
	
				return vars;
			},
		dateCompare:
			/** Date Comparison Function
			 * @function dateCompare
			 * 
			 * @param first javascript Date object
			 * @param second javascript Date object
			 * 
			 * @return 0 if dates are equal
			 * @return -1 if date1 < date2
			 * @return 255 if date2 > date1
			 */
			function (date1, date2) 
			{
				// First compare years
				if (date1.getFullYear() < date2.getFullYear()) return -1;
				if (date1.getFullYear() > date2.getFullYear()) return 255;				
				// Years must be equal, check month
				if (date1.getMonth() < date2.getMonth()) return -1;
				if (date1.getMonth() > date2.getMonth()) return 255;				
				// Months must be equal, compare days
				if (date1.getDate() < date2.getDate()) return -1;
				if (date1.getDate() > date2.getDate()) return 255;
				
				return 0;
			},
		store: 
			function( namespace, data ) {
				if ( arguments.length > 1 ) {
					return localStorage.setItem( namespace, JSON.stringify( data ) );
				} else {
					var store = localStorage.getItem( namespace );
					return ( store && JSON.parse( store ) ) || [];
				}
			},
		getStorage:
			function (key)
			{
				if (Modernizr.localstorage) 
				{
					var ret = localStorage[key];
					if (ret)
					{
						return (ret && JSON.parse( ret )) || null;
					}
//					else
//					{
//						console.log('GET: Key: '+key+' not found.');
//					}
				}
				else
				{
					console.log("localStorage not supported.");
				}
				
				return null;
			},
		setStorage:
			function (key, value)
			{
				if (Modernizr.localstorage) 
				{
					localStorage[key] = JSON.stringify(value);
					return true;
				}
				else
				{
					console.log("localStorage not supported.");
				}
				
				return false;
			},
		removeStorage:
			function (key)
			{
				if (Modernizr.localstorage) 
				{
					if (localStorage[key])
					{
						localStorage.removeItem(key);
						return true;
					}
					else
					{
						console.log('REMOVE: Key: '+key+' not found.');
					}
				}
				else
				{
					console.log("localStorage not supported.");
				}
				
				return false;
			}			
};