<?php


// app/Http/Controllers/Owner/PaymentController.php
namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Owner\StorePaymentRequest;
use App\Models\Bill;
use App\Models\Payment;
use App\Services\Owner\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $service) {}

    public function create(Request $request)
    {
        $ownerId = auth()->id();

        // preselect bill if provided
        $billId = (int) $request->get('bill_id', 0);
        $bill = $billId ? Bill::where('owner_id',$ownerId)->with(['flat','category','tenant'])->findOrFail($billId) : null;

        // Show a dropdown of owner bills if no bill_id
        $bills = Bill::where('owner_id',$ownerId)
            ->with(['flat:id,flat_number','category:id,name','tenant:id,name'])
            ->orderByDesc('month')
            ->limit(50)
            ->get();

        return view('owner.payments.create', compact('bill','bills'));
    }

    public function store(StorePaymentRequest $request)
    {
        $data = $request->validated();

        $this->service->addPayment(
            ownerId: auth()->id(),
            billId:  (int) $data['bill_id'],
            amount:  (float) $data['amount'],
            paidAt:  $data['paid_at'],
            method:  $data['method'] ?? null,
            ref: $data['reference'] ?? null,
            allowOverpay: false
        );

        return redirect()->route('owner.bills.index')->with('ok','Payment recorded and bill updated.');
    }

    // Optional index (list payments)
    public function index(Request $request)
    {
        $ownerId = auth()->id();
        $q = Payment::query()
            ->whereHas('bill', fn($b)=>$b->where('owner_id',$ownerId))
            ->with(['bill.flat:id,flat_number','bill.category:id,name','bill.tenant:id,name'])
            ->orderByDesc('paid_at');

        if ($billId = $request->integer('bill_id')) $q->where('bill_id',$billId);

        $payments = $q->paginate(20)->withQueryString();
        return view('owner.payments.index', compact('payments'));
    }

    // Optional delete (admin/owner choice)
    public function destroy(Payment $payment)
    {
        // authorize owner
        abort_unless($payment->bill->owner_id === auth()->id(), 403);
        $payment->delete();

        // Recompute bill status
        $bill = $payment->bill;
        $paid = (float) $bill->payments()->sum('amount');
        $gross = (float) $bill->amount + (float) $bill->due_carry_forward;
        $bill->status = $paid >= $gross ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid');
        $bill->save();

        return back()->with('ok','Payment deleted and bill recalculated.');
    }
}