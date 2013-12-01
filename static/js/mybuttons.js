// JavaScript Document
(function() {
    tinymce.create('tinymce.plugins.mylink', {
        init: function(ed, url) {
            var imagepath = url.replace("js", "");
            ed.addButton('mymigspaymentgatewaylink', {
                title: 'My Migs Shortcode',
                image: imagepath + '/images/icon.png',
                onclick: function() {
                    showMigsPaymentGatewayPopUp('mymigspaymentgateway', ed);
                    //ed.selection.setContent('[my_migs_button amount="100"]');
                }
            });
        },
        createControl: function(n, cm) {
            return null;
        },
    });
    tinymce.PluginManager.add('mymigspaymentgatewaylink', tinymce.plugins.mylink);


})();

function showMigsPaymentGatewayPopUp(id, ed) {
    if (jQuery("#" + id).length === 0) {
        var form = jQuery('<div id="' + id + '"><table id="' + id + '-table" class="form-table">\
			<tr>\
				<th><label for="' + id + '-amount">Amount *</label></th>\
				<td><input type="text" id="' + id + '-amount" name="amount" value="100" /><br />\
				<small>Specify the amount.</small></td>\
			</tr>\
			<tr>\
				<th><label for="' + id + '-text">Text</label></th>\
				<td><input type="text" name="text" id="' + id + '-text" value="Buy now" /><br />\
				<small>Specify The button text (By default its Buy now).</small>\
			</tr>\
			<tr>\
				<th><label for="' + id + '-cssclass">Css class</label></th>\
				<td><input type="text" name="cssclass" id="' + id + '-cssclass" value="" /><br />\
				<small>Specify The cssclass (By default its Buy now).</small>\
			</tr>\\n\
                        <tr>\
				<th><label for="' + id + '-showprice">Show price in button</label></th>\
				<td><input type="checkbox" name="showprice" id="' + id + '-showprice" value="1" /><br />\
				<small>Show price in button.</small></td>\
			</tr>\
                        <tr>\
				<th><label for="' + id + '-loggedinonly">Logged in user only</label></th>\
				<td><input type="checkbox" name="loggedinonly" id="' + id + '-loggedinonly" value="1" /><br />\
				<small>Show for logged in users only.</small></td>\
			</tr>\
			<tr>\
				<th><label for="' + id + '-post_id">Post id</label></th>\
				<td><input type="text" name="post_id" id="' + id + '-post_id" value="" /><br />\
                                <small>(Advanced) To link a button to one product by default its this post.</small></td>\
			</tr>\
		</table>\
		<p class="submit">\
			<input type="button" id="' + id + '-submit" class="button-primary" value="Insert Button" name="submit" />\
		</p>\
		</div>');
        var table = form.find('table');
        form.appendTo('body').hide();

        // handles the click event of the submit button
        form.find('#' + id + '-submit').click(function() {
            // defines the options and their default values
            // again, this is not the most elegant way to do this
            // but well, this gets the job done nonetheless
            var options = {
                'amount': '',
                'text': 'Buy now',
                'cssclass': '',
                'post_id': '',
                'showprice': '',
                'loggedinonly': ''
            };
            var shortcode = '[my_migs_button';

            for (var index in options) {
                var input = table.find('#' + id + '-' + index);
                var value = table.find('#' + id + '-' + index).val();
                if ( value !== options[index] ){
                    var addfield = true;
                    if (input.attr('type') === "checkbox"){
                        if (!input.prop('checked')){
                            addfield = false;
                        }
                    }
                    if (addfield)
                        shortcode += ' ' + index + '="' + value + '"';
                }
            }

            shortcode += ']';

            // inserts the shortcode into the active editor
            tinyMCE.activeEditor.execCommand('mceInsertContent', 0, shortcode);

            // closes Thickbox
            tb_remove();
        });
    }
    var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
    W = W - 80;
    H = H - 84;
    tb_show( 'My Migs Shortcode', '#TB_inline?width=' + W + '&height=' + H + '&inlineId='+id );
}