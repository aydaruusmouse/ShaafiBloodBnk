<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class HospitalScope implements Scope
{
	public function apply(Builder $builder, Model $model): void
	{
		$tenantId = (int) (app()->has('tenantId') ? app('tenantId') : 0);
		

		
		if ($tenantId > 0) {
			$builder->where($model->getTable() . '.hospital_id', $tenantId);
		} else {
			// If no tenant context, only show records with NULL or 0 hospital_id (legacy data)
			// This prevents new users from seeing old data
			$builder->where(function($query) use ($model) {
				$query->whereNull($model->getTable() . '.hospital_id')
					  ->orWhere($model->getTable() . '.hospital_id', 0);
			});
		}
	}
}

trait BelongsToHospital
{
	public static function bootBelongsToHospital(): void
	{
		static::addGlobalScope(new HospitalScope);

		static::creating(function (Model $model) {
			$tenantId = (int) (app()->has('tenantId') ? app('tenantId') : 0);
			if ($tenantId > 0 && empty($model->hospital_id)) {
				$model->hospital_id = $tenantId;
			}
		});
	}
} 