(function() {
    tinymce.create('tinymce.plugins.alcyoneSlider', {        
        init : function(ed, url) {
           ed.addCommand('insert_alcyone_slider_shortcode', function() {
				tb_show("Insert Alcyone Slider shortcode", '#TB_inline?referer=profile&custom_page=alcyoneslider&type=image&height=300&width=400');
				get_ajax();                
            });			
			function get_ajax(){
				jQuery.ajax({						
						url: "admin-ajax.php",
						type: "POST",
						data: {
						action: "get_alcyone_slider_generator",													
						},						
						success: function(response){
							jQuery("#TB_ajaxContent").empty()
							jQuery("#TB_ajaxContent").css({"height" : "auto"});
							jQuery("#TB_ajaxContent").html(response);
						}
				});		
			}
            ed.addButton('add_alcyone_slider_shortcode', {
                title : 'Insert slider',
                cmd : 'insert_alcyone_slider_shortcode',
                image : url + '/insert_alcyone.png'
            });
        },
        createControl : function(n, cm) {
            return null;
        }        
    });

    tinymce.PluginManager.add('alcyoneSlider', tinymce.plugins.alcyoneSlider);
})();