<?php
namespace Opencart\Admin\Controller\Sale;
class Recurring extends \Opencart\System\Engine\Controller {
	public function index(): void {
		$this->load->language('sale/recurring');

		$this->document->setTitle($this->language->get('heading_title'));

		$url = '';

		if (isset($this->request->get['filter_order_recurring_id'])) {
			$url .= '&filter_order_recurring_id=' . $this->request->get['filter_order_recurring_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_reference'])) {
			$url .= '&filter_reference=' . $this->request->get['filter_reference'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

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
			'href' => $this->url->link('sale/recurring', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['add'] = $this->url->link('sale/recurring|form', 'user_token=' . $this->session->data['user_token'] . $url);

		$data['user_token'] = $this->session->data['user_token'];

		$data['list'] = $this->getList();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('sale/recurring', $data));
	}

	protected function getList(): string {
		if (isset($this->request->get['filter_order_recurring_id'])) {
			$filter_order_recurring_id = (int)$this->request->get['filter_order_recurring_id'];
		} else {
			$filter_order_recurring_id = '';
		}

		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = (int)$this->request->get['filter_order_id'];
		} else {
			$filter_order_id = '';
		}

		if (isset($this->request->get['filter_reference'])) {
			$filter_reference = $this->request->get['filter_reference'];
		} else {
			$filter_reference = '';
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = 0;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'or.order_recurring_id';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_order_recurring_id'])) {
			$url .= '&filter_order_recurring_id=' . $this->request->get['filter_order_recurring_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_reference'])) {
			$url .= '&filter_reference=' . $this->request->get['filter_reference'];
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['recurrings'] = [];

		$filter_data = [
			'filter_order_recurring_id' => $filter_order_recurring_id,
			'filter_order_id'           => $filter_order_id,
			'filter_reference'          => $filter_reference,
			'filter_customer'           => $filter_customer,
			'filter_status'             => $filter_status,
			'filter_date_added'         => $filter_date_added,
			'order'                     => $order,
			'sort'                      => $sort,
			'start'                     => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'                     => $this->config->get('config_pagination_admin')
		];

		$recurrings_total = $this->model_sale_recurring->getTotalRecurrings($filter_data);

		$results = $this->model_sale_recurring->getRecurrings($filter_data);

		foreach ($results as $result) {
			if ($result['status']) {
				$status	= $this->language->get('text_status_' . $result['status']);
			} else {
				$status = '';
			}
			
			$data['recurrings'][] = [
				'order_recurring_id' => $result['order_recurring_id'],
				'order_id'           => $result['order_id'],
				'reference'          => $result['reference'],
				'customer'           => $result['customer'],
				'status'             => $status,
				'date_added'         => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'view'               => $this->url->link('sale/recurring|info', 'user_token=' . $this->session->data['user_token'] . '&order_recurring_id=' . $result['order_recurring_id'] . $url),
				'order'              => $this->url->link('sale/order|info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'])
			];
		}

		$data['user_token'] = $this->session->data['user_token'];

		$url = '';

