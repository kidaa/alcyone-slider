$jq = jQuery.noConflict();

var alcyoneSlider = function(id,duration,transition,type,banner_height,banner_width,arrows,dots,pause,autoplay,stop,hide_arrows,hide_dots){

	this.timer_is_on = 0;
	this.i = 0; // current item in loop
	this.transition = transition;
	this.duration = duration;
	this.new_i = 0;
	this.t = null;
	this.id = id;
	this.hide_arrows = hide_arrows;
	this.hide_dots = hide_dots;
	this.stop = stop;
	this.str_pause= pause;
	this.divs = $jq('#rslider_'+this.id+'  li[id^="content-"]');
	this.banner_width = width;
	this.banner_height = banner_height;
	this.type = type;
	this.first = true;
	this.dots = dots;	
	
	
	this.init = function() {
		if (autoplay) {
			this.doTimer(0);
		}  
		if (hide_arrows) {$jq("#rslider_"+id+" #previousSlide a, #rslider_"+id+" #nextSlide a").stop().animate({opacity: 0});	}
		if (hide_dots) {$jq("#rslider_"+id+" .dots_custom").stop().animate({opacity: 0});}		
    };    
	
	if (this.dots !== "hide"){this.addDots();}			
	
	this.init();	
}

alcyoneSlider.prototype.doTimer = function(status){
	if (!this.timer_is_on){
		this.timer_is_on = 1;
		//$jq("#site-title span a").html( " Started: " + status );	
		this.timedCount(status);
	}	
}

alcyoneSlider.prototype.pause = function(){	
	clearInterval(this.t);	
    this.diff = new Date() - this.start;
	this.remaining = this.duration - this.diff;
	//$jq("#site-title span a").html("paused");	
}

alcyoneSlider.prototype.resume = function(){
	//alert(this.i +" " + this.remaining);
	var my = this;	
	new_i = parseInt(this.new_i);	
	//$jq("#site-title span a").html("resumed " + new_i + " / " + this.first);
	
	this.t = setInterval(function(){my.timedCount(new_i);},this.remaining+this.transition);		
}

alcyoneSlider.prototype.stopTimer = function(){		
	this.timer_is_on = 0;
	this.timedCount(this.new_i);
	//this.first = true;
}
	
/* ADD navigation DOTS on slider ********************************************************/
alcyoneSlider.prototype.addDots = function(){	
	
	this.arrow_left = 0;
	this.arrow_right = 0;
	this.hig_dot = this.divs.length + 1;
	
	for (a=1;a<this.hig_dot;a++) {
		$jq("#rslider_"+this.id+" #pager").append('<a href="#">'+a+'</a>');
	}
	
	$jq("#rslider_"+this.id+" #pager a:nth-child(1)").addClass("activeSlide");
	
	if (this.navigation_arrows == "dots_side") {
		this.arrow_left = $jq("#rslider_"+this.id+" #previousSlide").width();
		this.arrow_right = $jq("#rslider_"+this.id+" #nextSlide").width();
	}
	
	this.dots = 16 * this.divs.length;		
	this.banner_nav = (parseInt(this.arrow_left) + parseInt(this.arrow_right) + parseInt(this.dots))/2;				
	$jq("#rslider_"+this.id+" .banner_nav").css("margin-left", "-"+this.banner_nav+'px');
	
}	

alcyoneSlider.prototype.timedCount = function(new_i){
	var id=this.id,i=this.i,divs=this.divs,t=this.t,first=this.first,type=this.type,banner_width=this.banner_width,banner_height=this.banner_height,modern_effect=this.modern_effect,direction=this.direction;
	clearInterval(this.t);
	this.start = new Date();
	var my = this;	
	//$jq("#site-title span a").html("timedCount: " + this.i +" / " + new_i );	
	
	if (first) {			
		switch(type)
		{		
		case "horiz":
			$jq("#rslider_"+id+" .rotating_slides li").css({'position' : 'relative', 'float' : 'left', 'opacity' : '1', 'display' : 'inline-block'});								
			$jq("#rslider_"+id+" .rotating_slides").prepend($jq("#rslider_"+id+" .rotating_slides").html());				
			$jq("#rslider_"+id+" .rotating_slides").css({'position' : 'absolute', 'left' : "-"+(banner_width*divs.length)+'px', 'width' : banner_width*(divs.length*2)+'px'});
			break;
		case "vert":
			$jq("#rslider_"+id+" .rotating_slides li").css({'position' : 'relative', 'opacity' : '1', 'display' : 'block', 'height': banner_height, 'width': banner_width});								
			$jq("#rslider_"+id+" .rotating_slides").prepend($jq("#rslider_"+id+" .rotating_slides").html());				
			$jq("#rslider_"+id+" .rotating_slides").css({'position' : 'absolute', 'top' : "-"+(banner_height*divs.length)+'px', 'height' : banner_height*(divs.length*2)+'px'});
			break;
		}
		this.first = false;			
		new_i++;
		this.new_i = new_i;		
		this.t = setInterval(function(){my.timedCount(new_i);},duration );				
	} 
	else {			
		new_i = new_i % divs.length;		
		switch(type)
		{
		case "fade":
			this.doFade(new_i);
			break;
		case "horiz":
			this.doHoriz(new_i);
			break;
		case "vert":
			this.doVert(new_i);
			break;
		case "vert_stripes":
			this.doModern(new_i);
			break;
		case "horiz_stripes":
			this.doModern2(new_i);
			break;
		}
	}
}

