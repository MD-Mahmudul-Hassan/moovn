(function(){
    var $btns = $('.viewer li'), $stream = $('ol.stream'), $container=$('.wrapper.timeline'), $wrapper = $('.wrapper.timeline'), first_id = 'stream-first-item_', latest_id = 'stream-latest-item_';

    // show images as each image is loaded
    $stream.on('itemloaded', function(){
        var $latest = $stream.find('>#'+latest_id).removeAttr('id'),
            $first = $stream.find('>#'+first_id).removeAttr('id'),
            $target=$(), viewMode;

        if(!$first.length || !$latest.length) {
            $target = $stream.children('li');
        } else {
            $target = $latest.nextAll('li');
        }

        $stream.find('>li:first-child').attr('id', first_id);
        $stream.find('>li:last-child').attr('id', latest_id);

        viewMode = 'grid';//$container.hasClass('vertical') ? 'vertical' : ($container.hasClass('normal') ? 'grid':'classic');

        if(viewMode=='grid'){
            $target.each(function(i,v,a){
                var $li = $(this), src_g;
                var $grid_img = $li.find(".figure.grid");
                $grid_img.css("background-image", "url("+$grid_img.attr("data-310-url")+")");
            });
        }

        if(viewMode == 'vertical'){
            $('#infscr-loading').show();
            setTimeout(function(){
                arrange(forceRefresh);
                $('#infscr-loading').hide();
            },10);
        }

    });
    $stream.trigger('itemloaded');


    $btns.click(function(event){
        event.preventDefault();
        if($wrapper.hasClass('anim')) return;

        var $btn = $(this);

        // hightlight this button only
        //$btns.find('a.current').removeClass('current');
        //$btn.find('a').addClass('current');

        if(/\b(normal|vertical|classic)\b/.test($btn.attr('class'))){
            setView(RegExp.$1);
        }
    });

    $wrapper.on('redraw', function(event){
        var curMode = '';
        if(/\b(normal|vertical|classic)\b/.test($container.attr('class'))) curMode = RegExp.$1;
        if(curMode) setView(curMode, true);
    });

    function setView(mode, force){
        if(!force && $container.hasClass(mode)) return;
        var $items = $stream.find('>li');

        if($items.length>100){
            $items.filter(":eq(100)").nextAll().detach();
        }

        if(!window.Modernizr || !Modernizr.csstransitions ){
            $stream.addClass('loading');
            $wrapper.trigger('before-fadeout');
            $stream.removeClass('loading');

            $wrapper.trigger('before-fadein');
            switchTo(mode);

            if(mode=='normal'){
                $items.each(function(i,v,a){
                    var $li = $(this);
                    var $grid_img = $li.find(".figure.grid");

                    if($li.height()>400){
                        $grid_img.css("background-image", "url("+$grid_img.attr("data-ori-url")+")");
                    }else{
                        $grid_img.css("background-image", "url("+$grid_img.attr("data-310-url")+")");
                    }
                });
            }

            $stream.find('>li').css('opacity',1);
            $wrapper.trigger('after-fadein');
            return;
        }

        $wrapper.trigger('before-fadeout').addClass('anim');
        $stream.addClass('loading');

        var item,
            $visibles, visibles = [], prevVisibles, thefirst,
            offsetTop = $stream.offset().top,
            hh = $('#header-new').height(),
            sc = $(window).scrollTop(),
            wh = $(window).innerHeight(),
            f_right, f_bottom, v_right, v_bottom,
            i, c, v, d, animated = 0;

        // get visible elements
        for(i=0,c=$items.length; i < c; i++){
            item = $items[i];
            if (offsetTop + item.offsetTop + item.offsetHeight < sc + hh) {
                //item.style.visibility = 'hidden';
            } else if (offsetTop + item.offsetTop > sc + wh) {
                //item.style.visibility = 'hidden';
                break;
            } else {
                visibles[visibles.length] = item;
            }
        }
        prevVisibles = visibles;

        // get the first animated element
        for(i=0,c=Math.min(visibles.length,10),thefirst=null; i < c; i++){
            v = visibles[i];

            if( !thefirst || (thefirst.offsetLeft > v.offsetLeft) || (thefirst.offsetLeft == v.offsetLeft && thefirst.offsetTop > v.offsetTop) ) {
                thefirst = v;
            }
        }

        if(visibles.length==0) fadeIn();
        // fade out elements using delay based on the distance between each element and the first element.
        for(i=0,c=visibles.length; i < c; i++){
            v = visibles[i];

            d = Math.sqrt(Math.pow((v.offsetLeft - thefirst.offsetLeft),2) + Math.pow(Math.max(v.offsetTop-thefirst.offsetTop,0),2));
            delayOpacity(v, 0, d/5);

            if(i == c -1){
                setTimeout(fadeIn,300+d/5);
            }
        }

        function fadeIn(){
            $wrapper.trigger('before-fadein');

            if($wrapper.hasClass("wait")){
                setTimeout(fadeIn, 50);
                return;
            }

            var i, c, v, thefirst, COL_COUNT, visibles = [], item;

            if($items.length !== $stream.get(0).childNodes.length || $items.get(0).parentNode !== $stream.get(0)) $items = $stream.find('>li');
            $stream.height($stream.parent().height());

            switchTo(mode);

            if(mode=='normal'){
                $items.each(function(i,v,a){
                    var $li = $(this);
                    var $grid_img = $li.find(".figure.grid");

                    if($li.height()>400){
                        $grid_img.css("background-image", "url("+$grid_img.attr("data-ori-url")+")");
                    }else{
                        $grid_img.css("background-image", "url("+$grid_img.attr("data-310-url")+")");
                    }
                });
            }

            $stream.removeClass('loading');
            $wrapper.removeClass('anim');

            // get visible elements
            for(i=0,c=$items.length; i < c; i++){
                item = $items[i];
                if (offsetTop + item.offsetTop + item.offsetHeight < sc + hh) {
                    //item.style.visibility = 'hidden';
                } else if (offsetTop + item.offsetTop > sc + wh) {
                    //item.style.visibility = 'hidden';
                    break;
                } else {
                    visibles[visibles.length] = item;
                    item.style.opacity = 0;
                }
            }

            $wrapper.addClass('anim');

            $(visibles).css({opacity:0,visibility:''});
            COL_COUNT = Math.floor($stream.width()/$(visibles[0]).width());

            // get the first animated element
            for(i=0,c=Math.min(visibles.length,COL_COUNT),thefirst=null; i < c; i++){
                v = visibles[i];

                if( !thefirst || (thefirst.offsetLeft > v.offsetLeft) || (thefirst.offsetLeft == v.offsetLeft && thefirst.offsetTop > v.offsetTop) ) {
                    thefirst = v;
                }
            }

            // fade in elements using delay based on the distance between each element and the first element.
            if(visibles.length==0) done();
            for(i=0,c=visibles.length; i < c; i++){
                v = visibles[i];

                d = Math.sqrt(Math.pow((v.offsetLeft - thefirst.offsetLeft),2) + Math.pow(Math.max(v.offsetTop-thefirst.offsetTop,0),2));
                delayOpacity(v, 1, d/5);

                if(i == c -1) setTimeout(done, 300+d/5);
            }
        };

        function done(){
            $wrapper.removeClass('anim');
            /*if(prevVisibles && prevVisibles.length) {
             for(var i=0,c=visibles.length; i < c; i++){
             if(visibles[i].style.opacity == '0') visibles[i].style.opacity = 1;
             }
             }*/
            $stream.find('>li').css('opacity',1);
            $wrapper.trigger('after-fadein');
        };

        function delayOpacity(element, opacity, interval){
            setTimeout(function(){ element.style.opacity = opacity }, Math.floor(interval));
        };

        function switchTo(mode){
            var currentMode = $container.hasClass('vertical')?'vertical':($container.hasClass('classic')?'classic':'normal')
            $container.removeClass('vertical normal classic').addClass(mode);
            if(mode == 'vertical') {
                arrange(true);
                $.infiniteshow.option('prepare',2000);
            } else {
                $stream.css('height','');
                $.infiniteshow.option('prepare',4000);
            }
            if($.browser.msie) $.infiniteshow.option('prepare',1000);
            $.cookie.set('profile-thing-view',mode,9999);
        };

    };

    var bottoms = [0,0,0];
    function arrange(force_refresh){
        var i, c, x, w, h, nh, min, $target, $marker, $first, $img, COL_COUNT, ITEM_WIDTH;

        var ts = new Date().getTime();

        $marker = $stream.find('li.page_marker_');

        if(force_refresh || !$marker.length) {
            force_refresh = true;
            bottoms = [0,0,0];
            $target = $stream.children('li');
        } else {
            $target = $marker.nextAll('li');
        }

        if(!$target.length) return;

        $first = $target.eq(0);
        $target.eq(-1).addClass('page_marker_');
        $marker.removeClass('page_marker_');

        //ITEM_WIDTH  = parseInt($first.width());
        //COL_COUNT   = Math.floor($stream.width()/ITEM_WIDTH);
        ITEM_WIDTH = 220;
        COL_COUNT = 3;

        for(i=0,c=$target.length; i < c; i++){
            min = Math.min.apply(Math, bottoms);

            for(x=0; x < COL_COUNT; x++) if(bottoms[x] == min) break;

            //$li = $target.eq(i);
            $li = $($target[i]);
            $img = $li.find('.figure.vertical > img');
            if(!(nh = $img.attr('data-calcHeight'))){
                w = +$img.attr('data-width');
                h = +$img.attr('data-height');

                if(w && h) {
                    //nh = $img.width()/w * h;
                    nh = 210/w * h;
                    nh = Math.max(nh,150);
                    $img.attr('height', nh).data('calcHeight', nh);
                }else{
                    nh = $img.height();
                }
            }

            $li.css({top:bottoms[x], left:x*ITEM_WIDTH})
            bottoms[x] = bottoms[x] + nh + 15;
        }

        $stream.height(Math.max.apply(Math, bottoms));

    };
    $wrapper.on('arrange', function(){ arrange(true); });

    $notibar = $('.new-content');
    $notibar.off('click').on('click', function(){
        setTimeout(function(){
            $.jStorage.deleteKey("fancy.prefetch.stream");
            $.jStorage.deleteKey("first-5326601-featured");
            $.jStorage.deleteKey("first-5326601-all");
            $.jStorage.deleteKey("first-5326601-following");
            $stream.trigger('itemloaded');

            if( $container.hasClass("normal") ){
                $stream.find("li").each(function(i,v,a){
                    var $li = $(this), src_g;
                    var $grid_img = $li.find(".figure.grid");

                    if($grid_img.height()>400){
                        $grid_img.css("background-image", "url("+$grid_img.attr("data-ori-url")+")");
                    }else{
                        $grid_img.css("background-image", "url("+$grid_img.attr("data-310-url")+")");
                    }
                });
            }
        },100);
    });

    // feed selection
    var $feedtabs = $('.sorting a[data-feed]');
    var init_ts = $stream.attr("ts");
    var ttl  = 5 * 60 * 1000;

    $feedtabs.click(function(e){
        var tab = $(e.target).data("feed")||"featured";
        switchTab(tab);
        e.preventDefault();
    });

    function switchTab(tab){
        $.jStorage.deleteKey("fancy.prefetch.stream");
        $feedtabs.removeClass("current");
        var $currentTab = $feedtabs.filter("a[data-feed="+tab+"]").addClass("current");
        $url = $('a.btn-more').hide();
        $win = $(window);

        var result = null;
        $wrapper.addClass("wait");
        // hide notibar if it showing
        $notibar.hide();
        $stream.attr('ts','').data('feed-url', '/user-stream-updates?new-timeline&feed='+tab);
        var loc = tab;
        var keys = {
            timestamp : 'fancy.home-new.timestamp.'+loc,
            stream  : 'fancy.home-new.stream.'+loc,
            latest  : 'fancy.home-new.latest.'+loc,
            nextURL : 'fancy.home-new.nexturl.'+loc
        };

        if(!(result=$.jStorage.get('first-5326601-'+tab))){
/*            $.ajax({
                url : '/?new-timeline&feed='+tab,
                dataType : 'html',
                success : function(data, st, xhr) {
                    result = data;
                    $.jStorage.set('first-5326601-'+tab, result, {TTL:5*60*1000});
                },
                error : function(xhr, st, err) {
                    url = '';
                },
                complete : function(){
                }
            });
*/        }

        var swapContent = function(){
            if(!result){
                setTimeout(swapContent,50);
                return;
            }

            if($wrapper.hasClass("swapping")) return;
            $wrapper.addClass("swapping");
            $stream.find(">li").detach();

            $container.removeClass('pattern2 pattern3');
            if( $container.hasClass("normal") ){
                var patterns = ['','pattern2','pattern3'];
                var pattern = patterns[Math.floor(Math.random()*3)]
                if(pattern){
                    $container.addClass(pattern);
                }
                $stream.find("li").each(function(i,v,a){
                    var $li = $(this), src_g;
                    var $grid_img = $li.find(".figure.grid");

                    if($grid_img.height()>400){
                        $grid_img.css("background-image", "url("+$grid_img.attr("data-ori-url")+")");
                    }else{
                        $grid_img.css("background-image", "url("+$grid_img.attr("data-310-url")+")");
                    }
                });
            }

            var $sandbox = $('<div>'),
                $contentBox = $('#content ol.stream'),
                $next, $rows;

            $sandbox[0].innerHTML = result.replace(/^[\s\S]+<body.+?>|<((?:no)?script|header|nav)[\s\S]+?<\/\1>|<\/body>[\s\S]+$/ig, '');
            $next = $sandbox.find('a.btn-more');
            $rows = $sandbox.find('#content ol.stream > li');

            $contentBox.append($rows);
            if(window.Modernizr && Modernizr.csstransitions )	$rows.css('opacity',0);

            $stream.trigger('itemloaded');

            if (tab!="suggestions" && $next.length) {
                url = $next.attr('href');
                $url.attr({
                    'href' : $next.attr('href'),
                    'ts'   : $next.attr('ts')
                });
                $stream.attr("ts",$currentTab.data("ts")||init_ts);
                $(window).trigger("prefetch.infiniteshow");
            } else {
                url = ''
                $url.attr({
                    'href' : '',
                    'ts'   : ''
                });
            }

            slideshow_request_url = '/home_slideshow.json?new-timeline&feed='+tab;
            Fancy.slideshow.reset();

            $wrapper.removeClass("wait");
            $wrapper.removeClass("swapping");
        }

        var done = function(){
            //setTimeout(function(){$('#content ol.stream > li').css('opacity',1)},500);
        }

        $stream.trigger("changeloc");
        $wrapper.off('before-fadein').on('before-fadein', swapContent);
        $wrapper.off('after-fadein').on('after-fadein', done);
        $wrapper.trigger("redraw");
    }

    $stream.on('changeloc',function(){
        $stream.attr("loc", ($feedtabs.filter(".current").attr("data-feed")||"featured") );
    })

    if("vertical"=="classic"){
        $wrapper.trigger("arrange");
    }
    $(window).trigger("prefetch.infiniteshow");

    $stream.delegate('.figure-item',"mouseover",function(){
        if ($(this).parents('.timeline').hasClass('classic')==true) {
            $(this).find('.figure.classic .back')
                .width($(this).find('.figure.classic img').width())
                .height($(this).find('.figure.classic img').height())
                .css('margin-left',-($(this).find('.figure.classic img').width()/2)+'px')
                .css('margin-top',-($(this).find('.figure.classic img').height()/2)+'px')
                .css('left','50%')
                .css('top','50%')
                .end();
            $(this).find('.price').css('margin-top',($(this).find('.figure.classic').height()-$(this).find('.figure.classic img').height())/2+'px').css('margin-left',($(this).find('.figure.classic').width()-$(this).find('.figure.classic img').width())/2+'px');
            $(this).find('.share').css('margin-top',($(this).find('.figure.classic').height()-$(this).find('.figure.classic img').height())/2+'px').css('margin-right',($(this).find('.figure.classic').width()-$(this).find('.figure.classic img').width())/2+'px');
        }else{
            $(this).find('.figure.classic .back').removeAttr('style').end()
                .find('.price').removeAttr('style').end()
                .find('.figure.classic .share').removeAttr('style');
        }
    });

    $.infiniteshow({
        itemSelector:'#content ol.stream > li',
        streamSelector:'#content ol.stream',
        dataKey:'profile-thing',
        post_callback: function($items){ $('ol.stream').trigger('itemloaded') },
        prefetch:true,

        newtimeline:true
    })
    if($.browser.msie) $.infiniteshow.option('prepare',1000);
})();