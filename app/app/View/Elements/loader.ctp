<!-- Loader Bar -->
<div id="loader_bar" style="display: none;">
    <div id="content">
        <div id="gif"><?php echo $this->Html->image('loader/load-9.gif', array('width' => '20', 'height' => '20', 'align' => 'left', 'border' => '0')); ?></div>
        <div id="mesg">Processing...</div>
    </div>
</div>

<!-- Notification Blanket -->
<div id="modal_blanket" style="display: none; position: fixed; z-index: 9999; left: 0px; top: 0px; background-image: initial; background-attachment: initial; background-origin: initial; background-clip: initial; background-color: black; height: 1080px; opacity: 0.6; width: 1972px; background-position: initial initial; background-repeat: initial initial; "></div>