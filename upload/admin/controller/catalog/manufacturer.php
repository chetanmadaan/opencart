<?php
namespace Opencart\Admin\Controller\Catalog;
class Manufacturer extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('catalog/manufacturer');

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
			'href' => $this->url->link('catalog/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['add'] = $this->url->link('catalog/manufacturer|add', 'user_token=' . $this->session->data['user_token'] . $url);

		$data['user_token'] = $this->session->data['user_token'];

		$data['list'] = $this->getList();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/manufacturer', $data));
	}

	public function list(): void {
		$this->response->setOutput($this->getList());
	}

	protected function getList(): string {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
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

		$data['manufacturers'] = [];

		$filter_data = [
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit' => $this->config->get('config_pagination_admin')
		];

		$this->load->model('catalog/manufacturer');

		$manufacturer_total = $this->model_catalog_manufacturer->getTotalManufacturers();

		$results = $this->model_catalog_manufacturer->getManufacturers($filter_data);

		foreach ($results as $result) {
			$data['manufacturers'][] = [
				'manufacturer_id' => $result['manufacturer_id'],
				'name'            => $result['name'],
				'sort_order'      => $result['sort_order'],
				'edit'            => $this->url->link('catalog/manufacturer|edit', 'user_token=' . $this->session->data['user_token'] . '&manufacturer_id=' . $result['manufacturer_id'] . $url)
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

		$data['sort_name'] = $this->url->link('catalog/manufacturer', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url);
		$data['sort_sort_order'] = $this->url->link('catalog/manufacturer', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $manufacturer_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('catalog/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($manufacturer_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($manufacturer_total - $this->config->get('config_pagination_admin'))) ? $manufacturer_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $manufacturer_total, ceil($manufacturer_total / $this->config->get('config_pagination_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('catalog/manufacturer_list', $data);
	}

	protected function getForm(): void {
		$this->load->language('catalog/manufacturer');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['text_form'] = !isset($this->request->get['manufacturer_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

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
			'href' => $this->url->link('catalog/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		if (!isset($this->request->get['manufacturer_id'])) {
			$data['action'] = $this->url->link('catalog/manufacturer|add', 'user_token=' . $this->session->data['user_token'] . $url);
		} else {
			$data['action'] = $this->url->link('catalog/manufacturer|edit', 'user_token=' . $this->session->data['user_token'] . '&manufacturer_id=' . $this->request->get['manufacturer_id'] . $url);
		}

		$data['back'] = $this->url->link('catalog/manufacturer', 'user_token=' . $this->session->data['user_token'] . $url);

		if (isset($this->request->get['manufacturer_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (!empty($manufacturer_info)) {
			$data['name'] = $manufacturer_info['name'];
		} else {
			$data['name'] = '';
		}

		$this->load->model('setting/store');

		$data['stores'] = [];

		$data['stores'][] = [
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		];

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = [
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			];
		}

		if (!empty($manufacturer_info)) {
			$data['manufacturer_store'] = $this->model_catalog_manufacturer->getStores($this->request->get['manufacturer_id']);
		} else {
			$data['manufacturer_store'] = [0];
		}

		if (!empty($manufacturer_info)) {
			$data['image'] = $manufacturer_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		if (is_file(DIR_IMAGE . html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'))) {
			$data['thumb'] = $this->model_tool_image->resize(html_entity_decode($data['image'], ENT_QUOTES, 'UTF-8'), 100, 100);
		} else {
			$data['thumb'] = $data['placeholder'];
		}

		if (!empty($manufacturer_info)) {
			$data['sort_order'] = $manufacturer_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (!empty($manufacturer_info)) {
			$data['manufacturer_seo_url'] = $this->model_catalog_manufacturer->getSeoUrls($this->request->get['manufacturer_id']);
		} else {
			$data['manufacturer_seo_url'] = [];
		}

		if (!empty($manufacturer_info)) {
			$data['manufacturer_layout'] = $this->model_catalog_manufacturer->getLayouts($this->request->get['manufacturer_id']);
		} else {
			$data['manufacturer_layout'] = [];
		}

		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/manufacturer_form', $data));
	}

	public function save(): void {
		$this->load->language('catalog/manufacturer');

		$json = [];

		if (!$this->user->hasPermission('modify', 'catalog/manufacturer')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen(trim($this->request->post['name'])) < 1) || (utf8_strlen($this->request->post['name']) > 64)) {
			$json['error']['name'] = $this->language->get('error_name');
		}

		if ($this->request->post['manufacturer_seo_url']) {
			$this->load->model('design/seo_url');

			foreach ($this->request->post['manufacturer_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if ($keyword) {
						$seo_url_info = $this->model_design_seo_url->getSeoUrlByKeyword($keyword, $store_id, $language_id);

						if ($seo_url_info && ($seo_url_info['key'] != 'manufacturer_id' || !isset($this->request->get['manufacturer_id']) || $seo_url_info['value'] != (int)$this->request->get['manufacturer_id'])) {
							$json['error']['keyword'][$store_id][$language_id] = $this->language->get('error_keyword');
						}
					} else {
						$json['error']['keyword'][$store_id][$language_id] = $this->language->get('error_seo');
					}
				}
			}
		}

		if ($json['error'] && !isset($json['error']['warning'])) {
			$json['error']['warning'] = $this->language->get('error_warning');
		}

		$this->load->model('catalog/manufacturer');

		$this->model_catalog_manufacturer->addManufacturer($this->request->post);
		$this->model_catalog_manufacturer->editManufacturer($this->request->get['manufacturer_id'], $this->request->post);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function delete(): void {
		$this->load->language('catalog/manufacturer');

		$json = [];

		if (isset($this->request->post['selected'])) {
			$selected = $this->request->post['selected'];
		} else {
			$selected = [];
		}

		if (!$this->user->hasPermission('modify', 'catalog/manufacturer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('catalog/product');

		foreach ($selected as $manufacturer_id) {
			$product_total = $this->model_catalog_product->getTotalProductsByManufacturerId($manufacturer_id);

			if ($product_total) {
				$json['error'] = sprintf($this->language->get('error_product'), $product_total);
			}
		}

		if (!$json) {
			$this->load->model('catalog/manufacturer');

			foreach ($selected as $manufacturer_id) {
				$this->model_catalog_manufacturer->deleteManufacturer($manufacturer_id);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function autocomplete(): void {
		$json = [];

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/manufacturer');

			$filter_data = [
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			];

			$results = $this->model_catalog_manufacturer->getManufacturers($filter_data);

			foreach ($results as $result) {
				$json[] = [
					'manufacturer_id' => $result['manufacturer_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				];
			}
		}

		$sort_order = [];

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
