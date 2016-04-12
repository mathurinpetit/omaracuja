	var actus;
	var map;
	var myLatLng;
	var mobile = /Android|webOS|iPhone|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ;
 	var slider;
	
	var lanceVideo= function(path) {		
		$("#player1").attr("src",path);
		$("#player1").get(0).play();
	}		

	$().ready(function() {
		 if (mobile===false) { 
			$('#video-wrapper').html('<video class="mejs-wmp" style="position:absolute; width: 100%; height:100%;" src="videos/omaracuja.mp4" type="video/mp4" id="player1" controls="controls" preload="none" poster="images/transparent.png" ></video>');
	
			$('video').mediaelementplayer({
				success: function(player, node) {}
			});
		}
		 
		var actu=0;
		var nb_actus;
		$.ajax({
			type: 'POST',			
			url: './json/events.json',
			dataType:'html',
			success: function(data) {
				actus= $.parseJSON(data);
				nb_actus= actus.length;
			}
		});	

		var photos=0;
		var nb_photos;
		$.ajax({
			type: 'POST',			
			url: './json/photos.json',
			dataType:'html',
			success: function(data) {
				photos= $.parseJSON(data);
				nb_photos= photos.length;
			}
		});	
		
		var videos=0;
		var nb_videos;
		$.ajax({
			type: 'POST',			
			url: './json/videos.json',
			dataType:'html',
			success: function(data) {
				videos= $.parseJSON(data);
				nb_videos= videos.length;
			}
		});	
		
		var defile_actu= function() {		
			
			$(".list-evenements")							
				.animate({"marginTop":"-120px","opacity":0},function() {
			$(".list-evenements").css({"marginTop":"120px","opacity":0})
				.html('<li onclick=\'openEvent("'+actus[actu].lien+'")\'>'+
			'		<a><img src="'+actus[actu].imgsrc+'">'+
			'		<div class="event-date">'+actus[actu].date+'</div>'+
			'		<div class="event-lieu">'+actus[actu].lieu+'</div>'+
			'		<div class="event-titre">'+actus[actu].titre+
			'		<span class="plus"></span></div></a>'+
			'</li>').animate({"marginTop":"0px","opacity":1});
			});
			actu++; if (actu>=nb_actus) actu=1;
		}
		defile_actu(); setInterval(defile_actu,6000);
   
		var titre= function(html_excerpt) {		
			$("#flow").html(html_excerpt);
			letitre= $("#flow .titre-bootbox").html();
			$("#flow").html("");
			return letitre;
		}

		$(document).on("click", ".entree-artistes", function(e) {	
			jQuery.ajax({
				type: 'POST',
				url: './connexion_form.html',
				success: function(data) {
					open_dialog({
						message: data,
						title: titre(data),
						buttons: {
							success: {
							  label: "Annuler",
							  className: "btn-cancel",
							  callback: function() {}
							},
							main: {
							  label: "Connexion",
							  className: "btn-primary",
							  callback: function() {}
							}
						}
					});	

				}
			});		
		});		

		$(document).on("click", ".energie", function(e) {
			jQuery.ajax({
				type: 'POST',
				url: './bulle_maracuja.html',
				success: function(data) {
					open_dialog({
						message: data,
						title: titre(data),
						buttons: {
							success: {
								label: "Trop cool !",
								className: "btn-success",
							},
						}
					});
				}
			});		
		});		
		$(document).on("click", ".logo", function(e) {
			jQuery.ajax({
				type: 'POST',
				url: './bulle_maracuja.html',
				success: function(data) {
					open_dialog({
						message: data,
						title: titre(data),
						buttons: {
							success: {
								label: "Trop cool !",
								className: "btn-success",
							},
						}
					});
				}
			});		
		});		

		$(document).on("click", "#menu-photos", function(e) {
			jQuery.ajax({
				type: 'POST',
				url: './photos.html',
				success: function(data) {
					$('#slider-galerie').eq(0).remove();
					$('#footer-galerie').eq(0).remove();
					$('body').append(data);
					$('#mini-galerie').html("");
					$('#mainContainer').find(".col").html("<div class='my-slider' id='slider-galerie' style='opacity:0'><ul></ul></div>");    
					
					for (i=0;i<photos.length;i++) { 
						photo=photos[i];	
						$('#mini-galerie').append('<img src="'+photo.imgsrc+'" onclick="slider.unslider('+"'animate:"+i+"'"+');" title="'+photo.titre+' - '+photo.texte+'">');
						$('#slider-galerie').find("ul").append('<li style="background-image:url('+photo.imgsrc+')"></li>');
					}
					calculht= $(window).height()-120;
					$('#slider-galerie li').css({"height":calculht});
					$('#slider-galerie').eq(0).animate({"opacity":"1"},1200,function() {});
					$('#footer-galerie').eq(0).animate({"opacity":"1"},1200,function() {});

					$('#galerie-close').click(function(e) {
						$('#slider-galerie').eq(0).animate({"opacity":"0"},700,function() { 
						$('#slider-galerie').eq(0).remove(); })	
						$('#footer-galerie').eq(0).animate({"opacity":"0"},700,function() { 
						$('#footer-galerie').eq(0).remove(); })	
					});

					slider= $('.my-slider').unslider({
						keys: true, arrows: false, nav: false, infinite: true 
					});
	
				}
			});		
		});			
		
		$(document).on("click", "#menu-videos", function(e) {
			jQuery.ajax({
				type: 'POST',
				url: './videos.html',
				success: function(data) {
					
					$('#slider-galerie').eq(0).remove();
					$('#footer-galerie').eq(0).remove();
					$('body').append(data);
					$('#mini-galerie').html("");
					
					for (i=0;i<videos.length;i++) { 
						video=videos[i];	
						$('#mini-galerie').append("<img src='"+video.preview+"' onclick='lanceVideo(\""+video.vidsrc+"\");'"+' title="'+video.titre+' - '+video.texte+'">');
					}
					calculht= $(window).height()-120;
					$('#footer-galerie').eq(0).animate({"opacity":"1"},1200,function() {});

					$('#galerie-close').click(function(e) {
						$('#footer-galerie').eq(0).animate({"opacity":"0"},700,function() { 
						$('#footer-galerie').eq(0).remove(); })	
					});

				}
			});		
		});			

		$(document).on("click", "#menu-contact", function(e) {
			jQuery.ajax({
				type: 'POST',
				url: './contact.html',
				success: function(data) {
					open_dialog({
						message: data,
						title: titre(data),
						buttons: {
							success: {
								label: "Ça me va.",
								className: "btn-success",
							},
						}
					});
				}
			});		
		});				

		$(document).on("click", "#menu-news", function(e) {
			jQuery.ajax({
				type: 'POST',
				url: './news.html',
				success: function(data) {
					open_dialog({
						message: data,
						title: titre(data),
						buttons: {
							success: {
								label: "S'abonner",
								className: "btn-success",
							},
						}
					});
				}
			});		
		});		
	
	});
	
			
	var open_dialog = function (content_dialog) {			
		$(".navbar-collapse").collapse('hide');
	
		if ($('.bootbox').length==0) {
			$('.modal-backdrop').eq(0).animate({"opacity":"0"},700,function() { 
				$('.modal-backdrop').eq(0).remove(); })	
			
			bootbox.dialog(content_dialog);
			
		 if (mobile===false) {
			ajust_bootbox();
		 } else {
			$('footer').animate({"opacity":0,"bottom":"-200px"}, function() {
				$(".bootbox-close-button").on("click", function(e) {
					$('footer').animate({"opacity":1,"bottom":"0"});
				});
				$(".btn-success").on("click", function(e) {
					$('footer').animate({"opacity":1,"bottom":"0"});
				});
			});
		 }
		}
		else {
			 //$(".bootbox").modal("hide");
			 $(".modal-title").html(content_dialog.title);
			 $(".bootbox-body").html(content_dialog.message);
			 $(".btn-success").html(content_dialog.buttons.success.label);		
			if (mobile===false) ajust_bootbox();
		  
		}
	}	
	var ajust_bootbox = function() {
		h_t= $(".modal-title").height()/44;
		hauteur= $(".fond").height()-400-(h_t*20);			
		$(".modal-body").css({"max-height":hauteur});
		$(".modal-body").delay(250).mCustomScrollbar({theme:"rounded-dots",setTop: 0, scrollInertia:0});
	}
			
	var openEvent = function(id) {
		id= parseInt(id);
		data=actus[id];
		if (id==(actus.length-1)) datanext=actus[1]; else datanext=actus[id+1];
		if (id==1)   dataprev=actus[actus.length-1]; else dataprev=actus[id-1];
		
		loremipsum=". Aenean vitae facilisis quam, eget eleifend enim. Fusce rutrum nisi quis. Vestibulum eget ornare turpis vulputate aliquam.";
		
		texte=  '<div class="row" style="margin:0"><div class="col-md-1 evl"><img src="./images/prev.png" class="event_prev" onclick="openEvent('+dataprev.lien+')"></div>'+
				'<div class="col-md-6 event-poster" style="background-image:url(\''+data.imgsrc+'\');"></div>'+
				'<div class="col-md-5 event-detail"><div class="event-date">'+data.date+'</div>'+
				'<div class="event-lieu">'+data.lieu+'</div><br>'+
				'<div class="event-texte"><span class="text-muted">'+data.texte+loremipsum+'</span></div><br>'+
				'<div class="gmap_event" id="gmap_event_'+id+'"></div>'+
				'</div><div class="col-md-1 evr" onclick="openEvent('+datanext.lien+')"><img src="./images/next.png" class="event_next"></div></div>';
		
				open_dialog({
					message: texte,
					title: "<h2>"+data.titre+"</h2>",
					buttons: {
						success: {
							label: "J'ai trop envie d'y aller.",
							className: "btn-success",
						},
					}
				});
				
				myLatLng = {lat: data.x, lng: data.y};
				setTimeout("map = new google.maps.Map(document.getElementById('gmap_event_"+id+"'), {"
					+"center: myLatLng, zoom: "+data.zoom+"});"
					+"var marker = new google.maps.Marker({ "
					+"position: myLatLng, map: map, title: ''});",800);
	
	}
	
	$(document).on("click", "#menu-evt", function(e) {
		texte="";
		for (i=1;i<actus.length;i++) { data=actus[i];
		
		texte+= '<div class="row mobile_condensed" onclick="openEvent('+data.lien+')">'+
				'<div class="col-md-4 event_preview_poster" style="text-align:right"><img src="'+data.imgsrc+'" style="height:120px;margin-bottom:10px"></div>'+
				'<div class="col-md-6 event_preview" style="cursor:pointer;"><div class="event-date">'+data.date+'</div>'+
				'<div class="event-lieu"><span class="text-muted">'+data.lieu+'</span></div>'+
				'<div class="event-titre">'+data.titre+'</div></div>'+
				'<div class="col-md-1 event_preview_next"><img src="./images/next.png" style="cursor:pointer"></div>'+
				'</div>';
		}
		
				open_dialog({
					message: texte,
					title: "<h2>Les évènements à venir</h2>",
					buttons: {
						success: {
							label: "J'ai trop envie d'y aller.",
							className: "btn-success",
						},
					}
				});

	});		
			