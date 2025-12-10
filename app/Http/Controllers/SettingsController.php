<?php

namespace App\Http\Controllers;

use App\Models\PredefinedCategory;
use App\Models\PredefinedUnit;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $categories = PredefinedCategory::orderBy('name')->get();
        $units = PredefinedUnit::orderBy('type')->orderBy('size')->get();
        
        return view('settings.index', compact('categories', 'units'));
    }

    // ==================== Category Methods ====================
    
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:predefined_categories,name'
        ]);

        PredefinedCategory::create([
            'name' => $request->name
        ]);

        return redirect()->route('settings.index')->with('success', 'Category added successfully!');
    }

    public function updateCategory(Request $request, PredefinedCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:predefined_categories,name,' . $category->id
        ]);

        $category->update([
            'name' => $request->name
        ]);

        return redirect()->route('settings.index')->with('success', 'Category updated successfully!');
    }

    public function destroyCategory(PredefinedCategory $category)
    {
        $category->delete();
        
        return redirect()->route('settings.index')->with('success', 'Category deleted successfully!');
    }

    // ==================== Unit Methods ====================
    
    public function storeUnit(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'default_sale_price' => 'required|numeric|min:0'
        ]);

        PredefinedUnit::create([
            'type' => $request->type,
            'size' => $request->size,
            'cost_price' => $request->cost_price,
            'default_sale_price' => $request->default_sale_price
        ]);

        return redirect()->route('settings.index')->with('success', 'Unit added successfully!');
    }

    public function updateUnit(Request $request, PredefinedUnit $unit)
    {
        $request->validate([
            'type' => 'required|string|max:255',
            'size' => 'required|string|max:255',
            'cost_price' => 'required|numeric|min:0',
            'default_sale_price' => 'required|numeric|min:0'
        ]);

        $unit->update([
            'type' => $request->type,
            'size' => $request->size,
            'cost_price' => $request->cost_price,
            'default_sale_price' => $request->default_sale_price
        ]);

        return redirect()->route('settings.index')->with('success', 'Unit updated successfully!');
    }

    public function destroyUnit(PredefinedUnit $unit)
    {
        $unit->delete();
        
        return redirect()->route('settings.index')->with('success', 'Unit deleted successfully!');
    }
}