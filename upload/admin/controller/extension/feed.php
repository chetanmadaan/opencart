<?php
namespace Opencart\Admin\Controller\Extension;
class Feed extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('extension/feed');

		$this->load->model('setting/extension');

		$this->response->setOutput($this->getList());
	}

	public function install(): void {
		$this->load->language('extension/feed');

		$this->load->model('setting/extension');

		if ($this->validate()) {
			$this->model_setting_extension->install('feed', $this->request->get['extension'], $this->request->get['code']);

			$this->load->model('user/user_group');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/' . $this->request->get['extension'] . '/feed/' . $this->request->get['code']);
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/' . $this->request->get['extension'] . '/feed/' . $this->request->get['code']);

			// Call install method if it exists
			$this->load->controller('extension/' . $this->request->get['extension'] . '/feed/' . $this->request->get['code'] . '|install');

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->response->setOutput($this->getList());
	}

	public function uninstall(): void {
		$this->load->language('extension/feed');

		$this->load->model('setting/extension');

		if ($this->validate()) {
			$this->model_setting_extension->uninstall('feed', $this->request->get['code']);

			// Call uninstall method if it exists
			$this->load->controller('extension/' . $this->request->get['extension'] . '/feed/' . $this->request->get['code'] . '|uninstall');

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->response->setOutput($this->getList());
	}

	public function getList(): string {
		$this->load->language('extension/feed');

		$available = [];

		$results = $this->model_setting_extension->getPaths('%/admin/controller/feed/%.php');

		foreach ($results as $result) {
			$available[] = basename($result['path'], '.php');
		}

		$installed = [];

		$extensions = $this->model_setting_extension->getExtensionsByType('feed');

		foreach ($extensions as $extension) {
			if (in_array($extension['code'], $available)) {
				$installed[] = $extension['code'];
			} else {
				$this->model_setting_extension->uninstall('feed', $extension['code']);
			}
		}

		$data['extensions'] = [];

		if ($results) {
			foreach ($results as $result) {
				$extension = substr($result['path'], 0, strpos($result['path'], '/'));

				$code = basename($result['path'], '.php');

				$this->load->language('extension/' . $extension . '/feed/' . $code, $code);

				$data['extensions'][] = [
					'name'      => $this->language->get($code . '_heading_title'),
					'status'    => $this->config->get('feed_' . $code . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
					'install'   => $this->url->link('extension/feed|install', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension . '&code=' . $code),
					'uninstall' => $this->url->link('extension/feed|uninstall', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension . '&code=' . $code),
					'installed' => in_array($code, $installed),
					'edit'      => $this->url->link('extension/' . $extension . '/feed/' . $code, 'user_token=' . $this->session->data['user_token'])
				];
			}
		}

		$data['promotion'] = $this->load->controller('marketplace/promotion');

		return $this->load->view('extension/feed', $data);
	}

	protected function validate(): bool {
		if (!$this->user->hasPermission('modify', 'extension/feed')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}