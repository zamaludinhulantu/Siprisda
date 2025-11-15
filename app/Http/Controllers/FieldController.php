<?php

namespace App\Http\Controllers;

use App\Models\Field;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index()
    {
        $fields = Field::orderBy('name')->get();
        return view('fields.index', compact('fields'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:fields,name',
        ]);

        Field::create($validated);

        return redirect()->route('fields.index')->with('success', 'Bidang berhasil ditambahkan.');
    }
}

