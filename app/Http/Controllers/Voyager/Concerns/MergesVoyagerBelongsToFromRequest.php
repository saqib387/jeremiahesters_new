<?php

namespace App\Http\Controllers\Voyager\Concerns;

use Illuminate\Http\Request;

trait MergesVoyagerBelongsToFromRequest
{
    /**
     * Voyager's insertUpdateData skips belongsTo rows and checks $request->has($row->field), while
     * relationship selects POST the FK column (e.g. gender_id). Merge those onto the model first.
     *
     * @param  \Illuminate\Support\Collection<int, mixed>  $rows
     * @param  \Illuminate\Database\Eloquent\Model  $data
     */
    protected function mergeBelongsToForeignKeysFromRequest(Request $request, $rows, $data): void
    {
        foreach ($rows as $row) {
            if ($row->type !== 'relationship' || ! isset($row->details->type) || $row->details->type !== 'belongsTo') {
                continue;
            }
            $column = $row->details->column;
            if (! $request->exists($column)) {
                continue;
            }
            $value = $request->input($column);
            $data->{$column} = ($value === '' || $value === null) ? null : $value;
        }
    }

    public function insertUpdateData($request, $slug, $rows, $data)
    {
        $this->mergeBelongsToForeignKeysFromRequest($request, $rows, $data);

        return parent::insertUpdateData($request, $slug, $rows, $data);
    }
}
