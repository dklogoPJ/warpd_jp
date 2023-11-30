<?php 
class PostDischarge extends AppModel
{
    public $name = "PostDischarge";

    public $belongsTo = array(
        'ProductType' => array(
            'className' => 'ProductType',
            'foreignKey' => 'product_type_id',
            'conditions' => '',
            'order' => '',
            'limit' => '',
            'dependent' => false
        )
    );

    public function getPostDischargeById($id) 
    {
        return $this->find('first', array(
            'conditions' => array('PostDischarge.id' => $id),
            'recursive' => -1 
        ));
    }

}
?>