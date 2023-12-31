<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Order;
use Stripe\Charge;
use Stripe\Stripe;
use Illuminate\Support\Facades\Session;
use RealRashid\SweetAlert\Facades\Alert;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::paginate(6);
        $category = Category::all();

        return view('home.userpage', compact('products', 'category'));
    }

    public function redirect()
    {
        $usertype  = Auth::user()->usertype;
        if ($usertype == '1') {
            $total_products = Product::all()->count();
            $total_orders = Order::all()->count();
            $total_users = User::all()->count();
            $order = Order::all();
            $total_revenue = 0;

            foreach ($order as $order) {
                $total_revenue += $order->total_price;
            }

            $orders_delivered = Order::where('delivery_status', '=', 'delivered')->get()->count();
            $orders_processing = Order::where('delivery_status', '=', 'processing')->get()->count();

            return view('admin.home', compact('total_products', 'total_orders', 'total_users', 'total_revenue', 'orders_delivered', 'orders_processing'));
        } else {
            $products = Product::limit(6)->get();
            $category = Category::all();
            return view('home.userpage', compact('products', 'category'));
        }
    }

    public function product_details($id)
    {
        $product = Product::find($id);
        $category = Category::all();

        return view('home.product_details', compact('product', 'category'));
    }

    public function add_cart(Request $request, $id)
    {
        if (Auth::id()) {
            $user = Auth::user();
            $product = Product::find($id);
            $cart = Cart::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->first();

            if ($cart) {
                $cart->quantity += $request->quantity;
                if ($product->discount_price != null) {
                    $cart->total_price = $product->discount_price * $cart->quantity;
                } else {
                    $cart->total_price = $product->price * $cart->quantity;
                }
                $cart->save();
            } else {
                $cart = new Cart;
                $cart->name = $user->name;
                $cart->email = $user->email;
                $cart->phone = $user->phone;
                $cart->address = $user->address;
                $cart->user_id = $user->id;
                $cart->product_title = $product->title;

                $cart->image = $product->image;
                $cart->product_id = $product->id;
                $cart->quantity = $request->quantity;
                if ($product->discount_price != null) {
                    $cart->price_per_one = $product->discount_price;
                    $cart->total_price = $product->discount_price * $request->quantity;
                } else {
                    $cart->total_price = $product->price * $request->quantity;
                    $cart->price_per_one = $product->price;
                }
                $cart->save();
            }
            Alert::success('Product added successfully', "Success Message");

            return redirect()->back();
        } else {
            return redirect('login');
        }
    }

    public function show_cart()
    {
        if (Auth::id()) {
            $id = Auth::user()->id;
            $cart = Cart::where('user_id', '=', $id)->get();
            $productCount = $cart->count();
            $category = Category::all();


            $subTotal = $cart->sum('total_price');
            session(['subTotal' => $subTotal]);

            return view('home.show_cart', compact('cart', 'productCount', 'subTotal', 'category'));
        } else {
            return redirect('login');
        }
    }

    public function remove_product_cart($id)
    {
        $cart = Cart::find($id);
        $cart->delete();

        return redirect()->back()->with("success", "Product removed successfully");
    }

    public function update_cart(Request $request, $id)
    {
        $cart = Cart::find($id);
        if (!$cart) {
            return redirect()->back()->with("error", "Cart not found");
        }

        $quantity = $request->input('quantity');
        if (!$quantity || $quantity < 1) {
            return redirect()->back()->with("error", "Invalid quantity");
        }

        $cart->quantity = $quantity;
        $cart->total_price = $cart->price_per_one * $cart->quantity;
        $cart->save();
        return redirect()->back()->with("success", "Cart updated successfully");
    }

    public function cash_order()
    {
        $user = Auth::user();
        $userId = $user->id;

        $cartItems = Cart::where('user_id', '=', $userId)->get();

        if ($cartItems->isEmpty()) {
            // Giỏ hàng trống, không có gì để đặt hàng
            return;
        }

        $order = new Order();
        $order->name = $user->name;
        $order->email = $user->email;
        $order->address = $user->address;
        $order->phone = $user->phone;
        $order->user_id = $userId;
        $order->product_item = collect();

        $totalPrice = 0;

        foreach ($cartItems as $cartItem) {
            $productItem = [
                'product_title' => $cartItem->product_title,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->total_price,
                'image' => $cartItem->image
            ];
            $order->product_item->push($productItem);
            $totalPrice += $cartItem->total_price;
        }

        $order->total_price = $totalPrice;
        $order->payment_status = 'cash on delivery';
        $order->delivery_status = 'processing';

        $order->save();

        Cart::where('user_id', '=', $userId)->delete();
        return redirect()->back()->with("success", "Order updated successfully");
    }

    public function stripe($subTotal)
    {
        $category = Category::all();
        return view('home.stripe', compact('subTotal', 'category'));
    }

    public function stripePost(Request $request, $subTotal)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        Charge::create([

            "amount" => $subTotal * 100,

            "currency" => "usd",

            "source" => $request->stripeToken,

            "description" => "Thank you for your payment"

        ]);

        $user = Auth::user();
        $userId = $user->id;

        $cartItems = Cart::where('user_id', '=', $userId)->get();

        $order = new Order();
        $order->name = $user->name;
        $order->email = $user->email;
        $order->address = $user->address;
        $order->phone = $user->phone;
        $order->user_id = $userId;
        $order->product_item = collect();

        $totalPrice = 0;

        foreach ($cartItems as $cartItem) {
            $productItem = [
                'product_title' => $cartItem->product_title,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->total_price,
                'image' => $cartItem->image
            ];
            $order->product_item->push($productItem);
            $totalPrice += $cartItem->total_price;
        }

        $order->total_price = $totalPrice;
        $order->payment_status = 'paid';
        $order->delivery_status = 'processing';

        $order->save();

        Cart::where('user_id', '=', $userId)->delete();

        Session::flash('success', 'Payment successful!');

        return redirect()->back();
    }

    public function get_product_by_category($category_name)
    {
        $category = Category::all();
        $selected_category = Category::where('category_name', $category_name)->first();
        $products = Product::where('category', $category_name)->get();
        return view('home.get_product_by_category', compact('category', 'selected_category', 'products'));
    }

    public function get_profile()
    {
        $user = Auth::user();
        $category = Category::all();

        return view('home.get_profile', compact('user', 'category'));
    }

    public function get_all_products()
    {
        $products = Product::all();
        $category = Category::all();

        return view('home.get_all_products', compact('products', 'category'));
    }
}
