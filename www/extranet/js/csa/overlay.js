var overlay = null;

function CSAOverlay(id, options) {
    this.id = id;
    this.instance = $('#' + this.id);
    this.instance_count = 0;

    this.backgroundColor = '#666';
    this.opacity = '30';
    this.zIndex = '4990';

    if( options != undefined){
        if( options['backgroundColor'] != undefined )
            this.backgroundColor = options['backgroundColor'];

        if( options['opacity'] != undefined )
            this.opacity = options['opacity'];

        if( options['zIndex'] != undefined )
         this.zIndex = options['zIndex'];
    }

    this.show = function(){
        this.instance_count++;

        if( this.instance.size() == 0){
           $('body').append('<div id="overlay" style="display: none">&nbsp;</div>');
            this.instance = $('#overlay');
            var window_height = $(window).height() + 'px';
            var window_width = $(window).width() + 'px';
            var loading = $( "#loading" );
            var loadLeft  = ($(window).width() / 2) - (loading.width() /2);
            var loadTop  = ($(window).height() / 2) + ($(window).scrollTop() - loading.height()) ;
            loading.css({'left' : loadLeft +'px', 'top' : loadTop+'px'});
            this.instance.css({
                'position': 'fixed',
                'top': '0px',
                'left': '0px',
                'height': window_height,
                'width': window_width,
                'opacity': this.opacity / 100,
                'filter': 'alpha(opacity='+this.opacity+')',
                '-moz-opacity': this.opacity / 100,
                'background-color': this.backgroundColor,
                'z-index': this.zIndex
            });
        }

        if( this.instance_count <= 1){
            this.instance.fadeIn();
            $( "#loading" ).show();
        }
    };
    this.hide = function(){
        this.instance_count--;
        if( this.instance_count <= 0){
            this.instance.fadeOut();
            $( "#loading" ).hide();
        }

    };
}
