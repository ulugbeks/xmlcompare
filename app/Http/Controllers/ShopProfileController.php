<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ShopProfile;
use App\Models\Bug;

class ShopProfileController extends Controller
{
    public function reviews()
    {
        return view('shop.profile.reviews');
    }

    public function campaigns()
    {
        return view('shop.profile.campaigns');
    }

    public function campaignsBanner()
    {
        return view('shop.profile.campaigns-banner');
    }

    public function campaignsElements()
    {
        return view('shop.profile.campaigns-elements');
    }

    public function historyPay()
    {
        return view('shop.profile.history-pay');
    }

    public function info()
    {
        $shopProfile = ShopProfile::where('user_id', Auth::id())->first();
        return view('shop.profile.info', compact('shopProfile'));
    }

    public function alert()
    {
        return view('shop.profile.alert');
    }

    public function email()
    {
        return view('shop.profile.email');
    }

    public function password()
    {
        return view('shop.profile.password');
    }

    public function bugs()
    {
        return view('shop.profile.bugs');
    }

    public function updateInfo(Request $request)
    {
        $request->validate([
            'shop_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'contact_number' => 'required|string|max:255',
            'public_number' => 'nullable|string|max:255',
            'website' => 'nullable|url|max:255',
            'xml_link' => 'nullable|url|max:255',
        ]);

        $shopProfile = ShopProfile::where('user_id', Auth::id())->first();
        if (!$shopProfile) {
            $shopProfile = new ShopProfile();
            $shopProfile->user_id = Auth::id();
        }

        $shopProfile->shop_name = $request->shop_name;
        $shopProfile->company_name = $request->company_name;
        $shopProfile->registration_number = $request->registration_number;
        $shopProfile->address = $request->address;
        $shopProfile->contact_number = $request->contact_number;
        $shopProfile->public_number = $request->public_number;
        $shopProfile->website = $request->website;
        $shopProfile->xml_link = $request->xml_link;
        
        // Handle payment methods
        $paymentMethods = [];
        if ($request->has('payment_cash')) $paymentMethods[] = 'cash';
        if ($request->has('payment_card')) $paymentMethods[] = 'card';
        if ($request->has('payment_transfer')) $paymentMethods[] = 'transfer';
        if ($request->has('payment_leasing')) $paymentMethods[] = 'leasing';
        $shopProfile->payment_methods = json_encode($paymentMethods);
        
        $shopProfile->payment_description = $request->payment_description;
        $shopProfile->delivery_description = $request->delivery_description;
        
        // Handle working hours
        $workingHours = [
            'monday' => ['from' => $request->monday_from, 'to' => $request->monday_to],
            'tuesday' => ['from' => $request->tuesday_from, 'to' => $request->tuesday_to],
            'wednesday' => ['from' => $request->wednesday_from, 'to' => $request->wednesday_to],
            'thursday' => ['from' => $request->thursday_from, 'to' => $request->thursday_to],
            'friday' => ['from' => $request->friday_from, 'to' => $request->friday_to],
            'saturday' => ['from' => $request->saturday_from, 'to' => $request->saturday_to],
            'sunday' => ['from' => $request->sunday_from, 'to' => $request->sunday_to],
        ];
        $shopProfile->working_hours = json_encode($workingHours);
        
        if ($request->hasFile('banner')) {
            $path = $request->file('banner')->store('banners', 'public');
            $shopProfile->banner = 'storage/' . $path;
        }
        
        $shopProfile->save();
        
        return redirect()->back()->with('success', 'Shop profile updated successfully');
    }
    
    // Implement other update methods similarly
}