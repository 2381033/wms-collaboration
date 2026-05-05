<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use App\Models\Master\Product as MasterProduct;

class ProductResolver
{
    public static function resolve($principal_id, $codeInput, $mode = 'inbound', $branchId = null)
    {
        if (!$branchId) {
            return null;
        }

        $principalBranch = DB::table('iv_principal_branch')
            ->where('principal_id', $principal_id)
            ->where('branch_id', $branchId)
            ->first();

        if (!$principalBranch) {
            return null;
        }

        // Tentukan mode alias
        $useAlias = false;

        if ($mode === 'inbound') {
            $useAlias = filter_var($principalBranch->inbound_alias_code, FILTER_VALIDATE_BOOLEAN);
        } elseif ($mode === 'outbound') {
            $useAlias = filter_var($principalBranch->outbound_alias_code, FILTER_VALIDATE_BOOLEAN);
        } elseif ($mode === 'inventory') {
            $useAlias = filter_var($principalBranch->inventory_alias_code, FILTER_VALIDATE_BOOLEAN);
        }

        $cleanInput = trim(strtolower($codeInput));

        $query = MasterProduct::where('principal_id', $principal_id);

        if ($useAlias) {
            $query->whereRaw('LOWER(TRIM(alias_code)) = ?', [$cleanInput]);
        } else {
            $query->whereRaw('LOWER(TRIM(product_code)) = ?', [$cleanInput]);
        }

        $product = $query->first();

        if (!$product) {
            return null;
        }

        return [
            'product'     => $product,
            'product_id'  => $product->id,
            'final_code'  => $useAlias ? $product->alias_code : $product->product_code,
            'use_alias'   => $useAlias
        ];
    }
}
