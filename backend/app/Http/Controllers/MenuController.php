<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $category = $request->query('category');
        $available = $request->query('available'); // 1/0

        $menus = Menu::query()
            ->when($q, fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->when($category && $category !== 'all', fn($qq) => $qq->where('category', $category))
            ->when($available !== null, fn($qq) => $qq->where('is_available', (bool)$available))
            ->orderByDesc('id')
            ->paginate(10);

        return response()->json($menus);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:2',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category' => 'nullable|string',
            'is_available' => 'nullable|boolean',
        ]);

        if (!isset($data['category'])) $data['category'] = 'Other';
        if (!isset($data['is_available'])) $data['is_available'] = true;

        $menu = Menu::create($data);

        return response()->json(['message' => 'Menu created', 'data' => $menu], 201);
    }

    public function show(Menu $menu)
    {
        return response()->json($menu);
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|min:2',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'category' => 'nullable|string',
            'is_available' => 'nullable|boolean',
        ]);

        $menu->update($data);

        return response()->json(['message' => 'Menu updated', 'data' => $menu]);
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return response()->json(['message' => 'Menu deleted']);
    }
}
