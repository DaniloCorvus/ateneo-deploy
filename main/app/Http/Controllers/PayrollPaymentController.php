<?php

namespace App\Http\Controllers;

use App\Models\PayrollPayment;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

class PayrollPaymentController extends Controller
{
	//
	use ApiResponser;
	/**
	 * Retorna JSON todos los pagos de nómina hechos hasta la fecha
	 *
	 * @return Illuminate\Http\Response
	 */
	public function getPagosNomina(Request $req)
	{

		return $this->success(PayrollPayment::with(['company' => function ($q) {
			$q->select('id', 'name', 'tin');
		}])
			->when($req->get('company_id'), function ($q, $fill) {
				$q->where('company_id', $fill);
			})
			->when($req->get('start_period'), function ($q, $fill) {
				$q->whereDate('start_period', '>=', $fill);
			})
			->when($req->get('end_period'), function ($q, $fill) {
				$q->whereDate('end_period', '<=', $fill);
			})
			->get());
	}
}