/******* FADE effect *****************************************************************************************************/
alcyoneSlider.prototype.doFade = function(new_i){
		var id=this.id,i=this.i,divs=this.divs,t=this.t,first=false,type=this.type,banner_width=this.banner_width,banner_height=this.banner_height,modern_effect=this.modern_effect,direction=this.direction;
		var my = this;
				
		//Fade Out current...
		divs.eq(i).animate({opacity: 0}, this.transition);	
		$jq("#rslider_"+id+"  #pager a.activeSlide").removeClass("activeSlide");
		divs.eq(i).css('z-index', 3);					
		
		//Fade In new...		
		$jq("#rslider_"+id+"  #pager a:nth-child("+(new_i+1)+")").addClass("activeSlide");
		divs.eq(new_i).animate({opacity: 1}, transition).css('z-index', 4);
		this.i = new_i;
		new_i = ++new_i % divs.length;
		
		this.new_i = new_i;
		
		//$jq("#site-title span a").html("doFade: " + this.i +" / " + new_i );	
		
		if (this.timer_is_on) {
			this.t = setInterval(function(){my.timedCount(new_i);},duration+transition );	
		}
}


/******* HORIZINTAL effect *****************************************************************************************************/
alcyoneSlider.prototype.doHoriz = function(new_i){
		var id=this.id,i=this.i,divs=this.divs,t=this.t,first=false,type=this.type,banner_width=this.banner_width,banner_height=this.banner_height,modern_effect=this.modern_effect,direction=this.direction;
		var my = this;
		rel_dist = parseInt(new_i)-i;			
		move_left = ((divs.length+rel_dist) * banner_width)+"px";					
		
		$jq("#rslider_"+id+" .rotating_slides").stop(true, true).animate({left: "-"+move_left}, transition, function() {
			if ( rel_dist > 0 ) {
				$jq("#rslider_"+id+" .rotating_slides").append($jq("#rslider_"+id+" .rotating_slides li:lt("+rel_dist+")").clone());
				$jq("#rslider_"+id+" .rotating_slides li:lt("+rel_dist+")").remove();				
				$jq("#rslider_"+id+" .rotating_slides").css({left: "-"+(banner_width * divs.length)+"px"});		
			} else {
				rel_dist = -rel_dist;
				take = (parseInt(divs.length)*2)-parseInt(rel_dist)+1;
				remove = (divs.length*2)+1;
				//alert(take+' - '+remove);							
				$jq("#rslider_"+id+" .rotating_slides").prepend($jq("#rslider_"+id+" .rotating_slides li:nth-child(n+"+take+")").clone());
				$jq("#rslider_"+id+" .rotating_slides li:nth-child(n+"+remove+")").remove();			
				$jq("#rslider_"+id+" .rotating_slides").css({left: "-"+(banner_width*divs.length)+"px"});											 
			}
		});	
		$jq("#rslider_"+id+"  #pager a.activeSlide").removeClass("activeSlide");
		$jq("#rslider_"+id+"  #pager a:nth-child("+(new_i+1)+")").addClass("activeSlide");										
		
		this.i = new_i;
		new_i = ++new_i % divs.length;
		this.new_i = new_i;
		if (this.timer_is_on) {
			this.t = setInterval(function(){my.timedCount(new_i);},duration+transition );									
		}
}


