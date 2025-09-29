<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreAdjustmentRequest;
use App\Models\Bill;
use App\Services\Owner\AdjustmentService;
use Illuminate\Http\Request;

class AdjustmentController extends Controller
{
  public function __construct(private AdjustmentService $service) {}

  public function create(Request $request)
  {
    $ownerId = auth()->id();
    $billId = (int)$request->get('bill_id',0);

    $bill = $billId
      ? Bill::where('owner_id',$ownerId)->with(['flat:id,flat_number','category:id,name','tenant:id,name'])->findOrFail($billId)
      : null;

    $bills = Bill::where('owner_id',$ownerId)
      ->with(['flat:id,flat_number','category:id,name','tenant:id,name'])
      ->orderByDesc('month')->limit(50)->get();

    return view('owner/adjustments/create', compact('bill','bills'));
  }

  public function store(StoreAdjustmentRequest $request)
  {
    $d = $request->validated();
    $this->service->addDue(
      ownerId: auth()->id(),
      billId: (int)$d['bill_id'],
      amount: (float)$d['amount'],
      reason: $d['reason'] ?? null,
      type: $d['type'] ?? 'manual_due'
    );

    return redirect()->route('owner.bills.index')->with('ok','Adjustment added.');
  }
}