<?php

namespace App\Http\Controllers;

use App\Services\Resources;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    /**
     * @var Resources
     */
    private $resources;

    public function __construct(Resources $resources)
    {
        $this->resources = $resources;
    }

    public function index(): View
    {
        $resources = $this->resources->getResourceList();

        return view('home', [
            'resources' => $resources
        ]);
    }
}
