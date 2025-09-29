<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\BillAdjustment;
use Illuminate\Http\Request;

class AdjustmentController extends Controller
{
    public function create(Request $request)
    {
        $ownerId = auth()->id();

        // Get bills for dropdown
        $bills = Bill::where('owner_id', $ownerId)
            ->with(['flat.building', 'category', 'tenant'])
            ->orderBy('month', 'desc')
            ->get();

        $selectedBillId = $request->get('bill_id');
        $bill = null;

        if ($selectedBillId) {
            $bill = Bill::where('owner_id', $ownerId)
                ->where('id', $selectedBillId)
                ->with(['flat.building', 'category', 'tenant', 'adjustments'])
                ->first();
        }

        return view('owner.adjustments.create', compact('bills', 'bill'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bill_id' => 'required|exists:bills,id',
            'type' => 'required|in:increase,decrease',
            'amount' => 'required|numeric|min:0.01|max:999999.99',
            'reason' => 'required|string|max:255',
        ]);

        try {
            $ownerId = auth()->id();

            // Verify bill belongs to owner
            $bill = Bill::where('owner_id', $ownerId)
                ->findOrFail($request->bill_id);

            // Create adjustment
            BillAdjustment::create([
                'bill_id' => $bill->id,
                'type' => $request->type,
                'amount' => $request->amount,
                'reason' => $request->reason,
                'notes' => $request->notes,
            ]);

            return redirect()->route('owner.bills.index')
                ->with('ok', 'Adjustment added successfully');

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to add adjustment: ' . $e->getMessage())
                ->withInput();
        }
    }
}