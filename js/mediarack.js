var showLimit = 5;
var showOffset = 0;
var loadShows = true;

var movieLimit = 5;
var movieOffset = 0;
var loadMovies = true;

var seasonOpacity = 0.7;

$(document).ready(function() {

	getLatest('Shows');
	getLatest('Movies');
	getShows();
	getMovies();
	
	$(".nav a").on('click',function(e) {
		var page = $(this).attr('href');
		e.preventDefault();
		window.location.hash = page;
		showPage(page);
	});
	
	$(window).scroll(function(){
		if  ($(window).scrollTop()+200 >= ($(document).height() - ($(window).height()))){
			if ($("#shows").is(":visible") && loadShows == true) {
				loadShows = false;
				getShows();
			}
			if ($("#movies").is(":visible") && loadMovies == true) {
				loadMovies = false;
				getMovies();
			}
		}
	});
	
	var url = document.location.toString();
	if (url.match('#')) {
		showPage('#'+url.split('#')[1]);
	}

});

function showPage(page) {
	$('.content').hide();
	$(".nav li").removeClass('active');
	$(".nav li:has(a[href="+page+"])").addClass('active');
	window.scrollTo(0, 0);
	$(page).show();
}

function getLatest(type) {
	$.getJSON('api.php', {
		'type': type.toLowerCase(),
		'get': 'latest'
		}, function(data) {
			$.each(data, function (key, ep) {
				var ulLatestContainer = $("#latest"+type);
				if(type == 'Shows') {
					var liLatestItem =  $('<li><img src="api.php?get=poster&show='+escape(ep.show)+'" /><div class="epLabel">'+ep.show+'<br />'+ep.episode+'<br />'+ep.airdate+'</div></li>');
				}else{
					var liLatestItem =  $('<li><img src="api.php?get=poster&movie='+escape(ep.movie)+'" /><div class="epLabel">'+ep.movie+'</div></li>');
				}
				ulLatestContainer.append(liLatestItem);
			});
			$('.jcarousel.jc'+type)
			.on('jcarousel:create jcarousel:reload', function() {
				var element = $(this),
					width = element.innerWidth();
				
				if (width > 1000) {
					width = width / 5;
				} else if (width > 800) {
					width = width / 4;
				} else if (width > 590) {
					width = width / 3;
				} else if (width < 590) {
					width = width / 2;
				}
				element.jcarousel('items').css('width', width + 'px');
			})
			.jcarousel({
				wrap: 'both'
			});
			$('.jcarousel-control-prev.jc'+type)
				.on('jcarouselcontrol:active', function() {
					$(this).removeClass('inactive');
				})
				.on('jcarouselcontrol:inactive', function() {
					$(this).addClass('inactive');
				})
				.jcarouselControl({
					target: '-=1'
				});

			$('.jcarousel-control-next.jc'+type)
				.on('jcarouselcontrol:active', function() {
					$(this).removeClass('inactive');
				})
				.on('jcarouselcontrol:inactive', function() {
					$(this).addClass('inactive');
				})
				.jcarouselControl({
					target: '+=1'
				});

		}
	);
}

function getShows() {
	$("#loading").show();
	$.getJSON('api.php', {
		'get': 'shows',
		'limit': showLimit.toString(),
		'offset': showOffset.toString()
		}, function(data) {
			var divShows = $("#shows");
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
				ulSeasons.fadeTo(0, seasonOpacity);
				ulSeasons.hover(function() {
					ulSeasons.fadeTo(300, 1);
				}, function() {
					ulSeasons.fadeTo(300, seasonOpacity);
				});
				divShowContainer.append(divShowHeader);
				divShowContainer.append(divShowBody);
				divShowBody.append(divShowPoster);
				divShowBody.append(ulSeasons);
				divShowContainer.append(divShowFooter);
				$("#shows div.panel-body.lazy").lazyload({
					//event: "scrollstop",
					effect: "fadeIn",
					threshold: 100
				});		
				$("#shows img.showPoster.lazy").lazyload({
					//event: "scrollstop",
					effect: "fadeIn",
					threshold: 100
				});
				$("#shows img.showLogo.lazy").lazyload({
					//event: "scrollstop",
					effect: "fadeIn",
					threshold: 100
				});
				divShows.append(divShowContainer);
				if(i < 5) {
					divShowHeader.find("img.showLogo").attr('src', 'api.php?get=logo&show='+escape(show.folder)).removeClass('lazy');
					divShowBody.css('background-image', 'url(api.php?get=fanart&show='+escape(show.folder)+')').removeClass('lazy');
					divShowPoster.find("img.showPoster").attr('src', 'api.php?get=poster&show='+escape(show.folder)).removeClass('lazy');
				}
				i++;
			});
			showOffset = showOffset+showLimit;
			if(i > 0) {
				loadShows = true;
			}
			$("#loading").hide();
		}
	);
}

