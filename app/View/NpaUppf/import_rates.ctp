<div class="workplace">

    <div class="page-header">
        <h1>Upload UPPF Rates <small> </small></h1>
    </div>

    <div class="row-fluid">
        <div class="span7">
            <?php echo $this->Form->create('Import', array('type' => 'file','id' => 'form','class'=>'form-horizontal'));?>
            <fieldset>
                <legend>Import File</legend>
                <!--<div class="uploader">

                    <span class="filename">No file selected</span>
                    <span class="action">Choose File</span>
                </div>-->

                <fieldset>
                    <?php echo $this->Form->input('attach', array('type' => 'file','id' => 'attach', 'div' => false, 'label' => false)); ?>
                    <div class="form-actions" style="padding-left: 10px;">
                        <button type="submit" class="btn btn-primary" id="upload-btn">Upload Rates </button>
                        <button type="reset" class="btn " id="cancel-btn">Cancel</button>
                        <p id="progress-msg" style="display: none">
                            <?php echo $this->Html->image('loaders/loader.gif', array('alt' =>'User name','class'=>'')); ?>
                             Please wait, the process will take a while to complete. You will get a response after completion.
                        </p>
                    </div>
                </fieldset>
                <?php echo $this->Form->end();?>
                <?php
                $flash_msg = $this->Session->read('Message');
                if($mesg){
                    ?>
                    <div id="loginmsg" style="">
                        <?php
                        // if($error){
                        echo $this->Message->msg('Rate Upload',$mesg,'warning',true);
                        /* }
                         else{
                             echo $this->Message->msg('Profile Update',$this->Session->flash(),'success',true);
                         }*/
                        ?>
                    </div>
                <?php
                }
                ?>
        </div>
    </div>

    <div class="dr"><span></span></div>

</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#upload-btn").click(function(){
            alert('o')
            $(this).hide();
            $("#cancel-btn").hide();
            $("#progress-msg").show();
        });
    });
</script>