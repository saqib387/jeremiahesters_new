<?php

namespace App\Http\Controllers\Voyager;

use App\Http\Controllers\Voyager\Concerns\MergesVoyagerBelongsToFromRequest;
use TCG\Voyager\Http\Controllers\VoyagerUserController as BaseVoyagerUserController;

/**
 * Users BREAD must use this controller (see data_types.controller) so belongsTo FKs
 * like gender_id are merged before save; the package controller alone skips them.
 */
class VoyagerUserController extends BaseVoyagerUserController
{
    use MergesVoyagerBelongsToFromRequest;
}
