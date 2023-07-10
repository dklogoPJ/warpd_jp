<style type="text/css">
    .progress-bar {
        float: left;
        width: 0;
        height: 100%;
        font-size: 12px;
        line-height: 20px;
        color: #fff;
        text-align: center;
        background-color: #428bca;
        -webkit-box-shadow: inset 0 -1px 0 rgba(0,0,0,0.15);
        box-shadow: inset 0 -1px 0 rgba(0,0,0,0.15);
        -webkit-transition: width .6s ease;
        transition: width .6s ease;
    }
    .progress.active .progress-bar {
        -webkit-animation: progress-bar-stripes 2s linear infinite;
        animation: progress-bar-stripes 2s linear infinite;
    }
    .progress-striped .progress-bar-success {
        background-image: -webkit-linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);
        background-image: linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);
    }
    .progress-striped .progress-bar {
        background-image: -webkit-linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);
        background-image: linear-gradient(45deg,rgba(255,255,255,0.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,0.15) 50%,rgba(255,255,255,0.15) 75%,transparent 75%,transparent);
        background-size: 40px 40px;
    }
    .progress-bar-success {
        background-color: #5cb85c;
    }
    .label-danger{
        background-color: #d9534f;
    }
    .text-danger {
        color: #d9534f;
    }
    .table th, .table td {
        padding: 4px;
    }
    .table tbody tr td span.preview a img {
        width: 50% !important;
    }

</style>
<div id="attachment_modal" class="modal hide fade" style="width: 700px; margin:-250px 0 0 -350px ; display: none">
    <form id="fileupload" action="" method="POST" enctype="multipart/form-data">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h5>Upload Files</h5>
        </div>
        <div class="modal-body">
            <!-- The global file processing state -->
            <span class="fileupload-process"></span>
            <!-- The table listing the files available for upload/download -->
            <table id="ajax_upload_table" role="presentation" class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Preview</th>
                        <th>File</th>
                        <th>Size/Progress</th>
                        <th>Action/Info</th>
                    </tr>
                </thead>
                <tbody class="files"></tbody>
            </table>
            <input type="hidden" name="type_id" id="type_id" value="">
            <input type="hidden" name="type" id="type" value="">
            <input type="hidden" name="log_activity_type" id="log_activity_type" value="">
            <input type="hidden" name="upload_by" id="upload_by" value="<?php echo $authUser['fname'].' '.$authUser['mname'].' '.$authUser['lname']; ?>">
            <input type="hidden" name="upload_from" id="upload_from" value="<?php echo $company_profile['name']; ?>">
            <input type="hidden" name="upload_by_id" id="upload_by_id" value="<?php echo $authUser['id']; ?>">
            <input type="hidden" name="upload_from_id" id="upload_from_id" value="<?php echo $company_profile['id']; ?>">
        </div>
        <div class="modal-footer fileupload-buttonbar">
            <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>Add files...</span>
                    <input type="file" name="files[]" multiple>
                </span>
            <button type="submit" class="btn btn-primary start">
                <i class="glyphicon glyphicon-upload"></i>
                <span>Start upload</span>
            </button>
            <button type="reset" class="btn btn-warning cancel">
                <i class="glyphicon glyphicon-ban-circle"></i>
                <span>Cancel upload</span>
            </button>
            <!--<button type="button" id="export-window-btn" class="btn btn-primary">Export Now</button>-->
            <a href="#attachment_modal" id="close_attachment_btn" class="btn" data-toggle="modal">Close</a>
        </div>
    </form>
</div>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
            <button class="btn btn-primary start" disabled>
                <i class="glyphicon glyphicon-upload"></i>
                <span>Start</span>
            </button>
            {% } %}
            {% if (!i) { %}
            <button class="btn btn-warning cancel">
                <i class="glyphicon glyphicon-ban-circle"></i>
                <span>Cancel</span>
            </button>
            {% } %}
        </td>
    </tr>
    {% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
            <p class="name">
                {% if (file.url) { %}
                <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?'data-gallery':''%}>{%=file.name%}</a>
                {% } else { %}
                <span>{%=file.name%}</span>
                {% } %}
            </p>
            {% if (file.error) { %}
            <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            {% if (file.deleteUrl) { %}
            <!--<button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
            <i class="glyphicon glyphicon-trash"></i>-->
            <span>{%=file.upload_by%}</span>
            <span>({%=file.upload_from%})</span>
            <!--</button>
            <input type="checkbox" name="delete" value="1" class="toggle">-->
            {% } else { %}
           <!-- <button class="btn btn-warning cancel">
                <i class="glyphicon glyphicon-ban-circle"></i>
                <span>Cancel</span>
            </button>-->
            {% } %}
        </td>
    </tr>
    {% } %}
</script>
<?php
    echo $this->Html->script('jquery.ui.widget.js');
    echo $this->Html->script('tmpl.min.js');
    echo $this->Html->script('fileupload/load-image.min.js');
    echo $this->Html->script('jquery.iframe-transport.js');
    echo $this->Html->script('fileupload/jquery.fileupload.js');
    echo $this->Html->script('fileupload/jquery.fileupload-process.js');
    echo $this->Html->script('fileupload/jquery.fileupload-image.js');
    echo $this->Html->script('fileupload/jquery.fileupload-audio.js');
    echo $this->Html->script('fileupload/jquery.fileupload-video.js');
    echo $this->Html->script('fileupload/jquery.fileupload-validate.js');
    echo $this->Html->script('fileupload/jquery.fileupload-ui.js');
    echo $this->Html->script('fileupload/main.js');
?>