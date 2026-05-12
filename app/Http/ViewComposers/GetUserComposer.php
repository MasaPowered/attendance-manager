<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class GetUserComposer
{

    /**
     * Bind data to the view.
     * @param View $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with(['admin' => Auth::guard('admin')->user()]);
        $view->with(['user' => Auth::guard('web')->user()]);
    }
}