/******* VERTICAL effect *****************************************************************************************************/
alcyoneSlider.prototype.doVert = function(new_i){
		var id=this.id,i=this.i,divs=this.divs,t=this.t,first=false,type=this.type,banner_width=this.banner_width,banner_height=this.banner_height,modern_effect=this.modern_effect,direction=this.direction;
		var my = this;
		rel_dist = parseInt(new_i)-i;
		move_top = ((divs.length+rel_dist) * banner_height)+"px";		
		//alert("current:"+this.i + " new:" + new_i +" id:" +id+ " rel_dist:"+rel_dist);		
	
		$jq("#rslider_"+id+" .rotating_slides").stop(true, true).animate({top: "-"+move_top}, transition, function() {
			if (rel_dist > 0) {
				$jq("#rslider_"+id+" .rotating_slides").append($jq("#rslider_"+id+" .rotating_slides li:lt("+rel_dist+")").clone());
				$jq("#rslider_"+id+" .rotating_slides li:lt("+rel_dist+")").remove();				
				$jq("#rslider_"+id+" .rotating_slides").css({top: "-"+(banner_height*divs.length)+"px"});		
			} else {
				rel_dist = -rel_dist;
				take = (parseInt(divs.length)*2)-parseInt(rel_dist)+1;
				remove = (divs.length*2)+1;				
				$jq("#rslider_"+id+" .rotating_slides").prepend($jq("#rslider_"+id+" .rotating_slides li:nth-child(n+"+take+")").clone());
				$jq("#rslider_"+id+" .rotating_slides li:nth-child(n+"+remove+")").remove();			
				$jq("#rslider_"+id+" .rotating_slides").css({top: "-"+(banner_height*divs.length)+"px"});											 
			}						
		});	
		$jq("#rslider_"+id+"  #pager a.activeSlide").removeClass("activeSlide");
		$jq("#rslider_"+id+"  #pager a:nth-child("+(new_i+1)+")").addClass("activeSlide");
		
		this.i = new_i;
		new_i = ++new_i % divs.length;	
		this.new_i = new_i;
		if (this.timer_is_on) {		
			this.t = setInterval(function(){my.timedCount(new_i);},duration+transition );
		}
}


/******* MODERN effect *****************************************************************************************************/
alcyoneSlider.prototype.doModern = function(new_i){
		var id=this.id,i=this.i,divs=this.divs,t=this.t,first=false,type=this.type,banner_width=this.banner_width,banner_height=this.banner_height,modern_effect=this.modern_effect,direction=this.direction;
		//alert(type);
		var my = this;		
		number_of_stripes = 10;
		//alert("current:"+this.i + " new:" + new_i +" id:" +id);		
		
		if (modern_effect == 0) {
			no_horiz = banner_height * number_of_stripes / banner_width ;					
			no_horiz = Math.round(no_horiz);
			modern_effect = 1;
		} else {
			no_horiz = 1;
			modern_effect = 0;
		}
		slice_width = Math.round(banner_width/number_of_stripes);
		slice_height = banner_height/no_horiz;
					
		$jq("#rslider_"+id+" .rotating-slider .slices").empty();					
		$jq("#rslider_"+id+" .rotating-slider .slices div").css({width: slice_width+"px", opacity: 1, top: "0px"});						
					
		for (h=0;h<no_horiz; h++) {
			margin_top = slice_height * h;
			for (a=0;a<number_of_stripes; a++) {
				left_margin=a*slice_width;
				$jq("#rslider_"+id+" .rotating-slider .slices").append('<div id="efect_slide_'+a+'_'+h+'" style="position:absolute;z-index:5;height:'+banner_height+'px;width:'+slice_width+'px;overflow:hidden;left:'+left_margin+'px;top:'+margin_top+'px;"></div>');					
				$jq("#rslider_"+id+" .rotating-slider .slices #efect_slide_"+a+"_"+h).append($jq("#rslider_"+id+" .rotating_slides li:nth-child("+(i+1)+") img").clone());					
				$jq("#rslider_"+id+" .rotating-slider .slices #efect_slide_"+a+"_"+h+" img").css({left: "-"+left_margin+"px", top: "-"+margin_top+"px", width: banner_width+'px', height: banner_height+'px'});					
			}	
		}		
		//alert("height:"+ banner_height+" width:"+ banner_width);
			
		divs.eq(i).animate({opacity: 0}, 0);	
		$jq("#rslider_"+id+"  #pager a.activeSlide").removeClass("activeSlide");
		divs.eq(i).css('z-index', 3);
		
		
		$jq("#rslider_"+id+"  #pager a:nth-child("+(new_i+1)+")").addClass("activeSlide");
		divs.eq(new_i).animate({opacity: 1}, 0);
		divs.eq(new_i).css('z-index', 4);
			g=0;
		new_speed = h;		
		margin_top = slice_height * h;
			if (this.direction == "left"){
				if (modern_effect == 0 ) {this.direction = "right";}
				for (e=number_of_stripes;e>=0; e--) {
					for (h=0;h<no_horiz; h++) {
						trans = (transition/number_of_stripes)*g/new_speed;
						$jq("#rslider_"+id+" .rotating-slider .slices #efect_slide_"+e+"_"+h).delay(trans).animate({opacity: "0"}, transition);														
						g++;
					}
				}		
			} else {
				if (modern_effect == 0 ) {this.direction = "left";}
				for (e=0;e<=number_of_stripes; e++) {
					for (h=0;h<no_horiz; h++) {
						trans = (transition/number_of_stripes)*g/new_speed;
						$jq("#rslider_"+id+" .rotating-slider .slices #efect_slide_"+e+"_"+h).delay(trans).animate({opacity: "0"}, transition);														
						g++;
					}
				}	
			}		
			
		this.i = new_i;
		new_i = ++new_i % divs.length;	
		this.new_i = new_i;
		if (this.timer_is_on) {
			this.t = setInterval(function(){my.timedCount(new_i);},duration+transition );							
		}
}


