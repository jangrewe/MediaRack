$(document).ready(function() {

	getShows();
	
	$(".nav a").on('click',function(e) {
		e.preventDefault();
		$('.content').hide();
		$(".nav li").removeClass('active');
		$(this).parent().addClass('active');
		$($(this).attr('href')).show();
	});

});

function getShows() {
	$.getJSON('api.php', {
		'get': 'shows'
		}, function(data) {
			var divShows = $("#shows");
			divShows.empty();
			var i = 0;
			$.each(data, function (key, show) {
				var divShowContainer = $('<div class="show panel panel-default" data-showid="'+show.id+'" id="show_'+show.id+'"></div>');
				var divShowHeader = $('<div class="showName panel-heading"><h2 class="panel-title text-center"><img class="showLogo lazy" style="min-height: 50px; height: 50px;" alt="'+show.name+'" data-original="api.php?get=logo&show='+escape(show.folder)+'" src="" /></h2></div>');
				var divShowBody = $('<div class="panel-body lazy" data-original="api.php?get=fanart&show='+escape(show.folder)+'"></div>');
				var divShowFooter =$('<div class="panel-footer">Seasons: , Episodes: </div>');
				var divShowPoster = $('<div class="col-md-2 text-center"><a href="#" class="thumbnail"><img id="poster_'+show.id+'" class="showPoster lazy" data-original="api.php?get=poster&show='+escape(show.folder)+'" src="img/no_poster.jpg" /></a></div>');
				var ulSeasons = $('<ul class="seasons col-md-10 list-group"></ul>');
				$.each(show.seasons, function(key, season) {
					var liSeason = $('<li class="season list-group-item" data-season="'+season.season+'"><strong>Season '+season.season+'</strong><span class="badge">'+season.count+'</span></li>');
					liSeason.bind('click', function() {
						getEpisodes($(this).parent().parent().parent().data('showid'), $(this).data('season'));
					});
					liSeason.hover(function() {
						$("#poster_"+show.id).error(function() {$(this).attr('src', 'api.php?get=poster&show='+escape(show.folder))});
						$("#poster_"+show.id).attr('src', 'api.php?get=poster&show='+escape(show.folder)+'&season='+season.season);
					}, function() {
						$("#poster_"+show.id).attr('src', 'api.php?get=poster&show='+escape(show.folder));
					});
					ulSeasons.append(liSeason);
				});
				divShowContainer.append(divShowHeader);
				divShowBody.append(divShowPoster);
				divShowBody.append(ulSeasons);
				$("img.showLogo.lazy").lazyload({
					event: "scrollstop",
					effect: "fadeIn",
					threshold: 800
				});
				divShowBody.lazyload({
					event: "scrollstop",
					effect: "fadeIn",
					threshold: 1600
				});
				$("img.showPoster.lazy").lazyload({
					event: "scrollstop",
					effect: "fadeIn",
					threshold: 800
				});
				divShowContainer.append(divShowBody);
				divShowContainer.append(divShowFooter);
				divShows.append(divShowContainer);
				if(i < 3) {
					divShowHeader.find("img.showLogo").attr('src', 'api.php?get=logo&show='+escape(show.folder));
					divShowBody.css('background-image', 'url(api.php?get=fanart&show='+escape(show.folder)+')');
					divShowPoster.find("img.showPoster").attr('src', 'api.php?get=poster&show='+escape(show.folder));
				}
				i++;
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
				var liEpisode = $('<tr class="episode text-'+status+'"><td>'+episode.episode+'</td><td>'+episode.name+'</td><td class="text-right">'+episode.airdate+'</td></tr>');
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
