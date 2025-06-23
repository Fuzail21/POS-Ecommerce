<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $title = 'Setting';
        return view('profile.setting', compact('title'));
    }

    
    public function saveSettings(Request $request){
        $request->validate([
            'business_name'    => 'required|string|max:255',
            'currency_symbol'  => 'required|string|max:10',
            'currency_code'    => 'required|string|max:10', // ✅ validate currency code
            'logo'             => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $setting = Setting::first() ?? new Setting();

        $setting->business_name   = $request->business_name;
        $setting->currency_symbol = $request->currency_symbol;
        $setting->currency_code   = $request->currency_code; // ✅ save currency code
        $setting->primary_color   = $request->primary_color;
        $setting->secondary_color = $request->secondary_color;


        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($setting->logo_path && Storage::disk('public')->exists($setting->logo_path)) {
                Storage::disk('public')->delete($setting->logo_path);
            }

            // Upload new logo
            $imagePath = $request->file('logo')->store('Logo', 'public');
            $setting->logo_path = $imagePath;
        }

        $setting->save();

        return redirect()->back()->with('success', 'Settings saved successfully.');
    }

}
