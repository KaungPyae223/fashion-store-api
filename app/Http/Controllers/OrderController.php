<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Mail\DeliverMail;
use App\Mail\PostMail;
use App\Models\Deliver;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Repositories\OrderRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $orderRepository;

    function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function index()
    {
        $totalOrderManagers = User::where("role", "Order Management")->count();

        $orderManagers = User::where('role', 'Order Management')->get();

        $currentUser = Auth::user();

        $adminRank = $orderManagers->search(function ($admin) use ($currentUser) {
            return $admin->id === $currentUser->id;
        }) + 1;

        $query = Order::query()
            ->where("status", "prepare")
            ->where("id", ">=", $adminRank) // Ensure id is at least 2
            ->whereRaw("(id - $adminRank) % $totalOrderManagers = 0")
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $orders = $query->map(function ($order) {
            return [

                "id" => $order->id,
                "customer_name" => $order->name,
                "address" => $order->address,
                "totalOrders" => $order->total_qty,
                "totalPrice" => $order->total_price,
                "question_at" => Carbon::parse($order->created_at)->diffForHumans(),

            ];
        });


        return response()->json([
            "data" => $orders,
            'meta' => [
                'current_page' => $query->currentPage(),
                'last_page' => $query->lastPage(),
                'total' => $query->total(),
            ],
        ]);
    }

    public function packagingData($id)
    {

        $order = Order::find($id);

        $orderProducts = $order->orderDetails->map(function ($orderDetail) {
            return [
                "product_id" => $orderDetail->product_id,
                "product_image" => $orderDetail->product->cover_photo,
                "product_size" => $orderDetail->size,
                "product_name" => $orderDetail->product->name,
                "product_price" => $orderDetail->unit_price,
                "product_qty" => $orderDetail->qty,
                "product_total" => $orderDetail->unit_price * $orderDetail->qty,
            ];
        });


        return response()->json([
            "order_data" => $order,
            "order_products" => $orderProducts,

        ], 200);
    }

    public function deliverData()
    {

        $delivery = Deliver::query()->where("status", "available")->get();

        return response()->json([
            "delivery_data" => $delivery,
        ], 200);
    }

    public function orderHistory(Request $request)
    {

        $customer = $request->input('customer');
        $admin = $request->input('admin');
        $orderMonth = $request->input('orderMonth');
        $orderID = $request->input('orderId');


        $query = Order::query();

        if ($customer) {
            $query->whereHas('customer.user', function ($q) use ($customer) {
                $q->where('name', "like", "%" . $customer . "%");
            });
        }

        if ($admin) {
            $query->whereHas('admin.user', function ($q) use ($admin) {
                $q->where('name', "like", "%" . $admin . "%");
            });
        }

        if ($orderID) {
            $query->where("id", $orderID);
        }

        if (preg_match('/^\d{4}-\d{2}$/', $orderMonth)) {
            $query->whereMonth('created_at', '=', date('m', strtotime($orderMonth)))
                ->whereYear('created_at', '=', date('Y', strtotime($orderMonth)));
        }

        $orders = $query->where("status", "delivered")->paginate(10);

        $data = $orders->map(function ($order) {
            return [
                "id" => $order->id,
                "customer_name" => $order->customer->user->name,
                "address" => $order->address,
                "total_products" => $order->orderDetails()->count(),
                "total_price" => $order->total_price,
                "order_at" => $order->created_at,
                "packager" => $order->admin->user->name,
                "delivery" => $order->delivery->name,

            ];
        });

        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function orderAnalysis(Request $request)
    {
        // Default to current year and month
        $getMonth = now()->month;
        $getYear = now()->year;

        // Get input in YYYY-MM format
        $orderMonth = $request->input("month");

        if (preg_match('/^\d{4}-\d{2}$/', $orderMonth)) {
            $date = \Carbon\Carbon::createFromFormat('Y-m', $orderMonth);
            $getMonth = $date->month;
            $getYear = $date->year;
        }

        $orders = Order::query()
            ->whereMonth('created_at', $getMonth)
            ->whereYear('created_at', $getYear)
            ->get();

        $totalOrders = $orders->count();
        $averageOrderValue = $orders->avg('total_price');

        $totalItemsSold = $orders->flatMap(function ($order) {
            return $order->orderDetails;
        })->sum('qty');

        $fulfilledOrders = $orders->where('status', 'delivered')->count();
        $fulfillmentRate = $totalOrders != 0 ? round(($fulfilledOrders / $totalOrders) * 100, 1) : 0;

        $orderPerDay = $orders->groupBy(function ($order) {
            return Carbon::parse($order->created_at)->format('Y-m-d');
        })->map(function ($dailyOrders) {
            return $dailyOrders->sum('total_price');
        });

        $categoryQuantities = collect();

        foreach ($orders as $order) {
            foreach ($order->orderDetails as $detail) {

                $categoryName = $detail->product->category->category ?? 'Unknown';
                $categoryQuantities[$categoryName] =
                    ($categoryQuantities[$categoryName] ?? 0) + $detail->qty;
            }
        }

        // Format for chart (Donut)
        $categoryChartData = [
            ['Category', 'Quantity']
        ];

        foreach ($categoryQuantities as $category => $qty) {
            $categoryChartData[] = [$category, $qty];
        }

        $priceRanges = [
            '>1,000,000' => 0,
            '500,001 ~ 1,000,000' => 0,
            '100,001 ~ 500,000' => 0,
            '<=100,000' => 0,
        ];

        foreach ($orders as $order) {
            $totalPrice = $order->total_price;

            if ($totalPrice > 1000000) {
                $priceRanges['>1,000,000']++;
            } elseif ($totalPrice > 500000) {
                $priceRanges['500,001 ~ 1,000,000']++;
            } elseif ($totalPrice > 100000) {
                $priceRanges['100,001 ~ 500,000']++;
            } else {
                $priceRanges['<=100,000']++;
            }
        }

        $priceRangeChartData = [
            ['Price Range', 'Order Count']
        ];

        foreach ($priceRanges as $range => $count) {
            $priceRangeChartData[] = [$range, $count];
        }

        $typeQuantities = [];

        foreach ($orders as $order) {
            foreach ($order->orderDetails as $detail) {
                $typeName = $detail->product->type->type ?? 'Unknown';
                $typeQuantities[$typeName] = ($typeQuantities[$typeName] ?? 0) + $detail->qty;
            }
        }



        arsort($typeQuantities); // Sort by quantity descending

        $topType = array_slice($typeQuantities, 0, 5, true);
        $otherType = array_slice($typeQuantities, 5, null, true);
        $otherTotal = array_sum($otherType);

        if ($otherTotal > 0) {
            $topType['Other'] = $otherTotal;
        }

        $totalQty = array_sum($topType);

        // Now build the final chart data with percentages
        $typeBreakdown = [];

        foreach ($topType as $type => $qty) {
            $percent = round(($qty / $totalQty) * 100, 1);
            $typeBreakdown[] = [
                'items' => $type,
                'percent' => $percent,
                'totalOrders' => $qty
            ];
        }

        $brandQuantities = [];

        foreach ($orders as $order) {
            foreach ($order->orderDetails as $detail) {
                $brandName = $detail->product->brand->name ?? 'Unknown';
                $brandQuantities[$brandName] = ($brandQuantities[$brandName] ?? 0) + $detail->qty;
            }
        }

        arsort($brandQuantities); // Sort by quantity descending

        $topBrands = array_slice($brandQuantities, 0, 5, true);
        $otherBrands = array_slice($brandQuantities, 5, null, true);
        $otherTotal = array_sum($otherBrands);

        if ($otherTotal > 0) {
            $topBrands['Other'] = $otherTotal;
        }

        $totalQty = array_sum($topBrands);

        // Prepare chart data
        $brandBreakdown = [];

        foreach ($topBrands as $brand => $qty) {
            $percent = round(($qty / $totalQty) * 100, 1);
            $brandBreakdown[] = [
                'items' => $brand,
                'percent' => $percent,
                'totalOrders' => $qty
            ];
        }

        $products = [];

        foreach ($orders as $order) {
            foreach ($order->orderDetails as $detail) {
                $product = $detail->product;
                $productId = $product->id;

                if (!isset($products[$productId])) {
                    $products[$productId] = [
                        'name' => $product->name,
                        'price' => $product->price,
                        'category' => $product->category->category,
                        'brand' => $product->brand->name,
                        'img' => $product->cover_photo,
                        'quantity' => 0,
                        'total' => 0
                    ];
                }


                $products[$productId]['quantity'] += $detail->qty;
                $products[$productId]['total'] += $detail->qty * $product->price;
            }
        }

        usort($products, function ($a, $b) {
            return $b['quantity'] <=> $a['quantity']; // Sort by quantity descending
        });

        $top10Products = array_slice($products, 0, 10);

        $total_amount = $order->sum("total_price");
        $tax = $order->sum("tax");
        $discount = $order->sum("discount_amount");
        $profit = $order->sum("profit_amount");

        $genderCounts = [
            'Men' => 0,
            'Women' => 0,
            'All' => 0,
        ];

        foreach ($orders as $order) {
            foreach ($order->orderDetails as $detail) {
                $gender = $detail->product->gender ?? 'All';

                if (in_array($gender, ['Men', 'Women', 'All'])) {
                    $genderCounts[$gender] += $detail->qty;
                }
            }
        }

        $genderPieChart = [
            ['Gender', 'Quantity Sold'],
            ['Men', $genderCounts['Men']],
            ['Women', $genderCounts['Women']],
            ['All', $genderCounts['All']],
        ];

        $paymentCounts = collect();


        foreach ($orders as $order) {

            $payment = $order->payment->payment;

            $paymentCounts[$payment] = ($paymentCounts[$payment] ?? 0) + 1;
        }

        // For Google Chart or Chart.js, return it as:
        $paymentPieChart = [
            ['Payment', 'Order Quantity'],
            ...$paymentCounts->map(function ($qty, $payment) {
                return [$payment, $qty];
            })->values()->all()
        ];




        return response()->json([
            'totalOrders' => $totalOrders,
            'average_order_value' => $averageOrderValue,
            'total_items_sold' => $totalItemsSold,
            'fulfillmentRate' => $fulfillmentRate,
            'order_bar_graph' => [
                ['Date', 'Total Order Value'],
                ...$orderPerDay->map(function ($total, $date) {
                    return [$date, $total];
                })->values()->all()
            ],
            'categoryChartData' => $categoryChartData,
            'priceRangeChartData' => $priceRangeChartData,
            'productTypeBreakdown' => $typeBreakdown,
            'brandBreakdown' => $brandBreakdown,
            'top10products' => $top10Products,
            "total_amount" => $total_amount,
            'tax' => $tax,
            'discount' => $discount,
            'profit' => $profit,
            'gender_pie_chart' => $genderPieChart,
            'payment_pie_chart' => $paymentPieChart
        ]);
    }

    public function orderList(Request $request)
    {
        $getMonth = now()->month;
        $getYear = now()->year;

        // Get input in YYYY-MM format
        $orderMonth = $request->input("month");

        if (preg_match('/^\d{4}-\d{2}$/', $orderMonth)) {
            $date = \Carbon\Carbon::createFromFormat('Y-m', $orderMonth);
            $getMonth = $date->month;
            $getYear = $date->year;
        }

        $order = Order::query()
            ->whereMonth('created_at', $getMonth)
            ->whereYear('created_at', $getYear)
            ->paginate(10);

        $data = $order->map(function ($order) {
            return [
                "id" => $order->id,
                "payment" => $order->payment->payment,
                "profit" => $order->profit_amount,
                "customer_name" => $order->customer->user->name,
                "total_qty" => $order->total_qty,
                "total_products" => $order->total_products,
                "total_amount" => $order->total_price,
                "discount_amount" => $order->discount_amount,
                "sub_total" => $order->sub_total,
                "tax" => $order->tax
            ];
        });

        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $order->currentPage(),
                'last_page' => $order->lastPage(),
                'total' => $order->total(),
            ]
        ]);
    }

    public function orderCSVExport(Request $request){


        $getMonth = now()->month;
        $getYear = now()->year;

        // Get input in YYYY-MM format
        $orderMonth = $request->input("month");

        if (preg_match('/^\d{4}-\d{2}$/', $orderMonth)) {
            $date = \Carbon\Carbon::createFromFormat('Y-m', $orderMonth);
            $getMonth = $date->month;
            $getYear = $date->year;
        }

        $orders = Order::query()
            ->whereMonth('created_at', $getMonth)
            ->whereYear('created_at', $getYear)
            ->get();


        $filename = "Order Report of " . $getMonth . " " . $getYear;

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Customer Name', 'Email','Note','Payment', 'Total Products', 'Total Quantity', 'Sub Total', 'Tax', 'Discount Amount', 'Total Price' , 'Order At']);

            foreach ($orders as $order) {

                $customer_name = $order->customer->user->name;
                $email = $order->customer->user->email;

                $payment = $order->payment->payment;


                $formattedDate = Carbon::parse($order->created_at)->format('d F Y H:i');


                fputcsv($file, [$order->id, $customer_name, $email, $order->note,$payment,$order->total_products,$order->total_qty,$order->sub_total,$order->tax,$order->discount_amount,$order->total_price,$formattedDate ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);

    }

    public function customerOrder(Request $request)
    {

        $customer = $request->input('customer');
        $orderMonth = $request->input('orderMonth');
        $payment = $request->input('payment');

        $query = Order::query();

        if ($customer) {
            $query->whereHas('customer.user', function ($q) use ($customer) {
                $q->where('name', "like", "%" . $customer . "%");
            });
        }

        if (preg_match('/^\d{4}-\d{2}$/', $orderMonth)) {
            $query->whereMonth('created_at', '=', date('m', strtotime($orderMonth)))
                ->whereYear('created_at', '=', date('Y', strtotime($orderMonth)));
        }

        if ($payment) {
            $query->whereHas('payment', function ($q) use ($payment) {
                $q->where('payment', "like", "%" . $payment . "%");
            });
        }

        $orders = $query->paginate(10);


        $data = $orders->map(function ($order) {
            return [
                "id" => $order->id,
                "customer_name" => $order->customer->user->name,
                "customer_email" => $order->customer->user->email,
                "total_products" => $order->orderDetails()->count(),
                "total_price" => $order->total_price,
                "order_at" => $order->created_at,
                "status" => $order->status,

            ];
        });

        return response()->json([
            "data" => $data,
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function orderHistoryDetails($id)
    {

        $order = Order::find($id);

        return response()->json([
            "order_information" => [
                "id" => $order->id,
                "date" => $order->created_at,
                "packager" => $order->admin ? $order->admin->user->name : "",
                "packager_id" => $order->admin_id,
                "deliver_date" => $order->updated_at,
                "products" => $order->orderDetails()->count(),
                "order_qty" => $order->total_qty,
                "status" => $order->status,
            ],
            "customer_information" => [
                "name" => $order->customer->user->name,
                "email" => $order->customer->user->email,
                "phone" => $order->customer->phone,
                "address" => $order->customer->address,
            ],
            "delivery_information" => [
                "name" => $order->delivery ? $order->delivery->name : null,
                "phone" => $order->delivery ? $order->delivery->phone : null,
                "address" => $order->delivery ? $order->delivery->address : null,
                "email" => $order->delivery ? $order->delivery->email : null,
            ],
            "payment_information" => [
                "method" => $order->payment->payment,
                "total_price" => $order->total_products,
                "tax" => $order->tax,
                "grand_total" => $order->total_price,
            ],
            "receiver_information" => [
                "name" => $order->name,
                "email" => $order->email,
                "phone" => $order->phone,
                "address" => $order->address,
            ],
            "note" => $order->note,
            "order_products" => $order->orderDetails()->get()->map(function ($orderDetail) {
                return [
                    "id" => $orderDetail->id,
                    "cover_image" => $orderDetail->product->cover_photo,
                    "product_name" => $orderDetail->product->name,
                    "product_size" => $orderDetail->size,
                    "product_color" => $orderDetail->product->color->color,
                    "product_price" => $orderDetail->unit_price,
                    "product_qty" => $orderDetail->qty,
                    "total_price" => $orderDetail->unit_price * $orderDetail->qty,
                ];
            }),

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        $order = $this->orderRepository->create($request->validated());

        Mail::to($request->user()->email)->send(new PostMail(env("Frontend_Base_URL") . "/order-details/" . $order->id));

        return response()->json([
            'message' => 'Order successfully',
            'data' => new OrderResource($order),
            "status" => 201
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOrderRequest $request, $id)
    {
        $order = $this->orderRepository->update(array_merge($request->validated(), ["id" => $id]));

        Mail::to($order->customer->user->email)->send(new DeliverMail(env("Frontend_Base_URL") . "/order-details/" . $order->id));

        return new OrderResource($order);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Order $order)
    {
        //
    }
}
