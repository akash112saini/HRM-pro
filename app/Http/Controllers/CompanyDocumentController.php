<?php

namespace App\Http\Controllers;

use App\Models\CompanyDocument;
use Illuminate\Http\Request;

class CompanyDocumentController extends Controller
{
    public function index()
    {
        $documents = CompanyDocument::latest()->paginate(10);
        return view('documents.company.index', compact('documents'));
    }
}
