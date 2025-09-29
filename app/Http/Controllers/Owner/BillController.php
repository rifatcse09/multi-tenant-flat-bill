<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StoreBillRequest;
use App\Http\Requests\Owner\UpdateBillRequest;
use App\Services\Owner\BillService;
use App\Models\Bill;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function __construct(private BillService $billService) {}

    public function index(Request $request)
    {
        $ownerId = auth()->id();

        $filters = [
            'flat_id'      => $request->integer('flat_id'),
            'category_id'  => $request->integer('category_id'),
            'status'       => $request->get('status'),
            'bill_to'      => $request->get('bill_to'),
            'month_from'   => $request->get('month_from'),
            'month_to'     => $request->get('month_to'),
            'q'            => trim($request->get('q', '')), // tenant name/email
        ];

        $bills = $this->billService->getBillsWithFilters($ownerId, $filters);
        $totals = $this->billService->calculateBillTotals($ownerId, $filters);
        $dropdownData = $this->billService->getDropdownData($ownerId);

        return view('owner.bills.index', array_merge(
            compact('bills', 'filters', 'totals'),
            $dropdownData
        ));
    }

    public function create()
    {
        $ownerId = auth()->id();
        $dropdownData = $this->billService->getDropdownData($ownerId);

        return view('owner.bills.create', $dropdownData);
    }

    public function store(StoreBillRequest $request)
    {
        try {
            $data = $request->validated();

            $bill = $this->billService->createMonthlyBill(
                ownerId: auth()->id(),
                flatId: (int)$data['flat_id'],
                categoryId: (int)$data['bill_category_id'],
                monthYmd: $data['month'],
                amount: (float)$data['amount'],
                notes: $data['notes'] ?? null
            );

            return redirect()->route('owner.bills.index')->with('ok', 'Bill created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create bill: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Bill $bill)
    {
        // Authorization is handled in UpdateBillRequest
        $dropdownData = $this->billService->getDropdownData(auth()->id());

        return view('owner.bills.edit', array_merge(
            compact('bill'),
            $dropdownData
        ));
    }

    public function update(UpdateBillRequest $request, Bill $bill)
    {
        try {
            $data = $request->validated();

            $bill->update([
                'flat_id'          => $data['flat_id'],
                'bill_category_id' => $data['bill_category_id'],
                'month'            => $data['month'],
                'amount'           => $data['amount'],
                'notes'            => $data['notes'],
            ]);

            return redirect()->route('owner.bills.index')->with('ok', 'Bill updated successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update bill: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Bill $bill)
    {
        // Ensure owner can only delete their own bills
        abort_unless($bill->owner_id === auth()->id(), 403);

        try {
            // Only allow deletion of unpaid bills
            if ($bill->status !== 'unpaid') {
                return back()->with('error', 'Only unpaid bills can be deleted. This bill has status: ' . $bill->status);
            }

            // Check if bill has any payments
            $paymentsCount = $bill->payments()->count();
            if ($paymentsCount > 0) {
                return back()->with('error', 'Cannot delete bill that has payments. Please remove payments first.');
            }

            $bill->delete();
            return back()->with('ok', 'Bill deleted successfully');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete bill: ' . $e->getMessage());
        }
    }

    public function payments(Bill $bill)
    {
        // Ensure owner can only view payments for their own bills
        abort_unless($bill->owner_id === auth()->id(), 403);

        $payments = $bill->payments()
            ->orderBy('created_at', 'desc')
            ->get();

        $totalPaid = $payments->sum('amount');
        $totalDue = $bill->amount + $bill->due_carry_forward;
        $remaining = max(0, $totalDue - $totalPaid);

        return view('owner.bills.payments', compact('bill', 'payments', 'totalPaid', 'totalDue', 'remaining'));
    }
}