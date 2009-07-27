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

//-------------------------------------------------------------
// Edit session titles
//-------------------------------------------------------------
var eip = new Class({
	initialize: function(els, action, params, options) {
		// Handle array of elements or single element
		if ($type(els) == 'array') {
			els.each(function(el){
				this.prepForm(el);
			}.bind(this));
		} else if ($type(els) == 'element') {
			this.prepForm(els);
		} else {
			return;
		}

		// Store the action (path to file) and params
		this.action = action;
		this.params = params;

		// Default options
		this.options = Object.extend({
			overCl: 'over',
			hiddenCl: 'hidden',
			editableCl: 'editable',
			textareaCl: 'textarea'
		}, options || {} );
	},

	prepForm: function(el) {
		var obj = this;
		el.addEvents({
			'mouseover': function(){this.addClass(obj.options.overCl);},
			'mouseout': function(){this.removeClass(obj.options.overCl);},
			'click': function(){obj.showForm(this);}
		});

	},

	showForm: function(el) {
		// Get the name (target) and id from your element
		var classes = el.getProperty('class').split(" ");
		for (i=classes.length-1;i>=0;i--) {
			if (classes[i].contains('item:')) {
				var target = classes[i].split(":")[1];
			} else if (classes[i].contains('id:')) {
				var id = classes[i].split(":")[1];
			}
		}

		// Hide your target element
		el.addClass(this.options.hiddenCl);

		// If the form exists already, let's show that
		if (el.form) {
			el.form.removeClass(this.options.hiddenCl);
			el.form[target].focus();
			return;
		}

		// Create new form
		var form = new Element('form', {
			'id': 'form_' + el.getProperty('id'),
			'action': this.action,
			'class': this.options.editableCl
		});

		// Store new form in the element
		el.form = form;

		// Create a textarea or input for user
		if (el.hasClass(this.options.textareaCl)) {
			var input = new Element('textarea', {
				'name': target
			}).appendText(el.innerHTML).injectInside(form);
		} else {
			var input = new Element('input', {
				'name': target,
				'value': el.innerHTML
			}).injectInside(form);
			//input.style.width = '120px';
		}

		// Need this to pass to the buttons
		var obj = this;

		// Add a submit button
		new Element('input', {
			'type': 'submit',
			'value': 'save',
			'events': {
				'click': function(evt){
					(new Event(evt)).stop();
					el.empty();
					el.appendText('saving...');
					obj.hideForm(form, el);
					form.send({update: el});
				}
			}
		}).injectInside(form);

		// Add a cancel button
		new Element('input', {
			'type': 'button',
			'value': 'cancel',
			'events': {
				'click': function(form, el){
					obj.hideForm(form, el);
				}.pass([form, el])
			}
		}).injectInside(form);

		// For every param, add a hidden input
		for (param in this.params) {
			new Element('input', {
				'type': 'hidden',
				'name': param,
				'value': this.params[param]
			}).injectInside(form);
		}

		//
		new Element('input', {
			'type': 'hidden',
			'name': 'id',
			'value': id
		}).injectInside(form);

		// Add the form after the target element
		form.injectAfter(el);

		// Focus on the input
		input.focus();
	},

	hideForm: function(form, el) {
		form.addClass(this.options.hiddenCl);
		el.removeClass(this.options.hiddenCl);
	}
});

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//-------------------------------------------------------------
// MW scripts
//
// NOTE: 'theapp' is a java app and using the MooTools method
// of $('theapp') to retrieve it seems to throw errors, so
// we use document.getElementById instead.
//-------------------------------------------------------------
HUB.Mw = {
	// Helper function used to load the proper applet (signed or unsigned).
	loadApplet: function() {
		// For now, we just load the unsigned applet.
		// Someday, the loadApplet() function will examine
		// the Mambo session table to decide which applet to load.
		if ($('signedapplet').value == '1') {
			loadSignedApplet();
		} else {
			loadUnsignedApplet();
		}
	},

	// Inform Mambo whether session needs signed applet.
	sessionUsesSignedApplet: function(value) {
		// Value should be either true or false.

		// This function doesn't do anything yet.
		// It will be called from the middleware.
		if (value) {
			var myAjax = new Ajax('index.php?option=com_mw&task=signed&no_html=1').request();
			var signed = $('signedapplet');
			signed.value = 1;
		}
	},
		
	// Clear the static troubleshooting message
	clearTroubleshoot: function() {
		var trouble = $('troubleshoot');
		if (trouble) {
			var par = trouble.parentNode;
			par.removeChild(trouble);
		}
	},
	
	// Tell user that we're connecting to the tool session.
	connectingTool: function() {
		document.getElementById('theapp').style.visibility = 'hidden';
	},
	
	// Delete the "Connecting..." message.
	cancelConnecting: function() {
		HUB.Mw.cancelTimeout();
		
		var theapp = document.getElementById('theapp');
		if (theapp) {
			theapp.style.visibility = 'visible';
			$('app-wrap').style.background = '';
		}
	},

	// Start a timer to show Java failure.
	appletTimeoutID: 0,
	
	// Show a message saying that Java didn't appear to work.
	appletTimeout: function() {
		HUB.Mw.clearTroubleshoot();
		HUB.Mw.cancelConnecting();

		var theapp = document.getElementById('theapp');
		if (theapp) {
			var par = theapp.parentNode;
			par.removeChild(theapp);
		}

		var errdiv = document.createElement('div');
		errdiv.id = 'theapp';
		errdiv.innerHTML = '<p class="error">' +
				'It appears that the Java environment did not ' +
				'start properly.  Please make sure that you ' +
				'have Java installed and enabled for your web ' +
				'browser.  The version of the Java environment ' +
				'must be greater than or equal to 1.4.  ' +
				'(<a href="/kb/misc/java/">How do I do this?</a>)  ' +
				'Without Java support you will not be able to ' +
				'view any applications.' +
				'</p>';
		par.appendChild(errdiv);
	},
	
	startAppletTimeout: function() {
		var timeout = 30;
		//if (HUB.Mw.checkJavaBug() == 0) {
			HUB.Mw.appletTimeoutID = self.setTimeout("HUB.Mw.appletTimeout()", timeout * 1000);
		//}
	},

	// Cancel the timer to show Java failure.
	cancelTimeout: function() {
		clearTimeout(HUB.Mw.appletTimeoutID);
	},

	// Show a message explaining that Java is not enabled.
	noJava: function() {
		HUB.Mw.cancelConnecting();
		var trouble = $('troubleshoot');
		if (!trouble) {
			return;
		}
		trouble.innerHTML = '<p class="error">' +
				'It appears that Java is either not installed or ' +
				'not enabled.  You will not be able to view tools ' +
				'until Java is enabled.<br />' +
				'(<a href="/kb/misc/java/">Learn how to enable Java</a>)  ' +
				'</p>';
	},

	// Show a message explaining that there is a browser/Java bug.
	javaBug: function() {
		HUB.Mw.cancelConnecting();
		var trouble = $('troubleshoot');
		if (!trouble) {
			return;
		}
		trouble.innerHTML ='<p class="error">' +
				'There is a problem caused by the specific version ' +
				'of Java you are using with this browser. You will ' +
				'likely not be able to view tools. There are three ' +
				'things you can try:<br /> ' +
				'1) Restart your browser and disable Javascript ' +
				'before starting a tool the ' +
				'first time and re-enable Javascript once the first ' +
				'tool is running.<br />' +
				'2) Switch to a different version of Java. ' +
				'Version 1.6.0 Update 02 (and earlier) will work ' +
				'but 1.6.0 Update 03 and 04 do not.<br>' +
				'3) Use a browser other than Firefox.<br>' +
				'(<a href="/kb/tools/unable_to_connect_error_in_firefox/">More information</a>)  ' +
				'</p>';
	},

	// Check for any Java bugs.
	checkJavaBug: function() {
		// A return value of 1 means there's a bug.
		var bv = navigator.userAgent.toLowerCase();
		if (bv.indexOf('firefox') == -1 &&
		    bv.indexOf('iceweasel') == -1) {
			// So far the only problems have been with Firefox.
			// If this is not Firefox, assume no problem.
			// Avoid future Javascript calls to invoke Java.
			return 0;
		}

		// If there's no Java, then there's a big problem.
		if (!navigator.javaEnabled || typeof java == 'undefined') {
			HUB.Mw.noJava();
			return 1;
		}

		// If the Java version is 1.6.0_{03,04} then it might not
		// work with Firefox while Javascript is enabled.  Bah.
		var jv = java.lang.System.getProperty('java.version');
		if (jv == '1.6.0_03' || jv == '1.6.0_04') {
			HUB.Mw.javaBug();
			return 1;
		}
		return 0;
	},

	// Helper function for filexfer and user-initiated alerts.
	clientAction: function(action) {
		if (action.slice(0,4) == "url ") {
			document.open(action.slice(4), '_blank', 'width=600,height=600,toolbar=no,menubar=no,scrollbars=yes,resizable=yes');
		} else if (action.slice(0,6) == "alert ") {
			alert(action.slice(6));
		} else {
			alert("Unknown action: " + action);
		}
	},

	// Helper function called by applet when the VNC server exits.
	serverExit: function() {
		window.location = "/myhub/";
	},

	// Helper function called by applet to explain signed applets.
	explainSignedApplet: function() {
		window.open('/kb/tools/signed_applet/', '_',
				'width=600,height=600,' +
				'toolbar=no,location=no,directories=no,' +
				'status=no,menubar=no,copyhistory=no,scrollbars=yes,resizable=yes');
	},

	// Force the size of the appwrap to the size of the app (plus some padding)
	forceSize: function(w,h) {
		HUB.Mw.clearTroubleshoot();
		HUB.Mw.cancelConnecting();
		
		var app = document.getElementById('theapp');
		if (app) {
			var appwrap = $('app-wrap')
			appwrap.style.height = (h+20) + 'px';
			appwrap.style.width = (w+20) + 'px';
			
			var sizex = $('sizex');
			var sizey = $('sizey');
			if (sizex) {
				sizex.value = w;
			}
			if (sizey) {
				sizey.value = h;
			}

			app.style.width = w + 'px';
			app.style.height = h + 'px';
		}
	},
	
	resizeIt: function() {
		var tw = parseFloat($('sizex').value);
		var th = parseFloat($('sizey').value);
		
		if (tw < 100) { tw = 100; }
		if (th < 100) { th = 100; }
		if (tw > 5000) { tw = 5000; }
		if (th > 5000) { th = 5000; }
		
		var appwrap = $('app-wrap');
		appwrap.style.width = (tw + 20) + 'px';
		appwrap.style.height = (th + 23) + 'px';

		var app = document.getElementById('theapp');
		if (app) {
			app.style.width = tw + 'px';
			app.style.height = th + 'px';
			app.requestResize(tw,th);
		}
	},
	
	editSessionTitle: function() {
		new eip($$('.session-title'), 'index.php', {option: 'com_mw', task: 'rename', no_html: 1});
	},

	toggleSessionList: function() {
		var alist = $('useroptions');
		if (alist) {
			var lists = alist.getElementsByTagName('a');
			if (lists) {
				for (var j=0; j < lists.length; j++) 
				{
					if (lists[j].title == 'My Sessions') {
						var sl = lists[j];
						break;
					}
				}
				sl.onclick = function() {
					var slist = $('slist');
					if (!slist.hasClass('off')) {
						slist.addClass('off');
					} else {
						slist.removeClass('off');
					}
					return false;
				}
			}
		}
	},
	
	storageMonitor: function() {
		function fetch(){			
			new Ajax('index.php?option=com_mw&task=diskusage&no_html=1&msgs=0',{
					 'method' : 'get',
					 'update' : $('diskusage')
					 }).request();
		}
		
		fetch.periodical(60000);
	},
	
	initialize: function() {
		//addParam($('theapp'), 'Cursor shape updates', 'Ignore');
		// Initiate the tabs
		var submenu = $('sub-menu');
		if (submenu) {
			$$('.tab').each(function(href) {
				href.onclick = function() { 
					var section = this.rel + '-section';

					$$('.section').each(function(sect) {
						if (sect.id == section && sect.hasClass('hide')) {
							sect.removeClass('hide');
						} else {
							if (!sect.hasClass('hide')) {
								sect.addClass('hide');
							}
						}
					}, this);
					
					$$('#sub-menu li').each(function(h) {
						if (h.hasClass('active')) {
							h.removeClass('active');
						}
					});
					
					this.parentNode.addClass('active');
					
					return false;
				}
			});
		}
		
		// Initiate app resizing
		var appwrap = $('app-wrap');
		if (appwrap) {
			// Create new paragraph for resize
			var resizehandle = $('resizehandle');
			if (!resizehandle) {
				var app = document.getElementById('theapp');
				if (app.width < 100) {
					app.width = 100;
				}
				if (app.height < 100) { 
					app.height = 100; 
					appwrap.style.height = (parseFloat(app.height) + 20) + 'px';
				}
				appwrap.style.width = (parseFloat(app.width) + 20) + 'px';
				
				// The next chunk of code generates the following HTML:
				// <p class="resize">
				//   <input type="text" size="4" id="sizey" value="##" onchange="function() { resizeIt(); }" />
				//   x
				//   <input type="text" size="4" id="sizex" value="##" onchange="function() { resizeIt(); }" />
				//   <img id="resizehandle" src="templates/stolas/images/corner.png" alt="resize" onmousedown="dragStart('resize', event, this);" />
				// </p>
				var p = document.createElement('p');
				p.setAttribute('id','resize');

				var ptxt = document.createTextNode(' x ');
				var psx = document.createElement('input');
				psx.setAttribute('type','text');
				psx.setAttribute('size','4');
				psx.setAttribute('id','sizex');
				psx.value = parseFloat(app.width);
				psx.onchange = function() { HUB.Mw.resizeIt(); }
				
				var psy = document.createElement('input');
				psy.setAttribute('type','text');
				psy.setAttribute('size','4');
				psy.setAttribute('id','sizey');
				psy.value = parseFloat(app.height);
				psy.onchange = function() { HUB.Mw.resizeIt(); }
				
				var pimg = document.createElement('img');
				pimg.setAttribute('src','/components/com_mw/images/corner.png');
				pimg.setAttribute('alt','resize');
				pimg.setAttribute('id','resizehandle');

				p.appendChild(psx);
				p.appendChild(ptxt);
				p.appendChild(psy);
				p.appendChild(pimg);
		
				appwrap.appendChild(p);
			}
			
			// Init the resizing capabilities
			appwrap.makeResizable({
				handle:$('resizehandle'),
				//limit:{x: [400, 800], y: [100, 100]},
				onDrag: function(el) {
					var size = el.getCoordinates();
					$('sizex').value = size.width - 20 + 5;
					$('sizey').value = size.height - 27 + 0;
				},
				onComplete: function(el) {
					var app = document.getElementById('theapp');
					if (app) {
						var size = el.getCoordinates();
						
						app.style.width = (size.width - 15) + 'px';
						app.style.height = (size.height - 27) + 'px';
						app.requestResize((size.width - 15),(size.height - 27));
					}
				}
			});
		}
		
		// Inititate session title editing
		HUB.Mw.editSessionTitle();
		
		// Initiate the sessions list
		//HUB.Mw.toggleSessionList();
		
		// Initiate the storage usage
		HUB.Mw.storageMonitor();
	}
}

function clientAction(action) 
{
	HUB.Mw.clientAction(action);
}

function startAppletTimeout() 
{
	HUB.Mw.startAppletTimeout();
}

function cancelTimeout()
{
	HUB.Mw.cancelTimeout();
}

function connectingTool()
{
	HUB.Mw.connectingTool();
}

function forceSize(w,h)
{
	HUB.Mw.forceSize(w,h);
}

//-------------------------------------------------------------
// Add functions to load event
//-------------------------------------------------------------

window.addEvent('domready', HUB.Mw.initialize);
