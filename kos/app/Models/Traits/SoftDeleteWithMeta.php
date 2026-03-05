<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

trait SoftDeleteWithMeta
{
    use SoftDeletes;

    public static function bootSoftDeleteWithMeta()
    {
        static::deleting(function (Model $model) {
            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                return;
            }
            $model->deleted = 1;
            try {
                $userId = auth()->check() ? auth()->id() : null;
                $ip = request()->ip();
            } catch (\Throwable $e) {
                $userId = null;
                $ip = null;
            }
            if ($model->isFillable('deleted_by') || array_key_exists('deleted_by', $model->getAttributes())) {
                $model->deleted_by = $userId;
            }
            if ($model->isFillable('deleted_ip') || array_key_exists('deleted_ip', $model->getAttributes())) {
                $model->deleted_ip = $ip;
            }
        });

        static::restoring(function (Model $model) {
            $model->deleted = 0;
            if ($model->isFillable('deleted_by') || array_key_exists('deleted_by', $model->getAttributes())) {
                $model->deleted_by = null;
            }
            if ($model->isFillable('deleted_ip') || array_key_exists('deleted_ip', $model->getAttributes())) {
                $model->deleted_ip = null;
            }
        });
    }
}
