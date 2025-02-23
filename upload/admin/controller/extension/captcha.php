<?php
namespace Opencart\Admin\Controller\Extension;
class Captcha extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('extension/captcha');

		$this->load->model('setting/extension');

		$this->response->setOutput($this->getList());
	}

	public function install(): void {
		$this->load->language('extension/captcha');

		$this->load->model('setting/extension');

		if ($this->validate()) {
			$this->model_setting_extension->install('captcha', $this->request->get['extension'], $this->request->get['code']);

			$this->load->model('user/user_group');

			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/' . $this->request->get['extension'] . '/captcha/' . $this->request->get['code']);
			$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/' . $this->request->get['extension'] . '/captcha/' . $this->request->get['code']);

			// Call install method if it exists
			$this->load->controller('extension/' . $this->request->get['extension'] . '/captcha/' . $this->request->get['code'] . '|install');

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->response->setOutput($this->getList());
	}

	public function uninstall(): void {
		$this->load->language('extension/captcha');

		$this->load->model('setting/extension');

		if ($this->validate()) {
			$this->model_setting_extension->uninstall('captcha', $this->request->get['code']);

			// Call uninstall method if it exists
			$this->load->controller('extension/' . $this->request->get['extension'] . '/captcha/' . $this->request->get['code'] . '|uninstall');

			$this->session->data['success'] = $this->language->get('text_success');
		}

		$this->response->setOutput($this->getList());
	}

	public function getList(): string {
		// Had top load again because the method is called directly.
		$this->load->language('extension/captcha');

		$available = [];

		$results = $this->model_setting_extension->getPaths('%/admin/controller/captcha/%.php');

		foreach ($results as $result) {
			$available[] = basename($result['path'], '.php');
		}

		$installed = [];

		$extensions = $this->model_setting_extension->getExtensionsByType('captcha');

		foreach ($extensions as $extension) {
			if (in_array($extension['code'], $available)) {
				$installed[] = $extension['code'];
			} else {
				$this->model_setting_extension->uninstall('captcha', $extension['code']);
			}
		}

		$data['extensions'] = [];

		if ($results) {
			foreach ($results as $result) {
				$extension = substr($result['path'], 0, strpos($result['path'], '/'));

				$code = basename($result['path'], '.php');

				$this->load->language('extension/' . $extension . '/captcha/' . $code, $code);

				$data['extensions'][] = [
					'name'      => $this->language->get($code . '_heading_title') . ($code == $this->config->get('config_captcha') ? $this->language->get('text_default') : ''),
					'status'    => $this->config->get('captcha_' . $code . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
					'install'   => $this->url->link('extension/captcha|install', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension . '&code=' . $code),
					'uninstall' => $this->url->link('extension/captcha|uninstall', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension . '&code=' . $code),
					'installed' => in_array($code, $installed),
					'edit'      => $this->url->link('extension/' . $extension . '/captcha/' . $code, 'user_token=' . $this->session->data['user_token'])
				];
			}
		}

		$data['promotion'] = $this->load->controller('marketplace/promotion');

		return $this->load->view('extension/captcha', $data);
	}

	protected function validate(): bool {
		if (!$this->user->hasPermission('modify', 'extension/captcha')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
