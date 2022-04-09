(function($){

    // $(".prepare_input_html").html("input HTML");
    // $(".add_new_tabs").html("input HTML");
    // $(".save_tabs").html("input HTML");

        $(document).ready(function () {
            initEditor();
        });

    	/**
	 * Check if a node is blocked for processing.
	 *
	 * @param {JQuery Object} $node
	 * @return {bool} True if the DOM Element is UI Blocked, false if not.
	 */
	var is_blocked = function( $node ) {
		return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
	};

	/**
	 * Block a node visually for processing.
	 *
	 * @param {JQuery Object} $node
	 */
	var block = function( $node ) {
		if ( ! is_blocked( $node ) ) {
			$node.addClass( 'processing' ).block( {
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			} );
		}
	};

	/**
	 * Unblock a node after processing is complete.
	 *
	 * @param {JQuery Object} $node
	 */
	var unblock = function( $node ) {
		$node.removeClass( 'processing' ).unblock();
        initEditor();
	};

    function initEditor(){
        console.log("triggering Wp editor");
        $(".enable_editor").each(function(i, el){

            console.log('el', $(el).attr("id") );
            var editor_id = $(el).attr("id");

            wp.editor.remove(editor_id);
            wp.editor.initialize(
                editor_id,
                {
                  tinymce: {
                    wpautop: true,
                    plugins : 'charmap colorpicker compat3x directionality fullscreen hr image lists media paste tabfocus textcolor wordpress wpautoresize wpdialogs wpeditimage wpemoji wpgallery wplink wptextpattern wpview',
                    toolbar1: 'bold italic underline strikethrough | bullist numlist | blockquote hr wp_more | alignleft aligncenter alignright | link unlink | fullscreen | wp_adv',
                    toolbar2: 'formatselect alignjustify forecolor | pastetext removeformat charmap | outdent indent | undo redo | wp_help'
                  },
                  quicktags: true,
                  mediaButtons: true,
                }
              )

        });

    }

    function beforeSend(){
        block($(".prepare_input_html"));
    }

    function  complete(){
        unblock($(".prepare_input_html"));
    }

    $(".add_new_tabs").on("click", function(e){
        console.log("Click Detected");
        var tabsFields =  $(".tabs_fields").serializeArray();
        $.ajax({
            type: "get",
            method:"POST",
            url: ucpt.ajax_url,            
            data: {
                action:"get_empty_tab",
                data  : tabsFields,
                post_id: ucpt.post_id
            },
            dataType: "json",
            beforeSend:function(){
                beforeSend();
            },
            success: function (response) {
                $(".prepare_input_html").html(response);
                console.log("response", response);
            },
            complete:function(){
                complete();
            }
        });
    });

    $(".get_tabs").on("click", function(e){
        console.log("Click Detected");
        $.ajax({
            type: "get",
            method:"POST",
            url: ucpt.ajax_url,            
            data: {
                action:"get_tabs",
                post_id: ucpt.post_id
            },
            dataType: "json",
            beforeSend:function(){
                beforeSend();
            },
            success: function (response) {
                $(".prepare_input_html").html(response);
                console.log("response", response);
            },
            complete:function(){
                complete();
            }
        });
    });

    $(".save_tabs").on("click", function(e){

        // var tabsFields =  JSON.stringify($(".tabs_fields").serializeArray(), 0 , 3);
        var tabsFields =  $(".tabs_fields").serializeArray();

        console.log("tabsFields", tabsFields);

        $.ajax({
            type: "save_tabs",
            url: ucpt.ajax_url,
            method:"POST",          
            data: {
                action:"save_tabs",
                data  : tabsFields,
                post_id: ucpt.post_id
            },
            dataType: "json",
            beforeSend:function(){
                beforeSend();
            },
            success: function (response) {
                console.log("Response After Save", response);
            },
            complete:function(){
                complete();
            }
        });

        console.log("editor Creating", tabsFields );

    });

    $("body").on("click", ".delete_tab", function(e){

        var tabId = $(this).data("tab_id");
        console.log("tab id: ", tabId);

        $.ajax({
            type: "save_tabs",
            url: ucpt.ajax_url,
            method:"POST",          
            data: {
                action:"delete_tabs",
                delete_tab_id  : tabId,
                post_id: ucpt.post_id
            },
            dataType: "json",
            beforeSend:function(){
                beforeSend();
            },
            success: function (response) {
                $(".prepare_input_html").html(response);
                console.log("Response After Delete", response);
            },
            complete:function(){
                complete();
            }
        });

    });


})(jQuery)