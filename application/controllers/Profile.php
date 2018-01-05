<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Controller for profile
 *
 * - Change Profile
 */
class Profile extends My_Controller {

  public function __construct() {
    parent::__construct();
    
    if ($this->uri->segment(2) == 'change_password') {
      $this->page_title = __("Change Password");
    } elseif ($this->uri->segment(2) == 'edit_profile') {
      $this->page_title = __("Edit Profile");
    }
    
  }

  public function index() {
    redirect("profile/change_password");
  }

  public function change_password() {
    $this->logined_user->add_field('old_password', array(
        'type' => 'password',
        'label' => __('Old Password'),
        'rules' => array('required')
    ));
    $this->logined_user->add_field('new_password', array(
        'type' => 'password',
        'label' => __('New Password'),
        'rules' => array(
            'required',
            'min_length[6]'
        )
    ));
    $this->logined_user->add_field('re_password', array(
        'type' => 'password',
        'label' => __('Repeat Password'),
        'rules' => array(
            'required',
            'matches[new_password]'
        )
    ));

    if ($this->input->post('action') == 'process') {
      if ($this->logined_user->form_validate($this->input->post()) == FALSE) {
        
      } else {
        $old_password = $this->logined_user->old_password;
        if (my_validate_password($old_password, $this->logined_user->password)) {
          $this->logined_user->password = my_encrypt_password($this->logined_user->new_password);
          if ($this->logined_user->save()) {
            $this->logined_user->add_msg(__("You have successfully changed password. After next login, you can use the new password."));
          } else {
            $this->logined_user->add_error('old_password', __("Failed change password."));
          }
        } else {
          $this->logined_user->add_error('old_password', __("Incorrect old password."));
        }
      }
    }

    $this->load->view('profile/change_password', array(
        "user_m" => $this->logined_user
    ));
  }

  public function edit_profile() {
    if ($this->input->post('action') == 'process') {
      if ($this->logined_userinfo->form_validate($this->input->post()) == FALSE) {
        
      } else {
        $this->logined_userinfo->user_id = $this->logined_user->id;
        if ($this->logined_userinfo->save()) {
          my_set_system_message(__("Successfully saved your informations."), "success");

          redirect("profile/edit_profile/" . $this->user_m->id);
        } else {
          $this->logined_userinfo->add_error("id", __("Failed save your informations."));
        }
      }
    }

    $this->load->view('profile/edit', array(
        'userinfo_m' => $this->logined_userinfo
    ));
  }

}
