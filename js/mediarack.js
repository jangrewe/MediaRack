$(document).ready(function() {

	getShows();

});

function getShows() {
	$.getJSON('api.php', {
		'get': 'shows'
		}, function(data) {
			var divShows = $("#shows");
			divShows.empty();
			$.each(data, function (key, show) {
				var divShowContainer = $('<div class="show panel panel-default" data-showid="'+show.id+'" id="show_'+show.id+'"></div>');
				var divShowHeader = $('<div class="showName panel-heading"><h2 class="panel-title text-center"><img class="showLogo" alt="'+show.name+'" src="api.php?get=logo&show='+escape(show.folder)+'" /></h2></div>');
				var divShowBody = $('<div class="panel-body" ></div>');
				var divShowFooter =$('<div class="panel-footer">Seasons: , Episodes: </div>');
				var divShowPoster = $('<div class="showPoster col-md-2 text-center"><a href="#" class="thumbnail"><img id="poster_'+show.id+'" src="api.php?get=poster&show='+escape(show.folder)+'" /></a></div>');
				var ulSeasons = $('<ul class="seasons col-md-10 list-group"></ul>');
				$.each(show.seasons, function(key, season) {
					var liSeason = $('<li class="season list-group-item" data-season="'+season.season+'"><strong>Season '+season.season+'</strong><span class="badge">'+season.count+'</span></li>');
					liSeason.bind('click', function() {
						getEpisodes($(this).parent().parent().parent().data('showid'), $(this).data('season'));
					});
					liSeason.hover(function() {
						$("#poster_"+show.id).attr('src', 'api.php?get=poster&show='+escape(show.folder)+'&season='+season.season);
					}, function() {
						$("#poster_"+show.id).attr('src', 'api.php?get=poster&show='+escape(show.folder));
					});
					ulSeasons.append(liSeason);
				});
				divShowContainer.append(divShowHeader);
				divShowBody.append(divShowPoster);
				divShowBody.append(ulSeasons);
				divShowBody.css('background', 'url(api.php?get=fanart&show='+escape(show.folder)+') repeat-y top center');
				//divShowHeader.append('<img class="showLogo" alt="'+show.name+'" src="api.php?get=logo&show='+escape(show.folder)+'" />');
				divShowContainer.append(divShowBody);
				divShowContainer.append(divShowFooter);
				divShows.append(divShowContainer);
			});
		}
	);
}

function getEpisodes(show, season) {
	$.getJSON('api.php', {
		'get': 'episodes',
		'show': show,
		'season': season
		}, function(data) {
			var liSeason = $("#shows").find('div[data-showid="'+show+'"] li[data-season="'+season+'"]');
			liSeason.find("table.episodes").remove();
			var olEpisodes = $('<table class="table table-bordered table-condensed episodes"></table>');
			$.each(data, function (key, episode) {
				console.log(episode.status.substr(-1, 1));
				var status;
				switch(episode.status.substr(-1, 1)) {
					case '5': // ignored
						status = "info";
						break;
					case '4': // downloaded
						status = "success";
						break;
					case '3': // wanted
						status = "danger";
						break;
					case '2': // snatched
						status = "warning";
						break;
					case '1': // unaired
						status = "";
						break;
					default:
						status = "text-default";
					
				}
				var liEpisode = $('<tr class="episode '+status+'"><td>'+episode.episode+'</td><td>'+episode.name+'</td><td class="text-right">'+episode.airdate+'</td></tr>');
				olEpisodes.append(liEpisode);
			});
			liSeason.append(olEpisodes);
			liSeason.unbind('click').bind('click', function() {
				$(this).find("table.episodes").remove();
				$(this).unbind('click').bind('click', function() {
					getEpisodes($(this).parent().parent().parent().data('showid'), $(this).data('season'));
				});
			});
		}
	);
}