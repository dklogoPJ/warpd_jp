<div class="workplace">

    <div class="page-header">
        <h1><?php echo $company_profile['name']; ?> <small> Profile</small></h1>
    </div>


    <?php
    $error = false;
    if($this->Session->check('process_error')){
        if($this->Session->read('process_error') == 'yes'){
            $error = true;
        }
        $controller->Session->delete('process_error');
    }
    ?>
    <?php
    $flash_msg = $this->Session->read('Message');
    if(isset($flash_msg['flash'])){
        ?>
        <div class="row-fluid">
            <div class="span12">
                <?php
                if($error){
                    echo $this->Message->msg('Status',$this->Session->flash(),'error',true);
                }
                else{
                    echo $this->Message->msg('Status',$this->Session->flash(),'success',true);
                }
                ?>
            </div>
        </div>
        <?php
    }
    ?>

    <div class="row-fluid">
        <div class="span6">
            <div class="profile clearfix">
                <div class="image">
                    <?php echo $this->Html->image("sitepages/$company_key/small-bg.jpg", array('alt' =>'Bg','class'=>'img-polaroid')); ?>
                    <!--<img src="img/users/user_bg.jpg">-->
                </div>
                <div class="user clearfix">
                    <div class="avatar">
                        <?php echo $this->Html->image("sitepages/$company_key/small-logo.png", array('alt' =>'logo','class'=>'img-polaroid')); ?>
                        <!--<img src="img/users/user_profile.jpg" class="img-polaroid">-->
                    </div>
                    <h2><?php echo $org['Org']['name']; ?></h2>
                    <div class="actions">

                       <!-- <div class="btn-group">
                            <button class="btn btn-small tip" data-original-title="Upload Company Logo"><span class="icon-upload icon-white"></span> Upload Logo</button>
                            <button class="btn btn-small tip" data-original-title="Upgrade Company Package"><span class="icon-share-alt icon-white"></span> Upgrade Package</button>
                        </div>-->

                    </div>
                </div>
                <div class="info">
                    <p><span class="icon-globe"></span> <span class="title">Address:</span>  <?php echo $org['Org']['address']; ?></p>
                    <p><span class="icon-gift"></span> <span class="title">Date of Registration:</span> <?php echo date("d M Y, g:ia", strtotime($org['Org']['created'])); ?></p>
                </div>

            </div>



        </div>

        <div class="span6">
            <div class="head clearfix">
                <div class="isw-edit"></div>
                <h1>Edit Company Infomation</h1>
            </div>
            <div class="block-fluid">
                <?php echo $this->Form->create('Org', array('id' => 'form','class'=>'form-horizontal'));?>
                <div class="row-form clearfix" style="border-top-width: 0px;">
                    <div class="span3">Name:</div>
                    <div class="span9">
                        <?php echo $this->Form->input('name', array('id'=>'name', 'value'=>$org['Org']['name'],'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="row-form clearfix">
                    <div class="span3">City:</div>
                    <div class="span9">
                        <?php echo $this->Form->input('city', array('id'=>'city', 'value'=>$org['Org']['city'],'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="row-form clearfix">
                    <div class="span3">Location:</div>
                    <div class="span9">
                        <?php echo $this->Form->input('location', array('id'=>'location', 'value'=>$org['Org']['location'],'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="row-form clearfix">
                    <div class="span3">Address:</div>
                    <div class="span9">
                        <?php echo $this->Form->input('address', array('id'=>'address', 'value'=>$org['Org']['address'], 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="row-form clearfix">
                    <div class="span3">Postal Code:</div>
                    <div class="span9">
                        <?php echo $this->Form->input('postal_code', array('id'=>'postal_code', 'value'=>$org['Org']['postal_code'], 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="row-form clearfix">
                    <div class="span3">Telephone:</div>
                    <div class="span9">
                        <?php echo $this->Form->input('telephone', array('id'=>'telephone', 'value'=>$org['Org']['telephone'], 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="row-form clearfix">
                    <div class="span3">Country:</div>
                    <div class="span9">
                        <?php echo $this->Form->input('country', array('id'=>'country', 'value'=>$org['Org']['country'], 'div' => false, 'label' => false)); ?>
                    </div>
                </div>

                <div class="footer tal">
                    <?php
                    if(in_array('E',$permissions)){
                     ?>
                        <button type="submit" class="btn">Update Info</button>
                    <?php
                    }
                    ?>
                    <?php echo $this->Form->input('id', array('type'=>'hidden','id'=>'id', 'value'=>$org['Org']['id'], 'div' => false, 'label' => false)); ?>
                </div>
            <?php echo $this->Form->end();?>
            </div>
        </div>
    </div>

    <div class="dr"><span></span></div>

</div>

<!-- URLs -->
<input type="hidden" id="company_save_url" value="<?php echo $this->Html->url(array('controller' => 'NpaAdmin', 'action' => 'company')); ?>" />

<!-- Le Script -->
<?php
    echo $this->Html->script('scripts/npa_company.js');
?>
