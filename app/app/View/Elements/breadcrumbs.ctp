<div class="breadLine">

    <ul class="breadcrumb">

       <li><span class="isb-graph" style="padding: 3px 0;"></span>&nbsp;<a href="javascript:void(0);">PRICE CHANGE</a> <span class="divider"> || </span></li>

       <!-- <li><?php /*echo $this->Html->link('Dashboard', array('controller' => 'Dashboard', 'action' => 'index'));*/?><span class="divider"></span></li>-->
        <?php
           // if(($this->params['action'] != 'index' )){
        ?>
               <!-- <li><?php /*echo __(ucfirst(preg_replace('/(?=[A-Z])/',' ',$this->params['action']))) ;*/?></li>-->
        <?php
           // }
        ?>
    </ul>

    <ul id="webticker" class="breadcrumb marquee">
        <li>
        <?php
        foreach($price_change as $name => $data){
            $price = $data['price'];
            $description = $data['description'];
            $unit = $data['unit'];
            ?>
            <a href="javascript:void(0);">
                <?php echo $name.' '.$description; ?>
            </a>
            <span class="arrow-e" style="font-size:1em;"></span>
            <span class="label label-warning" style="padding: 1px 2px; font-size: 10px;"><?php echo $price.' '.$unit; ?></span>
             &nbsp;&nbsp;&nbsp;
        <?php
        }
        ?>
        </li>
    </ul>

    <ul class="buttons">
        <li>
            <a href="http://rtheconsult.com/support/support.php" target="_blank">
                <span class="icon-wrench"></span>
                <span class="text">Help</span>
            </a>
        </li>
       <!-- <li>
            <a href="#" class="link_bcPopupSearch"><span class="icon-search"></span><span class="text">Search</span></a>

            <div id="bcPopupSearch" class="popup">
                <div class="head clearfix">
                    <div class="arrow"></div>
                    <span class="isw-zoom"></span>
                    <span class="name">Search</span>
                </div>
                <div class="body search">
                    <input type="text" placeholder="Some text for search..." name="search"/>
                </div>
                <div class="footer">
                    <button class="btn" type="button">Search</button>
                    <button class="btn btn-danger link_bcPopupSearch" type="button">Close</button>
                </div>
            </div>
        </li>-->
    </ul>

</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#webticker").marquee({
            scrollSpeed: 16,
            pauseSpeed:3500
        });
    });
</script>
