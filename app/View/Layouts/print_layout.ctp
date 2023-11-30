<?php
/**
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.view.templates.layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <?php echo $this->Html->charset(); ?>
    <title>
        <?php echo $title_for_layout; ?>
    </title>
    <?php
    echo $this->Html->meta('icon');
    echo $this->Html->css('bootstrap');
    echo $this->Html->css('bootstrap-responsive');
    echo $this->Html->css('print');
    echo $this->Html->css('print_media', 'stylesheet', array('media' => 'print'));
    ?>
</head>
<body>
    <div class="container-fluid">
        <div class="row-fluid">
            <div class="span12">
                <div id="print_cmd" style="font-size: 12px; font-family: Verdana,Arial,sans-serif; margin: 10px 0px 10px 10px;">
                    <?php
                    $img = $this->Html->image('icon-16.png', array('alt'=>'Print','width' => '26', 'height' => '26', 'border' => '0'));
                    echo $this->Html->link($img."&nbsp; Print", 'javascript: void(0); window.print();',array('class'=>'','title'=>'Print','style'=>'','escape'=>false));
                    ?>
                </div>
            </div>
        </div><!--/row-->
        <?php
            if(!isset($no_print_header)){
        ?>
        <div class="row-fluid" style="margin-bottom: 20px;">
            <div class="span12" style="text-align: center; font-size: 15px; font-weight: bold;">
                <?php
                //$img = $this->Html->image('yutong_logo.jpg', array('alt'=>'Print','width' => '100', 'height' => '100', 'border' => '0'));
                //echo $img;
                echo $company_profile['name'];
                ?>
            </div>
        </div><!--/row-->
        <?php
            }
        ?>
        <div class="row-fluid">
            <div class="span12">
                <div class="row-fluid">
                    <div class="span12">
                        <div class="top" style="text-align: center">
                            <div> <b><u> <?php echo $print_title; ?></u></b></div>
                        </div>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span12">
                        <?php echo $content_for_layout; ?>
                    </div>
                </div>
            </div>
        </div><!--/row-->
        <div class="row-fluid">
            <div class="span12">
                <hr />
                <?php //echo $this->element('print_footer'); ?>
            </div>
        </div><!--/row-->
    </div>
</body>
</html>