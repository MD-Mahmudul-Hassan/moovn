jQuery(function($){
	// select text range
	$.fn.selectRange=function(e,t){return this.each(function(){if(this.setSelectionRange)this.focus(),this.setSelectionRange(e,t);else if(this.createTextRange){var n=this.createTextRange();n.collapse(!0),n.moveEnd("character",t),n.moveStart("character",e),n.select()}})};

	if(location.args){
		var action = location.args['action'] || '';
		if('fancy' in location.args || action == 'fancy'){
			$('#content .figure-product a.button.fancy').click();
		} else if ('unfancy' in location.args){
			$('#content .figure-product a.button.fancyd').click();
		} else if('addtolist' in location.args){
			$('#show-add-to-list').click();
		} else if(action == 'buy') {
			$('#popup_container ul.figure-list > li > a:first').each(function(){
				var $this = $(this), img;

				img = new Image;
				img.onload = function(){
					$this.data('size', this.width+','+this.height)
					setTimeout(function(){ $('#sidebar .thing-description a').click() }, 10);
				};
				img.src = $this.attr('href');
			});
		}
	}

	$('.hotel-side2 .hotel-photo-list span img')
		.load(function(){
			$(this).each(function(){ var $img = $(this); if($img.height()<66) $img.css("height","66px"); });
		});

	var roomlist = [], roomscache = {};

	function resetResult() {
		$('.booking-result').hide();
		$('dd.btn-check').show().find('.loading').hide();
	}

	$('#sidebar')
		.on('click', '.sell', function(event){
			event.preventDefault();

			var $this = $(this), ntid = $this.attr('ntid'), ntoid = $this.attr('ntoid'), login_require = $this.attr('require_login');

			if (login_require && login_require === 'true') return require_login();
			location.href='/sales/create?ntid='+ ntid +'&ntoid='+ntoid;
		})
		.on('click', 'a.greenbook', function(){
			if($(this).attr('require_login') != undefined){
				location.href="/login?next="+location.href;
				return false;
			}

			$(this).hide();
			$('#sidebar .hotel-form dd').show().eq(0).addClass('on').find('input').focus();
			return false;
		})
		.find('div.calendar')
			.datepicker({dateFormat : 'MM d, yy', showOtherMonths: true, selectOtherMonths: true})
			.eq(0)
				.datepicker('option', 'altField', '#check-in')
				.datepicker('option', 'minDate', 1)
				.datepicker('option', 'onSelect', function(dateText, inst){
					var nextDate = new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay);
					nextDate.setDate(nextDate.getDate()+1);

					$('#check-out').focus();
					$('#sidebar div.calendar:eq(1)').datepicker('option', 'minDate', nextDate)
					resetResult();
				})
			.end()
			.eq(1)
				.datepicker('option', 'altField', '#check-out')
				.datepicker('option', 'minDate', 2)
				.datepicker('option', 'onSelect', function(){
					$('dd.people > label').click();
					resetResult();
				})
			.end()
		.end()
		.find('dd.calendar input')
			.attr('readonly', true)
			.on('focus', function(){
				$(this)
					.closest('dl')
						.find('dd').removeClass('on').end()
					.end()
					.closest('dd').addClass('on').end()
			})
		.end()
		.find('dd.people')
			.on('click', 'label', function(){
				$(this)
					.closest('.hotel-form').find('dd').removeClass('on').end().end()
					.closest('dd').addClass('on');
			})
			.on('change', 'select', function(){
				var adult = $('#adult-people').val(), child = $('#child-people').val();
				var text = adult+' Adult'+(adult>1?'s':'')+', '+child+((child>1 || child == 0)?' Children':' Child');

				$('#sidebar dd.people > label > b').text(text);

				resetResult();
			})
		.end()
		.find('dd.btn-check')
			.on('click', 'button', function(){
				if($(this).hasClass('disabled')) return false;

				$(this)
					.addClass('disabled')
					.nextAll('.loading').show().end()
					.closest('.hotel-form').find('dd').removeClass('on');

				// check avail rooms
				var params = {
					hotelId       : window.options.hotel_id,
					arrivalDate   : $('#check-in').val(),
					departureDate : $('#check-out').val(),
					csrfmiddlewaretoken : window.options.csrfmiddlewaretoken 
				};

				var adult = $('#adult-people').val();
				var child = '';

				for(var i=0;i<$('#child-people').val();i++) {
					child +=",10";
				}

				params.rooms = adult + child;

				$.ajax({
					type     : 'post',
					url      : '/ean/hotel/rooms/',
					data     : params,
					cache    : false,
					dataType : 'json',
					success  : function(json){
						var ean_error = json.EanWsError;
						var $result   = $('dl.booking-result');
						$result.find('dd').remove();

						if (ean_error == null) {
							var $template = $result.prev('script[type="fancy/template"]');
							var $dd, room;

							$('dd.btn-check').hide();
							roomlist = [];

							roomscache.arrivalDate = json.arrivalDate;
							roomscache.departureDate = json.departureDate;
							roomscache.rooms = params.rooms;

							var lowest = 99999999;
							for (var i=0,c=json.roomlist.length; i < c; i++) {
								room = json.roomlist[i];
								roomlist.push(room);
								var nightly_detail = "";
								var tax_detail = "";
								var price, description, long_description;

								if (room.supplierType == 'E') {
									if (parseFloat(room.chargeable.total) < lowest ) {
										lowest = parseFloat(room.chargeable.total);
									}

									var nightlyrates = room.nightlyrates;

									for (var j=0;j<nightlyrates.length;j++) {
										rate = nightlyrates[j];
										nightly_detail += "<li><span>" + rate.date + "</span><b>$" + parseFloat(rate.rate).toFixed(2) + " <small>" + room.chargeable.currencyCode + "</small></b></li>";
									}

									var taxes  = room.surcharges;
									if (taxes) {
										for (var j=0;j<taxes.length;j++) {
											tax = taxes[j];
											tax_detail += "<br/><li><span>" + tax.html + "</span><b>$" + parseFloat(tax.amount).toFixed(2) + " <small>" + room.chargeable.currencyCode + "</small></b></li><br/>";
										}
									}
									price = room.chargeable.total;
									description = room.RoomType.description;
									long_description = room.RoomType.descriptionLong;
								} else {
									if (room.chargeable.maxNightlyRate < lowest ) {
										lowest = room.chargeable.maxNightlyRate;
									}
									price = room.chargeable.maxNightlyRate;
									description = room.roomTypeDescription;
									long_description = "";
								}

								$dd  = $template.template({
									ROOMTYPE : description + ((room.nonRefundable == true) ? "<br/>* Non Refundable" : "") ,
									PRICE    : "$" + parseFloat(price).toFixed(2) + " " + room.chargeable.currencyCode,
									NIGHTLY : nightly_detail,
									TAXES : tax_detail,
									DETAILS  : long_description,
									INDEX : i+1,
								});

								$dd.appendTo($result);
								$('#low_rate').html("<b>$" + parseFloat(lowest).toFixed(2) +"</b> / Total");
							}
						} else {
							$('dd.btn-check').hide();
							$dd = $("<dd>" + ean_error.presentationMessage +"</dd>");
							$dd.appendTo($result);
						}
						$result.show();
					},
					error : function(jqXHR, status, error) {
						alert('An error occurred during request data.');
					},
					complete : function() {
						$('dd.btn-check')
							.find('>.loading').hide().end()
							.find('button').removeClass('disabled').end();
					}
				});

				return false;
			})
		.end()
		.find('dl.booking-result')
			.on('click', 'button.btn-bookit', function(){
				var idx = $(this).data('index');
				var room = roomlist[idx-1];

				var params = {
					hotelId  : window.options.hotel_id,
					rateCode : room.rateCode,
				};

				for(var name in roomscache) {
					params[name] = roomscache[name];
				}

				var query = '';
				for(var name in params) {
					query += '&'+name+'='+params[name];
				}

				if(query) query = query.substr(1);
				location.href = 'https://'+location.host+'/ean/hotel/book/?'+query;
			})
		.end()

	if (location.hash == '#book-now') $('#sidebar a.greenbook').click();

	// Slides
	if ($.fn.slides) {
		var $hotel_photos = $('#hotel-photos'), $frame = $hotel_photos.find('.slides_container');

		$hotel_photos
			.find('.slides_container img')
				.load(function(){
					var maxWidth = $frame.width(), maxHeight = $frame.height(), w = this.width, h = this.height, ml = 0, mt = 0;

					if(w > maxWidth) {
						w = maxWidth;
						h = parseInt(this.height * (maxWidth / this.width));
					}

					if(h > maxHeight) {
						h = maxHeight;
						w = parseInt(this.width * (maxHeight / this.height));
					}

					mt = parseInt((maxHeight - h)/2);
					ml = parseInt((maxWidth - w)/2);

					this.width  = w;
					this.height = h;
					this.style.marginTop  = mt+'px';
					this.style.marginLeft = ml+'px';
				})
			.end()
			.slides({
				preload: true,
				generateNextPrev: false,
				generatePagination: true,
				slideSpeed : 0,
				crossfade  : false
			});
	}

    var is_hotel = ($('.hotel-section').length > 0);
	// hotel image popup
	(function(){
		if(!is_hotel) return;

		var $popup_bg = null,
		    $section  = $('.hotel-section'),
		    $photo_popup = $('.popup-hotel-photo'),
		    $info_popup  = $('.popup-hotel-infomation'),
			$large_photo = $photo_popup.find('.popup-hotel-photo-frame > div');

		$section.find('.hotel-menu a').click(function(){$info_popup.trigger('show');return false});
		$section.find('.hotel-photo-list').delegate('a','click',function(){$photo_popup.trigger('show');return false});

		$popup_bg = $('<div class="popup-bg">').click(function(){$photo_popup.trigger('hide');$info_popup.trigger('hide')}).appendTo('body');

		$info_popup
			.on({
				show : function(){
					$popup_bg.show();
					$info_popup.show();

				},
				hide : function(){
					$popup_bg.hide();
					$info_popup.hide();
				}
			})
			.find('.fancy-close-x')
				.click(function(){ $info_popup.trigger('hide'); return false })
			.end()

		$photo_popup
			.on({
				show : function(){
					$popup_bg.show();
					$photo_popup.show();

					//var maxH = $(window).height() - 140;

					//if($photo_popup.height() > maxH) $photo_popup.height(maxH);

					$photo_popup.find('.pagination a').eq(0).click();
				},
				hide : function(){
					$popup_bg.hide();
					$photo_popup.hide();
				}
			})
			.find('.fancy-close-x')
				.click(function(){ $photo_popup.trigger('hide'); return false })
			.end()
			.find('.pagination a')
				.click(function(){
					var $img = $(this).find('img'), $target = $large_photo.not('.on').eq(0);
					$large_photo.removeClass('on');
					$target
						.find('img').attr({src:$img.attr('src'),alt:$img.attr('alt')}).end()
						.find('span').text($img.attr('alt')).end()
						.addClass('on');

					return false;
				})
			.end();

		$large_photo.find('img').load(function(){
			var $this = $(this), $div = $this.parent(), maxH = Math.max($large_photo.eq(0).height(), $large_photo.eq(1).height(), /*$photo_popup.find('ul.pagination').height(),*/ 420);
			$large_photo.parent().height(maxH);

			if(!$div.hasClass('on')) return;
			var t = Math.max(parseInt((maxH-$div.height())/2), 0);
			$div.css('top', t+'px');
		});
	})();

	// image list on right side of thing
	(function(){
		if(is_hotel) return;

		var $section=$('#sidebar > .thing-section'), dlg_detail = $.dialog('thing-detail'), $zoom, $crop, $zc, li_width, $c_codes, $currency, str_currency, price;

		$codes    = dlg_detail.$obj.find('.thing-info > .currency_codes');
		$currency = dlg_detail.$obj.find('.thing-info > .price > small');
		str_currency = $currency.text();
		price = $currency.attr('price');

		function text_currency(money, code) {
			var symbols = {USD:'$',CAD:'$',EUR:'€',GBP:'₤',JPY:'¥',KRW:'₩',TRY:'₺'};

			if(typeof(money) == 'number') {
				money = money.toFixed(2);
			}
			var str = str_currency.replace('%s', symbols[code]+money+' <a class="code">'+code+'</a>');
			$currency.html(str);
		};

		function show_currency(code, set_code){
			var type2 = /,23/.test($currency.attr('sample')), p = price;

			if(type2) p = p.replace(/,/g, '.').replace(/ /g, '');
			p = p.replace(/,/g, '');

			if(set_code) {
				$.ajax({
					type : 'POST',
					url  : '/set_my_currency.json',
					data : {currency_code:code}
				});
			}

 			if(code == 'USD') {
				return $currency.hide().parent().find('>.usd').html('<a class="code">USD</a>');
			}

			text_currency('...', code);
			$currency.show().parent().find('>.usd').html('USD');

			$.ajax({
				type : 'GET',
				url  : '/convert_currency.json?amount='+p+'&currency_code='+code,
				dataType : 'json',
				success  : function(json){
					if(!json || typeof(json.amount)=='undefined') return;
					var price = json.amount.toFixed(2) + '', regex = /(\d)(\d{3})([,\.]|$)/;
					while(regex.test(price)) price = price.replace(regex, '$1,$2$3');

					if(type2) price = price.replace(/,/g, ' ').replace(/\./g, ',');

					text_currency(price, code);
				}
			});
		};

		// get currency
		if(price){
			$.ajax({
				type : 'GET',
				url  : '/get_my_currency.json',
				dataType : 'json',
				success  : function(json){
					if(json && json.currency_code) show_currency(json.currency_code);
				}
			});

			$currency.parent().delegate('a.code', 'click', function(event){
				event.preventDefault();
				if($codes.is(':visible')) {
					$codes.hide();
				} else {
					$codes.appendTo(this.offsetParent).css({'top':this.offsetTop+this.offsetHeight+1,'left':this.offsetLeft}).show();
				}
			});
			$('#popup_container').click(function(event){
				if(!$(event.target).is('a.code')) $codes.hide();
			});

			$codes.delegate('a', 'click', function(event){
				event.preventDefault();
				var code = $(this).text().match(/\(([A-Z]+)\)/)[1];
				show_currency(code, true);
				$codes.hide();
			});
		}

		$section
			.find('.thing-description a,.figure-list a').click(function(event){ event.preventDefault(); dlg_detail.open() });

		dlg_detail.$obj
			.on('open', function(){
				var $this = $(this), $ul_fig = $this.find('ul.figure-list'), $li_fig = $ul_fig.find('>li');

				if(!dlg_detail.init){
					$this.data('images-count', $li_fig.length);

					$('#popup-sale-option_id').addClass('select-white').selectBox();
					$('#popup-sale-quantity').addClass('number').inputNumber();

					li_width = $li_fig.eq(0).outerWidth(true) 
					$ul_fig.width(li_width * $li_fig.length);

					dlg_detail.init = true;
				}

				$zoom = $this.find('.frame-zoom');
				$crop = $this.find('.crop');
				$zc   = $this.find('.zoom-container');

				$this.find('ul.figure-list > li:first > a').click();

				$ul_fig.css('margin-left', 0);
				$this.find('a.move').addClass('disabled');
				if($li_fig.length > 5) $this.find('a.move.next').removeClass('disabled');

				// currency codes
				$this.find('.currency_codes').hide().end();
			})
			.on('click', '.ly-close', function(){ dlg_detail.close(); return false })
			.on('click', 'a.move', function(event){
				var $this = $(this), is_next = $this.hasClass('next'), size = 5, idx, count, last_idx;

				event.preventDefault();

				if($this.hasClass('disabled')) return;
				
				idx      = dlg_detail.$obj.data('index') || 0;
				count    = dlg_detail.$obj.data('images-count') || 0;
				last_idx = Math.max(count - size, 0);

				idx += is_next?size:-size;
				idx = Math.min(Math.max(0, idx), last_idx);

				dlg_detail.$obj.data('index', idx);
				dlg_detail.$obj.find('ul.figure-list').css('margin-left', li_width * -idx);

				if(idx == 0) dlg_detail.$obj.find('a.move.prev').addClass('disabled');

				dlg_detail.$obj.find('a.move')
					.filter('.prev')[idx>0?'removeClass':'addClass']('disabled').end()
					.filter('.next')[idx<last_idx?'removeClass':'addClass']('disabled').end();
			})
			.on(
				{
					mouseenter : function(){
						$(this).addClass('hover');
						dlg_detail.$obj.find('.zoom-container').show();
					},
					mousemove : function(event){
						var $this = $(this), ratio, boundary, viewport, offset, left, top, ox, oy;

						ratio = $this.data('ratio');
						boundary = $this.data('boundary');
						viewport = $this.data('viewport').split(',');
						viewport[0] = parseInt(viewport[0]);
						viewport[1] = parseInt(viewport[1]);

						offset = $zoom.offset();
						ox = event.pageX - offset.left;
						oy = event.pageY - offset.top;

						left = Math.max(ox - viewport[0]/2, boundary.left);
						top  = Math.max(oy - viewport[1]/2, boundary.top);

						if(left+viewport[0] > boundary.right) left = boundary.right - viewport[0];
						if(top+viewport[1] > boundary.bottom) top = boundary.bottom - viewport[1];

						$crop.css({top:top, left:left}).find('>em').css({top:-top, left:-left});

						if(viewport[0] >= boundary.width) left = boundary.left;
						if(viewport[1] >= boundary.height) top = boundary.top;

						$zc.css('background-position', ((boundary.left-left)/ratio)+'px '+((boundary.top-top)/ratio)+'px');
					},
					mouseleave : function(){
						$(this).removeClass('hover');
						dlg_detail.$obj.find('.zoom-container').hide();
					}
				},
				'.frame-zoom'
			)
			.find('ul.figure-list > li > a')
				.each(function(){
					var $this = $(this), img;

					img = new Image;
					img.onload = function(){ $this.data('size', this.width+','+this.height) };
					img.src = $this.attr('href');
				})
				.click(function(event){
					if(event) event.preventDefault();

					var $this = $(this), img = $this.attr('href'), size = [0,0], ratio, boundary={}, $zoom, w, h;

				    try {
   					    size = $this.data('size').split(',')    
					}catch(err) { }

					$zoom = dlg_detail.$obj.find('span.frame-zoom');
					dlg_detail.$obj.find('span.frame-zoom, .crop > em, .zoom-container').css('background-image', 'url('+img+')').end();

					ratio = Math.min($zoom[0].offsetWidth/size[0], $zoom[0].offsetHeight/size[1]);

					boundary.width  = Math.round(size[0] * ratio);
					boundary.height = Math.round(size[1] * ratio);
					boundary.top    = Math.round(($zoom[0].offsetHeight-boundary.height)/2);
					boundary.left   = Math.round(($zoom[0].offsetWidth-boundary.width)/2);
					boundary.bottom = boundary.top  + boundary.height;
					boundary.right  = boundary.left + boundary.width;

					w = Math.floor(454*ratio);
					h = Math.floor(494*ratio);

					$zoom
						.data('boundary', boundary)
						.data('viewport', w+','+h)
						.data('ratio', ratio)
						.find('>.crop').width(w-6).height(h-6);
				})
			.end()
			.on('click', '.add_to_cart', function(){ dlg_detail.close() })
			.on('keypress', function(){
				if(event.keyCode == 27){ dlg_detail.close() }
			});
	})();

	// Recommendations
	(function(){
		var $box = $('.fancy-suggestions'), $tpl = $box.find('>script[type="fancy/template"]').remove();
		if(!window.thing_id) return;
		$.ajax({
			type : 'get',
			url  : '/might_also_fancy.json?thing_id='+window.thing_id+'&lang='+lang ,
			dataType : 'json',
            success  : function(json){
				var result = json.results, item, $item, args, w, h, to_h;
				if(!result || !result.length) return;

				$box.empty();
				for(var i=0,c=Math.min(result.length,3); i < c; i++){
					item = result[i];
					args = {
						ID   : item.id,
						URL  : item.url,
						NAME : item.name,
						THUMB_IMAGE_URL_200 : item.thumb_image_url_200,
						FANCYS : item.fancys-1,
						USER_URL  : item.user.url,
						USER_NAME : item.user.username,
						FANCY_CLASS : item["fancy'd"]?'fancyd':'fancy'
					};

					$item = $tpl.template(args);
					if(item["fancy'd"]) $item.find('.only-for-fancyd').remove();
					if(item.user.is_private) $item.find('.username > a').remove();
					if(!args.FANCYS) $item.find('.username > em').remove();

					w = item.thumb_image_url_200_width;
					h = item.thumb_image_url_200_height;

					if(w && h){
						if(w > h){
							w = Math.min(200,w);
							h = w/item.thumb_image_url_200_width*h;
						} else {
							h = Math.min(200,h);
							w = h/item.thumb_image_url_200_height*w;
						}
						$item.find('.fig-image > img').css({width:w,height:h});
					}

 					$item.appendTo($box);
				}
				to_h = $box.css('height','auto').height();
				$box.css('height','').addClass('anim').animate({height:to_h});
			},
			error : function(){
				$box.find('p.loading')
					.toggleClass('loading error')
					.find('>span')
						.text(gettext('Oops! Something went wrong.'));
			}
		});
	})();

	/**
	 * Find "featured on my profile" button and add click event handler to it
	 **/
	$('.feature,.feature-selected').click(function(){
		var $this = $(this), params = {}, is_featured = $this.hasClass('feature-selected'), url;

		// requires login?
		if($this.attr('require_login') == 'true') return require_login();

		// ignore click while requesting the url
		if($this.hasClass('loading')) return false;
		$this.addClass('loading');

		if(is_featured){
			url = '/delete_featured_find.xml';
			params.ffid = $this.attr('ffid');
		}else{
			url = '/add_featured_find.xml';
			params.object_id = $this.attr('oid');
			params.object_type = $this.attr('otype');
			params.object_owner_id = $this.attr('ooid');
			if(!params.object_owner_id) delete params.object_owner_id;
		}

		$.ajax({
			type : 'post',
			url  : url,
			data : params,
			dataType : 'xml',
			success  : function(xml){
				var $xml = $(xml), $st = $xml.find('status_code');
				if(!$st.length || $st.text() != 1) return;

				$this.removeClass('loading');

				if(is_featured){
					$this
						.removeAttr('ffid')
						.removeClass('feature-selected')
						.addClass('feature')
						.html('<i></i> '+gettext('Feature on my profile'));
				} else {
					$this
						.attr('ffid', $xml.find('id').text())
						.removeClass('feature')
						.addClass('feature-selected')
						.html('<i></i> '+gettext('Featured on my profile'));
				}
			}
		});

		return false;
	});


        $('select[name=option_id]').on('change', function(event) {
	    var val = $(this).val();
	    var soldout = window.sale_item_options[val] == 'True';
	    var $gift = $('.gift-section a.btn-campaign'), $notify = $('.gift-section a.notify');
	    var $btn_soldout = $('.greencart.add_to_cart.soldout'), $btn_cart = $('.greencart.add_to_cart').not(".soldout");
	    var is_waiting = window.user_waiting_options[val] == 'True';
	    if (soldout) {
		$notify.removeClass('hidden');
		$btn_soldout.removeClass('hidden');
		$btn_cart.addClass('hidden');
		if ($gift.hasClass('for-gifting')) {
		    $gift.addClass('hidden');
		}
		if (is_waiting) {
		    $('.gift-section .notify').addClass('notify-selected');
		} else {
		    $('.gift-section .notify').removeClass('notify-selected');
		}
	    } else {
		$notify.addClass('hidden');
		$btn_soldout.addClass('hidden');
		$btn_cart.removeClass('hidden');
		if ($gift.hasClass('for-gifting')) {
		    $gift.removeClass('hidden');
		}
	    }
	}).trigger('change');

	// "notify" button
	$('.gift-section a.notify').click(function(event) {
		var $this = $(this), _CLASS_='notify-selected', params, url, selected;
		event.stopPropagation();
		event.preventDefault();

		if($this.attr('require_login') === 'true') return require_login();

	        url = '/wait_for_product.json';
	        params = {sale_item_id : $this.attr('item_id')};
	        var option_id = $('select[name=option_id]').val();
	        if (typeof option_id !== "undefined" && option_id != null && option_id != '') {
		    params['option_id'] = option_id;
	        }
	        var remove = 0;
		if($this.hasClass(_CLASS_)){
		    remove = 1;
		    params['remove'] = remove;
		}

		$.ajax({
			type : 'post',
			url  : url,
			data : params,
			dataType : 'json',
			success  : function(json){
			    if(!json || json.status_code == undefined) return;
			    if(json.status_code == 1) {
				if (remove == 1) {
				    $this.removeClass(_CLASS_);
				    if ("option_id" in params) {
					window.user_waiting_options[option_id] = 'False';
				    }
				} else {
				    $this.addClass(_CLASS_);
				    if ("option_id" in params) {
					window.user_waiting_options[option_id] = 'True';
				    }
				}
			    } else if (json.status_code == 0 && json.message) {
				alert(json.message);
			    }
			}
		});
	});

	// "I own it" button
	$('.thing-info a.own').click(function(event) {
		var $this = $(this), _CLASS_='own-selected', params, url, selected;

		event.stopPropagation();
		event.preventDefault();

		if($this.attr('require_login') === 'true') return require_login();

		if(selected=$this.hasClass(_CLASS_)){
			url = '/delete_have_tag.xml';
			params = {rtid : $this.attr('rtid')};
		} else {
			url = '/add_have_tag.xml';
			params = {thing_id : $this.attr('tid')};
		}

		$.ajax({
			type : 'post',
			url  : url,
			data : params,
			dataType : 'xml',
			success  : function(xml){
				var $xml = $(xml), $st = $xml.find('status_code');
				if(!$st.length || $st.text() != 1) return;
				if(selected){
					$this.removeClass(_CLASS_).removeAttr('rtid');
				} else {
					$this.addClass(_CLASS_).attr('rtid', $xml.find('id').text());
				}
			}
		});
	});
	
	// report this thing
	$('#report-thing').click(function() {
		var $this = $(this);

		event.stopPropagation();
		event.preventDefault();

		if($this.attr('require_login') === 'true') return require_login();

		$.ajax({
			type : 'post',
			url  : '/report_thing.xml',
			data : {tid : $this.attr('tid')},
			dataType : 'xml',
			success  : function(xml){
				var $xml = $(xml), $st = $xml.find('status_code');
				// to do something?
			}
		});
			
		return false;
	});

	// Use natural background color - https://app.asana.com/0/369567867430/4763569164444
	(function(){
		var $canvas = $('<canvas />'), $img = $('.fig-image > img'), ctx;

		// activate this feature only when the image has extra horizontal margins
		if($img.width() >= $img.parent().width()) return;

		// check whehter canvas is supported
		if(!$canvas[0].getContext || !(ctx=$canvas[0].getContext('2d'))) return;

		var img = new Image;
		img.onload = function(){
			$canvas.attr({width:this.width,  height:this.height});

			try { ctx.drawImage(this, 0, 0); } catch(e) { return };

			var ltRGB = ctx.getImageData(0, 0, 1, 1),
			    rtRGB = ctx.getImageData(this.width-1,0,1,1),
			    lbRGB = ctx.getImageData(0, this.height-1, 1, 1),
			    rbRGB = ctx.getImageData(this.width-1, this.height-1, 1, 1),
				allWhite = true;

			$.each([ltRGB, rtRGB, lbRGB, rbRGB], function(idx, rgb){
				if(rgb2hsv.apply(this, rgb.data || rgb)[2] < 99) {
					allWhite = false;
					return false;
				}
			});

			if(allWhite) {
				$img.closest('.fig-image').css('background-color', '#fff');
			}
		};
		img.crossOrigin = 'anonymous';
		img.src = $img.attr('src')+'?_host='+(location.protocol+location.hostname).replace(/\W+/g,'_');

		// make sure the load event fires for cached images too
		if(img.complete || img.complete === undefined) {
			img.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
			img.src = $img.attr('src');
		};

		function rgb2hsv(r,g,b){
			r /= 255; g /= 255; b /= 255;
			var max = Math.max(r,g,b), min = Math.min(r,g,b), m1=max+min, m2=max-min, v=m1/2, s=0, h=0;
			if(max != min){
				s = m2 / ((v<.5)?m1:2-m1);
				if(r == max) h = (g-b)/m2;
				else if(g == max) h = 2+(b-r)/m2;
				else h = 4+(r-g)/m2;
			}
			h *= 100; s *= 100; v *= 100;
			if(h < 0) h += 360;

			return [h, s, v];
		};
	})();
});
