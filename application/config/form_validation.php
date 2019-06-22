<?php
$config = array(
        'forgotpass' => array(
                array(
                        'field' => 'email',
                        'label' => 'Email',
                        'rules' => 'required|valid_email'
                )
        ),
        'login' => array(
                array(
                        'field' => 'email',
                        'label' => 'Email',
                        'rules' => 'required|valid_email'
                ),
                array(
                        'field' => 'password',
                        'label' => 'Password',
                        'rules' => 'required'
                )
        ),
        'resetuserpass' => array(
                array(
                        'field' => 'email',
                        'label' => 'Email',
                        'rules' => 'required'
                ),
                array(
                        'field' => 'password',
                        'label' => 'Password',
                        'rules' => 'required'
                )
        ),
        'deleteoffer' => array(
                array(
                        'field' => 'offer_id',
                        'label' => 'Offer ID',
                        'rules' => 'required'
                )
        ),
        'fav' => array(
                array(
                        'field' => 'offer_id',
                        'label' => 'Offer ID',
                        'rules' => 'required'
                )
        ),
        'editoffer' => array(
                array(
                        'field' => 'name',
                        'label' => 'Name',
                        'rules' => 'required'
                ),
                array(
                        'field' => 'start_date',
                        'label' => 'Start Date',
                        'rules' => 'required|callback_validate_datetime'
                ),
                array(
                        'field' => 'end_date',
                        'label' => 'End Date',
                        'rules' => 'required|callback_validate_datetime'
                ),
                array(
                        'field' => 'description',
                        'label' => 'Description',
                        'rules' => 'required'
                ),
                 array(
                        'field' => 'offer_id',
                        'label' => 'Offer ID',
                        'rules' => 'required'
                )
        ),
        'createoffer' => array(
                array(
                        'field' => 'name',
                        'label' => 'Name',
                        'rules' => 'required'
                ),
                array(
                        'field' => 'start_date',
                        'label' => 'Start Date',
                        'rules' => 'required|callback_validate_datetime'
                ),
                array(
                        'field' => 'end_date',
                        'label' => 'End Date',
                        'rules' => 'required|callback_validate_datetime'
                ),
                array(
                        'field' => 'description',
                        'label' => 'Description',
                        'rules' => 'required'
                )
        ),
        'editprofile' => array(
                array(
                        'field' => 'name',
                        'label' => 'Name',
                        'rules' => 'required'
                ),
                array(
                        'field' => 'user_type',
                        'label' => 'User Type',
                        'rules' => 'required|callback_validate_usertype'
                )
        ),
        'registervendor' => array(
                array(
                        'field' => 'name',
                        'label' => 'Name',
                        'rules' => 'required'
                ),
                array(
                        'field' => 'email',
                        'label' => 'Email',
                        'rules' => 'required|valid_email|callback_unique_mail'
                ),
                array(
                        'field' => 'password',
                        'label' => 'Password',
                        'rules' => 'required|min_length[6]'
                ),
                array(
                        'field' => 'device_type',
                        'label' => 'Device Type',
                        'rules' => 'required|callback_validate_devicetype'
                )
        ),
        'registeruser' => array(
                array(
                        'field' => 'name',
                        'label' => 'Name',
                        'rules' => 'required'
                ),
                array(
                        'field' => 'email',
                        'label' => 'Email',
                        'rules' => 'required|valid_email|callback_unique_mail'
                ),
                array(
                        'field' => 'password',
                        'label' => 'Password',
                        'rules' => 'required|min_length[6]'
                ),
                array(
                        'field' => 'device_type',
                        'label' => 'Device Type',
                        'rules' => 'required|callback_validate_devicetype'
                )
        )
);