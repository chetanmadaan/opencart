<?php
namespace Opencart\Admin\Controller\Marketplace;
class Startup extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('marketplace/startup');

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
			'href' => $this->url->link('marketplace/startup', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['add'] = $this->url->link('marketplace/startup|form', 'user_token=' . $this->session->data['user_token'] . $url);

		$data['user_token'] = $this->session->data['user_token'];

		$data['list'] = $this->getList();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('marketplace/startup', $data));
	}

	public function getList(): string {
		$this->load->model('setting/startup');

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'code';
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

		$data['startups'] = [];

		$filter_data = [
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit' => $this->config->get('config_pagination_admin')
		];

		$startup_total = $this->model_setting_startup->getTotalStartups();

		$results = $this->model_setting_startup->getStartups($filter_data);

		foreach ($results as $result) {
			$data['startups'][] = [
				'startup_id' => $result['startup_id'],
				'code'       => $result['code'],
				'action'     => $result['action'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'enabled'    => $result['status'],
				'sort_order' => $result['sort_order']
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

		$data['sort_code'] = $this->url->link('marketplace/startup', 'user_token=' . $this->session->data['user_token'] . '&sort=code' . $url);
		$data['sort_action'] = $this->url->link('marketplace/startup', 'user_token=' . $this->session->data['user_token'] . '&sort=action' . $url);
		$data['sort_status'] = $this->url->link('marketplace/startup', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url);
		$data['sort_sort_order'] = $this->url->link('marketplace/startup', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $startup_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('marketplace/startup', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($startup_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($startup_total - $this->config->get('config_pagination_admin'))) ? $startup_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $startup_total, ceil($startup_total / $this->config->get('config_pagination_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('marketplace/startup', $data);
	}

	public function delete(): void {
		$this->load->language('marketplace/startup');

		$json = [];

		if (isset($this->request->post['selected'])) {
			$selected = $this->request->post['selected'];
		} else {
			$selected = [];
		}

		if (!$this->user->hasPermission('modify', 'marketplace/startup')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/startup');

			foreach ($selected as $startup_id) {
				$this->model_setting_startup->deleteStartup($startup_id);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function enable(): void {
		$this->load->language('marketplace/startup');

		$json = [];

		if (isset($this->request->get['startup_id'])) {
			$startup_id = (int)$this->request->get['startup_id'];
		} else {
			$startup_id = 0;
		}

		if (!$this->user->hasPermission('modify', 'marketplace/startup')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/startup');

			$this->model_setting_startup->editStatus($startup_id, 1);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function disable(): void {
		$this->load->language('marketplace/startup');

		$json = [];

		if (isset($this->request->get['startup_id'])) {
			$startup_id = (int)$this->request->get['startup_id'];
		} else {
			$startup_id = 0;
		}

		if (!$this->user->hasPermission('modify', 'marketplace/startup')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('setting/startup');

			$this->model_setting_startup->editStatus($startup_id, 0);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}