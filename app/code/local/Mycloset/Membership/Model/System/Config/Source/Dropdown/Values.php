<?php

class Mycloset_Membership_Model_System_Config_Source_Dropdown_Values
{
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'yes',
                'label' => 'Yes',
            ),
            array(
                'value' => 'no',
                'label' => 'No',
            ),
        );
    }
}