/******* MODENR 2 HORIZINTAL effect *****************************************************************************************************/
alcyoneSlider.prototype.doModern2 = function(new_i){
		var id=this.id,i=this.i,divs=this.divs,t=this.t,first=false,type=this.type,banner_width=this.banner_width,banner_height=this.banner_height,modern_effect=this.modern_effect,direction=this.direction;
		var my = this;		
		number_of_stripes = 10;
		no_horiz = 1;
		modern_effect = 0;
		
		/*
		if (modern_effect == 0) {
			no_horiz = banner_height * number_of_stripes / banner_width ;					
			no_horiz = Math.round(no_horiz);
			modern_effect = 1;
		} else {
			no_horiz = 1;
			modern_effect = 0;
		}*/
		
		//alert("current:"+this.i + " new:" + new_i +" id:" +id);
		//$jq("#site-title span a").html(this.i +" / " + new_i +" " + " /  " + this.t);	
		
		slice_width = Math.round(banner_width/number_of_stripes);
		slice_height = banner_height/no_horiz;
					
		$jq("#rslider_"+id+" .rotating-slider .slices").empty();					
		$jq("#rslider_"+id+" .rotating-slider .slices div").css({width: slice_width+"px", opacity: 1, top: "0px"});						
		
		for (h=0;h<no_horiz; h++) {
			margin_top = slice_height * h;
			for (a=0;a<number_of_stripes; a++) {
				left_margin=a*slice_width;
				$jq("#rslider_"+id+" .rotating-slider .slices").append('<div id="efect_slide_'+a+'_'+h+'" style="position:absolute;z-index:5;height:'+banner_height+'px;width:'+slice_width+'px;overflow:hidden;left:'+left_margin+'px;top:'+margin_top+'px;"></div>');					
				$jq("#rslider_"+id+" .rotating-slider .slices #efect_slide_"+a+"_"+h).append($jq("#rslider_"+id+" .rotating_slides li:nth-child("+(i+1)+") img").clone());					
				$jq("#rslider_"+id+" .rotating-slider .slices #efect_slide_"+a+"_"+h+" img").css({left: "-"+left_margin+"px", top: "-"+margin_top+"px"});					
			}	
		}
			
			
		divs.eq(i).animate({opacity: 0}, 0);	
		$jq("#rslider_"+id+"  #pager a.activeSlide").removeClass("activeSlide");
		divs.eq(i).css('z-index', 3);
						
		$jq("#rslider_"+id+"  #pager a:nth-child("+(new_i+1)+")").addClass("activeSlide");
		divs.eq(new_i).animate({opacity: 1}, 0);
		divs.eq(new_i).css('z-index', 4);
		g=0;
		new_speed = h;
		
			margin_top = slice_height * h;
			if (this.direction == "left"){
				if (modern_effect == 0 ) {this.direction = "right";}
				for (e=number_of_stripes;e>=0; e--) {
					for (h=0;h<no_horiz; h++) {
						trans = (transition/number_of_stripes)*g/new_speed;
						$jq("#rslider_"+id+" .rotating-slider .slices #efect_slide_"+e+"_"+h).delay(trans).animate({width: "0px", "margin-left": "-"+slice_width+"px" }, transition );														
						g++;
					}
				}		
			} else {
				if (modern_effect == 0 ) {this.direction = "left";}
				for (e=0;e<=number_of_stripes; e++) {
					for (h=0;h<no_horiz; h++) {
						trans = (transition/number_of_stripes)*g/new_speed;
						$jq("#rslider_"+id+" .rotating-slider .slices #efect_slide_"+e+"_"+h).delay(trans).animate({width: "0px", }, transition);														
						g++;
					}
				}	
			}
		this.i = new_i;
		new_i = ++new_i % divs.length;	
		this.new_i = new_i ;
		if (this.timer_is_on) {
			this.t = setInterval(function(){my.timedCount(new_i);},duration+transition );														
		}
}



