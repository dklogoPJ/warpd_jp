<div class="header">
    <?php
    $cp = $company_profile;
    if (isset($cp['name'])) {
        ?>
        <a class="brand" style="color:#ffffff;" href="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>"><?php echo $cp['name']; ?></a>
        <?php
    }
    else{
        ?>
        <!--<a class="logo" href="index.html"><img src="img/logo.png" alt="Aquarius -  responsive admin panel" title="Aquarius -  responsive admin panel"/></a>-->
        <a class="brand white" style="color:#ffffff;" href="<?php echo $this->Html->url(array('controller' => 'Dashboard', 'action' => 'index')); ?>">WARP-D</a>
        <?php
    }
    ?>
    <ul class="header_menu">
        <li class="list_icon"><a href="#">&nbsp;</a></li>
       <!-- <li class="settings_icon">
            <a href="#" class="link_themeSettings">&nbsp;</a>

            <div id="themeSettings" class="popup">
                <div class="head clearfix">
                    <div class="arrow"></div>
                    <span class="isw-settings"></span>
                    <span class="name">Theme settings</span>
                </div>
                <div class="body settings">
                    <div class="row-fluid">
                        <div class="span3"><strong>Style:</strong></div>
                        <div class="span9">
                            <a class="styleExample tip active" title="Default style" data-style="">&nbsp;</a>
                            <a class="styleExample silver tip" title="Silver style" data-style="silver">&nbsp;</a>
                            <a class="styleExample dark tip" title="Dark style" data-style="dark">&nbsp;</a>
                            <a class="styleExample marble tip" title="Marble style" data-style="marble">&nbsp;</a>
                            <a class="styleExample red tip" title="Red style" data-style="red">&nbsp;</a>
                            <a class="styleExample green tip" title="Green style" data-style="green">&nbsp;</a>
                            <a class="styleExample lime tip" title="Lime style" data-style="lime">&nbsp;</a>
                            <a class="styleExample purple tip" title="Purple style" data-style="purple">&nbsp;</a>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span3"><strong>Background:</strong></div>
                        <div class="span9">
                            <a class="bgExample tip active" title="Default" data-style="">&nbsp;</a>
                            <a class="bgExample bgCube tip" title="Cubes" data-style="cube">&nbsp;</a>
                            <a class="bgExample bghLine tip" title="Horizontal line" data-style="hline">&nbsp;</a>
                            <a class="bgExample bgvLine tip" title="Vertical line" data-style="vline">&nbsp;</a>
                            <a class="bgExample bgDots tip" title="Dots" data-style="dots">&nbsp;</a>
                            <a class="bgExample bgCrosshatch tip" title="Crosshatch" data-style="crosshatch">&nbsp;</a>
                            <a class="bgExample bgbCrosshatch tip" title="Big crosshatch" data-style="bcrosshatch">&nbsp;</a>
                            <a class="bgExample bgGrid tip" title="Grid" data-style="grid">&nbsp;</a>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span3"><strong>Fixed layout:</strong></div>
                        <div class="span9">
                            <input type="checkbox" name="settings_fixed" value="1"/>
                        </div>
                    </div>
                    <div class="row-fluid">
                        <div class="span3"><strong>Hide menu:</strong></div>
                        <div class="span9">
                            <input type="checkbox" name="settings_menu" value="1"/>
                        </div>
                    </div>
                </div>
                <div class="footer">
                    <button class="btn link_themeSettings" type="button">Close</button>
                </div>
            </div>

        </li>-->
    </ul>
</div>

<!-- For Welcome Package -->
<div style="display: none;">
    <div id="welcome-package-window" style="width: 500px;">
        <div class="content" style="padding: 20px; margin: 0px;">
            <fieldset style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
                <legend style="font-size: 14px; font-weight: bolder; width:34%; margin: 0px; border-bottom: none">Welcome Package</legend>
                <p>
                    <span style="font-size: 14px; font-family: Bookman Old Style">
                        Welcome to WARP-D application, we are thrilled to see you using our application!
                    </span>
                </p>
                <p>
                    <a href="<?php echo $this->Html->url('/').'files/'.'WARP-D Welcome Package.pdf'; ?>" onclick="$.colorbox.close();" target="_blank"> <strong>Click here</strong> </a>
                     <span style="font-size: 14px; font-family: Bookman Old Style"> to download our
                      <a href="<?php echo $this->Html->url('/').'files/'.'WARP-D Welcome Package.pdf'; ?>" onclick="$.colorbox.close();" target="_blank"> <strong>   Welcome Package.</strong> </a>
                     </span>
                </p>
            </fieldset>
        </div>
    </div>
</div>

<input type="hidden" id="welcome-link" value="<?php echo $this->Html->url(array('controller' => 'Users', 'action' => 'welcome')); ?>" />


<script type="text/javascript">
    var auth_user = <?php echo json_encode(isset($auth_user_view) ? $auth_user_view : array()); ?>;
    $(document).ready(function () {
        if(auth_user['shown_welcome'] == 'n' && (auth_user['user_type'] == 'omc' || auth_user['user_type'] == 'bdc')){
            $.colorbox({
                inline:true,
                scrolling:false,
                overlayClose:false,
                escKey:false,
                top:'15%',
                title:'WARP-D Welcome Package',
                href:"#welcome-package-window"
            });
            //Fire ajax to set welcome to shown
            //alert('setted')
            var url = $("#welcome-link").val();
            var query = '';
            $.ajax({
                url:url,
                data:query,
                dataType:'json',
                type:'POST',
                success:function (response) {
                    var txt = '';
                    if (typeof response.mesg == 'object') {
                        for (megTxt in response.mesg) {
                            txt += response.mesg[megTxt] + '<br />';
                        }
                    }
                    else {
                        txt = response.mesg
                    }
                    if (response.code === 0) {
                       //console.log(txt);
                    }
                    else if (response.code === 1) {
                        //console.log(txt);
                    }
                },
                error:function (xhr) {
                   // console.log(xhr.responseText);
                }
            });
        }
    });
</script>
<input type="hidden" id="datepicker_btn_img" value="<?php echo Router::url('/'.'img/', true); ?>" />
