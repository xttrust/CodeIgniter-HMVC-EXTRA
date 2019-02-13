<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class User extends MX_Controller {

    protected $model = '';
    protected $_module = '';
    protected $_logged_user = '';

    function __construct() {
        parent::__construct();
        
        $this->load->library('form_validation');
        $this->form_validation->CI = & $this;
        
        $this->load->model("user_model");
        $this->model = $this->user_model;

        // Set the module from the first uri.
        $this->load->module('site_security');
        $this->_module = $this->site_security->_get_module_name();
        // Get The logged User
		$this->_logged_user = $this->site_security->_get_logged_user();
		// Load the settings module
		$this->load->module('site_settings');
    }

    public function index() {

        $data['page_title'] = "Codeigniter 3.1.10 with HVMC in 2019 by xttrust";
        $data['page_description'] = "Codeigniter 3.1.10 with HVMC in 2019 by xttrust";
        $data['logged_user'] = $this->_logged_user;
        $data['alert'] = isset($this->session->alert) ? $this->session->alert : "";
        $data['module'] = $this->_module;
        $data['view_file'] = "index";

        echo Modules::run('template/public_full', $data);

	}

	public function manage() {

        // in future, check for security
        $this->site_security->_make_sure_is_admin();

        // Count rows for pagination
        $this->load->library('pagination');
        $config = $this->site_settings->_config_pagination("user/manage", 3);
        $result = $this->db->get('user');
        $data['total_rows'] = $config['total_rows'] = $result->num_rows();
        $this->pagination->initialize($config);

        // Get rows for display


		$data['query'] = $this->get_with_limit($config['per_page'], $this->uri->segment(3), 'register_date DESC');

        $data['page_title'] = "Administration > Manage Users";
        $data['page_description'] = "";
        $data['logged_user'] = $this->_logged_user;
        $data['alert'] = isset($this->session->alert) ? $this->session->alert : "";
        $data['module'] = $this->_module;
        $data['view_file'] = "manage";

        echo Modules::run('template/admin', $data);
	}

	public function deleteconf() {

        // in future, check for security
        $this->site_security->_make_sure_is_admin();

        $data['update_id'] = trim($this->uri->segment(3));

        if (!is_numeric($data['update_id'])) {
            redirect('admin');
        }

        $data['query'] = $this->get_where_row('id', $data['update_id']);

        $data['page_title'] = "Administration > Delete User > ".$data['query']->username;
        $data['page_description'] = "";
        $data['logged_user'] = $this->_logged_user;
        $data['alert'] = isset($this->session->alert) ? $this->session->alert : "";
        $data['module'] = $this->_module;
        $data['view_file'] = "deleteconf";

        echo Modules::run('template/admin', $data);
    }

    public function delete($id = FALSE) {
        if ($id != FALSE) {
            $this->site_security->_make_sure_is_admin();
            $id = trim($id);
            $row = $this->get_where_row('id', $id);

            if ($row) {
                // Genre found in database, attempt to delete
                if ($row->username == "admin") {
                    $message = "You can't delete the administrator of the website.";
                    $this->site_security->_alert('Danger! ', 'alert alert-danger', $message);
                    redirect("user/manage");
                } else {
                    $this->_delete($id);

                    $message = "The user was successfully deleted.";
                    $this->site_security->_alert('Info! ', 'alert alert-success', $message);
                    redirect("user/manage");
                }
            }
        } else {
            show_404();
        }
    }


    // Standard Functions for all controllers.
    function get($order_by = FALSE) {
        if ($order_by != FALSE) {
            $query = $this->model->get($order_by);
        } else {
            $query = $this->model->get();
        }

        return $query;
    }

    function search($row, $query, $order_by, $limit, $offset) {
        $query = $this->model->search($row, $query, $order_by, $limit, $offset);
        return $query;
    }

    function get_with_limit($limit, $offset, $order_by) {
        $query = $this->model->get_with_limit($limit, $offset, $order_by);
        return $query;
    }

    function get_where_id($id) {
        $query = $this->model->get_where_id($id);
        return $query;
    }
    
    function get_where($col, $value) {
        $query = $this->model->get_where($col, $value);
        return $query;
    }

    function get_where_row($col, $value) {
        $query = $this->model->get_where_row($col, $value);
        return $query;
    }

    function get_where_list($col, $value) {
        $query = $this->model->get_where_list($col, $value);
        return $query;
    }

    function _insert($data) {
        $this->model->_insert($data);
    }

    function _update($id, $data) {
        $this->model->_update($id, $data);
    }

    function _delete($id) {
        $this->model->_delete($id);
    }
    
    function count_all() {
        $count = $this->model->count_all();
        return $count;
    }

    function count_where($column, $value) {
        $count = $this->model->count_where($column, $value);
        return $count;
    }

    function get_max() {
        $max_id = $this->model->get_max();
        return $max_id;
    }

    function _custom_query($mysql_query) {
        $query = $this->model->_custom_query($mysql_query);
        return $query;
    }

    

}
