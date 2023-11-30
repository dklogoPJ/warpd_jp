<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php echo $this->Html->charset(); ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <!--[if gt IE 8]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <![endif]-->
    <title>
        <?php echo $title_for_layout; ?>
    </title>
    <?php
    echo $this->Html->meta('icon');

    echo $this->Html->css('stylesheets');
    ?>
    <!--[if lt IE 8]><?php echo $this->Html->css('ie7') ?><![endif]-->
    <?php
    echo $this->Html->css('fullcalendar.print.css','stylesheet',array('media'=>'print'));
    echo $this->Html->css('custom');
    echo $this->Html->script('jquery-1.7.2.min.js');
    echo $this->Html->script('plugins/bootstrap.min.js');
    echo $this->Html->script('plugins.js');

    echo $scripts_for_layout;
    ?>
</head>
    <body>
        <div class="wrapper">
            <?php echo $this->element('loader'); ?>
            <?php echo $this->element('header'); ?>
            <div class="content" style="margin-left: 0px;">
                <?php echo $this->fetch('content'); ?>
            </div>
        </div>
        <?php //echo $this->element('sql_dump'); ?>
        <?php echo $this->element('footer'); ?>
    </body>
</html>
