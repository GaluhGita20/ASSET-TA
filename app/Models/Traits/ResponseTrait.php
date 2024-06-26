<?php

namespace App\Models\Traits;

trait ResponseTrait
{
	public function responseSuccess($params = [])
	{
		$default = [
			'code'    => 200,
			'status'  => true,
			'message' => 'Success',
		];

		if (is_string($params)) {
			$default['message'] = $params;
			$params = [];
		}

		$data = array_merge($default, $params);
		return response()->json($data);
	}

	public function responseError($params = [])
	{
		$default = [
			'code'    => 500,
			'status'  => false,
			'message' => 'Error',
		];

		if (is_string($params)) {
			$default['message'] = $params;
			$params = [];
		}

		$data = array_merge($default, $params);
		// dd($data);
		if (!empty($data['errors']) && is_string($data['errors'])) {
			if (strpos($data['errors'], 'MESSAGE--', 0) !== false) {
				$data['message'] = trim(str_replace('MESSAGE--', '', $data['errors']));
				$data['errors'] = [];
				
			}
		}
		return response()->json($data, $data['code']);
	}

// 	public function responseError($params = [])
// {
//     $default = [
//         'code'    => 500,
//         'status'  => false,
//         'message' => 'Error',
//         'errors'  => [] // Menambahkan default untuk 'errors'
//     ];

//     if (is_string($params)) {
//         $default['message'] = $params;
//         $params = [];
//     }

//     $data = array_merge($default, $params);

//     // Mengatasi 'errors' jika berisi string dengan format 'MESSAGE--'
//     if (!empty($data['errors']) && is_string($data['errors'])) {
//         if (strpos($data['errors'], 'MESSAGE--') === 0) {
//             $data['message'] = trim(str_replace('MESSAGE--', '', $data['errors']));
//             $data['errors'] = [];
//         }
//     }

//     return response()->json($data, $data['code']);
// }


	// Select2
	public function responseSelect2($items, $text, $id = 'id')
	{
		$results = [];
		$more = false;
		foreach ($items as $item) {
			$results[] = ['id' => $item->$id, 'text' => $item->$text];
		}
		if (method_exists($items, 'hasMorePages')) {
			$more = $items->hasMorePages();
		}
		return response()->json(compact('results', 'more'));
	}

	public function responseSelect2ProcedureAudit($items, $text, $id = 'id')
	{
		$results = [];
		$more = false;
		foreach ($items as $item) {
			$results[] = ['id' => $item->$id, 'text' => $item->procedureAudit->procedure];
		}
		if (method_exists($items, 'hasMorePages')) {
			$more = $items->hasMorePages();
		}
		return response()->json(compact('results', 'more'));
	}

	// Transaction
	public function beginTransaction()
	{
		\DB::beginTransaction();
	}

	public function commit($params = [])
	{
		\DB::commit();
		return $this->responseSuccess($params);
	}

	// public function rollback($params = [])
	// {
	// 	\DB::rollback();
	// 	return $this->responseError($params);
		
	// }

	public function rollback($params = [])
	{
		\DB::rollback();

		// Check if $params is an array and contains a 'message' key
		if (is_array($params) && isset($params['message'])) {
			$errorMessage = $params['message'];
		} else {
			// If $params is a string, or if 'message' key is not set, use it as is
			$errorMessage = $params;
		}

		return $this->responseError($errorMessage);
	}


	// Saved
	public function commitSaved($params = [])
	{
		\DB::commit();
		$message = __('base.success.saved');
		return $this->responseSuccess(array_merge(compact('message'), $params));
	}

	// public function rollbackSaved($e, $params = [])
	// {
	// 	\DB::rollback();
	// 	$message = __('base.error.saved');
	// 	$errors = $e->getMessage();
	// 	$traces = $e->getTrace();
	// 	return $this->responseError(array_merge(compact('message', 'errors', 'traces'), $params));
	// }

	public function rollbackSaved($e, $params = [])
	{
		\DB::rollback();
		
		// Inisialisasi pesan dan error dengan nilai default
		$message = __('base.error.saved');
		$errors = $e; // Set nilai errors dengan nilai $e

		// Periksa apakah $e adalah objek pengecualian
		if ($e instanceof Exception) {
			// Jika $e adalah objek pengecualian, ambil pesan kesalahan dari objek tersebut
			$errors = $e->getMessage();
		}

		// Dapatkan trace jika $e adalah objek pengecualian
		$traces = ($e instanceof Exception) ? $e->getTrace() : [];

		// Gabungkan data untuk dikirim ke metode responseError
		$data = array_merge(compact('message', 'errors', 'traces'), $params);

		return $this->responseError($data);
	}


	// Deleted
	public function commitDeleted($params = [])
	{
		\DB::commit();
		$message = __('base.success.deleted');
		return $this->responseSuccess(array_merge(compact('message'), $params));
	}

	public function rollbackDeleted($e, $params = [])
	{
		\DB::rollback();
		$message = __('base.error.deleted');
		$errors = $e->getMessage();
		if ($e->getCode() == 23000) {
			$message = __('base.error.related');
			$errors = [];
		}
		$traces = $e->getTrace();
		return $this->responseError(array_merge(compact('message', 'errors', 'traces'), $params));
	}
}
