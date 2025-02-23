<?php
namespace Opencart\Admin\Controller\Localisation;
class TaxClass extends \Opencart\System\Engine\Controller {

	public function index(): void {
		$this->load->language('localisation/tax_class');

		$this->document->setTitle($this->language->get('heading_title'));

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('localisation/tax_class', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['add'] = $this->url->link('localisation/tax_class|form', 'user_token=' . $this->session->data['user_token'] . $url);

		$data['user_token'] = $this->session->data['user_token'];

		$data['list'] = $this->getList();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/tax_class', $data));
	}

	public function list(): void {
		$this->response->setOutput($this->getList());
	}

	protected function getList(): string {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'title';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['tax_classes'] = [];

		$filter_data = [
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit' => $this->config->get('config_pagination_admin')
		];

		$this->load->model('localisation/tax_class');

		$tax_class_total = $this->model_localisation_tax_class->getTotalTaxClasses();

		$results = $this->model_localisation_tax_class->getTaxClasses($filter_data);

		foreach ($results as $result) {
			$data['tax_classes'][] = [
				'tax_class_id' => $result['tax_class_id'],
				'title'        => $result['title'],
				'edit'         => $this->url->link('localisation/tax_class|edit', 'user_token=' . $this->session->data['user_token'] . '&tax_class_id=' . $result['tax_class_id'] . $url)
			];
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_title'] = $this->url->link('localisation/tax_class', 'user_token=' . $this->session->data['user_token'] . '&sort=title' . $url);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $tax_class_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('localisation/tax_class', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($tax_class_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($tax_class_total - $this->config->get('config_pagination_admin'))) ? $tax_class_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $tax_class_total, ceil($tax_class_total / $this->config->get('config_pagination_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('localisation/tax_class_list', $data);
	}

	protected function getForm(): void {
		$data['text_form'] = !isset($this->request->get['tax_class_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('localisation/tax_class', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		if (!isset($this->request->get['tax_class_id'])) {
			$data['action'] = $this->url->link('localisation/tax_class|add', 'user_token=' . $this->session->data['user_token'] . $url);
		} else {
			$data['action'] = $this->url->link('localisation/tax_class|edit', 'user_token=' . $this->session->data['user_token'] . '&tax_class_id=' . $this->request->get['tax_class_id'] . $url);
		}

		$data['back'] = $this->url->link('localisation/tax_class', 'user_token=' . $this->session->data['user_token'] . $url);

		if (isset($this->request->get['tax_class_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$tax_class_info = $this->model_localisation_tax_class->getTaxClass($this->request->get['tax_class_id']);
		}

		if (!empty($tax_class_info)) {
			$data['title'] = $tax_class_info['title'];
		} else {
			$data['title'] = '';
		}

		if (!empty($tax_class_info)) {
			$data['description'] = $tax_class_info['description'];
		} else {
			$data['description'] = '';
		}

		$this->load->model('localisation/tax_rate');

		$data['tax_rates'] = $this->model_localisation_tax_rate->getTaxRates();

		if (!empty($tax_class_info)) {
			$data['tax_rules'] = $this->model_localisation_tax_class->getTaxRules($this->request->get['tax_class_id']);
		} else {
			$data['tax_rules'] = [];
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('localisation/tax_class_form', $data));
	}

	public function save(): void {
		$this->load->language('localisation/tax_class');

		$json = [];

		if (!$this->user->hasPermission('modify', 'localisation/tax_class')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['title']) < 3) || (utf8_strlen($this->request->post['title']) > 32)) {
			$json['error']['title'] = $this->language->get('error_title');
		}

		if ((utf8_strlen($this->request->post['description']) < 3) || (utf8_strlen($this->request->post['description']) > 255)) {
			$json['error']['description'] = $this->language->get('error_description');
		}

		$this->load->model('localisation/tax_class');

		$this->model_localisation_tax_class->addTaxClass($this->request->post);
		$this->model_localisation_tax_class->editTaxClass($this->request->get['tax_class_id'], $this->request->post);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete(): void {
		$this->load->language('localisation/tax_class');

		$json = [];

		if (isset($this->request->post['selected'])) {
			$selected = $this->request->post['selected'];
		} else {
			$selected = [];
		}

		if (!$this->user->hasPermission('modify', 'localisation/tax_class')) {
			$json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('catalog/product');

		foreach ($selected as $tax_class_id) {
			$product_total = $this->model_catalog_product->getTotalProductsByTaxClassId($tax_class_id);

			if ($product_total) {
				$json['error'] = sprintf($this->language->get('error_product'), $product_total);
			}
		}

		if (!$json) {
			$this->load->model('localisation/tax_class');

			foreach ($selected as $tax_class_id) {
				$this->model_localisation_tax_class->deleteTaxClass($tax_class_id);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
