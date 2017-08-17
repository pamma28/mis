<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//core controller telah login
class Logged_Controller extends CI_Controller {
    protected $logged,$role;

    public function __construct() {
        parent::__construct();
        $this->logged     = $this->session->userdata('logged');
        $this->role     = $this->session->userdata('role');
        # or your own way to check if the user is logged in
        $page           = uri_string();

        if ($this->logged==false) {
            header("Location:".base_url('Login')."?rdr=" . urlencode($_SERVER['REQUEST_URI']));
        }
    }
}

//core controller admin
class Admin_Controller extends Logged_Controller {
    protected $role;

    public function __construct() {
        parent::__construct();

        $this->role     = $this->session->userdata('role');
        $page           = uri_string();

        if ($this->role<>1) {
            header("Location:".base_url('Login')."?rdr=" . urlencode($_SERVER['REQUEST_URI']));
        }
    }
}

//core controller organizer
class Org_Controller extends Logged_Controller {
    protected $role;

    public function __construct() {
        parent::__construct();

        $this->role     = $this->session->userdata('role');
        $page           = uri_string();

        if ($this->role<>2) {
            header("Location:".base_url('Login')."?rdr=" . urlencode($_SERVER['REQUEST_URI']));
        }
    }
}

//core controller member
class Mem_Controller extends Logged_Controller {
    protected $role;

    public function __construct() {
        parent::__construct();

        $this->role     = $this->session->userdata('role');
        $page           = uri_string();

        if ($this->role<>3) {
            header("Location:".base_url('Login')."?rdr=" . urlencode($_SERVER['REQUEST_URI']));
        }
    }
}
?>