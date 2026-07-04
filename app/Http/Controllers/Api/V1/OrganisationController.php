<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\OrganisationResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrganisationController extends Controller
{
    /**
     * List the organisations the authenticated user belongs to.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return OrganisationResource::collection(
            $request->user()->organisations()->get()
        );
    }
}
