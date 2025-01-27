<?php

class Vibe_Positiva_Events_List_Table extends WP_List_Table
{

	public function __construct()
	{
		parent::__construct(array(
			'singular' => 'evento',
			'plural'   => 'eventos',
			'ajax'     => false,
		));
	}

	/**
	 * Define as colunas da tabela.
	 * 
	 * @return array
	 */
	public function get_columns()
	{
		return [
			'cb'          => '<input type="checkbox" />',
			'title'       => 'Título',
			'description' => 'Descrição',
			'enabled'     => 'Ativo',
			'event_date'  => 'Data do Evento',
			'price'       => 'Preço',
			'image'       => 'Imagem',
			'page_path'   => 'URI da Página',
		];
	}

	/**
	 * Configura as colunas ordenáveis.
	 * 
	 * @return array
	 */
	protected function get_sortable_columns()
	{
		return [
			'title'      => ['title', true],
			'event_date' => ['event_date', false],
			'price'      => ['price', false],
			'enabled'      => ['enabled', false],
			'image'      => ['image', false],
		];
	}

	/**
	 * Aplica formatação nos dados da coluna.
	 * 
	 * @return string
	 */
	protected function column_default($item, $column_name)
	{
		switch ($column_name) {
			case 'id':
			case 'title':
			case 'description':
			case 'page_path':
				return esc_html($item[$column_name]);
			case 'enabled':
				return $item[$column_name] ? 'Sim' : 'Não';
			case 'event_date':
				return date('d/m/Y', strtotime($item[$column_name]));
			case 'price':
				return 'R$ ' . number_format($item[$column_name], 2, ',', '.');
			case 'image':
				return '<img src="' . esc_url($item[$column_name]) . '" alt="" style="width: 50px; height: 50px;">';
			default:
				return print_r($item, true); // Debug
		}
	}

	/**
	 * Esse método é usado para adicionar uma coluna de checkboxes à tabela
	 * 
	 * @return string
	 */
	protected function column_cb($item)
	{
		return sprintf('<input type="checkbox" name="eventos[]" value="%s" />', $item['id']);
	}

	/**
	 * Define o comportamento da coluna 'title'.
	 * 
	 * @return string
	 */
	protected function column_title($item)
	{
		$actions['edit'] = sprintf(
			'<span class="open-modal-edit" 
            data-id="' . esc_attr($item['id']) . '" 
            data-title="' . esc_attr($item['title']) . '" 
            data-description="' . esc_attr($item['description']) . '" 
            data-enabled="' . esc_attr($item['enabled']) . '"
            data-event_date="' . esc_attr($item['event_date']) . '"  
            data-price="' . esc_attr($item['price']) . '" 
            data-image="' . esc_url($item['image']) . '" 
            data-page_path="' . esc_attr($item['page_path']) . '"
        >Editar</span>'
		);

		$actions['delete'] = sprintf(
			'<span class="delete-event" 
			data-id="' . esc_attr($item['id']) . '" 
			data-title="' . esc_attr($item['title']) . '"
			>Excluir</span>'
		);

		// Return the title contents.
		return sprintf(
			'%1$s <span style="color:silver;">(id:%2$s)</span>%3$s',
			$item['title'],
			$item['id'],
			$this->row_actions($actions)
		);
	}

	/**
	 * Define a descrição da ação em lote 'delete'.
	 */
	protected function get_bulk_actions()
	{
		return [
			'delete' => 'Excluir',
		];
	}

	/**
	 * Comportamento da ação em lote 'delete'.
	 */
	protected function process_bulk_action()
	{
		// // Verifica se a ação em lote é 'delete'
		if ('delete' != $this->current_action()) {
			return;
		}

		// Obtém os IDs selecionados
		$ids = isset($_POST['eventos']) ? $_POST['eventos'] : [];

		if (empty($ids)) {
			return;
		}

		global $wpdb;

		// Nome da tabela
		$nome_tabela = $wpdb->prefix . 'events';

		// Construa a lista de placeholders para a cláusula IN
		$placeholders = implode(',', array_fill(0, count($ids), '%d'));

		//Execute a query
		$wpdb->query(
			$wpdb->prepare(
				"DELETE FROM $nome_tabela WHERE id IN ($placeholders)",
				$ids
			)
		);
	}

	/**
	 * Prepara os itens da tabela.
	 */
	public function prepare_items()
	{
		// Obtém um valor de pesquisa.
		$search = isset($_REQUEST['s']) ? wp_unslash(trim($_REQUEST['s'])) : '';

		global $wpdb; //This is used only if making any database queries
		$nome_tabela = $wpdb->prefix . 'events';

		// Ordenação
		$orderby = isset($_GET['orderby']) ? $_GET['orderby'] : 'id';
		$order   = isset($_GET['order']) ? $_GET['order'] : 'asc';

		// Paginação
		$per_page     = 2;
		$current_page = $this->get_pagenum();
		$offset       = ($current_page - 1) * $per_page;

		// Prepara a query para consulta
		$query = "SELECT * FROM $nome_tabela";

		// Adiciona a cláusula de busca, se aplicável
		if (!empty($search)) {
			$query .= $wpdb->prepare(" WHERE title LIKE %s OR description LIKE %s", "%$search%", "%$search%");
		}
				
		// Adiciona a ordenação e paginação
		$query .= " ORDER BY $orderby $order LIMIT %d OFFSET %d";
		$query = $wpdb->prepare($query, $per_page, $offset);

		$items = $wpdb->get_results($query, ARRAY_A);

		// Contagem total
		$total_items = $wpdb->get_var("SELECT COUNT(*) FROM $nome_tabela");

		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array($columns, $hidden, $sortable);

		$this->process_bulk_action();

		$data = $items;

		$current_page = $this->get_pagenum();

		$this->items = $data;

		$this->set_pagination_args(array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil($total_items / $per_page),
		));
	}

	/**
	 * Define as colunas ordenáveis.
	 * 
	 * @return string
	 */
	protected function usort_reorder($a, $b)
	{
		$orderby = ! empty($_REQUEST['orderby']) ? wp_unslash($_REQUEST['orderby']) : 'title';

		$order = ! empty($_REQUEST['order']) ? wp_unslash($_REQUEST['order']) : 'asc';

		$result = strcmp($a[$orderby], $b[$orderby]);
	
		return ('asc' === $order) ? $result : - $result;
	}
}
