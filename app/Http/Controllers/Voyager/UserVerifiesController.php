<?php

namespace App\Http\Controllers\Voyager;

use App\Http\Controllers\Voyager\VoyagerBaseController;
use App\Model\UserVerify;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use TCG\Voyager\Facades\Voyager;

class UserVerifiesController extends VoyagerBaseController
{
    /**
     * Override update method to refresh user relationship after status change
     */
    public function update(Request $request, $id)
    {
        $slug = $this->getSlug($request);
        $dataType = Voyager::model('DataType')->where('slug', '=', $slug)->first();

        // Compatibility with Model binding.
        $id = $id instanceof \Illuminate\Database\Eloquent\Model ? $id->{$id->getKeyName()} : $id;

        $model = app($dataType->model_name);
        $query = $model->query();
        if ($dataType->scope && $dataType->scope != '' && method_exists($model, 'scope'.ucfirst($dataType->scope))) {
            $query = $query->{$dataType->scope}();
        }
        if ($model && in_array(SoftDeletes::class, class_uses_recursive($model))) {
            $query = $query->withTrashed();
        }

        $data = $query->findOrFail($id);

        // Check permission
        $this->authorize('edit', $data);

        // Validate fields with ajax
        $val = $this->validateBread($request->all(), $dataType->editRows, $dataType->name, $id)->validate();

        // Get fields with images to remove before updating and make a copy of $data
        $to_remove = $dataType->editRows->where('type', 'image')
            ->filter(function ($item, $key) use ($request) {
                return $request->hasFile($item->field);
            });
        $original_data = clone($data);

        $this->insertUpdateData($request, $slug, $dataType->editRows, $data);

        // Delete Images
        $this->deleteBreadImages($original_data, $to_remove);

        // Refresh the user's verification relationship to clear cache
        if ($data instanceof UserVerify) {
            $user = \App\Model\User::find($data->user_id);
            if ($user) {
                // Clear any cached verification relationship
                $user->unsetRelation('verification');
                $user->load('verification');
            }
        }

        return redirect()
            ->route("voyager.{$dataType->slug}.index")
            ->with([
                'message'    => __('voyager::generic.successfully_updated')." {$dataType->getTranslatedAttribute('display_name_singular')}",
                'alert-type' => 'success',
            ]);
    }
    /**
     * Show the verify page for a specific user verification request
     *
     * @param int $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function verify($id)
    {
        $dataType = Voyager::model('DataType')->where('slug', '=', 'user-verifies')->first();
        
        // Check permission
        $this->authorize('read', app($dataType->model_name));
        
        $userVerify = UserVerify::with('user')->findOrFail($id);
        
        return Voyager::view('voyager.user-verifies.verify', compact('userVerify', 'dataType'));
    }
    
    /**
     * Update verification status
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $dataType = Voyager::model('DataType')->where('slug', '=', 'user-verifies')->first();
        
        // Check permission
        $this->authorize('edit', app($dataType->model_name));
        
        $request->validate([
            'status' => 'required|in:pending,verified,rejected',
            'rejectionReason' => 'nullable|string|max:500',
        ]);
        
        $userVerify = UserVerify::findOrFail($id);
        $oldStatus = $userVerify->status;
        
        $userVerify->update([
            'status' => $request->status,
            'rejectionReason' => $request->rejectionReason,
        ]);
        
        // Refresh the user's verification relationship to clear cache
        $user = \App\Model\User::find($userVerify->user_id);
        if ($user) {
            $user->load('verification');
            // Clear any cached verification relationship
            $user->unsetRelation('verification');
            $user->load('verification');
        }
        
        return redirect('/admin/user-verifies')
            ->with([
                'message' => __('Verification status updated successfully'),
                'alert-type' => 'success',
            ]);
    }
}
