<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends My_Controller
{

  public function __construct()
  {
    parent::__construct();

    $this->page_title = __("Products");
    $this->load->model("product_m");
    $this->load->model("site_product_m");
  }

  public function index()
  {
    $product_id = $this->uri->rsegment(3);
    if ($product_id) {
      $this->product_m->get_by_id($product_id);
    }
    if ($this->input->post('action') == 'process') {
      if ($this->product_m->form_validate($this->input->post()) == FALSE) {

      } else {
        if ($this->product_m->save()) {
          my_set_system_message(__("Successfully added Product."), "success");
          redirect("products");
        } else {
          $this->product_m->add_error("id", __("Failed add Product."));
        }
      }
    }
    $this->load->view('products/list');
  }

  public function ajax_get_products() {
    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');
    $search = $this->input->post('search');
    $result = $this->product_m->find($search['value'], $order, $start, $length);
    $products = array();
    if ($result['count'] > 0) {
      foreach ($result['data'] as $product) {
        $products[] = array(
          "index" => ( ++$start),
          "product_code" => $product->product_code,
          "product_type" => $product->product_type,
          "product_description" => $product->product_description,
          "over_price" => $product->over_price,
//          "price" => $product->price,
          "actions" => my_make_table_edit_btn(base_url('products/index/'. $product->id))
            . my_make_table_delete_btn('javascript:delete_product('. $product->id .')')
        );
      }
    }

    $returnData = array(
      "draw" => $draw,
      'recordsTotal' => $result['total'],
      'recordsFiltered' => $result['count'],
      'data' => $products
    );

    header('Content-Type: application/json');
    echo json_encode($returnData);
  }

  public function ajax_delete_product() {
    $id = $this->uri->rsegment(3);
    $this->product_m->get_by_id($id);
    if ($this->product_m->is_exists()) {
      $this->product_m->delete();
    }
  }

  public function sites()
  {
    $this->site_id = $this->uri->rsegment(3);
    $this->product_id = $this->uri->rsegment(4);

    $this->product_m->get_by_id($this->product_id);

    $this->db->where("site_id", $this->site_id);
    $this->site_product_m->get_by_product_id($this->product_id);
    $this->site_product_m->site_id = $this->site_id;
    $this->site_product_m->product_id = $this->product_id;
    $this->site_product_m->product_code = $this->product_m->product_code;

    if ($this->input->post('action') == 'process') {
      if ($this->site_product_m->form_validate($this->input->post()) == FALSE) {

      } else {

        $product = $this->product_m->get_by_id($this->input->post('product_id'));
        if ($product) {
          if ($this->site_product_m->save()) {
            my_set_system_message(__("Successfully saved Product."), "success");
            redirect('products/sites');
          } else {
            $this->site_product_m->add_error("id", __("Failed save Product."));
          }
        } else {
          my_set_system_message(__("Please enter product info."), "danger");
          redirect('products/sites');
        }
      }
    }
    $this->load->model('site_m');
    $this->sites = $this->site_m->get_all_sites();

    $this->load->view('products/site_products');
  }

  public function ajax_get_site_products() {
    $products = array();

    $draw = $this->input->post('draw');
    $start = $this->input->post('start');
    $length = $this->input->post('length');
    $order = $this->input->post('order');
    $search = $this->input->post('search');
    $site_id = $this->input->post('site_id');


    if ($site_id) {

      $where = "";
      if ($search['value']) {
        $where .= " WHERE p.product_code LIKE '%" . $search['value'] . "%'";
        $where .= " OR p.product_description LIKE '%" . $search['value'] . "%'";
      }
      $sql = " FROM " . $this->product_m->get_table() . " p LEFT JOIN ";
      $sql .= " (SELECT * FROM " . $this->site_product_m->get_table() . " WHERE site_id={$site_id}) s ON p.`id` = s.`product_id`";

      $query = $this->db->query("SELECT count(*) as all_count " . $sql);
      $temp = $query->row_array();
      $all_count = $temp['all_count'];
      $filter_count = 0;


      if ($all_count > 0) {
        $query = $this->db->query("SELECT count(*) as all_count " . $sql . $where);
        $temp = $query->row_array();
        $filter_count = $temp['all_count'];

        if ($filter_count > 0) {
          $sql = "SELECT p.id, p.`product_description`, p.`product_type`, "
            . "IFNULL(s.`product_code`, p.`product_code`) AS product_code, IFNULL(s.`price`, 0) AS product_price"
            . $sql . $where;
          $sql .= " ORDER BY product_price DESC, p.`product_code` ";
          $sql .= " LIMIT {$start}, {$length}";

          $temp = $this->db->query($sql);
          $start = 0;
          foreach ($temp->result() as $product) {
            $products[] = array(
              "index" => (++$start),
              "product_code" => $product->product_code,
              "product_type" => $product->product_type,
              "product_description" => $product->product_description,
              "price" => $product->product_price,
              "actions" => my_make_table_edit_btn(base_url('products/sites/' . $site_id . '/' . $product->id))
                . my_make_table_delete_btn('javascript:delete_site_product(' . $site_id . ',' . $product->id . ')')
            );
          }
        }
      }
    } else {
      $all_count = 0;
      $filter_count = 0;
//      my_set_system_message(__("Don't site exist!"), "danger");
    }
      $returnData = array(
        "draw" => $draw,
        'recordsTotal' => $all_count,
        'recordsFiltered' => $filter_count,
        'data' => $products
      );

      header('Content-Type: application/json');
      echo json_encode($returnData);
  }

  public function ajax_delete_site_product() {
    $site_id = $this->uri->rsegment(3);
    $product_id = $this->uri->rsegment(4);
    $this->db->where("site_id", $site_id);
    $this->site_product_m->get_by_product_id($product_id);
    if ($this->site_product_m->is_exists()) {
      $this->site_product_m->delete();
    }
  }

}
