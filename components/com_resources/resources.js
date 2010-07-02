/**
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Resource Ranking pop-ups
//----------------------------------------------------------

HUB.Resources = {

	initialize: function() {
		if (typeof(SqueezeBoxHub) != "undefined") {
			if (!SqueezeBoxHub) {
				SqueezeBoxHub.initialize({ size: {x: 750, y: 500} });
			}
			
			// Modal boxes for presentations
			$$('a.play').each(function(el) {
				if (el.href.indexOf('?') == -1) {
					el.href = el.href + '?no_html=1';
				} else {
					el.href = el.href + '&no_html=1';
				}
				el.addEvent('click', function(e) {
					new Event(e).stop();

					w = 750;
					h = 500;
					if (this.className) {
						var sizeString = this.className.split(' ').pop();
						if (sizeString && sizeString != 'play') {
							var sizeTokens = sizeString.split('x');
							w = parseInt(sizeTokens[0]);
							h = parseInt(sizeTokens[1]);
						}
					}

					SqueezeBoxHub.fromElement(el,{handler: 'url', size: {x: w, y: h}, ajaxOptions: {method: 'get'}});
				});
			});
			
			var win = new Element('a', {
				id: 'sbox-newwindow',
				href: '#',
				title: 'Open in a new window',
				events: {
					'click': function(event) {
						OpenWindow = window.open('', "newwin", "height=500,width=700,toolbar=no,scrollbars=yes,menubar=no,resizable=yes,location=no,scrollbars=no,directories=no,status=no");
						OpenWindow.document.write('<html>');
						OpenWindow.document.write('<title>Presentation</title>');
						OpenWindow.document.write('<body style="margin:0;padding:0;border:0;">');
						OpenWindow.document.write( $('sbox-window').innerHTML );
						OpenWindow.document.write('</body>');
						OpenWindow.document.write('</html>');
						OpenWindow.document.close();
						self.name = 'main';
						SqueezeBoxHub.close();
					}
				}
			});
			var res = new Element('div', {
				id: 'sbox-resizehandle',
				alt: 'Resize'
			})
			$('sbox-window').adopt(win, res);
			
			// Init the resizing capabilities
			$('sbox-window').makeResizable({
				handle:$('sbox-resizehandle'),
				onComplete: function(el) {
					var size = el.getCoordinates();
					SqueezeBoxHub.resize({x:size.width,y:size.height},false)
				}
			});
		}
		
		// Fixed resource tooltips
		var rTTips = new MooTips($$('.fixedResourceTip'), {
			showDelay: 500,
			maxTitleChars: 100,
			className: 'resource',
			fixed: true,
			offsets: {'x':20,'y':5}
		});
		
		// Ranking info pop-up
		var metadata = $$('.metadata');
		if (metadata) {
			/*
			metadata.each(function(meta) {
				meta.addEvent('mouseover', function(e) {
					var el = this.getElement('.rankinfo');
					el.addClass('active');
				});
				meta.addEvent('mouseout', function(e) {
					var el = this.getElement('.rankinfo');
					el.removeClass('active');
				});
			});
			*/
			metadata.each(function(meta) {
				$$('.rankinfo').addEvent('mouseover', function(e) {
						this.addClass('active');
					});
				$$('.rankinfo').addEvent('mouseout', function(e) {
						this.removeClass('active');
				});
			});
		}
		
		// Share links info pop-up
		var shareinfo = $$('.shareinfo');
		if (shareinfo) {
			var ell = metadata.getElement('.shareinfo');		
			$$('.share').each(function(item) {
					
					item.addEvent('mouseover', function() {					
						ell.addClass('active');
					});
			});
			$$('.share').each(function(item) {
				
					item.addEvent('mouseout', function() {					
						ell.removeClass('active');
					});
			});
		}
		
		// Audience info pop-up
		var explainscale = $$('.explainscale');
		if (explainscale) {
			var ex = metadata.getElement('.explainscale');		
			$$('.usagescale').each(function(item) {
					
					item.addEvent('mouseover', function() {					
						ex.addClass('active');
					});
			});
			$$('.usagescale').each(function(item) {
				
					item.addEvent('mouseout', function() {					
						ex.removeClass('active');
					});
			});
		}
		
		// Primary-document info pop-up
		var primarydoc = $('primary-document');
		var primarydocpop = $('primary-document_pop');
		if (primarydoc && primarydocpop) {
			primarydoc.addEvent('mouseover', function(e) {
				//new Event(e).stop();
				primarydocpop.style.display = "block";
			});
			
			primarydoc.addEvent('mouseout', function(e) {
				primarydocpop.style.display = "none";
			});
		}
		
		// Add to favorites
		var fav = $('fav-this');
		if (fav) {
			fav.addEvent('click', function(e) {
				new Event(e).stop();
				
				var rid = $('rid').value;
				new Ajax('index.php?option=com_resources&task=plugin&trigger=onResourcesFavorite&no_html=1&rid='+rid,{
					method : 'get',
					update : $('fav-this'),
					onSuccess : function(){
						if (fav.hasClass('faved')) {
						fav.removeClass('faved');
							var img = '/components/com_resources/images/broken-heart.gif';
							var txt = 'Favorite removed.';
						} else {
							fav.addClass('faved');
							var img = '/components/com_resources/images/heart.gif';
							var txt = 'Favorite saved.';
						}
						if (typeof(Growl) != "undefined") {
							Growl.Bezel({
								image: img,
								title: txt,
								text: ''
							});
						}
					}
				}).request();
			});
		}
		
		// Reply to review or comment
		var show = $$('.reply');
		if (show) {
			show.each(function(item) {
				item.addEvent('click', function(e) {
					new Event(e).stop();
					
					var f = $(this.parentNode.parentNode).getElement('.addcomment');
					if (f.hasClass('hide')) {
						f.removeClass('hide');
					} else {
						f.addClass('hide');
					}
				});
			});
			if ($$('.commentarea')) {
				$$('.commentarea').each(function(item) {
					// Clear the default text
					item.addEvent('focus', function() {
						if (item.value == 'Enter your comments...') {
							item.value = '';
						}
					});
				});
			}
			if ($$('.cancelreply')) {
				$$('.cancelreply').each(function(item) {
					item.addEvent('click', function(e) {
						new Event(e).stop();
						$(item.parentNode.parentNode.parentNode).addClass('hide');
					});
				});
			}
		}
		
		//------------------------
		// review ratings
		//------------------------
		var vhints 		=  $$('.votinghints');
		var thumbsvote  =  $$('.thumbsvote');
		if(vhints) {
			
			
			for (i = 0; i < thumbsvote.length; i++) 
			{
				thumbsvote[i].onmouseover=function() {
					
					var el = this.getLast();
					var el = el.getLast();
					el.style.display = "inline";
					
				}
				thumbsvote[i].onmouseout=function() {
					var el = this.getLast();
					var el = el.getLast();
					el.style.display = "none";
				}
			}
		}
		
			//------------------------
		// screenshot thumbnail slider
		//------------------------
		
		var target = $$('.showcase-pane')[0];
        	
		if($('showcase') && target) {	
			var sidemargin = 4;
  			var thumbwidth = 110;
			var moveto = 0;
			var active = 0;
			var panels = 0;
			
			var next = $('showcase-next');
			var prev = $('showcase-prev');
			
			thwidth = $$('.thumbima').length * sidemargin * 2 + $$('.thumbima').length * thumbwidth;
			var win_width = $('showcase-window').offsetWidth;
			
			if(thwidth/win_width < 1) {
						next.addClass('inactive');
						prev.addClass('inactive');
			}
					
			// go next		
			if (next) {
				next.addEvent('mouseover', function() {
					//var win_width = $('showcase-window').getStyle('width').toInt();
					var win_width = $('showcase-window').offsetWidth;
					if(thwidth/win_width < 1) {
						this.addClass('inactive');
						prev.addClass('inactive');
					}
					else {
						this.removeClass('inactive');
						prev.removeClass('inactive');
					}
				});								
				next.addEvent('click', function() {
					var win_width = $('showcase-window').offsetWidth;
					if(thwidth/win_width < 1) {
					 	panels = 0;	
					}
					else {
						panels = Math.round(thwidth/win_width);
					}
					
					if(panels >= 1 && active < panels) {
						active ++;
						moveto -= win_width;
								
						var fx = new Fx.Styles(target, {duration: 600, wait: false});
						 fx.start({
							'left': [moveto]
						});
					}
				});
			}
			
			// go prev
			if (prev) {
				prev.addEvent('mouseover', function() {
					var win_width = $('showcase-window').offsetWidth;
					if(thwidth/win_width < 1) {
						this.addClass('inactive');
						next.addClass('inactive');
					}
					else {
						this.removeClass('inactive');
						next.removeClass('inactive');
					}
				});	
				prev.addEvent('click', function() {
					var win_width = $('showcase-window').offsetWidth;
					var panels = Math.round(thwidth/win_width);	
					
					if(panels >= 1 && active > 0) {
									
						active --;
						moveto += win_width;
	
						var fxright = new Fx.Styles(target, {duration: 600, wait: false});
						 fxright.start({
							'left': [moveto]
						});
					}
				});
			}
		
		}
		
		
		//--------------
		var vote = $$('.revvote');
		if(vote) {
	
			for (i = 0; i < vote.length; i++) 
			{
				vote[i].onclick=function() {
					pn = $(this.parentNode.parentNode.parentNode);
					if($(this.parentNode).hasClass('gooditem')) {
						var s = 'yes';
					}
					else {
						var s = 'no';
					}
				
					var id = $(this.parentNode.parentNode.parentNode).getProperty('id').replace('reviews_','');
	
					var rid = $(this.parentNode.parentNode).getProperty('id').replace('rev'+id+'_','');	
					//var myAjax1 = new Ajax('index.php?option=com_resources&task=reviews&id='+rid+'&no_html=1&action=rateitem&refid='+id+'&ajax=1&vote='+s,{update:pn}).request();

					new Ajax('index.php?option=com_resources&task=plugin&trigger=onResourcesRateItem&action=rateitem&no_html=1&rid='+id+'&refid='+id+'&ajax=1&vote='+s,{
					//new Ajax('/resources/'+rid+'/reviews/?no_html=1&action=rateitem&refid='+id+'&ajax=1&vote='+s,{
						'method' : 'get',
						'update' : $(pn)
					}).request();				
				}
				
			}
		}
		
		
		// Recommendations web service
		var recoms = $('recommendations-section');
		if (recoms) {
			var sbjt = $('recommendations-subject')
			if (sbjt) {
				sbjt.empty();
				imgpath = '/components/com_resources/images/loading.gif';
				var p = new Element('p', {'id':'loading-section'});
				var img = new Element('img', {'id':'loading-img','src':imgpath}).injectInside(p);
				p.injectInside(sbjt);
			
				var rid = $('rid');
				if (rid) {
					new Ajax('index.php?option=com_resources&task=plugin&trigger=onResourcesRecoms&no_html=1&rid='+rid.value,{
							'method' : 'get',
							'update' : sbjt
						}).request();
				}
			}
		}
	}
}

window.addEvent('domready', HUB.Resources.initialize);
