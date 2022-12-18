<?php
class Log_
{
	public function __construct()
	{
	}

	public function __get($key)
	{
		return get_instance()->$key;
	}
	public function add_activity($user_id,  $service,$entity, $item_id = 0)
	{
		// die(json_encode($entity));
		$date = date('Y-m-d H:i:s');
		$this->db->insert( "logs",
			[
				'user_id' => intval($user_id),
				'entity' => $entity,
				'service' => $service,
				'item_id' => $item_id,
				'create_date' => $date
			]

		);
	}
}
