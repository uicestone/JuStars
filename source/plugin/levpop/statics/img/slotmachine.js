
var nowbox = 1;
var firstclick = 0;
var isfirstslot = 0;
( function($$) {
	$$.fn.slot = function(options) {
		$$('.slotsound').hide();
		$$('#loop').show();
		var animater = function(e, settings) {
			$$holder = $$(e);
			var m = 500;
			settings.speed = settings.speed + 60;
			if (settings.vertical) {
				var tf = -(settings.cHeight - settings.tHeight)
			} else {
				var tf = -(settings.cWidth - settings.tWidth)
			}
			if (settings.direction == 'ltr') {
				if (settings.vertical) {
					$$holder.css( {
						top :0
					});
					$$holder.animate( {
						top :tf
					}, settings.speed, settings.easing, function() {
						stop(m, e, settings)
					})
				} else {
					$$holder.css( {
						left :0
					});
					$$holder.animate( {
						left :tf
					}, settings.speed, settings.easing, function() {
						animater(e, settings)
					})
				}
			} else {
				if (settings.vertical) {
					$$holder.css( {
						top :tf
					});
					$$holder.animate( {
						top :0
					}, settings.speed, settings.easing, function() {
						animater(e, settings)
					})
				} else {
					$$holder.css( {
						left :tf
					});
					$$holder.animate( {
						left :0
					}, settings.speed, settings.easing, function() {
						animater(e, settings)
					})
				}
			}
		}
		var stop = function(m, e, settings) {
			if (settings.speed < m) {
				animater(e, settings)
			} else {
				randnum = settings.stopat.split("-");
				randnum = -(parseInt(randnum[nowbox - 1]) * 125);
				settings.speed = settings.speed + 1000;
				$$holder.css( {
					top :0
				});
				if (settings.result) {
					$$holder.animate( {
						top :(randnum)
					}, settings.speed, settings.easing, function() {
					})
				} else {
					$$holder.animate( {
						top :randnum
					}, settings.speed, settings.easing, function() {
					})
				}
				;
				if (nowbox < settings.totle) {
					$$holder.parent().next().slot( {
						direction :'ltr',
						vertical :true,
						speed :1,
						start :'auto',
						totle :3,
						result :settings.result,
						stopat :settings.stopat,
						isreal :settings.isreal
					});
					nowbox++
				} else {
					if (settings.result) {
						window.setTimeout(Bingo, 2000, settings.stopat, settings.isreal);
					} else {
						$$('#slotclose').show();
						$$('#lampstar').hide();
						$$('.nihong').hide();
						var stopline = settings.stopat.split("-");
						$$('.myfruits').eq(0).animate( {
							top :(-(parseInt(stopline[0]) * 125))
						});
						$$('.myfruits').eq(1).animate( {
							top :(-(parseInt(stopline[1]) * 125))
						});
						$$('.myfruits').eq(2).animate( {
							top :(-(parseInt(stopline[2]) * 125))
						});
						if (stopline[0] == 5 || stopline[1] == 5 || stopline[2] == 5) {
							setTimeout( function() {
								$$("#slotop_infop").fadeIn().fadeOut().fadeIn()
										.fadeOut().fadeIn().fadeOut().fadeIn()
										.fadeOut().fadeIn().html($$('#isaward5').html());
				            	window.setTimeout(function (){$$('.slot_btn').click();}, 10000);
							//	$$(".slotinfo").hide();
							//	$$("#slotinfo3").fadeIn('slow');
							//	$$('.slotsound').hide();
							//	$$('#nlose').show()
							}, 2000);
			            //	art.dialog({id:'msg', title:false, content:$$('#omsg').html(), lock:true, time:10, close:function (){$$('.slot_btn').click();}}); 
						} else {
							setTimeout(
									function() {
										$$("#slotop_infop").fadeIn().fadeOut()
												.fadeIn().fadeOut().fadeIn()
												.fadeOut().fadeIn().fadeOut()
												.fadeIn().html($$('#noaward5').html());
										$$(".slotinfo").hide();
										$$("#slotinfo2").fadeIn('slow');
										$$('.slotsound').hide();
										$$('#lose').show()
									}, 2000);
			            	window.setTimeout(function (){$$('.slot_btn').click();}, 5000);
						}
					};
					if (firstclick > 0) {
					}
				}
			}
		};
		return this.each( function() {
			var settings = {
				direction :'ltr',
				vertical :false,
				speed :20000,
				easing :'linear'
			};
			$$.extend(settings, options);
			settings.tHeight = $$(this).height();
			settings.tWidth = $$(this).width();
			var $$kids = $$(this).find('.myfruits').children();
			if (settings.vertical) {
				settings.cWidth = settings.tWidth;
				settings.cHeight = settings.tHeight * ($$kids.length + 1)
			} else {
				settings.cWidth = settings.tWidth * ($$kids.length + 1);
				settings.cHeight = settings.tHeight
			}
			;
			var $$holder = $$(this).find('div:first');
			$$holder.css( {
				position :'relative'
			});
			$$holder.css( {
				width :settings.cWidth,
				height :settings.cHeight
			});
			if (settings.direction == 'ltr') {
				$$holder.append($$kids.first().clone())
			} else {
				$$holder.prepend($$kids.last().clone())
			}
			;
			var $$kids = $$holder.children();
			$$(this).css( {
				overflow :'hidden',
				whiteSpace :'nowrap'
			});
			$$(this).css( {
				width :settings.tWidth,
				height :settings.tHeight
			});
			$$kids.css( {
				'float' :'left'
			});
			$$kids.css( {
				width :settings.tWidth,
				height :settings.tHeight
			});
			animater($$holder, settings)
		})
	}
}(jQuery));
var __sto = setTimeout;
window.setTimeout = function(callback, timeout, param) {
	var args = Array.prototype.slice.call(arguments, 2);
	var _cb = function() {
		if (callback.apply) callback.apply(null, args)
	};
	__sto(_cb, timeout)
}
function slotpop(popdiv, width, height, alpha) {return '';
	var A = window.pageYOffset || document.documentElement.scrollTop
			|| document.body.scrollTop || 0;
	var D = 0;
	D = Math.min(document.body.clientHeight,
			document.documentElement.clientHeight);
	if (D == 0) {
		D = Math.max(document.body.clientHeight,
				document.documentElement.clientHeight)
	}
	var topheight = (A + (D - 300) / 2) - 50 + "px";
	$$("#popbg").css( {
		height :$$(document).height(),
		width :document.documentElement.clientWidth,
		filter :"alpha(opacity=" + alpha + ")",
		opacity :alpha
	})
	$$("#popbg").fadeIn();
	$$(popdiv).css({left :((document.documentElement.clientWidth) / 2 - (parseInt(width) / 2))+ "px",
						top :topheight
					});
	$$(popdiv).show();
}
function popdiv(popdiv, width, height, alpha) {
	var A = window.pageYOffset || document.documentElement.scrollTop
			|| document.body.scrollTop || 0;
	var D = 0;
	D = Math.min(document.body.clientHeight,
			document.documentElement.clientHeight);
	if (D == 0) {
		D = Math.max(document.body.clientHeight,
				document.documentElement.clientHeight)
	}
	var topheight = (A + (D - 300) / 2) - 50 + "px";
	$$("#popbg").css( {
		height :$$(document).height(),
		width :document.documentElement.clientWidth,
		filter :"alpha(opacity=" + alpha + ")",
		opacity :alpha
	})
	$$("#popbg").fadeIn();
	$$(popdiv).css( {
		left :(($$(document).width()) / 2 - (parseInt(width) / 2)) + "px",
		top :topheight
	});
	$$(popdiv).show();
}