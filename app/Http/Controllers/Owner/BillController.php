<?php

namespace App\Http\Controllers\Owner;

use App\Models\Bill;
use App\Models\Flat;
use App\Models\BillCategory;
use Illuminate\Http\Request;
use App\Services\BillService;
use App\Http\Controllers\Controller;

class BillController extends Controller
{
    public function __construct(private BillService $service) {}

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
            'q'            => trim($request->get('q','')), // tenant name/email
        ];

        $query = Bill::with(['flat:id,flat_number','category:id,name','tenant:id,name,email'])
            ->where('owner_id', $ownerId)
            ->when($filters['flat_id'],     fn($q,$v) => $q->where('flat_id', $v))
            ->when($filters['category_id'], fn($q,$v) => $q->where('bill_category_id', $v))
            ->when($filters['status'],      fn($q,$v) => $q->where('status', $v))
            ->when($filters['bill_to'],     fn($q,$v) => $q->where('bill_to', $v))
            ->when($filters['month_from'],  fn($q,$v) => $q->whereDate('month', '>=', date('Y-m-01', strtotime($v.'-01'))))
            ->when($filters['month_to'],    fn($q,$v) => $q->whereDate('month', '<=', date('Y-m-t', strtotime($v.'-01'))))
            ->when($filters['q'], function ($q, $v) {
                $q->whereHas('tenant', fn($t) => $t->where('name','like',"%$v%")->orWhere('email','like',"%$v%"));
            })
            ->orderByDesc('month')->orderBy('flat_id');

        // totals for current filter (without pagination)
        $totals = (clone $query)->get()->reduce(function($carry, $bill) {
            $paid = $bill->payments()->sum('amount');
            $due  = max(0, ($bill->amount + $bill->due_carry_forward) - $paid);
            $carry['amount'] += $bill->amount;
            $carry['carry']  += $bill->due_carry_forward;
            $carry['paid']   += $paid;
            $carry['due']    += $due;
            return $carry;
        }, ['amount'=>0,'carry'=>0,'paid'=>0,'due'=>0]);

        $bills = $query->paginate(15)->withQueryString();

        // dropdown data
        $flats = Flat::where('owner_id',$ownerId)->orderBy('flat_number')->get(['id','flat_number']);
        $categories = BillCategory::where('owner_id',$ownerId)->orderBy('name')->get(['id','name']);

        return view('owner.bills.index', compact('bills','flats','categories','filters','totals'));
    }

    public function create()
    {
        $ownerId = auth()->id();
        $flats = Flat::where('owner_id',$ownerId)->orderBy('flat_number')->get(['id','flat_number']);
        $categories = BillCategory::where('owner_id',$ownerId)->orderBy('name')->get(['id','name']);
        return view('owner.bills.create', compact('flats','categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'flat_id'          => ['required','exists:flats,id'],
            'bill_category_id' => ['required','exists:bill_categories,id'],
            'month'            => ['required','date'], // use first day of month in UI
            'amount'           => ['required','numeric','min:0'],
            'notes'            => ['nullable','string','max:1000'],
        ]);

        $bill = $this->service->createMonthlyBill(
            ownerId: auth()->id(),
            flatId: (int)$data['flat_id'],
            categoryId: (int)$data['bill_category_id'],
            monthYmd: $data['month'],
            amount: (float)$data['amount'],
            notes: $data['notes'] ?? null
        );

        // (Optional) dispatch email notification here
        // Notification::send($bill->owner, new BillCreated($bill));

        return redirect()->route('owner.bills.index')->with('ok','Bill created');
    }
}