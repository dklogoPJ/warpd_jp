<div class="span12">

    <div class="widget">

        <div class="widget-header">
            <h3>
                <i class="icon-magic"></i>
                Finished
            </h3>
        </div> <!-- /widget-header -->

        <div class="widget-content">

            <div id="wizard" class="swMain">

                <div id="">
                    <br />
                    <div class="row-fluid">

                        <div class="span12">

                            <div id="success_msg">
                                <?php
                                $btn = "<a href=".$this->Html->url(array('controller' => 'Users', 'action' => 'login/'.$id))." class='btn btn-secondary'> Proceed to your homepage</a>"
                                ?>
                                <?php echo $this->Message->msg('Registration Success!','Thank you for registring with us!<br /><br /><br />'.$btn,'success'); ?>
                            </div>

                        </div> <!-- /span12 -->

                    </div> <!-- /row-fluid -->


                </div> <!-- /step -->

            </div> <!-- /wizard -->

        </div> <!-- /widget-content -->

        <div class="widget-toolbar">

        </div><!-- /.widget-toolbar -->

    </div> <!-- /widget -->

</div> <!-- /.span12 -->