		if (isset($this->request->get['filter_order_recurring_id'])) {
			$url .= '&filter_order_recurring_id=' . $this->request->get['filter_order_recurring_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_reference'])) {
			$url .= '&filter_reference=' . urlencode(html_entity_decode($this->request->get['filter_reference'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_order_recurring'] = $this->url->link('sale/recurring', 'user_token=' . $this->session->data['user_token'] . '&sort=or.order_recurring_id' . $url);
		$data['sort_order'] = $this->url->link('sale/recurring', 'user_token=' . $this->session->data['user_token'] . '&sort=or.order_id' . $url);
		$data['sort_reference'] = $this->url->link('sale/recurring', 'user_token=' . $this->session->data['user_token'] . '&sort=or.reference' . $url);
		$data['sort_customer'] = $this->url->link('sale/recurring', 'user_token=' . $this->session->data['user_token'] . '&sort=customer' . $url);
		$data['sort_status'] = $this->url->link('sale/recurring', 'user_token=' . $this->session->data['user_token'] . '&sort=or.status' . $url);
		$data['sort_date_added'] = $this->url->link('sale/recurring', 'user_token=' . $this->session->data['user_token'] . '&sort=or.date_added' . $url);

		$url = '';

		if (isset($this->request->get['filter_order_recurring_id'])) {
			$url .= '&filter_order_recurring_id=' . $this->request->get['filter_order_recurring_id'];
		}

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}

		if (isset($this->request->get['filter_reference'])) {
			$url .= '&filter_reference=' . urlencode(html_entity_decode($this->request->get['filter_reference'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $recurrings_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('sale/recurring', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($recurrings_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($recurrings_total - $this->config->get('config_pagination_admin'))) ? $recurrings_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $recurrings_total, ceil($recurrings_total / $this->config->get('config_pagination_admin')));

		$data['filter_order_recurring_id'] = $filter_order_recurring_id;
		$data['filter_order_id'] = $filter_order_id;
		$data['filter_reference'] = $filter_reference;
		$data['filter_customer'] = $filter_customer;
		$data['filter_status'] = $filter_status;
		$data['filter_date_added'] = $filter_date_added;
		
		$data['sort'] = $sort;
		$data['order'] = $order;
		
		$data['recurring_statuses'] = [];
		
		$data['recurring_statuses'][0] = [
			'text'  => '',
			'value' => 0
		];
			
		for ($i = 1; $i <= 6; $i++) {
			$data['recurring_statuses'][$i] = [
				'text'  => $this->language->get('text_status_' . $i),
				'value' => 1
			];
		}

		return $this->load->view('sale/recurring_list', $data);
	}

	public function info(): object|null {
		$this->load->model('sale/recurring');
		
		if (isset($this->request->get['order_recurring_id'])) {
			$order_recurring_id = (int)$this->request->get['order_recurring_id'];
		} else {
			$order_recurring_id = 0;
		}
		
		$order_recurring_info = $this->model_sale_recurring->getRecurring($order_recurring_id);

		if ($order_recurring_info) {
			$this->load->language('sale/recurring');
		
			$this->document->setTitle($this->language->get('heading_title'));

			$data['user_token'] = $this->session->data['user_token'];
			
			$url = '';

			if (isset($this->request->get['filter_order_recurring_id'])) {
				$url .= '&filter_order_recurring_id=' . $this->request->get['filter_order_recurring_id'];
			}

			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}

			if (isset($this->request->get['filter_reference'])) {
				$url .= '&filter_reference=' . $this->request->get['filter_reference'];
			}

			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

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
				'href' => $this->url->link('sale/recurring', 'user_token=' . $this->session->data['user_token'] . $url)
			];

			$data['back'] = $this->url->link('sale/recurring', 'user_token=' . $this->session->data['user_token'] . $url);
			
			// Recurring
			$data['order_recurring_id'] = $order_recurring_info['order_recurring_id'];
			$data['reference'] = $order_recurring_info['reference'];
			$data['recurring_name'] = $order_recurring_info['recurring_name'];
			
			if ($order_recurring_info['recurring_id']) {
				$data['recurring'] = $this->url->link('catalog/recurring|edit', 'user_token=' . $this->session->data['user_token'] . '&recurring_id=' . $order_recurring_info['recurring_id']);
			} else {
				$data['recurring'] = '';
			}			
			
			$data['recurring_description'] = $order_recurring_info['recurring_description'];
			
			if ($order_recurring_info['status']) {
				$data['recurring_status']= $this->language->get('text_status_' . $order_recurring_info['status']);
			} else {
				$data['recurring_status'] = '';
			}
			
			$this->load->model('sale/order');

			$order_info = $this->model_sale_order->getOrder($order_recurring_info['order_id']);
			
			$data['payment_method'] = $order_info['payment_method'];
			
			// Order
			$data['order_id'] = $order_info['order_id'];
			$data['order'] = $this->url->link('sale/order|info', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_info['order_id']);
			$data['firstname'] = $order_info['firstname'];
			$data['lastname'] = $order_info['lastname'];
			
			if ($order_info['customer_id']) {
				$data['customer'] = $this->url->link('customer/customer|edit', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $order_info['customer_id']);
			} else {
				$data['customer'] = '';
			}
		
			$data['email'] = $order_info['email'];
			$data['order_status'] = $order_info['order_status'];
			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
			
			// Product
			$data['product'] = $order_recurring_info['product_name'];
			$data['quantity'] = $order_recurring_info['product_quantity'];

			// Transactions
			$data['transactions'] = [];
			
			$transactions = $this->model_sale_recurring->getRecurringTransactions($order_recurring_info['order_recurring_id']);

			foreach ($transactions as $transaction) {
				$data['transactions'][] = [
					'date_added' => $transaction['date_added'],
					'type'       => $transaction['type'],
					'amount'     => $this->currency->format($transaction['amount'], $order_info['currency_code'], $order_info['currency_value'])
				];
			}

			$data['buttons'] = $this->load->controller('extension/payment/' . $order_info['payment_code'] . '/recurring');

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('sale/recurring_info', $data));

			return null;
		} else {
			return new \Opencart\System\Engine\Action('error/not_found');
		}
	}
}
