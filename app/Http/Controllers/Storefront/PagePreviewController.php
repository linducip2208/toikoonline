<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PagePreviewController extends Controller
{
    public function show(Request $request, Page $page)
    {
        $this->authorize('view', $page);

        return view('storefront.page-preview', ['page' => $page]);
    }
}
