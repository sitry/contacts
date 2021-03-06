<?php
class Contacts extends CI_Controller {

    public function __construct()
    {
            parent::__construct();
            $this->load->helper('url');
            $this->load->model('contact_model');
            $this->load->library('form_validation');
            $this->load->helper('form');
    }
    /**
     * Shows all contacts.
     */
    public function index()
    {
        $data['contacts']=$this->contact_model->get_all_contacts();
        $this->load->view('contact_view',$data);
    }
    /**
     * Check if input is valid and add contact.
     */
    public function contact_add()
    {
        if(!$this->input->is_ajax_request())
            show_404();
        $result=$this->validation();
        if($result===TRUE)
        {
            $data = array(
                        'first_name' => $this->input->post('first_name'),
                        'last_name' => $this->input->post('last_name'),
                        'phone' => $this->input->post('phone'),
                        'email' => $this->input->post('email'),
                );
            $insert = $this->contact_model->contact_add($data);
            echo json_encode(array("status" => TRUE));
        }
        else {
            echo json_encode(array("status" => FALSE, "field"=> $result));
        }

    }
    /**
     * retrieve contact
     * @param mixed $id the contact id.
     */
    public function ajax_edit($id)
    {
            if(!$this->input->is_ajax_request())
                show_404();
            $data = $this->contact_model->get_by_id($id);
            echo json_encode($data);
    }
    /**
     * Update contact
     */
    public function contact_update()
    {
        if(!$this->input->is_ajax_request())
            show_404();
        $data = array(
                        'first_name' => $this->input->post('first_name'),
                        'last_name' => $this->input->post('last_name'),
                        'phone' => $this->input->post('phone'),
                        'email' => $this->input->post('email'),
                );
        $result=$this->contact_model->contact_update(array('id' => $this->input->post('id')), $data);
        if((is_array($result))&&($result["code"]==-1))
            echo json_encode(array("status" => FALSE, "field"=>$result["field"]));
        else 
            echo json_encode(array("status" => TRUE));
    }
    /**
     * delete contact.
     *
     * @param mixed $id The contact id.
     */
    public function contact_delete($id)
    {
        if(!$this->input->is_ajax_request())
            show_404();
        $this->contact_model->delete_by_id($id);
        echo json_encode(array("status" => TRUE));
        return;
    }

    /**
     * make sure the phone of the contact is unique
    **/
    private function validation()
    {
        $this->form_validation->set_rules('phone','phone','is_unique[contacts.phone]');
        $this->form_validation->set_message('is_unique', "%s");
        if ($this->form_validation->run() == FALSE) 
            return strip_tags(validation_errors()); 
        return TRUE;

    }
}