function getEpisodes(show, season) {
	$("#loading").show();
	$.getJSON('api.php', {
		'get': 'episodes',
		'show': show,
		'season': season
		}, function(data) {
			var liSeason = $("#shows").find('div[data-showid="'+show+'"] li[data-season="'+season+'"]');
			liSeason.find("table.episodes").remove();
			var olEpisodes = $('<table class="table table-bordered table-condensed episodes"></table>');
			$.each(data, function (key, episode) {
				var status;
				switch(episode.status) {
					case 'Ignored':
						status = "info";
						break;
					case 'Downloaded':
						status = "success";
						break;
					case 'Wanted':
						status = "danger";
						break;
					case 'Snatched':
						status = "warning";
						break;
					case 'Unaired':
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
			$("#loading").hide();
		}
	);
}

function getMovies() {
	$.getJSON('api.php', {
		'get': 'movies',
		'limit': movieLimit.toString(),
		'offset': movieOffset.toString()
		}, function(data) {
			var divMovies = $("#movies");
			var i = 0;
			$.each(data, function (key, movie) {
				var divMovieContainer = $('<div class="movie panel panel-default" data-imdb="'+movie.imdb+'" id="movie_'+movie.imdb+'"></div>');
				var divMovieHeader = $('<div class="movieName panel-heading"><h2 class="panel-title text-center"><img class="movieLogo lazy" alt="'+movie.title+'" data-original="api.php?get=logo&movie='+escape(movie.folder)+'" src="" /></h2></div>');
				var divMovieBody = $('<div class="panel-body lazy" data-original="api.php?get=fanart&movie='+escape(movie.folder)+'"></div>');
				var divMoviePoster = $('<div class="col-md-2 text-center"><a href="#" class="thumbnail"><img id="poster_'+movie.imdb+'" class="moviePoster lazy" data-original="api.php?get=poster&movie='+escape(movie.folder)+'" src="img/no_poster.jpg" /></a></div>');
				var divMoviePlot = $('<div class="moviePlot col-md-10 panel panel-default"><div class="panel-heading">'+movie.tagline+'</div><div class="panel-body">'+movie.plot+'</div></div>');
				var divMovieFooter =$('<div class="panel-footer text-right">IMDB Rating: '+movie.rating+'</div>');
				divMovieContainer.append(divMovieHeader);
				divMovieContainer.append(divMovieBody);
				divMovieBody.append(divMoviePoster);
				divMoviePlot.fadeTo(0, seasonOpacity);
				divMovieBody.hover(function() {
					divMoviePlot.fadeTo(300, 1);
				}, function() {
					divMoviePlot.fadeTo(300, seasonOpacity);
				});
				divMovieBody.append(divMoviePlot);
				divMovieContainer.append(divMovieFooter);
				$("#movies div.panel-body.lazy").lazyload({
					//event: "scrollstop",
					effect: "fadeIn",
					threshold: 100
				});		
				$("#movies img.moviePoster.lazy").lazyload({
					//event: "scrollstop",
					effect: "fadeIn",
					threshold: 100
				});
				$("#movies img.movieLogo.lazy").lazyload({
					//event: "scrollstop",
					effect: "fadeIn",
					threshold: 100
				});
				divMovies.append(divMovieContainer);
				if(i < 5) {
					divMovieHeader.find("img.movieLogo").attr('src', 'api.php?get=logo&movie='+escape(movie.folder)).removeClass('lazy');
					divMovieBody.css('background-image', 'url(api.php?get=fanart&movie='+escape(movie.folder)).removeClass('lazy');
					divMoviePoster.find("img.moviePoster").attr('src', 'api.php?get=poster&movie='+escape(movie.folder)).removeClass('lazy');
				}
				i++;
			});
			movieOffset = movieOffset+movieLimit;
			if(i > 0) {
				loadMovies = true;
			}
		}
	);
}
