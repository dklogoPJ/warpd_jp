/*
 * jQuery File Upload Plugin JS Example 8.9.1
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/* global $, window */

$(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    $('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: $("#ajax_upload_url").val(),
		maxFileSize: 6000000,
		previewCrop: false,
		acceptFileTypes: /(\.|\/)(gif|jpe?g|png|doc|docx|pdf|xls|xlsx)$/i,
        previewMaxWidth: 60,
        previewMaxHeight: 60,
        singleFileUploads: false
    });
	
	$('#fileupload').bind('fileuploaddone', function (e, data) {
		/*console.log(data.result);
		console.log(data.textStatus);
		console.log(data.jqXHR.responseText);*/
	});
    $('#fileupload').bind('fileuploadfail', function (e, data) {
		/*console.log(data.errorThrown);
		console.log(data.textStatus);
		console.log(data.jqXHR);*/
	});

    // Enable iframe cross-domain access via redirect option:
    $('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );
});
